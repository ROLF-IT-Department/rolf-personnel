<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */

/**
 * ������ ������������� ��������.
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */
class Rp_Db_View_Companies extends Rp_Db_View_Abstract
{	
	protected $_name = 'user_rp_companies';
	
	protected $_primary = 'id';
	
	/**
	 * ���������� ������ �������� ��������.
	 *
	 * @param int|array $id    ������������� ��� ������ ��������������� ��������.
	 * @param string    $order ������� ����������.
	 * 
	 * @return array
	 */
	public function fetchNames($id = null, $order = 'name')
	{
		return $this->_fetchPairs('name', $id, $order);
	}
}