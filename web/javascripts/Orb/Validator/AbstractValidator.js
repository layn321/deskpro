Orb.createNamespace('Orb.Validator');

/**
 * Validator validates some value
 */
Orb.Validator.AbstractValidator = new Class({
	Implements: Options,
	
	/**
	 * An array of error codes
	 * @var {Array}
	 */
	errors: [],
	
	/**
	 * @var {Object}
	 */
	options: {},
	
	initialize: function(options) {
		this._setDefaultOptions();
		this.setOptions(options);
		this._init();
	},
	
	
	/**
	 * Empty hook called after constructor
	 */
	_init: function() {
		
	},
	
	
	
	/**
	 * Empty hook to set default optons
	 */
	_setDefaultOptions: function() {
		
	},
	
	
	
	/**
	 * Is the value valid
	 *
	 * @return {Boolean}
	 */
	isValid: function(value) {
		this.errors = [];
		
		this._checkForErrors(value);
		
		if (this.errors.length) {
			return false;
		}
		
		return true;
	},
	
	
	
	/**
	 * Hook function to check for errors.
	 */
	_checkForErrors: function(value) {
		
	},
	
	
	
	/**
	 * Get errors
	 *
	 * @param {Array}
	 */
	getErrors: function() {
		return this.errors;
	},
	
	
	
	/**
	 * Add an error
	 *
	 * @param {String} code
	 */
	addError: function(code) {
		this.errors.push(code);
	}
});