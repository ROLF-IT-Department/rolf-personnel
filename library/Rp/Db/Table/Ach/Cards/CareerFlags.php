<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */

/**
 * Объект таблицы справочника флагов рекомендаций по развитию карьеры.
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */
class Rp_Db_Table_Ach_Cards_CareerFlags extends Rp_Db_Table_Abstract 
{
	protected $_name = 'user_rp_ach_cards_career_flags';
	
	/**
	 * Возвращает массив названий флагов.
	 * В дополнении к табличным значениям, метод добавляет 
	 * в возвращаемый массив элемент с пустым значением ключа 
	 * (соответствует NULL значениям в базе данных).
	 *
	 * @return array
	 */
	public function fetchNames()
	{
		$names  = array('' => 'Не выбрано/Not Selected');
		$names += $this->_fetchPairs('name', null, 'sort, name');
		
		return $names;
	}
	
}