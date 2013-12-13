Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');
DeskPRO.Agent.PageFragment.Page.UserChat = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.TYPENAME = 'userchat';
	},

	initMetaData: function() {
		DeskPRO_Window.recentTabs.add(
			'userchat',
			this.meta.conversation_id,
			this.meta.title,
			BASE_URL + 'agent/chat/view/' + this.meta.conversation_id
		);
	},

	initPage: function(el) {
		var self = this;

		var OBJ_ID = this.OBJ_ID;
		this.el = el;

		this.chatStatus  = this.meta.status;
		this.chatEndedBy = this.meta.ended_by;

		var messageTextarea = this.getEl('replybox_txt');

		var sendMsg = function() {
			var msg = messageTextarea.val().trim();

			if (messageTextarea.data('redactor')) {
				messageTextarea.setCode('');
				messageTextarea.change();

				self.getEl('replybox').css('height', 40+69);
				self.getEl('messages_box').css('bottom', 40+69);
			} else {
				messageTextarea.val('');
			}

			if (!msg.length) {
				return;
			}

			var tmp_id = Orb.uuid();
			self.addMessageRow(self.meta.youName, msg, 'agent', DeskPRO_Window.canUseAgentReplyRte(), tmp_id, { no_notify: true, person_avatar: self.meta.youPictureUrl });

			self.sendMessage(msg, function(message_id) {
				var d = new Date();

				var a_p = "am";
				var curr_hour = d.getHours();
				if (d.getHours() > 12) {
					a_p = "pm";
				}
				if (curr_hour == 0) {
					curr_hour = 12;
				} else if (curr_hour > 12) {
					curr_hour = curr_hour - 12;
				}

				var curr_min = d.getMinutes();
				curr_min = curr_min + "";
				if (curr_min.length == 1) {
					curr_min = "0" + curr_min;
				}

				var time = curr_hour + ":" + curr_min + "" + a_p;

				// Sets the real message ID after we've come back from ajax
				self.getEl('messages_box').find('.message-' + tmp_id).addClass('message-' + message_id).addClass('server-ack').data('message-id', message_id).attr('title', 'User read message at: ' + time);
			});
		}

		this.doSendMsg = function() {
			sendMsg();
		}

		messageTextarea.on('keypress', function(ev) {
			if (ev.keyCode == 13 && !ev.metaKey) {
				ev.preventDefault();
				sendMsg();
			}
		});

		this.addEvent('destroy', function() {
			DeskPRO_Window.getMessageBroker().removeTaggedListeners(OBJ_ID)
			if (self.chatStatus == 'ended') {
				return;
			} if (self.closeAction == 'unassign') {
				self.leaveConvo('unassign');
			} else if (self.closeAction == 'end') {
				self.leaveConvo('end');
			} else {
				self.leaveConvo(null);
			}
		});

		this._initMenus();
		this._initAssignControl();
		this._initBlock();
		this._initLabels();

		DeskPRO_Window.getMessageBroker().addMessageListener('chat_convo.' + this.meta.conversation_id + '.newmessage', this.handleNewMessageCm, this, [this.OBJ_ID]);
		DeskPRO_Window.getMessageBroker().addMessageListener('chat_convo.' + this.meta.conversation_id + '.hidden_newmessage', this.handleNewMessageCm, this, [this.OBJ_ID]);
		DeskPRO_Window.getMessageBroker().addMessageListener('chat_convo.' + this.meta.conversation_id + '.ended', this.chatHasEnded, this, [this.OBJ_ID]);
		DeskPRO_Window.getMessageBroker().addMessageListener('chat_convo.' + this.meta.conversation_id + '.reassigned', function(data) { this.chatReassignedTo(data.agent_id); }, this, [this.OBJ_ID]);
		DeskPRO_Window.getMessageBroker().addMessageListener('chat_convo.' + this.meta.conversation_id + '.unassigned', function(data) { this.chatReassignedTo(data.agent_id); }, this, [this.OBJ_ID]);
		DeskPRO_Window.getMessageBroker().addMessageListener('chat_convo.' + this.meta.conversation_id + '.usertyping', function(data) { this.userTyping(data); }, this, [this.OBJ_ID]);
		DeskPRO_Window.getMessageBroker().addMessageListener('chat_convo.' + this.meta.conversation_id + '.ack_messages', function(data) { this.ackMessages(data); }, this, [this.OBJ_ID]);

		DeskPRO_Window.getMessageBroker().addMessageListener('chat.ended', function(data) {
			if (this.meta.conversation_id == data.conversation_id) {
				this.chatStatus = 'ended';
			}
		} , this, [this.OBJ_ID]);

		//------------------------------
		// Editor
		//------------------------------

		var textarea = this.getEl('replybox_txt'), isWysiwyg = false;

		if (DeskPRO_Window.canUseAgentReplyRte()) {
			isWysiwyg = true;

			DeskPRO_Window.initRteAgentReply(textarea, {
				defaultIsHtml: true,
				minHeight: 65,
				maxHeight: 40,
				inlineHiddenPosition: this.getEl('is_html_reply'),
				convertLinks: false, // we'll do it ourselves
				callback: function(obj) {
					obj.addBtnFirst('dp_attach', 'Click here to attach a file. You may also drag a file from your computer desktop into this reply area to upload attachments faster.', function(){});
					obj.addBtnAfter('dp_attach', 'dp_snippets', 'Open snippets', function(){});

					obj.addBtnAfter('horizontalrule', 'dp_create_ticket', 'Create Ticket', function(){
						DeskPRO_Window.newTicketLoader.open(function(page) {
							page.setNewByChat({ chat_id: self.meta.conversation_id, chat_title: self.meta.chatTitle, person_id: self.meta.person_id, sesson_id: self.meta.session_id, email: self.meta.email });
						});
					});
					obj.addBtnAfter('dp_create_ticket', 'dp_end_chat', 'End Chat', function(){
						self.endChat();
					});

					obj.addBtnAfter('dp_end_chat', 'dp_send_message', 'Send your message (or press the Enter or Return key on your keyboard)', function(){
						self.doSendMsg();
					});

					obj.addBtnSeparatorAfter('dp_attach');
					obj.addBtnSeparatorAfter('dp_snippets');
					obj.addBtnSeparatorAfter('horizontalrule');

					snippetBtn = obj.$toolbar.find('.redactor_btn_dp_snippets').closest('li');
					snippetBtn.addClass('snippets').find('a').html('<span class="show-key-shortcut">S</span>nippets');

					var tmp = obj.$toolbar.find('.redactor_btn_dp_attach').closest('li');
					tmp.addClass('attach');
					tmp.find('a').text('Attach').append('<input type="file" class="file" name="file-upload" />');

					tmp = obj.$toolbar.find('.redactor_btn_dp_send_message').closest('li');
					tmp.addClass('dp_send_message');
					tmp.find('a').text('Send');
				}
			});
			this.getEl('is_html_reply').val(1);

			if (textarea.data('redactor')) {
				var ed = textarea.getEditor();
				ed.on('keypress', function(ev) {
					if (ev.keyCode === 13 && !ev.shiftKey && !ev.ctrlKey && !ev.metaKey) {
						ev.preventDefault();
						window.setTimeout(function() {
							ed.linkify();
							window.setTimeout(function() {
								sendMsg();
							}, 100);
						}, 10);
					}
				});

				var lastH = ed.height();
				ed.on('keypress change', function() {
					window.setTimeout(function() {
						var tmp = ed.height();
						if (lastH != tmp) {
							lastH = tmp;
							self.getEl('replybox').css('height', lastH+44);
							self.getEl('messages_box').css('bottom', lastH+44);
						}
					}, 50);
				});
			}
		}

		if (textarea.data('redactor')) {
			var ed = textarea.getEditor();
			var api = textarea.data('redactor');

			var te = new DeskPRO.TextExpander({
				textarea: ed,
				onCombo: function(combo, ev) {
					combo = combo.replace(/%/g, '');
					if (window.DESKPRO_CHAT_SNIPPET_SHORTCODES && window.DESKPRO_CHAT_SNIPPET_SHORTCODES[combo]) {
						ev.preventDefault();

						var snippetId = window.DESKPRO_CHAT_SNIPPET_SHORTCODES[combo];

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

						$.ajax({
							url: BASE_URL + 'agent/text-snippets/chat/'+snippetId+'.json',
							dataType: 'json',
							success: function(data) {

								var snippet = data.snippet;
								var snippetId    = snippet.id;
								var snippetCode  = snippet.snippet;

								var agentText;
								var defaultText;
								var wantText;
								var useText;
								var result;

								Array.each(snippetCode, function(info) {
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

								var el = api.$editor.find('.editor-inserting-var.snippet-' + snippetId);

								var wrapper = $('<div/>');
								wrapper.html(useText);

								if (wrapper.find('> div, > p, > span')[0]) {
									data = wrapper.find('> *');
								} else {
									data = wrapper;
								}

								// trailing newlines
								var coll;
								if (data.length == 1) {
									coll = data;
								} else {
									coll = data.find('> p');
								}
								coll.each(function() {
									var l = $(this).find('> *').last();
									if (l.is('br')) {
										l.remove();
									}
								});

								if (data.find('> div, > span, > p').length == 1) {
									var span = $('<span></span>');
									span.append(data.find('> *'));
									data = span;
								} else if (data.find('> *').length == 0) {
									var span = $('<span></span>');
									span.html(data.html());
									data = span;
								}

								data.append('<span class="_cursor"></span>');
								var cursor = data.find('._cursor');

								el.after(data);
								el.remove();
								api.setSelection(cursor[0], 0, cursor[0], 0);
							}
						});
					}
				}
			});
		}

		//------------------------------
		// Snippets Viewer
		//------------------------------

		this.snippetsViewer = new DeskPRO.Agent.Widget.SnippetViewer({
			triggerElement: snippetBtn,
			snippetType: 'chat',
			onSnippetClick: function(info) {
				var snippetId    = info.snippetId;
				var snippetCode  = info.snippetCode;

				var agentText;
				var defaultText;
				var wantText;
				var useText;
				var result;

				Array.each(snippetCode, function(info) {
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

				var val = useText;

				var messageTextarea = self.getEl('replybox_txt')

				var data = $('<div></div>').html(val);
				if (data.find('> span, > div, > p').length == 1) {
					var span = $('<span></span>');
					span.append(data.find('> *'));
					data = span;
				} else if (data.find('> span, > div, > p').length == 0) {
					var span = $('<span></span>');
					span.html(data.html());
					data = span;
				}

				val = data.html();

				if (messageTextarea.data('redactor')) {

					try {
						messageTextarea.data('redactor').restoreSelection();
						messageTextarea.data('redactor').setBuffer();
					} catch (e) {}

					var html = val;
					html = html.replace(/<\/p>\s*<p>/g, '<br/>');
					html = html.replace(/^<p>/, '');
					html = html.replace(/<\/p>$/, '');
					messageTextarea.data('redactor').insertHtml(html);
					messageTextarea.change();
					window.setTimeout(function() {
						var tmp = ed.height();
						if (lastH != tmp) {
							lastH = tmp;
							self.getEl('replybox').css('height', lastH+69);
							self.getEl('messages_box').css('bottom', lastH+69);
						}
					}, 100);
				} else {
					var pos = messageTextarea.getCaretPosition();
					if (!pos) {
						messageTextarea.setCaretPosition(0);
					}

					messageTextarea.insertAtCaret(val);
				}

				self.snippetsViewer.close();
			}
		});

		//------------------------------
		// Intercept close events and cancel, so we
		// can confirm
		//------------------------------

		this.closeAction = false;
		this._confirmCloseOverlay = new DeskPRO.UI.Overlay({
			contentElement: this.getEl('closetab_prompt'),
			addClassname: 'normal-size',
			onPosition: function(evData) {
				var tabId = self.getTabId();
				if (!tabId) return;

				var tabEl = $('#tabbtn_' + tabId);
				if (!tabEl[0]) {
					return;
				}
				var tabW = tabEl.width();

				evData.left = (tabEl.offset().left + (tabW / 2)) - (evData.w / 2);
				evData.top = tabEl.offset().top;

				if ((evData.left+evData.w) > evData.pageW) {
					evData.left = evData.pageW - evData.w - 15;
				}
			},
			onContentSet: function(eventData) {
				$('.unassign-trigger').on('click', function() {
					self._confirmCloseOverlay.close();
					self.closeAction = 'unassign';
					DeskPRO_Window.TabBar.removeTabById(self.meta.tabId);
				});
				$('.end-trigger').on('click', function() {
					self._confirmCloseOverlay.close();
					self.closeAction = 'end';
					DeskPRO_Window.TabBar.removeTabById(self.meta.tabId);
				});
				$('.cancel-trigger').on('click', function() {
					self._confirmCloseOverlay.close();
				});
			}
		});

		this.addEvent('closeTab', function(event) {
			// Already ended or not assigned to us
			if (this.getEl('assign_btn').data('agent-id') != DESKPRO_PERSON_ID || this.chatStatus == 'ended') {
				return;
			}

			if (this.chatStatus == 'ended') {
				return;
			}

			if (this.closeAction) return;
			event.deskpro.cancelClose = true;

			this._confirmCloseOverlay.open();
		}, this);

		this.getEl('create_ticket_btn2').on('click', function() {
			DeskPRO_Window.newTicketLoader.open(function(page) {
				page.setNewByChat({ chat_id: self.meta.conversation_id, chat_title: self.meta.chatTitle, person_id: self.meta.person_id, sesson_id: self.meta.session_id, email: self.meta.email });
			});
		});

		var imposter = this.getEl('imposter');
		if (imposter[0]) {
			imposter.find('button.dismiss').on('click', function() {
				imposter.fadeOut('fast', function() {
					imposter.remove();
				});
			});
		}

		// Dismiss the notification at the same time
		var notifRow = DeskPRO_Window.notifications.findRow('id-chat-' + this.meta.conversation_id);
		if (notifRow) {
			DeskPRO_Window.notifications.removeRow(notifRow);
		}

		this.getEl('replybox_txt').on('focus', function() {
			$(this).addClass('is-focused');
		});
		this.getEl('replybox_txt').on('blur', function() {
			$(this).removeClass('is-focused');
		});

		this.getEl('messages_box').on('click', '.truncated-wrap', function() {
			var content = $(this).find('textarea').val();
			self.showFullMessage(content);
		});

		this.chatFields = new DeskPRO.Agent.PageHelper.ChatFields(this);
		this.chatFields.updateDisplay();
		this.ownObject(this.chatFields);

		var maintabs = this.getEl('main_tabs_nav');
		if (maintabs.data('simpletabs')) {
			maintabs.data('simpletabs').addEvent('tabSwitch', function(evData) {
				var tabEl = $(evData.tabEl);
				if (tabEl[0]) {
					if (tabEl.hasClass('is-fields-tab')) {
						self.getEl('field_edit_controls').show();
					} else {
						self.getEl('field_edit_controls').hide();
					}
				}
			});
		}

		// Init upload after RTE was added (it has an upload field on the 'attach' button)
		this._initUpload();
	},

	handleNewMessageCm: function(data, name) {

		// Ignore our own messages, unless its a file then we have a rendered version from the server
		if (data.author_type && data.author_type == 'agent' && data.author_id && data.author_id == DESKPRO_PERSON_ID && !(data.metadata && data.metadata.type && data.metadata.type == 'file')) {
			return;
		}

		// Fixes when typing indicator gets out of sync with messages
		// Can happen if a CM for new message comes in after typing was started but before it stopped
		if (this.userTypingTime && (new Date()).getTime() > this.userTypingTime) {
			this.userTypingTime = null;
			this.getEl('user_typing').hide();
		}

		this.addMessageRow(data.author_name, data.content, data.author_type, data.is_html, data.message_id, data.metadata, data);
	},

	chatReassignedTo: function(agent_id) {

		var btnEl = this.getEl('assign_btn');

		if (agent_id == "0") {
			var pic = '';
			var agentInfo = {
				name: 'Unassigned'
			};
		} else {
			var agentInfo = DeskPRO_Window.getAgentInfo(agent_id);
			if (!agentInfo) {
				return;
			}

			var pic = agentInfo.pictureUrlSizable.replace('{SIZE}', 20);
		}

		$('li', this.getEl('agent_parts')).show();
		if (agent_id != '0') {
			$('li.agent-' + agent_id, this.getEl('agent_parts')).hide();
		}
		if ($('li:visible', this.getEl).length) {
			this.getEl('agent_parts_none').hide();
		} else {
			this.getEl('agent_parts_none').show();
		}

		this.getEl('agent_assign_ob').data('assigned', agent_id);
		btnEl.css('background-image', pic);
		btnEl.text(agentInfo.name);
		btnEl.data('agent-id', agent_id);
	},

	addPart: function(agent_id) {
		if ($('.agent-' + agent_id, this.getEl('agent_parts')).length) {
			return;
		}

		var agentInfo = DeskPRO_Window.getAgentInfo(agent_id);

		var li = $('<li><a></a></li>');
		li.addClass('agent-' + agent_id);
		$('a', li).text(agentInfo.name).addClass('agent-link').css({
			'background-image': agentInfo.pictureUrlSizable.replace('{SIZE}', 20)
		})

		this.getEl('agent_parts').append(li);

		if (this.getEl('assign_btn').data('agent-id') == agent_id) {
			li.hide();
		}

		if ($('li:visible', this.getEl).length) {
			this.getEl('agent_parts_none').show();
		}
	},

	removePart: function(agent_id) {
		$('.agent-' + agent_id, this.getEl('agent_parts')).remove();

		if (!$('li:visible', this.getEl).length) {
			this.getEl('agent_parts_none').hide();
		}
	},

	updateActiveAgentList: function(assigned, parts) {
		var assigned_name = DeskPRO_Window.getDisplayName('agent', agent_id) || 'Unassigned';
		$('span.agent_id.val', this.el).html(assigned_name);

		var ul = $('.convo_participants ul', this.el);
		ul.empty();

		if (!parts.lenght) {
			ul.append('<li class="agent-0">None</li>');
		} else {
			Array.each(parts, function(agent_id) {
				var name = DeskPRO_Window.getDisplayName('agent', agent_id);
				ul.append('<li class="agent-'+agent_id+'">'+name+'</li>');
			});
		}
	},

	userTyping: function(data) {
		if (!data || !data.preview || !data.preview.length) {
			this.getEl('user_typing').hide();
			return;
		}

		var preview = data.preview;
		if (preview.length > 500) {
			preview = '...' + preview.substring(preview.length - 500);
		}

		this.userTypingTime = (new Date()).getTime();
		var el = this.getEl('user_typing');
		$('.prop-msg', el).text(preview);
		el.detach().appendTo(this.getEl('messages_box'));
		el.show();

		this.updateUi.bind(this);
		this.getEl('messages_box').scrollTop(10000);
	},

	ackMessages: function(data) {
		if (!data || !data.message_ids || !data.message_ids.length) {
			return;
		}

		Array.each(data.message_ids, function (message_id) {
			this.getEl('messages_box').find('.message-' + message_id).addClass('user-ack');
		}, this);
	},

	_initMenus: function() {
		var self = this;

		//------------------------------
		// Department
		//------------------------------

		var dep = this.getEl('department_id');
		DP.select(dep);

		dep.on('change', function() {
			var depId = parseInt($(this).val());

			DeskPRO_Window.util.ajaxWithClientMessages({
				url: BASE_URL + 'agent/chat/change-props/' + self.meta.conversation_id,
				data: [{ name: 'props[department_id]', value: depId }],
				type: 'POST'
			});
		});
	},

	endChat: function() {
		DeskPRO_Window.util.ajaxWithClientMessages({
			url: BASE_URL + 'agent/chat/end-chat/' + this.meta.conversation_id
		});
	},

	leaveChat: function() {
		if (this.chatStatus == 'ended' || this.getEl('assign_btn').data('agent-id') != DESKPRO_PERSON_ID) {
			return;
		}
		$.ajax({
			url: BASE_URL + 'agent/chat/assign/' + this.meta.conversation_id + '/0',
			data: { 'leaving': true },
			context: this,
			contentType: 'json'
		});
	},

	chatHasEnded: function(data) {

	},

	addPart: function(agent_id) {
		$.ajax({
			url: BASE_URL + 'agent/chat/add-part/' + this.meta.conversation_id + '/' + agent_id,
			context: this,
			contentType: 'json'
		});
	},

	syncPars: function(agent_ids) {
		var postData = [];
		Array.each(agent_ids, function(id) {
			postData.push({ name: 'agent_ids[]', value: id });
		});
		$.ajax({
			url: BASE_URL + 'agent/chat/sync-parts/' + this.meta.conversation_id,
			data: postData,
			type: 'POST',
			context: this,
			contentType: 'json'
		});
	},

	reassignConvo: function(agent_id) {
		DeskPRO_Window.util.ajaxWithClientMessages({
			url: BASE_URL + 'agent/chat/assign/' + this.meta.conversation_id + '/' + agent_id
		});
	},

	leaveConvo: function(after) {
		this.quitting = true;
		var self = this;

		var action = '';
		if (after) {
			if (after == 'unassign') {
				action = 'unassign';
			} else if (after == 'end') {
				action = 'end';
			}
		}

		DeskPRO_Window.util.ajaxWithClientMessages({
			url: BASE_URL + 'agent/chat/leave/' + this.meta.conversation_id,
			data: {
				action: action
			}
		});
	},

	addMessageRow: function(name, msg, type, is_html, message_id, metadata, reqData) {

		var notify = true;
		if (message_id && $('.message-' + message_id, this.getEl('messages_box')).length) {
			return;
		}

		if (type == 'sys') {
			name = '* ';
		} else {
			name = '&lt;' + name + '&gt; ';
		}

		var popoutclass = '';
		if (type == 'user') {
			popoutclass = " person-overview";
		}

		var addclass = '';
		if (metadata && metadata.new_user_track) {
			this.userTyping();
			addclass = 'user-track';
			notify = false;

			this.getEl('messages_box').find('.row.agent').addClass('user-ack');
		}

		var avatarHtml = '';
		var person_avatar = metadata.person_avatar || this.meta.userPictureUrl;
		person_avatar = person_avatar.replace(/\/avatar\/\d+\//, "/avatar/25/", person_avatar);
		person_avatar = person_avatar.replace(/\/size\/\d+\//, "/size/25/", person_avatar);
		if (person_avatar.indexOf('gravatar.com') !== -1) {
			person_avatar = person_avatar.replace(/&?s=\d+\//, "", person_avatar);
			person_avatar = Orb.appendQueryData(person_avatar, 's', '25');
		}

		avatarHtml = '<div class="avatar tipped" title="'+ Orb.escapeHtml(metadata.author_name || '') +'"><img src="' + person_avatar + '" /></div>';

		var html = ['<div class="row '+type+' ' + addclass + '"><div class="message-content">'];
			if (type == 'sys') {
				html.push('<div class="message prop-msg"></div><time></time>');
			} else if (type == 'agent') {
				html.push(avatarHtml);
				html.push('<div class="chatSend"><div class="chatMsgSend"><div class="prop-msg"></div><span class="bubbleLeft"></span></div></div><time></time><span class="ack-icon"></span>');
				html.push('<div class="chat-clear"></div>');
			} else if (type == 'user') {
				this.userTyping();
				html.push(avatarHtml);
				html.push('<div class="chatRecieve"><div class="chatMsgRecieve"><div class="prop-msg"></div><span class="bubbleRight"></span></div></div><time></time>');
				html.push('<div class="chat-clear"></div>');
			}
		html.push('</div></div>');

		var row = $(html.join(''));

		var d = new Date();

		var a_p = "am";
		var curr_hour = d.getHours();
		if (d.getHours() > 12) {
			a_p = "pm";
		}
		if (curr_hour == 0) {
			curr_hour = 12;
		} else if (curr_hour > 12) {
			curr_hour = curr_hour - 12;
		}

		var curr_min = d.getMinutes();
		curr_min = curr_min + "";
		if (curr_min.length == 1) {
			curr_min = "0" + curr_min;
		}


		$('time', row).text(curr_hour + ":" + curr_min + "" + a_p);

		if (message_id) {
			row.addClass('message-' + message_id);
		}

		if (type == 'sys') {
			if (msg.indexOf('{"phrase_id":') === 0) {
				try {
					var data = $.parseJSON(msg);
				} catch(e) {
					console.error(e);
					data = {};
				}

				if (data.phrase_id) {
					msg = DeskPRO_Window.getTranslate().phrase('agent.userchat.' + data.phrase_id, data, true);
				} else {
					msg = 'unknown phrase';
				}
			}
		}

		if (is_html) {
			var titleMsg = 'New chat message';
			$('.prop-msg', row).html(msg);
		} else {
			var titleMsg = msg;
			var isTruncated = false;
			var origMsg = msg;
			if (type == 'user' && msg.length > 500) {
				isTruncated = true;
				msg = msg.substring(0, 500);
			}

			msg = Orb.escapeHtml(msg);
			msg = Orb.nl2br(msg);
			msg = DeskPRO_Window.util.linkUrls(msg);

			if (isTruncated) {
				msg += ' <div class="truncated-wrap"><div class="truncated-btn">&bull; &bull; &bull;</div><textarea class="orig-message" style="display:none;">' + Orb.escapeHtml(origMsg) + '</textarea></div>';
			}

			$('.prop-msg', row).html(msg);
		}


		$('time', row).attr('datetime', (new Date()).toString());

		DeskPRO_Window.initInterfaceLayerEvents(row);
		row.appendTo(this.getEl('messages_box'));

		this.getEl('messages_box').scrollTop(10000);

		// Ignore our own messages
		if (notify) {
			if (reqData && reqData.author_type && reqData.author_type == 'agent' && reqData.from_client == DESKPRO_SESSION_ID) {
				notify = false;
			}
		}

		if (metadata && metadata.no_notify) {
			notify = false;
		}

		if (notify && !this.quitting) {
			this.alertTab();

			DeskPRO_Window.faviconBadge.enableCrazyMode(titleMsg);

			// Add 'pop' sound if its not us
			var alertEl = $.tmpl('user_chat_newmsg_sound');
			alertEl.appendTo(this.el);
			DeskPRO_Window.handleSoundElements(alertEl);
		}

		this.updateUi();
	},

	sendMessage: function(msg, success) {
		DeskPRO_Window.util.ajaxWithClientMessages({
			type: 'POST',
			url: BASE_URL + 'agent/chat/send-message/' + this.meta.conversation_id,
			data: {
				content: msg,
				is_html: DeskPRO_Window.canUseAgentReplyRte()
			},
			execSuccessBefore: true,
			success: function(data) {
				if (success && data.message_id) {
					success(data.message_id);
				}
			}
		});
	},

	sendInvite: function(agent_id) {
		DeskPRO_Window.util.ajaxWithClientMessages({
			url: BASE_URL + 'agent/chat/invite/' + this.meta.conversation_id + '/' + agent_id
		});
	},

	showFullMessage: function(msg) {
		if (!this.fullMessageOverlay) {
			this.fullMessageOverlay = new DeskPRO.UI.Overlay({
				contentElement: this.getEl('view_fulle_message_overlay')
			});
		}

		msg = Orb.escapeHtml(msg);
		msg = Orb.nl2br(msg);

		this.getEl('view_full_message_content').html(msg);
		this.fullMessageOverlay.openOverlay();
	},

	//#################################################################
	//# Reassignment
	//#################################################################

	_initAssignControl: function() {

		var self = this;
		var followersList = this.getEl('followers_list');
		var el = this.getEl('agent_assign_ob');
		this.assignOptionBox = new DeskPRO.UI.OptionBoxRevertable({
			element: el,
			trigger: this.getEl('assign_ob_trigger'),
			onSave: function(ob) {
				var selections = ob.getAllSelected();

				var agent_id = parseInt(selections.agents || 0);

				followersList.empty();
				var selections = ob.getAllSelected();

				var part_ids = [];
				Array.each(selections.followers, function(part_id) {
					var label = $('.agent-part-label-' + part_id, ob.getElement()).first().text().trim();

					var li = $('<li />');
					var span = $('<span />');
					span.addClass('agent-link');
					span.data('agent-id', part_id);
					span.attr('data-agent-id', part_id);
					span.text(label);
					span.appendTo(li);

					followersList.append(li);

					part_ids.push(part_id);
				});

				if (part_ids.length) {
					self.syncPars(part_ids);
				}

				if (!selections.followers.length) {
					followersList.append('<li>No followers</li>');
				}

				var current_agent = parseInt(el.data('assigned'));
				if (current_agent != agent_id) {
					self.reassignConvo(agent_id);
				}
			}
		});

		var box1 = self.getEl('people_box_person_container');
		var box2 = self.getEl('people_box_agent_container');
		var box1_in = $('> article', box1);
		var box2_in = $('> article', box2);

		var chatView = this.getEl('chat_view'),
			chatPositioner = this.getEl('chat_positioner'),
			header = self.wrapper.find('.page-header');

		var syncSizes = function() {
			var h1 = box1_in.height();
			var h2 = box2_in.height();

			/*var h = (h1 > h2) ? h1 : h2;

			box2.css('min-height', h);
			box1.css('min-height', h);*/

			box1_in.each(function() { var thisH = $(this).outerHeight(); if (thisH > h1) { h1 = thisH; } });
			box2_in.each(function() { var thisH = $(this).outerHeight(); if (thisH > h2) { h2 = thisH; } });

			var h = (h1 > h2) ? h1 : h2;

			box2.css('min-height', h);
			box1.css('min-height', h);
		};

		var syncChatSize = function() {
			chatView.css('top', chatPositioner.outerHeight() + header.outerHeight());
		};

		// TODO handle resize without element resize monitor
		chatPositioner.on('resize', syncChatSize);
		box1.on('resize', syncSizes);
		box2.on('resize', syncSizes);

		syncSizes();
		syncChatSize();
	},

	//#################################################################
	//# Upload message
	//#################################################################

	_initUpload: function() {

		var self = this;

		DeskPRO_Window.util.fileupload(this.el, {
			uploadTemplate: $('.template-upload', this.el),
			downloadTemplate: $('.template-download', this.el),
			dropZone: this.getEl('replybox').find('nav')
		});
		this.el.bind('fileuploaddone', function(ev, data) {
			if (data.result && data.result.length) {
				var items = data.result, x;
				for (x = 0; x < items.length; x++) {
					DeskPRO_Window.util.ajaxWithClientMessages({
						url: BASE_URL + 'agent/chat/send-file-message/' + self.meta.conversation_id,
						data: {send_blob_id: items[0].blob_id }
					});
				}
			}

			self.getEl('uploading_list').hide().find('> ul').empty();
		});
		this.el.bind('fileuploadstart', function() {
			self.getEl('uploading_list').detach().appendTo(self.getEl('messages_box')).show();
			self.updateUi();
			self.getEl('messages_box').scrollTop(10000);
		});
	},

	//#################################################################
	//# Block controls
	//#################################################################

	_initBlock: function() {
		var self = this;
		this.getEl('block_user').on('click', function(ev) {
			$(this).prop('disabled', true).html('<em>Loading</em>');

			ev.preventDefault();

			var postData = {
				block_ip: self.getEl('block_ip').is(':checked') ? 1 : 0,
				reason: self.getEl('block_reason').val()
			};

			$.ajax({
				url: BASE_URL + 'agent/chat/block-user/' + self.meta.conversation_id,
				type: 'POST',
				data: postData,
				dataType: 'json',
				complete: function() {
					self.closeSelf();
					DeskPRO_Window.loadPage(BASE_URL + 'agent/chat/view/' + self.meta.conversation_id, {ignoreExist:true});
				}
			})
		});

		this.getEl('unblock_user').on('click', function(ev) {
			$(this).prop('disabled', true).html('<em>Loading</em>');
			ev.preventDefault();
			$.ajax({
				url: BASE_URL + 'agent/chat/unblock-user/' + self.meta.conversation_id,
				type: 'POST',
				dataType: 'json',
				complete: function() {
					self.closeSelf();
					DeskPRO_Window.loadPage(BASE_URL + 'agent/chat/view/' + self.meta.conversation_id, {ignoreExist:true});
				}
			})
		});
	},

	//#################################################################
	//# Labels
	//#################################################################

	_initLabels: function() {
		if (this.getEl('labels_input')[0]) {
			this.labelsInput = new DeskPRO.UI.LabelsInput({
				type: 'chat_conversations',
				input: this.getEl('labels_input'),
				onChange: this.saveLabels.bind(this)
			});
			this.ownObject(this.labelsInput);
		}
	},

	saveLabels: function() {
		if (this._saveLabelsTimeout) {
			window.clearTimeout(this._saveLabelsTimeout);
		}

		this._labelsData = this.labelsInput.getFormData();
		this._saveLabelsTimeout = this._doSaveLabels.delay(2000, this);
	},

	_doSaveLabels: function() {
		var data = this._labelsData;

		$.ajax({
			url: BASE_URL + 'agent/chat/' + this.meta.conversation_id + '/ajax-save-labels',
			type: 'POST',
			context: this,
			data: data,
			dataType: 'json'
		});
	}
});
