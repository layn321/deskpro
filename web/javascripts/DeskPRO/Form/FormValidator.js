Orb.createNamespace('DeskPRO.Form');

/**
 * A form validator just handles running validators and displaying errors.
 *
 * Note that this JS concept of forms/validation differs from the DeskPRO
 * PHP Form components in that theres this FormValidator that handles
 * validation (instead of validation being attached to specific form elements).
 * This is because in JS we also have to worry about triggers such as onSubmit
 * or onChange, and that is better suited to a controller like this than
 * on the actual FormField object.
 *
 * The default error handler:
 * 1. Finds closest container with class .errors. Starts at the field node,
 * and will continue up the dom tree until its found. This means it's possible
 * to show all errors in one big error list if you wanted to.
 * 2. Each validator when it errors returns an error code such as 'too_long'.
 * The handler will try and unhide an li element with class '.field_id.too_long'.
 * This means all errors must already be rendered in the page; so by default
 * when a user loads a page all fields should have a CSS class that hides them.
 *
 * For example:
 * <code>
 *     <div class="errors">
 *     	The username you entered is incorrect
 *     	<ul>
 *     		<li class="error_off username too_short">Please enter at least 3 characters</li>
 *     		<li class="error_off username too_long">The longest username is 15 characters</li>
 *     		<li class="error_off username taken">That username is already taken</li>
 *     	</ul>
 *     </div>
 *     <input type="text" name="username" id="username" value="" />
 *     </div>
 * </code>
 *
 * Every form field can have any number of local validators (regex etc), and at most one
 * ajax validator (so put all ajax logic into the one call).
 *
 * Example:
 * <code>
 *    var formval = new DeskPRO.Form.FormValidator();
 *
 *    var username_field = new new DeskPRO.Form.FormField($('#username'));
 *    formval.addValidator(username_field, new DeskPRO.Form.Validator.Length({
 *    	minLength: 3,
 *    	maxLength: 15
 *    }), ['change']);
 *
 *    formval.addValidator(username_field, new DeskPRO.Form.Validator.Ajax({
 *    	ajax: { url: 'check_username.php' }
 *    }), ['submit']);
 * </code>
 */
DeskPRO.Form.FormValidator = new Class({
	Implements: Options,

	validators: null,
	field_errors: null,

	running_ajax: null,

	/**
	 * Hash of options
	 * @var {Object}
	 */
	options: {
		errorContainerSelector: '.errors',
		errorListSelector: 'ul',
		errorClassHide: 'error_off',
		errorCallback: null
	},

	errorListHandler: null,


	initialize: function (options) {
		options = options ||{};
		this.setOptions(options);

		if (!this.options['errorCallback']) {
			this.errorListHandler = new DeskPRO.ErrorListHandler({
				errorContainerSelector: this.options['errorContainerSelector'],
				errorListSelector: this.options['errorListSelector'],
				errorClassHide: this.options['errorClassHide']
			});
		}

		this.validators = new Hash();
		this.field_errors = new Hash();
		this.runing_ajax = new Hash();
	},


	/**
	 * Add a form validator to one or more elements.
	 *
	 * @option {Boolean} enableInstant Enable/disable the instant validator (overrides default)
	 *
	 * @param {jQuery} element One element jQuery collection
	 * @param {Object} validator A form validator
	 * @param {Object} options
	 */
	addValidator: function(field, validator, triggers) {

		if (!triggers) triggers = ['submit'];

		if (!this.validators.has(field.getId())) {
			this.validators.set(field.getId(), {
				field: field,
				validators: [],
				validators_status: new Hash(),
				currentErrors: []
			});
		}

		this.validators.get(field.getId()).validators.push([validator, triggers]);

		var has_change_validator = triggers.contains('change');

		// Whenever an input field is changed, store the last changed time
		// so dont re-run validators we dont have to
		var self = this;
		this.getFormInputElements().on('change', function() {
			$(this).attr('data-changed-at', (new Date()).getTime());
			if (has_change_validator) {
				self.runValidators(field, 'change');
			}
		});
	},


	/**
	 * Run validators on a field for a given trigger (submit, change)
	 */
	runValidators: function(field, trigger) {

		var info = this.validators.get(field.getId());

		// Reset error status
		this.runCallback(field, null);
		info.currentErrors = [];

		var els = field.getFormInputElements();

		var last_changed = 0;
		els.each(function() {
			if ($(this).attr('data-changed-at') && $(this).attr('data-changed-at') > last_changed) {
				last_chagned = $(this).attr('data-changed-at');
			}
		});

		// If its not the submit trigger, then we dont
		// want to run any validator unless the user has changed
		// the field at least once.
		if (trigger != 'submit' && last_changed == 0) {
			return [];
		}

		//------------------------------
		// Run the validators that need to run
		//------------------------------

		var errors = [];

		var ajax_validator = null;

		for (var i = 0; i < info.validators.length; i++) {

			// The validator has already run on this data,
			// dont need to run it again
			if (info.validator_status.has(i) && info.validator_status.get(i) > last_changed) {
				continue;
			}

			var validator = info.validators[i];

			// If its an ajax validator, we'll run it in a sec
			if (validator.isAjaxValidator) {
				ajax_validator = validator;
				continue;
			}

			if (!validator.isValid(field)) {
				errors.combine(validator.getErrors());
			}

			info.validator_status.set(i, (new Date()).getTime());
		}

		info.currentErrors = errors;

		this.runCallback(field, errors);

		// run the ajax validator now
		if (ajax_validator) {
			this.running_ajax.set(field.getId(), ajax_validator);

			var self = this;
			ajax_validator.isValid(field, function(errors) {
				if (!errors.length) {
					return;
				}

				info.currentErrors.combine(errors);
				self.runCallback(field, errors);

				self.running_ajax.erase(field.getId());
				if (!self.running_ajax.getLength()) {
					self.allAjaxComplete();
				}
			});
		}
	},


	allAjaxComplete: function() {
		// TODO
		// Handle onsubmit handlers, when all ajax is
		// complete we need to see if we can submit the form
		// now
	},


	/**
	 * Run the error handler callback
	 */
	runCallback: function(field, errors) {
		// A custom callback
		if (this.options['errorCallback']) {
			this.options['errorCallback'](field, errors);
			return;
		}

		this.errorListHandler.showErrors(field, errors);
	}
});
