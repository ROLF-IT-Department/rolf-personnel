<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */

/**
 * ������ ������������� ������� �����������.
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */
class Rp_Db_View_Employees extends Rp_Db_View_Abstract
{	
	protected $_name = 'user_rp_employees_PM';
	
	protected $_primary = 'person_id';
	
	protected $_rowClass = 'Rp_Db_View_Row_Employee';
	
	/**
	 * ���������� ����� ������� �����������, ��������� 
	 * �� �������� �������������� ���. ����.
	 * ���� ���. ���� ������������� ��������� �����������, 
	 * �� ����� ���������� ��� ������. ���������� ����� ������� 
	 * ������������ ������� �� �������� �������������� ���.����, ����� 
	 * �� �������� ���� ���������� �� ��������.
	 *
	 * @param int|array $personId ������������� ��� ������ ��������������� ���. ���.
	 * 
	 * @return Rp_Db_View_Rowset
	 */
	public function findByPersonId($personId)
	{
		$personId = $this->_quote($personId);
		$where = "person_id IN ($personId)";
		$order = 'person_id, dismissal_date DESC';
		
		return $this->fetchAll($where, $order);
	}
}