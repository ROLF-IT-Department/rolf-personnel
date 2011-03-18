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
			$cardid = $request->getParam('cardid', NULL);
			$card = $cards->findByPersonIdAndCard($personId, $cardid);//, $period);
		}

		$user = Rp_User::getInstance();
		$emp = $card->getEmployee();
		$person = $emp->getPerson();

		$cards_and_periods = $cards->get_cards_and_periods($personId);

        $ratings = new Rp_Db_Table_Ach_Ratings();
        $rate_weights = $ratings->fetchWeights();
		$rate_names = $ratings->fetchNames();

		$rates = array();
		foreach($rate_names as $id => $name)
		{
			$rates[$id] = array(
				'name' => $name,
				'weight' => (isset($rate_weights[$id]['weight'])) ? $rate_weights[$id]['weight'] : NULL,
				'id' => $id,
			);
		}


		$today_year = date('Y');
		$more_years = FALSE;
		$periods =  NULL;
		$statistics =  NULL;
		$count = count($cards_and_periods);
		$i = 0;
		foreach($cards_and_periods as $card_and_periods)
		{
			$period_year = $card_and_periods->period;
			$_periods = NULL;
			$_period_start = strtotime($card_and_periods->period_start);
			$_period_end   = strtotime($card_and_periods->period_end);
			if($card_and_periods->period_start OR $card_and_periods->period_end)
			{
				$_periods = ': ' . date('m', $_period_start) . '-' . date('m', $_period_end);
				if($card_and_periods->period == $card->period)
				{
					$statistics[$card_and_periods->id] = array(
						'period' => array(
//							'year' => $card_and_periods->period,
							'start' => date('d.m.Y', $_period_start),
							'end' => date('d.m.Y', $_period_end),
						),
						'ratings' => array(
							'tasks'     => $rates[$card_and_periods->rtg_tasks_id],
							'competens' => $rates[$card_and_periods->rtg_competens_id],
							'total'     => $rates[$card_and_periods->rtg_total_id],
						),
					);
				}
			}

			$periods[$card_and_periods->id] = array(
				'year' => $card_and_periods->period,
				'name' => $card_and_periods->period . $_periods
			);

			$i++;
			if($count == $i)
			{
				if($card_and_periods->period > $today_year)
				{
					$more_years = TRUE;
				}
			}
		}

		$rate_calc = array('name' => '-');
		$common_rating_id = NULL;
		$common_rating_confirmed = FALSE;
		if($statistics)
		{
			$rate_sum = 0;
			$rate_num = 0;
			foreach($statistics as $_rate)
			{
				$rate_sum += $_rate['ratings']['total']['weight'];
				$rate_num++;
			}
			$common_rate = round($rate_sum / $rate_num);

			foreach($rate_weights as $id => $weight)
			{
				if($weight['weight'] == $common_rate)
				{
					$rate_calc = $rates[$id];
				}
			}

			$common_ratings = new Rp_Db_Table_Ach_Cards_Agreements();
			$common_rating = $common_ratings->cards_agreement($personId, $period_year);

			if($common_rating != NULL)
			{
				$common_rating_confirmed = (bool) $common_rating->confirmed;
				$common_rating_id        = $common_rating->id;
			}
		}

		if( ! $more_years)
		{
			$periods[0] = array(
				'year' => $today_year + 1,
				'name' => $today_year + 1,
			);
		}

		$period_start = date('d.m.Y', strtotime($card->period_start));
		$period_end   = date('d.m.Y', strtotime($card->period_end));
		$period_text = ($card->period_start OR $card->period_end)
			? $period_start. ' - ' . $period_end
			: NULL;
		$statuses = new Rp_Db_Table_Ach_Cards_Statuses();
		$status = $statuses->find($card->status_id)->current();

		$months = new Rp_Db_Table_Months();

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
		$view->period_text = $period_text;
		$view->status = $status;
		$view->tasks = $card->fetchTasks();
		$view->personalTasks = $card->fetchPersonalTasks();
		$view->competences = $card->fetchCompetences();
		$view->personalCompetences = $card->fetchPersonalCompetences();
		$view->trainings = $card->fetchTrainings();
		$view->personalTrainings = $card->fetchPersonalTrainings();
		$view->approvals = new Achievs_ApprovalsModel($card);
		$view->months = $months->fetchNames();
		$view->ratings = $rate_names;
		$view->rate_weights = $rate_weights;
		$view->rate_name_weights = $ratings->fetchNameWeights();
		$view->rate_calc = $rate_calc;
		$view->common_rating_confirmed = $common_rating_confirmed;
		$view->common_rating_id = $common_rating_id;
		$view->trainsGroupsMethods = $trainsGroupsMethods->toArrayNames();
		$view->trainsGroupsMethodsActual = $trainsGroupsMethods->toArrayNamesWithoutDisabled();
		$view->trainsRespons = $trainsRespons->fetchNames();
		$view->careerFlags = $careerFlags->fetchNames();
		$view->count_func = count($emp->getFuncManagers()->getCol('person_id'));
		$view->emails = $this->_getEmails($card, $user);
		$view->statistics = $statistics;

		$view->tab = (isset($_SESSION['tab'])) ? $_SESSION['tab'] : 'tasks';
	}

	public function printAction()
	{
		$this->indexAction();
	}

	public function saveAction()
	{
		$request = $this->getRequest();
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

		$date_save = array('save_date'=> date("m.d.Y h:I"));

		$card->setFromArray($date_save);

		$card->save();

		$this->_redirect('/card/achievs/index/personid/' . $card->person_id . '/cardid/' . $card->id);
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

			//  Ограничение для Ника Хокинса, Сергея Петрова и Салиты. Чтобы он не был вышестоящим руководителем
			if ($highMngsIds[0] == 29790 OR $highMngsIds[0] == 44345 OR $highMngsIds[0] == 44107)
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