<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */

/**
 * ������ ������ �������� ����������.
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */
class Rp_Db_Table_Row_Ach_Card extends Rp_Db_Table_Row 
{
	/**
	 * ������ ������ ����������.
	 * 
	 * @var Rp_Db_View_Row_Employee
	 */
	protected $_employee = null;
	
	/**
	 * ������ ������� ������-�����.
	 *
	 * @var Rp_Db_Table_Ach_Tasks
	 */
	protected $_tableTasks = null;
	
	/**
	 * ������ ������� ����������� ��������.
	 *
	 * @var Rp_Db_Table_Ach_Cards_Competences
	 */
	protected $_tableCardsCompetences = null;
	
	/**
	 * ������ ������� ����� ����. ��������.
	 *
	 * @var Rp_Db_Table_Ach_Trainings
	 */
	protected $_tableTrainings = null;
	
	/**
	 * ���������� ������ ������ ����������.
	 *
	 * @return Rp_Db_View_Row_Employee
	 */
	public function getEmployee()
	{
		if (empty($this->_employee)) {
			$employees = new Rp_Db_View_Employees();
			$this->_employee = $employees->findByPersonId($this->person_id)->current();
		}
		return $this->_employee;
	}
	
	/**
	 * ���������� ������ ������� ������-�����.
	 *
	 * @return Rp_Db_Table_Ach_Tasks
	 */
	public function getTableTasks()
	{
		if (empty($this->_tableTasks)) {
			$this->_tableTasks = new Rp_Db_Table_Ach_Tasks();
		}
		return $this->_tableTasks;
	}
	
	/**
	 * ���������� ������ ������� ����������� ��������.
	 *
	 * @return Rp_Db_Table_Ach_Cards_Competences
	 */
	public function getTableCardsCompetences()
	{
		if (empty($this->_tableCardsCompetences)) {
			$this->_tableCardsCompetences = new Rp_Db_Table_Ach_Cards_Competences();
		}
		return $this->_tableCardsCompetences;
	}
	
	/**
	 * ���������� ������ ������� ����� ����. ��������.
	 *
	 * @return Rp_Db_Table_Ach_Trainings
	 */
	public function getTableTrainings()
	{
		if (empty($this->_tableTrainings)) {
			$this->_tableTrainings = new Rp_Db_Table_Ach_Trainings();
		}
		return $this->_tableTrainings;
	}


	
	/**
	 * ��������� ������-���� � ��������.
	 *
	 * @param array $tasks ������ ������-�����.
	 * 
	 * @return void
	 */
	public function insertTasks(array $tasks)
	{
		$tableTasks = $this->getTableTasks();
		
		foreach ($tasks as $row) {
			$row['card_id'] = $this->id;
			$tableTasks->insert($row);
		}
	}
	
	/**
	 * ��������� ������-����.
	 *
	 * @param array $tasks ������ ������-�����.
	 * 
	 * @return void
	 */
	public function updateTasks(array $tasks)
	{
		$tableTasks = $this->getTableTasks();
		
		foreach ($tasks as $id => $row) {
			$tableTasks->update($row, $id);
		}
	}
	
	
	public function insertCompetences(array $competences)
	{
		$tableCompetences = $this->getTableCardsCompetences();
		
		foreach ($competences as $row) {
			$row['card_id'] = $this->id;
			$tableCompetences->insert($row);
		}
	}
	
	/**
	 * ��������� �����������.
	 *
	 * @param array $competences ������ �����������.
	 * 
	 * @return void
	 */
	public function updateCompetences(array $competences)
	{
		$tableCompetences = $this->getTableCardsCompetences();
		
		foreach ($competences as $id => $row) {
			$tableCompetences->update($row, $id);
		}
	}
	
	/**
	 * ��������� ���� ����. �������� � ��������.
	 *
	 * @param array $trainings ������ ����� ����. ��������.
	 * 
	 * @return void
	 */
	public function insertTrainings(array $trainings)
	{
		$tableTrainings = $this->getTableTrainings();
		
		foreach ($trainings as $row) {
			$row['card_id'] = $this->id;
			$tableTrainings->insert($row);
		}
	}
	
	/**
	 * ��������� ���� ����. ��������.
	 *
	 * @param array $trainings ������ ����� ����. ��������.
	 * 
	 * @return void
	 */
	public function updateTrainings(array $trainings)
	{
		$tableTrainings = $this->getTableTrainings();
		
		foreach ($trainings as $id => $row) {
			$tableTrainings->update($row, $id);
		}
	}
	
	/**
	 * ���������� ����� ������-�����.
	 *
	 * @return Rp_Db_Table_Rowset
	 */
	public function fetchTasks()
	{
		return $this->getTableTasks()->findByCardId($this->id);
	}
	
	public function fetchPersonalTasks()
	{
		return $this->getTableTasks()->findByCardIdAndPersonal($this->id);
	}
	
	/**
	 * ���������� ����� �����������.
	 *
	 * @return Rp_Db_Table_Rowset
	 */
	public function fetchCompetences()
	{
		return $this->getTableCardsCompetences()->findByCardId($this->id);
	}
	
	public function fetchPersonalCompetences()
	{
		return $this->getTableCardsCompetences()->findByCardIdAndPersonal($this->id);
	}
	
	/**
	 * ���������� ����� ����� ����. ��������.
	 *
	 * @return Rp_Db_Table_Rowset
	 */
	public function fetchTrainings()
	{
		return $this->getTableTrainings()->findByCardId($this->id);
	}
	
	public function fetchPersonalTrainings()
	{
		return $this->getTableTrainings()->findByCardIdAndPersonal($this->id);
	}
}