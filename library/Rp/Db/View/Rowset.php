<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */

/**
 * ������ ������ ����� �������������.
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */
class Rp_Db_View_Rowset implements Iterator, Countable 
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
	 * ������ �����.
	 *
	 * @var array
	 */
	protected $_rows = array();
	
	/**
	 * �������� ������ �����.
	 *
	 * @var string
	 */
	protected $_rowClass = 'Rp_Db_View_Row';
	
	/**
	 * ��������� ������� �����.
	 *
	 * @var int
	 */
	protected $_pointer = 0;
	
	/**
	 * ���������� ����� � ������.
	 *
	 * @var int
	 */
	protected $_count = null;
	
	/**
	 * �����������.
	 *
	 * @param array $config ���������������� ������.
	 * ������������ ��������� ���������:
	 * - view     - ������ �������������;
	 * - data     - ������ ������;
	 * - rowClass - �������� ������ �����.
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
	 * ���������� ������ �������������.
	 *
	 * @return Rp_Db_View_Abstract
	 */
	public function getView()
	{
		return $this->_view;
	}
	
	/**
	 * ���������� ������ �������� ���� $column ���� �����.
	 *
	 * @param string $column �������� ����.
	 * 
	 * @return array
	 * @throws Exception
	 */
	public function getCol($column)
	{
		$values = array();
		foreach ($this->_data as $row) {
			if (!array_key_exists($column, $row)) {
				throw new Exception("���� \"$column\" �� ������� � ������.");
			}
			$values[] = $row[$column];
		}
		return $values;
	}
	
	/**
	 * ������������� ��������� �� ������ ������.
	 * 
	 * @return void
	 */
	public function rewind()
	{
		$this->_pointer = 0;
	}
	
	/**
	 * ���������� ������� ������ �� ������.
	 *
	 * @return Rp_Db_View_Row ��� null, ���� ��������� ����� �� ������� ������.
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
	 * ���������� ���� ������� ������ � ������.
	 *
	 * @return int
	 */
	public function key()
	{
		return $this->_pointer;
	}
	
	/**
	 * ���������� ��������� �� ��������� ������.
	 * 
	 * @return void
	 */
	public function next()
	{
		++$this->_pointer;
	}
	
	/**
	 * ��������� �� ����� �� ��������� �� ������� ������ �����.
	 *
	 * @return boolean
	 */
	public function valid()
	{
		return $this->_pointer < $this->_count;
	}
	
	/**
	 * ���������� ���������� ����� � ������.
	 *
	 * @return int
	 */
	public function count()
	{
		return $this->_count;
	}
	
	/**
	 * ��������� ������� ����� � ������.
	 *
	 * @return boolean
	 */
	public function exists()
	{
		return $this->_count > 0;
	}
	
	/**
	 * ���������� ����� ����� � ���� �������.
	 *
	 * @return array
	 */
	public function toArray()
	{
		return $this->_data;
	}
}