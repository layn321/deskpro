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

Orb.createNamespace('DeskPRO.Admin');

/**
 * Similar in responsibility to the Agent.Window (global object/init etc), but completely
 * different.
 *
 * Within the admin interface, this is the gloabl var DeskPRO_Window.
 *
 * If a page defines the special function DeskPRO_Window_Init(), it will be called
 * automatically once the page is ready.
 */
DeskPRO.Admin.Window = new Orb.Class({

	Extends: DeskPRO.BasicWindow,

	init: function() {
		var self = this;
		this.util = {
			modCountEl: function(el, op, num) {

				el = $(el);

				if (!num) num = 1;

				var count = parseInt(el.text().trim());

				if (op == '-' || op == 'rem' || op == 'del' || op == 'sub') {
					count -= num;
					if (count < 0) count = 0;
				} else if (op == '+' || op == 'add') {
					count += num;
				} else {
					count = num;
				}

				el.text(count);

				return count;
			},

			showSavePuff: function(overEl) {

				if (!overEl || !overEl[0]) {
					return;
				}

				var pos = overEl.offset();

				if (!pos) {
					return;
				}

				var el = $('<div class="load-puff" style="display: none; opacity: 0" />');
				el.appendTo('body');

				el.css({
					top: pos.top + 15,
					left: pos.left + overEl.width() - 4
				});

				var endPos1 = pos.top - 5;
				var endPos2 = pos.top - 15;

				el.show();
				el.animate({
					top: endPos1,
					opacity: 1
				}, 200, 'swing', function() {
					window.setTimeout(function() {
						el.animate({
							top: endPos2,
							opacity: 0
						}, 200, 'swing', function() {
							el.remove();
						});
					}, 225);
				});
			},

			/**
			 * Get a "plain" article. ie of type="text/x-deskpro-plain"
			 *
			 * @param el
			 * @return {String}
			 */
			getPlainTpl: function(el) {
				var el = $(el);
				var html = el.get(0).innerHTML;

				html = html.replace(/%startScript%/g, '<script>');
				html = html.replace(/%endScript%/g, '</script>');

				return html;
			},

			fileupload: function(el, options) {

				var setel;
				if (!options) options = {};

				if (options.page) {
					options.namespace = options.page.OBJ_ID + '_fileupload';
				}

				if (!options.namespace) {
					options.namespace = Orb.uuid();
				}

				if (!options.dropZone) {
					options.dropZone = $(el);
				}

				if (typeof options.autoUpload == 'undefined') {
					options.autoUpload = true;
				}

				if (!options.url) {
					if (options.saveMedia) {
						options.url = BASE_URL + 'agent/misc/accept-upload?save_media=1';
					} else {
						options.url = BASE_URL + 'agent/misc/accept-upload';
					}
				}

				if (options.uploadTemplate) {
					var setel = options.uploadTemplate;
				} else {
					var setel = $('.template-upload', el);
				}

				if (!setel || !setel[0]) {
					console.error("Invalid uploadTemplate");
					return $(el);
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

				if (!setel || !setel[0]) {
					console.error("Invalid downloadTemplate");
					return $(el);
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
					options.filesContainer.show();
				};

				// Same as default except added check for 'that' still exists
				options.done = function (e, data) {
					var that = $(this).data('fileupload'),
						template,
						preview;

					// Means the widget is no longer visible (eg tab closed before upload finished)
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
				};

				// Same as default except added check for 'that' still exists
				options.stop = function (e) {
					var that = $(this).data('fileupload');
					if (!that) {
						return;
					}
					that._transition($(this).find('.fileupload-buttonbar .progress')).done(
						function () {
							$(this).find('.bar').css('width', '0%');
							that._trigger('stopped', e);
						}
					);
				},

				$(el).on('click', '.remove-attach-trigger', function(ev) {
					// Ignore .delete as they may be items rendered with the page,
					// eg. the list handles delete of existing attachments on its own
					if ($(this).hasClass('delete')) {
						return;
					}
					ev.preventDefault();

					var clicked = $(this), li = clicked.closest('li');
					li.slideUp('fast', function() {
						clicked.remove();

						if (options.filesContainer.hasClass('dp-hide-empty')) {
							options.filesContainer.hide();
						}
					});

					el.trigger('fileremoved', [li]);
				});

				return $(el).fileupload(options);
			},
		};

		$(window).on('resize', function() {
			self.updatePageNavPos();
		});

		$(document).ajaxError(this.ajaxGlobalErrorHandler.bind(this));
	},

	ajaxGlobalErrorHandler: function(event, xhr, ajaxOptions, errorThrown, force) {
		// Session timed out / cookies cleared
		if (xhr && xhr.status && xhr.status == '403') {
			window.location = BASE_URL + 'admin/';
		}
	},

	initRteAgentReply: function(textarea, options) {
		return DeskPRO.Agent.RteEditor.initRteAgentReply(textarea, options);
	},

	initPage: function() {
		var self = this;

		$.fn.qtip.zindex = 999999999;

		var side = $('#dp_admin_page_sidebar, #dp_admin_page_sidebar_right').first();
		if (side.length) {
			window.setInterval(function() {
				$('#dp_admin_page_inner').css('min-height', side.outerHeight() + 25);
			}, 300);
		}

		this.menuEls = $('#menus_container > div').addClass('header-menu').each(function() {
			$(this).detach().appendTo('body');
		});
		$('#menus_container').remove();

		this.menuTriggerEls = $('#dp_admin_nav li[data-menu]');
		$('#dp_admin_nav').on('click', 'li[data-menu]', function(ev) {
			ev.preventDefault();
			self.openHeaderMenu($(this));
		});

		$(':checkbox.onoff-slider').checkbox({
			empty: ASSETS_BASE_URL + '/vendor/jquery/jquery-checkbox/empty.png'
		});

		$('table.with-reorderable').each(function() {
			var table = $(this);
			table.data('table-reorder', new DeskPRO.Admin.TableReorder(table));
		});

		// Interface toggle
		$('#DP-InterfaceSwitcher > .DP-adminSwitch > .adminSwitcher').on('click', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			var list = $('#interfacesToggle');
			list.hide();
			list.css({
				top: 7,
				left: 9
			});
			list.show();

			list.find('a').on('click', function() { list.hide(); backdrop.remove(); });

			var backdrop = $('<div class="backdrop" />').appendTo('body');
			backdrop.on('click', function() {
				list.hide();
				backdrop.remove();
			});
		});

		$(document).on('mouseover', '.tipped', function(ev) {
			if ($(this).is('.tipped-inited')) {
					return;
				}

				var options = {};
				if ($(this).data('tipped-options')) {
					eval('options = {' + $(this).data('tipped-options') + '}');
				}

				qtipOptions = {};
				if ($(this).data('tipped')) {
					qtipOptions.content = {
						attr: 'data-tipped'
					};
				} else {
					qtipOptions.content = {
						attr: 'title'
					};
				}

				if (options.inline) {
					qtipOptions.content.attr = null;
					var el = $('#' + $(this).data('tipped'));
					qtipOptions.content.text = function() {
						return el.html();
					};
				}

				qtipOptions.style = {
					classes: 'ui-tooltip-shadow ui-tooltip-rounded'
				};

				qtipOptions.position = {
					my: 'top center',
					at: 'bottom center',
					viewport: $(window)
				};

				$(this).qtip(qtipOptions).qtip('show', ev);
				$(this).addClass('tipped-inited');
		});

		$(document).on('click', '.click-go', function(ev) {
			var url = $(this).data('url');
			if (url) {
				window.location = url;
				ev.preventDefault();
				ev.stopPropagation();
			}
		});

		$(document).on('click', '.click-confirm', function(ev) {
			if (!confirm($(this).data('confirm'))) {
				ev.preventDefault();
			}
		});

		DeskPRO.ElementHandler_Exec();

		$('time.timeago').timeago();

		if (typeof window.DeskPRO_Window_Init == 'function') {
			window.DeskPRO_Window_Init();
		}

		if (document.getElementById('dp_page_nav')) {
			$('#dp_admin_page').css('min-height', $('#dp_page_nav').outerHeight() + 10);

			if ($('#dp_page_nav').outerHeight() < $(window).height() - 100) {
				if ($('#dp_page_nav').hasClass('fixed')) {
					this.updatePageNavPos();
				} else {
					window.setTimeout(this.updatePageNavPos.bind(this), 30);
					$(window).scroll(this.updatePageNavPos.bind(this));
					$(window).on('resize', this.updatePageNavPos.bind(this));
				}
			}
		}

		$('#portal_nav').on('click', 'li', function(ev) {
			if ($(ev.target).is('a')) {
				return;
			}
			var a = $('a', this).first();
			if (!a.attr('href')) {
				return;
			}

			ev.preventDefault();
			window.location = a.attr('href');
		});

		this._initHelp();

		// User menu
		$('#userSetting_trigger').on('click', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			var list = $('#userSetting');
			list.hide().detach().appendTo('#dp_header');
			list.css({
				top: 41,
				left: 3
			});
			list.show();

			var backdrop = $('<div class="backdrop" />').appendTo('body');

			var close = function() {
				list.hide();
				backdrop.remove();
			};
			backdrop.on('click', close);
			list.on('click', close);
			$('ul', list).on('click', function(ev) {
				ev.stopPropagation();
			});
		});

		$('.confirm-delete-trigger').on('click', function(ev) {
			var message = $(this).data('prompt');
			if (!message) {
				message = 'Are you sure you want to delete this?';
			}

			if (!confirm(message)) {
				ev.preventDefault();
			}
		});

		// DeskPRO logo menu
		var logoBackdrop = null;
		$('#dp_logo_wrap .button-wrap').on('click', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();
			$('#dp_logo_expand_wrap').css('right', 0).css('top', 0).show();
			if (!logoBackdrop) {
				logoBackdrop = $('<div class="backdrop" />').appendTo('body');
				logoBackdrop.click(function() { logoBackdrop.hide();$('#dp_logo_expand_wrap').hide(); });
			}
			logoBackdrop.show();
		});
		$('#dp_logo_expand_wrap').on('click', function(ev) {
			ev.stopPropagation();
			$('#dp_logo_expand_wrap').hide();
			if (logoBackdrop) logoBackdrop.hide();
		});

		var newDashOverlay = new DeskPRO.UI.Overlay({
			triggerElement: '#new_dashboard_trigger',
			contentElement: '#new_dashboard_overlay'
		});

		$('form.with-form-validator').each(function() {
			var v = new DeskPRO.Form.FormValidator($(this));
			$(this).data('form-validator-inst', v);
		});

		window.setTimeout(function() {
			self.sessionPing();
		}, 30000);

		DP.select($('select.dp-select'));
	},

	/**
	 * Simple request to ping our session to keep it alive while on this page
	 */
	sessionPing: function() {
		var self = this;
		$.ajax({
			url: BASE_PATH + 'dp.php/session-ping.json?i=aa',
			cache: false,
			complete: function() {
				window.setTimeout(function() {
					self.sessionPing();
				}, 30000);
			},
			success: function(data) {
				if (data.request_token) {
					window.DP_REQUEST_TOKEN = data.request_token;
				}
			}
		});
	},

	updatePageNavPos: function() {

		var nav  = $('#dp_page_nav');

		if (!nav[0]) {
			return;
		}

		var page = $('#dp_admin_page');
		var mode = 'normal';

		if (!page.length) {
			page = $('#dp_fauxbrowser');
			//nav.addClass('fauxbrowser');
			//var mode = 'alt';
		}

		var winWidth = $(window).width();
		var navWidth = 175 + (mode=='alt' ? 35 : 0); // width of the nav, minus the few pixels of overlap
		var pageWidth = 971;
		var totalWidth = pageWidth+navWidth; // 971 is width of page, aka $('#dp_header').outerWidth();

		var workingWidth = winWidth;

		// If its over, force a scroll
		if (totalWidth > winWidth) {
			$('body').css({'padding-left': navWidth, 'min-width': 971});
		} else {
			$('body').css({width: 'auto'});
			// The space naturally available to the left
			var spaceAvail = (workingWidth - pageWidth) / 2;
			if (spaceAvail < navWidth) {
				$('body').css('padding-left', navWidth);
			} else {
				$('body').css('padding-left', 0);
			}
		}

		var top = page.offset().top + 15 + (mode=='alt' ? 35 : 0);
		top += $(window).scrollTop();

		var left = page.offset().left;

		nav.css({
			top: top,
			left: left - nav.outerWidth() + 5 - (mode=='alt' ? 35 : 0)
		});

		if (nav.height() > page.height()) {
			$('.dp-page-box', page).last().css('min-height', nav.height() + 100);
		}

		nav.css('visibility', 'visible');
	},

	openHeaderMenu: function(triggerEl) {
		if (!this.headerMenuBackdrop) {
			this.headerMenuBackdrop = $('<div class="backdrop" />').appendTo('body').hide();
			this.headerMenuBackdrop.on('click', this.closeHeaderMenu.bind(this));
		}

		this.menuTriggerEls.removeClass('open');
		this.menuEls.hide();

		var pos = triggerEl.offset();
		var h = triggerEl.outerHeight();
		var menuEl = $(triggerEl.data('menu'));

		triggerEl.addClass('open')
		menuEl.css({
			top: pos.top + h,
			left: pos.left
		});

		if (!menuEl.hasClass('has-init')) {
			menuEl.addClass('has-init');
			menuEl.find('li').on('click', function(ev) {
				if ($(ev.target).is('a')) {
					return;
				}
				ev.preventDefault();
				window.location = $(this).find('a').first().attr('href');
			})
		}

		menuEl.show();
		triggerEl.show();
		this.headerMenuBackdrop.show();
	},

	closeHeaderMenu: function() {
		this.menuTriggerEls.removeClass('open');
		var vis = this.menuEls.filter(':visible');
		vis.fadeOut('fast');
		this.headerMenuBackdrop.hide();
	},

	/**
	 * Dismiss a help message. This removes the help element, and sends an ajax
	 * request to the server to record the dismiss so it doesnt show again.
	 *
	 * The element must have a data-message-id attribute.
	 *
	 * @param el
	 */
	dismissHelpMessage: function(el) {
		el = $(el);

		var messageId = el.data('message-id');

		el.remove();

		if (!messageId) {
			return;
		}

		$.ajax({
			dataType: 'json',
			url: BASE_URL + 'agent/misc/dismiss-help-message/' + escape(messageId),
			type: 'GET'
		});
	},


	_initHelp: function() {
		this.helpBtn = $('#dp_pagehelp_btn');
		this.helpBox = $('#dp_pagehelp');

		if (!this.helpBox.length) {
			return;
		}

		if (this.helpBox.is(':visible')) {
			this.helpBtn.addClass('on');
		}

		this.helpBtn.on('click', function() {

		});
	}
});

