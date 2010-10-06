<?php

class ErrorController extends Zend_Controller_Action
{
	public function errorAction()
	{	
		$errors = $this->getRequest()->getParam('error_handler');
		$e = isset($errors->exception) ? $errors->exception : null;
		
		$this->getResponse()->clearBody();
		
		$view = $this->initView();
		$view->title = Rp::getTitle('Ñîîáùåíèå îá îøèáêå');
		$view->caption = 'Îøèáêà';
		$view->message = '';
		if ($e instanceof Exception) {
			if (Rp::getConfig('system')->debug_mode) {
				$view->message = $e->__toString();
			} else {
				$view->message = $e->getMessage();
			}
		}
	}
}