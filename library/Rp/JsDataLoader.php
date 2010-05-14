<?php
ob_start("_JSCALLBACK");

$GLOBALS['_JSQUERYID'] = empty($_GET['JSQUERYID']) ? 0 : $_GET['JSQUERYID'];

// Convert PHP scalar, array or hash to JS scalar/array/hash. 
function _PHP2JS($a) 
	{ 
	if (is_null($a)) return 'null'; 
	if ($a === false) return 'false'; 
	if ($a === true) return 'true'; 
	if (is_int($a) || is_float($a)) return strval($a); 
	if (is_scalar($a)) { 
		$a = addslashes($a); 
		$a = str_replace("\n", '\n', $a); 
		$a = str_replace("\r", '\r', $a); 
		return "'$a'"; 
	} 
	$isList = true; 
	for ($i=0, reset($a); $i<count($a); $i++, next($a)) 
	if (key($a) !== $i) { $isList = false; break; } 
	$result = array(); 
	if ($isList) { 
		foreach ($a as $v) $result[] = _PHP2JS($v); 
		return '[ ' . join(',', $result) . ' ]'; 
	} else { 
		foreach ($a as $k=>$v) $result[] = _PHP2JS($k) . ': ' . _PHP2JS($v); 
		return '{ ' . join(',', $result) . ' }'; 
	}	 
}

function _JSCALLBACK($buffer) {
	header("Content-type: text/javascript; charset: windows-1251");
	$result = isset($GLOBALS['_RESULT']) ? _PHP2JS(@$GLOBALS['_RESULT']) : 'null';
	$buffer = "JsDataLoader.dataReady(" . _PHP2JS($GLOBALS['_JSQUERYID']) . ", " . $result  . ", " . _PHP2JS($buffer) . ");";
	
	return $buffer;
}
?>