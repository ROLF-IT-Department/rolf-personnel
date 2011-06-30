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
 * с атрибутом "Функциональный руководитель".
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */
class Rp_Db_View_TreePosts_Func extends Rp_Db_View_Abstract
{
	protected $_name = 'user_rp_tree_posts_func';

	/**
	 * Возвращает массив идентификаторов функциональных подчиненных должностей.
	 *
	 * @param  int|array $postId Идентификатор или массив идентификаторов
	 * функциональных подчиненных должностей.
	 *
	 * @return array
	 */
	public function fetchFuncEmployeeIds($postId)
	{
		$postId = $this->_quote($postId);
		$where = "post_id IN ($postId)";
		return $this->_fetchCol('post_func_id', $where);
	}
}