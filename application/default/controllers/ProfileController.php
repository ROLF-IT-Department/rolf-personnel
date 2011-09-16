<?php

class ProfileController extends Zend_Controller_Action
{
	public function indexAction()
	{	
		$personId = $this->_getParam('personid');
		
		$cards = new Rp_Db_Table_Ach_Cards();
		$card = $cards->findByPersonIdAndPeriod($personId, date('Y'));
		$employee = $card->getEmployee();

		$user = Rp_User::getInstance();
		
		$managersIds = $employee->getManagers()->getCol('person_id');
		$func_managersIds = $employee->getFuncManagers()->getCol('person_id');
		$count_func = count($employee->getFuncManagers()->getCol('person_id'));
		$columnNotes = '';
		if (in_array($user->person_id, $managersIds)) $columnNotes = 'MNG';
		if (in_array($user->person_id, $func_managersIds)) $columnNotes = 'FUNC';
			
		$view = $this->initView();
		$view->person = $employee->getPerson();
		$view->tasks = $card->fetchTasks();
		$view->trainings = $card->fetchTrainings();
		$view->columnNotes = $columnNotes;
		$view->count_func = $count_func;
	}
	
	public function trainingsAction()
	{
		$personId = $this->_getParam('personid');
		
		$cards = new Rp_Db_Table_Ach_Cards();
		$card = $cards->findByPersonIdAndPeriod($personId, date('Y'));
		
		$months = new Rp_Db_Table_Months();
		$respons = new Rp_Db_Table_Ach_Trainings_Respons();
		$groupsMethods = new Rp_Db_Table_Ach_Trainings_GroupsMethods();
		
		$view = $this->initView();
		$view->trainings = $card->fetchTrainings();
		$view->months = $months->fetchNames();
		$view->respons = $respons->fetchNames();
		$view->groupsMethods = $groupsMethods->toArrayNames();
	}
	
	public function personalAction()
	{
		$this->_forward('index', 'personal', 'card');
	}
}