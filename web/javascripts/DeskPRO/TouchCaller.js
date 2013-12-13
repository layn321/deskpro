Orb.createNamespace('DeskPRO');

/**
 * This calls a callback function every time something touches the object, but limits
 * it to a minimum timeout period between calls. Feedbackl for things like ajax searchers
 * where you want to update as the user types, but dont want to do a request
 * every millisecond as they enter new characters.
 */
DeskPRO.TouchCaller = new Orb.Class({

	Implements: [Orb.Util.Options],

	initialize: function(options) {
		this.options = {
			/**
			 * The minimum amount of time between the calls
			 */
			timeout: 0,

			/**
			 * The callback to call
			 */
			callback: function() {},

			/**
			 * The object context in which to call the callback
			 */
			context: null,

			/**
			 * Any call to touch fires the event
			 */
			alwaysChange: false
		};

		this.setOptions(options);

		this.touched = null;
		this.lastTouch = null;
		this.lastTime = new Date();
		this.timeout = null;
	},

	touch: function(touch, force) {

		if (touch === '') {
			touch = '(empty)';
		}

		if (typeof touch == 'undefined' || touch == null) {
			this.lastTouch = null;
			touch = true;
		}

		this.touched = touch;

		if (!force) {
			// Not different, nothing to do
			if (!this.options.alwaysChange && (this.lastTouch !== null && this.lastTouch == touch)) {
				return;
			}

			var now = new Date();
			var diff = now.getTime() - this.lastTime.getTime();
			if (diff < this.options.timeout) {
				// Too soon. Wait until timeout expires
				if (!this.timeout) {
					this.timeout = window.setTimeout(this.exec.bind(this), this.options.timeout);
				}
				return;
			}
		}

		// If we got here, we need to run it
		this.exec();
	},

	exec: function() {
		if (this.timeout) {
			window.clearTimeout(this.timeout);
			this.timeout = null;
		}

		this.lastTouch = this.touched;
		this.touched = null;
		this.lastTime = new Date();

		if (this.options.context) {
			this.options.callback.apply(this.options.context);
		} else {
			this.options.callback();
		}
	},

	destroy: function() {
		if (this.timeout) {
			window.clearTimeout(this.timeout);
			this.timeout = null;
		}
		this.options = null;
		this.lastTime = null;
	}
});
