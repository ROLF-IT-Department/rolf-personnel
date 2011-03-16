<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */

/**
 * Абстрактный объект таблицы базы данных.
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */
abstract class Rp_Db_Table_Abstract extends Zend_Db_Table_Abstract
{
	/**
	 * Название класса объектов строк.
	 *
	 * @var string
	 */
	protected $_rowClass = 'Rp_Db_Table_Row';

	/**
	 * Название класса объектов наборов строк.
	 *
	 * @var string
	 */
	protected $_rowsetClass = 'Rp_Db_Table_Rowset';

	/**
	 * Конструктор.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct(Rp::getDbAdapter());
	}

	/**
	 * Вставляет новую строку в таблицу.
	 * Все значения '' (пустая строка) в массиве $data заменяются на null.
	 *
	 * @param  array $data Массив пар "поле => значение".
	 * @return mixed Значение первичного ключа вставленной строки.
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
	 * Обновляет строки таблицы.
	 * Все значения '' (пустая строка) в массиве $data заменяются на null.
	 *
	 * @param array        $data  Массив пар "поле => значение".
	 *
	 * @param array|string $where Условие отбора строк.
	 * Если $where является числом, то будет обновлена строка,
	 * значение первичного ключа которой равно $where.
	 *
	 * @return int Количество обновленных строк.
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
	 * Создает набор строк из таблицы.
	 *
	 * @param string $sql Строка запроса для получения данных.
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
	 * Возвращает массив пар ключ-значение из таблицы.
	 * Значения ключей берутся из поля первичного ключа таблицы,
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