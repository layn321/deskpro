/**
 * User chat handler
 */
var DpChat_Display = (function() {

	var chatBox = null;
	var chatBoxBtn = null;
	var messageWrapper = null;
	var findingAgentEl = null;
	var findingAgentLongEl = null;
	var noAgentTimer = null;

	var self = this;

	var $ = null;

	this.initDisplay = function(options) {

		$ = options.jQuery;

		DpChatConsole.log('DpChat_Display.initDisplay');

		if (chatBox) {
			chatBox.remove();
			chatBoxBtn.remove();
		}

		//--------------------
		// The butotn that opens the chat
		//--------------------

		var el = $('<div id="dpchat_btn" style="display: none" class="dp-hide-print"><div id="dpchat_btn_label"><span class="start-chat">Click here to chat with us</span><span class="open-chat">Open your chat</span></div></div>');
		el.appendTo('body');
		chatBoxBtn = $(el);

		chatBoxBtn.on('click', function() {
			chatBox.addClass('dpchat-panel-open');
		});

		DpChatConsole.log('DpChat_Display.initDisplay: chatBoxBtn %o', chatBoxBtn);

		//--------------------
		// The chat box
		//--------------------

		options.name = options.email = 1;

		var html = [];
		html.push('<div id="dpchat_panel" style="display: none" class="dp-hide-print">');
			html.push('<div id="dpchat_titlebar"><h3>Chat</h3><span id="dpchat_closepanel">Minimize</span><span id="dpchat_endchat">End Chat</span><span id="dpchat_popchat">Open in new window</span></div>');
			if (options.name || options.email || options.departmentSelect) {
				html.push('<div id="dpchat_preform">')

				if (options.name) {
					html.push('<div class="dpchat-row dpchat-name">');
						html.push('<label>Your name:</label><div class="dpchat-input"><input type="text" value="" name="name" /></div>');
					html.push('</div>');
				}
				if (options.email) {
					html.push('<div class="dpchat-row dpchat-email">');
						html.push('<label>Your email address:</label><div class="dpchat-input"><input type="text" value="" name="email" /></div>');
					html.push('</div>');
				}
				if (options.departmentSelect) {
					html.push('<div class="dpchat-row dpchat-department">');
						html.push('<label>Choose a department:</label><div class="dpchat-input">' + options.departmentSelect + '</div>');
					html.push('</div>');
				}

				html.push('<div class="controls"><button id="dpchat_preform_submit">Start Chatting</button></div>');
				html.push('</div>');
			}
			html.push('<div id="dpchat_messages" ' + (options.departmentSelect ? 'style="display:none"' : '') + '>');
				html.push('<div class="dpchat-info dpchat-instruction">Type in your question to get started</div>');
				html.push('<div class="dpchat-finding-agent" style="display: none">Please wait while we find an agent to take your chat.</div>');
				html.push('<div class="dpchat-finding-agent-long" style="display: none">We are still trying to find an agent to take your chat but it is taking longer than we thought. Maybe you want to <a href="'+DpChat_Options.deskproUrl+'tickets/new">send us an email</a> instead?</div>');
			html.push('</div>');
			html.push('<div id="dpchat_input" ' + (options.departmentSelect ? 'style="display:none"' : '') + '><textarea></textarea><button id="dpchat_send">Send</button><div id="dpchat_upload"><span id="dpchat_upload_label">Send a file from your computer</span><span id="dpchat_upload_exist" style="display: none"></span></div></div>')
			html.push('<div id="dpchat_ended" style="display:none">Your chat has finished. <a id="dpchat_ended_send_btn">Click here to send a chat transcript.</a><span id="dpchat_ended_did_send">A transcript has been sent to <span class="dp-chat-insert-email"></span></span><div style="padding-top: 10px;text-align: center;"><button id="dpchat_start_new">Start another chat</button></div></div>')
			html.push('<div id="dpchat_sound_tpl" style="display:none"><audio preload="preload"><source src="' + DpChat_Options.staticUrl + 'sounds/pop.mp3" /><source src="' + DpChat_Options.staticUrl + 'sounds/pop.ogg" /></audio></div>');
		html.push('</div>');

		var el = $(html.join(''));

		if (options.formValues && options.formValues.length) {
			var x = 0;
			for (x = 0; x < options.formValues.length; x++) {
				$('[name="' + options.formValues[x][0] + '"]', el).val(options.formValues[x][1]);
			}
		}

		el.appendTo('body');
		chatBox = el;

		$('#dpchat_start_new').on('click', function() {
			DpChat.endChatReboot();
		});

		DpChatConsole.log('DpChat_Display.initDisplay: chatBox %o', chatBox);

		messageWrapper = $('#dpchat_messages');
		findingAgentEl = $('.dpchat-finding-agent', messageWrapper);
		findingAgentLongEl = $('.dpchat-finding-agent-long', messageWrapper);

		$('a', findingAgentLongEl).on('click', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			var href = $(this).attr('href');
			chatBox.hide();
			chatBoxBtn.hide();
			DpChat.endChat(function() {
				window.location = href;
			});
		});

		var self = this;
		$('#dpchat_preform_submit').on('click', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			var altData = getAltFormData();
			altData.startNew = true;
			DpChat.sendMessage('', altData);
			self.addMessageRow(false, false, 'justStart');
		});

		$('#dpchat_closepanel').on('click', function() {
			chatBox.removeClass('dpchat-panel-open');
		});

		$('#dpchat_endchat').on('click', function() {
			if (confirm('Are you sure you want to end this chat?')) {
				DpChat.endChat();
			}
		});

		$('#dpchat_popchat').on('click', function() {
			DpChat.popChat();
		});

		var messageTextarea = $('#dpchat_input > textarea');
		if (window.sessionStorage) {
			var val = window.sessionStorage.getItem('dpchat_typing');
			if (val) {
				messageTextarea.val(val);
			}
			delete val;
		}
		messageTextarea.on('keyup', function(ev) {
			if (window.sessionStorage) window.sessionStorage.setItem('dpchat_typing', messageTextarea.val());
			DpChat.userTypingIndicator(messageTextarea.val());
		});
		messageTextarea.on('change', function(ev) {
			if (window.sessionStorage) window.sessionStorage.setItem('dpchat_typing', messageTextarea.val());
			DpChat.userTypingIndicator(messageTextarea.val());
		});
		messageTextarea.on('keypress', function(ev) {
			if (ev.keyCode == 13 && !ev.metaKey) {
				ev.preventDefault();
				doSend();
			}
		});

		$('#dpchat_send').on('click', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();
			doSend();
		});

		$('#dpchat_btn').show();
		$('#dpchat_panel').show();
	};

	var playSound = function() {
		var audio = $('#dpchat_sound_tpl > audio').clone();
		audio.attr('preload', null);
		audio.bind('ended', function() {
			$(this).remove();
		});

		audio.appendTo('body');
		try {
			audio.get(0).play();
		} catch (e) { }
	};

	this.getMessage = function() {
		return $('#dpchat_input > textarea').val();
	};

	var doSend = function() {
		var messageTextarea = $('#dpchat_input > textarea');

		var msg = $.trim(messageTextarea.val());
		messageTextarea.val('');

		if (!msg.length) {
			return;
		}

		DpChat.sendMessage(msg, getAltFormData());
		self.addMessageRow('You', msg, 'user');
	};

	this.showAssignedStatus = function(isAssigned) {
		findingAgentLongEl.hide();
		if (!isAssigned) {
			if (noAgentTimer) window.clearTimeout(noAgentTimer);
			noAgentTimer = window.setTimeout(function() { DpChat_Display.showTicketLink(); }, 30000);

			findingAgentEl.detach().appendTo(messageWrapper).show();
			DpChatConsole.log('DpChat_Display.showAssignedStatus: not assigned');
		} else {
			if (noAgentTimer) window.clearTimeout(noAgentTimer);
			noAgentTimer = null;

			findingAgentEl.hide();
			DpChatConsole.log('DpChat_Display.showAssignedStatus: assigned');
		}

		if (DpChat.assignedCallback) {
			DpChat.assignedCallback();
			DpChat.assignedCallback = null;
		}
	};

	this.showTicketLink = function() {
		findingAgentEl.hide();
		findingAgentLongEl.detach().appendTo(messageWrapper).show();
	};

	this.showProactive = function() {
		var html = [];
		html.push('<div id="dpchat_proactive_wrapper">');
			html.push('<div id="dpchat_proactive_close"></div>');
		html.push('</div>');

		var el = $(html.join('')).appendTo('body');
		el.on('click', function() {
			el.remove();
			DpChat.popChat();
		});
		$('#dpchat_proactive_close').on('click', function(ev) {
			ev.stopPropagation();
			el.remove();

			DpChat.proactiveHidden();
		});
	};

	var getAltFormData = function() {
		return $('#dpchat_preform :input').serializeArray();
	};

	this.showChatPanel = function() {
		chatBox.addClass('dpchat-panel-open');
	};

	this.hideChatPanel = function() {
		chatBox.removeClass('dpchat-panel-open');
	};

	var overlay = null;
	this.openIframeOverlay = function(url) {
		var html = [];
		html.push('<div class="dpchat-overlay-outer"');
			html.push('<div class="dpchat-overlay-inner">');
				html.push('<div class="dpchat-overlay-content">');
					html.push('<a class="close-overlay close-trigger">X</a>');
					html.push('<iframe src="'+url+'" style="width: 600px; height: 400px;" marginheight="0" marginwidth="0" frameborder="0"></iframe>');
				html.push('</div>');
			html.push('</div>');
		html.push('</div>');

		overlay = $(html.join(''));
		$('.close-trigger', overlay).on('click', function() {
			overlay.remove();
		});

		var x = ($(window).width() - 600) / 2;
		var y = ($(window).height() - 400) / 2;

		overlay.css({
			'zIndex': 1000000,
			'display': 'block',
			'top': y,
			'right': x
		});

		overlay.appendTo('body');
	};


	/**
	 * Add a message row
	 *
	 * @param name
	 * @param message
	 * @param type
	 */
	this.addMessageRow = function(name, message, type, meta) {
		if (!meta) {
			meta = {};
		}

		var is_html = false;
		if (meta.is_html) {
			is_html = true;
		}

		$('#dpchat_preform').hide();
		$('#dpchat_messages').show();
		$('#dpchat_input').show();

		chatBoxBtn.addClass('has-chat');
		chatBox.addClass('has-chat');
		$('.dpchat-instruction', messageWrapper).hide();

		if (type == 'justStart') {
			return;
		}

		type = type || 'user';
		if (type == 'sys') {
			name = '* ';
		} else {
			name = name + ': ';
		}

		var html = [];
		html.push('<div class="dpchat-message dpchat-'+type+'">');
			html.push('<div class="dpchat-author">' + name + '</div>');
			html.push('<div class="dpchat-msg"></div>');
		html.push('</div>');

		var el = $(html.join(''));
		if (is_html) {
			$('.dpchat-msg', el).html(message);
		} else {
			message = DpChat.util.escapeHtml(message);
			message = DpChat.util.linkUrls(message);
			$('.dpchat-msg', el).html(message);
		}

		el.appendTo(messageWrapper);

		messageWrapper.scrollTop(100000);

		DpChat_Display.showChatPanel();

		if (type == 'agent' && !meta.is_initial) {
			playSound();
		}

		return el;
	};

	this.showEnd = function() {

		var email = $('#dpchat_preform').find('input[name="email"]').val().trim();
		if (email) {
			$('#dpchat_ended_send_btn').hide();
			$('#dpchat_ended_did_send').show().find('.dp-chat-insert-email').text(email);
		} else {
			$('#dpchat_ended_send_btn').show();
			$('#dpchat_ended_did_send').hide();
		}

		$('#dpchat_input').hide();
		$('#dpchat_endchat').hide();
		$('#dpchat_ended').show();

		$('#dpchat_ended_send_btn').attr('href', DpChat.getFinisehdUrl()).on('click', function(ev) {
			ev.stopPropagation();
			ev.preventDefault();
			window.open(DpChat.getFinisehdUrl());
		});
	},

	this.destroy = function() {
		chatBox.remove();
		chatBoxBtn.remove();
	};

	return this;
})();
DpChat.setDisplay(DpChat_Display);
