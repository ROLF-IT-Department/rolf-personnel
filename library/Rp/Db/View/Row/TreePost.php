<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */

/**
 * ќбъект строки представлени€ дерева должностей.
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */
class Rp_Db_View_Row_TreePost extends Rp_Db_View_Row   
{
	/**
	 * ¬озвращает вышесто€щую должность (родительскую строку).
	 * ≈сли выщесто€ща€ должность не найдена возвращает null.
	 * 
	 * @return Rp_Db_View_Row_TreePost »ли null, если вышесто€ща€ должность не найдена.
	 */
	public function findParentPost()
	{
		$view = $this->getView();
		
		return $view->find($this->pid)->current();
	}
	
	/**
	 * ¬озвращает подчиненные должности (дочерние строки).
	 * ≈сли аргумент $columnLast = true, то метод добавит 
	 * в каждой строке поле "last", которое содержит:
	 * 0 - если должность имеет подчиненных;
	 * 1 - если должность не имеет подчиненных (конечный элемент дерева).
	 *
	 * @param boolean $columnLast
	 * 
	 * @return Rp_Db_View_Rowset
	 */
	public function findChildPosts($columnLast = false)
	{	
		$view = $this->getView();
		
		return $view->findChildPosts($this->id, $columnLast);
	}
	
	/**
	 * ¬озвращает просматриваемые должности.
	 * ≈сли аргумент $columnLast = true, то метод добавит 
	 * в каждой строке поле "last", которое содержит:
	 * 0 - если должность имеет подчиненных;
	 * 1 - если должность не имеет подчиненных (конечный элемент дерева).
	 *
	 * @param boolean $columnLast
	 * 
	 * @return Rp_Db_View_Rowset
	 */
	public function findViewedPosts($columnLast = false)
	{
		$view = $this->getView();
		
		return $view->findViewedPosts($this->id, $columnLast);
	}
	
	public function findFunctionalPosts($columnLast = false)
	{
		$view = $this->getView();
		
		return $view->findFunctionalPosts($this->id, $columnLast);
	}
	
	/**
	 * ¬озвращает набор строк должностей, 
	 * наход€щихс€ на уровне $level дерева должностей относительно 
	 * уровн€ этой строки.
	 *
	 * @param int $level ”ровень относительно уровн€ этой должности.
	 * Ќапример,
	 * $level =  0: уровень этой должности;
	 * $level =  1: уровень должностей непосредственных руководителей;
	 * $level = -1: уровень должностей непосредственных подчиненных.
	 * 
	 * @return Rp_Db_View_Rowset
	 */
	public function fetchAllByLevel($level)
	{
		$view = $this->getView();
		
		if ($level > 0) {
			return $view->fetchAllByLevel($this->pid, $level - 1);
		}
		return $view->fetchAllByLevel($this->id, $level);
	}
}