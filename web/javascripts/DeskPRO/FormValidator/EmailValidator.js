Orb.createNamespace('DeskPRO.Form');

DeskPRO.Form.EmailValidator = new Orb.Class({
	Extends: DeskPRO.Form.FieldValidator,

	init: function() {
		var self = this;

		this.el.on('change', function() {
			self.validate('change');
		});
	},

	validate: function(action) {
		var len = 0;
		var val = this.el.val().trim();

		if (val.length < 3 || val.indexOf('@') < 1) {
			this.setErrorCodes(['invalid_email']);
		} else {
			this.setErrorCodes([]);
		}
	},

	_getMyErrorCodes: function() {
		return ['invalid_email'];
	}
});
