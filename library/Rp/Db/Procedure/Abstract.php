<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Procedure
 */

/**
 * ����������� ������ �������� ��������� ���� ������.
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Procedure
 */
abstract class Rp_Db_Procedure_Abstract
{
	/**
	 * ������ �������� ���� ������ �������.
	 * 
	 * @var Zend_Db_Adapter_Abstract
	 */
	private static $_db = null;
	
	/**
	 * �������� ��������� � ���� ������.
	 *
	 * @var string
	 */
	protected $_name = null;
	
	/**
	 * �����������.
	 * 
	 * @return void
	 */
	public function __construct()
	{
	}
	
	/**
	 * ���������� ������ �������� ���� ������ �������.
	 * 
	 * @return Zend_Db_Adapter_Abstract
	 */
	public function getAdapter()
	{
		if (empty(self::$_db)) {
			self::$_db = Rp::getDbAdapter();
		}
		return self::$_db;
	}
	
	/**
	 * ��������� �������� ���������.
	 *
	 * @param string $params ��������� ���������.
	 * 
	 * @return void
	 * @throws Exception
	 */
	public function exec($params = null)
	{
		if (empty($this->_name)) {
			throw new Exception('�������� ��������� �� ����������.');
		}
		
		$sql = "exec {$this->_name} $params";
		$this->getAdapter()->query($sql);
	}
}