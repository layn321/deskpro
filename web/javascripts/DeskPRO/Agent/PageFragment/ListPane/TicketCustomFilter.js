Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.TicketCustomFilter = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.BasicTicketResults,

	initPage: function(el) {

		if (this.getMetaData('view_label')) {
			DeskPRO_Window.getMessageBroker().sendMessage('ticket-section.list-activated', { listType: 'label', id: this.getMetaData('label') });
		} else if (this.getMetaData('view_flag')) {
			DeskPRO_Window.getMessageBroker().sendMessage('ticket-section.list-activated', { listType: 'flag', id: this.getMetaData('flag') });
		} else if (this.getMetaData('view_spam')) {
			DeskPRO_Window.getMessageBroker().sendMessage('ticket-section.list-activated', { listType: 'archive', id: this.getMetaData('spam') });
		} else if (this.getMetaData('view_validating')) {
			DeskPRO_Window.getMessageBroker().sendMessage('ticket-section.list-activated', { listType: 'archive', id: this.getMetaData('validating') });
		} else if (this.getMetaData('view_recycle_bin')) {
			DeskPRO_Window.getMessageBroker().sendMessage('ticket-section.list-activated', { listType: 'archive', id: this.getMetaData('recycle_bin') });
		}


		this.parent(el);
		this.resultTypeId = this.getMetaData('cache_id');
	}
});
