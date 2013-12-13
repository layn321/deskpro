Orb.createNamespace('DeskPRO.User.Chat');

/**
 * User chat handler
 */
DeskPRO.User.Chat.ChatHandler = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(options) {
		this.options = {
			messageChanneler: {},
			messageBroker: null
		};

		this.setOptions(options);

		this.messageBroker = this.options.messageBroker;
		if (!this.messageBroker) {
			this.messageBroker = this.messageBroker = new DeskPRO.MessageBroker();
		}

		this.messageChanneler = new DeskPRO.MessageChanneler.AjaxChanneler(
			this.messageBroker,
			this.options.messageChanneler
		);
	}
});