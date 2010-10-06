<?php

class Zend_View_Helper_AchievsTableApprovals
{	
	public function achievsTableApprovals(Achievs_ApprovalsModel $approvals, $cardStatusId, $have_func)
	{
		$approvals = $approvals->toArray();
		
		$roles = array(
			'mng' => 'руководитель/manager',
			'emp' => 'сотрудник/employee',
			'hmg' => 'вышест. руководитель/ high manager',
			'fnc' => 'функциональный руководитель/functional manager'
		);
		
		$xhtml   = array();
		$xhtml[] = '
			<table class="approvals-table">
				<thead>
					<tr>
						<th colspan="3">Согласование планирования<br/>Plan confirmation</th>
						<th colspan="3">Согласование оценки<br/>Rating confirmation</th>
					</tr>
				</thead>
				<tbody>
		';
		foreach ($approvals as $id => $item) {
			$plan_date   = $item['plan_status'] ? $item['plan_date'] : '-';
			$plan_status = ($item['plan_status'] || $cardStatusId == 'CPN') ?
				'approvals-status' . $item['plan_status'] : '';
			$rate_date   = $item['rate_status'] ? $item['rate_date'] : '-';
			$rate_status = ($item['rate_status'] || $cardStatusId == 'CRG') ?
				'approvals-status' . $item['rate_status'] : '';
			if (($id == 'fnc') && ($have_func == 0)) continue;		// если у сотрудника нет функциональных руководителей, то не выводим информацию о согласовании функционального руководителя
			$xhtml[] = '
					<tr>
						<td class="approvals-field-name">' . $item['plan_name'] . '</td>
						<td class="approvals-field-status ' . $plan_status . '"></td>
						<td class="approvals-field-date">' . $plan_date . '</td>
						<td class="approvals-field-name">' . $item['rate_name'] . '</td>
						<td class="approvals-field-status ' . $rate_status . '"></td>
						<td class="approvals-field-date">' . $rate_date . '</td>
					</tr>
					<tr>
						<td class="approvals-field-role" colspan="3">' . $roles[$id] . '</td>
						<td class="approvals-field-role" colspan="3">' . $roles[$id] . '</td>
					</tr>
			';
		}
		$xhtml[] = '
				</tbody>
			</table>
		';
		
		return implode('', $xhtml);
	}
}