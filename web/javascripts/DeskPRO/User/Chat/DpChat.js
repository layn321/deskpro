if (window.DpChat_EnableDebug) {
	var DpChatConsole = window.console;
} else {
	var DpChatConsole = {};
	DpChatConsole['error'] = function(){};
	DpChatConsole['log'] = function(){};
	DpChatConsole['warn'] = function(){};
	DpChatConsole['info'] = function(){};
	DpChatConsole['debug'] = function(){};
}

/**
 * The main user chat handler. Loaded onto a page using a async loader (UserBundle:Common:chat-loader.html.twig).
 *
 * This itself loads the display adapter for the theme, and then sets up and handles proper routing
 * of chat messages etc.
 */
var DpChatMake = function() {
	var DpChat = (function() {
		var options = {
			protocol: null,
			staticUrl: null,
			deskproUrl: null,
			displayType: 'Box'
		};

		var self = this;

		var hasStarted = false;

		/**
		 * Scoped reference to jQuery
		 * @var {jQuery}
		 */
		var $ = null;

		/**
		 * The display handler that defines the theme etc
		 */
		var display = null;

		/**
		 * The session code of this users session
		 * @var {Integer}
		 */
		var sessionCode = null;

		/**
		 * Any previous messages that'll be pushed into the chat window upon load
		 * @var {Array}
		 */
		var initialMessages = null;

		/**
		 * HTML for a select box if department selector is enabled
		 * @var {String}
		 */
		var departmentSelect = null;

		/**
		 * If no one is available for chat
		 * @var {Boolean}
		 */
		var notAvailable = null;

		var scriptDisplay = null;
		var scriptSession = null;

		var hasEnded = false;

		var showProactiveChat = false;

		var conversationId = 0;
		var lastMessageId = 0;

		var formVars = [];
		var doAutoOpen = false;

		/**
		 * This is a pre-init that is called automatically when the client has downloaded
		 * this source file. It ensures jQuery first, and then runs initScript that starts
		 * our actual chat init.
		 */
		this.init = function(autoOpen) {
			doAutoOpen = autoOpen;

			DpChatConsole.log('DpChat.init');

			if (window.jQuery === undefined || window.jQuery.fn.jquery.indexOf('1.7.') === -1) {

				DpChatConsole.log('DpChat.init: loading jquery');

				var initJquery = function() {
					DpChatConsole.log('DpChat.init: jquery loaded');
					$ = window.jQuery.noConflict(true);
					initScript();
				};

				var script_tag = document.createElement('script');
				script_tag.setAttribute("type", "text/javascript");
				script_tag.setAttribute("src", ('https:' == document.location.protocol ? 'https' : 'http') + "://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js");
				script_tag.setAttribute("async", 'true');
				script_tag.onload = function() {
					initJquery();
				};
				script_tag.onreadystatechange = function () { // Same thing but for IE
					if (this.readyState == 'complete' || this.readyState == 'loaded') {
						initJquery();
					}
				};

				(document.getElementsByTagName("head")[0] || document.documentElement).appendChild(script_tag);
			} else {

				DpChatConsole.log('DpChat.init: already have jquery');

				$ = window.jQuery;
				initScript();
			}
		};


		/**
		 * initScript() is called from init() and loads the resources for the theme, and
		 * also fetches the users session code and existing chat data, if there is any from previous pages.
		 */
		var initScript = function() {

			DpChatConsole.log('DpChat.initScript');

			if (window.DpChat_Options) {
				options = $.extend({}, options, window.DpChat_Options);
			}

			if (window.DpChat_Options.interceptLeavingDomains) {
				$(document).on('click', 'a', function(ev) {
					if ($(this).is('dp-no-touch')) {
						return;
					}

					var href = $(this).attr('href');
					if (!href) return;

					var m = href.match(/:\/\/(.[^/]+)/)[1];
					if (!m || !m[1]) return;

					var domain = m[1];

					var foundDomain = false;

					Array.each(DpChat_Options.interceptLeavingDomains, function(checkDomain) {
						if (domain == checkDomain || domain.indexOf(checkDomain) !== -1) {
							foundDomain = true;
							return false;
						}
					});

					if (!foundDomain) {
						if (!confirmGoingAway()) {
							ev.preventDefault();
							ev.stopPropagation();
						}
					}
				});
			}

			// Box.js
			scriptDisplay = $('<script type="text/javascript" async="true" src="' + options.staticUrl + 'javascripts/DeskPRO/User/Chat/Display/' + options.displayType + '.js?___='+(new Date().getTime())+'"></script>').appendTo('body');

			// DeskPRO script that sets/gets session and initial messages
			var url = options.deskproUrl + 'chat/chat-session?_1=';
			if (DpChat_Options && DpChat_Options.currentPageUrl) {
				url += DpChat_Options.currentPageUrl;
			} else {
				url += encodeURIComponent(document.location.href);
			}
			url += '&amp;_2=';

			if (DpChat_Options && DpChat_Options.referrerPageUrl) {
				url += encodeURIComponent(document.location.href);
			} else {
				url += encodeURIComponent(document.referrer);
			}

			url += '&amp;'+(new Date().getTime());

			if (options.displayType == 'DpWindow') {
				url += '&amp;is_window=1';
			}

			scriptSession = $('<script type="text/javascript" async="true" src="' + url + '"></script>').appendTo('body');

			// Box.css
			$('<link rel="stylesheet" type="text/css" href="' + options.staticUrl + 'javascripts/DeskPRO/User/Chat/Display/' + options.displayType + '.css" />').appendTo('body');

			$('<div id="dpchat_events" style="width: 1px; height: 1px; background: transparent; border: none; position: absolute; left: 0; bottom: 0;" />').appendTo('body');
		};

		var confirmGoingAway = function() {
			if (hasStarted && !hasEnded) {
				return confirm('Are you sure you want to leave our website? Your chat will be closed.');
			}
		};


		/**
		 * When the display source file is loaded by the client, it calls DpChat.setDisplay() to set itself.
		 * If the sessionCode is already fetched, then the main chat app can finally be fully set up.
		 *
		 * @param display
		 */
		this.setDisplay = function(_display) {
			DpChatConsole.log('DpChat.setDisplay(%o)', _display);
			display = _display;

			mainRunner();
		};


		/**
		 * @return display
		 */
		this.getDislay = function() {
			return display;
		};


		/**
		 * When the session source file is loaded by the client, it calls this DpChat.setSessionCode() to set itself.
		 * Just like setDisplay, it checks if all values are set and if they are, main() is called to fully run the chat.
		 *
		 * @param sessionCode
		 */
		this.setSessionCode = function(_sessionCode, _showProactive) {
			DpChatConsole.log('DpChat.setSessionCode(%o)', _sessionCode);
			sessionCode = _sessionCode;
			showProactiveChat = _showProactive;

			mainRunner();
		};

		/**
		 * The session source can call DpChat.setInitialMessages() to load messages that were exchanged on a previous
		 * page.
		 */
		this.setInitialMessages = function(_initialMessages) {
			DpChatConsole.log('DpChat.setInitialMessages(%o)', _initialMessages);
			initialMessages = _initialMessages;
		};

		/**
		 * The session source can tell DpChat to set the select box options
		 */
		this.setDepartmentSelect = function(_departmentSelect) {
			DpChatConsole.log('DpChat.setDepartmentSelect(%o)', _departmentSelect);
			departmentSelect = _departmentSelect;
		};

		/**
		 * Called from the session source to say no one is available for chat
		 */
		this.setNotAvailable = function() {
			DpChatConsole.log('DpChat.setNotAvailable');
			notAvailable = true;
		};

		/**
		 * Pop the chat out into a new window
		 */
		this.popChat = function() {

			console.log('DpChat.popChat');

			hasEnded = true;
			display.destroy();
			ajaxPoller.disable = true;
			ajaxPoller._clearDelays();

			var url = options.deskproUrl + 'chat/chat-window/' + sessionCode;
			var win = window.open(url,'dpchat','width=650,height=500,location=0,menubar=0,scrollbars=0,status=0,toolbar=0,resizable=1');
			return win;
		};

		/**
		 * Opens the chat panel
		 */
		this.openChatPanel = function() {
			display.showChatPanel();
		};

		/**
		 * Called when a user hides a proactive. We insert a script request so it
		 * sets the cookie on the correct domain
		 */
		this.proactiveHidden = function() {
			var url = options.deskproUrl + 'chat/ignore-proactive';
			var el = $('<script type="text/javascript" async="true" src="' + url + '"></script>').appendTo('body');
		};

		/**
		 * Sets a value to be used in the template form
		 *
		 * @param {String} k
		 * @param {Mixed} v
		 */
		this.setFormVar = function(k, v) {
			formVars.push([k, v]);
		}

		//#################################################################
		//# Simple implementations of message broker and poller
		//#################################################################

		var messageBroker = this.messageBroker = {
			messageListeners: {},

			sendMessage: function (name, data) {

				if (this.messageListeners[name] !== undefined) {
					for (var x = 0; x < this.messageListeners[name].length; x++) {
						this.messageListeners[name][x](data, name);
					}
				}

				var nameparts = name.split('.');
				var cur_name = null;

				while (nameparts.pop()) {
					cur_name = nameparts.join('.') + '.*';
					if (this.messageListeners[cur_name] !== undefined) {
						for (var x = 0; x < this.messageListeners[cur_name].length; x++) {
							this.messageListeners[cur_name][x](data, name);
						}
					}
				}
			},

			addMessageListener: function(name, callback) {
				if (this.messageListeners[name] === undefined) {
					this.messageListeners[name] = [];
				}

				this.messageListeners[name].push(callback);
			}
		};

		var ajaxPoller = this.ajaxPoller = {
			options: {
				interval: 2000,
				initialDelay: 1500
			},
			filterdData: [],
			disable: true,
			maxDelayTimers: [],

			init: function() {
				this.autoSendTimeout = Function_Delay(this.send, this.options.initialDelay, this);
			},



			addData: function(data, name, options) {
				name = name || 'default';
				options = options || {};

				if (options.addedTime === undefined) {
					options.addedTime = new Date();
				}

				if (options.maxDelay) {
					Function_Delay(function() {
						this.send();
					}, options.maxDelay, this);
				}

				this.filterdData.push([name, data, options]);
			},

			send: function() {

				this._clearDelays();

				if (this.disable) {
					this.autoSendTimeout = Function_Delay(this.send, this.options.interval, this);
					return;
				}

				//------------------------------
				// Build data to send
				//------------------------------

				var now = new Date();

				var send_data = [];
				var sent_info = [];

				var filterdData = this.filterdData;
				this.filterdData = [];

				var item = null;
				while (item = filterdData.shift()) {
					var item_name = item[0];
					var item_data = item_orig_data = item[1];
					var item_opts = item[2];

					if (item_opts.minDelay && !(item_opts.minDelayAfterOne && !item_opts.sentCount)) {
						// If its too soon, add it back immediately
						if (item_opts.minDelay > (now.getTime() - item_opts.addedTime.getTime())) {
							this.addData(item_orig_data, item_name, item_opts);
							continue;
						}
					}

					item_data = item_data(item_name, {}, item_opts);
					send_data.push(item_data);

					sent_info.push([item_orig_data, item_name, item_opts]);
				}

				send_data.push({
					name: 'since',
					value: lastMessageId
				});
				send_data.push({
					name: 'conversation_id',
					value: conversationId
				});

				//------------------------------
				// Send data
				//------------------------------

				$.ajax({
					cache: false,
					url: options.deskproUrl + 'chat/poll/' + sessionCode,
					context: this,
					crossDomain: true,
					data: send_data,
					dataType: 'jsonp',
					complete: function() {
						// Start auto timer
						this.autoSendTimeout = Function_Delay(this.send, this.options.interval, this);
					},
					success: function (data) {

						if (data.conversation_id) {
							conversationId = data.conversation_id;
						}
						if (data.last_id) {
							lastMessageId = data.last_id;
						}
						this._handleAjaxSuccess({
							messages: data.messages
						}, sent_info);
					}
				});
			},

			_handleAjaxSuccess: function (data, sent_info) {

				var item = null;
				while (item = sent_info.shift()) {
					var item_name = item[0];
					var item_data = item[1];
					var item_opts = item[2];

					if (item_opts.recurring) {
						item_opts.lastSent = new Date();

						if (item_opts.sentCount === undefined) item_opts.sentCount = 0;
						item_opts.sentCount++;

						// Delete addedTime so minDelay check will reset too
						delete item_opts.addedTime;

						this.addData(item_name, item_data, item_opts);
					}
				}

				if (data.messages === undefined) {
					return;
				}

				if (data.messages.length) {

					console.log('DpChat Poll Data: %o', data);

					var message = null;
					while (message = data.messages.shift()) {
						var name = message[0].replace(/chat_convo\.([0-9]+)\./, '');
						messageBroker.sendMessage(name, message[1]);
					}
				}
			},

			_clearDelays: function() {

				this.autoSendTimeout = window.clearTimeout(this.autoSendTimeout);
				this.autoSendTimeout = null;

				var t = null;
				while (t = this.maxDelayTimers.pop()) {
					window.clearTimeout(t);
				}
			}
		};


		/**
		 * Sends a new chat message. The server decides if the chat should be new or not
		 *
		 * @param message
		 */
		this.sendMessage = function(message, data) {

			DpChatConsole.log('DpChat.sendMessage: %s', message);

			hasStarted = true;
			if (typingIndicatorTime) window.clearTimeout(typingIndicatorTime);

			data = data || [];
			data.push({
				name: 'content',
				value: message
			});

			$.ajax({
				cache: false,
				url: options.deskproUrl + 'chat/send-message/' + sessionCode,
				context: this,
				crossDomain: true,
				data: data,
				dataType: 'jsonp',
				success: function(data) {
					if (data.conversation_id) {
						conversationId = data.conversation_id;
					}

					ajaxPoller.options.interval = 2000;
					ajaxPoller.disable = false;
					ajaxPoller.send();
				}
			});
		};

		var typingIndicatorTime = null;
		var typingIndicatorMsg = null;
		this.userTypingIndicator = function(message) {
			if (!hasStarted) return;
			if (typingIndicatorTime) return;

			typingIndicatorTime = Function_Delay(sendTypingIndicator, 1000, self);
		};

		var sendTypingIndicator;
		sendTypingIndicator = function() {
			if (typingIndicatorTime) {
				window.clearTimeout(typingIndicatorTime);
				typingIndicatorTime = null;
			}

			typingIndicatorMsg = $.trim(DpChat_Display.getMessage());

			$.ajax({
				cache: false,
				url: options.deskproUrl + 'chat/user-typing/' + sessionCode,
				context: this,
				crossDomain: true,
				data: {'partial_message': typingIndicatorMsg},
				dataType: 'jsonp'
			});
		};

		//#################################################################
		//# Util
		//#################################################################

		var Function_Delay = function(fn, delay, bind, args) {
			args = args || [];
			bind = bind || fn;

			var timeout = window.setTimeout(function() {
				fn.apply(bind, args);
			}, delay);
			return timeout;
		};

		var escapeHtml = function(string) {
			return string.replace(/&/g, "&amp;")
				.replace(/>/g, "&gt;")
				.replace(/</g, "&lt;")
				.replace(/"/g, "&quot;");
		};

		var linkUrls = function(string) {
			return string
				.replace(/(https?:\/\/[^\s]+)/gi, '<a href="$1">$1</a>');
		};

		this.util = {
			escapeHtml: escapeHtml,
			linkUrls: linkUrls,
			Function_Delay: Function_Delay
		};

		//#################################################################
		//# Main
		//#################################################################

		/**
		 * Called in the setX methods to run main once all data has been collected
		 */
		var mainRunner = function() {
			if (display && sessionCode) {
				main();
			}
		};

		var main = function() {

			DpChatConsole.log('DpChat.main');

			ajaxPoller.init();
			display.initDisplay({
				jQuery: $,
				departmentSelect: departmentSelect,
				formValues: formVars
			});

			if (initialMessages) {

				hasStarted = true;

				for (var i = 0; i < initialMessages.length; i++) {
					var info = initialMessages[i];
					info.is_initial = true;
					if (initialMessages[i].is_html) {
						info.is_html = true;
					}

					if (initialMessages[i].author_type == 'user') {
						initialMessages[i].author_name = 'You';
					}

					display.addMessageRow(
						initialMessages[i].author_name,
						initialMessages[i].content,
						initialMessages[i].author_type,
						info
					);
				}

				display.showChatPanel();
				ajaxPoller.options.interval = 2000;
				ajaxPoller.disable = false;
				ajaxPoller.send();

				chatAssigned({agent_id:1});
			}

			messageBroker.addMessageListener('newmessage', addIncomingMessage);

			if (showProactiveChat) {
				display.showProactive();
			}

			// Attach click event to any chat triggers to open new chat window
			$('.dp-chat-trigger').on('click', function(ev) {
				ev.preventDefault();
				ev.stopPropagation();

				DpChat.showChatPanel();
			});
			$('.dp-chat-window-trigger').on('click', function(ev) {
				ev.preventDefault();
				ev.stopPropagation();

				DpChat.popChat();
			});

			if (doAutoOpen) {
				display.showChatPanel();
			}
		};

		var chatAssigned = function(data) {
			if (!display.showAssignedStatus) {
				return;
			}
			var isAssigned = true;
			if (!parseInt(data.agent_id)) {
				isAssigned = false;
			}
			display.showAssignedStatus(isAssigned);
		};
		this.chatAssigned = chatAssigned;

		var addIncomingMessage = function(data) {
			if (data.is_html) {
				if (!data.metadata) {
					data.metadata = {};
				}
				data.metadata.is_html = true;
			}

			DpChatConsole.log('DpChat_Display:addIncomingMessage: %o', data);

			display.addMessageRow(data.author_name, data.content, data.author_type, data.metadata);

			if (data.metadata.chat_unassigned) {
				chatAssigned({ agent_id: 0 });
			}
			if (data.metadata.chat_assigned) {
				chatAssigned({ agent_id: data.metadata.assigned_to });
			}

			if (data.metadata.chat_ended) {
				endChat();
			}
		};

		var endChat = this.endChat = function(callback) {
			if (hasEnded) return; //already ended
			hasEnded = true;
			ajaxPoller.disable = true;
			ajaxPoller._clearDelays();

			$.ajax({
				cache: false,
				url: options.deskproUrl + 'chat/chat-finished/' + sessionCode + '?conversation_id=' + conversationId + '&is_ajax=1',
				context: this,
				crossDomain: true,
				dataType: 'jsonp',
				complete: callback || function() {}
			});

			DpChat_Display.showEnd();
		};

		this.getFinisehdUrl = function() {
			return options.deskproUrl + 'chat/chat-finished/' + sessionCode + '?conversation_id=' + conversationId
		};

		this.endChatReboot = function() {
			DpChatConsole.log('DpChat_Display.endChatReboot');
			window.DpChat_Display.destroy();
			delete window.DpChat_Display;
			delete window.DpChat;

			window.DpChat = window.DpChatMake();
			window.DpChat.init(true);
		};

		return this;
	})();
	return DpChat;
};
window.DpChat = DpChatMake();
window.DpChat.init();
