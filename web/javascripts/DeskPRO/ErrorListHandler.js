Orb.createNamespace('DeskPRO');

DeskPRO.ErrorListHandler = new Orb.Class({
	Implements: [Orb.Util.Options],
	
	initialize: function (options) {
		this.options = {
			errorContainerSelector: '.errors',
			errorListSelector: 'ul'
		};
		
		if (options) {
			this.setOptions(options);
		}
	},

	/**
	 * Handle showing errors in the list for a specific
	 * 'field' (any element, but usually form fields).
	 */
	showErrors: function(field, errors) {
		
		// Looks for errorContainerSelector in current field context
		// or in parent context, and then tries to reveal
		// li's based on the error codes
		
		var field = $(field);
		var field_id = field.attr('id');
		
		var el = $('#' + field_id);
		var el_id = field_id;
		var error_container = el.closest(this.options['errorContainerSelector']);
		
		// Hide all LI's first to 'reset'
		// ul .my_element_id
		$(this.options['errorListSelector'] + ' .' + el_id, error_container).hide(this.options['errorClassHide']);
		
		// Now go through and show the errors we need to show
		if (errors && errors.length) {
			for (var i = 0; i < errors.length; i++) {
				// ul .my_element_id.my_error_code
				// Note that no space   ^ because we want an intersection (aka both classes to apply)
				$(this.options['errorListSelector'] + ' .' + el_id + '.' + errors[i], error_container).show();
			}
		}
		
		// If there are no errors displayed, then we can just hide the entire error container
		var num_errors = $(this.options['errorListSelector'], error_container).not('.'+this.options['errorClassHide']).size();
		if (!num_errors) {
			error_container.hide();
		}
		
		// Or show it if its not already
		if (num_errors && error_container.is(':hidden')) {
			error_container.fadeIn('fast');
		}
	}
});