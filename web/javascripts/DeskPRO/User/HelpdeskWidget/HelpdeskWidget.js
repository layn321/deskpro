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

if (!window.DpHelpdesk_Options) DpHelpdesk_Options = {};

var HelpdeskWidget = new (function() {

	var self = this;
	var me = this;
	var iframeContainer = null;
	var iframe = null;
	var targetUrl = null;

	var options = {
		deskproUrl:     DpHelpdesk_Options.deskproUrl    || '',
		initialHeight:  DpHelpdesk_Options.initialHeight || 500,
		departmentId:   DpHelpdesk_Options.departmentId  || 0,
		containerId:    DpHelpdesk_Options.containerId   || 'dp_helpdesk',
		simpleMode:     DpHelpdesk_Options.simpleMode    || false,
		loadPath:       DpHelpdesk_Options.loadPath      || false,
		languageId:     DpHelpdesk_Options.languageId    || 0
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
		hasPostMessage: window.postMessage && (!isIE || ieVer > 8),
		cacheBust: 0,
		pollingInterval: 130,
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
		setupReciever: function(callback) {
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

	var tellChild = function(messageId, data) {
		if (!targetUrl) {
			return null;
		}

		if (typeof data != 'undefined' && !data.join) {
			data = [data];
		}

		data = data || [];
		var messageStr = messageId + ':' + data.join(':');
		try	{
			comms.send(messageStr, targetUrl, iframe.contentWindow);
		} catch (e) {
			console.log("E: %s", e);
		}

		console.log('[HelpdeskWidget] comms.send: %s %o', messageId, data);

		return null;
	};

	function childListen(messageData) {

		if (messageData && messageData.data) {
			messageData = messageData.data;
		}

		var data = messageData.split(':');
		var messageId = data.shift();

		DpConsole.log('[HelpdeskWidget] comms received: %s %o', messageId, data);

		switch (messageId) {

			// R-eady
			case 'dphR':
				var height = parseInt(data[0]);
				if (height < 300) height = 300;
				iframe.style.height = (height + 15) + 'px';

				if (data[1] && options.simpleMode) {
					targetUrl = data[1];
					targetUrl = targetUrl.replace(/__DP__COL__/, ':');
					tellChild('dphS');
				}
				break;

			// H-eight
			case 'dphH':
				var height = parseInt(data[0]);
				if (height < 300) height = 300;

				iframe.style.height = (height + 15) + 'px';
				break;
		}
	};

	this.init = function() {

		var src, iframeHtml;

		var src = options.deskproUrl;
		if (options.loadPath) {
			src += options.loadPath.replace(/^\//, '');
		}

		if (src.indexOf('?') === -1) {
			src += '?';
		} else {
			src += '&';
		}

		src += 'dp_website_url=' + encodeURIComponent(window.location + '')

		if (options.languageId) {
			src += '&language_id=' + options.languageId;
		}

		src += '#' + encodeURIComponent(document.location.href);

		iframeContainer = document.getElementById(options.containerId);
		iframeContainer.innerHTML = '<iframe id="'+options.containerId+'_iframe" name="dp_helpdesk_iframe" allowtransparency="true" src="' + src + '" style="width:100%; height: '+options.initialHeight+'px;" align="middle" frameborder="0" marginheight="0" marginwidth="0" scrolling="no"></iframe>';
		iframeContainer.style.display = 'block';

		iframe = document.getElementById(options.containerId+'_iframe');

		comms.setupReciever(function(m) {
			childListen(m);
		}, src);
	};
});

// Simple domready implementation so we dont need to include jquery
// See https://github.com/ded/domready
!function(a,b){typeof module!="undefined"?module.exports=b():typeof define=="function"&&typeof define.amd=="object"?define(b):this[a]=b()}("domready",function(a){function m(a){l=1;while(a=b.shift())a()}var b=[],c,d=!1,e=document,f=e.documentElement,g=f.doScroll,h="DOMContentLoaded",i="addEventListener",j="onreadystatechange",k="readyState",l=/^loade|c/.test(e[k]);return e[i]&&e[i](h,c=function(){e.removeEventListener(h,c,d),m()},d),g&&e.attachEvent(j,c=function(){/^c/.test(e[k])&&(e.detachEvent(j,c),m())}),a=g?function(c){self!=top?l?c():b.push(c):function(){try{f.doScroll("left")}catch(b){return setTimeout(function(){a(c)},50)}c()}()}:function(a){l?a():b.push(a)}})
domready(function() { HelpdeskWidget.init(); });