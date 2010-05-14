<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */

/**
 * ����������� ������ ������������� ���� ������.
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */
abstract class Rp_Db_View_Abstract
{
	/**
	 * ������ �������� ���� ������ �������.
	 * 
	 * @var Zend_Db_Adapter_Abstract
	 */
	private static $_db = null;
	
	/**
	 * �������� ������� ������������� � ���� ������.
	 *
	 * @var string
	 */
	protected $_name = null;
	
	/**
	 * �������� ���� ���������� �����.
	 *
	 * @var string
	 */
	protected $_primary = null;
	
	/**
	 * �������� ������ �������� �����.
	 *
	 * @var string
	 */
	protected $_rowClass = 'Rp_Db_View_Row';
	
	/**
	 * �������� ������ �������� ������� �����.
	 *
	 * @var string
	 */
	protected $_rowsetClass = 'Rp_Db_View_Rowset';
	
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
	 * ���������� �������� ������ ������.
	 *
	 * @return string
	 */
	public function getRowClass()
	{
		return $this->_rowClass;
	}
	
	/**
	 * ���������� �������� ������ ������ �����.
	 *
	 * @return string
	 */
	public function getRowsetClass()
	{
		return $this->_rowsetClass;
	}
	
	/**
	 * ���������� ����� ����� �� ������������� �� ���������� �����.
	 *
	 * @param int|array $key �������� ��� ������ �������� ���������� �����.
	 * 
	 * @return Rp_Db_View_Rowset
	 * @throws Exception
	 */
	public function find($key)
	{
		if (empty($this->_primary)) {
			throw new Exception("������� ���������� ����� �� ���������.");
		}
		$key = $this->_quote($key);
		$where = $this->_primary . " IN ($key)";
		return $this->fetchAll($where);
	}
	
	/**
	 * ���������� ����� ����� �� �������������.
	 *
	 * @param string $where ������� �������.
	 * @param string $order ������� ����������.
	 * @param string $count ������������ ���������� ������������ �����.
	 * 
	 * @return Rp_Db_View_Rowset
	 */
	public function fetchAll($where = null, $order = null, $count = null)
	{
		$config = array(
			'view'     => $this,
			'data'     => $this->_fetch($where, $order, $count),
			'rowClass' => $this->_rowClass
		);
		return new $this->_rowsetClass($config);
	}
	
	/**
	 * ���������� ���� ������ �� �������������.
	 *
	 * @param string $where ������� �������.
	 * @param string $order ������� ����������.
	 * 
	 * @return Rp_Db_View_Row ��� null, ���� ������ �� �������.
	 */
	public function fetchRow($where = null, $order = null)
	{
		$rows = $this->_fetch($where, $order, 1);
		if (empty($rows)) {
			return null;
		}
		$config = array(
			'view' => $this,
			'data' => $rows[0]
		);
		return new $this->_rowClass($config);
	}
	
	/**
	 * ����������� �������� $value � ������ � ��������������� ��������� 
	 * ��� ����������� ������������� � sql-������������.
	 *
	 * @param mixed $value ��������.
	 * @param mixed $type  ��� ������.
	 * 
	 * @return string
	 */
	protected function _quote($value, $type = null)
	{
		if (is_array($value) && count($value) == 0) {
			return "''";
		}
		return $this->getAdapter()->quote($value, $type);
	}
	
	/**
	 * ���������� ������ ����� �� �������������.
	 *
	 * @param string $where ������� �������.
	 * @param string $order ������� ����������.
	 * @param string $count ������������ ���������� ������������ �����.
	 * 
	 * @return array
	 */
	protected function _fetch($where = null, $order = null, $count = null)
	{
		if ($where !== null) {
			$where = 'WHERE ' . $where;
		}
		if ($order !== null) {
			$order = 'ORDER BY ' . $order;
		}
		if ($count !== null) {
			$count = 'TOP ' . $count;
		}
		
		$sql = "SELECT $count * FROM {$this->_name} $where $order";
		return $this->getAdapter()->fetchAll($sql);
	}
	
	/**
	 * ���������� ������ �������� ������� �� �������������.
	 *
	 * @param string $column �������� �������.
	 * @param string $where  ������� �������.
	 * 
	 * @return array
	 */
	protected function _fetchCol($column, $where = null)
	{
		if ($where !== null) {
			$where = 'WHERE ' . $where;
		}
		$sql = "SELECT $column FROM {$this->_name} $where";
		return $this->getAdapter()->fetchCol($sql);
	}
	
	/**
	 * ���������� ������ ��� ����-�������� �� �������������.
	 * �������� ������ ������� �� ���� ���������� ����� �������������, 
	 * � ���������� �������� - �� ���� $column.
	 *
	 * @param string $column �������� ����.
	 * 
	 * @param mixed  $where  ������� �������.
	 * ���� $where �������� ������ ��� ��������, �� ����� �������� ������, 
	 * �������� ���������� ����� ������� ����� ������ �� �������� $where.
	 * 
	 * @param string $order  ������� ����������.
	 * 
	 * @return array
	 * @throws Exception
	 */
	protected function _fetchPairs($column, $where = null, $order = null)
	{
		if (empty($this->_primary)) {
			throw new Exception("������� ���������� ����� �� ���������.");
		}
		if ($where !== null) {
			if (is_array($where) || is_numeric($where)) {
				$where = $this->_quote($where);
				$where = "WHERE {$this->_primary} IN ($where)";
			} else {
				$where = 'WHERE ' . $where;
			}
		}
		if ($order !== null) {
			$order = 'ORDER BY ' . $order;
		}
		
		$sql = "SELECT {$this->_primary}, $column FROM {$this->_name} $where $order";
		
		return $this->getAdapter()->fetchPairs($sql);
	}
}