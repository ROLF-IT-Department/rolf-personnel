<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */

/**
 * Объект представления должностей из иерархической структуры 
 * с атрибутом "Наблюдатель".
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */
class Rp_Db_View_TreePosts_Viewers extends Rp_Db_View_Abstract
{	
	protected $_name = 'user_rp_tree_posts_viewers';
	
	/**
	 * Возвращает массив идентификаторов просматриваемых должностей.
	 *
	 * @param  int|array $postId Идентификатор или массив идентификаторов 
	 * просматривающих должностей.
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