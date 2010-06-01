<?php

class Zend_View_Helper_AchievsPrintTrainings
{		
	/**
	 * Объект представления.
	 * 
     * @var Zend_View_Interface
     */
    public $view;
	
	public function setView(Zend_View_Interface $view)
    {
		$this->view = $view;
    }
    
    public function achievsPrintTrainings(Zend_Db_Table_Rowset_Abstract $trainings,	array $groupsMethods, array $respons, array $months, $status_id)
    {
    	$methods = array();
		foreach ($groupsMethods as $item) {
			$methods += $item;
		}
		
		$plan = false;
    	
    	if (($status_id == 'NEW') || ($status_id == 'PLN') || ($status_id == 'CPN'))
    		$plan = true;
		
		$xhtml   = array();
		$xhtml[] = '
			<table class="table trainings">
				<thead>
					<tr>
						<th class="trains-field-num">№</th>
						<th class="trains-field-situation">Текущая ситуация<div>Current situation</div></th>
						<th class="trains-field-objective">Цель развития<div>Development objective</div></th>
						<th class="trains-field-method">Метод<div>Method</div></th>
						<th class="trains-field-responsible">Ответственный<div>Person in charge</div></th>
						<th class="trains-field-term">Срок<div>Timing</div></th>
						<th class="trains-field-result">Достижение цели плана развития<div>Individual development objective observable change</div></th>
					</tr>
				</thead>
				<tbody>
		';
		foreach ($trainings as $item) {
			$xhtml[] = $this->_rowTraining($item, $methods, $groupsMethods, $respons, $months);
		}
		$count = count($trainings);
		
		if ($plan)
    	{
			$newRow = $trainings->getTable()->createRow();
			for ($i = 0; $i < 6; $i++) 
					$xhtml[] = $this->_rowTraining($newRow, $methods, $groupsMethods, $respons, $months);
    	}
				
		$xhtml[] = '
				</tbody>
			</table>
		';
		
		return implode('', $xhtml);
	}
    
    private function _rowTraining(Zend_Db_Table_Row_Abstract $training,
		array $methods, array $groupsMethods, array $respons, array $months)
	{
		static $counter = 0;
		
		$class = '';
		$method = '';
		$month_term = '';
		$responsible = '';
		if ($training->id) {
			if ($training->status == '0') {
				$class = 'row-canceled';
			}
			$method = $methods[$training->method_id];
			$month_term = $months[$training->month_term_id];
			$responsible = $respons[$training->responsible_id];
		}
		
		$groupMethodsName = '';
		foreach ($groupsMethods as $groupName => $items) {
			if (array_key_exists($training->method_id, $items)) {
				$groupMethodsName = $groupName;
				break;
			}
		}
		
		return '
			<tr class="' . $class . '">
				<td class="trains-field-num">' . ++$counter . '</td>
				<td class="trains-field-situation">' . nl2br($training->situation) . '</td>
				<td class="trains-field-objective">' . nl2br($training->objective) . '</td>
				<td class="trains-field-method">
					<div>' . $groupMethodsName . '<div>' . $method . '</div></div>
					' . nl2br($training->method_comment) . '
				</td>
				<td class="trains-field-responsible">
					<div>' . $responsible . '</div>
					' . nl2br($training->responsible_comment) . '
				</td>
				<td class="trains-field-term">' . $month_term . '</td>
				<td class="trains-field-result">' . nl2br($training->result) . '</td>
			</tr>
		';
    }
}