<?php

class Card_AchievsController extends Zend_Controller_Action
{
	const ROLE_VIEWER       = 0;			// право просмотра карточки
	const ROLE_EMPLOYEE     = 1;			// право сотрудника
	const ROLE_MANAGER      = 2;			// право непосредственного руководителя
	const ROLE_HIGH_MANAGER = 4;			// право вышестоящего руководителя
	const ROLE_FUNC_MANAGER = 8;			// право функционального руководителя



	public function indexAction()
	{
		$request = $this->getRequest();

		$cards = new Rp_Db_Table_Ach_Cards();
		$id = $request->getParam('id', null);
		if ($id !== null) {
			$card = $cards->find($id)->current();
			if (empty($card)) {
				throw new Exception('Карточка достижений с указанным идентификатором не найдена.');
			}
		} else {
			$personId = $request->getParam('personid', null);
			$period = $request->getParam('period', date('Y'));
			$card = $cards->findByPersonIdAndPeriod($personId, $period);
		}

		$user = Rp_User::getInstance();
		$emp = $card->getEmployee();
		$person = $emp->getPerson();

		$periods = range(2006, date('Y') + 1);
		$periods = array_combine($periods, $periods);
		$statuses = new Rp_Db_Table_Ach_Cards_Statuses();
		$status = $statuses->find($card->status_id)->current();

		$months = new Rp_Db_Table_Months();
		$ratings = new Rp_Db_Table_Ach_Ratings();
		$trainsGroupsMethods = new Rp_Db_Table_Ach_Trainings_GroupsMethods();
		$trainsRespons = new Rp_Db_Table_Ach_Trainings_Respons();
		$careerFlags = new Rp_Db_Table_Ach_Cards_CareerFlags();

		$view = $this->initView();
		$view->title = Rp::getTitle(array($person->fullname, "Карточка достижений {$card->period}"));
		$view->emp = $emp;
		$view->person = $person;
		$view->user = $user;
		$view->userRole = $this->_getUserRole($card, $user);
		$view->card = $card;
		$view->periods = $periods;
		$view->status = $status;
		$view->tasks = $card->fetchTasks();
		$view->personalTasks = $card->fetchPersonalTasks();
		$view->competences = $card->fetchCompetences();
		$view->personalCompetences = $card->fetchPersonalCompetences();
		$view->trainings = $card->fetchTrainings();
		$view->personalTrainings = $card->fetchPersonalTrainings();
		$view->approvals = new Achievs_ApprovalsModel($card);
		$view->months = $months->fetchNames();
		$view->ratings = $ratings->fetchNames();
		$view->rate_weights = $ratings->fetchWeights();
		$view->rate_name_weights = $ratings->fetchNameWeights();
		$view->trainsGroupsMethods = $trainsGroupsMethods->toArrayNames();
		$view->trainsGroupsMethodsActual = $trainsGroupsMethods->toArrayNamesWithoutDisabled();
		$view->trainsRespons = $trainsRespons->fetchNames();
		$view->careerFlags = $careerFlags->fetchNames();
		$view->count_func = count($emp->getFuncManagers()->getCol('person_id'));
		$view->emails = $this->_getEmails($card, $user);

		$view->tab = (isset($_SESSION['tab'])) ? $_SESSION['tab'] : 'tasks';
	}

	public function printAction()
	{
		$this->indexAction();
	}

	public function saveAction()
	{
		$request = $this->getRequest();
//		echo '<div style="height:100%;width:100%;display:block;overflow:auto"><pre>';
//		exit(var_dump($request->getPost('tasks')));
		$cardId  = $request->getPost('id', null);

		$tab = $request->getPost('tab', 'tasks');
		$_SESSION['tab'] = $tab;

		$cards = new Rp_Db_Table_Ach_Cards();
		$card = $cards->find($cardId)->current();

		$card->insertTasks($request->getPost('newTasks', array()));
		$card->updateTasks($request->getPost('tasks_in_personal', array()));
		$card->updateTasks($request->getPost('tasks', array()));

		$card->insertCompetences($request->getPost('newCompetences', array()));
		$card->updateCompetences($request->getPost('competences', array()));
		$card->insertTrainings($request->getPost('newTrainings', array()));
		$card->insertTrainings($request->getPost('newTrainingsPersonal', array()));
		$card->updateTrainings($request->getPost('trainings', array()));


		$card->setFromArray($request->getPost('ratings', array()));
		$card->setFromArray($request->getPost('comments', array()));
		$card->setFromArray($request->getPost('approvals', array()));
		$card->setFromArray($request->getPost('ratio', array()));

		$date_save = array('save_date'=> date("m.d.Y h:i:00"));

		$card->setFromArray($date_save);

		$card->save();

		$this->_redirect('/card/achievs/index/id/' . $cardId);
	}

