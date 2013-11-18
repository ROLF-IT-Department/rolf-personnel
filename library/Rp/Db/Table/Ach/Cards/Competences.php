<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */

/**
 * Объект таблицы компетенций карточек достижений.
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */
class Rp_Db_Table_Ach_Cards_Competences extends Rp_Db_Table_Abstract
{
	protected $_name = 'user_rp_ach_cards_competences';

	protected $_rowClass = 'Rp_Db_Table_Row_Ach_Competence';

	protected $_nameTableCompetences = 'user_rp_ach_competences';

	protected $_referenceMap = array(
		'Card' => array(
			'columns'       => 'card_id',
			'refTableClass' => 'Rp_Db_Table_Ach_Cards',
			'refColumns'    => 'id'
		),
		'Competence' => array(
			'columns'       => 'competence_id',
			'refTableClass' => 'Rp_Db_Table_Ach_Competences',
			'refColumns'    => 'id'
		)
	);

	/**
	 * Возвращает набор компетенций карточки
	 * с идентификатором $cardId. Каждая компетенция
	 * дополняется полями из справочника компетенций:
	 * название, цель, описание, признак дополнительной компетенции.
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
			SELECT
				compets.name,
				compets.target,
				compets.description,
				compets.english_description,
				compets.additional,
				cards.period,
				cards_compets.*
			FROM
				{$this->_name} cards_compets
				INNER JOIN {$this->_nameTableCompetences} compets
					ON cards_compets.competence_id = compets.id

				LEFT JOIN user_rp_ach_cards cards
					ON cards.id = $cardId

			WHERE
				card_id = $cardId
			ORDER BY
				cards_compets.date_record, compets.additional, compets.sort
		";

		return $this->_createRowset($sql);
	}

	public function findByCardIdAndPersonal($cardId)
	{
		$db = $this->getAdapter();
		$cardId = $db->quote($cardId);

		$sql = "
			SELECT  *

			FROM
				{$this->_name} cards_compets

			WHERE
				(card_id = $cardId) AND (cards_compets.is_personal IS NOT NULL)

		";

		return $this->_createRowset($sql);
	}

}