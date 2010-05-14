<?php
/**
 * System
 *
 * @category   System
 * @package    System_View
 * @subpackage System_View_Helper
 */

/** @see System_View_Helper_Xhtml */
require_once 'System/View/Helper/Xhtml.php';

/**
 * Конструктор xhtml-элементов.
 * 
 * @category   System
 * @package    System_View
 * @subpackage System_View_Helper
 */
class System_View_Helper_XhtmlElement extends System_View_Helper_Xhtml
{	
	/**
	 * Создает xhtml-элемент.
	 *
	 * @param string $name Название элемента.
	 * 
	 * @param string $content Содержимое элемента.
	 * Если параметр опущен, то элемент закрывается символами '/>', 
	 * если передан - парным закрывающим тегом.
	 * 
	 * @param string|array $attribs Атрибуты элемента.
	 * Строка или массив пар атрибут-значение.
	 * 
	 * @param boolean $specialchars Флаг преобразования спец. символов в html-сущности.
	 * 
	 * @return string
	 */
	public function xhtmlElement($name, $content = null, $attribs = null, $specialchars = true)
	{
		$attribs = $this->_xhtmlAttribs($attribs);
		
		$element = '<' . $name . $attribs;
		if ($content === null) {
			$element .= ' />';
		} else {
			$element .= '>'
			          . ($specialchars ? $this->_specialchars($content, ENT_QUOTES) : $content)
			          . '</' . $name . '>';
		}
		return $element;
	}
}