Orb.createNamespace('DeskPRO.Agent.Ticket.Property');

DeskPRO.Agent.Ticket.Property.Flag = new Class({
	Extends: DeskPRO.Agent.Ticket.Property.Abstract,

	optionName: 'flag',

	init: function() {

	},

	getName: function() {
		return this.optionName;
	},

	getValue: function() {
		return this.ticketPage.getEl('flag').val();
	},

	getInterfaceElement: function() {
		return this.ticketPage.getEl('flag');
	},

	setValue: function(value) {
		var old_flag = this.ticketPage.getEl('flag_old').val();
		this.ticketPage.getEl('flag_old').val(value);
		this.ticketPage.getEl('flag').val(value);

		if (old_flag == value) {
			return;
		}

		if (old_flag && old_flag != "") {
			DeskPRO_Window.util.modCountEl($('#ticket_flag_'+old_flag+'_count'), '-', 1);
		}

		var winCountEl = $('#ticket_flag_'+value+'_count');
		DeskPRO_Window.util.modCountEl(winCountEl, '+', 1);

		var label = winCountEl.closest('li').find('.flag-label').text().trim();
		this.ticketPage.getEl('flagicon').get(0).className = this.ticketPage.getEl('flagicon').get(0).className.replace(/flag\-color\-\w+/g, '');
		if (value) {
			this.ticketPage.getEl('flagicon').addClass('flag-color-' + value);
			this.ticketPage.getEl('flagtext').text(label);
		} else {
			this.ticketPage.getEl('flagtext').text('');
		}
	}
});
