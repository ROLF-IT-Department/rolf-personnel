<?php

class Zend_View_Helper_ProfileTasks
{	
	public function profileTasks(Zend_Db_Table_Rowset_Abstract $tasks,  $columnNotes, $have_func)
	{
		$xhtml   = array();
		$func_tasks = array();
		$xhtml[] = '
			<div class="gridbox">
				<div class="gridbox-head">
					<table class="gridbox-head-table">
						<thead>
							<tr>
								<th class="tasks-field-num">№</th>
								<th class="tasks-field-description">Описание</th>
								<th class="tasks-field-term">Срок</th>
								<th class="tasks-field-notes">' . ($columnNotes ? 'Заметки' : '') . '</th>
								<th class="tasks-field-status"></th>
							</tr>
						</thead>
					</table>
				</div>
				<div class="gridbox-body">
				<div class="tasks-header">Бизнес-цели - Business Objectives</div>
					<table class="gridbox-body-table">
						<tbody>
		';
		$count = 0;
		$count_func = 0;
		foreach ($tasks as $row) {		// просмотр заметок к целям
			if ($row->is_personal != 1)
				switch ($columnNotes) 
				{
					case 'MNG':
						if ($row->is_functional == 1) 	
	    						$func_tasks[] = $this->_rowTask($row, false, ++$count_func);
	    				else 
	    						$xhtml[] = $this->_rowTask($row, true, ++$count);
						break;
					case 'FUNC':
						if ($row->is_functional == 1) 	
	    						$func_tasks[] = $this->_rowTask($row, true, ++$count_func);
	    				else 
	    						$xhtml[] = $this->_rowTask($row, false, ++$count);
						break;
					default:
						if ($row->is_functional == 1) 	
	    						$func_tasks[] = $this->_rowTask($row, false, ++$count_func);
	    				else 
	    						$xhtml[] = $this->_rowTask($row, false, ++$count);
						break;
						break;
				}
			
		}
		$xhtml[] = '
				</tbody>
				</table> ';
		
		if ($have_func > 0) 
		{
			$xhtml[] = '
					<div class="tasks-header">Функциональные бизнес-цели - Functional Business Objectives</div>
					<table class="gridbox-body-table">
					<tbody>';
			$xhtml[] = implode('', $func_tasks);
			$xhtml[] = '      				
					</tbody>
					</table>';
		}
		
		$xhtml[] = ' 
				</div>
			</div>
		';
		
		return implode('', $xhtml);
	}
	
	private function _rowTask(Rp_Db_Table_Row_Ach_Task $task, $readNotes, $counter)
	{
		$all_notes = new Rp_Db_Table_Ach_Tasks_Notes();
		$kol = count($all_notes->fetchTaskNotes($task->id));
			
		$notes = '';
		if ($readNotes) {
			//$countNotes = count($task->fetchNotes());
			$notes = '<div onclick="openNotes(' . $task->id . ', 0)">' . $kol . '</div>';
		}
		$status = ($task->status == '0' ? 'Отменена' : '');
		
		$term_date = '';
		if ($task->date_term != null)
			$term_date = date('d.m.y', strtotime($task->date_term));
		
		return '
			<tr class="tasks-row-status' . $task->status . '">
				<td class="tasks-field-num">' . $counter . '.</td>
				<td class="tasks-field-description">' . nl2br(htmlspecialchars($task->description)) . '</td>
				<td class="tasks-field-term">' . $term_date . '</td>
				<td class="tasks-field-notes">' . $notes . '</td>
				<td class="tasks-field-status">' . $status . '</td>
			</tr>
		';
	}
}