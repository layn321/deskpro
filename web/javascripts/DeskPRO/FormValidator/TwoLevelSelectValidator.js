Orb.createNamespace('DeskPRO.Form');

DeskPRO.Form.TwoLevelSelectValidator = new Orb.Class({
	Extends: DeskPRO.Form.FieldValidator,

	init: function() {
		var self = this;

		this.parentSel = $('select.parent-option', this.el);
		this.childSel = $('.dp-sub-options > select', this.el);

		this.parentSel.on('change', function() {
			self.validate('change');
		});
		this.childSel.on('change', function() {
			self.validate('change');
		});
	},

	validate: function(action) {

		// Dont trigger errors when simply changing values,
		// because youd see the red box when selecting a new parent
		// that has children. So just show errors on submit
		if (action == 'change' && !this.hasError()) {
			return;
		}

		var errorCodes = [];

		var visibleChild = this.childSel.filter(':visible');
		if (visibleChild.length) {
			var val = $('option:selected', visibleChild).val();
			if (val === '' || val == '0') {
				errorCodes.push('select_child_value');
			}
		} else {
			var val = $('option:selected', this.parentSel).val();
			if (val === '' || val == '0') {
				errorCodes.push('select_value');
			}
		}

		this.setErrorCodes(errorCodes);
	},

	_getMyErrorCodes: function() {
		return ['select_value', 'select_child_value'];
	}
});
