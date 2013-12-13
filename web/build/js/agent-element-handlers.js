Orb.createNamespace('DeskPRO.Agent.ElementHandler');

DeskPRO.Agent.ElementHandler.FormSaver = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var self = this;
		this.textarea = $('textarea', this.el);
                this.inout = $('input:text', this.el); // Added by Abdullah Kiser inorder to catch the touch event.
		this.list = null;
		this.resultHtmlKey = this.el.data('form-result-html-key') || 'html';

		// Optionally append ajax result to a list
		if (this.el.data('form-list-selector')) {
			this.list = this.el.closest(this.el.data('form-list-selector'));
		}

		DP.console.log(this.list);

		this.url = this.el.data('form-save-url');

		this.statusSave   = $('header .save', this.el);
		this.statusSaved  = $('header .saved', this.el);
		this.statusSaving = $('header .is-loading', this.el);

		this.statusSave.on('click', function(ev) {
			ev.preventDefault();
			self.save();
		});

		this.textarea.on('change', this.touch.bind(this));
		this.textarea.on('keypress', this.touch.bind(this));

                // Added by Abdullah Kiser inorder to catch the touch event.
                this.inout.on('change', this.touch.bind(this));
		this.inout.on('keypress', this.touch.bind(this));


		this.countEl = null;
		if (this.el.data('form-count-el')) {
			this.countEl = $(this.el.data('form-count-el'));
		}
	},

	touch: function() {
		this.statusSave.show();
		this.statusSaved.hide();
		this.statusSaving.hide();
	},

	save: function() {
		this.statusSave.hide();
		this.statusSaved.hide();
		this.statusSaving.show();

		var formEls  = $('input, textarea, select', this.el);
		var postData = formEls.serializeArray();

		var doSend = true;
		var checkBlankEls = formEls.filter('[data-not-blank]').each(function(){
			if ($(this).val().trim() === '') {
				doSend = false;
				return false;
			}
		});

		if (!doSend) {
			this.statusSave.hide();
			this.statusSaved.hide();
			this.statusSaving.hide();
			return;
		}


		$.ajax({
			url: this.url,
			type: 'POST',
			data: postData,
			dataType: 'json',
			context: this,
			complete: function() {
				this.statusSave.hide();
				this.statusSaved.show();
				this.statusSaving.hide();

				window.setTimeout((function() {
					this.statusSaved.fadeOut('slow');
				}).bind(this), 1000);
			},
			success: function(data) {

				if (data.error) {
					return;
				}

				if (this.list) {
					var newRow = $(data[this.resultHtmlKey]);
					DeskPRO_Window.initInterfaceServices(newRow);

					if (this.el.parent().get(0) == this.list.get(0)) {
						newRow.insertBefore(this.el);
					} else {
						this.list.append(newRow);
					}
					this.textarea.val('');
				}

				if (this.countEl) {
					DeskPRO_Window.util.modCountEl(this.countEl, '+');
				}
			}
		});
	}
});

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

Orb.createNamespace('DeskPRO.Agent.ElementHandler');

DeskPRO.Agent.ElementHandler.TicketCcManage = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	initPage: function() {
		var self = this;
		var addUrl = this.el.data('add-url');
		this.deleteUrl = this.el.data('delete-url');

		var list = $('ul', this.el).first();
		var newrow = $('li.newrow', this.el);

		this.el.find('li').each(function() {
			self.initRow($(this));
		})

		var addRow = $('.addrow', this.el);
		if (addRow.length) {
			addRow.autoCompleteElement = new DeskPRO.Agent.ElementHandler.SimpleAutoComplete(addRow);

			addRow.on('click', '.cc-saverow-trigger', function(ev) {
				var email = $('input', addRow).val().trim();

				if (!email) {
					return;
				}

				addRow.addClass('loading');

				$.ajax({
					url: addUrl,
					type: 'POST',
					data: { email_address: email },
					dataType: 'json',
					complete: function() {
						addRow.removeClass('loading');
					},
					success: function(data) {
						if (data.error) {
							if (data.error_code == 'invalid_email') {
								DeskPRO_Window.showAlert('Please enter a valid email address');
							} else if (data.error_code == 'invalid_email_gatewayaccount') {
								DeskPRO_Window.showAlert('The email address you entered belongs to a an account in Admin > Tickets > Email Accounts. You cannot add email accounts as CCs.');
							} else if (data.error_code == 'is_agent') {
								DeskPRO_Window.showAlert('The user you specified is an agent. To add an agent to this ticket, use the "Add a follower" button in the Properties box.');
								self.el.closest('.tabViewDetailContent').find('ul.cc-row-list').each(function() {
									$(this).empty().html(data.cc_list || '');
									$(this).find('li').each(function() {
										self.initRow($(this));
									});
								});
							}
							return;
						}

						if (data.is_dupe) {
							DeskPRO_Window.showAlert('The user you specified is already on this ticket.');
							return;
						}

						addRow.find('input').val('');

						self.el.find('ul.cc-row-list').each(function() {
							$(this).empty().html(data.cc_list || '');
							$(this).find('li').each(function() {
								self.initRow($(this));
							});
						});
					}
				});
			});
		}
	},

	initRow: function(row) {
		var self = this;
		row.find('.remove-row-trigger').on('click', function(ev) {
			ev.stopPropagation();
			ev.preventDefault();

			var personId = row.data('person-id');
			var email = row.data('email-address');

			if (personId) {
				$.ajax({
					url: self.deleteUrl,
					type: 'POST',
					data: { person_id: personId },
					dataType: 'json',
					success: function(data) {
						self.el.closest('.tabViewDetailContent').find('ul.cc-row-list').each(function() {
							$(this).empty().html(data.cc_list || '');
							self.el.find('ul.cc-row-list').each(function() {
								$(this).empty().html(data.cc_list || '');
								$(this).find('li').each(function() {
									self.initRow($(this));
								});
							});
						});
					},
					error: function() {
						row.show();
					}
				});

				row.fadeOut('fast');
			}

			row.fadeOut('fast', function() {
				row.remove();
			});
		});
	}
});

Orb.createNamespace('DeskPRO.Agent.ElementHandler');

