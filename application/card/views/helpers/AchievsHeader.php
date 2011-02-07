<?php

class Zend_View_Helper_AchievsHeader
{
	public function achievsHeader(Rp_Db_View_Row_Employee $emp)
	{
		$persons = new Rp_Db_View_Persons();
		$companies = new Rp_Db_View_Companies();
		$departments = new Rp_Db_View_Departments();
		$appointments = new Rp_Db_View_Appointments();

		$category = $emp->isManager() ? 'Руководитель' : 'Сотрудник';
		$managersIds = $emp->getManagers()->getCol('person_id');

		$managers = implode('<br />', $persons->fetchFullnames($managersIds));

		$is_integrate = "";

//		if ($emp->person_id >= 90000000)
		if ($emp->persg >= 90000000)
			$is_integrate = "<span style='color: blue; font-size: 12px;'>(совместитель)</span>";

		$is_testperiod = '';
		if( $emp->endtest_date >= date('Y-m-d'))
			$is_testperiod = '<span style="color: green; font-size: 12px;">&nbsp;(испытательный срок)</span>';

		// если ID компании и ID отдела совпадают, то в поле отдел пишем знак '-', так как люди привязаны к юр. лицу напрямую
		$dep_name = "-";
		if ($emp->department_id != '-')
			$dep_name = current($departments->fetchNames($emp->department_id));

		return '
			<table class="header" cellspacing="5">
				<tbody>
					<tr>
						<td class="header-field-name">Сотрудник<div>Employee</div></td>
						<td class="header-field-value">
							<div>' . $emp->getPerson()->fullname . '</div>
							<input type="hidden" id="employee_fio" value="' . $emp->getPerson()->fullname . '" />
						</td>
						<td class="header-field-name">Категория<div>Category</div></td>
						<td class="header-field-value"><div>' . $category . '</div></td>
						<td class="header-field-name">Руководитель<div>Manager</div></td>
						<td class="header-field-value"><div>' . $managers . '</div></td>
						<td class="header-field-empty"></td>
					</tr>
					<tr>
						<td class="header-field-name">Компания<div>Company</div></td>
						<td class="header-field-value">
							<div>' . current($companies->fetchNames($emp->company_id)) . '</div></td>
						<td class="header-field-name">Отдел<div>Department</div></td>
						<td class="header-field-value">
							<div>' . $dep_name . '</div></td>
						<td class="header-field-name">Должность<div>Position</div></td>
						<td class="header-field-value">
							<div>' . current($appointments->fetchNames($emp->appointment_id)) . $is_integrate . $is_testperiod . '</div></td>
					</tr>
				</tbody>
			</table>
		';
	}
}