Orb.createNamespace('DeskPRO.MessageChanneler');

/**
 * The AJAX channeler uses AJAX polling to fetch new messages from the server.
 */
DeskPRO.MessageChanneler.AjaxChanneler = new Orb.Class({
	Extends: DeskPRO.MessageChanneler.AbstractChanneler,

	_init: function() {
		this.count = 0;
		this.hasDoneInitialLoad = false;

		this.lastMessageId = -1;
		this.poller = new DeskPRO.AjaxPoller.Poller({
			ajaxUrl: this.options.ajaxMessagesUrl,
			interval: this.options.interval,
			ajaxType: 'SMART',
			postTypes: [
				'dismiss_alerts',
				'recent_tabs'
			]
		});

		this.poller.addData((function () {
			if (this.lastMessageId === null) return null;
			return { 'since': this.lastMessageId };
		}).bind(this), 'since', { recurring: true });

		this.poller.addData((function () {
			if (!DeskPRO_Window.dismissAlertQueue.length) return null;

			var q = DeskPRO_Window.dismissAlertQueue;
			DeskPRO_Window.dismissAlertQueue = [];

			var send = [];
			Array.each(q, function(item) {
				send.push({
					name: 'dismiss_alerts[]',
					value: item
				})
			});

			return send;
		}).bind(this), 'dismiss_alerts', { recurring: true });

		this.poller.addData((function () {
			return { 'count': ++this.count };
		}).bind(this), 'since', { recurring: true });

		this.poller.addData((function () {
			if (!(DeskPRO_Window.recentTabs && DeskPRO_Window.recentTabs.recentPendingSync && DeskPRO_Window.recentTabs.recentPendingSync.length)) {
				return null;
			}

			var recent = [];
			Array.each(DeskPRO_Window.recentTabs.recentPendingSync, function(item, idx) {
				recent.push([
					item[0],
					item[1],
					item[2],
					item[3],
					item[4]
				]);
			});

			DeskPRO_Window.recentTabs.recentPendingSync = [];

			var recentData = JSON.stringify(recent);
			return [{name: 'recent_tabs', value: recentData}];
		}).bind(this), 'recent_tabs', { recurring: true });

		this.poller.addData({is_initial_poll:1}, 'is_initial_poll');

		this.poller.addEvent('ajaxSuccess', this.handleMessageAjax.bind(this));

		if (this.options.lastMessageId) {
			this.lastMessageId = this.options.lastMessageId;
		}
	},

	handleMessageAjax: function(data) {
		if (!data) {
			console.debug("Data is false");
			return;
		}

		var ins_order, i, x, d, messages;
		var ordered = {};
		var orders = [];

		if (data.messages && data.messages.length) {
			for (x = 0; x < data.messages.length; x++) {
				d = data.messages[x];
				if (d[0] && (parseInt(d[0], 10) <= this.lastMessageId) && (!d[3] || !d[3]['offline_messsage'])) {
					console.debug("%o Dropping message older than lastMessageId %d", d, this.lastMessageId);
					return;
				}

				var ins_order = 50;
				if (d[1] == 'agent.ticket-updated') {
					ins_order = 55;
				}

				if (!ordered[ins_order]) {
					ordered[ins_order] = [];
					orders.push(ins_order);
				}

				ordered[ins_order].push(d);

				if (d[0] && d[0] > this.lastMessageId) {
					this.lastMessageId = d[0];
				}
			}
		}

		orders.sort(function(a,b){return a - b});
		for (i = 0; i < orders.length; i++) {
			messages = ordered[orders[i]];
			for (x = 0; x < messages.length; x++) {
				d = messages[x];

				try {
					this.sendMessage(d[1], d[2]);
				} catch (err) {
					DpErrorLog.logError('[AjaxChanneler] ' + err, err.stack || '', err.fileName || '', err.lineNumber || '');
				}
			}
		}

		if (typeof data.last_id != 'undefined' && parseInt(data.last_id) > this.lastMessageId) {
			this.lastMessageId = parseInt(data.last_id);
		}

		this.fireEvent('postMessageSend', [data]);
		this.hasDoneInitialLoad = true;
	},

	getLastMessageId: function() {
		return this.lastMessageId;
	},

	setLastMessageId: function(messageId) {
		this.lastMessageId = messageId;
	}
});