DeskPRO.Agent.ElementHandler.SimpleAutoComplete = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	initPage: function() {
        var self = this;
        this.searchUrl = BASE_URL + 'agent/people-search/search-quick?format=json&limit=10&start_with=a';
        this.el.on('click', '.cc-people-search-trigger', function() {
            self.open();
        });
	},

    /**
     * Inits the search box to ensure it can be positioned properly
     */
    _initResultsBox: function() {
        var self = this;

        if (this._hasInitResultsBox) return;
        this._hasInitResultsBox = true;

        this.termInput   = $('.cc-people-search-trigger', this.el);
        this.resultsBox  = $('.person-search-box', this.el);
        this.resultsList = $('.results-list', this.resultsBox);
        this.addButton = $('.cc-saverow-trigger', this.el);

        this.tplHtml = DeskPRO_Window.util.getPlainTpl($('.user-row-tpl', this.el.parent()));

        //------------------------------
        // Update caller schedules the update requests
        //------------------------------

        var updateCaller =
        this.updateCaller = new DeskPRO.TouchCaller({
            timeout: 500,
            callback: (this.updateResults).bind(this),
            context: this
        });

        //------------------------------
        // Input events
        //------------------------------

        // Touch the timer so we will search in a few seconds,
        // or handle arrow and enter keys to select values in the list
        this.termInput.on('keypress', function(ev) {
            if (ev.keyCode == 27) {
                self.close.bind(self);
            } else if (ev.keyCode == 13 /* enter key */) {

                ev.preventDefault();

                var current = $('li.on', self.resultsList);

                if (current.length) {
                    var personId = current.data('person-id');
                    var name  = $('.user-name', current).text().trim();
                    var email = $('.user-email', current).text().trim();

                    self.termInput.val(email);
                    self.el.trigger('personsearchboxclick', [personId, name, email, self]);
                } else {
                    var term = self.getTerm();
                    self.el.trigger('personsearchboxclicknew', [term, self]);
                }

                self.addButton.click();
                self.close();
            } else if (ev.keyCode == 40 /* down key */ || ev.keyCode == 38 /* up key */) {

                ev.preventDefault();

                var dir = ev.keyCode == 40 ? 'down' : 'up';

                var current = $('li.on', self.resultsList);
                $('li', self.resultsList).removeClass('on');

                if (!current.length) {
                    if (dir == 'down') {
                        $('li', self.resultsList).first().addClass('on');
                    } else {
                        $('li', self.resultsList).last().addClass('on');
                    }
                } else {
                    if (dir == 'down') {
                        var next = current.next('li');
                        if (!next.length) {
                            next = $('li', self.resultsList).first();
                        }
                    } else {
                        var next = current.prev('li');
                        if (!next.length) {
                            next = $('li', self.resultsList).last();
                        }
                    }

                    next.addClass('on');
                }
            } else {
                updateCaller.touch(self.getTerm());
            }
        }).on('change', function() { updateCaller.touch(self.getTerm()); });

        // Stop bubbling so it doesnt reach the document and close itself
        this.termInput.on('click', function(ev) { self.open();ev.stopPropagation(); });
        this.resultsBox.on('click', function(ev) { ev.stopPropagation(); });

        $(document).on('click', this.close.bind(this));


        //------------------------------
        // Clicking on an item fires an event that
        // the page controller can listen to
        //------------------------------

        this.resultsList.on('click', 'li', function(ev) {
            ev.preventDefault();
            var email = $('.user-email', this).text().trim();

            self.termInput.val(email);
            self.addButton.click();
            self.close();
        });

        this.resultsBox.detach().hide().appendTo('body');
    },


    /**
     * Reset the box back to empty
     */
    reset: function() {
        if (this.runningAjax) {
            this.runningAjax.abort();
            this.runningAjax = null;
        }
        this.termInput.val('');
        this.resultsList.empty();
    },


    /**
     * Refresh the position of the search box relative to its bound element.
     */
    refreshPosition: function() {
        var termPos = this.termInput.offset();
        var termW   = this.termInput.outerWidth();
        var termH   = this.termInput.outerHeight();

        this.resultsBox.css({
            top: termPos.top + termH - 1,
            left: termPos.left,
            width: termW,
            position: 'absolute',
            zIndex: '1000'
        });
    },


    /**
     * Get the search term in the box
     *
     * @return {String}
     */
    getTerm: function() {
        return this.termInput.val().trim();
    },


    /**
     * Sends the ajax request to find users that match the term in the search box
     */
    updateResults: function() {
        var term = this.getTerm();

        var postData = [];
        postData.push({
            name: 'term',
            value: term
        });

        this.runningAjax = $.ajax({
            type: 'GET',
            url: this.searchUrl,
            data: postData,
            dataType: 'json',
            context: this,
            complete: function() {
                this.runningAjax = null;
            },
            success: function(data) {
                var currentPersonId = parseInt($('li.on', this.resultsList).data('person-id')) || 0;
                this.resultsList.empty();

                Array.each(data, function(user) {
                    var row = $(this.tplHtml);

                    row.data('person-id', user.id);
                    row.attr('person-id', user.id);
                    row.addClass('person-' + user.id);

                    if (currentPersonId && currentPersonId == parseInt(user.id)) {
                        row.addClass('on');
                        currentPersonId = false;
                    }

                    if (this.el.data('highlight-term')) {
                        var term  = Orb.escapeHtml(this.getTerm());
                        var name  = Orb.escapeHtml(user.name);
                        var email = Orb.escapeHtml(user.email);

                        term = (term+'').replace(/([\\\.\+\*\?\[\^\]\$\(\)\{\}\=\!\<\>\|\:])/g, "\\$1");
                        name = name.replace( new RegExp( "(" + term + ")", 'gi' ), '<span class="highlight">$1</span>' );
                        email = email.replace( new RegExp( "(" + term + ")", 'gi' ), '<span class="highlight">$1</span>' );

                        $('.user-name', row).html(name);
                        $('.user-email', row).html(email);

                    } else {
                        $('.user-name', row).text(user.name);
                        $('.user-email', row).text(user.email);
                    }

                    if (!user.email || user.name == user.email) {
                        $('address', row).hide();
                    }

                    this.resultsList.append(row);
                }, this);

                if(this.termInput.is(':focus') && data.length) {
                    this.open();
                }
            }
        });
    },


    /**
     * Opens the results box
     */
    open: function() {
        this._initResultsBox();

        this.refreshPosition();
        this.resultsBox.show();
    },


    /**
     * Closes the results box and stops any updating stuff
     */
    close: function() {
		// Could be destroyed so need to check that el still exists
		if (this.resultsBox) {
			this.resultsBox.hide();
		}
    },


    /**
     * Destroys the widget
     */
    destroy: function() {
        if (this._hasInitResultsBox) {
            this.resultsBox.remove();
        }

        this.resultsBox = null;
        this.resultsBox = null;
    }
});

Orb.createNamespace('DeskPRO.Agent.ElementHandler');

/**
 * Any wrapper that has 'nav ul' for tabs. The wrapper acts
 * as the context for data-tab-for
 */
DeskPRO.Agent.ElementHandler.TabBox = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	initPage: function() {
		var ul = $('nav ul', this.el).first();
		this.tabs = new DeskPRO.UI.SimpleTabs({
			triggerElements: $('li', ul),
			context: this.el
		});
	},

	destroy: function() {
		if (this.tabs) {
			this.tabs.destroy();
			this.tabs = null;
		}
		this.el = null;
	}
});

Orb.createNamespace('DeskPRO.Agent.ElementHandler');

/**
 * Any wrapper that has 'nav ul' for tabs. The wrapper acts
 * as the context for data-tab-for
 */
DeskPRO.Agent.ElementHandler.PersonSearchBox = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	initPage: function() {
		var self = this;

		this.termInput   = $('input.term', this.el);
		this.idInput     = $('input.person-id', this.el);
		this.resultsBox  = $('.person-search-box', this.el);
		this.resultsList = $('.results-list', this.resultsBox);

		this.termInput.on('focus', function() {
			self.open();
		});

		this.el.on('dp_hide', function() {
			self.close();
		});
	},


	/**
	 * Inits the search box to ensre it can be positioned properly
	 */
	_initResultsBox: function() {
		var self = this;

		if (this._hasInitResultsBox) return;
		this._hasInitResultsBox = true;

		this.tplHtml = DeskPRO_Window.util.getPlainTpl($('.user-row-tpl', this.el));

		//------------------------------
		// Update caller schedules the update requests
		//------------------------------

		var updateCaller;
		this.updateCaller = new DeskPRO.TouchCaller({
			timeout: 500,
			callback: this.updateResults,
			context: this
		});
		updateCaller = this.updateCaller;

		//------------------------------
		// Input events
		//------------------------------

		// Touch the timer so we will search in a few seconds,
		// or handle arrow and enter keys to select values in the list
		this.termInput.on('keydown', function(ev) {
			if (ev.keyCode == 13 /* enter key */) {

				ev.preventDefault();

				var current = $('li.on', self.resultsList);
				if (current.length) {
					var personId = current.data('person-id');
					var name  = $('.user-name', current).text().trim();
					var email = $('.user-email', current).text().trim();

					self.termInput.val(email);

					self.el.trigger('personsearchboxclick', [personId, name, email, self]);
				} else {
					var term = self.getTerm();
					self.el.trigger('personsearchboxclicknew', [term, self]);
				}

			} else if (ev.keyCode == 40 /* down key */ || ev.keyCode == 38 /* up key */) {

				ev.preventDefault();

				var dir = ev.keyCode == 40 ? 'down' : 'up';

				var current = $('li.on', self.resultsList);
				$('li', self.resultsList).removeClass('on');

				if (!current.length) {
					if (dir == 'down') {
						$('li', self.resultsList).first().addClass('on');
					} else {
						$('li', self.resultsList).last().addClass('on');
					}
				} else {
					if (dir == 'down') {
						var next = current.next('li');
						if (!next.length) {
							next = $('li', self.resultsList).first();
						}
					} else {
						var next = current.prev('li');
						if (!next.length) {
							next = $('li', self.resultsList).last();
						}
					}

					next.addClass('on');
				}
			}
		}).on('keyup', function(ev) {
			if (ev.keyCode == 13 /* enter key */) {
			} else if (ev.keyCode == 40 /* down key */ || ev.keyCode == 38 /* up key */) {
			} else {
				updateCaller.touch(self.getTerm());
			}
		}).on('change', function() { updateCaller.touch(self.getTerm()); });

		// Stop bubbling so it doesnt reach the document and close itself
		this.termInput.on('click', function(ev) { ev.stopPropagation(); });
		this.resultsBox.on('click', function(ev) { ev.stopPropagation(); });

		$(document).on('click', this.close.bind(this));
		$(this.termInput).closest('.doc-layer').on('click', this.close.bind(this));

		//------------------------------
		// Clicking on an item fires an event that
		// the page controller can listen to
		//------------------------------

		this.resultsList.on('click', 'li', function(ev) {
			ev.preventDefault();
			var personId = $(this).data('person-id');
			var name  = $('.user-name', this).text().trim();
			var email = $('.user-email', this).text().trim();

			self.el.trigger('personsearchboxclick', [personId, name, email, self]);
		});

		$('.create-user', this.resultsBox).on('click', function(ev) {
			ev.preventDefault();
			var term = self.getTerm();
			self.el.trigger('personsearchboxclicknew', [term, self]);
		});

		//------------------------------
		// Bound element: The element to show the results box under
		//------------------------------

		// Figure out the element the resultsbox is bound to
		this.boundEl = this.termInput;
		if (this.el.data('position-bound')) {
			var boundDesc = this.el.data('position-bound');
			if (boundDesc[0] == '#') {
				this.boundEl = $(boundDesc);
			} else if (boundDesc == '@self') {
				this.boundEl = this.el;
			} else if (boundDesc.test(/^@parent\((.*?)\)$/)) {
				var sel = boundDesc.match(/^@parent\((.*?)\)$/)[1];
				this.boundEl = this.el.closest(sel);
			} else {
				this.boundEl = $(boundDesc, this.el);
			}
		}

		if (!this.boundEl || !this.boundEl.length) {
			DP.console.error('Could not find position-bound element %s on %o', this.el.data('position-bound'), this);
		}

		this.resultsBox.detach().hide().appendTo('body');
	},


	/**
	 * Reset the box back to empty
	 */
	reset: function() {
		if (this.runningAjax) {
			this.runningAjax.abort();
			this.runningAjax = null;
		}
		this.termInput.val('');
		this.resultsList.empty();
	},


	/**
	 * Refresh the position of the search box relative to its bound element.
	 */
	refreshPosition: function() {
		var termPos = this.boundEl.offset();
		var termW   = this.boundEl.outerWidth();
		var termH   = this.boundEl.outerHeight();

		this.resultsBox.css({
			top: termPos.top + termH - 1,
			left: termPos.left,
			width: termW
		});
	},


	/**
	 * Get the search term in the box
	 *
	 * @return {String}
	 */
	getTerm: function() {
		return this.termInput.val().trim();
	},


	/**
	 * Sends the ajax request to find users that match the term in the search box
	 */
	updateResults: function() {

		var url = this.el.data('search-url');
		var term = this.getTerm();

		var postData = [];
		postData.push({
			name: this.el.data('search-param') || 'term',
			value: term
		});

		this.termInput.parent().addClass('loading');
		this.runningAjax = $.ajax({
			type: 'GET',
			url: url,
			data: postData,
			dataType: 'json',
			context: this,
			complete: function() {
				this.termInput.parent().removeClass('loading');
				this.runningAjax = null;
			},
			success: function(data) {
				var currentPersonId = parseInt($('li.on', this.resultsList).data('person-id')) || 0;
				this.resultsList.empty();

				Array.each(data, function(user) {
					var row = $(this.tplHtml);

					row.data('person-id', user.id);
					row.attr('person-id', user.id);
					row.addClass('person-' + user.id);

					if (currentPersonId && currentPersonId == parseInt(user.id)) {
						row.addClass('on');
						currentPersonId = false;
					}

					if (this.el.data('highlight-term')) {
						var term  = Orb.escapeHtml(this.getTerm());
						var name  = Orb.escapeHtml(user.name);
						var email = Orb.escapeHtml(user.email);

						term = (term+'').replace(/([\\\.\+\*\?\[\^\]\$\(\)\{\}\=\!\<\>\|\:])/g, "\\$1");
						name = name.replace( new RegExp( "(" + term + ")", 'gi' ), '<span class="highlight">$1</span>' );
						email = email.replace( new RegExp( "(" + term + ")", 'gi' ), '<span class="highlight">$1</span>' );

						$('.user-name', row).html(name);
						$('.user-email', row).html(email);

					} else {
						$('.user-name', row).text(user.name);
						$('.user-email', row).text(user.email);
					}

					if (!user.email || user.name == user.email) {
						$('address', row).hide();
					}

					this.resultsList.append(row);
				}, this);
			}
		});
	},


	/**
	 * Opens the results box
	 */
	open: function() {
		this._initResultsBox();

		this.refreshPosition();
		this.resultsBox.show();
	},


	/**
	 * Closes the results box and stops any updating stuff
	 */
	close: function() {
		if (this.resultsBox) {
			this.resultsBox.hide();
		}
	},


	/**
	 * Destroys the widget
	 */
	destroy: function() {
		if (this._hasInitResultsBox) {
			this.resultsBox.remove();
		}

		this.resultsBox = null;
		this.idInput = null;
		this.resultsBox = null;
	}
});

