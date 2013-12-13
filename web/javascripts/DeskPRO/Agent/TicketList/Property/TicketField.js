Orb.createNamespace('DeskPRO.Agent.TicketList.Property');

DeskPRO.Agent.TicketList.Property.TicketField = new Class({
	Extends: DeskPRO.Agent.TicketList.Property.Abstract,

	optionName: null,

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

	_getInterfaceElement: function() {
		var el = $('.prop-val .custom-field-' + this.options.fieldId, this.ticketPage.actionsBarHelper.tableEl);

		if (!el.length) {
			el = this.getSublineElement();
		}

		return el;
	}
});