Orb.createNamespace('DeskPRO.Admin');

/**
 * A popout window just has a basic layout for header/footer
 */
DeskPRO.Admin.PopoutWindow = new Class({
	Extends: DeskPRO.Admin.Window,

	initialize: function() {
		$('body').layout({
			applyDefaultStyles: false
		});
	}
});
Orb.createNamespace('DeskPRO.Admin.PageHandler');

DeskPRO.Admin.PageHandler.Basic = new Orb.Class({

	Implements: [Orb.Util.Events],

	initialize: function(context, options) {

		this.options = {};
		this.meta = {};

		if (context) {
			this.contextEl = $(context);
		} else {
			this.contextEl = $(document.body);
		}

		options = options || {};
		this.options = options;

		this.messageBroker = DeskPRO_Window.getMessageBroker();
	},

	getMessageBroker: function() {
		return this.messageBroker;
	},

	initPage: function() {

	},

	/**
	 * Set metadata about this page.
	 *
	 * @param mixed name Either a string name to use with value, or an object of key/value pairs
	 * @param mixed value Only used if name is a string, the value to set
	 */
	setMetaData: function(name, value) {
		// Assigning multiple values from a hash
		if (value === undefined && typeOf(name) == 'object') {
			this.meta = Object.merge(this.meta, name);
		} else {
			this.meta[name] = value;
		}
	},



	/**
	 * Get a hash of all the metadata.
	 *
	 * @return {Object}
	 */
	getAllMetaData: function() {
		return this.meta;
	},



	/**
	 * Get a specific piece of metadata.
	 *
	 * @param {String} name The name of the data you want
	 * @param mixed default_value The value to return if the metadata is undefined
	 */
	getMetaData: function(name, default_value) {
		if (default_value === undefined) {
			default_value = null;
		}

		if (this.meta[name] === undefined) {
			return default_value;
		}

		return this.meta[name];
	}
});

