Orb.createNamespace('DeskPRO.Agent.Ticket.Property');

DeskPRO.Agent.Ticket.Property.Hold = new Class({
	Extends: DeskPRO.Agent.Ticket.Property.Abstract,

	optionName: 'is_hold',

	init: function() {

	},

	getName: function() {
		return this.optionName;
	},

	getValue: function() {
		return this.getFormEl().val();
	},

	setValue: function(value) {
		if (typeof value === 'boolean') {
			value = value ? 1 : 0;
		} else {
			value = parseInt(value);
		}

		this.getFormEl().val(value);

		if (value) {
			this.ticketPage.getEl('hold_message').show();
			this.ticketPage.getEl('menu_set_hold').hide();
			this.ticketPage.getEl('menu_unset_hold').show();
		} else {
			this.ticketPage.getEl('hold_message').hide();
			this.ticketPage.getEl('menu_set_hold').show();
			this.ticketPage.getEl('menu_unset_hold').hide();
		}
	},

	_formEl: null,
	getFormEl: function() {
		if (this._formEl !== null) return this._formEl;

		this._formEl = $('input.is_hold:first', this.ticketPage.valueForm);

		return this._formEl;
	}
});
