Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');
DeskPRO.Agent.PageFragment.Page.PersonSession = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'person_session';
	},

	initPage: function(el) {
		this.el = el;

		this.tabs = new DeskPRO.UI.SimpleTabs({
			triggerElements: $('li', this.getEl('chats_tab_triggers'))
		});
	}
});
