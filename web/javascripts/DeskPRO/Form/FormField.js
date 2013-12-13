Orb.createNamespace('DeskPRO.Form');

/**
 * Simple wrapper around a form field
 */
DeskPRO.Form.FormField = new Class({
	field: null,

	/**
	 * @param {jQuery} el The element
	 */
	initialize: function (el) {
		this.field = el;

		this.getFormInputElements().on('change', function() {
			$(this).attr('data-changed-at', (new Date()).getTime());
		});
	},



	/**
	 * Get the fields ID
	 */
	getId: function() {
		if (!el.attr('id').length) {
			el.attr('id', Orb.getUniqueId('field-'));
		}

		return el.attr('id');
	},



	/**
	 * Given a jQuery collection, return elements that are input elements.
	 *
	 * @param {jQuery}
	 * @return {jQuery}
	 */
	getFormInputElements: function() {
		// Already an input element
		if (this.field.is('input, textarea, select')) {
			return this.field;
		}

		// A collection of input elements
		return this.field.filter('input, textarea, select');
	}
});
