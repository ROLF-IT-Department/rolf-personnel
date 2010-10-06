<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */

/**
 * Объект представления атрибутов штатных сотрудников.
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */
class Rp_Db_View_Employees_Attribs extends Rp_Db_View_Abstract
{	
	protected $_name = 'user_rp_employees_attribs';
	
	protected $_primary = 'person_id';
}