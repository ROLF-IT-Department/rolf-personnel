<?php
/**
 * ROLF Personnel library
 *
 * @category Rp
 * @package  Rp_User
 */

/**
 * ������ ������������ �������.
 *
 * @category Rp
 * @package  Rp_User
 */
class Rp_User
{
	/**
	 * ���������������� ������.
	 *
	 * @var Rp_User
	 */
	private static $_instance = null;
	
	/**
	 * ������ ����������.
	 *
	 * @var Rp_Db_View_Row_Employee
	 */
	private static $_employee = null;
	
	/**
	 * ������������� ���. ����.
	 *
	 * @var int
	 */
	private $_personId = null;
	
	/**
	 * ������������� ����� ����� � �������.
	 * 
	 * @var int
	 */
	private $_logonId = null;
	
	/**
	 * �����������.
	 * 
	 * ��� ��������� ������� ������������ ����� {@link setInstance()}.
	 * ��� ��������� ������� ������������ ����� {@link getInstance()}.
	 * ������ ������������ - ��� ������-singleton.
	 *
	 * @param int $personId ������������� ���. ����.
	 * 
	 * @return void
	 * @throws Exception
	 */
	public function __construct($personId)
	{
		if (isset(self::$_instance)) {
			throw new Exception('������ ������������ ��� ���������� � �� ����� ���� ������ ��������.');
		}
		if (!is_numeric($personId)) {
			throw new Exception('������������� ���. ���� ������ ���� ����� ������.');
		}
		$this->_personId = $personId;
	}
	
	/**
	 * ���������� �����.
	 * 
	 * ������ ������������ ������������� ��������� ��� ������� 
	 * � ��������� (����� ������) ���������������� ��� ������� ����������.
	 *
	 * @param  string $field �������� ����.
	 * @return string
	 */
	public function __get($field)
	{
		return $this->getEmployee()->$field;
	}
	
	/**
	 * ���������� �����.
	 * 
	 * ������ ������������ ������������� ��������� ��� ������� 
	 * � ������� ���������������� ��� ������� ����������.
	 * �������������� ������ ����� ������� ��� ����������.
	 *
	 * @param string $method �������� ������.
	 * @param array  $args   ������ ����������.
	 * 
	 * @return mixed
	 * @throws Exception
	 */
	public function __call($method, array $args)
	{
		if (!empty($args)) {
			throw new Exception('����� ������� ������� ���������� � ����������� �� ��������������.');
		}
		return $this->getEmployee()->$method();
	}
	
	/**
	 * ���������� �����.
	 * 
	 * ������������� ������������ �������.
	 * 
	 * @return void
	 */
	public function __clone()
	{
		throw new Exception('������ ������������ �� ����� ���� ����������.');
	}
	
	/**
	 * ���������� ������������� ���. ����.
	 *
	 * @return int
	 */
	public function getPersonId()
	{
		return $this->_personId;
	}
	
	/**
	 * ������������ ������ ������������.
	 *
	 * @param int $personId ������������� ���. ����.
	 * 
	 * @return void
	 */
	public static function setInstance($personId)
	{
		self::$_instance = new self($personId);
		
		$session = new Zend_Session_Namespace(__CLASS__);
		$session->instance = self::$_instance;
	}
	
	/**
	 * ���������� ���������������� ������ ������������.
	 * ���� ������ ����� �� ��� ������������� � �� ���������� � ������,
	 * �� ����� ����������� ����������.
	 *
	 * @return Rp_User
	 * @throws Exception
	 */
	public static function getInstance()
	{	
		if (empty(self::$_instance)) {
			$session = new Zend_Session_Namespace(__CLASS__);
			if (! $session->instance instanceof self) {
				throw new Exception('������ ������������ �� ����������.');
			}
			self::$_instance = $session->instance;
		}
		return self::$_instance;
	}
	
	/**
	 * ��������� ��� �� ������������� ������ ������������.
	 *
	 * @return boolean
	 */
	public static function hasInstance()
	{
		try {
			return (bool) self::getInstance();
		} catch (Exception $e) {
			return false;
		}
	}
	
	/**
	 * ���������� ������ ����������.
	 *
	 * @return Rp_Db_View_Row_Employee
	 * @throws Exception
	 */
	public function getEmployee()		// ������� ������������ � ������� �����������
	{
		if (empty(self::$_employee)) {
			$employees = new Rp_Db_View_Employees();
			$employee = $employees->findByPersonId($this->_personId)->current();
			if (empty($employee)) {
				throw new Exception('������ ���������� �� ����������.');
			}
			self::$_employee = $employee;
		}
		return self::$_employee;
	}
}