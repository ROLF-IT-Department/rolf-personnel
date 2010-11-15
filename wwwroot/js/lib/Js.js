
Js = new function()
{
	var _idCounter = 0;

	this.id = function()
	{
		return ++_idCounter;
	}

	this.get = function(id)
	{
		return document.getElementById(id);
	}

	this.toBool = function(value)
	{
		if (!value || value == '0') {
			return false;
		}
		return true;
	}

	this.open = function(url, name, width, height, top, left, params)
	{
		params = params ? [params.join('=1,') + '=1'] : [];

		if (width) {
			params.push('width=' + width);
		}
		if (height) {
			params.push('height=' + height);
		}
		if (top) {
			params.push('top=' + top);
		}
		if (left) {
			params.push('left=' + left);
		}
		params = params.join(',');

		return window.open(url, name, params);
	}
}

$(document).ready(function(){
   $('textarea').change(function(){
	   Card._textfieldChangeHandler(this);
   })
 });
