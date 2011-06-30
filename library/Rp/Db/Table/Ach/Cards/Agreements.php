<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */

/**
 * Объект таблицы справочника статусов карточек достижений.
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */
class Rp_Db_Table_Ach_Cards_Agreements extends Rp_Db_Table_Abstract
{
	protected $_name = 'user_rp_ach_cards_agreements';

	/**
	 * Возвращает информацию по согласованию карт сотрудника за указанный период
	 *
	 * @param integer $person_id
	 * @param integer $period_year
	 * @return Zend_Db_Table_Row_Abstract
	 */
	public function cards_agreement($person_id, $period_year)
	{
		$where  = 'person_id = '.(int) $person_id;
		$where .= ' AND period = ' . $period_year;
		return $this->fetchRow($where);
	}

	/**
	 * Создаёт новую запись (или редактирует имеющуюся) согласования общего рейтинга всех карт
	 *
	 * @param  $person_id
	 * @param  $period_year
	 * @param int $rating_id
	 * @param int $confirmed
	 * @param null $confirmation_id
	 * @return int
	 */
	public function card_agreement($person_id, $period_year, $rating_id = 0, $confirmed = 0, $confirmation_id = NULL)
	{
		$new_data  = array(
			'person_id'    => (int) $person_id,
			'period'       => $period_year,
			'rtg_total_id' => (int) $rating_id,
			'confirmed'    => $confirmed,
		);

		if($confirmation_id === NULL)
		{
			$confirmation_id = $this->insert($new_data);
		}
		else
		{
			$this->update($new_data, $confirmation_id);
		}

		return $this->find($confirmation_id)->count();
	}
}