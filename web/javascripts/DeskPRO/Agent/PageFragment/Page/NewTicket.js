Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');

DeskPRO.Agent.PageFragment.Page.NewTicket = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'newticket';
		this.allowDupe = true;
	},

	initPage: function(el) {
		var self = this;
		this.wrapper = el;
		this.el = el;
		this.contentWrapper = this.wrapper.children('.layout-content').attr('id', Orb.getUniqueId());
		this.parent(el);

		el.find('select').addClass('with-select2');

		this.form = $('form', this.wrapper).on('submit', function(ev) {
			ev.preventDefault();
		});

		this._initUserSection();
		this._initMessageSection();
		this._initOtherSection();
		this._initCcSelection();

		this.meta.person_api_data = {};

		this.addEvent('activate', function() {
			window.setTimeout(function() {
				if (!self.getEl('user_searchbox').find('input.person-id').val()) {
					self.getEl('userselect').focus();
				}
			}, 60);
		});

		if (this.getEl('headerbox_box_billing').length) {
			var billing = new DeskPRO.Agent.PageHelper.TicketBilling(this.getEl('headerbox_box_billing'), this.meta.baseId, {
				auto_start_bill: this.meta.auto_start_bill
			});
			this.addEvent('activate', function() {
				if (this.meta.auto_start_bill) {
					billing.startBillingTimer(true);
				}
			});
			this.addEvent('deactivate', function() {
				billing.stopBillingTimer(true);
			});
		}

		$('.submit-trigger', this.wrapper).on('click', this.submit.bind(this));

		//------------------------------
		// Upload handling
		//------------------------------

		DeskPRO_Window.util.fileupload(this.wrapper, {
			dropZone: $('.option-rows', this.wrapper),
			uploadTemplate: $('.template-upload', this.wrapper),
			downloadTemplate: $('.template-download', this.wrapper)
		});
		this.wrapper.bind('fileuploaddone', function() {
			self.getEl('attach_row').slideDown().removeClass('is-hidden');
		});
		this.wrapper.bind('fileuploadstart', function() {
			self.getEl('attach_row').slideDown().removeClass('is-hidden');
		});

		this.wrapper.on('click', '.remove-attach-trigger', function() {

			var row = $(this).closest('li');
			row.fadeOut('fast', function() {
				row.remove();

				var rows = $('ul.files li', self.getEl('attach_row'));
				if (!rows.length) {
					self.getEl('attach_row').slideUp().addClass('is-hidden');
				}
			});
        });

		$('.Date.customfield input', this.wrapper).datepicker({
			dateFormat: 'yy-mm-dd',
			showButtonPanel: true,
			beforeShow: function(input) {
				setTimeout(function() {
					var buttonPane = $(input).datepicker("widget").find(".ui-datepicker-buttonpane");

					buttonPane.find('button:first').remove();

					var btn = $('<button class="ui-datepicker-current ui-state-default ui-priority-secondary ui-corner-all" type="button">Clear</button>');
					btn.unbind("click").bind("click", function () { $.datepicker._clearDate( input ); });
					btn.appendTo( buttonPane );

					$(input).datepicker("widget").css('z-index', 30002);
				},1);
			}
		});

		this.wrapper.find('.pending-info').on('click', '.reset', function(ev) {
			ev.preventDefault();
			self._resetForX();
		});

		this.addEvent('deactivate', function() {
			this._resetForX();
		}, this);

		var messageEl = this.getEl('message');
		var subjectEl = this.getEl('subject');
		var appliedMsgTpl = null;
		var sig = $.trim(self.getEl('signature_value').val());

		messageEl.on('keydown', function() {
			messageEl.addClass('editted');
		});
		subjectEl.on('keydown', function() {
			subjectEl.addClass('editted');
		});
		this.getEl('message_template').on('change', function() {
			var id = $(this).val();

			if (appliedMsgTpl == id) {
				return;
			}

			if (!id) {
				if (!messageEl.hasClass('editted')) {
					self.setMessageText('');
				}
				if (!subjectEl.hasClass('editted')) {
					subjectEl.val('');
				}
				return;
			}

			appliedMsgTpl = id;

			$.ajax({
				url: BASE_URL + 'agent/tickets/get-message-template/'+id+'.json',
				type: 'GET',
				cache: false,
				dataType: 'json',
				success: function(data) {
					if (messageEl.hasClass('editted')) {
						var msgCmp = data.message.replace(/(\r\n|\n|\r)/gm, " ");
						var valCmp = messageEl.val().replace(/(\r\n|\n|\r)/gm, " ");
						if (valCmp.indexOf(msgCmp) === -1) {
							self.insertMessageText(data.message);
						}
					} else {
						var val = data.message;

						if (sig) {
							val += "\n\n";
							val += sig;
						}

						self.setMessageText(val);
					}

					if (subjectEl.hasClass('editted')) {
						if (subjectEl.val().indexOf(data.subject) === -1) {
							subjectEl.insertAtCaret(data.subject);
						}
					} else {
						subjectEl.val(data.subject);
					}
				}
			});
		});

		// This is so the select2 box has proper width for the longest template title
		var w = this.getEl('message_template').width() + 55;
		if (w > 350) w = 350;
		this.getEl('message_template').css('width', w);
		this.getEl('message_template_holder').css({
			visibility: 'visible',
			display: 'none'
		});

		window.setTimeout(function() {
			if (self.OBJ_DESTROYED) return;

			self.wrapper.find('select').each(function() {
				if ($(this).prop('multiple')) {
					$(this).width(300);
				}

				DP.select($(this));
			});
			self.updateUi();
		}, 50);

		self.wrapper.find('select').each(function() {
			var len = 0;
			$(this).find('option').each(function() {
				var ol = $(this).text().length;
				if (ol > len) len = ol;
			});
			$(this).width((10 * len) + 25);
		});

		var depSel = this.getEl('dep');

		var ticketReader = {
			getCategoryId: function() {
				var catId = self.getEl('cat').val();
				return parseInt(catId) || 0;
			},
			getPriorityVal: function() {
				var id = this.getPriorityId();
				if (!id) {
					return -999999999;
				}

				if (!window.DESKPRO_TICKET_PRI_MAP || !window.DESKPRO_TICKET_PRI_MAP[id]) {
					return 0;
				}

				return parseInt(window.DESKPRO_TICKET_PRI_MAP[id]);
			},
			getPriorityId: function() {
				var catId = self.getEl('pri').val();
				return parseInt(catId) || 0;
			},
			getProductId: function() {
				var catId = self.getEl('prod').val();
				return parseInt(catId) || 0;
			},
			getOrganizationId: function() {
				return 0;
			},
			getWorkflow: function() {
				var catId = self.getEl('work').val();
				return parseInt(catId) || 0;
			}
		};

		var tplHolder = this.getEl('message_template_holder');
		var tplSel = this.getEl('message_template');
		var tplSelOrig = this.getEl('message_template_orig');

		var fieldDisplayFetch = new DeskPRO.Agent.PageHelper.TicketFieldDisplay(ticketReader, 'create');
		function updateFields() {
			$('.ticket-field', self.getEl('fields_container')).removeClass('item-on').hide();
			var fieldDisplay = fieldDisplayFetch.getFields(depSel.val());

			Object.each(fieldDisplay, function(fields, section) {
				Array.each(fields, function(f) {
					if (f.field_type == 'ticket_field') {
						var classname = 'ticket-field-' + f.field_id;
					} else {
						var classname = f.field_type;
					}

					$('.ticket-field.' + classname, self.wrapper).not('.error-message').detach().appendTo(self.getEl('fields_container')).show().addClass('item-on');
				});
			});

			var depId = depSel.val();
			var opts = tplSelOrig.find('option.department_' + depId +', option.department_0').clone();
			if (opts[0]) {

				var selected = tplSel.val();

				tplSel.empty();
				tplSel.append('<option value="0" selected="selected">Blank</option>');
				tplSel.append(opts);
				tplHolder.show();
				self.getEl('message_template_holder_row').show();

				var selectedOpt = tplSel.find('[value="'+selected+'"]');

				if (selectedOpt[0]) {
					tplSel.select2('val', selected);
				} else {
					tplSel.select2('val', 0);
					tplSel.change();
				}
			} else {
				tplSel.empty();
				tplHolder.hide();
				self.getEl('message_template_holder_row').hide();
			}

			self.getEl('fields_container').find('tbody').removeClass('last').filter(':visible').last().addClass('last');

			self.updateUi();
		};

		depSel.on('change', function(ev) {
			updateFields();
		});

		$('.ticket-field select', this.wrapper).on('change', function() {
			updateFields();
		});

		updateFields();

		//------------------------------
		// Status menu
		//------------------------------

		var statusMenuTrigger = this.el.find('.status-menu-trigger');
		var footerEl = this.getEl('message_footer');
		var statusMenu = this.getEl('status_menu');
		statusMenu.css('z-index', 999999);
		var statusMenuH = null;
		var statusBackdrop = null;
		var statusMacroFilter = null;
		var statusMacroList = statusMenu.find('.macro-list');
		var statusListItems = null;
		var replyAsType = this.getEl('reply_as_type');

		var closeStatusMenu = function() {
			statusBackdrop.hide();
			statusMenu.hide();
		};

		var updateStatusPos = function() {
			statusMenuH = statusMenu.height();
			if (statusMenu > 500) {
				statusMenu.find('macro-list').css('max-height', 500).css('overflow', 'auto');
				statusMenuH = 500;
			}

			var pos = footerEl.offset();
			statusMenu.css({
				left: pos.left + 6,
				top: pos.top - statusMenuH + 3
			});
		};

		var openStatusMenu = function() {
			statusListItems = statusMenu.find('li[data-type]').not('.off');

			// Means we're opening fo rhte first time
			if (!statusBackdrop) {
				statusBackdrop = $('<div class="backdrop"></div>');
				statusBackdrop.css('z-index', 999998);
				statusBackdrop.appendTo('body');
				statusBackdrop.on('click', function(ev) {
					ev.stopPropagation();
					closeStatusMenu();
				});
				statusMenu.detach().appendTo('body');

				// Handle macro filtering
				statusMacroFilter = statusMenu.find('.macro-filter');

				statusMenu.on('click', 'li[data-type]', function(ev) {
					ev.stopPropagation();
					self.setReplyAsOption($(this));
					closeStatusMenu();
				});

				statusMacroFilter.on('keyup', function(ev) {

					var isCtrl = false;
					if (ev.ctrlKey && DeskPRO_Window.keyboardShortcuts.isMac) {
						isCtrl = true;
					} else if (ev.altKey) {
						isCtrl = true;
					}
					if (isCtrl) {
						if (isCtrl && (ev.which == 85)) {
							closeStatusMenu();
							self.setReplyAsOptionName('awaiting_user');
							return;
						}
						if (isCtrl && (ev.which == 65)) {
							closeStatusMenu();
							self.setReplyAsOptionName('awaiting_agent');
							return;
						}
						if (isCtrl && (ev.which == 68)) {
							closeStatusMenu();
							self.setReplyAsOptionName('resolved');
							return;
						}
					}

					if (ev.keyCode == 13 /* enter key */) {
						ev.preventDefault();
						var current = statusListItems.filter('.cursor');
						if (current[0]) {
							self.setReplyAsOption(current);
							closeStatusMenu();
						}
					} else if (ev.keyCode == 27 /* escape key */) {
						ev.preventDefault();
						closeStatusMenu();
					} else if (ev.keyCode == 40 /* down key */ || ev.keyCode == 38 /* up key */) {
						ev.preventDefault();
						var dir = ev.keyCode == 40 ? 'down' : 'up';

						var current = statusListItems.filter('.cursor');
						if (!current.length) {
							if (dir == 'down') {
								statusListItems.first().addClass('cursor');
							} else {
								statusListItems.last().addClass('cursor');
							}
						} else {
							var nextIndex = statusListItems.index(current);
							if (dir == 'down') {
								nextIndex++;
							} else {
								nextIndex--;
							}

							if (nextIndex < 0) {
								nextIndex = statusListItems.length-1;
							} else if (nextIndex > (statusListItems.length-1)) {
								nextIndex = 0;
							}

							current.removeClass('cursor');
							statusListItems.eq(nextIndex).addClass('cursor');
						}
					}
				});

				statusMacroFilter.on('keyup', function() {
					var val = $.trim($(this).val());

					if (!val) {
						statusMacroList.find('li').show().removeClass('off');
						updateStatusPos();
					} else {
						val = val.toLowerCase();
						statusMacroList.find('li').each(function() {
							if ($(this).text().toLowerCase().indexOf(val) !== -1) {
								$(this).show().removeClass('off');
							} else {
								$(this).hide().addClass('off');
							}
						});
						updateStatusPos();
					}

					statusListItems = statusMenu.find('li[data-type]').not('.off');
					if (!statusListItems.filter('.cursor')[0]) {
						statusMenu.find('li.cursor').removeClass('cursor');
						statusListItems.first().addClass('cursor');
					}
				});
			}

			// Pre-select proper value
			var type = replyAsType.data('type');
			statusMenu.find('li').removeClass('cursor')
				.filter('[data-type]').removeClass('on')
				.filter('[data-type="' + type + '"]').addClass('on');

			var w = self.getEl('reply_btn_group').width() - 3;
			if (w < 200) {
				w = 200;
			}
			statusMenu.width(w);

			statusBackdrop.show();
			updateStatusPos();
			statusMenu.show();

			statusMacroFilter.focus();
		};

		this.openStatusMenu = openStatusMenu;

		statusMenuTrigger.on('click', function(ev) {
			ev.preventDefault();
			openStatusMenu();
		});

		$('#settingswin').on('dp_macros_updated', function(ev) {
			Array.each(ev.macroItems, function(info) {
				var has = statusMacroList.find('.res-ticketmacro-' + info.id);
				if (has[0]) {
					return;
				}

				var li = $('<li><div class="on-icon"><i class="icon-okay"></i></div><span class="macro-title"></span></li>');
				li.data('get-macro-url', BASE_URL + 'agent/tickets/0/ajax-get-macro?macro_id=' + info.id + '&macro_reply_context=1');
				li.data('label', 'Send Reply and ' + info.title);
				li.data('type', 'macro:'+info.id);
				li.attr('data-type', 'macro:'+info.id);
				li.find('.macro-title').text(info.title);

				statusMacroList.append(li);
			});
		});

		this.el.find('.expander').on('click', function() {
			var target = self.el.find($(this).data('target'));
			if (target.is(':visible')) {
				$(this).removeClass('expanded').addClass('is-hidden');
				target.slideUp('fast');
			} else {
				$(this).addClass('expanded').removeClass('is-hidden');
				target.slideDown('fast');
			}
		});
	},

	setReplyAsOptionName: function(name) {
		var item = this.getEl('status_menu').find('li[data-type="' + name + '"]').first();
		if (item[0]) {
			this.setReplyAsOption(item);
		}
	},

	setReplyAsOption: function(item) {
		var replyAsType = this.getEl('reply_as_type');

		var html = Orb.escapeHtml(item.data('label'));
		html = html.replace(/^Send Reply/, 'Send <span class="show-key-shortcut">R</span>eply');
		replyAsType.data('type', item.data('type')).html(html);

		var macroUrl = item.data('get-macro-url');

		var textarea = this.textarea;
		var api = this.textarea.data('redactor');

		if (!macroUrl) {
			this.getEl('actions_row').hide();
			this.updateUi();
			this.wrapper.find('div.layout-content').trigger('goscrollbottom');
		} else {
			var actionsRow = this.getEl('actions_row');
			var actionsRowList = actionsRow.find('ul');
			actionsRowList.empty();
			actionsRowList.append('<li class="load"><i class="flat-spinner"></i></li>');

			actionsRow.show();

			this.updateUi();
			this.wrapper.find('div.layout-content').trigger('goscrollbottom');

			$.ajax({
				url: macroUrl,
				type: 'GET',
				context: this,
				dataType: 'json',
				success: function(data) {
					actionsRowList.empty();
					Array.each(data.descriptions, function(desc) {
						var li = $('<li />');
						li.html(desc);

						actionsRowList.append(li);
					});

					// There's a snippet reply point
					var sig = null;
					if (api) {
						sig = api.$editor.find('.dp-signature-start');
						if (!sig[0]) {
							sig = null;
						}
					}

					actionsRowList.find('.with-reply, .with-snippet').each(function() {
						var pos = $(this).data('reply-pos');
						var html = $(this).find('.reply-text').get(0).innerHTML;

						if (pos) {
							if (api) {
								if (pos == 'overwrite') {
									api.$editor.html(html);
									if (sig) {
										api.$editor.append(sig);
									}
								} else if (pos == 'prepend') {
									api.$editor.prepend(html);
								} else {
									if (sig) {
										var usesig = sig;
										var prev = sig.prev();
										if (prev[0] && prev.is('p') && $.trim(prev.text()) === '') {
											usesig = prev;
											var prev2 = prev.prev();
											if (prev2[0] && prev2.is('p') && $.trim(prev2.text()) === '') {
												prev2.remove()
											}
										}
										usesig.before(html);
									} else {
										api.$editor.append(html);
									}
								}

								api.syncCode();
							} else {
								var text = $('<div>' + html + '</div>');
								text = text.text().trim();
								textarea.val($.trim(textarea.val() + "\n\n" + text));
							}
						}
					});

					var agentId = parseInt(actionsRowList.find('.with-agent').data('agent-id'));
					if (agentId) {
						if (agentId == -1) {
							agentId = DESKPRO_PERSON_ID;
						}

						this.getEl('agent_sel').select2('val', agentId);
					}
					var agentTeamId = parseInt(actionsRowList.find('.with-agent-team').data('agent-team-id'));
					if (agentTeamId) {
						if (agentTeamId == -1) {
							if (!window.DESKPRO_TEAM_IDS || !window.DESKPRO_TEAM_IDS.length) {
								agentTeamId = null;
							} else {
								agentTeamId = window.DESKPRO_TEAM_IDS[0];
							}
						}

						if (agentTeamId) {
							this.getEl('agent_team_sel').select2('val', agentTeamId);
						}
					}

					if (actionsRowList.find('.with-close-tab')) {
						this.getEl('close_tab_opt').prop('checked', true);
					}

					var setSubject = actionsRowList.find('.with-set-subject').text().trim();
					if (setSubject) {
						this.getEl('subject').val(setSubject);
					}

					this.updateUi();
					this.wrapper.find('div.layout-content').trigger('goscrollbottom');
				}
			});
		}
	},

	markForReload: function() {
		if (!this.markedForReload) {
			this.markedForReload = true;
			this.addEvent('deactivate', this.closeSelf.bind(this));
		}
	},

	closeSelf: function() {
		var ev = {cancel: false};
		this.fireEvent('closeSelf', ev);

		if (!ev.cancel) {
			this.parent();
		}
	},

	submit: function() {

		if (this.pauseSend) {
			window.setTimeout(this.submit.bind(this), 250);
		}

		this.getEl('action').val(this.getEl('reply_as_type').data('type'));
		var formData = this.form.serializeArray();

		$('div.error.section', this.wrapper).removeClass('error');
		$('.error-message-on', this.wrapper).removeClass('error-message-on').hide();
		this.getEl('error_section').hide();

		this.wrapper.parent().addClass('loading');
		this.getEl('send_btn').hide();
		this.getEl('send_loading').show();

		$.ajax({
			url: BASE_URL + 'agent/tickets/new/save',
			type: 'POST',
			data: formData,
			dataType: 'json',
			context: this,
			complete: function() {
				this.wrapper.parent().removeClass('loading');
				this.getEl('send_btn').show();
				this.getEl('send_loading').hide();
			},
			success: function(data) {
				if (data.error) {
					if (data.is_dupe) {
						DeskPRO_Window.showConfirm('The ticket you tried to submit is an exact duplicate of an existing ticket. This new ticket was not saved.', function() {
							DeskPRO_Window.runPageRoute('ticket:' + BASE_URL + 'agent/tickets/' + data.dupe_ticket_id)
						}, function() {}, 'View Existing Ticket', 'hidden');
					} else {
						Array.each(data.error_codes, function(code) {
							this.showErrorCode(code);
						}, this);

						if (data.error_messages) {
							this.showErrorCode('free');
							var free = $('<div/>');
							Array.each(data.error_messages, function(msg) {
								var x = $('<div/>');
								x.text('- ' + msg);
								free.append(x);
							});
							this.getEl('freemessage').html(free.html());
						}

						this.updateUi();
					}
				}

				if (data.ticket_id) {
					if (data.comment_id) {
						DeskPRO_Window.getMessageBroker().sendMessage('agent-ui.comment-remove', {
							comment_id: data.comment_id,
							comment_type: data.comment_type
						});
					}

					if (data.can_view && this.getEl('opt_open_tab').is(':checked')) {
						DeskPRO_Window.runPageRoute('ticket:' + BASE_URL + 'agent/tickets/' + data.ticket_id);
					}
					this.closeSelf();
				}
			}
		});
	},

	showErrorCode: function(code) {
		$('.' + code + '.error-message', this.wrapper).addClass('error-message-on').show();
		switch (code) {
			case 'person_id':
			case 'person_no_user':
			case 'person_email_address':
				$('div.user-section.section', this.wrapper).addClass('error');
				break;

			case 'subject':
				$('div.subject-section.section', this.wrapper).addClass('error');
				break;

			case 'message':
				$('div.message-section.section', this.wrapper).addClass('error');
				break;
		}
		this.getEl('error_section').show();
		this.updateUi();
	},

	clearErrorCode: function(code) {
		$('.' + code + '.error-message', this.wrapper).removeClass('error-message-on').hide();
		switch (code) {
			case 'person_id':
			case 'person_no_user':
			case 'person_email_address':
				$('div.user-section.section', this.wrapper).removeClass('error');
				break;

			case 'subject':
				$('div.subject-section.section', this.wrapper).removeClass('error');
				break;

			case 'message':
				$('div.message-section.section', this.wrapper).removeClass('error');
				break;
		}

		if (this.getEl('error_section').find('.error-message-on')[0]) {
			this.getEl('error_section').show();
		} else {
			this.getEl('error_section').hide();
		}

		this.updateUi();
	},

	updateUi: function() {
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
			}
		}

		this.fireEvent('updateUi');
	},

	insertMessageText: function(content) {
		var textarea = this.getEl('message');

		if (textarea.data('redactor')) {
			textarea.data('redactor').insertHtml(DP.convertTextToWysiwygHtml(content, true));
		} else {
			var pos = textarea.getCaretPosition();
			if (!pos) {
				textarea.setCaretPosition(0);
			}

			textarea.insertAtCaret(content);
			textarea.trigger('textareaexpander_fire');
		}
	},

	setMessageText: function(content, is_quote) {
		var textarea = this.getEl('message');

		if (is_quote) {
			content = "> " + content.replace(/\r\n|\n/, "\n> ");
		}

		if (textarea.data('redactor')) {
			content = DP.convertTextToWysiwygHtml(content, true);
			if (is_quote) {
				content = "<br/><br/><blockquote>" + content + '</blockquote>';
			}
			textarea.setCode(content);
		} else {
			if (is_quote) {
				content = "\n\n" + content;
			}
			textarea.val(content);
			textarea.trigger('textareaexpander_fire');
		}
	},

	setNewByComment: function(data) {

		this.setMessageText(data.name + " <" + data.email + "> wrote:\n" + data.message, true);
		this.getEl('for_comment_type').val(data.content_type);
		this.getEl('for_comment_id').val(data.comment_id);
		$('.pending-info.comment', this.wrapper).show();

		this.getEl('comment_title').text(data.name + " (" + data.email + ")");
		this.getEl('comment_object_link').data('route', 'page:' + data.object_url).text(data.object_title);

		this.getEl('user_searchbox').find('input.person-id').val(data.person_id);
		this.getEl('usersearch').val(data.email_address);

		this.getEl('user_section').hide();
		this.getEl('choose_user').hide();
		this.getEl('user_searchbox').find('input.person-id').val(data.person_id);

		this.setUser(data.person_id);

		if (data.status == 'validating') {
			$('option[value="approve"]', this.getEl('comment_action')).hide();
		} else {
			$('option[value="approve"]', this.getEl('comment_action')).show();
		}

		this.updateUi();
	},

	setNewByChat: function(data) {
		var self = this;
		this.getEl('for_chat_id').val(data.chat_id);
		this.getEl('chat_title').text(data.chat_title);
		$('.pending-info.chat', this.wrapper).show();

		if (data.person_id) {
			this.setUser(data.person_id, data.session_id);
			this.getEl('user_searchbox').find('input.person-id').val(data.person_id);
			this.getEl('user_section').hide();
			this.getEl('choose_user').hide();
		} else {
			$.ajax({
				type: 'GET',
				url: BASE_URL + 'agent/tickets/new/get-person-row/0',
				data: { 'email': data.email },
				dataType: 'html',
				context: this,
				success: function(html) {
					self.placeUserRow(html);
					self.updateUi();
				}
			});
		}

		this.updateUi();
	},

	setNewByPerson: function(data) {
		this.getEl('user_searchbox').find('input.person-id').val(data.person_id);
		this.getEl('choose_user').hide();
		this.getEl('user_section').show();
		this.getEl('user_choice').show().html('<div style="padding:10px;"><div class="loading-icon-big"></div></div>');

		this.setUser(data.person_id);
		this.updateUi();
	},

	_resetForX: function() {
		this.wrapper.find('.pending-info').hide();
		this.getEl('user_section').show();
		this.getEl('choose_user').show();

		this.getEl('for_chat_id').val('');
		this.getEl('for_comment_type').val('');
		this.getEl('for_comment_id').val('');

		this.updateUi();
	},

	//#########################################################################
	//# User Section
	//#########################################################################

	_initUserSection: function() {
		var self = this;
		var searchbox = this.getEl('user_searchbox');
		var userfields = this.getEl('user_choice');
		var rechooseBtn = this.getEl('switch_user');

		rechooseBtn.on('click', function(ev) {
			ev.preventDefault(); // default would be submitting the ticket form
			showUserChoice();
		});

		var showUserChoice = function() {
			userfields.empty();
			userfields.hide();
			searchbox.show();
			self.getEl('choose_user').show();
			rechooseBtn.hide();
			self.loadSnippetsViewer();
			self.updateUi();
		};

		var placeUserRow = function(html) {
			self.placeUserRow(html);
		};

		searchbox.bind('personsearchboxclick', function(ev, personId, name, email, sb) {
			$.ajax({
				type: 'GET',
				url: BASE_URL + 'agent/tickets/new/get-person-row/' + personId,
				dataType: 'html',
				context: this,
				success: function(html) {
					self.clearErrorCode('person_id');
					self.clearErrorCode('person_email_address');
					self.clearErrorCode('person_no_user');

					$('input.person-id', searchbox).val(personId);
					placeUserRow(html);
					self.loadSnippetsViewer();
					self.updateUi();
				}
			});
			sb.close();
			sb.reset();
		});
		searchbox.bind('personsearchboxclicknew personsearchenter', function(ev, term, sb) {
			$.ajax({
				type: 'GET',
				url: BASE_URL + 'agent/tickets/new/get-person-row/0',
				data: { 'email': term },
				dataType: 'html',
				context: this,
				success: function(html) {
					placeUserRow(html);

					if (term.indexOf('@') !== -1) {
						$('input.email', userfields).val(term);
					} else {
						$('input.name', userfields).val(term);
					}

					var personId = self.getEl('user_choice').find('.set_userid').val();

					if (personId) {
						self.clearErrorCode('person_id');
						self.clearErrorCode('person_email_address');
						self.clearErrorCode('person_no_user');

						$('input.person-id', self.getEl('user_searchbox')).val(personId);
						self.loadSnippetsViewer();
					}

					self.updateUi();
				}
			});
			sb.close();
			sb.reset();
		});
	},

	setUser: function(person_id, session_id) {
		var self = this;
		$.ajax({
			type: 'GET',
			url: BASE_URL + 'agent/tickets/new/get-person-row/0',
			data: { 'person_id': person_id, 'session_id': session_id },
			dataType: 'html',
			context: this,
			success: function(html) {
				this.placeUserRow(html);
			}
		});
	},

	placeUserRow: function(html) {
		var self = this;
		var searchbox = this.getEl('user_searchbox');
		var userfields = this.getEl('user_choice');
		var rechooseBtn = this.getEl('switch_user');

		userfields.empty();
		userfields.html(html);

		self.getEl('choose_user').hide();
		rechooseBtn.show();
		searchbox.hide();
		userfields.show();

		var apiData = userfields.find('.api_data');
		this.meta.person_api_data = {};
		if (apiData[0]) {
			try {
				this.meta.person_api_data = $.parseJSON(apiData.val());
			} catch (e) {}
		}

		var e = $('input.email', userfields);
		if (e && e[0]) {
			var fnCheck = function() {
				if (e.val() && e.val().indexOf('@') !== -1) {
					self.clearErrorCode('person_email_address');
					self.clearErrorCode('person_no_user');
				}
			}
			fnCheck();
			e.on('change', fnCheck);
		}
		var e = $('input.set_person_id', userfields);
		if (e[0]) {
			var person_id = e.val();
			this.getEl('user_searchbox').find('input.person-id').val(person_id);
		}

		this.updateUi();
	},

	//#########################################################################
	//# CC Selection
	//#########################################################################

	_initCcSelection: function() {
		var self = this;
		var ccbox = this.getEl('user_ccbox');

		ccbox.bind('personsearchboxclick', function(ev, personId, name, email, sb) {
			$.ajax({
				type: 'GET',
				url: BASE_URL + 'agent/people/' + personId + '/basic.json',
				dataType: 'json',
				context: this,
				success: function(data) {
					var html = [];
					html.push('<li>');
						html.push('<em class="remove"></em>');
						html.push('<a data-route="page:'+data.url+'">' + data.contact_name + '</a>');
						html.push('<input type="hidden" name="newticket[add_cc_person][]" value="'+personId+'" />');
					html.push('</li>');

					html = html.join('');
					self.getEl('cc_list').append(html);
					self.updateUi();
				}
			});
			sb.close();
			sb.reset();
		});
		ccbox.bind('personsearchboxclicknew personsearchenter', function(ev, term, sb) {

			var rowid = Orb.uuid();

			var html = [];
			html.push('<li>');
				html.push('<em class="remove"></em>');
				html.push('<input type="text" class="name" name="newticket[add_cc_newperson]['+rowid+'][name]" placeholder="Enter a full name" />');
				html.push('<input type="text" class="email" name="newticket[add_cc_newperson]['+rowid+'][email]" placeholder="Enter an email address" />');
			html.push('</li>');

			html = $(html.join(''));

			if (term.indexOf('@') !== -1) {
				$('input.email', html).val(term);
			} else {
				$('input.name', html).val(term);
			}

			self.getEl('cc_list').append(html);
			self.updateUi();

			sb.close();
			sb.reset();
		});

		this.getEl('cc_list').on('click', 'em.remove', function() {
			$(this).closest('li').remove();
			self.updateUi();
		});
	},

	//#########################################################################
	//# Message Section
	//#########################################################################

	_initMessageSection: function() {
		var self = this;
		this.getEl('text_snippets_btn').on('click', function(ev) {
			ev.preventDefault();
			self.openSnippetsViewer();
		});

		this.loadSnippetsViewer();

		var textarea = this.getEl('message');
		this.textarea = textarea;

		if (DeskPRO_Window.canUseAgentReplyRte()) {
			var sig = this.getEl('signature_value_html').val() || "";
			sig = sig.replace(/<div class="dp-signature-start">([\w\W]*)<\/div>/, '<p class="dp-signature-start">$1</p>');
			if (sig && parseInt(this.getEl('parent_ticket_id').val()) === 0) {
				textarea.val(($.browser.msie ? '<p></p><p></p>' : '<p><br></p><p><br></p>') + '\n\n' + sig);
			}

			DeskPRO_Window.initRteAgentReply(textarea, {
				defaultIsHtml: true,
				inlineHiddenPosition: this.getEl('is_html_reply'),
				callback: function(obj) {
					obj.addBtnFirst('dp_attach', 'Click here to attach a file. You may also drag a file from your computer desktop into this reply area to upload attachments faster.', function(){});
					obj.addBtnAfter('dp_attach', 'dp_snippets', 'Open snippets', function(){
						self.openSnippetsViewer();
					});
					obj.addBtnSeparatorAfter('dp_attach');
					obj.addBtnSeparatorAfter('dp_snippets');

					var snippetBtn = obj.$toolbar.find('.redactor_btn_dp_snippets').closest('li');
					snippetBtn.addClass('snippets').find('a').html('<span class="show-key-shortcut">S</span>nippets');

					var attachBtn = obj.$toolbar.find('.redactor_btn_dp_attach').closest('li');
					attachBtn.addClass('attach');
					attachBtn.find('a').text('Attach').append('<input type="file" class="file" name="file-upload" />');
				}
			});
			this.getEl('is_html_reply').val(1);

			var ed = textarea.getEditor();
			var api = textarea.data('redactor');
			var lastH = ed.height();
			ed.on('keyup', function(ev) {
				var isCtrl = false;
				if (ev.ctrlKey && DeskPRO_Window.keyboardShortcuts.isMac) {
					isCtrl = true;
				} else if (ev.altKey) {
					isCtrl = true;
				}

				if (isCtrl) {
					if (isCtrl && (ev.which == 85)) {
						ev.preventDefault();
						self.shortcutReplySetAwaitingUser();
						return;
					}
					if (isCtrl && (ev.which == 65)) {
						ev.preventDefault();
						self.shortcutReplySetAwaitingAgent();
						return;
					}
					if (isCtrl && (ev.which == 68)) {
						ev.preventDefault();
						self.shortcutReplySetResolved();
						return;
					}
					if (isCtrl && (ev.which == 82)) {
						ev.preventDefault();
						self.shortcutSendReply();
						return;
					}
					if (isCtrl && (ev.which == 83)) {
						ev.preventDefault();
						window.setTimeout(function() {
							self.shortcutOpenSnippets();
						}, 10);
						return;
					}
					if (isCtrl && (ev.which == 79)) {
						ev.preventDefault();
						window.setTimeout(function() {
							self.shortcutReplyOpenProperties();
						}, 10);
						return;
					}
				}
			});
			ed.on('keypress change', function() {
				textarea.addClass('touched');

				if (lastH != ed.height()) {
					lastH = ed.height();
					self.doScrollBottom = true;
					window.setTimeout(function() {
						if (self.page) {
							self.page.updateUi();
						}
					}, 50);
				}
			});

			var te = new DeskPRO.TextExpander({
				textarea: ed,
				onCombo: function(combo, ev) {
					combo = combo.replace(/%/g, '');
					if (window.DESKPRO_TICKET_SNIPPET_SHORTCODES && window.DESKPRO_TICKET_SNIPPET_SHORTCODES[combo]) {
						ev.preventDefault();

						var snippetId = window.DESKPRO_TICKET_SNIPPET_SHORTCODES[combo];

						var focus = api.getFocus(),
							focusNode = $(focus[0]),
							testText;

						if (focus[0].nodeType == 3) {
							testText = focusNode.text().substring(0, focus[1]);
						} else {
							focus[0] = focusNode.contents().get(focus[1] - 1);
							focusNode = $(focus[0]);
							testText = focusNode.text();
							focus[1] = testText.length;
						}

						var	lastAt = testText.lastIndexOf('%'), matches = [];

						if (lastAt != -1) {
							api.setSelection(focus[0], lastAt, focus[0], focus[1]);
						}

						// web kit handles content editable without an issue. this prevents the span
						// from being extended unnecessarily
						var editable = $.browser.webkit ? ' contenteditable="false"' : '';
						api.insertHtml('<span class="editor-inserting-var snippet-'+snippetId+'" ' + editable + ' data-snippet-id="' + snippetId + '">Inserting snippet...</span>');

						var personId = self.getEl('user_searchbox').find('input.person-id').val() || 0;
						self.pauseSend = true
						$.ajax({
							url: BASE_URL + 'agent/tickets/0/get-snippet/' + snippetId,
							dataType: 'text',
							data: {person_id: personId},
							complete: function() {
								self.pauseSend = false;
							},
							success: function(data) {
								var el = api.$editor.find('.editor-inserting-var.snippet-' + snippetId);
								data = $('<div>' + data + '</div>');

								// trailing newlines
								var coll = data.find('> br');
								coll.last().remove();

								var cursor = $('<span class="_cursor"></span>');
								var cursorPos = data.find('> p');
								if (!cursorPos[0]) {
									cursorPos = data;
								}

								el.after(data);
								cursorPos.append(cursor);
								el.remove();

								var next = data.next();
								if (next.is('br')) {
									next.remove();
								}
								if (cursor.next().is('br')) {
									cursor.next().remove();
								}
								if (cursor.prev().is('br')) {
									cursor.prev().remove();
								}
								api.setSelection(cursor[0], 0, cursor[0], 0);
								api.syncCode();
							}
						});
					}
				}
			});
		} else {
			var sig = this.getEl('signature_value').val();
			if (sig) {
				textarea.val('\n\n' + sig);
			}

			textarea.css('height', 100);

			textarea.TextAreaExpander(150, 1000).on('textareaexpander_expanded', function() {
				self.updateUi();
				window.setTimeout(function() {
					if (self.wrapper) {
						self.wrapper.find('div.layout-content').trigger('goscrollbottom_stick');
					}
				}, 250);
			});
		}
	},

	loadSnippetsViewer: function() {
		var self = this;
		if (this.snippetsViewer) {
			this.snippetsViewer.destroy();
		}

		var self = this;

		this.snippetsViewer = new DeskPRO.Agent.Widget.SnippetViewer({
			positionMode: this.meta.isPopover ? 'over' : 'side',
			onBeforeOpen: function() {
				var redactor = self.getEl('message').data('redactor');
				if (redactor) {
					redactor.saveSelection();
				}
			},
			onSnippetClick: function(info) {

				var ticketLangId = self.getEl('value_form').find('.language_id').val();
				if (!ticketLangId) {
					ticketLangId = info.language_id == DESKPRO_DEFAULT_LANG_ID;
				}
				var snippetId    = info.snippetId;
				var snippetCode  = info.snippetCode;

				var agentText;
				var defaultText;
				var wantText;
				var useText;
				var result;

				Array.each(snippetCode, function(info) {
					if (info.value) {
						if (info.language_id == ticketLangId) {
							wantText = info.value;
						}
						if (info.language_id == DESKPRO_PERSON_LANG_ID) {
							agentText = info.value;
						}
						if (info.language_id == DESKPRO_DEFAULT_LANG_ID) {
							defaultText = info.value;
						}
						useText = info.value;
					}
				});

				if (wantText) {
					useText = wantText;
				} else if (agentText) {
					useText = agentText;
				} else if (defaultText) {
					useText = defaultText;
				}

				try {
					var tpl = twig({
						data: useText,
						strict_variables: true
					});
					result = tpl.render({
						ticket: {
							person: self.meta.person_api_data
						}
					}, {
						strict_variables: true
					});
				} catch(e) {
					console.log("Snippet render failed: %o", e);
					result = useText;
				}

				var redactor = self.getEl('message').data('redactor');
				if (redactor) {
					var html = result;
					html = html.replace(/<\/p>\s*<p>/g, '<br/>');
					html = html.replace(/^<p>/, '');
					html = html.replace(/<\/p>$/, '');

					redactor.restoreSelection();
					redactor.insertHtml(html);
				} else {
					self.insertMessageText(result);
				}
			}
		});
	},

	openSnippetsViewer: function() {
		this.snippetsViewer.open();
	},

	//#########################################################################
	//# Other Section
	//#########################################################################

	_initOtherSection: function() {
		var self = this;

		this.otherTabs = new DeskPRO.UI.SimpleTabs({
			triggerElements: $('li', this.getEl('other_props_tabs')),
			context: this.getEl('other_props_tabs_content'),
			autoSelectFirst: false,
			onTabSwitch: function(eventData) {
				if (!self.labelsInput && eventData.tabContent.hasClass('tab-properties')) {
					self.labelsInput = new DeskPRO.UI.LabelsInput({
						type: 'tickets',
						textarea: $(".ticket-tags input", eventData.tabContent)
					});
					self.ownObject(self.labelsInput);
				}

				self.updateUi();
			},
			onTabClick: (function(ev) {
				var contentWrap = this.getEl('other_props_tabs_content');
				var navWrap = this.getEl('other_props_tabs_wrap');
				var tab = ev.tabEl;

				// Toggle content state if we're clicking for the first time,
				// or re-clicking a tab
				if (!$('.on', navWrap).length || tab.is('.on')) {
					if (contentWrap.is(':visible')) {
						contentWrap.hide();
						navWrap.removeClass('on');
					} else {
						contentWrap.show();
						navWrap.addClass('on');
					}
				}

				self.updateUi();
			}).bind(this)
		});
		this.ownObject(this.otherTabs);

		// Add CC's
		var self = this;
		$('.add-cc-trigger', this.wrapper).on('click', function() {
			var txt = self.getEl('add_cc_txt');
			var val = txt.val();
			var el = $('<li>' + val + '<input type="hidden" name="newticket[new_parts][]" value="'+val+'" />&nbsp;&nbsp;<span class="remove-trigger" style="cursor: pointer;">x</span></li>');

			$('.remove-trigger', el).on('click', function(ev) {
				ev.preventDefault();
				ev.stopPropagation();
				el.remove();
				self.updateUi();
			});

			el.appendTo(self.getEl('cc_list'));
			self.updateUi();

			txt.val('');
		});

		// Attachments
		var list = $('.file-list', this.wrapper);
		$('input', list[0]).live('click', function() {
			var el = $(this);
			var li = el.parent();
			if (el.is(':checked')) {
				li.removeClass('unchecked');
			} else {
				li.addClass('unchecked');
			}
			self.updateUi();
		});
	},

	focusOnReply: function() {
		var txt = this.textarea;

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

	shortcutOpenSnippets: function() {
		this.openSnippetsViewer();
	},

	shortcutSendReply: function() {
		this.submit();
	},

	shortcutReplySetAwaitingUser: function() {
		this.setReplyAsOptionName('awaiting_user');
	},

	shortcutReplySetAwaitingAgent: function() {
		this.setReplyAsOptionName('awaiting_agent');
	},

	shortcutReplySetResolved: function() {
		this.setReplyAsOptionName('resolved');
	},

	shortcutReplyOpenProperties: function() {
		this.openStatusMenu();
	}
});