Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.OpenChats = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.Basic,


	initPage: function(el) {
		var self = this;
		this.el = el;

		var agentId = this.meta.agent_id;

		var removeConvo = function(convoId) {
			var row = $('article.convo-' + convoId);
			row.slideUp('fast', function() {
				row.remove();
			});
		};
		var addConvo = function(convoId) {
			// TODO later prepend the row like in tickets,
			// for now this is easiest
			DeskPRO_Window.loadRoute(self.meta.routeData);
		};

		DeskPRO_Window.getMessageBroker().addMessageListener('chat.new', function(data) {
			if (data.agent_id && data.agent_id == agentId) {
				addConvo(data.conversation_id);
			}
		}, this);
		DeskPRO_Window.getMessageBroker().addMessageListener('chat.reassigned', function(data) {
			if (data.old_agent_id && data.old_agent_id == agentId) {
				removeConvo(data.conversation_id);
			} else if (data.agent_id && data.agent_id == agentId) {
				addConvo(data.conversation_id);
			}
		}, this);
		DeskPRO_Window.getMessageBroker().addMessageListener('chat.unassigned', function(data) {
			if (data.old_agent_id && data.old_agent_id == agentId) {
				removeConvo(data.conversation_id);
			}
		}, this);
		DeskPRO_Window.getMessageBroker().addMessageListener('chat.ended', function(data) {
			if (data.agent_id && data.agent_id == agentId) {
				removeConvo(data.conversation_id);
			}
		}, this);

		this.enableHighlightOpenRows('userchat', 'conversation_id', '.row-item.convo-');
	}
});
