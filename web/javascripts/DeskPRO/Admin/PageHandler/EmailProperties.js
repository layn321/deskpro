Orb.createNamespace('DeskPRO.Admin.PageHandler');

DeskPRO.Admin.PageHandler.EmailProperties = new Class({
	Extends: DeskPRO.Admin.PageHandler.Basic,

	initialize: function() {

	},

	initPage: function() {
		this.tabs = new DeskPRO.UI.SimpleTabs({
			triggerElements: $('#email_properties_tabs > ul > li')
		});
	}
});