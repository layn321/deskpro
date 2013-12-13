Orb.createNamespace('DeskPRO.MessageChanneler');

/**
 * A message channeler handles subscribing to channels, and delivering messages
 * from the server to the message broker, which in turn notifies any listeners.
 */
DeskPRO.MessageChanneler.AbstractChanneler = new Orb.Class({

	Implements: [Orb.Util.Options, Orb.Util.Events],

	initialize: function(messageBroker, options) {

		this.channels = [];
		this.options = {};

		this.messageBroker = messageBroker;
		if (options) this.setOptions(options);

		this._init();
	},

	_init: function() { /* Child class hook method */ },

	sendMessage: function(channel, message) {

		if (DeskPRO_Window && DeskPRO_Window.getDebug('logClientMessages')) {
			DP.console.log('channel(%s): %o', channel, message);
		}

		this.messageBroker.sendMessage(channel, message);
	}
});
