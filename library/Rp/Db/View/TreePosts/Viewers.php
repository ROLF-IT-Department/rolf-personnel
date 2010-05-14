<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */

/**
 * ������ ������������� ���������� �� ������������� ��������� 
 * � ��������� "�����������".
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */
class Rp_Db_View_TreePosts_Viewers extends Rp_Db_View_Abstract
{	
	protected $_name = 'user_rp_tree_posts_viewers';
	
	/**
	 * ���������� ������ ��������������� ��������������� ����������.
	 *
	 * @param  int|array $postId ������������� ��� ������ ��������������� 
	 * ��������������� ����������.
	 * 
	 * @return array
	 */
	public function fetchPostViewedIds($postId)
	{
		$postId = $this->_quote($postId);
		$where = "post_id IN ($postId)";
		return $this->_fetchCol('post_viewed_id', $where);
	}
}