Orb.createNamespace('DeskPRO.Agent.ElementHandler');

/**
 * TODO: Refactor out common bits from PersonSearchBox
 *
 * Data options:
 * touch-focus: When the box is in focus, show the list immediately (even if no text and no change)
 * super-container: The selector that represents the master container. Whena click bubbles to this container, the box is closed. Defaults to document
 * position-bound: The selector of the element to bind the box position to. Defaults to this element.
 * search-url: The AJAX search url
 * search-term: The name of the 'term' to pass in the querystring
 * highlight-term: Attempt to highlight the searched text string in the results
 */
DeskPRO.Agent.ElementHandler.OrgSearchBox = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	initPage: function() {
		var self = this;
		this.el.data('org-search-box', this);

		this.termInput   = $('input.org-name', this.el);
		this.idInput     = $('input.org-id', this.el);
		this.resultsBox  = $('.person-search-box', this.el);
		this.resultsList = $('.results-list', this.resultsBox);

		// Always show results when the box is in focus
		this.termInput.on('focus', function() {
			self.open();
			if (self.el.data('touch-focus')) {
				// double touch forces an update
				self.updateCaller.touch(self.getTerm(), true);
			}
		});

		this.el.on('dp_hide', function() {
			self.close();
		});
	},


	/**
	 * Inits the search box to ensre it can be positioned properly
	 */
	_initResultsBox: function() {
		var self = this;

		if (this._hasInitResultsBox) return;
		this._hasInitResultsBox = true;

		this.tplHtml = DeskPRO_Window.util.getPlainTpl($('.user-row-tpl', this.el));

		//------------------------------
		// Update caller schedules the update requests
		//------------------------------

		var updateCaller;
		this.updateCaller = new DeskPRO.TouchCaller({
			timeout: 500,
			callback: this.updateResults,
			context: this
		});
		updateCaller = this.updateCaller;

		//------------------------------
		// Input events
		//------------------------------

		// Touch the timer so we will search in a few seconds
		this.termInput.on('keyup', function() { updateCaller.touch(self.getTerm()); }).on('change', function() { updateCaller.touch(self.getTerm()); });

		// Stop bubbling so it doesnt reach the document and close itself
		this.termInput.on('click', function(ev) { ev.stopPropagation(); });
		this.resultsBox.on('click', function(ev) { ev.stopPropagation(); });

		if (this.el.data('super-container')) {
			this.el.closest(this.el.data('super-container')).on('click', this.close.bind(this));
		} else {
			$(document).on('click', this.close.bind(this));
		}

		//------------------------------
		// Clicking on an item fires an event that
		// the page controller can listen to
		//------------------------------

		this.resultsList.on('click', 'li', function(ev) {
			ev.preventDefault();
			var orgId = $(this).data('org-id');
			var name  = $('.org-name', this).text().trim();

			self.termInput.val(name);
			self.idInput.val(orgId);
			self.el.removeClass('is-new').addClass('is-set');

			self.wasSet = true;
			self.close();

			self.el.trigger('orgsearchboxclick', [orgId, name, self]);
		});

		$('.create-org', this.resultsBox).on('click', function(ev) {
			ev.preventDefault();

			self.idInput.val('0');
			self.el.addClass('is-new').removeClass('is-set');

			self.wasSet = true;
			self.close();

			self.el.trigger('orgsearchboxcreate', [self.getTerm(), self]);
		});

		//------------------------------
		// Bound element: The element to show the results box under
		//------------------------------

		// Figure out the element the resultsbox is bound to
		this.boundEl = this.termInput;
		if (this.el.data('position-bound')) {
			var boundDesc = this.el.data('position-bound');
			if (boundDesc[0] == '#') {
				this.boundEl = $(boundDesc);
			} else if (boundDesc == '@self') {
				this.boundEl = this.el;
			} else if (boundDesc.test(/^@parent\((.*?)\)$/)) {
				var sel = boundDesc.match(/^@parent\((.*?)\)$/)[1];
				this.boundEl = this.el.closest(sel);
			} else {
				this.boundEl = $(boundDesc, this.el);
			}
		}

		if (!this.boundEl || !this.boundEl.length) {
			DP.console.error('Could not find position-bound element %s on %o', this.el.data('position-bound'), this);
		}

		this.resultsBox.detach().hide().appendTo('body');
	},


	/**
	 * Reset the box back to empty
	 */
	reset: function() {
		this.termInput.val('');
		this.resultsList.empty();
	},


	/**
	 * Refresh the position of the search box relative to its bound element.
	 */
	refreshPosition: function() {
		var termPos = this.boundEl.offset();
		var termW   = this.boundEl.outerWidth() + 6;
		var termH   = this.boundEl.outerHeight();

		this.resultsBox.css({
			top: termPos.top + termH - 1,
			left: termPos.left - 1,
			width: termW
		});
	},


	/**
	 * Get the search term in the box
	 *
	 * @return {String}
	 */
	getTerm: function() {
		return this.termInput.val().trim();
	},


	/**
	 * Sends the ajax request to find users that match the term in the search box
	 */
	updateResults: function() {

		var url = this.el.data('search-url');
		var term = this.getTerm();

		var postData = [];
		postData.push({
			name: this.el.data('search-param') || 'term',
			value: term
		});

		this.runningAjax = $.ajax({
			type: 'GET',
			url: url,
			data: postData,
			dataType: 'json',
			context: this,
			complete: function() {
				this.runningAjax = null;
			},
			success: function(data) {
				this.resultsList.empty();

				Array.each(data, function(org) {
					var row = $(this.tplHtml);

					row.data('org-id', org.id);
					row.attr('org-id', org.id);

					if (this.el.data('highlight-term')) {
						var term  = Orb.escapeHtml(this.getTerm());
						var name  = Orb.escapeHtml(org.name);

						term = (term+'').replace(/([\\\.\+\*\?\[\^\]\$\(\)\{\}\=\!\<\>\|\:])/g, "\\$1");
						name = name.replace( new RegExp( "(" + term + ")", 'gi' ), '<span class="highlight">$1</span>' );

						$('.org-name', row).html(name);

					} else {
						$('.org-name', row).text(org.name);
					}

					this.resultsList.append(row);
				}, this);
			}
		});
	},


	/**
	 * Opens the results box
	 */
	open: function() {
		this.origValue = this.termInput.val();
		this.wasSet = false;

		this._initResultsBox();

		this.refreshPosition();
		this.resultsBox.show();
	},


	/**
	 * Closes the results box and stops any updating stuff
	 */
	close: function() {
		if (this.resultsBox) {
			this.resultsBox.hide();
		}

		if (!this.idInput) {
			return;
		}

		if (!this.wasSet) {
			if (!this.termInput.val().trim().length) {
				this.idInput.val('0');
				this.el.removeClass('is-new').removeClass('is-set');
				this.el.trigger('orgsearchboxcleared', [this]);
			} else {
				this.termInput.val(this.origValue);
				this.el.trigger('orgsearchreverted', [this.getTerm(), this]);
			}
		}
	},


	/**
	 * Destroys the widget
	 */
	destroy: function() {
		if (this._hasInitResultsBox) {
			this.resultsBox.remove();
		}

		this.resultsBox = null;
		this.idInput = null;
		this.resultsBox = null;
	}
});

