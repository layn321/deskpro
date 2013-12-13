Orb.createNamespace('DeskPRO.Agent.PageHelper');

DeskPRO.Agent.PageHelper.StateSaver = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(options) {
		var self = this;
		this.options = {
			/**
			 * The ID that will be used with the preferences
			 */
			stateId: '',

			/**
			 * How long until the state expires
			 */
			expireTime: '+7 days',

			/**
			 * How often to save
			 */
			time: 1000,

			/**
			 * Theres always a "change". Ie it always saves,
			 * no need to triggerChange()
			 */
			alwaysChanged: false,

			/**
			 * A container to listen in on all form elements for changes.
			 * This automatically calls triggerChange() for you.
			 */
			listenOn: null,

			/**
			 * Callback that fetches the data to be saved.
			 * This will serialzie all form fields in listenOn by default.
			 */
			callback: null
		};

		this.setOptions(options);

		if (this.options.listenOn) {
			var wrap = $(this.options.listenOn);
			$(':input, textarea, select', wrap).on('change', function() {
				self.triggerChange();
			});
			$('input[type=text], textarea', wrap).on('keypress', function() {
				self.triggerChange();
			});

			if (!this.options.callback) {
				this.options.callback = function() {
					if (wrap.is('form')) {
						return wrap.serializeArray();
					} else {
						return $(':input, textarea, select', wrap).serializeArray();
					}
				};
			}
		}

		if (!this.options.callback) {
			this.options.callback = function() {};
			DP.console.error('No callback for state save');
		}

		this.doRestartTimer = false;
		this.hasChanged = false;

		if (this.alwaysChanged) {
			this.restartTimer();
		}
	},

	/**
	 * Declare that something has changed so the state needs to be updated
	 */
	triggerChange: function() {
		this.hasChanged = true;
		this.restartTimer();
	},


	/**
	 * Restart the timer
	 */
	restartTimer: function() {

		if (this.ajax) {
			this.doRestartTimer = true;
		}

		if (this.timer) {
			window.clearTimeout(this.timer);
			this.timer = null;
		}

		this.doRestartTimer = false;

		this.timer = window.setTimeout(this.saveState.bind(this), this.options.time);
	},


	/**
	 * Send the AJAX request that saves the state
	 */
	saveState: function() {
		var setData = this.options.callback();
		this.hasChanged = false;

		this.fireEvent('beforeSaveState', [setData]);

		var namePart = function(name) {
			if (name.indexOf('[') === -1) {
				name = '[' + name + ']';
			}  else {
				name = name.replace(/^([\w\d]+)\[(.*?)$/, '[$1][$2');
			}

			return name;
		};

		var data = [];
		data.push({
			name: 'prefs_expire[agent.ui.state.'+this.options.stateId+']',
			value: this.options.expireTime
		});
		if (typeOf(setData) == 'array') {
			Array.each(setData, function(x) {
				data.push({
					name: 'prefs[agent.ui.state.'+this.options.stateId+']' + namePart(x.name),
					value: x.value
				});
			}, this);
		} else {
			data.push({
				name: 'prefs[agent.ui.state.'+this.options.stateId+']',
				value: text
			});
		}

		$.ajax({
			url: BASE_URL + 'agent/misc/ajax-save-prefs',
			type: 'POST',
			data: data,
			context: this,
			complete: function() {
				this.ajax = null;

				if (this.doRestartTimer || this.alwaysChanged) {
					this.restartTimer();
				}
			}
		});
	},

	resetState: function() {
		var data = [];
		data.push({
			name: 'prefs[agent.ui.state.'+this.options.stateId+']',
			value: ''
		});
		data.push({
			name: 'prefs_expire[agent.ui.state.'+this.options.stateId+']',
			value: ''
		});

		window.setTimeout(function() {
			$.ajax({
				url: BASE_URL + 'agent/misc/ajax-save-prefs',
				type: 'POST',
				data: data,
				context: this
			});
		}, 150);
	},

	stop: function() {
		if (this.ajax) {
			this.ajax.abort();
		}
		if (this.timer) {
			window.clearTimeout(this.timer);
			this.timer = null;
		}
	},

	destroy: function() {
		if (this.ajax) {
			this.ajax.abort();
		}
		if (this.timer) {
			window.clearTimeout(this.timer);
			this.timer = null;
		}
	}
});
