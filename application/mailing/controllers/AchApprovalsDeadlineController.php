<?php

class Mailing_AchApprovalsDeadlineController extends Zend_Controller_Action
{
	const DAYS_LIMIT = 0;
	
	const TIME_LIMIT = 1200;
	
	private $_logHandle = null;
	
	private $_countSending = 0;
	
	private $_countErrors = 0;
	
	public function indexAction()
	{	
		set_time_limit(self::TIME_LIMIT);
		
		$logPath = './log/log_' . date('YmdHis') . '.txt';
		$this->_logHandle = @fopen($logPath, 'xb');
		
		$maxTimeCheck = time() - self::DAYS_LIMIT * 24 * 60 * 60;
		$maxDateCheck = date('Y-m-d H:i:s', $maxTimeCheck);
		/*
		$sql = "
			SELECT 
				cards.id,
				cards.person_id,
				cards.period,
				cards.status_id,
				cards.status_date,
				cards.plan_emp_status,
				cards.plan_hmg_status,
				cards.plan_fnc_status,
				cards.rate_emp_status,
				cards.rate_hmg_status,
				cards.rate_fnc_status,
				persons.fullname AS emp_fullname,
				persons.email AS emp_email,
				dbo.user_func_rp_tree_post_persons_emails(tree_posts.id, ',') AS mngs_emails,
				dbo.user_func_rp_tree_post_persons_emails(tree_posts.pid, ',') AS hmgs_emails,
				dbo.user_func_rp_tree_post_persons_emails(tree_posts_func.post_func_id, ',') AS func_emails
			FROM
				user_rp_ach_cards cards
				INNER JOIN user_rp_persons_PM persons
					ON cards.person_id = persons.id
				INNER JOIN user_rp_tree_posts_employees_PM posts_employees
					ON cards.person_id = posts_employees.person_id
				INNER JOIN user_rp_tree_posts tree_posts
					ON posts_employees.post_pid = tree_posts.id
				LEFT JOIN user_rp_tree_posts_func tree_posts_func
					ON cards.person_id = tree_posts_func.person_id
			WHERE
				cards.status_id IN ('CPN', 'CRG') AND cards.status_date < '$maxDateCheck'
			ORDER BY
				cards.status_date
		";
		*/
		
		
		$sql = "
			SELECT 
				cards.id,
				cards.person_id,
				cards.period,
				cards.status_id,
				cards.status_date,
				cards.plan_emp_status,
				cards.plan_hmg_status,
				cards.plan_fnc_status,
				cards.rate_emp_status,
				cards.rate_hmg_status,
				cards.rate_fnc_status,
				persons.fullname AS emp_fullname,
				persons.email AS emp_email,
				dbo.user_func_rp_tree_post_persons_emails(tree_posts.id, ',') AS mngs_emails,
				dbo.user_func_rp_tree_post_persons_emails(tree_posts.pid, ',') AS hmgs_emails,
				dbo.user_func_rp_tree_post_persons_emails(tree_posts_func.post_func_id, ',') AS func_emails
			FROM
				user_rp_ach_cards cards
				INNER JOIN user_rp_persons_PM persons
					ON cards.person_id = persons.id
				INNER JOIN user_rp_tree_posts_employees_PM posts_employees
					ON cards.person_id = posts_employees.person_id
				INNER JOIN user_rp_tree_posts tree_posts
					ON posts_employees.post_pid = tree_posts.id
				LEFT JOIN user_rp_tree_posts_func tree_posts_func
					ON cards.person_id = tree_posts_func.person_id
			WHERE
				cards.status_id IN ('CPN', 'CRG') AND cards.period > (YEAR(GETDATE()) - 2)
			ORDER BY
				cards.status_date
		";
		
		$db = Rp::getDbAdapter();
		$rows = $db->fetchAll($sql);
		
		$ps = "\n"
		    . "\n" . '----------------'
		    . "\n" . '��� ������ ������� ������������� � �� ������� ������.'
		    . "\n" . '�� ���� ����������� �������� ����������� � HR-���������'
		    . ' � ������������ �� ��������� ������ �������������.';
		
		foreach ($rows as $row) 
		{		
			
			if ($row['status_id'] == 'CPN') 
			{
				
				// ������������ ������������.
				$message = "\n" . "�������� ���������� �� {$row['period']} ��� (��������� {$row['emp_fullname']})"
				         . " ���� ���������� �� ������������ {$row['status_date']}."
				         . "\n" . '�� ������ ������ �� �������� ������������ ��'
				         . ($row['plan_emp_status'] ? '' : ' ����������' . ($row['plan_hmg_status'] ? '' : ' �'))
				         . ($row['plan_hmg_status'] ? '' : ' ������������ ������������') 
				         . ($row['plan_fnc_status'] ? '' : ' , a ����� �� ��������������� ������������') . '.'
				         . "\n" . '����������, ����������� ���� ��� ���������� �������� ������������ ��������.'
				         . $ps;
				$mails = explode(',', $row['mngs_emails']);
				$this->_sendMail($mails, $message);
				if (!$row['plan_emp_status'] && $row['emp_email']) {
					$message = "\n" . '���� �������� ���������� �� ' . $row['period'] . ' ���'
					         . ' ���� ���������� �� ������������ ����� ������������� ' . $row['status_date'] . '.'
					         . "\n" . '����������, ����������� ���� �������� � ������������� ������ � ��������.'
					         . $ps;
					$this->_sendMail($row['emp_email'], $message);
				}
				if (!$row['plan_hmg_status']) {
					$message = "\n" . '�������� ���������� �� ' . $row['period'] . ' ���'
					         . ' (��������� ' . $row['emp_fullname'] . ')'
					         . ' ���� ���������� ������������� �� ������������ ' . $row['status_date'] . '.'
						     . "\n" . '����������, ����������� ���� �������� � ������������� ������ � ��������.'
						     . $ps;
					$mails = explode(',', $row['hmgs_emails']);
					$this->_sendMail($mails, $message);
				}
				if (!$row['plan_fnc_status']) {
					$message = "\n" . '�������� ���������� �� ' . $row['period'] . ' ���'
					         . ' (��������� ' . $row['emp_fullname'] . ')'
					         . ' ���� ���������� ������������� �� ������������ ' . $row['status_date'] . '.'
						     . "\n" . '����������, ����������� ���� �������� � ������������� ������ � ��������.'
						     . $ps;
					$mails = explode(',', $row['func_emails']);
					$this->_sendMail($mails, $message);
				}
			} else if ($row['status_id'] == 'CRG') 
			{
				
				// ������������ ������.
				$message = "\n" . "�������� ���������� �� {$row['period']} ��� (��������� {$row['emp_fullname']})"
				         . " ���� ���������� �� ������������ {$row['status_date']}."
				         . "\n" . '�� ������ ������ �� �������� ������������ ��'
				         . ($row['rate_emp_status'] ? '' : ' ����������' . ($row['rate_hmg_status'] ? '' : ' �'))
				         . ($row['rate_hmg_status'] ? '' : ' ������������ ������������') 
				         . ($row['rate_fnc_status'] ? '' : ' , a ����� �� ��������������� ������������') . '.'
				         . "\n" . '����������, ����������� ���� ��� ���������� �������� ������������ ��������.'
				         . $ps;
				$mails = explode(',', $row['mngs_emails']);
				$this->_sendMail($mails, $message);
				if (!$row['rate_emp_status'] && $row['emp_email']) {
					$message = "\n" . '���� �������� ���������� �� ' . $row['period'] . ' ���'
					         . ' ���� ���������� �� ������������ ����� ������������� ' . $row['status_date'] . '.'
					         . "\n" . '����������, ����������� ���� �������� � ������������� ��������.'
					         . $ps;
					$this->_sendMail($row['emp_email'], $message);
				}
				if (!$row['rate_hmg_status']) {
					$message = "\n" . '�������� ���������� �� ' . $row['period'] . ' ���'
					         . ' (��������� ' . $row['emp_fullname'] . ')'
					         . ' ���� ���������� ������������� �� ������������ ' . $row['status_date'] . '.'
						     . "\n" . '����������, ����������� ���� �������� � ������������� ��������.'
						     . $ps;
					$mails = explode(',', $row['hmgs_emails']);
					$this->_sendMail($mails, $message);
				}
				if (!$row['rate_fnc_status']) {
					$message = "\n" . '�������� ���������� �� ' . $row['period'] . ' ���'
					         . ' (��������� ' . $row['emp_fullname'] . ')'
					         . ' ���� ���������� ������������� �� ������������ ' . $row['status_date'] . '.'
						     . "\n" . '����������, ����������� ���� �������� � ������������� ��������.'
						     . $ps;
					$mails = explode(',', $row['func_emails']);
					$this->_sendMail($mails, $message);
				}
			}
			
		}
		
		$log = "\n\n" . '���������� ��������: ' . count($rows)
		     . "\n"   . '���������� ���������: ' . $this->_countSending
		     . "\n"   . '������: ' . $this->_countErrors;
		
		fwrite($this->_logHandle, $log);
		fclose($this->_logHandle);
		
		$view = $this->initView();
		$view->title = Rp::getTitle('�������� ����������');
	}
	
	private function _sendMail($mails, $message)
	{
		$mails   = (array) $mails;
		$subject = '�����-�������� :: �����������';
		$headers = 'From: rolf-personnel@rolf.ru' . "\n"
		         . 'Content-Type: text/plain; charset="windows-1251"' . "\n"
		         . 'Content-Transfer-Encoding: quoted-printable' . "\n"
		         . 'Content-Disposition: inline' . "\n";
		         
		foreach ($mails as $mail) {
			if (trim($mail)) {
				if (@mail($mail, $subject, $message, $headers)) {
					$this->_countSending++;
					$log = "\n\n" . $mail . $message;
				}
				else {
					$this->_countErrors++;
					$log = "\n\n" . $mail . "\n" . '<��������� �� ����������>';
				}
				fwrite($this->_logHandle, $log);
			}
		}
	}
}