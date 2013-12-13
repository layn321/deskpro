var Orb = {};

if (!window.console) {
	window.console = {};
}
['error', 'log', 'warn', 'info', 'debug', 'trace'].each(function(v) {
	if (!window.console[v]) {
		window.console[v] = function() { };
	}
});

/**
 * Create a namespace.
 *
 * @param {String} namespace The namespace to create. Ex: Orb.something.whatever
 */
Orb.createNamespace = function(namespace, obj) {
	var parts = namespace.split('.');
	var objcheck = window;

	parts.forEach(function(part) {
		if (!objcheck[part]) {
			objcheck[part] = {};
		}

		objcheck = objcheck[part];
	});
};



/**
 * Gets the actual object from a string namespace.
 *
 * @param {String} fullname The full namespace path
 * @return {Object}
 */
Orb.getNamespacedObject = function(fullname) {

	var obj = window;

	fullname_parts = fullname.split('.');

	var part = null;
	while (part = fullname_parts.shift()) {
		if (obj[part] === undefined) {
			console.warn('Orb.getNamespacedObject(%s) is an invalid name', fullname);
			return null;
		}

		obj = obj[part];
	}

	return obj;
};



/**
 * Generate a new unique ID.
 *
 * @param {String} prefix An optional prefix
 * @return {String}
 */
Orb.getUniqueId = function(prefix) {

	if (!prefix) {
		prefix = '';
	}

	var id = '';
	do {
		id = prefix + Orb.uuid();
	} while (document.getElementById(id));

	return id;
};



/**
 * Get a unique ID that can be used app-wide. This is completely different from element
 * ID's; these should be used as other JS-type identifiers.
 *
 * @return {String}
 */
Orb.uuid = function() {
	return 'orb_uuid_' + (++Orb.uuid_num);
};
Orb.uuid_num = 0;

/**
 * Like uuid but has a random and a time component as well. Usefil when you need an actual unique id
 * on a page.
 */
Orb.uuidRand = function() {
	var time = (new Date()).getTime();
	var rand = Number.random(1, 999);
	return 'uuid_' + (++Orb.uuid_num) + '_' + time + '_' + rand;
};



/**
 * Get an element. If the param IS an element, returns that.
 *
 * @param {String/HTMLElement} el The ID of an element, or an actual element to return as-is
 * @retrun {HTMLElement}
 */
Orb.getEl = function(el) {
	if (typeOf(el) == 'element') {
		return el;
	}

	return document.getElementById(el);
};
$el = function(el) { return Orb.getEl(el); };



/**
 * Sleep the client for a time. Note this actually freezes the client so should be used
 * very seldomly.
 *
 * @param {Integer} ms How many milliseconds to sleep for
 */
Orb.sleep = function(ms) {
	var start = new Date().getTime();
	for (var i = 0; i < 1e7; i++) {
		if ((new Date().getTime() - start) > ms){
			break;
		}
	}
};

// I dont think this works :)
Orb.mouseInElement = function(mouseX, mouseY, el) {
	var pos = el.offset();
	var width = el.outerWidth();
	var height = el.outerHeight();

	if (mouseX < pos.left || mouseX > pos.left+width) {
		return false;
	}

	if (mouseY < pos.top || mouseY > pos.top+height) {
		return false;
	}

	return true;
};



/**
 * Find the highest z-index value.
 *
 * @param {jQuery} els A jQuery collection. If non specified, then all elements on the page are scanned.
 * @return {Integer}
 */
Orb.findHighestZindex = function(els) {
	if (!els) {
		els = $('body > *');
	}

	var highest = 0;
	els.each(function() {
		var z = parseInt($(this).css('z-index'));
		if (z < 1000000000 && z > highest) {
			highest = z;
		}
	});

	return highest;
};



/**
 * Escape special HTML characters.
 *
 * @param string
 */
