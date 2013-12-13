Orb.createNamespace('DeskPRO.Form');

/**
 * A simple event chainer that is meant to apply to onsubmit handlers.
 * This will fire each callback in the chain with the event object
 * unless one returns false. If a callback returns false, the chain
 * breaks and the submit is cancelled.
 *
 * Useful for things like running validations, but if you have multiple
 * validators that need to run or different kinds of systems that could
 * all potentially halt.
 */
DeskPRO.Form.PresubmitChain = new Class({

	/**
	 * The form
	 * @var {HTMLElement}
	 */
	form: null,

	/**
	 * An array of callbacks
	 * @var {Array}
	 */
	callbacks: [],



	/**
	 * @param {HTMLElemenet} form_el The HTML form we're attaching to
	 */
	initialize: function(form_el) {
		this.form = form_el;

		var self = this;
		$(this.form).on('submit', function(ev) { return self.handleSubmit(ev); });
	},



	/**
	 * Handles a submit
	 *
	 * @parma {jQuery.Event} ev The jQuery event
	 */
	handleSubmit: function(ev) {

		for (var i = 0; i < this.callbacks.length; i++) {
			var fn = this.callbacks[i];
			if (!fn(this.form, ev)) {
				ev.preventDefault();
				return false;
			}
		}

		return true;
	},



	/**
	 * Add a new callback. The callback must take two arguments:
	 * - {HTMLElement} form: The HTML form element
	 * - {jQuery.Event} ev: The jQuery event object
	 *
	 * A callbuck should return false if the chain should be cut
	 * and the submit cancelled.
	 *
	 * @param {Function} fn
	 */
	addCallback: function(fn) {
		this.callbacks.puhs(fn);
	}
});
