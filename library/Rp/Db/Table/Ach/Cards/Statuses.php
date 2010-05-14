<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */

/**
 * Объект таблицы справочника статусов карточек достижений.
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */
class Rp_Db_Table_Ach_Cards_Statuses extends Rp_Db_Table_Abstract 
{
	protected $_name = 'user_rp_ach_cards_statuses';
	
	/**
	 * Возвращает массив названий статусов.
	 *
	 * @return array
	 */
	public function fetchNames()
	{
		return $this->_fetchPairs('name', null, 'sort');
	}
}