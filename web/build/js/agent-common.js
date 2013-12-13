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

var DpErrorLog = {
	saveUrl: null,
	hasSentReport: true,
	logCount: 0,
	init: function() {
		if (!this.saveUrl) {
			return;
		}

		if (window.jQuery && window.jQuery.cookie) {
			if ($.cookie('dp_jse_report')) {
				this.hasSentReport = true;
			}
		}
	},

	logError: function(message, trace, script, line) {

		if (window.DP_LOADED_TIME) {
			var timeUsing = ((new Date()).getTime() / 1000) - window.DP_LOADED_TIME;
		} else {
			var timeUsing = 0;
		}

		if (!message || message == 'false' || message.indexOf('Error connecting to extension') !== -1 || message.indexOf('flashBridge') !== -1) {
			return;
		}

		// Some errors made by browser extensions that we catch
		if (message.indexOf('Automation server') !== -1) {
			return;
		}

		if (trace && trace == '?() in :0') {
			// not a usefu message
			return;
		}

		if (!line || line == 0 || line === '0') {
			return;
		}

		// Scripts of resource:// are extensions, so we dont want to log those
		if (script && script.indexOf('resource://') === 0) {
			return;
		}

		if (parseInt(line) == 1 && script.indexOf('/agent/') != -1) {
			return;
		}

		// Send max 5 per session
		if (this.logCount++ > 5) {
			return;
		}

		message += ' (timeUsing: ' + timeUsing + ')';

		var data = {
			message: message || '',
			trace:   trace   || '',
			script:  script  || '',
			line:    line    || '0'
		};

		if (this.saveUrl) {

			message = message+'';

			if (ASSETS_BASE_URL) {
				var r = new RegExp(ASSETS_BASE_URL.escapeRegExp(), 'g');
				message = message.replace(r, '');
			}

			if (data.script.indexOf('#app.') !== -1) {
				data.script = data.script.replace(/#.*$/, '');
			}

			$.ajax({
				url: this.saveUrl,
				data: data,
				error: function() { },// prevents DeskPRO_Window's global error handler from firing on error
				type: 'POST'
			});
		}

		// AJAX/network errors shouldnt popup the report notice
		// They're bubbled through the handlers for Cloud so we get reports,
		// but we dont want the feedback window coming up as well
		if (message.indexOf('AJAX Error') !== -1) {
			return;
		}

		if (window.SEND_FEEDBACK_WINDOW && !this.hasSentReport) {
			this.hasSentReport = true;

			if (window.jQuery && window.jQuery.cookie) {
				$.cookie('dp_jse_report', '1', { expires: 1 });
			}

			window.SEND_FEEDBACK_WINDOW.open(
				"We have detected a browser Javascript error that may prevent the interface from functioning properly. " +
				"To help us identify and fix the problem, we would appreciate it if you could describe what you were viewing " +
				"and the actions you were performing just before this notice appeared.",

				"Message: " + data.message + "\nScript: " + data.script + "\nLine:" + data.line + "\nTrace:" + data.trace + "\nUser Agent: " + navigator.userAgent,

				true
			);
		}
	},
};
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

Orb.Util.EventObj = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(options) {
		this.setOptions(options);
	}
});

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

Orb.createNamespace('Orb.Util');

Orb.Util.CallQueue = new Orb.Class({
	initialize: function(options) {
		this.queue  = [];
		this.length = 0;
		this.hasFiredStart = false;
		this.startCallback = options.startCallback || null;
		this.endCallback   = options.endCallback   || null;
	},
	setStartCallback: function(startCallback) {
		this.startCallback = startCallback;
	},
	setEndCallback: function(endCallback) {
		this.endCallback = endCallback;
	},
	addCall: function(fn, bind) {
		this.queue.push([fn, bind || null]);
		this.length++;
	},
	call: function(fn, bind) {
		this.queue.push([fn, bind || null]);
		this.length++;

		this.next();
	},
	hasNext: function() {
		return this.queue.length;
	},
	next: function() {
		var next = this.queue.shift();

		if (!next) {
			this.hasFiredStart = false;
			if (this.endCallback) {
				this.endCallback();
			}
			return;
		}

		this.length--;

		if (!this.hasFiredStart) {
			this.hasFiredStart = true;
			if (this.startCallback) {
				this.startCallback();
			}
		}

		var fn = next[0];
		var bind = next[1];

		if (!bind) {
			bind = fn;
		}

		return fn.call(bind);
	}
});

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

/**
 * Go through the dom tree and attach element handlers on an object.
 *
 * @param jQuery context Context. Defaults to the entire document.
 */
DeskPRO.ElementHandler_Exec = function(context) {
	$('[data-element-handler]', context || document).not('.with-handler').each(function() {
		var el = $(this);
		var className = el.data('element-handler');
		var classObj = Orb.getNamespacedObject(className);

		if (!classObj) {
			DP.console.error("Unknown element handler `%s` on element %o", className, this);
			return;
		}

		if (!el.attr('id')) {
			el.attr('id', Orb.getUniqueId('dp_'));
		}

		try {
			var obj = new classObj(el);
			el.addClass('with-handler');
		} catch (e) {
			console.error("Failed created element-handler (%o) : %o", className, e);
		}
	});
};

/**
 * An element handler takes a single element, usually a central wrapper element
 * of some kind, and adds functionality to it. Generally handlers are defined in
 * HTML source with data-element-handler="xxx" and attached at runtime.
 *
 * A handler can optionally register itself with a parent elements handler by defining
 * a selector with data-register-handler="li.parent" (we'll walk up the DOM tree until its found).
 * This way a parent might have code that can give the child handler options or other necessary connections.
 */
