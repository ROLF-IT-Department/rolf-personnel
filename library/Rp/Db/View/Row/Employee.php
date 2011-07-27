<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */

/**
 * ����� ������ ������������� ������� �����������.
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */
class Rp_Db_View_Row_Employee extends Rp_Db_View_Row
{
	/**
	 * ������ ����������� ����.
	 *
	 * @var Rp_Db_View_Row_Person
	 */
	protected $_person = null;

	/**
	 * ���������������� ������������ ����������.
	 *
	 * @var Rp_Db_View_Rowset
	 */
	protected $_managers = null;

	/**
	 * �������������� ������������ ����������.
	 *
	 * @var Rp_Db_View_Rowset
	 */
	protected $_func_managers = null;

	/**
	 * ��������� �� ������ ����������.
	 *
	 * @var Rp_Db_View_Row_TreePost
	 */
	protected $_treePost = null;

	/**
	 * ���������� ������ ������ ���. ����.
	 *
	 * @return Rp_Db_View_Row_Person
	 */
	public function getPerson()
	{
		if (empty($this->_person)) {
			$persons = new Rp_Db_View_Persons();
			$persons = $persons->find($this->person_id);

			foreach($persons as $person)
			{
				if($person->netname AND $person->out_date == ' ')
				{
					$this->_person = $person;
				}
			}
		}
		return $this->_person;
	}

	/**
	 * ���������� ������ ������ ��������� ����������
	 * �� ������ ����������.
	 *
	 * @return Rp_Db_View_Row_TreePost
	 * @throws Exception
	 */
	public function getTreePost()
	{
		if (empty($this->_treePost))
		{
			$treePosts = new Rp_Db_View_TreePosts();
			$this->_treePost = $treePosts->findByEmployeeId($this->person_id);
			if (empty($this->_treePost))
			{
				throw new Zend_Exception('��������� �� ��������� � ������������� ��������� ����������.');
			}
		}
		return $this->_treePost;
	}

	/**
	 * ���������� ������ ������ ��������� ����������.
	 *
	 * @return Rp_Db_View_Row ��� null, ���� ������ ��������� �� �������.
	 */
	public function getAttribs()
	{
		$attribs = new Rp_Db_View_Employees_Attribs();
		$persons = new Rp_Db_View_Persons();
		$person_id = $persons->fetchRow('id = '.$this->person_id, 'id DESC');

		return $attribs->find($person_id->New_ID)->current();
	}

	/**
	 * ���������� true, ���� ��������� ��������
	 * �� ������� ������ ������� � ��������� � �������������
	 * ��������� ����������, ����� ���������� false.
	 *
	 * @return boolean
	 */
	public function isCurrent()
	{
		try {
			return (bool) $this->getTreePost();
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * ���������� true, ���� ��������� �������� ���������
	 * �� ������� ������ �������, ����� ���������� false.
	 *
	 * @return boolean
	 */
	public function isRedundant()
	{
		if (count($this->dismissal_date) < 8) return false;

		if ($this->dismissal_date < date('Y-m-d')) {
			return true;
		}
		return false;
	}

	/**
	 * ���������� true, ���� ��������� �������� ����������� ���������,
	 * ����� ���������� false.
	 *
	 * @return boolean
	 */
	public function isManager()
	{
		$childPosts = $this->getTreePost()->findChildPosts();

		return count($childPosts) > 0;
	}

	/**
	 * ���������� true, ���� ��������� �������� ���������
	 * � ��������� "���-��������", ����� ���������� false.
	 *
	 * @return boolean
	 */
	public function isTop()
	{
		$postId = $this->getTreePost()->id;
		$postsTops = new Rp_Db_View_TreePosts_Tops();

		return count($postsTops->find($postId)) > 0;
	}

	/**
	 * ���������� ���������������� ������������� ����������.
	 *
	 * @return Rp_Db_View_Rowset
	 */
	public function getManagers()
	{
		if (empty($this->_managers)) {
			$treePost = $this->getTreePost();			// 	�������� id ��������� ���������� �� ������ ����������
			$postsEmps = new Rp_Db_View_TreePosts_Employees();
			$empIds = $postsEmps->fetchEmployeeIds($treePost->pid);   // �������� � ������ id ����������������� ������������ ����������
			while (empty($empIds) && ($treePost = $treePost->findParentPost())) {
				$empIds = $postsEmps->fetchEmployeeIds($treePost->pid);
			}
			$this->_managers = $this->getView()->find($empIds);
		}
		return $this->_managers;
	}

	/**
	 * ���������� ����������� ������������� ����������.
	 *
	 * @return Rp_Db_View_Rowset
	 */
	public function getHighManagers()
	{
		$parentPost = $this->getTreePost()->findParentPost();
		if (empty($parentPost) || empty($parentPost->pid)) {
			return $this->getManagers();
		}
		$postsEmps = new Rp_Db_View_TreePosts_Employees();
		$empIds = $postsEmps->fetchEmployeeIds($parentPost->pid);
		if (empty($empIds)) {
			return $this->getManagers();
		}
		return $this->getView()->find($empIds);
	}

	/**
	 * ���������� �������������� ������������� ����������.
	 *
	 * @return Rp_Db_View_Rowset
	 */
	public function getFuncManagers()				// ���������� employee_id ��� �������������� �������������
	{
		if (empty($this->_func_managers)) {
			$treePost = $this->getTreePost();
			$postsEmps = new Rp_Db_View_TreePosts_Func();
			$empIds = $postsEmps->fetchFuncEmployeeIds($treePost->id);		// ���������� ������ post_func_id, ����� �� ��� ���� employee_id
			$emp = new Rp_Db_View_TreePosts_Employees();
			$func = $emp->fetchEmployeeIds($empIds);		// ������� employee_id �������������� �������������
			$this->_func_managers = $this->getView()->find($func);
		}
		return $this->_func_managers;
	}
}