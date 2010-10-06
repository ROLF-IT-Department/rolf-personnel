<?php

class Personal_DataModel
{	
	protected $_data = array();
	
	public function __construct($personId)
	{
		$employees = new Rp_Db_View_Employees();
		$companies = new Rp_Db_View_Companies();
		$departments = new Rp_Db_View_Departments();
		
		$employee = $employees->findByPersonId($personId)->current();
		$person = $employee->getPerson();
		
		// если ID компании и ID отдела совпадают, то в поле отдел пишем знак '-', так как люди привязаны к юр. лицу напрямую
		$dep_name = "-";
		if ($employee->department_id != '-')
			$dep_name = current($departments->fetchNames($employee->department_id));
			
		$redundant = '';
		if ($employee->isRedundant()) {
			$redundant = '(уволен ' . date('d.m.Y', strtotime($employee->dismissal_date)) . ')';
		}
		
		$this->_data['fullname'] = $person->fullname;
		$this->_data['date_birth'] = date('d.m.Y', strtotime($person->date_birth));
		$this->_data['company'] = current($companies->fetchNames($employee->company_id));
		$this->_data['department'] = $dep_name;
		$this->_data['redundant'] = $redundant;
		
		$attribs = $employee->getAttribs();
		if (!empty($attribs)) {
			$this->_data += $attribs->toArray();
		}
	}
	
	public function __get($field)
	{
		if (array_key_exists($field, $this->_data)) {
			return $this->_data[$field];
		}
		return null;
	}
}