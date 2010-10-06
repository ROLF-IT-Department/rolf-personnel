<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Procedure
 */

/**
 * Абстрактный объект хранимой процедуры базы данных.
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Procedure
 */
abstract class Rp_Db_Procedure_Abstract
{
	/**
	 * Объект адаптера базы данных системы.
	 * 
	 * @var Zend_Db_Adapter_Abstract
	 */
	private static $_db = null;
	
	/**
	 * Название процедуры в базе данных.
	 *
	 * @var string
	 */
	protected $_name = null;
	
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
	 * Выполняет хранимую процедуру.
	 *
	 * @param string $params Параметры процедуры.
	 * 
	 * @return void
	 * @throws Exception
	 */
	public function exec($params = null)
	{
		if (empty($this->_name)) {
			throw new Exception('Название процедуры не определено.');
		}
		
		$sql = "exec {$this->_name} $params";
		$this->getAdapter()->query($sql);
	}
}