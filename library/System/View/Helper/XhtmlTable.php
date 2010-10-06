<?php
/**
 * System
 *
 * @category   System
 * @package    System_View
 * @subpackage System_View_Helper
 */

/** @see System_View_Helper_XhtmlElement */
require_once 'System/View/Helper/XhtmlElement.php';

/**
 * Конструктор xhtml-элемента "table".
 * 
 * @category   System
 * @package    System_View
 * @subpackage System_View_Helper
 */
class System_View_Helper_XhtmlTable extends System_View_Helper_XhtmlElement
{	
	/**
	 * Создает элемент "table".
	 *
	 * @param array &$table Ссылка на массив строк элемента "table".
	 * Каждый элемент массива $table должен являться массивом ячеек строки.
	 * Остальные параметры функции могут быть также переданы через массив $table.
	 * В этом случае:
	 * 1. $table должен содержать элемент $table['rows'] - массив строк таблицы
	 * (только при наличии этого элемента функция попробует найти другие параметры в этом же массиве);
	 * 2. значение параметра функции param должно содержаться в элементе $table['param'];
	 * 3. функция в первую очередь будет пытаться найти значения параметров в массиве $table,
	 * затем (для ненайденных значений) будет использовать значения соответствующих параметров.
	 * Формат передачи данных о конкретной строке и ячейке полностью совпадает с форматом первого параметра
	 * соответственно методов {@link _xhtmlTableRow} и {@link _xhtmlTableCell}.
	 * 
	 * @param string|array $attribs Строка или массив атрибутов элемента "table".
	 * 
	 * @param string $caption Заголовок таблицы.
	 * 
	 * @param string|array $cols Строка колонок или массив атрибутов колонок элемента "table".
	 * 
	 * @param string|array $thead Строка или массив ячеек шапки таблицы.
	 * По-умолчанию спец. символы в ячейках шапки не переводятся в html-сущности.
	 * 
	 * @param string|array $tfoot Строка или массив ячеек footer'а таблицы.
	 * 
	 * @param boolean $specialchars Флаг преобразования в ячейках таблицы (tbody и tfoot) 
	 * спец. символов в html-сущности.
	 * 
	 * @return string
	 * @throws Exception
	 */
	public function xhtmlTable(&$table, $attribs = null, $caption = null, $cols = null, 
		$thead = null, $tfoot = null, $specialchars = true)
	{
		if (!is_array($table)) {
			throw new Exception('First param must be an array');
		}
		
		$rows =& $table;
		if (isset($table['rows'])) {
			if (!is_array($table['rows'])) {
				throw new Exception('Param "rows" must be an array');
			}
			
			if (isset($table['attribs'])) {
				$attribs = $table['attribs'];
			}
			if (isset($table['caption'])) {
				$caption = $table['caption'];
			}
			if (isset($table['cols'])) {
				$cols = $table['cols'];
			}
			if (isset($table['thead'])) {
				$thead = $table['thead'];
			}
			if (isset($table['tfoot'])) {
				$tfoot = $table['tfoot'];
			}
			if (isset($table['specialchars'])) {
				$specialchars = $table['specialchars'];	
			}
			$rows =& $table['rows'];
		}
		
		try {
			$attribs = $this->_xhtmlAttribs($attribs);
			if ($caption !== null) {
				$caption = "\n\t" . $this->xhtmlElement('caption', $caption);
			}
			if (is_array($cols)) {
				foreach ($cols as $key => $col_attribs) {
					$cols[$key] = '<col' . $this->_xhtmlAttribs($col_attribs) . ' />';
				}
				$cols = "\n\t" . implode("\n\t", $cols);
			} elseif ($cols) {
				$cols = "\n\t" . $cols;
			}
			if (is_array($thead)) {
				$thead = $this->_xhtmlTableRow($thead, null, 'th', true);
				$thead = "\n\t" . '<thead>' . "\n\t\t" . $thead . "\n\t" . '</thead>';
			} else {
				$thead = "\n\t" . $thead;
			}
			if (is_array($tfoot)) {
				$tfoot = $this->_xhtmlTableRow($tfoot, null, 'td', true);
				$tfoot = "\n\t" . '<tfoot>' . "\n\t\t" . $tfoot . "\n\t" . '</tfoot>';
			} else {
				$tfoot = "\n\t" . $tfoot;
			}
		
			$tbody = array();
			foreach ($rows as $row) {
				$tbody[] = $this->_xhtmlTableRow($row, null, 'td', $specialchars);
			}
			$tbody = "\n\t" . '<tbody>' . "\n\t\t" . implode("\n\t\t", $tbody) . "\n\t" . '</tbody>';
		} catch (Exception $e) {
			throw $e;
		}

		$element = '<table' . $attribs . '>' . $caption . $cols . $thead . $tbody . $tfoot . "\n" . '</table>';
		return $element;
	}
	
