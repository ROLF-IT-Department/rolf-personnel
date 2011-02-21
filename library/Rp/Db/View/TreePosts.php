<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */

/**
 * Объект представления иерархической структуры должностей.
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
	 * Возвращает массив названий должностей.
	 *
	 * @param int|array $id    Идентификатор или массив идентификаторов должностей.
	 * @param string    $order Условие сортировки.
	 *
	 * @return array
	 */
	public function fetchNames($id = null, $order = 'name')
	{
		return $this->_fetchPairs('name', $id, $order);
	}

	/**
	 * Возвращает набор должностей (набор строк), найденных
	 * по значению $keyValue поля $keyName.
	 * Если аргумент $columnLast = true, то метод добавит
	 * в каждой строке поле "last", которое содержит:
	 * 0 - если должность имеет подчиненных;
	 * 1 - если должность не имеет подчиненных (конечный элемент дерева).
	 *
	 * @param string    $keyName    Название поля, по которому будет
	 * производиться поиск строк ("id" или "pid").
	 *
	 * @param int|array $keyValue   Значение или массив значений поля $keyName.
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
	 * Возвращает набор должностей (набор строк), отобранных
	 * по значению первичного ключа.
	 * Если аргумент $columnLast = true, то метод добавит
	 * в каждой строке поле "last", которое содержит:
	 * 0 - если должность имеет подчиненных;
	 * 1 - если должность не имеет подчиненных (конечный элемент дерева).
	 *
	 * @param int|array $id Идентификатор или массив идентификаторов должностей.
	 * @param boolean   $columnLast
	 *
	 * @return Rp_Db_View_Rowset
	 */
	public function find($id, $columnLast = false)
	{
		return $this->_find($this->_primary, $id, $columnLast);
	}

	/**
	 * Возвращает должность (строку), на которой находится
	 * сотрудник с указанным $employeeId.
	 *
	 * @param int $employeeId Идентификатор сотрудника.
	 *
	 * @return Rp_Db_View_Row_TreePost или null, если должность не найдена.
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
	 * Возвращает вышестоящую должность (родительскую строку)
	 * для должности с идентификатором $id.
	 * Если должность с идентификатором $id не найдена, то метод
	 * возвращает false. Если не найдена выщестоящая должность
	 * возвращает null.
	 *
	 * @param int $id Идентификатор должности.
	 *
	 * @return Rp_Db_View_Row_TreePost или null, если вышестоящая должность не найдена.
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
	 * Возвращает подчиненные должности (дочерние строки)
	 * для должности с идентификатором $id.
	 * Если аргумент $columnLast = true, то метод добавит
	 * в каждой строке поле "last", которое содержит:
	 * 0 - если должность имеет подчиненных;
	 * 1 - если должность не имеет подчиненных (конечный элемент дерева).
	 *
	 * @param int|array $id Идентификатор или массив идентификаторов должностей.
	 * @param boolean   $columnLast
	 *
	 * @return Rp_Db_View_Rowset
	 */
	public function findChildPosts($id, $columnLast = false)
	{
		return $this->_find('pid', $id, $columnLast);
	}

	/**
	 * Возвращает просматриваемые должности
	 * для должности с идентификатором $id.
	 * Если аргумент $columnLast = true, то метод добавит
	 * в каждой строке поле "last", которое содержит:
	 * 0 - если должность имеет подчиненных;
	 * 1 - если должность не имеет подчиненных (конечный элемент дерева).
	 *
	 * @param int|array $id Идентификатор или массив идентификаторов
	 * просматривающих должностей.
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
	 * Возвращает набор строк должностей,
	 * находящихся на уровне $level дерева должностей относительно
	 * уровня должности с идентификатором $startId.
	 *
	 * @param int|array $startId Идентификатор или массив идентификаторов должностей.
	 * @param int       $level   Уровень относительно уровня должности $startId.
	 * Например,
	 * level = 0: уровень должности $startId,
	 * level > 0: уровень вышестоящих должностей,
	 * level < 0: уровень нижестоящих должностей;
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