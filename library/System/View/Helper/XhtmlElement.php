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
 * ����������� xhtml-���������.
 * 
 * @category   System
 * @package    System_View
 * @subpackage System_View_Helper
 */
class System_View_Helper_XhtmlElement extends System_View_Helper_Xhtml
{	
	/**
	 * ������� xhtml-�������.
	 *
	 * @param string $name �������� ��������.
	 * 
	 * @param string $content ���������� ��������.
	 * ���� �������� ������, �� ������� ����������� ��������� '/>', 
	 * ���� ������� - ������ ����������� �����.
	 * 
	 * @param string|array $attribs �������� ��������.
	 * ������ ��� ������ ��� �������-��������.
	 * 
	 * @param boolean $specialchars ���� �������������� ����. �������� � html-��������.
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