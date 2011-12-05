
Js.TabPanel = function()
{
	var _items = {};
	var _itemActivated = null;
	var _classNames = {
		item          : 'tabs-item',
		itemHover     : 'tabs-item-hover',
		itemDisabled  : 'tabs-item-disabled',
		itemActivated : 'tabs-item-activated',
		body          : 'tabs-body',
		bodyActivated : 'tabs-body-activated'
	};

	this.addTab = function(id, bodyId)
	{
		var bookmark = new Js.TabPanelItem(id, bodyId, _classNames);
		_items[id] = bookmark;
		bookmark.setTabPanel(this);
		if (!_itemActivated) {
			bookmark.activate();
		}
	}

	/**
	 * Выделение определённой закладки
	 * @param id
	 * @param bodyId
	 */
	this.focus = function(id, bodyId)
	{
		var bookmark = new Js.TabPanelItem(id, bodyId, _classNames);
		_items[id] = bookmark;
		bookmark.setTabPanel(this);
		if (_itemActivated.id != id) {
			bookmark.activate();
		}
	}

	this.addTabs = function(items)
	{
		for (var id in items) {
			this.addTab(id, items[id]);
		}
	}

	this.getTab = function(id)
	{
		return _items[id];
	}

	this.onactivateHandler = function(item)
	{
		if (_itemActivated) {
			_itemActivated.deactivate();
		}
		_itemActivated = item;
	}

	this.ondeactivateHandler = function(item)
	{
		if (_itemActivated == item) {
			_itemActivated = null;
		}
	}

	this.setClassNames = function(classNames)
	{
		for (var key in classNames) {
			_classNames[key] = classNames[key];
		}
	}
}

Js.TabPanelItem = function(id, bodyId, classNames)
{
	this.id = id;
	this.activated = null;
	this.disabled = null;

	var _this = this;
	var _panel = null;
	var _tag = Js.get(id);
	var _body = Js.get(bodyId);
	var _classNames = classNames;

	_tag.onclick = function(e)
	{
		var tab_re = new RegExp("tabs-item");
		var card_active_tab = this.id.replace(tab_re, '');

		if(document.card)
		{
			var card_id = document.card.id.value;
			document.card.tab.value = card_id + card_active_tab;
		}

		_this.activate();
	}

	_tag.focus = function(e)
	{
		var tab_re = new RegExp("tabs-item-");
		if(document.card)
		{
			document.card.tab.value = this.id.replace(tab_re, '');
		}

		_this.activate();
	}

	_tag.onmouseover = function()
	{
		this.className += ' ' + _classNames.itemHover;
	}

	_tag.onmouseout = function()
	{
		var re = new RegExp("\\s*\\b" + _classNames.itemHover + "\\b", "ig");
		this.className = this.className.replace(re, '');
	}

	this.setTabPanel = function(panel)
	{
		if (panel.getTab(this.id) == this) {
			return _panel = panel;
		}
		return false;
	}

	this.activate = function()
	{
		if (this.activated) {
			return false;
		}
		_tag.className += ' ' + _classNames.itemActivated;
		_body.className += ' ' + _classNames.bodyActivated;
		this.activated = true;
		if (_panel) {
			_panel.onactivateHandler(this);
		}
	}

	this.deactivate = function()
	{
		if (!this.activated) {
			return false;
		}
		var re = new RegExp("\\s*\\b" + _classNames.itemActivated + "\\b", "ig");
		_tag.className = _tag.className.replace(re, '');
		re = new RegExp("\\s*\\b" + _classNames.bodyActivated + "\\b", "ig");
		_body.className = _body.className.replace(re, '');
		this.activated = false;
		if (_panel) {
			_panel.ondeactivateHandler(this);
		}
	}
}