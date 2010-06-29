<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */

/**
 * Объект строки компетенции. 
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */
class Rp_Db_Table_Row_Ach_Competence extends Rp_Db_Table_Row 
{		
	/**
	 * Возвращает набор заметок к компетенции.
	 *
	 * @return Rp_Db_Table_Rowset
	 */
	public function fetchNotes($competence_id = NULL)
	{
		$tableNotes = new Rp_Db_Table_Ach_Competences_Notes();
		
		return $tableNotes->findNotes($competence_id);
	}

	public function fetchPersonalNotes($competence_id = NULL)
	{
		$tablePersonalNotes = new Rp_Db_Table_Ach_Competences_Personal_Notes();

		return $tablePersonalNotes->findPersonalNotes($competence_id);
	}
}