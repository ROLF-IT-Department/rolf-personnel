<?php

class HelpController extends Zend_Controller_Action
{
	public function indexAction()
	{	
		$user = Rp_User::getInstance();
		
		$view = $this->initView();
		$view->title = Rp::getTitle('Справка');
	}
}