<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */

/**
 * Объект таблицы справочника рейтингов.
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */
class Rp_Db_Table_Ach_Ratings extends Rp_Db_Table_Abstract
{
	protected $_name = 'user_rp_ach_ratings';
	
	/**
	 * Возвращает массив названий рейтингов.
	 * В дополнении к табличным значениям, метод добавляет 
	 * в возвращаемый массив элемент с пустым значением ключа 
	 * (соответствует NULL значениям в базе данных).
	 *
	 * @return array
	 */
	public function fetchNames()
	{
		$names  = array('' => '-');
		$names += $this->_fetchPairs('name', null, 'sort');
		
		return $names;
	}
	
	public function fetchWeights()
	{
		$names = array();	
		$names += $this->_fetchAssoc('id', 'weight');
		
		return $names;
	}
	
	public function fetchNameWeights()
	{
		$names = array();	
		$names += $this->_fetchAssoc('name', 'weight');
		
		return $names;
	}
}