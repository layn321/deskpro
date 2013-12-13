Orb.createNamespace('DeskPRO.AjaxPoller');

/**
 * A specialized ajax poller where the returned AJAX result contains a correctly
 * formatted JSON structure:
 *
 * <code>
 * { messages: [{name: 'some.message.name', data: 'data'}, ...] }
 * </code>
 *
 * These messages are passed through a message broker for handling in the app.
 */
DeskPRO.AjaxPoller.MessagePoller = new Orb.Class({
	Extends: DeskPRO.AjaxPoller.Poller,

	initialize: function(messageBroker, options) {
		this.parent(options);

		this.messageBroker = messageBroker;

		this.addEvent('ajaxSuccess', this._sendMessages, this);
	},

	getMessageBroker: function() {
		return this.messageBroker;
	},

	_sendMessages: function(data) {

		if (data.messages === undefined || typeOf(data.messages) != 'array') {
			return;
		}

		var message = null;
		while (message = data.messages.shift()) {
			this.messageBroker.sendMessage(message[0], message[1]);
		}
	}
});
