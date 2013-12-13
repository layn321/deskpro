Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');
DeskPRO.Agent.PageFragment.ListPane.Basic = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.Basic,

	initialize: function(html) {
		this.parent(html);

		this.addEvent('activate', function() {
			DeskPRO_Window.updateWindowUrlFragment();
			DeskPRO_Window.getMessageBroker().sendMessage('list-page-fragment.activated', { page: this });
		}, this);
	},

	enableHighlightOpenRows: function(tabtype, id_property, css_prefix) {
		this.addEvent('watchedTabAdded', function(tab) {
			$(css_prefix + tab.page.meta[id_property], this.wrapper || this.el).addClass('open');
		});
		this.addEvent('watchedTabRemoved', function(tab) {
			$(css_prefix + tab.page.meta[id_property], this.wrapper || this.el).removeClass('open');
		});
		DeskPRO_Window.getTabWatcher().addTabTypeWatcher(tabtype, this, true);
	}
});
