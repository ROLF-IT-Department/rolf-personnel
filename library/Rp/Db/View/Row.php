<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */

/**
 * ������ ������ �������������.
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */
class Rp_Db_View_Row 
{
	/**
	 * ������ �������������.
	 *
	 * @var Rp_Db_View_Abstract
	 */
	protected $_view = null;
	
	/**
	 * ������ ������.
	 *
	 * @var array
	 */
	protected $_data = array();
	
	/**
	 * �����������.
	 *
	 * @param array $config ���������������� ������.
	 * ������������ ��������� ���������:
	 * - view - ������ �������������;
	 * - data - ������ ������.
	 * 
	 * @return void
	 */
	public function __construct(array $config)
	{
		if (isset($config['view'])) {
			$this->_view = $config['view'];
		}
		if (isset($config['data'])) {
			$this->_data = $config['data'];	
		}
	}
	
	/**
	 * ���������� �������� ���� $column ������.
	 *
	 * @param  string $column �������� ����.
	 * @return string
	 */
	public function __get($column)
	{
		if (!array_key_exists($column, $this->_data)) {
			throw new Exception("���� \"$column\" �� ������� � ������.");
		}
		return $this->_data[$column];
	}
	
	/**
	 * ���������� ���������� ��� ������� ���������� �������� ���� ������.
	 *
	 * @param string $column �������� ����.
	 * @param mixed  $value  �������� ����.
	 * 
	 * @return void
	 * @throws Exception
	 */
	public function __set($column, $value)
	{
		throw new Exception("������ ������ ������������� �� ������������ ��������� �������� �����.");
	}
	
	/**
	 * ��������� ������� ���� $column � ������.
	 *
	 * @param  string $column �������� ����.
	 * @return boolean
	 */
	public function __isset($column)
	{
		return array_key_exists($column, $this->_data);
	}
	
	/**
	 * ���������� ������ �������������.
	 *
	 * @return Rp_Db_View_Abstract
	 */
	public function getView()
	{
		return $this->_view;
	}
	
	/**
	 * ���������� ������ � ���� �������.
	 *
	 * @return array
	 */
	public function toArray()
	{
		return $this->_data;
	}
}