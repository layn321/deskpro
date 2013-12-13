Orb.createNamespace('DeskPRO.Agent.ElementHandler');

DeskPRO.Agent.ElementHandler.TicketReplyBox = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		this.baseId = this.el.data('base-id');
		this.agentNotifyListShown = false;
	},

	initPage: function() {
		var self = this;
		this.page = this.el.closest('.with-page-fragment').data('page-fragment');

		this.lang = eval(this.el.data('dp-lang') || '{}');

		var textarea = this.getElById('replybox_txt'), isWysiwyg = false;
		this.textarea = textarea;

		this.isNote = false;
		var snippetBtn = null;

		var closeTabCheck = this.getElById('close_tab_opt');
		var closeReply    = this.el.data('close-reply') ? true : false;
		var closeNote     = this.el.data('close-note') ? true : false;

		var agentSel      = this.getElById('agent_sel');
		var agentSelText  = this.getElById('agent_sel_text');
		var agentSelCheck = this.getElById('agent_sel_check');
		var teamSel       = this.getElById('agent_team_sel');
		var teamSelText   = this.getElById('agent_team_sel_text');
		var teamSelCheck  = this.getElById('agent_team_sel_check');

		if (DeskPRO_Window.canUseAgentReplyRte()) {
			var sig = this.el.find('textarea.signature-value-html').val() || "";
			sig = sig.replace(/<div class="dp-signature-start">([\w\W]*)<\/div>/, '<p class="dp-signature-start">$1</p>');

			var draft = this.getElById('draft_html');
			if (draft.length) {
				textarea.val(draft.val());
			} else if (sig) {
				textarea.val(($.browser.msie ? '<p></p><p></p>' : '<p><br></p><p><br></p>') + '\n\n' + sig);
			}

			isWysiwyg = true;

			DeskPRO_Window.initRteAgentReply(textarea, {
				defaultIsHtml: true,
				inlineHiddenPosition: this.getElById('is_html_reply'),
				autosaveContent: 'ticket',
				minHeight: 120,
				autosaveContentId: (this.page ? this.page.meta.ticket_id : false),
				focusCallback: function() {
					if (!self.page.hasReplyFocused) {
						self.wrapper.find('div.layout-content').trigger('goscrolltop');
					}

					self.page.hasReplyFocused = true;
				},
				preAutosaveCallback: function(textarea, data) {

					if (self.getElById('reply_is_trans').val() != "") {
						var newContent = textarea.data('redactor').getCode(),
						name = textarea.attr('name');

						data = [];
						data.push({
							name: name,
							value: newContent
						});
					}

					data.push({
						name: 'extras[is_note]',
						value: self.isNote ? 1 : 0
					});

					self.el.find('input[name="attach[]"]').each(function() {
						data.push({
							name: 'extras[attach][]',
							value: $(this).val()
						});
					});

					self.el.find('input[name="blob_inline_ids[]"]').each(function() {
						data.push({
							name: 'extras[blob_inline_ids][]',
							value: $(this).val()
						});
					});

					return data;
				},
				callback: function(obj) {
					obj.addBtnFirst('dp_attach', 'Click here to attach a file. You may also drag a file from your computer desktop into this reply area to upload attachments faster.', function(){});
					obj.addBtnAfter('dp_attach', 'dp_snippets', 'Open snippets', function(){});
					obj.addBtnSeparatorAfter('dp_attach');

					snippetBtn = obj.$toolbar.find('.redactor_btn_dp_snippets').closest('li');

					var snippets_html = self.lang.snippets_btn;
					snippets_html = snippets_html.replace(/Ss/, '<span class="show-key-shortcut">S</span>');
					snippetBtn.addClass('snippets').find('a').html(snippets_html);

					var attachBtn = obj.$toolbar.find('.redactor_btn_dp_attach').closest('li');
					attachBtn.addClass('attach');
					attachBtn.find('a').text(self.lang.attach_btn).append('<input type="file" class="file" name="file-upload" />');

					obj.addBtnSeparatorAfter('dp_snippets');
				}
			});
			this.getElById('is_html_reply').val(1);

			if (textarea.data('redactor')) {
				var ed = textarea.getEditor();
				var lastH = ed.height();
				if (DESKPRO_ENABLE_KB_SHORTCUTS) {
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
								self.page.shortcutReplySetAwaitingUser();
								return;
							}
							if (isCtrl && (ev.which == 65)) {
								ev.preventDefault();
								self.page.shortcutReplySetAwaitingAgent();
								return;
							}
							if (isCtrl && (ev.which == 68)) {
								ev.preventDefault();
								self.page.shortcutReplySetResolved();
								return;
							}
							if (isCtrl && (ev.which == 82)) {
								ev.preventDefault();
								self.page.shortcutSendReply();
								return;
							}
							if (isCtrl && (ev.which == 83)) {
								ev.preventDefault();
								window.setTimeout(function() {
									self.page.shortcutOpenSnippets();
								}, 10);
								return;
							}
							if (isCtrl && (ev.which == 79)) {
								ev.preventDefault();
								window.setTimeout(function() {
									self.page.shortcutReplyOpenProperties();
								}, 10);
								return;
							}
						}
					});
				}
				var heightUp = function() {
					textarea.addClass('touched');

					if (self.page && lastH != ed.height()) {
						var newH = ed.height();
						var hDiff = newH - lastH;
						lastH = newH;

						if (!self.page.meta.ticket_reverse_order) {
							self.page.doScrollBottom = true;
						}
						window.setTimeout(function() {
							if (self.page) {
								var sEl = self.page.wrapper.find('.layout-content').first().find('.scroll-viewport').first();
								if (sEl && sEl[0]) {
									sEl.get(0).scrollTop = sEl.get(0).scrollTop + hDiff;
								}

								var focus = textarea.getObject().getFocus();
								if (focus && focus[0]) {
									if (focus[0].nodeType == 3) {
										var focusEl = $(focus[0].parentNode);
									} else {
										var focusEl = $(focus[0]);
									}
									var focusPos = focusEl.offset();
									if (focusPos.top+focusEl.height() > $('#dp_window').height()) {
										self.page.updateUi(newH);
									}
								} else {
									self.page.updateUi();
								}
							}
						}, 60);
					}
				};
				ed.on('paste', function(ev) {
					heightUp();
				});
				ed.on('keypress change', function() {
					heightUp();
				});

				this._initAgentNotifier(textarea);
			}
		} else {
			var sig = this.el.find('textarea.signature-value').val();
			if (sig) {
				textarea.val('\n\n' + sig);
			}

			textarea.data('expander-max-height', $(window).height() - 500).TextAreaExpander(150, $(window).height() - 500).on('textareaexpander_expanded', function() {
				var h = $(this).height();
				window.setTimeout(function() {
					if (self.page && $(window).height() - 500 > h) {
						if (!self.page.meta.ticket_reverse_order) {
							self.page.wrapper.find('div.layout-content').trigger('goscrollbottom');
						}
					}
				}, 250);
			});

			textarea.on('keypress change', function() {
				$(this).addClass('touched');
			});
		}

		var translateControls = this.el.find('.translate-controls');
		if (translateControls[0]) {
			var transTrigger = translateControls.find('.trans-trigger');
			translateControls.find('select').on('change', function(ev) {
				var langId = $(this).val();
				var langTitle = $.trim($(this).find(':selected').text());
				transTrigger.find('.translate-lang').data('locale', langId).text(langTitle);
			});

			transTrigger.on('click', function(ev) {
				Orb.cancelEvent(ev);
				self.refreshMessageTranslation(transTrigger.find('.translate-lang').data('locale'));
			});

			var textarea2 = self.getElById('replybox_txt2');
			DeskPRO_Window.initRteAgentReply(textarea2, {
				defaultIsHtml: true,
				minHeight: 120,
				callback: function(obj) {
					obj.addBtn('dp_cancel_trans', 'Cancel message translation', function(){
						self.closeMessageTranslation();
					});
					obj.setBtnRight('dp_cancel_trans');

					var cancelTransBtn = obj.$toolbar.find('.redactor_btn_dp_cancel_trans').closest('li');
					cancelTransBtn.addClass('cancel_trans');
					cancelTransBtn.find('a').text('Cancel Translation');
				}
			});

			self.page.getEl('value_form').find('.language_id').on('change', function() {
				var langId     = $(this).val();
				if (!langId) {
					langId = DESKPRO_DEFAULT_LANG_ID;
				}

				var langLocale = DESKPRO_NAME_REGISTRY.lang_data[langId].locale;
				var langTitle  = $.trim(DESKPRO_NAME_REGISTRY.lang_data[langId].title);

				transTrigger.find('.translate-lang').data('locale', langLocale).text(langTitle);
			});
		}

		var wasAgentChecked = agentSelCheck.prop('checked');
		var wasTeamChecked  = teamSelCheck.prop('checked');

		var storedReplyText = '';
		var storedNoteText = '';
		var replyMode = 'reply';

		this.getElById('replybox_replytab_btn').on('click', function() {
			if (replyMode == 'reply') {
				return;
			}
			replyMode = 'reply';

			self.el.removeClass('dp-note-on');
			$(this).addClass('on');
			self.getElById('replybox_notetab_btn').removeClass('on');
			$('.hide-note:not(.is-hidden)', self.el).show();
			$('.hide-reply', self.el).hide();
			self.getElById('is_note').val('0');
			self.isNote = false;
			self.hideAgentNotifyList();

			if (closeReply) {
				closeTabCheck.prop('checked', true);
			} else {
				closeTabCheck.prop('checked', false);
			}

			if (wasAgentChecked) {
				agentSelCheck.prop('checked', true);
			}
			if (wasTeamChecked) {
				teamSelCheck.prop('checked', true);
			}

			if (isWysiwyg && textarea.data('redactor')) {
				storedNoteText = textarea.getCode();
				textarea.setCode(storedReplyText || '');
			} else {
				storedNoteText = textarea.val();
				textarea.val(storedReplyText || '');
			}

			if (self.page) {
				var scroller = self.page.wrapper.find('div.layout-content');
				scroller.data('scroll_handler').updateSize();
				if (!self.page.meta.ticket_reverse_order) {
					scroller.trigger('goscrollbottom');
				}
			}
		});

		this.getElById('replybox_notetab_btn').on('click', function() {
			if (replyMode == 'note') {
				return;
			}
			replyMode = 'note';

			self.el.addClass('dp-note-on');
			$(this).addClass('on');
			self.getElById('replybox_replytab_btn').removeClass('on');
			$('.hide-note', self.el).hide();
			$('.hide-reply', self.el).show();
			self.getElById('is_note').val('1');
			self.isNote = true;
			self.hideAgentNotifyList();

			if (closeNote) {
				closeTabCheck.prop('checked', true);
			} else {
				closeTabCheck.prop('checked', false);
			}

			if (isWysiwyg && textarea.data('redactor')) {
				storedReplyText = textarea.getCode();
				textarea.setCode(storedNoteText || '');
			} else {
				storedReplyText = textarea.val();
				textarea.val(storedNoteText || '');
			}

			wasAgentChecked = agentSelCheck.prop('checked');
			wasTeamChecked  = teamSelCheck.prop('checked');

			agentSelCheck.prop('checked', false);
			teamSelCheck.prop('checked', false);

			if (self.page) {
				var scroller = self.page.wrapper.find('div.layout-content');
				scroller.data('scroll_handler').updateSize();
				if (!self.page.meta.ticket_reverse_order) {
					scroller.trigger('goscrollbottom');
				}
			}
		});

		if (this.el.data('default-is-note') == '1') {
			this.el.addClass('dp-note-on');
			this.getElById('replybox_notetab_btn').addClass('on');
			this.getElById('replybox_replytab_btn').removeClass('on');
			$('.hide-note', this.el).hide();
			$('.hide-reply', this.el).show();
			this.getElById('is_note').val('1');
			this.isNote = true;
			this.hideAgentNotifyList();
		}

		//------------------------------
		// Expanding cc row
		//------------------------------

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

		var cc_add_wrap = this.getElById('newcc');
		var cc_del_wrap = this.getElById('delcc');

        var cc_row = this.getElById('cc_row');
        var cc_input = this.getElById('cc_input');
		var cc_user_rows = this.getElById('cc_user_rows');

        cc_row.autoCompleteElement = new DeskPRO.Agent.ElementHandler.SimpleAutoComplete(cc_row);
        this.ccRowTpl = '';
        var ccRemoveFunction = function() {
			var row = $(this).closest('.cc-user-row');
			var input = $('<input type="hidden" />');
			input.attr('name', 'delcc[]');
			input.val(row.data('email-address'));

			cc_del_wrap.append(input);
			row.remove();
        };
        $('.user-rows', cc_row).on('click', '.remove-row-trigger', ccRemoveFunction);

        $('.cc-saverow-trigger', cc_row).on('click', function(ev) {
			var user_row = $(self.ccRowTpl);
			var email = $('input.user-part', cc_row).val().trim();
			var parts = email.split('@');

			if(email == ''
			|| parts.length != 2
			|| !parts[0]
			|| !parts[1]
			|| email.indexOf(',') != -1) {
				return;
			}

			var input = $('<input type="hidden" />');
			input.attr('name', 'addcc[]');
			input.val(email);
			$('input.user-part', cc_row).val('');

			cc_add_wrap.append(input);

			var newrow = $('<li />').addClass('cc-user-row').data('email', email);
			newrow.append('<span class="btn-small-remove remove-row-trigger" />');
			var span = $('<span class="user-email" />');
			span.text(email);
			newrow.append(' ');
			newrow.append(span);

			cc_user_rows.append(newrow);

			ev.stopPropagation();
			cc_row.autoCompleteElement.close();
		});

		//------------------------------
		// Upload handling
		//------------------------------

		DeskPRO_Window.util.fileupload(this.el, {
			dropZone: this.getElById('file_drop_zone'),
			uploadTemplate: $('.template-upload', this.el),
			downloadTemplate: $('.template-download', this.el)
		});
		this.el.bind('fileuploaddone', function() {
			self.getElById('attach_row').show().removeClass('is-hidden');
			if (self.page) {
				self.page.updateUi();
				if (!self.page.meta.ticket_reverse_order) {
					if (self.page.scrollHandlers && self.page.scrollHandlers[0]) {
						$(self.page.scrollHandlers[0]).data('scroll_handler').getElement().trigger('goscrollbottom_stick');
					}
				}
			}
		});
		this.el.bind('fileuploadstart', function() {
			self.getElById('attach_row').show().removeClass('is-hidden');
			if (self.page) {
				self.page.updateUi();
				if (self.page.scrollHandlers && self.page.scrollHandlers[0]) {
					if (!self.page.meta.ticket_reverse_order) {
						$(self.page.scrollHandlers[0]).data('scroll_handler').getElement().trigger('goscrollbottom_stick');
					}
				}
			}
		});

		this.el.on('click', '.remove-attach-trigger', function() {

			var row = $(this).closest('li');
			row.fadeOut('fast', function() {
				row.remove();

				var rows = $('ul.files li', self.getElById('attach_row'));
				if (!rows.length) {
					self.getElById('attach_row').hide().addClass('is-hidden');
					if (self.page) {
						self.page.updateUi();
						if (!self.page.meta.ticket_reverse_order) {
							if (self.page.scrollHandlers && self.page.scrollHandlers[0]) {
								$(self.page.scrollHandlers[0]).data('scroll_handler').getElement().trigger('goscrollbottom_stick');
							}
						}
					}
				}
			});
        });

		//------------------------------
		// Toggle buttons
		//------------------------------

		$('.option-buttons', this.el).on('click', 'li.toggle', function() {
			if ($(this).hasClass('keep_open_toggle')) {
				return;
			}
			var check = $(':checkbox', this);
			if (!check.length) {
				return;
			}

			if (check.is(':checked')) {
				check.attr('checked', false);
				$(this).removeClass('on');
			} else {
				check.attr('checked', true);
				$(this).addClass('on');
			}
		});


		//------------------------------
		// Snippets Viewer
		//------------------------------

		this.snippetsViewer = new DeskPRO.Agent.Widget.SnippetViewer({
			driver: DeskPRO_Window.ticketSnippetDriver,
			triggerElement: snippetBtn,
			onBeforeOpen: function() {
				if (isWysiwyg && textarea.data('redactor')) {
					try {
						textarea.data('redactor').saveSelection();
					} catch (e) {}
				}
			},
			onSnippetClick: function(info) {

				if (!self.page) {
					return;
				}

				var ticketLangId = self.page.getEl('value_form').find('.language_id').val();
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
						strict_variables: false
					});
					if (tpl) {
						result = tpl.render({
							ticket: self.page.meta.api_data
						}, {
							strict_variables: false
						});
					} else {
						result = useText;
					}
				} catch(e) {
					console.log("Snippet render failed: %o", e);
					result = useText;
				}

				if (isWysiwyg && textarea.data('redactor')) {
					try {
						textarea.data('redactor').restoreSelection();
						textarea.data('redactor').setBuffer();
					} catch (e) {}

					var html = result;
					html = html.replace(/<\/p>\s*<p>/g, '<br/>');
					html = html.replace(/^<p>/, '');
					html = html.replace(/<\/p>$/, '');
					textarea.data('redactor').insertHtml(html);
				} else {
					self.page.insertTextInReply(result);
				}

				self.snippetsViewer.close();
			}
		});

		self.wasSnippetOpen = false;
		this.el.bind('page_deactivate', function() {
			if (self.snippetsViewer && self.snippetsViewer.pop && self.snippetsViewer.pop.isOpen()) {
				self.snippetsViewer.close();
				self.wasSnippetOpen = true;
			}
		});
		this.el.bind('page_activate', function() {
			if (self.wasSnippetOpen) {
				self.snippetsViewer.open();
			}
		});

		if (textarea.data('redactor')) {
			var ed = textarea.getEditor();
			var api = textarea.data('redactor');

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

						if (!self.page) {
							self.page = self.el.closest('.with-page-fragment').data('page-fragment');
						}

						if (self.page) self.page.pauseSend = true;
						$.ajax({
							url: BASE_URL + 'agent/text-snippets/tickets/'+snippetId+'.json',
							dataType: 'json',
							complete: function() {
								if (self.page) self.page.pauseSend = false;
							},
							success: function(data) {

								var snippet = data.snippet;
								var ticketLangId = self.page ? self.page.getEl('value_form').find('.language_id').val() : 0;
								var snippetId    = snippet.id;
								var snippetCode  = snippet.snippet;

								var agentText;
								var defaultText;
								var wantText;
								var useText;
								var result;

								Array.each(snippetCode, function(info) {
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
									if (tpl) {
										result = tpl.render({
											ticket: self.page ? self.page.meta.api_data : {}
										}, {
											strict_variables: true
										});
									} else {
										result = useText;
									}
								} catch(e) {
									console.log("Snippet render failed: %o", e);
									result = useText;
								}

								var data = result;

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
		}

		//------------------------------
		// Status
		//------------------------------

		var statusDetailEl = this.getElById('status_detail');
		this.statusMenu = new DeskPRO.UI.Menu({
			triggerElement: $('.status-trigger', statusDetailEl),
			menuElement: this.getElById('status_menu'),
			onItemClicked: function(info) {
				var item = $(info.itemEl);
				var val = item.data('status');

				if (val == 'no-change') {
					self.getElById('ticket_do_status').val(0);
					statusDetailEl.removeClass('changed');
				} else {
					$('.new-val-label', statusDetailEl).text(item.text().trim());
					self.getElById('ticket_do_status').val(1);
					self.getElById('ticket_status').val(val);
					statusDetailEl.addClass('changed');
				}
			}
		});

		//------------------------------
		// Assignments
		//------------------------------

		window.setTimeout(function() {
			DP.select(agentSel);
			DP.select(teamSel);
		}, 150);

		agentSel.on('change', function() {
			var option = agentSel.find(':selected');
			agentSelText.text(option.data('name-short'));
			agentSelText.css('background-image', 'url(' + option.data('icon')+ ')');
			agentSelCheck.prop('checked', true);

			if (agentSel.data('auto-switch-status')) {
				if (agentSelCheck.get(0).checked) {
					if (self.getElById('action').val().indexOf('macro') === -1) {
						self.setReplyAsOptionName('awaiting_agent')
					}
				}
			}
		});
		teamSel.on('change', function() {
			teamSelText.text($(this).find(':selected').text());
			teamSelCheck.prop('checked', true);
		});

		var option = agentSel.find(':selected');
		agentSelText.text(option.data('name-short'));
		agentSelText.css('background-image', 'url(' + option.data('icon')+ ')');
		teamSelText.text(teamSel.find(':selected').text());

		if (agentSel.data('auto-switch-status')) {
			agentSelCheck.on('change', function() {
				if (agentSelCheck.get(0).checked) {
					if (self.getElById('action').val().indexOf('macro') === -1) {
						self.setReplyAsOptionName('awaiting_agent')
					}
				}
			});
		}

		//------------------------------
		// Submit
		//------------------------------

		this.el.on('submit', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();
		});

		this.el.find('.submit-trigger').on('click', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			if (isWysiwyg && textarea.data('redactor')) {
				textarea.data('redactor').syncCode();
			}

			if (isWysiwyg) {
				var copy = $.trim(self.el.find('.editor-row').find('.redactor_editor').text()).replace(/\s/g, ' ');
				var tmp = $('<div/>').html(self.el.find('textarea.signature-value-html').val());
				var sig = $.trim(tmp.text()).replace(/\s/g, ' ');

				if (!copy || copy == sig) {
					DeskPRO_Window.showAlert('Please enter a message.');
					return;
				}
			}

			self.getElById('action').val(self.getElById('reply_as_type').data('type'));

			var formData = self.el.serializeArray();
			self.el.trigger('replyboxsubmit', [formData, self]);
		});

		this.getElById('keep_open_toggle').on('click', function(ev) {
			ev.preventDefault();
			if ($(this).hasClass('radio-on')) {
				$(this).removeClass('radio-on on');
			} else {
				$(this).addClass('radio-on on');
			}
		});

		//------------------------------
		// Status menu
		//------------------------------

		var statusMenuTrigger = this.el.find('.status-menu-trigger');
		var footerEl = this.el.find('footer').first();
		var statusMenu = this.getElById('status_menu');
		var statusMenuH = null;
		var statusBackdrop = null;
		var statusMacroFilter = null;
		var statusMacroList = statusMenu.find('.macro-list');
		var statusMacroListMap = null;
		var statusListItems = null;
		var replyAsType = this.getElById('reply_as_type');

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
							self.page.shortcutReplySetAwaitingUser();
							return;
						}
						if (isCtrl && (ev.which == 65)) {
							closeStatusMenu();
							self.page.shortcutReplySetAwaitingAgent();
							return;
						}
						if (isCtrl && (ev.which == 68)) {
							closeStatusMenu();
							self.page.shortcutReplySetResolved();
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
						var lis = statusMacroList.find('li');
						var lis_show = [];

						val = val.toLowerCase();

						if (window.DESKPRO_MACRO_LABELS) {

							if (!statusMacroListMap) {
								statusMacroListMap = {};
								// Generate map of macro_id => li element
								for (var i = 0; i < window.DESKPRO_MACRO_LABELS.length; i++) {
									statusMacroListMap[window.DESKPRO_MACRO_LABELS[i][0]] = document.getElementById(self.baseId + '_res_ticketmacro_' + window.DESKPRO_MACRO_LABELS[i][0]);
								}
							}

							for (var i = 0; i < window.DESKPRO_MACRO_LABELS.length; i++) {
								if (window.DESKPRO_MACRO_LABELS[i][1].indexOf(val) !== -1) {
									if (statusMacroListMap[window.DESKPRO_MACRO_LABELS[i][0]]) {
										lis_show.push(statusMacroListMap[window.DESKPRO_MACRO_LABELS[i][0]]);
									} else {
										lis_show.push(document.getElementById(self.baseId + '_res_ticketmacro_' + window.DESKPRO_MACRO_LABELS[i][0]));
									}
								}
							}

							if (lis_show.length) {
								if (lis_show.length < lis.length) {
									lis.not(lis_show).addClass('off').hide();
								}
								$(lis_show).removeClass('off').show();
							} else {
								lis.addClass('off').hide();
							}

						} else {
							statusMacroList.find('li').each(function() {
								if ($(this).text().toLowerCase().indexOf(val) !== -1) {
									$(this).show().removeClass('off');
								} else {
									$(this).hide().addClass('off');
								}
							});
						}
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

			var w = self.getElById('reply_btn_group').width() - 3;
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
				if (self.page) {
					li.data('get-macro-url', BASE_URL + 'agent/tickets/' + self.page.meta.ticket_id + '/ajax-get-macro?macro_id=' + info.id + '&macro_reply_context=1');
				}
				li.data('label', 'Send Reply and ' + info.title);
				li.data('type', 'macro:'+info.id);
				li.attr('data-type', 'macro:'+info.id);
				li.find('.macro-title').text(info.title);

				statusMacroList.append(li);
			});
		});

		if (this.page) {
			this.page.setTicketReplyBox(this);

			// If agent/team already set to the default values, dont precheck (makes it a bit clearer that nothing would change)
			if (agentSel.val() == this.page.getEl('value_form').find('.agent_id').val()) {
				agentSelCheck.prop('checked', false);
			}
			if (teamSel.val() == this.page.getEl('value_form').find('.agent_team_id').val()) {
				teamSelCheck.prop('checked', false);
			}
		}

		// Init macro title list
		if (!window.DESKPRO_MACRO_LABELS) {
			window.DESKPRO_MACRO_LABELS = [];
			statusMacroListMap = {};
			statusMacroList.find('li').each(function() {
				var label = $(this).data('macro-title').toLowerCase();
				var macro_id = parseInt($(this).data('macro-id'));
				window.DESKPRO_MACRO_LABELS.push([macro_id, label.toLowerCase()])
				statusMacroListMap[macro_id] = this;
			});
		}

		// Depending on perms, the note tab might be already on
		// And we need ot run certain other hide/show actions
		if (this.el.hasClass('dp-note-on')) {
			this.getElById('replybox_notetab_btn').click();
		}
	},

	setReplyAsOptionName: function(name) {
		var item = this.getElById('status_menu').find('li[data-type="' + name + '"]').first();
		if (item[0]) {
			this.setReplyAsOption(item);
		}
	},

	setReplyAsOption: function(item) {
		var replyAsType = this.getElById('reply_as_type');

		var html = Orb.escapeHtml(item.data('label'));
		html = html.replace(/^Send Reply/, 'Send <span class="show-key-shortcut">R</span>eply');
		replyAsType.data('type', item.data('type')).html(html);

		var macroUrl = item.data('get-macro-url');

		var textarea = this.textarea;
		var api = this.textarea.data('redactor');

		if (this.el.data('resolve-auto-close')) {
			if (item.data('type') == 'resolved') {
				this.getElById('close_tab_opt').prop('checked', true);
			}
		}

		if (!macroUrl) {
			this.getElById('actions_row').hide();
			if (this.page) {
				this.page.updateUi();
				if (!this.page.meta.ticket_reverse_order) {
					this.page.wrapper.find('div.layout-content').trigger('goscrollbottom');
				}
			}
		} else {
			var actionsRow = this.getElById('actions_row');
			var actionsRowList = actionsRow.find('ul');
			actionsRowList.empty();
			actionsRowList.append('<li class="load"><i class="flat-spinner"></i></li>');

			actionsRow.show();

			if (this.page) {
				this.page.updateUi();
				if (!this.page.meta.ticket_reverse_order) {
					this.page.wrapper.find('div.layout-content').trigger('goscrollbottom');
				}
			}

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

						this.getElById('agent_sel').select2('val', agentId);
						this.getElById('agent_sel').change();
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
							this.getElById('agent_team_sel').select2('val', agentTeamId);
							this.getElById('agent_team_sel').change();
						}
					}

					if (actionsRowList.find('.with-close-tab')) {
						this.getElById('close_tab_opt').prop('checked', true);
					}

					if (this.page) {
						this.page.updateUi();
						if (!this.page.meta.ticket_reverse_order) {
							this.page.wrapper.find('div.layout-content').trigger('goscrollbottom');
						}
					}
				}
			});
		}

		if (this.page) {
			this.page.focusOnReply()
		}
	},

	hideAgentNotifyList: function() {
		DeskPRO_Window.hideAgentNotifyList(this);
	},

	_initAgentNotifier: function(textarea) {
		DeskPRO_Window.initAgentNotifierForRte(
			this, textarea,
			this.page && this.page.meta.agentMap ? this.page.meta.agentMap : false
		);
	},

	getElById: function(id) {
		var el = $('#' + this.baseId + '_' + id);
		return el;
	},

	addCc: function(email) {
		var input = $('.token-input input', this.getElById('cc_input'));
		input.val(email).focus().blur();
	},

	removeCc: function(email) {
		$('.token-x', this.getElById('cc_input')).each(function() {
			if ($(this).data('for-value') == email) {
				$(this).click();
			}
		});
	},

	appendToMessage: function(content) {
		var textarea = this.getElById('replybox_txt');
		var isWysiwyg = textarea.data('redactor') ? true : false;

		var sig = this.getElById('signature_value').val();

		if (isWysiwyg) {
			sig = DP.convertTextToWysiwygHtml(sig, true);
			content = DP.convertTextToWysiwygHtml(content, true);

			var val = textarea.getCode();
			if (val == '<p></p>' || val == '<p><br></p>') {
				val = '';
			}
		} else {
			var val = textarea.val();
		}

		if (val.trim().length) {

			// Always put it before the signature
			// (if have sig and val ends with sig)
			if (sig.length && val.indexOf(sig, val.length - sig.length) !== -1) {
				val = content + val;
			} else {
				val += " ";
				val += content;
			}
		} else {
			val = content;
		}

		if (isWysiwyg) {
			textarea.setCode(val);
		} else {
			textarea.val(val);
		}
	},

	refreshMessageTranslation: function(to) {
		var self       = this;
		var previewRow = this.el.find('.translate-row');

		if (!to) {
			this.closeMessageTranslation();
			return;
		}

		var formData = {
			from: 'me',
			to: to,
			message_text: this.getElById('replybox_txt').val()
		};

		var translateControls = this.el.find('.translate-controls');
		translateControls.addClass('dp-loading-on');
		$.ajax({
			url: window.DESKPRO_TRANSLATE_SERVICE.translate_text_url,
			data: formData,
			type: 'POST',
			dataType: 'json',
			complete: function() {
				translateControls.removeClass('dp-loading-on');
			},
			success: function(data) {
				previewRow.show();

				self.getElById('replybox_txt2').data('redactor').setCode(formData.message_text);
				self.getElById('replybox_txt').data('redactor').setCode(data.message);
				self.getElById('reply_is_trans').val(to);
			}
		});
	},

	closeMessageTranslation: function() {
		var previewRow = this.el.find('.translate-row');
		previewRow.hide();

		this.getElById('replybox_txt').data('redactor').setCode(this.getElById('replybox_txt2').data('redactor').getCode());
		this.getElById('replybox_txt2').data('redactor').setCode('');
		this.getElById('reply_is_trans').val('');
	},

	destroy: function() {
		var textarea = this.getElById('replybox_txt');
		if (textarea.data('redactor')) {
			try {
				textarea.destroyEditor();
			} catch (e) {}
		}
		if (this.agentNotifyList) {
			this.agentNotifyList.remove();
		}
		if (this.snippetsViewer) {
			this.snippetsViewer.destroy();
		}
	}
});
