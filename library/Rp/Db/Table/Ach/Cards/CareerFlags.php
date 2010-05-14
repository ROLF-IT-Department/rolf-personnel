<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */

/**
 * ������ ������� ����������� ������ ������������ �� �������� �������.
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */
class Rp_Db_Table_Ach_Cards_CareerFlags extends Rp_Db_Table_Abstract 
{
	protected $_name = 'user_rp_ach_cards_career_flags';
	
	/**
	 * ���������� ������ �������� ������.
	 * � ���������� � ��������� ���������, ����� ��������� 
	 * � ������������ ������ ������� � ������ ��������� ����� 
	 * (������������� NULL ��������� � ���� ������).
	 *
	 * @return array
	 */
	public function fetchNames()
	{
		$names  = array('' => '�� �������/Not Selected');
		$names += $this->_fetchPairs('name', null, 'sort, name');
		
		return $names;
	}
	
}