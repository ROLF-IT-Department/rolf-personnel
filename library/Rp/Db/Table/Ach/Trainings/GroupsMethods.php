<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */

/**
 * ������ ������� ����������� ����� ������� ����. ��������.
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */
class Rp_Db_Table_Ach_Trainings_GroupsMethods extends Rp_Db_Table_Abstract
{
	protected $_name = 'user_rp_ach_trainings_groups_methods';

	protected $_dependentTables = array('Rp_Db_Table_Ach_Trainings_Methods');

	/**
	 * ���������� ������ �������� ����� �������.
	 *
	 * @return array
	 */
	public function fetchNames()
	{
		return $this->_fetchPairs('name', null, 'sort, name');
	}

	/**
	 * ���������� ������ �������� ����� �������.
	 *
	 * @return array
	 */
	public function fetchDisabledNames()
	{
		return $this->_fetchPairs('name', 'disabled is null', 'sort, name', 'platform');
	}

	/**
	 * ���������� ��� �������� ����� � ������� � ���� �������.
	 * ������� ����� ������� �������� �������� ����� �������,
	 * � ���������� - ������� �������� ������� ��������������� ������.
	 *
	 * @return array
	 */
	public function toArrayNames()
	{
		$groups = $this->fetchNames();
		$methods = new Rp_Db_Table_Ach_Trainings_Methods();
		$methods = $methods->fetchAll(null, array('sort', 'name'))->toArray();

		foreach ($methods as $item) {
			$group =& $groups[$item['group_id']];
			if (!is_array($group)) {
				$group = array(
					'name'    => $group,
					'methods' => array()
				);
			}
			$group['methods'][$item['id']] = $item['name'];
		}
		$groupsNames = array();
		foreach ($groups as $item) {
			if (is_array($item)) {
				$groupsNames[$item['name']] = $item['methods'];
			}
		}

		return $groupsNames;
	}

	/**
	 * ���������� ������ ���������� �������� ����� � ������� � ���� �������.
	 * ������� ����� ������� �������� �������� ����� �������,
	 * � ���������� - ������� �������� ������� ��������������� ������.
	 *
	 * @return array
	 */
	public function toArrayNamesWithoutDisabled($platform = NULL)
	{
		$groups = $this->fetchDisabledNames();
		$methods = new Rp_Db_Table_Ach_Trainings_Methods();
		$methods = $methods->fetchAll(array('disabled is null', 'platform = \''.$platform.'\''), array('sort', 'name'))->toArray();

		foreach ($methods as $item) {
			$group =& $groups[$item['group_id']];
			if (!is_array($group)) {
				$group = array(
					'name'    => $group,
					'methods' => array()
				);
			}
			$group['methods'][$item['id']] = $item['name'];
		}
		$groupsNames = array();
		foreach ($groups as $item) {
			if (is_array($item)) {
				$groupsNames[$item['name']] = $item['methods'];
			}
		}

		return $groupsNames;
	}
}