<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */

/**
 * Объект таблицы справочника групп методов проф. развития.
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
	 * Возвращает массив названий групп методов.
	 *
	 * @return array
	 */
	public function fetchNames()
	{
		return $this->_fetchPairs('name', null, 'sort, name');
	}

	/**
	 * Возвращает массив названий групп методов.
	 *
	 * @return array
	 */
	public function fetchDisabledNames()
	{
		return $this->_fetchPairs('name', 'disabled is null', 'sort, name', 'platform');
//		return $this->_fetchPairs('name', 'disabled is null', 'sort, name');
	}
	
	/**
	 * Возвращает ВСЕ названия групп и методов в виде массива.
	 * Ключами этого массива являются названия групп методов, 
	 * а значениями - массивы названий методов соответствующей группы.
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
	 * Возвращает ТОЛЬКО АКТУАЛЬНЫЕ названия групп и методов в виде массива.
	 * Ключами этого массива являются названия групп методов, 
	 * а значениями - массивы названий методов соответствующей группы.
	 *
	 * @return array
	 */
	public function toArrayNamesWithoutDisabled()
	{
		$groups = $this->fetchDisabledNames();
		$methods = new Rp_Db_Table_Ach_Trainings_Methods();
		$methods = $methods->fetchAll('disabled is null', array('sort', 'name'))->toArray();

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