<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */

/**
 * Объект таблицы справочника компетенций.
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */
class Rp_Db_Table_Ach_Competences extends Rp_Db_Table_Abstract 
{
	protected $_name = 'user_rp_ach_competences';
	
	protected $_dependentTables = array('Rp_Db_Table_Ach_Cards_Competences', 'Rp_Db_Table_Ach_Competences_Notes');
}