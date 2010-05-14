<?php

class Zend_View_Helper_AchievsPrintCompetences
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
    
    public function achievsPrintCompetences(Rp_Db_Table_Rowset $competences, array &$ratings, $rate_weights, $cardRtgCompetensId)
    {
    	$competences = $competences->toArray();
    	
    	$xhtml  = array();
    	$stands = array();
    	$addits = array();
    	
    	$xhtml[] = '
			<table class="table">
				<thead>
					<tr>
						<th class="compets-field-num">�</th>
						<th class="compets-field-name">�����������<div>Competence</div></th>
						<th class="compets-field-description">��������<div>Description</div></th>
						<th class="compets-field-result">���������� �� �����������<div>Competence achievement</div></th>
						<th class="compets-field-rating">�������<div>Rating</div></th>
					</tr>
				</thead>
			</table>
		';
    	
    	$stands[] = '
    		<div class="compets-type">������������� ����������� - Corporate competences</div>
    		<table class="table">
    			<tbody>
    	';
    	$addits[] = '
    		<div class="compets-type">����������� ������ ���������� - Job families competences</div>
    		<table class="table">
    			<tbody>
    	';
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
    	$addits[] = '
    			</tbody>
    		</table>
    	';
    	
    	$xhtml[] = implode('', $stands) . implode('', $addits);
    	
    	$xhtml[] = '</div>
			<div class="grid-footer">
				<table class="grid-footer-table">
					<tbody>
						<tr>
							<td width="20%">
								����������� �������:<br/>Calculated rating:
							</td>
							<td width="5%">' . $this->CalculateWeights($competences, $rate_weights) . '</td>
							<td width="50%">&nbsp;</td>
							<td width="20%">�������� �������:<br/>Total rating:</td>
							<td width="5%">' . $ratings[$cardRtgCompetensId] . '</td>
						</tr>
					</tbody>
				</table>
			</div>
    	';
    	
    	return implode('', $xhtml);
    }
    
    private function CalculateWeights($competences, $rate_weights)	// ������� ������������ ��������
    {
    	$sum = 0;
    	$count = 0;
    	foreach ($competences as $item) {
    		if (!($item['disabled']) && ($item['rating_id'])) 
    			{
    				$val = $rate_weights[$item['rating_id']][weight];		// ��� ��������
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
    
    public function _rowCompetence(array $competence, array &$ratings)
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