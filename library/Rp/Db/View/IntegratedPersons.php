<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */

/**
 * ������ ������������� ����������� �����������.
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */
class Rp_Db_View_IntegratedPersons extends Rp_Db_View_Abstract
{
	protected $_name = 'user_rp_persons_integrated_PM';

	protected $_primary = 'person_id';

	/**
	 * ���������� ������ �������� ����������.
	 *
	 * @param int|array $id    ������������� ��� ������ ��������������� �����������.
	 * @param string    $order ������� ����������.
	 *
	 * @return array
	 */
	public function fetchRefID($person_id = null)
	{
		return ($person_id) ? $this->_fetchCol('ref_id', 'person_id = ' . $person_id) : NULL;
	}

	public function fetchPersonID($ref_id = null)
	{
		return ($ref_id) ? $this->_fetchCol('person_id', 'ref_id = ' . $ref_id) : NULL;
	}

}