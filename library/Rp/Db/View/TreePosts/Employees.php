<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */

/**
 * Объект представления сотрудников из иерархической структуры должностей.
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */
class Rp_Db_View_TreePosts_Employees extends Rp_Db_View_Abstract
{	
	protected $_name = 'user_rp_tree_posts_employees';
	
	/**
	 * Возвращает массив идентификаторов должностей, 
	 * на которых находятся сотрудники с идентификаторами $employeeId.
	 *
	 * @param  int|array $employeeId Идентификатор или массив идентификаторов сотрудников.
	 * @return array
	 */
	public function fetchPostIds($employeeId)
	{
		$employeeId = $this->_quote($employeeId);
		$where = "person_id IN ($employeeId)";
		return $this->_fetchCol('post_id', $where);
	}
	
	/**
	 * Возвращает массив идентификаторов сотрудников, 
	 * находящихся на должности с идентификатором $postId.
	 *
	 * @param  int|array $postId Идентификатор или массив идентификаторов должности.
	 * @return array
	 */
	public function fetchEmployeeIds($postId)
	{
		$postId = $this->_quote($postId);
		$where = "post_id IN ($postId)";
		return $this->_fetchCol('person_id', $where);
	}
}