Orb.createNamespace('DeskPRO.Admin.PageHandler');

DeskPRO.Admin.PageHandler.TicketFiltersList = new Class({
	Extends: DeskPRO.Admin.PageHandler.Basic,

	initPage: function() {
		this.initPopoutTriggers();

		this.tabs = new DeskPRO.UI.SimpleTabs({
			triggerElements: $('#ticket_filter_types_tabs > ul > li')
		});
	}
});