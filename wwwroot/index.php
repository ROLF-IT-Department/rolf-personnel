
<?php
date_default_timezone_set('Europe/Moscow');
set_include_path('../library');

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload();

ini_set('display_errors', Rp::getConfig('system')->debug_mode);

Zend_Controller_Action_HelperBroker::addHelper(new Rp_Controller_Action_Helper_ViewRenderer());
$cache = Zend_Cache::factory('Core', 'Memcached', array('lifetime' => 7200, 'automatic_serialization' => TRUE));
Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);

$controller = Zend_Controller_Front::getInstance()
	->registerPlugin(new Rp_Controller_Plugin_System())
	->addModuleDirectory('../application');
if(isset($_SERVER['ENVIRONMENT']) AND $_SERVER['ENVIRONMENT'] == 'development')
{
	$options = array(
	    'plugins' => array('Variables',
			'Html',
			'Database' => array('adapter' => array(
				'rolf-personnel' => Rp::getDbAdapter(),
			)),
			'File' => array('basePath' => '../application'),
			'Memory',
			'Time',
			'Registry',
			'Cache' => array('backend' => $cache->getBackend()),
			'Exception')
	);
	$controller->registerPlugin(new ZFDebug_Controller_Plugin_Debug($options));
}


$controller->dispatch();