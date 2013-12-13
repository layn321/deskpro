if (window.console) {
	var DpConsole = window.console;
} else {
	var DpConsole = {};
	DpConsole['error'] = function(){};
	DpConsole['log'] = function(){};
	DpConsole['warn'] = function(){};
	DpConsole['info'] = function(){};
	DpConsole['debug'] = function(){};
}

if (!window.Dp_WaitingLibLoad) {
	window.Dp_WaitingLibLoad = [];
}

var DpOverlayWidget = new (function() {

	//##################################################################################################################
	//# Util
	//##################################################################################################################

	var util = {
		createEl: function(html) {
			var div = document.createElement('div');
			div.innerHTML = html;

			return div.firstChild;
		},

		hasClass: function(el, className) {
			if (el.className === "") {
				return false;
			}

			return (" " + el.className + " ").indexOf(" " + className + " ") > -1;
		},

		addClass: function(el, className) {
			if (!this.hasClass(el, className)) {
				el.className += " " + className;
			}
		},

		removeClass: function(el, className) {
			if (this.hasClass(el, className)) {
				el.className.replace(new RegExp("(^|\\s)" + className + "(\\s|$)"), " ").replace(/\s$/, "");
			}
		},

		hideEl: function(el) {
			el.style.display = 'none';
		},

		showEl: function(el) {
			el.style.display = 'block';
		},

		getWindowSize: function() {
			var winW = 0, winH = 0;

			if (typeof window.innerWidth != 'undefined') {
				winW = window.innerWidth,
				winH = window.innerHeight
			} else if (typeof document.documentElement != 'undefined' && typeof document.documentElement.clientWidth != 'undefined' && document.documentElement.clientWidth != 0) {
				winW = document.documentElement.clientWidth,
				winH = document.documentElement.clientHeight
			} else {
				winW = document.getElementsByTagName('body')[0].clientWidth,
				winH = document.getElementsByTagName('body')[0].clientHeight
			}

			return {w: winW, h: winH};
		},

		getElWidth: function (el) {
			return el.offsetWidth;
		},

		getElHeight: function(el) {
			return el.offsetHeight;
		},

		removeEl: function(el) {
			el.parentNode.removeChild(el);
		},

		bind: function(el, eventName, callback) {
			if (el.addEventListener) {
				el.addEventListener(eventName, callback);
			} else {
                el.attachEvent("on" + eventName, callback);
			}
		},

		extend: function(obj, obj2) {
			for (var property in obj2) {
				obj[property] = obj2[property];
			}

			return obj;
		}
	};

	//##################################################################################################################
	//# Overlay Widget
	//##################################################################################################################

	var body = document.body;
	var tmp, tmpi;

	var options = {
		protocol: null,
		staticUrl: null,
		deskproUrl: null,
		tabLocation: 'left',
		tabClass: ''
	};

	var me = this;
	var self = this;

	/**
	 * The overlay backdrop div
	 * @var {HTMLElement}
	 */
	var overlayBack = null;

	/**
	 * The overlay wrapper
	 * @var {HTMLElement}
	 */
	var overlayWrap = null;

	/**
	 * The inner overlay wrapper
	 * @var {HTMLElement}
	 */
	var overlayWrapInner = null;


	/**
	 * The overlay iframe
	 * @var {HTMLElement}
	 */
	var overlayIframe = null;

	/**
	 * The overlay the loads to the right of the left pane for alt content
	 */
	var contentWrap;

	/**
	 * Is the overlay currently open?
	 * @var {Boolean}
	 */
	var isOpen = false;

	/**
	 * The current window height
	 * @var {Integer}
	 */
	var winHeight = 0;

	/**
	 * The current window width
	 * @var {Integer}
	 */
	var winWidth  = 0;

	/**
	 * Contains whether the page being viewed is RTL.
	 */
	var isRtl = false;

	var lastWinHeight = 0;
	var lastWinWidth = 0;
	var childRequestedHeight = 500;

	var isIE  = (navigator && navigator.appName && navigator.appName == 'Microsoft Internet Explorer');
	var ieVer = 0;
	if (isIE) {
		var re = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
		if (re.exec(navigator.userAgent) != null) ieVer = parseFloat(RegExp.$1);
	}

	var comms = {
		intervalId: null,
		lastHash: null,
		hasPostMessage: window.postMessage && (!isIE || ieVer > 9),
		cacheBust: 0,
		recieveCallback: null,
		send: function(message, targetUrl, target) {
			if (this.hasPostMessage) {
				target.postMessage(message, targetUrl.replace( /([^:]+:\/\/[^\/]+).*/, '$1'))
			} else {
				var targetLoc = target.location + '';
				target.location = targetLoc.replace(/#.*$/, '') + '#' + (+new Date) + (this.cacheBust++) + '&' + message;
			}
		},
		setupReciever: function(callback, sourceUrl) {
			this.recieveCallback = callback;

			if (this.hasPostMessage) {
				if (window.addEventListener) {
					window[this.recieveCallback ? 'addEventListener' : 'removeEventListener']('message', this.recieveCallback, false);
				} else {
					window[this.recieveCallback ? 'attachEvent' : 'detachEvent' ]('onmessage', this.recieveCallback);
				}
			} else {
				if (this.intervalId) {
					window.clearInterval(this.intervalId);
				}

				if (this.recieveCallback) {
					var me = this;
					this.intervalId = window.setInterval(function() {
						var hash = document.location.hash;
						var re = /^#?\d+&/;
						if (hash !== me.lastHash && re.test(hash)) {
							me.lastHash = hash;
							me.recieveCallback({ data: hash.replace( re, '') });
						}
					}, 60);
				}
			}
		}
	};

	/**
	 * The child iframe talks to us through this method.
	 *
	 * Note that the parent never talks to the child. All communication is done through the child
	 * asking the parent for information.
	 *
	 * @param {Array} messageData
	 */
	this.childListen = function(messageData) {

		if (messageData && messageData.data) {
			messageData = messageData.data;
		}

		var data = messageData.split(':');
		var messageId = data.shift();

		DpConsole.log('[ChatWidget] comms received: %s %o', messageId, data);

		var self = this;
		DpConsole.log('[Recieving] %s', messageId);

		switch (messageId) {

			case 'closeMe':
				self.close();
				break;

			// When the child wants to resize to a certain height (ie to accomodate more stuff) they send this message
			// Afterwards we pass back the height we were able to set which may be smaller than it wanted
			case 'requestHeight':
				var height = data[0];
				var winMaxHeight = winHeight - 40;

				if (height < 600) {
					height = 600;
				}
				if (height > winMaxHeight) {
					height = winMaxHeight;
				}

				childRequestedHeight = data[0];
				setHeight(height);

				break;

			case 'requestChat':

				var data = {
					name: data[0].replace(/__DP_COL__/g, ':'),
					email: data[1].replace(/__DP_COL__/g, ':'),
					department_id: data[2].replace(/__DP_COL__/g, ':')
				};

				if (window.DpChatWidget) {
					DpChatWidget.open([
						['name', data.name],
						['email', data.email],
						['department_id', data.department_id],
						['auto_start', 1]
					]);
					self.close();
				} else {
					// Load it dynamically

					window.DpChatWidget_Options = window.DpChatWidget_Options || {};
					window.DpChatWidget_Options.protocol = ('https:' == document.location.protocol ? 'https' : 'http');
					window.DpChatWidget_Options.deskproUrl = options.deskproUrl;
					window.DpChatWidget_Options.staticUrl = options.deskproUrl;
					window.DpChatWidget_Options.startPhrase = 'Chat with us';
					window.DpChatWidget_Options.resumePhrase = 'Open your chat';
					window.DpChatWidget_Options.tabLocation = 'right';

					window.DpChatWidget_Options.currentPageUrl = window.location;
					window.DpChatWidget_Options.referrerPageUrl = document.referrer;
					window.DpChatWidget_Options.onInitCallback = function() {
						DpChatWidget.open([
							['name', data.name],
							['email', data.email],
							['department_id', data.department_id],
							['auto_start', 1]
						]);
						self.close();
					};

					var scr   = document.createElement('script');
					scr.type  = 'text/javascript';
					scr.async = true;
					scr.src   = options.staticUrl + 'javascripts/DeskPRO/User/ChatWidget/ChatWidget.js';
					(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(scr);
				}

				return;

			case 'showContentPage':

				if (contentWrap) {
					util.removeEl(contentWrap);
					contentWrap = null;
				}

				var w = util.getElWidth(overlayWrapInner);
				var h = util.getElHeight(overlayWrapInner);

				var myWidth  = w - 350 + 20; // 350 is width of the left pane inside
				var myHeight = h + 50; // 20 for some space around

				var top = (winHeight - myHeight) / 2;

				var css = [];
				css.push('position: fixed');
				css.push('border: 1px solid #7F8394');
				css.push('background: #ffffff url(' + options.staticUrl + 'images/spinners/loading-big-circle.gif) no-repeat 50% 50%');
				css.push('-moz-background-clip: padding');
				css.push('-webkit-background-clip: padding-box');
				css.push('background-clip: padding-box');
				css.push('width: ' + myWidth + 'px');
				css.push('height: ' + myHeight + 'px');
				css.push('margin: 0');
				css.push('padding: 0');
				css.push('top: ' + top + 'px');
				css.push((isRtl ? 'right' : 'left') + ': ' + (((winWidth - myWidth) / 2) + 175 + 20) + 'px');
				css.push('-webkit-border-radius: 8px');
				css.push('-moz-border-radius: 8px');
				css.push('border-radius: 8px');
				css.push('z-index: 16001');
				css.push('box-shadow: 0 0px 3px rgba(0, 0, 0, 0.5)');
				css = css.join(';');
				contentWrap = util.createEl('<div style="' + css  +'"></div>');
				body.appendChild(contentWrap);

				var css = [];
				css.push('position: absolute');
				css.push('top: 0');
				css.push('right: 0');
				css.push('bottom: 0');
				css.push('left: 0');
				css.push('-webkit-border-radius: 9px');
				css.push('-moz-border-radius: 9px');
				css.push('border-radius: 9px');
				css.push('overflow: hidden');
				css.push('-moz-background-clip: padding');
				css.push('-webkit-background-clip: padding-box');
				css.push('background-clip: padding-box');
				css = css.join(';');
				var inner = util.createEl('<div style="' + css  +'"></div>');
				contentWrap.appendChild(inner);

				css = [];
				css.push('border: none');
				css.push('width: 28px');
				css.push('height: 28px');
				css.push('margin: 0');
				css.push('padding: 0');
				css.push('cursor: pointer');
				css.push('box-shadow: none');
				css.push('overflow: hidden');
				css.push('position: absolute');
				css.push('background: url(' + options.staticUrl + 'images/user/widget/btn-close.png)');
				css.push('top: -10px');
				css.push((isRtl ? 'left' : 'right') + ': -10px');
				css = css.join(';');

				tmp = util.createEl('<span style="'+css+'"></span>');
				contentWrap.appendChild(tmp);

				util.bind(tmp, 'click', function(ev) {
					if (ev && ev.preventDefault) ev.preventDefault();
					else window.event.returnValue = false;

					util.removeEl(contentWrap);
					contentWrap = null;
				});

				css = [];
				css.push('width: ' + myWidth + 'px');
				css.push('height: ' + myHeight + 'px');
				css.push('margin: 0');
				css.push('padding: 0');
				css.push('box-shadow: none');
				css.push('overflow: hidden');
				css = css.join(';');

				var url = data[0];
				url = url.replace(/__DP_COL__/g, ':');
				if (url.indexOf('?') == -1) {
					url += '?';
				} else {
					url += '&';
				}
				url += 'parent_url=' + encodeURIComponent(window.location.href);

				tmp = util.createEl('<iframe src="' + url + '" style="' + css  +'" align="middle" frameborder="0" marginheight="0" marginwidth="0" scrolling="no"></iframe>');
				inner.appendChild(tmp);

				// Controls to open in new window
				css = [];
				css.push('position: absolute');
				css.push('left: 0');
				css.push('right: 0');
				css.push('bottom: 0');
				css.push('height: 22px');
				css.push('height: 48px');
				css.push('line-height: 100%');
				css.push('background-color: #E8E8E8');
				css.push('z-index: 1');
				css = css.join(';');
				tmpi = util.createEl('<div style="'+css+'"></div>');
				inner.appendChild(tmpi);

				css = [];
				css.push('float: right');
				css.push('background-color: #FFFFFF');
				css.push('border-radius: 5px');
				css.push('border: 1px solid #CCCCCC');
				css.push('display: block');
				css.push('padding: 5px 12px');
				css.push('text-decoration: none');
				css.push('margin: 10px 10px 0 0');
				css = css.join(';');

				// Not a partial for full link
				url = url.replace(/_partial=overlayWidget/, '');

				tmp = util.createEl('<a href="'+url+'" style="'+css+'" target="_blank">Open in new window</a>');
				tmpi.appendChild(tmp);

				util.bind(tmp, 'click', function(ev) {

					var embeddedIframe = document.getElementById('dp_helpdesk_iframe');
					if (embeddedIframe) {
						if (ev && ev.preventDefault) ev.preventDefault();
						else window.event.returnValue = false;

						self.close();

						embeddedIframe.src = url;
						return false;
					}
				});

				break;
		}
	};


	/**
	 * Opens the overlaying iframe
	 */
	this.open = function() {
		if (isOpen) {
			return;
		}

		var self = this;

		// Always re-create the iframe so the stage resets
		if (overlayIframe) {
			util.removeEl(overlayIframe);
			overlayIframe = null;
		}

		if (!overlayWrap) {
			isNew = true;

			var css = [];
			css.push('position: fixed');
			css.push('display: none');
			css.push('box-shadow: none');
			css.push('overflow: hidden');
			css.push('background: #000000');
			css.push('top: 0');
			css.push('right: 0');
			css.push('bottom: 0');
			css.push('left: 0');
			css.push('filter: alpha(opacity=0.7)');
			css.push('-khtml-opacity: 0.7');
			css.push('-moz-opacity: 0.7');
			css.push('opacity: 0.7');
			css.push('z-index: 15000');
			css = css.join(';');
			overlayBack = util.createEl('<div id="dp_overlay_back" style="' + css  +'"></div>');
			body.appendChild(overlayBack);

			css = [];
			css.push('position: fixed');
			css.push('display: none');
			css.push('overflow: hidden');
			css.push('top: 0');
			css.push('right: 0');
			css.push('bottom: 0');
			css.push('left: 0');
			css.push('text-align: center');
			css.push('z-index: 15001');
			css = css.join(';');
			overlayWrap = util.createEl('<div id="dp_overlay_wrap" style="' + css  +'"></div>');
			body.appendChild(overlayWrap);

			css = [];
			css.push('position: relative');
			css.push('text-align: left');
			css.push('border: 2px solid #B8B8B8');
			css.push('background: #F9FAFC url(' + options.staticUrl + 'images/spinners/loading-big-circle.gif) no-repeat 50% 50%');
			css.push('width: 890px');
			css.push('height: 500px');
			css.push('margin: auto');
			css.push('padding: 0');
			css.push('-webkit-border-radius: 4px');
			css.push('-moz-border-radius: 4px');
			css.push('border-radius: 4px');
			css.push('box-shadow:0 0 9px #000000');
			css.push('-webkit-box-shadow: 0 0 9px #000000');
			css.push('-moz-box-shadow: 0 0 9px #000000');
			css = css.join(';');
			overlayWrapInner = util.createEl('<div style="' + css  +'"></div>');
			overlayWrap.appendChild(overlayWrapInner);

			css = [];
			css.push('border: none');
			css.push('width: 28px');
			css.push('height: 28px');
			css.push('margin: 0');
			css.push('padding: 0');
			css.push('cursor: pointer');
			css.push('box-shadow: none');
			css.push('overflow: hidden');
			css.push('position: absolute');
			css.push('background: url(' + options.staticUrl + 'images/user/widget/btn-close.png)');
			css.push('top: -10px');
			css.push('right: -10px');
			css = css.join(';');

			var close = util.createEl('<span style="'+css+'"></span>');
			overlayWrapInner.appendChild(close);

			util.bind(close, 'click', function(ev) {
				if (ev && ev.preventDefault) ev.preventDefault();
				else window.event.returnValue = false;

				self.close();
			});
		}

		isOpen = true;

		css = [];
		css.push('background: transparent');
		css.push('width: 894px');
		css.push('height: 500px');
		css.push('margin: 0');
		css.push('padding: 0');
		css.push('box-shadow: none');
		css.push('overflow: hidden');
		css = css.join(';');

		var src = options.deskproUrl + 'widget/overlay.html?h=' + winHeight + '&website_url=' + encodeURIComponent(window.location + '');

		if (DpOverlayWidget_Options && DpOverlayWidget_Options.languageId) {
			src += '&language_id=' + DpOverlayWidget_Options.languageId;
		}

		src += '&parent_url=' + encodeURIComponent(window.location.href);
		overlayIframe = util.createEl('<iframe id="dp_overlay_iframe" name="dp_overlay_iframe" allowtransparency="true" src="' + src + '" style="' + css  +'" align="middle" frameborder="0" marginheight="0" marginwidth="0" scrolling="no"></iframe>');
		overlayWrapInner.appendChild(overlayIframe);

		comms.setupReciever(function(m) {
			me.childListen(m);
		}, src);
		setHeight(500);
		updatePosition();

		util.showEl(overlayBack);
		util.showEl(overlayWrap);
	};


	/**
	 * Closes the overlay
	 */
	this.close = function() {
		if (!isOpen) {
			return;
		}

		if (contentWrap) {
			util.removeEl(contentWrap);
			contentWrap = null;
		}

		isOpen = false;

		if (!comms.hasPostMessage) {
			util.hideEl(overlayBack);
			util.hideEl(overlayWrap);

			var targetLoc = window.location + '';
			window.location = targetLoc.replace(/#.*$/, '#');
		} else {
			util.hideEl(overlayBack);
			util.hideEl(overlayWrap);
		}
	};


	//##################################################################################################################
	//# Private helper methods
	//##################################################################################################################

	function setHeight(height) {
		DpConsole.log('DpOverlayWidget:setHeight ' + height);

		overlayWrapInner.style.height = height + "px";
		overlayWrapInner.style.top = ((util.getWindowSize().h - height) / 2) + "px";
		overlayIframe.style.height = height + "px";
	};

	function updatePosition() {
		overlayWrapInner.style.top = ((winHeight - util.getElHeight(overlayWrapInner)) / 2) + "px";

		var winMaxHeight = winHeight - 40;
		var height = util.getElHeight(overlayWrapInner);
		if (height > winMaxHeight) {
			setHeight(winMaxHeight);
		} else if (height < childRequestedHeight) {
			if (childRequestedHeight > winMaxHeight) {
				setHeight(winMaxHeight);
			} else {
				setHeight(childRequestedHeight);
			}
		}
	};


	//##################################################################################################################
	//# Initialize Helpers
	//##################################################################################################################

	function initWidget() {

		DpConsole.log('DpOverlayWidget.initScript');

		isRtl = (document.documentElement && document.documentElement.dir && document.documentElement.dir == 'rtl');

		if (window.DpOverlayWidget_Options) {
			if (isRtl && !window.DpOverlayWidget_Options.tabLocation) {
				window.DpOverlayWidget_Options.tabLocation = 'right';
			}
			util.extend(options, window.DpOverlayWidget_Options);
		}

		if (!options.staticUrl) {
			options.staticUrl = options.deskproUrl.replace(/index\.php\//, '') + 'web/';
		}

		var bgColor  = '#3F3F3F';
		var border   = '2px solid #727272';
		var textColor = '#FFFFFF';
		var textShadow = '0px 0px 2px #000000';
		var font = 'bold 13px Arial, sans-serif';

		if (DpOverlayWidget_Options && DpOverlayWidget_Options.btnStyle) {
			if (DpOverlayWidget_Options.btnStyle.bgColor) {
				bgColor = DpOverlayWidget_Options.btnStyle.bgColor;
			}
			if (DpOverlayWidget_Options.btnStyle.border) {
				border = DpOverlayWidget_Options.btnStyle.border;
			}
			if (DpOverlayWidget_Options.btnStyle.textColor) {
				textColor = DpOverlayWidget_Options.btnStyle.textColor;
			}
			if (DpOverlayWidget_Options.btnStyle.textShadow) {
				textShadow = DpOverlayWidget_Options.btnStyle.textShadow;
			}
			if (DpOverlayWidget_Options.btnStyle.font) {
				font = DpOverlayWidget_Options.btnStyle.font;
			}
		}

		var css = [];
		css.push('position: fixed');
		css.push('display: block');
		css.push('cursor: pointer');
		css.push('box-shadow: none');
		css.push('background: ' + bgColor);
		css.push('border: ' + border);
		css.push('overflow: hidden');
		css.push('cursor: pointer');
		css.push('text-shadow: ' + textShadow);
		css.push('color: ' + textColor);
		css.push('font: ' + font);
		css.push('letter-spacing: 1px');
		css.push('height: 34px');
		css.push('line-height: 25px');
		css.push('padding: 0 13px 0 13px');
		css.push('margin: 0');
		css.push('opacity: 0.85');
		css.push('z-index: 10000');

		var degrees;

		if (options.tabLocation == 'left') {
			css.push('left: 0');
			degrees = 90;
		} else {
			css.push('right: 0');
			degrees = 270;
		}

		if (options.bottomPosition) {
			css.push('bottom: ' + options.bottomPosition);
		} else {
			if (options.topPosition) {
				css.push('top: ' + options.topPosition);
			} else {
				css.push('top: 200px');
			}
		}

		css.push('-webkit-transform: rotate(' + degrees + 'deg)');
		css.push('-moz-transform: rotate(' + degrees + 'deg)');
		css.push('-ms-transform: rotate(' + degrees + 'deg)');
		css.push('-o-transform: rotate(' + degrees + 'deg)');

		if (isIE ) {
			if (parseInt((ieVer+'').slice(0,1)) >= "9") {
				css.push('filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=4)');
			} else {
				css.push('filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=1)');
			}
		} else {
			// IE's filter to rotate the box makes the edges around
			// the rounded corners black, so better to not use rounded corners
			css.push('border-radius: 9px');
			css.push('-webkit-border-radius: 9px');
		}

		css = css.join(';');

		var phrase = 'Feedback & Support';
		if (DpOverlayWidget_Options && DpOverlayWidget_Options.lang) {
			if (DpOverlayWidget_Options.lang['user.widget.btn']) {
				phrase = DpOverlayWidget_Options.lang['user.widget.btn'];
			}
		}
		if (DpOverlayWidget_Options && DpOverlayWidget_Options.phrase) {
			phrase = DpOverlayWidget_Options.phrase;
		}

		phrase = phrase.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');

		tmp = util.createEl('<div id="dp_overlay_btn" class="dp-overlay-widget-trigger" style="' + css + '" class="dp-hide-print ' + options.tabClass + '">' + phrase + '</div>');
		body.appendChild(tmp);

		tmp.style[options.tabLocation] = '-' + ((util.getElWidth(tmp) / 2) - 10) + 'px';

		util.bind(tmp, 'click', function(ev) {
			if (ev && ev.preventDefault) ev.preventDefault();
			else window.event.returnValue = false;

			self.open();
		});

		if (document.getElementsByClassName) {
			tmp = document.getElementsByClassName('dp-overlay-widget-trigger');
			for (tmpi = 0; tmpi < tmp.length; tmpi++) {
				util.bind(tmp[tmpi], 'click', function(ev) {
					if (ev && ev.preventDefault) ev.preventDefault();
					else window.event.returnValue = false;

					self.open();
				});
			}
		}

		// Preload images used in the overlay
		(new Image()).src = options.staticUrl + 'images/spinners/loading-big-circle.gif';
		(new Image()).src = options.staticUrl + 'images/user/widgetlogo.png';
		(new Image()).src = options.staticUrl + 'images/user/widgetlogo-on.png';
		(new Image()).src = options.staticUrl + 'images/user/widget/btn-close.png';

		winWidth  = lastWinWidth  = util.getWindowSize().w;
		winHeight = lastWinHeight = util.getWindowSize().h;

		var repositionTimeout = null;
		util.bind(window, 'resize', function() {
			lastWinWidth  = winWidth;
			lastWinHeight = winHeight;

			winWidth  = util.getWindowSize().w;
			winHeight = util.getWindowSize().h;

			if (!isOpen) {
				return;
			}

			if (lastWinWidth != winWidth || lastWinHeight != winHeight) {
				if (repositionTimeout) {
					window.clearTimeout(repositionTimeout);
				}

				repositionTimeout = window.setTimeout(function() {
					repositionTimeout = null;
					updatePosition();
				}, 80);
			}
		});
	};

	//##################################################################################################################
	//# Initialize
	//##################################################################################################################

	DpConsole.log('DpDpOverlayWidget.init');
	initWidget();

	return this;
})();
