Orb.createNamespace('DeskPRO.Form');

DeskPRO.Form.LengthValidator = new Orb.Class({
	Extends: DeskPRO.Form.FieldValidator,

	init: function() {
		var self = this;

		this.min = 1;
		this.max = -1;

		// used with selects to exclude blank selections
		this.excludeBlank = true;

		if (this.el.data('min-len')) {
			this.min = parseInt(this.el.data('min-len'));
		}
		if (this.el.data('max-len')) {
			this.max = parseInt(this.el.data('max-len'));
		}

		if (this.el.data('exclude-blank')) {
			this.excludeBlank = parseInt(this.el.data('exclude-blank'));
			this.excludeBlank = this.excludeBlank ? true : false;
		}

		this.el.on('change', function() {
			self.validate();
		});
	},

	validate: function(action) {
		var len = 0;

		if (this.el.is('select')) {
			if (this.excludeBlank) {
				$('option:selected', this.el).each(function() {
					if ($(this).val() != '0' && $(this).val().trim() !== '') {
						len++;
					}
				});
			} else {
				len = $('option:selected', this.el).length;
			}

		} else {
			len = this.el.val().trim().length;
		}

		var errorCodes = [];

		if (this.min > -1 && len < this.min) {
			errorCodes.push('len_too_short');
		}
		if (this.max > -1 && len > this.max) {
			errorCodes.push('len_too_long');
		}

		this.setErrorCodes(errorCodes);
	},

	_getMyErrorCodes: function() {
		return ['len_too_long', 'len_too_short'];
	}
});
