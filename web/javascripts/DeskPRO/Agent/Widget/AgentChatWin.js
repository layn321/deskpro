Orb.createNamespace('DeskPRO.Agent.Widget');

DeskPRO.Agent.Widget.AgentChatWin_Registry = {};

DeskPRO.Agent.Widget.AgentChatWin_Find = function(chatId) {
	var found = null;

	Object.each(DeskPRO.Agent.Widget.AgentChatWin_Registry, function(chatWin) {
		if (chatWin && !found && chatWin.getConvoId() == chatId) {
			found = chatWin;
		}
	});

	return found;
};

DeskPRO.Agent.Widget.AgentChatWin_FindAgents = function(agent_ids) {
	var found = null;

	agent_ids = agent_ids.sort(function(a,b) {
		return parseInt(a) - parseInt(b);
	});

	agent_ids_str = agent_ids.join(',');

	Object.each(DeskPRO.Agent.Widget.AgentChatWin_Registry, function(chatWin) {
		if (chatWin && !found && chatWin.agentIdsStr == agent_ids_str) {
			found = chatWin;
		}
	});

	return found;
};

/**
 * An agent chat window handles send/rec of chat messages to a particular
 * "window" (conversation) between at least one agent.
 */
DeskPRO.Agent.Widget.AgentChatWin = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(options) {

		this.options = {
			convoId: 0,
			agentIds: [],
			title: null
		};

		this.setOptions(options);

		this.uuid = this.OBJ_ID;
		this.convoId = this.options.convoId;

		this.chatsWrapper = $('#agent_chats_wrapper');

		DeskPRO.Agent.Widget.AgentChatWin_Registry[this.uuid] = this;

		this.agentIds = [];
		Array.each(this.options.agentIds, function(i) {
			this.agentIds.push(parseInt(i));
		}, this);

		this.agentIds = this.agentIds.sort(function(a,b) {
			return a-b;
		});

		// Used with find
		this.agentIdsStr = this.agentIds.join(',');

		this.wrapper = null;

		this._initWindow();
	},

	/**
	 * Inits the actual chat window elements on the page
	 */
	_initWindow: function() {
		if (this._hasInitWin) return;
		this._hasInitWin = true;

		var self = this;

		if (this.convoId) {
			// Make sure it doesnt already exist
			var exist = $('#agent_chat_conversation_' + this.convoId);
			if (exist.length) {
				return;
			}
		}

		// One agent: we're sending a new one, we dont define ourselves
		// Two agents with us: incoming new message and got agentids from server, which includes us
		if (!this.options.title && (this.agentIds.length == 1 || (this.agentIds.length == 2 && this.agentIds.indexOf(parseInt(DESKPRO_PERSON_ID)) != -1) )) {

			if (this.agentIds[0] == DESKPRO_PERSON_ID) {
				var agentInfo = DeskPRO_Window.getAgentInfo(this.agentIds[1]);
			} else {
				var agentInfo = DeskPRO_Window.getAgentInfo(this.agentIds[0]);
			}

			if (!agentInfo) {
				return;
			}

			var newContainer = $.tmpl('agent_chat_conversation', {
				local_id: this.uuid,
				to_agent_name: agentInfo.name,
				to_agent_shortname: agentInfo.shortName,
				to_agent_id: agentInfo.id,
				to_agent_picture: agentInfo.pictureUrlSizable.replace(/_SIZE_/g, 15)
			});
		} else {
			var newContainer = $.tmpl('agent_groupchat_conversation', {
				local_id: this.uuid,
				title: this.options.title || 'Group'
			});
		}

		this.wrapper = newContainer;

		newContainer.find('> .window').find('> header, > div.messages-box, > .input-message-wrap').on('click', function(ev) {
			var count = 0;
			Object.each(DeskPRO.Agent.Widget.AgentChatWin_Registry, function(win) {
				if (win) {
					win.wrapper.css('z-index', count++);
				}
			});
			self.wrapper.css('z-index', count+1);
		});
		newContainer.find('> nav').on('click', function() {
			var count = 0;
			Object.each(DeskPRO.Agent.Widget.AgentChatWin_Registry, function(win) {
				if (win) {
					win.wrapper.css('z-index', count++);
				}
			});
			self.wrapper.css('z-index', count+1);
		});

		// Accept clicks on routes
		newContainer.on('click', '[data-route]', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			DeskPRO_Window.runPageRouteFromElement($(this));
		});

		this.chatsWrapper.append(newContainer);
		this.resetPosition();

		$('textarea', newContainer).on('keypress', (function(ev) {
			// Enter, but not when meta key (alt, ctrl etc) are pressed
			if (ev.keyCode == 13 && !ev.metaKey) {
				ev.preventDefault();//dont enter enter key
				this._fireSendMessage();
			}
		}).bind(this));

		var nav = $('> nav', newContainer);
		nav.on('click', function(ev) {
			if (newContainer.is('.open')) {
				newContainer.removeClass('open');
			} else {
				newContainer.addClass('open');
			}
		});

		$('.close-trigger', nav).on('click', function(ev) {
			ev.stopPropagation();
			self.fireEvent('close');
			self.destroy();
		});

		$('.minimize', newContainer).on('click', function(ev) {
			ev.stopPropagation();
			self.fireEvent('minimize');
			self.close();
		});
		$('.close', newContainer).on('click', function(ev) {
			ev.stopPropagation();
			self.fireEvent('close');
			self.destroy();
		});

		this.loadLastConvo();
	},

	loadLastConvo: function() {
		var data = [];
		Array.each(this.agentIds, function(id) {
			data.push({
				name: 'agent_ids[]',
				value: id
			});
		});

		$.ajax({
			url: BASE_URL + 'agent/agent-chat/get-last-convo',
			data: data,
			contentType: 'json',
			context: this,
			success: function(data) {
				if (data.conversation_id) {
					this.convoId = data.conversation_id;
				}

				if (data.messages) {
					Array.each(data.messages, function(messageInfo) {
						if (messageInfo.agent_id == DESKPRO_PERSON_ID) {
							this.showMyMessage(messageInfo.message);
						} else {
							this.showMessage(messageInfo.agent_id, messageInfo.message, messageInfo.time);
						}
					}, this);
				}
			}
		});
	},

	resetPosition: function() {
		if (!this.wrapper) {
			return;
		}
		var chats = $('> section.agent-chat', this.chatsWrapper);
		if (chats.length > 1) {
			var lastChat = chats.eq(-2);
			var leftPos = lastChat.position().left + $('> nav', lastChat).outerWidth() + 8;
			this.wrapper.css('left', leftPos);
		} else {
			this.wrapper.css('left', 0);
		}
	},

	_fireSendMessage: function() {
		var txt = $('textarea', this.wrapper);
		var msg = txt.val().trim();
		txt.val('');

		if (!msg.length) {
			return;
		}

		var messageBlock = this.showMyMessage(msg);
		this.sendMessage(msg, messageBlock);
	},


	/**
	 * Get the convo ID
	 *
	 * @return {Integer}
	 */
	getConvoId: function() {
		return this.convoId;
	},


	/**
	 * Get the local uuid we've given the chat. Useful for things like element IDs.
	 *
	 * @return {String}
	 */
	getConvoLocalId: function() {
		return this.uuid;
	},


	/**
	 * Send a new message
	 *
	 * @param {String} message
	 * @param messageBlock
	 */
	sendMessage: function(message, messageBlock) {
		var data = [];
		data.push({
			name: 'content',
			value: message
		});
		data.push({
			name: 'local_id',
			value: this.uuid
		});

		Array.each(this.agentIds, function(id) {
			data.push({
				name: 'agent_ids[]',
				value: id
			});
		});

		var messageLocalId = Orb.uuid();
		var info = {
			message: message,
			localMessageId: messageLocalId,
			convoId: this.convoId,
			convoLocalId: this.uuid
		};

		this.fireEvent('sendMessage', [this, info]);

		$.ajax({
			url: BASE_URL + 'agent/agent-chat/send-agent-message/' + this.convoId,
			data: data,
			contentType: 'json',
			context: this,
			success: function(data) {
				this.convoId = data.conversation_id;

				info.messageId = data.message_id;
				info.convoId = this.convoId;
				this.fireEvent('sendMessageDone', [this, info]);

				if (messageBlock) {
					messageBlock.find('time').text(data.time);
				}
			}
		});
	},


	/**
	 * Show a new incoming message from someone
	 *
	 * @param agent_id
	 * @param message
	 * @param time
	 */
	showMessage: function(agent_id, message, time) {

		var agentInfo = DeskPRO_Window.getAgentInfo(agent_id);

		if (!agentInfo) {
			return;
		}

		var newMessage = $.tmpl('agent_chat_message', {
			author_id: agent_id,
			author_name: agentInfo.name,
			author_picture: agentInfo.pictureUrlSizable.replace(/_SIZE_/g, 25),
			message: '',
			time: time || ''
		});

		newMessage.find('span.message-text').html(this.formatMessage(message));

		$('.messages-container', this.wrapper).append(newMessage);
		$('.messages-box').scrollTop(100000);
	},


	formatMessage: function(message) {
		var message = Orb.escapeHtml(message);
		var idMap = {
			't': {title: 'Ticket', url: BASE_URL + 'agent/tickets/'},
			'p': {title: 'Person', url: BASE_URL + 'agent/people/'},
			'o': {title: 'Organization', url: BASE_URL + 'agent/organizations/'},
			'a': {title: 'Article', url: BASE_URL + 'agent/kb/article/'},
			'n': {title: 'News', url: BASE_URL + 'agent/news/post/'},
			'd': {title: 'Download', url: BASE_URL + 'agent/downloads/file/'},
			'i': {title: 'Feedback', url: BASE_URL + 'agent/feedback/view/'}
		};
		Object.each(idMap, function(info, prefix) {
			var re = new RegExp('\{\{\s*' + prefix + '\-([0-9]+)\s*\}\}', 'g');
			message = message.replace(re, '<a data-route="page:'+info.url+'$1">'+info.title+' #$1</a>');
		});

		var re = new RegExp('\{\{\s*tw\-([0-9]+)\s*\}\}', 'g');
		message = message.replace(re, '<a data-route="poppage:' + BASE_URL + 'agent/twitter/status/tweet-overlay?account_status_id=$1">Tweet #$1</a>');

		message = message.replace(/(https?:\/\/[^\s]+)/gi, '<a href="$1" target="_blank">$1</a>');

		return message;
	},


	/**
	 * Add own messages to the window immediately
	 *
	 * @param message
	 */
	showMyMessage: function(message) {
		var newMessage = $.tmpl('agent_chat_message_me', { message: '', time: '...' });
		newMessage.find('span.message-text').html(this.formatMessage(message));

		$('.messages-container', this.wrapper).append(newMessage);
		$('.messages-box').scrollTop(100000);

		return newMessage;
	},


	/**
	 * Open the chat tab
	 */
	open: function() {
		if (this.wrapper) {
			this.wrapper.addClass('open');
			this.wrapper.find('textarea').focus();
		}
	},


	/**
	 * Close the chat tab
	 */
	close: function() {
		if (this.wrapper) {
			this.wrapper.removeClass('open');
		}
	},


	/**
	 * Remove the chat window
	 */
	destroy: function() {
		if (this.wrapper) {
			this.wrapper.remove();
			this.wrapper = null;
		}

		DeskPRO.Agent.Widget.AgentChatWin_Registry[this.uuid] = null;
		delete DeskPRO.Agent.Widget.AgentChatWin_Registry[this.uuid];
		this.fireEvent('destroy', [this]);
	}
});
