<?php
/**
 * ROLF Personnel library
 * 
 * @category   Rp
 * @package    Rp_View
 * @subpackage Rp_View_Helper
 */

/**
 * Помощник для определения базового адреса URL.
 * 
 * @category   Rp
 * @package    Rp_View
 * @subpackage Rp_View_Helper
 */
class Rp_View_Helper_BaseUrl
{
	/**
	 * Базовый адрес URL.
	 * 
	 * @var string
	 */
	private $_baseUrl = null;
	
	/**
	 * Возвращает базовый адрес URL.
	 * Например, "/subdomain" для адреса "http://domain.ru/subdomain".
	 *
	 * @return string
	 */
	public function baseUrl()
	{
		if ($this->_baseUrl === null) {
			$front = Zend_Controller_Front::getInstance();
			$this->_baseUrl = $front->getBaseUrl();
		}
		return $this->_baseUrl;
	}
}