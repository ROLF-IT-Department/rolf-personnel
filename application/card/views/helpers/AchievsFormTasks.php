<?php

class Zend_View_Helper_AchievsFormTasks
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

    public function achievsFormTasks(Zend_Db_Table_Rowset_Abstract $tasks, $have_func, array $ratings, $rate_weights, $cardRtgTasksId, $userRole, $card, $cardRtgFuncId)
    {
    	$xhtml   = array();
    	$func_tasks = array();	// временный буфер для функциональных целей
    	$func = ($userRole) ? 1 : 0;

    	$xhtml[] = '
    		<div class="grid-head">
				<table class="grid-head-table">
					<thead>
						<tr>
							<th class="tasks-field-num">'.$count_func.'№</th>
							<th class="tasks-field-description">Бизнес-цель<div>Business objective</div></th>
							<th class="tasks-field-term">Срок<div>Timing</div></th>
							<th class="tasks-field-weight">Вес (%)<div>Weight (%)</div></th>
							<th class="tasks-field-note"></th>
							<th class="tasks-field-result">Достижение бизнес-цели<div>Business objective achievement</div></th>
							<th class="tasks-field-rating">Рейтинг<div>Rating</div></th>
						</tr>
					</thead>
				</table>
			</div>';
    	if ($have_func > 0)
    		$xhtml[] = '<div class="grid-body" style="bottom:100px; height: expression(this.parentNode.offsetHeight - 137 + \'px\')">';
    	else
			$xhtml[] = '<div class="grid-body">';

    	if ($have_func > 0)
    		$xhtml[] = '<div class="tasks-type"><span class="translate_ratio"><table id="ratio_tab" width="480px">
    							<tr>
    								<td width="360px">Соотношение веса - бизнес-цели / функциональные цели<br/>
    									Weight ratio - business objectives / functional objectives
    								</td><td width="10px" align="center"> = </td>
    								<td width="50px" align="center"> <label id="ratio_mng"></label><input align="left" id="ratio[ratio_mng]" name="ratio[ratio_mng]" type="text" value="' . $card->ratio_mng . '" size="2" />
    								</td><td width="10px" align="center">/</td>
    								<td width="50px" align="center"><label id="ratio_fnc"></label><input align="left" id="ratio[ratio_fnc]" name="ratio[ratio_fnc]" type="text" value="' . $card->ratio_fnc . '" size="2" />
    								</td>
    							</tr>
    						  </table></span></div>';

    	$xhtml[] = '<div class="tasks-type">Бизнес-цели - <span class="translate_category_tasks">Business Objectives</span></div>
				<table class="grid-body-table" id="tasks">
					<tbody>';
    	$count = 0;
    	$count_func = 0;
    	foreach ($tasks as $item) {
    		if ($item->is_personal == null)
	    		if ($item->is_functional == 1)
	    			$func_tasks[] = $this->_rowTask($item, $ratings, 1, ++$count_func);
	    		else
	    			$xhtml[] = $this->_rowTask($item, $ratings, null, ++$count);
    	}
    	$xhtml[] = $this->_rowTask($tasks->getTable()->createRow(), $ratings, $func);
    	$xhtml[] = '</tbody>
				</table>';

    	if ($have_func > 0)		// если есть функциональный руководитель, то показываем функциональные бизнес-цели
    	{
	    	$xhtml[] = '
				<div class="tasks-type">Функциональные бизнес-цели - <span class="translate_category_tasks">Functional Business Objectives</span></div>
				<table class="grid-body-table" id="functasks">
				<tbody>
					';
	    	$xhtml[] = implode('', $func_tasks);
		    $xhtml[] = $this->_rowTask($tasks->getTable()->createRow(), $ratings, $func);
	    	$xhtml[] = '
	    				</tbody>
					</table>
				';
	    }

	    $xhtml[] = '</div>

			<div class="grid-footer">
				<table class="grid-footer-table">
					<tbody>';
	    if ($have_func > 0)
			$xhtml[] = '<tr height="60px">
							<td colspan="3" style="border-bottom:1px #666666 solid" valign="top" >
							<span class="translate_func_comment">Комментарии функционального руководителя - Functional Manager\'s comment</br></span>

							<textarea name="comments[fnc_comment]" id="comments[fnc_comment]" class="func_comment">' . $card->fnc_comment . '</textarea>
							<div class="func_div" id="func_div">' . $card->fnc_comment . '</div>
							</td>
							<th class="tasks-field-rating-total" style="border-bottom:1px #666666 solid">Функциональный рейтинг:<br/>Functional rating:</th>
							<td class="field-rating tasks-field-rating-total" id="fieldRatingFunc" style="border-bottom:1px #666666 solid">
									' . $this->view->formSelect('ratings[rtg_func_id]', $cardRtgFuncId, null, $ratings) . '
									<div>' . $ratings[$cardRtgFuncId] . '</div>
							</td>
						</tr>';

		$xhtml[] = '	<tr>
							<td><div class="button" id="buttonAddTask">Добавить бизнес-цель<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Add business objective</div></td>
							<th class="tasks-field-rating-total">Вычисленный рейтинг:<br/>Calculated rating:</th>
							<td class="field-rating tasks-field-rating-total"><div>'.$this->CalculateWeights($tasks, $rate_weights, $card).'</div>
							<input id="sum_tasks" type="hidden" value="' . $this->CalculateWeights($tasks, $rate_weights, $card). '" /></td>
							<th class="tasks-field-rating-total">Итоговый рейтинг:<br/>Total rating:</th>
							<td class="field-rating tasks-field-rating-total" id="fieldRatingTasks">
								' . $this->view->formSelect('ratings[rtg_tasks_id]', $cardRtgTasksId, null, $ratings) . '
								<div>' . $ratings[$cardRtgTasksId] . '</div>
								<input id="rtg_tasks" type="hidden" value="" />
							</td>
						</tr>
					</tbody>
				</table>
			</div>
    	';

    	return implode('', $xhtml);
    }

    private function CalculateWeights($tasks, $rate_weights, $card)	// рассчет вычисляемого рейтинга
    {
    	$sum = 0;
    	$func_sum = 0;

    	foreach ($tasks as $item) {
    		if (($item->weight) && ($item->status != 0) && ($item->is_personal == null))
    		{
    			if ($item->is_functional)
    			{
    				$val = $rate_weights[$item->rating_id][weight];		// вес рейтинга
    				$wght = $item->weight;
    				//if ($val != 0)
    					$func_sum += $val * $wght;
    			}
    			else
    			{
    				$value = $rate_weights[$item->rating_id][weight];		// вес рейтинга
    				$weight = $item->weight;
    				//if ($value != 0)
    					$sum += $value * $weight;
    			}
    		}

    	}
   		$sum /= 100;
   		$func_sum /=100;
   		$result = 0;
   		if ($func_sum > 0)
   		{
   			$rate_sum = round($sum);
   			$rate_func_sum = round($func_sum);
   			$result =  round(($rate_sum * $card->ratio_mng + $rate_func_sum * $card->ratio_fnc) / 100);
   		}
   		else $result = round($sum);

   		$rate = new Rp_Db_Table_Ach_Ratings();
   		$name = $rate->fetchNameWeights();
   		$ret = "";
   		foreach ($name as $key=>$value)
   			if ($value[weight]==$result) $ret = $key;
    	return $ret;

    }

	private function _rowTask(Zend_Db_Table_Row_Abstract $task, array $ratings, $func, $counter = null)
	{

		if ($func)
		{
			$table = 'functasks';
		}
		else
		{
			$table = 'tasks';
		}

		if (empty($task->id)) {
			$num   = '*';
			$term  = '';//(date('n') < 12 ? date('Y') : (date('Y') + 1)) . '-12-31';
			$term_date = '[дата]';
			$note  = '';
			$name  = 'taskPattern';
			$class = 'row-pattern';
			$toggle = '<div class="toggle-cancel" onclick="Card.removeRow(this, \'' . $table . '\')">&nbsp;</div>';
			$weight = '0';
		} else {

			$all_notes = new Rp_Db_Table_Ach_Tasks_Notes();
			$kol = count($all_notes->fetchTaskNotes($task->id));

			$num   = $counter;
			$term = '';
			$term_date = '[дата]';
			if ($task->date_term != null)
			{
				$term = $task->date_term;
				$term_date = date('d.m.y', strtotime($term));
			}

			$note  = '<div style="display:none" onclick="openNotes(' . $task->id . ' , 0)" title="Заметки" style:>' . $kol . '</div>';
			$name  = 'tasks[' . $task->id . ']';
			$weight = $task->weight;
			$class = '';
			$toggle = '';
			if ($task->status == '0') {
				$class = 'row-canceled';
			} elseif($task->status > 0) {
				$class = 'row-approval';
			}
			if ($task->status != 2) {
				$toggle = '<div class="toggle-cancel" onclick="Card.toggleCancel(this, \'' . $table . '\')">&nbsp;</div>';
			}
		}

		return '
			<tr class="' . $class . '">
				<td class="tasks-field-num">
					<input name="' . $name . '[status]" type="hidden" value="' . $task->status . '" />
					<div>' . $num . '</div>' . $toggle . '
				</td>
				<td class="tasks-field-description">
					<textarea name="' . $name . '[description]" id="' . $name . '" readonly="readonly">' . $task->description . '</textarea>
				</td>
				<td class="tasks-field-term">
					<input name="term_display" type="text" value="' . $term_date .'" readonly="readonly" />
					<input name="' . $name . '[date_term]" type="hidden" value="' . $term .'" />
				</td>
				<td class="tasks-field-weight">
					<textarea name="' . $name . '[weight]" readonly="readonly">' . $weight . '</textarea>
				</td>
				<td class="tasks-field-note">
					' . $note . '
					<input name="' . $name . '[is_functional]" type="hidden" value="' . $func . '" />
				</td>
				<td class="tasks-field-result">
					<textarea name="' . $name . '[result]" readonly="readonly">' . $task->result . '</textarea>
				</td>
				<td class="tasks-field-rating">
					' . $this->view->formSelect($name . '[rating_id]', $task->rating_id , null, $ratings) . '
					<div>' . $ratings[$task->rating_id] . '</div>
				</td>
			</tr>
		';
	}
}