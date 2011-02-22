<?php

class Zend_View_Helper_IntegratedBlock
{
	public function integratedBlock()
	{
		$xhtml = array();

		$user = Rp_User::getInstance();;
		$user_id = $user->getPersonId();
		$companies = new Rp_Db_View_Companies();
		$departments = new Rp_Db_View_Departments();
		$employees = new Rp_Db_View_Employees();
		$appointments = new Rp_Db_View_Appointments();
		$integrated = new Rp_Db_View_IntegratedPersons();
		$integrate_note = "";
		$kol = 0;

		$xhtml[] = "<div class='caption_int'>Совмещенные должности</div>

							<table class='integrate_table'>
								<tr>
									<td width='30px' align='center'><b>№</b></td>
									<td align='center'><b>Компания</b></td>
									<td align='center'><b>Полный путь</b></td>
									<td align='center'><b>Должность</b></td>
								</tr>";

//		if ($user_id < 90000000)
		switch($user->persg)
		{
			case 2:
			case 3:
			case 7:
			case 8:
			case 'S':
				$person_id = $integrated->fetchPersonID($user_id);
				$refer_id = $integrated->fetchRefID($person_id[0]);
				$employee = $employees->findByPersonId($person_id[0])->current();
				$company = current($companies->fetchNames($employee->company_id));
				$department = current($departments->fetchNames($employee->department_id));
				$appointment = current($appointments->fetchNames($employee->appointment_id));
				$full_path = $employee->getPerson()->FullPath;
				$integrate_note = "<div class='integrate_note'>* основное место работы</div>";

				$xhtml[] = "<tr style='color: blue;'>
									<td align='center'>" . ++$kol . "*</td>
									<td><a href='#' style='color: blue;' onclick='forwardIntegratePerson(". $person_id[0] .")'>" . $company . "</a></td>
									<td><a href='#' style='color: blue;' onclick='forwardIntegratePerson(". $person_id[0] .")'>" . $full_path . "</a></td>
									<td><a href='#' style='color: blue;' onclick='forwardIntegratePerson(". $person_id[0] .")'>" . $appointment . "</a></td>
							</tr>";
				break;
			default:
				$refer_id = $integrated->fetchRefID($user_id);
				break;
		}

		foreach ($refer_id as $item)
		{
			if ($user_id == $item) continue;
			else
			{
				$employee = $employees->findByPersonId($item)->current();
				$company = current($companies->fetchNames($employee->company_id));
				$department = current($departments->fetchNames($employee->department_id));
				$appointment = current($appointments->fetchNames($employee->appointment_id));
				$full_path = $employee->getPerson()->FullPath;
				$xhtml[] = "<tr>
								<td align='center'>" . ++$kol . "</td>
								<td><a  style='color: black;' href='#' onclick='forwardIntegratePerson(".$item .")'>" . $company . "</a></td>
								<td><a  style='color: black;' href='#' onclick='forwardIntegratePerson(".$item .")'>" . $full_path . "</a></td>
								<td><a  style='color: black;' href='#' onclick='forwardIntegratePerson(".$item .")'>" . $appointment .  "</a></td>
							</tr>";
			}
		}

		$xhtml[] = "</table>" . $integrate_note . "<br><br>";



		return implode($xhtml);
	}


}