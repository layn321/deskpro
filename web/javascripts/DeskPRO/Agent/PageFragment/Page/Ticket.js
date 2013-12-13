Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');

DeskPRO.Agent.PageFragment.Page.Ticket = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'ticket';
		this.wrapper = null;
		this.changeManager = null;
		this.valueForm = null;
		this.layout = null;
		this.popout = null;
		this.popout_overview = null;
		this.isMouseOverPopout = false;
		this.hasInitPopout = false;
		this.popoutPage = null;
		this.lastActiveDate = null;
		this.ticketReplyBox = null;
	},

	getAlertId: function() {
		return 'ticket-row-' + this.meta.ticket_id;
	},

	initMetaData: function() {
		DeskPRO_Window.recentTabs.add(
			'tickets',
			this.meta.ticket_id,
			this.meta.title,
			BASE_URL + 'agent/tickets/' + this.meta.ticket_id
		);
	},

	initPage: function(el) {
		this.wrapper = el;
		var self = this;
		this.getEl('replybox_wrap').data('page', this);
		this.hasReplyFocused = false;

		if (this.getEl('linked_count').data('count') == "0") {
			this.getEl('linked_wrap_tab').hide();
		}

		try {
			var flashEnabled = !!(navigator.mimeTypes["application/x-shockwave-flash"] || window.ActiveXObject && new ActiveXObject('ShockwaveFlash.ShockwaveFlash'));
			if (flashEnabled) {
				// Set timeout to have it exec in global scope,
				// so errors (eg flash has crashed) can be ignored and dont break the rest of this init
				window.setTimeout(function() {
					self.wrapper.find('.copy-btn').each(function() {
						var btnEl = this;
						var btn = $(this);

						try {
							var clip = new ZeroClipboard(this, {
								btnEl: this,
								savePuffEl: self.getEl('idref_switch')
							});
							clip.on('mouseover', function(client, args) {
								$(client.options.btnEl).addClass('over');
							});
							clip.on('mouseout', function(client, args) {
								$(client.options.btnEl).removeClass('over');
							});
							clip.on('complete', function(client, args) {
								DeskPRO_Window.util.showSavePuff($(this).closest('.id-number'));
							});

							self.addEvent('destroy', function() {
								try {
									clip.unglue(btnEl);
								} catch (e) {}
							});
							self.addEvent('activate', function() {
								try {
									clip.reposition();
								} catch (e) {}
							});
						} catch (e) {}
					});
				}, 100);
			} else {
				this.wrapper.find('.copy-btn').remove();
			}
		} catch (e) {
			this.wrapper.find('.copy-btn').remove();
		}

		this.valueForm = $('form.value-form:first', this.wrapper);
		this.valueForm.on('submit', function(ev) {
			// Never actually submit the form (would load a new page)
			ev.preventDefault();
		});

		this.changeManager = new DeskPRO.Agent.Ticket.ChangeManager(this);
		this.changeManager.addEvent('updateResult', function(data) {
			self.clearAlerts();
			DeskPRO_Window.getMessageBroker().sendMessage('agent.ui.ticket_updated', { ticket_id: self.meta.ticket_id });

			if (data.data && !data.data.can_view) {
				self.closeSelf();
			}

			if (data.data && data.data.refresh) {
				DeskPRO_Window.loadPage(BASE_URL + 'agent/tickets/' + self.getMetaData('ticket_id'), {ignoreExist:true});
				self.closeSelf();
			}
		});

		this.changePic = new DeskPRO.Agent.PageFragment.Page.PersonHelper.ChangePic(this, {
			loadUrl: BASE_URL + "agent/people/" + this.meta.person_id + "/change-picture-overlay",
			saveUrl: BASE_URL + 'agent/people/' + this.meta.person_id + '/ajax-save'
		});
		this.ownObject(this.changePic);

		this.ticketFields = new DeskPRO.Agent.PageHelper.TicketFields(this);
		this.ownObject(this.ticketFields);

		this._initMessage(this.wrapper.find('.messages-wrap'));

		this.getEl('value_form').find('.language_id').on('change', function() {
			var langId     = $(this).val();
			if (!langId) {
				langId = DESKPRO_DEFAULT_LANG_ID;
			}

			var langLocale = DESKPRO_NAME_REGISTRY.lang_data[langId].locale;
			var langTitle  = $.trim(DESKPRO_NAME_REGISTRY.lang_data[langId].title);

			self.getEl('message_page_wrap').find('.translate-from-lang').each(function() {
				$(this).text(langTitle).data(langLocale);
			});
		});

		this._initTicketActionsMenu();
		this._initMessageActionsMenu();
		this._initLabels();
		this._initTicketLocking();
		this._initTasks();
		this._initEditName();
		this._initSlas();

		// Change email menu
		var emailText = this.getEl('user_email_text');
		var emailChangeTrig = this.getEl('user_email_menu_trigger');
		var emailChangeMenu = this.getEl('user_email_menu');
		var emailChangeBackdrop = null;
		if (emailChangeTrig[0]) {

			var closeEmailChangeMenu = function() {
				emailChangeBackdrop.hide();
				emailChangeMenu.hide();
			};

			var updateEmailChangePos = function() {
				var pos = emailChangeTrig.offset();
				emailChangeMenu.css({
					left: pos.left + 3,
					top: pos.top + 32
				});
			};

			var openEmailChangeMenu = function() {
				// Means we're opening fo rhte first time
				if (!emailChangeBackdrop) {
					emailChangeBackdrop = $('<div class="backdrop"></div>');
					emailChangeBackdrop.appendTo('body');
					emailChangeBackdrop.on('click', function(ev) {
						ev.stopPropagation();
						closeEmailChangeMenu();
					});
					emailChangeMenu.detach().appendTo('body');

					emailChangeMenu.find('li').on('click', function(ev) {
						ev.preventDefault();;
						var item = $(this);
						var emailId = item.data('email-id');
						var text = item.text().trim();

						emailText.text(text);
						closeEmailChangeMenu();

						$.ajax({
							url: BASE_URL + 'agent/tickets/'+self.meta.ticket_id+'/ajax-change-email.json',
							data: { email_id : emailId },
							dataType: 'json',
							type: 'POST'
						});
					});
				}

				emailChangeBackdrop.show();
				updateEmailChangePos();
				emailChangeMenu.show();
			};


			emailChangeTrig.on('click', function(ev) {
				ev.preventDefault();
				openEmailChangeMenu();
			});
		}

		this.billing = new DeskPRO.Agent.PageHelper.TicketBilling(this.getEl('billing_wrap'), this.meta.baseId, {
			auto_start_bill: this.meta.auto_start_bill
		});

		this.addEvent('deactivate', function() {
			$('form.ticket-reply-form', this.getEl('replybox_wrap')).trigger('page_deactivate');
		});
		this.addEvent('activate', function() {
			$('form.ticket-reply-form', this.getEl('replybox_wrap')).trigger('page_activate');
		});

		this.addEvent('destroy', function() {
			if (self.meta.unlockOnClose && self.getEl('locked_message').data('locked-self')) {
				$.ajax({
					url: BASE_URL + 'agent/tickets/'+self.meta.ticket_id+'/release-lock.json',
					type: 'POST'
				});
			}
		}, false, false, true);

		if (this.meta.ticket_perms['delete']) {
			this.wrapper.on('click', 'button.undelete-trigger', this.doTicketUndelete.bind(this));
			this.wrapper.on('click', 'button.unspam-trigger', this.doTicketUnspam.bind(this));
		}

		DeskPRO_Window.getMessageBroker().sendMessage('ui.ticket.opened', { ticketId: this.getMetaData('ticket_id') });
		DeskPRO_Window.getMessageBroker().sendMessage('ui.tab.opened', { type: 'tickets', id: this.getMetaData('ticket_id') });

		DeskPRO_Window.getMessageBroker().addMessageListener('tickets.deleted', (function(ticket_ids) {
			if (ticket_ids.indexOf(this.getMetaData('ticket_id')) !== -1) {
				DeskPRO_Window.removePage(this);
			}
		}).bind(this), this.pageUid);

		DeskPRO_Window.getMessageBroker().addMessageListener('agent.ui.reload', function (info) {
			self.getReplyTextArea().trigger('dp_autosave_trigger');
		});

		this.addEvent('shortcutFocusReply', function(ev) {
			ev.preventDefault();

			// Scroll down
			if (!self.meta.ticket_reverse_order) {
				self.wrapper.find('div.layout-content').trigger('goscrollbottom');
			}
			self.focusOnReply();
		});

		this.addEvent('shortcutOpenSnippets', function(ev) {
			ev.preventDefault();
			self.shortcutOpenSnippets();
		});
		this.addEvent('shortcutSendReply', function(ev) {
			ev.preventDefault();
			self.shortcutSendReply();
		});
		this.addEvent('shortcutReplySetAwaitingUser', function(ev) {
			ev.preventDefault();
			self.shortcutReplySetAwaitingUser();
		});
		this.addEvent('shortcutReplySetAwaitingAgent', function(ev) {
			ev.preventDefault();
			self.shortcutReplySetAwaitingAgent();
		});
		this.addEvent('shortcutReplySetResolved', function(ev) {
			ev.preventDefault();
			self.shortcutReplySetResolved();
		});
		this.addEvent('shortcutReplyOpenProperties', function(ev) {
			ev.preventDefault();
			self.shortcutReplyOpenProperties();
		});

		this.addEvent('openUserProfile', function(ev) {
			ev.preventDefault();
			self.getEl('profile_link').trigger('click');
		});

		this.addEvent('openOrgProfile', function(ev) {
			ev.preventDefault();
			self.getEl('org_link').trigger('click');
		});

		if (this.meta.ticket_perms.modify_merge) {
			this.merge = new DeskPRO.Agent.Widget.Merge({
				tabType: 'ticket',
				metaId: self.meta.ticket_id,
				metaIdName: 'ticket_id',
				trigger: $('.merge-menu-trigger', this.wrapper),
				overlayUrl: BASE_URL + 'agent/tickets/{id}/merge-overlay/{other}',
				mergeUrl: BASE_URL + 'agent/tickets/{id}/merge/{other}',
				loadRoute: 'ticket:' + BASE_URL + 'agent/tickets/{id}',
				overlayLoaded: function(overlay, merge) {
					overlay.getWrapper().find('.ticket-finder').bind('ticketsearchboxclick', function(ev, ticketId, subject, sb) {
						sb.close();

						$.ajax({
							url: merge._getOverlayUrl(merge.options.metaId, ticketId),
							type: 'get',
							dataType: 'html',
							success: function(html) {
								merge.resetOverlay(html);
							}
						});
					});
				}
			});
			this.ownObject(this.merge);

			this.mergeMenu = new (function() {
				var menuEl = null;
				var menuElInner = null;
				var backEl = null;
				var hasInitUserTickets = false;
				var lastOvers = null;
				var lastOverId = null;

				var updateOverHighlight = function(ticketId) {
					removeLastOverHighlight();

					if (!DeskPRO_Window.sections.tickets_section || !DeskPRO_Window.sections.tickets_section.isVisible()) {
						return;
					}

					var searchListEl = DeskPRO_Window.sections.tickets_section.getListElement();
					lastOverId = ticketId;
					lastOvers = searchListEl.find('.ticket-' + ticketId);
					lastOvers = lastOvers.add($('#tabNavigationPane').find('.ticket-' + ticketId));
					lastOvers.addClass('item-hover-over');
				};

				var removeLastOverHighlight = function() {
					if (lastOvers) {
						lastOvers.removeClass('item-hover-over');
					}
					lastOvers = null;
					lastOverId = null;
				}

				var renderTicketOption = function(ticket) {
					var row = $('<li><time></time><a><strong></strong><span></span></a></li>');
					row.data('ticket-id', ticket.id);
					row.addClass('ticket-' + ticket.id + ' ticket');

					row.find('strong').text(ticket.id);
					row.find('span').text(ticket.subject);

					var d = new Date(ticket.last_activity*1000);
					row.find('time').attr('datetime', d.toISOString()).timeago();

					row.on('mouseover', function() {
						updateOverHighlight(ticket.id);
					}).on('mouseout', function() {
						if (lastOverId && lastOverId == ticket.id) {
							removeLastOverHighlight();
						}
					});

					return row.get(0);
				};

				var refreshOpenTickets = function() {
					var append = [];
					Array.each(DeskPRO_Window.getTabWatcher().findTabType('ticket'), function(tab) {
						var id = tab.page.getMetaData('ticket_id');
						if (id && id != self.meta.ticket_id) {
							var row = renderTicketOption({
								id: id,
								subject: tab.title,
								last_activity: tab.page.getMetaData('last_activity')
							});

							append.push(row);
						}
					});

					if (append.length) {
						menuEl.find('.open-tickets').show().find('ul').empty().append($(append));
					} else {
						menuEl.find('.open-tickets').hide().find('ul').empty();
					}
				};

				var refreshUserTickets = function() {
					$.ajax({
						url: BASE_URL + 'agent/ticket-search/quick-search',
						data: {
							person_id: self.meta.person_id
						},
						dataType: 'json',
						success: function(data) {
							var append = [];
							Array.each(data, function(t) {
								if (t.id && t.id != self.meta.ticket_id) {
									var row = renderTicketOption(t);
									append.push(row);
								}
							});

							if (append.length) {
								menuEl.find('.users-tickets').show().find('ul').empty().append($(append));
							} else {
								menuEl.find('.users-tickets').hide().find('ul').empty();
							}
						}
					});
				};

				var refreshFilterResults = function() {
					if (!DeskPRO_Window.sections.tickets_section || !DeskPRO_Window.sections.tickets_section.isVisible()) {
						menuEl.find('.filter-tickets').hide().find('ul').empty();
						return;
					}

					var searchListEl = DeskPRO_Window.sections.tickets_section.getListElement();
					var append = [];

					searchListEl.find('.row-item').each(function() {
						var el = $(this);
						var t = {
							id: el.data('ticket-id'),
							subject: $.trim(el.find('.subject').text()),
							last_activity: parseInt(el.data('ticket-lastactivity'))
						};
						if (!t.id) {
							return;
						}

						if (t.id && t.id != self.meta.ticket_id) {
							var row = renderTicketOption(t);
							append.push(row);
						}
					});

					if (append.length) {
						menuEl.find('.filter-tickets').show().find('ul').empty().append($(append));
					} else {
						menuEl.find('.filter-tickets').hide().find('ul').empty();
					}
				};

				var openMenu = function(atEl) {
					var tmp;
					if (!menuEl) {
						menuEl = $('<div/>');
						menuEl.addClass('dp-popover');
						menuEl.css('width', 500);

						menuElInner = $('<div/>').addClass('dp-popover-inner');
						menuElInner.appendTo(menuEl);

						backEl = $('<div/>');
						backEl.addClass('dp-popover-backdrop');

						tmp = $('<div/>').html('<section><header><strong>'+self.meta.lang.find_ticket+'</strong></header><article style="padding: 6px;"><button class="trigger-search dp-btn dp-btn-small">'+self.meta.lang.search+'</button></article></section>');
						tmp.addClass('search-tickets');
						tmp.appendTo(menuElInner);

						tmp = $('<div/>').html('<section><header><strong>'+self.meta.lang.users_tickets+'</strong></header><article><ul></ul></article></section>');
						tmp.addClass('users-tickets').hide();
						tmp.appendTo(menuElInner);

						tmp = $('<div/>').html('<section><header><strong>'+self.meta.lang.open_tickets+'</strong></header><article><ul></ul></article></section>');
						tmp.addClass('open-tickets').hide();
						tmp.appendTo(menuElInner);

						tmp = $('<div/>').html('<section><header><strong>'+self.meta.lang.filter_results+'</strong></header><article><ul></ul></article></section>');
						tmp.addClass('filter-tickets').hide();
						tmp.appendTo(menuElInner);

						menuEl.find('.trigger-search').on('click', function(ev) {
							Orb.cancelEvent(ev);
							self.merge.open();
							closeMenu();
						});

						menuEl.on('click', 'li', function(ev) {
							Orb.cancelEvent(ev);
							closeMenu();
							self.merge.openWithId($(this).data('ticket-id'));
						});

						backEl.on('click', function(ev) {
							Orb.cancelEvent(ev);
							closeMenu();
						});

						menuEl.appendTo('body');
						backEl.appendTo('body')
					}

					if (!hasInitUserTickets) {
						hasInitUserTickets = true;
						refreshUserTickets();
					}
					window.setTimeout(function() { refreshOpenTickets(); }, 1);
					window.setTimeout(function() { refreshFilterResults(); }, 1);

					var maxH = parseInt(($(window).height() / 2) - 40);
					menuEl.find('.dp-popover-inner').css('max-height', maxH);

					menuEl.show();
					menuEl.position({
						of: atEl,
						my: 'center top',
						at: 'center bottom',
						collision: 'flipfit'
					});
					backEl.show();
				};

				var closeMenu = function() {
					menuEl.hide();
					backEl.hide();

					removeLastOverHighlight();
				};

				self.wrapper.find('.merge-menu-trigger').on('click', function(ev) {
					Orb.cancelEvent(ev);
					openMenu($(this));
				});

				this.destroy = function() {
					if (menuEl) {
						menuEl.detach();
					}
					if (backEl) {
						backEl.detach();
					}
				};
			})();
		}

		this.addEvent('deactivate', function() {
			if (self.ticketReplyBox && self.ticketReplyBox.textarea) {
				self.ticketReplyBox.textarea.trigger('dp_autosave_trigger');
			}
		});

		$('form.ticket-reply-form', this.getEl('replybox_wrap')).bind('replyboxsubmit', this.handleReplySave.bind(this));

		this.ticketActions = new DeskPRO.Agent.PageFragment.Page.Ticket.TicketActions(this);
		this.ownObject(this.ticketActions);

		if (this.meta.isLocked) {
			this.ticketLocked = new DeskPRO.Agent.PageFragment.Page.Ticket.TicketLocked(this);
			this.ownObject(this.ticketLocked);
		}

        $('.agent-link.other-agent', this.El).on('click', function() {
            DeskPRO_Window.sections.agent_chat_section.newChatWindow([$(this).data('agent-id')]);
        });

		this.getEl('newtask').on('click', function(ev) {
			ev.preventDefault();
			DeskPRO_Window.newTaskLoader.open();
		});

		this.ticketFields.updateDisplay();

		this.wrapper.find('.lock-overlay').on('click', function(ev) {
			ev.preventDefault();
			self.showLockAlert();
		});

		this.getEl('idref_switch').on('click', function() {
			if ($(this).hasClass('refmode')) {
				$(this).removeClass('refmode')
				self.getEl('ref_num').hide();
				self.getEl('id_num').show();
			} else {
				$(this).addClass('refmode')
				self.getEl('id_num').hide();
				self.getEl('ref_num').show();
			}
		});

		DeskPRO.ElementHandler_Exec(this.wrapper);
		var messageboxTabs = this.getEl('messagebox_tabs').data('simpletabs');
		if (messageboxTabs) {
			messageboxTabs.addEvent('tabSwitch', function(evData) {
				var type = evData.tabEl.data('list-type');

				if (type == 'messages') {
					self.getEl('messages_wrap').removeClass('show-log');
					self.getEl('messages_wrap').find('article.content-message').show();
				} else if (type == 'feedback') {
					self.getEl('messages_wrap').removeClass('show-log');
					self.getEl('messages_wrap').find('article.content-message').show().not('article.with-feedback').hide();
				} else if (type == 'log') {
					self.refreshLogTypes();
				}
			});
		}

		this.getEl('people_box_agent').find('.select2-container-multi').css('width', '90%').find('input.select2-input').css('width', '90%');

		if (this.getEl('field_errors').hasClass('on')) {
			this.wrapper.addClass('field-error');
			this.ticketFields.openEditMode();
		}

		var messagePageWrap = this.getEl('message_page_wrap');
		var messagesWrap = this.getEl('messages_wrap');
		this.getEl('message_prev_page').on('click', function(ev) {
			ev.preventDefault();
			if (self.meta.ticket_reverse_order) {
				var p = parseInt(messagesWrap.data('page')) - 1;
			} else {
				var p = parseInt(messagesWrap.data('page')) + 1;
			}
			self.loadMessagePage(p);
		});

		this.getEl('message_next_page').on('click', function(ev) {
			ev.preventDefault();
			if (self.meta.ticket_reverse_order) {
				var p = parseInt(messagesWrap.data('page')) + 1;
			} else {
				var p = parseInt(messagesWrap.data('page')) - 1;
			}
			self.loadMessagePage(p);
		});

		var head = this.getEl('properties_header');
		var st   = head.find('nav').data('simpletabs');
		if (st) {
			st.addEvent('tabSwitch', function(evData) {
				var id = $(evData.tabEl).attr('id') || '';

				if (id && id.indexOf('fields_display_main_wrap_tab') !== -1) {
					head.removeClass('controls-off');
				} else {
					head.addClass('controls-off');
				}
			});
		}

		this.getEl('cc_list_btn').on('click', function(ev) {
			ev.preventDefault();

			if (self.getEl('cc_list').hasClass('cc-open')) {
				if (!self.getEl('cc_row_list').find('li')[0]) {
					self.getEl('cc_list').hide().removeClass('cc-open');
				} else {
					self.getEl('cc_list').find('.addrow').toggle();
				}
			} else {
				self.getEl('cc_list').show().addClass('cc-open');
				self.getEl('cc_list').find('.addrow').show();
			}
		});

		var logsWrap = this.getEl('logs_wrap');
		logsWrap.on('click', '.trigger-update-filter', function(ev) {
			Orb.cancelEvent(ev);

			var logsNav  = logsWrap.find('nav').first();
			var filter = $(this).data('typename');

			var postData = [];

			if (filter && filter != 'all') {
				postData.push({name: 'filter', value: filter});
			}
			postData.push({name: 'page', value: 1});

			logsNav.addClass('dp-loading-on');
			if (filter == 'attach') {
				$.ajax({
					url: BASE_URL + 'agent/tickets/'+self.meta.ticket_id+'/load-attach-list',
					data: postData,
					complete: function() {
						logsNav.removeClass('dp-loading-on');
					},
					success: function(html) {
						logsWrap.html(html);
						self.updateUi();
					}
				});
			} else {
				$.ajax({
					url: BASE_URL + 'agent/tickets/'+self.meta.ticket_id+'/load-logs',
					data: postData,
					complete: function() {
						logsNav.removeClass('dp-loading-on');
					},
					success: function(html) {
						logsWrap.html(html);
						self.updateUi();
					}
				});
			}
		});

		logsWrap.on('click', '.trigger-next-page', function(ev) {
			var btn = $(this);
			var logsNav  = logsWrap.find('nav').first();

			var filter = logsNav.data('filter');
			var page   = logsNav.data('page');

			var postData = [];
			if (filter && filter != 'all') {
				postData.push({name: 'filter', value: filter});
			}
			if (page) {
				page++;
				postData.push({name: 'page', value: page});
			}

			logsNav.addClass('dp-loading-on');
			btn.addClass('dp-loading-on');
			$.ajax({
				url: BASE_URL + 'agent/tickets/'+self.meta.ticket_id+'/load-logs',
				data: postData,
				complete: function() {
					logsNav.removeClass('dp-loading-on');
					btn.remove();
				},
				success: function(html) {
					var el = $(html);
					var newNav = el.find('nav').first();
					var newPage = el.find('.logs-page').first();

					DeskPRO_Window.initInterfaceServices(newPage);

					logsNav.replaceWith(newNav);
					logsWrap.append(newPage);

					self.updateUi();
				}
			});
		});


		logsWrap.on('click', '.expand', function(ev) {
			var expandBtn = $(this);
			var el = expandBtn.closest('.log-row');

			if (!el[0]) {
				return;
			}

			Orb.cancelEvent(ev);

			var sel = '.expand-set';
			if ($(this).data('set')) {
				sel = $(this).data('set');
			}

			var expandEl = $(sel, el);
			if (expandEl.is(':visible')) {
				expandEl.slideUp();
				expandBtn.removeClass('open');
			} else {
				expandEl.slideDown();
				expandBtn.addClass('open');
			}
		});
	},

	setTicketReplyBox: function(rb) {
		var isFirst = this.ticketReplyBox ? false : true;

		if (this.ticketReplyBox) {
			this.ticketReplyBox.destroy();
			this.ticketReplyBox = null;
		}

		this.ticketReplyBox = rb;

		if (isFirst && this.meta.ticket_reverse_order) {
			this.focusOnReply();
		}
	},

	loadMessagePage: function(page, noShowLoading) {
		var messagePageWrap = this.getEl('message_page_wrap');
		var messagesWrap = this.getEl('messages_wrap');

		// No page means reload current page
		var reload = false;
		if (!page) {
			reload = true;
			page = parseInt(messagesWrap.data('page'));
		}

		var loadDiv = false;
		if (!noShowLoading) {
			loadDiv = $('<div style="padding: 25px;"><div class="loading-icon-big">&nbsp;</div></div>');
			if (!this.meta.ticket_reverse_order) {
				messagePageWrap.empty();
			}
			messagePageWrap.append(loadDiv);
			this.updateUi();
		}

		$.ajax({
			url: BASE_URL + 'agent/tickets/'+ this.meta.ticket_id +'/message-page/' + page,
			type: 'GET',
			dataType: 'html',
			context: this,
			success: function(html) {
				if (loadDiv) {
					loadDiv.remove();
				}
				if (this.meta.ticket_reverse_order && !reload) {
					var div = $('<div></div>');
					div.html(html);
					this._initMessage(div.find('article.content-message'));

					messagePageWrap.append(div);
				} else {
					messagePageWrap.empty();
					messagePageWrap.html(html);
					this._initMessage(messagePageWrap.find('article.content-message'));
				}
				this.updateUi();

				var d = messagePageWrap.find('> div').first();
				if (d[0]) {
					messagesWrap.data('page-count', d.data('page-count'));
					messagesWrap.data('page', d.data('page'));
				}

				messagesWrap.data('page', page);
				var numPages = parseInt(messagesWrap.data('page-count'));

				if (this.meta.ticket_reverse_order) {
					if (page == numPages) {
						this.getEl('message_next_page').hide();
					} else {
						this.getEl('message_next_page').show();
					}
				} else {
					if (page == numPages) {
						this.getEl('message_prev_page').hide();
					} else {
						this.getEl('message_prev_page').show();
					}
					if (page == 1) {
						this.getEl('message_next_page').hide();
					} else {
						this.getEl('message_next_page').show();
					}
				}
			}
		});
	},

	refreshLogTypes: function() {
		var self     = this;
		var logsLi   = this.getEl('messagebox_tabs').find('.logs');
		var logsWrap = this.getEl('logs_wrap');
		var logsNav  = logsWrap.find('nav').first();
		var isActive = logsLi.hasClass('on');

		if (!isActive || !logsLi.hasClass('dirty')) {
			return;
		}

		var filter = logsNav.data('filter');
		var page   = logsNav.data('page');

		var postData = [];
		if (filter && filter != 'all') {
			postData.push({name: 'filter', value: filter});
		}
		if (page && page != 1) {
			postData.push({name: 'page', value: page});
			postData.push({name: 'up_to_page', value: 1});
		}

		$.ajax({
			url: BASE_URL + 'agent/tickets/'+self.meta.ticket_id+'/load-logs',
			data: postData,
			success: function(html) {
				logsWrap.html(html);
				self.updateUi();
			}
		});
	},

	showLockAlert: function() {
		DeskPRO_Window.showAlert('You are not allowed to make any changes to this ticket until it has been unlocked.');
	},

	handleReplySave: function(ev, formData, handler) {

		if (this.pauseSend) {
			window.setTimeout((function() {
				this.handleReplySave(ev, formData, handler);
			}).bind(this), 250);
		}

		if (this.replySaveAjax) {
			return;
		}

		var self = this;
		var closetabTimeoutHit = false;
		var ajaxHit = false;
		var hitRun = false;
		var reply_form = handler.el;

		formData.push({
			name: 'client_messages_since',
			value: DeskPRO_Window.getLastClientMessageId()
		});

		formData.push({
			name: 'last_message_id',
			value: this.getLastMessageId()
		});
		formData.push({
			name: 'last_log_id',
			value: this.getEl('messages_wrap').find('.log-row').last().data('log-id')
		});

		if (this.getReplyTextArea()) {
			this.getReplyTextArea().data('disable-autosave', true);
			if (this.getReplyTextArea().data('autosave-running')) {
				this.getReplyTextArea().data('autosave-running').abort();
				this.getReplyTextArea().data('autosave-running', null);
			}
		}

		var form = this.getEl('replybox_wrap').find('.ticket-reply-form');
		var keepOpen = true;
		if (this.getEl('replybox_wrap').find('[name="options[close_tab]"]').prop('checked')) {
			keepOpen = false;
		}

		var loadingEl = this.getEl('replybox_wrap').find('.ticket-sending-overlay');
		loadingEl.fadeIn();

		this.getEl('replybox_wrap').find('textarea.touched').removeClass('touched');

		DeskPRO_Window.getMessageChanneler().poller.pause();
		function hitDone() {
			hitRun = true;
			DeskPRO_Window.getMessageChanneler().poller.unpause();

			if (!keepOpen) {
				self.closeSelf();

				if (self.getMetaData('goNextOnReply')) {
					var listPage = DeskPRO_Window.getListPage();
					if (listPage && listPage.wrapper) {
						console.log(listPage.wrapper);
						var ticketListEl = listPage.wrapper.find('article.row-item.ticket-' + self.getMetaData('ticket_id'));
						if (ticketListEl.length) {
							var next = ticketListEl.next('article.row-item');
							console.log(ticketListEl);
							console.log(next);
							if (next.length) {
								DeskPRO_Window.runPageRouteFromElement(next);
							}
						}
					}
				}

				return;
			}

			var result = ajaxHit;

			// If the agent cant see the ticket anymore, they dont have permission to
			// view it anymore.
			if (!result.can_view) {
				self.closeSelf();
				return;
			}

			if (result.refresh_tab) {
				// Reload the ticket page
				DeskPRO_Window.loadPage(BASE_URL + 'agent/tickets/' + self.getMetaData('ticket_id'), {ignoreExist:true});
				self.closeSelf();
			}

			loadingEl.hide();

			if (result.error && result.error == 'no_message') {
				DeskPRO_Window.showAlert("Please enter a message");
				return;
			}

			if (result.dupe_message) {
				DeskPRO_Window.showAlert("You have already sent that message.");
				self.loadMessagePage(0, true);
				return;
			}

			self.handleTicketUpdate(result);

			// Reload the message row in results
			//addTicket
			if (DeskPRO_Window.sections.tickets_section && DeskPRO_Window.sections.tickets_section.listPage) {
				var row = DeskPRO_Window.sections.tickets_section.listPage.wrapper.find('article.ticket-' + self.meta.ticket_id);
				if (row[0] && !row.hasClass('removing')) {
					DeskPRO_Window.sections.tickets_section.listPage.addTicket(self.meta.ticket_id, true);
				}
			}
		};

		this.clearAlerts();

		this.replySaveAjax = $.ajax({
			url: reply_form.attr('action'),
			type: 'POST',
			dataType: 'json',
			data: formData,
			context: this,
			noErrorOverride: true,
			complete: function() {
				this.replySaveAjax = null;
				DeskPRO_Window.getMessageChanneler().poller.unpause();

				this.getReplyTextArea().data('disable-autosave', false);
			},
			error: function(event, xhr, ajaxOptions, errorThrown, force) {
				DeskPRO_Window.getMessageChanneler().poller.unpause();
				var loadingEl = this.getEl('replybox_wrap').find('.ticket-sending-overlay');
				loadingEl.hide();

				DeskPRO_Window._globalHandleAjaxError(event, xhr, ajaxOptions, errorThrown, force);
			},
			success: function(result) {

				// Always perform CM processing right now
				DeskPRO_Window.getMessageChanneler().poller.unpause();

				if (result.client_messages) {
					DeskPRO_Window.getMessageChanneler().handleMessageAjax(result.client_messages);

					// null out so handleTicketUpdate called in hitDone doesnt re-process them
					result.client_messages = null;
				}

				if (result.error_messages) {

					var prop = self.changeManager.getPropertyManager('status');
					self.changeManager.setInstantChange(prop, 'awaiting_agent');

					var list = self.getEl('field_errors').find('ul').empty();
					Array.each(result.error_messages, function(msg) {
						var li = $('<li/>');
						li.text(msg);
						li.appendTo(list);
					});

					self.getEl('field_errors').show().addClass('on');

					self.getEl('field_edit_start').click();
					self.getEl('field_edit_cancel').show();
					self.getEl('field_edit_save').show();
					self.getEl('field_edit_controls').removeClass('loading');

					DeskPRO_Window.showAlert('Your reply was saved but the status was not set to resolved because of form errors. You should correct these errors and then you may set the status to resolved.');
					keepOpen = true;
				}

				if (result.notified_agents && DeskPRO.Agent.Widget.AgentChatWin_Registry) {
					Array.each(result.notified_agents, function(aid) {
						aid = parseInt(aid);
						Object.each(DeskPRO.Agent.Widget.AgentChatWin_Registry, function(v,k) {
							if (v.agentIds.length == 1 && v.agentIds.indexOf(aid) !== -1) {
								v.loadLastConvo();
							}
						});
					});
				}

				ajaxHit = result;
				hitDone();
			}
		});
	},

	getLastMessageId: function() {
		var id = this.getEl('messages_wrap').find('article.message').last().data('message-id');
		return id || 0;
	},

	getLastLogId: function() {
		return parseInt($('.log-row', this.getEl('messages_wrap')).last().data('log-id') || 0);
	},

	destroyPage: function() {
		if (this.ticketReplyBox) {
			this.ticketReplyBox.destroy();
			this.ticketReplyBox = null;
		}
		DeskPRO_Window.getMessageBroker().sendMessage('ui.ticket.closed', { ticketId: this.getMetaData('ticket_id') });
	},

	handleTicketUpdate: function(data) {
		var self = this;
		if (data.client_messages) {
			DeskPRO_Window.getMessageChanneler().handleMessageAjax(data.client_messages);
		}

		if (data.active_drafts) {
			this.wrapper.find('.agent-draft-message').remove();
			if (data.active_drafts.length) {
				var insertPos = this.wrapper.find('.ticket-messages .messages-wrap');
				for (var i = 0; i < data.active_drafts.length; i++) {
					if (this.meta.ticket_reverse_order) {
						insertPos.prepend(data.active_drafts[i]);
					} else {
						insertPos.append(data.active_drafts[i]);
					}
				}
			}
		}

		// Might be unloaded by the time this callback is called
		if (!this.changeManager) {
			return;
		}

		var new_messages = null;
		if (data.ticket_messages_block) {
			new_messages = $(data.ticket_messages_block);

			var any = false;
			if (new_messages.hasClass('message')) {
				if (!this.getEl('messages_wrap').find('.message-' + new_messages.data('message-id'))[0]) {
					any = true;
				}
			} else {
				new_messages.find('.message').each(function() {
					if (self.getEl('messages_wrap').find('.message-' + $(this).data('message-id'))[0]) {
						$(this).hide();
					} else {
						any = true;
					}
				});
			}

			if (any) {
				this.wrapper.find('.agent-draft-message').remove();
				self.loadMessagePage(0, true);
			}
		}

		if (data.updated_agent_parts_html) {
			this.getEl('agent_part_list').html(data.updated_agent_parts_html);
			$('.agent-part-count', this.wrapper).text(data.updated_agent_parts_count);
		}

		if (data.replybox_html) {
			// Only refresh the box if we've not begun writing a message
			if (!this.getEl('replybox_wrap').find('textarea.touched')[0]) {
				var textarea = this.getReplyTextArea();
				if (textarea.data('redactor')) {
					textarea.destroyEditor();
				}
				this.getEl('replybox_wrap').empty().append(data.replybox_html);
				DeskPRO_Window.initInterfaceServices(this.getEl('replybox_wrap'));
				$('form.ticket-reply-form', this.getEl('replybox_wrap')).bind('replyboxsubmit', this.handleReplySave.bind(this));
			}
		}

		if (typeof data.cc_list == "string") {
			this.wrapper.find('ul.cc-row-list').empty().html(data.cc_list);
		}

		var billing = this.billing;
		if (billing.hasBilling) {
			if (data.charge_html) {
				billing.addBillingRow(data.charge_html);
				billing.updateBillingForm(true);
				billing.resetBillingForm();
			} else {
				billing.updateBillingForm(false);
				billing.resetBillingForm();
			}
		}

		if (data.locked_by_agent_id && data.locked_by_agent_id != DESKPRO_PERSON_ID) {
			// Reload the ticket page
			DeskPRO_Window.loadPage(BASE_URL + 'agent/tickets/' + self.getMetaData('ticket_id'), {ignoreExist:true});
			self.closeSelf();
			return;
		} else {
			this.wrapper.find('.lock-overlay').remove();
			this.getEl('locked_message').hide();
			this.getEl('locked_message').data('locked-self', false);
			this.getEl('lock_ticket').show();
			this.getEl('unlock_ticket').hide();
		}

		var props = ['status', 'department_id', 'category_id', 'product_id', 'workflow_id', 'priority_id', 'urgency', 'is_hold'];
		if (data.via_reply) {
			if (data.changed_agent) {
				props.push('agent_id');
			}
			if (data.changed_team) {
				props.push('agent_team_id');
			}
		} else {
			props.push('agent_id');
			props.push('agent_team_id');
		}

		Array.each(props, function(propId) {
			var val = '0';
			if (data[propId]) {
				val = data[propId];
			}

			var prop = this.changeManager.getPropertyManager(propId);
			prop.setIncomingValue(val);
		}, this);

		if (data.dupe_message) {
			// If its a dupe then it'd already be added ot the message list,
			// we can just clear out the message box
			var sig = this.getEl('replybox_wrap').find('textarea.signature-value').val();
			if (sig) sig = "\n\n" + sig;

			var textarea = this.getReplyTextArea();
			if (textarea.data('redactor')) {
				textarea.setCode(DP.convertTextToWysiwygHtml(sig, true));
			} else {
				textarea.val(sig);
			}
			return;
		}

		this.getEl('messagebox_tabs').find('.logs').addClass('dirty');
		this.refreshLogTypes();
	},

	updateUi: function(toReplyHeight) {
		var x;
		if (!this.IS_ACTIVE) {
			return;
		}
		if (this.wrapper) {
			if (!this.scrollHandlers) {
				this.scrollHandlers = this.wrapper.find('div.with-scroll-handler');
			}
			for (x = 0; x < this.scrollHandlers.length; x++) {
				var sh = $(this.scrollHandlers[x]).data('scroll_handler');
				if (sh && sh.updateSize) {
					sh.updateSize();
				}
			};

			if (this.doScrollBottom) {
				this.wrapper.find('div.layout-content').trigger('goscrollbottom_stick');
				this.doScrollBottom = false;
			} else if (toReplyHeight) {
				var oWrap = this.wrapper.find('div.layout-content').first();
				oWrap.trigger('goscrollto', toReplyHeight+10);
			}

			this.getEl('labels_wrap').find('.select2-input').width('95%');
		}
	},

	_initMessage: function(messageEl) {
		var self = this;
		var imageEls = $('ul.attachment-list li.is-image a, a.dp-is-image', messageEl);

		DeskPRO_Window.initStickyTips(messageEl);

		$('.timeago', messageEl).timeago();

		imageEls.colorbox({
			title: function(){
				if ($(this).data('deskpro-url')) {
					var url = $(this).data('deskpro-url');
				} else {
					var url = $(this).attr('href');
				}
				var dl_url = Orb.appendQueryData(url, 'dl', '1');
				return '<a href="'+url+'" target="_blank">Open In New Window</a> | <a href="'+dl_url+'" target="_blank">Download</a>'
			},
			onComplete: function() {
				var image = $('#cboxLoadedContent img');
				if (image.length) {
					$('#cboxLoadedContent').append(
						$('<a />').attr('href', $(this).attr('href')).attr('target', '_blank').append(image)
					);
				}
			},
			width: '50%',
			height: '50%',
			initialWidth: '200',
			initialHeight: '150',
			scalePhotos: true,
			photo: true,
			opacity: 0.5,
			transition: 'none'
		});

		var lastCount = 0;
		if (this.lastMessageCount) {
			lastCount = this.lastMessageCount;
		}

		if (messageEl.hasClass('messages-wrap')) {
			var articles = messageEl.find('article.content-message');
		} else {
			var articles = messageEl.filter('article.content-message');
		}
		articles.each(function() {
			var article = $(this);

			if (article.is('.agent-draft-message')) {
				return;
			}

			var fullEl = article.find('.body-text-full-message');
			if (fullEl[0]) {
				var simpleEl = article.find('.body-text-message');
				fullEl.find('.message-toggle-btn > em').on('click', function(ev) {
					ev.preventDefault();
					fullEl.hide();
					simpleEl.show();
					self._initTicketMessageClipped(article);
					self.updateUi();
				});
				simpleEl.find('.message-toggle-btn > em').on('click', function(ev) {
					ev.preventDefault();
					fullEl.show();
					simpleEl.hide();
					self.updateUi();

					if (!fullEl.hasClass('loaded')) {
						var row = $(this).closest('article.content-message');
						var message_id = row.data('message-id');

						$.ajax({
							url: BASE_URL + 'agent/tickets/messages/'+message_id+'/get-full-message.json',
							type: 'GET',
							success: function(data) {
								row.find('.full-message-content').html(data.message_full)
								self._initTicketMessageClipped(article);
								self.updateUi();
							}
						});
					}
				});
			}

			self._initTicketMessageClipped(article);

			var trans = article.find('.message-translate-controls');
			if (trans[0]) {
				var transShow     = article.find('.body-message-translated');
				var existTo       = transShow.data('to-lang-code');
				var existFrom     = transShow.data('from-lang-code');
				var transMenu     = trans.find('.dp-lang-choose');
				var transMenuBack = null;
				var transFromEl   = trans.find('.translate-from-lang');
				var transToEl     = trans.find('.translate-to-lang');

				trans.on('click', '.translate-controls-off', function(ev) {
					Orb.cancelEvent(ev);
					trans.addClass('on');
				});

				trans.find('.dp-dropdown-toggle').on('click', function(ev) {
					Orb.cancelEvent(ev);

					if (!transMenu.hasClass('has-init')) {
						transMenu.detach().appendTo('body');

						transMenu.addClass('has-init');
						transMenuBack = $('<div/>').addClass('dp-popover-backdrop').hide().appendTo('body');

						self.addEvent('destroy', function() {
							transMenu.detach();
							transMenuBack.detach();
						});

						transMenuBack.on('click', function(ev) {
							Orb.cancelEvent(ev);
							transMenu.hide();
							transMenuBack.hide();
						});

						transMenu.find('select').on('change', function(){
							var locale = $(this).val();
							var title = $(this).find(':selected').text();

							if ($(this).attr('name') == 'from') {
								transFromEl.data('locale', locale).text(title);
							} else {
								transToEl.data('locale', locale).text(title);
							}
						});
					}

					transMenu.css({top:0, left:0}).position({
						of: $(this),
						my: 'right top',
						at: 'right bottom',
						collision: 'flipfit'
					}).show();
					transMenuBack.show();
				});

				trans.find('.trans-trigger').on('click', function(ev) {
					Orb.cancelEvent(ev);
					self.refreshMessageTranslation(article);
				});
			}
		});
		this.lastMessageCount = lastCount;

		messageEl.find('img').bind('load', function() {
			var article = $(this).closest('article.content-message');
			self._initTicketMessageClipped(article);
		});

		var wr = this.getEl('messages_wrap');
		wr.find('.message-id-txt').each(function() {
			var findclass = '.message-counter-' + $(this).data('message-id');
			var counterText = wr.find(findclass).text().trim();
			if (counterText.length) {
				$(this).attr('title', $(this).text()).text(counterText).removeClass('message-id-txt');
			}
		});
	},

	refreshMessageTranslation: function(messageEl) {
		var trans     = messageEl.find('.message-translate-controls');
		var transShow = messageEl.find('.body-message-translated');

		var selFrom = trans.find('.translate-from-lang');
		var selTo   = trans.find('.translate-to-lang');

		var formData = {
			message_id: messageEl.data('message-id'),
			from: selFrom.data('locale'),
			to: selTo.data('locale')
		};

		selFrom.parent().find('em').text(selFrom.find(':selected').text());
		selTo.parent().find('em').text(selTo.find(':selected').text());

		trans.addClass('dp-loading-on');
		$.ajax({
			url: window.DESKPRO_TRANSLATE_SERVICE.translate_ticket_message_url,
			data: formData,
			type: 'POST',
			dataType: 'json',
			complete: function() {
				trans.removeClass('dp-loading-on');
			},
			success: function(data) {
				if (data.error_code) {
					DeskPRO_Window.showAlert("Could not translate message: " + data.message);
					return;
				}

				trans.removeClass('on');

				transShow.data('to-lang-code', data.to_lang_code);
				transShow.data('from-lang-code', data.from_lang_code);
				transShow.empty().html(data.message);
				transShow.show();

				transShow.parent().addClass('with-translated');
			}
		});
	},

	_initTicketMessageClipped: function(article) {
		var h = article.find('div.body-text').height();
		if (h >= 600) {
			if (!article.hasClass('with-clipped-body')) {
				article.addClass('with-clipped-body');
				article.find('.fade-bar-longmsg').one('click', function(ev) {
					ev.stopPropagation();
					article.addClass('clipped-show');
				});
			}
		} else {
			if (article.hasClass('with-clipped-body')) {
				article.removeClass('with-clipped-body')
			}
		}
	},

	incCount: function(id) {
		var countEl = $('.'+id+'-count', this.wrapper);
		var count = countEl.data('count') + 1;
		countEl.data('count', count).html('(' + count + ')');
	},

	setCount: function(id, count) {
		var countEl = $('.'+id+'-count', this.wrapper);
		countEl.data('count', count).html('(' + count + ')');
	},

	appendToMessage: function(content, is_html) {

		if (is_html) {
			var textarea = this.getReplyTextArea();
			if (textarea.data('redactor')) {
				try {
					textarea.data('redactor').restoreSelection();
					textarea.data('redactor').setBuffer();
				} catch (e) {}

				var html = content;
				html = html.replace(/<\/p>\s*<p>/g, '<br/>');
				html = html.replace(/^<p>/, '');
				html = html.replace(/<\/p>$/, '');
				textarea.data('redactor').insertHtml(html);
			}
		} else {
			this.insertTextInReply(content);

			// Scroll down
			if (!this.meta.ticket_reverse_order) {
				this.wrapper.find('div.layout-content').trigger('goscrollbottom_stick');
			}

			this.focusOnReply();

			// Resize it by firing change which'll run the resize
			this.getReplyTextArea().trigger('textareaexpander_fire');
		}
	},

	addAttachToList: function(attachInfo) {
		var row = $('.template-download', this.getEl('replybox')).tmpl(attachInfo);
		$('.file-list', this.getEl('replybox')).append(row);
		this.updateUi();
	},

	//#################################################################
	//# Property managers
	//#################################################################

	getPropertyManager: function(type, type_id) {

		DP.console.error('Depreciated');
		return this.changeManager.getPropertyManager(type, type_id);
	},

	//#################################################################
	//# Labels
	//#################################################################

	_initLabels: function() {
		if (this.getEl('labels_input')[0]) {
			this.labelsInput = new DeskPRO.UI.LabelsInput({
				type: 'tickets',
				input: this.getEl('labels_input'),
				onChange: this.saveLabels.bind(this)
			});
			this.ownObject(this.labelsInput);
		}
	},

	saveLabels: function() {
		if (this.changeManager.hasChanges()) {
			// If change manager has changes, we dont save new/removed tags
			return;
		}

		if (this._saveLabelsTimeout) {
			window.clearTimeout(this._saveLabelsTimeout);
		}

		this._labelsData = this.labelsInput.getFormData();
		this._saveLabelsTimeout = this._doSaveLabels.delay(2000, this);
	},

	_doSaveLabels: function() {
		var data = this._labelsData;

		$.ajax({
			url: this.getMetaData('labelsSaveUrl'),
			type: 'POST',
			context: this,
			data: data,
			dataType: 'json'
		});
	},

	//#################################################################
	//# Ticket actions menu
	//#################################################################

	_initTicketLocking: function() {
		var self = this;
		$([this.getEl('unlock_ticket').get(0), this.getEl('unlock_ticket2').get(0)]).on('click', function() {
			self.wrapper.find('.hide-locked').removeClass('hide-locked');
			self.wrapper.find('.lock-overlay').remove();
			self.getEl('locked_message').hide();
			self.getEl('locked_message').data('locked-self', false);
			self.getEl('lock_ticket').show();
			self.getEl('unlock_ticket').hide();
			$.ajax({
				url: BASE_URL + 'agent/tickets/' + self.meta.ticket_id + '/unlock-ticket.json',
				type: 'POST',
				dataType: 'json',
				complete: function() {

				}
			});
		});

		this.getEl('lock_ticket').on('click', function() {
			self.wrapper.find('.lock-overlay').remove();
			self.getEl('locked_message').data('locked-self', true);
			self.getEl('locked_message').show();
			self.getEl('locked_message_self').show();
			self.getEl('locked_message_other').hide();
			self.getEl('lock_ticket').hide();
			self.getEl('unlock_ticket').show();

			$.ajax({
				url: BASE_URL + 'agent/tickets/' + self.meta.ticket_id + '/lock-ticket.json',
				type: 'POST',
				dataType: 'json',
				success: function(data) {
					if (data.error) {
						DeskPRO_Window.showAlert('Someone else has already locked the ticket');
						self.getEl('locked_message').hide();
						self.getEl('lock_ticket').show();

						// Reload the ticket page
						DeskPRO_Window.loadPage(BASE_URL + 'agent/tickets/' + self.getMetaData('ticket_id'), {ignoreExist:true});
						self.closeSelf();
					}
				}
			});
		});
	},

	_initTicketActionsMenu: function() {
		var self = this;

		var removeMenu = new DeskPRO.UI.Menu({
			triggerElement: this.getEl('remove_menu_trigger'),
			menuElement: this.getEl('remove_menu'),
			onItemClicked: function(info) {
				var it = $(info.itemEl);
				var doBan = false;
				if (it.data('action').indexOf('.ban') !== -1) {
					doBan = true;
				}

				switch (it.data('action')) {
					case 'spam':
					case 'spam.ban':
						self.doTicketSpam(doBan);
						break;

					case 'delete':
					case 'delete.ban':
						self.showDeleteOverlay(doBan);
						break;
				}
			}
		});

		var actionsMenu = new DeskPRO.UI.Menu({
			triggerElement: this.getEl('actions_menu_trigger'),
			menuElement: this.getEl('actions_menu'),
			onBeforeMenuOpened: function(info) {
				var status = self.getEl('status_code').val();
				if (status == 'awaiting_agent') {
					if (self.getEl('value_form').find('.is_hold').val()) {
						self.getEl('menu_unset_hold').show();
					} else {
						self.getEl('menu_set_hold').show();
					}
				} else {
					self.getEl('menu_set_hold').hide();
					self.getEl('menu_unset_hold').hide();
				}
			},
			onItemClicked: function(info) {

				var it = $(info.itemEl);

				switch(it.data('action')) {
					case 'change-user':
					var changeUserOverlay = new DeskPRO.Agent.Widget.TicketChangeUser({
							ticketId: self.getMetaData('ticket_id'),
							destroyOnClose: true,
							onSuccess: function(data) {
								self.closeSelf();
								DeskPRO_Window.runPageRoute('ticket:' + BASE_URL + 'agent/tickets/' + data.ticket_id);
								changeUserOverlay.close();
							}
						});
						changeUserOverlay.open();
						break;

					case 'split':
						self.showSplitOverlay('');
						break;

					case 'print':
						window.print();
						break;

					case 'set-hold':
						self.setHold(true);
						break;

					case 'unset-hold':
						self.setHold(false);
						break;

					case 'linked_ticket':
						DeskPRO_Window.newTicketLoader.newLinkedTicket(self.meta.ticket_id);
						break;

					case 'kb-pending':
						if (!self.pendingKbOverlay) {
							var el = self.getEl('pending_add');
							self.pendingKbOverlay = new DeskPRO.UI.Overlay({
								contentElement: self.getEl('pending_add')
							});

							el.find('.save-new-trigger').on('click', function(ev) {
								ev.preventDefault();
								var formData = el.find('input, textarea').serializeArray();

								el.addClass('loading');
								$.ajax({
									url: el.data('save-url'),
									type: 'POST',
									data: formData,
									dataType: 'json',
									complete: function() {
										el.removeClass('loading');
									},
									success: function() {
										self.pendingKbOverlay.close();
										el.find('textarea').val('');
									}
								});
							});
						}
						self.pendingKbOverlay.open();
						break;
				}
			}
		});

		this.getEl('unhold_btn').on('click', function(ev) {
			ev.preventDefault();
			self.setHold(false);
		});
	},

	setHold: function(val) {
		var prop = this.changeManager.getPropertyManager('is_hold');
		this.changeManager.setInstantChange(prop, val ? 1 : 0);
	},

	_initDeleteOverlay: function() {

		if (this.deleteOverlay) return;

		this.deleteOverlayEl = $('.delete-ticket-overlay:first', this.wrapper);
		this.deleteOverlay = new DeskPRO.UI.Overlay({
			contentElement: this.deleteOverlayEl
		});
		this.ownObject(this.deleteOverlay);

		$('.save-trigger', this.deleteOverlayEl).on('click', (function() {
			this.doTicketDelete();
		}).bind(this));
	},

	showDeleteOverlay: function(doBan) {
		this._initDeleteOverlay();
		this.deleteOverlay.doBan = doBan;

		if (doBan) {
			this.getEl('delete_user_list').show();
		} else {
			this.getEl('delete_user_list').hide();
		}

		this.deleteOverlay.openOverlay();
	},

	doTicketDelete: function() {

		$('.loading-off', this.deleteOverlayEl).hide();
		$('.loading-on', this.deleteOverlayEl).show();

		var data = [];
		data.push({
			name: 'reason',
			value: $('.delete-reason', this.deleteOverlayEl).val()
		});

		if (this.deleteOverlay.doBan) {
			data.push({
				name: 'ban',
				value: 1
			})
		}

		var self = this;

		$.ajax({
			url: BASE_URL + 'agent/tickets/' + this.getMetaData('ticket_id') + '/delete',
			type: 'POST',
			data: data,
			dataType: 'json',
			success: function(data) {
				self.deleteOverlay.closeOverlay();
				self.getEl('remove_menu_trigger').hide();

				if (data.hidden_html) {
					self.getEl('page_header').before($(data.hidden_html));
				} else {
					DeskPRO_Window.removePage(self);
					DeskPRO_Window.loadPage(BASE_URL + 'agent/tickets/' + self.getMetaData('ticket_id'), {ignoreExist:true});
				}
			}
		});
	},

	doTicketSpam: function(doBan) {
		var self = this;

		this.getEl('actions_loading').show();

		$.ajax({
			url: BASE_URL + 'agent/tickets/' + this.getMetaData('ticket_id') + '/spam',
			type: 'POST',
			dataType: 'json',
			data: {
				ban: doBan ? 1 : 0
			},
			success: function(data) {
				self.getEl('actions_loading').hide();
				self.getEl('remove_menu_trigger').hide();

				if (data.hidden_html) {
					self.getEl('page_header').before($(data.hidden_html));
				}

				DeskPRO_Window.removePage(self);
			}
		});
	},

	doTicketUndelete: function() {
		var self = this;
		var prop = this.changeManager.getPropertyManager('status');
		this.changeManager.setInstantChange(prop, 'awaiting_agent', function() {
			DeskPRO_Window.removePage(self);
		});
	},

	doTicketUnspam: function() {
		var self = this;
		var prop = this.changeManager.getPropertyManager('status');
		this.changeManager.setInstantChange(prop, 'awaiting_agent', function() {
			DeskPRO_Window.removePage(self);

			// Reload the ticket page
			DeskPRO_Window.loadPage(BASE_URL + 'agent/tickets/' + self.getMetaData('ticket_id'), {ignoreExist:true});
		});
	},

	_initMessageActionsMenu: function() {
		var self = this;
		var menuElement = $('.ticket-message-edit-menu', this.wrapper);
		this.messageActionsMenu = new DeskPRO.UI.Menu({
			triggerElement: null,
			menuElement: menuElement,
			onBeforeMenuOpened: function(info) {
				var message = $(info.menu.getOpenTriggerElement()).closest('article.message');
				if (message.hasClass('note-message')) {
					menuElement.find('li.set-as-message').show();
					menuElement.find('li.set-as-note').hide();
				} else {
					menuElement.find('li.set-as-message').hide();
					menuElement.find('li.set-as-note').show();
				}

				if (message.hasClass('with-attach')) {
					menuElement.find('li.delete-attachments-link').show();
				} else {
					menuElement.find('li.delete-attachments-link').hide();
				}
			},
			onItemClicked: function(info) {
				var itemEl = $(info.itemEl);
				var triggerEl = $(info.menu.getOpenTriggerElement());
				if (!triggerEl.hasClass('ticket-message-edit-btn')) {
					triggerEl = triggerEl.closest('.ticket-message-edit-btn');
				}
				self._doMessageAction(itemEl.data('option-id'), triggerEl.data('message-id'), itemEl);
			}
		});
		this.ownObject(this.messageActionsMenu);

		// We're using a live event because new messages are always
		// added. So we take care of opening the menu manually.
		var menu = this.messageActionsMenu;
		var wrap = $('.messages-wrap', this.wrapper)[0];
		$('.ticket-message-edit-btn', wrap).live('mousedown', function(event) {
			var textarea = self.getReplyTextArea();
			if (textarea.data('redactor')) {
				// save the selection - but don't focus the editor as that can break this
				$.proxy(function() {
					this.savedSel = this.getOrigin();
					this.savedSelObj = this.getFocus();
				}, textarea.data('redactor'))();
			}
		});
		$('.ticket-message-edit-btn', wrap).live('click', function(event) {
			menu.openMenu(event);
		});
	},

	_doMessageAction: function(optionId, messageId, itemEl) {
		if (!messageId) {
			return;
		}

		switch (optionId) {
			case 'quote':
				var quote = $('textarea.message-quote-' + messageId, this.wrapper).val();
				if (!quote) {
					quote = '';
				}

				var textarea = this.getReplyTextArea();
				if (textarea.data('redactor')) {
					textarea.data('redactor').restoreSelection();
				}

				this.insertTextInReply(quote.trim() + "\n");

				// Scroll down
				if (!this.meta.ticket_reverse_order) {
					this.wrapper.find('div.layout-content').trigger('goscrollbottom');
				}

				this.focusOnReply();

				break;

			case 'delete':
				this.showDeleteMessageOverlay(messageId);
				break;

			case 'delete-attachments':
				var self = this;
				var overlay = new DeskPRO.UI.Overlay({
					contentMethod: 'ajax',
					contentAjax: { url: BASE_URL + 'agent/tickets/messages/' + messageId + '/attachments' },
					zIndex: 40000, // Above floating people windows
					onAjaxDone: function() {
						var wrapper = overlay.getWrapper();

						wrapper.on('click', '.delete-trigger', function(e) {
							e.preventDefault();

							var $this = $(this), attachmentId = $this.data('attachment-id');
							var container = $this.closest('.overlay-content');
							var row = $this.closest('.attachment-row');

							if (!confirm(container.data('confirm'))) {
								return;
							}

							row.addClass('loading');

							$.ajax({
								url: BASE_URL + 'agent/tickets/messages/' + messageId + '/attachments/' + attachmentId + '/delete',
								type: 'POST',
								dataType: 'json'
							}).always(function() {
								row.removeClass('loading');
							}).done(function(data) {
								if (data.message_html) {
									self.wrapper.find('article.message-' + messageId).replaceWith(data.message_html);
								}

								row.remove();
								if (!container.find('.attachment-row').length) {
									overlay.close();
								}
							});
						});
					}
				});
				overlay.open();

				break;

			case 'setnote.note':
			case 'setnote.message':

				var is_note = optionId == 'setnote.note' ? '1' : '0';
				var row = this.wrapper.find('article.message-' + messageId);
				row.addClass('gear-loading');

				$.ajax({
					url: BASE_URL + 'agent/tickets/messages/'+messageId+'/set-message-note.json',
					data: {
						is_note: is_note
					},
					complete: function() {
						row.removeClass('gear-loading');
					},
					success: function(info) {
						if (info.is_note) {
							row.addClass('note-message');
						} else {
							row.removeClass('note-message');
						}
					}
				});

				break;

			case 'split':
				this.showSplitOverlay(messageId);
				break;

			case 'linked_ticket':
				DeskPRO_Window.newTicketLoader.newLinkedTicket(this.meta.ticket_id, messageId);
				break;

			case 'fwd':
				this.showFwdOverlay(messageId);
				break;

			case 'edit':
				this.showMessageEditor(messageId);
				break;

			case 'window':
				var url    = itemEl.data('url');
				url = url.replace(/00000/g, this.meta.ticket_id);
				url = url.replace(/11111/g, messageId);
				var width  = 780;
				var height = 600;

				window.open(url, 'msgwin', "status=0,toolbar=0,location=0,menubar=0,directories=0,resizable=1,scrollbars=1,height="+height+",width="+width);
				break;

			case 'debug':
				var url = itemEl.data('url');
				url = url.replace(/00000/g, this.meta.ticket_id);
				var width  = 200;
				var height = 200;

				window.open(url, 'debugwin', "status=0,toolbar=0,location=0,menubar=0,directories=0,resizable=1,scrollbars=1,height="+height+",width="+width);
				break;
		}
	},

	_initDeleteMessageOverlay: function() {
		if (this.deleteMessageOverlay) return;

		this.deleteMessageOverlayEl = $('.delete-message-overlay:first', this.wrapper);
		this.deleteMessageOverlay = new DeskPRO.UI.Overlay({
			contentElement: this.deleteMessageOverlayEl
		});
		this.ownObject(this.deleteMessageOverlay);

		$('.save-trigger', this.deleteMessageOverlayEl).on('click', (function() {
			this.doTicketMessageDelete();
		}).bind(this));
	},

	showDeleteMessageOverlay: function(messageId) {
		this._initDeleteMessageOverlay();
		$('.message-id', this.deleteMessageOverlayEl).val(messageId);

		this.deleteMessageOverlayEl.find('.ticket-messages').empty().html(
			this.wrapper.find('article.message-' + messageId).clone()
		);
		this.deleteMessageOverlayEl.find('.ticket-messages .edit-gear').remove();
		this.deleteMessageOverlay.openOverlay();
	},

	doTicketMessageDelete: function() {
		$('.loading-off', this.deleteMessageOverlayEl).hide();
		$('.loading-on', this.deleteMessageOverlayEl).show();

		var messageId = $('.message-id', this.deleteMessageOverlayEl).val();
		var self = this;

		$.ajax({
			url: BASE_URL + 'agent/tickets/messages/' + messageId + '/delete',
			type: 'POST',
			dataType: 'json',
			success: function(data) {
				self.deleteMessageOverlay.closeOverlay();

				if (data.ticket_deleted) {
					self.getEl('remove_menu_trigger').hide();

					if (data.hidden_html) {
						var html = $(data.hidden_html);
						self.getEl('page_header').before(html);
						html.closest('.with-scrollbar').trigger('goscrolltop');
					} else {
						DeskPRO_Window.removePage(self);
						DeskPRO_Window.loadPage(BASE_URL + 'agent/tickets/' + self.getMetaData('ticket_id'), {ignoreExist:true});
					}
				} else {
					self.wrapper.find('article.message-' + messageId).remove();
				}
			}
		}).always(function() {
			$('.loading-off', this.deleteMessageOverlayEl).show();
			$('.loading-on', this.deleteMessageOverlayEl).hide();
		})
	},

	showSplitOverlay: function(messageId) {
		var self = this;
		var overlay = new DeskPRO.UI.Overlay({
			contentMethod: 'ajax',
			contentAjax: { url: BASE_URL + 'agent/tickets/' + this.meta.ticket_id + '/split/' + messageId },
			zIndex: 40000, // Above floating people windows
			onAjaxDone: function() {
				var wrapper = overlay.getWrapper(),
					form = wrapper.find('form');

				wrapper.on('click', '.body-text', function() {
					var $this = $(this);
					if ($this.find('.fade-bar').is(':visible')) {
						$this.find('.fade-bar').hide();
						$this.find('.body-text-message').css('max-height', '');
					} else {
						$this.find('.fade-bar').show();
						$this.find('.body-text-message').css('max-height', '70px');
					}
				});

				wrapper.on('change', '.message-id-checkbox', function() {
					var $this = $(this), container = $this.closest('.content-message');
					if ($this.is(':checked')) {
						container.removeClass('message-unselected');
					} else {
						container.addClass('message-unselected');
					}
				});
				wrapper.on('click', '.content header', function(e) {
					if ($(e.target).is('.message-id-checkbox')) {
						return;
					}

					var cb = $(this).find('.message-id-checkbox');
					cb.attr('checked', !cb.attr('checked'));
					cb.trigger('change');
				});

				form.on('submit', function(e) {
					e.preventDefault();

					form.addClass('loading');

					$.ajax({
						url: form.attr('action'),
						type: 'POST',
						data: form.serializeArray(),
						dataType: 'json'
					}).always(function() {
						form.removeClass('loading');
					}).done(function(data) {
						overlay.close();

						if (data.ticket_id) {
							DeskPRO_Window.removePage(self);
							if (!data.old_ticket_deleted) {
								DeskPRO_Window.loadPage(BASE_URL + 'agent/tickets/' + self.getMetaData('ticket_id'), {ignoreExist:true});
							}

							DeskPRO_Window.runPageRoute('ticket:' + BASE_URL + 'agent/tickets/' + data.ticket_id);
						}
					});
				});
			}
		});
		overlay.open();
	},

	showFwdOverlay: function(messageId) {
		var self = this;
		var overlay = new DeskPRO.UI.Overlay({
			contentMethod: 'ajax',
			contentAjax: { url: BASE_URL + 'agent/tickets/' + this.meta.ticket_id + '/forward/' + messageId },
			zIndex: 40000, // Above floating people windows
			destroyOnClose: true,
			onAjaxDone: function() {
				var wrapper = overlay.getWrapper(),
					form = wrapper.find('form'),
					sendBtn = wrapper.find('.save-trigger'),
					footer = wrapper.find('.overlay-footer');

				form.on('submit', function(ev) {
					ev.preventDefault();
					ev.stopPropagation();
				});

				sendBtn.on('click', function(ev) {
					ev.preventDefault();
					ev.stopPropagation();

					var formData = form.serializeArray();
					footer.addClass('loading');

					$.ajax({
						url: form.attr('action'),
						type: 'POST',
						data: formData,
						dataType: 'json',
						success: function(data) {
							if (data.error && data.error == 'invalid_to') {
								DeskPRO_Window.showAlert('Please enter a valid To address');
								footer.removeClass('loading');
							} else {
								DeskPRO_Window.showAlert('Your message has been sent.');
								overlay.close();
							}
						}
					})
				});
			}
		});
		overlay.open();
	},

	showMessageEditor: function(message_id) {
		var self = this;
		this.currentOpenMessageId = message_id;
		if (!this.messageEditOverlay) {
			var overlayEl = this.getEl('message_edit_overlay');
			overlayEl.find('.save-text-trigger').on('click', function(ev) {
				ev.preventDefault();

				$(this).hide();
				overlayEl.find('.save-text-loading').show();

				if (overlayEl.find('textarea.message_text').data('redactor')) {
					overlayEl.find('textarea.message_text').data('redactor').syncCode();
				}

				var postData = {
					message_html: overlayEl.find('textarea.message_text').val()
				};

				$.ajax({
					url: BASE_URL + 'agent/tickets/messages/'+self.currentOpenMessageId+'/save-message-text.json',
					type: 'POST',
					data: postData,
					dataType: 'json',
					complete: function() {
						overlayEl.find('.save-text-loading').hide();
						overlayEl.find('.save-text-trigger').show();
					},
					success: function(info) {
						self.messageEditOverlay.close();
						var messageHtml = info.message_html;
						self.wrapper.find('article.message-' + self.currentOpenMessageId).find('.body-text-message').html(messageHtml);
					}
				});
			});

			this.messageEditOverlay = new DeskPRO.UI.Overlay({
				contentElement: this.getEl('message_edit_overlay'),
				fullScreen: true,
				fullScreenMargin: 55,
				onOverlayOpened: function() {
					overlayEl.find('input.message_id').val(self.currentOpenMessageId);

					if (!self.messageEditOverlay.hasInitRte) {
						self.messageEditOverlay.hasInitRte = true;
						overlayEl.find('textarea.message_text').height(overlayEl.find('.overlay-content').height() - 50);
						//DP.rteTextarea(overlayEl.find('textarea.message_text'), {});
						DeskPRO_Window.initRteAgentReply(overlayEl.find('textarea.message_text'), {
							autoresize: false
						});
					}

					//overlayEl.find('textarea.message_text').html('Loading...');
					overlayEl.find('textarea.message_text').setCode('Loading...');
					$.ajax({
						url: BASE_URL + 'agent/tickets/messages/'+self.currentOpenMessageId+'/get-message-text.json',
						dataType: 'json',
						success: function(data) {
							//overlayEl.find('textarea.message_text').html(data.message_html);
							overlayEl.find('textarea.message_text').setCode(data.message_html);
						}
					});
				}
			});
		}

		this.messageEditOverlay.open();
	},

	getReplyTextArea: function() {
		return this.getEl('replybox_wrap').find('textarea[name="message"]');
	},

	insertTextInReply: function(text) {
		var txt = this.getReplyTextArea();

		if (txt.data('redactor')) {

			try {
				txt.data('redactor').restoreSelection();
				txt.data('redactor').setBuffer();
			} catch (e) {}

			var html = DP.convertTextToWysiwygHtml(text, true);
			html = html.replace(/<\/p>\s*<p>/g, '<br/>');
			html = html.replace(/^<p>/, '');
			html = html.replace(/<\/p>$/, '')
			txt.data('redactor').insertHtml(html);
		} else {
			var pos = txt.getCaretPosition();
			if (!pos) {
				txt.setCaretPosition(0);
			}

			txt.insertAtCaret(text);
			txt.trigger('textareaexpander_fire');
		}
	},

	focusOnReply: function() {
		var txt = this.getReplyTextArea();

		if (txt.data('redactor')) {
			var first = !txt.hasClass('touched');
			txt.setFocus();

			if (first) {
				var cursor = txt.data('redactor').$editor.find('> *').first();
				txt.data('redactor').setSelection(cursor[0], 0, cursor[0], 0);
			}
		} else {
			txt.focus();
		}
	},


	doTicketUpdate: function() {
		if (this.doTicketUpdateRunning) {
			this.doTicketUpdateRunning.abort();
			this.doTicketUpdateRunning = null;
		}

		var formData = [];
		formData.push({
			name: 'last_message_id',
			value: this.getLastMessageId()
		});
		formData.push({
			name: 'last_log_id',
			value: this.getEl('messages_wrap').find('.log-row').last().data('log-id')
		});

		this.doTicketUpdateRunning = $.ajax({
			url: BASE_URL + 'agent/tickets/' + this.getMetaData('ticket_id') + '/update-views.json',
			type: 'POST',
			dataType: 'json',
			data: formData,
			context: this,
			success: function(result) {
				this.alertTab();
				this.handleTicketUpdate(result);
			}
		});
	},

	//#################################################################
	//# Tasks
	//#################################################################

	_initTasks: function() {
		var self = this;
		var openForEl = null;

		var menuVis2  = this.getEl('task_menu_vis').clone().appendTo(this.wrapper);

		var statusMenu = new DeskPRO.UI.Menu({
			menuElement: this.getEl('task_menu_vis'),
			onItemClicked: function(info) {
				$('input.input-vis', openForEl).val($(info.itemEl).data('vis'));
				$('.opt-trigger.visibility label', openForEl).text($(info.itemEl).text());
				sendUpdate(openForEl, 'visibility', $(info.itemEl).data('vis'));
			}
		});

		var sendUpdate = function(rowEl, prop, val, callback) {
			var taskId = rowEl.data('task-id');

			if (!taskId) {
				return;
			}

			var url = BASE_URL + 'agent/tasks/'+taskId+'/ajax-save';

			var postData = [];
			postData.push({
				name: 'action',
				value: prop
			});
			postData.push({
				name: 'value',
				value: val
			});

			$.ajax({
				url: url,
				type: 'POST',
				data: postData,
				dataType: 'json',
				success: callback || function() {}
			});
		};

		var rowContainer = this.getEl('tasks_wrap');

		var openForEl = null;
		rowContainer.on('click', '.remove-row-trigger', function(ev) {
			var row = $(this).closest('.row-item');
			if (confirm($(this).data('confirm'))) {
				row.slideUp();
				$.ajax({
					url: BASE_URL + 'agent/tasks/' + row.data('task-id') + '/delete',
					error: function() {
						row.show();
					},
					success: function() {
						row.remove();
						if (DeskPRO_Window.sections.tasks_section) {
							DeskPRO_Window.sections.tasks_section.refresh();
						}
					}
				});
			}
		});
		rowContainer.on('click', '.opt-trigger.visibility', function(ev) {
			openForEl = $(this).closest('.row-item');
			statusMenu.open.open(ev);
		});
		rowContainer.find('li.assigned_agent select.agents_sel').each(function() {
			$(this).addClass('has-init');
			var row = $(this).closest('.row-item');
			DP.select($(this));

			$(this).on('change', function() {
				var val = $(this).val();
				var label = $(this).find(':selected').text().trim();

				if (!val) {
					val = '';
					label = 'Me';
				}

				row.find('.assigned_agent').find('label').text(label);
				$('input.input-agent', row).val(val);

				sendUpdate(row, 'assigned', val, function() {
					DeskPRO_Window.getMessageBroker().sendMessage('agent.ui.tasks.refresh-task-list');
				});
			});
		});
		rowContainer.on('click', '.opt-trigger.date_due', function(ev) {
			var label = $('label', this);
			var row = $(this).closest('.row-item');
			var field = $('input.input-date-due', row);
			var date = $('input.input-date-due', row).val();
			if (!date) {
				date = new Date();
			}

			field.datepicker('dialog', date, function(date, inst) {
				sendUpdate(row, 'date_due', date);
				$('input.input-date-due', row).val(date);
				label.text(date);
			}, {
				dateFormat: 'yy-mm-dd',
				showButtonPanel: true,
				beforeShow: function(input) {
					setTimeout(function() {
						var buttonPane = $(input).datepicker("widget").find(".ui-datepicker-buttonpane");

						$('button', buttonPane).remove();

						var btn = $('<button class="ui-datepicker-current ui-state-default ui-priority-secondary ui-corner-all" type="button">Clear</button>');
						btn.unbind("click").bind("click", function () { $.datepicker._clearDate( input ); label.text('No due date'); });
						btn.appendTo( buttonPane );

						$(input).datepicker("widget").css('z-index', 30101);
					},1);
				}
			}, ev);
		});

		this.getEl('task_save').on('click', function(ev) {
			ev.preventDefault();

			if ($(this).hasClass('saving')) {
				return;
			}

			var title = $.trim(self.getEl('newtask_title').val());
			if (!title) {
				alert('Please enter a description');
				return;
			}

			$(this).addClass('saving').html('<em>Saving</em>');
			var postData = self.getEl('task_row').find('input').serializeArray();
			postData.push({
				name: 'from_ticket',
				value: 1
			});

			$.ajax({
				url: BASE_URL + 'agent/tasks/save',
				data: postData,
				type: 'POST',
				dataType: 'json',
				complete: function() {
					self.getEl('task_save').removeClass('saving').text('Add');
				},
				success: function(data) {

					updateTaskPane();
					self.getEl('newtask_title').val('');

					if (!data.tasks || !data.tasks[0]) {
						return;
					}

					data = data.tasks[0];
					var row = $(data.row_html);

					row.find('li.assigned_agent select.agents_sel').each(function() {
						$(this).addClass('has-init');
						var row = $(this).closest('.row-item');
						DP.select($(this));

						$(this).on('change', function() {
							var val = $(this).val();
							var label = $(this).find(':selected').text().trim();

							if (!val) {
								val = '';
								label = 'Me';
							}

							row.find('.assigned_agent').find('label').text(label);
							$('input.input-agent', row).val(val);
						});
					});

					self.getEl('task_list').show().prepend(row);

					DeskPRO_Window.util.modCountEl(self.getEl('task_count'), '+', 1);

					if (DeskPRO_Window.sections.tasks_section) {
						DeskPRO_Window.sections.tasks_section.refresh();
					}
				}
			});
		});

		var control = new DeskPRO.Agent.PageHelper.TaskListControl(this.wrapper, {
			menuVis:  menuVis2,
			completeCountEl: null
		});

		control.addEvent('updateUi', function() {
			self.updateUi();
			updateTaskPane();
		});
		control.addEvent('updateCount', function() {
			updateTaskPane();
		});

		var updateTaskPane = function() {
			if (DeskPRO_Window.sections.tasks_section) {
				DeskPRO_Window.sections.tasks_section.markUnloadPage();
			}
		};
	},

	//#################################################################
	//# Slas
	//#################################################################

	_initSlas: function() {
		var self = this;
		var form = this.getEl('sla_form');
		var idSelect = form.find('select[name=sla_id]');
		var rows = this.getEl('sla_rows');
		var tabHeader = this.getEl('sla_wrap_tab');

		var addSlaRow = function(html) {
			var add = $(html);

			rows.append(add);
			add.find('.timeago').timeago();
			rows.closest('table').show();

			tabHeader.append(
				$('<span />')
					.addClass('sla-pip')
					.addClass(add.data('sla-status'))
					.data('sla-id', add.data('sla-id'))
			);
		};

		var getVisibleOptions = function(options) {
			return options.filter(function() {
				return $(this).css('display') !== 'none';
			});
		};

		var rowRemoved = function(slaId) {
			var table = rows.closest('table');

			if (!table.find('tbody tr').length) {
				table.hide();
			}

			if (idSelect.length) {
				idSelect.find('option[value="' + slaId + '"]').show();
				if (getVisibleOptions(idSelect.find('option')).length > 1) {
					form.show();
				}
			}

			tabHeader.find('.sla-pip').each(function() {
				var $this = $(this);
				if ($this.data('sla-id') == slaId) {
					$this.remove();
					return false;
				}
			});
		};

		rows.on('click', 'a.sla-delete', function(e) {
			var $this = $(this);

			e.preventDefault();

			if (confirm(rows.data('delete-confirm'))) {
				DeskPRO_Window.util.ajaxWithClientMessages({
					url: $this.attr('href'),
					type: 'POST',
					dataType: 'json'
				}).done(function (json) {
					if (json.success) {
						var slaId = $this.closest('tr').data('sla-id');
						var table = $this.closest('table');

						$this.closest('tr').remove();

						rowRemoved(slaId);
					}
				});
			}
		});

		if (form.length) {
			if (getVisibleOptions(idSelect.find('option')).length <= 1) {
				// only the empty option
				form.hide();
			}

			var progress = this.getEl('sla_save_progress');

			DP.select(idSelect, {
				// todo: try to get it to hide hidden select elements
			});

			form.on('click', 'button', function() {
				var val = idSelect.val();
				if (val.length && val != '0') {
					progress.show();

					DeskPRO_Window.util.ajaxWithClientMessages({
						url: form.data('submit-url'),
						data: form.find('input, textarea, select').serializeArray(),
						type: 'POST',
						dataType: 'json'
					}).done(function(json) {
						if (json.inserted) {
							addSlaRow(json.html);

							idSelect.find('option[value="' + val + '"]').hide();
							if (getVisibleOptions(idSelect.find('option')).length <= 1) {
								// only the empty option
								form.hide();
							} else {
								idSelect.val('0');
							}
						}
					}).always(function() {
						progress.hide();
					});
				}
			});

			// manage sla updates to the ticket
			DeskPRO_Window.getMessageBroker().addMessageListener('agent.ticket-sla-updated', function(info) {
				if (info.ticket_id == self.getMetaData('ticket_id')) {
					rows.find('tr').each(function() {
						var row = $(this);
						if (row.data('sla-id') == info.sla_id) {
							if (info.removed) {
								row.remove();
								rowRemoved(info.sla_id);
							} else {
								row.find('.sla-status-icon').removeClass(info.original_status).addClass(info.sla_status);
								row.data('sla-status', info.sla_status);

								row.find('.warn-date').html(
									info.warn_date
										? $('<time class="timeago" datetime="' + info.warn_date + '"></time>').timeago()
										: 'N/A'
								);
								row.find('.fail-date').html(
									info.fail_date
										? $('<time class="timeago" datetime="' + info.fail_date + '"></time>').timeago()
										: 'N/A'
								);

								if (info.is_completed) {
									row.find('.delete').addClass('completed').removeClass('delete');
								} else {
									row.find('.completed').addClass('delete').removeClass('completed');
								}

								tabHeader.find('.sla-pip').each(function() {
									var pip = $(this);
									if (pip.data('sla-id') == info.sla_id) {
										pip.removeClass(info.original_status).addClass(info.sla_status);
										return false;
									}
								});
							}

							return false;
						}
					});
				}
			}, this.pageUid);
		}
	},

	//#################################################################
	//# Edit name
	//#################################################################

	_initEditName: function() {
		var self = this;
		var namef       = this.getEl('showname');
		var editName    = this.getEl('editname');
		var startBtn    = this.getEl('editname_start');
		var stopBtn     = this.getEl('editname_end');

		var startEditable = function() {
			namef.hide();
			editName.show();
			startBtn.hide();
			stopBtn.show();
		};

		var stopEditable = function() {
			var nametxt = editName.find('input').first();

			var setName = nametxt.val().trim();
			if(!setName) {
				return;
			}

			editName.hide();
			startBtn.show();
			namef.show();
			stopBtn.hide();
			namef.text(setName);

			var postData = [];
			postData.push({
				name: 'subject',
				value: setName
			});

			$.ajax({
				url: BASE_URL + 'agent/tickets/'+self.meta.ticket_id+'/ajax-save-subject.json',
				type: 'POST',
				data: postData
			});
		};

		namef.on('dblclick', startEditable).on('keypress', function(ev) {
			if (ev.keyCode == 13 /* enter key */) {
				ev.preventDefault();
				stopEditable();
			}
		});
		this.getEl('editname_start').on('click', startEditable);
		this.getEl('editname_end').on('click', stopEditable);
	},

	shortcutOpenSnippets: function() {
		if (!this.ticketReplyBox) {
			return;
		}

		if (!this.meta.ticket_reverse_order) {
			this.wrapper.find('div.layout-content').trigger('goscrollbottom');
		}
		this.focusOnReply();
		this.ticketReplyBox.snippetsViewer.open();
	},

	shortcutSendReply: function() {
		if (!this.ticketReplyBox) {
			return;
		}

		if (!this.meta.ticket_reverse_order) {
			this.wrapper.find('div.layout-content').trigger('goscrollbottom');
		}
		this.ticketReplyBox.el.find('.submit-trigger').click();
	},

	shortcutReplySetAwaitingUser: function() {
		if (!this.ticketReplyBox) {
			return;
		}

		this.ticketReplyBox.setReplyAsOptionName('awaiting_user');
		if (!this.meta.ticket_reverse_order) {
			this.wrapper.find('div.layout-content').trigger('goscrollbottom');
		}
		this.focusOnReply();
	},

	shortcutReplySetAwaitingAgent: function() {
		if (!this.ticketReplyBox) {
			return;
		}

		this.ticketReplyBox.setReplyAsOptionName('awaiting_agent');
		if (!this.meta.ticket_reverse_order) {
			this.wrapper.find('div.layout-content').trigger('goscrollbottom');
		}
		this.focusOnReply();
	},

	shortcutReplySetResolved: function() {
		if (!this.ticketReplyBox) {
			return;
		}

		this.ticketReplyBox.setReplyAsOptionName('resolved');
		if (!this.meta.ticket_reverse_order) {
			this.wrapper.find('div.layout-content').trigger('goscrollbottom');
		}
		this.focusOnReply();
	},

	shortcutReplyOpenProperties: function() {
		if (!this.ticketReplyBox) {
			return;
		}

		if (!this.meta.ticket_reverse_order) {
			this.wrapper.find('div.layout-content').trigger('goscrollbottom');
		}
		this.ticketReplyBox.openStatusMenu();
	}
});
