Orb.createNamespace('DeskPRO.Form.Validator');

/**
 * AJAX validator
 */
DeskPRO.Form.Validator.Ajax = new Class({
	Extends: Orb.Validator.AbstractValidator,
	
	isAjaxValidator: true,
	
	/**
	 * Is the value valid
	 *
	 * @return {Boolean}
	 */
	isValid: function(field, callback) {
		this.errors = [];

		var data = field.getFormInputElements().serializeArray();
		var options = $.merge({
			data: data,
			success: function (data) {
				var errors = [];
				if (data.errors) errors = data.errors;
				
				callback(errors);
			},
			error: function (XMLHttpRequest, textStatus, errorThrown) {
				callback([]);
			}
		}, this.options['ajax']);
		
		$.ajax(options);
	},
	
	_setDefaultOptions: function() {
		this.setOptions({
			ajax: {
				url: '',
				type: 'POST',
				dataType: 'json'
			}
		});
	}
	
});