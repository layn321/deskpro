Orb.createNamespace('Orb.Util');

Orb.Util.Options = {
	setOptions: function(setOptions){

		var options = $.extend(true, {}, this.options || {}, setOptions);

		if (this.addEvent) {
			for (var option in options){
				if (option == 'defaultEventContext') {
					this.setDefaultEventContext(options[option]);
					delete options[option];
				} else if (typeof options[option] == 'function' && (/^on[A-Z]/).test(option)) {
					this.addEvent(option, options[option]);
					delete options[option];
				}
			}
		}

		this.options = options;

		return this;
	},

	getOption: function(option, default_value) {
		if (typeof this.options[option] === undefined) {
			return default_value;
		}

		return this.options[option];
	}
};