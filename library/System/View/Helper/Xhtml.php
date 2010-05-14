<?php
/**
 * System
 * 
 * @category   System
 * @package    System_View
 * @subpackage System_View_Helper
 */

/**
 * Базовый класс конструкторов xhtml-элементов.
 * 
 * @category   System
 * @package    System_View
 * @subpackage System_View_Helper
 */
abstract class System_View_Helper_Xhtml
{
	/**
	 * Кодировка.
	 * 
	 * @var string
	 */
	protected $_charset = 'UTF-8';
	
	/**
	 * Устанавливает кодировку.
	 *
	 * @param string $charset
	 * @return void
	 */
	public function setCharset($charset)
	{
		$this->_charset = $charset;
	}
	
	/**
	 * Преобразует специальные символы в html-сущности.
	 *
	 * @param string $string Строка для преобразования.
	 * @param int $quoteStyle Режим обработки одиночных и двойных кавычек.
	 * @param string $charset Кодировка, используемая при преобразовании.
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
	 * Формирует строку атрибутов.
	 * При успешном выполнении строка атрибутов начинается с пробела,
	 * в противном случае возвращается пустая строка.
	 *
	 * @param array $attribs Массив пар атрибут-значение.
	 * Если переданное значение не является массивом,
	 * то оно приводится к строке и возвращается.
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