Orb.createNamespace('DeskPRO.Admin');

/**
 * Adds drag+drop reordering to table rows
 */
DeskPRO.Admin.TableReorder = new Orb.Class({

	initialize: function(table) {
		var self = this;

		this.table = table;

		var sendUpdate = function() {
			var postData = [];
			$('tr[data-item-id]', table).each(function() {
				if ($(this).data('item-id')) {
					postData.push({
						name: 'display_order[]',
						value: $(this).data('item-id')
					});
				}
			});

			$.ajax({
				url: table.data('reorder-save-url'),
				type: 'POST',
				data: postData
			});
		};

		this.table.sortable({
			items: 'tbody',
			handle: 'tr.depth-0',
			placeholder: {
				element: function() {
					return $('<tbody class="placeholder"><tr><td colspan="100">&nbsp;</td></tr></tbody>');
				},
				update: function() {
					return;
				}
			},
			helper: function(event, element) {
				var t = self.table.clone(false);
				t.empty();
				t.append(element.clone());
				t.addClass('dragging');

				$('tr td:not(.title)', t).remove();
				t.css('width', 300);
				return t;
			},
			update: function() {
				sendUpdate();
			}
		});

		$('tbody', this.table).each(function() {
			var tbody = $(this);
			tbody.sortable({
				items: 'tr.depth-1',
				placeholder: {
					element: function() {
						return $('<tr class="placeholder"><td colspan="100">&nbsp;</td></tr>');
					},
					update: function() {
						return;
					}
				},
				helper: function(event, element) {
					var t = self.table.clone(false);
					t.empty();
					t.append(element.clone());
					t.addClass('dragging');
					$('tr td:not(.title)', t).remove();
					t.css('width', 300);
					return t;
				},
				update: function() {
					sendUpdate();
				}
			});
		});
	}
});

Orb.createNamespace('DeskPRO.Form');

/**
 * A rule builder is a form widget that lets you add multiple "rules" to a list.
 * A rule conists of a rule type (for example, "category"), an op ("is" or "is not" etc),
 * and then a user input or selection (the actual category choice).
 *
 * This builder handles everything except form naming (eg. rule[0][type] etc), and when
 * to add rows (eg. on a button click). Some other component will figure those parts out.
 *
 * Example:
 *    <script type="text/javascript" charset="utf-8">
 *        $(document).ready(function() {
 *            var editor = new DeskPRO.Form.RuleBuilder($('#rules-tpl'));
 *            $('#add_rule_btn').data('add-count', 0).on('click', function() {
 *                var count = parseInt($(this).data('add-count'));
 *                var basename = 'newrule['+count+']';
 *
 *                $(this).data('add-count', count+1);
 *
 *                editor.addNewRow($('#rules'), basename);
 *            });
 *        });
 *    </script>
 *    <input type="button" value="Add Rule" id="add_rule_btn" />
 *    <div id="rules"></div>
 *    <div id="rules-tpl" style="display:none">
 *        <div class="row"><div class="type"></div><div class="op"></div><div class="choice"></div></div>
 *        <div class="type" title="Department" data-rule-type="department">
 *            <div class="op"><select name="op"><option value="is">is</option><option value="not">is not</option></select></div>
 *            <div class="choice"><select name="department"><option value="1">Sales</option><option value="2">Support</option></select></div>
 *        </div>
 *    </div>
 */
