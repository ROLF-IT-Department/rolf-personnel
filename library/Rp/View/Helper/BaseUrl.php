<?php
/**
 * ROLF Personnel library
 * 
 * @category   Rp
 * @package    Rp_View
 * @subpackage Rp_View_Helper
 */

/**
 * �������� ��� ����������� �������� ������ URL.
 * 
 * @category   Rp
 * @package    Rp_View
 * @subpackage Rp_View_Helper
 */
class Rp_View_Helper_BaseUrl
{
	/**
	 * ������� ����� URL.
	 * 
	 * @var string
	 */
	private $_baseUrl = null;
	
	/**
	 * ���������� ������� ����� URL.
	 * ��������, "/subdomain" ��� ������ "http://domain.ru/subdomain".
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