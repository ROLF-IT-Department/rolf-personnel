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
 * ����������� xhtml-�������� "table".
 * 
 * @category   System
 * @package    System_View
 * @subpackage System_View_Helper
 */
class System_View_Helper_XhtmlTable extends System_View_Helper_XhtmlElement
{	
	/**
	 * ������� ������� "table".
	 *
	 * @param array &$table ������ �� ������ ����� �������� "table".
	 * ������ ������� ������� $table ������ �������� �������� ����� ������.
	 * ��������� ��������� ������� ����� ���� ����� �������� ����� ������ $table.
	 * � ���� ������:
	 * 1. $table ������ ��������� ������� $table['rows'] - ������ ����� �������
	 * (������ ��� ������� ����� �������� ������� ��������� ����� ������ ��������� � ���� �� �������);
	 * 2. �������� ��������� ������� param ������ ����������� � �������� $table['param'];
	 * 3. ������� � ������ ������� ����� �������� ����� �������� ���������� � ������� $table,
	 * ����� (��� ����������� ��������) ����� ������������ �������� ��������������� ����������.
	 * ������ �������� ������ � ���������� ������ � ������ ��������� ��������� � �������� ������� ���������
	 * �������������� ������� {@link _xhtmlTableRow} � {@link _xhtmlTableCell}.
	 * 
	 * @param string|array $attribs ������ ��� ������ ��������� �������� "table".
	 * 
	 * @param string $caption ��������� �������.
	 * 
	 * @param string|array $cols ������ ������� ��� ������ ��������� ������� �������� "table".
	 * 
	 * @param string|array $thead ������ ��� ������ ����� ����� �������.
	 * ��-��������� ����. ������� � ������� ����� �� ����������� � html-��������.
	 * 
	 * @param string|array $tfoot ������ ��� ������ ����� footer'� �������.
	 * 
	 * @param boolean $specialchars ���� �������������� � ������� ������� (tbody � tfoot) 
	 * ����. �������� � html-��������.
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
	 * ������� ������ �������� "table".
	 *
	 * @param array $row ������ ����� ������.
	 * ����������� �������� ����� ��� ���� ��������� ���������� � ���� �� �������.
	 * � ���� ������:
	 * $row['cells'] - ������ ����� (������������ �������);
	 * $row['attribs'] - �������� �������� $attribs;
	 * $row['cellType'] - �������� �������� $cellType;
	 * $row['specialchars'] - �������� �������� $specialchars.
	 * ������ �������� ������ � ���������� ������ ��������� ���������
	 * � �������� ������� ��������� ������ {@link _xhtmlTableCell}.
	 * 
	 * @param string|array $attribs ������ ��� ������ ��������� �������� "tr".
	 * 
	 * @param string $cellType ��� ����� ������ ("td" ��� "th").
	 * 
	 * @param boolean $specialchars ���� �������������� � ������� ������ ����. �������� � html-��������.
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
	 * ������� ������ �������� "tr".
	 *
	 * @param string|array $cell ���������� ������.
	 * ���� $cell �������� ��������, ��:
	 * $cell['content'] - ���������� ������;
	 * $cell['attribs'] - �������� �������� $attribs;
	 * $cell['type'] - �������� �������� $type;
	 * $cell['specialchars'] - �������� �������� $specialchars.
	 * 
	 * @param string|array $attribs ������ ��� ������ ��������� ������.
	 * 
	 * @param string $type ��� ������ ("td" ��� "th").
	 * 
	 * @param boolean $specialchars ���� �������������� � ������ ����. �������� � html-��������.
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