DeskPRO.Form.RuleBuilder = new Orb.Class({
	Implements: [Orb.Util.Events],

	/**
	 * @param {jQuery} ruleTpl This is the wrapper element that contains the templates used for each rule type
	 */
	initialize: function(ruleTpl) {
		this.ruleTpl = null;
		this.typeSelectHtml = null;
		this.types = {};
		this.rowDestroy = {};
		this.enableTypes = {};

		this.ruleTpl = ruleTpl;
		var self = this;

		this.genTypeSel();
	},

	enableType: function(type) {
		this.enableTypes[type] = true;
		this.genTypeSel();
	},

	disableType: function(type) {
		this.enableTypes[type] = false;
		this.genTypeSel();
	},

	genTypeSel: function() {
		var groups = {};
		var self = this;
		if (this.typeSel) {
			this.typeSel.remove();
			this.typeSel = null;
		}

		if (document.getElementById('dp_admin_page')) {
			var html = ['<select name="type" style="max-width: 400px;"><option>&nbsp;</option>'];
		} else {
			var html = ['<select name="type" style="max-width: 320px;"><option>&nbsp;</option>'];
		}
		$('.builder-type', this.ruleTpl).each(function(i,el) {
			var type = $(el).data('rule-type');
			if ($(el).data('type-off') && !self.enableTypes[type]) {
				return;
			}

			var title = $(el).attr('title');
			var subgroup = $(el).data('rule-group');

			self.types[type] = title;

			if (subgroup) {
				var id = Orb.uuid();
				if (!groups[subgroup]) {
					groups[subgroup] = {'id': id, types: []};
					html.push('<optgroup label="'+subgroup+'" class="'+id+'"></optgroup>');
				}

				groups[subgroup]['types'].push([type, title]);
			} else {
				html.push('<option value="' + type + '">' + title + '</option>');
			}
		});
		html.push('</select>');
		html = html.join('');

		var typeSel = $(html);

		Object.each(groups, function(info, group) {
			var ul = $('optgroup.' + info.id, typeSel);
			var lis = [];
			Array.each(info.types, function(type) {
				lis.push('<option value="' + type[0] + '">' + type[1] + '</option>');
			});

			var lis = $(lis.join(''));
			ul.append(lis);
		});

		this.typeSel = typeSel;
		this.typeSel.css({
			position: 'absolute',
			bottom: 0,
			left: 0,
			visibility: 'hidden'
		});
		this.typeSel.appendTo('body');
		this.typeSel.css('width', this.typeSel.width() + 35);
		this.typeSel.hide();
	},

	destroy: function() {
		Object.each(this.rowDestroy, function (rowDestroy) {
			Array.each(rowDesotry, function (item) {
				if (item.destroy) {
					item.destroy();
				} else if (item.remove) {
					item.remove();
				}
			});
		});

		this.typeSel.remove();
	},

	/**
	 * Add a new rule row
	 *
	 * @param  {jQuery} addToEl The element to append the new rule to
	 * @param  {String} formBaseName The base name for the form. For example, newrule[0], and after you might use newrule[1] etc.
	 * @param  {Object} existing Existing data to set
	 * @return {jQuery} The newly added row
	 */
	addNewRow: function(addToEl, formBaseName, existing) {

		var isStatic = addToEl.is('.static-list');

		if (existing && !this.types[existing.type]) {
			return null;
		}

		var rowId = Orb.uuid();

		var new_row = $('.row', this.ruleTpl).children().clone();
		new_row.data('row-id', rowId);

		if (isStatic) {
			new_row.addClass('static-list');
		}

		// Add select
		var select = this.typeSel.clone();
		select.css({
			position: 'static',
			left: '',
			bottom: ''
		});
		$('.builder-type-choice', new_row).append(select);

		var self = this;

		$('.builder-remove', new_row).on('click', function() {
			self.removeRow(new_row);
		});

		// Update its name
		if (formBaseName) {
			new_row.data('form-base-name', formBaseName);
			this.updateFormName(new_row, formBaseName);
		}

		// Handle when its type is changed
		select.on('change', (function() {
			this.handleSelectChange(new_row);
		}).bind(this));

		$(addToEl).append(new_row);

		if (!isStatic) {
			DP.select(select);
		} else {
			var lbl = $('<span />');
			lbl.text(select.find('option:selected').text());
			select.hide().after(lbl);
		}

		var opt = false;
		if (existing && this.types[existing.type]) {

			select.val(existing.type).change();

			var label = $('.builder-type .current-value', new_row);
			label.text(this.types[existing.type]);

			this.handleSelectChange(new_row);
			$('.builder-op select', new_row)
				.val(existing.op)
				.addClass('op')
				.change();

			if (typeof existing.options == 'string' || typeof existing.options == 'number' || typeOf(existing.options) != 'object') {
				// If its just one item, then we'll just assume its the first field
				var els = $(':input, textarea, select', new_row).filter(':not(.op, .type)').first().val(existing.options);
			} else {
				// Otherwise we'll assume its a k=>v array
				Object.each(existing.options, function(val, name) {
					if (!name || !name.length) return;

					var name_safe = name.replace(/\[/, '\\[').replace(/\]/, '\\]');
					if (typeof val == 'string' || typeof val == 'number') {
						var el = $('[name="'+name_safe+'"], [name$="'+this.makeArrayName(name,true)+'"]', new_row).first().val(val).change();
						if (el.is('select')) {
							el.find('option').each(function() {
								if (this.value == val) {
									$(this).prop('selected', true);
								}
							});
						}
					} else if (typeOf(val) == 'object') {
						Object.each(val, function(subval, subname) {
							var sub_name = name_safe + "["+subname+"]";
							var sub_name_safe = name_safe + "\\["+subname+"\\]";

							if (typeOf(subval) == 'object') {
								Object.each(subval, function(v, k) {
									var k_name = sub_name + "[" + k + "]";
									var el = $('[name$="'+this.makeArrayName(k_name,true)+'"]', new_row).first();
									if (el.is(':checkbox')) {
										el.prop('checked', true).change();
									} else {
										el.val(v).change();
									}
								}, this);
							} else if (typeOf(subval) == 'array') {
								Array.each(subval, function(v) {
									var k_name = sub_name + "[]";
									var el = $('[name$="'+this.makeArrayName(k_name,true)+'"]', new_row);
									if (el.is('select')) {
										el.find('[value="' + v + '"]').prop('selected', true);
									}
								}, this);
							} else {
								var el = $('[name="'+sub_name_safe+'"], [name$="'+this.makeArrayName(sub_name,true)+'"]', new_row).first().val(subval).change();
							}
						}, this);
					} else if (typeOf(val) == 'array') {
						if (name == 'labels') {
							var texts = [];
							var labelval = $('.builder-options select.label-values', new_row);
							Array.each(val, function(subval) {
								texts.push(subval);
							});

							$('.builder-options .menu-trigger', new_row).text(texts.join(', ')).data('select-texts', texts);
						}

						Array.each(val, function(subval) {
							var el = $('option[value="'+subval+'"]', new_row).first().get(0);
							if (el) el.selected = true;
						}, this);
					} else {
						var el = $('[name="'+name_safe+'"], [name$="'+this.makeArrayName(name,true)+'"]', new_row).first().val(val).change();
					}
				}, this);
			}

			var ruleHandler = new_row.data('rule-handler-inst');
			if (ruleHandler) {
				ruleHandler.initValues();
			}
		} else {
			existing = null;
		}

		this.fireEvent('newRow', [new_row, addToEl, existing]);

		return new_row;
	},



	/**
	 * When a select element is changed we need to update the op and choices.
	 *
	 * @param {jQuery} row The row that we need to update
	 */
	handleSelectChange: function(row) {

		// Destroy previous
		this.destroyRow(row);

		var isStatic = row.is('.static-list');

		var rowId = row.data('row-id');
		var rowDestroy = [];

		var type = $('.builder-type-choice select', row).val();

		var rule_tpl = $('.builder-type[data-rule-type="'+type+'"]', this.ruleTpl);

		var op = $('div.builder-op', rule_tpl).children().clone();

		var rule_options_tpl = $('.builder-options', rule_tpl);
		var choice = rule_options_tpl.clone();
		if (!rule_options_tpl.hasClass('newline')) {
			choice.css('display', 'inline');
		}

		$('.builder-op', row).empty().append(op);
		$('.builder-op', row).find('select').addClass('op');
		$('.builder-options', row).empty().append(choice);

		row.find('select.op').css('visibility', 'hidden');
		if (!isStatic) {
			DP.select(row.find('select.op'));
		} else {
			var lbl = $('<span />');
			lbl.text(row.find('select.op').find('option:selected').text());
			row.find('select.op').hide().after(lbl);
		}

		var ruleHandlerName = rule_tpl.data('rule-handler');
		var ruleHandler = null;
		if (ruleHandlerName) {
			ruleHandlerObj = Orb.getNamespacedObject(ruleHandlerName);
			ruleHandler = new ruleHandlerObj({
				ruleBuilder: this,
				rowEl: row,
				rowId: rowId,
				opMenu: row.find('select.op')
			});

			rowDestroy.push(ruleHandler);
		}

		var numChilds = choice.children().length;

		if (numChilds == 1) {
			var choiceSel = row.find('.builder-options').find('select').not('.no-auto');
			if (choiceSel.length) {
				choiceSel.css('visibility', 'hidden');
				choiceSel.each(function() {
					if (!isStatic) {
						var sel = $(this);
						sel.css('visibility', 'hidden');
						window.setTimeout(function() {
							if (sel.attr('multiple')) {
								var cellWidth = sel.closest('td').find('> .builder-options').width();
								sel.width(cellWidth - 10);
							}
							DP.select(sel);
						}, 150);
					} else {
						var lbl = $('<span />');
						lbl.text($(this).find('option:selected').text());
						$(this).hide().after(lbl);
					}
				});
			}
		}
		DP.select(row.find('.select2'));

		if (row.data('form-base-name')) {
			this.updateFormName($('.builder-op', row), row.data('form-base-name'));
			this.updateFormName($('.builder-options', row), row.data('form-base-name'));
		}

		if (ruleHandler) {
			ruleHandler.initRow();
			row.data('rule-handler-inst', ruleHandler);
		}

		if (rowDestroy.length) {
			this.rowDestroy[rowId] = rowDestroy;
		}

		var opSel = $('.builder-op select', row);
		var updateOp = function() {
			var val = opSel.val();
			if (val == 'changed') {
				$('.builder-options', row).hide();
			} else {
				$('.builder-options', row).show();
			}
		};
		opSel.on('change', updateOp);

		this.fireEvent('selectChange', [row, type]);
	},

	destroyRow: function(row) {
		var rowId = row.data('row-id');
		if (this.rowDestroy[rowId]) {
			Array.each(this.rowDestroy[rowId], function(item) {
				if (item.destroy) {
					item.destroy();
				} else if (item.remove) {
					item.remove();
				}
			});
			delete this.rowDestroy[rowId];
		}
	},

	removeRow: function(row) {
		this.destroyRow(row);
		row.remove();
	},



	/**
	 * This updates the form name to prepend a basename, and turns it into an array usable
	 * by php. For example, if the name was before type and formbaseName is newrule[1],
	 * the new form name is newrule[1][type].
	 *
	 * @param {jQuery} el The element to look within to change ALL names of
	 * @param {String} formBaseName The base form name to set
	 */
	updateFormName: function(el, formBaseName) {
		$('[name]', el).each(function() {
			var name = $(this).attr('name');
			name = name.replace(/^([\w\d]*)/, '[$1]');
			name = formBaseName + name;

			$(this).attr('name', name);
		});
	},

	makeArrayName: function(name, safe) {
		if (name.indexOf('[') === -1) {
			name = '[' + name + ']';
		}  else {
			name = name.replace(/^([\w\d]+)\[(.*?)$/, '[$1][$2');
		}

		if (safe) {
			name = name.replace(/\[/g, '\\[').replace(/\]/g, '\\]');
		}

		return name;
	}
});

Orb.createNamespace('DeskPRO.Agent.RuleBuilder');

/**
 * A limited verso
 */
DeskPRO.Agent.RuleBuilder.TermAbstract = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(options) {
		this.options = {
			ruleBuilder: null,
			rowEl: null,
			rowId: null,
			opMenu: null
		};

		if (options) this.setOptions(options);

		this.ruleBuilder = this.options.ruleBuilder;
		this.rowEl = $(this.options.rowEl);
		this.rowId = this.options.rowId;
		this.opMenu = this.options.opMenu;

		this.init();
	},

	init: function() { },

	initRow: function() { },

	initValues: function() { }
});
Orb.createNamespace('DeskPRO.Admin.RuleBuilder');

