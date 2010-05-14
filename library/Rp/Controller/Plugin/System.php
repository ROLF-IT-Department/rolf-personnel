<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Controller
 * @subpackage Rp_Controller_Plugin
 */

/**
 * ��������� ������.
 *
 * @category   Rp
 * @package    Rp_Controller
 * @subpackage Rp_Controller_Plugin
 */
class Rp_Controller_Plugin_System extends Zend_Controller_Plugin_Abstract
{	
	/**
	 * ������ ������������ �������.
	 *
	 * @var array
	 */
	private static $_includeModules = array();
	
	/**
	 * ����� ���������� ����� ������� ��������������� �������.
	 * ���� ������������ ������������ ����������� � ������ �����������
	 * (������������ �����������), �� ������ �� ��������� ������� ��������
	 * � ���������� ������������ ����������.
	 * ���� ������������ �� �����������, �� ���������� �������� ������������
	 * �������. ���� ������ ���������, �� �� ���������������� ����������� �����������.
	 * � ��������� ������, ������������ ����������.
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
			throw new Exception('������ �����������. �� ���������� ������.');
		}
		$request->setModuleName('default');
		$request->setControllerName('auth');
		$request->setActionName('index');
	}
	
	/**
	 * ����� ���������� �� ����, ��� ����������� ����� ������� ��������.
	 * ��������� � ������ ���������� ��� �������� (include_path) 
	 * ���������� ������� �������� ������.
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