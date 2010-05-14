<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */

/**
 * ������ ������� ����������� �������.
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */
class Rp_Db_Table_Months extends Rp_Db_Table_Abstract
{
	protected $_name = 'user_rp_months';
	
	/**
	 * ���������� ������ �������� �������.
	 *
	 * @return array
	 */
	public function fetchNames()
	{
		return $this->_fetchPairs('name', null, 'sort');
	}
}