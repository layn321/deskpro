Orb.createNamespace('DeskPRO.Agent');

/**
 * The super duper Window that connects controls from all over the interface.
 *
 * Contains a shared registry (perhaps not used?), global data like display names for agents
 * and other elements, and data for things like department-to-cat maps.
 *
 * Is also responsible for "routing" and loading page fragments. The router uses strings and decides where
 * they should be loaded (and how). For example, "navpane:filters/", the first part says it'll
 * be a navpane fragment. The second part is a simple URL we can load via AJAX.
 */
DeskPRO.Agent.Window = new Orb.Class({

	Extends: DeskPRO.BasicWindow,

	init: function() {

		this.onloadStack = [];
		this.dismissAlertQueue = [];
		this.routePrefixes = {};

		this.messageChanneler = null;
		this.poller = null;

		this.sections = {};
		this.openSection = null;

		this.listPage = null;

		this.innerLayout = null;

		this._alertOverlay = null;
		this._confirmOverlay = null;

		this.loadingIndicatorEl = null;
		this.loadingIndicatorCount = 0;
		this.ajaxErrorOverlay = null;

		this.cancelHashLoad = 0;
		this.activeListNav = null;
		this.activityTime = new Date();

		this.agentNotifyListShown = false;

		this.paneVis = {
			source: true,
			list: true,
			tabs: true
		};

		this.util = {
			modCountEl: function(el, op, num) {

				el = $(el);

				if (!num && num !== 0) num = 1;

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

				if (el.data('tag')) {
					$('i.' + el.data('tag')).text(count);
				}

				return count;
			},

			/**
			 * Get a "plain" article. ie of type="text/x-deskpro-plain"
			 *
			 * @param el
			 * @return {String}
			 */
			getPlainTpl: function(el) {

				if (!el) {
					DP.console.error('Invalid template element passed %o', el);
					return '';
				}

				var el = $(el);


				if (!el.length) {
					DP.console.error('No template element passed %o', el);
					return '';
				}

				var html = el.get(0).innerHTML;

				html = html.replace(/%startScript%/g, '<script>');
				html = html.replace(/%endScript%/g, '</script>');

				html = html.replace(/%scriptWord%/g, 'script');

				var uid = Orb.uuid();
				html = html.replace(/%baseId%/g, uid);

				var baseId = Orb.uuid();
				html = html.replace(/%baseId%/g, baseId);

				return html;
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

			ajaxWithClientMessages: function(options) {

				if (!options.data) {
					options.data = [];
				}

				// Assume a k:v object, convert it to an array
				if (!options.data.push) {
					var newData = [];
					Object.each(options.data, function(v, k) {
						newData.push({ name: k, value: v});
					});

					options.data = newData;
				}

				options.data.push({
					name: 'client_messages_since',
					value: DeskPRO_Window.getLastClientMessageId()
				});

				var old_success = function() {};
				if (options.success) {
					if (options.context) old_success = options.success.bind(options.context);
					else old_success = options.success;
				}

				var old_complete = function() {};
				if (options.complete) {
					if (options.context) old_complete = options.complete.bind(options.context);
					else old_complete = options.complete;
				}

				options.complete = function() {
					DeskPRO_Window.getMessageChanneler().poller.unpause();
					old_complete();
				}

				options.success = function(data) {
					DeskPRO_Window.getMessageChanneler().poller.unpause();
					if (options.execSuccessBefore) {
						old_success(data);
					}
					if (data.client_messages) {
						DeskPRO_Window.getMessageChanneler().handleMessageAjax(data.client_messages);
					}
					if (!options.execSuccessBefore) {
						old_success(data);
					}
				}

				options.dataType = 'json';

				DeskPRO_Window.getMessageChanneler().poller.pause();
				return $.ajax(options);
			},

			slugify: function(str) {
				str = str.replace(/[^a-zA-Z0-9\-]/g, '-');
				str = str.replace(/\-{2,}/g, '-');
				str = str.replace(/^\-/, '');
				str = str.replace(/\-$/, '');

				return str;
			},

			linkUrls: function(string) {
				string = string||'';
				string = Orb.linkUrls(string);
				string = string.replace(/<a /g, '<a target="_blank" ');
				return string;
			},

			dpCheckbox: function(input) {
				if (!input.attr('id')) {
					input.attr('id', Orb.getUniqueId('dp_chk'));
				}

				var id = input.attr('id');

				input.hide().addClass('with-dp-checkbox');

				var check = $('<span class="dp-checkbox" data-bound="#'+id+'" />');

				check.attr('id', Orb.getUniqueId('dp_chk'));
				if (input.is(':checked')) {
					check.addClass('checked');
				}

				input.data('bound', '#' + check.attr('id'));

				check.insertAfter(input)

				input.on('change', function(ev) {
					if ($(this).is(':checked')) {
						check.addClass('checked');
					} else {
						check.removeClass('checked');
					}
				});
				check.on('click', function(ev) {
					ev.stopPropagation();
					if (input.is(':checked')) {
						$(this).addClass('checked');
					} else {
						$(this).removeClass('checked');
					}
				});
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

			updateUserEmailAddressDisplay: function(person_id, email) {
				var sel = $('b.pemail-' + person_id);
				var mode = 'chance';
				if (!email || !email.length) {
					mode = 'hide';
				}

				sel.each(function() {
					var el = $(this);

					var hideEl = el;
					if (el.data('hide') && el.data('hide') == '@parent') {
						hideEl = el.parent();
					}

					if (mode == 'chance') {
						hideEl.show();
						el.text(email);
					} else {
						el.text('');
						hideEl.hide();
					}
				});
			},

			reloadInterface: function() {
				$('#reload_overlay').show().on('click', function(ev) { ev.stopPropagation(); });
				window.location.reload(false);
			}
		};
	},

	initPage: function() {

		var startHash = window.location.hash + "";
		startHash = startHash.substring(1);

		var loadNewTicket = false;
		if (loadNewTicket = window.location.hash.match(/#newticket:(\d+)/)) {
			loadNewTicket = loadNewTicket[1];
		}

		var loadSearchTerm = false;
		if (loadSearchTerm = window.location.hash.match(/#q:(.*?)$/)) {
			loadSearchTerm = loadSearchTerm[1];
		}

		var loadVis = false;
		if (loadVis = window.location.hash.match(/vis:([0-5]{1})/)) {
			loadVis = parseInt(loadVis[1]);
		}

		$.fn.qtip.zindex = 999999999;
		if (!$('html').hasClass('browser-ie')) {
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

		$('html').addClass('dp');
		this._initLayout();
		this._initWindowInterface();
		this._initBasic();
		this._initRoutes();
		this._initSections();
		this._initInterfaceServices();

		if (window.DESKPRO_SNIPPETS_USE_CLIENT_DB) {
			this.ticketSnippetDriver = new DeskPRO.Agent.TextSnippetClientDbDriver('tickets');
			this.chatSnippetDriver   = new DeskPRO.Agent.TextSnippetClientDbDriver('chat');
		} else {
			this.ticketSnippetDriver = new DeskPRO.Agent.TextSnippetAjaxDriver('tickets');
			this.chatSnippetDriver   = new DeskPRO.Agent.TextSnippetAjaxDriver('chat');
		}

		if (window.devicePixelRatio && window.devicePixelRatio >= 2) {
			$('body').addClass('dp-is-retina');
		}

		$('#dp_loading').remove();
		$('#page_loading').remove();
		$('#loading_css').remove();

		if (!window.DeskPRO_FragmentRouter) {
			DP.console.warn('window.DeskPRO_FragmentRouter is missing. Using empty router.');
			window.DeskPRO_FragmentRouter = {
				baseUrl: '',
				setBaseUrl: function(x) { this.baseUrl = x; },
				hasFragment: function() { return false; },
				getFragmentPattern: function() { return ''; },
				getFragmentType: function() { return ''; },
				getUrl: function() { return ''; },
				getUrlNamedArgs: function() { return ''; }
			};
		}

		this.fragmentRouter = window.DeskPRO_FragmentRouter;
		this.fragmentRouter.setBaseUrl(BASE_URL);

		var self = this;
		this.hashInitial = false;

		$.history.init(function(hash){
			if (!self.hashInitial) {
				self.hashInitial = true;
				return;
			}
			self.loadHashPath(hash);
		},{ unescape: ",/:" });

		if (!this.openSection) {
			this.switchToSection($('#dp_nav [data-section-handler]').first().attr('id'));
		}

		this.messageChanneler.poller.send();

		if (DESKPRO_TIME_OUT_OF_SYNC) {
			DESKPRO_TIME_OUT_OF_SYNC = false;
			$.ajax({
				url: BASE_URL + 'agent/misc/get-server-time',
				dataType: 'json',
				success: function(data) {

					$('#time_outofsync').find('.server_time').text(data.time_formatted);

					var now_ts = ((new Date()).getTime() / 1000) - (new Date().getTimezoneOffset() * 60);
					var diff = Math.abs(now_ts - data.timestamp);

					if (diff > 1200) {
						DESKPRO_TIME_OUT_OF_SYNC = diff;
						console.log("(Recheck) Time is off by %s seconds", diff);

						if (DESKPRO_TIME_OUT_OF_SYNC_IGNORE && Math.abs(diff - DESKPRO_TIME_OUT_OF_SYNC_IGNORE) < 480) {
							DESKPRO_TIME_OUT_OF_SYNC = null;
							console.log("(Recheck) Time offset is ignored");
						}
					}

					if (DESKPRO_TIME_OUT_OF_SYNC) {
						$('#time_outofsync').trigger('dp_open');
					}
				}
			});
		}

		var fn;
		while (fn = this.onloadStack.shift()) {
			fn();
		}

		$('#user_settings_link_profile').on('click', function(ev) {
			ev.preventDefault();
			$('#settingswin').trigger('dp_open', 'profile');
		});
		$('#user_settings_link_signature').on('click', function(ev) {
			ev.preventDefault();
			$('#settingswin').trigger('dp_open', 'signature');
		});
		$('#user_settings_link_ticketnotify').on('click', function(ev) {
			ev.preventDefault();
			$('#settingswin').trigger('dp_open', 'ticket-notify');
		});
		$('#user_settings_link_othernotify').on('click', function(ev) {
			ev.preventDefault();
			$('#settingswin').trigger('dp_open', 'notify');
		});
		$('#user_settings_link_macros').on('click', function(ev) {
			ev.preventDefault();
			$('#settingswin').trigger('dp_open', 'macros');
		});
		$('#user_settings_link_filters').on('click', function(ev) {
			ev.preventDefault();
			$('#settingswin').trigger('dp_open', 'filters');
		});
		$('#user_settings_link_snippets').on('click', function(ev) {
			ev.preventDefault();

			// Open it for the current ticket if we are viewing one
			var currentTab = DeskPRO_Window.TabBar.getActiveTab();
			if (currentTab && currentTab.page && currentTab.page.TYPENAME && currentTab.page.TYPENAME == 'ticket') {
				currentTab.page.shortcutOpenSnippets();
			} else {
				var snippetsViewer = new DeskPRO.Agent.Widget.SnippetViewer({
					viewUrl: $(this).data('snippet-viewer-url'),
					destroyOnClose: true,
					onSnippetClick: function(evData) {
						evData.cancelClose = true;
						evData.snippetEl.find('.edit-trigger').click();
					}
				});
				snippetsViewer.open();
			}
		});

		$('#dp_nav_sections').find('ul').find('li').find('a').each(function() {
			$(this).qtip({
				position: {
					my: 'left center',
					at: 'right center',
					target: $(this)
				},
				content: {attr: 'title'},
				style: {
					classes: 'qtip-dark qtip-rounded'
				}
			});
		});

		$('.panevis-switcher').find('li').each(function() {
			$(this).qtip({
				position: {
					my: 'top center',
					at: 'bottom center',
					target: $(this)
				},
				content: {attr: 'title'},
				style: {
					classes: 'qtip-dark qtip-rounded'
				}
			});
		});

		$(document).on('click', '.click-confirm', function(ev) {
			if (!confirm($(this).data('confirm'))) {
				ev.preventDefault();
			}
		});

		$(document).on('click', '.twitter-user-find', function(ev) {
			var name = $(this).data('name');
			if (name.indexOf('@') != 0) {
				name = '@' + name;
			}
			if (name.length > 1) {
				ev.preventDefault();

				$.ajax({
					url: BASE_URL + 'agent/twitter/user/find',
					data: {name: name},
					type: 'GET',
					dataType: 'json',
					success: function(data) {
						if (data.success) {
							var route = 'page:' + data.url;
							DeskPRO_Window.runPageRoute(route);
						} else {
							alert("There is no Twitter user named " + name);
						}
					}
				});
			}
		});

		// Used by the poller to send flag to update the last active time
		$(document).on('click mousemove keypress', function() {
			self.activityTime = new Date();
		});

		if (document.getElementById('notice_trigger')) {
			this._noticeIndex = -1;
			this._noticeIds = $('#notice_trigger').data('ids').split(',');
			$('#notice_trigger').on('click', function(ev) {
				ev.preventDefault();
				ev.stopPropagation();
				self.openNotices();
			});
		}

		$('#dp_source, #dp_left_collapsed').on('click', '.toggle_source_pane', function(ev) {
			ev.preventDefault();
			DeskPRO_Window.setPaneVis('source', !DeskPRO_Window.paneVis.source);
		});
		$('#dp_list, #dp_left_collapsed').on('click', '.toggle_list_pane', function(ev) {
			ev.preventDefault();
			DeskPRO_Window.setPaneVis('list', !DeskPRO_Window.paneVis.list);
		});
		$('#dp_right_collapsed').on('click', function(ev) {
			ev.preventDefault();
			DeskPRO_Window.setPaneVis('tabs', true);
		});

		$('#dp_nav_sections').on('click', function() {
			if (!self.paneVis['source']) {
				self.paneVis['source'] = true;
				self.layout.doResize(true);
			}
		});

		$(document).on('click', '.panevis-toggle-sourcepane', function() {
			self.paneVis['source'] = !self.paneVis['source'];
			self.layout.doResize(true);
		});

		$(document).on('click', '.panevis-toggle-tableview', function() {
			self.paneVis['list'] = true;
			self.paneVis['tabs'] = false;
			self.layout.doResize(true);
		});
		$(document).on('click', '.panevis-toggle-normalview', function() {
			self.paneVis['list'] = true;
			self.paneVis['tabs'] = true;
			self.layout.doResize(true);
		});
		$(document).on('click', '.panevis-toggle-tabview', function() {
			self.paneVis['list'] = false;
			self.paneVis['tabs'] = true;
			self.layout.doResize(true);
		});

		$('#dp_list').on('click', '.maximise_list_pane', function(ev) {
			ev.preventDefault();

			if (!DeskPRO_Window.paneVis.source && !DeskPRO_Window.paneVis.tabs) {
				DeskPRO_Window.setPaneVis('source', true, 'tabs', true);
			} else {
				DeskPRO_Window.setPaneVis('source', false, 'tabs', false);
			}
		});

		$('#tabNavigationPane .maximise_tabs_pane').on('click', function(ev) {
			ev.preventDefault();

			if (!DeskPRO_Window.paneVis.source && !DeskPRO_Window.paneVis.list) {
				DeskPRO_Window.setPaneVis('source', true, 'list', true);
			} else {
				DeskPRO_Window.setPaneVis('source', false, 'list', false);
			}
		});


		$('#dp_header_userchat_btn').on('click', function() {
			if (DeskPRO_Window.sections.chat_section) {
				DeskPRO_Window.sections.chat_section.refreshOnlineUsers();
			}
			var wrap = $(this).parent();
			wrap.addClass('active');

			var closeFn = function() {
				wrap.removeClass('active');
			};

			Orb.shimClickCallback(closeFn, 'zindex-chrome0');
		});

		var isIe = $('html').hasClass('browser-ie');

		$('#dp_header').find('.btn-group-actions').find('.btn').on('click', function() {
			var wrap = $(this).parent();
			var btnMenu = wrap.find('.btn-menu');

			// Bug in IE10 means the li's dont render properly
			// until you force a repaint somehow while they are displayed
			// So we show with no opacity, toggle the display on li's
			// which does the trick of repainting them, then set the opacity
			// back to 1. The user doesnt see anything amiss and we solve the bug :)
			if (isIe) {
				btnMenu.css('opacity', 0);
			}

			wrap.addClass('active');
			Orb.Util.TimeAgo.refreshElements(wrap.find('time').toArray());

			if (isIe) {
				window.setTimeout(function() {
					btnMenu.find('li').css('display', 'block');
					btnMenu.css('opacity', 1);
				}, 10);
			}

			var closeFn = function() {
				wrap.removeClass('active');
			};

			if (!wrap.data('has-init')) {
				btnMenu.on('click', function(ev) {
					Orb.cancelEvent(ev);
					Orb.shimClickCallbackPop();
				});
			}

			Orb.shimClickCallback(closeFn, 'zindex-chrome0');

			if ($(this).hasClass('dp-recent-btn')) {

				DeskPRO_Window.recentTabs.idW = 0;
				var maxw = 0;
				DeskPRO_Window.recentTabs.list.find('strong').each(function() {
					var w = $(this).width();
					if (w > maxw) {
						maxw = w;
					}
				});

				DeskPRO_Window.recentTabs.idW = maxw;
				DeskPRO_Window.recentTabs.list.find('strong').css('min-width', maxw);

				$('#recent_tabs_list_filter').focus();
				$('#recent_tabs_list').parent().scrollTop(0);
			}
		});

		$('#dp_header_help_trigger').on('click', function(ev) {
			ev.preventDefault();

			var wrap = $('#dp_header_help');
			wrap.addClass('active');

			var closeFn = function() {
				wrap.removeClass('active');
			};

			if (!wrap.data('has-init')) {
				wrap.find('.btn-menu').on('click', function(ev) {
					Orb.cancelEvent(ev);
					Orb.shimClickCallbackPop();
				});
			}

			Orb.shimClickCallback(closeFn, 'zindex-chrome0');
		});

		$('#dp_header_notify_wrap').find('> ul > li').on('click', function() {
			var wrap = $(this);
			wrap.addClass('active');
			Orb.Util.TimeAgo.refreshElements(wrap.find('time').toArray());

			var closeFn = function() {
				wrap.removeClass('active');
				Orb.shimClickCallbackPop();
			};

			if (!wrap.data('has-init')) {
				wrap.find('ul').on('click', function(ev) {
					closeFn();
				});
			}

			Orb.shimClickCallback(closeFn, 'zindex-chrome0');
		});

		if (loadNewTicket) {
			DeskPRO_Window.newTicketLoader.open(function(page) {
				var data = {
					person_id: loadNewTicket
				};
				page.setNewByPerson(data);
			});
		}

		if (loadSearchTerm) {
			$('#dp_search_box').focus().val(decodeURIComponent(loadSearchTerm)).trigger('keypress');
		}

		if (loadVis) {
			this.layout.enableHashUpdate = false;
			this.setPaneVisNum(loadVis);
			this.layout.enableHashUpdate = true;
		}

		this.cancelHashLoad = 0;
		this.loadHashPath(startHash);
	},

	addOnloadFunction: function(fn) {
		this.onloadStack.push(fn);
	},

	loadHashPath: function(browserHash, force) {

		if (this.DEBUG.disableUrlFragments) return;

		// This is sometimes set to prevent any of the below loading
		// to happen when the hash is updated to reflect an already-set
		// URL state
		if (this.cancelHashLoad > 0) {
			this.cancelHashLoad--;
			if (this.cancelHashLoad < 0) {
				this.cancelHashLoad = 0;
			}

			if (!force) {
				return;
			}
		}

		if (!browserHash.length) {
			return;
		}

		if (browserHash.indexOf('%') !== -1) {
			browserHash = decodeURIComponent(browserHash);
		}

		// Hashes are #keyword.tabid:arg1:arg2
		// tabid part is for non-unique pages (ie newticket) and
		// a user is clicking between tabs. It is optional,
		// and ignored if the tabid doesn't exist.

		// Hash segments are separated by commas. Each segment is a different
		// page. The first segment should be the list pane, but this is enforced
		// anyway by the fragment_type in routing.yml

		var segments = browserHash.split(',');
		var activateSection = null;
		var activateTabId = null;
		var firstTabId = null;
		var activateSettings = null;

		DeskPRO_Window.TabBar.options.activateNew = false;

		Array.each(segments, function (hash, i) {

			var m;
			if (m = hash.match(/app\.([a-zA-Z]+)/)) {
				activateSection = m[1];
				return;
			}

			if (m = hash.match(/settings\.([a-zA-Z0-9-]+)/)) {
				activateSettings = m[1];
				return;
			}

			// Active tab has .o on it, like ticket.o:1234
			// So detect that, and then remove the .o
			var isOpen = false;
			if (hash.match(/\.o:/)) {
				isOpen = true;
				hash = hash.replace(/\.o:/, ':');
			}

			var tabId = DeskPRO_Window.TabBar.findTabByFragment(hash);
			if (tabId) {
				tabId = tabId.id;
				if (isOpen) {
					activateTabId = tabId;
				} else if (!firstTabId) {
					firstTabId = tabId;
				}
				return;
			}

			var listPage = this.getCurrentListPage();
			if (listPage && listPage.getMetaData('url_fragment') == hash) {
				return;
			}

			var parts = hash.match(/^(.*?)(\.(.*?))?:(.*?)$/);

			if (!parts) {
				// Invalid
				return;
			}

			var tabId = null;
			if (parts[3]) {
				var tabId = parts[3];
			}

			var fragmentName = parts[1];
			var args = parts[4];

			if (!args.length) {
				args = [];
			} else {
				args = args.split(':');
			}

			if (!this.fragmentRouter.hasFragment(fragmentName)) {
				return;
			}

			var argRequired = false;
			switch (fragmentName) {
				case 'knowledgebase':
				case 'news':
				case 'downloads':
				case 'category':
				case 'status':
				case 'label':
				case 'ended':
					argRequired = true;
					break;
			}

			if (argRequired && !args.length) {
				return;
			}

			var url = this.fragmentRouter.getUrl(fragmentName, args);
			var type = this.fragmentRouter.getFragmentType(fragmentName);

			if (type == 'vis') {
			} else if (type == 'list') {
				this.loadingListFragment = hash;
				this.loadListPane(url, { url_fragment: hash });
			} else {
				this.loadingPageFragment = hash;
				this.loadPage(url, { url_fragment: hash, noToggle: true });
			}
		}, this);

		this.cancelHashLoad++;
		if (activateTabId) {
			DeskPRO_Window.TabBar.activateTabById(activateTabId);
		} else if (firstTabId) {
			DeskPRO_Window.TabBar.activateTabById(firstTabId);
		}

		if (activateSection) {
			var activateSectionId = null;
			Object.each(this.sections, function(section, id) {
				if (section.urlFragmentName && section.urlFragmentName == activateSection) {
					activateSectionId = id;
					return false;
				}
			});

			if (activateSectionId) {
				this.fragLoadingSection = activateSectionId;
				this.switchToSection(activateSectionId);
			}
		}

		if (activateSettings) {
			var settingsInterval = setInterval(function() {
				if (window.SETTINGS_WINDOW) {
					clearInterval(settingsInterval);
					settingsInterval = false;
					$('#settingswin').trigger('dp_open', activateSettings);
				}
			}, 250);
		}

		DeskPRO_Window.TabBar.options.activateNew = true;
	},

	updateWindowUrlFragment: function() {

		if (this.DEBUG.disableUrlFragments) return;
		if (!jQuery.history) return;

		var segments = [];

		if (this.openSection) {
			if (this.openSection.urlFragmentName) {
				segments.push('app.' + this.openSection.urlFragmentName);
			}
			if (this.openSection.listPage && this.openSection.listPage.getMetaData('url_fragment')) {
				segments.push(this.openSection.listPage.getMetaData('url_fragment'));
			}
		}

		if (DeskPRO_Window.TabBar) {			// Only if we have current tab, cuz no current tab means there are no tabs open at all
			$('#tabNavigationPane ul.dp-tab-list li').each(function() {
				var isActive = $(this).hasClass('activeTabList');

				var tab = $(this).data('tab');
				if (tab && tab.page && tab.page.getMetaData('url_fragment')) {
					var tabPage = tab.page;
					var hash = tabPage.getMetaData('url_fragment');

					if (isActive) {
						if (hash.indexOf(':') !== -1) {
							// ticket:123 to ticket.o:123
							hash = hash.replace(/:/, '.o:');
						} else {
							// somename to somename.o
							hash = hash + '.o';
						}
					}

					segments.push(hash);
				}
			});
		}

		var paneVisNum = this.getPaneVisNum();
		if (paneVisNum) {
			segments.push('vis:'+paneVisNum)
		}

		var browserHash = segments.join(',');

		this.cancelHashLoad++;
		jQuery.history.load(browserHash);
	},

	//#################################################################
	//# Global registry, getters
	//#################################################################

	/**
	 * Get the ID of the last client message.
	 */
	getLastClientMessageId: function() {
		if (this.messageChanneler.lastMessageId) {
			return this.messageChanneler.lastMessageId;
		}

		return 0;
	},

	forwardClientMessageData: function(data) {
		if (this.messageChanneler.handleMessageAjax) {
			this.messageChanneler.handleMessageAjax(data);
		}
	},


	/**
	 * Get the AJAX poller
	 */
	getPoller: function() {
		return this.messageChanneler.poller;
	},

	/**
	 * Get the tab watcher
	 */
	getTabWatcher: function() {
		return this.tabWatcher;
	},

	/**
	 * Get the tab strip
	 */
	getTabStrip: function() {
		return DeskPRO_Window.TabBar;
	},

	/**
	 * Get a name for some type of basic thing (department, category etc).
	 */
	getDisplayName: function(type, id) {
		if (!window.DESKPRO_NAME_REGISTRY[type] || !window.DESKPRO_NAME_REGISTRY[type][id]) {
			if (!window.DESKPRO_NAME_REGISTRY[type]) {
				DP.console.error('Unknown name type %s', type);
			}

			return null;
		}

		return window.DESKPRO_NAME_REGISTRY[type][id];
	},


	/**
	 * Get information about an agent like their name, initials, picture URL.
	 *
	 * @param {Integer} agent_id
	 * @return {Object}
	 */
	getAgentInfo: function(agent_id) {
		// offline list always has all agents and their info
		var agentEl = $('#agent_offline_list .agent-' + agent_id);

		if (!agentEl.length) {
			DP.console.error('Unknown agent %i', agent_id);
			return null;
		}

		return {
			id: agent_id,
			name: agentEl.data('agent-name'),
			email: agentEl.data('email'),
			shortName: agentEl.data('agent-short-name'),
			pictureUrl: agentEl.data('picture-url'),
			pictureUrlSizable: agentEl.data('picture-url-sizable')
		};
	},


	/**
	 * Get information about a team
	 *
	 * @param {Integer} agent_team_id
	 * @return {Object}
	 */
	getTeamInfo: function(team_id) {

		team_id = parseInt(team_id);

		// chat list always has teams and info
		var teamEl = $('#agent_team_list .team-' + team_id);

		if (!teamEl.length) {
			DP.console.error('Unknown team %i', team_id);
			return null;
		}

		return {
			id: team_id,
			name: teamEl.data('team-name'),
			pictureUrl: teamEl.data('picture-url'),
			pictureUrlSizable: teamEl.data('picture-url-sizable')
		};
	},


	/**
	 * Get a URL pattern
	 */
	getUrl: function(name, vars) {
		if (!window.DESKPRO_URL_REGISTRY[name]) {
			DP.console.error('Unknown url name %s', name);
			return null;
		}

		var url = window.DESKPRO_URL_REGISTRY[name];
		if (vars) {
			Object.each(vars, function(v,k) {
				url = url.replace('{'+k+'}', v);
			});
		}

		return url;
	},


	/**
	 * Get data
	 * @param name
	 */
	getData: function(name) {
		if (!window.DESKPRO_DATA_REGISTRY || !window.DESKPRO_DATA_REGISTRY[name]) {
			DP.console.error('Unknown data name %s', name);
			return null;
		}

		return window.DESKPRO_DATA_REGISTRY[name];
	},


	//#################################################################
	//# Simple UI features
	//#################################################################

	showAlert: function(msg, classname) {
		this._initAlertOverlay();

		if (typeof msg == 'string') {
			var msgEl = $('<div/>');
			msgEl.text(msg);
			msg = msgEl;
		}

		$('#alert_overlay_msg').empty().append(msg);

		var wrapper = this._alertOverlay.elements.wrapperOuter;
		var wrapperModel = this._alertOverlay.elements.modal;
		if (wrapper.data('added-class')) {
			wrapper.removeClass(wrapper.data('added-class'));
			wrapperModel.removeClass(wrapper.data('added-class'));
			wrapper.data('added-class', null);
		}
		if (classname) {
			wrapper.addClass(classname);
			wrapperModel.addClass(classname);
			wrapper.data('added-class', classname);
		}

		this._alertOverlay.openOverlay();
	},

	showConfirm: function(msg, callback_yes, callback_no, phrase_yes, phrase_no, w, h) {
		this._initConfirmOverlay();

		w = w || 350;
		h = h || 150;

		phrase_yes = phrase_yes || 'Okay';
		phrase_no = phrase_no || 'Cancel';

		this._confirmOverlay_callback_yes = callback_yes || function() { };
		this._confirmOverlay_callback_no = callback_no || function() { };

		$('#confirm_overlay').find('> .confirm-overlay').width(w).height(h);

		$('#confirm_overlay_msg').html(msg);
		$('#confirm_overlay .okay-trigger').text(phrase_yes);

		if (phrase_no == 'hidden') {
			$('#confirm_overlay .cancel-trigger').text(phrase_no).hide();
		} else {
			$('#confirm_overlay .cancel-trigger').text(phrase_no).show();
		}

		this._confirmOverlay.openOverlay();
	},

	showPrompt: function(msg, callback_ok, callback_cancel) {
		this._initPromptOverlay();

		this._promptOverlay_callback_yes = callback_ok || function() { };
		this._promptOverlay_callback_no = callback_cancel || function() { };

		$('#prompt_overlay_msg').html(msg);
		this._promptOverlay.openOverlay();
	},

	_initAlertOverlay: function() {
		if (this._alertOverlay) return;

		this._alertOverlay = new DeskPRO.UI.Overlay({
			zIndex: 'none', // the .window-alert sets the zindex
			contentElement: $('#alert_overlay'),
			customClassname: 'window-alert',
			onContentSet: function(eventData) {
				$('.close-trigger', eventData.wrapperEl).on('click', (function() {
					eventData.overlay.closeOverlay();
				}).bind(this));
			}
		});

		// Need to init it now because showAlert will try to set a
		// class on it sometimes, and we need the elements ready
		this._alertOverlay.initOverlay();
	},

	_initConfirmOverlay: function() {
		if (this._confirmOverlay) return;

		this._confirmOverlay_callback_yes = function() { };
		this._confirmOverlay_callback_no = function() { };

		var self = this;

		this._confirmOverlay = new DeskPRO.UI.Overlay({
			contentElement: $('#confirm_overlay'),
			zIndex: '50000',
			onContentSet: function(eventData) {
				$('.cancel-trigger', eventData.wrapperEl).on('click', (function() {
					eventData.overlay.closeOverlay();
					self._confirmOverlay_callback_no();
					self._confirmOverlay_callback_no = function() {};
				}).bind(this));
				$('.okay-trigger', eventData.wrapperEl).on('click', (function() {
					eventData.overlay.closeOverlay();
					self._confirmOverlay_callback_yes();
					self._confirmOverlay_callback_yes = function() {};
				}).bind(this));
			}
		});
	},

	_initPromptOverlay: function() {
		if (this._promptOverlay) return;

		this._promptOverlay_callback_yes = function() { };
		this._promptOverlay_callback_no = function() { };

		var self = this;

		this._promptOverlay = new DeskPRO.UI.Overlay({
			contentElement: $('#prompt_overlay'),
			zIndex: 'top',
			onContentSet: function(eventData) {
				$('.cancel-trigger', eventData.wrapperEl).on('click', (function() {
					eventData.overlay.closeOverlay();
					self._promptOverlay_callback_no($('#prompt_overlay_input').val(), $('#prompt_overlay'));
					self._promptOverlay_callback_no = function() {};
					$('#prompt_overlay_input').val('');
				}).bind(this));
				$('.okay-trigger', eventData.wrapperEl).on('click', (function() {
					eventData.overlay.closeOverlay();
					self._promptOverlay_callback_yes($('#prompt_overlay_input').val(), $('#prompt_overlay'));
					self._promptOverlay_callback_yes = function() {};
					$('#prompt_overlay_input').val('');
				}).bind(this));
			}
		});
	},

	showRefreshAlert: function(admin_name) {

		var self = this;

		if (this._refreshAlertTimeout) {
			window.clearTimeout(this._refreshAlertTimeout);
			this._refreshAlertTimeout = null;
		}

		if (!this._refreshAlertOverlay) {
			this._refreshAlertOverlay = new DeskPRO.UI.Overlay({
				contentElement: $('#refresh_alert_overlay'),
				zIndex: '50000',
				escapeClose: false,
				modalClickClose: false
			});

			$('#refresh_alert_overlay').find('button.okay-trigger').on('click', function(ev) {
				ev.preventDefault();
				ev.stopPropagation();

				if (self._refreshAlertTimeout) {
					window.clearTimeout(self._refreshAlertTimeout);
					self._refreshAlertTimeout = null;
				}
				window.location.reload(false);
			});
			$('#refresh_alert_overlay').find('button.cancel-trigger').on('click', function(ev) {
				ev.preventDefault();
				ev.stopPropagation();

				if (self._refreshAlertTimeout) {
					window.clearTimeout(self._refreshAlertTimeout);
					self._refreshAlertTimeout = null;
				}

				self._refreshAlertOverlay.closeOverlay();
			});
		}

		$('#refresh_alert_overlay').find('.admin-name').text(admin_name);

		var time = 30;
		var timeShow = $('#refresh_alert_overlay').find('.countdown').text(30);

		this._refreshAlertTimeout = window.setInterval(function() {
			time--;
			$('#refresh_alert_overlay').find('.countdown').text(time);

			if (time <= 0) {
				if (self._refreshAlertTimeout) {
					window.clearTimeout(self._refreshAlertTimeout);
					self._refreshAlertTimeout = null;
				}

				window.location.reload(false);
			}
		}, 1000);

		this._refreshAlertOverlay.openOverlay();
	},

	//#################################################################
	//# Routes and page loading
	//#################################################################

	addListPage: function(page) {
		DP.console.error('Invalid call to addListPage for %o', page);
		this.setListPage(page);
	},

	setListPage: function(page, noswitch) {

		// Route a list page fragment into the proper
		// section

		var testcl = function(x) {
			return page.getMetaData('fragmentClass', '').indexOf(x) != -1;
		};
		var handler = null;
		var sectionId = null;
		if (testcl('.Kb') || testcl('.News') || testcl('.Download') || testcl('.Publish') || testcl('PublishSearch')) {
			sectionId = 'publish_section';
		} else if (testcl('.Ticket') || testcl('.NewCustomFilter')) {
			sectionId = 'tickets_section';
		} else if (testcl('.People') || testcl('.Org')) {
			sectionId = 'people_section';
		} else if (testcl('.AgentChat')) {
			sectionId = 'agent_chat_section';
		} else if (testcl('.OpenChats') || testcl('.UserChatFilter')) {
			sectionId = 'chat_section';
		} else if (testcl('.Feedback') || testcl('.FeedbackSearch')) {
			sectionId = 'feedback_section';
		} else if (testcl('.TicketFilter') || testcl('.RecycleBin')) {
			sectionId = 'tickets_section';
		} else if (testcl('.Task')) {
			sectionId = 'tasks_section';
		} else if (testcl('.Twitter')) {
			sectionId = 'twitter_section';
		}

		if (sectionId) {
			handler = this.sections[sectionId];
		}

		if (!handler) {
			DP.console.error('List page fragment has no section: %s: %o', page.getMetaData('fragmentClass', ''), page);
			return;
		}

		if (typeof noswitch === undefined) {
			if (!noswitch && !handler.isVisible()) {
				noswitch = true;
			}

			if (handler.isVisible() && !handler.listPage) {
				noswitch = false;
			}
		}

		handler.setListPageFragment(page, noswitch);

		if (!noswitch) {
			this.listPage = page;
			this.switchToSection(sectionId, true);
			this.updateWindowUrlFragment();
		}
	},

	getListPage: function() {
		return this.listPage;
	},

	addPageTab: function(page) {
		DeskPRO_Window.TabBar.addTab(page);
	},

	/**
	 * Checks views for a specific page and removes it
	 */
	removePage: function(page) {
		DeskPRO_Window.TabBar.removeTabById(page.meta.tabId);
	},

	/**
	 * Add a loader for a particular prefix.
	 *
	 * @param {String} prefix The prefix to lisen for. Eg "navpane:tickets"
	 * @param {Function} callback The function to call when the prefix is used
	 */
	addPageRouteLoader: function(prefix, callback) {
		if (this.routePrefixes[prefix] == undefined) {
			this.routePrefixes[prefix] = [];
		}

		this.routePrefixes[prefix].push(callback);
	},


	/**
	 * Loads a route.
	 *
	 * @param {String} route The route to match, like navpane:tickets:filters
	 */
	runPageRoute: function(route, extraData) {
		var found_listener = false;

		var data = this.parseRoute(route);
		if (extraData) {
			data = Object.merge(extraData, data);
		}

		Object.each(this.routePrefixes, function(listeners, prefix) {
			if (route.indexOf(prefix) == 0) {
				Array.each(listeners, function(callback) {
					callback(data);
					found_listener = true;
				});
				if (data.stopListeners) {
					return true;
				}
			}
		}, this);

		if (!found_listener) {
			DP.console.error('Unknown route: %s', route);
		}
	},

	/**
	 * Parse a route into its parts
	 *
	 * @param {String} route
	 * @return {Object}
	 */
	parseRoute: function(route) {
		// Like:
		// master.masterTag:sectioninfo:moreinfo:url/here/at/end
		// (There might not be any sectioninfo)
		// Example:
		// listpane:/agent/ticket-search/filter/123

		var sections = route.split(':');
		var master = sections.shift();
		var masterTag = null;
		if (master.indexOf('.') != -1) {
			var tmp = master.split('.');
			master = tmp.shift();
			masterTag = tmp.pop();
		}

		var url = sections.pop();

		var data = {
			'route': route,
			'master': master,
			'masterTag': masterTag,
			'sections': sections,
			'url': url,
			stopListeners: false
		};

		return data;
	},



	/**
	 * Loads a route attached to an element. Useful for quickly assigning click events.
	 *
	 * @param {jQuery} el The element to inspect for a route
	 */
	runPageRouteFromElement: function(el, extraData) {

		el = $(el);

		if (el.is('.route-do-confirm')) {
			if (!el.is('.did-confirm')) {
				DeskPRO_Window.showConfirm('Are you sure?', function() {
					el.addClass('did-confirm');
					DeskPRO_Window.runPageRouteFromElement(el);
				});
				return;
			}
			el.removeClass('did-confirm');
		}

		if (el.is('.cancel-route')) {
			return;
		}

		if (!el.data('route')) {
			DP.console.error('Element has no route: %o', el);
			DP.console.trace();
			return;
		}

		extraData = extraData || {};
		extraData.routeTriggerEl = el;
		if (el.data('route-title')) {
			extraData.title = el.data('route-title');
			if (extraData.title == '@text') {
				extraData.title = el.text().trim().replace(/[\n\r]/g, ' ').replace(/\s+/g, ' ');
			} else if (extraData.title == '@title') {
				extraData.title = el.attr('title');
			} else if (extraData.title.test(/^@selector\((.*?)\)$/)) {
				var sel = extraData.title.match(/^@selector\((.*?)\)$/)[1];
				var titleEl = null;
				if (sel[0] == "#") {
					titleEl = $(sel);
				} else {
					titleEl = $(sel, el);
				}

				if (titleEl && titleEl.length) {
					extraData.title = titleEl.text().trim().replace(/[\n\r]/g, ' ').replace(/\s+/g, ' ');
				} else {
					delete extraData.title;
				}
			}
		}
		if (el.data('route-openclass')) {
			extraData.toggleOpenClass = el.data('route-openclass');
		}

		if (el.data('route-notabreload')) {
			extraData.noToggle = true;
			extraData.focus = true;
		} else {
			if (el.closest('#dp_content_wrap')[0] || el.closest('.popover-wrapper')[0]) {
				extraData.noToggle = true;
				extraData.focus = true;
			}
		}

		if (el.data('route-preload-id')) {
			extraData.preloadId = el.data('route-preload-id');
		}

		this.runPageRoute(el.data('route'), extraData);
	},



	/**
	 * Load route data into the interface.
	 *
	 * @param {Object} routeData
	 */
	loadRoute: function(routeData) {

		routeData.openInSection = routeData.master;

		switch (routeData.openInSection) {
			case 'listpane':
				this.loadListPane(routeData.url, routeData);
				break;

			default:
				this.loadPage(routeData.url, routeData);
				break;
		}
	},


	loadRouteOverlay: function(routeData) {

		var positionAbove = null;
		var zindex = 0;
		var trigger = routeData.routeTriggerEl;
		if (trigger) {
			if (trigger.data('zindex')) {
				zindex = trigger.data('zindex');
			} else {
				var parent = trigger.parentsUntil('body').last();
				if (parent.length && parent.parent().is('body')) {
					positionAbove = parent;
				}
			}
		}

		var fragmentOverlay = new DeskPRO.Agent.PageHelper.FragmentOverlay({
			routeData: routeData,
			positionAbove: positionAbove,
			zIndex: zindex
		});
	},


	/**
	 * Load a URL and treat it as a list pane.
	 *
	 * @param {String} url The URL of the list pane.
	 */
	loadListPane: function(url, routeData, callback) {

		if (routeData && !routeData.isBackgroundLoad) {
			if (this.loadingListPage) {
				this.loadingListPage.abort();
				this.loadingListPage = null;
			}
		}

		if (routeData && !routeData.isBackgroundLoad) {
			$('#dp_list > section').removeClass('on');
			$('#dp_list_loading').addClass('on');
		}

		var xhr = this._doAjaxLoadRoute(url, routeData, (function(data) {

			if (!routeData) {
				routeData = {};
			}
			if (routeData && !routeData.isBackgroundLoad) {
				this.loadingListPage = null;
			}

			$('#dp_list_loading').removeClass('on');

			var page = this.createPageFragment(data, 'DeskPRO.Agent.PageFragment.ListPane.Basic');

			page.setMetaData('routeUrl', url);
			if (routeData) {
				page.setMetaData('routeData', routeData);
			}

			this.setListPage(page, routeData.isBackgroundLoad || false);

			if (callback) callback(page);
		}).bind(this));

		if (routeData && !routeData.isBackgroundLoad) {
			this.loadingListPage = xhr;
		}
	},



	/**
	 * Load a URL and treat and put it into the tabbed pane.
	 *
	 * @param {String} url The URL of the page
	 */
	loadPage: function(url, routeData, callback) {
		var self = this;
		if (!routeData || (!routeData.ignoreExist)) {
			var existTab = DeskPRO_Window.TabBar.findTabByRouteUrl(url);
			if (existTab && routeData.noToggle) {
				if (routeData.focus) {
					DeskPRO_Window.TabBar.activateTab(existTab);
				}
				return;
			}
			if (existTab && !(existTab.page.allowDupe && existTab.page.TYPENAME != 'loading')) {
				if(routeData && routeData.routeTriggerEl && routeData.routeTriggerEl.data('route-notabreload')) {
					DeskPRO_Window.TabBar.tabToFrontTabById(existTab.id);
					DeskPRO_Window.TabBar.activateTabById(existTab.id);
				}
				else {
					DeskPRO_Window.TabBar.removeTabById(existTab.id);
					if (routeData.routeTriggerEl && routeData.toggleOpenClass) {
						routeData.routeTriggerEl.removeClass(routeData.toggleOpenClass);
					}
				}
				return;
			}
		}

		// Add a temporary tab to the tabstrip
		routeData.tabPlaceholderId = DeskPRO_Window.TabBar.addTabPlaceholder(url, routeData);

		if (routeData.routeTriggerEl && routeData.toggleOpenClass) {
			routeData.routeTriggerEl.addClass(routeData.toggleOpenClass);
		}

		var successFn = (function(data) {
			try {
				var page = this.createPageFragment(data);
			} catch (e) {
				if (routeData.tabPlaceholderId) {
					DeskPRO_Window.TabBar.removeTabById(routeData.tabPlaceholderId);
				}
				this._showAjaxError('<div class="error-details">There was a problem loading the tab. Here is the raw page output: <textarea class="raw">' + Orb.escapeHtml(data) + '</textarea></div>');
				return;
			}

			page.setMetaData('routeUrl', url);
			if (routeData) {
				page.setMetaData('routeData', routeData);
				if (routeData.tabPlaceholderId) {
					page.setMetaData('tabPlaceholderId', routeData.tabPlaceholderId);
				}
			}
			if (routeData.fragment) {
				page.setMetaData('fragment', routeData.fragment);
			}

			this.addPageTab(page);

			if (callback) callback(page);
		}).bind(this);

		if (routeData.preloadId) {
			preloadEl = document.getElementById(routeData.preloadId);
			if (preloadEl) {
				var content = preloadEl.innerHTML;
				preloadEl.parentNode.removeChild(preloadEl);
				content = content.replace(/<deskpro_script/g, '<script');
				content = content.replace(/<\/deskpro_script/g, '</script');
				successFn(content);
				return;
			}
		}

		this._doAjaxLoadRoute(url, routeData, successFn);
	},



	_doAjaxLoadRoute: function(url, routeData, successFn) {

		routeData = routeData || {};
		if (!url) {
			DP.console.error('No URL provided! routeData: %o', routeData);
			return;
		}

		var self = this;

		if (routeData && routeData.postData) {
			var xhr = $.ajax({
				dataType: 'text',
				url: url,
				type: 'POST',
				data: routeData.postData,
				success: (function(data) {
					successFn(data);
				}).bind(this),
				noErrorOverride: true,
				timeout: 180000
			});

			routeData.xhr = xhr;
		} else {
			var xhr = $.ajax({
				dataType: 'text',
				url: url,
				type: 'GET',
				success: (function(data) {
					successFn(data);
				}).bind(this),
				noErrorOverride: true,
				timeout: 180000
			});

			routeData.xhr = xhr;
		}

		return xhr;
	},



	/**
	 * This creates a PageFragment.
	 *
	 * @param {String} html The HTML page
	 * @return {DeskPRO.Agent.PageFragment.Basic}
	 */
	createPageFragment: function (html, classname, force_classname) {

		pageMeta = {
			'title': false,
			'fragmentClass': classname || 'DeskPRO.Agent.PageFragment.Basic'
		};

		var regex = /<script>([\s\S]*?)<\/script>/im;
		var matches = regex.exec(html);

		if (!matches || !matches.length) {
			var regex = /<script\s*type="text\/javascript">([\s\S]*?)<\/script>/im;
			var matches = regex.exec(html);
		}

		if (matches && matches.length) {
			eval(matches[1]);

			// Cut out the pageMeta from the HTML string
			html = html.replace(matches[0], '');
		}

		if (force_classname) {
			pageMeta.fragmentClass = classname;
		}

		// Hard switch that prevents page fragments from
		// rendering a login page into the interface
		// - The login page is redirected to within the code when session expires,
		// so in the template we set this metadata to force this redirect
		if (pageMeta && pageMeta.goToLogin) {
			window.location = BASE_URL + 'agent/';

			var page = new DeskPRO.Agent.PageFragment.Basic('');
			return page;
		}

		//DP.console.debug('PageFragment class: %s', pageMeta.fragmentClass);
		var fragment_class = Orb.getNamespacedObject(pageMeta.fragmentClass);

		var page = new fragment_class(html);
		page.setMetaData(pageMeta);

		return page;
	},


	getCurrentListPage: function() {
		return this.listPage;
	},

	getCurrentTabPage: function() {
		var tab = DeskPRO_Window.TabBar.getActiveTab();
		if (!tab) return null;

		return tab.page;
	},


	/**
	 * Reloads the currently selected tab if it has the proper rotueData metadata.
	 * This is usually only used for dev, reloading a tab rather than the full page,
	 * or re-clicking a link.
	 */
	reloadSelectedTab: function() {
		var tab = DeskPRO_Window.TabBar.getActiveTab();
		if (!tab) return;

		var route = tab.page.meta.routeData.route;

		// Delete current page so its not just deteceted as already loaded
		DeskPRO_Window.TabBar.removeTabById(tab.id);

		this.runPageRoute(route);
	},


	/**
	 * Reloads the currently selected list if it has the proper rotueData metadata.
	 */
	reloadSelectedList: function() {
		if (!this.listPage) {
			return;
		}

		var route = this.listPage.meta.routeData.route;
		this.runPageRoute(route);
	},


	/**
	 * Get the message channeler
	 */
	getMessageChanneler: function() {
		return this.messageChanneler;
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


	/**
	 * Plays a sound through HTML5 audio element.
	 *
	 * @param files A file or array of file sources (MP3 and OGG for best cross-browser)
	 * @param options
	 * @return jQuery
	 */
	playSound: function(files, setOptions) {

		setOptions = setOptions || {};

		options = $.extend({}, {
			'autoplay': true,
			'volume': false,
			'loop': false,
			'destroyAfter': true
		}, setOptions);

		if (this.volume == 0) {
			return null;
		}

		if (typeof files == 'string') {
			files = [files];
		}

		var volume = this.volume;
		if (options.volume) {
			volume = options.volume;
		}

		volume = volume + 0.0;

		var html = [];
		html.push('<audio ');
		if (volume != 1) {
			html.push(' volume="' + volume + '" ');
		}
		if (options.loop) {
			html.push(' loop="loop" ');
		}
		html.push('>');

		Array.each(files, function(f) {
			html.push('<source src="' + f.path + '" type="' + f.type + '" />');
		});

		html.push('</audio>');
		html = html.join('');

		var el = $(html);

		try {
			el.get(0).volume = volume;
		} catch (e) {}

		if (options.destroyAfter) {
			el.bind('ended', function() {
				$(this).remove();
			});
		}

		if (options.appendTo) {
			$(options.appendTo).append(el);
		} else {
			el.appendTo('body');
		}

		if (options.autoplay) {
			try	{
				el.get(0).play();
			} catch(e) { }
		}

		return el;
	},


	/**
	 * Plays a standard sound from the static dir. This assumes an MP3
	 * and OGG version of the file exists.
	 *
	 * @param name
	 * @param options
	 */
	playLibrarySound: function(name, options) {
		if ($.browser.msie) {
			var files = [{path: ASSETS_BASE_URL + '/sounds/' + name + '.wav', type: 'audio/wav'}];
		} else {
			var files = [
				{path: ASSETS_BASE_URL + '/sounds/' + name + '.mp3', type: 'audio/mpeg'},
				{path: ASSETS_BASE_URL + '/sounds/' + name + '.ogg', type: 'audio/ogg'}
			];
		}

		this.playSound(files, options);
	},

	handleSoundElements: function(el) {
		var self = this;
		if ($(el).is('[data-play-sound]')) {
			self.playLibrarySound($(el).data('play-sound'), {appendTo: el});
		} else {
			$('[data-play-sound]', el).each(function() {
				self.playLibrarySound($(this).data('play-sound'), {appendTo: el});
			});
		}
	},

	//#################################################################
	//# AJAX and loading
	//#################################################################

	_globalHandleAjaxComplete: function(event, xhr, ajaxOptions) {

		var is_success = false;
		if (xhr.status && xhr.status == 200) {
			is_success = true;
		} else if (xhr.status == 0 || (xhr.statusText && xhr.statusText == 'abort')) {
			return;
		}

		// Only polling-type requests really dictate the "network" status
		if (is_success) {
			$('#network_status_indicator > a').removeClass('on').data('error-count', 0);
			$('#network_status_tip').removeClass('error');

			// If we're showing the update_running notice,
			// then the first success afterwards means
			// the helpdesk is back and we should relaod the page
			if (this.update_running) {
				window.location.reload(true);
			}
		} else {
			this.incNetworkError();
		}
	},

	showUpdateRunning: function() {
		this.update_running = true;
		$('#reload_overlay').show();
		$('#reload_overlay_updates').show();
	},

	_globalHandleAjaxError: function(event, xhr, ajaxOptions, errorThrown, force) {

		if (this.update_running) {
			return;
		}

		// status of 0 means aborted
		// eg. the user hit escape
		if (!xhr || (xhr.status == 0 && xhr.statusText != 'timeout')) {
			// ignore it, not actually an error
			return;
		}

		if (xhr && xhr.status && xhr.status == '404') {
			this.showAlert($('<div><strong>Not Found</strong><br />The page you are trying to view could not be found. It may have been moved or deleted.</div>'));
			return;
		}

		// We dont care about aborts
		// This is caused when the user navigates away from a page, any running
		// ajax requests are aborted by the browser. Without this the user
		// would see the error popup briefly before the page went away
		if (force || (xhr.statusText && xhr.statusText == 'abort')) {
			return;
		}

		if (ajaxOptions.errorDp) {
			ajaxOptions.errorDp.call(ajaxOptions.context || xhr, event, xhr, ajaxOptions, errorThrown);
		}

		var data = xhr.responseText;
		try {
			data = $.parseJSON(data);
		} catch (e) {
			data = null;
		}

		if (xhr && xhr.status && xhr.status == '503' && data && data.error && data.error == 'update_running') {
			this.showUpdateRunning();
			return;
		}

		if (DPC_IS_CLOUD) {
			if (xhr && xhr.status && (xhr.status == '503' || xhr.status == '500')) {
				this.showAlert($('<div>We detected a problem while trying to load the page you requested. Please try again.</div>'));
				if (DpErrorLog) {
					DpErrorLog.logError('AJAX Error ' + xhr.status + ' on ' + ajaxOptions.url);
				}
				return;
			}
			if (xhr && (xhr.status == 'timeout' || xhr.statusText == 'timeout' || xhr.responseText == 'timeout' || errorThrown == 'timeoutec')) {
				this.showAlert($('<div>We could not load the page you requested because the connection timed out. Please try again.</div>'));
				return;
			}
		}

		if (xhr && xhr.status && xhr.status == '403') {

			if (data && data.error && (data.error == 'session_expired' || data.error == 'invalid_request_token')) {
				var url = data.redirect_login;
				url += '?return=' + encodeURIComponent(window.location.href);
				url += '&timeout=1'

				window.location = url;
				ajaxOptions.error = null;
				ajaxOptions.complete = null;

				$('#reload_overlay').show();

				return;
			}

			if (data && data.error && data.error == 'not_allowed') {
				this.showAlert($('<div>The action you attempted to execute is not allowed:<br />' + data.errorMessage + '</div>'));
				return;
			} else {
				// All 403's should be json responses that are caught above,
				// but this is to catch other edge cases (e.g., an agent was just made a non-agent)
				if (xhr.responseText && xhr.responseText.indexOf('DeskPRO')) {
					this.showAlert($('<div><strong>No Permission</strong><br />You do not have permission to view the requested page. If you think this is a mistake, you should contact your administrator.</div>'));

				// This would mean the actual server responded with a 403--DeskPRO was not involved
				} else {
					// On cloud, a 403 generally means CF is blocking the request because it thinks we are a bot.
					if (DPC_IS_CLOUD) {
						if (DpErrorLog) {
							DpErrorLog.hasSentReport = true; // dont ask to report, just send it
							DpErrorLog.logError(
								"CloudFlare Network Error: " + message,
								'URL: ' + ajaxOptions.url,
								'agent',
								1
							);
						}
						// Try reloading the interface
						// In case of CF blocks, this would result in the user seeing a "challenge" response
						// which will let them whitelist themselves
						this.util.reloadInterface();
					} else {
						this.showAlert($('<div><strong>Server Error</strong><br />You do not have permission to view the requested page. If you think this is a mistake, you should contact your administrator.</div>'));
					}
				}

				return;
			}
		}

		// We dont use this handler if there was an error handler used
		if (ajaxOptions && ajaxOptions.error && !ajaxOptions.noErrorOverride) return;

		// We dont show the error popup if it was just an error with polling
		if (ajaxOptions && ajaxOptions.dpIsPolling) {
			this.incNetworkError();
			return;
		};

		if (xhr.status == 'timeout' || xhr.statusText == 'timeout' || xhr.responseText == 'timeout' || errorThrown == 'timeoutec') {
			this.showAlert($('<div><strong>Network Error</strong><br />The request timed out. The server may be too busy to handle your request, or you may have been disconnected from the internet. Try again.</div>'), 'network_error');
			this.incNetworkError();
			return;
		}

		// We dont know if the request was JSON or HTML (eg the sn code might be embedded in html in json),
		// so we have to sniff the raw responseText to see about any embedded SN code
		var sn = null;
		if (data && data.sn) {
			sn = data.sn;
		} else {
			var match = /\[SN([0-9A-Z]{8})\]/.exec(xhr.responseText);
			if (match) {
				sn = match[1];
			}
		}

		// Show overlay about failed
		if (sn) {
			var status = (xhr.status || '') + ' ' + (errorThrown || '') + ' ' + (xhr.statusText || '');
			var url    = ajaxOptions.url;
			var method = ajaxOptions.type;

			var showsn = 'SN' + sn;
			if (DESKPRO_PERSON_ISADMIN) {
				showsn = '<a href="' + BASE_URL + 'admin/server/error-logs/SN' + sn + '">SN' + sn + '</a>';
			}

			this._showAjaxError('<div>If the error persists, give your administrator this code: ' + showsn + '</div><div class="error-details">Here is the raw output returned from the server error:<textarea class="raw">' + Orb.escapeHtml(method) + ' ' + Orb.escapeHtml(url) + "\n" + Orb.escapeHtml(status) + "\n\n" + Orb.escapeHtml(xhr.responseText) + '</textarea></div>');
		} else {
			this._showAjaxError('<div class="error-details">Here is the raw output returned from the server error:<textarea class="raw">' + Orb.escapeHtml(method) + ' ' + Orb.escapeHtml(url) + "\n" + Orb.escapeHtml(status) + "\n\n" + Orb.escapeHtml(xhr.responseText) + '</textarea></div>');
		}
	},

	incNetworkError: function() {
		var a = $('#network_status_indicator > a').addClass('on');
		a.data('error-count', parseInt(a.data('error-count')) + 1);
		$('#network_status_tip').addClass('error');
	},

	_showAjaxError: function(message, type) {

		if (DPC_IS_CLOUD) {
			if (message.indexOf('http://www.cloudflare.com/') !== -1 && message.indexOf('<title>Website is currently unreachable</title>') !== -1) {
				if (DpErrorLog) {
					DpErrorLog.hasSentReport = true; // dont ask to report
					DpErrorLog.logError(
						"CloudFlare Network Error: " + message,
						'',
						'agent',
						1
					);
				}
				return;
			}
		}

		var self = this;
		$('#global_ajax_error_info').empty();
		if (message) {
			$('#global_ajax_error_info').html(message);
		} else {
			$('#global_ajax_error_info').empty();
		}

		if (!this.ajaxErrorOverlay) {
			this.ajaxErrorOverlay = new DeskPRO.UI.Overlay({
				contentElement: $('#global_ajax_error'),
				zIndex: 50000 /* this should be bigger than everything */
			});
		}

		$('#global_ajax_error_submit').off('click').on('click', function(ev) {
			ev.preventDefault();
			$('#global_ajax_error').removeClass('switch-success').addClass('switch-loading');
			$.ajax({
				url: BASE_URL + 'dp/report-error.json',
				data: {
					error_text: message
				},
				type: 'POST',
				success: function() {
					$('#global_ajax_error').removeClass('switch-loading').addClass('switch-success');
				}
			});
		});

		this.ajaxErrorOverlay.initOverlay(); // needed so we can access wrapperOuter next
		this.ajaxErrorOverlay.elements.wrapperOuter.addClass('error');
		$('#global_ajax_error').removeClass('switch-success switch-loading');

		this.ajaxErrorOverlay.openOverlay();
	},



	//#################################################################
	//# Inits
	//#################################################################

	_initBasic: function() {

		this.options.messageChanneler.interval = DP_POLLER_INTERVAL;
		this.messageChanneler = new DeskPRO.MessageChanneler.AjaxChanneler(this.messageBroker, this.options.messageChanneler);
		//this.messageChanneler = new DeskPRO.MessageChanneler.AbstractChanneler(this.messageBroker, this.options.messageChanneler);

		this.messageChanneler.poller.addData((function () {
			return {'at': parseInt(this.activityTime.getTime() / 1000)};
		}).bind(this), 'at', { recurring: true });

		// Add chats we're looking at right now
		this.messageChanneler.poller.addData((function () {
			var chatIdsData = [];
			Array.each(this.getTabWatcher().findTabType('userchat'), function(t) {
				chatIdsData.push({
					name: 'chat_ids[]',
					value: t.page.meta.conversation_id
				});
			});

			if (!chatIdsData.length) {
				return false;
			}

			return chatIdsData;
		}).bind(this), 'chat_ids', { recurring: true });

		this.getTabWatcher().addTabTypeWatcher('userchat', new DeskPRO.Agent.WindowElement.TabWatcher.UserChat());
	},

	_initRoutes: function() {
		// Set ourselves up as the first route listener
		this.addPageRouteLoader('listpane', (function(routeData) {

			if (!this.paneVis.list && !routeData.noChangePaneVis) {
				this.setPaneVis('list', true);
			}

			this.loadRoute(routeData);
		}).bind(this));
		this.addPageRouteLoader('page', this.loadRoute.bind(this));
		this.addPageRouteLoader('article', this.loadRoute.bind(this));
		this.addPageRouteLoader('download', this.loadRoute.bind(this));
		this.addPageRouteLoader('news', this.loadRoute.bind(this));
		this.addPageRouteLoader('feedback', this.loadRoute.bind(this));
		this.addPageRouteLoader('org', this.loadRoute.bind(this));
		this.addPageRouteLoader('ticket', (function(routeData) {

			routeData.forTypename = 'ticket';

			var m = routeData.url.match(/tickets\/([0-9]+)/);
			if (!m || !m[1]) {
				console.error('Bad page loader call: ' + routeData.url + ' %o', routeData);
				return;
			}
			var ticketId = m[1];

			routeData.tabLoad = function() {
				DeskPRO_Window.getMessageBroker().sendMessage('ui.ticket.opened', { ticketId: ticketId });
			};
			routeData.tabUnload = function() {
				DeskPRO_Window.getMessageBroker().sendMessage('ui.ticket.closed', { ticketId: ticketId });
			};
			this.loadRoute(routeData);
		}).bind(this));
		this.addPageRouteLoader('person', this.loadRoute.bind(this));
		this.addPageRouteLoader('kb_article_view', this.loadRoute.bind(this));
		this.addPageRouteLoader('kb_article_new', this.loadRoute.bind(this));
		this.addPageRouteLoader('kb_article_edit', this.loadRoute.bind(this));
		this.addPageRouteLoader('poppage', this.loadRouteOverlay.bind(this));

		var self = this;
	},

	_initWindowInterface: function() {
		var self = this;

		// Update sizes when textareas resize
		$(document).on('textareaexpander_expanded', function() {
			$('.with-scroll-handler').each(function() {
				if ($(this).data('scroll_handler')) {
					$(this).data('scroll_handler').updateSize();
				}
			});
		});

		this.notifications = new DeskPRO.Agent.Notifications();

		// Settings is a window
		$('#user_settings_link').on('click', function() {
			$('#settingswin').trigger('dp_open');
		});

		// Global AJAX handler for errors if no error handler is attached
		$(document).ajaxError(this._globalHandleAjaxError.bind(this));
		$(document).ajaxComplete(this._globalHandleAjaxComplete.bind(this));

		// The favicon count
		this.faviconBadge = new DeskPRO.FaviconBadge({
			favicon: '#favicon'
		});

		this.notifications.addEvent('modCount', function(data) {
			var count = 0;
			$('#dp_header_notify_wrap').find('.badge').not('.no-count').each(function() {
				count += parseInt($(this).text().trim());
			});

			var doanim = false;
			if (!$('html').is('.window-active')) {
				doanim = true;
			}

			self.faviconBadge.updateBadge(count, true);
		});

		this.volume = 0.8;
		$('#sound_icon').find('i').removeClass('icon-volume-down icon-volume-up icon-volume-off').addClass('icon-volume-up');

		var updateVolumeUi = function() {
			if (self.volume == 0 || self.volume == 0.0) {
				self.volume = 0;
				$('#sound_icon_in').addClass('off');
				$('#sound_icon').find('i').removeClass('icon-volume-down icon-volume-up').addClass('icon-volume-off');
			} else {
				if (self.volume < 0.6) {
					$('#sound_icon').find('i').removeClass('icon-volume-down icon-volume-up icon-volume-off').addClass('icon-volume-down');
				} else {
					$('#sound_icon').find('i').removeClass('icon-volume-down icon-volume-up icon-volume-off').addClass('icon-volume-up');
				}

				$('#sound_icon_in').removeClass('off');
			}

			$('audio').each(function() {
				this.volume = self.volume;
			});

			$('#volume_controls .slider').slider('value', self.volume * 100);
		}

		// Volume slider
		$('#volume_controls .slider').slider({
			orientation: "vertical",
			range: "min",
			min: 0,
			max: 100,
			value: 80,
			slide: function(event, ui) {
				self.volume = parseInt(ui.value) / 100;
				$('#sound_icon_in').data('last-value', $('#volume_controls .slider').slider('value'));
				updateVolumeUi();
			}
		});

		$('#sound_icon_in').data('last-value', $('#volume_controls .slider').slider('value'));

		$('#sound_icon_in').on('click', function(ev) {
			ev.stopPropagation();
			if ($(this).is('.off')) {
				var last = $(this).data('last-value');
				if (last == 0 || last == 0.0) {
					last = 80;
				}
				self.volume = parseInt(last) / 100;
				updateVolumeUi();
			} else {
				self.volume = 0;
				updateVolumeUi();
			}
		});

		var closeSoundMenu = function() {
			$('#volume_controls_back').hide();
			$('#volume_controls').fadeOut();
		};

		// Use of a backdrop here ensures we can handle the click and not
		// fire anything else by accident on bubbling
		// Also the document click is unreliable since there may be other
		// elements that also stop bubbling.
		$('#volume_controls_back').on('click', function(ev) {
			ev.stopPropagation();
			closeSoundMenu();
		});

		var showSoundMenu = function() {
			$('#volume_controls_back').show();

			var atEl = $('#sound_icon').find('i');
			$('#volume_controls').css({
				'top': atEl.offset().top - 1,
				'left': atEl.offset().left - 2
			});

			$('#volume_controls').fadeIn();
		};

		$('#sound_icon').on('click', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			showSoundMenu();
		});

		// Create menu
		this.createMenu = new DeskPRO.UI.Menu({
			triggerElement: '#create_content_trigger',
			menuElement: '#create_content_menu'
		});

		var autostart = true;
		if (DeskPRO_Window.DEBUG.disableSectionHandlers) {
			autostart = false;
		}

		if (DESKPRO_PERSON_PERMS['agent_tickets.create']) {
			var self = this;
			this.newTicketLoader = new DeskPRO.Agent.Widget.BackgroundPopout({
				loadUrl: BASE_URL + 'agent/tickets/new',
				tabRoute: 'page:' + BASE_URL + 'agent/tickets/new',
				autostart: autostart
			});
			this.newTicketLoader.newLinkedTicket = function(ticket_id, message_id) {
				self.newTicketLoader.nextParams = {
					ticket_id: ticket_id,
					message_id: message_id || 0
				};
				self.newTicketLoader.open();
			};
			$('#create_ticket_btn').on('click', function() { DeskPRO_Window.newTicketLoader.toggle(); });
		}

		if (DESKPRO_PERSON_PERMS['agent_people.create']) {
			this.newPersonLoader = new DeskPRO.Agent.Widget.BackgroundPopout({
				loadUrl: BASE_URL + 'agent/people/new',
				tabRoute: 'page:' + BASE_URL + 'agent/people/new',
				autostart: autostart
			});
			$('#create_person_btn').on('click', function() { DeskPRO_Window.newPersonLoader.toggle(); });
		}

		if (DESKPRO_PERSON_PERMS['agent_org.create']) {
			this.newOrganizationLoader = new DeskPRO.Agent.Widget.BackgroundPopout({
				loadUrl: BASE_URL + 'agent/organizations/new',
				tabRoute: 'page:' + BASE_URL + 'agent/organizations/new',
				autostart: autostart
			});
			$('#create_organization_btn').on('click', function() { DeskPRO_Window.newOrganizationLoader.toggle(); });
		}

		if (DESKPRO_PERSON_PERMS['agent_publish.create']) {
			this.newArticleLoader = new DeskPRO.Agent.Widget.BackgroundPopout({
				loadUrl: BASE_URL + 'agent/kb/article/new',
				tabRoute: 'page:' + BASE_URL + 'agent/kb/article/new',
				autostart: autostart
			});
			this.newNewsLoader = new DeskPRO.Agent.Widget.BackgroundPopout({
				loadUrl: BASE_URL + 'agent/news/new',
				tabRoute: 'page:' + BASE_URL + 'agent/news/new',
				autostart: autostart
			});
			this.newDownloadLoader = new DeskPRO.Agent.Widget.BackgroundPopout({
				loadUrl: BASE_URL + 'agent/downloads/new',
				tabRoute: 'page:' + BASE_URL + 'agent/news/new',
				autostart: autostart
			});
			this.newFeedbackLoader = new DeskPRO.Agent.Widget.BackgroundPopout({
				loadUrl: BASE_URL + 'agent/feedback/new',
				tabRoute: 'page:' + BASE_URL + 'agent/feedback/new',
				autostart: autostart
			});

			$('#create_article_btn').on('click', function() { DeskPRO_Window.newArticleLoader.toggle(); });
			$('#create_news_btn').on('click', function() { DeskPRO_Window.newNewsLoader.toggle(); });
			$('#create_download_btn').on('click', function() { DeskPRO_Window.newDownloadLoader.toggle(); });
			$('#create_feedback_btn').on('click', function() { DeskPRO_Window.newFeedbackLoader.toggle(); });
		}

		this.newTaskLoader = new DeskPRO.Agent.Widget.BackgroundPopout({
			loadUrl: BASE_URL + 'agent/tasks/new',
			autostart: autostart
		});
		$('#create_task_btn').on('click', function() { $('form#newTaskForm input, form#newTaskForm select').val(''); DeskPRO_Window.newTaskLoader.toggle(); });

		if ($('#twitter_section').length) {
			this.newTweetLoader = new DeskPRO.Agent.Widget.BackgroundPopout({
				loadUrl: BASE_URL + 'agent/twitter/new',
				autostart: autostart
			});
			$('#create_tweet_btn').on('click', function() { DeskPRO_Window.newTweetLoader.toggle(); });
		}

		// Create toggle
		$('#createContentTrigger').on('click', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			var pos = $(this).offset();
			var w = $(this).outerWidth();
			var h= $(this).outerHeight();

			var list = $('#createTicketToggle');
			list.hide().detach().appendTo('body');
			list.css({
				top: pos.top,
				left: pos.left
			});
			list.show();

			var backdrop = $('<div class="backdrop" />').appendTo('body');

			var close = function() {
				list.hide();
				backdrop.remove();
			};
			backdrop.on('click', close);
			list.on('click', close);
		});


		// Interface toggle
		$('#DP-InterfaceSwitcher > .DP-adminSwitch > .adminSwitcher').on('click', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			var list = $('#interfacesToggle');
			list.hide().detach().appendTo('body');
			list.css({
				top: 7,
				right: 69
			});
			list.show();

			var backdrop = $('<div class="backdrop" />').appendTo('body');

			var close = function() {
				list.hide();
				backdrop.remove();
			};
			backdrop.on('click', close);
			list.find('a').on('click', close);
			$('ul', list).on('click', function(ev) {
				ev.stopPropagation();
			});
		});

		// User menu
		$('#userSetting_trigger').on('click', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			var list = $('#userSetting');
			list.hide().detach().appendTo('body');
			list.css({
				top: 41,
				left: 4
			});
			list.show();

			var backdrop = $('<div class="backdrop" />').appendTo('body');

			var close = function() {
				list.hide();
				backdrop.remove();
			};
			backdrop.on('click', close);
			list.find('a').on('click', close);
			$('ul', list).on('click', function(ev) {
				ev.stopPropagation();
			});
		});

		var getkbbackdrop = function() {
			if (this.el) {
				return this.el;
			}

			this.el = $('<div />').addClass('backdrop').appendTo('body').on('click', function() {
				$('#dp_keyboard_shortcuts').hide();
				getkbbackdrop().hide();
			});
			return this.el;
		};

		$('#dp_keyboard_shortcuts').find('.close').on('click', function() {
			$('#dp_keyboard_shortcuts').hide();
			getkbbackdrop().hide();
		});
		$('#keyboard_shortcuts_trigger').on('click', function() {
			$('#dp_keyboard_shortcuts').show();
			getkbbackdrop().show();
		});

		DeskPRO_Window.getMessageBroker().addMessageListener('agent.ui.reload', function (info) {
			DeskPRO_Window.showRefreshAlert(info.person_name);
		});

		this.keyboardShortcuts = new DeskPRO.Agent.KeyboardShortcuts();
	},

	initStickyTips: function(els) {
		if (!els.hasClass('with-stickytip')) {
			els = els.find('.with-stickytip');
		}

		els.each(function() {
			if ($(this).hasClass('dp-stickytip-init')) {
				return;
			}

			var me = $(this);
			$(this).addClass('dp-stickytip-init');

			$(this).one('mouseover', function() {
				$(this).attr('title', '');
				var target = $(me.data('stickytip-target'));
				me.data('stickytip-target', target);

				var hideTimout = null;
				var hideFn = function() {
					if (target.hasClass('over') || me.hasClass('over')) {
						return;
					}

					target.hide();
					target.removeClass('over');
					me.removeClass('over');
				};

				var showFn = function() {
					var pos = me.offset();
					target.css({
						left: pos.left,
						top: pos.top + 15
					});
					target.show();
				};

				var hideTimeout = null;

				target.on('mouseover', function() {
					target.addClass('over');
					if (hideTimeout) {
						window.clearTimeout(hideTimeout);
						hideTimeout = null;
					}
				}).on('mouseout', function() {
					target.removeClass('over');
					if (hideTimeout) {
						window.clearTimeout(hideTimeout);
						hideTimeout = null;
					}
					hideTimeout = window.setTimeout(hideFn, 240);
				});

				me.on('mouseover', function() {
					me.addClass('over');
					showFn();
					if (hideTimeout) {
						window.clearTimeout(hideTimeout);
						hideTimeout = null;
					}
				}).on('mouseout', function() {
					me.removeClass('over');
					if (hideTimeout) {
						window.clearTimeout(hideTimeout);
						hideTimeout = null;
					}
					hideTimeout = window.setTimeout(hideFn, 240);
				});

				me.addClass('over');
				target.detach().appendTo('body');
				showFn();
			});
		});
	},

	_initSections: function() {

		var self = this;
		var count = -1;

		var secttimeout = 2500;

		this.getSectionDataStartQueue();

		$('#dp_nav [data-section-handler]').each(function() {
			var el = $(this);
			if (!el.attr('id')) {
				el.attr('id', Orb.getUniqueId('section_'));
			}

			var handlerClassName = el.data('section-handler');

			if (DeskPRO_Window.DEBUG.disableSectionHandlers) {
				if (!DeskPRO_Window.DEBUG.enableSectionHandlers || DeskPRO_Window.DEBUG.enableSectionHandlers.indexOf(handlerClassName) === -1) {
					return;
				}
			}

			var handlerClass = Orb.getNamespacedObject(handlerClassName);
			var handler = new handlerClass();
			handler.section_id = el.attr('id');

			if (++count) {
				handler.addEvent('sectionInit', function() {
					window.setTimeout(function() { handler._loadAutoLoadRoutes(true); }, secttimeout);
					secttimeout += (400 * count);
				});
			} else {
				// First one, load it for real
				handler.addEvent('sectionInit', function() {
					handler._loadAutoLoadRoutes();
				});
			}

			self.sections[el.attr('id')] = handler;

			if (!el.is('.no-click-switch')) {
				el.on('click', function() { self.switchToSection(el.attr('id')) });
			}
		});

		this.getSectionDataSendQueued();
	},

	switchToSection: function(section_id, no_load_list) {

		DP.console.debug('Switching to %s', section_id);

		var handler = this.sections[section_id];
		if (!handler) {
			if (section_id != 'test_section') {
				DP.console.error('Invalid section: %s', section_id);
			}
			return;
		}

		// Already active
		if (this.openSection == handler) {
			return;
		}

		var btn = $('#' + section_id);

		// Already on
		if (btn.is('.on')) {
			return;
		}

		if (this.openSection) {
			this.openSection.fireEvent('hide');
		}

		$('#dp_nav li.active').removeClass('active');
		btn.addClass('active');

		$('#dp_source > section.on').removeClass('on');
		$('#dp_list > section.on').removeClass('on');


		$('#dp_list_loading, #dp_source_loading').addClass('on');

		if (this.openSection) {
			this.openSection.fireEvent('afterhide');
		}

		$('#dp_list_loading').removeClass('on');

		handler.fireEvent('show', [no_load_list]);
		var sectionEl = handler.getSectionElement();
		if (sectionEl) {
			sectionEl.addClass('on');
		}
		var listEl = handler.getListElement();
		if (listEl) {
			listEl.addClass('on');
		}
		handler.fireEvent('aftershow', [no_load_list]);

		this.openSection = handler;

		if (this.openSection.listPage) {
			this.listPage = this.openSection.listPage;
		}

		this.updateWindowUrlFragment();
		if (this.openSection) {
			this.openSection.updateUi();
			if (this.listPage) {
				this.listPage.updateUi();
			}
		}
	},

	getOpenSection: function() {
		return this.openSection;
	},

	_initLayout: function() {

		this.layout = new DeskPRO.Agent.Layout.DeskproWindow();
		this.layout.doResize(true);

		this.TabBar = new DeskPRO.Agent.WindowElement.TabBar({
			tabPane: $('#tabNavigationPane'),
			bodyPane: $('#dp_content_wrap'),
			menuBtn: $('#tabDropdownPicker')
		});

		this.tabWatcher = new DeskPRO.Agent.TabWatcher({
			tabManager: DeskPRO_Window.TabBar
		});

		this.tabWatcher.addTabTypeWatcher('ticket', new DeskPRO.Agent.WindowElement.TabWatcher.Tickets());

		this.recentTabs = new DeskPRO.Agent.RecentTabs();
	},

	_initInterfaceServices: function() {
		var self = this;

		this.popover_inited = {};

		this.initInterfaceLayerEvents(document);
	},

	_initInterfacePopover: function(el, opennow) {
		var self = this;
		var popover_inited = this.popover_inited;

		var route = el.data('route');
		var routeData = self.parseRoute(route);

		if (el.data('route-preload-id')) {
			routeData.preloadId = el.data('route-preload-id');
		}

		var popover;

		if (!popover_inited[route]) {

			popover = new DeskPRO.Agent.PageHelper.Popover({
				pageUrl: routeData.url,
				preloadId: routeData.preloadId,
				tabRoute: route,
				loadTimeout: (el.is('.preload') ? 1500 : 0)
			});

			popover_inited[route] = {
				count: 0,
				popover: popover
			};

			popover.addEvent('close', function() {
				if (popover_inited[route].count < 1) {
					popover_inited[route].popover.destroy();
					delete popover_inited[route];
				}
			});

			popover.addEvent('destroy', function() {
				delete popover_inited[route];
			});
		} else {
			popover = popover_inited[route].popover;
		}

		popover_inited[route].count++;

		var tabWrapper = el.closest('.with-page-fragment');
		if (tabWrapper.length) {
			var page = tabWrapper.data('page-fragment');
			page.addEvent('destroy', function() {
				if (popover_inited[route]) {
					popover_inited[route].count--;
					if (popover_inited[route].count < 1) {
						popover_inited[route].popover.destroy();
						delete popover_inited[route];
					}
				}
			});
		} else {
			popover.options.destroyOnClose = true;
		}

		return popover;
	},

	/**
	 * Attaches central handlers on a layer. These handlers are added to the document,
	 * but if you have a new layer that prevents propagation up to the document,
	 * then you'll need to init it as a new layer with its own handlers.
	 *
	 * @param context
	 */
	initInterfaceLayerEvents: function(context) {
		var self = this;
		if ($(context).is('.dp-interface-layer')) {
			return;
		}

		$(context).addClass('dp-interface-layer');

		window.setTimeout(function() {
			// Accept clicks on routes
			$(context).on('click', '[data-route]', function(ev) {
				if ($(this).is('.as-popover')) {
					return;
				}

				if ($(this).is('.row-item') && (!$(ev.target).is('.click-through') && $(ev.target).is('input, a, button, textarea'))) {
					return;
				}

				ev.preventDefault();
				ev.stopPropagation();

				self.runPageRouteFromElement($(this));

				// If this was a list-pane and we have an open popover,
				// we need to close the popover so the listpane can actually load
				if ($(this).data('route').indexOf('listpane:') === 0) {
					Object.each(DeskPRO.Agent.PageHelper.Popover_Instances, function(inst) {
						if (inst.isOpen()) {
							inst.close();
						}
					}, this);
				}
			});

			$(context).on('click', '.agent-link', function(ev) {
				ev.preventDefault();
				ev.stopPropagation();

				var agentId = $(this).data('agent-id');
				DP.console.log('Agent click %i', agentId);
				if (!agentId || agentId === '0' || agentId === '' || agentId == DESKPRO_PERSON_ID) {
					return;
				}

				if (!DeskPRO_Window.sections.agent_chat_section) {
					DP.console.error('The agent chat section is not enabled');
					return;
				}

				DeskPRO_Window.sections.agent_chat_section.newChatWindow([agentId]);
			});

			$(context).on('click', '.as-popover', function(ev) {
				ev.preventDefault();
				ev.stopPropagation();
				self._initInterfacePopover($(this)).toggle();
			});
		}, 100);

		window.setTimeout(function() {
			$(context).find('.tipped').one('mouseover', function(ev) {

				if ($(this).hasClass('tipped-inited')) {
					return;
				}
				$(this).addClass('tipped-inited');

				var options = {};
				if ($(this).data('tipped-options')) {
					eval('options = {' + $(this).data('tipped-options') + '}');
				}

				qtipOptions = {};

				if (options.ajax) {
					qtipOptions.content = {
						text: 'Loading...',
						ajax: {
							url: $(this).data('tipped'),
							type: 'GET'
						}
					};
				} else if ($(this).data('tipped')) {
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

				qtipOptions = $.extend(true, qtipOptions, options);

				$(this).qtip(qtipOptions).qtip('show', ev);
				$(this).addClass('tipped-inited');
			});
		}, 200);

		$('.timeago', context).timeago();
		DeskPRO.ElementHandler_Exec(context);
	},

	initInterfaceServices: function(context) {
		var self = this;
		var page = false;

		if (context.hasClass('dp-inited-iface')) {
			return;
		}

		if (context.is('.with-page-fragment')) {
			page = context.data('page-fragment');
		} else {
			var tabWrapper = context.closest('.with-page-fragment');
			page = tabWrapper.data('page-fragment');
		}

		$('.as-popover.preload', context).each(function() {
			var p = self._initInterfacePopover($(this));
		});

		if (page) {
			var scrollEls = $('.with-scrollbar', context);
			if (scrollEls.length) {
				scrollEls.each(function() {
					new DeskPRO.Agent.ScrollerHandler(page, $(this), {
						showEvent: 'show',
						hideEvent: 'hide'
					});
				});
			}
		}

		$('.timeago', context).timeago();
		DeskPRO.ElementHandler_Exec(context);

		$('input.dp-checkbox', context).each(function() {
			DeskPRO_Window.util.dpCheckbox($(this));
		});

		window.setTimeout(function() {
			$(context).find('.tipped').one('mouseover', function(ev) {

				if ($(this).hasClass('tipped-inited')) {
					return;
				}
				$(this).addClass('tipped-inited');

				var options = {};
				if ($(this).data('tipped-options')) {
					eval('options = {' + $(this).data('tipped-options') + '}');
				}

				qtipOptions = {};

				if (options.ajax) {
					qtipOptions.content = {
						text: 'Loading...',
						ajax: {
							url: $(this).data('tipped'),
							type: 'GET'
						}
					};
				} else if ($(this).data('tipped')) {
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

				qtipOptions = $.extend(true, qtipOptions, options);

				$(this).qtip(qtipOptions).qtip('show', ev);
			});
		}, 200);
	},

	getSectionData: function(section_id, callback, extra_data) {
		var self = this;
		var url;

		if (!this.loadingSections) {
			this.loadingSections = {};
		}

		if (this.loadingSections[section_id]) {
			return;
		}

		this.loadingSections[section_id] = true;

		// If we're in queued mode, then dont send anything yet
		if (this._getSectionDataQueued) {
			this._getSectionDataQueued.push([section_id, callback]);
			return;
		}

		switch (section_id) {
			case 'tickets_section':
				url = BASE_URL + 'agent/ticket-search/get-section-data.json';
				break;

			case 'chat_section':
				url = BASE_URL + 'agent/chat/get-section-data.json';
				break;

			case 'twitter_section':
				url = BASE_URL + 'agent/twitter/get-section-data.json';
				break;

			case 'people_section':
				url = BASE_URL + 'agent/people-search/get-section-data.json';
				break;

			case 'feedback_section':
				url = BASE_URL + 'agent/feedback/get-section-data.json';
				break;

			case 'publish_section':
				url = BASE_URL + 'agent/publish/get-section-data.json';
				break;

			case 'tasks_section':
				url = BASE_URL + 'agent/tasks/get-section-data.json';
				break;

			case 'deals_section':
				url = BASE_URL + 'agent/deals/get-section-data.json';
				break;

			case 'agent_chat_section':
				url = BASE_URL + 'agent/agent-chat/get-section-data.json';
				break;
		}

		if (!url) {
			DP.console.error('getSectionData: Unknown section %s', section_id);
			return;
		}

		var errorFn = function() {
			this.tryCount++;
			if (this.tryCount <= this.retryLimit) {
				$.ajax(this);
				return;
			}
			delete self.loadingSections[section_id];
		};

		$.ajax({
			url: url,
			data: extra_data || {},
			timeout: 15000,
			dataType: 'json',
			success: function(data) {
				delete self.loadingSections[section_id];

				if (!data || !data.section_html) {
					errorFn();
					return;
				}

				callback(data);
			},
			tryCount : 0,
		    retryLimit: 3,
			error: function(xhr, textStatus, errorThrown) {
				errorFn();
				DeskPRO_Window._globalHandleAjaxError(null, xhr, this, errorThrown);
			}
		});
	},

	getSectionDataStartQueue: function() {
		this._getSectionDataQueued = [];
	},

	getSectionDataSendQueued: function() {
		var self = this;
		if (!this._getSectionDataQueued || !this._getSectionDataQueued.length) {
			return;
		}

		var callback_map = {};
		var data = [];
		Array.each(this._getSectionDataQueued, function(info) {
			data.push({
				name: 'section_ids[]',
				value: info[0]
			});

			callback_map[info[0]] = info[1];
		});

		this._getSectionDataQueued = null;

		$.ajax({
			url: BASE_URL + 'agent/get-combined-section-data.json',
			type: 'GET',
			data: data,
			dataType: 'json',
			timeout: 30000,
			tryCount : 0,
		    retryLimit: 3,
			error: function(xhr, textStatus, errorThrown) {
				this.tryCount++;
				if (this.tryCount <= this.retryLimit) {
					$.ajax(this);
					return;
				}
				var status = (xhr.status || '') + ' ' + (errorThrown || '') + ' ' + (xhr.statusText || '');
				self._showAjaxError('<div class="error-details">Here is the raw output returned from the server error:<textarea class="raw">' + status + "\n\n" + Orb.escapeHtml(xhr.responseText) + '</textarea></div>');

				self.loadingSections = {};
			},
			success: function(data) {
				self.loadingSections = {};

				Object.each(data, function(sectionData, sectionId) {
					if (sectionData === null || !sectionData.section_html) {
						// Means an error, send it normally
						self.getSectionData(sectionId);
					}
					if (callback_map[sectionId]) {
						callback_map[sectionId](sectionData);
					}
				});
			}
		});
	},

	prepareWidgetedHtml: function(html) {
		var finalHtml = html,
			widgetCssRegex = /<style type="text\/css" data-widget="(\d+)" data-hash="([a-zA-Z0-9]+)">([\s\S]*?)<\/style>/g,
			widgetJsRegex = /<script type="text\/javascript"([^>]*)>([\s\S]*?)<\/script>/g,
			cssExists = {},
			jsSource = [],
			jsInline = [],
			match;

		$('style[data-widget]').each(function () { cssExists[$(this).data('widget')] = $(this).data('hash'); });

		while (match = widgetCssRegex.exec(html)) {
			finalHtml = finalHtml.replace(match[0], '');

			// only insert the CSS once
			if (cssExists[match[1]] !== match[2]) {
				cssExists[match[1]] = match[2];
				$(match[0]).appendTo('head');
			}
		}

		while (match = widgetJsRegex.exec(html)) {
			finalHtml = finalHtml.replace(match[0], '');

			if (match[1].match(/src="([^"]+)"/)) {
				jsSource.push(RegExp.$1);
			} else {
				if (match[2]) {
					jsInline.push({
						widget: match[1].match(/data-widget="(\d+)"/) ? RegExp.$1 : false,
						htmlId: match[1].match(/data-html-id="([^"]+)"/) ? RegExp.$1 : false,
						code: match[2]
					});
				}
			}
		}

		return {
			html: finalHtml,
			jsSource: jsSource,
			jsInline: jsInline
		};
	},

	runWidgetedJs: function(page, src, inline) {
		var run = function() {
			for (var i = 0; i < inline.length; i++) {
				var code = inline[i].code,
					htmlId = inline[i].htmlId,
					context;

				if (inline[i].widget) {
					context = {
						page: page,
						meta: page.getAllMetaData(),
						id: htmlId,
						containerEl: (htmlId ? $('#' + htmlId + '_container') : false),
						contentEl: (htmlId ? $('#' + htmlId) : false),
						tabEl: (htmlId ? $('#' + htmlId + '_tab') : false)
					};
					eval('(function() {' + code + '}).call(context);');
				} else {
					$.globalEval(code);
				}
			}
		};

		if (!src.length) {
			run();
		} else {
			var remaining = src.length, self = this;

			for (var i = 0; i < src.length; i++) {
				$.ajax({
					url: src[i],
					type: 'GET',
					dataType: 'script',
					cache: true
				}).always(function() {
						remaining--;
						if (remaining == 0) {
							run();
						}
					});
			}
		}
	},

	canUseAgentReplyRte: function() {
		if (window.DP_SETTINGS) {
			return window.DP_SETTINGS['core_tickets.enable_agent_rte'];
		}

		return true;
	},

	initRteAgentReply: function(textarea, options) {
		return DeskPRO.Agent.RteEditor.initRteAgentReply(textarea, options);
	},

	initAgentNotifierForRte: function(obj, textarea, agentMap, alwaysAvailable) {
		var api = textarea.data('redactor');
		if (!api) {
			return;
		}

		if (!agentMap) {
			return;
		}

		var ed = textarea.getEditor();
		var self = this;

		delete agentMap[0];

		var agentMapLower = {}, hasAgents = false;
		Object.each(agentMap, function(data, agentId) {
			hasAgents = true;
			agentMapLower[agentId] = data.name.toLowerCase();
		});

		if (!hasAgents) {
			return;
		}

		obj.agentNotifyList = $('<ul />').addClass('message-agent-notify-list').hide().appendTo(document.body);

		var insertAgentNotify = function(agentId) {
			if (typeof agentMap[agentId] === 'undefined') {
				return;
			}

			self.hideAgentNotifyList(obj);

			var focus = api.getFocus(),
				focusNode = $(focus[0]),
				testText;

			if (!focus || !focus[0]) {
				return;
			}

			if (focus[0].nodeType == 3) {
				testText = focusNode.text().substring(0, focus[1]);
			} else {
				focus[0] = focusNode.contents().get(focus[1] - 1);
				focusNode = $(focus[0]);
				testText = focusNode.text();
				focus[1] = testText.length;
			}

			var	lastAt = testText.lastIndexOf('@'),
				matches = [];

			if (lastAt != -1) {
				api.setSelection(focus[0], lastAt, focus[0], focus[1]);
			}

			// web kit handles content editable without an issue. this prevents the span
			// from being extended unnecessarily
			var editable = $.browser.webkit ? ' contenteditable="false"' : '';
			api.insertHtml('<span' + editable + ' data-notify-agent-id="' + agentId + '">@' + Orb.escapeHtml(agentMap[agentId].name) + '</span>&nbsp;');
		};

		obj.agentNotifyList.on('mousedown', 'li', function(e) {
			e.preventDefault();
			insertAgentNotify($(this).data('agent-id'));
		});

		ed.on('click blur', function() {
			if (obj.isNote || alwaysAvailable) {
				self.hideAgentNotifyList(obj);
			}
		});

		ed.on('keydown', function(e) {
			if (!obj.isNote && !alwaysAvailable) {
				self.hideAgentNotifyList(obj);
				return;
			}

			switch (e.keyCode) {
				case 38: // up
				case 40: // down
				case 13: // enter
					if (!obj.agentNotifyList.is(':visible')) {
						return;
					}
					break;

				default:
					return;
			}

			e.preventDefault();

			if (e.keyCode == 13) { // enter - inserting the selected
				var li = obj.agentNotifyList.find('li.selected');
				if (!li.length) {
					li = obj.agentNotifyList.find('li:first');
				}

				insertAgentNotify(li.data('agent-id'));
			} else if (e.keyCode == 40) { // down - moves down the list
				var li = obj.agentNotifyList.find('li.selected');
				if (!li.length) {
					obj.agentNotifyList.find('li:first').addClass('selected');
				} else {
					li.removeClass('selected');
					var next = li.next('li');
					if (next.length) {
						next.addClass('selected');
					} else {
						obj.agentNotifyList.find('li:first').addClass('selected');
					}
				}
			} else if (e.keyCode == 38) { // up - moves up the list
				var li = obj.agentNotifyList.find('li.selected');
				if (!li.length) {
					obj.agentNotifyList.find('li:last').addClass('selected');
				} else {
					li.removeClass('selected');
					var prev = li.prev('li');
					if (prev.length) {
						prev.addClass('selected');
					} else {
						obj.agentNotifyList.find('li:last').addClass('selected');
					}
				}
			}
		});

		ed.on('keyup', function(e) {
			if (!obj.isNote && !alwaysAvailable) {
				return;
			}

			if (e.ctrlKey || e.metaKey) {
				return;
			}

			switch (e.keyCode) {
				case 16: // shift
				case 17: // ctrl
				case 18: // alt
				case 19: // pause/break
				case 20: // caps lock
				case 91: // left windows
				case 92: // right windows
				case 93: // select
				case 224: // apple key
					return;

				case 13: // enter
				case 38: // up
				case 40: // down
					// these don't hide as that messes up the keydown handler
					e.stopImmediatePropagation();
					e.preventDefault();
					return;

				case 9: // tab
				case 27: // esc
				case 33: // page up
				case 34: // page down
				case 35: // end
				case 36: // home
				case 37: // left
				case 39: // right
					self.hideAgentNotifyList(obj);
					return;

				default:
					// function keys and other special ones
					if (e.keyCode >= 112 && e.keyCode <= 145) {
						self.hideAgentNotifyList(obj);
						return;
					}
			}

			var focus = api.getFocus(),
				origin = api.getOrigin(),
				selection = api.getSelection();

			if (focus[0] != origin[0] || focus[1] != origin[1]) {
				// selected multiple points, don't show
				self.hideAgentNotifyList(obj);
				return;
			}

			var	focusNode = $(focus[0]),
				testText = focus[0].nodeType == 3 ? focusNode.text().substring(0, focus[1]) : $(focusNode.contents().get(focus[1] - 1)).text(),
				lastAt = testText.lastIndexOf('@'),
				matches = [];

			if (lastAt != -1 && (lastAt == 0 || testText[lastAt - 1].match(/^(\s|[\.!?:;,()<>|/-])$/))) {
				var afterAt = testText.substring(lastAt + 1, testText.length).toLowerCase();

				if (afterAt.length >= 2 && afterAt.length < 75) {
					Object.each(agentMap, function(data, agentId) {
						if (agentMapLower[agentId].indexOf(afterAt) == 0) {
							matches.push(agentId);
						}
					});
				}
			}

			if (matches.length) {
				var selectedId = obj.agentNotifyList.find('li:selected').data('agent-id');

				obj.agentNotifyList.empty();
				for (var i = 0; i < matches.length; i++) {
					var li = $('<li>')
						.text(agentMap[matches[i]].name)
						.css('background-image', 'url('+agentMap[matches[i]].picture_url+')')
						.data('agent-id', matches[i]);
					if (matches[i] === selectedId) {
						li.addClass('selected');
					}
					obj.agentNotifyList.append(li);
				}

				if (!obj.agentNotifyList.find('li:selected').length) {
					obj.agentNotifyList.find('li:first').addClass('selected');
				}

				var containingNode = focus[0].nodeType == 3 ? focusNode.parent() : focusNode;
				if (!containingNode.is('div, p, li, ul, ol, blockquote, table, body')) {
					containingNode = containingNode.closest('div, p, li, ul, ol, blockquote, table, body');
				}
				var offset = containingNode.offset();

				if (selection) {
					var selOffset = Orb.getSelectionCoords(selection);
					if (selOffset) {
						offset = selOffset;
					}
				}

				obj.agentNotifyList.css({
					top: offset.top - obj.agentNotifyList.outerHeight() - 1,
					left: offset.left
				});

				obj.agentNotifyList.show();
				obj.agentNotifyListShown = true;
			} else {
				self.hideAgentNotifyList(obj);
			}
		});

		// this is important as I need this keyup handler to run before redactor's own because of new line handling
		ed.data('events').keyup.reverse();
	},

	hideAgentNotifyList: function(obj) {
		if (obj.agentNotifyList && obj.agentNotifyListShown) {
			obj.agentNotifyList.empty().hide();
			obj.agentNotifyListShown = false;
		}
	},


	dumpDom: function() {
		var zindexRules = [];
		var zindexGot = {};
		$('*').each(function() {
			var zindex = $(this).css('z-index');
			if (zindex != 'auto') {
				if (!zindexGot[zindex]) {
					zindexRules.push('.zindex'+ zindex + ' { z-index: ' + zindex + ' !important; }');
					zindexGot[zindex] = true;
				}
				$(this).addClass('zindex'+zindex);
			}
		});

		zindexRules = zindexRules.join("\n");

		var css = ["<!-- DP_DUMP_CSS_BEGIN -->", "<style type=\"text/css\">"];
		$.each(document.styleSheets, function(sheetIndex, sheet) {
			$.each(sheet.cssRules || sheet.rules, function(ruleIndex, rule) {
				css.push(rule.cssText);
			});
		});

		css.push(zindexRules);
		css.push("</style>")
		css.push("<!-- DP_DUMP_CSS_END -->")
		css = css.join("\n");

		var html = $('html').html();
		html = html.replace('</head>', css + '</head>');
		html = html.replace('DP_IS_DOMDUMP_VIEW = false', 'DP_IS_DOMDUMP_VIEW = true');

		$.ajax({
			type: 'POST',
			url: BASE_URL + 'agent/save-dom.json',
			data: {html: html}
		});
	},

	//##################################################################################################################
	// Notices Window
	//##################################################################################################################

	openNotices: function() {
		var first = false;
		if (!this._noticeEl) {
			first = true;
		}
		this.getNoticeEl().show();
		if (first) {
			this.loadNextNotice();
		}
	},

	_updateNoticeEl: function() {
		var left = ($(window).width() / 2) - (this._noticeEl.outerWidth() / 2);
		this._noticeEl.css('left', left);

		if (this._noticeIds.length == 1) {
			$('#notices_control').remove();
			$('#notices_content').css('padding-bottom', 0);
		}
	},

	getNoticeEl: function() {
		var self = this;

		if (this._noticeEl) {
			this._updateNoticeEl();
			return this._noticeEl;
		}

		var html = '<div class="dark-overlay-box dp-notices-box">' +
				'<em class="close-trigger"></em>' +
				'<form>' +
					'<div class="title">' +
						'<div style="float:right">' +
							'<button class="clean-white dismiss">Dismiss</button>' +
							'<button class="clean-white dismiss-all">Dismiss All</button>' +
						'</div>' +
						'New Version Notes</div>' +
					'<div id="notices_content"></div>' +
					'<div id="notices_control">' +
						'<button class="clean-white prev">&larr;</button>' +
						'<button class="clean-white next">&rarr;</button>' +
					'</div>' +
				'</form>' +
			'</div>';

		this._noticeEl = $(html);
		this._noticeEl.hide();
		this._noticeEl.appendTo('body');

		this._noticeEl.find('.close-trigger').on('click', function(ev) {
			ev.preventDefault();
			self._noticeEl.hide();
		});
		this._noticeEl.find('.dismiss-all').on('click', function(ev) {
			ev.preventDefault();
			self.dismissAllNotices();
		});
		this._noticeEl.find('.dismiss').on('click', function(ev) {
			ev.preventDefault();
			var current = self._noticeIds[self._noticeIndex];
			self.dismissNotice(current);

			self._noticeIndex--;
			self.loadNextNotice();
		});
		this._noticeEl.find('.prev').on('click', function(ev) {
			ev.preventDefault();
			self.loadPrevNotice();
		});
		this._noticeEl.find('.next').on('click', function(ev) {
			ev.preventDefault();
			self.loadNextNotice();
		});

		this._updateNoticeEl();

		return this._noticeEl;
	},

	dismissAllNotices: function() {
		$.ajax({
			url: BASE_URL + 'agent/misc/version-notices/ALL/dismiss.json',
			dataType: 'json'
		});

		this._noticeEl.hide();
		$('#notice_trigger').hide();
	},

	dismissNotice: function(id) {
		$.ajax({
			url: BASE_URL + 'agent/misc/version-notices/' + id + '/dismiss.json',
			dataType: 'json'
		});

		this._noticeIds.erase(id);

		if (!this._noticeIds.length) {
			this._noticeEl.hide();
			$('#notice_trigger').hide();
			return;
		}

		this._updateNoticeEl();
		$('#notice_trigger').find('.badge').text(this._noticeIds.length);
	},

	loadNotice: function(id) {
		$('#notices_content').html('<div class="loading-icon-big"></div>');
		$.ajax({
			url: BASE_URL + 'agent/misc/version-notices/' + id + '/log.html',
			dataType: 'html',
			success: function(html) {
				$('#notices_content').html(html);
			}
		});
	},

	loadNextNotice: function() {
		this._noticeIndex++;
		if (this._noticeIds.length == this._noticeIndex) {
			this._noticeIndex = 0;
		}

		if (!this._noticeIds.length) {
			this._noticeEl.hide();
			$('.DP-version-notes').hide();
			return;
		}

		this.loadNotice(this._noticeIds[this._noticeIndex]);
	},

	loadPrevNotice: function() {
		this._noticeIndex--;
		if (this._noticeIndex < 0) {
			this._noticeIndex = this._noticeIds.length - 1;
		}

		this.loadNotice(this._noticeIds[this._noticeIndex]);
	},

	setPaneVis: function(id, vis) {
		this.paneVis[id] = vis;
		this.layout.doResize(true);
	},

	setPaneVisNum: function(num) {
		switch (num) {
			case 0:
				this.paneVis.source = true;
				this.paneVis.list = true;
				this.paneVis.tabs = true;
				break;

			case 1:
				this.paneVis.source = true;
				this.paneVis.list = true;
				this.paneVis.tabs = false;
				break;

			case 2:
				this.paneVis.source = true;
				this.paneVis.list = false;
				this.paneVis.tabs = true;
				break;

			case 3:
				this.paneVis.source = false;
				this.paneVis.list = true;
				this.paneVis.tabs = false;
				break;

			case 4:
				this.paneVis.source = false;
				this.paneVis.list = false;
				this.paneVis.tabs = true;
				break;

			case 5:
				this.paneVis.source = false;
				this.paneVis.list = true;
				this.paneVis.tabs = true;
				break;
		}

		this.layout.doResize(true);
	},

	getPaneVisNum: function() {
		var source = this.paneVis.source, list = this.paneVis.list, tabs = this.paneVis.tabs;

		if (source && list && tabs)   return 0;
		if (source && list && !tabs)  return 1;
		if (source && !list && tabs)  return 2;
		if (!source && list && !tabs) return 3;
		if (!source && !list && tabs) return 4;
		if (!source && list && tabs)  return 5;
		return 0;
	}
});