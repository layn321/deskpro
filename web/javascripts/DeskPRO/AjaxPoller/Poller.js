Orb.createNamespace('DeskPRO.AjaxPoller');

/**
 * An AJAX poller takes a bunch of data and sends it in packs based on an interval.
 */
DeskPRO.AjaxPoller.Poller = new Orb.Class({

	Implements: [Orb.Util.Events, Orb.Util.Options],

	/**
	 * @option {String} ajaxUrl The URL that will handle the data we POST with this poller.
	 * @option {Integer} interval Interval time in milliseconds where the filter is sent automatically.
	 *                            Note that specific items may have a max wait time that might fire the interval
	 *                            before this time.
	 *
	 * @param {DeskPRO.MessageBroker} messageBroker The message broker to send return data through
	 * @param {Object} options Options to set.
	 */
	initialize: function(options) {

		this.dataTransformers = [];
		this.filterdData = [];
		this.messageBroker = null;

		this.maxDelayTimers = [];
		this.isPaused = false;

		this.autoSendTimeout = null;

		this.options = {
			ajaxUrl: null,
			interval: 6000,
			alwaysRequest: false,
			ajaxType: 'SMART',
			postTypes: []
		};

		this.disabled = false;

		this.setOptions(options);

		this.autoSendTimeout = this.send.delay(this.options.interval, this);
	},


	pause: function() {
		this.isPaused = true;
	},

	unpause: function() {
		this.isPaused = false;
	},

	setInterval: function(interval) {
		this.options.interval = interval;
	},



	/**
	 * Data transformers intercept data before it's sent and can change it
	 * or augment it.
	 */
	addDataTransformer: function(name, callback) {
		this.dataTransformers.push(callback);
	},



	/**
	 * Run the data transformers on a piece of data to be sent.
	 */
	transformData: function(name, data, options) {

		var nameparts = name.split('.');
		var cur_name = null;

		while (nameparts.pop()) {
			cur_name = nameparts.join('.') + '.*';
			if (this.dataTransformers[cur_name] !== undefined) {
				this.dataTransformers[cur_name].each(function(callback) {
					data = callback(data, options, name);
				});
			}
		}

		return data;
	},



	/**
	 * Add some data to send on the next poll.
	 *
	 * @option {Integer} maxDelay The max time in milliseconds to wait before a request is sent automatically with this data.
	 * @option {Integer} minDelay The min time in milliseconds to wait before this data will be sent
	 * @option {Boolean} recurring To automatically re-add this data after it's been sent
	 *
	 * @param {Object} data Data to send. This must be in the jQuery data format, either a `k:v` object or `[{name: k, value: v}]` array.
	 *                      Can optionally be a function instead, which when called must return false or the same format as above.
	 * @param {String} name Name for the data, used with transformers to identify the type.
	 * @param {Object} options Options
	 */
	addData: function(data, name, options) {
		name = name || 'default';
		options = options || {};

		if (options.addedTime === undefined) {
			options.addedTime = new Date();
		}

		if (options.maxDelay) {
			(function() {
				this.send();
			}).delay(options.maxDelay, this);
		}

		this.filterdData.push([name, data, options]);
	},



	/**
	 * Send all filterd data items now.
	 */
	send: function() {

		this._clearDelays();

		if (this.isPaused) {
			// Paused, reset timer and dont do anything
			this.autoSendTimeout = this.send.delay(this.options.interval, this);
			return;
		}

		if (!this.options.alwaysRequest && !this.filterdData.length) {
			this.autoSendTimeout = this.send.delay(this.options.interval, this);
			return;
		}

		//------------------------------
		// Build data to send
		//------------------------------

		var now = new Date();

		var send_data = [];
		var sent_info = [];
		var hasPostType = false;

		var filterdData = this.filterdData;
		this.filterdData = [];

		var item = null;
		while (item = filterdData.shift()) {
			var item_name = item[0];
			var item_data = item_orig_data = item[1];
			var item_opts = item[2];

			if (item_opts.minDelay && !(item_opts.minDelayAfterOne && !item_opts.sentCount)) {
				// If its too soon, add it back immediately
				if (item_opts.minDelay > (now.getTime() - item_opts.addedTime.getTime())) {
					this.addData(item_orig_data, item_name, item_opts);
					continue;
				}
			}

			if (typeOf(item_data) == 'function') {
				item_data = item_data(item_name, {}, item_opts);
			}

			item_data = this.transformData(item_name, item_data, item_opts);
			sent_info.push([item_orig_data, item_name, item_opts]);

			if (!item_data) continue;

			if (!hasPostType && this.options.postTypes.indexOf(item_name) !== -1) {
				hasPostType = true;
			}

			if (typeOf(item_data) == 'array') {
				send_data.append(item_data);
			} else {
				Object.each(item_data, function(v, k) {
					send_data.push({ name: k, value: v });
				});
			}
		}

		if (!this.options.alwaysRequest && !sent_info.length) {
			this._handleAjaxSuccess({}, sent_info);
			return;
		}

		//------------------------------
		// Send data
		//------------------------------

		type = this.options.ajaxType;
		if (type == 'SMART') {
			if (hasPostType) {
				type = 'POST';
			} else {
				type = 'GET';
			}
		}

		$.ajax({
			cache: false,
			type: type,
			url: this.options.ajaxUrl,
			context: this,
			data: send_data,
			dataType: 'json',
			dpIsPolling: true,
			success: function (data) {
				this._handleAjaxSuccess(data, sent_info);
			},
			error: function(xhr, textStatus, errorThrown) {
				this._handleAjaxError(sent_info, xhr, textStatus, errorThrown);
			}
		});
	},



	/**
	 * Handles AJAX success.
	 *
	 * Reattaches recurring data points if there are any, and then sends
	 * messages through the message broker for handling.
	 *
	 * @param {Object} data The data returned from the AJAX call
	 * @param {Object} sent_data Information about data we sent in the call
	 */
	_handleAjaxSuccess: function (data, sent_info) {

		if (data && data.request_token) {
			window.DP_REQUEST_TOKEN = data.request_token;
		}

		// Start auto timer
		this.autoSendTimeout = this.send.delay(this.options.interval, this);

		this.resetSentItems(sent_info);

		this.fireEvent('ajaxSuccess', data);
	},

	resetSentItems: function(sent_info) {
		var item;
		while (item = sent_info.shift()) {
			var item_name = item[0];
			var item_data = item[1];
			var item_opts = item[2];

			if (item_opts.recurring) {
				item_opts.lastSent = new Date();

				if (item_opts.sentCount === undefined) item_opts.sentCount = 0;
				item_opts.sentCount++;

				// Delete addedTime so minDelay check will reset too
				delete item_opts.addedTime;

				this.addData(item_name, item_data, item_opts);
			}
		}
	},

	_handleAjaxError: function (sent_info, xhr, textStatus, errorThrown) {
		this.resetSentItems(sent_info);

		// Start auto timer
		this.autoSendTimeout = this.send.delay(this.options.interval, this);

		DP.console.error("Polling Error %s for %o", textStatus, xhr);

		this.fireEvent('ajaxError', [xhr, textStatus, errorThrown]);
	},



	/**
	 * Clear all delay timeouts
	 */
	_clearDelays: function() {

		this.autoSendTimeout = window.clearTimeout(this.autoSendTimeout);
		this.autoSendTimeout = null;

		var t = null;
		while (t = this.maxDelayTimers.pop()) {
			window.clearTimeout(t);
		}
	}
});
