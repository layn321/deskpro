Orb.createNamespace('DeskPRO');

/**
 * Go through the dom tree and attach element handlers on an object.
 *
 * @param jQuery context Context. Defaults to the entire document.
 */
DeskPRO.ElementHandler_Exec = function(context) {
	$('[data-element-handler]', context || document).not('.with-handler').each(function() {
		var el = $(this);
		var className = el.data('element-handler');
		var classObj = Orb.getNamespacedObject(className);

		if (!classObj) {
			DP.console.error("Unknown element handler `%s` on element %o", className, this);
			return;
		}

		if (!el.attr('id')) {
			el.attr('id', Orb.getUniqueId('dp_'));
		}

		try {
			var obj = new classObj(el);
			el.addClass('with-handler');
		} catch (e) {
			console.error("Failed created element-handler (%o) : %o", className, e);
		}
	});
};

/**
 * An element handler takes a single element, usually a central wrapper element
 * of some kind, and adds functionality to it. Generally handlers are defined in
 * HTML source with data-element-handler="xxx" and attached at runtime.
 *
 * A handler can optionally register itself with a parent elements handler by defining
 * a selector with data-register-handler="li.parent" (we'll walk up the DOM tree until its found).
 * This way a parent might have code that can give the child handler options or other necessary connections.
 */
DeskPRO.ElementHandler = new Orb.Class({

	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(el) {

		this.el = el;
		this.options = {};
		this.childHandlers = {};
		this.parentHandlerElement = null;

		this.init();

		this.el.data('handler', this);

		var initNow = true;

		if (this.el.data('register-handler')) {

			var sel = this.el.data('register-handler');
			if (sel == "1" || !sel.length || sel == "yes" || sel == "true") {
				sel = '.with-handler';
			}

			var parentHandlerEl = this.el.closest(sel);
			if (parentHandlerEl.length) {
				if (parentHandlerEl.data('handler')) {
					this.parentHandlerElement = parentHandlerEl;
					parentHandlerEl.data('handler')._registerChildHandler(this.el);
				} else {
					DP.console.error('Parent handler element %s has no handler object on element %o and handler %o', sel, this.el, this);
				}
				initNow = false;
			} else {
				DP.console.error('Unknown parent handler element %s on element %o and handler %o', sel, this.el, this);
			}
		}

		if (initNow) {
			this.initPage();
		}
	},

	/**
	 * Called when the object is first initiated.
	 */
	init: function() {

	},

	/**
	 * Called when the object is ready to affect the DOM. This is after pageready, and after
	 * any parents have called back to set options.
	 */
	initPage: function() {

	},


	/**
	 * Register a child handler
	 *
	 * @param el
	 */
	_registerChildHandler: function(el) {
		this.childHandlers[el.attr('id')] = el;

		var ret = this.registerChildHandler(el.data('handler'), el.data('handler').getHandlerName(), el) || {};
		this.fireEvent('childHandler', [el, ret, this]);

		el.data('handler').setParentReturnOptions(ret);
	},


	/**
	 * Register a child handler and return options to it.
	 *
	 * @param handler
	 * @param handlerName
	 * @param el
	 * @return {Object}
	 */
	registerChildHandler: function(handler, handlerName, el) {
		return {};
	},


	/**
	 * When this handler is registered with a parent, and the parent returns
	 * data, its passed to this function for the handler to process it.
	 *
	 * @param ret
	 */
	setParentReturnOptions: function(ret) {
		this.setOptions(ret);
		this.fireEvent('parentReturn', [ret, this.parentHandlerElement, this]);

		this.initPage();
	},


	/**
	 * Get the handler name/type. This is mainly use with registering a handler with a parent.
	 * If the parent knows the type, then it might call back the child to return data.
	 */
	getHandlerName: function() {
		return 'element_handler';
	}
});
