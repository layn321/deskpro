Orb.createNamespace('DeskPRO.MessageChanneler');

/**
 * The pusherapp channeler uses websockets and pusherapp.com to delivery messages.
 */
DeskPRO.MessageChanneler.PusherappChanneler = new Orb.Class({
	Extends: DeskPRO.MessageChanneler.AbstractChanneler,

	_init: function() {

		this._add_subs = [];
		this._add_subs_timeout = null;
		this._del_subs = [];
		this._del_subs_timeout = null;

		this.socket = new Pusher(this.options.apiKey);
	},

	handleMessage: function(event_name, data) {
		var name = this.channelToPusherapp(event_name);
		this.sendMessage(name, data);
	},

	/**
	 * Names in pusherapp use dashes to separate words, we use dots.
	 *
	 * @param name
	 * @return string
	 */
	channelToPusherapp: function(name) {
		return name.replace('-', '.');
	},

	pusherappToChannel: function(name) {
		return name.replace('.', '-');
	},


	//#########################################################################
	//# Handle subcriptions
	//#########################################################################

	subscribeChannel: function(channel) {

		var channel = this.socket.subscribe(channel);
		channel.bind_all(function (event_name, data) {
			var event_name = channel + '-' + event_name;
		});

		this._add_subs.include(channel);

		if (this._add_subs_timeout) {
			window.clearTimout(this._add_subs_timeout);
		}

		this._add_subs_timeout = this._sendSubscribeChannels.delay(200, this);

		if (callback) {
			this.messageBroker.addMessageListener(channel, callback);
		}
	},

	_sendSubscribeChannels: function() {
		var data = [];
		Array.each(this._add_subs, function(v){
			data.push({ name: 'channels[]', value: v });
		});
		this._add_subs = [];

		$.ajax({
			url: this.options.ajaxSubscribeUrl,
			type: 'POST',
			data: data,
			dataType: 'json',
			context: this,
			success: function(data) {
				this._doneSubscribeChannels(data.subscribed_channels);

				Array.each(data.subscribed_channels, function(channel_name) {
					channel_name = this.channelToPusherapp(channel_name);
					var channel = this.socket.subscribe(channel_name);
					var self = this;
					channel.bind_all(function (event_name, data) {
						var event_name = this.puserappToChannel(channel + '-' + event_name);
						self.handleMessage(event_name, data);
					});
				});
			}
		});
	},

	unsubscribeChannel: function(channel) {
		this._del_subs.include(channel);

		if (this._del_subs_timeout) {
			window.clearTimout(this._del_subs_timeout);
		}

		this._del_subs_timeout = this._sendUnsubscribeChannels.delay(200, this);
	},

	_sendUnsubscribeChannels: function() {
		var data = [];
		Array.each(this._del_subs, function(v){
			data.push({ name: 'channels[]', value: v });
		});
		this._del_subs = [];

		$.ajax({
			url: this.options.ajaxUnsubscribeUrl,
			type: 'POST',
			data: data,
			dataType: 'json',
			context: this,
			success: function(data) {
				this._doneUnsubscribeChannels(data.unsubscribed_channels);

				Array.each(data.subscribed_channels, function(channel_name) {
					channel_name = this.channelToPusherapp(channel_name);
					this.socket.unsubscribe(channel);
				});
			}
		});
	}
});
