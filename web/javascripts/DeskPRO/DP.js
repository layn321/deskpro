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