DeskPRO.Admin.RuleBuilder.TemplateEdit = new Orb.Class({
	Extends: DeskPRO.Agent.RuleBuilder.TermAbstract,

	initRow: function() {
		var self = this;
		this.overlay = null;

		this.tplInput = this.rowEl.find('input.tpl-name');
		this.currentValue = this.rowEl.find('.status-value');
		this.trimCurrentValue();
		this.currentValue.on('click', this.show.bind(this));

		this.tplDir    = this.currentValue.data('tpldir');
		this.tplPrefix = this.currentValue.data('tplprefix')
	},

	trimCurrentValue: function() {
		var text = $.trim(this.currentValue.text());
		text = text.replace(/DeskPRO:custom_emails_user:/, '');
		text = text.replace(/DeskPRO:custom_emails_agent:/, '');
		text = text.replace(/DeskPRO:emails_user:/, '');
		text = text.replace(/DeskPRO:emails_agent:/, '');

		this.currentValue.text(text);
	},

	initValues: function() {
		if (this.tplInput.val()) {
			this.currentValue.text(this.tplInput.val());
			this.trimCurrentValue();
		} else {
			this.currentValue.text('(click to change)');
		}
	},

	hide: function() {
		if (this.overlay) {
			this.overlay.close();
		}
	},

	destroy: function() {
		if (this.tplOverlayEl) {
			this.tplOverlayEl.remove();
			this.tplOverlayBack.remove();
		}

		if (this.overlay) {
			this.overlay.destroy();
		}

		if (this.newOverlay) {
			this.newOverlay.destroy();
		}
	},

	show: function() {
		if (!this.overlay) {
			var self = this;
			this.overlay = new DeskPRO.UI.Overlay({
				fullScreen: true,
				contentMethod: 'ajax',
				contentAjax: {
					url: BASE_URL + 'admin/templates/mini-manager/' + this.tplDir + '/' + this.tplPrefix
				},
				onAjaxDone: function() {
					self.initEditor(self.overlay.elements.wrapper);
				}
			});
		}

		this.overlay.open();
	},

	initEditor: function(el) {
		var self = this;
		this.wrapperEl = el;

		el.on('click', '.edit-tpl-trigger', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			var row = $(this).closest('tr');
			var name = row.data('template-name');
			self.openTemplateEditor(name);
		});

		el.on('click', '.delete-tpl-trigger', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			var row = $(this).closest('tr');
			var name = row.data('template-name');

			row.slideUp('fast', function() {
				row.remove();
			});

			$.ajax({
				url: BASE_URL + 'admin/templates/revert-template.json?name=' + name,
				type: 'POST'
			});
		});

		el.find('.create-new-tpl-trigger').on('click', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			self.openNewOverlay();
		});

		el.find('button.apply-trigger').on('click', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			var val = el.find(':radio:checked').val().trim();
			if (val) {
				self.tplInput.val(val);
				self.currentValue.text(val);
				this.trimCurrentValue();
			} else {
				self.tplInput.val('');
				self.currentValue.text('(click to change)');
			}

			self.overlay.close();
		});
	},

	openNewOverlay: function() {
		if (!this.newOverlay) {
			var self = this;
			var el = self.wrapperEl.find('.dp-tpl-new-overlay');
			this.newOverlay = new DeskPRO.UI.Overlay({
				contentElement: el,
				onBeforeOverlayOpened: function() {
					var html = '<option value="">None (blank template)</option>';

					self.wrapperEl.find('tr.template-row').each(function() {
						html += '<option value="' + $(this).data('template-name') +'">' + $(this).find('h3').text().trim() + '</option>';
					});

					el.find('select.copy_tpl').empty().html(html);
				}
			});

			el.find('button.save-trigger').on('click', function() {
				el.find('.overlay-footer').addClass('loading');

				var postData = {
					name: el.find('input.name').data('tpl-name-prefix') + el.find('input.name').val() + '.html.twig',
					copy_tpl: el.find('select.copy_tpl').val()
				};

				$.ajax({
					url: BASE_URL + 'admin/templates/create-template',
					data: postData,
					complete: function() {
						el.find('.overlay-footer').removeClass('loading');
					},
					success: function(data) {
						var tpl = self.wrapperEl.find('.template-row-tpl').get(0).innerHTML;
						var row = $(tpl);
						row.data('template-name', data.name);
						row.find('.place-title').text(data.name);
						row.find(':radio').val(data.name);

						row.insertBefore(self.wrapperEl.find('.custom-template-make-row'));

						self.newOverlay.close();
					}
				});
			});
		}

		this.newOverlay.open();
	},



	//##################################################################################################################
	//# Template Editing
	//##################################################################################################################

	openTemplateEditor: function(template_name) {
		if (!this.tplOverlayEl) {
			this.tplOverlayBack = $('<div class="backdrop fade" />').hide().appendTo('body').on('click', this.closeTemplateEditor.bind(this));
			this.tplOverlayEl = this.wrapperEl.find('.dp-tpl-edit-overlay').detach().appendTo('body');
			this.tplOverlayEl.find('.close-overlay').on('click', this.closeTemplateEditor.bind(this));
			this.tplOverlayEl.find('.save-trigger').on('click', this.saveTemplateEditor.bind(this));
			this.tplOverlayEl.find('.revert-trigger').on('click', this.revertTemplateEditor.bind(this));

			this.withSubject = this.tplOverlayEl.find('.subject-field');
			if (!this.withSubject[0]) {
				this.withSubject = null;
			}
		}

		this.editingTemplate = template_name;
		this.tplOverlayEl.find('.template-title').text(template_name);
		this.tplOverlayEl.find('textarea.template-code').val('Loading template, please wait...').addClass('loading');
		this.tplOverlayBack.fadeIn('fast');
		this.tplOverlayEl.fadeIn('fast');

		this.tplOverlayEl.find('.overlay-footer').removeClass('loading');
		if (this.withSubject) {
			this.withSubject.hide().find('textarea.template-subject-code').val('');
			this.tplOverlayEl.find('textarea.template-code').css('height', '98%');
		}

		$.ajax({
			url: BASE_URL + 'admin/templates/get-template-code?name=' + template_name,
			context: this,
			error: function() {
				this.closeTemplateEditor();
			},
			success: function(val) {
				this.editingTemplate = template_name;

				code = val;

				if (this.withSubject) {
					var m = code.match(/\s*<dp:subject>[\s\S]*<\/dp:subject>\s*/);
					if (m) {
						this.withSubject.find('textarea.template-subject-code').val($.trim(m[1]));

						code = $.trim(code.replace(m[0], "\n"));
						this.withSubject.show();
						this.tplOverlayEl.find('textarea.template-code').css('height', '79%');
					}
				}

				this.tplOverlayEl.find('textarea.template-code').val(code).removeClass('loading');
			}
		});
	},

	revertTemplateEditor: function() {
		this.tplOverlayEl.find('.overlay-footer').addClass('loading');
		$.ajax({
			url: BASE_URL + 'admin/templates/revert-template.json?name=' + this.editingTemplate,
			type: 'POST',
			context: this,
			success: function(val) {
				this.closeTemplateEditor();
			}
		});
	},

	saveTemplateEditor: function() {
		this.tplOverlayEl.find('.overlay-footer').addClass('loading');

		var code = this.tplOverlayEl.find('textarea.template-code').val();

		if (this.withSubject) {
			code = '<dp:subject>' + this.withSubject.find('textarea.template-subject-code').val() + "</dp:subject>\n" + code;
		}

		var postData = {
			name: this.editingTemplate,
			code: code
		};

		$.ajax({
			url: BASE_URL + 'admin/templates/save-template.json',
			context: this,
			type: 'POST',
			data: postData,
			success: function(data) {
				this.tplOverlayEl.find('.overlay-footer').removeClass('loading');

				if (data.error) {
					alert(data.error_message + "\n\nLine: " + data.error_line);
					return;
				}

				this.closeTemplateEditor();
			}
		});
	},

	closeTemplateEditor: function() {
		if (!this.tplOverlayEl) return;
		this.tplOverlayEl.fadeOut('fast');
		this.tplOverlayBack.fadeOut('fast');
		this.editingTemplate = null;
	}
});

