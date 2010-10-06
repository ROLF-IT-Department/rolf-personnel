<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */

/**
 * Объект представления подразделений.
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */
class Rp_Db_View_Departments extends Rp_Db_View_Abstract
{	
	protected $_name = 'user_rp_departments';
	
	protected $_primary = 'id';
	
	/**
	 * Возвращает массив названий подразделений.
	 *
	 * @param int|array $id    Идентификатор или массив идентификаторов подразделений.
	 * @param string    $order Условие сортировки.
	 * 
	 * @return array
	 */
	public function fetchNames($id = null, $order = 'name')
	{
		return $this->_fetchPairs('name', $id, $order);
	}
}