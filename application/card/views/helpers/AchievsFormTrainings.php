<?php

class Zend_View_Helper_AchievsFormTrainings
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

    public function achievsFormTrainings(Zend_Db_Table_Rowset_Abstract $trainings,
		array $groupsMethods, array $respons, array $months, array $groupsMethodsActual)
    {
    	$methods = array();
		foreach ($groupsMethods as $item) {
			$methods += $item;
		}

		$xhtml   = array();
		$xhtml[] = '
			<div class="grid-head">
				<table class="grid-head-table">
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
				</table>
			</div>
			<div class="grid-body">
				<table class="grid-body-table" id="trains">
					<tbody>
		';
		foreach ($trainings as $item) {
			$xhtml[] = $this->_rowTraining($item, $methods, $groupsMethods, $respons, $months, $groupsMethodsActual);
		}
		$xhtml[] = $this->_rowTraining($trainings->getTable()->createRow(), $methods, $groupsMethods, $respons, $months, $groupsMethodsActual);
		$xhtml[] = '
					</tbody>
				</table>
			</div>
			<div class="grid-footer">
				<table class="grid-footer-table">
					<tbody>
						<tr>
							<td><div class="button" id="buttonAddTraining">Добавить цель проф. развития<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Add development objective</div></td>
						</tr>
					</tbody>
				</table>
			</div>
		';

		return implode('', $xhtml);
	}
    
    private function _rowTraining(Zend_Db_Table_Row_Abstract $training,
		array $methods, array $groupsMethods, array $respons, array $months, array $groupsMethodsActual)
	{
		static $counter = 0;

		if (empty($training->id))
		{
			$num   = '*';
			$name  = 'trainingPattern';
			$class = 'row-pattern';
			$toggle = '<div class="toggle-cancel-train" onclick="Card.removeRow(this)">&nbsp;</div>';
			
			end($months);
			$training->method_id = key($methods);
			$training->month_term_id = key($months);
			$training->responsible_id = key($respons);
		}
		else
		{
			$num   = ++$counter;
			$name  = 'trainings[' . $training->id . ']';
			$class = '';

			$toggle = '';
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
				<td class="trains-field-num">
					<div>' . $num . '</div>' . $toggle . '
				</td>
				<td class="trains-field-situation">
					<textarea name="' . $name . '[situation]" readonly>' . $training->situation . '</textarea>
				</td>
				<td class="trains-field-objective">
					<textarea name="' . $name . '[objective]" readonly>' . $training->objective . '</textarea>
				</td>
				<td class="trains-field-method">
					' . $this->view->formSelect($name . '[method_id]', $training->method_id , null, $groupsMethodsActual) . '
					<div title="' . $groupMethodsName . '">' . $groupMethodsName . '
						<div title="' . $methods[$training->method_id] . '">' . $methods[$training->method_id] . '</div>
					</div>
					<textarea name="' . $name . '[method_comment]" readonly>' . $training->method_comment . '</textarea>
				</td>
				<td class="trains-field-responsible">
					' . $this->view->formSelect($name . '[responsible_id]', $training->responsible_id , null, $respons) . '
					<div>' . $respons[$training->responsible_id] . '</div>
					<textarea name="' . $name . '[responsible_comment]" readonly>' . $training->responsible_comment . '</textarea>
				</td>
				<td class="trains-field-term">
					' . $this->view->formSelect($name . '[month_term_id]', $training->month_term_id , null, $months) . '
					<div>' . $months[$training->month_term_id] . '</div>
				</td>
				<td class="trains-field-result">
					<textarea name="' . $name . '[result]" readonly>' . $training->result . '</textarea>
				</td>
			</tr>
		';
    }
}