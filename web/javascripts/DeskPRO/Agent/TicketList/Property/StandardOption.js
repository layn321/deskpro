Orb.createNamespace('DeskPRO.Agent.TicketList.Property');

/**
 * These standard options are simple values taken from a menu. Since
 * they're so similar, this single property class can handle all of them.
 */
DeskPRO.Agent.TicketList.Property.StandardOption = new Class({
	Extends: DeskPRO.Agent.TicketList.Property.Abstract,

	optionName: null,

	init: function() {
		var valid_options = ['department', 'category', 'product', 'priority', 'workflow', 'status', 'agent', 'agent_team'];

		if (valid_options.indexOf(this.options.optionName) == -1) {
			throw 'invalidOptionName:'+this.options.optionName;
		}

		this.optionName = this.options.optionName;

		switch (this.optionName) {
			case 'department': this.displayNameType = 'department_full'; this.displayCaption = 'Department'; break;
			case 'category': this.displayNameType = 'ticket_category_full'; this.displayCaption = 'Category'; break;
			case 'product': this.displayNameType = 'product'; this.displayCaption = 'Product'; break;
			case 'priority': this.displayNameType = 'ticket_priority'; this.displayCaption = 'Priority'; break;
			case 'workflow': this.displayNameType = 'ticket_workflow'; this.displayCaption = 'Workflow'; break;
			case 'status': this.displayNameType = 'status'; this.displayCaption = 'Status'; break;
			case 'agent': this.displayNameType = 'agent'; this.displayCaption = 'Agent'; break;
			case 'agent_team': this.displayNameType = 'agent_team'; this.displayCaption = 'Agent Team'; break;
		}
	},

	getValue: function() {
		return this.getInterfaceElement().data('prop-value');
	},

	getName: function() {
		return this.optionName;
	},

	setValue: function(value) {
		if (value == "0") value = 0;

		this.getInterfaceElement().data('prop-value', value);

		if (value) {
			var displayName = value;
			if (this.displayNameType) {
				displayName = DeskPRO_Window.getDisplayName(this.displayNameType, value);
				if (!displayName) displayName = value;
			}

			var text = displayName;

			if (this.getInterfaceElement().is('.generated')) {
				var text = this.displayCaption + ': ' + displayName;
			}

			this.getInterfaceElement().removeClass('no-value').text(text);
		} else {
			this.getInterfaceElement().addClass('no-value').text('none');
		}
	},

	_getInterfaceElement: function() {
		var el = $(this._buildSelector('.prop-val.' + this.optionName+':first'), this.ticketPage.actionsBarHelper.tableEl);

		if (!el.length) {
			el = this.getSublineElement();
		}

		return el;
	}
});