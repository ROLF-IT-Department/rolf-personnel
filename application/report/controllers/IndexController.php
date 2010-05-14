<?php

class Report_IndexController extends Zend_Controller_Action
{
	public function indexAction()
	{	
		$ratePeriod = 2007;
		$planPeriod = 2008;
		
		$db = Rp::getDbAdapter();
		$sql = "
			SELECT
				persons.id,
				persons.fullname,
				companies.name AS company,
				appointments.name AS appointment,
				departments.name AS department,
				rate_mng_date = CASE WHEN cards_rate.rate_mng_status IS NULL THEN 'нет' 
					ELSE CONVERT(varchar(255), cards_rate.rate_mng_date, 104) END,
				rate_emp_date = CASE WHEN cards_rate.rate_emp_status IS NULL THEN 'нет' 
					WHEN cards_rate.rate_emp_status = 0 THEN 'несогласен'
					ELSE CONVERT(varchar(255), cards_rate.rate_emp_date, 104) END,
				rate_hmg_date = CASE WHEN cards_rate.rate_hmg_status IS NULL THEN 'нет' 
					WHEN cards_rate.rate_hmg_status = 0 THEN 'несогласен'
					ELSE CONVERT(varchar(255), cards_rate.rate_hmg_date, 104) END,
				ratings.name AS rtg_total,
				plan_mng_date = CASE WHEN cards_plan.plan_mng_status IS NULL THEN 'нет' 
					ELSE CONVERT(varchar(255), cards_plan.plan_mng_date, 104) END,
				plan_emp_date = CASE WHEN cards_plan.plan_emp_status IS NULL THEN 'нет' 
					WHEN cards_plan.plan_emp_status = 0 THEN 'несогласен'
					ELSE CONVERT(varchar(255), cards_plan.plan_emp_date, 104) END,
				plan_hmg_date = CASE WHEN cards_plan.plan_hmg_status IS NULL THEN 'нет' 
					WHEN cards_plan.plan_hmg_status = 0 THEN 'несогласен'
					ELSE CONVERT(varchar(255), cards_plan.plan_hmg_date, 104) END,
				dbo.user_func_rp_department_path(employees.department_id, DEFAULT) AS department_path,
				dbo.user_func_rp_tree_post_persons(posts_employees.post_pid, ', ', '; ') AS managers
			FROM
				user_rp_tree_posts_employees posts_employees
				INNER JOIN user_rp_persons persons
					ON posts_employees.person_id = persons.id
				INNER JOIN user_rp_employees employees
					ON persons.id = employees.person_id
				LEFT JOIN user_rp_companies companies
					ON employees.company_id = companies.id
				LEFT JOIN user_rp_departments departments
					ON employees.department_id = departments.id
				LEFT JOIN user_rp_appointments appointments
					ON employees.appointment_id = appointments.id
				LEFT JOIN user_rp_ach_cards cards_rate
					ON persons.id = cards_rate.person_id AND cards_rate.period = $ratePeriod
				LEFT JOIN user_rp_ach_cards cards_plan
					ON persons.id = cards_plan.person_id AND cards_plan.period = $planPeriod
				LEFT JOIN user_rp_ach_ratings ratings
					ON cards_rate.rtg_total_id = ratings.id
			ORDER BY
				persons.fullname
		";
		
		$view = $this->initView();
		$view->title = Rp::getTitle('Сводный отчет по сотрудникам');
		$view->rows = $db->fetchAll($sql);
		$view->ratePeriod = $ratePeriod;
		$view->planPeriod = $planPeriod;
	}
}