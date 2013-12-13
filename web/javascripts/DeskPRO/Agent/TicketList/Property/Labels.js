Orb.createNamespace('DeskPRO.Agent.TicketList.Property');

DeskPRO.Agent.TicketList.Property.Labels = new Class({
	Extends: DeskPRO.Agent.TicketList.Property.Abstract,

	optionName: 'labels',
	displayCaption: 'Flag',

	mode: 'add',

	init: function() {
		this.mode = this.options.mode;

		if (this.mode == 'add') {
			this.displayCaption = 'Add Labels';
		} else {
			this.displayCaption = 'Remove Labels';
		}
	},

	isSameValue: function(compare) {
		return false;
	},

	getName: function() {
		return this.mode + '_' + this.optionName; // add_labels or remove_labels
	},

	getValue: function() {
		return this._values;
	},

	_values: null,

	setValue: function(values) {
		this._values = values;

		var text = this.displayCaption + ': ';

		Array.each(values, function(val) {
			text += ' '+val;
		}, this);

		this.getInterfaceElement().text(text);
	},

	_getInterfaceElement: function() {
		var el = this.getSublineElement();
		return el;
	}
});