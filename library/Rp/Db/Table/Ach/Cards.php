<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */

/**
 * Объект таблицы карточек достижений.
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */
class Rp_Db_Table_Ach_Cards extends Rp_Db_Table_Abstract
{
	protected $_name = 'user_rp_ach_cards';

	protected $_rowClass = 'Rp_Db_Table_Row_Ach_Card';

	protected $_dependentTables = array(
		'Rp_Db_Table_Ach_Tasks',
		'Rp_Db_Table_Ach_Trainings',
		'Rp_Db_Table_Ach_Cards_Competences',
	);

	/**
	 * Создает новую карточку достижений.
	 *
	 * @param int $personId Идентификатор физ. лица.
	 * @param int $period   Период карточки.
	 *
	 * @return Rp_Db_Table_Row_Ach_Card
	 * @throws Exception
	 */
	public function createCard($personId, $period)
	{
		if (!is_numeric($personId)) {
			throw new Exception('Идентификатор физ. лица должен быть целым числом.');
		}
		if (!is_numeric($period)) {
			throw new Exception('Период карточки должен быть целым числом.');
		}

		$cardId = $this->insert(array(
			'person_id' => $personId,
			'period'    => $period
		));
		return $this->find($cardId)->current();
	}

	/**
	 * Возвращает карточку, найденную по значениям
	 * идентификатора физ. лица и периода карточки.
	 * Если карточка не будет найдена, то метод создаст
	 * новую карточку с указанными значениями $personId и $period.
	 *
	 * @param int $personId Идентификатор физ. лица.
	 * @param int $period   Период карточки.
	 *
	 * @return Rp_Db_Table_Row_Ach_Card
	 */
	public function findByPersonIdAndPeriod($personId, $period)
	{
		$db = $this->getAdapter();

		$where  = 'person_id = ' . $db->quote($personId);
		$where .= ' AND period = ' . $db->quote($period);

		$rowCard = $this->fetchRow($where);
		if (empty($rowCard)) {
			$rowCard = $this->createCard($personId, $period);
		}
		return $rowCard;
	}
}