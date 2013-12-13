Orb.createNamespace('DeskPRO.Form');

DeskPRO.Form.FieldValidator = new Orb.Class({
	Implements: [Orb.Util.Events],

	initialize: function(el) {
		this.el = $(el);

		if (this.el.data('val-wrap-sel')) {
			this.wrapper = this.el.closest(this.el.data('val-wrap-sel'));
		} else {
			this.wrapper = this.el.closest('.dp-form-row, .dp-control-group').first();
		}

		this.init();
	},

	init: function() { },

	setErrorCodes: function(codes) {
		console.log("Errors on %o: %o", this, codes);
		this.wrapper.removeClass('dp-error-' + this._getMyErrorCodes().join(' dp-error-'));

		if (codes && codes.length) {
			if (typeof codes == 'string') {
				codes = [codes];
			}
			this.wrapper.addClass('dp-error-' + codes.join(' dp-error-'));

			this.wrapper.addClass('dp-error');

		// None of our errors, remove dp-form-error
		// if we know no others are added
		} else {
			if (!this.wrapper.is('[class*="dp-error-"]')) {
				this.wrapper.removeClass('dp-error');
			}
		}
	},

	/**
	 * Checks to see if error state is currently on
	 */
	hasError: function() {
		if (this.wrapper.is('.dp-error-' + this._getMyErrorCodes().join(', .dp-error-'))) {
			return true;
		}

		return false;
	},

	/**
	 * Runs the validator on the class. This doesnt return a value, but should
	 * set the proper classes on the wrapper so that hasErorr() properly runs.
	 */
	validate: function(action) {
		// Override in subclass
	},

	/**
	 * Returns an array of possible error codes this class adds
	 */
	_getMyErrorCodes: function() {
		// Override in subclass
	},

	destroy: function() {

	}
});
