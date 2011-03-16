<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */

/**
 * ����������� ������ ������� ���� ������.
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */
abstract class Rp_Db_Table_Abstract extends Zend_Db_Table_Abstract
{
	/**
	 * �������� ������ �������� �����.
	 *
	 * @var string
	 */
	protected $_rowClass = 'Rp_Db_Table_Row';

	/**
	 * �������� ������ �������� ������� �����.
	 *
	 * @var string
	 */
	protected $_rowsetClass = 'Rp_Db_Table_Rowset';

	/**
	 * �����������.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct(Rp::getDbAdapter());
	}

	/**
	 * ��������� ����� ������ � �������.
	 * ��� �������� '' (������ ������) � ������� $data ���������� �� null.
	 *
	 * @param  array $data ������ ��� "���� => ��������".
	 * @return mixed �������� ���������� ����� ����������� ������.
	 */
	public function insert(array $data)
	{
		if ($keys = array_keys($data, '')) {
			foreach ($keys as $key) {
				$data[$key] = null;
			}
		}
		return parent::insert($data);
	}

	/**
	 * ��������� ������ �������.
	 * ��� �������� '' (������ ������) � ������� $data ���������� �� null.
	 *
	 * @param array        $data  ������ ��� "���� => ��������".
	 *
	 * @param array|string $where ������� ������ �����.
	 * ���� $where �������� ������, �� ����� ��������� ������,
	 * �������� ���������� ����� ������� ����� $where.
	 *
	 * @return int ���������� ����������� �����.
	 */
	public function update(array $data, $where)
	{
		if ($keys = array_keys($data, '')) {
			foreach ($keys as $key) {
				$data[$key] = null;
			}
		}
		if (is_numeric($where)) {
			$primary = (array) $this->_primary;
			$where   = reset($primary) . ' = ' . $where;
		}
		return parent::update($data, $where);
	}


	/**
	 * ������� ����� ����� �� �������.
	 *
	 * @param string $sql ������ ������� ��� ��������� ������.
	 *
	 * @return Rp_Db_Table_Rowset
	 */
	protected function _createRowset($sql)
	{
		$config = array(
			'table'    => $this,
			'data'     => $this->getAdapter()->fetchAll($sql),
			'rowClass' => $this->_rowClass,
			'stored'   => true
		);
		return new $this->_rowsetClass($config);
	}

	/**
	 * ���������� ������ ��� ����-�������� �� �������.
	 * �������� ������ ������� �� ���� ���������� ����� �������,
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
	 */
	protected function _fetchPairs($column, $where = null, $order = null)
	{
		$primary = (array) $this->_primary;
		$primary = reset($primary);
		$db = $this->getAdapter();

		if ($where !== null) {
			if (is_array($where) || is_numeric($where)) {
				if (count($where) == 0) {
					$where = null;
				}
				$where = $db->quote($where);
				$where = "WHERE $primary IN ($where)";
			} else {
				$where = 'WHERE ' . $where;
			}
		}
		if ($order !== null) {
			$order = 'ORDER BY ' . $order;
		}

		$sql = "SELECT $primary, $column FROM {$this->_name} $where $order";
		return $db->fetchPairs($sql);
	}

	protected function _fetchAssoc($column_key, $column_value)
	{
		$db = $this->getAdapter();
		$sql = "SELECT $column_key, $column_value FROM {$this->_name}";
		return $db->fetchAssoc($sql);
	}


}