DeskPRO.ElementHandler = new Orb.Class({

	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(el) {

		this.el = el;
		this.options = {};
		this.childHandlers = {};
		this.parentHandlerElement = null;

		this.init();

		this.el.data('handler', this);

		var initNow = true;

		if (this.el.data('register-handler')) {

			var sel = this.el.data('register-handler');
			if (sel == "1" || !sel.length || sel == "yes" || sel == "true") {
				sel = '.with-handler';
			}

			var parentHandlerEl = this.el.closest(sel);
			if (parentHandlerEl.length) {
				if (parentHandlerEl.data('handler')) {
					this.parentHandlerElement = parentHandlerEl;
					parentHandlerEl.data('handler')._registerChildHandler(this.el);
				} else {
					DP.console.error('Parent handler element %s has no handler object on element %o and handler %o', sel, this.el, this);
				}
				initNow = false;
			} else {
				DP.console.error('Unknown parent handler element %s on element %o and handler %o', sel, this.el, this);
			}
		}

		if (initNow) {
			this.initPage();
		}
	},

	/**
	 * Called when the object is first initiated.
	 */
	init: function() {

	},

	/**
	 * Called when the object is ready to affect the DOM. This is after pageready, and after
	 * any parents have called back to set options.
	 */
	initPage: function() {

	},


	/**
	 * Register a child handler
	 *
	 * @param el
	 */
	_registerChildHandler: function(el) {
		this.childHandlers[el.attr('id')] = el;

		var ret = this.registerChildHandler(el.data('handler'), el.data('handler').getHandlerName(), el) || {};
		this.fireEvent('childHandler', [el, ret, this]);

		el.data('handler').setParentReturnOptions(ret);
	},


	/**
	 * Register a child handler and return options to it.
	 *
	 * @param handler
	 * @param handlerName
	 * @param el
	 * @return {Object}
	 */
	registerChildHandler: function(handler, handlerName, el) {
		return {};
	},


	/**
	 * When this handler is registered with a parent, and the parent returns
	 * data, its passed to this function for the handler to process it.
	 *
	 * @param ret
	 */
	setParentReturnOptions: function(ret) {
		this.setOptions(ret);
		this.fireEvent('parentReturn', [ret, this.parentHandlerElement, this]);

		this.initPage();
	},


	/**
	 * Get the handler name/type. This is mainly use with registering a handler with a parent.
	 * If the parent knows the type, then it might call back the child to return data.
	 */
	getHandlerName: function() {
		return 'element_handler';
	}
});

Orb.createNamespace('DeskPRO.Agent.PageHelper');

DeskPRO.ElementHandler.ListRadio = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var self = this;

		this.list = $('ul, ol', this.el).first();
		this.list.on('click', 'li', function() {
			$('li', self.list).removeClass('on');
			$(this).addClass('on');

			self.el.trigger('listradiochange', [$(this).data('value'), $(this), this]);
		});
	}
});

Orb.createNamespace('DeskPRO.Agent.PageHelper');

DeskPRO.ElementHandler.SimpleTabs = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var triggerElements, activeClassname = 'on';
		if (this.el.data('trigger-elements')) {
			triggerElements = $(this.el.data('trigger-elements'), this.el);
		} else {
			if (this.el.is('ul')) {
				triggerElements = this.el.find('> li');
			} else {
				triggerElements = this.el.find('ul').first().find('> li');
			}
		}

		if (this.el.data('active-classname')) {
			activeClassname = this.el.data('active-classname');
		}

		this.simpleTabs = new DeskPRO.UI.SimpleTabs({
			triggerElements: triggerElements,
			activeClassname: activeClassname
		});

		this.el.data('simpletabs', this.simpleTabs);
	}
});

Orb.createNamespace('DeskPRO.Agent.PageHelper');

DeskPRO.ElementHandler.CheckboxToggle = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var self = this;
		var targets = $(this.el.data('targets'));
		var clearTargets = this.el.data('clear-targets');

		if (!targets.length) return;

		var checkFn = function() {
			if (self.el.is(':checked')) {
				targets.show();
			} else {
				targets.hide();

				if (clearTargets) {
					targets.find('input[type="text"], input[type="password"], textarea').val('');
				}
			}
		}

		this.el.on('click', function() {
			checkFn();
		});

		checkFn();
	}
});

Orb.createNamespace('DeskPRO.Agent.PageHelper');

DeskPRO.ElementHandler.CheckboxCallUrl = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var self = this;
		var url = this.el.data('url');
		var prop = this.el.data('state-property');

		this.el.on('click', function() {
			$.ajax({
				url: url,
				type: 'POST',
				dataType: 'json',
				success: function(data) {
					// The server returned a state with the request, so make sure
					// the toggle is correctly set now
					if (prop) {
						var checked = !!data[prop];
						self.el.attr('checked', !!data[prop]);
					}
				}
			});
		});
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
 * Finds words in text and wraps them in a span
 */
