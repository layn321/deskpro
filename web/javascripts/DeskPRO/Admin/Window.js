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
