Orb.createNamespace('DeskPRO.Agent.TicketList.Property');

/**
 * New reply area
 */
DeskPRO.Agent.TicketList.Property.NewReply = new Class({
	Extends: DeskPRO.Agent.TicketList.Property.Abstract,

	displayCaption: 'Reply',

	getName: function() {
		return 'new_reply';
	},

	isSameValue: function(compare) {
		return false;
	},

	getValue: function() {
		return null;
	},

	setValue: function(value) {
		if (value) {
			var text = value;
			this.getInterfaceElement().removeClass('no-value').text(text);
		}
	},

	setIncomingValue: function(value) {

	},

	_getInterfaceElement: function() {
		el = this.getSublineElement();
		return el;
	}
});