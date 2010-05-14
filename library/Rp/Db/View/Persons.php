<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */

/**
 * Объект представления физических лиц.
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */
class Rp_Db_View_Persons extends Rp_Db_View_Abstract
{	
	protected $_name = 'user_rp_persons';
	
	protected $_primary = 'id';
	
	protected $_rowClass = 'Rp_Db_View_Row_Person';
	
	/**
	 * Возвращает массив полных имен физ. лиц.
	 *
	 * @param int|array $id    Идентификатор или массив идентификаторов физ. лиц.
	 * @param string    $order Условие сортировки.
	 * 
	 * @return array
	 */
	public function fetchFullnames($id = null, $order = 'fullname')
	{
		return $this->_fetchPairs('fullname', $id, $order);
	}
}