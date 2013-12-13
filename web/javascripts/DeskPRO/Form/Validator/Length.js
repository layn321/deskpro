Orb.createNamespace('DeskPRO.Form.Validator');

DeskPRO.Form.Validator.Length = new Class({
	Extends: Orb.Validator.Length,
	
	_checkForErrors: function(field) {
		var value = field.getFormInputElements().val();
		return this.parent(value);
	}
});