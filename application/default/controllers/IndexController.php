<?php

class IndexController extends Zend_Controller_Action
{
	public function indexAction()
	{
		$have_integrate = 0;
		$is_integrate = "";

		$user = Rp_User::getInstance();;
		$user_id = $user->getPersonId();
		$integrated = new Rp_Db_View_IntegratedPersons();
		$refer_id = $integrated->fetchRefID($user_id);
//		if (($user_id >= 90000000) OR (count($refer_id) > 0))
//		if ($user_id >= 90000000)
		switch($user->persg)
		{
			case 2:
			case 3:
			case 7:
			case 8:
			case 'S':
				$have_integrate = 1;
				$is_integrate = "Совместитель";
				break;
			default:
				if(count($refer_id) > 0)
				{
					$have_integrate = 1;
				}
				break;
		}

		$view = $this->initView();
		$view->title = Rp::getTitle();
		$view->have_integrate = $have_integrate;

		$view->is_integrate = $is_integrate;
	}

	public function menuAction()
	{
		$request = $this->getRequest();
		$id = $request->getParam('id', null);

		if ($id == 'people' OR $id == 'subpeople' OR $id == 'funcpeople')
		{
			$user = Rp_User::getInstance();			// получаем объект пользователя
			$treePost = $user->getEmployee()->getTreePost();	// получаем должность сотрудника в дереве должностей

			// если мы нажали на просмотр непосредственных подчиненных, то получаем id непоср. подчиненных,  в противном случае ищем id подчиненных подчиненных
			$postIds = ($id == 'people' OR $id == 'funcpeople')
				? $treePost->id
				: $treePost->findChildPosts()->getCol('id');

			$func = ($id == 'funcpeople') ? TRUE : FALSE;

			$params = array(
				'postIds' => $postIds,
				'showEmps' => FALSE,
				'showSubEmps' => TRUE,		// выводить список подчиненных
				'func' => $func				// выводить список функциональных подчиненных
			);
			$this->_forward('index', 'employees', null, $params);
			return;
		}
		$this->_forward('index', 'empty');
	}
}