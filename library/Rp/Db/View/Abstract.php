<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */

/**
 * Абстрактный объект представления базы данных.
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */
abstract class Rp_Db_View_Abstract
{
	/**
	 * Объект адаптера базы данных системы.
	 *
	 * @var Zend_Db_Adapter_Abstract
	 */
	private static $_db = null;

	/**
	 * Название объекта представления в базе данных.
	 *
	 * @var string
	 */
	protected $_name = null;

	/**
	 * Название поля первичного ключа.
	 *
	 * @var string
	 */
	protected $_primary = null;

	/**
	 * Название класса объектов строк.
	 *
	 * @var string
	 */
	protected $_rowClass = 'Rp_Db_View_Row';

	/**
	 * Название класса объектов наборов строк.
	 *
	 * @var string
	 */
	protected $_rowsetClass = 'Rp_Db_View_Rowset';

	/**
	 * Конструктор.
	 *
	 * @return void
	 */
	public function __construct()
	{
	}

	/**
	 * Возвращает объект адаптера базы данных системы.
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
	 * Возвращает название класса строки.
	 *
	 * @return string
	 */
	public function getRowClass()
	{
		return $this->_rowClass;
	}

	/**
	 * Возвращает название класса набора строк.
	 *
	 * @return string
	 */
	public function getRowsetClass()
	{
		return $this->_rowsetClass;
	}

	/**
	 * Возвращает набор строк из представления по первичному ключу.
	 *
	 * @param int|array $key Значение или массив значений первичного ключа.
	 *
	 * @return Rp_Db_View_Rowset
	 * @throws Exception
	 */
	public function find($key)
	{
		if (empty($this->_primary)) {
			throw new Exception("Столбец первичного ключа не определен.");
		}
		$key = $this->_quote($key);
		$where = $this->_primary . " IN ($key)";
		return $this->fetchAll($where);
	}

	/**
	 * Возвращает набор строк из представления.
	 *
	 * @param string $where Условие выборки.
	 * @param string $order Условие сортировки.
	 * @param string $count Максимальное количество возвращаемых строк.
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
	 * Возвращает одну строку из представления.
	 *
	 * @param string $where Условие выборки.
	 * @param string $order Условие сортировки.
	 *
	 * @return Rp_Db_View_Row или null, если строка не найдена.
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
	 * Преобразует значение $value в строку с экранированными символами
	 * для безопасного использования в sql-конструкциях.
	 *
	 * @param mixed $value Значение.
	 * @param mixed $type  Тип данных.
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
	 * Возвращает массив строк из представления.
	 *
	 * @param string $where Условие выборки.
	 * @param string $order Условие сортировки.
	 * @param string $count Максимальное количество возвращаемых строк.
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
	 * Возвращает массив значений колонки из представления.
	 *
	 * @param string $column Название колонки.
	 * @param string $where  Условие выборки.
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
	 * Возвращает массив пар ключ-значение из представления.
	 * Значения ключей берутся из поля первичного ключа представления,
	 * а собственно значения - из поля $column.
	 *
	 * @param string $column Название поля.
	 *
	 * @param mixed  $where  Условие выборки.
	 * Если $where является числом или массивом, то будут отобраны строки,
	 * значение первичного ключа которых равно одному из значений $where.
	 *
	 * @param string $order  Условие сортировки.
	 *
	 * @return array
	 * @throws Exception
	 */
	protected function _fetchPairs($column, $where = null, $order = null)
	{
		if (empty($this->_primary)) {
			throw new Exception("Столбец первичного ключа не определен.");
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