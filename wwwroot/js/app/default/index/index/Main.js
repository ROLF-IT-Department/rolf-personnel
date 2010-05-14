
Main = function()
{	
	var _main = this;
	
	this.sidebar = null;
	this.container = null;
	this.delimiter = null;
	
	this.init = function()
	{
		this.sidebar = new Sidebar();
		this.container = new Container();
		this.delimiter = new Delimiter();
	}
	
	function Sidebar()
	{
		this.node = document.getElementById('sidebar');
		this.menu = document.getElementById('menu');
		
		this.setWidth = function(w)
		{
			if (w >= 0) {
				var x = w + this.node.offsetLeft;
				
				this.node.style.width = w + 'px';
				_main.delimiter.node.style.left = x + 3 + 'px';
				_main.container.node.style.left = x + 6 + 'px';
			}
		}
	}
	
	function Container()
	{
		this.node = document.getElementById('container');
		this.content = document.getElementById('content');
		
		var _captionLabel = document.getElementById('captionLabel');
		
		this.setCaption = function(text)
		{
			_captionLabel.innerHTML = text;
		}
		
		this.setCaptionClass = function(className)
		{
			_captionLabel.className = className;
		}
		
		this.replace = function(url)
		{
			(window.content || this.content.contentWindow).location = url;
		}
	}
	
	function Delimiter()
	{
		this.node = document.getElementById('delimiter');
		
		this.node.ondrag = function(e)
		{
			_main.sidebar.setWidth((e || window.event).clientX - _main.sidebar.node.offsetLeft - 3);
		}
		
		this.node.onmousedown = function()
		{
			document.onmousemove = this.ondrag;
			this.className += ' delimiter-activated';
		}
		
		this.node.onmouseup = function()
		{
			document.onmousemove = null;
			this.className = this.className.replace(/\s*\bdelimiter-activated\b/ig, '');
		}
	}
}