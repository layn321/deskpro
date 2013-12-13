Orb.createNamespace('DeskPRO.Agent.WindowElement.TabWatcher');

/**
 * This handles increasing poll frequency when a userchat is open
 */
DeskPRO.Agent.WindowElement.TabWatcher.UserChat = new Orb.Class({
	Implements: [Orb.Util.Events],

	initialize: function() {
		this.addEvent('watchedTabAdded', this.tabAdded, this);
		this.addEvent('watchedTabRemoved', this.tabRemoved, this);
	},

	tabAdded: function(tab) {
		console.debug('Enabling fast poller interval: %d', DP_POLLER_INTERVAL_FAST);
		DeskPRO_Window.getMessageChanneler().poller.setInterval(DP_POLLER_INTERVAL_FAST);
	},

	tabRemoved: function(tab) {
		// If there are no more chat tabs, restore the default poller interval
		var tabs = DeskPRO_Window.tabWatcher.findTabType('userchat');
		if (!tabs.length) {
			console.debug('Restoring poller interval: %d', DP_POLLER_INTERVAL);
			DeskPRO_Window.getMessageChanneler().poller.setInterval(DP_POLLER_INTERVAL);
		}
	}
});
