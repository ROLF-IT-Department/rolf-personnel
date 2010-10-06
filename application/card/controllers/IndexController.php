<?php

class Card_IndexController extends Zend_Controller_Action
{
	public function indexAction()
	{	
		$personId = $this->_getParam('personid', null);
		$persons = new Rp_Db_View_Persons();
		$person = $persons->find($personId)->current();
		
		$view = $this->initView();
		$view->title = Rp::getTitle(array($person->fullname, 'Карточка сотрудника'));
		$view->person = $person;
	}
}