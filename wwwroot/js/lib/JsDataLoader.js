
// JavaScript Data Loader (version 1.1)

var JsDataLoader = new function() {
	var COUNT = 0;
	var TIMEOUT = 30000;
	var PENDING = {};
	var CACHE = {};
	var TAGS = null;

	this.setTimeout = function(milliSeconds) {
		TIMEOUT = milliSeconds ? milliSeconds : null;
	}

	this.tagScriptsReuse = function(reuse) {
		if (reuse && !TAGS)
			TAGS = [];
		else if (!reuse && TAGS) {
			for (var key in TAGS)
				TAGS[key].parentNode.removeChild(TAGS[key]);
				//TAGS[key].removeNode(true); // не поддерживается FireFox
			TAGS = null;
		}
	}

	this.cacheReset = function(scriptName) {
		if (scriptName) {
			scriptName = scriptName.toString();
			for (var key in CACHE)
				if (key.substr(0, scriptName.length) == scriptName)
					delete CACHE[key];
		}
		else
			CACHE = {};
	}
	
	this.dataReady = function(id, result, text) {
		if (!PENDING[id])
			return false;

		var pending = PENDING[id];
		
		if (pending.timer)
			clearTimeout(pending.timer);

		pending.objQuery.result = result;
		pending.objQuery.text = text;

		if (pending.objQuery.onexecuted)
			pending.objQuery.onexecuted(result, text);

		if (pending.href) {
			CACHE[pending.href] = {};
			CACHE[pending.href].result = result;
			CACHE[pending.href].text = text;
		}
		this.remove(id);
	}

	this.abort = function(id) {
		if (PENDING[id]) {
			var pending = PENDING[id];
			this.remove(id);
			if (pending.onabort) pending.onabort();
		}
	}

	this.remove = function(id) {
		if (PENDING[id]) {
			_cleanupTagScript(PENDING[id].tagScript);
			delete PENDING[id];
		}
	}

	this.create = function(script, onexecuted, onabort, timeout) {
		var objQuery = new queryPattern();

		if (script)
			objQuery.script = script;
		if (onexecuted)
			objQuery.onexecuted = onexecuted;
		if (onabort)
			objQuery.onabort = onabort;
		if (timeout)
			objQuery.timeout = timeout;

		return objQuery;
	}
	
	function queryPattern() {
		this.script = null;
		this.result = null;
		this.text = null;
		this.onexecuted = null;
		this.onabort = null;
		this.timeout = TIMEOUT;

		this.send = function(params, cacheUse) {	
			if (!this.script)
				return false;

			var strParams = _getStrParams(params);
			var href = this.script + (this.script.indexOf('?') >= 0 ? '&' : '?') + strParams;

			if (cacheUse && CACHE[href]) {
				this.result = CACHE[href].result;
				this.text = CACHE[href].text;
				if (this.onexecuted)
					this.onexecuted(this.result, this.text);
				return false;
			}
			else {
				var pending = new pendingPattern(this);
				
				if (cacheUse)
					pending.href = href;
				
				var link = href + (href.indexOf('?') >= 0 ? '&' : '?') + 'JSQUERYID=' + pending.id;
				if (this.timeout)
					pending.timer = setTimeout("JsDataLoader.abort(" + pending.id + ")", this.timeout);
				pending.tagScript.setAttribute('src', link);
				//return true;
				return pending.id;
			}
		}
	}

	function pendingPattern(objQuery) {
		this.id = COUNT++;
		this.objQuery = objQuery;
		this.tagScript = _getTagScript();
		this.onabort = objQuery.onabort;
		
		PENDING[this.id] = this;
	}

	function _getStrParams(params) {
		var buf = [];
		if (params instanceof Object) {
			for (var key in params)
				buf[buf.length] = key + '=' + params[key];
		}
		else buf = [params];
		
		return buf.join('&');
	}

	function _getTagScript() {
		var tagScript;
		if (TAGS == null || !TAGS.length) {
			tagScript = document.getElementsByTagName("HEAD")[0].appendChild(document.createElement("SCRIPT"));
			tagScript.setAttribute('language', 'javascript');
		}
		else {
			tagScript = TAGS[TAGS.length - 1];
			delete TAGS[TAGS.length - 1];
			TAGS.length--;
		}
		return tagScript;
	}

	function _cleanupTagScript(tagScript) {
		if (tagScript) {
			if (TAGS == null)
				tagScript.parentNode.removeChild(tagScript);
			else
				TAGS[TAGS.length] = tagScript;
		}
	}
}