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