<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */

/**
 * Объект строки бизнес-цели. 
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */
class Rp_Db_Table_Row_Ach_Training extends Rp_Db_Table_Row 
{		
	/**
	 * Возвращает набор заметок к целям проф. развития
	 *
	 * @return Rp_Db_Table_Rowset
	 */
	public function fetchNotes()
	{
		$tableNotes = new Rp_Db_Table_Ach_Trainings_Notes();
		
		return $this->findDependentRowset($tableNotes);
	}

	
}