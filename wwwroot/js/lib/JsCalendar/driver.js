
function JsCalendarInit(pathView)
{
	pathView += '/calendar.html';
	
	window.JsCalendar = new function()
	{
		//var pathView = pathView || 'http://co-hr-prog/rolf-personnel/js/library/JsCalendar/calendar.html'; 
		this.handler  = null;
		
		this.open = function()
		{
			//var href = this.pathView + '?2006,10,13';
			var href = pathView;
			var params = 'width=200,height=200,menubar=0,toolbar=0,location=0,status=0,scrollbars=0,resizable=0';
			window.open(href, null, params);
		}
	}
}