Orb.createNamespace('DeskPRO.Agent.ElementHandler');

/**
 * Any wrapper that has 'nav ul' for tabs. The wrapper acts
 * as the context for data-tab-for
 */
DeskPRO.Agent.ElementHandler.TicketSearchBox = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	initPage: function() {
		var self = this;

		this.termInput   = $('input.term', this.el);
		this.idInput     = $('input.ticket-id', this.el);
		this.resultsBox  = $('.ticket-search-box', this.el);
		this.resultsList = $('.results-list', this.resultsBox);

		this.termInput.on('focus', function() {
			self.open();
		});

		this.el.on('dp_hide', function() {
			self.close();
		});
	},


	/**
	 * Inits the search box to ensure it can be positioned properly
	 */
	_initResultsBox: function() {
		var self = this;

		if (this._hasInitResultsBox) return;
		this._hasInitResultsBox = true;

		this.tplHtml = DeskPRO_Window.util.getPlainTpl($('.ticket-row-tpl', this.el));

		//------------------------------
		// Update caller schedules the update requests
		//------------------------------

		var updateCaller;
		this.updateCaller = new DeskPRO.TouchCaller({
			timeout: 500,
			callback: this.updateResults,
			context: this
		});
		updateCaller = this.updateCaller;

		//------------------------------
		// Input events
		//------------------------------

		// Touch the timer so we will search in a few seconds,
		// or handle arrow and enter keys to select values in the list
		this.termInput.on('keypress', function(ev) {
			if (ev.keyCode == 13 /* enter key */) {

				ev.preventDefault();

				var current = $('li.on', self.resultsList);
				if (current.length) {
					var ticketId = current.data('ticket-id');
					var subject  = $('.ticket-subject', current).text().trim();

					self.termInput.val(subject);

					self.el.trigger('ticketsearchboxclick', [ticketId, subject, self]);
				}

			} else if (ev.keyCode == 40 /* down key */ || ev.keyCode == 38 /* up key */) {

				ev.preventDefault();

				var dir = ev.keyCode == 40 ? 'down' : 'up';

				var current = $('li.on', self.resultsList);
				$('li', self.resultsList).removeClass('on');

				if (!current.length) {
					if (dir == 'down') {
						$('li', self.resultsList).first().addClass('on');
					} else {
						$('li', self.resultsList).last().addClass('on');
					}
				} else {
					if (dir == 'down') {
						var next = current.next('li');
						if (!next.length) {
							next = $('li', self.resultsList).first();
						}
					} else {
						var next = current.prev('li');
						if (!next.length) {
							next = $('li', self.resultsList).last();
						}
					}

					next.addClass('on');
				}
			} else {
				updateCaller.touch(self.getTerm());
			}
		}).on('change', function() { updateCaller.touch(self.getTerm()); });

		// Stop bubbling so it doesnt reach the document and close itself
		this.termInput.on('click', function(ev) { ev.stopPropagation(); });
		this.resultsBox.on('click', function(ev) { ev.stopPropagation(); });

		$(document).on('click', this.close.bind(this));
		$(this.termInput).closest('.doc-layer').on('click', this.close.bind(this));

		//------------------------------
		// Clicking on an item fires an event that
		// the page controller can listen to
		//------------------------------

		this.resultsList.on('click', 'li', function(ev) {
			ev.preventDefault();
			var ticketId = $(this).data('ticket-id');
			var subject = $('.ticket-subject', this).text().trim();

			self.el.trigger('ticketsearchboxclick', [ticketId, subject, self]);
		});

		//------------------------------
		// Bound element: The element to show the results box under
		//------------------------------

		// Figure out the element the resultsbox is bound to
		this.boundEl = this.termInput;
		if (this.el.data('position-bound')) {
			var boundDesc = this.el.data('position-bound');
			if (boundDesc[0] == '#') {
				this.boundEl = $(boundDesc);
			} else if (boundDesc == '@self') {
				this.boundEl = this.el;
			} else if (boundDesc.test(/^@parent\((.*?)\)$/)) {
				var sel = boundDesc.match(/^@parent\((.*?)\)$/)[1];
				this.boundEl = this.el.closest(sel);
			} else {
				this.boundEl = $(boundDesc, this.el);
			}
		}

		if (!this.boundEl || !this.boundEl.length) {
			DP.console.error('Could not find position-bound element %s on %o', this.el.data('position-bound'), this);
		}

		this.resultsBox.detach().hide().appendTo('body');
	},


	/**
	 * Reset the box back to empty
	 */
	reset: function() {
		if (this.runningAjax) {
			this.runningAjax.abort();
			this.runningAjax = null;
		}
		this.termInput.val('');
		this.resultsList.empty();
	},


	/**
	 * Refresh the position of the search box relative to its bound element.
	 */
	refreshPosition: function() {
		var termPos = this.boundEl.offset();
		var termW   = this.boundEl.outerWidth();
		var termH   = this.boundEl.outerHeight();

		this.resultsBox.css({
			top: termPos.top + termH - 1,
			left: termPos.left,
			width: termW
		});
	},


	/**
	 * Get the search term in the box
	 *
	 * @return {String}
	 */
	getTerm: function() {
		return this.termInput.val().trim();
	},


	/**
	 * Sends the ajax request to find tickets that match the term in the search box
	 */
	updateResults: function() {

		var url = this.el.data('search-url');
		var term = this.getTerm();

		var postData = [];
		postData.push({
			name: this.el.data('search-param') || 'term',
			value: term
		});

		this.termInput.parent().addClass('loading');
		this.runningAjax = $.ajax({
			type: 'GET',
			url: url,
			data: postData,
			dataType: 'json',
			context: this,
			complete: function() {
				this.termInput.parent().removeClass('loading');
				this.runningAjax = null;
			},
			success: function(data) {
				var currentTicketId = parseInt($('li.on', this.resultsList).data('ticket-id')) || 0;
				this.resultsList.empty();

				Array.each(data, function(ticket) {
					var row = $(this.tplHtml);

					row.data('ticket-id', ticket.id);
					row.attr('ticket-id', ticket.id);
					row.addClass('ticket-' + ticket.id);

					if (ticket.status == 'awaiting_agent') {
						row.find('.ticket-status').addClass('awaiting_agent').text('Awaiting Agent');
					} else if (ticket.status == 'awaiting_user') {
						row.find('.ticket-status').addClass('awaiting_user').text('Awaiting User');
					} else if (ticket.status == 'resolved') {
						row.find('.ticket-status').addClass('resolved').text('Resolved');
					} else if (ticket.status == 'closed') {
						row.find('.ticket-status').addClass('closed').text('Closed');
					} else {
						row.find('.ticket-status').remove();
					}

					var d = new Date(ticket.last_activity*1000);
					row.find('.ticket-time').attr('datetime', d.toISOString()).timeago();

					if (currentTicketId && currentTicketId == parseInt(ticket.id)) {
						row.addClass('on');
						currentTicketId = false;
					}

					if (this.el.data('highlight-term')) {
						var term  = Orb.escapeHtml(this.getTerm());
						var subject = Orb.escapeHtml(ticket.subject);

						term = (term+'').replace(/([\\\.\+\*\?\[\^\]\$\(\)\{\}\=\!\<\>\|\:])/g, "\\$1");
						subject = subject.replace( new RegExp( "(" + term + ")", 'gi' ), '<span class="highlight">$1</span>' );

						$('.ticket-subject', row).html(subject);
					} else {
						$('.ticket-subject', row).text(ticket.subject);
					}

					$('.ticket-id', row).text(ticket.id);

					this.resultsList.append(row);
				}, this);
			}
		});
	},


	/**
	 * Opens the results box
	 */
	open: function() {
		this._initResultsBox();

		this.refreshPosition();
		this.resultsBox.show();
	},


	/**
	 * Closes the results box and stops any updating stuff
	 */
	close: function() {
		this.resultsBox.hide();
	},


	/**
	 * Destroys the widget
	 */
	destroy: function() {
		if (this._hasInitResultsBox) {
			this.resultsBox.remove();
		}

		this.resultsBox = null;
		this.idInput = null;
		this.resultsBox = null;
	}
});

