Orb.createNamespace('DeskPRO.Agent.Ticket.Property');

/**
 * New reply area
 */
DeskPRO.Agent.Ticket.Property.Reply = new Class({
	Extends: DeskPRO.Agent.Ticket.Property.Abstract,

	optionName: null,
	menuRepository: null,

	getName: function() {
		return 'reply';
	},

	getValue: function() {
		this.ticketPage.getEl('replybox_wrap').find('textarea[name="message"]').val();
	},

	highlightInterfaceElement: function() {
		this.ticketPage.getEl('replybox_wrap').find('textarea[name="message"]').addClass('highlight-change-on');
	},

	unhighlightInterfaceElement: function() {
		this.ticketPage.getEl('replybox_wrap').find('textarea[name="message"]').removeClass('highlight-change-on');
	},

	setValue: function(value) {
		if (value.reply_text) {
			value = value.reply_text;
		}

		return this.ticketPage.getEl('replybox_wrap').find('textarea[name="message"]').val(value);
	},

	setIncomingValue: function(value) {

	},

	getInterfaceElement: function() {
		return this.ticketPage.getEl('replybox_wrap').find('textarea[name="message"]');
	}
});
