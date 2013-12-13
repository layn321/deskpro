if (window.Dp_EnableDebug) {
	var DpConsole = window.console;
} else {
	var DpConsole = {};
	DpConsole['error'] = function(){};
	DpConsole['log'] = function(){};
	DpConsole['warn'] = function(){};
	DpConsole['info'] = function(){};
	DpConsole['debug'] = function(){};
}

if (top === self) {
	var HelpdeskWin = new (function() {
		this.isWidgetMode = function() { return false; }
		this.tellParent = function() { return false; }
	});
} else {
	var HelpdeskWin = new (function() {

		var self = this;

		var isIE  = (navigator && navigator.appName && navigator.appName == 'Microsoft Internet Explorer');
		var ieVer = 0;
		if (isIE) {
			var re = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
			if (re.exec(navigator.userAgent) != null) ieVer = parseFloat(RegExp.$1);
		}

		//##############################################################################################################
		//# Methods
		//##############################################################################################################

		/**
		 * Pass a message up to the parent controller
		 *
		 * @param {String} messageId
		 * @param {Object} [data]
		 */
		this.tellParent = function(messageId, data) {
			if (typeof data != 'undefined' && !data.join) {
				data = [data];
			}

			data = data || [];
			var messageStr = messageId + ':' + data.join(':');
			comms.send(messageStr, this.parentUrl, window.parent);

			console.log('[HelpdeskWin] comms.send: %s %o', messageId, data);

			return null;
		};

		this.isWidgetMode = function() {
			return true;
		};

		var getDocHeight = function() {
			if (!document.body || !document.documentElement) return 0;

			var h = Math.max(
				document.body.offsetHeight,
				document.body.clientHeight
			);

			h += 10;

			return h;
		};

		var createCookie = function(name,value,days) {
			if (days) {
				var date = new Date();
				date.setTime(date.getTime()+(days*24*60*60*1000));
				var expires = "; expires="+date.toGMTString();
			}
			else var expires = "";
			document.cookie = name+"="+value+expires+"; path=/";
		};

		var readCookie = function(name) {
			var nameEQ = name + "=";
			var ca = document.cookie.split(';');
			for(var i=0;i < ca.length;i++) {
				var c = ca[i];
				while (c.charAt(0)==' ') c = c.substring(1,c.length);
				if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
			}
			return null;
		};

		//##############################################################################################################
		//# Init coms
		//##############################################################################################################

		var comms = {
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
			},
			setupReciever: function(callback) {				// Unset existing
				if (callback && comms.recieveCallback) {
					comms.recieveCallback = null;
					comms.setupReciever(null);
				}

				comms.recieveCallback = callback;

				if (comms.hasPostMessage) {
					if (window.addEventListener) {
						window[comms.recieveCallback ? 'addEventListener' : 'removeEventListener']('message', comms.recieveCallback, false);
					} else {
						window[comms.recieveCallback ? 'attachEvent' : 'detachEvent' ]('onmessage', comms.recieveCallback);
					}
				} else {
					if (comms.intervalId) {
						window.clearInterval(comms.intervalId);
					}

					if (comms.recieveCallback) {
						comms.intervalId = window.setInterval(function() {
							var hash = document.location.hash;
							var re = /^#?\d+&/;
							if (hash !== comms.lastHash && re.test(hash)) {
								comms.lastHash = hash;
								comms.recieveCallback({ data: hash.replace( re, '') });
							}
						});
					}
				}
			}
		};

		var childListen = function(messageData) {

			if (messageData && messageData.data) {
				messageData = messageData.data;
			}

			var data = messageData.split(':');
			var messageId = data.shift();

			DpConsole.log('[HelpdeskWin] comms received: %s %o', messageId, data);

			switch (messageId) {
				// S-imple mode
				case 'dphS':
					var tmp = window.setInterval(function() {
						if (document.body) {
							window.clearTimeout(tmp);
							document.documentElement.className += ' dp-in-frame-simple';
							document.body.className += ' ' + ' dp-in-frame-simple';
						}
					}, 10);
					break;
			}
		};

		this.parentUrl = decodeURIComponent(document.location.hash.replace( /^#/, ''));
		if (this.parentUrl.indexOf('http') === -1) {
			var c = readCookie('dp_fr_url');
			if (c) {
				this.parentUrl = c;
			}
		}

		if (!this.parentUrl) {
			this.parentUrl = '';
		}

		if (this.parentUrl) {
			createCookie('dp_fr_url', this.parentUrl, 0);
		} else {
			createCookie('dp_fr_url', '', -1);
		}

		//##############################################################################################################
		//# Init
		//##############################################################################################################

		var to = window.setInterval(function() {
			var element;
			if (document.body) {
				window.clearTimeout(to);
				document.documentElement.className += ' dp-in-frame';
				document.body.className += ' ' + ' dp-in-frame';

				if (element = document.getElementById('dpchat_wrap')) {
					element.parentNode.removeChild(element);
				}
			}
		}, 10);

		var lastHeight, currentHeight;
		lastHeight = getDocHeight();

		var url = window.location.href + '';
		url = url.replace(/:/, '__DP__COL__');
		self.tellParent('dphR', [lastHeight, url]);

		window.setInterval(function() {
			currentHeight = getDocHeight();

			if (lastHeight != currentHeight) {
				self.tellParent('dphH', [currentHeight]);
			}
			lastHeight = currentHeight;
		}, 115);

		comms.setupReciever(function(m) {
			childListen(m);
		});
	});
}