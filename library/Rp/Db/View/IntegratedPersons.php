<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */

/**
 * Объект представления совмещенных табельников.
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */
class Rp_Db_View_IntegratedPersons extends Rp_Db_View_Abstract
{
	protected $_name = 'user_rp_persons_integrated_PM';

	protected $_primary = 'person_id';

	/**
	 * Возвращает массив названий должностей.
	 *
	 * @param null|int|array $person_id Идентификатор или массив идентификаторов сотрудников.
	 * @return array|null
	 */
	public function fetchRefID($person_id = null)
	{
		return ($person_id === NULL) ?  NULL : $this->_fetchCol('ref_id', 'person_id = ' . $person_id);
	}

	/**
	 * 
	 * @param null|int $ref_id
	 * @return array|null
	 */
	public function fetchPersonID($ref_id = NULL)
	{
		return ($ref_id === NULL) ? NULL : $this->_fetchCol('person_id', 'ref_id = ' . $ref_id);
	}
}