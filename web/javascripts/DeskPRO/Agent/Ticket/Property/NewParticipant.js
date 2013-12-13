Orb.createNamespace('DeskPRO.Agent.Ticket.Property');

DeskPRO.Agent.Ticket.Property.NewParticipant = new Class({
	Extends: DeskPRO.Agent.Ticket.Property.Abstract,

	getName: function() {
		return 'new_participant';
	},

	getValue: function() {
		return 0;
	},

	setValue: function(value) {
		$('div.ticket-participants ul', this.ticketPage).add('<li>' + value + '</li>');
	},

	getInterfaceElement: function() {
		return $([]);
	}
});
