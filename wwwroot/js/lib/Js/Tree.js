
Js.Tree = function(id, hoverEnable, classNames)
{
	var _root = Js.get(id).appendChild(document.createElement('ul'));
	var _nodes = {};
	var _nodesSelected = {};
	var _hoverEnable = hoverEnable;
	var _classNames = {
		root           : 'tree-root',
		branch         : 'tree-branch',
		branchExpanded : 'tree-branch-expanded',
		node           : 'tree-node',
		nodeExpanded   : 'tree-node-expanded',
		nodeLeaf       : 'tree-node-leaf',
		nodeHover      : 'tree-node-hover',
		nodeSelected   : 'tree-node-selected'
	};
	this.onselect = null;
	this.ontoggle = null;
	this.onexpand = null;
	this.oncollapse = null;
	
	this.getNode = function(id)
	{
		return _nodes[id];
	}
	
	this.getNodesSelected = function()
	{
		return _nodesSelected;
	}
	
	this.getAllNodes = function()
	{
		return _nodes;
	}
	
	this.expandAllNodes = function()
	{
		for (var id in _nodes) {
			_nodes[id].expand();
		}
	}
	
	this.addNode = function(attribs)
	{
		var node = new Js.TreeNode(attribs);
		return (_nodes[attribs.pid] || this).appendChild(node);
	}
	
	this.appendChild = function(node)
	{
		this.registryNode(node);
		_root.appendChild(node.render(_classNames, _hoverEnable));
		return node;
	}
	
	this.registryNode = function(node)
	{
		var childs = node.childNodes();
		for (var i = 0; i < childs.length; i++) {
			this.registryNode(childs[i]);
		}
		if (node.selected) {
			_nodesSelected[node.id] = node;
		}
		_nodes[node.id] = node;
		node.setTree(this);
	}
	
	this.onselectHandler = function(node)
	{
		for (var id in _nodesSelected) {
			if (id != node.id) {
				_nodesSelected[id].unselect();
				delete _nodesSelected[id];
			}
		}
		_nodesSelected[node.id] = node;
		if (this.onselect) {
			this.onselect(node);
		}
		$('#loading').css({
			display: 'block'
		});
	}
	
	this.onunselectHandler = function(node)
	{
		delete _nodesSelected[node.id];
	}
	
	this.ontoggleHandler = function(node)
	{
		if (this.ontoggle) {
			this.ontoggle(this);
		}
	}
	
	this.onexpandHandler = function(node)
	{
		if (this.onexpand) {
			this.onexpand(node);
		}
	}
	
	this.oncollapseHandler = function(node)
	{
		if (this.oncollapse) {
			this.oncollapse(node);
		}
	}
	
	this.setClassNames = function(classNames)
	{
		for (var key in classNames) {
			_classNames[key] = classNames[key];
		}
	}
	
	this.setClassNames(classNames);
	_root.className = _classNames.root;
}

