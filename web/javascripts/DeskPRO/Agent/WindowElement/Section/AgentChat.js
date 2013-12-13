Orb.createNamespace('DeskPRO.Agent.WindowElement.Section');

DeskPRO.Agent.WindowElement.Section.AgentChat = new Orb.Class({
	Extends: DeskPRO.Agent.WindowElement.Section.AbstractSection,

	init: function() {
		this.buttonEl = $('#agent_chat_section');
		this.chatsWrapper = $('#agent_chats_wrapper');
		this.setSectionElement($('<section id="agent_chat_outline"></section>'));

		this.urlFragmentName = 'agentchat';

		$('#agent_chat_conversation').template('agent_chat_conversation');
		$('#agent_groupchat_conversation').template('agent_groupchat_conversation');
		$('#agent_chat_message').template('agent_chat_message');
		$('#agent_chat_message_me').template('agent_chat_message_me');

		this._initMessageHandlers();
		this._initInterface();

		DeskPRO_Window.getSectionData('agent_chat_section', (function(data) {
			this.setHasInitialLoaded();
			this.contentEl.html(data.section_html);
			this.initSection();
		}).bind(this));

		DeskPRO_Window.getMessageBroker().addMessageListener('agentchat-section.list-activated', function (info) {
			this.highlightNavItem($('.agent-' + info.id, this.getSectionElement()));
		}, this);

		DeskPRO_Window.getPoller().addData(
			[{name: 'do[]', value: 'get-online-agents'}],
			'agent.online-agents',
			{recurring: true, minDelay: 210000/*3.5 mintues*/, minDelayAfterOne:true }
		);
	},

	onShow: function() {
		DeskPRO_Window.getSectionData('agent_chat_section', (function(data) {
			this.setHasInitialLoaded();
			this.contentEl.html(data.section_html);
			this.initSection();
		}).bind(this));
	},

	initSection: function() {

	},

	_initMessageHandlers: function() {
		var self = this;
		DeskPRO_Window.getMessageBroker().addMessageListener('agent_chat.new-message', this.newIncomingMessage, this);
		DeskPRO_Window.getMessageBroker().addMessageListener('agent.new-agent-online', function(info) {
			var agent_id = info.agent_id;
			this.addOnlineAgent(agent_id);
		}, this);
		DeskPRO_Window.getMessageBroker().addMessageListener('agent.online-agents', function(info) {
			$('#agent_online_list').find('li').not('.no-agents').remove();
			$('#agent_online_list').find('li.no-agents').show();
			$('#agent_offline_list').find('li').show();
			self.onlineCountEl.html('0');

			Array.each(info.online_agents, function(agent_id) {
				self.addOnlineAgent(agent_id);
			});
		}, this);
	},

	_initInterface: function() {
		this.panelEl = $('#agent_chat_panel');
		this.onlineListEl = $('#agent_online_list');
		this.offlineListEl = $('#agent_offline_list');
		this.onlineCountEl = $('#chat_online_count');
		this.agentTeamList = $('#agent_team_list');

		$('.show-offline-opt', this.panelEl).on('click', function() {
			if ($(this).is(':checked')) {
				$('#agent_chat_panel').addClass('show-offline');
			} else {
				$('#agent_chat_panel').removeClass('show-offline');
			}
		});

		this.panelEl.on('click', function(ev) {
			// dont bubble to doc which will close the panel again
			ev.stopPropagation();
		});

		$('.show-section', this.panelEl).on('click', function() {
			DeskPRO_Window.switchToSection('agent_chat_section');
		});

		$('#agent_chat_section').on('click', (function(ev) {
			ev.stopPropagation();
			this.panelEl.toggleClass('open');
		}).bind(this));
		$('#agent_chat_panel .close-trigger').on('click', (function() {
			this.close();
		}).bind(this));

		// stop propagation for clicks on the chat wrapper
		// so it dorsnt bubble up and close the open chat window
		this.chatsWrapper.on('click', function(ev) {
			ev.stopPropagation();
		});

		var self = this;

		var openChatFn = function (ev) {
			ev.stopPropagation();
			var agent_id = $(this).data('agent-id');
			if (agent_id) {
				self.newChatWindow([agent_id]);
			}
		};

		this.onlineListEl.on('click', 'li', openChatFn);
		this.offlineListEl.on('click', 'li', openChatFn);
		this.agentTeamList.on('click', 'li', function(ev) {
			ev.stopPropagation();
			var agentIds = $(this).data('member-ids') || '';
			agentIds = (agentIds+"").split(',');
			agentIds.include(window.DESKPRO_PERSON_ID);
			agentIds = agentIds.filter(function(x) {
				if (x) return true;
			});

			var name = $(this).data('team-name');

			self.newChatWindow(agentIds, name);
		});

		// Agents/teams tabs
		this.listTabs = new DeskPRO.UI.SimpleTabs({
			triggerElements: $('#agent_chat_panel_listviews > li'),
			onTabSwitch: function(info) {
				if (info.tabEl.is('.teams')) {
					$('.show-offline', self.panelEl).hide();
				} else {
					$('.show-offline', self.panelEl).show();
				}
			}
		});

		$('#agent_chat_panel .view-history-trigger').on('click', function(ev) {
			DeskPRO_Window.switchToSection('agent_chat_section');
			self.close();
		});

		$('li.online-now', '#agent_offline_list').each(function() {
			self.addOnlineAgent($(this).removeClass('online-now').data('agent-id'));
		});

		this.panelEl.on('click', function() {

		});
	},

	close: function() {
		this.panelEl.removeClass('open');
	},

	newChatWindow: function(agent_ids, title) {
		var self = this;

		var chatWin = DeskPRO.Agent.Widget.AgentChatWin_FindAgents(agent_ids);
		if (!chatWin) {
			chatWin = new DeskPRO.Agent.Widget.AgentChatWin({
				agentIds: agent_ids,
				title: title || null,
				onDestroy: function() { self.reflowButtons(); }
			});
		}

		this.close();

		chatWin.open();
	},

	newIncomingMessage: function(info) {
		var self = this;

		var chatWin = DeskPRO.Agent.Widget.AgentChatWin_Find(info.conversation_id);
		if (!chatWin) {
			chatWin = new DeskPRO.Agent.Widget.AgentChatWin({
				convoId: info.conversation_id,
				agentIds: info.participant_ids,
				onDestroy: function() { self.reflowButtons(); }
			});
		}

		chatWin.showMessage(info.author_id, info.message, info.time);
		chatWin.open();
	},

	/**
	 * Re-positions buttons after one is closed
	 */
	reflowButtons: function() {
		var lastChat = null;
		$('> section.agent-chat', this.chatsWrapper).each(function() {
			var left = 0;
			if (lastChat) {
				left += lastChat.position().left + $('> nav', lastChat).outerWidth() + 8;
			}

			$(this).css('left', left);
			lastChat = $(this);
		});
	},

	//#########################################################################
	//# Online agent handling
	//#########################################################################

	addOnlineAgent: function(agent_id) {

		if (typeof agent_id.agent_id != 'undefined') {
			agent_id = agent_id.agent_id;
		}

		var origLi = $('.agent-' + agent_id, this.offlineListEl);

		if (!origLi.length) {
			DP.console.error('No agent element for %i', agent_id);
			return;
		}

		// Make sure they aren't already there (ie logged out/logged in before we could see theyre gone)
		if ($('.agent-' + agent_id, this.onlineListEl).length) {
			return;
		}

		var li = origLi.clone();
		li.show();
		this.onlineListEl.append(li);

		// Offline one is hidden now
		origLi.hide();

		var countInt = parseInt(this.onlineCountEl.html());
		countInt++;
		this.onlineCountEl.html(countInt);

		$('li.no-agents', this.onlineListEl).hide();
	},

	removeOnlineAgent: function(agent_id) {
		if (DESKPRO_PERSON_ID && agent_id == DESKPRO_PERSON_ID) {
			return;
		}

		var li = $('.agent-' + agent_id, this.onlineListEl);
		var offlineLi = $('.agent-' + agent_id, this.offlineListEl);

		if (!li.length) {
			return;
		}

		li.remove();

		// Show them in offline again
		offlineLi.show();

		var countInt = parseInt(this.onlineCountEl.html());
		countInt--;
		this.onlineCountEl.html(countInt);

		if (countInt < 1) {
			$('li.no-agents', this.onlineListEl).show();
		}
	}
});