Orb.createNamespace('DeskPRO.Agent.ElementHandler');

DeskPRO.Agent.ElementHandler.PhoneCountryCode = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	initPage: function() {
		this.optionsList = $(this.el.data('options-list'));

		var options = [];
		$('li', this.optionsList).each(function(){
			options.push({
				country_code: $(this).data('country-code'),
				value: $(this).data('calling-code'),
				label: $(this).html()
			});
		});

		var countryCodeSel = $('.country-code', this.el);
		var flagEl = $('.icon-flag', this.el);

		var updateIcon = function() {
			if (countryCodeSel.is('.cancel-next-update')) {
				countryCodeSel.removeClass('cancel-next-update');
				return;
			}

			var val = countryCodeSel.val().trim();
			var cc = null;
			if (val) {
				Array.each(options, function(opt) {
					if ((opt.value+'') == (val+'')) {
						cc = opt.country_code;
					}
				});
			}

			if (cc) {
				flagEl.attr('class', '').addClass('icon-flag').addClass('icon-flag-' + cc.toLowerCase()).show();
			} else {
				flagEl.hide();
			}

			countryCodeSel.autocomplete('close');
		};

		countryCodeSel.autocomplete({
			minLength: 0,
			source: function(req, callback) {
				var term = req.term.trim();

				if (term === '') {
					return options;
				}

				var ret = [];
				Array.each(options, function(opt) {
					if ((opt.value+'').indexOf(term) === 0) {
						ret.push(opt);
					}
				});
				callback(ret);
			},
			delay: 0
		})
		.on('change', updateIcon)
		.data( "autocomplete" )._renderItem = function(ul, item) {
			return $("<li></li>")
				.addClass('ui-menu-item')
				.data("item.autocomplete", item)
				.html(item.label)
				.appendTo(ul);
		};

		updateIcon();
	}
});

Orb.createNamespace('DeskPRO.Agent.ElementHandler');

