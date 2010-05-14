<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Controller
 * @subpackage Rp_Controller_Plugin
 */

/**
 * Системный плагин.
 *
 * @category   Rp
 * @package    Rp_Controller
 * @subpackage Rp_Controller_Plugin
 */
class Rp_Controller_Plugin_System extends Zend_Controller_Plugin_Abstract
{	
	/**
	 * Массив подключенных модулей.
	 *
	 * @var array
	 */
	private static $_includeModules = array();
	
	/**
	 * Метод вызывается перед началом диспетчеризации запроса.
	 * Если идентичность пользователя установлена в классе авторизации
	 * (пользователь авторизован), то плагин не выполняет никаких действий
	 * и управление возвращается диспетчеру.
	 * Если пользователь не авторизован, то происходит проверка корректности
	 * запроса. Если запрос корректен, то он перенаправляется контроллеру авторизации.
	 * В противном случае, генерируется исключение.
	 * 
	 * @param Zend_Controller_Request_Abstract $request
	 * 
	 * @return void
	 * @throws Exception
	 */
	public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
	{		
		if (Zend_Auth::getInstance()->hasIdentity()) {
			return;
		}
		
		$module = $request->getModuleName();
		$controller = $request->getControllerName();
		
		if ($module != 'default' || !in_array($controller, array('index', 'auth'))) {
			throw new Exception('Ошибка авторизации. Не корректный запрос.');
		}
		$request->setModuleName('default');
		$request->setControllerName('auth');
		$request->setActionName('index');
	}
	
	/**
	 * Метод вызывается до того, как диспетчером будет вызвано действие.
	 * Добавляет в список директорий для загрузки (include_path) 
	 * директорию моделей текущего модуля.
	 * 
	 * @param Zend_Controller_Request_Abstract $request
	 * @return void
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		$module = $request->getModuleName();
		
		if (in_array($module, self::$_includeModules)) {
			return;
		}
		$pathModels = '../application/' . $module . '/models';
		set_include_path(get_include_path() . PATH_SEPARATOR . $pathModels);
		self::$_includeModules[] = $module;
	}
}