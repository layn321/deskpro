var dpchat = {
	init: function(options) {
		options = options || {};

		if (window.jQuery === undefined || window.jQuery.fn.jquery.indexOf('1.5.') !== -1) {

			var initJquery = function() {
				dpchat.$ = window.jQuery.noConflict(true);
				dpchat.main(options);
			};

			if (!options.protocol) {
				options.protocol = ('https:' == document.location.protocol ? 'https' : 'http');
			}

			var script_tag = document.createElement('script');
			script_tag.setAttribute("type","text/javascript");
			script_tag.setAttribute("src", options.protocol + "://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js");
			script_tag.setAttribute("async", 'true');
			script_tag.onload = function() { initJquery(); };
			script_tag.onreadystatechange = function () { // Same thing but for IE
				if (this.readyState == 'complete' || this.readyState == 'loaded') {
					initJquery();
				}
			};
			
			(document.getElementsByTagName("head")[0] || document.documentElement).appendChild(script_tag);
		} else {
			dpchat.main(options);
		}
	},

	main: function(options) {
		this.options = this.$.extend({}, {
			staticUrl: null,
			deskproUrl: null,
			protocol: null,
			displayType: 'Box'
		}, options || {});

		if (this.staticUrl === null) {
			
		}
	}
};