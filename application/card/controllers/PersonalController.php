<?php

class Card_PersonalController extends Zend_Controller_Action
{
	public function indexAction()
	{	
		$personId = $this->_getParam('personid');
		$personal = new Personal_DataModel($personId);
		
		$view = $this->initView();
		$view->personal = $personal;
	}
}