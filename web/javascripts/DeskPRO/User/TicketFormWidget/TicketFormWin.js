Orb.createNamespace('DeskPRO.User.WebsiteWidget');

DeskPRO.User.WebsiteWidget.TicketFormWin = new Orb.Class({
	Implements: [Orb.Util.Options, Orb.Util.Events],

	initialize: function() {

		var isIE  = (navigator && navigator.appName && navigator.appName == 'Microsoft Internet Explorer');
		var ieVer = 0;
		if (isIE) {
			var re = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
        	if (re.exec(navigator.userAgent) != null) ieVer = parseFloat(RegExp.$1);
		}

		this.comms = {
			hasPostMessage: window.postMessage && (!isIE || ieVer > 8),
			cacheBust: 0,
			send: function(message, targetUrl, target) {
				if (!targetUrl) {
					return;
				}
				if (this.hasPostMessage) {
					target.postMessage(message, targetUrl.replace(/([^:]+:\/\/[^\/]+).*/, '$1'))
				} else {
					var targetLoc = target.location + '';
					target.location = targetLoc.replace(/#.*$/, '') + '#' + (+new Date) + (this.cacheBust++) + '&' + message;
				}
			}
		};

		this.parentUrl = decodeURIComponent(document.location.hash.replace( /^#/, ''));
		if (!this.parentUrl) {
			this.parentUrl = '';
		}
	},

	initPage: function() {
		var self = this;
		this.tellParent('dpticket_ready');

		var lastHeight, currentHeight;
		lastHeight = $('body').height();
		self.tellParent('dpticket_requestHeight', [lastHeight]);

		window.setInterval(function() {
			currentHeight = $('body').height();

			if (lastHeight != currentHeight) {
				self.tellParent('dpticket_requestHeight', [currentHeight]);
			}
			lastHeight = currentHeight;
		}, 115);
	},


	/**
	 * Pass a message up to the parent controller
	 *
	 * @param {String} messageId
	 * @param {Object} [data]
	 */
	tellParent: function(messageId, data) {
		if (typeof data != 'undefined' && !data.join) {
			data = [data];
		}

		data = data || [];
		var messageStr = messageId + ':' + data.join(':');
		this.comms.send(messageStr, this.parentUrl, window.parent);

		console.log('[TicketFormWin] comms.send: %s %o', messageId, data);

		return null;
	}
});