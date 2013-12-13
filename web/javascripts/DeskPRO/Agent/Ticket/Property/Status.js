Orb.createNamespace('DeskPRO.Agent.Ticket.Property');

DeskPRO.Agent.Ticket.Property.Status = new Class({
	Extends: DeskPRO.Agent.Ticket.Property.Abstract,

	setValue: function(value) {

		var hidden_status = false;
		var status_classname = value;

		if (value && value.constructor.toString().indexOf("Array") != -1) {
			hidden_status = value[1].value;
			value = value[0].value;
			status_classname = value;
		} else {
			if (value.indexOf('.') != -1) {
				var parts = value.split('.');

				var value = parts[0];
				var hidden_status = parts[1];
				status_classname = value + '_' + hidden_status;

				this.ticketPage.fireEvent('ticketHidden', [hidden_status]);
			}
		}

		this.ticketPage.wrapper.find('div.layout-content').removeClass('awaiting_agent awaiting_user resolved closed hidden_deleted hidden_spam hidden_validating hidden_temp').addClass(status_classname);

		$('input.status:first', this.ticketPage.valueForm).val(value);
		$('input.hidden_status:first', this.ticketPage.valueForm).val(hidden_status);

		// Hold is automatically taken off on PHP side when not awaiting user,
		// so need to reshow the 'set hold' button now incase user toggles status back to awaiting agent
		if (value == 'awaiting_agent') {
			this.ticketPage.getEl('hold_container').css('display', 'inline');
		} else {
			this.ticketPage.getEl('hold_container').css('display', 'none');
			this.ticketPage.getEl('hold_container').find('.hold').show();
			this.ticketPage.getEl('hold_container').find('.unhold').hide();
		}

		this.ticketPage.getEl('status_code').select2('val', value);

		var scode = value;
		if (hidden_status) {
			value += '_' + hidden_status;
		}
		this.ticketPage.getEl('status_code').val(value);

		var txt = this.ticketPage.getEl('status_code').find('option:selected').text().trim();
		this.getInterfaceElement().text(txt);
	},

	getValue: function() {
		var data = [];
		data.push({
			full_name: 'actions[status]',
			value: $('input.status:first', this.ticketPage.valueForm).val()
		});

		data.push({
			full_name: 'actions[hidden_status]',
			value: $('input.hidden_status:first', this.ticketPage.valueForm).val()
		});

		return data;
	},

	getInterfaceElement: function() {
		return this.ticketPage.getEl('status_txt');
	},

	getName: function() {
		return 'status';
	}
});
