Orb.createNamespace('DeskPRO.Form.Validator');

DeskPRO.Form.Validator.Regex = new Class({
	Extends: Orb.Validator.Regex,
	
	_checkForErrors: function(field) {
		var value = field.getFormInputElements().val();
		return this.parent(value);
	}
});