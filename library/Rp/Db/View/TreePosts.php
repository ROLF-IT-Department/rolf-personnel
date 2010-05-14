<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */

/**
 * ������ ������������� ������������� ��������� ����������.
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */
class Rp_Db_View_TreePosts extends Rp_Db_View_Abstract
{	
	protected $_name = 'user_rp_tree_posts';
	
	protected $_primary = 'id';
	
	protected $_rowClass = 'Rp_Db_View_Row_TreePost';
	
	/**
	 * ���������� ������ �������� ����������.
	 *
	 * @param int|array $id    ������������� ��� ������ ��������������� ����������.
	 * @param string    $order ������� ����������.
	 * 
	 * @return array
	 */
	public function fetchNames($id = null, $order = 'name')
	{
		return $this->_fetchPairs('name', $id, $order);
	}
	
	/**
	 * ���������� ����� ���������� (����� �����), ��������� 
	 * �� �������� $keyValue ���� $keyName.
	 * ���� �������� $columnLast = true, �� ����� ������� 
	 * � ������ ������ ���� "last", ������� ��������:
	 * 0 - ���� ��������� ����� �����������;
	 * 1 - ���� ��������� �� ����� ����������� (�������� ������� ������).
	 *
	 * @param string    $keyName    �������� ����, �� �������� ����� 
	 * ������������� ����� ����� ("id" ��� "pid").
	 * 
	 * @param int|array $keyValue   �������� ��� ������ �������� ���� $keyName.
	 * @param boolean   $columnLast
	 * 
	 * @return Rp_Db_View_Rowset
	 */
	private function _find($keyName, $keyValue, $columnLast)
	{
		$keyValue = $this->_quote($keyValue);
		
		if (!$columnLast) {
			$where = "$keyName IN ($keyValue)";
			return $this->fetchAll($where);
		}
		$sql = "
			SELECT DISTINCT
				posts.*,
				last = CASE WHEN subposts.id IS NULL THEN 1 ELSE 0 END
			FROM
				{$this->_name} posts
				LEFT JOIN {$this->_name} subposts
					ON posts.id = subposts.pid
			WHERE
				posts.$keyName IN ($keyValue)
			ORDER BY
				posts.name
		";
		$config = array(
			'view'     => $this,
			'data'     => $this->getAdapter()->fetchAll($sql),
			'rowClass' => $this->_rowClass
		);
		$rowsetClass = $this->getRowsetClass();
		return new $rowsetClass($config);
	}
	
	/**
	 * ���������� ����� ���������� (����� �����), ���������� 
	 * �� �������� ���������� �����.
	 * ���� �������� $columnLast = true, �� ����� ������� 
	 * � ������ ������ ���� "last", ������� ��������:
	 * 0 - ���� ��������� ����� �����������;
	 * 1 - ���� ��������� �� ����� ����������� (�������� ������� ������).
	 *
	 * @param int|array $id ������������� ��� ������ ��������������� ����������.
	 * @param boolean   $columnLast
	 * 
	 * @return Rp_Db_View_Rowset
	 */
	public function find($id, $columnLast = false)
	{
		return $this->_find($this->_primary, $id, $columnLast);
	}
	
	/**
	 * ���������� ��������� (������), �� ������� ��������� 
	 * ��������� � ��������� $employeeId.
	 *
	 * @param int $employeeId ������������� ����������.
	 * 
	 * @return Rp_Db_View_Row_TreePost ��� null, ���� ��������� �� �������.
	 */
	public function findByEmployeeId($employeeId)
	{
		$postsEmployees = new Rp_Db_View_TreePosts_Employees();
		$postIds = $postsEmployees->fetchPostIds($employeeId);
		return $this->find($postIds)->current();
	}
	
	
	public function findByFuncEmployeeId($employeeId)
	{
		$postsEmployees = new Rp_Db_View_TreePosts_Functional();
		$postIds = $postsEmployees->fetchPostIds($employeeId);
		return $this->find($postIds)->current();
	}
	/**
	 * ���������� ����������� ��������� (������������ ������) 
	 * ��� ��������� � ��������������� $id.
	 * ���� ��������� � ��������������� $id �� �������, �� ����� 
	 * ���������� false. ���� �� ������� ����������� ��������� 
	 * ���������� null.
	 * 
	 * @param int $id ������������� ���������.
	 * 
	 * @return Rp_Db_View_Row_TreePost ��� null, ���� ����������� ��������� �� �������.
	 */
	public function findParentPost($id)
	{
		$row = $this->find($id)->current();
		if (empty($row)) {
			return false;
		}
		return $this->find($row->pid)->current();
	}
	
	/**
	 * ���������� ����������� ��������� (�������� ������) 
	 * ��� ��������� � ��������������� $id.
	 * ���� �������� $columnLast = true, �� ����� ������� 
	 * � ������ ������ ���� "last", ������� ��������:
	 * 0 - ���� ��������� ����� �����������;
	 * 1 - ���� ��������� �� ����� ����������� (�������� ������� ������).
	 *
	 * @param int|array $id ������������� ��� ������ ��������������� ����������.
	 * @param boolean   $columnLast
	 * 
	 * @return Rp_Db_View_Rowset
	 */
	public function findChildPosts($id, $columnLast = false)
	{	
		return $this->_find('pid', $id, $columnLast);
	}
	
	/**
	 * ���������� ��������������� ��������� 
	 * ��� ��������� � ��������������� $id.
	 * ���� �������� $columnLast = true, �� ����� ������� 
	 * � ������ ������ ���� "last", ������� ��������:
	 * 0 - ���� ��������� ����� �����������;
	 * 1 - ���� ��������� �� ����� ����������� (�������� ������� ������).
	 *
	 * @param int|array $id ������������� ��� ������ ��������������� 
	 * ��������������� ����������.
	 * 
	 * @param boolean $columnLast
	 * 
	 * @return Rp_Db_View_Rowset
	 */
	public function findViewedPosts($id, $columnLast = false)
	{
		$postsViewers = new Rp_Db_View_TreePosts_Viewers();
		$viewedIds = $postsViewers->fetchPostViewedIds($id);
		return $this->find($viewedIds, $columnLast);
	}
	
	public function findFunctionalPosts($id, $columnLast = false)
	{
		$postsFunctional = new Rp_Db_View_TreePosts_Functional();
		$functionalIds = $postsFunctional->fetchPostFunctionalIds($id);
		return $this->find($functionalIds, $columnLast);
	}
	
	/**
	 * ���������� ����� ����� ����������, 
	 * ����������� �� ������ $level ������ ���������� ������������ 
	 * ������ ��������� � ��������������� $startId.
	 *
	 * @param int|array $startId ������������� ��� ������ ��������������� ����������.
	 * @param int       $level   ������� ������������ ������ ��������� $startId.
	 * ��������,
	 * level = 0: ������� ��������� $startId,
	 * level > 0: ������� ����������� ����������,
	 * level < 0: ������� ����������� ����������;
	 * 
	 * @return Rp_Db_View_Rowset
	 */
	public function fetchAllByLevel($startId, $level)
	{
		$level = (int) $level;
		
		if ($level == 0) {
			return $this->find($startId);
		}
		if ($level > 0) {
			$rows = $this->fetchAllByLevel($startId, $level - 1);
			return $this->find($rows->getCol('pid'));
		} else {
			$rows = $this->fetchAllByLevel($startId, $level + 1);
			return $this->findChildPosts($rows->getCol('id'));
		}
	}
}