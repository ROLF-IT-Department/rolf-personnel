<?php

class Zend_View_Helper_AchievsFormPersonal
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

    public function achievsFormPersonal(
		Zend_Db_Table_Rowset_Abstract $tasks,
		Zend_Db_Table_Rowset_Abstract $personalTrainings,
		Zend_Db_Table_Rowset_Abstract $personalCompetences,
		array $ratings, $rate_weights, $userRole, $card, $status_id,
		Rp_Db_Table_Rowset $competences) {

		$personal = 1;

    	$xhtml   = array();

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

    	$xhtml[] = '<div class="grid-body">';

			// вывод целей руководителя для оценки сотрудником
			if (($status_id == 'RTG') || ($status_id == 'CRG') || ($status_id == 'CLS') || ($status_id == 'PLN') || ($status_id == 'CPN'))
			{
				$xhtml[] = '<div class="tasks-type">Бизнес-цели (руководитель) - <span class="translate_category_tasks">Business Objectives (manager)</span></div>
						<table class="grid-body-table" id="managertasks">
							<tbody>';
				$count = 0;

				foreach ($tasks as $item) {
					if ($item->is_personal != 1)
						$xhtml[] = $this->_rowManagerTask($item, $ratings, ++$count);
				}

				$xhtml[] = '</tbody>
						</table>';
			}

			$xhtml[] = '<div class="tasks-type">Бизнес-цели (сотрудник) - <span class="translate_category_tasks">Business Objectives (employee)</span></div>
					<table class="grid-body-table" id="personaltasks">
						<tbody>';

			$count = 0;

			foreach ($tasks as $item) {
				if ($item->is_personal == 1)
					$xhtml[] = $this->_rowTask($item, $ratings, $personal, ++$count);
			}

			$xhtml[] = $this->_rowTask($tasks->getTable()->createRow(), $ratings, $personal);

			$xhtml[] = '
						</tbody>
					</table>';

		$xhtml[] = '</div>';

		$xhtml[] = '
			<div class="grid-footer">
				<table class="grid-footer-table">
					<tbody>
						<tr>
							<td>
								<div class="button" id="buttonAddPersonalTask">Добавить бизнес-цель<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Add business objective</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>';

    	// [START] компетенции
		$competences = $competences->toArray();
		$cardRtgCompetensId = $card->rtg_competens_id;

		$stands = array();
    	$addits = array();

		$xhtml[] = '
			<div class="grid-head">
				<table class="grid-head-table">
					<thead>
						<tr>
							<th class="compets-field-num">№</th>
							<th class="compets-field-name">Компетенция<div>Competence</div></th>
							<th class="compets-field-note">&nbsp;</th>
							<th class="compets-field-result">Достижение по компетенции<div>Competence achievement</div></th>
							<th class="compets-field-rating">Рейтинг<div>Rating</div></th>
						</tr>
					</thead>
				</table>
			</div>';

		$xhtml[] = '<div class="grid-body">';

	    $period = NULL;
	    foreach($competences as $competence)
	    {
		    $period = $competence['period'];
		    break;
	    }


		    $stands[] = ($period < 2013)
			    ? '<div class="compets-type">Корпоративные компетенции - <span class="translate_category_tasks">Corporate competences</span></div>'
			    : NULL;

		    $stands[] = '
					<table class="grid-body-table" id="personalStandsCompets">
						<tbody>
			';
			$addits[] = ($period < 2013)
				? '<div class="compets-type">Компетенции группы должностей - <span class="translate_category_tasks">Job families competences</span></div>
					<table class="grid-body-table" id="personalAdditsCompets">
						<tbody>'
				: NULL;
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
	        $addits[] = ($period < 2013)
			    ? '</tbody></table>'
			    : NULL
	        ;

			$xhtml[] = implode('', $stands) . implode('', $addits);

		$xhtml[] = '</div>';


    	$xhtml[] = '
			<div class="grid-footer">
				<table class="grid-footer-table">
					<tbody><tr><td>&nbsp;</td></tr></tbody>
				</table>
			</div>
		';

		// [END] компетенции

		// [START] собственные компетенции

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

		//$xhtml[] ='</div>';

//	    $xhtml[] = '
//			<div class="grid-footer">
//				<table class="grid-footer-table">
//					<tbody>';
//
//		$xhtml[] = '		<td><div class="button" id="buttonAddPersonalTask">Добавить бизнес-цель<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Add business objective</div></td>
//
//						</tr>
//					</tbody>
//				</table>
//			</div>
//    	';

//		$xhtml  = array();
//    	$stands = array();
//    	$addits = array();

		// [END] собственные компетенции

    	return implode('', $xhtml);
    }

    private function getPersonalCompetences(Zend_Db_Table_Row_Abstract $personalCompetence, array $ratings)
    {
    	if (empty($personalCompetence->id))
    	{
    		$name = 'newCompetences[0]';
    		$note  = '';
    	}
    	else
    	{
    		$name =  'competences[' . $personalCompetence->id . ']';
    		$note  = '<div style="display:none" id="competencePersonalNote" onclick="openNotesCompetence(' . $personalCompetence->id . ', 1)" title="Заметки" style:>' . count($personalCompetence->fetchNotes($personalCompetence->id, 1)) . '</div>';
    	}

    	$html = '
			<div class="personal-grid-body">
				<table class="personal-grid-body-table" id="personalCompetence">
					<tr class="personal-competence-header">

						<td class="tasks-field-note" style="border-right: 1px solid #999999"></td>
						<td class="tasks-field-description">Ваши примеры по достижению компетенций<div>Your examples to reach competences</div></td>
						<td class="tasks-field-num"></td>
						<td class="tasks-field-term"></td>
						<td class="tasks-field-weight"></td>
						<td class="tasks-field-result"></div></td>
						<td class="compets-field-rating" style="border-left: 1px solid #999999; border-right: 1px solid #999999">Рейтинг<div>Rating</div></td>
					</tr>


					<tr>
						<td class="tasks-field-note" style="border-right: 1px solid #999999">
							' . $note . '
							<input name="' . $name . '[is_personal]" type="hidden" value="1" />
						</td>
						<td colspan="5">
							<textarea name="' . $name . '[result]" readonly="readonly" >' . $personalCompetence->result .
							//Zend_debug::dump($personalCompetence).
							'</textarea>
						</td>

						<td class="compets-field-rating" style="border-left: 1px solid #999999; border-right: 1px solid #999999">
							<div id="cmp_rtg_form" style="display:none">' . $this->view->formSelect($name . '[rating_id]', $personalCompetence->rating_id , null, $ratings) . '</div>
							<div id="cmp_rtg" style="display:block">' . $ratings[$personalCompetence->rating_id] . '</div>
						</td>
					</tr>
				</table>
			</div>';

    	return $html;

    }

    private function getPersonalTraining(Zend_Db_Table_Row_Abstract $personalTraining)
    {

    	if (empty($personalTraining->id))
    	{
    		$name = 'newTrainingsPersonal[0]';
    		$note  = '';
    	}
    	else
    	{
    		$name =  'trainings[' . $personalTraining->id . ']';
    		$note  = '<div style="display:none" id="trainingPersonalNote" onclick="openNotesTraining(' . $personalTraining->id . ')" title="Заметки" style:>' . count($personalTraining->fetchNotes()) . '</div>';
    	}

    	$html = '
			<div class="personal-grid-body">
				<table class="personal-grid-body-table" id="personalTraining">
					<tr class="personal-competence-header">
						<td class="tasks-field-note" style="border-right: 1px solid #999999"></td>

						<td class="tasks-field-description">Ваши комментарии по плану развития<div>Your development plan comments</div></td>
						<td class="tasks-field-num"></td>
						<td class="tasks-field-term"></td>
						<td class="tasks-field-weight"></td>
						<td class="tasks-field-result"></td>
						<td class="tasks-field-rating"></td>
					</tr>

					<tr>
						<td class="tasks-field-note" style="border-left: 1px solid #999999; border-right: 1px solid #999999">
							' . $note . '
							<input name="' . $name . '[is_personal]" type="hidden" value="1" />
							<input name="' . $name . '[month_term_id]" type="hidden" value="12" />
						</td>
						<td colspan="6">
							<textarea name="' . $name . '[result]" readonly="readonly" >' . $personalTraining->result . '</textarea>
						</td>
					</tr>
				</table>
			</div>';

    	return $html;

    }

    private function getEmployeeComment($card)
    {

    	$html = '
			<div class="personal-grid-body">
				<table id="comment_employee" class="grid-body-table comments-table">
					<tbody>
						<tr><th>Комментарий сотрудника - <span class="translate_category_tasks">Employee comment</span></th></tr>
						<tr>
							<td>
								<textarea id="emp_comment" name="comments[emp_comment]" readonly>' . $card->emp_comment . '</textarea>
							</td>
						</tr>
					</tbody>
				</table>
			</div>';

    	return $html;

    }

	private function _rowTask(Zend_Db_Table_Row_Abstract $task, array $ratings, $func, $counter = null)
	{

		if (empty($task->id)) {
			$num   = '*';
			$term  = '';//(date('n') < 12 ? date('Y') : (date('Y') + 1)) . '-12-31';
			$note  = '';
			$name  = 'taskPattern';
			$class = 'row-pattern';
			$toggle = '<div class="toggle-cancel" onclick="Card.removeRow(this, \'personaltasks\')">&nbsp;</div>';
			$weight = '0';
		} else {

			$all_notes = new Rp_Db_Table_Ach_Tasks_Notes();
			$kol = count($all_notes->fetchTaskNotes($task->id));


			$num   = $counter;
			$term = '';
			$term_date = '';
			if ($task->date_term != null)
			{
				$term = $task->date_term;
				$term_date = date('d.m.y', strtotime($term));
			}

			$note  = '<div style="display:none" onclick="openNotes(' . $task->id . ', 0)" title="Заметки" style:>' . $kol . '</div>';
			$name  = 'tasks_in_personal[' . $task->id . ']';
			$weight = $task->weight;
			$class = '';
			$toggle = '';
			if ($task->status == '0') {
				$class = 'row-canceled';
			} elseif($task->status > 0) {
				$class = 'row-approval';
			}
			if ($task->status != 2) {
				$toggle = '<div class="toggle-cancel" onclick="Card.toggleCancel(this, \'personaltasks\')">&nbsp;</div>';
			}
		}

		return '
			<tr class="' . $class . '">
				<td class="tasks-field-num">
					<div>' . $num . '</div>' . $toggle . '
					<input name="' . $name . '[status]" type="hidden" value="' . $task->status . '" />
				</td>
				<td class="tasks-field-description">
					<textarea name="' . $name . '[description]" readonly="readonly">' . $task->description . '</textarea>
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
					<input name="' . $name . '[is_personal]" type="hidden" value="' . $func . '" />
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

	private function _rowManagerTask(Zend_Db_Table_Row_Abstract $task, array $ratings, $counter = null)
	{

		$all_notes = new Rp_Db_Table_Ach_Tasks_Notes();
		$kol = count($all_notes->fetchPersonalManagerNotes($task->id));

		$num   = $counter;
		$term  = $task->date_term;
		$note  = '<div style="display:none" onclick="openNotes(' . $task->id . ', 1)" title="Заметки" style:>' . $kol . '</div>';
		$name  = 'tasks_in_personal[' . $task->id . ']';
		$class = ($task->status == '0') ? 'row-canceled' : '';
		$toggle = '';

		//if ($task->status == '0') return;

		//{
		//	$class = 'row-canceled';
		//} elseif($task->status > 0) {
		//	$class = 'row-approval';
		//}

		return '
			<tr class="' . $class . '">
				<td class="tasks-field-num">
					<div>' . $num . '</div>' . $toggle . '
					<input name="' . $name . '[status]" type="hidden" value="' . $task->status . '" />
				</td>
				<td class="tasks-field-description">
					<textarea name="' . $name . '[description]" readonly="readonly">' . $task->description . '</textarea>
				</td>
				<td class="tasks-field-term">
					<input name="term_display" type="text" value="' . date('d.m.y', strtotime($term)) .'" readonly="readonly" />
					<input name="' . $name . '[date_term]" type="hidden" value="' . $term .'" />
				</td>
				<td class="tasks-field-weight">
					' .$task->weight . '
				</td>
				<td class="tasks-field-note">
					' . $note . '
				</td>
				<td class="tasks-field-result">
					<textarea name="' . $name . '[result_personal]" readonly="readonly">' . $task->result_personal . '</textarea>
				</td>
				<td class="tasks-field-rating">
					' . $this->view->formSelect($name . '[rating_id_personal]', $task->rating_id_personal , null, $ratings) . '
					<div>' . $ratings[$task->rating_id_personal] . '</div>
				</td>
			</tr>
		';
	}

    public function _rowCompetence(array $competence, array $ratings, $in_person = FALSE)
    {
    	static $standsCounter = 0;
    	static $additsCounter = 0;


    	$competen = new Rp_Db_Table_Ach_Cards_Competences();
		$competen = $competen->find($competence['id'])->current();

		$num  = $competence['additional'] ? ++$additsCounter : ++$standsCounter;
		$name = ($in_person === TRUE)
			?'competences_in_person[' . $competence['id'] . ']'
			:'competences[' . $competence['id'] . ']';


		$kol = count($competen->fetchPersonalNotes($competen->id));
		$note1  = '<div style="display:none" onclick="openNotesCompetence(' . $competence['id'] . ', 1)" title="Заметки">' . $kol . '</div>';

		return '
			<tr>
				<td class="compets-field-num">
					<div>' . $num . '</div>
				</td>
				<td class="compets-field-name">
					<div>' . $competence['name'] . '<div>' . $competence['target'] . '</div></div>
				</td>
				<td class="compets-field-note">
					' . $note1 . '
				</td>
				<td class="compets-field-result">
					<textarea name="' . $name . '[result_personal]" readonly="readonly">' . $competence['result_personal'] . '</textarea>
				</td>
				<td class="compets-field-rating">
					' . $this->view->formSelect($name . '[rating_id_personal]', $competence['rating_id_personal'] , null, $ratings) . '
					<div>' . $ratings[$competence['rating_id_personal']] . '</div>
				</td>
			</tr>
		';
    }
}