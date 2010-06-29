<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */

/**
 * Объект таблицы заметок к компетенциям.
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */
class Rp_Db_Table_Ach_Competences_Personal_Notes extends Rp_Db_Table_Abstract
{
	protected $_name = 'user_rp_ach_cards_competences_notes';
	
	protected $_referenceMap = array(
		'Competence' => array(
			'columns'       => 'competence_id',
			'refTableClass' => 'Rp_Db_Table_Ach_Cards_Competences',
			'refColumns'    => 'id'
		)
	);

	// заметки сотрудника к компетенциям is_personal  is_personal = 1
	public function findPersonalNotes($competence_id)
	{
		$db = $this->getAdapter();

		$competence_id = $db->quote($competence_id);

		$sql = "
			SELECT
				*
			FROM
				$this->_name
			WHERE
				(competence_id = $competence_id) AND (is_personal = 1)
			ORDER BY
				date_record ASC
		";

		return $this->_createRowset($sql);
	}
}