	public function approvalAction()
	{
		/**
		 * Для сохранения согласований без лишних действий
		 * по сохранению бизнес-целей, компетенций и целей проф. развития.
		 */
	}

	private function _getUserRole(Rp_Db_Table_Row_Ach_Card $card, Rp_User $user)	// определяем какая роль у пользователя для данной карточки сотрудника
	{
		$period = $card->period;
		$prevYear = date('Y') - 1;
		$curMonth = date('n');

		$userRole = self::ROLE_VIEWER;			// по умолчанию ставим роль просмотра =0

		/*if ($period < $prevYear || ($period == $prevYear && $curMonth > 1)) {
			return $userRole;
		}*/

		$emp = $card->getEmployee();
		$empId = $emp->person_id;		// id сотрудника
		$userId = $user->person_id;	// id пользователя

		/*
		if ($empId == $userId AND $user->isTop()) {
			$userRole |= self::ROLE_EMPLOYEE | self::ROLE_MANAGER | self::ROLE_HIGH_MANAGER;
			return $userRole;
		}
		*/

		$mngsIds = $emp->getManagers()->getCol('person_id');			// список непосредственных руководителей
		$highMngsIds = $emp->getHighManagers()->getCol('person_id');	// список вышестоящих руководителей
		$funcMngsIds = $emp->getFuncManagers()->getCol('person_id');					// список функциональных руководителей


		if ($empId == $userId) {					// если пользователь является обычным сотрудником
			$userRole |= self::ROLE_EMPLOYEE;		// устанавливаем права сотрудника
			if (count($mngsIds) == 0) {				// если нет непосредственных руководителей
				$userRole |= self::ROLE_MANAGER;		// устанавливаем сначала роль руководителя
				$userRole |= self::ROLE_HIGH_MANAGER;	// устанавливаем роль вышестоящего руководителя
			}
		}
		if (in_array($userId, $mngsIds)) {			// если пользователь есть в списке непосредственных руководителей
			$userRole |= self::ROLE_MANAGER;		// устанавливаем роль руководителя
			if (count($highMngsIds) == 0) {					// если нет вышестоящих руководителей
				$userRole |= self::ROLE_HIGH_MANAGER;		// устанавливаем роль вышестоящего руководителя
			}

			/////  Ограничение для Ника Хокинса. Чтобы он не был вышестоящим руководителем
			if ($highMngsIds[0] == 29790 OR $highMngsIds[0] == 43835)
				$userRole |= self::ROLE_HIGH_MANAGER;
		}

		if (in_array($userId, $highMngsIds)) {			// если пользователь в списке вышестояих руководителей, то устанавливаем ему эту роль
			$userRole |= self::ROLE_HIGH_MANAGER;

			/////  Ограничение для Ника Хокинса и Сергея Петрова. Чтобы он не был вышестоящим руководителем
			//if ($highMngsIds[0] == 29790) 	$userRole = self::ROLE_VIEWER;
		}


		if (in_array($userId, $funcMngsIds)) {			// если пользователь в списке функциональных руководителей, то устанавливаем ему эту роль
			$userRole |= self::ROLE_FUNC_MANAGER;
		}



		return $userRole;		// возвращаем роль пользователя
	}

	private function _getEmails(Rp_Db_Table_Row_Ach_Card $card, Rp_User $user)
	{

		$emp = $card->getEmployee();				// получаем сотрудника по его карточке
		$highMngsIds = $emp->getHighManagers();		// список вышестоящих руководителей
		$funcMngsIds = $emp->getFuncManagers();		// список функциональных руководителей

		$userId = $user->person_id;
		$mngsIds = $emp->getManagers()->getCol('person_id');

		$mail = '';

		if (in_array($userId, $mngsIds))
		{

			foreach ($highMngsIds as $m)
			{
				$hmail = $m->getPerson()->email;

				if (strlen($hmail) > 3 AND strpos($mail, $hmail) === false AND $hmail != 'NHAWKINS@ROLF.RU')		// оператор === , так как функция возвращает 0, если не находит
					$mail = $mail . $hmail . '; ';
			}

			foreach ($funcMngsIds as $m)
			{
				$fmail = $m->getPerson()->email;
				if (strlen($fmail) > 3 AND strpos($mail, $fmail) === false)
					$mail = $mail . $fmail . '; ';
			}

			$emp_email = $emp->getPerson()->email;		// добавляем в конце почту подчиненного
			if (strlen($emp_email) > 3)
				$mail = $mail . $emp_email;

		}

		return $mail;
	}
}