DeskPRO.Agent.ElementHandler.QuickSearch = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	initPage: function() {
		var searchBox  = $('#dp_search_box');
		var searchHelp = $('#dp_header_search_help');
		var searchUrl  = this.el.data('search-url');
		var listWrap   = $('#dp_search_box_list_wrap');
		var list       = $('#dp_search_box_list');
		var eatClick   = false;

		searchBox.data('handler', {
			setSearch: function(term) {
				searchBox.focus();
				searchBox.val('');
				searchBox.val(term);
				updateSearch(true);
			}
		});

		list.on('click', '.show-more', function(ev) {
			Orb.cancelEvent(ev);
			var type = $(this).closest('.title').data('type');
			if (type) {
				list.find('li.type-' + type).show().addClass('dp-vis');
			}
			$(this).hide();

			searchBox.addClass('dp-focus');
			searchBox.removeClass('dp-stick-open');
			searchBox.focus();
		});

		listWrap.on('mousedown', function(ev) {
			$(this).addClass('dp-focus');
			searchBox.focus();
			Orb.cancelEvent(ev);
			ev.stopImmediatePropagation();
		});

		list.on('mousedown', function(ev) {
			$(this).addClass('dp-focus');
			searchBox.focus();
			Orb.cancelEvent(ev);
			ev.stopImmediatePropagation();
		});

		//------------------------------
		// Helpbox trigger
		//------------------------------

		$('#dp_search_box_help_trigger').on('mousedown', function(ev) {
			Orb.cancelEvent(ev);
			searchBox.addClass('dp-stick-open dp-regaining-focus');
			searchHelp.addClass('active');

			var closeFn = function() {
				searchBox.addClass('dp-focus');
				searchBox.removeClass('dp-stick-open');
				searchHelp.removeClass('active');
				searchBox.focus();

				window.setTimeout(function() {
					searchBox.removeClass('dp-regaining-focus');
				}, 200);
			};

			if (!searchHelp.data('has-init')) {
				searchHelp.on('click', function(ev) {
					ev.stopPropagation();
				});
			}

			Orb.shimClickCallback(closeFn, 'zindex-chrome0');
		});

		$('#search_icons_nav').find('li').on('click', function(ev) {
			Orb.cancelEvent(ev);
			var section = $(this).data('target-section');
			DeskPRO_Window.switchToSection(section);
			DeskPRO_Window.sections[section].getSectionElement().find('.pane-tabs').find('[data-tab-id="pane-content-search"]').click();
			Orb.shimClickCallbackPop();
		});

		//------------------------------
		// Expanding the search box
		//------------------------------

		var lastWinW = 0;
		searchBox.on('focus', function() {

			var winW = $(window).width();
			$(this).addClass('dp-focus');
			if ($(this).hasClass('expanded')) {

				if (list.find('li')[0]) {
					openResults();
				}

				return;
			}

			var txt, wrap, w, addToW;

			addToW = 125;
			if (!$('#dp_header_logo_wrap').is(':visible')) {
				addToW = 0;
			}

			$('#dp_header_logo_wrap').hide();

			wrap = $('#dp_header_search_wrap');
			if (wrap.data('orig-width') && winW == lastWinW) {
				w = wrap.data('orig-width');
			} else {
				w = wrap.width();
				wrap.data('orig-width', w);
			}
			wrap.width(w + addToW);

			if (addToW) {
				wrap.css('margin-right', '8px');
			}

			txt = $(this);
			if (txt.data('orig-width') && winW == lastWinW) {
				w = txt.data('orig-width');
			} else {
				w = txt.width();
				txt.data('orig-width', w);
			}
			txt.addClass('expanded');

			if (addToW) {
				txt.stop();
				txt.animate({ width: w+addToW }, 300, function() {
					$('#dp_search_box_help_trigger').show();
					$('#dp_search_box_help_trigger').css('opacity', 0);
					$('#dp_search_box_help_trigger').animate({opacity: 100}, 1500);
				});
			} else {
				$('#dp_search_box_help_trigger').show();
				$('#dp_search_box_help_trigger').css('opacity', 0);
				$('#dp_search_box_help_trigger').stop();
				$('#dp_search_box_help_trigger').animate({opacity: 100}, 1500);
			}

		}).on('blur', function() {
			$(this).removeClass('dp-focus');

			if ($(this).hasClass('dp-regaining-focus')) {
				return;
			}

			window.setTimeout(function() {
				if (!searchBox.hasClass('dp-focus')) {
					closeResults();
				}

				if (searchBox.val().length || searchBox.hasClass('dp-stick-open')) {
					return;
				}

				var txt, wrap, w;

				wrap = $('#dp_header_search_wrap');
				wrap.width(wrap.data('orig-width'));
				wrap.css('margin-right', '0');

				txt = searchBox;
				txt.stop();
				$('#dp_search_box_help_trigger').stop();
				txt.width(txt.data('orig-width')).removeClass('expanded');
				$('#dp_search_box_help_trigger').hide();

				$('#dp_header_logo_wrap').show();
			}, 130);
		});

		//------------------------------
		// Updating and selecting search results
		//------------------------------

		var clearResults = function() {
			list.empty();
		};

		var closeResults = function() {
			listWrap.hide();
		};

		var openResults = function() {
			listWrap.show();
		};

		var setResults = function(results, clear) {
			var self = this;
			clearResults();
			if (!results) {
				closeResults();
				return;
			}

			var count = 0;

			Object.each(results, function(typeResults, type) {

				var sectionEl, listEl, resultEl, hasMore, res, subList, subResultEl, subRes;

				if (!typeResults || !typeResults.length || !$.isArray(typeResults)) {
					return;
				}

				sectionEl = $(DeskPRO_Window.util.getPlainTpl('#dp_header_search_row_title_tpl'));
				sectionEl.data('type', type);
				sectionEl.find('.type-icon').addClass($('#dp_header_search_row_title_tpl').data('icon-' + type) || 'icon-caret-right');
				sectionEl.find('.type-title').text($('#dp_header_search_row_title_tpl').data('title-' + type) || type);
				sectionEl.find('.show-more').hide();
				sectionEl.appendTo(list);

				hasMore = false;
				for (var ri = 0; ri < typeResults.length; ri++) {
					count++;
					res = typeResults[ri];

					resultEl = $(DeskPRO_Window.util.getPlainTpl('#dp_header_search_row_tpl'))
					resultEl.addClass(type + ' ' + 'type-' + type).data('type', type);
					resultEl.find('.row-id').text(res.id)
					resultEl.find('.row-title').html(res.title);
					resultEl.data('route', res.route).attr('data-route', res.route);
					resultEl.data('route-notabreload', '1').attr('data-route-notabreload', '1');

					if (res.subs) {
						subList = $('<ul></ul>');
						for (var sub_ri = 0; sub_ri < res.subs.length; sub_ri++) {
							subRes = res.subs[sub_ri];
							subResultEl = $(DeskPRO_Window.util.getPlainTpl('#dp_header_search_row_tpl'))
							subResultEl.addClass(subRes.type + ' ' + 'type-' + subRes.type).data('type', subRes.type);
							subResultEl.find('.row-id').text(subRes.id)
							subResultEl.find('.row-title').html(subRes.title);
							subResultEl.data('route', subRes.route).attr('data-route', subRes.route);
							subResultEl.data('route-notabreload', '1').attr('data-route-notabreload', '1');
							subResultEl.appendTo(subList);
						}

						subList.hide();
						subList.appendTo(resultEl);

						resultEl.find('.row-title').first().before('<span class="sublist-toggle"></span>');
					}


					if (ri >= 5) {
						resultEl.hide();
						hasMore = true;
					} else {
						resultEl.addClass('dp-vis')
					}

					resultEl.appendTo(list);
				}
				if (hasMore) {
					var showMoreEl = sectionEl.find('.show-more');
					Orb.phraseTextEl(showMoreEl, {count: typeResults.length - 5});
					showMoreEl.show();
				}
			}, this);

			list.find('.sublist-toggle').on('click', function(ev) {
				Orb.cancelEvent(ev);
				eatClick = true;

				$(this).addClass('dp-focus');
				searchBox.focus();

				if ($(this).hasClass('expanded')) {
					$(this).removeClass('expanded');
					$(this).closest('li').find('ul').hide();
				} else {
					$(this).addClass('expanded');
					$(this).closest('li').find('ul').show();
				}
			});

			list.find('[data-route]').on('click', function(ev) {
				console.log("CLick");
				Orb.cancelEvent(ev);
				ev.stopImmediatePropagation();
				DeskPRO_Window.runPageRouteFromElement($(this));
				searchBox.blur();
				closeResults();
				eatClick = false;
			});

			if (count) {
				openResults();
			} else {
				closeResults();
			}
		};

		var updateSearch = function(force) {
			if (updateTimeout) {
				window.clearTimeout(updateTimeout);
				updateTimeout = null;
			}

			if (runningAjax) {
				if (force) {
					runningAjax.abort();
				} else {
					return;
				}
			}

			var input = $.trim(searchBox.val());

			if (input === "") {
				clearResults();
				closeResults();
			}

			if (input == prevInput && !force) {
				return;
			}

			prevInput = input;

			$('#dp_search_box_help_trigger').hide();
			$('#dp_search_box_loading').show();
			$.ajax({
				url: searchUrl,
				data: { q: input },
				type: 'GET',
				dataType: 'json',
				complete: function() {
					$('#dp_search_box_help_trigger').show();
					$('#dp_search_box_loading').hide();

					runningAjax = null;
					if (updateTimeout) {
						window.clearTimeout(updateTimeout);
						updateTimeout = null;
					}
					updateSearch(); // re-run, nothing will happen if no changes
				},
				success: function(results) {
					setResults(results);
				}
			});
		};

		var runningAjax = null;
		var eatNext = false;
		var prevInput = null;
		var updateTimeout = null;
		searchBox.on('keydown', function(ev) {
			if (ev.keyCode == 13 /* enter key */) {
				Orb.cancelEvent(ev);
				var current = list.find('.dp-cursor');
				eatNext = true;

				// Selected item
				if (current[0]) {
					DeskPRO_Window.runPageRouteFromElement(current);
					searchBox.blur();
					closeResults();

				// Or else just re-run the current search
				} else {
					updateSearch(true);
				}

			} else if (ev.keyCode == 40 /* down key */ || ev.keyCode == 38 /* up key */) {
				Orb.cancelEvent(ev);
				eatNext = true;
				var current = list.find('.dp-cursor');
				current.removeClass('dp-cursor');
				var dir = ev.keyCode == 40 ? 'down' : 'up';
				var next;

				if (!current.length) {
					if (dir == 'down') {
						list.find('.dp-vis').first().addClass('dp-cursor');
					} else {
						list.find('.dp-vis').last().addClass('dp-cursor');
					}
				} else {
					if (dir == 'down') {
						next = current.next('li.dp-vis');
						if (!next.length) {
							// could be a title to skip
							next = current.next().next('li.dp-vis');
						}
						if (!next.length) {
							next = list.find('.dp-vis').first().addClass('dp-cursor');
						}
					} else {
						next = current.prev('li.dp-vis');
						if (!next.length) {
							// could be a title to skip
							next = current.prev().prev('li.dp-vis');
						}
						if (!next.length) {
							next = list.find('.dp-vis').last().addClass('dp-cursor');
						}
					}

					next.addClass('dp-cursor');
				}
			} else {
				if (!updateTimeout) {
					updateTimeout = window.setTimeout(updateSearch, 520);
				}
			}
		}).on('keypress', function(ev) {
			if (!updateTimeout) {
				updateTimeout = window.setTimeout(updateSearch, 520);
			}
		});
	}
});
Orb.createNamespace('DeskPRO.Agent.ElementHandler');

DeskPRO.Agent.ElementHandler.PasswordPrompt = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var self = this;

		this.el.bind('dp_open', function(ev, options) {
			self.open(options);
		});
	},

	_initOverlay: function() {
		var self = this;
		if (this._hasInit) return;
		this._hasInit = true;

		this.el.detach().hide().appendTo('body');

		this.passwordField = $('input.password-input', this.el);
		this.explainEl     = $('.explain-wrap', this.el);

		this.backdropEl = $('<div class="backdrop dp-overlay-backdrop" />');
		this.backdropEl.css('z-index', '40000').hide().appendTo('body');

		this.backdropEl.on('click', (function(ev) {
			ev.stopPropagation();
			this.close();
		}).bind(this));

		$('header .close-trigger', this.el).on('click', (function(ev) {
			ev.stopPropagation();
			ev.preventDefault();
			this.close();
		}).bind(this));

		$('button.continue-trigger').on('click', function() {
			var password = self.passwordField.val();
			if (password === "") {
				return;
			}

			$.ajax({
				url: BASE_URL + 'agent/password-confirm-code.json',
				type: 'POST',
				dataType: 'json',
				data: { password: password },
				context: self,
				success: function(res) {
					if (res.invalid) {
						this.failedFn(this);
						this.el.addClass('with-error');
					} else {
						this.cancelFn = function() {};// dont fire for success
						this.close();
						this.successFn(res.code, this);
					}
				}
			});
		});
	},

	open: function(options) {
		this.successFn = options.success || function() {};
		this.failedFn  = options.failed  || function() {};
		this.cancelFn  = options.cancel  || function() {};

		this._initOverlay();

		if (options.explain) {
			if (typeof options.explain === "string") {
				this.explainEl.text(options.explain).show();
			} else {
				this.explainEl.append(options.explain).show();
			}
		}

		this.updatePositions();
		this.el.show();
	},

	updatePositions: function() {

		var elW = this.el.width();
		var elH = this.el.height();

		var pageW = $(window).width();
		var pageH = $(window).height();

		this.el.css({
			top: (pageH-elH) / 2,
			left: (pageW-elW) / 2
		});
	},

	close: function() {
		this.cancelFn();

		this.el.hide();
		this.backdropEl.hide();
	},

	reset: function() {
		this.cancelFn();

		this.cancelFn  = null;
		this.successFn = null;
		this.failedFn  = null;

		this.passwordField.val('');
		this.explainEl.empty().hide();
		this.el.removeClass('with-error');
	}
});

Orb.createNamespace('DeskPRO.Agent.ElementHandler');

