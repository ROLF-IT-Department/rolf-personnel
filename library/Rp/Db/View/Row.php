<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */

/**
 * Îáúåêò ñòğîêè ïğåäñòàâëåíèÿ.
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_View
 */
class Rp_Db_View_Row 
{
	/**
	 * Îáúåêò ïğåäñòàâëåíèÿ.
	 *
	 * @var Rp_Db_View_Abstract
	 */
	protected $_view = null;
	
	/**
	 * Ìàññèâ äàííûõ.
	 *
	 * @var array
	 */
	protected $_data = array();
	
	/**
	 * Êîíñòğóêòîğ.
	 *
	 * @param array $config Êîíôèãóğàöèîííûå äàííûå.
	 * Ïîääåğæèâàåò ñëåäóşùèå ïàğàìåòğû:
	 * - view - îáúåêò ïğåäñòàâëåíèÿ;
	 * - data - ìàññèâ äàííûõ.
	 * 
	 * @return void
	 */
	public function __construct(array $config)
	{
		if (isset($config['view'])) {
			$this->_view = $config['view'];
		}
		if (isset($config['data'])) {
			$this->_data = $config['data'];	
		}
	}
	
	/**
	 * Âîçâğàùàåò çíà÷åíèå ïîëÿ $column ñòğîêè.
	 *
	 * @param  string $column Íàçâàíèå ïîëÿ.
	 * @return string
	 */
	public function __get($column)
	{
		if (!array_key_exists($column, $this->_data)) {
			throw new Exception("Ïîëå \"$column\" íå íàéäåíî â ñòğîêå.");
		}
		return $this->_data[$column];
	}
	
	/**
	 * Ãåíåğèğóåò èñêëş÷åíèå ïğè ïîïûòêå óñòàíîâèòü çíà÷åíèå ïîëÿ ñòğîêè.
	 *
	 * @param string $column Íàçâàíèå ïîëÿ.
	 * @param mixed  $value  Çíà÷åíèå ïîëÿ.
	 * 
	 * @return void
	 * @throws Exception
	 */
	public function __set($column, $value)
	{
		throw new Exception("Îáúåêò ñòğîêè ïğåäñòàâëåíèÿ íå ïîääåğæèâàåò óñòàíîâêó çíà÷åíèé ïîëåé.");
	}
	
	/**
	 * Ïğîâåğÿåò íàëè÷èå ïîëÿ $column â ñòğîêå.
	 *
	 * @param  string $column Íàçâàíèå ïîëÿ.
	 * @return boolean
	 */
	public function __isset($column)
	{
		return array_key_exists($column, $this->_data);
	}
	
	/**
	 * Âîçâğàùàåò îáúåêò ïğåäñòàâëåíèÿ.
	 *
	 * @return Rp_Db_View_Abstract
	 */
	public function getView()
	{
		return $this->_view;
	}
	
	/**
	 * Âîçâğàùàåò ñòğîêó â âèäå ìàññèâà.
	 *
	 * @return array
	 */
	public function toArray()
	{
		return $this->_data;
	}
}