Orb.createNamespace('DeskPRO.Agent.RuleBuilder');

DeskPRO.Agent.RuleBuilder.DateTerm = new Orb.Class({
	Extends: DeskPRO.Agent.RuleBuilder.TermAbstract,

	initRow: function() {
		this._initUi();
	},

	initValues: function() {
		var timestamp = null, date = null;

		timestamp = this.date1Input.val();
		if (timestamp) {
			date = new Date(timestamp * 1000);
			this.date1Widget.datepicker('setDate', date);
		}

		timestamp = this.date2Input.val();
		if (timestamp) {
			date = new Date(timestamp * 1000);
			this.date2Widget.datepicker('setDate', date);
		}

		//------------------------------
		// Existing values
		//------------------------------

		if (parseInt($('.date1-relative-input', this.rowEl).val())) {
			$('.relative1-input', this.date1).val($('.date1-relative-input', this.rowEl).val());
			$('.relative1-type', this.date1).val($('.date1-relative-type', this.rowEl).val());

			$('.date', this.date1).hide();
			$('.relative', this.date1).show().addClass('on');
		}

		if (parseInt($('.date2-relative-input', this.rowEl).val())) {
			$('.relative2-input', this.date2).val($('.date2-relative-input', this.rowEl).val());
			$('.relative2-type', this.date2).val($('.date2-relative-type', this.rowEl).val());

			$('.date', this.date2).hide();
			$('.relative', this.date2).show().addClass('on');
		}

		this.updateStatus();
	},

	_initUi: function() {

		//------------------------------
		// References to elements and move
		// overlay into body
		//------------------------------

		this.opInput = $('select.op', this.rowEl);

		this.date1Input = $('input.date1-input', this.rowEl);
		this.date2Input = $('input.date2-input', this.rowEl);

		this.date1Display = $('input.date1-display', this.rowEl);
		this.date2Display = $('input.date2-display', this.rowEl);

		this.currentValue = $('.status-value', this.rowEl);
		this.currentValue.text('(click to set)');
		this.currentValue.on('click', this.show.bind(this));

		this.dateWrap = $('.date-wrap', this.rowEl);

		this.backdrop = $('<div class="backdrop" style="display: none"></div>');
		this.backdrop.appendTo('body');
		this.backdrop.on('click', this.hide.bind(this));

		this.wrapper = $('<div class="field-overlay" style="display:none"><div class="close-trigger"></div></div>');
		$('.close-trigger', this.wrapper).on('click', this.hide.bind(this));

		this.dateWrap.detach().appendTo(this.wrapper).css('display', 'block');
		this.wrapper.appendTo('body');

		this.date1 = $('.date1', this.dateWrap);
		this.date2 = $('.date2', this.dateWrap);

		//------------------------------
		// Init date elements
		//------------------------------

		var self = this;
		this.date1Widget = $('.widget', this.date1).datepicker({
			dateFormat: 'M d, yy',
			onSelect: function(dateText, inst) {

				self.date1Input.val(self.date1Widget.datepicker('getDate').getTime() / 1000);

				self.date1Display.val(dateText);
				self.updateStatus();
			}
		});

		this.date2Widget = $('.widget', this.date2).datepicker({
			dateFormat: 'M d, yy',
			onSelect: function(dateText, inst) {

				self.date2Input.val(self.date2Widget.datepicker('getDate').getTime() / 1000);

				self.date2Display.val(dateText);
				self.updateStatus();
			}
		});

		var getDate = function (el) {
			var timestamp = strtotime(el.val());
			if (!timestamp) {
				return null;
			}

			var date = new Date(timestamp * 1000);
			return date;
		};

		//------------------------------
		// Detect changes to text fields for
		// human times "1 day ago" etc
		//------------------------------

		this.date1Display.on('change', function() {
			var date = getDate($(this));
			if (!date) {
				$(this).val('');
				return;
			}
			self.date1Widget.datepicker('setDate', date);
		});

		this.date2Display.on('change', function() {
			var date = getDate($(this));
			if (!date) {
				$(this).val('');
				return;
			}
			self.date2Widget.datepicker('setDate', date);
		});

		//------------------------------
		// Switcher between relative input
		//------------------------------

		$('.switcher', this.date1).on('click', (function() {
			var date = $('.date', this.date1);
			var rel  = $('.relative', this.date1);

			if (date.is(':visible')) {
				date.hide();
				rel.show().addClass('on');
			} else {
				rel.hide().removeClass('on');
				date.show();
			}
		}).bind(this));

		$('.switcher', this.date2).on('click', (function() {
			var date = $('.date', this.date2);
			var rel  = $('.relative', this.date2);

			if (date.is(':visible')) {
				date.hide();
				rel.show().addClass('on');
			} else {
				rel.hide().removeClass('on');
				date.show();
			}
		}).bind(this));
	},

	show: function() {

		if (this.opInput.val() == 'between') {
			this.dateWrap.addClass('two');
		} else {
			this.dateWrap.removeClass('two');
		}

		this.wrapper.css({
			left: this.currentValue.offset().left,
			top: this.currentValue.offset().top
		});

		this.backdrop.show();
		this.wrapper.show();
	},

	updateStatus: function() {

		var str1 = '', str2 = '', status = '';

		// If we're using the relative times, update the values on close
		var relative1 = $('.relative1', this.date1);
		var relative2 = $('.relative2', this.date2);

		if ($('.relative', this.date1).hasClass('on')) {
			$('.date1-relative-input', this.rowEl).val($('.relative1-input', this.date1).val());
			$('.date1-relative-type', this.rowEl).val($('.relative1-type', this.date1).val());

			// Erase any calendar time we mightve set before
			this.date1Input.val('');

			if ($('.relative1-input', this.date1).val().trim().length) {
				str1 = $('.relative1-input', this.date1).val() + ' ' + $('.relative1-type', this.date1).val() + ' ago';
			}
		} else {
			var date1 = this.date1Widget.datepicker('getDate');
			if (date1) {
				str1 = $.datepicker.formatDate('M d, yy', date1);
			}
		}

		if ($('.relative', this.date2).hasClass('on')) {
			$('.date2-relative-input', this.rowEl).val($('.relative2-input', this.date2).val());
			$('.date2-relative-type', this.rowEl).val($('.relative2-type', this.date2).val());

			this.date2Input.val('');

			if ($('.relative2-input', this.date2).val().trim().length) {
				str2 = $('.relative2-input', this.date2).val() + ' ' + $('.relative2-type', this.date2).val() + ' ago';
			}
		} else {
			var date2 = this.date2Widget.datepicker('getDate');
			if (date2) {
				str2 = $.datepicker.formatDate('M d, yy', date2);
			}
		}

		if (!str1.length) str1 = '(click to set)';
		if (!str2.length) str1 = '(click to set)';

		if (this.opInput.val() == 'between') {
			status = str1 + ' and ' + str2;
		} else {
			status = str1;
		}

		this.currentValue.text(status);
	},

	hide: function() {
		this.updateStatus();
		this.backdrop.hide();
		this.wrapper.hide();
	},

	destroy: function() {
		this.wrapper.remove();
		this.backdrop.remove();
	}
});

