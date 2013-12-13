if (!DpChatWidget) {
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

var DpChatWidget = new (function() {

	//##################################################################################################################
	//# Util
	//##################################################################################################################

	var util = {
		createEl: function(html) {
			var div = document.createElement('div');
			div.innerHTML = html;

			return div.firstChild;
		},

		addStyleEl: function(css) {
			var styleEl = document.createElement('style');
			(document.getElementsByTagName("head")[0] || document.documentElement).appendChild(styleEl);
			styleEl.setAttribute('type', 'text/css');

			// IE
			if (styleEl.styleSheet) {
				styleEl.styleSheet.cssText = css;

			// Others
			} else {
				styleEl.appendChild(document.createTextNode(css));
			}

			return styleEl;
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
		},

		makeId: function(len) {
			var text  = "";
			var chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

			if (!len) {
				len = 8;
			}

			for( var i = 0; i < len; i++) {
				text += chars.charAt(Math.floor(Math.random() * chars.length));
			}

			return text;
		}
	};

	//##################################################################################################################
	//# Chat Widget
	//##################################################################################################################

	var body = document.body;
	var tmp, tmpi;

	var options = {
		protocol: null,
		deskproUrl: null,
		btnClass: 'dp-chat-btn',
		onInitCallback: null,
		languageId: 0,
		offlineUrl: null
	};

	var self = this;

	/**
	 * Is the chat currently open?
	 * @var {Boolean}
	 */
	var isOpen = false;

	/**
	 * The chat iFrame
	 * @var {HTMLElement}
	 */
	var chatIframeHolder;
	var chatIframeWinTab;
	var chatIframe;
	var closingChatIframe;

	/**
	 * The URL of the iframe
	 */
	var frameSrc;

	/**
	 * The button on the page that the user clicks to open the chat iframe
	 */
	var openBtn;

	/**
	 * Contains whether the page being viewed is RTL.
	 */
	var isRtl = false;

	/**
	 * Location of the tab (defaults to right for LTR and left for RTL).
	 */
	var tabLocation = 'right';

	/**
	 * The child iframe talks to us
	 *
	 * @param {String} messageId
	 * @param {Object} [data]
	 */
	this.childListen = function(messageId, data) {

	};


	/**
	 * Opens the overlaying iframe
	 */
	this.open = function(data) {

		if (this.toLoginPage) {
			window.location = options.deskproUrl + 'login';
			return;
		}

		if (isOpen) {
			return;
		}

		if (!chatIframe) {

			var bgColor = '#2A69A9';
			var textColor = '#fff';

			if (DpChatWidget_Options && DpChatWidget_Options.frameStyle) {
				if (DpChatWidget_Options.frameStyle.bgColor) {
					bgColor = DpChatWidget_Options.frameStyle.bgColor;
				}
				if (DpChatWidget_Options.frameStyle.textColor) {
					textColor = DpChatWidget_Options.frameStyle.textColor;
				}
			}

			var css = [];
			css.push('position: fixed');
			css.push('bottom: 0');
			css.push(tabLocation + ': 20px');
			css.push('width: 340px');
			css.push('height: 350px');
			css.push('background: #ffffff');
			css.push('margin: 0');
			css.push('padding: 0');
			css.push('box-shadow: none');
			css.push('border: 3px solid ' + bgColor);
			css.push('border-bottom: none');
			css.push('-moz-background-clip: padding');
			css.push('-webkit-background-clip: padding-box');
			css.push('background-clip: padding-box');
			css.push('-webkit-border-top-left-radius: 4px');
			css.push('-webkit-border-top-right-radius: 4px');
			css.push('-moz-border-radius-topleft: 4px');
			css.push('-moz-border-radius-topright: 4px');
			css.push('border-top-left-radius: 4px');
			css.push('border-top-right-radius: 4px');
			css.push('-webkit-box-shadow:  0px -1px 3px 1px rgba(0, 0, 0, 0.2)');
			css.push('box-shadow:  0px -1px 3px 1px rgba(0, 0, 0, 0.2)');
			css.push('z-index: 90000');
			css = css.join(';');

			chatIframeHolder = util.createEl('<div id="dp_chat_iframe_holder" class="dp-chat-iframe-holder" style="' + css  +'"></div>');
			body.appendChild(chatIframeHolder);


			// The little tabby thing at the top
			var css = [];
			css.push('background: ' + bgColor);
			css.push('color: ' + textColor);
			css.push('-webkit-border-top-left-radius: 4px');
			css.push('-webkit-border-top-right-radius: 4px');
			css.push('-moz-border-radius-topleft: 4px');
			css.push('-moz-border-radius-topright: 4px');
			css.push('border-top-left-radius: 4px');
			css.push('border-top-right-radius: 4px');
			css.push('z-index: 90001');
			css.push('font-size: 10px');
			css.push('line-height: 100%');
			css.push('padding: 3px 5px 3px 5px');
			css.push('position: absolute');
			css.push('top: -18px');
			css.push(tabLocation + ': 28px');
			css.push('font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif');
			css.push('cursor: pointer');
			css.push('-webkit-box-shadow:  0px -1px 3px 1px rgba(0, 0, 0, 0.2)');
			css.push('box-shadow:  0px -1px 3px 1px rgba(0, 0, 0, 0.2)');
			css = css.join(';');

			var phrase = 'Open this chat in a new window';
			if (DpChatWidget_Options && DpChatWidget_Options.openInWindowPhrase) {
				phrase = DpChatWidget_Options.openInWindowPhrase;
			}
			chatIframeWinTab = util.createEl('<div id="dp_chat_iframe_wintab" class="dp-chat-iframe-wintab" style="' + css + '">' + phrase + '</div>');
			chatIframeHolder.appendChild(chatIframeWinTab);
			util.bind(chatIframeWinTab, 'click', openInWindow);

			// Minmize button
			var css = [];
			css.push('background: ' + bgColor);
			css.push('color: ' + textColor);
			css.push('-webkit-border-top-left-radius: 4px');
			css.push('-webkit-border-top-right-radius: 4px');
			css.push('-moz-border-radius-topleft: 4px');
			css.push('-moz-border-radius-topright: 4px');
			css.push('border-top-left-radius: 4px');
			css.push('border-top-right-radius: 4px');
			css.push('z-index: 90001');
			css.push('font-size: 10px');
			css.push('line-height: 100%');
			css.push('padding: 3px 5px 3px 5px');
			css.push('position: absolute');
			css.push('text-align: left');
			css.push('top: -18px');
			css.push((isRtl ? 'left' : 'right') + ': 2px');
			css.push('font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif');
			css.push('cursor: pointer');
			css.push('-webkit-box-shadow:  0px -1px 3px 1px rgba(0, 0, 0, 0.2)');
			css.push('box-shadow:  0px -1px 3px 1px rgba(0, 0, 0, 0.2)');
			css = css.join(';');

			var phrase = 'Minimize';
			tmp = util.createEl('<div id="dp_chat_iframe_closebtn" class="dp-chat-iframe-closetab" title="'+phrase+'" style="' + css + '">&#9660;</div>');
			chatIframeHolder.appendChild(tmp);
			util.bind(tmp, 'click', function() { self.close() });

			isNew = true;

			css = [];
			css.push('position: absolute');
			css.push('bottom: 0');
			css.push('top: 0');
			css.push('width: 340px');
			css.push('height: 350px');
			css.push('margin: 0');
			css.push('padding: 0');
			css.push('box-shadow: none');
			css.push('overflow: hidden');
			css = css.join(';');

			var qs = '?';

			var sid = getCookie('dpchat_sid');
			if (sid) {
				qs += 'dpsid=' + sid;
			}

			if (data) {
				for (var i = 0; i < data.length; i++) {
					qs += '&' + encodeURIComponent(data[i][0]) + '=' + encodeURIComponent(data[i][1]);
				}
			}

			qs += '&parent_url=' + encodeURIComponent(window.location.href);
			if (options.languageId) {
				qs += '&language_id=' + options.languageId;
			}

			frameSrc = options.deskproUrl + 'widget/chat.html' + qs;
			chatIframe = document.createElement('iframe');
			chatIframe.setAttribute('src', frameSrc);
			chatIframe.setAttribute('id', 'dp_chat_iframe');
			chatIframe.frameBorder = 'no';
			chatIframe.scrolling = 'no';
			chatIframe.marginWidth = '0';
			chatIframe.marginHeight = '0';
			chatIframe.align = 'middle';
			chatIframe.style.position = 'absolute';
			chatIframe.style.bottom = '0';
			chatIframe.style.top = '0';
			chatIframe.style.left = '0';
			chatIframe.style.width = '340px';
			chatIframe.style.height = '350px';
			chatIframe.style.margin = '0';
			chatIframe.style.padding = '0';
			chatIframe.style.boxShadow = 'none';
			chatIframe.style.overflow = 'hidden';
			chatIframe.style.border = 'none';

			chatIframeHolder.appendChild(chatIframe);

			comms.setupReciever(childListen, frameSrc);

			util.bind(chatIframe, 'click', function(ev) {
				if (ev && ev.stopPropagation) ev.stopPropagation();
				else window.event.cancelBubble = true;
			});
		}

		isOpen = true;
		util.showEl(chatIframeHolder);
	};


	/**
	 * Closes the overlay
	 */
	this.close = function() {
		if (!isOpen) {
			return;
		}

		isOpen = false;
		util.hideEl(chatIframeHolder);
	};


	//##################################################################################################################
	//# Initialize Helpers
	//##################################################################################################################

	function openInWindow() {
		var src = chatIframe.src;
		src = src.replace(/\?/, '?is_window_mode=1&');

		window.open(src, 'dpchatwin','width=500,height=400,location=0,menubar=0,scrollbars=0,status=0,toolbar=0,resizable=1');

		util.removeEl(chatIframeHolder);
		util.hideEl(openBtn);
	};

	function setCookie(name,value,days) {
		if (days) {
			var date = new Date();
			date.setTime(date.getTime()+(days*24*60*60*1000));
			var expires = "; expires="+date.toGMTString();
		} else {
			var expires = "";
		}
		document.cookie = name+"="+value+expires+"; path=/";
	};

	function getCookie(c_name) {
		var i,x,y,ARRcookies=document.cookie.split(";");

		for (i=0;i<ARRcookies.length;i++) {
			x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
			y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
			x=x.replace(/^\s+|\s+$/g,"");
			if (x==c_name) {
				return unescape(y);
			}
		}
	};

	function initSession() {

		if (document.body && document.body.className && document.body.className.indexOf('dp-in-frame') !== -1) {
			return;
		}

		if (DpChatWidget_Options.onInitCallback) {
			options.onInitCallback = DpChatWidget_Options.onInitCallback;
		}

		if (DpChatWidget_Options.languageId) {
			options.languageId = parseInt(DpChatWidget_Options.languageId);
		}

		// Now load our session script
		// DeskPRO script that sets/gets session and initial messages
		isRtl = (document.documentElement && document.documentElement.dir && document.documentElement.dir == 'rtl');
		if (isRtl) {
			tabLocation = 'left';
		}

		if (DpChatWidget_Options && DpChatWidget_Options.tabLocation) {
			tabLocation = DpChatWidget_Options.tabLocation;
		}

		var url = DpChatWidget_Options.deskproUrl.replace(/index\.php\//, '') + 'dp.php/vis.js?chat&url=';
		if (DpChatWidget_Options && DpChatWidget_Options.currentPageUrl) {
			url += encodeURIComponent(DpChatWidget_Options.currentPageUrl);
		} else {
			url += encodeURIComponent((document.location.href+'') || '');
		}

		if (DpChatWidget_Options && DpChatWidget_Options.visitorCode) {
			url += '&vc=' + encodeURIComponent(DpChatWidget_Options.visitorCode);
		} else if (window.DESKPRO_VISITOR_ID) {
			url += '&vc=' + encodeURIComponent(window.DESKPRO_VISITOR_ID);
		} else {
			var vc = getCookie('dpvc');
			if (vc) {
				url += '&vc=' + encodeURIComponent(vc);
			}
		}

		if (DpChatWidget_Options && DpChatWidget_Options.visitorUserToken) {
			url += '&vut=' + encodeURIComponent(DpChatWidget_Options.visitorUserToken);
		} else if (window.DESKPRO_VISITOR_USER_TOKEN) {
			url += '&vut=' + encodeURIComponent(window.DESKPRO_VISITOR_USER_TOKEN);
		} else {
			var vut = getCookie('dpvut');
			if (!vut) {
				vut = util.makeId(8);
				setCookie('dpvut', vut, 1);
			}

			url += '&vut=' + encodeURIComponent(vut);
		}

		if (DpChatWidget_Options && DpChatWidget_Options.visitorUpdateTrackId) {
			url += '&v_tid=' + encodeURIComponent(DpChatWidget_Options.visitorUpdateTrackId);
		} else if (window.DESKPRO_VISITOR_TRACK_ID) {
			url += '&v_tid=' + encodeURIComponent(window.DESKPRO_VISITOR_TRACK_ID);
		}

		url += '&title=';
		if (DpChatWidget_Options && DpChatWidget_Options.currentPageTitle) {
			url += encodeURIComponent(DpChatWidget_Options.currentPageTitle);
		} else {
			url += encodeURIComponent(document.title || '');
		}

		url += '&rurl=';
		if (DpChatWidget_Options && DpChatWidget_Options.referrerPageUrl) {
			url += encodeURIComponent(DpChatWidget_Options.referrerPageUrl);
		} else {
			url += encodeURIComponent((document.referrer+'') || '');
		}

		if (typeof DESKPRO_SESSION_ID != 'undefined') {
			var sid = DESKPRO_SESSION_ID;
		} else {
			var sid = getCookie('dpchat_sid');
		}
		if (sid) {
			url += '&dpsid=' + sid + '&';
		}

		url += '&'+(new Date().getTime());

		if (options.displayType == 'DpWindow') {
			url += '&is_window=1';
		}

		if (DpChatWidget_Options && DpChatWidget_Options.noTrack) {
			url += '&notrack=1';
		}

		url += '&_ts=' + ((new Date()).getTime());

		DpConsole.log('DpChatWidget.initSession: adding script: ' + url);

		var script_tag = document.createElement('script');
		script_tag.setAttribute("type", "text/javascript");
		script_tag.setAttribute("src", url);
		script_tag.setAttribute("async", 'true');
		(document.getElementsByTagName("head")[0] || document.documentElement).appendChild(script_tag);
	};

	this.setNotAvailable = function() {
		util.addClass(body, 'dp-chat-disabled');

		if (DpChatWidget_Options && DpChatWidget_Options.offlineUrl) {
			options.offlineUrl = DpChatWidget_Options.offlineUrl;
		}

		if (options.offlineUrl) {
			drawButton();

			util.hideEl(document.getElementById('dpchat_btn_label_start_chat2'));
			util.showEl(document.getElementById('dpchat_btn_label_offline2'));

			util.bind(openBtn, 'click', function(ev) {
				window.location = options.offlineUrl;
			});
		}
	},

	this.initVisitorCode = function(visitor_code, user_token) {
		if (visitor_code) {
			window.DESKPRO_VISITOR_ID = visitor_code;
			setCookie('dpvc', visitor_code, 365);

			if (user_token) {
				window.DESKPRO_USER_TOKEN = user_token;
				setCookie('dpvut', user_token, 1);
			}
		}
	};

	this.initWidget = function(sessionId) {

		if (document.body && document.body.className && document.body.className.indexOf('dp-in-frame') !== -1) {
			return;
		}

		if (sessionId) {
			setCookie('dpchat_sid', sessionId, 7);
			DESKPRO_SESSION_ID = sessionId;
		} else {
			// No session also means no chat, so make sure to unset the chat cookie
			setCookie('dpchatid', 0, -7);
		}

		DpConsole.log('DpChatWidget.initWidget');

		drawButton();

		util.bind(openBtn, 'click', function(ev) {
			if (ev && ev.preventDefault) ev.preventDefault();
			else window.event.returnValue = false;

			if (ev && ev.stopPropagation) ev.stopPropagation();
			else window.event.cancelBubble = true;

			self.open();
		});

		if (this.isWindowChat) {

		} else {
			if (this.doResume) {
				self.open();
			} else {
				util.showEl(openBtn);
			}
		}

		util.addClass(body, 'dp-chat-enabled');
		if (document.getElementsByClassName) {
			tmp = document.getElementsByClassName('dp-chat-trigger');
			for (tmpi = 0; tmpi < tmp.length; tmpi++) {
				util.bind(tmp[tmpi], 'click', function(ev) {
					if (ev && ev.preventDefault) ev.preventDefault();
					else window.event.returnValue = false;

					DpChatWidget.open();
				});
			}
		}

		if (options.onInitCallback) {
			options.onInitCallback(this);
		}
	};

	var drawButton = function() {
		if (window.DpChatWidget_Options) {
			util.extend(options, window.DpChatWidget_Options);
		}

		var bgColor  = '#3F3F3F';
		var border   = '2px solid #727272';
		var textColor = '#FFFFFF';
		var textShadow = '0px 1px 2px #000000';
		var font = 'bold 12px Arial, sans-serif';

		if (DpChatWidget_Options && DpChatWidget_Options.btnStyle) {
			if (DpChatWidget_Options.btnStyle.bgColor) {
				bgColor = DpChatWidget_Options.btnStyle.bgColor;
			}
			if (DpChatWidget_Options.btnStyle.border) {
				border = DpChatWidget_Options.btnStyle.border;
			}
			if (DpChatWidget_Options.btnStyle.textColor) {
				textColor = DpChatWidget_Options.btnStyle.textColor;
			}
			if (DpChatWidget_Options.btnStyle.textShadow) {
				textShadow = DpChatWidget_Options.btnStyle.textShadow;
			}
			if (DpChatWidget_Options.btnStyle.font) {
				font = DpChatWidget_Options.btnStyle.font;
			}
		}

		var css = "\
			#dpchat_wrap { \
			  position: fixed; \
			  bottom: 0; \
			  " + tabLocation + ": 20px; \
			  cursor: pointer; \
			  z-index: 10000; \
			  opacity: 0.85; \
			  width: 225px; \
			} \
			 \
			#dpchat_wrap:hover { \
			  opacity: 1; \
			} \
			 \
			#dpchat_border_table, #dpchat_border_table tbody, #dpchat_border_table tr, #dpchat_border_table td { \
			  border-collapse: collapse; \
			  padding: 0; \
			  margin: 0; \
			  vertical-align: bottom;\
			} \
			#dpchat_border_table td#dpchat_border_11 div { \
			  background: " + bgColor + "; \
			  border: " + border + "; \
			  border-bottom: none; \
			  border-radius: 6px 6px 0 0; \
			  height: 6px; \
			  overflow: hidden; \
			} \
			#dpchat_border_table td#dpchat_border_12 div { \
			  height: 6px; \
			  overflow: hidden; \
			} \
			#dpchat_border_table td#dpchat_border_21 div { \
			  background: " + bgColor + "; \
			  border: " + border + "; \
			  border-top: none; \
			  border-bottom: none; \
			  height: 11px; \
			  overflow: hidden; \
			  color: transparent; \
			  white-space: nowrap; \
			} \
			 \
			#dpchat_border_table td#dpchat_border_21 div em { \
			  font: " + font + "; \
			  color: transparent; \
			  padding: 0 10px; \
			} \
			 \
			#dpchat_border_table td#dpchat_border_22 div { \
			  height: 11px; \
			  overflow: hidden; \
			} \
			 \
			#dpchat_border_table td#dpchat_border_31 div { \
			  background: " + bgColor + "; \
			  border-left: " + border + "; \
			  height: 9px; \
			  overflow: hidden; \
			} \
			 \
			#dpchat_border_table td#dpchat_border_32 div { \
			  background: " + bgColor + "; \
			  border: " + border + "; \
			  border-bottom: none; \
			  border-left: none; \
			  height: 7px; \
			  overflow: hidden; \
			  border-radius: 0 6px 0 0; \
			} \
			 \
			#dpchat_btn_text { \
			  position: absolute; \
			  left: 0; \
			  bottom: 5px; \
			  color: " + textColor + "; \
			  text-align: center; \
			  padding: 0; \
			} \
			#dpchat_btn_text em { \
			  display: block; \
			  text-align: center; \
			  font-style: normal; \
			  font: " + font + "; \
			  text-shadow: " + textShadow + "; \
			} \
		";

		util.addStyleEl(css);

		var isIE  = (navigator && navigator.appName && navigator.appName == 'Microsoft Internet Explorer');
		var ieVer = 0;
		if (isIE) {
			var re = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
			if (re.exec(navigator.userAgent) != null) ieVer = parseFloat(RegExp.$1);
		}

		var tpl = '<div id="dpchat_wrap"> \
			  <table id="dpchat_border_table" cellspacing="0" border="0" width="100%"> \
				<tbody> \
				  <tr> \
					<td id="dpchat_border_11"><div>&#160;</div></td> \
					<td id="dpchat_border_12"><div>&#160;</div></td> \
				  </tr> \
				  <tr> \
					<td id="dpchat_border_21" style="width:100px;"><div> \
					  <em id="dpchat_btn_label_start_chat">PHRASE1</em> \
					  <em id="dpchat_btn_label_open_chat" style="display:none;">PHRASE2</em> \
					  <em id="dpchat_btn_label_offline" style="display:none;">PHRASE3</em> \
					</div></td> \
					<td id="dpchat_border_22"><div>&#160;</div></td> \
				  </tr> \
				  <tr> \
					<td id="dpchat_border_31"><div>&#160;</div></td> \
					<td id="dpchat_border_32"><div>&#160;</div></td> \
				  </tr> \
				</tbody> \
			  </table> \
			  <div id="dpchat_btn_text"> \
				<em id="dpchat_btn_label_start_chat2">PHRASE1</em> \
				<em id="dpchat_btn_label_open_chat2" style="display: none;">PHRASE2</em> \
				<em id="dpchat_btn_label_offline2" style="display:none;">PHRASE3</em> \
			  </div> \
			</div>';

		var phrase1 = 'Click here to chat with us';
		var phrase2 = 'Open your chat';
		var phrase3 = 'Click here to contact us';

		if (DpChatWidget_Options && DpChatWidget_Options.startPhrase) {
			phrase1 = DpChatWidget_Options.startPhrase;
		}
		if (DpChatWidget_Options && DpChatWidget_Options.resumePhrase) {
			phrase2 = DpChatWidget_Options.resumePhrase;
		}
		if (DpChatWidget_Options && DpChatWidget_Options.offlinePhrase) {
			phrase3 = DpChatWidget_Options.offlinePhrase;
		}

		tpl = tpl.replace(/PHRASE1/g, phrase1);
		tpl = tpl.replace(/PHRASE2/g, phrase2);
		tpl = tpl.replace(/PHRASE3/g, phrase3);

		openBtn = util.createEl(tpl);
		if (isRtl) {
			util.addClass(openBtn, 'rtl');
		}
		if (self.isWindowChat) {
			util.hideEl(openBtn);
		}
		body.appendChild(openBtn);

		tmp = util.getElWidth(document.getElementById('dpchat_border_11'));
		if (tmp < 170) {
			document.getElementById('dpchat_border_11').style.width = 170 + 'px';
			tmp = 170;
		}
		document.getElementById('dpchat_btn_label_start_chat2').style.width = tmp + 'px';
		document.getElementById('dpchat_btn_label_open_chat2').style.width = tmp + 'px';
		document.getElementById('dpchat_btn_label_offline2').style.width = tmp + 'px';

		// This correction is in a timeout because in some cases Chrome
		// seems to stretch the table if this is run immediately,
		// Setting this small timeout seems to fix that.
		window.setTimeout(function() {
			// Correct width if the phrase is long
			tmp = util.getElWidth(document.getElementById('dpchat_border_table'));
			if (tmp > 300) {
				tmp = 300;
			}
			if (tmp > 225) {
				document.getElementById('dpchat_wrap').style.width = (tmp+30) + 'px';
			}
		}, 200);
	};

	var confirmGoingAway = function() {
		if (hasStarted && !hasEnded) {
			return confirm('Are you sure you want to leave our website? Your chat will be closed.');
		}
	};

	//##################################################################################################################
	// Comms interface to the chat window
	//##################################################################################################################

	function childListen(messageData) {

		if (messageData && messageData.data) {
			messageData = messageData.data;
		}

		var data = messageData.split(':');
		var messageId = data.shift();

		DpConsole.log('[ChatWidget] comms received: %s %o', messageId, data);

		switch (messageId) {
			case 'started':
				util.hideEl(document.getElementById('dpchat_btn_label_start_chat'));
				util.hideEl(document.getElementById('dpchat_btn_label_start_chat2'));
				util.showEl(document.getElementById('dpchat_btn_label_open_chat'));
				util.showEl(document.getElementById('dpchat_btn_label_open_chat2'));

				// The button might be hidden because of doResume above,
				// but we want to show it all the time (its overlapped anyway)
				util.showEl(openBtn);

				tmp = util.getElWidth(document.getElementById('dpchat_border_11'));
				document.getElementById('dpchat_btn_label_start_chat2').style.width = tmp + 'px';
				document.getElementById('dpchat_btn_label_open_chat2').style.width = tmp + 'px';
				document.getElementById('dpchat_btn_label_offline2').style.width = tmp + 'px';

				if (data[0]) {
					setCookie('dpchat_sid', data[0], 7);
					DESKPRO_SESSION_ID = data[0];
				}

				break;

			case 'hide':
				self.close();
				break;

			case 'show':
				self.open();
				break;

			case 'destroy':
				self.close();
				comms.reset();
				chatIframeHolder = null;
				chatIframeWinTab = null;
				chatIframe = null;

				if (chatIframeHolder) {
					util.removeEl(chatIframeHolder);
				}

				if (!comms.hasPostMessage) {
					var targetLoc = window.location.href + '';
					window.location.replace(targetLoc.replace(/#.*$/, '') + '#');
				}
				break;
		}
	};

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
				target.postMessage(message, targetUrl.replace(/([^:]+:\/\/[^\/]+).*/, '$1'))
			} else {
				var targetLoc = targetUrl;
				target.location.replace(targetLoc.replace(/#.*$/, '') + '#' + (+new Date) + (this.cacheBust++) + '&' + message);

				if (this.resetHashTimeout) {
					window.clearTimeout(this.resetHashTimeout);
				}
				this.resetHashTimeout = window.setTimeout(function() {
					target.location.replace(targetLoc.replace(/#.*$/, '') + '#');
				}, 95);
			}
		},
		reset: function() {
			this.recieveCallback = null;
			this.lastHash = null;
			if (this.intervalId) {
				window.clearInterval(this.intervalId);
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

	//##################################################################################################################
	//# Initialize
	//##################################################################################################################

	DpConsole.log('DpChatWidget.init');
	initSession();

	return this;
})();

var DpVis = {
	init: function(visitor_code, user_token) {
		window.DpChatWidget.initVisitorCode(visitor_code, user_token);
	}
};
}