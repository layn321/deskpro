Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.PublishSearchLog = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'publish_searchlog';
	},

	initPage: function(el) {
		this.tabs = new DeskPRO.UI.SimpleTabs({
			triggerElements: $('li', this.getEl('tabs'))
		});
		this.ownObject(this.tabs);
	}
});
