<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */

/**
 * Объект таблицы статистики
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */
class Rp_Db_Table_Logon extends Rp_Db_Table_Abstract
{
	protected $_name = 'user_rp_logon';
	
	
	public function insert(array $data)
	{
		return parent::insert($data);
	}
	
	public function updateLogoff(array $data, $sid)
	{
		$where = "session_id = " . '\'' . $sid . '\'';	// условием является id установленной сессии
		return parent::update($data, $where);
	}
}