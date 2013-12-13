Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.TicketFlagged = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.BasicTicketResults,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'ticket-flagged';

		this.resultTypeName = 'flagged';
		this.resultTypeId = 0;
	},

	initPage: function(el) {

		this.meta['view_name'] = 'flag';
		this.meta['view_extra'] = this.getMetaData('flag');

		DeskPRO.Agent.PageFragment.ListPane.BasicTicketResults.prototype.initPage.apply(this, [el]);
		this.resultTypeId = this.getMetaData('flag');
	}
});
