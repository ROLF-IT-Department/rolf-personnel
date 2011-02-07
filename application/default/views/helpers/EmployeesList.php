<?php

class Zend_View_Helper_EmployeesList
{
	public function employeesList(Employees_List $list)
	{
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

	private function _listTable($id, $class, array &$rows)
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

	private function _listRow(array &$row)
	{
		$statusFirst = isset($row['statusFirst']) ? $row['statusFirst'] : 'Новая';
		$statusSecond = isset($row['statusSecond']) ? $row['statusSecond'] : 'Новая';
		$is_integrate = "";
		$is_testperiod = '';
//		if ( $row['id'] >= 90000000)
		if ( $row['persg'] != 1)
			$is_integrate = "<span style='color: blue; font-size: 10px;'>&nbsp;(" . $row['pgtxt'] . ")</span>";
//			$is_integrate = "<span style='color: blue; font-size: 10px;'>&nbsp;(совместитель)</span>";

		if( $row['endtest_date'] >= date('Y-m-d'))
			$is_testperiod = '<span style="color: green; font-size: 10px;">&nbsp;(испытательный срок)</span>';

		return '
			<tr>
				<td class="field-id">' . $row['id'] . '</td>
				<td class="field-name">' . $row['fullname'] . '</td>
				<td class="field-depart">' . $row['department'] . '</td>
				<td class="field-post">' . $row['appointment'] . $is_integrate . $is_testperiod . '</td>
				<td class="field-ach-status status' . $row['statusFirstId'] . '"
					title="' . $statusFirst . '"></td>
				<td class="field-ach-rating">' . $row['ratingFirst'] . '</td>
				<td class="field-ach-status status' . $row['statusSecondId'] . '"
					title="' . $statusSecond . '"></td>
				<td class="field-ach-rating">' . $row['ratingSecond'] . '</td>
			</tr>
		';
	}
}