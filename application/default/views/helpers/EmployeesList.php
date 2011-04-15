<?php

class Zend_View_Helper_EmployeesList
{

	protected $rates;
	
	public function employeesList(Employees_List $list, array $rates)
	{
		$this->rates = $rates;
		$xhtml = array();
		$xhtml[] = '
			<div class="gridbox">
				<div class="gridbox-head">
					<table class="list-head-table">
						<thead>
							<tr>
								<th class="field-id"></th>
								<th class="field-name">Сотрудник</th>
								<th class="field-depart">Отдел</th>
								<th class="field-post">Должность</th>
								<th class="field-ach">УД-' . $list->periodFirst . '</th>
								<th class="field-ach">УД-' . $list->periodSecond . '</th>
							</tr>
						</thead>
					</table>
				</div>
				<div class="gridbox-body">
		';
		if ($list->rows !== null) {
			$title = implode(', ', $list->postNames);
			$xhtml[] = '<div class="list-title">' . $title . '</div>';
			$message = '';

			if (count($list->rows) == 0) {
				$message = 'Нет сотрудников на данной должности';
			}
			$xhtml[] = '<div class="list-message">' . $message . '</div>';
			$xhtml[] = $this->_listTable('emps', 'list-body-table', $list->rows);
			if ($list->subRows !== null) {
				$title = 'Непосредственные подчиненные';
				$xhtml[] = '<div class="list-title list-title-subemps">' . $title . '</div>';
			}
		}
		if ($list->subRows !== null) {
			$message = '';
			if (count($list->subRows) == 0) {
				$message = 'Нет подчиненных сотрудников';
			}
			$xhtml[] = '<div class="list-message">' . $message . '</div>';
			$xhtml[] = $this->_listTable('subemps', 'list-body-table', $list->subRows);
		}
		$xhtml[] = '
				</div>
			</div>
		';

		return implode($xhtml);
	}

	private function _listTable($id, $class, array $rows)
	{
		$xhtml = '
			<table id="' . $id . '" class="' . $class . '">
				<tbody>
		';
		foreach ($rows as $row) {
			$xhtml .= $this->_listRow($row);
		}
		$xhtml .= '
				</tbody>
			</table>
		';
		return $xhtml;
	}

	private function _listRow(array $row)
	{
		$period_status = array();
		foreach($row['cards'] as $year => $cards)
		{
			$count = count($cards);

			if($count AND $count == 1)
			{
				$period_status[$year] = array(
					'status' => $cards[0]->status_id,
					'rate' => $cards[0]->rtg_total_id
				);
			}
			elseif($count > 1)
			{
				foreach($cards as $card)
				{
					$ratings = array();
					if($card->rtg_total_id)
					{
						$ratings[] = array(
							'days' => (strtotime($card->period_end) - strtotime($card->period_start))/86400,
							'rating' => $card->rtg_total_id,
						);
					}
				}
				$result_rating = 0;
				$count_days = 0;
				foreach($ratings as $rating)
				{
					$result_rating += $rating['rating']*$rating['days'];
					$count_days += $rating['days'];
				}

				$result_rating = round($result_rating/$count_days);

				$period_status[$year] = array(
					'status' => 'MTPL',
					'rate' => ($result_rating) ? $result_rating : NULL
				);
			}
			else
			{
				$period_status[$year] = array(
					'status' => 'Новая',
					'rate' => NULL
				);
			}
		}

		$is_integrate = "";
		$is_testperiod = '';
		if ( $row['info']->persg != 1)
		{
			$text = NULL;
			switch($row['info']->persg)
			{
				case 2:
				case 3:
				case 7:
				case 8:
				case 10:
					$text = 'Совместитель';
					break;
				case 4:
				case 6:
					$text = 'Несписочный Состав';
					break;
				case 9:
					$text = 'Внешние Совместители';
					break;
			}
			$is_integrate = ($text)
				? "<span style='color: blue; font-size: 10px;'>&nbsp;(" . $text . ")</span>"
				: NULL;
		}

		if( $row['info']->endtest_date >= date('Y-m-d'))
			$is_testperiod = '<span style="color: green; font-size: 10px;">&nbsp;(испытательный срок)</span>';

		$view = '
			<tr>
				<td class="field-id">' . $row['info']->id . '</td>
				<td class="field-name">' . $row['info']->fullname . '</td>
				<td class="field-depart">' . preg_replace('/\/([-\pL\s]+)/', '', $row['info']->FullPath) . '</td>
				<td class="field-post">' . $row['attribs']->appointment . $is_integrate . $is_testperiod . '</td>';

		foreach($period_status as $period)
		{
			$view .= '<td class="field-ach-status status' . $period['status'] . '"
					title="' . $period['status']. '"></td>
				<td class="field-ach-rating">' . $this->rates[$period['rate']]['name'] . '</td>';
		}

			$view .= '</tr>';

		return $view;
	}
}