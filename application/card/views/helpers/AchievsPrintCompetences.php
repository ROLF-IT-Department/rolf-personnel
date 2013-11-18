<?php

class Zend_View_Helper_AchievsPrintCompetences
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

    public function achievsPrintCompetences(Rp_Db_Table_Rowset $competences, array $ratings, $rate_weights, $cardRtgCompetensId)
    {
    	$competences = $competences->toArray();

    	$xhtml  = array();
    	$stands = array();
    	$addits = array();

	    $period = NULL;
	    foreach($competences as $competence)
	    {
		    $period = $competence['period'];
		    break;
	    }

    	$xhtml[] = '
			<table class="table">
				<thead>
					<tr>
						<th class="compets-field-num">№</th>
						<th class="compets-field-name">Компетенция<div>Competence</div></th>
						<th class="compets-field-description">Описание<div>Description</div></th>
						<th class="compets-field-result">Достижение по компетенции<div>Competence achievement</div></th>
						<th class="compets-field-rating">Рейтинг<div>Rating</div></th>
					</tr>
				</thead>
			</table>
		';

	    $stands[] = ($period < 2013)
		    ? '<div class="compets-type">Корпоративные компетенции - Corporate competences</div>'
		    : NULL;
    	$stands[] = '
    		<table class="table">
    			<tbody>
    	';
	    $addits[] = ($period < 2013)
	        ? '<div class="compets-type">Компетенции группы должностей - Job families competences</div>
    		<table class="table">
    			<tbody>'
	        : NULL;
    	foreach ($competences as $item) {
    		if (!$item['disabled']) {
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
	    $addits[] = ($period < 2013)
		    ? '</tbody></table>'
		    : NULL
	    ;

    	$xhtml[] = implode('', $stands) . implode('', $addits);

    	$xhtml[] = '</div>
			<div class="grid-footer">
				<table class="grid-footer-table">
					<tbody>
						<tr>
							<td width="20%">
								Вычисленный рейтинг:<br/>Calculated rating:
							</td>
							<td width="5%">' . $this->CalculateWeights($competences, $rate_weights) . '</td>
							<td width="50%">&nbsp;</td>
							<td width="20%">Итоговый рейтинг:<br/>Total rating:</td>
							<td width="5%">' . $ratings[$cardRtgCompetensId] . '</td>
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
    		if (!($item['disabled']) && ($item['rating_id']))
    			{
    				$val = $rate_weights[$item['rating_id']][weight];		// вес рейтинга
    				$sum += $val;
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

		$num  = $competence['additional'] ? ++$additsCounter : ++$standsCounter;

		return '
			<tr>
				<td class="compets-field-num">' . $num . '</td>
				<td class="compets-field-name">
					<div>' . $competence['name'] . '<div>' . $competence['target'] . '</div></div>
				</td>
				<td class="compets-field-description">' . nl2br($competence['description']) . '</td>
				<td class="compets-field-result">' . nl2br($competence['result']) . '</td>
				<td class="compets-field-rating">' . $ratings[$competence['rating_id']] . '</td>
			</tr>
		';
    }
}