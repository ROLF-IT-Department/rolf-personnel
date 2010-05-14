<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */

/**
 * ������ ������ ������������� ������ ����������.
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */
class Rp_Db_View_Row_TreePost extends Rp_Db_View_Row   
{
	/**
	 * ���������� ����������� ��������� (������������ ������).
	 * ���� ����������� ��������� �� ������� ���������� null.
	 * 
	 * @return Rp_Db_View_Row_TreePost ��� null, ���� ����������� ��������� �� �������.
	 */
	public function findParentPost()
	{
		$view = $this->getView();
		
		return $view->find($this->pid)->current();
	}
	
	/**
	 * ���������� ����������� ��������� (�������� ������).
	 * ���� �������� $columnLast = true, �� ����� ������� 
	 * � ������ ������ ���� "last", ������� ��������:
	 * 0 - ���� ��������� ����� �����������;
	 * 1 - ���� ��������� �� ����� ����������� (�������� ������� ������).
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
	 * ���������� ��������������� ���������.
	 * ���� �������� $columnLast = true, �� ����� ������� 
	 * � ������ ������ ���� "last", ������� ��������:
	 * 0 - ���� ��������� ����� �����������;
	 * 1 - ���� ��������� �� ����� ����������� (�������� ������� ������).
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
	 * ���������� ����� ����� ����������, 
	 * ����������� �� ������ $level ������ ���������� ������������ 
	 * ������ ���� ������.
	 *
	 * @param int $level ������� ������������ ������ ���� ���������.
	 * ��������,
	 * $level =  0: ������� ���� ���������;
	 * $level =  1: ������� ���������� ���������������� �������������;
	 * $level = -1: ������� ���������� ���������������� �����������.
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