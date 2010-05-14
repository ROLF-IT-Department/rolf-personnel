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
 * с атрибутом "Топ-менеджер".
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */
class Rp_Db_View_TreePosts_Tops extends Rp_Db_View_Abstract
{	
	protected $_name = 'user_rp_tree_posts_tops';
	
	protected $_primary = 'post_id';	
}