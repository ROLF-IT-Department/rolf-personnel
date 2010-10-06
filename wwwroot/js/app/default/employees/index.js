
var list = new function()		// список подчиненных или просматриваемых сотрудников
{
	var _this = this;
	var _emps = null;
	var _subemps = null;
	var _rowSelected = null;
	var _rowHover = null;
	
	this.init = function()
	{
		_emps = document.getElementById('emps');
		_subemps = document.getElementById('subemps');
		
		if (_emps) {
			this.select(_emps.rows[0]);
			_emps.onclick = _listOnclickHandler;
			_emps.ondblclick = _listOndblclickHandler;
			_emps.onmouseover = _listOnmouseoverHandler;
			_emps.onmouseout = _listOnmouseoutHandler;
		}
		if (_subemps) {
			if (!_emps) {
				this.select(_subemps.rows[0]);
			}
			_subemps.onclick = _listOnclickHandler;
			_subemps.ondblclick = _listOndblclickHandler;
			_subemps.onmouseover = _listOnmouseoverHandler;
			_subemps.onmouseout = _listOnmouseoutHandler;
		}
	}
	
	this.select = function(row)
	{
		if (!row || row.nodeName.toUpperCase() != 'TR') {
			return false;
		}
		this.replaceProfile(row.cells[0].innerHTML);
		this.unselect();
		row.className += ' list-row-selected';
		_rowSelected = row;
	}
	
	this.unselect = function()
	{
		if (!_rowSelected) {
			return false;
		}
		_rowSelected.className = _rowSelected.className.replace(/\s*\blist-row-selected\b/ig, '');
		_rowSelected = null;
	}
	
	this.replaceProfile = function(personId)
	{
		var url = BASE_URL + '/default/profile/index/personid/' + personId;
		window.profile.location = url;
	}
	
	function _listOnclickHandler(e)
	{
		e = e || window.event;
		_this.select((e.srcElement || e.target).parentNode);
	}
	
	function _listOndblclickHandler(e)
	{
		e = e || window.event;
		e.cancelBubble = true;
		var personId = (e.srcElement || e.target).parentNode.cells[0].innerHTML;
		var url = BASE_URL + '/card/achievs/index/personid/' + personId;
		window.open(url, '', 'width=1000,height=714,status=1,resizable=1');
	}
	
	function _listOnmouseoverHandler(e)
	{
		e = e || window.event;
		var row = (e.srcElement || e.target).parentNode;
		row.className += ' list-row-hover';
		_rowHover = row;
	}
	
	function _listOnmouseoutHandler(e)
	{
		if (_rowHover) {
			_rowHover.className = _rowHover.className.replace(/\s*\blist-row-hover\b/ig, '');
			_rowHover = null;
		}
	}
}

function init()
{
	window.focus();
	document.body.onselectstart = function() {return false};
	
	list.init();
}