Orb.escapeHtml = function(string) {
	string = string||'';

	if (typeOf(string) == 'element') {
		string = $(string).text();
	} else if (typeOf(string) != 'string') {
		if (string.toString) {
			string = string.toString();
		}
		console.error("Invalid type passed to Orb.escapeHtml: %o", string);
		console.trace();
		return (typeof string) + '';
	}

	return string.replace(/&/g, "&amp;")
		.replace(/>/g, "&gt;")
		.replace(/</g, "&lt;")
		.replace(/"/g, "&quot;");
};


/**
 * Convert newlines into HTML breaks
 *
 * @param string
 * @return string
 */
Orb.nl2br = function(string) {
	return string.replace(/\r\n|\n/g, "<br />\n");
};


/**
 * Link URLs in texts
 *
 * @param string
 */
Orb.linkUrls = function(string) {
	string = string||'';
	return string.replace(/\b(https?:\/\/|www\.)([^\s]+)\b(.?)/gi, function(match, o1, o2, o3, offset, s) {
		if (o3 && o3 == '/') {
			o2 += '/';
		}
		if (o1 == 'www.') {
			return '<a href="http://www.'+o2+'">' + match + '</a>';
		} else {
			return '<a href="'+o1+o2+'">' + match + '</a>';
		}
	});
};

/**
 * Appends query data to a URL which might already have query data. So this
 * appends an ? or an & depending.
 *
 * @param url
 * @param k
 * @param v
 */
Orb.appendQueryData = function(url, k, v) {
	var kev = k;
	if (v !== undefined) {
		kev += '=' + encodeURI(v);
	}

	if (url.indexOf('?') === -1) {
		url += '?' + kev;
	} else {
		url += '&' + kev;
	}

	return url;
};

/**
 * Serialize form elements wihtin context
 */
Orb.serializeFormElements = function(context, visitedEls) {
	var postData = [];

	if (!visitedEls) visitedEls = [];

	context.each(function() {
		$(this).find('input, select, textarea').each(function() {

			if (visitedEls.indexOf(this) !== -1) {
				return;
			}

			visitedEls.push(this);

			var el = $(this);
			var name = el.attr('name');

			if (!name) {
				return;
			}

			if (el.is(':checkbox, :radio')) {
				if (el.is(':checked')) {
					postData.push({name: name, value: el.val() });
				}
			} else if (el.is('input, textarea')) {
				postData.push({name: name, value: el.val() });
			} else if (el.is('select')) {
				el.find('option').filter(':selected').each(function() {
					postData.push({name: name, value: $(this).val() });
				});
			}
		});
	});

	return postData;
};


/**
 * Repeat a string `str` `count` times
 *
 * @param str
 * @param count
 * @return string
 */
Orb.strRepeat = function(str, count) {
	var finalStr = [];
	while (count-- > 0) {
		finalStr.push(str);
	}

	return finalStr.join('');
};

/**
 * Check if a string ends with a string
 *
 * @param {String} str
 * @param {String} suffix
 * @return {Boolean}
 */
Orb.strEndsWith = function(str, suffix) {
    return str.indexOf(suffix, str.length - suffix.length) !== -1;
}


/**
 * Takes a regular expression string and escapes special characters
 *
 * @param strRegex
 * @return string
 */
Orb.regexQuote = function(strRegex) {
	return strRegex.replace(/([.?*+^$[\]\\(){}-])/g, "\\$1");
};


/**
 * Check if a string is an email address
 *
 * @param email
 */
Orb.strIsEmail = function(email) {
	if (email.indexOf('@') === -1) {
		return false;
	}

	var parts = email.split('@');
	if (parts.length != 2) {
		return false;
	}

	// Match the part before the @
	var regexName = /^[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*$/i;

	// Match a regular domain name after the @
	var regexDomain = /^(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+(?:[A-Z]{2,15})$/i;

	// Match a IP address after the @
	var regexIp = /^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/i;

	if (!regexName.test(parts[0])) {
		return false;
	}

	if (!regexDomain.test(parts[1]) && !regexIp.test(parts[1])) {
		return false;
	}

	return true;
};


/**
 * Enables a phrase element by phraseId.
 *
 * This .show()'s a phrase element and .hide()'s any other
 * phrases in the same element.
 *
 * @param phraseId
 * @param parentEl
 */
Orb.enablePhraseEl = function(phraseId, parentEl) {
	var phraseEl, phraseClass = phraseId.replace(/\./g, '_');
	if (parentEl) {
		$(parentEl).find('.dp-phrase-switch').removeClass('dp-phrase-on');
		$(parentEl).find('.'+phraseClass).addClass('dp-phrase-on');
	} else {
		phraseEl = $('.' + phraseClass);
		if (phraseEl[0]) {
			$(phraseEl.parent()).find('.dp-phrase-text').removeClass('dp-phrase-on');
			phraseEl.addClass('dp-phrase-on');
		}
	}
};


/**
 * Re-executes a phrase and modifies the text of the element with the new value.
 *
 * @param {jQuery} el
 * @param {Object} vars
 */
Orb.phraseTextEl = function(el, vars) {
	if (!DeskPRO_Window.translate) {
		return;
	}

	var phraseText = el.data('phrase-text');
	phraseText = DeskPRO_Window.translate.phraseWithString(phraseText, vars, true);

	if (el.data('phrase-html')) {
		el.html(phraseText);
	} else {
		el.text(phraseText);
	}

	return el;
};


/**
 * Take elements of array and chunk them into subarrays of size
 *
 * @param {Array}   array
 * @param {Integer} size
 */
Orb.arrayChunk = function(array, size) {
	var newArray = [], chunk = [], i;

	for (i = 0; i < array.length; i++) {
		if (chunk.length == size) {
			newArray.push(chunk);
			chunk = [];
		}

		if (chunk.length < size) {
			chunk.push(array[i]);
		}
	}

	if (chunk.length) {
		newArray.push(chunk);
	}

	return newArray;
};


/**
 * Get selection from a range object
 *
 * @param sel
 */
Orb.getSelectionCoords = function(sel) {
	var range, rect;
	if (sel.rangeCount) {
		range = sel.getRangeAt(0).cloneRange();
		try {
			if (range.getClientRects) {
				range.collapse(true);
				rect = range.getClientRects()[0];
				if (rect) {
					return null;
				}

				return {
					left: rect.left,
					top: rect.top
				};
			}
		} catch (e) {
			return null;
		}
	} else if (sel.type && sel.type != "Control" && sel.createRange) {
		range = sel.createRange();
		range.collapse(true);

		if (range.boundingLeft && range.boundingTop) {
			return {
				left: range.boundingLeft,
				top: range.boundingTop
			}
		}
	}

	return null;
};

(function() {
	var cleanupCallbacks = [];
	var origRemove = jQuery.fn.remove;
	var origEmpty = jQuery.fn.empty;

	jQuery.addElementCleanupCallback = function(fn) {
		cleanupCallbacks.push(fn);
	};

	jQuery.fn.empty = function() {
		var i;
		if (this.length) {
			for (i = 0; i < cleanupCallbacks.length; i++) {
				cleanupCallbacks[i](this, 'empty');
			}
		}

		return origEmpty.apply(this, arguments);
	};

	jQuery.fn.remove = function() {
		var i;
		if (this.length) {
			for (i = 0; i < cleanupCallbacks.length; i++) {
				cleanupCallbacks[i](this, 'remove');
			}
		}

		return origRemove.apply(this, arguments);
	};
})();


/**
 * Cancel an event. Stops bubbling and prevents default.
 * @param ev
 */
Orb.cancelEvent = function(ev) {
	ev.stopPropagation();
	ev.preventDefault();
};


Orb.shimClickCallback_shim  = null;
Orb.shimClickCallback_stack = [];
/**
 * This inserts a transparent shim at zIndex that is meant to capture click events.
 *
 * @param callback
 * @param zIndex
 */
Orb.shimClickCallback = function(callback, zIndex) {
	if (!Orb.shimClickCallback_shim) {
		Orb.shimClickCallback_shim = $('<div/>').hide();
		Orb.shimClickCallback_shim.css({
			position: 'absolute',
			top: 0,
			right: 0,
			left: 0,
			bottom: 0,
			background: 'transparent'
		});
		Orb.shimClickCallback_shim.appendTo('body');

		Orb.shimClickCallback_shim.on('click', function(ev) {
			Orb.cancelEvent(ev);
			Orb.shimClickCallbackPop(false, [ev]);
		});
	}

	Orb.shimClickCallback_stack.push([callback, zIndex]);

	if (Orb.shimClickCallback_shim.data('zindex-class')) {
		Orb.shimClickCallback_shim.removeClass(Orb.shimClickCallback_shim.data('zindex-class'));
	}
	Orb.shimClickCallback_shim.addClass(zIndex).data('zindex-class', zIndex);
	Orb.shimClickCallback_shim.show();
};

Orb.shimClickCallbackPop = function(no_callback, args) {
	var lvl = Orb.shimClickCallback_stack.pop();

	if (lvl && !no_callback) {
		lvl[0].call(args);
	}

	if (Orb.shimClickCallback_stack.length) {
		if (Orb.shimClickCallback_shim.data('zindex-class')) {
			Orb.shimClickCallback_shim.removeClass(Orb.shimClickCallback_shim.data('zindex-class'));
		}
		Orb.shimClickCallback_shim.addClass(Orb.shimClickCallback_stack[Orb.shimClickCallback_stack.length-1][1])
			.data('zindex-class', Orb.shimClickCallback_stack[Orb.shimClickCallback_stack.length-1][1]);
		Orb.shimClickCallback_shim.show();
	} else {
		Orb.shimClickCallback_shim.hide();
	}
};

/**
 * Simple way to load Javascript and CSS files on-demand.
 *
 * Usage:
 * <code>
 *  Orb.resourceLoader.loadScript('whatever.js');
 *  Orb.resourceLoader.loadBatch([{
 *  	type: 'script',
 *  	src: 'whatever.js'
 *  }, {
 *  	type: 'css',
 *  	src: 'whatever.css'
 *  }], function() { alert("All resources loaded"); });
 * </code>
 */
Orb.resourceLoader = {
	batches: {},
	batchesCallback: {},

	/**
	 * Load a new Javascript source
	 *
	 * @param {String} src The path or full URL to the source file
	 * @param {Function} callback The function to execute when the file has been loaded
	 */
	loadScript: function(src, callback) {
		this.loadBatch([{
			type: 'script',
			url: src
		}], callback);
	},



	/**
	 * Load a new CSS stylesheet
	 *
	 * @param {String} url The path or full URL to the CSS file
	 * @param {Function} callback The function to execute when the file has been loaded
	 */
	loadStylesheet: function(url, callback) {
		this.loadBatch([{
			type: 'css',
			url: url
		}], callback);
	},



	/**
	 * Load a number of resources all at once, and be notified when they've all finished
	 * loading.
	 *
	 * `resources` must be a hash of `type` being 'script' or 'stylesheet', and `url` being the
	 * path or full URL to the file.
	 *
	 * @param {Object} resources Descriptions of each resource
	 * @param {Function} callback The function to execute when all files have been loaded
	 */
	loadBatch: function(resources, callback) {

		var batchId = Orb.uuid();
		var head = $('head');

		this.batches[batchId] = [];
		this.batchesCallback[batchId] = callback;

		var res = null;
		while (res = resources.shift()) {
			var resourceId = Orb.uuid();

			var fn = function() {
				Orb.resourceLoader._resourceDoneLoading(batchId, resourceId);
			};

			if (res.type == 'script') {
				var tag = document.createElement('script');
				tag.type = "text/javascript";
				tag.src = res.url;
			} else if (res.type == 'stylesheet') {
				var tag = document.createElement('link');
				tag.rel = "stylesheet";
				tag.type = 'text/css';
				tag.href = res.url;
				tag.media = "screen";

				if (res.media != undefined) {
					tag.media = res.media;
				}
			}

			tag.onreadystatechange= function () {
				if (this.readyState == 'complete') fn();
			}
			tag.onload = fn;

			this.batches[batchId].push(resourceId);
		}

	},

	_resourceDoneLoading: function(batchId, resourceId) {
		if (this.batches[batchId] == undefined) {
			return false;
		}

		this.batches[batchId].erase(resourceId);

		if (!this.batches[batchId].length) {
			var callback = this.batchesCallback[batchId];

			delete this.batches[batchId];
			delete this.batchesCallback[batchId];

			callback();
		}
	}
};

/**
 * Simple wrapper around HTML5 notifications in Webkit
 */
Orb.DesktopNotify = (function() {
	if (window.webkitNotifications) {
		var supported = true;
	} else {
		var supported = false;
	}

	this.isSupported = function() {
		return supported;
	};

	if (supported && window.webkitNotifications.checkPermission() != 0) {
		hasPermission = true;
	}

	var hasPermission = this.hasPermission = function() {
		return (supported && window.webkitNotifications.checkPermission() != 0);
	};

	this.askPermission = function(callback) {
		if (!supported || hasPermission()) return;

		callback = callback || function() {};

		window.webkitNotifications.requestPermission(callback);
	};

	this.show = function(options) {
		if (!supported || !hasPermission()) return;

		options = $.extend({}, options, {
			iconUrl: null,
			title: '',
			content: '',
			click: null,
			show: null,
			close: null,
			error: null
		});

		var notif = window.webkitNotifications.createNotification(
			options.iconUrl,
			options.title,
			options.content
		);

		if (options.click) {
			notif.onclick = options.click;
		}
		if (options.show) {
			notif.onshow = options.show;
		}
		if (options.close) {
			notif.onclose = options.close;
		}
		if (options.error) {
			notif.onerror = options.error;
		}

		return notif;
	};

	this.showUrl = function(options) {

		if (typeof options == 'string') {
			options = {url: options};
		}

		var notif = window.webkitNotifications.createHTMLNotification(options.url);

		if (options.click) {
			notif.onclick = options.click;
		}
		if (options.show) {
			notif.onshow = options.show;
		}
		if (options.close) {
			notif.onclose = options.close;
		}
		if (options.error) {
			notif.onerror = options.error;
		}

		return notif;
	};

	return this;
})();


/**
 * There is no way to attach a single click handler and a double-click handler.
 * So to do it, we have to emulate double-click detection by setting a timeout.
 * If a second click happens before the timeout, then we can run the double-click callback.
 * If no second click happens and the timeout expires, then we can run the original.
 *
 * @param {Function} single_click_callback The single-click function
 * @param {Function} double_click_callback The double-click function
 * @param {Integer} timeout How long the user has to make a second-click (default 250)
 */
$.fn.single_double_click = function(single_click_callback, double_click_callback, timeout) {
	timeout = timeout || 250;
	return this.each(function() {
	    var clicks = 0;
		var self = this;

		// ie triggers dblclick instead of click if they are fast
	    if ($.browser.msie) {
	        $(this).bind("dblclick", function(event) {
	            clicks = 2;
	            double_click_callback.call(self, event);
	        });
	        $(this).bind("click", function(event) {
	            setTimeout(function() {
	                if (clicks != 2) {
	                    single_click_callback.call(self, event);
	                }
	                clicks = 0;
	            }, timeout);
	        });

	    } else {
	        $(this).bind("click", function(event) {
	            clicks++;
	            if (clicks == 1) {
	                setTimeout(function() {
	                    if (clicks == 1) {
	                        single_click_callback.call(self, event);
	                    } else {
	                        double_click_callback.call(self, event);
	                    }
	                    clicks = 0;
	                }, timeout);
	            }
	        });
	    }
	});
};


jQuery.fn.extend({
	insertAtCaret: function(myValue) {
		return this.each(function(i) {
			if (document.selection) {
				this.focus();
				sel = document.selection.createRange();
				sel.text = myValue;
				this.focus();
			}
			else if (this.selectionStart || this.selectionStart == '0') {
				var startPos = this.selectionStart;
				var endPos = this.selectionEnd;
				var scrollTop = this.scrollTop;
				this.value = this.value.substring(0, startPos)+myValue+this.value.substring(endPos,this.value.length);
				this.focus();
				this.selectionStart = startPos + myValue.length;
				this.selectionEnd = startPos + myValue.length;
				this.scrollTop = scrollTop;
			} else {
				this.value += myValue;
				this.focus();
			}
		});
	},

	setCaretPosition: function(caretPos) {
		return $.proxy(function() {
			if(this.createTextRange) {
				var range = this.createTextRange();
				range.move('character', caretPos);
				range.select();
			}
			else {
				if(this.selectionStart) {
					this.focus();
					this.setSelectionRange(caretPos, caretPos);
				}
				else {
					this.focus();
				}
			}
		}, this.get(0));
	},

	getCaretPosition: function() {
		return $.proxy(function() {
			if (document.selection && document.selection.createRange) {
				var range = document.selection.createRange();
				var bookmark = range.getBookmark();
				return bookmark.charCodeAt(2) - 2;
			} else if (this.setSelectionRange) {
				return this.selectionStart;
			} else {
				return 0;
			}
		}, this.get(0));
	}
});

/*
 * More info at: http://phpjs.org
 *
 * Licensed under MIT License http://phpjs.org/pages/license/#MIT
 */
function strtotime(str, now) {
    // http://kevin.vanzonneveld.net
    // +   original by: Caio Ariede (http://caioariede.com)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +      input by: David
    // +   improved by: Caio Ariede (http://caioariede.com)
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +   bugfixed by: Wagner B. Soares
    // +   bugfixed by: Artur Tchernychev
    // %        note 1: Examples all have a fixed timestamp to prevent tests to fail because of variable time(zones)
    // *     example 1: strtotime('+1 day', 1129633200);
    // *     returns 1: 1129719600
    // *     example 2: strtotime('+1 week 2 days 4 hours 2 seconds', 1129633200);
    // *     returns 2: 1130425202
    // *     example 3: strtotime('last month', 1129633200);
    // *     returns 3: 1127041200
    // *     example 4: strtotime('2009-05-04 08:30:00');
    // *     returns 4: 1241418600
    var i, match, s, strTmp = '',
        parse = '';

    strTmp = str;
    strTmp = strTmp.replace(/\s{2,}|^\s|\s$/g, ' '); // unecessary spaces
    strTmp = strTmp.replace(/[\t\r\n]/g, ''); // unecessary chars
    if (strTmp == 'now') {
        return (new Date()).getTime() / 1000; // Return seconds, not milli-seconds
    } else if (!isNaN(parse = Date.parse(strTmp))) {
        return (parse / 1000);
    } else if (now) {
        now = new Date(now * 1000); // Accept PHP-style seconds
    } else {
        now = new Date();
    }

    strTmp = strTmp.toLowerCase();

    var __is = {
        day: {
            'sun': 0,
            'mon': 1,
            'tue': 2,
            'wed': 3,
            'thu': 4,
            'fri': 5,
            'sat': 6
        },
        mon: {
            'jan': 0,
            'feb': 1,
            'mar': 2,
            'apr': 3,
            'may': 4,
            'jun': 5,
            'jul': 6,
            'aug': 7,
            'sep': 8,
            'oct': 9,
            'nov': 10,
            'dec': 11
        }
    };

    var process = function (m) {
        var ago = (m[2] && m[2] == 'ago');
        var num = (num = m[0] == 'last' ? -1 : 1) * (ago ? -1 : 1);

        switch (m[0]) {
        case 'last':
        case 'next':
            switch (m[1].substring(0, 3)) {
            case 'yea':
                now.setFullYear(now.getFullYear() + num);
                break;
            case 'mon':
                now.setMonth(now.getMonth() + num);
                break;
            case 'wee':
                now.setDate(now.getDate() + (num * 7));
                break;
            case 'day':
                now.setDate(now.getDate() + num);
                break;
            case 'hou':
                now.setHours(now.getHours() + num);
                break;
            case 'min':
                now.setMinutes(now.getMinutes() + num);
                break;
            case 'sec':
                now.setSeconds(now.getSeconds() + num);
                break;
            default:
                var day;
                if (typeof(day = __is.day[m[1].substring(0, 3)]) != 'undefined') {
                    var diff = day - now.getDay();
                    if (diff == 0) {
                        diff = 7 * num;
                    } else if (diff > 0) {
                        if (m[0] == 'last') {
                            diff -= 7;
                        }
                    } else {
                        if (m[0] == 'next') {
                            diff += 7;
                        }
                    }
                    now.setDate(now.getDate() + diff);
                }
            }
            break;

        default:
            if (/\d+/.test(m[0])) {
                num *= parseInt(m[0], 10);

                switch (m[1].substring(0, 3)) {
                case 'yea':
                    now.setFullYear(now.getFullYear() + num);
                    break;
                case 'mon':
                    now.setMonth(now.getMonth() + num);
                    break;
                case 'wee':
                    now.setDate(now.getDate() + (num * 7));
                    break;
                case 'day':
                    now.setDate(now.getDate() + num);
                    break;
                case 'hou':
                    now.setHours(now.getHours() + num);
                    break;
                case 'min':
                    now.setMinutes(now.getMinutes() + num);
                    break;
                case 'sec':
                    now.setSeconds(now.getSeconds() + num);
                    break;
                }
            } else {
                return false;
            }
            break;
        }
        return true;
    };

    match = strTmp.match(/^(\d{2,4}-\d{2}-\d{2})(?:\s(\d{1,2}:\d{2}(:\d{2})?)?(?:\.(\d+))?)?$/);
    if (match != null) {
        if (!match[2]) {
            match[2] = '00:00:00';
        } else if (!match[3]) {
            match[2] += ':00';
        }

        s = match[1].split(/-/g);

        for (i in __is.mon) {
            if (__is.mon[i] == s[1] - 1) {
                s[1] = i;
            }
        }
        s[0] = parseInt(s[0], 10);

        s[0] = (s[0] >= 0 && s[0] <= 69) ? '20' + (s[0] < 10 ? '0' + s[0] : s[0] + '') : (s[0] >= 70 && s[0] <= 99) ? '19' + s[0] : s[0] + '';
        return parseInt(this.strtotime(s[2] + ' ' + s[1] + ' ' + s[0] + ' ' + match[2]) + (match[4] ? match[4] / 1000 : ''), 10);
    }

    var regex = '([+-]?\\d+\\s' + '(years?|months?|weeks?|days?|hours?|min|minutes?|sec|seconds?' + '|sun\\.?|sunday|mon\\.?|monday|tue\\.?|tuesday|wed\\.?|wednesday' + '|thu\\.?|thursday|fri\\.?|friday|sat\\.?|saturday)' + '|(last|next)\\s' + '(years?|months?|weeks?|days?|hours?|min|minutes?|sec|seconds?' + '|sun\\.?|sunday|mon\\.?|monday|tue\\.?|tuesday|wed\\.?|wednesday' + '|thu\\.?|thursday|fri\\.?|friday|sat\\.?|saturday))' + '(\\sago)?';

    match = strTmp.match(new RegExp(regex, 'gi')); // Brett: seems should be case insensitive per docs, so added 'i'
    if (match == null) {
        return false;
    }

    for (i = 0; i < match.length; i++) {
        if (!process(match[i].split(' '))) {
            return false;
        }
    }

    return (now.getTime() / 1000);
}




/*! Copyright (c) 2008 Brandon Aaron (brandon.aaron@gmail.com || http://brandonaaron.net)
 * Dual licensed under the MIT (http://www.opensource.org/licenses/mit-license.php)
 * and GPL (http://www.opensource.org/licenses/gpl-license.php) licenses.
 */

/**
 * Gets the width of the OS scrollbar
 */
(function($) {
	var scrollbarWidth = 0;
	$.getScrollbarWidth = function() {
		if ( !scrollbarWidth ) {
			if ( $.browser.msie ) {
				var $textarea1 = $('<textarea cols="10" rows="2"></textarea>')
						.css({ position: 'absolute', top: -1000, left: -1000 }).appendTo('body'),
					$textarea2 = $('<textarea cols="10" rows="2" style="overflow: hidden;"></textarea>')
						.css({ position: 'absolute', top: -1000, left: -1000 }).appendTo('body');
				scrollbarWidth = $textarea1.width() - $textarea2.width();
				$textarea1.add($textarea2).remove();
			} else {
				var $div = $('<div />')
					.css({ width: 100, height: 100, overflow: 'auto', position: 'absolute', top: -1000, left: -1000 })
					.prependTo('body').append('<div />').find('div')
						.css({ width: '100%', height: 200 });
				scrollbarWidth = 100 - $div.width();
				$div.parent().remove();
			}
		}
		return scrollbarWidth;
	};
})(jQuery);


(function($){

    $.fn.autoGrowInput = function(o) {

        o = $.extend({
            maxWidth: 1000,
            minWidth: 0,
            comfortZone: 10
        }, o);

        this.filter('input:text').each(function(){

            var minWidth = o.minWidth || $(this).width(),
                val = '',
                input = $(this),
                testSubject = $('<tester/>').css({
                    position: 'absolute',
                    top: -9999,
                    left: -9999,
                    width: 'auto',
                    fontSize: input.css('fontSize'),
                    fontFamily: input.css('fontFamily'),
                    fontWeight: input.css('fontWeight'),
                    letterSpacing: input.css('letterSpacing'),
                    whiteSpace: 'nowrap'
                }),
                check = function() {

                    if (val === (val = input.val())) {return;}

                    // Enter new content into testSubject
                    var escaped = val.replace(/&/g, '&amp;').replace(/\s/g,'&nbsp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                    testSubject.html(escaped);

                    // Calculate new width + whether to change
                    var testerWidth = testSubject.width(),
                        newWidth = (testerWidth + o.comfortZone) >= minWidth ? testerWidth + o.comfortZone : minWidth,
                        currentWidth = input.width(),
                        isValidWidthChange = (newWidth < currentWidth && newWidth >= minWidth)
                                             || (newWidth > minWidth && newWidth < o.maxWidth);

                    // Animate width
                    if (isValidWidthChange) {
                        input.width(newWidth);
                    }

                };

            testSubject.insertAfter(input);

            $(this).bind('keyup keydown blur update', check);
			check();
        });

        return this;

    };

})(jQuery);

// Implement toISOString for browsers that dont already
if (!Date.prototype.toISOString) {
    Date.prototype.toISOString = function() {
        function pad(n) { return n < 10 ? '0' + n : n }
        return this.getUTCFullYear() + '-'
            + pad(this.getUTCMonth() + 1) + '-'
            + pad(this.getUTCDate()) + 'T'
            + pad(this.getUTCHours()) + ':'
            + pad(this.getUTCMinutes()) + ':'
            + pad(this.getUTCSeconds()) + 'Z';
    };
}
if (typeof DP_DEBUG == 'undefined' || !DP_DEBUG) DP_DEBUG = false;

var DP = {
	console: {
		error: function() {},
		log: function() {},
		warn: function() {},
		info: function() {},
		debug: function() {},
		trace: function() {}
	},

	init: function() {
		if (typeof window.console != 'undefined') {
			DP.console = window.console;
		}
		['error', 'log', 'warn', 'info', 'debug', 'trace'].each(function(v) {
			if (!DP.console[v]) {
				DP.console[v] = function() {};
			}
		});

		delete DP.init;
	},

	rteTextarea: function(field, options) {

		options = options || {};

		if (!field) {
			return;
		}

		field = $(field);

		if (!field.closest('body')) {
			return;
		}

		defaultOptions = {
			script_url: ASSETS_BASE_URL + '/vendor/tiny_mce/tiny_mce_src.js',

			skin : "o2k7",
			skin_variant : "silver",

			theme: 'advanced',
			plugins : "fullscreen,table,wordcount",
			theme_advanced_buttons1: 'bold,italic,underline,|,justifyleft,justifycenter,justifyright,|,forecolor,backcolor,|,styleselect,fontselect,fontsizeselect',
			theme_advanced_buttons2: 'bullist,numlist,|,outdent,indent,|,link,unlink,anchor,dp_media,image,|,hr,tablecontrols,|,pasteword,visualaid,code,removeformat,fullscreen',
			theme_advanced_buttons3: '',
			theme_advanced_toolbar_location: 'top',
			theme_advanced_toolbar_align: 'left',
			theme_advanced_resizing: true,
			theme_advanced_statusbar_location: 'bottom',
			theme_advanced_path: false,
			relative_urls: false,
			width: '100%',
			content_css: ASSETS_BASE_URL + '/stylesheets/user/content-editor.css',

			style_formats: [
				{ title: 'Paragraph', block: 'p' },
				{ title: 'Heading 1', block: 'h2' },
				{ title: 'Heading 2', block: 'h3' },
				{ title: 'Heading 3', block: 'h4' },
				{ title: 'Heading 4', block: 'h5' },
				{ title: 'Quote', block: 'blockquote' },
				{ title: 'Code Box', block: 'code', classes: 'codebox' }
			]
		};

		var oldsetup = options.setup || function() { };
		options.setup = function(ed) {
			ed.addButton('dp_media', {
				title : 'Upload Image',
				image : ASSETS_BASE_URL + '/images/agent/icons/picture_add.png',
				onclick : function() {
					MEDIA_MANAGER_WINDOW.bindToEditor(ed);
					MEDIA_MANAGER_WINDOW.open();
				}
			});

			oldsetup(ed);
		};

		options = Object.merge(defaultOptions, options || {});

		return field.tinymce(options);
	},

	convertTextToWysiwygHtml: function(text, pOneLine) {
		if (!text.length) {
			return '';
		}

		text = text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');

		if (pOneLine) {
			text = '<p>' + text.replace(/\r?\n/g, "</p>\n\n<p>") + '</p>';
		} else {
			text = '<p>' + text.replace(/\r?\n/g, '<br>\n').replace(/<br>\n<br>\n/g, "</p>\n\n<p>") + '</p>';
		}

		if (!$.browser.msie) {
			// IE renders the empty <p> tags
			text = text.replace(/<p><\/p>/g, '<p><br></p>');
		}

		return text;
	},

	drawBox: function(w, h) {
		if (this.lastBox) {
			this.lastBox.remove();
			this.lastBox = null;
		}
		this.lastBox = $('<div style="background-color: #263343; position: absolute; width: '+w+'px; height: '+h+'px; z-index: 99999999;"></div>');

		var left = ($(window).width() / 2) - (w/2);
		var top  = ($(window).height() / 2) - (h/2);

		this.lastBox.css({left: left, top: top}).appendTo('body').show();
		return this.lastBox;
	},

	removeDrawnBox: function() {
		if (this.lastBox) {
			this.lastBox.remove();
			this.lastBox = null;
		}
	},

	select: function(el, options) {
		if (el.data('select2')) {
			return;
		}

		if (el.length && el.length > 1) {
			el.each(function() {
				DP.select($(this), options);
			});
			return;
		}

		var options = options || {};

		if (el.data('style-type')) {
			switch (el.data('style-type')) {
				case 'icons':
					var iconSize = el.data('select-icon-size');
					if (!iconSize) iconSize = 16;
					iconSize = parseInt(iconSize);

					var addCss = '';
					var addCssLh = '';
					if (iconSize != 16) {
						addCss = 'padding-left: ' + (iconSize + 4) + 'px; line-height: ' + iconSize + 'px';
						addCssLh = 'line-height: ' + iconSize + 'px';
					}

					options.addWidth = iconSize + 35;

					el.addClass('dpe_select-with-icon dpe_select-with-icon-' + iconSize);

					options.addResultClass = 'with-icon';
					options.formatResult = function(result) {
						if (typeof result.id === 'undefined') {
							return Orb.escapeHtml(result.text);
						}

						var opt = el.find('option[value="' + result.id + '"]');
						var name = Orb.escapeHtml(opt.text());
						if (opt.data('icon')) {
							return '<div class="result-icon" style="background-image: url(' + opt.data('icon') + ');'+addCss+'">' + name + '</div>';
						} else {
							return '<div class="result-icon no-icon" style="'+addCssLh+'">' + name + '</div>';
						}
					};
					options.formatSelection = function(data) {
						var opt = el.find('option[value="' + data.id + '"]');
						if (!opt) {
							return '';
						}
						var name = Orb.escapeHtml(opt.text());
						if (opt.data('icon')) {
							return {type: 'html', value: '<span class="choice-icon" style="background-image: url(' + opt.data('icon') + '); padding-left: ' + (iconSize + 5) + 'px">' + name + '</span>' };
						} else {
							return name;
						}
					};
					break;

				case 'urgency':
					options.formatResult = function(data) {
						var name = Orb.escapeHtml(data.text);
						if (data.id <= 0) {
							return name;
						}

						return '<span class="urgency urgency-' + data.id + '"><i>' + name + '</i></span>';
					};

					options.formatSelection = function(data) {
						var name = Orb.escapeHtml(data.text);
						if (data.id <= 0) {
							return name;
						}

						return {type: 'html', value: '<span class="urgency urgency-' + data.id + '"><i>' + name + '</i></span>'};
					};
					break;
			}
		} else {
			var withFullTitle = el.find('option[data-full-title]');
			if (withFullTitle[0]) {
				withFullTitle.each(function() {
					$(this).data('single-title', $(this).text().trim());
					$(this).text($(this).data('full-title'));
				});

				options.formatSelection = function(data) {
					var n = $('<span/>').text(data.text);
					return {type: 'el', value: n};
				}
				options.formatResult = function(result) {
					var opt = el.find('option[value="' + result.id + '"]');
					if (!opt || !opt[0]) {
						return $('<span/>').text(result.text || '');
					}
					var name = opt.data('single-title') || result.text;
					name = $('<span/>').text(name);
					return name;
				};
			}
		}

		if (el.data('select-nogrouptitle')) {
			options.noGroupTitle = true;
		}

		if (el.data('select-width') == 'auto') {
			var shrink;
			if (el.data('select-width-shrink')) {
				shrink = parseInt(el.data('select-width-shrink'), 10);
			} else {
				shrink = 15;
			}

			if (el.parent().width()) {
				options.width = el.parent().width() - shrink + 'px';
			} else {
				// hidden farther up the change so walk up until we can see something
				// and then come back down
				var path = [];
				var shown = [];
				var testEl = el.parent();
				while (testEl && testEl.is(':hidden')) {
					path.push(testEl);
					testEl = testEl.parent();
				}

				if (testEl) {
					// this is visible
					for (var i = path.length - 1; i >= 0; i--) {
						if (path[i].is(':hidden')) {
							path[i].show();
							shown.push(path[i]);
						}
					}

					options.width = (el.parent().width() - shrink) + 'px';

					for (var i = 0; i < shown.length; i++) {
						shown[i].hide();
					}
				} // otherwise we didn't find something that wasn't hidden
			}
		} else if (el.data('select-width')) {
			options.width = el.data('select-width');
		} else {
			options.width = function() {
				var select_el = el;
				var largest = 0, label, charsize = 6, tmp;
				select_el.find('> *').each(function() {
					var el = $(this);
					if (el.is('optgroup')) {
						tmp = $.trim(el.attr('label')).length * charsize;
						if (tmp > largest) {
							largest = tmp;
						}

						el.find('option').each(function() {
							var s_el = $(this);
							tmp = ($.trim(s_el.text()).length * charsize) + 15; // +15 for optgroup indent
							if (tmp > largest) {
								largest = tmp;
							}
						});
					} else {
						tmp = ($.trim(el.text()).length * charsize);
						if (tmp > largest) {
							largest = tmp;
						}
					}
				});

				largest += 35;

				return largest + 'px';
			};
		}

		if (el.data('select-clear')) {
			options.allowClear = true;
		}

		if (el.data('placeholder')) {
			options.placeholder = el.data('placeholder');
		}

		if (el.data('autocomplete-url')) {
			options = $.extend(true, {
				ajax: {
					url: el.data('autocomplete-url'),
					dataType: 'json',
					quietMillis: 250,
					data: function(term, page) {
						return {
							q: term
						};
					},
					results: function(data, page) {
						if (data.results) {
								data = data.results;
							}
						var results = [];
						for (var i = 0; i < data.length; i++) {
							results.push({
								id: data[i].id,
								text: data[i].name
							});
						}

						return {
							more: false,
							results: results
						};
					}
				},
				initSelection: function(element, callback) {
					var data = [];
					if (element.val().length) {
						$.ajax({
							url: el.data('autocomplete-url'),
							method: 'get',
							data: {ids: element.val().split(',')},
							success: function(json) {
								if (json.results) {
									json = json.results;
								}
								var data = [];
								for (var i = 0; i < json.length; i++) {
									data.push({
										id: json[i].id,
										text: json[i].name
									});
								}
								callback(data);
							},
							failure: function() {
								callback([]);
							}
						});
					} else {
						callback([]);
					}

					callback(data);
				}
			}, options);

			if (el.data('multiple')) {
				options.multiple = true;
			}
		}

		el.find('option').each(function() {
			var opt = $(this);
			if (!$.trim(opt.text())) {
				opt.html('&nbsp;');
			}
		});

		if (!options.addWidth) {
			options.addWidth = 18;
		}

		if (el.is('[multiple]') || options.multiple) {
			options.addWidth = null;
			if (!options.width || options.width) {
				options.width = '95%';
			}
		}

		options.formatNoMatches = function() { return ''; }

		if (el.data('invisible-trigger')) {
			options.containerCssClass = (options.containerCssClass || '') + ' invisible-trigger';
			options.dropdownCssClass = (options.dropdownCssClass || '') + ' invisible-trigger';
		} else if (el.data('invisible-trigger-right')) {
			options.containerCssClass = (options.containerCssClass || '') + ' invisible-trigger right';
			options.dropdownCssClass = (options.dropdownCssClass || '') + ' invisible-trigger right';
		}

		if (el.data('dropdown-css-class')) {
			options.dropdownCssClass = (options.dropdownCssClass || '') + ' ' + el.data('dropdown-css-class');
		}

		if (el.data('dropdown-nosearch')) {
			options.dropdownCssClass = (options.dropdownCssClass || '') + ' dp-select2-nosearch';
		}

		if (el.data('label-bound')) {
			var boundEl = $(el.data('label-bound'));
			var updateBoundEl = function() {
				var text = [];
				el.find(':selected').each(function() {
					text.push($.trim($(this).text()));
				});
				boundEl.text(text.join(', '));
			};
			$(el).on('change', updateBoundEl);
			updateBoundEl();
		}

		el.addClass('with-select2');
		el.select2(options);
	}
};
DP.init();

if (!Orb) var Orb = {};

Orb.Class = function(properties) {

	//------------------------------
	// DisableParentCall: true
	//
	// These utils are used to detect when a
	// function should implement a parent call
	//------------------------------

	// If the special DisableParentCall directive is used,
	// we always disable it.
	if (properties.DisableParentCall) {
		function checkParentUse(obj) {
			return false;
		}

	// Otherwise, we can know if its neccessary by checking for it's
	// usage. Most browsers support turning a function into a string,
	// for those that dont we can just play it safe and assume parent
	// is used.
	} else {
		if (typeof ORB_CLASS_DO_PARENT_DETECT == 'undefined') {
			ORB_CLASS_DO_PARENT_DETECT = (function() {xyz}).toString().indexOf('xyz') != -1;
		}
		function checkParentUse(obj) {
			if (!ORB_CLASS_DO_PARENT_DETECT) {
				return true;
			}

			return obj.toString().indexOf('this.parent(') != -1;
		}
	}

	delete properties.DisableParentCall;

	//------------------------------
	// Extends: SomeClass
	//
	// Copies everything from this class
	// into this new one we're making
	//------------------------------

	if (!properties.Extends) {
		properties.Extends = function() {};
	}

	var parent_class = properties.Extends;
	var parent_proto = parent_class.prototype;
	parent_class.__is_prototyping = true;
	var proto = new parent_class;
	delete parent_class.__is_prototyping;

	delete properties.Extends;


	//------------------------------
	// Implements: SomeMixin
	//
	// Copies all properties from the mix-in into
	// this new class we're making
	//------------------------------

	if (properties.Implements) {
		for (var i = 0, n = properties.Implements.length; i != n; ++i) {
			var mixin = properties.Implements[i];
			for (var name in mixin) {
				if (!mixin.prototype || mixin.prototype.hasOwnProperty(name)) {
					if (typeof mixin[name] == 'function') {
						proto[name] = mixin[name];
					}
				}
			}
		}
	}

	delete properties.Implements;


	//------------------------------
	// ClassVars: {}
	//
	// Copies all of these properties
	// over to the class object
	//------------------------------

	var static_props = null;
	if (properties.ClassVars) {
		static_props = properties.ClassVars;
		delete properties.ClassVars;
	}


	//------------------------------
	// Actually copies this classes properties
	// and methods now
	//------------------------------

	if (properties.destroy) {
		properties.__destroy = properties.destroy;
		properties.destroy = (function(old) {
			return function() {
				if (!this.OBJ_DESTROYED) {
					old.apply(this);
				}
				this.OBJ_DESTROYED = true;
			};
		})(properties.__destroy);
	} else {
		properties.destroy = (function() {
			return function() {
				this.OBJ_DESTROYED = true;
			};
		})();
	}

	for (var name in properties) {
		if (properties.prototype && !properties.prototype.hasOwnProperty(name)) {
			continue;
		}

		var value = properties[name];

		if (typeof value == 'function') {
			if (name != 'destroy' && checkParentUse(value)) {
				value = (function(func, name) {
					return function() {
						this.parent = parent_proto[name];
						return func.apply(this, arguments);
					};
				})(value, name);
			}
			proto[name] = value;
		} else {
			console.error("[Orb.Class] Non-function property in class: %o extends %o", this, properties);
			throw "Error: Non-function property in class";
			return;
		}
	}

	var newClass;
	newClass = function() {
		if (newClass.__is_prototyping) {
			return this;
		}

		// Easy reference to the class object
		// Ex to use the set ClassVars easier
		this.CLASS  = newClass;
		this.SUPER  = parent_class;
		this.OBJ_ID = Orb.uuid();
		this.OBJ_DESTROYED = false;

		if (this.initialize) {
			this.initialize.apply(this, arguments);
		}

		return this;
	}

	if (static_props) {
		for (name in static_props) {
			if (!static_props.prototype || static_props.prototype.hasOwnProperty(name)) {
				newClass[name] = static_props[name];
			}
		}
	}

	newClass.prototype = proto;
	newClass.constructor = newClass;

	delete properties;
	delete static_props;
	delete name;
	delete value;
	delete checkParentUse;

	return newClass;
};

Orb.createNamespace('Orb.Util');

Orb.Util.Options = {
	setOptions: function(setOptions){

		var options = $.extend(true, {}, this.options || {}, setOptions);

		if (this.addEvent) {
			for (var option in options){
				if (option == 'defaultEventContext') {
					this.setDefaultEventContext(options[option]);
					delete options[option];
				} else if (typeof options[option] == 'function' && (/^on[A-Z]/).test(option)) {
					this.addEvent(option, options[option]);
					delete options[option];
				}
			}
		}

		this.options = options;

		return this;
	},

	getOption: function(option, default_value) {
		if (typeof this.options[option] === undefined) {
			return default_value;
		}

		return this.options[option];
	}
};
Orb.createNamespace('Orb.Util');

Orb.Util.Events = {

	__initEventsObj: function() {
		if (!this.__events) {
			this.__events = {};
			this.__events_tagged = {};
			this.__preventCleanupTagged = false;
		}
	},

	setDefaultEventContext: function(context) {
		this.__events_default_context = context;
	},

	normalizeEventName: function(type) {
		return type.toLowerCase().replace(/^on/, '');
	},

	addEvent: function(type, fn, context, tags, beginning){

		this.__initEventsObj();

		type = this.normalizeEventName(type);
		if (!context) {
			context = this.__events_default_context;
		}

		if (!this.__events[type]) {
			this.__events[type] = [];
		}

		if (beginning && this.__events[type].length) {
			var newVal = [];
			newVal.push([fn, context]);
			for (var i = 0; i < this.__events[type].length; i++) {
				newVal.push(this.__events[type][i]);
			}

			this.__events[type] = newVal;
		} else {
			this.__events[type].push([fn, context]);
		}

		if (context && context.OBJ_ID) {
			tags = (tags || []).push(context.OBJ_ID);
		} else if (fn.OBJ_ID) {
			tags = (tags || []).push(fn.OBJ_ID);
		}

		if (tags) {
			Array.each(tags, function(tag) {
				if (!this.__events_tagged[tag]) {
					this.__events_tagged[tag] = [];
				}

				this.__events_tagged[tag].push([type, fn, context]);
			}, this);
		}

		return this;
	},

	addEvents: function(events, context, tags){
		for (var type in events) {
			this.addEvent(type, events[type], context, tags);
		}
		return this;
	},

	fireEvent: function(type, args, delay){
		this.__initEventsObj();

		type = this.normalizeEventName(type);
		if (!this.__events[type]) {
			return this;
		}

		var defaultContext = this.__events_default_context || this;

		args = Array.from(args);
		Object.each(this.__events[type], function(fn_info){
			if (delay) {
				fn_info[0].delay(delay, fn_info[1] || defaultContext, args);
			} else {
				fn_info[0].apply(fn_info[1] || defaultContext, args);
			}
		});

		return this;
	},

	removeEvent: function(type, fn, context){
		var newFns = [], hasChange = false;

		this.__initEventsObj();

		type = this.normalizeEventName(type);
		if (!this.__events[type]) {
			return this;
		}

		if (!context) {
			context = null;
		}

		Array.each(this.__events[type], function(fn_info){
			if (fn_info[0] == fn && fn_info[1] == context) {
				hasChange = true;
			} else {
				newFns.push(fn_info);
			}
		});

		if (hasChange) {
			this.__events[type] = newFns;

			if (!this.__preventCleanupTagged) {
				this.__cleanupTaggedEvents();
			}
		}

		return this;
	},

	removeEvents: function(events, context){
		var type;

		this.__initEventsObj();

		this.__preventCleanupTagged = true;

		for (type in this.__events){
			if (events && events != type) continue;
			var fns = this.__events[type];
			for (var i = fns.length; i--;) if (i in fns){
				this.removeEvent(type, fns[i], context);
			}
		}

		this.__preventCleanupTagged = false;
		this.__cleanupTaggedEvents();
		return this;
	},

	removeTaggedEvents: function(tag) {
		if (!this.__events_tagged[tag]) return;

		this.__preventCleanupTagged = true;

		Array.each(this.__events_tagged[tag], function (x) {
			this.removeEvent(x[0], x[1], x[2]);
		}, this);

		this.__preventCleanupTagged = false;
		this.__cleanupTaggedEvents();
	},

	__cleanupTaggedEvents: function() {
		return;
		Object.each(this.__events_tagged, function(tag_fns, tag) {
			var newTaggedFns = [], hasChange = false;
			Array.each(tag_fns, function(tag_fn) {
				if (fn != tag_fn) {
					newTaggedFns.push(fn);
				} else {
					hasChange = true;
				}
			});

			if (hasChange) {
				if (newTaggedFns.length) {
					this.__events_tagged[tag] = newTaggedFns;
				} else {
					delete this.__events_tagged[tag];
				}
			} else {
				newTaggedFns = null;
			}
		}, this);
	}
};

Orb.createNamespace('Orb.Util');

Orb.Util.TimeAgo = {

	_watchTimer: null,

	/**
	 * How often to update the elements
	 */
	refreshPeriod: 60000,//1min

	phrases: {
		'sec_less': '1 second',
		'sec':      '1 second',
		'secs':     '{0} seconds',
		'min':      '1 minute',
		'mins':     '{0} minutes',
		'hour':     '1 hour',
		'hours':    '{0} hours',
		'day':      '1 day',
		'days':     '{0} days',
		'week':     '1 week',
		'weeks':    '{0} weeks',
		'month':    '1 month',
		'months':   '{0} months',
		'year':     '1 year',
		'years':    '{0} years',
		'ago':      'ago'
	},


	/**
	 * Get the full words for a given date.
	 *
	 * @param date
	 */
	get: function(date, ago, relativeCutoff, title) {
		return this.getForMs(this.getDateDiff(date), ago, relativeCutoff, title);
	},


	/**
	 * Apply to an array of elements.
	 *
	 * @param $els
	 */
	applyToElements: function(els) {

		var self = this;

		els.each(function(el) {
			$(el).addClass('timeago-auto-update');
			self.refreshElements([el]);
		});

		if (this._watchTimer === null) {
			this._watchTimer = window.setInterval(this.refreshElements.bind(this), this.refreshPeriod);
		}
	},


	/**
	 * APply to a jQuery collection
	 *
	 * @param $els
	 */
	applyToJquery: function($els) {
		this.applyToElements($els.toArray());
	},


	refreshElements: function(els) {
		if (!els) els = $('.timeago-auto-update').toArray();

		var self = this;

		els.each(function(el) {

			// Could be removed, just skip it
			// might be reinserted later
			if (!el || !el.parentNode) {
				return;
			}

			el = $(el);
			el.addClass('with-timeago');

			if (!el.data("timeago")) {

				var isTime = el.get(0).tagName.toLowerCase() == 'time';
				var iso8601 = isTime && el.attr('datetime') ? el.attr('datetime') : el.attr('title');

				if (!iso8601 || typeof iso8601 != 'string') {
					return;
				}

				var s = iso8601.replace(/\.\d\d\d+/,""); // remove milliseconds
				s = s.replace(/-/,"/").replace(/-/,"/");
				s = s.replace(/T/," ").replace(/Z/," UTC");
				s = s.replace(/([\+-]\d\d)\:?(\d\d)/," $1$2"); // -04:00 -> -0400

				var titleText = $.trim(el.text());
				if (titleText.length > 0) el.attr("title", titleText);

				el.data("timeago", { datetime: new Date(s), relativeCutoff: el.data('relative-cutoff') || 0, title: titleText });
			}

			var data = el.data('timeago');
			if (!isNaN(data.datetime)) {
				var ago = true;
				if (el.data('timeago-no-ago') == "1") {
					ago = false;
				} else {
					if (data.datetime > (new Date())) {
						ago = false;
					}
				}
                var text = self.get(data.datetime, ago, data.relativeCutoff, data.title);
				el.text(text);
			}
		});
	},


	/**
	 * Get the relative date info for ms.
	 *
	 * @param int ms
	 */
	getRelativeInfo: function(ms) {
		var secs = 0, mins = 0, hours = 0, days = 0, years = 0;

		secs = parseInt(ms / 1000);

		years = parseInt(secs / 29030400);
		secs -= years * 29030400;

		days = parseInt(secs / 86400);
		secs -= days * 86400;

		hours = parseInt(secs / 3600);
		secs -= hours * 3600;

		mins = parseInt(secs / 60);
		secs -= mins * 60;

		return {
			'secs':    secs,
			'mins':    mins,
			'hours':   hours,
			'days':    days,
			'years':   years
		}
	},


	/**
	 * Get the full words for given ms.
	 *
	 * @param ms
	 */
	getForMs: function(ms, ago, relativeCutoff, title) {
		var info = this.getRelativeInfo(ms);
		var total_secs = parseInt(ms / 1000);

		if (title && relativeCutoff && total_secs > relativeCutoff) {
			return title;
		}

		// less than 60 secons: 20 seconds
		if (total_secs < 60) {
			return this.getPhraseFor('sec', total_secs, ago);

		// less than 120 minutes: 20 minutes
		} else if (total_secs <= 7200) {
			return this.getPhraseFor('min', info.mins + (info.hours * 60), ago);

		// less than 24 hours: 2 1/2 hours
		} else if (total_secs <= 86400) {
			var fraction;
			if (info.mins <= 15) {
				fraction = '';
			} else if (info.mins <= 30) {
				fraction = '¼';
			} else if (info.mins <= 45) {
				fraction = '½';
			} else if (info.mins <= 60) {
				fraction = '¾';
			}

			var phrase_num = info.hours;
			var phrase_hours = info.hours + '';

			// Inc to override plural for ex 1 1/2 hours
			if (fraction !== '') {
				phrase_num += 1;
				phrase_hours = (info.hours || 1) + '' + fraction;
			}

			return this.getPhraseFor('hour', phrase_hours, ago, phrase_num);

		// less than 3 days: 2 days 2 hours
		} else if (total_secs <= 259200) {
			var phrase_days = this.getPhraseFor('day', info.days, ago);
			if (info.hours > 0) {
				phrase_days += ' ' + this.getPhraseFor('hour', info.hours, ago);
			}

			return phrase_days;

		// less than 1 month: 5 days
		} else if (total_secs <= 2419200) {
			return this.getPhraseFor('day', info.days, ago);

		// less than 3 months: 5 weeks
		} else if (total_secs <= 7257600) {
			var weeks = parseInt(info.days / 7);
			return this.getPhraseFor('week', weeks, ago);

		// less than a year: 8 months
		} else if (total_secs <= 29030400) {
			var months = parseInt(info.days / 30);
			return this.getPhraseFor('month', months, ago);

		// less than 5 years: 1 year 3 months
		} else if (total_secs <= 145152000) {
			var phrase_years = this.getPhraseFor('year', info.years, ago);
			if (info.months > 0) {
				phrase_years += ' ' + this.getPhraseFor('month', info.months, ago);
			}

			return phrase_years;

		// more than 5 years: 8 years
		} else {
			return this.getPhraseFor('year', info.years, ago);
		}
	},


	/**
	 * Get the difference in ms or s for a date and current date.
	 *
	 * @param date
	 * @param bool secs True to return seconds instead of ms
	 */
	getDateDiff: function(date, secs) {
		var now_ts  = (new Date()).getTime();
		var date_ts = date.getTime();
		var diff;

		if (now_ts < date_ts) {
			diff = date_ts - now_ts;
		} else {
			diff = now_ts - date_ts;
		}

		if (diff < 0) {
			return 0;
		}

		if (secs) {
			diff /= 1000;
		}
		return diff;
	},

	/**
	 * Get the phrase for a time denomination
	 *
	 * @param string type
	 * @param int num
	 */
	getPhraseFor: function(type, num, ago, num_exact) {

		if (!num_exact) {
			num_exact = num;
		}

		if (window.Orb_Util_TimeAgo_getPhraseFor) {
			if (num < 0) {
				num = 0;
			}

			return window.Orb_Util_TimeAgo_getPhraseFor(type, num, ago);
		}

		if (type == 'sec' && num_exact <= 0) {
			return this.phrases['sec_less'];
		}

		var k = type;
		if (num_exact != 1) {
			k += 's';
		}

		var phrase = this.phrases[k];

		if (ago) {
			phrase += ' ' + this.phrases['ago'];
		}

		phrase = phrase.replace(/\{0\}/g, num);

		return phrase;
	}
};

if (jQuery) {
	jQuery.fn.timeago = function() {
		Orb.Util.TimeAgo.applyToJquery(this);
		return this;
	};
}

Orb.createNamespace('Orb.Compat.WebForms');

Orb.Compat.WebForms.isPlaceholderSupported = function() {
	this.isSupported = null;

	if (this.isSupported === null) {
		this.isSupported = ('placeholder' in document.createElement('input'));
	}

	return this.isSupported;
};

Orb.Compat.WebForms.placeholder = function(input) {

	if (!input) return null;

	input_col = $(input);
	if (!input_col.length) return null;

	input_col.each(function() {

		var input = $(this);

		// Check if its already supported
		if (input.placeholder && this.isPlaceholderSupported()) return;

		// Get the palceholder and check that its actually a value
		var placeholder = input.attr('placeholder');
		if (!placeholder || !placeholder.length) return;

		// Make sure we havent already run the placeholder compat
		if (input.is('.has-placeholder')) return;

		input.addClass('has-placeholder');

		// See if we should enable the placeholder now
		if (input.val() === '' || input.val() == placeholder) {
			input.val(placeholder);
			input.addClass('placeholder-visible');
		}

		input.focus(function() {
			if (input.is('.placeholder-visible')) {
				input.val('');
				input.removeClass('placeholder-visible');
			}
		});

		input.blur(function() {
			if (input.val() === '') {
				input.addClass('placeholder-visible');
				input.val(placeholder);
			} else {
				input.removeClass('placeholder-visible');
			}
		});

		if (input.get(0) && input.get(0).form) {
			$(input.get(0).form).submit(function() {
				if (input.is('.placeholder-visible')) {
					input.val('');
				}
			});
		}
	});
};
Orb.createNamespace('DeskPRO');

DeskPRO.IntervalCaller = new Orb.Class({

	Implements: [Orb.Util.Options],

	initialize: function(options) {
		this.options = {
			/**
			 * Does a touch reset the timer?
			 */
			touchResets: true,

			/**
			 * Is a touch required to run the callback?
			 * If we're not touched when the timer runs out, then nothing will happen.
			 */
			touchRequired: true,

			/**
			 * If touchResets is true, whats the max time to pass before
			 * we call the callback anyway?
			 */
			resetTimeForce: 0,

			/**
			 * The callback to call
			 */
			callback: function() {},

			/**
			 * The object context in which to call the callback
			 */
			context: null,

			/**
			 * The timer timeout
			 */
			timeout: null,

			/**
			 * Autostart the timer as soon as this class is instantiated
			 */
			autostart: true
		};

		this.setOptions(options);

		if (this.options.autostart) {
			this.start();
		}

		this.touched = false;
		this.paused = false;
		this.lastTime = new Date();
	},

	start: function() {
		if (this.timer) {
			window.clearTimeout(this.timer);
			this.timer = null;
		}

		this.timer = window.setTimeout(this.exec.bind(this), this.options.timeout);
	},

	stop: function() {
		if (this.timer) {
			window.clearTimeout(this.timer);
			this.timer = null;
		}
	},

	touch: function() {
		this.touched = true;
		if (this.options.touchResets) {
			if (this.options.resetTimeForce) {
				var now = new Date();
				var diff = now.getTime() - this.lastTime.getTime();
				if (diff > this.options.resetTimeForce) {
					return;
				}
			}

			// Restart if its not over max time
			this.start();
		}
	},

	exec: function(force) {

		if (!force && this.options.touchRequired && !this.touched) {
			this.start();
			return;
		}

		this.lastTime = new Date();
		this.touched = false;

		if (this.options.context) {
			this.options.callback.apply(this.options.context);
		} else {
			this.options.callback();
		}

		this.start();
	},

	execNow: function() {
		this.exec();
	},

	destroy: function() {
		this.stop();
		this.options = null;
		this.lastTime = null;
	}
});

Orb.createNamespace('DeskPRO');

/**
 * A central way to handle and pass messages around to listeners. Glorified
 * event manager.
 *
 * <code>
 * var messageBroker = new DeskPRO.MessageBroker();
 * messageBroker.addMessageListener('example.message.*', function (data) { alert(data); });
 * messageBroker.sendMessage('example.message.test', "Hello, world!");
 * </code>
 */
DeskPRO.MessageBroker = new Orb.Class({

	Implements: [Orb.Util.Events],

	/**
	 * Send a message to all listeners.
	 *
	 * @param {String} name The message name
	 * @param {Object} data Any data to send
	 */
	sendMessage: function (name, data) {
		this.fireEvent(name, [data, name]);

		var nameparts = name.split('.');
		var cur_name = null;

		while (nameparts.pop()) {
			cur_name = nameparts.join('.') + '.*';
			this.fireEvent(cur_name, [data, cur_name]);
		}
	},

	/**
	 * Add a listener on a message.
	 *
	 * `name` should use dots to separate namespaces/groups of message types.
	 * Use an asterisk at the end of a namespace and all messages of that namespace
	 * will be sent through the same callback: example.*
	 *
	 * @param {String} name Message name
	 * @param {Function} callback Callback to execute with message
	 */
	addMessageListener: function(name, callback, context, tags) {
		this.addEvent(name, callback, context, tags);
	},



	/**
	 * Remove a listener
	 *
	 * @param {String} name Message name
	 * @param {Function} callback Callback to remove
	 */
	removeMessageListener: function (name, callback, context) {
		this.removeEvent(name, callback, context);
	},



	/**
	 * Remove all listeners tagged with a certain tag.
	 *
	 * @param tag
	 */
	removeTaggedListeners: function(tag) {
		this.removeTaggedEvents(tag);
	}
});

Orb.createNamespace('DeskPRO');

/**
 * Global window object controls some central things
 */
DeskPRO.BasicWindow = new Orb.Class({

	Implements: [Orb.Util.Options],

	initialize: function(options) {

		$('html').addClass('window-active');
		window.setTimeout(function() { $('html').addClass('window-active'); }, 1000); // because modernizer resets the className attr

		var _winshow = function() { $(document).trigger('windowshow'); $('html').addClass('window-active'); };
		var _winhide = function() { $(document).trigger('windowhide'); $('html').removeClass('window-active'); };
		if (/*@cc_on!@*/false) {
			document.onfocusin  = _winshow;
			document.onfocusout = _winhide;
		} else {
			window.onfocus  = _winshow;
			window.onblur  = _winhide;
		}

		// html5
		window.onpageshow = _winshow();
		window.onpagehide = _winhide();

		this.DEBUG = {};
		this.options = this.getDefaultOptions();
		this.registry = {};

		this.messageBroker = new DeskPRO.MessageBroker();
		this.translate = new DeskPRO.Translate();

		if (options) {
			this.setOptions(options);
		}

		this.init();
	},

	/**
	 * Empty hook method for children to implement init code
	 */
	init: function() {

	},

	/**
	 * This method is called ondomready usually, it should init interface elements
	 */
	initPage: function() {

	},

	/**
	 * Default options values for this window
	 */
	getDefaultOptions: function() {
		return {};
	},

	//#################################################################
	//# Global registry, getters
	//#################################################################

	/**
	 * Return the translate
	 */
	getTranslate: function() {
		return this.translate;
	},

	/**
	 * Get a value from the registry.
	 *
	 * @param {String} id The ID of the item
	 * @return mixed
	 */
	get: function(id, def) {
		if (this.registry[id] === undefined) {
			return def;
		}

		return this.registry[id];
	},


	/**
	 * Add or reset a value in the registry.
	 *
	 * @param {String} id The ID of the item
	 * @param mixed value The value of the item
	 */
	set: function(id, value) {
		this.registry[id] = value;
	},


	/**
	 * Get the message broker
	 */
	getMessageBroker: function() {
		return this.messageBroker;
	},


	/**
	 * Get a debug option
	 *
	 * @param name
	 * @return mixed
	 */
	getDebug: function(name) {
		if (this.DEBUG[name] === undefined) {
			return false;
		}

		return this.DEBUG[name];
	}
});

Orb.createNamespace('DeskPRO.UI');

/**
 * This is a simple tabbing system where the tab triggers and tab contents can be completely
 * separate, and are linked by attribuets in the source. This tab system simply
 * toggles an 'on' CSS class on elements, so it's up to you to style the elements. For example,
 * content elements without 'on' sholud be display:none etc.
 *
 * There are two elements: tab triggers and tab content. Tab triggers are attached a click event
 * that stitches the 'on' state of all the tab contents. So the tab clicked becoems "on" (and your
 * CSS makes it visible, and the rest invisible).
 *
 * <code>
 *     <li data-tab-for=".some-tab">Some Tab</li>
 *     ...
 *     <div class="some-tab"></div>
 * </code>
 */
DeskPRO.UI.SimpleTabs = new Orb.Class({
	Implements: [Orb.Util.Options, Orb.Util.Events],

	initialize: function(options) {

		// Initial values
		this.options = {
			/**
			 * Elements that will act as tab triggers. Tabs
			 * must have a data-tab-for attribute with a jQuery selector
			 * to define which tab they activate.
			 *
			 * You can add new triggers dynamically with addTriggerElement() later
			 */
			triggerElements: '.tab-trigger',

			/**
			 * This classname is added to tabs and tab content wrappers when they're activated
			 */
			activeClassname: 'on',

			/**
			 * The context for tab contents when executing data-tab-for
			 */
			context: document,

			autoSelectFirst: true,

			/**
			 * The effect to for showing/hiding the tab: slide, fade
			 */
			effect: null
		};

		this.lastActiveTab = null;
		this.triggerEls = null;

		if (options) this.setOptions(options);

		this.triggerEls = this.options.triggerElements;

		if (typeOf(this.triggerEls) == 'string') {
			this.triggerEls = $(this.triggerEls, this.options.context);
		}

		var self = this;
		this.triggerEls.on('click', function(ev) {
			ev.cancel = false;
			ev.tabEl = $(this);

			self.fireEvent('tabClick', [ev]);

			if (!ev.cancel) {
				self._handleTabClick(this, ev);
			}

			self.fireEvent('postTabClick', [ev]);
		});

		if (this.options.autoSelectFirst) {
			var firstTab = this.triggerEls.filter('.' + this.options.activeClassname).filter(':visible').first();
			if (!firstTab.length) {
				firstTab = this.triggerEls.filter(':visible').first();
				if (!firstTab.length) {
					firstTab = this.triggerEls.first();
				}
			}

			// Check again, there might not be any tabs
			if (firstTab.length) {
				// need to hide all others
				var self = this;
				this.triggerEls.each(function() {
					self.getContentElFromTab($(this)).hide();
				});

				this.activateTab(firstTab);
			}
		}
	},

	addTriggerElement: function(el) {
		var self = this;

		this.triggerEls.add(el);
		el.on('click', function(ev) {
			ev.cancel = false;
			ev.tabEl = $(this);

			self.fireEvent('tabClick', [ev]);

			if (!ev.cancel) {
				self._handleTabClick(this, ev);
			}
		});
	},

	_handleTabClick: function(el, event) {
		var tab = $(el);
		this.activateTab(tab, event);
	},

	activateTab: function(tabEl, event) {

		if (!tabEl) {
			return;
		}

		tabEl = $(tabEl);

		if (this.lastActiveTab && this.lastActiveTab.get(0) == tabEl.get(0)) {
			return;
		}

		var eventData = {
			event: event || null,
			tabEl: tabEl,
			lastTabEl: this.lastActiveTab,
			tabContent: this.getContentElFromTab(tabEl),
			manager: this,
			cancel: false
		};

		this.fireEvent('beforeTabSwitch', eventData);

		if (eventData.cancel) {
			return;
		}

		delete eventData['cancel'];

		var showFn = (function() {
			this.lastActiveTab = tabEl;
			this.lastActiveTab.addClass(this.options.activeClassname);
			var x = eventData.tabContent.addClass(this.options.activeClassname);

			this.lastActiveTabContent = eventData.tabContent;

			var parentContainer = eventData.tabContent.closest('.tabViewDetailContent, .with-page-fragment').first();
			if (parentContainer) {
				if (parentContainer.data('page-fragment')) {
					parentContainer.data('page-fragment').updateUi();
				} else {
					parentContainer.find('.with-scroll-handler').each(function() {
						if ($(this).data('scroll_handler')) {
							$(this).data('scroll_handler').updateSize();
						}
					});
				}
			}

			if (this.lastActiveTab && this.lastActiveTab.data('tab-on-show')) {
				this.lastActiveTab.data('tab-on-show')(eventData);
			}
			if (this.lastActiveTabContent && this.lastActiveTabContent.data('tab-on-show')) {
				this.lastActiveTabContent.data('tab-on-show')(eventData);
			}

			this.fireEvent('tabSwitch', eventData);

			if (this.lastActiveTabContent.data('load-url') && !this.lastActiveTabContent.data('tab-loaded')) {
				this._triggerTabAjaxLoad(this.lastActiveTab, this.lastActiveTabContent, eventData);
			}

			if (this.options.effect == 'slide') {
				x.slideDown('fast');
			} else if (this.options.effect == 'fade') {
				x.fadeIn('fast');
			} else {
				x.show();
			}

			x.trigger('dp_simpletabs_show');
		}).bind(this);

		if (this.lastActiveTab) {
			this.lastActiveTab.removeClass(this.options.activeClassname);
			var x = this.getContentElFromTab(this.lastActiveTab).removeClass(this.options.activeClassname);

			this.lastActiveTab = null;

			if (this.lastActiveTab && this.lastActiveTab.data('tab-on-hide')) {
				this.lastActiveTab.data('tab-on-hide')(eventData);
			}
			if (this.lastActiveTabContent && this.lastActiveTabContent.data('tab-on-hide')) {
				this.lastActiveTabContent.data('tab-on-hide')(eventData);
			}

			if (this.options.effect == 'slide') {
				x.slideUp('fast', showFn);
			} else if (this.options.effect == 'fade') {
				x.fadeOut('fast', showFn);
			} else {
				x.hide();
				showFn();
			}

			x.trigger('dp_simpletabs_hide');
		} else {
			showFn();
		}
	},

	_triggerTabAjaxLoad: function(tabEl, contentEl, eventData) {
		var self = this;

		contentEl.data('tab-loaded', true);

		delete eventData['cancel'];

		this.fireEvent('beforeTabLoad', eventData);
		if (eventData.cancel) {
			return;
		}

		$.ajax({
			url: contentEl.data('load-url'),
			method: 'get',
			dataType: 'html',
			success: function(html) {
				delete eventData['cancel'];
				self.fireEvent('beforeTabLoaded', eventData);
				if (eventData.cancel) {
					return;
				}

				contentEl.html(html);
				eventData.tabContent = self.getContentElFromTab(tabEl);

				self.fireEvent('tabLoaded', eventData);
			}
		})
	},

	getActiveTab: function() {
		return this.lastActiveTab;
	},

	getActiveTabContent: function() {
		return this.getContentElFromTab(this.getActiveTab());
	},

	getContentElFromTab: function(tabEl) {
		if (!tabEl || !tabEl.data || !tabEl.data('tab-for')) {
			DP.console.error('tab has no tab-for: %o', tabEl);
			if (console && console.trace) console.trace();
			return $();
		}

		if (tabEl.data('tab-for') == 'NOOP') {
			return $();
		}

		var el = $(tabEl.data('tab-for'), this.options.context);

		if (el.length < 1) {
			DP.console.error('no tab content exists for tab: %o', tabEl);
			console.trace();
		}

		return el;
	},

	destroy: function() {

	}
});

(function($) {
$.fn.dpTwoSelect = function(options) {
	$(this).each(function() {
		dpTwoSelectInit($(this), options);
	});
	return this;
}

function dpTwoSelectInit(el, options) {
	var structure = [],
		has_optgroup = false,
		uid = 0,
		blankOption,
		debugMessge,
		setValue,
		findValue,
		placeholder,
		parentElWrapper,
		parentEl,
		childElWrapper,
		childEl;

	options = $.extend({
		debug: Dp_EnableDebug || false,
		renderPlaceholders: function() {
			var wrap = $('<div class="dp-two-select" />');
			wrap.append('<div class="dp-parent"><select /></div>')
			wrap.append('<div class="dp-child"><select /></div>')

			return wrap;
		}
	}, options || {});

	if (options.debug) {
		debugMessge = function() {
			if (console.log && console.log.apply) {
				console.log.apply(console, arguments);
			}
		}
	} else {
		debugMessge = function() {};
	}

	debugMessge("dpTwoSelect: %o %o", el, options);

	//------------------------------
	// Build up structure
	//------------------------------

	el.find('> optgroup, > option').each(function() {
		var opt = $(this), sub_structure, has_selected, id;

		if (opt.is('optgroup')) {
			sub_structure = [];
			has_optgroup = true;
			has_selected = false;

			opt.find('> option').each(function() {
				id = 'dpid_' + (uid++);
				sub_structure.push({
					id: id,
					value: $(this).val(),
					label: $.trim($(this).text()),
					isSelected: $(this).is(':selected'),
					el: $(this)
				});

				if ($(this).is(':selected')) {
					has_selected = true;
				}
			});

			id = 'dpid_' + (uid++);
			structure.push({
				id: id,
				label: $.trim(opt.attr('label')),
				isGroup: true,
				value: id,
				isSelected: has_selected,
				subs: sub_structure,
				el: opt
			});
		} else {

			// Enables blank option
			if (opt.val() === "") {
				blankOption = opt;
				return;
			}

			id = 'dpid_' + (uid++);
			structure.push({
				id: id,
				value: opt.val(),
				label: $.trim(opt.text()),
				isSelected: opt.is(':selected'),
				el: opt
			});
		}
	});

	debugMessge("dpTwoSelect structure: %o", structure);

	// If this select box has no optgroups then its not a two-level select
	if (!has_optgroup) {
		debugMessge("dpTwoSelect no optgroup, skipping");
		return;
	}

	//------------------------------
	// Methods
	//------------------------------

	/**
	 * Find a value in the structure
	 *
	 * @param {String} val
	 * @return {Array}
	 */
	findValue = function(val) {
		var iParent = null, iChild = null;

		$.each(structure, function(iP, optInfo) {
			if (optInfo.isGroup) {
				if (optInfo.value == val) {
					iParent = iP;
					return false;
				}

				$.each(optInfo.subs, function(iC, subOptInfo) {
					if (subOptInfo.value == val) {
						iParent = iP;
						iChild = iC;
						return false;
					}
				});

				if (iParent) {
					return false;
				}
			} else {
				if (optInfo.value == val) {
					iParent = iP;
					iChild = null;
					return false;
				}
			}
		});

		if (iParent === null) {
			return null;
		}

		return [iParent, iChild];
	};

	/**
	 * Set the current value
	 * @param {String} val
	 */
	setValue = function(val) {
		var idx = findValue(val),
			parentOptInfo,
			subBlankOption,
			selectedVal,
			childOptInfo = false;

		if (!idx) {
			if (blankOption) {
				placeholder.removeClass('dp-show-child');
				parentEl.find('option').first().prop('selected', true);
				el.find('option[value=""], option:not([value])').first().prop('selected', true).trigger('change');
			} else {
				setValue(parentEl.find('option').first().data('dp-id'));
			}
			return false;
		}

		parentOptInfo = structure[idx[0]];

		if (idx && idx[1] !== null && typeof idx[1] !== 'undefined') {
			childOptInfo = parentOptInfo.subs[idx[1]];
		}

		parentEl.find('option.' + parentOptInfo.id).prop('selected', true);

		if (parentOptInfo.isGroup) {
			childEl.get(0).innerHTML = '';

			if (blankOption) {
				subBlankOption = blankOption.clone();

				if (!childOptInfo) {
					subBlankOption.prop('selected', true);
					selectedVal = subBlankOption.val();
				}

				childEl.append(subBlankOption.clone());
			}

			$.each(parentOptInfo.subs, function(i, optInfo) {
				var opt = $('<option />');
				opt.addClass(optInfo.id);
				opt.attr('value', optInfo.value);
				opt.text(optInfo.label);

				if (childOptInfo && optInfo.id == childOptInfo.id) {
					opt.prop('selected', true);
					selectedVal = opt.val();
				}

				childEl.append(opt);
			});

			// this toggle is required for IE to update the display of the select box,
			// or else you'll end up with a select box that looks like it has the same options
			// even though it doesnt
			childEl.hide().show();
			placeholder.addClass('dp-show-child');
		} else {
			selectedVal = parentOptInfo.value;
			placeholder.removeClass('dp-show-child');
		}

		// Update the bound original select box
		el.find('option[value="' + selectedVal + '"]').prop('selected', true).trigger('change');
	};

	//------------------------------
	// Init
	//------------------------------

	el.hide().addClass('dp-two-select').data('dp-two-select', this);
	placeholder = options.renderPlaceholders(el, options);
	placeholder.insertAfter(el);

	parentElWrapper = placeholder.find('.dp-parent');
	parentEl        = parentElWrapper.find('select');
	childElWrapper  = placeholder.find('.dp-child');
	childEl         = childElWrapper.find('select');

	if (blankOption) {
		parentEl.append(blankOption.clone());
	}

	// Process parent select options
	$.each(structure, function(i, optInfo) {
		var opt = $('<option />');
		opt.addClass(optInfo.id).data('dp-id', optInfo.id);
		if (optInfo.isGroup) {
			opt.attr('value', optInfo.id);
			opt.text(optInfo.label);
		} else {
			opt.attr('value', optInfo.value);
			opt.text(optInfo.label);
		}

		parentEl.append(opt);
	});

	parentEl.on('change', function() {
		var opt = $(this).find('option:selected');
		var val = opt.val();

		setValue(val);
	});

	childEl.on('change', function() {
		var val = $(this).val();

		// Update the bound original select box
		el.find('option[value="' + val + '"]').prop('selected', true).trigger('change');
	});

	// Set initial value
	setValue(el.find('option:selected').val());

	//------------------------------
	// Public accessors
	//------------------------------

	this.setValue = function(val) {
		setValue(val);
	};
	this.getOptions = function() {
		return options;
	};
	this.getPlaceholder = function() {
		return placeholder;
	};
	this.getParentEl = function() {
		return parentEl;
	};
	this.getChildEl = function() {
		return childEl;
	};
}
})(jQuery);
Orb.createNamespace('DeskPRO.UI');

/**
 * This creates an overlay (optionally modal) whose contents can be fetched via AJAX,
 * or already exist within the page.
 */
DeskPRO.UI.Overlay = new Orb.Class({
	Implements: [Orb.Util.Options, Orb.Util.Events],

	initialize: function(options) {

		// Init vars
		this.objectId = null;

		this.options = {
			triggerElement: null,
			contentMethod: 'element',
			contentElement: null,
			contentAjax: {
				url: '',
				type	: 'GET',
				dataType: 'html'
			},
			iframeUrl: null,
			iframeId: false,
			maxHeight: 700,
			maxWidth: 900,
			destroyOnClose: false,
			customClassname: '',
			classname: '',
			isModal: true,
			zIndex: 10001,
			escapeClose: true,
			modalClickClose: true,
			objectGroup: 'default',
			addClose: true,
			fullScreen: false,	// Fullscreen overlay, ignores maxHeight, maxWidth options
			fullScreenMargin: '35px'
		};

		this.isThisDestroyed = false;
		this.hasInit = false;
		this.hasSentAjax = false;
		this.elements = {};

		if (options) this.setOptions(options);

		if (this.options.triggerElement) {
			this.setupTriggerElement($(this.options.triggerElement));
		}

		if (this.options.zIndex == 'none') {
			this.options.zIndex = '';
		}

		if (this.options.escapeClose) {
			$(document).on('keydown', (function (ev) {
				if (ev.which == 27) {
					this.closeOverlay();
				}
			}).bind(this));
		}
	},

	/**
	 * Get the main wrapper for the overlay
	 */
	getElement: function() {
		return this.getWrapper();
	},


	/**
	 * Alias for isOverlayOpen.
	 *
	 * @return {Boolean}
	 */
	isOpen: function() {
		return this.isOverlayOpen();
	},


	/**
	 * Check to see if the overlay is currently open.
	 */
	isOverlayOpen: function() {
		if (!this.hasInit) return false;
		return this.elements.wrapper.is(':visible');
	},


	/**
	 * Standard naming for UI elements. Alias for openOverlay.
	 */
	open: function() {
		return this.openOverlay();
	},


	/**
	 * Display the overlay
	 */
	openOverlay: function() {
		if (!this.initOverlay()) {
			return;
		}

		if (this.isOverlayOpen()) {
			return;
		}

		var evData = { overlay: this, cancel: false };
		this.fireEvent('beforeOverlayOpened', evData);
		if (evData.cancel) {
			return;
		}

		var zindex = this.options.zIndex;
		if (zindex == 'top') {
			var zindex = Orb.findHighestZindex()+1;
		}

		this.elements.modal.css({
			'z-index': zindex,
			'position': 'fixed',
			'top': 0,
			'right': 0,
			'bottom': 0,
			'left': 0
		});

		this.elements.modal.fadeIn(200);

		var topOffset = $(document).scrollTop();

		if (this.options.contentMethod == 'iframe') {

			var w = $(window).width() - 250;
			var h = $(window).height() - 150;

			if (w > this.options.maxWidth) w = this.options.maxWidth;
			if (h > this.options.maxHeight) h = this.options.maxHeight;

			this.elements.wrapper.css({ width: w, height: h });
			$('iframe:first', this.elements.wrapper).css({ width: w, height: h-37 });

			var x = ($(window).width() - this.elements.wrapperOuter.outerWidth()) / 2;
			var y = ($(window).height() - this.elements.wrapperOuter.outerHeight()) / 2;

		} else {
			var w = this.elements.wrapperOuter.outerWidth();
			var pageW = $(window).width();
			var leftForCenter = (pageW / 2) - (w / 2);

			var h = this.elements.wrapperOuter.outerHeight();
			var pageH = $(window).height();
			var topForCenter = (pageH / 2) - (h / 2);

			this.elements.wrapperOuter.css({
				'top': topForCenter+topOffset,
				'left': leftForCenter
			});
		}

		if (true == this.getOption('fullScreen')) {
			this.elements.wrapperOuter.css({
				'z-index': (zindex ? zindex+1 : ''),
				'position': 'fixed',
				'left': this.getOption('fullScreenMargin'),
				'right': this.getOption('fullScreenMargin'),
				'top': this.getOption('fullScreenMargin'),
				'bottom': this.getOption('fullScreenMargin')
			});
		}
		else {
			this.elements.wrapperOuter.css({
				'z-index': (zindex ? zindex+1 : ''),
				'position': 'fixed',
				'left': leftForCenter
			});
		}

		this.reposition();
		this.elements.wrapperOuter.fadeIn(450, (function() {
			this.fireEvent('overlayOpened', { overlay: this });
		}).bind(this));
	},


	/**
	 * Recalculate positions
	 */
	reposition: function() {
		if (this.getOption('fullScreen')) {
			return;
		}

		var w = this.elements.wrapperOuter.outerWidth();
		var pageW = $(window).width();
		var leftForCenter = (pageW / 2) - (w / 2);

		var h = this.elements.wrapperOuter.outerHeight();
		var pageH = $(window).height();
		var topForCenter = (pageH / 2) - (h / 2);

		var evData = {
			overlay: this,
			wrapperOuter: this.elements.wrapperOuter,
			pageW: pageW,
			pageH: pageH,
			w: w,
			h: h,
			top: topForCenter,
			left: leftForCenter,
			setLeft: function(x) { this.left = x; },
			setTop: function(x) { this.top = x; }
		};

		this.fireEvent('position', evData);

		this.elements.wrapperOuter.css({
			'top': evData.top,
			'left': evData.left
		});
	},

	recalcForResize: function() {
		if (!this.isOpen()) return;

		this.reposition();
	},


	/**
	 * Standard naming for UI elements. Alias for closeOverlay.
	 */
	close: function() {
		return this.closeOverlay();
	},


	/**
	 * Close/hide the overlay
	 */
	closeOverlay: function() {

		if (!this.isOverlayOpen()) {
			return;
		}

		var eventData = { overlay: this, cancelClose: false };
		this.fireEvent('beforeOverlayClosed', eventData);

		if (eventData.cancelClose) return;

		this.elements.modal.fadeOut(450);
		this.elements.wrapperOuter.fadeOut(200);

		this.fireEvent('overlayClosed', { overlay: this });
		this.fireEvent('close', { overlay: this });

		if (this.options.destroyOnClose) {
			this.destroy();
		}
	},



	/**
	 * Initiate the overlay by created the various elements needed etc.
	 */
	initOverlay: function() {

		if (this.hasInit) return true;

		$(window).bind('resize.' + this.OBJ_ID, this.recalcForResize.bind(this));

		if (this.options.isModal) {
			this.elements.modal = $('<div class="deskpro-overlay-overlay '+this.options.customClassname+'" style="display:none" />');
			this.elements.modal.appendTo('body');

			if (this.options.modalClickClose) {
				this.elements.modal.on('click', (function(ev) {
					if (ev && ev.deskpro && ev.deskpro.cancelClose) return;
					this.closeOverlay();
				}).bind(this));
			}
		}

		this.elements.wrapperOuter = $('<div class="deskpro-overlay-outer '+this.options.customClassname+' ' + this.options.classname + '" style="display:none" />');
		this.elements.wrapperOuter.appendTo('body');

		var overlayStyles = "";
		if (true == this.getOption('fullScreen')) {
			overlayStyles = "height: 100%";
		}
		this.elements.wrapper = $('<div class="deskpro-overlay '+this.options.customClassname+'" style="'+overlayStyles+'">');
		this.elements.wrapper.appendTo(this.elements.wrapperOuter);

		switch (this.options.contentMethod) {
			case 'element':

				var el = $(this.options.contentElement);
				if (el.data('overlay-apply-class')) {
					this.elements.wrapperOuter.addClass(el.data('overlay-apply-class'));
				}
				this._setContent(el);

				this.hasInit = true;

				return true;
				break;

			case 'ajax':

				// Already sending
				if (this.hasSentAjax) return false;
				this.hasSentAjax = true;

				var ajaxConfig = Object.merge(this.options.contentAjax, {
					success: this._handleAjaxSuccess.bind(this)
				});

				$.ajax(ajaxConfig);

				this.fireEvent('ajaxStart', {
					overlay: this
				});

				return false;
				break;

			case 'iframe':

				var name = 'iframe_' + Orb.uuid();
				var el = $('<iframe name="'+name+'" src="'+this.options.iframeUrl+'"></iframe>');
				if (this.options.iframeId) {
					el.attr('id', this.options.iframeId);
				}

				this._setContent(el);
				this.hasInit = true;

				this.elements.wrapper.addClass('no-pad').addClass('iframe');
				this.elements.wrapper.find('> .overlay-content').addClass('no-footer');

				return true;
				break;
		}

		DP.console.error('Unknown content method: %s', this.options.contentMethod);

		return false;
	},



	/**
	 * Callback used with the ajax content setter when the result was
	 * fetched from the server.
	 */
	_handleAjaxSuccess: function(data) {

		var dataEl = $(data);

		// We always want a top-level elements.
		// So if AJAX supplied multiple roots,
		// we'll just wrap in a single container div

		if (dataEl.length != 1) {
			var el = $('<div />');
			el.append(dataEl);
		} else {
			var el = dataEl;
		}

		el.show();
		this._setContent(el);

		this.hasInit = true;

		var eventData = {
			overlay: this,
			ajaxData: data
		};
		this.fireEvent('ajaxDone', eventData);

		this.openOverlay();
	},



	/**
	 * Handle setting the content of the overlay.
	 */
	_setContent: function (el) {
		this.elements.wrapper.empty();
		el.detach().appendTo(this.elements.wrapper);

		// Often pages will hide content by default in the initial page,
		// but our wrapper element is hidden so we dont want the innards to be hidden.
		el.show();

		// If the caller didnt provide full markup for the content, we'll wrap it ourselves
		if (!$('div.overlay-content:first', el).length) {
			var insideEl = el;
			var el = $('<div class="overlay-content" />');
			insideEl.wrap(el);
		}

		if (this.options.title && !this.elements.wrapper.find('.overlay-title')[0]) {
			this.elements.wrapper.prepend('<div class="overlay-title"><h4></h4></div>').find('h4').text(this.options.title);
		}

		if (this.options.addClose && !$('.close-trigger, .close-overlay', this.elements.wrapper).length) {
			$('.overlay-title:first', this.elements.wrapper).prepend('<a class="close close-trigger close-overlay">Close</a>');
		}

		$('.overlay-close-trigger, .close-trigger, .close-overlay', this.elements.wrapper).first().on('click', (function (ev) {
			ev.preventDefault();
			this.closeOverlay();
		}).bind(this));

		if (!$('.overlay-footer:first', el).length) {
			$('div.overlay-content:first', el).addClass('no-footer');
		}

		this.reposition();

		this.fireEvent('contentSet', {
			overlay: this,
			contentEl: el,
			wrapperEl: this.elements.wrapper
		});
	},

	setContent: function(el) {
		this._setContent(el);
	},



	/**
	 * Set up a click trigger on an element (or elements).
	 *
	 * @param mixed el A selector, an element, or a jQuery collection
	 */
	setupTriggerElement: function(el) {
		el = $(el);

		var fn = (function (ev) {
			this.openOverlay();
			ev.preventDefault();
		}).bind(this);

		if (el.is('.dbl-click-trigger')) {
			el.on('dblclick', fn);
		} else {
			el.on('click', fn);
		}
	},


	/**
	 * Get the wrapper element for the overlay
	 *
	 * @return {jQuery}
	 */
	getWrapper: function() {
		if (!this._hasInit) {
			this.initOverlay();
		}
		return $(this.elements.wrapperOuter);
	},


	/**
	 * Destroy this overlay and all of its supporting elements.
	 */
	destroy: function() {

		this.fireEvent('beforeDestroy', [this]);

		if (this.elements.wrapperOuter) {
			this.elements.wrapperOuter.remove();
		}
		if (this.elements.modal) {
			this.elements.modal.remove();
		}
		this.isThisDestroyed = true;

		this.fireEvent('destroyed', [this]);
		$(window).unbind('.' + this.OBJ_ID);
	},


	isDestroyed: function() {
		return this.isThisDestroyed;
	}
});

Orb.createNamespace('DeskPRO.User');

/**
 * User window handler
 */
DeskPRO.User.Window = new Orb.Class({

	Extends: DeskPRO.BasicWindow,

	init: function() {
		this.PAGE = null;
	},

	initPage: function() {
		var self = this;

		var isIE = (navigator.appVersion.toLowerCase().indexOf("msie")!=-1);
		if (isIE) {
			$('html').addClass('browser-ie');
		}

		if (!isIE) {
			// Prevents default browser action of navigating to a dropped file
			// if a drop target isnt configured yet (ie no tab open to accept a file)
			$(document).bind('drop dragover', function (e) {
				e.preventDefault();
			});

			$(document).bind('dragover', function (e) {
				var timeout = window.dropZoneTimeout;
				if (!timeout) {
					$('body').addClass('file-drag-over');
				} else {
					clearTimeout(timeout);
				}

				window.dropZoneTimeout = setTimeout(function () {
					window.dropZoneTimeout = null;
					$('body').removeClass('file-drag-over');
				}, 100);
			});
		}

		//------------------------------
		// Correct min heights for clean looking sidebar
		//------------------------------

		var calcHeights = function() {
			var h = $('#dp_sidebar').height();
			$('#dp_content').css({
				'min-height': h
			});
		};

		if (!window.IS_ADMIN_CONTROLS) {
			calcHeights();
		}

		if (this.PAGE) {
			this.PAGE.initPage();
		}

		this.elementHandlers = {};
		this.initFeatures(document);
		this.initLanguageChoices();

		$('#dp_footer').css({'display': 'block', 'opacity': 1, 'visibility': 'visible'});
		$('.dp-copy').css({'display': 'block', 'opacity': 1, 'visibility': 'visible'})
				.find('a').css({'display': 'inline', 'opacity': 1, 'visibility': 'visible'});

		$('<style type="text/css">#dp_footer, .dp-copy { display: block !important; opacity: 1 !important; visibility: visible !important; } .dp-copy a { display: inline !important; opacity: 1 !important; visibility: visible !important; }</style>').appendTo('head');

		if ($('html').attr('dir') == 'rtl' && $('#share-this-js').length) {
			var interval;

			interval = setInterval(function() {
				var wrapper = $('#stwrapper');
				if (wrapper.length) {
					wrapper.css({
						left: '',
						right: '-999px'
					});
					clearInterval(interval);
				}
			}, 100);
		}

		window.setTimeout(function() {
			self.sessionPing();
		}, 600000);

		// Erase content from the trap fields
		// (a precaution against if someone uses a browser extention to fill in fields etc)
		$('.dp-its-a-trap').find('input').on('change keypress', function() {
			$(this).val('');
		}).val('');
	},

	sessionPing: function() {
		var self = this;
		$.ajax({
			url: BASE_PATH + 'dp.php/session-ping.json?i=u',
			cache: false,
			complete: function() {
				window.setTimeout(function() {
					self.sessionPing();
				}, 600000);
			},
			success: function(data) {
				if (data.request_token) {
					window.DP_REQUEST_TOKEN = data.request_token;
				}
			}
		});
	},

	initFeatures: function(contextEl) {
		var self = this;

		$('.with-handler[data-element-handler]', contextEl).each(function() {
			var el = $(this);
			var className = el.data('element-handler');
			var classObj = Orb.getNamespacedObject(className);

			if (!classObj) {
				DP.console.error("Unknown portal handler `%s` on element %o", className, this);
				return;
			}

			if (!el.attr('id')) {
				el.attr('id', Orb.getUniqueId('portal_'));
			}

			var obj = new classObj({ el: el });
			self.elementHandlers[el.attr('id')] = obj;
		});

		$('form.with-form-validator', contextEl).each(function() {
			var v = new DeskPRO.Form.FormValidator($(this));
			$(this).data('form-validator-inst', v);
		});

		$('a.in-overlay').on('click', function(ev) {
			ev.preventDefault();

			var el = $(this);
			var url = el.attr('href');
			if (url.indexOf('?') !== -1) {
				url += '&_partial';
			} else {
				url += '?_partial';
			}

			var overlay = new DeskPRO.UI.Overlay({
				contentMethod: 'ajax',
				contentAjax: {
					url: url
				},
				destroyOnClose: true
			});

			overlay.open();
		});

		$('.timeago').timeago();
		$('input.datepicker, .datepicker input').datepicker({
			dateFormat: 'yy-mm-dd'
		});

		$(document).on('click', '.dp-bound-faded', function() {
			var parent = $(this).parent();
			var link = $('a[href]', parent).first();
			window.location = link.attr('href');
		});

		$('.dp-inplace-drop').each(function() {
			var sel = $(this).find('select').first();
			var label = $(this).find('.dp-opt-label');

			var updateTitle = function() {
				var opt = sel.find('option:selected').first();
				if (!opt[0]) {
					opt = sel.find('option').first();
				}

				label.text(opt.text());

				if (sel.data('bind-to')) {
					var bound = $(sel.data('bind-to'));
					bound.text(opt.text());
				}
			};

			sel.on('change', function() {
				updateTitle();
			});

			updateTitle();
		});

		if (typeof $.prototype.popover != 'undefined') {
			$('.dp-help-pop').popover({
				placement: function (tip, element) {
					var $element = $(element);
					var pos = $.extend({}, $element.offset(), {
						width: element.offsetWidth,
						height: element.offsetHeight
					});

					var actualWidth = 355;
					var actualHeight = 200;
					var boundTop = $(document).scrollTop();
					var boundLeft = $(document).scrollLeft();
					var boundRight = boundLeft + $(window).width();
					var boundBottom = boundTop + $(window).height();
					var elementAbove = {
						top: pos.top - actualHeight,
						left: pos.left + pos.width / 2 - actualWidth / 2
					};
					var elementBelow = {
						top: pos.top + pos.height,
						left: pos.left + pos.width / 2 - actualWidth / 2
					};
					var elementLeft = {
						top: pos.top + pos.height / 2 - actualHeight / 2,
						left: pos.left - actualWidth
					};
					var elementRight = {
						top: pos.top + pos.height / 2 - actualHeight / 2,
						left: pos.left + pos.width
					};
					var above = isWithinBounds(elementAbove);
					var below = isWithinBounds(elementBelow);
					var left = isWithinBounds(elementLeft);
					var right = isWithinBounds(elementRight);

					function isWithinBounds (elementPosition) {
						return boundTop < elementPosition.top
						&& boundLeft < elementPosition.left
						&& boundRight > (elementPosition.left + actualWidth)
						&& boundBottom > (elementPosition.top + actualHeight)
					};

					var position;
					if (above) {
						position = 'top';
					} else if (below) {
						position = 'bottom';
					} else if (left) {
						position = 'left';
					} else {
						position = 'right';
					}

					if ($('html').attr('dir') == 'rtl') {
						setTimeout(function() {
							var $tip = $(tip);
							$tip.css({left: '', right: ''});

							if (position == 'top' || position == 'bottom') {
								$tip.css({right: $(window).width() - pos.left - pos.width / 2 - $tip.width() / 2, left: ''});
							} else if (position == 'left') {
								$tip.css({right: $(window).width() - pos.left, left: ''});
							} else if (position == 'right') {
								$tip.css({right: $(window).width() - pos.left - pos.width - $tip.width(), left: ''});
							}
						}, 0);
					}

					return position;
				}
			});
		}
	},

	initLanguageChoices: function() {
		var select = $('#dp_lang_chooser_form').find('select');
		if (!select.length) {
			return;
		}

		var choices = {}, selected;
		select.find('option').each(function() {
			var $this = $(this);
			choices[$this.attr('value')] = {
				name: $this.html(),
				val: $this.attr('value'),
				flag: $this.data('flag')
			};
			if ($this.attr('selected')) {
				selected = $this.attr('value');
			}
		});

		if (!selected) {
			selected = select.find('option:first').attr('value');
		}

		var getChoiceHtml = function(choice) {
			if (choice.flag && choice.flag.length) {
				return '<img src="' + ASSETS_BASE_URL + '/images/flags/' + choice.flag + '" class="flag" alt="" /> ' + choice.name;
			} else {
				return '<span class="flag"></span> ' + choice.name;
			}
		};

		var choiceHtml = $('<span class="language-choice" />'),
			innerHtml = $('<span class="country-name" />').html(getChoiceHtml(choices[selected]));
		choiceHtml.append(innerHtml);
		choiceHtml.append($('<span class="drop" />'));

		var choiceMenu = $('<ul class="language-options-list" />').css({display: 'none', position: 'absolute'});
		for (var i in choices) {
			var li = $('<li />').html(getChoiceHtml(choices[i]));
			(function(choice) {
				li.click(function() {
					select.val(choice.val);
					select.closest('form').submit();
					choiceMenu.hide();
					innerHtml.html(getChoiceHtml(choice));
				});
			})(choices[i]);
			choiceMenu.append(li);
		}
		$('#dp').append(choiceMenu);

		innerHtml.css({
			width: choiceMenu.outerWidth() - 24
		});
		var updatePos = function() {
			if (!choiceMenu.hasClass('open')) return;
			var pos = choiceHtml.offset();
			choiceMenu.css({
				top: pos.top + choiceHtml.outerHeight(true) - 1,
				left: pos.left
			});
		};
		$(window).on('resize', updatePos);
		choiceHtml.click(function() {
			choiceMenu.addClass('open');
			updatePos();
			choiceMenu.show();
		});

		select.hide();
		select.after(choiceHtml);

		$(document).click(function(e) {
			if (!choiceHtml.find(e.target).length && !choiceMenu.find(e.target).length) {
				choiceMenu.hide().removeClass('open');
			}
		});
	},

	showAutoSignInOverlay: function() {
		var overlay = $('#auto-sign-in-overlay'), overlayWidth = overlay.width();


		overlay.css({
			left: Math.floor(($(document.body).width() - overlayWidth) / 2)
		}).show();
	},

	getHandler: function(id) {
		return this.handlers[id];
	},

	hasHandler: function(id) {
		return !!this.handlers[id];
	},

	setPageHandler: function(page) {
		this.PAGE = page;
	},

	getPageHandler: function() {
		return this.PAGE;
	}
});

function Orb_Util_TimeAgo_getPhraseFor(type, num, ago) {

	var phrasepre = 'time';
	if (ago) {
		phrasepre = 'time-ago';
	}

	if (type == 'min') type = 'minute';
	else if (type == 'mins') type = 'minutes';
	else if (type == 'sec') type = 'second';
	else if (type == 'secs') type = 'seconds';

	var phrasename = 'user.time.' + phrasepre + '_x_' + type;
	if (num == 1) {
		var phrasename = 'user.time.' + phrasepre + '_1_' + type;
	}
	if (type == 'sec' && num <= 0) {
		var phrasename = 'user.time.' + phrasepre + '_less_second';
	}

	if (!window.DESKPRO_LANG || !DESKPRO_LANG[phrasename]) {
		if (window.console && window.console.warn) {
			console.warn("Missing phrase %s", phrasename);
		}
		return 'timeago_missing_phrase: '+ phrasename;
	}

	return (DESKPRO_LANG[phrasename] || "").replace(/\{0\}/g, num);
}
Orb.createNamespace('DeskPRO');

/**
 * This calls a callback function every time something touches the object, but limits
 * it to a minimum timeout period between calls. Feedbackl for things like ajax searchers
 * where you want to update as the user types, but dont want to do a request
 * every millisecond as they enter new characters.
 */
DeskPRO.TouchCaller = new Orb.Class({

	Implements: [Orb.Util.Options],

	initialize: function(options) {
		this.options = {
			/**
			 * The minimum amount of time between the calls
			 */
			timeout: 0,

			/**
			 * The callback to call
			 */
			callback: function() {},

			/**
			 * The object context in which to call the callback
			 */
			context: null,

			/**
			 * Any call to touch fires the event
			 */
			alwaysChange: false
		};

		this.setOptions(options);

		this.touched = null;
		this.lastTouch = null;
		this.lastTime = new Date();
		this.timeout = null;
	},

	touch: function(touch, force) {

		if (touch === '') {
			touch = '(empty)';
		}

		if (typeof touch == 'undefined' || touch == null) {
			this.lastTouch = null;
			touch = true;
		}

		this.touched = touch;

		if (!force) {
			// Not different, nothing to do
			if (!this.options.alwaysChange && (this.lastTouch !== null && this.lastTouch == touch)) {
				return;
			}

			var now = new Date();
			var diff = now.getTime() - this.lastTime.getTime();
			if (diff < this.options.timeout) {
				// Too soon. Wait until timeout expires
				if (!this.timeout) {
					this.timeout = window.setTimeout(this.exec.bind(this), this.options.timeout);
				}
				return;
			}
		}

		// If we got here, we need to run it
		this.exec();
	},

	exec: function() {
		if (this.timeout) {
			window.clearTimeout(this.timeout);
			this.timeout = null;
		}

		this.lastTouch = this.touched;
		this.touched = null;
		this.lastTime = new Date();

		if (this.options.context) {
			this.options.callback.apply(this.options.context);
		} else {
			this.options.callback();
		}
	},

	destroy: function() {
		if (this.timeout) {
			window.clearTimeout(this.timeout);
			this.timeout = null;
		}
		this.options = null;
		this.lastTime = null;
	}
});

Orb.createNamespace('DeskPRO');

/**
 * A translate class that works like the PHP Translate class.
 */
DeskPRO.Translate = new Orb.Class({
	/**
	 * @param {Function} loader A function loader to return a phrase when it isnt set locally
	 */
	initialize: function(loader) {
		this.phrases = {};
		this.loader = loader || function(id) { return '['+id+']'; };

		if (window.DESKPRO_LANG) {
			this.phrases = window.DESKPRO_LANG;
		}
	},


	/**
	 * Get the raw phrase text, or load it from the loader if it hasnt been loaded yet
	 *
	 * @param {String} phrase_name
	 * @return {String}
	 */
	getPhraseText: function(phrase_name) {
		if (typeof this.phrases[phrase_name] == 'undefined') {
			this.phrases[phrase_name] = this.loader(phrase_name);
		}

		if (typeof this.phrases[phrase_name] == 'undefined') {
			return null;
		}

		return this.phrases[phrase_name];
	},


	/**
	 * Get a phrase and put in var replacements
	 *
	 * @param {String} phrase_name
	 * @param {Object} vars
	 * @return {String}
	 */
	phrase: function(phrase_name, vars, raw) {
		var text = this.getPhraseText(phrase_name) || '[' + phrase_name + ']';
		return this.phraseWithString(text, vars, raw);
	},


	/**
	 * @param {String} text
	 * @param {Object} vars
	 * @param {Boolean} raw
	 */
	phraseWithString: function(text, vars, raw) {
		if (vars) {
			if (typeof vars.count != 'undefined') {
				text = this.choosePlural(text, vars.count);
			}

			Object.each(vars, function (value, key) {
				var re = new RegExp('\{\{\s*' + Orb.regexQuote(key) + '\s*\}\}' , 'g');

				if (raw) {
					text = text.replace(re, value);
				} else {
					text = text.replace(re, Orb.escapeHtml(value));
				}
			});
		}

		return text;
	},


	/**
	 * Check if we know about a specific phrase
	 *
	 * @param {String} phrase_name
	 * @return {Boolean}
	 */
	hasPhrase: function(phrase_name) {
		if (this.getPhraseText(phrase_name) === null) {
			return false;
		}

		return true;
	},


	/**
	 * Chooses a plural from the string <var>text</var> based on <var>number</var>.
	 *
	 * This is a JS port of the Symfony MessageSelector class.
	 * Syntax described: http://symfony.com/doc/current/book/translation.html#pluralization
	 *
	 * @param text
	 * @param number
	 */
	choosePlural: function(text, number) {
		var parts = text.split('|');
		if (number == 0 || number != 1) {
			return parts[1];
		} else {
			return parts[0];
		}
	},

	testInterval: function(number, interval) {
		var x = 0
		var number = parseInt(number);
		interval = interval.trim();

		var leftDelimIndex  = 1;
		var leftIndex       = 2;
		var rightIndex      = 3;
		var rightDelimIndex = 4;

		var intervalRe = /({\s*(\-?\d+[\s*,\s*\-?\d+]*)\s*})|([\[\]])\s*(-Inf|\-?\d+)\s*,\s*(\+?Inf|\-?\d+)\s*([\[\]])/;

		var match = interval.exec(intervalRe);
		if (!match) {
			if (window.console && window.console.error) {
				console.error("Invalid interval: %s", interval);
			}
			return 'invalid interval';
		}

		if (matches[1]) {
			var nums = matches[2].split(',');
			for (x = 0; x < nums.length; x++) {
				if (number == parseInt(nums[x])) {
					return true;
				}
			}
		} else {
			var leftNum    = match[leftIndex];
			var rightNum   = match[rightIndex];

			if (leftNum == '-Inf') leftNum = Number.NEGATIVE_INFINITY;
			else if (leftNum == 'Inf') leftNum = Number.POSITIVE_INFINITY;
			if (rightNum == '-Inf') rightNum = Number.NEGATIVE_INFINITY;
			else if (rightNum == 'Inf') rightNum = Number.POSITIVE_INFINITY;

			var leftDelim  = match[leftDelimIndex];
			var rightDelim = match[rightDelimIndex];

			if (
				( ('[' == leftDelim && number >= leftNum) || number > leftNum )
				&& ( (']' == rightDelim && number >= rightNum) || number > rightNum )
			)
			{
				return true;
			}
		}

		return false;
	}
});
Orb.createNamespace('DeskPRO.User.ElementHandler');

/**
 * Element handlers are detected in the source and instantiated automatically with the
 * element being passed in.
 *
 * Element handlers are always instantiated when the document is ready.
 */
DeskPRO.User.ElementHandler.ElementHandlerAbstract = new Orb.Class({

	Implements: [Orb.Util.Options],

	initialize: function(options) {
		this.options = {};
		if (options) {
			this.setOptions(options);
		}

		this.el = null;
		if (this.options.el) {
			this.el = $(this.options.el);
		}

		this.init();
	},


	/**
	 * Empty hook method for children to implement init code
	 */
	init: function() {

	}
});
Orb.createNamespace('DeskPRO.User.ElementHandler');

DeskPRO.User.ElementHandler.MoreLoader = new Orb.Class({

	Extends: DeskPRO.User.ElementHandler.ElementHandlerAbstract,

	init: function() {

		this.listEl   = $('.content-list:first ul:first', this.el);
		this.moreWrap = $('.content-more:first', this.el);
		this.moreBtn  = $('button:first, a.button:first', this.el).first();

		this.loadUrl = this.el.data('load-url');

		this.currentPage = 1;

		var self = this;
		this.moreBtn.on('click', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();
			self.loadNextPage();
		});
	},

	loadNextPage: function() {

		if (this.moreWrap.is('.loading')) {
			return;
		}

		this.moreWrap.addClass('loading');

		var url = this.loadUrl.replace('{page}', this.currentPage + 1);

		if (url.indexOf('?') === -1) {
			url += '?_partial=more';
		} else {
			url += '&_partial=more';
		}

		$.ajax({
			url: url,
			dataType: 'html',
			context: this,
			success: function(html) {
				this.insertNewItems(html.trim());
			}
		});
	},

	insertNewItems: function(html) {
		if (html.length) {
			var els = $(html);
		} else {
			var els = null;
		}

		this.currentPage++;

		this.moreWrap.removeClass('loading');
		if (!els || !els.length || !els.last().is('.has-more')) {
			this.moreWrap.remove();
		}

		this.listEl.append(els);
	}
});

Orb.createNamespace('DeskPRO.User.ElementHandler');

DeskPRO.User.ElementHandler.LoginBox = new Orb.Class({

	Extends: DeskPRO.User.ElementHandler.ElementHandlerAbstract,

	init: function() {
		this.loginBox = $('#dp_login_box');

		this.loginSection = $('.dp-login-section', this.el);
		this.resetSection = $('.dp-reset-section', this.el);

		this._initResetSection();
	},

	//#########################################################################
	//# Reset Stuff
	//#########################################################################

	_initResetSection: function() {
		$('.forgot', this.el).on('click', (function(ev) {
			ev.preventDefault();
			this.showReset();
		}).bind(this));

		$('.back', this.resetSection).on('click', (function(ev) {
			this.hideReset();
		}).bind(this));

		$('.dp-do-send', this.resetSection).on('click', (function(ev) {
			ev.preventDefault();
			this.sendReset();
		}).bind(this));
	},

	sendReset: function() {

		this.resetSection.addClass('loading');

		$.ajax({
			url: BASE_URL + 'login/reset-password/send.json',
			type: 'POST',
			data: {
				email: $('#dp_login_email').val()
			},
			dataType: 'json',
			context: this,
			success: function() {
				this.resetSection.removeClass('loading');

				var descEl = $('.dp-reset-desc', this.resetSection);
				var sentEl = $('.dp-reset-sent', this.resetSection);

				descEl.slideUp('fast', function() {
					sentEl.slideDown();
				});
			}
		});
	},

	showReset: function() {
		this.loginSection.slideUp('fast', (function() {
			this.resetSection.slideDown('fast');
		}).bind(this));
	},

	hideReset: function(quick) {
		if (quick) {
			this.resetSection.hide();
			this.loginSection.show();
			$('.dp-reset-desc', this.resetSection).show();
			$('.dp-reset-sent', this.resetSection).hide();
		} else {
			this.resetSection.slideUp('fast', (function() {
				this.loginSection.slideDown('fast');

				// Also reset view on others
				$('.dp-reset-desc', this.resetSection).show();
				$('.dp-reset-sent', this.resetSection).hide();
			}).bind(this));
		}
	}
});

Orb.createNamespace('DeskPRO.User.ElementHandler');

DeskPRO.User.ElementHandler.NewTicket = new Orb.Class({

	Extends: DeskPRO.User.ElementHandler.ElementHandlerAbstract,

	init: function() {
		this.titleTxt = $('#newticket_ticket_subject');
		this.messageTxt = $('#newticket_ticket_message');

		this.ticketForm = $('#dp_newticket_form');

		$('#dp_newticket_form').find('select').not('.no-dp-select').dpTwoSelect();

		this._initSuggestionsBox();
		this._initFields();
		this._initLoginForm(this.el);
		this._initPreticketStatus();
	},

	//#########################################################################
	//# Suggestions
	//#########################################################################

	_initSuggestionsBox: function() {
		if (this.el.data('suggestions-url')) {
			this.inlineSuggestions = new DeskPRO.User.InlineSuggestions({
				elementWrapper: this.el,
				titleText: this.titleTxt,
				contentText: this.messageTxt,
				onResolved: this.setTicketSolvedAjax.bind(this),
				onResolvedRedirect: this.setTicketSolvedRedirect.bind(this),
				onNotResolved: this.setTicketUnsolvedAjax.bind(this)
			});
		}
	},

	setTicketSolvedAjax: function(content_type, content_id) {
		this.setTicketSolvedStatusAjax(content_type, content_id, false);
	},
	setTicketUnsolvedAjax: function(content_type, content_id) {
		this.setTicketSolvedStatusAjax(content_type, content_id, true);
	},

	setTicketSolvedStatusAjax: function(content_type, content_id, setUnsolved) {
		var preticket_id = $('#dp_newticket_preticket_status_id').val();
		if (preticket_id > 0) {
			url = BASE_URL + 'tickets/new/content-solved-save.json?'
					+ 'preticket_status_id=' + escape(preticket_id) + '&'
					+ 'content_type=' + escape(content_type) + '&'
					+ 'content_id=' + escape(content_id);

			if (setUnsolved) {
				url += '&add_unsolved=1';
			}

			$.ajax({
				url: url,
				type: 'GET'
			});
		}
	},

	setTicketSolvedRedirect: function(url, content_type, content_id) {

		var preticket_id = $('#dp_newticket_preticket_status_id').val();
		if (preticket_id > 0) {
			url = BASE_URL + 'tickets/new/content-solved-redirect?'
				+ 'preticket_status_id=' + escape(preticket_id) + '&'
				+ 'content_type=' + escape(content_type) + '&'
				+ 'content_id=' + escape(content_id) + '&'
				+ 'url=' + escape(url);
		}

		window.location = url;
	},

	//#########################################################################
	//# Department and field stuff
	//#########################################################################

	_initFields: function() {
		this.depSelect = $('select.department_id, input.department_id', this.el).first();
		this.departmentId = -1;

		var self = this;
		this.depSelect.on('change', function() {
			self.handleDepChange();
		});
		this.depSelect.data('original-name', this.depSelect.attr('name'));

		$('select.category_id, select.priority_id, select.product_id, .dp-two-select select').on('change', function() {
			self.runChecks();
		});

		this.handleDepChange();
	},

	handleDepChange: function() {
		this.setDepartment(this.depSelect.val());
	},

	setDepartment: function(department_id) {

		if (department_id == this.departmentId) {
			// nochange
			return;
		}

		this.clearAll();

		this.departmentId = department_id;
		var activeDepId = this.departmentId;

		if (!window.DESKPRO_TICKET_DISPLAY) {
			return;
		}
		if (!activeDepId || !window.DESKPRO_TICKET_DISPLAY[activeDepId]) {
			activeDepId = 0;
		}

		var depItems = window.DESKPRO_TICKET_DISPLAY[activeDepId];
		this.depItems = depItems;
		this.depItemsWithChecked = false;

		DP.console.log('depItems %o', depItems);

		$('.ticket-display-field').hide();

		Array.each(depItems, function(item) {
			var itemId = this.getItemId(item);
			var itemEl = $('.' + itemId).closest('.ticket-display-field');

			// Detach and re-attach to correct ordering
			itemEl.detach().appendTo('#fields_container');

			// Turn on criteria-less fields now
			if (!item.check) {
				itemEl.show();
			} else {
				itemEl.addClass('with-criteria');
				this.depItemsWithChecked = true;
			}
		}, this);

		this.runChecksRecursionCount = 0;
		if (this.depItemsWithChecked) {
			this.runChecks();
		}
	},

	runChecks: function() {
		if (this.runChecksRecursionCount > 30) {
			console.error('runChecks running too many times: %o', this.depItems);
			return;
		}

		var self = this;
		var changed = false;
		$('.with-criteria').each(function() {
			var el = $(this);
			var item = self.findItemForEl(el);
			if (!item) return;

			if (item.check(ticketReader)) {
				if (!el.is(':visible')) {
					changed = true;
				}
				el.show();
			} else {
				if (el.is(':visible')) {
					changed = true;
				}
				el.hide();
			}
		});

		if (changed) {
			this.runChecksRecursionCount++;
			this.runChecks();
			this.runChecksRecursionCount--;
		}
	},

	findItemForEl: function(el) {
		var fieldId = el.data('field-id');
		var theitem = null;
		Array.each(this.depItems, function(item) {
			if (item.id == fieldId) {
				theitem = item;
				return false;
			}
		});

		return theitem;
	},

	clearAll: function() {
		$('.ticket-display-field').hide().removeClass('field-enabled with-criteria');
	},

	getItemId: function(item) {
		var itemId = item.field_type;
		if (item.field_id) {
			itemId += '_' + item.field_id;
		}

		return itemId;
	},


	//#########################################################################
	// In-page login form
	//#########################################################################

	_initLoginForm: function(context) {
		this.inlineLogin = new DeskPRO.User.InlineLoginForm({
			context: this.el
		});
	},

	//#########################################################################
	// Preticket status
	//#########################################################################

	_initPreticketStatus: function() {
		$('input, select', this.ticketForm).on('change', (function() {
			this.updatePreticketStatus();
		}).bind(this));
	},

	updatePreticketStatus: function() {
		var formData = this.ticketForm.serializeArray();

		$.ajax({
			url: BASE_URL + 'tickets/new/save-status',
			type: 'POST',
			data: formData,
			dataType: 'json',
			success: function(data) {
				$('#dp_newticket_preticket_status_id').val(data.preticket_status_id);
			}
		});
	}
});

Orb.createNamespace('DeskPRO.User.ElementHandler');

DeskPRO.User.ElementHandler.FormUploadHandler = new Orb.Class({
	Extends: DeskPRO.User.ElementHandler.ElementHandlerAbstract,

	init: function() {

		var self = this;
		var dropZone = this.el;
		if (this.el.data('drop-document') == '1') {
			dropZone = $(document);
		}

		var options = {
			url: this.el.data('upload-to'),
			dropZone: dropZone,
			autoUpload: true,
			formData: {
				security_token: this.el.data('security-token')
			},
			done: function(e, data) {
				var that = $(this).data('fileupload'),
					template,
					preview;

				if (!that) {
					return;
				}

				if (data.context) {
					data.context.each(function (index) {
						var file = ($.isArray(data.result) &&
								data.result[index]) || {error: 'emptyResult'};
						if (file.error && that._adjustMaxNumberOfFiles) {
							that._adjustMaxNumberOfFiles(1);
						}
						that._transition($(this)).done(
							function () {
								var node = $(this);
								template = that._renderDownload([file])
									.css('height', node.height())
									.replaceAll(node);
								that._forceReflow(template);
								that._transition(template).done(
									function () {
										data.context = $(this);
										that._trigger('completed', e, data);
									}
								);
							}
						);
					});
				} else {
					template = that._renderDownload(data.result)
						.appendTo(that.options.filesContainer);
					that._forceReflow(template);
					that._transition(template).done(
						function () {
							data.context = $(this);
							that._trigger('completed', e, data);
						}
					);
				}

				if (that.options.filesContainer) {
					if (!that.options.filesContainer.find('.uploading')[0]) {
						self.el.trigger('dp_upload_all_done');
					}
				}
			}
		};

		this._handleOptions(options);

		this.el.fileupload(options);

		$('.dp-fallback', this.el).remove();
		$('.dp-good-upload', this.el).show();
	},

	_handleOptions: function(options) {
		var el = this.el;

		if (!options.namespace) {
			options.namespace = Orb.uuid();
		}

		if (!options.dropZone) {
			options.dropZone = $(el);
		}

		if (typeof options.autoUpload == 'undefined') {
			options.autoUpload = true;
		}

		if (options.uploadTemplate) {
			var setel = options.uploadTemplate;
		} else {
			var setel = $('.template-upload', el);
		}
		if (!setel.attr('id')) {
			var id = Orb.getUniqueId('up');
			setel.attr('id', id);
		} else {
			var id = setel.attr('id');
		}
		delete(options.uploadTemplate);
		options.uploadTemplateId = id;

		if (options.downloadTemplate) {
			var setel = options.downloadTemplate;
		} else {
			var setel = $('.template-download', el);
		}
		if (!setel.attr('id')) {
			var id = Orb.getUniqueId('up');
			setel.attr('id', id);
		} else {
			var id = setel.attr('id');
		}
		delete(options.downloadTemplate);
		options.downloadTemplateId = id;

		if (!options.filesContainer) {
			options.filesContainer = $(el).find('.files');
		}

		options.start = function() {
			// Dont stack error messes. Once you upload again, the old one disappears
			$(el).find('.error').remove();
		};

		$(el).on('click', '.remove-attach-trigger', function(ev) {
			// Ignore .delete as they may be items rendered with the page,
			// eg. the list handles delete of existing attachments on its own
			if ($(this).hasClass('delete')) {
				return;
			}
			ev.preventDefault();
			var el = $(this).closest('li');
			el.slideUp('fast', function() {
				el.remove();
			});
		});
	}
});

Orb.createNamespace('DeskPRO.User.ElementHandler');

DeskPRO.User.ElementHandler.TicketList = new Orb.Class({

	Extends: DeskPRO.User.ElementHandler.ElementHandlerAbstract,

	init: function() {
		$('a.close-ticket-trigger').on('click', function(ev) {
			var text = "Once a ticket is closed, our agents will not reply to it anymore. Are you sure you want to close this ticket?";
			if (!confirm(text)) {
				ev.preventDefault();
			}
		});
	}
});

Orb.createNamespace('DeskPRO.User.ElementHandler');

DeskPRO.User.ElementHandler.TicketView = new Orb.Class({

	Extends: DeskPRO.User.ElementHandler.ElementHandlerAbstract,

	init: function() {
		$('.feedback-link', this.el).each(function() {
			var el = $(this);

			$('a', this).on('click', function(ev) {
				ev.preventDefault();

				window.open(
					$(this).attr('href'),
					'feedback',
					"status=no,toolbar=no,location=no,menubar=no,resizable=no,scrollbars=yes,height=310,width=720"
				);
			}).on('mouseover', function() {
				if ($(this).is('.helpful')) {
					el.addClass('rating-helpful').removeClass('rating-not-helpful');
				} else {
					el.removeClass('rating-helpful').addClass('rating-not-helpful');
				}
			}).on('mouseout', function() {
				el.removeClass('rating-helpful').removeClass('rating-not-helpful');
			});
		});
	}
});

Orb.createNamespace('DeskPRO.User.ElementHandler');

DeskPRO.User.ElementHandler.InlineEmailManage = new Orb.Class({

	Extends: DeskPRO.User.ElementHandler.ElementHandlerAbstract,

	init: function() {
		var self = this;
		this.emailField = $('.dp-email-field', this.el);
		this.newEmailField = $('.dp_inline_email_new', this.el);
		this.emailList = $('.dp-email-manage-list', this.el);
		this.controlsEl = $('.dp-email-manage-controls', this.el);
		$('input[name="dp_inline_email_choice"]', this.controlsEl).on('click', function() {
			var val = $(this).val();
			if (val == 'NEW') {
				self.setNewMode();
			} else {
				self.emailField.val(val);
			}
		});

		this.emailError = $('.error-message', this.el);

		this.newEmailField.on('keypress', function(ev) {
			if (ev.keyCode == 13) {
				ev.preventDefault();
				ev.stopPropagation();
			}
		});
		this.newEmailField.on('keyup', function(ev) {
			if (self.mode == 'new') {
				self.emailField.val(self.newEmailField.val());
			}
			self.newEmailField.removeClass('error');
		});

		this.mode = 'normal';

		if ($('input[name="dp_inline_email_choice_radio"]:checked').val() == 'NEW') {
			this.setNewMode();
		}

		$('input[name="dp_inline_email_choice_radio"]').not('[value="NEW"]').on('click', function() {
			self.setNormalMode();
			self.emailField.val($(this).val());
		});

		$('input[name="dp_inline_email_choice_radio"][value="NEW"]').on('click', function() {
			self.emailField.val(self.newEmailField.val());
			self.setNewMode();
		});

		var changeEmail = $('.change-email', this.el).on('click', function(ev) {
			ev.preventDefault();
			self.controlsEl.slideDown('fast');
			changeEmail.hide();
			changeEmailClose.show();
		});
		var changeEmailClose =  $('.change-email-close', this.el).on('click', function(ev) {
			ev.preventDefault();
			if ($('input[name="dp_inline_email_choice_radio"][value="NEW"]').is(':checked')) {
				var email = self.newEmailField.val();
				if (!email || email.indexOf('@') === -1) {
					self.newEmailField.addClass('error').focus();
					return;
				}
			}

			self.controlsEl.slideUp('fast');
			changeEmail.show();
			changeEmailClose.hide();
		});
	},

	setNormalMode: function() {
		this.mode = 'normal';
	},

	setNewMode: function() {
		this.mode = 'new';
		this.emailField.val(this.newEmailField.val());
	}
});

Orb.createNamespace('DeskPRO.User.ElementHandler');

DeskPRO.User.ElementHandler.CommentFormLogin = new Orb.Class({
	Extends: DeskPRO.User.ElementHandler.ElementHandlerAbstract,

	init: function() {
		var self = this;

		window.DP_LOGIN_NOTIFY = function() {
			$('#comments_form_login_area').empty();
			$.ajax({
				url: BASE_URL + '_misc/comment-form-login-partial',
				dataType: 'html',
				success: function(html) {
					$('#comments_form_login_area').html(html);
				}
			});
		};

		this.el.find('.auth-popup').click(function(ev) {
			ev.preventDefault();
			ev.stopPropagation();
			ev.stopImmediatePropagation();

			window.open($(this).attr('href'),'dpauth','width=600,height=400,location=0,menubar=0,scrollbars=0,status=0,toolbar=0,resizable=0');
		});
	}
});

Orb.createNamespace('DeskPRO.User.ElementHandler.Helper');

DeskPRO.User.ElementHandler.FeedbackAgreeBtn = new Orb.Class({

	Extends: DeskPRO.User.ElementHandler.ElementHandlerAbstract,

	init: function() {
		var self = this;

		if (this.el.hasClass('dp-feedback-closed')) {
			return;
		}

		this.el.find('.dp-feedback-btn').on('click', function() {
			self.voteOnElement(self.el);
		});
	},

	voteOnElement: function(el) {
		var rating;
		if (el.is('.dp-voted')) {
			rating = 0;
		} else {
			rating = 1;
		}

		var parentRow = el.closest('.dp-feedback-row');

		var old_num = parseInt($('em', el).first().text());

		if (rating) {
			el.addClass('dp-voted');
			parentRow.addClass('dp-voted-row');
			$('em', el).first().text(old_num+1);
		} else {
			el.removeClass('dp-voted');
			parentRow.removeClass('dp-voted-row');
			$('em', el).first().text(old_num-1);
		}

		$.ajax({
			url: el.data('vote-url'),
			data: { rating: rating },
			dataType: 'json',
			context: this,
			type: 'POST',
			success: function(data) {
				if (data.voted) {
					el.addClass('dp-voted');
					parentRow.addClass('dp-voted-row');
				} else {
					el.removeClass('dp-voted');
					parentRow.removeClass('dp-voted-row');
				}

				$('em', el).first().text(data.total_rating);
			}
		});
	}
});

Orb.createNamespace('DeskPRO.User.ElementHandler');

DeskPRO.User.ElementHandler.OmniSearch = new Orb.Class({

	Extends: DeskPRO.User.ElementHandler.ElementHandlerAbstract,

	init: function() {
		var self = this;

		this.backdrop    = $('<div class="dp-backdrop" />').hide().appendTo('#dp');
		this.assistEl    = $('#dp_search_assist');
		this.searchboxEl = $('#dp_search');

		this.resultsEl   = $('div.results', this.assistEl);
		$('#dp_search_assist').on('click', function() {
			$('#dp_omnisearch').submit();
		});

		this.searchboxEl.on('focus', this.activateAssist.bind(this));

		this.backdrop.on('click', function(ev) {
			ev.stopPropagation();
			self.deactivateAssist();
		});

		this.isActivated = false;

		this.searchTimer = new DeskPRO.IntervalCaller({
			touchResets: true,
			touchRequired: true,
			resetTimeForce: 1500,
			timeout: 750,
			autostart: true,
			callback: this.updateResults.bind(this)
		});

		this.searchboxEl.on('keyup', function(ev) {
			if (!$(this).val().trim().length) {
				self.close();
			} else {
				self.activateAssist();
				if (ev.keyCode == '32') {
					self.searchTimer.exec(true);
				} else {
					self.searchTimer.touch();
				}
			}
		});

		this.searchboxEl.on('focus', function() {
			self.activateAssist();
		});

		this.lastTerms = null;

		$('.foot a', this.assistEl).on('click', function(ev) {
			var el = $(this);
			if (el.is('.no-omni-trigger') || el.closest('li').is('.no-omni-trigger')) {
				return;
			}

			ev.preventDefault();
			ev.stopPropagation();

			var url = el.attr('href');
			url = Orb.appendQueryData(url, 'q', self.searchboxEl.val().trim());

			window.location = url;
		});
	},

	activateAssist: function() {
		var wasActive = this.isActivated;

		this.isActivated = true;
		this.updatePosition();

		if (!this.searchboxEl.val().trim().length) {
			this.deactivateAssist();
		} else {
			this.open();
			if (!wasActive) {
				this.searchTimer.execNow();
			}
		}
	},

	open: function() {
		this.assistEl.show();
		this.backdrop.show();
	},

	deactivateAssist: function() {
		this.isActivated = false;
		this.close();
	},

	close: function() {
		this.assistEl.hide();
		this.backdrop.hide();
	},

	updatePosition: function() {
		var pos = this.searchboxEl.offset();

		var w = this.searchboxEl.outerWidth();
		var h = this.searchboxEl.outerHeight();

		this.assistEl.css({
			top: pos.top + h + 1,
			left: pos.left,
			width: w - 1
		});
	},

	updateResults: function() {
		if (!this.isActivated) {
			return;
		}

		var terms = this.searchboxEl.val().trim();

		if (terms == this.lastTerms || terms === '') {
			return;
		}

		this.lastTerms = terms;

		this.searchboxEl.addClass('loading');
		$.ajax({
			url: BASE_URL + 'search/omnisearch/' + encodeURI(terms),
			dataType: 'html',
			context: this,
			complete: function() {
				this.searchboxEl.removeClass('loading');
			},
			success: function(html) {
				if (html.indexOf('<body') !== -1 || html.indexOf('<html') !== -1) {
					// If its a full document then its not a proper
					// response. (e.g., was a redirect to login)
					return;
				}

				var wrap = $(html);
				this.resultsEl.empty();

				if (!$('li', wrap).length) {

				} else {
					this.resultsEl.append(wrap);

					if (this.resultsEl.find('li').length >= 10) {
						$('#dp_search_assist').find('.dp-more-link').show();
					} else {
						$('#dp_search_assist').find('.dp-more-link').hide();
					}

					this.open();
				}
			}
		});
	}
});
Orb.createNamespace('DeskPRO.User.ElementHandler');

DeskPRO.User.SuggestedContentOverlay = new Orb.Class({
	Implements: [Orb.Util.Options, Orb.Util.Events],

	initialize: function(options) {
		this.options = {
			template: null,

			url: null,
			pageUrl: null,

			contentType: null,
			contentId: null,

			destroyOnClose: true,

			maxHeight: 700,
			openNear: null
		};

		this.setOptions(options);

		this.overlayEl = null;
		this.backdropEl = null;
		this.runningAjax = false;
	},

	_initOverlay: function() {
		if (this._hasInit) return;
		this._hasInit = true;

		var self = this;

		this.overlayEl = $(this.options.template).hide().appendTo('#dp');
		this.controlsWrap = $('.dp-controls', this.overlayEl).hide();
		this.backdropEl = $('<div class="dp-backdrop dp-faded" />').appendTo('#dp');
		this.backdropEl.on('click', function(el) {
			self.close();
		});

		$('.dp-close-btn', this.overlayEl).on('click', function(ev) {
			ev.preventDefault();
			self.close();
		});

		this.runningAjax = $.ajax({
			url: this.options.url,
			type: 'GET',
			context: this,
			error: function() {
				this.close();
			},
			success: function(html) {
				this.controlsWrap.show();
				$('.dp-content-holder', this.overlayEl).empty().html(html);

				$('.dp-section-toggle', this.controlsWrap).on('click', function(ev) {
					ev.preventDefault();
					var toggleSel = $(this).data('toggle-section');
					$('.dp-control-section', self.controlsWrap).fadeOut('fast', function() {
						window.setTimeout(function() {
							$(toggleSel, self.controlsWrap).fadeIn();
						}, 150);
					});
				});

				$('.dp-toggle-sel', this.overlayEl).on('click', function(ev) {
					ev.preventDefault();
					var el = $($(this).data('toggle-sel'), self.overlayEl);

					if ($(this).is('.open')) {
						$(this).removeClass('open');
						el.slideUp();
					} else {
						if ($(this).data('toggle-self')) {
							$(this).slideUp('fast', function() {
								el.slideDown();
							}).addClass('open');
						} else {
							el.slideDown();
							$(this).addClass('open');
						}
					}
				});

				if (self.options.pageUrl) {
					$('.dp-open-full', this.overlayEl).on('click', function(ev) {
						ev.preventDefault();
						window.open(self.options.pageUrl);
						self.close();
					});

					$('.dp-open-full a', this.overlayEl).attr('href', self.options.pageUrl);
				}

				this.fireEvent('init', [this.overlayEl, this.controlsEl, this]);
			}
		});
	},

	open: function() {
		this._initOverlay();

		var pos = {
			top: 40,
			left: 100,
			width: null
		};

		this.fireEvent('preOpen', [pos, this]);
		if (pos.cancel) {
			return;
		}

		var h = this.overlayEl.height();
		var w = this.overlayEl.width();

		var winH = $(window).height();
		var winW = $(window).width();
		h = winH * 0.7;
		w = winW * 0.6;

		if (h < 250) {
			h = 250;
		}
		if (w < 400) {
			w = 400;
		}

		if (h > this.options.maxHeight) {
			h = this.options.maxHeight;
		}

		this.overlayEl.css('height', h);
		this.overlayEl.css('width', w);

		this.overlayEl.css({
			top: (winH / 2) - (h / 2),
			left: (winW / 2) - (w / 2)
		});

		this.overlayEl.fadeIn('fast').addClass('open');
		this.backdropEl.show();
	},

	close: function() {
		if (!this._hasInit) return;
		if (!this.overlayEl.is('.open')) {
			return;
		}

		this.overlayEl.fadeOut('fast', (function() {
			if (this.options.destroyOnClose) {
				this.destroy();
			}
		}).bind(this));
		this.backdropEl.hide();
	},

	destroy: function() {
		if (this._hasInit) return;
		this.overlayEl.remove();
		this.backdropEl.remove();

		this.overlayEl = null;
		this.backdropEl = null;

		if (this.runningAjax) {
			this.runningAjax.abort();
			this.runningAjax = null;
		}
	}
});

Orb.createNamespace('DeskPRO.User.ElementHandler');

DeskPRO.User.InlineSuggestions = new Orb.Class({
	Implements: [Orb.Util.Options, Orb.Util.Events],

	initialize: function(options) {
		this.options = {
			elementWrapper: null,
			titleText: '#__dp_nomatch',
			contentText: '#__dp_nomatch'
		};
		this.setOptions(options);

		this.el = $(this.options.elementWrapper);
		this.titleTxt = $(this.options.titleText);
		this.messageTxt = $(this.options.contentText);

		this._initSuggestionsBox();
	},

	_initSuggestionsBox: function() {
		this.suggestionsBox = $('.dp-related-search', this.el);
		this.resultsEl = $('.results', this.suggestionsBox);
		this.moreLink = $('.more-link', this.suggestionsBox);
		this.lastSuggestions = null;
		this.lastString = null;
		this.notAnsweredResults = [];

		this.hasStartedSearch = false;

		this.moreLink.on('click', (function(ev) {
			this.moreLink.hide();
			$('li', this.resultsEl).show();
		}).bind(this));

		this.suggestionsUrl = this.el.data('suggestions-url');

		this.sugTitleTimer = null;
		this.sugMessageTimer = null;

		this.titleTxt.on('keypress', (function() {
			if (!this.hasStartedSearch) return;
			if (this.sugTitleTimer) return;
			this.sugTitleTimer = this.updateSuggestions.delay(400, this);
		}).bind(this));

		this.titleTxt.on('blur', (function() {
			this.hasStartedSearch = true;
			this.updateSuggestions();
		}).bind(this));

		this.messageTxt.on('keypress', (function() {
			if (!this.hasStartedSearch) return;
			if (this.sugMessageTimer) return;
			this.sugMessageTimer = this.updateSuggestions.delay(1200, this);
		}).bind(this));
	},

	updateSuggestions: function() {

		if (this.sugTitleTimer) {
			window.clearTimeout(this.sugTitleTimer);
			this.sugTitleTimer = null;
		}
		if (this.sugMessageTimer) {
			window.clearTimeout(this.sugMessageTimer);
			this.sugMessageTimer = null;
		}

		var content = (this.titleTxt.val().trim() + ' ' + this.messageTxt.val().trim()).trim();

		if (this.lastSearchString && this.lastSearchString == content) {
			return;
		}

		this.lastSearchString = content;

		if (!content.length) {
			this.suggestionsBox.hide();
			return;
		}

		// Already set to repeat
		if (this.doSuggestResend) {
			return;
		}

		if (this.isSuggestActive) {
			this.doSuggestResend = true;
			return;
		}

		this.isSuggestActive = true;

		$.ajax({
			url: this.suggestionsUrl,
			dataType: 'html',
			data: {'content': content},
			context: this,
			success: function(html) {
				this.isSuggestActive = false;

				if (this.doSuggestResend) {
					this.doSuggestResend = false;
					this.updateSuggestions();
				}

				if (this.lastSuggestions && this.lastSuggestions == html) {
					return;
				}

				this.lastSuggestions = html;

				this.resultsEl.empty().html(html);

				// Make sure unsolved results dont reappear
				if (this.notAnsweredResults.length) {
					var x;
					for (x = 0; x < this.notAnsweredResults.length; x++) {
						$('li.' + this.notAnsweredResults[x], this.resultsEl).remove();
					}
				}

				if (!$('li:first', this.resultsEl).length) {
					this.suggestionsBox.hide();
					this.lastSuggestions = null;
				} else {

					if (!this.moreLink.data('has-mored')) {
						this.moreLink.data('has-mored', true);
						var count = $('li', this.resultsEl).length;

						if (count > 6) {
							var remainCount = count - 6;
							$('.count', this.moreLink).text(remainCount);
							this.moreLink.show();

							$('li', this.resultsEl).slice(5).hide();
						} else {
							this.moreLink.hide();
						}
					} else {
						this.moreLink.hide();
					}

					var self = this;
					$('li a[href]', this.suggestionsBox).on('click', function(ev) {
						ev.preventDefault();
						self.openSuggestedContent($(this));
					});

					this.suggestionsBox.show();
				}
			}
		});
	},

	openSuggestedContent: function(aEl) {

		var origUrl = aEl.attr('href');
		var url = Orb.appendQueryData(origUrl, '_partial', 'overlaySuggest');
		var contentType = aEl.data('content-type');
		var contentId = aEl.data('content-id');
		var self = this;

		var tplEl = $('.related-content-overlay-tpl', this.el);

		if (!tplEl[0]) {
			window.open(origUrl);
			return;
		}

		var overlay = new DeskPRO.User.SuggestedContentOverlay({
			template: tplEl.get(0).innerHTML,
			url: url,
			pageUrl: origUrl,
			contentType: aEl.data('content-type'),
			contentId: aEl.data('content-id'),
			destroyOnClose: true,
			openNear: $('.dp-related-search', this.el),
			onInit: (function(overlayEl, controls, overlay) {
				// As soon as they click we subimt the request to record it
				$('.dp-set-answered', controls).on('click', function(ev) {
					ev.preventDefault();
					self.fireEvent('resolved', [contentType, contentId, true]);
				});

				// But we still send them through the redirect, so they
				// can visit the article quickly without waiting for the save to return
				$('.dp-answererd', controls).on('click', function(ev) {
					ev.preventDefault();
					var type = $(this).data('type');

					if (type == 'close') {
						self.fireEvent('resolvedRedirect', [origUrl, contentType, contentId, this]);
					} else {
						overlay.close();
					}
				});

				$('.dp-not-answered', controls).on('click', function(ev) {
					ev.preventDefault();
					self.fireEvent('notResolved', [contentType, contentId, true]);

					aEl.parent().addClass('not-answered');
					self.notAnsweredResults.push(contentType + '-' + contentId);
					overlay.close();
				});
			}).bind(this)
		});
		overlay.open();
	}
});

Orb.createNamespace('DeskPRO.User.ElementHandler');

DeskPRO.User.InlineLoginForm = new Orb.Class({
	Implements: [Orb.Util.Options, Orb.Util.Events],

	initialize: function(options) {
		var self = this;

		this.options = {
			emailSel: '#dp_inline_login_email',
			passwordSel: '#dp_inline_login_pass',
			context: null
		};
		this.setOptions(options);

		this.context = this.options.context || document;

		this._initLoginForm($('.dp-inline-login', this.context));
	},

	_initLoginForm: function(wrapper) {
		var self = this;

		this.el = wrapper;
		this.loginWrapper    = wrapper;
		this.passwordRow     = $('.dp-inline-login-pass', wrapper);
		this.nonloginWrapper = $('.dp-inline-non-login', this.context);
		this.loginBtn        = $('.dp-login-trigger', wrapper);

		this.loginSection = $('.dp-login-section', this.el);
		this.resetSection = $('.dp-reset-section', this.el);

		$('.dp-inline-login-open', wrapper).on('click', (function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			if (this.loginWrapper.is('.open')) {
				this.closeLogin();
			} else {
				this.openLogin();
			}
		}).bind(this));

		this.loginBtn.on('click', (function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			this.processLogin();
		}).bind(this));

		$(this.options.passwordSel, wrapper).on('keypress', function(ev) {
			if (ev.keyCode == 13) {
				ev.preventDefault();

				if (self.isOpen()) {
					self.processLogin();
				}
			}
		});
		$(this.options.emailSel, wrapper).on('keypress', function(ev) {
			if (ev.keyCode == 13) {
				ev.preventDefault();

				if (self.isOpen()) {
					self.processLogin();
				}
			}
		});

		this._initResetSection();
	},

	isOpen: function() {
		return this.loginWrapper.is('.open');
	},

	openLogin: function() {
		this.loginWrapper.addClass('open');
		this.passwordRow.slideDown('fast');
		this.nonloginWrapper.animate({ opacity: '0.4', duration: 'fast' });
	},

	closeLogin: function() {
		this.passwordRow.slideUp('fast', (function() {
			this.loginWrapper.removeClass('open');
		}).bind(this));
		this.nonloginWrapper.animate({ opacity: '1', duration: 'fast' });
	},

	processLogin: function() {
		var postData = [];
		postData.push({
			name: 'email',
			value: $(this.options.emailSel, this.context).val()
		});
		postData.push({
			name: 'password',
			value: $(this.options.passwordSel, this.context).val()
		});

		if (this.loginBtn.is('.mode-advanced')) {
			postData.push({
				name: 'mode',
				value: 'advanced'
			});
		}

		$.ajax({
			url: BASE_URL + 'login/inline-login',
			type: 'POST',
			data: postData,
			dataType: 'json',
			context: this,
			success: function(data) {
				var newEl = $(data.html);
				if (data.person_id) {
					DeskPRO_Window.initFeatures(newEl);
					$('#dp_inline_login_row').replaceWith(newEl);
					this.nonloginWrapper.css({ opacity: '1'});
				} else {
					$('#dp_inline_login_row').replaceWith(newEl);
					$('.dp-inline-login-pass', newEl).show();
				}

				if (data.sections_replace) {
					Object.each(data.sections_replace, function(html, id) {
						$('#' + id).empty().replaceWith(html);
					});
				}

				this._initLoginForm(newEl);

				this.fireEvent('success', [data, this]);
			}
		})
	},

	//#########################################################################
	//# Reset Stuff
	//#########################################################################

	_initResetSection: function() {
		$('.forgot', this.el).on('click', (function(ev) {
			ev.preventDefault();
			this.showReset();
		}).bind(this));

		$('.back', this.resetSection).on('click', (function(ev) {
			this.hideReset();
		}).bind(this));

		$('.dp-do-send', this.resetSection).on('click', (function(ev) {
			ev.preventDefault();
			this.sendReset();
		}).bind(this));
	},

	sendReset: function() {

		this.resetSection.addClass('loading');

		$.ajax({
			url: BASE_URL + 'login/reset-password/send',
			type: 'POST',
			data: {
				email: $(this.options.emailSel, this.el).val()
			},
			dataType: 'json',
			context: this,
			success: function() {
				this.resetSection.removeClass('loading');

				var descEl = $('.dp-reset-desc', this.resetSection);
				var sentEl = $('.dp-reset-sent', this.resetSection);

				descEl.slideUp('fast', function() {
					sentEl.slideDown();
				});
			}
		});
	},

	showReset: function() {
		this.loginSection.slideUp('fast', (function() {
			this.resetSection.slideDown('fast');
		}).bind(this));
	},

	hideReset: function(quick) {
		if (quick) {
			this.resetSection.hide();
			this.loginSection.show();
			$('.dp-reset-desc', this.resetSection).show();
			$('.dp-reset-sent', this.resetSection).hide();
		} else {
			this.resetSection.slideUp('fast', (function() {
				this.loginSection.slideDown('fast');

				// Also reset view on others
				$('.dp-reset-desc', this.resetSection).show();
				$('.dp-reset-sent', this.resetSection).hide();
			}).bind(this));
		}
	}
});

Orb.createNamespace('DeskPRO.Form');

DeskPRO.Form.FormValidator = new Orb.Class({
	Implements: [Orb.Util.Events],

	initialize: function(el) {
		var self = this;
		this.el = $(el);

		var attachWaitMessage = this.el.find('.attach-is-loading-warn');
		var submitting = false;

		if (this.el.is('form')) {
			this.el.on('submit', function(ev) {

				if (submitting) {
					ev.preventDefault();
					return;
				}

				$('.dp-error').removeClass('dp-error');
				self.validateAll();
				if (self.hasErrors()) {
					var debugbox = $('#debug_box');
					if (debugbox[0]) {
						var append = ["Fields with errors:"];
						$('.dp-error').each(function() {
							var field = $(this).find('input, textarea, select').first();
							append.push(field.attr('name') || field.attr('id') || field.val());
						});
						debugbox.text(debugbox.text() + "\n" + append.join("\n"));
					}
					ev.preventDefault();
				} else {

					var isUploading = false;
					self.el.find('.form-upload-section').each(function() {
						if ($(this).find('.uploading')[0]) {
							isUploading = true;
							$(this).one('dp_upload_all_done', function(ev) {
								attachWaitMessage.hide();
								self.el.submit();
							});
							return false;
						}
					});

					if (isUploading) {
						ev.preventDefault();
						attachWaitMessage.show();

					} else {
						submitting = true;
					}
				}
			});

			$('[required]', self.el).each(function() {
				$(this).attr('required', false);
			});
		}

		this.refreshElements();
	},

	refreshElements: function() {
		var el = this.el;
		this.formElements = $('[data-field-validators]', this.el).each(function() {
			var names, i, validators, className, classObj;

			names = $(this).data('field-validators').split(',');

			validators = [];
			for (i = 0; i < names.length; i++) {

				if ($(this).data('field-validators-inst')) {
					Array.each($(this).data('field-validators-inst'), function(v) {
						v.destroy();
					});
					$(this).data('field-validators-inst', null);
				}

				var className = names[i].trim();
				var classObj = Orb.getNamespacedObject(className);

				if (!classObj) {
					DP.console.error("Unknown form validator `%s` on element %o", className, this);
					continue;
				}

				var obj = new classObj($(this));
				validators.push(obj);
			}

			if (validators.length) {
				$(this).data('field-validators-inst', validators);
			}
		});
	},

	validateAll: function() {
		this.el.find('.dp-error.dp-error-static').removeClass('dp-error');

		this.formElements.each(function() {

			var validators, i;

			if (!$(this).is(':visible')) {
				return;
			}

			validators = $(this).data('field-validators-inst');

			for (i = 0; i < validators.length; i++) {
				validators[i].validate('submit');
			}
		});
	},

	hasErrors: function() {
		if ($('.dp-error', this.el).filter(':visible').length > 0) {
			return true;
		}

		return false;
	}
});

Orb.createNamespace('DeskPRO.Form');

DeskPRO.Form.FieldValidator = new Orb.Class({
	Implements: [Orb.Util.Events],

	initialize: function(el) {
		this.el = $(el);

		if (this.el.data('val-wrap-sel')) {
			this.wrapper = this.el.closest(this.el.data('val-wrap-sel'));
		} else {
			this.wrapper = this.el.closest('.dp-form-row, .dp-control-group').first();
		}

		this.init();
	},

	init: function() { },

	setErrorCodes: function(codes) {
		console.log("Errors on %o: %o", this, codes);
		this.wrapper.removeClass('dp-error-' + this._getMyErrorCodes().join(' dp-error-'));

		if (codes && codes.length) {
			if (typeof codes == 'string') {
				codes = [codes];
			}
			this.wrapper.addClass('dp-error-' + codes.join(' dp-error-'));

			this.wrapper.addClass('dp-error');

		// None of our errors, remove dp-form-error
		// if we know no others are added
		} else {
			if (!this.wrapper.is('[class*="dp-error-"]')) {
				this.wrapper.removeClass('dp-error');
			}
		}
	},

	/**
	 * Checks to see if error state is currently on
	 */
	hasError: function() {
		if (this.wrapper.is('.dp-error-' + this._getMyErrorCodes().join(', .dp-error-'))) {
			return true;
		}

		return false;
	},

	/**
	 * Runs the validator on the class. This doesnt return a value, but should
	 * set the proper classes on the wrapper so that hasErorr() properly runs.
	 */
	validate: function(action) {
		// Override in subclass
	},

	/**
	 * Returns an array of possible error codes this class adds
	 */
	_getMyErrorCodes: function() {
		// Override in subclass
	},

	destroy: function() {

	}
});

Orb.createNamespace('DeskPRO.Form');

DeskPRO.Form.LengthValidator = new Orb.Class({
	Extends: DeskPRO.Form.FieldValidator,

	init: function() {
		var self = this;

		this.min = 1;
		this.max = -1;

		// used with selects to exclude blank selections
		this.excludeBlank = true;

		if (this.el.data('min-len')) {
			this.min = parseInt(this.el.data('min-len'));
		}
		if (this.el.data('max-len')) {
			this.max = parseInt(this.el.data('max-len'));
		}

		if (this.el.data('exclude-blank')) {
			this.excludeBlank = parseInt(this.el.data('exclude-blank'));
			this.excludeBlank = this.excludeBlank ? true : false;
		}

		this.el.on('change', function() {
			self.validate();
		});
	},

	validate: function(action) {
		var len = 0;

		if (this.el.is('select')) {
			if (this.excludeBlank) {
				$('option:selected', this.el).each(function() {
					if ($(this).val() != '0' && $(this).val().trim() !== '') {
						len++;
					}
				});
			} else {
				len = $('option:selected', this.el).length;
			}

		} else {
			len = this.el.val().trim().length;
		}

		var errorCodes = [];

		if (this.min > -1 && len < this.min) {
			errorCodes.push('len_too_short');
		}
		if (this.max > -1 && len > this.max) {
			errorCodes.push('len_too_long');
		}

		this.setErrorCodes(errorCodes);
	},

	_getMyErrorCodes: function() {
		return ['len_too_long', 'len_too_short'];
	}
});

Orb.createNamespace('DeskPRO.Form');

DeskPRO.Form.EmailValidator = new Orb.Class({
	Extends: DeskPRO.Form.FieldValidator,

	init: function() {
		var self = this;

		this.el.on('change', function() {
			self.validate('change');
		});
	},

	validate: function(action) {
		var len = 0;
		var val = this.el.val().trim();

		if (val.length < 3 || val.indexOf('@') < 1) {
			this.setErrorCodes(['invalid_email']);
		} else {
			this.setErrorCodes([]);
		}
	},

	_getMyErrorCodes: function() {
		return ['invalid_email'];
	}
});

Orb.createNamespace('DeskPRO.Form');

DeskPRO.Form.RegexValidator = new Orb.Class({
	Extends: DeskPRO.Form.FieldValidator,

	init: function() {
		var self = this;

		this.el.on('change', function() {
			self.validate('change');
		});
	},

	validate: function(action) {
		var len = 0;
		var val = this.el.val().trim();

		if (!this.regex) {
			if (this.el.data('regex-modifiers')) {
				this.regex = new RegExp(this.el.data('regex'), this.el.data('regex-modifiers'));
			} else {
				this.regex = new RegExp(this.el.data('regex'));
			}
		}

		if (!this.regex.test(val)) {
			this.setErrorCodes(['pattern_no_match']);
		} else {
			this.setErrorCodes([]);
		}
	},

	_getMyErrorCodes: function() {
		return ['pattern_no_match'];
	}
});

Orb.createNamespace('DeskPRO.Form');

DeskPRO.Form.TwoLevelSelectValidator = new Orb.Class({
	Extends: DeskPRO.Form.FieldValidator,

	init: function() {
		var self = this;

		this.parentSel = $('select.parent-option', this.el);
		this.childSel = $('.dp-sub-options > select', this.el);

		this.parentSel.on('change', function() {
			self.validate('change');
		});
		this.childSel.on('change', function() {
			self.validate('change');
		});
	},

	validate: function(action) {

		// Dont trigger errors when simply changing values,
		// because youd see the red box when selecting a new parent
		// that has children. So just show errors on submit
		if (action == 'change' && !this.hasError()) {
			return;
		}

		var errorCodes = [];

		var visibleChild = this.childSel.filter(':visible');
		if (visibleChild.length) {
			var val = $('option:selected', visibleChild).val();
			if (val === '' || val == '0') {
				errorCodes.push('select_child_value');
			}
		} else {
			var val = $('option:selected', this.parentSel).val();
			if (val === '' || val == '0') {
				errorCodes.push('select_value');
			}
		}

		this.setErrorCodes(errorCodes);
	},

	_getMyErrorCodes: function() {
		return ['select_value', 'select_child_value'];
	}
});
