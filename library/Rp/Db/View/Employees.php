<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */

/**
 * Объект представления штатных сотрудников.
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */
class Rp_Db_View_Employees extends Rp_Db_View_Abstract
{
	protected $_name = 'user_rp_employees_PM';

	protected $_primary = 'person_id';

	protected $_rowClass = 'Rp_Db_View_Row_Employee';

	/**
	 * Возвращает набор записей сотрудников, найденных
	 * по значению идентификатора физ. лица.
	 * Если физ. лицу соответствуют несколько сотрудников,
	 * то будут возвращены все записи. Полученный набор записей
	 * отсортирован сначала по значению идентификатора физ.лица, затем
	 * по значению даты увольнения по убыванию.
	 *
	 * @param int|array $personId Идентификатор или массив идентификаторов физ. лиц.
	 *
	 * @return Rp_Db_View_Rowset
	 */
	public function findByPersonId($personId)
	{
		$count = count($personId);

		$personId = $this->_quote($personId);
		$where = ($count == 1) ?  'person_id = ' . $personId  : 'person_id IN (' . $personId . ')';
		$order = 'person_id, dismissal_date DESC';

		return $this->fetchAll($where, $order);
	}

	public function find_full_info($person_id)
	{
		$db = Rp::getDbAdapter();
		$sql = '
			SELECT
				departments.name AS department,
				appointments.name AS appointment

			FROM
				' . $this->_name . ' AS employees

			LEFT JOIN user_rp_departments departments
				ON employees.department_id = departments.id
			LEFT JOIN user_rp_appointments appointments
				ON employees.appointment_id = appointments.id

			WHERE employees.person_id = ' . $person_id;

		return $db->fetchRow($sql);
	}
}