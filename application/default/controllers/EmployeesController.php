<?php

class EmployeesController extends Zend_Controller_Action
{
	public function indexAction()
	{
		$request = $this->getRequest();
		$postIds = $request->getParam('postIds', null);
		$showEmps = $request->getParam('showEmps', true);
		$showSubEmps = $request->getParam('showSubEmps', true);
		$func = $request->getParam('func', null);

		$periodFirst = date('Y') + (date('n') < 12 ? -1 : 0);
		$periodSecond = $periodFirst + 1;

		$listEmployees = new Employees_List($postIds, $periodFirst, $periodSecond, $showEmps, $showSubEmps, $func);

		$view = $this->initView();
		$view->listEmployees = $listEmployees;
	}
}