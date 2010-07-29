<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */

/**
 * ������ ������������� ���������� ���.
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */
class Rp_Db_View_Persons extends Rp_Db_View_Abstract
{	
	protected $_name = 'user_rp_persons_PM';
	
	protected $_primary = 'id';
	
	protected $_rowClass = 'Rp_Db_View_Row_Person';
	
	/**
	 * ���������� ������ ������ ���� ���. ���.
	 *
	 * @param int|array $id    ������������� ��� ������ ��������������� ���. ���.
	 * @param string    $order ������� ����������.
	 * 
	 * @return array
	 */
	public function fetchFullnames($id = null, $order = 'fullname')
	{
		return $this->_fetchPairs('fullname', $id, $order);
	}
}