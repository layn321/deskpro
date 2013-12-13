Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.TicketFilter = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.BasicTicketResults,

	initializeProperties: function() {
		this.TYPENAME = 'ticket-filter';

		this.resultTypeName = 'filter';
		this.resultTypeId = 0;
	},

	initPage: function(el) {

		DeskPRO_Window.getMessageBroker().sendMessage('ticket-section.list-activated', { listType: 'filter', id: this.getMetaData('filter_id'), topGroupingOption: this.meta.topGroupingOption || null });
		this.resultTypeId = this.getMetaData('filter_id');
		this.parent(el);
	},

	activate: function() {
		if (this.getMetaData('filter_id')) {
			DeskPRO_Window.getMessageBroker().sendMessage('filter.view-activated', this.getMetaData('filter_id'));
		}
	},

	deactivate: function() {
		if (this.getMetaData('filter_id')) {
			DeskPRO_Window.getMessageBroker().sendMessage('filter.view-deactivated', this.getMetaData('filter_id'));
		}
	}
});
