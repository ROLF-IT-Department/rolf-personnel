var oCalendar = new function()
{
	this.days          = null;
	this.months        = null;
	this.years         = null;
	this.oCellSelected = null;
	this.oDateSelected = null;
	this.blurTimeout   = null;

	this.init = function()
	{
//		this.resize(190, 215);
		//window.onblur  = this.onblurHandler;
		window.onfocus = this.onfocusHandler;

		this.days   = document.getElementById('days').getElementsByTagName('td');
		this.months = document.getElementById('months');
		this.years  = document.getElementById('years');
		
		var params = location.search.substr(1).split(',');
		params[0] = parseInt(params[0]);  // год или метка времени
		params[1] = parseInt(params[1]);  // месяц
		params[2] = parseInt(params[2]);  // число
		
		if (isNaN(params[1])) {
			if (params[0]) {
				this.oDateSelected = new Date(params[0]);
			}
			var oDate = this.oDateSelected || new Date();
			params[0] = oDate.getFullYear();
			params[1] = oDate.getMonth();
		}
		else if (params[2]) {
			this.oDateSelected = new Date(params[0], params[1], params[2]);
		}
		
		this.reload(params[0], params[1]);
	}

	this.reload = function(year, month)
	{
		var i   = 0;
		var num = this.years.options.length;
		while (i < num && this.years.options[i].value != year) {
			i++;
		}
		if (i == num) {
			return false;
		}
		this.years.options[i].selected      = true;
		this.months.options[month].selected = true;

		var oDate      = new Date(year, month, 1);
		var numDays    = oDate.numDays();
		var offsetDays = oDate.offsetDays();
		
		var cell = null;
		for (i = 0; i < offsetDays; i++) {
			cell = this.days[i];
			cell.innerHTML   = '&nbsp;';
			cell.className   = '';
			cell.onmouseover = null;
			cell.onmouseout  = null;
		}
		for (num = 1; num <= numDays; num++, i++) {
			cell = this.days[i];
			cell.innerHTML   = num;
			cell.className   = '';
			cell.onmouseover = this.onmouseoverHandler;
			cell.onmouseout  = this.onmouseoutHandler;
		}
		for (; i < 42; i++) {
			cell = this.days[i];
			cell.innerHTML   = '&nbsp;';
			cell.className   = '';
			cell.onmouseover = null;
			cell.onmouseout  = null;
		}
		
		var oDateCurrent = new Date();
		if (oDateCurrent.getMonth() == month && oDateCurrent.getFullYear() == year) {
			this.days[offsetDays + oDateCurrent.getDate() - 1].className = 'current';
		}
		var oDateSelected = this.oDateSelected;
		if (oDateSelected && oDateSelected.getMonth() == month && oDateSelected.getFullYear() == year) {
			var oCell = this.days[offsetDays + oDateSelected.getDate() - 1];
			this.setCellSelected(oCell);
		}
		return true;
	}
	
	this.today = function()
	{
		var oDate = new Date();
		this.oDateSelected = oDate;
		this.reload(oDate.getFullYear(), oDate.getMonth());
	}
	
	this.send = function()
	{
		var time  = this.oDateSelected.getTime();
		var year  = this.oDateSelected.getFullYear();
		var month = this.oDateSelected.getMonth();
		var day   = this.oDateSelected.getDate();
		
		var opener = window.opener;
		if (!opener || !opener.JsCalendar || !opener.JsCalendar.handler) {
			return false;
		}
		opener.JsCalendar.handler(time, year, month, day);
		
		this.close();
		return true;
	}
	
	this.resize = function(width, height)
	{
		window.resizeTo(width, height);
		if (document.body.clientHeight < 185) {
			window.resizeBy(0, 185 - document.body.clientHeight);
		}
		return true;
	}
	
	this.close = function()
	{
		window.close();
	}

	this.onmouseoverHandler = function(e)
	{
		if (!e)	var e = window.event;
		var obj = e.srcElement || e.target;
		obj.className += ' hover';
	}
				
	this.onmouseoutHandler = function(e)
	{
		if (!e) var	e = window.event;
		var obj = e.srcElement || e.target;
		obj.className = obj.className.replace(/\s?\bhover\b/, ''); // FF удаляет лишние пробелы
	}
				
	this.onclickHandler = function(e)
	{
		if (!e) var	e = window.event;
		var obj = e.srcElement || e.target;
		
		this.setDateSelected(obj);
		this.send();
		return true;
	}
	
	this.setDateSelected = function(oCell)
	{
		var date = parseInt(oCell.innerHTML);
		if (!date) {
			return false;
		}
		var month = getSelectValue(this.months);
		var year  = getSelectValue(this.years);
		
		this.oDateSelected = new Date(year, month, date);
		this.setCellSelected(oCell);
		return true;
	}
	
	this.setCellSelected = function(oCell)
	{
		if (this.oCellSelected) {
			this.oCellSelected.className = this.oCellSelected.className.replace(/\s?\bselected\b/, '');
		}
		oCell.className += ' selected';
		this.oCellSelected = oCell;
	}
			
	this.onchangeHandler = function()
	{
		var month = getSelectValue(this.months);
		var year  = getSelectValue(this.years);
		
		this.reload(year, month);
	}
	
	this.onblurHandler = function()
	{
		this.blurTimeout = window.setTimeout(this.close, 1000);
	}
	
	this.onfocusHandler = function()
	{
		if (this.blurTimeout) {
			window.clearTimeout(this.blurTimeout);
			this.blurTimeout = null;
		}
	}
	
	function getSelectValue(obj)
	{
		return obj.options[obj.selectedIndex].value;
	}
}

Date.prototype.numDays = function()
{
	switch (this.getMonth()) {
		case  1:
			var year = this.getFullYear();
			return (year % 4 == 0 && (year % 100 != 0 || year % 400 == 0)) ? 29 : 28;
		case  3:
		case  5:
		case  8:
		case 10:
			return 30;
		default:
			return 31;
	}
}

Date.prototype.offsetDays = function()
{
	var offset = this.getDay() - 1;
	if (offset < 0)	offset = 6;
	return offset;
}