DeskPRO.WordHighlighter = {
	highlight: function(node, words, excluseStopwords, onlyFirst) {

		var i, w;

		// We need the longest words to process first or they'll be passed up in favour of shorter guys
		words.sort(function(a, b) {
			if (a.length > b.length) {
				return -1;
			} else {
				return 1;
			}
		});

		if (excluseStopwords) {
			words = words.filter(function(w) {
				return (DeskPRO.WordHighlighter.stopWords.indexOf(w) === -1);
			});
		}

		if (!words.length) {
			return [];
		}

		// Build a list of words we know are actually in the text
		var text = $(node).text().toLowerCase();
		var useWords = [];
		for (i = 0; i < words.length; i++) {
			var w = words[i].toLowerCase();
			if (!w || !w.length) {
				continue;
			}
			if (text.indexOf(w) !== -1) {
				useWords.push(w);
			}
		}

		if (!useWords.length) {
			return [];
		}

		var addedNodes = [];
		this._do(node, useWords, words, addedNodes, onlyFirst, {});

		return addedNodes;
	},

	_do: function(node, words, originalWords, addedNodes, onlyFirst, _doneWords) {
		var i, tmp;

		var proc_node = [node];
		var replaceBits = [];

		while (node = proc_node.pop()) {
			if (node.nodeType == 3) {
				for (i = 0; i < words.length; i++) {
					if (onlyFirst && _doneWords[i]) continue;

					var pos = node.data.toLowerCase().indexOf(words[i]);
					if (pos >= 0 && !$(node.parentNode).hasClass('dp-highlight-word') && !$(node.parentNode).closest('.dp-highlight-word')[0]) {
						_doneWords[i] = true;

						var spannode = document.createElement('span');
						spannode.className = 'dp-highlight-word';
						spannode.setAttribute('data-word', originalWords[i]);
						addedNodes.push(spannode);

						var middlebit = node.splitText(pos);
						var endbit = middlebit.splitText(words[i].length);
						var middleclone = middlebit.cloneNode(true);
						spannode.appendChild(middleclone);

						middlebit.parentNode.replaceChild(spannode, middlebit);

						proc_node.push(endbit);
					}
				}
			} else if (node.nodeType == 1 && node.childNodes && !/(script|style)/i.test(node.tagName)) {
				var children = $.makeArray(node.childNodes);
				for (i = 0; i < children.length; i++) {
					proc_node.push(children[i]);
				}
			}
		}
	},

	/**
	 * Engish stop words courtesy of MySQL
	 */
	stopWords: [
		"a's","able","about","above","according","accordingly","across","actually","after","afterwards","again",
		"against","ain't","all","allow","allows","almost","alone","along","already","also","although","always",
		"am","among","amongst","an","and","another","any","anybody","anyhow","anyone","anything","anyway","anyways",
		"anywhere","apart","appear","appreciate","appropriate","are","aren't","around","as","aside","ask","asking",
		"associated","at","available","away","awfully","be","became","because","become","becomes","becoming","been",
		"before","beforehand","behind","being","believe","below","beside","besides","best","better","between","beyond",
		"both","brief","but","by","c'mon","c's","came","can","can't","cannot","cant","cause","causes","certain",
		"certainly","changes","clearly","co","com","come","comes","concerning","consequently","consider","considering",
		"contain","containing","contains","corresponding","could","couldn't","course","currently","definitely",
		"described","despite","did","didn't","different","do","does","doesn't","doing","don't","done","down",
		"downwards","during","each","edu","eg","eight","either","else","elsewhere","enough","entirely","especially",
		"et","etc","even","ever","every","everybody","everyone","everything","everywhere","ex","exactly","example",
		"except","far","few","fifth","first","five","followed","following","follows","for","former","formerly","forth",
		"four","from","further","furthermore","get","gets","getting","given","gives","go","goes","going","gone","got",
		"gotten","greetings","had","hadn't","happens","hardly","has","hasn't","have","haven't","having","he","he's",
		"hello","help","hence","her","here","here's","hereafter","hereby","herein","hereupon","hers","herself","hi",
		"him","himself","his","hither","hopefully","how","howbeit","however","i'd","i'll","i'm","i've","ie","if",
		"ignored","immediate","in","inasmuch","inc","indeed","indicate","indicated","indicates","inner","insofar",
		"instead","into","inward","is","isn't","it","it'd","it'll","it's","its","itself","just","keep","keeps",
		"kept","know","knows","known","last","lately","later","latter","latterly","least","less","lest","let","let's",
		"like","liked","likely","little","look","looking","looks","ltd","mainly","many","may","maybe","me","mean",
		"meanwhile","merely","might","more","moreover","most","mostly","much","must","my","myself","name","namely",
		"nd","near","nearly","necessary","need","needs","neither","never","nevertheless","new","next","nine","no",
		"nobody","non","none","noone","nor","normally","not","nothing","novel","now","nowhere","obviously","of","off",
		"often","oh","ok","okay","old","on","once","one","ones","only","onto","or","other","others","otherwise",
		"ought","our","ours","ourselves","out","outside","over","overall","own","particular","particularly","per",
		"perhaps","placed","please","plus","possible","presumably","probably","provides","que","quite","qv","rather",
		"rd","re","really","reasonably","regarding","regardless","regards","relatively","respectively","right","said",
		"same","saw","say","saying","says","second","secondly","see","seeing","seem","seemed","seeming","seems","seen",
		"self","selves","sensible","sent","serious","seriously","seven","several","shall","she","should","shouldn't",
		"since","six","so","some","somebody","somehow","someone","something","sometime","sometimes","somewhat",
		"somewhere","soon","sorry","specified","specify","specifying","still","sub","such","sup","sure","t's","take",
		"taken","tell","tends","th","than","thank","thanks","thanx","that","that's","thats","the","their","theirs",
		"them","themselves","then","thence","there","there's","thereafter","thereby","therefore","therein","theres",
		"thereupon","these","they","they'd","they'll","they're","they've","think","third","this","thorough",
		"thoroughly","those","though","three","through","throughout","thru","thus","to","together","too","took",
		"toward","towards","tried","tries","truly","try","trying","twice","two","un","under","unfortunately","unless",
		"unlikely","until","unto","up","upon","us","use","used","useful","uses","using","usually","value","various",
		"very","via","viz","vs","want","wants","was","wasn't","way","we","we'd","we'll","we're","we've","welcome",
		"well","went","were","weren't","what","what's","whatever","when","whence","whenever","where","where's",
		"whereafter","whereas","whereby","wherein","whereupon","wherever","whether","which","while","whither","who",
		"who's","whoever","whole","whom","whose","why","will","willing","wish","with","within","without","won't",
		"wonder","would","would","wouldn't","yes","yet","you","you'd","you'll","you're","you've","your","yours",
		"yourself","yourselves","zero"
	]
};

Orb.createNamespace('DeskPRO.AjaxPoller');

/**
 * An AJAX poller takes a bunch of data and sends it in packs based on an interval.
 */
