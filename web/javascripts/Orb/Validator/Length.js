Orb.createNamespace('Orb.Validator');

Orb.Validator.Length = new Class({
	Extends: Orb.Validator.AbstractValidator,
	
	_setDefaultOptions: function() {
		this.setOptions({
			minLength: null,
			maxLength: null,
			trim: true
		});
	},
	
	_checkForErrors: function(value) {
		
		if (this.options['trim']) {
			value = value.trim();
		}
		
		var len = value.length;
		
		if (this.options['minLength'] && len < this.options['minLength']) {
			this.addError('too_short');
		}
		if (this.options['maxLength'] && len > this.options['maxLength']) {
			this.addError('too_long');
		}
	}
});