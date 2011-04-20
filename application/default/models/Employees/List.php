<?php
/**
 * ROLF Personnel default models
 *
 * @category   Default
 * @package    Employees
 * @subpackage Employees_List
 */

/**
 * Список сотрудников.
 *
 * @category   Default
 * @package    Employees
 * @subpackage Employees_List
 */
class Employees_List
{
	public $rows = null;

	public $subRows = null;

	public $postNames = null;

	public $periodFirst = null;

	public $periodSecond = null;

	public function __construct($postIds, $periodFirst, $periodSecond, $fetchEmps = true, $fetchSubEmps = true, $func)
	{
		if (!empty($postIds))
		{
			$table = 'user_rp_tree_posts_employees_PM';
			if ($fetchEmps)
			{
				$treePostsEmployees =      new Rp_Db_View_TreePosts_Employees();
				$person_model =            new Rp_Db_View_Persons();
				$employee_model =          new Rp_Db_View_Employees();
				$cards_model =             new Rp_Db_Table_Ach_Cards();

				$employees_ids = $treePostsEmployees->fetchEmployeeIds($postIds);
				$_persons      = $person_model->find($employees_ids);

				$employees = NULL;
				foreach($_persons as $person)
				{
					$employees[$person->id]['info'] = $person;
					$employees[$person->id]['attribs'] = $employee_model->find_full_info($person->id);//$employees_attribs_model->find($person->id)->current();

					$where ='person_id = ' . $person->id . ' AND period IN (' . $periodFirst . ',' . $periodSecond . ')';
					$cards = $cards_model->fetchAll($where);

					foreach($cards as $card)
					{
						if($card->period == $periodFirst)
						{
							$employees[$person->id]['cards'][$periodFirst][] = $card;
						}
						elseif($card->period == $periodSecond)
						{
							$employees[$person->id]['cards'][$periodSecond][] = $card;
						}
					}
				}

				$this->rows = $employees;
				$treePosts = new Rp_Db_View_TreePosts();
				$this->postNames = $treePosts->fetchNames($postIds);
			}

			if ($fetchSubEmps)
			{
				$treePostsEmployees =      new Rp_Db_View_TreePosts_Employees();
				$person_model =            new Rp_Db_View_Persons();
				$employee_model =          new Rp_Db_View_Employees();
				$cards_model =             new Rp_Db_Table_Ach_Cards();

				$employees_ids = $treePostsEmployees->fetchEmployeeIds_by_pid($postIds);
				$_persons      = $person_model->find($employees_ids);

				foreach($_persons as $person)
				{
					$employees[$person->id]['info'] = $person;
					$employees[$person->id]['attribs'] = $employee_model->find_full_info($person->id);//$employees_attribs_model->find($person->id)->current();

					$where ='person_id = ' . $person->id . ' AND period IN (' . $periodFirst . ',' . $periodSecond . ')';
					$cards = $cards_model->fetchAll($where);

					foreach($cards as $card)
					{
						if($card->period == $periodFirst)
						{
							$employees[$person->id]['cards'][$periodFirst][] = $card;
						}
						elseif($card->period == $periodSecond)
						{
							$employees[$person->id]['cards'][$periodSecond][] = $card;
						}
					}
				}

				$this->subRows = $employees;
			}

			if ($func)
			{
				$table = 'user_rp_tree_posts_func';
				$this->subRows = $this->_fetch('post_func_id', $postIds, $periodFirst, $periodSecond, $table);
			}
		}
		else
		{
			if ($fetchEmps) {
				$this->rows = array();
			}
			if ($fetchSubEmps) {
				$this->subRows = array();
			}
			if ($func) {
				$this->subRows = array();
			}
		}
		$this->periodFirst = $periodFirst;
		$this->periodSecond = $periodSecond;
	}

	private function _fetch($keyName, $postIds, $periodFirst, $periodSecond, $table)
	{
		$db = Rp::getDbAdapter();
		$postIds = $db->quote($postIds);
		$periodFirst = $db->quote($periodFirst);
		$periodSecond = $db->quote($periodSecond);
		$sql = '
			SELECT DISTINCT
				persons.id,
				persons.persg,
				persons.pgtxt,
				persons.fullname,
				departments.name AS department,
				appointments.name AS appointment,
				cards_first.status_id AS statusFirstId,
				cards_second.status_id AS statusSecondId,
				statuses_first.name AS statusFirst,
				statuses_second.name AS statusSecond,
				ratings_first.name AS ratingFirst,
				ratings_second.name AS ratingSecond,
				employees.endtest_date

			FROM
				' . $table . ' posts_employees
				INNER JOIN user_rp_employees_PM employees
					ON posts_employees.' . $keyName . ' IN (' . $postIds . ')
					AND posts_employees.person_id = employees.person_id
				INNER JOIN user_rp_persons_PM persons
					ON employees.person_id = persons.id
				LEFT JOIN user_rp_ach_cards cards_first
					ON persons.id = cards_first.person_id
					AND cards_first.period = \'' . $periodFirst .'\'
				LEFT JOIN user_rp_ach_cards cards_second
					ON persons.id = cards_second.person_id
					AND cards_second.period = \'' . $periodSecond . '\'
				LEFT JOIN user_rp_departments departments
					ON employees.department_id = departments.id
				LEFT JOIN user_rp_appointments appointments
					ON employees.appointment_id = appointments.id
				LEFT JOIN user_rp_ach_cards_statuses statuses_first
					ON cards_first.status_id = statuses_first.id
				LEFT JOIN user_rp_ach_cards_statuses statuses_second
					ON cards_second.status_id = statuses_second.id
				LEFT JOIN user_rp_ach_ratings ratings_first
					ON cards_first.rtg_total_id = ratings_first.id
				LEFT JOIN user_rp_ach_ratings ratings_second
					ON cards_second.rtg_total_id = ratings_second.id

			ORDER BY
				persons.fullname

			--WHERE persons.out_date = \' \' OR persons.out_date = \'\'
		';

		return $db->fetchAll($sql);
	}
}