Orb.createNamespace('DeskPRO.Agent.RuleBuilder');

DeskPRO.Agent.RuleBuilder.LabelsTerm = new Orb.Class({
	Extends: DeskPRO.Agent.RuleBuilder.TermAbstract,

	initRow: function() {
		var self = this;
		this.inner = $('.label-chooser-wrap', this.rowEl);
		this.labelType = this.rowEl.data('label-type');
		this.labelsList = $('input.labels-box', this.rowEl).first();

		this.labelsInput = new DeskPRO.UI.LabelsInput({
			type: 'tickets',
			input: this.labelsList,
			onChange: this.updateLabels.bind(this)
		});

		this.currentValue = $('.status-value', this.rowEl);
		this.currentValue.text('(click to set)');
		this.currentValue.on('click', this.show.bind(this));

		this.values = $('.label-values', this.rowEl);

		this.backdrop = $('<div class="backdrop" style="display: none"></div>');
		this.backdrop.appendTo('body');
		this.backdrop.on('click', this.hide.bind(this));

		this.wrapper = $('<div class="field-overlay labels-chooser" style="display:none"><div class="close-trigger"></div></div>');
		$('.close-trigger', this.wrapper).on('click', this.hide.bind(this));

		this.inner.detach().appendTo(this.wrapper).css('display', 'block');
		this.wrapper.appendTo('body');

		window.setTimeout(function() {
			var vals = self.currentValue.data('select-texts');
			if (vals) {
				Array.each(vals, function(val) {
					var input = $('<option value="" selected="selected" />');
					input.val(val);

					input.appendTo(self.values);
				});
			}
		}, 450);
	},

	updateLabels: function() {
		var labels = this.labelsInput.getLabels();
		var status = '(click to set)';

		if (labels.length) {
			status = labels.join(', ');
		}

		this.currentValue.text(status);

		this.values.empty();

		if (labels.length) {
			Array.each(labels, function(label) {
				var input = $('<option value="" selected="selected" />');
				input.val(label);

				input.appendTo(this.values);
			}, this);
		}
	},

	show: function() {
		var vals = [];
		this.values.find('option').each(function() {
			vals.push($(this).val());
		});
		this.labelsList.select2('val', vals);

		this.wrapper.css({
			left: this.currentValue.offset().left,
			top: this.currentValue.offset().top
		});

		this.wrapper.show();
		this.backdrop.show().css('z-index', parseInt(this.wrapper.css('z-index')) - 1);
	},

	hide: function() {
		this.backdrop.hide();
		this.wrapper.hide();
	},

	destroy: function() {
		this.wrapper.remove();
		this.backdrop.remove();
		this.labelsInput.destroy();
	}
});

Orb.createNamespace('DeskPRO.Agent.RuleBuilder');

DeskPRO.Agent.RuleBuilder.SelectNewOption = new Orb.Class({
	Extends: DeskPRO.Agent.RuleBuilder.TermAbstract,

	initRow: function() {
		var self = this;
		this.select = this.rowEl.find('select.template_name');
		this.select.on('change', function() {
			self.updateOption();
		});
		this.input = this.rowEl.find('input.new_option');

		setTimeout(function() {
			self.rowEl.find('select.template_name, select.agents').each(function() {
				DP.select($(this));
			});
		},10);

		this.updateOption();
	},

	updateOption: function() {
		var val = this.select.val();
		if (val == "NEW") {
			this.input.show();
		} else {
			this.input.hide();
		}
	},

	show: function() {
		this.wrapper.css({
			left: this.currentValue.offset().left,
			top: this.currentValue.offset().top
		});

		this.wrapper.show();
		this.backdrop.show().css('z-index', parseInt(this.wrapper.css('z-index')) - 1);
	},

	hide: function() {
		this.backdrop.hide();
		this.wrapper.hide();
	},

	destroy: function() {

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
