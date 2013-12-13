Orb.createNamespace('Orb.Validator');

Orb.Validator.Length = new Class({
	Extends: Orb.Validator.AbstractValidator,
	
	_setDefaultOptions: function() {
		this.setOptions({
			regex: /.?/,
			trim: true
		});
	},
	
	_checkForErrors: function(value) {
		
		if (this.options['trim']) {
			value = value.trim();
		}
		
		if (!value.match(this.options['regex'])) {
			this.addError('no_match');
		}
	}
});