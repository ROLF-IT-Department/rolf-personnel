<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */

/**
 * Объект набора строк представления.
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */
class Rp_Db_View_Rowset implements Iterator, Countable 
{
	/**
	 * Объект представления.
	 *
	 * @var Rp_Db_View_Abstract
	 */
	protected $_view = null;
	
	/**
	 * Массив данных.
	 *
	 * @var array
	 */
	protected $_data = array();
	
	/**
	 * Массив строк.
	 *
	 * @var array
	 */
	protected $_rows = array();
	
	/**
	 * Название класса строк.
	 *
	 * @var string
	 */
	protected $_rowClass = 'Rp_Db_View_Row';
	
	/**
	 * Указатель массива строк.
	 *
	 * @var int
	 */
	protected $_pointer = 0;
	
	/**
	 * Количество строк в наборе.
	 *
	 * @var int
	 */
	protected $_count = null;
	
	/**
	 * Конструктор.
	 *
	 * @param array $config Конфигурационные данные.
	 * Поддерживает следующие параметры:
	 * - view     - объект представления;
	 * - data     - массив данных;
	 * - rowClass - название класса строк.
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
		if (isset($config['rowClass'])) {
			$this->_rowClass = $config['rowClass'];
		}
		
		$this->_count = count($this->_data);
	}
	
	/**
	 * Возвращает объект представления.
	 *
	 * @return Rp_Db_View_Abstract
	 */
	public function getView()
	{
		return $this->_view;
	}
	
	/**
	 * Возвращает массив значений поля $column всех строк.
	 *
	 * @param string $column Название поля.
	 * 
	 * @return array
	 * @throws Exception
	 */
	public function getCol($column)
	{
		$values = array();
		foreach ($this->_data as $row) {
			if (!array_key_exists($column, $row)) {
				throw new Exception("Поле \"$column\" не найдено в строке.");
			}
			$values[] = $row[$column];
		}
		return $values;
	}
	
	/**
	 * Устанавливает указатель на первую строку.
	 * 
	 * @return void
	 */
	public function rewind()
	{
		$this->_pointer = 0;
	}
	
	/**
	 * Возвращает текущую строку из набора.
	 *
	 * @return Rp_Db_View_Row или null, если указатель вышел за пределы набора.
	 */
	public function current()
	{
		if ($this->valid() == false) {
			return null;
		}
		
		if (empty($this->_rows[$this->_pointer])) {
			$this->_rows[$this->_pointer] = new $this->_rowClass(
				array(
					'view' => $this->_view,
					'data' => $this->_data[$this->_pointer]
				)
			);
		}
		return $this->_rows[$this->_pointer];
	}
	
	/**
	 * Возвращает ключ текущей строки в наборе.
	 *
	 * @return int
	 */
	public function key()
	{
		return $this->_pointer;
	}
	
	/**
	 * Перемещает указатель на следующую строку.
	 * 
	 * @return void
	 */
	public function next()
	{
		++$this->_pointer;
	}
	
	/**
	 * Проверяет не вышел ли указатель за пределы набора строк.
	 *
	 * @return boolean
	 */
	public function valid()
	{
		return $this->_pointer < $this->_count;
	}
	
	/**
	 * Возвращает количество строк в наборе.
	 *
	 * @return int
	 */
	public function count()
	{
		return $this->_count;
	}
	
	/**
	 * Проверяет наличие строк в наборе.
	 *
	 * @return boolean
	 */
	public function exists()
	{
		return $this->_count > 0;
	}
	
	/**
	 * Возвращает набор строк в виде массива.
	 *
	 * @return array
	 */
	public function toArray()
	{
		return $this->_data;
	}
}