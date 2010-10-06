<?php

// Класс для обработки события onunload

class LogoffController extends Zend_Controller_Action
{
	public function indexAction()
	{
		$date = date("m.d.y H:i:s");
		$sid = session_id();
		
		$log = new Rp_Db_Table_Logon;
		
		$data = array('date_logoff'=>$date);
		$log->updateLogoff($data, $sid);
		
	}
}