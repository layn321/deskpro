Orb.createNamespace('DeskPRO.Admin.PageHandler');

DeskPRO.Admin.PageHandler.Basic = new Orb.Class({

	Implements: [Orb.Util.Events],

	initialize: function(context, options) {

		this.options = {};
		this.meta = {};

		if (context) {
			this.contextEl = $(context);
		} else {
			this.contextEl = $(document.body);
		}

		options = options || {};
		this.options = options;

		this.messageBroker = DeskPRO_Window.getMessageBroker();
	},

	getMessageBroker: function() {
		return this.messageBroker;
	},

	initPage: function() {

	},

	/**
	 * Set metadata about this page.
	 *
	 * @param mixed name Either a string name to use with value, or an object of key/value pairs
	 * @param mixed value Only used if name is a string, the value to set
	 */
	setMetaData: function(name, value) {
		// Assigning multiple values from a hash
		if (value === undefined && typeOf(name) == 'object') {
			this.meta = Object.merge(this.meta, name);
		} else {
			this.meta[name] = value;
		}
	},



	/**
	 * Get a hash of all the metadata.
	 *
	 * @return {Object}
	 */
	getAllMetaData: function() {
		return this.meta;
	},



	/**
	 * Get a specific piece of metadata.
	 *
	 * @param {String} name The name of the data you want
	 * @param mixed default_value The value to return if the metadata is undefined
	 */
	getMetaData: function(name, default_value) {
		if (default_value === undefined) {
			default_value = null;
		}

		if (this.meta[name] === undefined) {
			return default_value;
		}

		return this.meta[name];
	}
});