DeskPRO.Agent.ElementHandler.TimezoneSwitch = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var self = this;

		this.el.bind('dp_open', function(ev, options) {
			self.open(options);
		});
	},

	_initOverlay: function() {
		var self = this;
		if (this._hasInit) return;
		this._hasInit = true;

		var now = new Date();
		var hour = now.getHours();
		var ampm = 'am';
		if (hour > 12) {
			hour -= 12;
			ampm = 'pm';
		}
		var min = now.getMinutes();
		if (min < 10) {
			min = '0' + min;
		}
		var time = hour + ':' + min + ' ' + ampm;

		this.el.find('.js_time').text(time);

		this.el.detach().hide().appendTo('body');

		this.backdropEl = $('<div class="backdrop dp-overlay-backdrop" />');
		this.backdropEl.css('z-index', '40000').hide().appendTo('body');

		this.backdropEl.on('click', (function(ev) {
			ev.stopPropagation();
			this.close();
		}).bind(this));

		$('header .close-trigger', this.el).on('click', (function(ev) {
			ev.stopPropagation();
			ev.preventDefault();
			this.close();
		}).bind(this));

		var tzField = $('select.timezone', this.el);
		DP.select(tzField);

		this.el.find('button.dismiss-trigger').on('click', function() {
			$.ajax({
				url:  BASE_URL + 'agent/misc/ajax-save-prefs',
				type: 'POST',
				dataType: 'json',
				data: [{ name: 'prefs[agent.ui.tz_detect_dismiss]', value: DESKPRO_TIME_OUT_OF_SYNC }]
			});

			self.close();
			return;
		});

		this.el.find('button.continue-trigger').on('click', function() {
			var tz = tzField.find('option:selected').val();

			if (!tz) {
				self.close();
				return;
			}

			$.ajax({
				url: BASE_URL + 'agent/settings/profile/update-timezone.json',
				type: 'POST',
				dataType: 'json',
				data: { timezone: tz },
				context: self,
				success: function(res) {
					self.close();
					DeskPRO_Window.util.reloadInterface();
				}
			});
		});
	},

	open: function(options) {
		this._initOverlay();
		this.updatePositions();
		this.el.show();
	},

	updatePositions: function() {

		var elW = this.el.width();
		var elH = this.el.height();

		var pageW = $(window).width();
		var pageH = $(window).height();

		this.el.css({
			top: (pageH-elH) / 2,
			left: (pageW-elW) / 2
		});
	},

	close: function() {
		this.el.hide();
		this.backdropEl.hide();
	}
});

Orb.createNamespace('DeskPRO.Admin.ElementHandler');

DeskPRO.Admin.ElementHandler.RadioExpander = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var self = this;

		var groupClass  = this.el.data('group-class');
		var expandClass = this.el.data('expand-class');
		var radios = $('.option-trigger', this.el);

		var firstSubRadio = this.el.data('select-first-subradio');
		var currentGroup = null;

		function switchtoradio(radio) {
			self.el.find('.' + groupClass + '.on').removeClass('on');

			if (currentGroup && expandClass) {
				$('.' + expandClass, currentGroup).hide();
			}

			var group = radio.closest('.' + groupClass).addClass('on');
			if (expandClass) {
				$('.' + expandClass, group).show();
			}
			currentGroup = group;

			if (firstSubRadio) {
				group.find(':radio.' + firstSubRadio).first().click();
			}
		}

		$(':radio.option-trigger:checked', this.el).each(function() {
			switchtoradio($(this));
		});

		this.el.on('click', ':radio.option-trigger', function() {
			switchtoradio($(this));
		});

		this.el.on('click', '.' + groupClass + ':not(.on)', function(e) {
			var radio = $(this).find('.option-trigger');
			if (radio.length) {
				radio.prop('checked', 'checked');
				switchtoradio(radio);

				if (!$(e.target).is('input[type=radio]'))
				{
					// clicked something outside radio - need to manually fire change event
					radio.change();
				}
			}
		});
	}
});

Orb.createNamespace('DeskPRO.Agent.ElementHandler');

DeskPRO.Agent.ElementHandler.FirstLogin = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	initPage: function() {
		var self = this;
		var el = this.el;
		window.FIRST_LOGIN_WINDOW = this;

		DeskPRO_Window.util.fileupload(this.el.find('.profile-picture'));
		this.el.find('.profile-picture').bind('fileuploadadd', function() {
			$('.files', el).empty();
		});


		$('<div style="position:absolute;top:0;left:0;right:0;bottom:0;z-index:51001;background-color:rgba(255,255,255,0.55);"></div>').appendTo('body');

		this.el.find('.save-trigger').on('click', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			var name = el.find('input.name').val().trim();
			if (!name) {
				alert('Please enter your name');
				return;
			}

			$(this).after($('<i class="flat-spinner"></i>'));
			$(this).hide();

			$.ajax({
				url: el.find('form').attr('action'),
				type: 'POST',
				data: {
					new_blob_id: el.find('input.new_blob_id').val(),
					name: el.find('input.name').val(),
					timezone: el.find('select.timezone').val()
				},
				dataType: 'json',
				complete: function() {
					window.location.reload(false);
				}
			});
		});

		DP.select(this.el.find('select'));

		this.updatePositions();
		this.el.show();
	},

	updatePositions: function() {
		var left = ($(window).width() / 2) - (this.el.outerWidth() / 2);
		this.el.css('left', left);

		this.el.show();
	}
});

Orb.createNamespace('DeskPRO.Agent.ElementHandler');

DeskPRO.Agent.ElementHandler.TwitterFeed = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		this.twitterUsername = this.el.data('twitter-username');
		this.tpl = DeskPRO_Window.util.getPlainTpl($('.twitter-list-item-tpl', this.el));
		this.list = $('.twitter-list', this.el);
		this.limit = parseInt(this.el.data('tweet-limit')) || 5;
	},

	initPage: function() {
		var ify = function() {
			return {
			  entities: function (t) {
				return t.replace(/(&[a-z0-9]+;)/g, function (m) {
				  return ENTITIES[m];
				});
			  },
			  link: function(t) {
				return t.replace(/[a-z]+:\/\/([a-z0-9-_]+\.[a-z0-9-_:~\+#%&\?\/.=]+[^:\.,\)\s*$])/ig, function(m, link) {
				  return '<a title="' + m + '" href="' + m + '">' + ((link.length > 36) ? link.substr(0, 35) + '&hellip;' : link) + '</a>';
				});
			  },
			  at: function(t) {
				return t.replace(/(^|[^\w]+)\@([a-zA-Z0-9_]{1,15}(\/[a-zA-Z0-9-_]+)*)/g, function(m, m1, m2) {
				  return m1 + '@<a href="http://twitter.com/' + m2 + '">' + m2 + '</a>';
				});
			  },
			  hash: function(t) {
				return t.replace(/(^|[^&\w'"]+)\#([a-zA-Z0-9_^"^<]+)/g, function(m, m1, m2) {
				  return m.substr(-1) === '"' || m.substr(-1) == '<' ? m : m1 + '#<a href="http://search.twitter.com/search?q=%23' + m2 + '">' + m2 + '</a>';
				});
			  },
			  clean: function(tweet) {
				return this.hash(this.at(this.link(tweet)));
			  }
			};
		}();

		$.ajax({
			url: 'http://api.twitter.com/1/statuses/user_timeline.json?screen_name=' + this.twitterUsername,
			dataType: 'jsonp',
			context: this,
			success: function(data) {
				Array.each(data, function(tweet, index) {
					if (index > this.limit) {
						return false;
					}

					var item = $(this.tpl);
					var text = tweet.text;
					text = ify.clean(text);

					$('.tweet', item).html(text);

					this.list.append(item);

					this.el.show();
				}, this);
			}
		});
	}
});

Orb.createNamespace('DeskPRO.Agent.SourcePane');

/**
 * A SearchForm represents an entire source pane search form:
 * - Many inputs
 * - Many panels
 *
 * @type {Orb.Class}
 */
