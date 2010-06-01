<?php

class Zend_View_Helper_AchievsPrintTasks
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
	
    public function achievsPrintTasks(Zend_Db_Table_Rowset_Abstract $tasks, array $ratings, $have_func, $rate_weights, $card, $rtg, $status_id)
    {
    	$xhtml   = array();
    	$func_tasks = array();
    	
    	$plan = false;
    	
    	if (($status_id == 'NEW') || ($status_id == 'PLN') || ($status_id == 'CPN'))
    		$plan = true;
    	
    	$xhtml[] = '
			<table class="table">
				<thead>
					<tr>
						<th class="tasks-field-num">№</th>
						<th class="tasks-field-description">Бизнес-цель<div>Business objective</div></th>
						<th class="tasks-field-term">Срок<div>Timing</div></th>
						<th class="tasks-field-weight">Вес<div>Weight</div></th>
						<th class="tasks-field-note"></th>
						<th class="tasks-field-result">Достижение бизнес-цели<div>Business objective achievement</div></th>
						<th class="tasks-field-rating">Рейтинг<div>Rating</div></th>
					</tr>
				</thead>
			</table>';
    	
    	if ($have_func > 0) 
    		$xhtml[] = '<div class="tasks-type"><span class="translate_ratio"><table id="ratio_tab" width="480px">
    							<tr>
    								<td width="360px">Соотношение веса - бизнес-цели / функциональные цели<br/>
    									Weight ratio - business objectives / functional objectives
    								</td><td width="10px" align="center"> = </td>
    								<td width="50px" align="center"> <label id="ratio_mng">' . $card->ratio_mng . '</label>
    								</td><td width="10px" align="center">/</td>
    								<td width="50px" align="center"><label id="ratio_fnc">' . $card->ratio_fnc . '</label>
    								</td>
    							</tr>
    						  </table></span></div>';
    	
    	$xhtml[] = '	
				<div class="tasks-type">Бизнес-цели - <span class="translate_category_tasks">Business Objectives</span></div>
			<table class="table">
				<tbody>';
    	$count = 0;
    	$count_func = 0;
		foreach ($tasks as $item) {
    		if ($item->is_personal == null) 
	    		if ($item->is_functional == 1) 
	    			$func_tasks[] = $this->_rowTask($item, $ratings, ++$count_func);
	    		else 
	    			$xhtml[] = $this->_rowTask($item, $ratings, ++$count);
    	}
    	
    	if ($plan)
    	{
			$newRow = $tasks->getTable()->createRow();
			for ($i = 0; $i < 6; $i++) 
				$xhtml[] = $this->_rowTask($newRow, $ratings, ++$count);
    	}
		
		$xhtml[] = '		
				</tbody>
			</table>';
		
		if ($have_func > 0)
		{
			$xhtml[] = '
				<div class="tasks-type">Функциональные бизнес-цели - <span class="translate_category_tasks">Functional Business Objectives</span></div>
				<table class="table">
					<tbody>
			';
			$xhtml[] = implode('', $func_tasks);
			
			if ($plan)
    		{
				$newRow = $tasks->getTable()->createRow();
				for ($i = 0; $i < 6; $i++) 
					$xhtml[] = $this->_rowTask($newRow, $ratings, ++$count_func);
    		}
			
			$xhtml[] = '		
					</tbody>
				</table>';
		}
		
		$xhtml[] = '</div>
			<div class="grid-footer">
				<table class="grid-footer-table">
					<tbody>';
		
		if ($have_func > 0)
			$xhtml[] = '<tr height="60px">
						<td colspan="3" style="border-bottom:1px #666666 solid" valign="top" >
						<span class="translate_func_comment">Комментарии функционального руководителя - Functional Manager\'s comment</br></span>
						<div class="func_div" id="func_div">' . $card->fnc_comment . '</div>
						</td>
						<th class="tasks-field-rating-total" style="border-bottom:1px #666666 solid">Функциональный рейтинг:<br/>Functional rating:</th>
						<td class="field-rating tasks-field-rating-total" id="fieldRatingFunc" style="border-bottom:1px #666666 solid">
								<div>' . $ratings[$card->rtg_func_id] . '</div>
						</td>
						</tr>
						<tr>';
		
		
		$xhtml[] = '	<tr>
							<td width="20%">
								Вычисленный рейтинг:<br/>Calculated rating:
							</td>
							<td width="5%">' . $this->CalculateWeights($tasks, $rate_weights, $card) . '</td>
							<td width="50%">&nbsp;</td>
							<td width="20%">Итоговый рейтинг:<br/>Total rating:</td>
							<td width="5%">' . $rtg . '</td>
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
    				$func_sum += $val * $wght;
    			}
    			else
    			{
    				$value = $rate_weights[$item->rating_id][weight];		// вес рейтинга
    				$weight = $item->weight;
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
   		$ret = null;
   		foreach ($name as $key=>$value)
   			if ($value[weight]==$result) $ret = $key; 
    	return $ret;
    }
    
	private function _rowTask(Zend_Db_Table_Row_Abstract $task, array $ratings, $counter)
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
}