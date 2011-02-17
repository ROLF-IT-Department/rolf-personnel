<?php

class Zend_View_Helper_AchievsFormCompetences
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

    public function achievsFormCompetences(Rp_Db_Table_Rowset $competences, array $ratings, $rate_weights, $cardRtgCompetensId)
    {
    	$competences = $competences->toArray();

		$xhtml  = array();
    	$stands = array();
    	$addits = array();
    	$xhtml[] = '
			<div class="grid-head">
				<table class="grid-head-table">
					<thead>
						<tr>
							<th class="compets-field-num">№</th>
							<th class="compets-field-name">Компетенция<div>Competence</div></th>
							<th class="compets-field-description">Описание<div>Description</div></th>
							<th class="compets-field-note">&nbsp;</th>
							<th class="compets-field-result">Достижение по компетенции<div>Competence achievement</div></th>
							<th class="compets-field-rating">Рейтинг<div>Rating</div></th>
						</tr>
					</thead>
				</table>
			</div>
    		<div class="grid-body">
		';



    	$stands[] = '
    			<div class="compets-type">Корпоративные компетенции - <span class="translate_category_tasks">Corporate competences</span></div>
    			<table class="grid-body-table" id="standsCompets">
    				<tbody>
    	';
    	$addits[] = '
    			<div class="compets-type">Компетенции группы должностей - <span class="translate_category_tasks">Job families competences</span></div>
    			<table class="grid-body-table" id="additsCompets">
    				<tbody>
    	';
    	foreach ($competences as $item) {
    		if ((!$item['disabled']) && (!$item['is_personal'])) {
    			if ($item['additional']) {
					$addits[] = $this->_rowCompetence($item, $ratings);
				} else {
					$stands[] = $this->_rowCompetence($item, $ratings);
				}
    		}
    	}
    	$stands[] = '
    				</tbody>
    			</table>
    	';
    	$addits[] = '
    				</tbody>
    			</table>
    	';

		$name = 'ratings[rtg_competens_id]';
    	$xhtml[] = implode('', $stands) . implode('', $addits);
    	$xhtml[] = '
			</div>
			<div class="grid-footer">
				<table class="grid-footer-table">
					<tbody>
						<tr>
							<th class="tasks-field-rating-total">Вычисленный рейтинг:<br/>Calculated rating:</th>
							<td class="field-rating tasks-field-rating-total"><div>'.$this->CalculateWeights($competences, $rate_weights).'</div></td>
							<th class="compets-field-rating-total">Итоговый рейтинг:<br/>Total rating:</th>
							<td class="field-rating compets-field-rating-total" id="fieldRatingCompets">
								' . $this->view->formSelect($name, $cardRtgCompetensId, null, $ratings) . '
								<div>' . $ratings[$cardRtgCompetensId] . '</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		';

    	return implode('', $xhtml);
    }

    private function CalculateWeights($competences, $rate_weights)	// рассчет вычисляемого рейтинга
    {
    	$sum = 0;
    	$count = 0;
    	foreach ($competences as $item) {
    		if ((!$item['disabled']) && ($item['rating_id']) && (!$item['is_personal']))
    			{
    				$val = $rate_weights[$item['rating_id']]['weight'];		// вес рейтинга
    				$sum += $val;
    				if ($val != 0)
    					$count++;
    			}

    	}
    	$result = 0;
   		if ($count) $result = round($sum / $count);
   		$rate = new Rp_Db_Table_Ach_Ratings();
   		$name = $rate->fetchNameWeights();
   		$ret = null;
   		foreach ($name as $key=>$value)
   			if ($value[weight]==$result) $ret = $key;
    	return $ret;
    }

    public function _rowCompetence(array $competence, array $ratings)
    {
    	static $standsCounter = 0;
    	static $additsCounter = 0;


    	$competen = new Rp_Db_Table_Ach_Cards_Competences();
		$competen = $competen->find($competence['id'])->current();

		$num  = $competence['additional'] ? ++$additsCounter : ++$standsCounter;
		$name = 'competences[' . $competence['id'] . ']';
		$kol = count($competen->fetchNotes($competen->id));
		$note1  = '<div style="display:none" onclick="openNotesCompetence(' . $competence['id'] . ', 0)" title="Заметки">' . $kol . '</div>';

		return '
			<tr>
				<td class="compets-field-num">
					<div>' . $num . '</div>
				</td>
				<td class="compets-field-name">
					<div>' . $competence['name'] . '<div>' . $competence['target'] . '</div></div>
				</td>
				<td class="compets-field-description">
					<textarea readonly="readonly">' . $competence['description'] .  $competence['english_description'] . '</textarea>
				</td>
				<td class="compets-field-note">
					' . $note1 . '
				</td>
				<td class="compets-field-result">
					<textarea name="' . $name . '[result]" readonly="readonly">' . $competence['result'] . '</textarea>
				</td>
				<td class="compets-field-rating">
					' . $this->view->formSelect($name . '[rating_id]', $competence['rating_id'] , null, $ratings) . '
					<div>' . $ratings[$competence['rating_id']] . '</div>
				</td>
			</tr>
		';
    }
}