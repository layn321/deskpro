Orb.createNamespace('Orb.Compat.WebForms');

Orb.Compat.WebForms.isPlaceholderSupported = function() {
	this.isSupported = null;

	if (this.isSupported === null) {
		this.isSupported = ('placeholder' in document.createElement('input'));
	}

	return this.isSupported;
};

Orb.Compat.WebForms.placeholder = function(input) {

	if (!input) return null;

	input_col = $(input);
	if (!input_col.length) return null;

	input_col.each(function() {

		var input = $(this);

		// Check if its already supported
		if (input.placeholder && this.isPlaceholderSupported()) return;

		// Get the palceholder and check that its actually a value
		var placeholder = input.attr('placeholder');
		if (!placeholder || !placeholder.length) return;

		// Make sure we havent already run the placeholder compat
		if (input.is('.has-placeholder')) return;

		input.addClass('has-placeholder');

		// See if we should enable the placeholder now
		if (input.val() === '' || input.val() == placeholder) {
			input.val(placeholder);
			input.addClass('placeholder-visible');
		}

		input.focus(function() {
			if (input.is('.placeholder-visible')) {
				input.val('');
				input.removeClass('placeholder-visible');
			}
		});

		input.blur(function() {
			if (input.val() === '') {
				input.addClass('placeholder-visible');
				input.val(placeholder);
			} else {
				input.removeClass('placeholder-visible');
			}
		});

		if (input.get(0) && input.get(0).form) {
			$(input.get(0).form).submit(function() {
				if (input.is('.placeholder-visible')) {
					input.val('');
				}
			});
		}
	});
};