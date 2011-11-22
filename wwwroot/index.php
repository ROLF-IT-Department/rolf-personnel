<?php
date_default_timezone_set('Europe/Moscow');
set_include_path('../library');

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload();

ini_set('display_errors', Rp::getConfig('system')->debug_mode);

Zend_Controller_Action_HelperBroker::addHelper(new Rp_Controller_Action_Helper_ViewRenderer());

Zend_Controller_Front::getInstance()
	->registerPlugin(new Rp_Controller_Plugin_System())
	->addModuleDirectory('../application')
	->dispatch();