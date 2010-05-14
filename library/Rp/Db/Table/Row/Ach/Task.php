<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */

/**
 * ������ ������ ������-����. 
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */
class Rp_Db_Table_Row_Ach_Task extends Rp_Db_Table_Row 
{		
	/**
	 * ���������� ����� ������� � ������-����.
	 *
	 * @return Rp_Db_Table_Rowset
	 */
	public function fetchNotes()
	{
		$tableNotes = new Rp_Db_Table_Ach_Tasks_Notes();
		
		return $this->findDependentRowset($tableNotes);
	}
}