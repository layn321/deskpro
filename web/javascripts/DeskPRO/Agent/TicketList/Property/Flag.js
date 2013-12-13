Orb.createNamespace('DeskPRO.Agent.TicketList.Property');

DeskPRO.Agent.TicketList.Property.Flag = new Class({
	Extends: DeskPRO.Agent.TicketList.Property.Abstract,

	optionName: 'flag',
	displayCaption: 'Flag',

	init: function() {

	},

	getValue: function() {
		return this.getInterfaceElement().data('flag');
	},

	getName: function() {
		return this.optionName;
	},

	setValue: function(value) {
		var last_value = this.getInterfaceElement().data('flag');

		this.getInterfaceElement().data('flag', value);
		this.getInterfaceElement().removeClass('icon-flag-' + last_value).addClass('icon-flag-' + value);
	},

	_getInterfaceElement: function() {
		var el = $(this._buildSelector('.ticket-flag:first'), this.ticketPage.actionsBarHelper.tableEl);
		return el;
	}
});