DeskPRO.AjaxPoller.Poller = new Orb.Class({

	Implements: [Orb.Util.Events, Orb.Util.Options],

	/**
	 * @option {String} ajaxUrl The URL that will handle the data we POST with this poller.
	 * @option {Integer} interval Interval time in milliseconds where the filter is sent automatically.
	 *                            Note that specific items may have a max wait time that might fire the interval
	 *                            before this time.
	 *
	 * @param {DeskPRO.MessageBroker} messageBroker The message broker to send return data through
	 * @param {Object} options Options to set.
	 */
	initialize: function(options) {

		this.dataTransformers = [];
		this.filterdData = [];
		this.messageBroker = null;

		this.maxDelayTimers = [];
		this.isPaused = false;

		this.autoSendTimeout = null;

		this.options = {
			ajaxUrl: null,
			interval: 6000,
			alwaysRequest: false,
			ajaxType: 'SMART',
			postTypes: []
		};

		this.disabled = false;

		this.setOptions(options);

		this.autoSendTimeout = this.send.delay(this.options.interval, this);
	},


	pause: function() {
		this.isPaused = true;
	},

	unpause: function() {
		this.isPaused = false;
	},

	setInterval: function(interval) {
		this.options.interval = interval;
	},



	/**
	 * Data transformers intercept data before it's sent and can change it
	 * or augment it.
	 */
	addDataTransformer: function(name, callback) {
		this.dataTransformers.push(callback);
	},



	/**
	 * Run the data transformers on a piece of data to be sent.
	 */
	transformData: function(name, data, options) {

		var nameparts = name.split('.');
		var cur_name = null;

		while (nameparts.pop()) {
			cur_name = nameparts.join('.') + '.*';
			if (this.dataTransformers[cur_name] !== undefined) {
				this.dataTransformers[cur_name].each(function(callback) {
					data = callback(data, options, name);
				});
			}
		}

		return data;
	},



	/**
	 * Add some data to send on the next poll.
	 *
	 * @option {Integer} maxDelay The max time in milliseconds to wait before a request is sent automatically with this data.
	 * @option {Integer} minDelay The min time in milliseconds to wait before this data will be sent
	 * @option {Boolean} recurring To automatically re-add this data after it's been sent
	 *
	 * @param {Object} data Data to send. This must be in the jQuery data format, either a `k:v` object or `[{name: k, value: v}]` array.
	 *                      Can optionally be a function instead, which when called must return false or the same format as above.
	 * @param {String} name Name for the data, used with transformers to identify the type.
	 * @param {Object} options Options
	 */
	addData: function(data, name, options) {
		name = name || 'default';
		options = options || {};

		if (options.addedTime === undefined) {
			options.addedTime = new Date();
		}

		if (options.maxDelay) {
			(function() {
				this.send();
			}).delay(options.maxDelay, this);
		}

		this.filterdData.push([name, data, options]);
	},



	/**
	 * Send all filterd data items now.
	 */
	send: function() {

		this._clearDelays();

		if (this.isPaused) {
			// Paused, reset timer and dont do anything
			this.autoSendTimeout = this.send.delay(this.options.interval, this);
			return;
		}

		if (!this.options.alwaysRequest && !this.filterdData.length) {
			this.autoSendTimeout = this.send.delay(this.options.interval, this);
			return;
		}

		//------------------------------
		// Build data to send
		//------------------------------

		var now = new Date();

		var send_data = [];
		var sent_info = [];
		var hasPostType = false;

		var filterdData = this.filterdData;
		this.filterdData = [];

		var item = null;
		while (item = filterdData.shift()) {
			var item_name = item[0];
			var item_data = item_orig_data = item[1];
			var item_opts = item[2];

			if (item_opts.minDelay && !(item_opts.minDelayAfterOne && !item_opts.sentCount)) {
				// If its too soon, add it back immediately
				if (item_opts.minDelay > (now.getTime() - item_opts.addedTime.getTime())) {
					this.addData(item_orig_data, item_name, item_opts);
					continue;
				}
			}

			if (typeOf(item_data) == 'function') {
				item_data = item_data(item_name, {}, item_opts);
			}

			item_data = this.transformData(item_name, item_data, item_opts);
			sent_info.push([item_orig_data, item_name, item_opts]);

			if (!item_data) continue;

			if (!hasPostType && this.options.postTypes.indexOf(item_name) !== -1) {
				hasPostType = true;
			}

			if (typeOf(item_data) == 'array') {
				send_data.append(item_data);
			} else {
				Object.each(item_data, function(v, k) {
					send_data.push({ name: k, value: v });
				});
			}
		}

		if (!this.options.alwaysRequest && !sent_info.length) {
			this._handleAjaxSuccess({}, sent_info);
			return;
		}

		//------------------------------
		// Send data
		//------------------------------

		type = this.options.ajaxType;
		if (type == 'SMART') {
			if (hasPostType) {
				type = 'POST';
			} else {
				type = 'GET';
			}
		}

		$.ajax({
			cache: false,
			type: type,
			url: this.options.ajaxUrl,
			context: this,
			data: send_data,
			dataType: 'json',
			dpIsPolling: true,
			success: function (data) {
				this._handleAjaxSuccess(data, sent_info);
			},
			error: function(xhr, textStatus, errorThrown) {
				this._handleAjaxError(sent_info, xhr, textStatus, errorThrown);
			}
		});
	},



	/**
	 * Handles AJAX success.
	 *
	 * Reattaches recurring data points if there are any, and then sends
	 * messages through the message broker for handling.
	 *
	 * @param {Object} data The data returned from the AJAX call
	 * @param {Object} sent_data Information about data we sent in the call
	 */
	_handleAjaxSuccess: function (data, sent_info) {

		if (data && data.request_token) {
			window.DP_REQUEST_TOKEN = data.request_token;
		}

		// Start auto timer
		this.autoSendTimeout = this.send.delay(this.options.interval, this);

		this.resetSentItems(sent_info);

		this.fireEvent('ajaxSuccess', data);
	},

	resetSentItems: function(sent_info) {
		var item;
		while (item = sent_info.shift()) {
			var item_name = item[0];
			var item_data = item[1];
			var item_opts = item[2];

			if (item_opts.recurring) {
				item_opts.lastSent = new Date();

				if (item_opts.sentCount === undefined) item_opts.sentCount = 0;
				item_opts.sentCount++;

				// Delete addedTime so minDelay check will reset too
				delete item_opts.addedTime;

				this.addData(item_name, item_data, item_opts);
			}
		}
	},

	_handleAjaxError: function (sent_info, xhr, textStatus, errorThrown) {
		this.resetSentItems(sent_info);

		// Start auto timer
		this.autoSendTimeout = this.send.delay(this.options.interval, this);

		DP.console.error("Polling Error %s for %o", textStatus, xhr);

		this.fireEvent('ajaxError', [xhr, textStatus, errorThrown]);
	},



	/**
	 * Clear all delay timeouts
	 */
	_clearDelays: function() {

		this.autoSendTimeout = window.clearTimeout(this.autoSendTimeout);
		this.autoSendTimeout = null;

		var t = null;
		while (t = this.maxDelayTimers.pop()) {
			window.clearTimeout(t);
		}
	}
});

