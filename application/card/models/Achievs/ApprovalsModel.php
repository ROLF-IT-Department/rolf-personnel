<?php

class Achievs_ApprovalsModel
{
	/**
	 * Объект карточки достижений.
	 *
	 * @var Rp_Db_Table_Row_Ach_Card
	 */
	protected $_card = null;

	public function __construct(Rp_Db_Table_Row_Ach_Card $card)
	{
		$this->_card = $card;
	}

	/**
	 * Возвращает информацию о согласованиях карточки достижений.
	 *
	 * @return array
	 */
	public function toArray()
	{
		$card = $this->_card;

		$approvalsPersons = array(
			'plan_mng_id' => $card->plan_mng_id,
			'plan_hmg_id' => $card->plan_hmg_id,
			'plan_fnc_id' => $card->plan_fnc_id,
			'rate_mng_id' => $card->rate_mng_id,
			'rate_hmg_id' => $card->rate_hmg_id,
			'rate_fnc_id' => $card->rate_fnc_id
		);

		$person = $card->getEmployee()->getPerson();

		if (array_search(NULL, $approvalsPersons) !== FALSE)
		{
			$employee = $card->getEmployee();
			if ($employee && $employee->isCurrent())
			{
				if (empty($approvalsPersons['rate_mng_id']))
				{
					$mngsPersonIds = $employee->getManagers()->getCol('person_id');

					if (count($mngsPersonIds) == 1)
					{
						$approvalsPersons['rate_mng_id'] = $mngsPersonIds[0];
					}
					elseif (count($mngsPersonIds) == 0)
					{
						$approvalsPersons['rate_mng_id'] = $person->id;
					}

					if (empty($approvalsPersons['plan_mng_id']))
					{
						$approvalsPersons['plan_mng_id'] = $approvalsPersons['rate_mng_id'];
					}
				}

				if (empty($approvalsPersons['rate_hmg_id']))
				{
					$mngsPersonIds = $employee->getHighManagers()->getCol('person_id');
					if (count($mngsPersonIds) == 1)
					{
						$approvalsPersons['rate_hmg_id'] = $mngsPersonIds[0];

						//////  Ограничение для Ника Хокинса и Сергея Петрова. Чтобы он не был вышестоящим руководителем
						if ($mngsPersonIds[0] == 29790 OR $mngsPersonIds[0] == 43835)
						{
							$mngsPersonIds = $employee->getManagers()->getCol('person_id');
							$approvalsPersons['rate_hmg_id'] = $mngsPersonIds[0];
						}

					}
					elseif (count($mngsPersonIds) == 0)
					{
						$approvalsPersons['rate_hmg_id'] = $person->id;
					}

					if (empty($approvalsPersons['plan_hmg_id']))
					{
						$approvalsPersons['plan_hmg_id'] = $approvalsPersons['rate_hmg_id'];
					}

				}

				if (empty($approvalsPersons['rate_fnc_id']))
				{
					$fnc_mngsPersonIds = $employee->getFuncManagers()->getCol('person_id');
					if (count($fnc_mngsPersonIds) == 1)
					{
						$approvalsPersons['rate_fnc_id'] = $fnc_mngsPersonIds[0];
					}
					elseif (count($fnc_mngsPersonIds) == 0)
					{
						$approvalsPersons['rate_fnc_id'] = $person->id;
					}
					elseif (count($fnc_mngsPersonIds) > 1)
					{
						$approvalsPersons['rate_fnc_id'] = '';
					}

					if (empty($approvalsPersons['plan_fnc_id']))
					{
						$approvalsPersons['plan_fnc_id'] = $approvalsPersons['rate_fnc_id'];
					}
				}
			}
		}


		$fullnames  = array('' => '', 0 => '');
		$fullnames += $person->getView()->fetchFullnames($approvalsPersons);

		$approvals = array(
			'mng' => array(
				'plan_name'   => $fullnames[$approvalsPersons['plan_mng_id']],
				'plan_date'   => $card->plan_mng_date,
				'plan_status' => $card->plan_mng_status,
				'rate_name'   => $fullnames[$approvalsPersons['rate_mng_id']],
				'rate_date'   => $card->rate_mng_date,
				'rate_status' => $card->rate_mng_status,
			),
			'emp' => array(
				'plan_name'   => $person->fullname,
				'plan_date'   => $card->plan_emp_date,
				'plan_status' => $card->plan_emp_status,
				'rate_name'   => $person->fullname,
				'rate_date'   => $card->rate_emp_date,
				'rate_status' => $card->rate_emp_status,
			),
			'hmg' => array(
				'plan_name'   => $fullnames[$approvalsPersons['plan_hmg_id']],
				'plan_date'   => $card->plan_hmg_date,
				'plan_status' => $card->plan_hmg_status,
				'rate_name'   => $fullnames[$approvalsPersons['rate_hmg_id']],
				'rate_date'   => $card->rate_hmg_date,
				'rate_status' => $card->rate_hmg_status,
			),
			'fnc' => array(
				'plan_name'   => $fullnames[$approvalsPersons['plan_fnc_id']],
				'plan_date'   => $card->plan_fnc_date,
				'plan_status' => $card->plan_fnc_status,
				'rate_name'   => $fullnames[$approvalsPersons['rate_fnc_id']],
				'rate_date'   => $card->rate_fnc_date,
				'rate_status' => $card->rate_fnc_status
			)
		);

		return $approvals;
	}
}