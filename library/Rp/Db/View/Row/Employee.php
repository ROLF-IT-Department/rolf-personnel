<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */

/**
 *  ласс строки представлени€ штатных сотрудников.
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */
class Rp_Db_View_Row_Employee extends Rp_Db_View_Row
{
	/**
	 * ќбъект физического лица.
	 *
	 * @var Rp_Db_View_Row_Person
	 */
	protected $_person = null;

	/**
	 * Ќепосредственные руководители сотрудника.
	 *
	 * @var Rp_Db_View_Rowset
	 */
	protected $_managers = null;

	/**
	 * ‘ункциональные руководители сотрудника.
	 *
	 * @var Rp_Db_View_Rowset
	 */
	protected $_func_managers = null;

	/**
	 * ƒолжность из дерева должностей.
	 *
	 * @var Rp_Db_View_Row_TreePost
	 */
	protected $_treePost = null;

	/**
	 * ¬озвращает объект строки физ. лица.
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
	 * ¬озвращает объект строки должности сотрудника
	 * из дерева должностей.
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
				throw new Zend_Exception('—отрудник не определен в иерархической структуре должностей.');
			}
		}
		return $this->_treePost;
	}

	/**
	 * ¬озвращает объект строки атрибутов сотрудника.
	 *
	 * @return Rp_Db_View_Row или null, если строка атрибутов не найдена.
	 */
	public function getAttribs()
	{
		$attribs = new Rp_Db_View_Employees_Attribs();
		$persons = new Rp_Db_View_Persons();
		$person_id = $persons->fetchRow('id = '.$this->person_id, 'id DESC');

		return $attribs->find($person_id->New_ID)->current();
	}

	/**
	 * ¬озвращает true, если сотрудник работает
	 * на текущий момент времени и определен в иерархической
	 * структуре должностей, иначе возвращает false.
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
	 * ¬озвращает true, если сотрудник €вл€етс€ уволенным
	 * на текущий момент времени, иначе возвращает false.
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
	 * ¬озвращает true, если сотрудник занимает руковод€щую должность,
	 * иначе возвращает false.
	 *
	 * @return boolean
	 */
	public function isManager()
	{
		$childPosts = $this->getTreePost()->findChildPosts();

		return count($childPosts) > 0;
	}

	/**
	 * ¬озвращает true, если сотрудник занимает должность
	 * с атрибутом "“оп-менеджер", иначе возвращает false.
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
	 * ¬озвращает непосредственных руководителей сотрудника.
	 *
	 * @return Rp_Db_View_Rowset
	 */
	public function getManagers()
	{
		if (empty($this->_managers)) {
			$treePost = $this->getTreePost();			// 	получаем id должности сотрудника из дерева должностей
			$postsEmps = new Rp_Db_View_TreePosts_Employees();
			$empIds = $postsEmps->fetchEmployeeIds($treePost->pid);   // посылаем в запрос id непосредственного руководител€ сотрудника
			while (empty($empIds) && ($treePost = $treePost->findParentPost())) {
				$empIds = $postsEmps->fetchEmployeeIds($treePost->pid);
			}
			$this->_managers = $this->getView()->find($empIds);
		}
		return $this->_managers;
	}

	/**
	 * ¬озвращает вышесто€щих руководителей сотрудника.
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
	 * ¬озвращает функциональных руководителей сотрудника.
	 *
	 * @return Rp_Db_View_Rowset
	 */
	public function getFuncManagers()				// возвращаем employee_id дл€ функциональных руководителей
	{
		if (empty($this->_func_managers)) {
			$treePost = $this->getTreePost();
			$postsEmps = new Rp_Db_View_TreePosts_Func();
			$empIds = $postsEmps->fetchFuncEmployeeIds($treePost->id);		// возвращаем массив post_func_id, затем по ним ищем employee_id
			$emp = new Rp_Db_View_TreePosts_Employees();
			$func = $emp->fetchEmployeeIds($empIds);		// находим employee_id функциональных руководителей
			$this->_func_managers = $this->getView()->find($func);
		}
		return $this->_func_managers;
	}
}