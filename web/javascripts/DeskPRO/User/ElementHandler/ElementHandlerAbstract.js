Orb.createNamespace('DeskPRO.User.ElementHandler');

/**
 * Element handlers are detected in the source and instantiated automatically with the
 * element being passed in.
 *
 * Element handlers are always instantiated when the document is ready.
 */
DeskPRO.User.ElementHandler.ElementHandlerAbstract = new Orb.Class({

	Implements: [Orb.Util.Options],

	initialize: function(options) {
		this.options = {};
		if (options) {
			this.setOptions(options);
		}

		this.el = null;
		if (this.options.el) {
			this.el = $(this.options.el);
		}

		this.init();
	},


	/**
	 * Empty hook method for children to implement init code
	 */
	init: function() {

	}
});