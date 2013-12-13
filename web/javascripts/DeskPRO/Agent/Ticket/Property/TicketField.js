Orb.createNamespace('DeskPRO.Agent.Ticket.Property');

DeskPRO.Agent.Ticket.Property.TicketField = new Class({
	Extends: DeskPRO.Agent.Ticket.Property.Abstract,

	optionName: '',

	init: function() {
		this.optionName = 'ticket_field.' + this.options.fieldId;
	},

	getName: function() {
		return this.optionName;
	},

	getValue: function() {
		return this.getInterfaceElement().html();
	},

	setValue: function(value) {
		this.getInterfaceElement().html(value);
	},

	getInterfaceElement: function() {
		return $('.show-fields .custom-field-' + this.options.fieldId + ' .field-input', this.ticketPage.contentWrapper);
	},

	isDisplayOnly: function() {
		return true;
	}
});
