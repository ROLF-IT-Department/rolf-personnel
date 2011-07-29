<?php

class JsonController extends Zend_Controller_Action
{
	public function userAction()		// переводим полученный список должностей в массив javascript, чтобы отобразить их в дереве меню
	{
		$user = Rp_User::getInstance();
		$userData = array(
			'personId'  => $user->getPersonId(),
			'fullname'  => $user->getPerson()->fullname,
			'subposts'  => array(),
			'viewposts' => array()
		);
		if ($user->isCurrent()) {
			$treePost = $user->getTreePost();
			$userData['subposts'] = $treePost->findChildPosts(true)->toArray();
			$userData['viewposts'] = $treePost->findViewedPosts(true)->toArray();
		}

		$view = $this->initView();
		$view->user = Zend_Json_Encoder::encode($userData);
	}

	public function subPostsAction()
	{
		$request = $this->getRequest();

		$uri_arr = explode('=', $request->getRequestUri());
		$_GET['JSQUERYID'] = array_pop($uri_arr);
		Zend_Loader::loadFile('JsDataLoader.php', 'Rp');

		$id = $request->getParam('id');
		$treePosts = new Rp_Db_View_TreePosts();
		$result['pid'] = $id;
		$result['posts'] = $treePosts->findChildPosts($id, true)->toArray();

		$GLOBALS['_RESULT'] = $result;
		exit();
	}
}