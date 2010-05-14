<?php
/**
 * System
 * 
 * @category   System
 * @package    System_View
 * @subpackage System_View_Helper
 */

/**
 * ������� ����� ������������� xhtml-���������.
 * 
 * @category   System
 * @package    System_View
 * @subpackage System_View_Helper
 */
abstract class System_View_Helper_Xhtml
{
	/**
	 * ���������.
	 * 
	 * @var string
	 */
	protected $_charset = 'UTF-8';
	
	/**
	 * ������������� ���������.
	 *
	 * @param string $charset
	 * @return void
	 */
	public function setCharset($charset)
	{
		$this->_charset = $charset;
	}
	
	/**
	 * ����������� ����������� ������� � html-��������.
	 *
	 * @param string $string ������ ��� ��������������.
	 * @param int $quoteStyle ����� ��������� ��������� � ������� �������.
	 * @param string $charset ���������, ������������ ��� ��������������.
	 * 
	 * @return string
	 */
	protected function _specialchars($string, $quoteStyle = ENT_COMPAT, $charset = null)
	{
		if ($charset === null) {
			$charset = $this->_charset;
		}
		return htmlspecialchars($string, $quoteStyle, $charset);
	}
	
	/**
	 * ��������� ������ ���������.
	 * ��� �������� ���������� ������ ��������� ���������� � �������,
	 * � ��������� ������ ������������ ������ ������.
	 *
	 * @param array $attribs ������ ��� �������-��������.
	 * ���� ���������� �������� �� �������� ��������,
	 * �� ��� ���������� � ������ � ������������.
	 * 
	 * @return string
	 */
	protected function _xhtmlAttribs($attribs)
	{	
		$strAttribs = '';
		if (is_array($attribs)) {
			foreach ($attribs as $attr => $value) {
				$attr = $this->_specialchars($attr, ENT_COMPAT);
				if (is_array($value)) {
					$value = implode(' ', $value);
				}
				$value = $this->_specialchars($value, ENT_COMPAT);
				$strAttribs .= ' ' . $attr . '="' . $value . '"';
			}
		} elseif (strlen((string) $attribs)) {
			$strAttribs = ' ' . $attribs;
		}
		return $strAttribs;
	}
}