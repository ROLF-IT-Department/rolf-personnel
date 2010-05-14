<?php
/**
 * ROLF Personnel library
 * 
 * @category   Rp
 * @package    Rp_Controller
 * @subpackage Rp_Controller_Action
 */

/**
 * Помощник действия для интеграции объекта представления.
 * 
 * @category   Rp
 * @package    Rp_Controller
 * @subpackage Rp_Controller_Action
 */
class Rp_Controller_Action_Helper_ViewRenderer extends Zend_Controller_Action_Helper_ViewRenderer
{
	/**
	 * Инициализирует объект представления.
	 * 
	 * @return void
	 */
	public function init()
	{
		if ($this->view === null) {
			$view = new Zend_View();
			$view->addHelperPath('Rp/View/Helper', 'Rp_View_Helper_');
			$this->setView($view);
		}
		parent::init();
	}
}