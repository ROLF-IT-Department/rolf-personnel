<?php

class IntegrateController extends Zend_Controller_Action
{
	public function indexAction()
	{

		$request = $this->getRequest();
		$id = $request->getParam('id', null);

		Rp_User::setInstance($id);

		$user = Rp_User::getInstance();
		$employees = new Rp_Db_View_Employees();
		$employee_first = $employees->findByPersonId($_SESSION['user_id'])->current();
		$person_first = $employee_first->getPerson();

		$employee = $employees->findByPersonId($id)->current();
		$person = $employee->getPerson();

		if ($person_first->fullname != $person->fullname)
		{
			$this->_forward('error', 'error');
			return;
		}


		$this->_forward('index', 'index');
		return;


	}
}