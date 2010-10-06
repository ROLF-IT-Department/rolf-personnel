<?php

class Zend_View_Helper_AchievsPrintPersonal
{	
	
	/**
	 * ������ �������������.
	 * 
     * @var Zend_View_Interface
     */
    public $view;
	
	public function setView(Zend_View_Interface $view)
    {
		$this->view = $view;
    }
	
    public function achievsPrintPersonal(Zend_Db_Table_Rowset_Abstract $tasks, Zend_Db_Table_Rowset_Abstract $personalTrainings,
     Zend_Db_Table_Rowset_Abstract $personalCompetences, array &$ratings, $rate_weights, $card, $status_id)
    {
    	
    	$xhtml   = array();
    	
    	$xhtml[] = '

				<table class="table">
					<thead>
						<tr>
							<th class="tasks-field-num">'.$count_func.'�</th>
							<th class="tasks-field-description">������-����<div>Business objective</div></th>
							<th class="tasks-field-term">����<div>Timing</div></th>
							<th class="tasks-field-weight">��� (%)<div>Weight (%)</div></th>
							<th class="tasks-field-note"></th>
							<th class="tasks-field-result">���������� ������-����<div>Business objective achievement</div></th>
							<th class="tasks-field-rating">�������<div>Rating</div></th>
						</tr>
					</thead>
				</table>
			';
    	
			  	
    	$xhtml[] = '<div class="tasks-type">������-���� (���������) - <span class="translate_category_tasks">Business Objectives (employee)</span></div>
				<table class="table">
					<tbody>';
    	
    	$count = 0;
    	
    	foreach ($tasks as $item) {
    		if ($item->is_personal == 1)
    			$xhtml[] = $this->_rowTask($item, $ratings, ++$count);
    	}
    	
    	
    	
    	$xhtml[] = '</tbody>
				</table>';
		
    	
    	// ����� ����� ������������ ��� ������ �����������
    	if (($status_id == 'RTG') || ($status_id == 'CRG') || ($status_id == 'CLS') || ($status_id == 'CPN'))
    	{
	    	$xhtml[] = '<div class="tasks-type">������-���� (������������) - <span class="translate_category_tasks">Business Objectives (manager)</span></div>
					<table class="table">
						<tbody>';
	    	$count = 0;
	    	
	    	foreach ($tasks as $item) {
	    		if ($item->is_personal != 1)
	    			$xhtml[] = $this->_rowManagerTask($item, $ratings, ++$count);
	    	}
	    	
	    	$xhtml[] = '</tbody>
					</table>';
    	}
    	
    	
    	if (count($personalCompetences) > 0)
		{
			foreach ($personalCompetences as $itemCompetence)
			{
				$xhtml[] = $this->getPersonalCompetences($itemCompetence, $ratings);
				break;
			}
		}
		else 
			$xhtml[] = $this->getPersonalCompetences($personalCompetences->getTable()->createRow(), $ratings);		
		
			
			
		if (count($personalTrainings) > 0)
		{
			foreach ($personalTrainings as $itemTraining)
			{
				$xhtml[] = $this->getPersonalTraining($itemTraining);
				break;
			}
		}
		else 
			$xhtml[] = $this->getPersonalTraining($personalTrainings->getTable()->createRow());		
		
		$xhtml[] = $this->getEmployeeComment($card);
		
    	return implode('', $xhtml);
    }

    private function getPersonalCompetences(Zend_Db_Table_Row_Abstract $personalCompetence, array &$ratings)
    {
    	
    	$html = '
						<table class="table">
							<thead>
								<tr>
									<th class="tasks-field-description">���� ������� �� ���������� �����������<div>Your examples to reach competences</div></td>
									<th class="tasks-field-num"></td>
									<th class="tasks-field-term"></td>
									<th class="tasks-field-weight"></td>
									<th class="tasks-field-note"></td>
									<th class="tasks-field-result"></td>
									<th class="compets-field-rating" style="border-left: 1px solid #999999;" >�������<div>Rating</div></td>
								</tr>
    						</thead>
    
			    	<tr>
						<td colspan="6">' . $personalCompetence->result . '</td>
						<td class="compets-field-rating" style="border-left: 1px solid #999999; >' . $ratings[$personalCompetence->rating_id] . '</td>
					</tr>
				</table>
		';
    	
    	return $html;
    	
    }
    
    private function getPersonalTraining(Zend_Db_Table_Row_Abstract $personalTraining)
    {
    	
    	$html = '
						<table class="table">
							<thead>
								<tr>
									<th class="tasks-field-description">���� ����������� �� ����� ��������<div>Your development plan comments</td>
									<th class="tasks-field-num"></td>
									<th class="tasks-field-term"></td>
									<th class="tasks-field-weight"></td>
									<th class="tasks-field-note"></td>
									<th class="tasks-field-result"></td>
									<th class="tasks-field-rating"></td>
								</tr>
    						</thead>
			    	<tr>
						<td colspan="7">' . $personalTraining->result . '</td>				
					</tr>
				</table>
			';
    	
    	return $html;
    	
    }
    
    private function getEmployeeComment($card)
    {
    	$html = '
						<table class="table">
							<thead>
								<tr>
									<th class="tasks-field-description">����������� ����������<div>Employee comment</td>
									<th class="tasks-field-num"></td>
									<th class="tasks-field-term"></td>
									<th class="tasks-field-weight"></td>
									<th class="tasks-field-note"></td>
									<th class="tasks-field-result"></td>
									<th class="tasks-field-rating"></td>
								</tr>
    						</thead>
			    	<tr>
						<td colspan="7">' . $card->emp_comment . '</td>				
					</tr>
				</table>
			';
    	
    	return $html;
    	
    }
    
	private function _rowTask(Zend_Db_Table_Row_Abstract $task, array &$ratings, $counter)
	{	
		
		$class = '';
		$rating = '';
		if ($task->id) {
			if ($task->status == '0') {
				$class = 'row-canceled';
			}
			$rating = $ratings[$task->rating_id];
		}
		$date_term = '';
		if (!empty($task->date_term)) {
			$date_term = date('d.m.y', strtotime($task->date_term));
		}
		
		return '
			<tr class="' . $class . '">
				<td class="tasks-field-num">' . $counter . '</td>
				<td class="tasks-field-description">' . nl2br($task->description) . '</td>
				<td class="tasks-field-term">' . $date_term . '</td>
				<td class="tasks-field-weight">' . $task->weight . '</td>
				<td class="tasks-field-note"></td>
				<td class="tasks-field-result">' . nl2br($task->result) . '</td>
				<td class="tasks-field-rating">' . $rating . '</td>
			</tr>
		';
	}
	
	private function _rowManagerTask(Zend_Db_Table_Row_Abstract $task, array &$ratings, $counter)
	{	
		
		$class = '';
		$rating = '';
		if ($task->id) {
			if ($task->status == '0') {
				$class = 'row-canceled';
			}
			$rating = $ratings[$task->rating_id_personal];
		}
		$date_term = '';
		if (!empty($task->date_term)) {
			$date_term = date('d.m.y', strtotime($task->date_term));
		}
		
		return '
			<tr class="' . $class . '">
				<td class="tasks-field-num">' . $counter . '</td>
				<td class="tasks-field-description">' . nl2br($task->description) . '</td>
				<td class="tasks-field-term">' . $date_term . '</td>
				<td class="tasks-field-weight">' . $task->weight . '</td>
				<td class="tasks-field-note"></td>
				<td class="tasks-field-result">' . nl2br($task->result_personal) . '</td>
				<td class="tasks-field-rating">' . $rating . '</td>
			</tr>
		';
	}
}