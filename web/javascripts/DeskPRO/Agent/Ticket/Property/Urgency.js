Orb.createNamespace('DeskPRO.Agent.Ticket.Property');

DeskPRO.Agent.Ticket.Property.Urgency = new Class({
	Extends: DeskPRO.Agent.Ticket.Property.Abstract,

	setValue: function(value) {
		this.ticketPage.getEl('urgency').select2('val', value);

		var layoutDiv = this.ticketPage.wrapper.find('div.layout-content');

		if (!this.getInterfaceElement()[0]) {
			return;
		}

		this.getInterfaceElement().text(value || 1);
		this.getInterfaceElement().get(0).className = this.getInterfaceElement().get(0).className.replace(/urgency\-value\-\d+/g, '');
		layoutDiv.get(0).className = layoutDiv.get(0).className.replace(/urgency\-\d+/g, '');
		this.getInterfaceElement().addClass('urgency-value-' + value);
		layoutDiv.addClass('urgency-' + value);
	},

	getValue: function() {
		return this.ticketPage.getEl('urgency').val();
	},

	getInterfaceElement: function() {
		return this.ticketPage.getEl('urgency_txt');
	},

	getName: function() {
		return 'urgency';
	}
});
