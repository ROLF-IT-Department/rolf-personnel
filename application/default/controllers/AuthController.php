<?php

class AuthController extends Zend_Controller_Action
{
	public function indexAction()
	{
		$request  = $this->getRequest();
		$authtype = Rp_Auth_Adapter_DbTable::AUTH_TRANSPARENT;
		$username = basename($request->getServer('AUTH_USER', NULL));

		$password = '';

		if ($_POST OR isset($_GET['force_auth']))
		{
			$authtype = Rp_Auth_Adapter_DbTable::AUTH_FORM;
			$username = $request->getPost('username', NULL);
			$password = $request->getPost('password', NULL);
		}

		$message = '';

		if($username)
		{
			try
			{
				$auth = Zend_Auth::getInstance();
				$adapter = new Rp_Auth_Adapter_DbTable($username, $password, $authtype);
				$result = $auth->authenticate($adapter);

				if ($result->isValid())
				{
					$row = $adapter->getResultRowObject('id');

					Rp_User::setInstance($row->id);

					$user = Rp_User::getInstance();
					$appointments = new Rp_Db_View_Appointments();

					$log = new Rp_Db_Table_Logon;

					if ( ! isset($_SESSION['user_id']))
					{
						$logon = array();
						$logon['session_id'] = session_id();
						$logon['user_id'] = $user->getPerson()->id;
						$logon['userfullname'] = $user->getPerson()->fullname;
						$logon['userpost'] = current($appointments->fetchNames($user->getEmployee()->appointment_id));
						$logon['date_logon'] = date("m.d.y H:i:s");
						$logon['ip'] = $_SERVER['REMOTE_ADDR'];
						$logon['version'] = $_SERVER['HTTP_USER_AGENT'];

						$log->insert($logon);

						$_SESSION['user_id'] = $user->getPerson()->id;
					}


					$this->_forward('index', 'index');
					return;


				}
				elseif ($result->getCode() != Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND)
				{
					$message = implode("\n", $result->getMessages());
				}
				elseif ($authtype == Rp_Auth_Adapter_DbTable::AUTH_FORM)
				{
					$message = 'ѕользователь с указанным сочетанием логин/пароль не найден.';
				}
			}
			catch (Exception $e)
			{
				$message = $e->getMessage();
			}
		}

		$view = $this->initView();
		$view->title = Rp::getTitle('јвторизаци€');
		$view->message = $message;
	}

}