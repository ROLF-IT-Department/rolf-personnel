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
				INNER JOIN user_rp_persons persons
					ON cards.person_id = persons.id
				INNER JOIN user_rp_tree_posts_employees posts_employees
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
				INNER JOIN user_rp_persons persons
					ON cards.person_id = persons.id
				INNER JOIN user_rp_tree_posts_employees posts_employees
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
		    . "\n" . 'Это письмо создано автоматически и не требует ответа.'
		    . "\n" . 'По всем возникающим вопросам обращайтесь к HR-партнерам'
		    . ' и специалистам по персоналу Вашего подразделения.';
		
		foreach ($rows as $row) 
		{		
			
			if ($row['status_id'] == 'CPN') 
			{
				
				// Согласование планирования.
				$message = "\n" . "Карточка достижений за {$row['period']} год (сотрудник {$row['emp_fullname']})"
				         . " была выставлена на согласование {$row['status_date']}."
				         . "\n" . 'На данный момент не получено согласование от'
				         . ($row['plan_emp_status'] ? '' : ' сотрудника' . ($row['plan_hmg_status'] ? '' : ' и'))
				         . ($row['plan_hmg_status'] ? '' : ' вышестоящего руководителя') 
				         . ($row['plan_fnc_status'] ? '' : ' , a также от функционального руководителя') . '.'
				         . "\n" . 'Пожалуйста, предпримите меры для завершения процесса согласования карточки.'
				         . $ps;
				$mails = explode(',', $row['mngs_emails']);
				$this->_sendMail($mails, $message);
				if (!$row['plan_emp_status'] && $row['emp_email']) {
					$message = "\n" . 'Ваша карточка достижений за ' . $row['period'] . ' год'
					         . ' была выставлена на согласование Вашим руководителем ' . $row['status_date'] . '.'
					         . "\n" . 'Пожалуйста, подтвердите Ваше согласие с поставленными целями и задачами.'
					         . $ps;
					$this->_sendMail($row['emp_email'], $message);
				}
				if (!$row['plan_hmg_status']) {
					$message = "\n" . 'Карточка достижений за ' . $row['period'] . ' год'
					         . ' (сотрудник ' . $row['emp_fullname'] . ')'
					         . ' была выставлена руководителем на согласование ' . $row['status_date'] . '.'
						     . "\n" . 'Пожалуйста, подтвердите Ваше согласие с поставленными целями и задачами.'
						     . $ps;
					$mails = explode(',', $row['hmgs_emails']);
					$this->_sendMail($mails, $message);
				}
				if (!$row['plan_fnc_status']) {
					$message = "\n" . 'Карточка достижений за ' . $row['period'] . ' год'
					         . ' (сотрудник ' . $row['emp_fullname'] . ')'
					         . ' была выставлена руководителем на согласование ' . $row['status_date'] . '.'
						     . "\n" . 'Пожалуйста, подтвердите Ваше согласие с поставленными целями и задачами.'
						     . $ps;
					$mails = explode(',', $row['func_emails']);
					$this->_sendMail($mails, $message);
				}
			} else if ($row['status_id'] == 'CRG') 
			{
				
				// Согласование оценки.
				$message = "\n" . "Карточка достижений за {$row['period']} год (сотрудник {$row['emp_fullname']})"
				         . " была выставлена на согласование {$row['status_date']}."
				         . "\n" . 'На данный момент не получено согласование от'
				         . ($row['rate_emp_status'] ? '' : ' сотрудника' . ($row['rate_hmg_status'] ? '' : ' и'))
				         . ($row['rate_hmg_status'] ? '' : ' вышестоящего руководителя') 
				         . ($row['rate_fnc_status'] ? '' : ' , a также от функционального руководителя') . '.'
				         . "\n" . 'Пожалуйста, предпримите меры для завершения процесса согласования карточки.'
				         . $ps;
				$mails = explode(',', $row['mngs_emails']);
				$this->_sendMail($mails, $message);
				if (!$row['rate_emp_status'] && $row['emp_email']) {
					$message = "\n" . 'Ваша карточка достижений за ' . $row['period'] . ' год'
					         . ' была выставлена на согласование Вашим руководителем ' . $row['status_date'] . '.'
					         . "\n" . 'Пожалуйста, подтвердите Ваше согласие с поставленными оценками.'
					         . $ps;
					$this->_sendMail($row['emp_email'], $message);
				}
				if (!$row['rate_hmg_status']) {
					$message = "\n" . 'Карточка достижений за ' . $row['period'] . ' год'
					         . ' (сотрудник ' . $row['emp_fullname'] . ')'
					         . ' была выставлена руководителем на согласование ' . $row['status_date'] . '.'
						     . "\n" . 'Пожалуйста, подтвердите Ваше согласие с поставленными оценками.'
						     . $ps;
					$mails = explode(',', $row['hmgs_emails']);
					$this->_sendMail($mails, $message);
				}
				if (!$row['rate_fnc_status']) {
					$message = "\n" . 'Карточка достижений за ' . $row['period'] . ' год'
					         . ' (сотрудник ' . $row['emp_fullname'] . ')'
					         . ' была выставлена руководителем на согласование ' . $row['status_date'] . '.'
						     . "\n" . 'Пожалуйста, подтвердите Ваше согласие с поставленными оценками.'
						     . $ps;
					$mails = explode(',', $row['func_emails']);
					$this->_sendMail($mails, $message);
				}
			}
			
		}
		
		$log = "\n\n" . 'Количество карточек: ' . count($rows)
		     . "\n"   . 'Отправлено сообщений: ' . $this->_countSending
		     . "\n"   . 'Ошибок: ' . $this->_countErrors;
		
		fwrite($this->_logHandle, $log);
		fclose($this->_logHandle);
		
		$view = $this->initView();
		$view->title = Rp::getTitle('Рассылка оповещений');
	}
	
	private function _sendMail($mails, $message)
	{
		$mails   = (array) $mails;
		$subject = 'РОЛЬФ-Персонал :: Напоминание';
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
					$log = "\n\n" . $mail . "\n" . '<Сообщение не доставлено>';
				}
				fwrite($this->_logHandle, $log);
			}
		}
	}
}