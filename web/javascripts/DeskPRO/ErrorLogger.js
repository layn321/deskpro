var DpErrorLog = {
	saveUrl: null,
	hasSentReport: true,
	logCount: 0,
	init: function() {
		if (!this.saveUrl) {
			return;
		}

		if (window.jQuery && window.jQuery.cookie) {
			if ($.cookie('dp_jse_report')) {
				this.hasSentReport = true;
			}
		}
	},

	logError: function(message, trace, script, line) {

		if (window.DP_LOADED_TIME) {
			var timeUsing = ((new Date()).getTime() / 1000) - window.DP_LOADED_TIME;
		} else {
			var timeUsing = 0;
		}

		if (!message || message == 'false' || message.indexOf('Error connecting to extension') !== -1 || message.indexOf('flashBridge') !== -1) {
			return;
		}

		// Some errors made by browser extensions that we catch
		if (message.indexOf('Automation server') !== -1) {
			return;
		}

		if (trace && trace == '?() in :0') {
			// not a usefu message
			return;
		}

		if (!line || line == 0 || line === '0') {
			return;
		}

		// Scripts of resource:// are extensions, so we dont want to log those
		if (script && script.indexOf('resource://') === 0) {
			return;
		}

		if (parseInt(line) == 1 && script.indexOf('/agent/') != -1) {
			return;
		}

		// Send max 5 per session
		if (this.logCount++ > 5) {
			return;
		}

		message += ' (timeUsing: ' + timeUsing + ')';

		var data = {
			message: message || '',
			trace:   trace   || '',
			script:  script  || '',
			line:    line    || '0'
		};

		if (this.saveUrl) {

			message = message+'';

			if (ASSETS_BASE_URL) {
				var r = new RegExp(ASSETS_BASE_URL.escapeRegExp(), 'g');
				message = message.replace(r, '');
			}

			if (data.script.indexOf('#app.') !== -1) {
				data.script = data.script.replace(/#.*$/, '');
			}

			$.ajax({
				url: this.saveUrl,
				data: data,
				error: function() { },// prevents DeskPRO_Window's global error handler from firing on error
				type: 'POST'
			});
		}

		// AJAX/network errors shouldnt popup the report notice
		// They're bubbled through the handlers for Cloud so we get reports,
		// but we dont want the feedback window coming up as well
		if (message.indexOf('AJAX Error') !== -1) {
			return;
		}

		if (window.SEND_FEEDBACK_WINDOW && !this.hasSentReport) {
			this.hasSentReport = true;

			if (window.jQuery && window.jQuery.cookie) {
				$.cookie('dp_jse_report', '1', { expires: 1 });
			}

			window.SEND_FEEDBACK_WINDOW.open(
				"We have detected a browser Javascript error that may prevent the interface from functioning properly. " +
				"To help us identify and fix the problem, we would appreciate it if you could describe what you were viewing " +
				"and the actions you were performing just before this notice appeared.",

				"Message: " + data.message + "\nScript: " + data.script + "\nLine:" + data.line + "\nTrace:" + data.trace + "\nUser Agent: " + navigator.userAgent,

				true
			);
		}
	},
};