	/**
	 * Создает строку элемента "table".
	 *
	 * @param array $row Массив ячеек строки.
	 * Допускается указание части или всех остальных параметров в этом же массиве.
	 * В этом случае:
	 * $row['cells'] - массив ячеек (обязательный элемент);
	 * $row['attribs'] - замещает параметр $attribs;
	 * $row['cellType'] - замещает параметр $cellType;
	 * $row['specialchars'] - замещает параметр $specialchars.
	 * Формат передачи данных о конкретной ячейке полностью совпадает
	 * с форматом первого параметра метода {@link _xhtmlTableCell}.
	 * 
	 * @param string|array $attribs Строка или массив атрибутов элемента "tr".
	 * 
	 * @param string $cellType Тип ячеек строки ("td" или "th").
	 * 
	 * @param boolean $specialchars Флаг преобразования в ячейках строки спец. символов в html-сущности.
	 * 
	 * @return string
	 * @throws Exception
	 */
	protected function _xhtmlTableRow($row, $attribs = null, $cellType = 'td', $specialchars = true)
	{	
		if (!is_array($row)) {
			throw new Exception('First param must be an array');
		}
		
		if (isset($row['cells'])) {
			if (!is_array($row['cells'])) {
				throw new Exception('Param "cells" must be an array');
			}
			
			if (isset($row['attribs'])) {
				$attribs = $row['attribs'];
			}
			if (isset($row['cellType'])) {
				$cellType = $row['cellType'];
			}
			if (isset($row['specialchars'])) {
				$specialchars = $row['specialchars'];
			}
			$row = $row['cells'];
		}
		$attribs = $this->_xhtmlAttribs($attribs);
		
		foreach ($row as $key => $cell) {
			$row[$key] = $this->_xhtmlTableCell($cell, null, $cellType, $specialchars);
		}
		$row = '<tr' . $attribs . '>' . implode('', $row) . '</tr>';
		return $row;
	}
	
	/**
	 * Создает ячейку элемента "tr".
	 *
	 * @param string|array $cell Содержимое ячейки.
	 * Если $cell является массивом, то:
	 * $cell['content'] - содержимое ячейки;
	 * $cell['attribs'] - замещает параметр $attribs;
	 * $cell['type'] - замещает параметр $type;
	 * $cell['specialchars'] - замещает параметр $specialchars.
	 * 
	 * @param string|array $attribs Строка или массив атрибутов ячейки.
	 * 
	 * @param string $type Тип ячейки ("td" или "th").
	 * 
	 * @param boolean $specialchars Флаг преобразования в ячейке спец. символов в html-сущности.
	 * 
	 * @return string
	 */
	protected function _xhtmlTableCell($cell = null, $attribs = null, $type = 'td', $specialchars = true)
	{	
		if (is_array($cell)) {
			if (isset($cell['attribs'])) {
				$attribs = $cell['attribs'];
			}
			if (isset($cell['type'])) {
				$type = $cell['type'];
			}
			if (isset($cell['specialchars'])) {
				$specialchars = $cell['specialchars'];
			}
			if (isset($cell['content'])) {
				$cell = $cell['content'];
			}
		}
		return $this->xhtmlElement($type, $cell, $attribs, $specialchars);
	}
}