
Js.Toolbar = function(id, classNames)
{
	var _tag          = Js.get(id).appendChild(document.createElement('table'));
	var _row          = _tag.insertRow(-1);
	var _items        = {};
	var _itemSelected = null;
	var _classNames   = {
		toolbar       : 'toolbar',
		item          : 'toolbarItem',
		itemHover     : 'toolbarItemHover',
		itemSelected  : 'toolbarItemSelected'
	};

	this.onclick  = null;
	this.onselect = null;

	this.getItem = function(id)
	{
		return _items[id];
	}

	this.addItem = function(attribs)
	{
		var item = new Js.ToolbarItem(attribs);

		return this.appendItem(item);
	}

	this.appendItem = function(item)
	{
		_row.appendChild(item.render(_classNames));
		_items[item.id] = item;
		item.setToolbar(this);

		return item;
	}

	this.setClassNames = function(classNames)
	{
		for (var key in classNames) {
			_classNames[key] = classNames[key];
		}
	}

	this.onclickHandler = function(item)
	{
		if (this.onclick && !item.onclick) {
			this.onclick(item);
		}
	}

	this.onselectHandler = function(item)
	{
		if (_itemSelected) {
			_itemSelected.unselect();
		}
		_itemSelected = item;

		if (this.onselect && !item.onselect) {
			this.onselect(item);
		}
	}

	this.setClassNames(classNames);
	_tag.className = _classNames.toolbar;
}

Js.ToolbarItem = function(attribs)
{
	if (!(attribs instanceof Object)) {
		attribs = {text : attribs};
	}

	var _item       = this;
	var _tag        = document.createElement('td');
	var _label      = _tag.appendChild(document.createElement('div'));
	var _classNames = {};

	this.id         = attribs.id || ('toolbarItem' + Js.id());
	this.text       = attribs.text || '';
	this.toolbar    = null;
	this.rendered   = false;
	this.selected   = false;
	this.className  = attribs.className || '';
	this.onclick    = attribs.onclick || null;
	this.onselect   = attribs.onselect || null;

	this.setToolbar = function(toolbar)
	{
		if (toolbar.getItem(this.id) == this) {
			return this.toolbar = toolbar;
		}
		return false;
	}

	this.render = function(classNames)
	{
		_classNames = classNames;

		_tag.className = this.className += ' ' + classNames.item;

		if (this.selected) {
			_tag.className += ' ' + classNames.itemSelected;
		}
		_label.innerHTML = this.text;

		this.rendered = true;

		return _tag;
	}

	this.click = function()
	{
		if (this.onclick) {
			this.onclick(this);
		}
		if (this.toolbar) {
			this.toolbar.onclickHandler(this);
		}
		this.select();
	}

	this.select = function()
	{
		if (!this.selected && this.rendered) {
			_tag.className += ' ' + _classNames.itemSelected;
		}
		this.selected = true;

		if (this.onselect) {
			this.onselect(this);
		}
		if (this.toolbar) {
			this.toolbar.onselectHandler(this);
		}
	}

	this.unselect = function()
	{
		var re = new RegExp("\\s*\\b" + _classNames.itemSelected + "\\b", "ig");
		_tag.className = _tag.className.replace(re, '');

		this.selected = false;
	}

	this.addClassName = function(className)
	{
		if (className) {
			this.className += ' ' + className;

			if (this.rendered) {
				_tag.className = this.className;
			}
		}
	}

	_tag.onclick = function()
	{
		_item.click();
	}

	_tag.onmouseover = function()
	{
		this.className += ' ' + _classNames.itemHover;
	}

	_tag.onmouseout = function()
	{
		this.className = _item.className;
		if (_item.selected) {
			this.className += ' ' + _classNames.itemSelected;
		}
	}
}