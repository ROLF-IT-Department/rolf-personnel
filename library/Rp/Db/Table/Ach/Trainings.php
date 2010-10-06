<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */

/**
 * Объект таблицы целей профессионального развития.
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
	 * Вставляет новую строку в таблицу целей проф. развития.
	 * При отсутствии в массиве данных значения идентификатора 
	 * руководителя метод в качестве этого значения устанавливает 
	 * значение идентификатора физ. лица пользователя.
	 *
	 * @param array $data Массив пар "поле => значение".
	 * 
	 * @return mixed Значение первичного ключа вставленной строки.
	 */
	public function insert(array $data)
	{
		if (empty($data['manager_id'])) {
			$data['manager_id'] = Rp_User::getInstance()->getPersonId();
		}
		return parent::insert($data);
	}
	
	/**
	 * Обновляет строки таблицы целей проф. развития.
	 * При отсутствии в массиве данных значения идентификатора 
	 * руководителя метод в качестве этого значения устанавливает 
	 * значение идентификатора физ. лица пользователя 
	 * (для несогласованных строк).
	 *
	 * @param array        $data  Массив пар "поле => значение".
	 * 
	 * @param array|string $where Условие отбора строк.
	 * Если $where является числом, то будет обновлена строка, 
	 * значение первичного ключа которой равно $where.
	 * 
	 * @return int Количество обновленных строк.
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
	 * Возвращает набор целей проф. развития карточки 
	 * с идентификатором $cardId.
	 *
	 * @param int $cardId Идентификатор карточки.
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