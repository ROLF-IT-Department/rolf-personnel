<?php
/**
 * ROLF Personnel library
 *
 * @category Rp
 * @package  Rp_Mail
 */

/**
 * Класс для отправки электронной почты.
 *
 * @category Rp
 * @package  Rp_Mail
 */
class Rp_Mail extends Zend_Mail 
{
	/**
	 * Конструктор.
	 *
	 * @param string $charset
	 */
	public function __construct($charset = 'windows-1251')
	{
		parent::__construct($charset);
	}
	
	/**
     * Sets From-header and sender of the message
     *
     * @param  string    $email
     * @param  string    $name
     * @return Zend_Mail Provides fluent interface
     * @throws Zend_Mail_Exception if called subsequent times
     */
    public function setFrom($email, $name = '')
    {
        if ($this->_from === null) {
            $email = strtr($email,"\r\n\t",'???');
            $this->_from = $email;
            $this->_storeHeader('From', $email, true);
        } else {
            throw new Zend_Mail_Exception('From Header set twice');
        }
        return $this;
    }
}