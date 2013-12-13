Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.TicketDeletedList = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.BasicTicketResults,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'ticket-deleted-list';

		this.resultTypeName = 'filter';
		this.resultTypeId = 0;
	},

	initPage: function(el) {
		this.parent(el);
		this.resultTypeId = this.getMetaData('cache_id');
	}
});