Js.TreeNode = function(attribs)
{
	attribs = attribs || {};
	if (typeof attribs == typeof '') {
		attribs = {text : attribs};
	}
	this.id = attribs.id || ('tree-node' + Js.id());
	this.text = attribs.text;
	this.leaf = Js.toBool(attribs.leaf);
	this.expanded = attribs.expanded;
	this.selected = attribs.selected;
	
	var _this = this;
	var _tree = null;
	var _childNodes = [];
	var _tag = document.createElement('li');
	var _node = _tag.appendChild(document.createElement('div'));
	var _label = _node.appendChild(document.createElement('div'));
	var _branch = _tag.appendChild(document.createElement('ul'));
	var _rendered = false;
	var _classNames = {};
	var _hoverEnable = false;
	
	attribs = null;
	
	this.childNodes = function()
	{
		return _childNodes;
	}
	
	this.hasChildNodes = function()
	{
		return _childNodes.length > 0;
	}
	
	this.setTree = function(tree)
	{
		if (tree.getNode(this.id) == this) {
			return _tree = tree;
		}
		return false;
	}
	
	this.render = function(classNames, hoverEnable)
	{
		_classNames = classNames;
		_hoverEnable = hoverEnable;
		var nodeClassName = [_node.className, classNames.node];
		var branchClassName = [_branch.className, classNames.branch];
		if (this.leaf) {
			nodeClassName.push(classNames.nodeLeaf);
		}
		if (this.expanded) {
			nodeClassName.push(classNames.nodeExpanded);
			branchClassName.push(classNames.branchExpanded);
		}
		if (this.selected) {
			nodeClassName.push(classNames.nodeSelected);
		}
		_node.className = nodeClassName.join(' ');
		_branch.className = branchClassName.join(' ');
		_label.innerHTML = '<span>' + this.text + '</span>';
		
		_node.onclick = _nodeOnclickHandler;
		_label.onclick = _labelOnclickHandler;
		
		if (hoverEnable) {
			this.setHover(true);
		}
		_rendered = true;
		for (var i = 0; i < _childNodes.length; i++) {
			_branch.appendChild(_childNodes[i].render(classNames, hoverEnable));
		}
		return _tag;
	}
	
	function _nodeOnclickHandler(e)
	{
		_this.toggle();
	}
	
	function _labelOnclickHandler(e)
	{
		(e || window.event).cancelBubble = true;
		_this.select();
	}
	
	this.select = function()
	{
		if (_rendered && !this.selected) {
			_node.className += ' ' + _classNames.nodeSelected;
			this.selected = true;
		}
		if (_tree) {
			_tree.onselectHandler(this);
		}
	}
	
	this.unselect = function()
	{
		if (_rendered) {
			var re = new RegExp("\\s*\\b" + _classNames.nodeSelected + "\\b", "ig");
			_node.className = _node.className.replace(re, '');
		}
		this.selected = false;
		if (_tree) {
			_tree.onunselectHandler(this);
		}
	}
	
	this.toggle = function()
	{
		if (this.leaf) {
			return false;
		}
		this.expanded ? this.collapse() : this.expand();
		if (_tree) {
			_tree.ontoggleHandler(this);
		}
	}
	
	this.expand = function()
	{
		if (this.leaf || this.expanded) {
			return false;
		}
		if (_rendered) {
			_node.className += ' ' + _classNames.nodeExpanded;
			_branch.className += ' ' + _classNames.branchExpanded;
		}
		if (!this.hasChildNodes()) {
			this.setLeaf(true);
		}
		this.expanded = true;
		if (_tree) {
			_tree.onexpandHandler(this);
		}
	}
	
	this.collapse = function()
	{
		if (this.leaf || !this.expanded) {
			return false;
		}
		if (_rendered) {
			var re = new RegExp("\\s*\\b" + _classNames.nodeExpanded + "\\b", "ig");
			_node.className = _node.className.replace(re, '');
			re = new RegExp("\\s*\\b" + _classNames.branchExpanded + "\\b", "ig");
			_branch.className = _branch.className.replace(re, '');	
		}
		this.expanded = false;
		if (_tree) {
			_tree.oncollapseHandler(this);
		}
	}
	
	this.appendChild = function(node)
	{
		_childNodes.push(node);
		if (_tree) {
			_tree.registryNode(node);
		}
		if (_rendered) {
			_branch.appendChild(node.render(_classNames, _hoverEnable));
		}
		if (this.leaf) {
			this.setLeaf(false);
		}
		return node;
	}
	
	this.setText = function(text)
	{
		if (_rendered) {
			_label.firstChild.innerHTML = text
		}
		this.text = text;
	}
	
	this.setLeaf = function(flag)
	{
		if (_rendered) {
			if (flag && !this.leaf) {
				_node.className += ' ' + _classNames.nodeLeaf;
			} else if (!flag && this.leaf) {
				var re = new RegExp("\\s*\\b" + _classNames.nodeLeaf + "\\b", "ig");
				_node.className = _node.className.replace(re, '');
			}
		}
		this.leaf = flag;
	}
	
	this.setHover = function(flag)
	{
		if (flag) {
			_node.onmouseover = function()
			{
				this.className += ' ' + _classNames.nodeHover;
			}
			_node.onmouseout = function()
			{
				var re = new RegExp("\\s*\\b" + _classNames.nodeHover + "\\b", "ig");
				this.className = this.className.replace(re, '');
			}
		} else {
			_node.onmouseover = null;
			_node.onmouseout = null;
		}
	}
	
	this.addClassNames = function(classNames)
	{
		if (typeof classNames == typeof '') {
			classNames = {node : classNames};
		}
		if (classNames.node) {
			_node.className += ' ' + classNames.node;
		}
		if (classNames.branch) {
			_branch.className += ' ' + classNames.branch;
		}
	}
	
	this.removeClassNames = function(classNames)
	{
		if (typeof classNames == typeof '') {
			classNames = {node : classNames};
		}
		if (classNames.node) {
			var re = new RegExp("\\s*\\b" + classNames.node + "\\b", "ig");
			_node.className = _node.className.replace(re, '');
		}
		if (classNames.branch) {
			var re = new RegExp("\\s*\\b" + classNames.branch + "\\b", "ig");
			_branch.className = _branch.className.replace(re, '');
		}	
	}
}