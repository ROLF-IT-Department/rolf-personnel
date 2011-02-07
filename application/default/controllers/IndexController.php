<?php

class IndexController extends Zend_Controller_Action
{
	public function indexAction()
	{
		$have_integrate = 0;
		$user = Rp_User::getInstance();;
		$user_id = $user->getPersonId();
		$integrated = new Rp_Db_View_IntegratedPersons();
		$refer_id = $integrated->fetchRefID($user_id);
//		if (($user_id >= 90000000) OR (count($refer_id) > 0))
		if (($user->persg != 1) OR (count($refer_id) > 0))
				$have_integrate = 1;

		$view = $this->initView();
		$view->title = Rp::getTitle();
		$view->have_integrate = $have_integrate;
		$is_integrate = "";
//		if ($user_id >= 90000000)
		if ($user->persg != 1)
			$is_integrate = $user->pgtxt;//"������������";

		$view->is_integrate = $is_integrate;
	}

	public function menuAction()
	{
		$request = $this->getRequest();
		$id = $request->getParam('id', null);

		if ($id == 'people' || $id == 'subpeople' || $id == 'funcpeople')
		{
			$user = Rp_User::getInstance();			// �������� ������ ������������
			$treePost = $user->getEmployee()->getTreePost();	// �������� ��������� ���������� � ������ ����������

			// ���� �� ������ �� �������� ���������������� �����������, �� �������� id ������. �����������,  � ��������� ������ ���� id ����������� �����������
			$postIds = ($id == 'people' || $id == 'funcpeople') ? $treePost->id : $treePost->findChildPosts()->getCol('id');

			$func = false;
			if ($id == 'funcpeople') $func = true;

			$params = array(
				'postIds' => $postIds,
				'showEmps' => false,
				'showSubEmps' => true,		// �������� ������ �����������
				'func' => $func				// �������� ������ �������������� �����������
			);
			$this->_forward('index', 'employees', null, $params);
			return;
		}
		$this->_forward('index', 'empty');
	}
}