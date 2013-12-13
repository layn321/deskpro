Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.TicketSla = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.BasicTicketResults,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'ticket-sla';

		this.resultTypeName = 'sla';
		this.resultTypeId = 0;
	},

	initPage: function(el) {

		DeskPRO_Window.getMessageBroker().sendMessage('ticket-section.list-activated', { listType: 'sla', id: this.getMetaData('sla_id'), topGroupingOption: this.meta.topGroupingOption || null });
		this.resultTypeId = this.getMetaData('sla_id');
		this.parent(el);
	},

	activate: function() {
		if (this.getMetaData('sla_id')) {
			DeskPRO_Window.getMessageBroker().sendMessage('sla.view-activated', this.getMetaData('sla_id'));
		}
	},

	deactivate: function() {
		if (this.getMetaData('sla_id')) {
			DeskPRO_Window.getMessageBroker().sendMessage('sla.view-deactivated', this.getMetaData('sla_id'));
		}
	},

	updateSlaListForTicket: function(info) {
		if (!info.ticket_id || !info.sla_id) {
			return;
		}

		// run this for every SLA change, as a ticket may have multiple SLAs
		// and the general status could change
		// todo: in the future we could possibly resolve this without always
		// refreshing if we look at the list and only update if there's a ticket
		// with this SLA
		this.refreshSlaTicketList();
	},

	refreshSlaTicketList: function() {
		var self = this;

		if (this.isRefreshing) {
			return;
		}
		this.isRefreshing = true;

		setTimeout(function() {
			self.isRefreshing = false;
			DeskPRO_Window.loadListPane(self.meta.refreshUrl);
		}, 0);
	}
});
