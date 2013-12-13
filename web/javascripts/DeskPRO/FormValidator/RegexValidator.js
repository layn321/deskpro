Orb.createNamespace('DeskPRO.Form');

DeskPRO.Form.RegexValidator = new Orb.Class({
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

		if (!this.regex) {
			if (this.el.data('regex-modifiers')) {
				this.regex = new RegExp(this.el.data('regex'), this.el.data('regex-modifiers'));
			} else {
				this.regex = new RegExp(this.el.data('regex'));
			}
		}

		if (!this.regex.test(val)) {
			this.setErrorCodes(['pattern_no_match']);
		} else {
			this.setErrorCodes([]);
		}
	},

	_getMyErrorCodes: function() {
		return ['pattern_no_match'];
	}
});
