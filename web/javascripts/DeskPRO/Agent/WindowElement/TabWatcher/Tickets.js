Orb.createNamespace('DeskPRO.Agent.WindowElement.TabWatcher');

/**
 * This just handles releasing locks on tickets
 */
DeskPRO.Agent.WindowElement.TabWatcher.Tickets = new Orb.Class({
	Implements: [Orb.Util.Events],

	initialize: function() {
		this.addEvent('activateTab', this.activateTab, this);
		this.addEvent('deactivateTab', this.deactivateTab, this);

		this.releasing = {};

		DeskPRO_Window.getMessageBroker().addMessageListener('agent-notification.tickets.unlocked', function(info) {
			var ticketId = info.ticket_id;
			Array.each(DeskPRO_Window.getTabWatcher().findTabType('ticket'), function(tab) {
				if (tab.page.getMetaData('ticket_id') == ticketId && tab.page.ticketLocked) {
					tab.page.ticketLocked.unlock();
				}
			});
		}, this);
	},

	activateTab: function(tab) {

		// When a ticket is opened, double-check that we dont have a release
		// request already processing. If so, cancel it, because we just opened it again
		// (ie a quick open/close)

		var ticketId = tab.page.getMetaData('ticket_id');
		if (this.releasing[ticketId]) {
			this.releasing[ticketId].abort();
			delete this.releasing[ticketId];
		}
	},

	deactivateTab: function(tab) {

	}
});
