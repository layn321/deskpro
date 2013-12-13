Orb.createNamespace('DeskPRO');

DeskPRO.IntervalCaller = new Orb.Class({

	Implements: [Orb.Util.Options],

	initialize: function(options) {
		this.options = {
			/**
			 * Does a touch reset the timer?
			 */
			touchResets: true,

			/**
			 * Is a touch required to run the callback?
			 * If we're not touched when the timer runs out, then nothing will happen.
			 */
			touchRequired: true,

			/**
			 * If touchResets is true, whats the max time to pass before
			 * we call the callback anyway?
			 */
			resetTimeForce: 0,

			/**
			 * The callback to call
			 */
			callback: function() {},

			/**
			 * The object context in which to call the callback
			 */
			context: null,

			/**
			 * The timer timeout
			 */
			timeout: null,

			/**
			 * Autostart the timer as soon as this class is instantiated
			 */
			autostart: true
		};

		this.setOptions(options);

		if (this.options.autostart) {
			this.start();
		}

		this.touched = false;
		this.paused = false;
		this.lastTime = new Date();
	},

	start: function() {
		if (this.timer) {
			window.clearTimeout(this.timer);
			this.timer = null;
		}

		this.timer = window.setTimeout(this.exec.bind(this), this.options.timeout);
	},

	stop: function() {
		if (this.timer) {
			window.clearTimeout(this.timer);
			this.timer = null;
		}
	},

	touch: function() {
		this.touched = true;
		if (this.options.touchResets) {
			if (this.options.resetTimeForce) {
				var now = new Date();
				var diff = now.getTime() - this.lastTime.getTime();
				if (diff > this.options.resetTimeForce) {
					return;
				}
			}

			// Restart if its not over max time
			this.start();
		}
	},

	exec: function(force) {

		if (!force && this.options.touchRequired && !this.touched) {
			this.start();
			return;
		}

		this.lastTime = new Date();
		this.touched = false;

		if (this.options.context) {
			this.options.callback.apply(this.options.context);
		} else {
			this.options.callback();
		}

		this.start();
	},

	execNow: function() {
		this.exec();
	},

	destroy: function() {
		this.stop();
		this.options = null;
		this.lastTime = null;
	}
});
