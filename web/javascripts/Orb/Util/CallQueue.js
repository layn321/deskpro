Orb.createNamespace('Orb.Util');

Orb.Util.CallQueue = new Orb.Class({
	initialize: function(options) {
		this.queue  = [];
		this.length = 0;
		this.hasFiredStart = false;
		this.startCallback = options.startCallback || null;
		this.endCallback   = options.endCallback   || null;
	},
	setStartCallback: function(startCallback) {
		this.startCallback = startCallback;
	},
	setEndCallback: function(endCallback) {
		this.endCallback = endCallback;
	},
	addCall: function(fn, bind) {
		this.queue.push([fn, bind || null]);
		this.length++;
	},
	call: function(fn, bind) {
		this.queue.push([fn, bind || null]);
		this.length++;

		this.next();
	},
	hasNext: function() {
		return this.queue.length;
	},
	next: function() {
		var next = this.queue.shift();

		if (!next) {
			this.hasFiredStart = false;
			if (this.endCallback) {
				this.endCallback();
			}
			return;
		}

		this.length--;

		if (!this.hasFiredStart) {
			this.hasFiredStart = true;
			if (this.startCallback) {
				this.startCallback();
			}
		}

		var fn = next[0];
		var bind = next[1];

		if (!bind) {
			bind = fn;
		}

		return fn.call(bind);
	}
});