Orb.createNamespace('DeskPRO.AjaxPoller');

/**
 * A specialized ajax poller where the returned AJAX result contains a correctly
 * formatted JSON structure:
 *
 * <code>
 * { messages: [{name: 'some.message.name', data: 'data'}, ...] }
 * </code>
 *
 * These messages are passed through a message broker for handling in the app.
 */
DeskPRO.AjaxPoller.MessagePoller = new Orb.Class({
	Extends: DeskPRO.AjaxPoller.Poller,

	initialize: function(messageBroker, options) {
		this.parent(options);

		this.messageBroker = messageBroker;

		this.addEvent('ajaxSuccess', this._sendMessages, this);
	},

	getMessageBroker: function() {
		return this.messageBroker;
	},

	_sendMessages: function(data) {

		if (data.messages === undefined || typeOf(data.messages) != 'array') {
			return;
		}

		var message = null;
		while (message = data.messages.shift()) {
			this.messageBroker.sendMessage(message[0], message[1]);
		}
	}
});

Orb.createNamespace('DeskPRO.MessageChanneler');

/**
 * A message channeler handles subscribing to channels, and delivering messages
 * from the server to the message broker, which in turn notifies any listeners.
 */
DeskPRO.MessageChanneler.AbstractChanneler = new Orb.Class({

	Implements: [Orb.Util.Options, Orb.Util.Events],

	initialize: function(messageBroker, options) {

		this.channels = [];
		this.options = {};

		this.messageBroker = messageBroker;
		if (options) this.setOptions(options);

		this._init();
	},

	_init: function() { /* Child class hook method */ },

	sendMessage: function(channel, message) {

		if (DeskPRO_Window && DeskPRO_Window.getDebug('logClientMessages')) {
			DP.console.log('channel(%s): %o', channel, message);
		}

		this.messageBroker.sendMessage(channel, message);
	}
});

Orb.createNamespace('DeskPRO.MessageChanneler');

/**
 * The AJAX channeler uses AJAX polling to fetch new messages from the server.
 */
