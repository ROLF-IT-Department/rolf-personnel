<?php

class Zend_View_Helper_AchievsPrintPersonal
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

    public function achievsPrintPersonal(
	    Zend_Db_Table_Rowset_Abstract $tasks,
	    Zend_Db_Table_Rowset_Abstract $personalTrainings,
        Zend_Db_Table_Rowset_Abstract $personalCompetences,
	    array $ratings, $rate_weights, $card, $status_id,
		Rp_Db_Table_Rowset $competences)
    {

	    $personal = 1;

    	$xhtml   = array();

    	$xhtml[] = '
				<table class="table">
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
			';


    	$xhtml[] = '<div class="tasks-type">Бизнес-цели (сотрудник) - <span class="translate_category_tasks">Business Objectives (employee)</span></div>
				<table class="table">
					<tbody>';

    	$count = 0;

    	foreach ($tasks as $item) {
    		if ($item->is_personal == 1)
    			$xhtml[] = $this->_rowTask($item, $ratings, $personal, ++$count);
    	}



    	$xhtml[] = '</tbody>
				</table>';


    	// вывод целей руководителя для оценки сотрудником
    	if (($status_id == 'RTG') || ($status_id == 'CRG') || ($status_id == 'CLS') || ($status_id == 'PLN') || ($status_id == 'CPN'))
    	{
	    	$xhtml[] = '<div class="tasks-type">Бизнес-цели (руководитель) - <span class="translate_category_tasks">Business Objectives (manager)</span></div>
					<table class="table">
						<tbody>';
	    	$count = 0;

	    	foreach ($tasks as $item) {
	    		if ($item->is_personal != 1)
	    			$xhtml[] = $this->_rowManagerTask($item, $ratings, $personal, ++$count);
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

	    $xhtml[] = '</tbody>
					</table>';

		// [START] компетенции
		$competences = $competences->toArray();
		$cardRtgCompetensId = $card->rtg_competens_id;

		$stands = array();
    	$addits = array();

		$xhtml[] = '
				<table class="table">
					<thead>
						<tr class="personal-competence-header">
							<th class="compets-field-num">№</th>
							<th class="compets-field-description" colspan="4">Компетенция<div>Competence</div></th>
							<th class="compets-field-result">Достижение по компетенции<div>Competence achievement</div></th>
							<th class="compets-field-rating">Рейтинг<div>Rating</div></th>
						</tr>
					</thead>
				</table>';

			$stands[] = '
					<div class="compets-type">Корпоративные компетенции - <span class="translate_category_tasks">Corporate competences</span></div>
					<table class="table">
						<tbody>
			';
			$addits[] = '
					<div class="compets-type">Компетенции группы должностей - <span class="translate_category_tasks">Job families competences</span></div>
					<table class="table">
						<tbody>
			';
			foreach ($competences as $item) {
				if ((!$item['disabled'])) {
					if ($item['additional']) {
						$addits[] = $this->_rowCompetence($item, $ratings, $in_person);
					} else {
						$stands[] = $this->_rowCompetence($item, $ratings, $in_person);
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

		// [END] компетенции

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
									<th class="compets-field-num"></td>
									<th class="compets-field-description">Ваши примеры по достижению компетенций<div>Your examples to reach competences</div></td>
									<th class="compets-field-term"></td>
									<th class="compets-field-weight"></td>
									<th class="compets-field-note"></td>
									<th class="compets-field-result"></td>
									<th class="compets-field-rating" style="border-left: 1px solid #999999;" >Рейтинг<div>Rating</div></td>
								</tr>
    						</thead>
							<tbody>
								<tr>
									<td colspan="6">' . $personalCompetence->result . '</td>
									<td class="compets-field-rating" style="border-left: 1px solid #999999; >' . $ratings[$personalCompetence->rating_id] . '</td>
								</tr>
							</tbody>
						</table>';

    	return $html;

    }

	public function _rowCompetence(array $competence, array $ratings, $in_person = FALSE)
    {
    	static $standsCounter = 0;
    	static $additsCounter = 0;

		$num  = $competence['additional'] ? ++$additsCounter : ++$standsCounter;

		return '
			<tr class="personal-competence-body">
				<td class="compets-field-num"><div>' . $num . '</div></td>
				<td class="compets-field-name"><div>' . $competence['name'] . '<div>' . $competence['target'] . '</div></div></td>
				<td class="compets-field-result">' . $competence['result_personal'] . '</td>
				<td class="compets-field-rating">' . $ratings[$competence['rating_id_personal']] . '</td>
			</tr>
		';
    }

    private function getPersonalTraining(Zend_Db_Table_Row_Abstract $personalTraining)
    {

    	$html = '
						<table class="table">
							<thead>
								<tr>
									<th class="tasks-field-description">Ваши комментарии по плану развития<div>Your development plan comments</td>
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
									<th class="tasks-field-description">Комментарий сотрудника<div>Employee comment</td>
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

	private function _rowTask(Zend_Db_Table_Row_Abstract $task, array $ratings, $func, $counter)
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

	private function _rowManagerTask(Zend_Db_Table_Row_Abstract $task, array $ratings, $func, $counter)
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