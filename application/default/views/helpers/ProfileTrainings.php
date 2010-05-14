<?php

class Zend_View_Helper_ProfileTrainings
{	
	public function profileTrainings(Zend_Db_Table_Rowset_Abstract $trainings, 
		array& $groupsMethods, array& $respons, array& $months)
	{
		$xhtml   = array();
		$xhtml[] = '
			<div class="gridbox">
				<div class="gridbox-head">
					<table class="gridbox-head-table">
						<thead>
							<tr>
								<th class="training-field-num">№</th>
								<th class="training-field-situation">Текущая ситуация</th>
								<th class="training-field-objective">Цель развития</th>
								<th class="training-field-method">Метод</th>
								<th class="training-field-responsible">Ответственный</th>
								<th class="training-field-term">Срок</th>
							</tr>
						</thead>
					</table>
				</div>
				<div class="gridbox-body">
					<table class="gridbox-body-table">
						<tbody>
		';
		foreach ($trainings as $row) {
			$xhtml[] = $this->_rowTraining($row, $groupsMethods, $respons, $months);
		}
		
		$xhtml[] = '
						</tbody>
					</table>
				</div>
			</div>
		';
		
		return implode('', $xhtml);
	}
	
	private function _rowTraining(Zend_Db_Table_Row_Abstract $training, 
		array& $groupsMethods, array& $respons, array& $months)
	{
		static $counter = 0;
		
		$groupMethodsName = '';
		$methodName = '';
		foreach ($groupsMethods as $groupName => $items) {
			if (array_key_exists($training->method_id, $items)) {
				$groupMethodsName = $groupName;
				$methodName = $items[$training->method_id];
				break;
			}
		}
		
		return '
			<tr>
				<td class="training-field-num">' . ++$counter . '.</td>
				<td class="training-field-situation">' . nl2br(htmlspecialchars($training->situation)) . '</td>
				<td class="training-field-objective">' . nl2br(htmlspecialchars($training->objective)) . '</td>
				<td class="training-field-method">' . $groupMethodsName . '<div>' . $methodName . '</div></td>
				<td class="training-field-responsible">' . $respons[$training->responsible_id] . '
					<div>' . $training->responsible_comment . '</div>
				</td>
				<td class="training-field-term">' . $months[$training->month_term_id] . '</td>
			</tr>
		';
	}
}