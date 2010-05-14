<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */

/**
 * Объект таблицы заметок к бизнес-целям.
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */
class Rp_Db_Table_Ach_Tasks_Notes extends Rp_Db_Table_Abstract
{
	protected $_name = 'user_rp_ach_tasks_notes';
	
	protected $_referenceMap = array(
		'Task' => array(
			'columns'       => 'task_id',
			'refTableClass' => 'Rp_Db_Table_Ach_Tasks',
			'refColumns'    => 'id'
		)
	);
	
	// заметки руководителя к бизнес-целям  is_personal = 0
	public function fetchTaskNotes($taskId)
	{
		$db = $this->getAdapter();
		$taskID = $db->quote($taskId);
		
		$sql = "
			SELECT
				*
			FROM
				$this->_name
			WHERE
				(task_id = $taskID) AND (is_personal = 0)
			ORDER BY
				date_record ASC
		";
		
		return $this->_createRowset($sql);
	}
	
	// заметки сотрудника к бизнес-целям руководителя  is_personal = 1
	public function fetchPersonalManagerNotes($taskId)
	{
		$db = $this->getAdapter();
		$taskID = $db->quote($taskId);
		
		$sql = "
			SELECT
				*
			FROM
				$this->_name
			WHERE
				(task_id = $taskID) AND (is_personal = 1)
			ORDER BY
				date_record ASC
		";
		
		return $this->_createRowset($sql);
	}
}