DeskPRO.Agent.SourcePane.SearchForm = new Orb.Class({
	Implements: [Orb.Util.Events],

	initialize: function(el) {
		var self = this;
		this.el = el;
		this.origHtml = el.html();
		this.hasInit = false;

		this.formPanels = [];
		this.widgets = [];

		this.el.addClass('dp-with-activate-listener');
		this.el.on('dp_activated', function() {
			self.initPanel();
		});
	},

	reset: function() {
		this.el.html(this.origHtml);
		Array.each(this.formPanels, function(formPanel) {
			formPanel.destroy();
		});
		this.formPanels = [];
		this.widgets = [];
		this.hasInit = false;
		this.initPanel();
	},

	getFormData: function() {
		var postData = [];
		var visitedEls = [];

		postData = Orb.serializeFormElements(this.el.find('.add-to-search'), visitedEls);
		Array.each(this.formPanels, function(panel) {
			postData = postData.append(Orb.serializeFormElements(panel.el.find('.add-to-search'), visitedEls));
		});

		return postData;
	},

	initPanel: function() {
		if (this.hasInit) return;
		this.hasInit = true;

		var self = this;

		this.el.find('.trigger-open-panel').each(function() {
			if ($(this).hasClass('has-init-trigger-open-panel')) {
				return;
			}
			$(this).addClass('has-init-trigger-open-panel');
			var panelTrigger = $(this);
			var panelEl      = self.el.find('.' + panelTrigger.data('panel-id')).first();
			var panelSummary = self.el.find(panelTrigger.data('target-summary')).first();
			var panel        = new DeskPRO.Agent.SourcePane.SearchFormPanel(panelEl);

			panel.targetSummaryEl = panelSummary;

			panelTrigger.on('click', function(ev) {
				Orb.cancelEvent(ev);
				panel.open(this);
			});

			self.formPanels.push(panel);
		});

		var submitBtn = this.el.find('.trigger-submit-search').on('click', function(ev) {
			Orb.cancelEvent(ev);
			var postData = self.getFormData();

			DeskPRO_Window.loadListPane(
				$(this).data('search-url'),
				{postData: postData}
			);
		});
		submitBtn = submitBtn.first();

		this.el.find('.reset-form-trigger').on('click', function(ev) {
			Orb.cancelEvent(ev);
			self.reset();
		});

		this.el.find('input[type="text"]').on('keypress', function(ev) {
			if (ev.keyCode == 13 /* enter key */) {
				submitBtn.click();
			}
		});

		this.initStandardFormElements(this.el);
	},


	/**
	 * Inits standard form elements and controls
	 *
	 * @param context
	 */
	initStandardFormElements: function(context) {
		var self = this;

		context.find('.trigger-clone-row').each(function(ev) {
			var btn    = $(this);
			var target = $(btn.data('target'));
			btn.data('tpl', target.html());
		});

		context.find('.trigger-clone-row').on('click', function(ev) {
			Orb.cancelEvent(ev);

			var btn        = $(this);
			var target     = $(btn.data('target'));
			var targetList = $(btn.data('target-list'));
			var clone      = $('<div/>').html(btn.data('tpl')).addClass('pane-row add-to-search');

			clone.removeClass('row-orig');

			self.initStandardFormElements(clone);
			clone.insertAfter(targetList);
		});

		context.find('.trigger-remove-row').on('click', function(ev) {
			Orb.cancelEvent(ev);
			$(this).closest('.pane-row').remove();
		});

		context.find('.dp-select-widget-simple').each(function() {
			var widget = new DeskPRO.UI.Select.WidgetSimple($(this));
			self.widgets.push(widget);
		});

		context.find('.date-term-wrap').each(function() {
			var el = $(this);
			var status = el.find('.status-value-outer').hide();;
			var dateTerm = new DeskPRO.Agent.RuleBuilder.DateTerm({
				ruleBuilder: null,
				rowEl: el,
				rowId: null,
				opMenu: null
			});
			dateTerm.initRow();

			el.find('select.op').on('change', function() {
				if ($(this).val() != "0") {
					dateTerm.updateStatus();
					status.show();
				} else {
					status.hide();
				}
			});

			if (el.data('base-name')) {
				var baseName = el.data('base-name');
				el.find('input').each(function() {
					$(this).attr('name', baseName + '[' + $(this).attr('name') + '][]');
				});
			}
		});
	},


	/**
	 * Destroys els that were detached
	 */
	destroy: function() {
		Array.each(this.formPanels, function(formPanel) {
			formPanel.destroy();
		});
		this.formPanels = [];
	}
});


/**
 * A "panel" is a form that opens to the side of the source pane.
 *
 * @type {Orb.Class}
 */
DeskPRO.Agent.SourcePane.SearchFormPanel = new Orb.Class({
	Implements: [Orb.Util.Events],

	initialize: function(el) {
		this.el         = el;
		this._isOpen    = false;
		this.hasInit    = false;
		this.shim       = null;
		this.updateTypesTimer = null;
		this.searchBuilderLists = [];
		this.searchValList = [];
		this.searchStringList = [];
		this.targetSummaryEl = null;
	},

	initPanel: function() {
		if (this.hasInit) return;
		this.hasInit = true;

		this.el.find('.with-select2').each(function() {
			DP.select($(this));
		});

		var self = this;

		// For absolute positioning over things
		this.el.detach().appendTo('body');

		this.el.find('.with-search-builder').each(function() {
			if ($(this).hasClass('has-init-search-builder')) {
				return;
			}
			$(this).addClass('has-init-search-builder');

			var critTpl = $(this).find('.criteria_tpl');
			var critList = $(this).find('.criteria_list');

			self.searchBuilderLists.push(critList.get(0));

			var editor = new DeskPRO.Form.RuleBuilder(critTpl);
			editor.addEvent('newRow', function(new_row) {
				$('.trigger-remove-row', new_row).on('click', function() {
					new_row.remove();
				});
			});
			$('.add-term', critList).on('click', function() {
				var basename = 'terms['+Orb.uuid()+']';
				editor.addNewRow($('.search-terms', critList), basename);
			});
		});

		this.searchValList = self.el.find('.ensure-value');

		if (this.el.data('target-summary')) {
			this.targetSummaryEl = $(this.el.data('target-summary'));
		}

		if (this.targetSummaryEl) {
			this.el.find('.search-string').on('keyup keydown change', function() {
				self.targetSummaryEl.text(($(this).val()));
			});

			if (this.targetSummaryEl.is('input.is-bound')) {
				this.targetSummaryEl.on('focus', function(ev) {
					if (self.el.find('.pane-row').length > 1) {
						$(this).blur();
						self.open();
					}
				});

				this.targetSummaryEl.on('keypress', function(ev) {
					if (self.el.find('.pane-row').length > 1) {
						$(this).val('');
						$(this).blur();
						self.open();
					} else {
						self.el.find('input[type="text"]').val('');
					}
				});
			}
		}

		this.shim = $('<div class="dp-shim"></div>');
		this.shim.appendTo('body');

		this.shim.on('click', function(ev) {
			Orb.cancelEvent(ev);
			self.close();
		});
	},

	updateTypes: function() {
		var self = this;
		var texts = [];

		if (this.searchBuilderLists.length) {
			Array.each(this.searchBuilderLists, function(o_el) {
				$.trim($(o_el).find('.builder-type-choice').each(function() {
					var el = $(this);
					var type = $.trim($(el).find('select').find('option').filter(':selected').text());
					if (type) {
						texts.push(type);
					}
				}));
			});
		}

		if (this.searchValList.length) {
			this.searchValList.each(function() {
				var val = $(this).val() || "";
				if (val.length != 0) {
					texts.push($(this).closest('.pane-row').find('.row-label').text());
				}
			});
		}

		var searchStringList = self.el.find('.search-string');
		if (searchStringList.length) {
			searchStringList.each(function() {
				var val = $(this).val() || "";
				if (val.length) {
					texts.push(val);
				}
			});
		}

		if (self.targetSummaryEl.is('input')) {
			self.targetSummaryEl.val(texts.join(', '));
		} else {
			self.targetSummaryEl.text(texts.join(', '));
		}
	},


	/**
	 * @returns {jQuery}
	 */
	getEl: function() {
		return this.el;
	},


	/**
	 * Is the side panel open?
	 *
	 * @returns {Boolean}
	 */
	isOpen: function() {
		return this._isOpen;
	},


	/**
	 * Open the side panel. If nearEl is specified, then we will try to open the side panel "near" this element.
	 *
	 * @param {HTMLElement} nearEl The element to open the panel near
	 */
	open: function(nearEl) {

		var self = this;

		if (!this.updateTypesTimer) {
			this.updateTypesTimer = window.setInterval(function() {
				self.updateTypes();
			}, 300);
		}

		if (this._isOpen) return;
		this._isOpen = true;

		// Actual panel events are lazy inited on first open
		this.initPanel();

		this.el.show();
		this.shim.show();

		var left = 269;
		var top  = 200;

		if (nearEl) {
			top = $(nearEl).offset().top - 10;
		}

		var winH = $(window).height();
		var maxH = winH - top - 80;

		if (maxH < 250) {
			if (winH > 250) {
				top -= (250 - maxH);
			} else {
				top = 60;
			}

			maxH = winH - top - 80;
		}

		if (winH / 1.5 > 500) {
			maxH = parseInt(Math.min(maxH, winH / 1.5));
		}

		this.el.css({
			left: left,
			top: top,
			'max-height': maxH
		});
	},


	/**
	 * CLose the panel if its open
	 */
	close: function() {
		if (!this._isOpen) return;
		this._isOpen = false;

		if (this.updateTypesTimer) {
			window.clearTimeout(this.updateTypesTimer);
			this.updateTypesTimer = null;
		}

		this.el.hide();
		this.shim.hide();
	},


	/**
	 * Destroy the panel by removing any additionally attached elements
	 */
	destroy: function() {
		this.close();

		if (this.hasInit) {
			this.el.detach();
			this.shim.detach();

			Array.each(this.widgets, function(w) {
				w.destroy();
			});
		}
	}
});