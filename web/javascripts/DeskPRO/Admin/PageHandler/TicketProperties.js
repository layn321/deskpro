Orb.createNamespace('DeskPRO.Admin.PageHandler');

DeskPRO.Admin.PageHandler.TicketProperties = new Class({
	Extends: DeskPRO.Admin.PageHandler.Basic,

	initialize: function() {

	},

	initPage: function() {
		this.initPopoutTriggers();
	}
});