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
	public function createCard($personId, $period, $period_start = NULL, $period_end = NULL, $card_creator_id = NULL)
	{
		if (!is_numeric($personId)) {
			throw new Exception('Идентификатор физ. лица должен быть целым числом.');
		}
		if (!is_numeric($period)) {
			throw new Exception('Период карточки должен быть целым числом.');
		}

		$cardId = $this->insert(array(
			'person_id'       => $personId,
			'period'          => $period,
			'period_start'    => $period_start,
			'period_end'      => $period_end,
			'card_creator_id' => $card_creator_id,
		));

		return $this->find($cardId)->current();
	}

	public function cut_the_card($person_id, $card_id,  $period, $period_start = NULL, $period_end = NULL, $card_creator_id = NULL)
	{
		if (!is_numeric($card_id)) {
			throw new Exception('Идентификатор карты должен быть целым числом.');
		}
		if (!is_numeric($period)) {
			throw new Exception('Период карточки должен быть целым числом.');
		}

		$db = $this->getAdapter();
		$procedure = new Rp_Db_Procedure_Ach_CutCard;
		$procedure->exec(
			$db->quote($card_id) .',' .
			$db->quote($period_start) . ',' .
			$db->quote($period_end) . ',' .
			$db->quote($card_creator_id)
		);

		$where =
			'person_id = '             . $db->quote($person_id)
			.' AND period = '          . $db->quote($period)
			.' AND period_start = '    . $db->quote($period_start)
			.' AND period_end = '      . $db->quote($period_end);

		return $this->fetchRow($where);
	}

	public function blockCard($card_id, $user_id)
	{
		$db = $this->getAdapter();
		$card = $this->fetchRow('id = ' . $db->quote($card_id));

		$status = 0;
		if($card->is_blocked == 0)
		{
			$status = 1;
		}

		$this->update(array('is_blocked' => $db->quote($status, Zend_Db::INT_TYPE), 'blocked_status_changer_id' => $db->quote($user_id, Zend_Db::INT_TYPE)), $db->quote($card_id, Zend_Db::INT_TYPE));

		return $this->fetchRow('id = ' . $db->quote($card_id))->is_blocked;
	}

	/**
	 * Возвращает карточку, найденную по значениям
	 * идентификатора физ. лица и id карточки.
	 * Если карточка не будет найдена, то метод создаст
	 * новую карточку с указанными значениями $personId.
	 *
	 * @param int $personId Идентификатор физ. лица.
	 * @param int $card_id   Период карточки.
	 *
	 * @return Rp_Db_Table_Row_Ach_Card
	 */
	public function findByPersonIdAndCard($personId, $card_id = NULL, $period = NULL)
	{
		if($card_id == 0)
		{
			$card_id = NULL;
		}

		$db = $this->getAdapter();

		$period = ($period) ? $period : date('Y');
		$period_now = time();

		if($card_id === NULL)
		{
			$where  = 'person_id = ' . $db->quote($personId);
			$where .= ' AND period = ' . $period;
		}
		else
		{
			$where = 'id = ' . $db->quote($card_id);
		}

		if($card_id != NULL)
		{
			$rowCard = $this->fetchRow($where);
		}
		else
		{
			$cards = $this->fetchAll($where);

			$rowCard = NULL;
			if(count($cards) > 1)
			{
				foreach($cards as $card)
				{
					$period_start = strtotime($card->period_start);
					$period_end = strtotime($card->period_end);

					if($period_now >= $period_start AND $period_now <= $period_end)
					{
						$rowCard = $card;
					}
				}
			}
			elseif(count($cards) == 1)
			{
				foreach($cards as $card)
				{
					if($card->period_start AND $card->period_end)
					{
						if($period_now > $card->period_end)
						{
							$rowCard = $this->createCard($personId, $period, ($card->period_end + 1), 12, $period);
						}
						else
						{
							$rowCard = $card;
						}
					}
					else
					{
						$rowCard = $card;
					}

					break;
				}
			}
			else
			{
				$rowCard = $this->createCard($personId, $period);
			}
		}

		return $rowCard;
	}

	public function get_cards_and_periods($person_id)
	{
		$db = $this->getAdapter();

		$where = 'person_id = ' . $db->quote($person_id);

		return $this->fetchAll($where, array('period', 'period_start'));
	}
}