<?php
/**
 * ROLF Personnel library
 *
 * @category Rp
 * @package  Rp_User
 */

/**
 * Объект пользователя системы.
 *
 * @category Rp
 * @package  Rp_User
 */
class Rp_User
{
	/**
	 * Инстанцированный объект.
	 *
	 * @var Rp_User
	 */
	private static $_instance = null;

	/**
	 * Объект сотрудника.
	 *
	 * @var Rp_Db_View_Row_Employee
	 */
	private static $_employee = null;

	/**
	 * Идентификатор физ. лица.
	 *
	 * @var int
	 */
	private $_personId = null;

	/**
	 * Идентификатор метки входа в систему.
	 *
	 * @var int
	 */
	private $_logonId = null;

	/**
	 * Конструктор.
	 *
	 * Для установки объекта используется метод {@link setInstance()}.
	 * Для получения объекта используется метод {@link getInstance()}.
	 * Объект пользователя - это объект-singleton.
	 *
	 * @param int $personId Идентификатор физ. лица.
	 *
	 * @return void
	 * @throws Exception
	 */
	public function __construct($personId)
	{
		if (isset(self::$_instance)) {
			throw new Exception('Объект пользователя уже установлен и не может быть создан повторно.');
		}
		if (!is_numeric($personId)) {
			throw new Exception('Идентификатор физ. лица должен быть целым числом.');
		}
		$this->_personId = $personId;
	}

	/**
	 * Магический метод.
	 *
	 * Объект пользователя предоставляет интерфейс для доступа
	 * к свойствам (полям строки) соответствующего ему объекта сотрудника.
	 *
	 * @param  string $field Название поля.
	 * @return string
	 */
	public function __get($field)
	{
		return $this->getEmployee()->$field;
	}

	/**
	 * Магический метод.
	 *
	 * Объект пользователя предоставляет интерфейс для доступа
	 * к методам соответствующего ему объекта сотрудника.
	 * Поддерживается только вызов методов без параметров.
	 *
	 * @param string $method Название метода.
	 * @param array  $args   Массив параметров.
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public function __call($method, array $args)
	{
		if (!empty($args)) {
			throw new Exception('Вызов методов объекта сотрудника с параметрами не поддерживается.');
		}
		return $this->getEmployee()->$method();
	}

	/**
	 * Магический метод.
	 *
	 * Предотвращает клонирование объекта.
	 *
	 * @return void
	 */
	public function __clone()
	{
		throw new Exception('Объект пользователя не может быть клонирован.');
	}

	/**
	 * Возвращает идентификатор физ. лица.
	 *
	 * @return int
	 */
	public function getPersonId()
	{
		return $this->_personId;
	}

	/**
	 * Инстанцирует объект пользователя.
	 *
	 * @param int $personId Идентификатор физ. лица.
	 *
	 * @return void
	 */
	public static function setInstance($personId)
	{
		self::$_instance = new self($personId);

		$session = new Zend_Session_Namespace(__CLASS__);
		$session->instance = self::$_instance;
	}

	/**
	 * Возвращает инстанцированный объект пользователя.
	 * Если объект ранее не был инстанцирован и не установлен в сессии,
	 * то метод сгенерирует исключение.
	 *
	 * @return Rp_User
	 * @throws Exception
	 */
	public static function getInstance()
	{
		if (empty(self::$_instance)) {
			$session = new Zend_Session_Namespace(__CLASS__);
			if (! $session->instance instanceof self) {
				throw new Exception('Объект пользователя не установлен.');
			}
			self::$_instance = $session->instance;
		}
		return self::$_instance;
	}

	/**
	 * Проверяет был ли инстанцирован объект пользователя.
	 *
	 * @return boolean
	 */
	public static function hasInstance()
	{
		try {
			return (bool) self::getInstance();
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Возвращает объект сотрудника.
	 *
	 * @return Rp_Db_View_Row_Employee
	 * @throws Exception
	 */
	public function getEmployee()		// находим пользователя в таблице сотрудников
	{
		if (empty(self::$_employee)) {
			$employees = new Rp_Db_View_Employees();

			$employee = $employees->findByPersonId($this->_personId)->current();
			if (empty($employee)) {
				throw new Exception('Объект сотрудника не установлен.');
			}
			self::$_employee = $employee;
		}
		return self::$_employee;
	}
}