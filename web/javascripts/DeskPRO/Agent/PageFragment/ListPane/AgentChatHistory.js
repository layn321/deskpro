Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.AgentChatHistory = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.Basic,

	initializeProperties: function() {
		this.parent();
		this.wrapper = null;
		this.contentWrapper = null;
	},

	initPage: function(el) {

		DeskPRO_Window.getMessageBroker().sendMessage('agentchat-section.list-activated', { id: this.meta.agentId });

		this.wrapper = $(el);
		this.contentWrapper = $('div.content:first', this.wrapper);

		this.enableHighlightOpenRows('agentchat', 'conversation_id', '.row-item.convo-');

		if (this.getMetaData('noResults')) {
			this.noMoreResults = true;
			$('.no-more-results', this.contentWrapper).show();
		}
	}
});
