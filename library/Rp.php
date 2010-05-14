<?php
/**
 * ROLF Personnel library
 *
 * @category Rp
 * @package  Rp
 * @version  $Id: Rp.php 0000 2008-03-14 00:00:00Z $
 */

/**
 * �������������� ������� ROLF Personnel.
 *
 * @category Rp
 * @package  Rp
 */
class Rp
{
	/**
	 * �������� �������.
	 */
	const NAME = '�����-��������';
	
	/**
	 * ������ �������.
	 */
	const VERSION = 'ver. 2, build 1.3';
	
	/**
	 * ��������� ���������������� ���� (���� ������������ ����� index.php).
	 */
	const CONFIG = '../config.ini';
	
	/**
	 * ������ �������� ���� ������ �������.
	 * 
	 * @var Zend_Db_Adapter_Abstract
	 */
	private static $_db = null;
	
	/**
	 * ������ ����������������� ����� ������� {@link CONFIG}.
	 * 
	 * @var Zend_Config_Ini
	 */
	private static $_config = null;
	
	/**
	 * ���������� ��������������� ������ ���������.
	 *
	 * @param string|array $prefix ������, ������� ����� ���������
	 * � ������ ���������. ���� $prefix - ������, �� ��� �������� �����
	 * ���������� � ������.
	 * 
	 * @return string
	 */
	public static function getTitle($prefix = null)
	{	
		$title = array_merge((array) $prefix, array(self::NAME));
		
		return implode(' - ', $title);
	}
	
	/**
	 * ���������� ������ ����������������� ����� �������. 
	 * ��� ����������� ������������ ����� ������������ ������������ ��������
	 * ��������� {@link CONFIG}.
	 * 
	 * @param string $section �������� ������� ����������������� �����.
	 * 
	 * @return Zend_Config_Ini
	 * @throws Exception
	 */
	public static function getConfig($section = null)
	{	
		if (empty(self::$_config)) {
			self::$_config = new Zend_Config_Ini(self::CONFIG, null);
		}
		if ($section !== null) {
			return self::$_config->$section;
		}
		return self::$_config;
	}
	
	/**
	 * ���������� ������ �������� ���� ������ �������.
	 * ��� �������� � ��������� ���������� ������ ���� �������
	 * � ��������� ���������������� ����� {@link CONFIG} � ������� "database".
	 * 
	 * @return Zend_Db_Adapter_Abstract
	 * @throws Exception
	 */
	public static function getDbAdapter()
	{
		if (empty(self::$_db)) {
			$adapter = null;
			$params = self::getConfig('database')->toArray();
			
			if (isset($params['adapter'])) {
				$adapter = $params['adapter'];
				unset($params['adapter']);
			}
			self::$_db = Zend_Db::factory($adapter, $params);
		}
		return self::$_db;
	}
}