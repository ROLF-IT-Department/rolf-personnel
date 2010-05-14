<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */

/**
 * Объект таблицы бизнес-целей.
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */
class Rp_Db_Table_Ach_Tasks extends Rp_Db_Table_Abstract
{
	protected $_name = 'user_rp_ach_tasks';
	
	protected $_rowClass = 'Rp_Db_Table_Row_Ach_Task';
	
	protected $_dependentTables = array('Rp_Db_Table_Ach_Tasks_Notes');
	
	protected $_referenceMap = array(
		'Card' => array(
			'columns'       => 'card_id',
			'refTableClass' => 'Rp_Db_Table_Ach_Cards',
			'refColumns'    => 'id'
		)
	);
	
	/**
	 * Вставляет новую строку в таблицу бизнес-целей.
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
	 * Обновляет строки таблицы бизнес-целей.
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
		if (empty($data['manager_id']) && !is_numeric($data['status'])) {
			$data['manager_id'] = Rp_User::getInstance()->getPersonId();
		}
		return parent::update($data, $where);
	}
	
	/**
	 * Возвращает набор бизнес-целей карточки 
	 * с идентификатором $cardId.
	 *
	 * @param int $cardId Идентификатор карточки.
	 * 
	 * @return Rp_Db_Table_Rowset
	 */
	public function findByCardId($cardId)
	{
		$db = $this->getAdapter();
		$cardId = $db->quote($cardId);
		
		$sql = "
			SELECT *,
				date_term = CONVERT(varchar(10), date_term, 120)
			FROM
				{$this->_name}
			WHERE
				card_id = $cardId
		";
		
		return $this->_createRowset($sql);
	}
	
	public function findByCardIdAndPersonal($cardId)
	{
		$db = $this->getAdapter();
		$cardId = $db->quote($cardId);
		
		$sql = "
			SELECT *,
				date_term = CONVERT(varchar(10), date_term, 120)
			FROM
				{$this->_name}
			WHERE
				(card_id = $cardId) AND (is_personal = 1)
		";
		
		return $this->_createRowset($sql);
	}
}