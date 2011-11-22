<?php
/**
 * ROLF Personnel library
 *
 * @category Rp
 * @package  Rp
 * @version  $Id: Rp.php 0000 2008-03-14 00:00:00Z $
 */

/**
 * Информационная система ROLF Personnel.
 *
 * @category Rp
 * @package  Rp
 */
class Rp
{
	/**
	 * Название системы.
	 */
	const NAME = 'РОЛЬФ-Персонал';

	/**
	 * Системный конфигурационный файл (путь относительно файла index.php).
	 */
	const CONFIG = '../config.ini';

	/**
	 * Объект адаптера базы данных системы.
	 *
	 * @var Zend_Db_Adapter_Abstract
	 */
	private static $_db = null;

	/**
	 * Объект конфигурационного файла системы {@link CONFIG}.
	 *
	 * @var Zend_Config_Ini
	 */
	private static $_config = null;

	/**
	 * Возвращает форматированную строку заголовка.
	 *
	 * @param string|array $prefix Строка, которая будет добавлена
	 * в начало заголовка. Если $prefix - массив, то его элементы будут
	 * объединены в строку.
	 *
	 * @return string
	 */
	public static function getTitle($prefix = null)
	{
		$title = array_merge((array) $prefix, array(self::NAME));

		return implode(' - ', $title);
	}

	/**
	 * Возвращает объект конфигурационного файла системы.
	 * Для определения расположения файла конфигурации используется значение
	 * константы {@link CONFIG}.
	 *
	 * @param string $section Название раздела конфигурационного файла.
	 *
	 * @return Zend_Config_Ini
	 * @throws Exception
	 */
	public static function getConfig($section = null)
	{
		if (empty(self::$_config)) {
			self::$_config = new Zend_Config_Ini(self::CONFIG, null);
		}
		if ($section !== null) {
			return self::$_config->$section;
		}
		return self::$_config;
	}

	/**
	 * Возвращает объект адаптера базы данных системы.
	 * Тип адаптера и параметры соединения должны быть указаны
	 * в системном конфигурационном файле {@link CONFIG} в разделе "database".
	 *
	 * @return Zend_Db_Adapter_Abstract
	 * @throws Exception
	 */
	public static function getDbAdapter()
	{
		if (empty(self::$_db)) {
			$adapter = null;
			$params = self::getConfig('database')->toArray();

			if (isset($params['adapter'])) {
				$adapter = $params['adapter'];
				unset($params['adapter']);
			}
			self::$_db = Zend_Db::factory($adapter, $params);
		}
		return self::$_db;
	}
}