DeskPRO.MessageChanneler.AjaxChanneler = new Orb.Class({
	Extends: DeskPRO.MessageChanneler.AbstractChanneler,

	_init: function() {
		this.count = 0;
		this.hasDoneInitialLoad = false;

		this.lastMessageId = -1;
		this.poller = new DeskPRO.AjaxPoller.Poller({
			ajaxUrl: this.options.ajaxMessagesUrl,
			interval: this.options.interval,
			ajaxType: 'SMART',
			postTypes: [
				'dismiss_alerts',
				'recent_tabs'
			]
		});

		this.poller.addData((function () {
			if (this.lastMessageId === null) return null;
			return { 'since': this.lastMessageId };
		}).bind(this), 'since', { recurring: true });

		this.poller.addData((function () {
			if (!DeskPRO_Window.dismissAlertQueue.length) return null;

			var q = DeskPRO_Window.dismissAlertQueue;
			DeskPRO_Window.dismissAlertQueue = [];

			var send = [];
			Array.each(q, function(item) {
				send.push({
					name: 'dismiss_alerts[]',
					value: item
				})
			});

			return send;
		}).bind(this), 'dismiss_alerts', { recurring: true });

		this.poller.addData((function () {
			return { 'count': ++this.count };
		}).bind(this), 'since', { recurring: true });

		this.poller.addData((function () {
			if (!(DeskPRO_Window.recentTabs && DeskPRO_Window.recentTabs.recentPendingSync && DeskPRO_Window.recentTabs.recentPendingSync.length)) {
				return null;
			}

			var recent = [];
			Array.each(DeskPRO_Window.recentTabs.recentPendingSync, function(item, idx) {
				recent.push([
					item[0],
					item[1],
					item[2],
					item[3],
					item[4]
				]);
			});

			DeskPRO_Window.recentTabs.recentPendingSync = [];

			var recentData = JSON.stringify(recent);
			return [{name: 'recent_tabs', value: recentData}];
		}).bind(this), 'recent_tabs', { recurring: true });

		this.poller.addData({is_initial_poll:1}, 'is_initial_poll');

		this.poller.addEvent('ajaxSuccess', this.handleMessageAjax.bind(this));

		if (this.options.lastMessageId) {
			this.lastMessageId = this.options.lastMessageId;
		}
	},

	handleMessageAjax: function(data) {
		if (!data) {
			console.debug("Data is false");
			return;
		}

		var ins_order, i, x, d, messages;
		var ordered = {};
		var orders = [];

		if (data.messages && data.messages.length) {
			for (x = 0; x < data.messages.length; x++) {
				d = data.messages[x];
				if (d[0] && (parseInt(d[0], 10) <= this.lastMessageId) && (!d[3] || !d[3]['offline_messsage'])) {
					console.debug("%o Dropping message older than lastMessageId %d", d, this.lastMessageId);
					return;
				}

				var ins_order = 50;
				if (d[1] == 'agent.ticket-updated') {
					ins_order = 55;
				}

				if (!ordered[ins_order]) {
					ordered[ins_order] = [];
					orders.push(ins_order);
				}

				ordered[ins_order].push(d);

				if (d[0] && d[0] > this.lastMessageId) {
					this.lastMessageId = d[0];
				}
			}
		}

		orders.sort(function(a,b){return a - b});
		for (i = 0; i < orders.length; i++) {
			messages = ordered[orders[i]];
			for (x = 0; x < messages.length; x++) {
				d = messages[x];

				try {
					this.sendMessage(d[1], d[2]);
				} catch (err) {
					DpErrorLog.logError('[AjaxChanneler] ' + err, err.stack || '', err.fileName || '', err.lineNumber || '');
				}
			}
		}

		if (typeof data.last_id != 'undefined' && parseInt(data.last_id) > this.lastMessageId) {
			this.lastMessageId = parseInt(data.last_id);
		}

		this.fireEvent('postMessageSend', [data]);
		this.hasDoneInitialLoad = true;
	},

	getLastMessageId: function() {
		return this.lastMessageId;
	},

	setLastMessageId: function(messageId) {
		this.lastMessageId = messageId;
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
Orb.createNamespace('DeskPRO.Agent');

DeskPRO.Agent.RteEditor = {
	initRteAgentReply: function(textarea, options) {
		textarea = $(textarea);
		options = options || {};

		if (!options.defaultIsHtml) {
			var val = textarea.val();
			if (val.length) {
				textarea.val(DP.convertTextToWysiwygHtml(val, true));
			}
		}

		var inlineHiddenPosition = options.inlineHiddenPosition;

		// must be done before initializing
		var dropZone = textarea.siblings('.drop-file-zone');

		if (window.DP_AGENT_RTE_BUTTONS) {
			var b = window.DP_AGENT_RTE_BUTTONS;
			var buttons = [];
			if (b.html) buttons.push('html');

			if ((b.bold || b.italic || b.underline || b.strike) && buttons.length) buttons.push('|');
			if (b.bold) buttons.push('bold');
			if (b.italic) buttons.push('italic');
			if (b.underline) buttons.push('underline');
			if (b.strike) buttons.push('deleted');

			if (b.color) {
				if (buttons.length) buttons.push('|');
				buttons.push('fontcolor');
			}

			if (b.alignment) {
				if (buttons.length) buttons.push('|');
				buttons.push('alignment');
			}

			if (b.list) {
				if (buttons.length) buttons.push('|');
				buttons.push('unorderedlist');
				buttons.push('orderedlist');
				buttons.push('outdent');
				buttons.push('indent');
			}

			if ((b.image || b.link || b.table || b.hr) && buttons.length) buttons.push('|');
			if (b.image) buttons.push('image');
			if (b.link) buttons.push('link');
			if (b.table) buttons.push('table');
			if (b.hr) buttons.push('horizontalrule');

		} else {
			var buttons = ['html', '|', 'bold', 'italic', 'underline', '|',  'unorderedlist', 'orderedlist', 'outdent', 'indent', '|', 'image', 'link', '|', 'alignment'];
		}

		var defaultOptions = {
			direction: textarea.attr('dir') || 'ltr',
			buttons: buttons,
			minHeight: 150,
			observeImages: false,
			cleanup: false,
			imageUpload: BASE_URL + 'agent/misc/accept-redactor-image-upload',
			uploadFields: {
				_rt: window.DP_REQUEST_TOKEN
			},
			plugins: ['clean_text'],
			imageUploadCallback: function(obj, json) {
				if (inlineHiddenPosition) {
					inlineHiddenPosition.after($('<input type="hidden" name="blob_inline_ids[]" />').val(json.blob_id));
				}
			},
			imageUploadErrorCallback: function(obj, json) {
				alert(json.error);
			}
		};

		if (options.autosaveContent && options.autosaveContentId) {
			defaultOptions.autosave = BASE_URL + 'agent/misc/redactor-autosave/' + options.autosaveContent + '/' + options.autosaveContentId;
			defaultOptions.interval = 5;
		}

		options = Object.merge(defaultOptions, options);

		var autosaveUrl = options.autosave,
			autosaveInterval = options.interval || 5,
			preAutosaveCallback = options.preAutosaveCallback;

		options.autosave = false;
		options.cleanup = false; // must always be false for paste of images to work - code below implements default cleanup
		textarea.addClass('with-redactor');
		textarea.redactor(options);

		var api = textarea.data('redactor');
		if (!api) {
			return false;
		}

		var editor = textarea.getEditor();
		if (!editor) {
			return false
		}

		api.$toolbar.find('a').attr('unselectable', 'on').attr('tabindex', '-1');
		api.$editor.addClass('unreset');

		editor.bind('keydown', function(ev) {
			ev.stopPropagation();

			if (ev.metaKey && !ev.ctrlKey) { // pressing "cmd" on a mac
				var sel;
				if (window.getSelection && (sel = window.getSelection()) && sel.modify) {
					var adjustmentType = ev.shiftKey ? "extend" : "move";

					switch (ev.keyCode) {
					case 39: // right - act like "end" in windows
							sel.modify(adjustmentType, "right", "lineboundary");
							ev.preventDefault();
							break;

						case 37: // left - act like "home" in windows
							sel.modify(adjustmentType, "left", "lineboundary");
							ev.preventDefault();
							break;
					}
				}
			}
		});

		editor.bind('keypress', function(ev) {
			ev.stopPropagation();
		});

		editor.bind('dragover drop', function(ev) {
			ev.stopPropagation();
		});

		// setup autosave
		if (autosaveUrl) {
			var getAutosaveData = function(api) {
				var newContent = api.getCode(),
					name = api.$el.attr('name');

				var data = [];
				data.push({
					name: name,
					value: newContent
				});

				if (preAutosaveCallback) {
					data = preAutosaveCallback(textarea, data);
				}

				return data;
			};

			var autosaveContent = api.getCode(),
				autosaveData = getAutosaveData(api);

			var saveFnRunning = false;
			var saveFn = $.proxy(function() {
				if (saveFnRunning) {
					return;
				}
				if (!textarea.data('redactor')) {
					clearInterval(autosaveTimer);
					autosaveTimer = false;
					return;
				}

				if (!api.$editor.is(':visible')) {
					return;
				}

				if (textarea.data('disable-autosave')) {
					return;
				}

				var newContent = this.getCode(),
					newData = getAutosaveData(this);

				if (newData.length) {
					for (var i = 0; i < newData.length; i++) {
						if (newData[i].name == name) {
							newContent = newData[i].value;
							break;
						}
					}
				}

				if (window.JSON && window.JSON.stringify) {
					if (JSON.stringify(newData) === JSON.stringify(autosaveData)) {
						return;
					}
				} else {
					if (newContent == autosaveContent) {
						return;
					}
				}

				autosaveContent = newContent;
				autosaveData = newData;

				saveFnRunning = true;
				var ajax = $.ajax({
					url: autosaveUrl,
					type: 'post',
					data: newData,
					complete: function() {
						saveFnRunning = false;
						textarea.data('autosave-running', null);
					},
					success: $.proxy(function(data) {
						if (typeof this.opts.autosaveCallback === 'function') {
							this.opts.autosaveCallback(data, this);
						}
					}, this)
				});
				textarea.data('autosave-running', ajax);
			}, api);

			var autosaveTimer = setInterval(saveFn, autosaveInterval * 1000);

			textarea.on('dp_autosave_trigger', saveFn);
		}

		// drag onto the editor to upload
		if (api.opts.imageUpload && !$.browser.msie) {
			var dropTarget = dropZone.length ? dropZone : editor;
			dropTarget.bind('drop', function(event) {
				event.preventDefault();

				var file = event.originalEvent.dataTransfer.files[0];
				if (!file) {
					return;
				}
				var fd = new FormData();

				// append file data
				fd.append('file', file);

				$.ajax({
					url: api.opts.imageUpload,
					dataType: 'html',
					data: fd,
					cache: false,
					contentType: false,
					processData: false,
					type: 'POST',
					success: $.proxy(function(data) {
						var json = $.parseJSON(data);

						if (typeof json.error == 'undefined') {
							$.proxy(api.imageUploadCallback, api)(json);
						} else {
							$.proxy(api.opts.imageUploadErrorCallback, api)(api, json);
							$.proxy(api.imageUploadCallback, api)(false);
						}

					}, api)
				});
			});

			if (dropZone.length) {
				textarea.getEditor().after(dropZone);
			}
		} else {
			dropZone.remove();
		}

		// setup paste support for images (Webkit, FireFox only)
		var pasteImageCounter = 1;

		var sendImage = function(pasteId, type, data, encoding) {
			try {
				var form = new FormData();
				if (typeof(data) == 'string') {
					// data URI
					var byteString;
					if (encoding == 'base64') {
						byteString = atob(data);
					} else {
						byteString = unescape(data);
					}

					var array = [];
					for(var i = 0; i < byteString.length; i++) {
						array.push(byteString.charCodeAt(i));
					}
					data = new Blob([new Uint8Array(array)], {type: 'image/' + type});
				}

				form.append('file', data, 'upload.' + type);
				form.append('filename', 'upload.' + type);
			} catch (e) {
				return false;
			}

			$.ajax({
				url: BASE_URL + 'agent/misc/accept-redactor-image-upload',
				type: 'POST',
				dataType: 'json',
				data: form,
				processData: false,
				contentType: false,
				success: function(json) {
					if (!textarea.data('redactor')) {
						return;
					}

					var img = textarea.getEditor().find('img[data-paste-id=' + pasteId + ']');
					if (json.error) {
						img.remove();
					} else {
						img.data('paste-id', '').attr('src', json.filelink);
						if (typeof api.opts.imageUploadCallback === 'function') {
							api.opts.imageUploadCallback(api, json);
						}
					}

					textarea.data('redactor').insertHtml('');
				}
			});

			return true;
		};

		// since our <p> tags only have one linebreak, lets turn them into <divs> since
		// that's how they act
		textarea.getEditor().on('copy', function(e) {
			api.saveSelection();

			var html = api.getSelectedHtml();
			html = html.replace(/<p/gi, '<p data-redactor="1"');
			if (!$.browser.msie) {
				html = html.replace(/<(p|div)[^>]><\/(p|div)>/i, '');
			}

			var div = $('<div data-redactor-wrapper="1" />').html(html).css({
				position: 'absolute',
				left: '-9999px'
			});

			$(document.body).append(div);

			var sel = api.getSelection();
			try {
				sel.selectAllChildren(div.get(0));
			} catch (e) {
				if (document.createRange && sel.removeAllRanges && sel.addRange) {
					var range = document.createRange();
					range.selectNode(div.get(0));
					sel.removeAllRanges();
					sel.addRange(range);
				}
			}

			setTimeout(function() {
				div.remove();
				api.restoreSelection();
			}, 0);
		});

		textarea.getEditor().on('paste', $.proxy(function(ev) {
			this.pasteRunning = true;

			if (ev.originalEvent.clipboardData) {
				var items = ev.originalEvent.clipboardData.items;
				if (items) {
					var hasImage = false;
					for (var i = 0; i < items.length; i++) {
						if (items[i].type.match(/^image\/([a-z0-9_-]+)$/i)) {
							var blob = items[i].getAsFile();
							var URLObj = window.URL || window.webkitURL;
							var source = URLObj.createObjectURL(blob);

							var pasteImageId = pasteImageCounter++;

							if (sendImage(pasteImageId, RegExp.$1, blob)) {
								textarea.insertHtml('<img src="' + source + '" data-paste-id="' + pasteImageId + '">');
								hasImage = true;
							}
						}
					}

					// pasted an image - won't be other content
					if (hasImage) {
						ev.preventDefault();
						ev.stopPropagation();
						return;
					}
				}
			}

			this.setBuffer();

			if (this.opts.autoresize === true) {
				this.saveScroll = document.body.scrollTop;
			} else {
				this.saveScroll = this.$editor.scrollTop();
			}

			var frag = this.extractContent();

			setTimeout($.proxy(function() {
				var pastedFrag = this.extractContent();
				this.$editor.append(frag);

				this.restoreSelection();

				var imgs = pastedFrag.querySelectorAll('img');
				if (imgs) {
					for (var i = 0; i < imgs.length; i++) {
						imgs[i].setAttribute('style', '-x-ignore: 1');
						if (imgs[i].src.match(/^data:image\/([a-z0-9_-]+);([a-z0-9_-]+),([\W\w]+)$/i)) {
							var pasteImageId = pasteImageCounter++;
							imgs[i].setAttribute('data-paste-id',  pasteImageId);

							if (!sendImage(pasteImageId, RegExp.$1, RegExp.$3, RegExp.$2)) {
								imgs[i].parentNode.removeChild(imgs[i]);
							}
						}
					}
				}

				var html = this.getFragmentHtml(pastedFrag);

				// since <p> only counts as one line break, we need to fix that
				html = $.trim(html);
				html = html.replace(/^<div[^>]* data-redactor-wrapper="1"[^>]*>([\w\W]+)<\/div>$/, '$1');
				html = html.replace(/(<p[^>]* data-redactor="1"[^>]*>[\w\W]*?<\/p>)<p>(<br>)?<span><span><\/span><\/span><\/p>/ig, '$1');
				html = html.replace(/<p>(<br>)?<span><span><\/span><\/span><\/p>$/, '');

				// convert divs to p's and keep empty ones
				html = html.replace(/<div/gi, '<p').replace(/<\/div>/g, '</p>');
				html = html.replace(/<p([^>]*)>(\s*|<br\s*\/?>|&nbsp;)<\/p>/gi, '<br/>');
				html = html.replace(/(<p[^>]*) data-redactor="1"/g, '$1');
				html = html.replace(/<\/p>\s*<p>/g, '<\/p><p>');
				html = html.replace(/^<p>/, '');
				html = html.replace(/<\/p>$/, '');

				this.pasteCleanUp(html);

				this.pasteRunning = false;
			}, this), 1);

		}, textarea.data('redactor')));

		return textarea;
	}
};

if (typeof RedactorPlugins === 'undefined') var RedactorPlugins = {};

// Based on the plugin by João Sardinha
// (https://github.com/johnsardine/redactor-plugins)
RedactorPlugins.clean_text = {

	init: function() {

		// Create button
		this.addBtn('clean_text', 'Clean selection formatting', function(redactor, event, button_key) {

			// Grab selected text
			var html = redactor.getSelectedHtml();

			html = html.replace(/<\/td>/g, "\t");
			html = html.replace(/<\/tr>/g, "\n");
			html = html.replace(/\s*<div[^>]*>\s*/g, "\n");
			html = html.replace(/\s*<\/p>\s*<p[^>]*>\s*/g, "\n\n");
			html = html.replace(/\s*<p[^>]*>\s*/g, "\n");
			html = html.replace(/\s*<br[^>]*\/>\s*/g, "\n");
			html = html.replace(/\s*<br[^>]*>\s*/g, "\n");

			// Strip out html
			html = html.replace(/(<([^>]+)>)/ig,"");
			html = $.trim(html);
			html = html.replace(/\r|\r\n|\n/g, "\n<br/>");

			// Set buffer (allows undo shortcut)
			redactor.setBuffer();

			// Replace selection with clean text
			redactor.insertHtml(html);

			// Sync code
			redactor.syncCode();
		});

		// Add separator before button
		this.addBtnSeparatorBefore('clean_text');

		// Add icon to button
		jQuery('a.redactor_btn_clean_text').css({
			backgroundImage : ' url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAYAAACNMs+9AAAAQ0lEQVQYV2MMDQ39zwAEq1evZgTR6AAmzwhjYFOMLAc2BZtidDG4dcgSyNbDnITiLnTFyO4mXSFRVhPlGaKDh9gABwAJuDgDsQ44aQAAAABJRU5ErkJggg==)',
			backgroundPosition : '7px 8px'
		});
	}
}
Orb.createNamespace('DeskPRO');

DeskPRO.TextExpander = new Orb.Class({

	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(options) {
		this.options = {
			textarea: null
		};

		this.setOptions(options);

		this.comboString = null;
		this.$txt = $(this.options.textarea);

		var self = this;
		this.$txt.on('keypress', function(ev) {
			// % key
			if (ev.which == 37) {
				if (!self.comboString) {
					self.comboString = '%';
				} else {
					var combo = self.comboString + '%';
					self.comboString = null;
					self.fireEvent('combo', [combo, ev]);
				}

			} else if (ev.which == 8) {
				// nothing
				// some browsers like (firefox) pass backspace event
				// into keypress, while others (webkit, ie) do not

			// Other input keys after 'start'
			// of combo string
			} else if (self.comboString) {
				var chr = String.fromCharCode(ev.which);
				if (chr.match(/[a-zA-Z0-9:\.\-_]/)) {
					self.comboString += chr;
				} else {
					self.comboString = null;
				}
			} else {
				self.comboString = null;
			}
		});

		// Handle backspace
		this.$txt.on('keyup', function(ev) {
			if (self.comboString && ev.which == 8) {
				self.comboString = self.comboString.substring(0, self.comboString.length-1);
			}
		});
	}
});
