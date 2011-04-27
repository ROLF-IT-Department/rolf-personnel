<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Auth
 * @subpackage Rp_Auth_Adapter
 */

/**
 * Адаптер авторизации в системе.
 *
 * @category   Rp
 * @package    Rp_Auth
 * @subpackage Rp_Auth_Adapter
 */
class Rp_Auth_Adapter_DbTable extends Zend_Auth_Adapter_DbTable
{
	/**
	 * Авторизация через форму ввода логина/пароля.
	 */
	const AUTH_FORM = 1;

	/**
	 * Прозрачная авторизация (windows-авторизация).
	 */
	const AUTH_TRANSPARENT = 2;

	/**
	 * Конструктор.
	 *
	 * @param string $username Имя пользователя.
	 * @param string $password Пароль пользователя.
	 * @param int    $authtype Тип авторизации.
	 *
	 * @return void
	 * @throws Exception
	 */
	public function __construct($username, $password, $authtype)
	{
		$identityColumn = '';
		$credentialColumn = 'password';

		if ( ! $username)
		{
			throw new Exception('Не указан логин пользователя.');
		}
		if ($authtype === self::AUTH_TRANSPARENT)
		{
			$identityColumn = 'netname';
		}
		elseif($authtype === self::AUTH_FORM)
		{
			if ( ! $password)
			{
				throw new Exception('Не указан пароль пользователя.');
			}

			$identityColumn = 'login';
		}
		else
		{
			throw new Exception('Не верно указан тип авторизации.');
		}

		$this->setIdentity($username);
		$this->setCredential($password);

		parent::__construct(Rp::getDbAdapter(), 'user_rp_persons_PM', $identityColumn, $credentialColumn);
	}

	/**
	 * Переопределенный метод Zend_Auth_Adapter_DbTable::authenticate().
	 * После исправления ошибок в стандартном методе нужно удалить этот метод.
	 *
	 * @return Zend_Auth_Result
	 * @throws Zend_Auth_Adapter_Exception if answering the authentication query is impossible.
	 */
	public function authenticate()
    {
        $exception = NULL;

        if ($this->_tableName == '')
        {
            $exception = 'A table must be supplied for the Zend_Auth_Adapter_DbTable authentication adapter.';
        }
        elseif ($this->_identityColumn == '')
        {
            $exception = 'An identity column must be supplied for the Zend_Auth_Adapter_DbTable authentication adapter.';
        }
        elseif ($this->_credentialColumn == '')
        {
            $exception = 'A credential column must be supplied for the Zend_Auth_Adapter_DbTable authentication adapter.';
        }
        elseif ($this->_identity == '')
        {
            $exception = 'A value for the identity was not provided prior to authentication with Zend_Auth_Adapter_DbTable.';
        }
        elseif ($this->_credential === NULL)
        {
            $exception = 'A credential value was not provided prior to authentication with Zend_Auth_Adapter_DbTable.';
        }

        if ($exception)
        {
            /**
             * @see Zend_Auth_Adapter_Exception
             */
            require_once 'Zend/Auth/Adapter/Exception.php';
            throw new Zend_Auth_Adapter_Exception($exception);
        }

        // create result array
        $authResult = array(
            'code'     => Zend_Auth_Result::FAILURE,
            'identity' => $this->_identity,
            'messages' => array()
            );

        // build credential expression
        if (empty($this->_credentialTreatment) || (strpos($this->_credentialTreatment, "?") === false)) {
            $this->_credentialTreatment = '?';
        }

        $credentialExpression = $this->_credentialColumn . ' AS zend_auth_credential_match';

        // get select
        $dbSelect = $this->_zendDb->select();
        $dbSelect->from($this->_tableName, array('*', $credentialExpression))
                 ->where($this->_zendDb->quoteIdentifier($this->_identityColumn) . ' = ?', $this->_identity);

	    if($this->_credential)
	    {
		    $dbSelect->where($this->_zendDb->quoteIdentifier($this->_credentialColumn) . ' = ?', $this->_credential);
	    }

        $select = str_replace('"', '', $dbSelect->__toString());

		// query for the identity
        try
        {
        	$resultIdentities = $this->_zendDb->fetchAll($select);
        }
        catch (Exception $e)
        {
            /**
             * @see Zend_Auth_Adapter_Exception
             */
            require_once 'Zend/Auth/Adapter/Exception.php';
            throw new Zend_Auth_Adapter_Exception('The supplied parameters to Zend_Auth_Adapter_DbTable failed to '
                                                . 'produce a valid sql statement, please check table and column names '
                                                . 'for validity.');
        }

        if (count($resultIdentities) < 1)
        {
            $authResult['code'] = Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND;
            $authResult['messages'][] = 'A record with the supplied identity could not be found.';
            return new Zend_Auth_Result($authResult['code'], $authResult['identity'], $authResult['messages']);
        }
        elseif (count($resultIdentities) > 1)
        {
            $authResult['code'] = Zend_Auth_Result::FAILURE_IDENTITY_AMBIGUOUS;
            $authResult['messages'][] = 'More than one record matches the supplied identity.';
            return new Zend_Auth_Result($authResult['code'], $authResult['identity'], $authResult['messages']);
        }

        $resultIdentity = $resultIdentities[0];
        /*
        if (trim($resultIdentity['zend_auth_credential_match']) !== $this->_credential) {
            $authResult['code'] = Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID;
            $authResult['messages'][] = 'Supplied credential is invalid.';
            return new Zend_Auth_Result($authResult['code'], $authResult['identity'], $authResult['messages']);
        }
		*/
        unset($resultIdentity['zend_auth_credential_match']);
        $this->_resultRow = $resultIdentity;

        $authResult['code'] = Zend_Auth_Result::SUCCESS;
        $authResult['messages'][] = 'Authentication successful.';
        return new Zend_Auth_Result($authResult['code'], $authResult['identity'], $authResult['messages']);
    }
}