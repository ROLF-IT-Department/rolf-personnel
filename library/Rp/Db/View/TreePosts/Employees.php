<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */

/**
 * ������ ������������� ����������� �� ������������� ��������� ����������.
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */
class Rp_Db_View_TreePosts_Employees extends Rp_Db_View_Abstract
{	
	protected $_name = 'user_rp_tree_posts_employees_PM';
	
	/**
	 * ���������� ������ ��������������� ����������, 
	 * �� ������� ��������� ���������� � ���������������� $employeeId.
	 *
	 * @param  int|array $employeeId ������������� ��� ������ ��������������� �����������.
	 * @return array
	 */
	public function fetchPostIds($employeeId)
	{
		$employeeId = $this->_quote($employeeId);
		$where = "person_id IN ($employeeId)";
		return $this->_fetchCol('post_id', $where);
	}
	
	/**
	 * ���������� ������ ��������������� �����������, 
	 * ����������� �� ��������� � ��������������� $postId.
	 *
	 * @param  int|array $postId ������������� ��� ������ ��������������� ���������.
	 * @return array
	 */
	public function fetchEmployeeIds($postId)
	{
		$postId = $this->_quote($postId);
		$where = "post_id IN ($postId)";
		return $this->_fetchCol('person_id', $where);
	}
}