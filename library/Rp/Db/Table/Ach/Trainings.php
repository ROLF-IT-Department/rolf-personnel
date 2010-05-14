<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */

/**
 * ������ ������� ����� ����������������� ��������.
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */
class Rp_Db_Table_Ach_Trainings extends Rp_Db_Table_Abstract 
{
	protected $_name = 'user_rp_ach_trainings';
	
	protected $_rowClass = 'Rp_Db_Table_Row_Ach_Training';
	
	protected $_dependentTables = array('Rp_Db_Table_Ach_Trainings_Notes');
	
	protected $_referenceMap = array(
		'Card' => array(
			'columns'       => 'card_id',
			'refTableClass' => 'Rp_Db_Table_Ach_Cards',
			'refColumns'    => 'id'
		)
	);
	
	/**
	 * ��������� ����� ������ � ������� ����� ����. ��������.
	 * ��� ���������� � ������� ������ �������� �������������� 
	 * ������������ ����� � �������� ����� �������� ������������� 
	 * �������� �������������� ���. ���� ������������.
	 *
	 * @param array $data ������ ��� "���� => ��������".
	 * 
	 * @return mixed �������� ���������� ����� ����������� ������.
	 */
	public function insert(array $data)
	{
		if (empty($data['manager_id'])) {
			$data['manager_id'] = Rp_User::getInstance()->getPersonId();
		}
		return parent::insert($data);
	}
	
	/**
	 * ��������� ������ ������� ����� ����. ��������.
	 * ��� ���������� � ������� ������ �������� �������������� 
	 * ������������ ����� � �������� ����� �������� ������������� 
	 * �������� �������������� ���. ���� ������������ 
	 * (��� ��������������� �����).
	 *
	 * @param array        $data  ������ ��� "���� => ��������".
	 * 
	 * @param array|string $where ������� ������ �����.
	 * ���� $where �������� ������, �� ����� ��������� ������, 
	 * �������� ���������� ����� ������� ����� $where.
	 * 
	 * @return int ���������� ����������� �����.
	 */
	public function update(array $data, $where)
	{
		/*
		if (empty($data['manager_id']) && !is_numeric($data['status'])) {
			$data['manager_id'] = Rp_User::getInstance()->getPersonId();
		}
		*/
		return parent::update($data, $where);
	}
	
	/**
	 * ���������� ����� ����� ����. �������� �������� 
	 * � ��������������� $cardId.
	 *
	 * @param int $cardId ������������� ��������.
	 * 
	 * @return Rp_Db_Table_Rowset
	 */
	public function findByCardId($cardId)
	{
		$where = '(card_id = ' . $cardId . ') AND (is_personal IS NULL)';
		
		return $this->fetchAll($where);
	}
	
	
	public function findByCardIdAndPersonal($cardId)
	{
		$where = '(card_id = ' . $cardId . ') AND (is_personal IS NOT NULL)';
		
		return $this->fetchAll($where);
	}
}