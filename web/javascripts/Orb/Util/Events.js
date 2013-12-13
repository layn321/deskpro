Orb.createNamespace('Orb.Util');

Orb.Util.Events = {

	__initEventsObj: function() {
		if (!this.__events) {
			this.__events = {};
			this.__events_tagged = {};
			this.__preventCleanupTagged = false;
		}
	},

	setDefaultEventContext: function(context) {
		this.__events_default_context = context;
	},

	normalizeEventName: function(type) {
		return type.toLowerCase().replace(/^on/, '');
	},

	addEvent: function(type, fn, context, tags, beginning){

		this.__initEventsObj();

		type = this.normalizeEventName(type);
		if (!context) {
			context = this.__events_default_context;
		}

		if (!this.__events[type]) {
			this.__events[type] = [];
		}

		if (beginning && this.__events[type].length) {
			var newVal = [];
			newVal.push([fn, context]);
			for (var i = 0; i < this.__events[type].length; i++) {
				newVal.push(this.__events[type][i]);
			}

			this.__events[type] = newVal;
		} else {
			this.__events[type].push([fn, context]);
		}

		if (context && context.OBJ_ID) {
			tags = (tags || []).push(context.OBJ_ID);
		} else if (fn.OBJ_ID) {
			tags = (tags || []).push(fn.OBJ_ID);
		}

		if (tags) {
			Array.each(tags, function(tag) {
				if (!this.__events_tagged[tag]) {
					this.__events_tagged[tag] = [];
				}

				this.__events_tagged[tag].push([type, fn, context]);
			}, this);
		}

		return this;
	},

	addEvents: function(events, context, tags){
		for (var type in events) {
			this.addEvent(type, events[type], context, tags);
		}
		return this;
	},

	fireEvent: function(type, args, delay){
		this.__initEventsObj();

		type = this.normalizeEventName(type);
		if (!this.__events[type]) {
			return this;
		}

		var defaultContext = this.__events_default_context || this;

		args = Array.from(args);
		Object.each(this.__events[type], function(fn_info){
			if (delay) {
				fn_info[0].delay(delay, fn_info[1] || defaultContext, args);
			} else {
				fn_info[0].apply(fn_info[1] || defaultContext, args);
			}
		});

		return this;
	},

	removeEvent: function(type, fn, context){
		var newFns = [], hasChange = false;

		this.__initEventsObj();

		type = this.normalizeEventName(type);
		if (!this.__events[type]) {
			return this;
		}

		if (!context) {
			context = null;
		}

		Array.each(this.__events[type], function(fn_info){
			if (fn_info[0] == fn && fn_info[1] == context) {
				hasChange = true;
			} else {
				newFns.push(fn_info);
			}
		});

		if (hasChange) {
			this.__events[type] = newFns;

			if (!this.__preventCleanupTagged) {
				this.__cleanupTaggedEvents();
			}
		}

		return this;
	},

	removeEvents: function(events, context){
		var type;

		this.__initEventsObj();

		this.__preventCleanupTagged = true;

		for (type in this.__events){
			if (events && events != type) continue;
			var fns = this.__events[type];
			for (var i = fns.length; i--;) if (i in fns){
				this.removeEvent(type, fns[i], context);
			}
		}

		this.__preventCleanupTagged = false;
		this.__cleanupTaggedEvents();
		return this;
	},

	removeTaggedEvents: function(tag) {
		if (!this.__events_tagged[tag]) return;

		this.__preventCleanupTagged = true;

		Array.each(this.__events_tagged[tag], function (x) {
			this.removeEvent(x[0], x[1], x[2]);
		}, this);

		this.__preventCleanupTagged = false;
		this.__cleanupTaggedEvents();
	},

	__cleanupTaggedEvents: function() {
		return;
		Object.each(this.__events_tagged, function(tag_fns, tag) {
			var newTaggedFns = [], hasChange = false;
			Array.each(tag_fns, function(tag_fn) {
				if (fn != tag_fn) {
					newTaggedFns.push(fn);
				} else {
					hasChange = true;
				}
			});

			if (hasChange) {
				if (newTaggedFns.length) {
					this.__events_tagged[tag] = newTaggedFns;
				} else {
					delete this.__events_tagged[tag];
				}
			} else {
				newTaggedFns = null;
			}
		}, this);
	}
};
