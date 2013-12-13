/**
 * User chat handler
 */
var DpChat_Display = (function() {

	var chatBox = null;
	var chatBoxBtn = null;
	var messageWrapper = null;

	var typingFuncTime = null;

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

		var html = [];
		html.push('<div id="dpchat_btn"><div id="dpchat_btn_label"><span class="start-chat">Chat</span><span class="open-chat">Open</span></div></div>');

		var el = $(html.join(''));
		el.appendTo('body');
		chatBoxBtn = el;

		chatBoxBtn.on('click', function() {
			chatBox.addClass('dpchat-panel-open');
		});

		DpChatConsole.log('DpChat_Display.initDisplay: chatBoxBtn %o', chatBoxBtn);

		//--------------------
		// The chat box
		//--------------------

		var html = [];
		html.push('<div id="dpchat_panel">');
			html.push('<div id="dpchat_titlebar"><h3>Chat</h3><span id="dpchat_closepanel">Minimize</span><span id="dpchat_endchat">End Chat</span></div>');
			if (options.departmentSelect) {
				html.push('<div id="dpchat_messages"><div class="dpchat-info dpchat-instruction alt-form">Choose a department and type in your question to get started: ' + options.departmentSelect + '</div></div>');
			} else {
				html.push('<div id="dpchat_messages"><div class="dpchat-info dpchat-instruction">Type in your question to get started</div></div>');
			}
			html.push('<div id="dpchat_input"><textarea></textarea></div>')
		html.push('</div>');

		var el = $(html.join(''));
		el.appendTo('body');
		chatBox = el;

		DpChatConsole.log('DpChat_Display.initDisplay: chatBox %o', chatBox);

		messageWrapper = $('#dpchat_messages');

		$('#dpchat_closepanel').on('click', function() {
			chatBox.removeClass('dpchat-panel-open');
		});

		$('#dpchat_endchat').on('click', function() {
			if (confirm('Are you sure you want to end this chat?')) {
				DpChat.endChat();
			}
		});

		var messageTextarea = $('#dpchat_input > textarea');
		messageTextarea.on('keypress', function(ev) {
			DpChat.userTypingIndicator(messageTextarea.val());

			if (ev.keyCode == 13 && !ev.metaKey) {
				ev.preventDefault();

				var msg = $.trim(messageTextarea.val());
				messageTextarea.val('');

				if (!msg.length) {
					return;
				}

				if (typingFuncTime) {
					window.clearTimeout(typingFuncTime);
				}

				DpChat.sendMessage(msg, getAltFormData());
				self.addMessageRow('You', msg, 'user');
			}
		});
	};

	var getAltFormData = function() {
		return $('.alt-form :input', chatBox).serializeArray();
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
	this.addMessageRow = function(name, message, type) {

		chatBoxBtn.addClass('has-chat');
		chatBox.addClass('has-chat');
		$('.dpchat-instruction', messageWrapper).hide();

		type = type || 'user';
		if (type == 'sys') {
			name = '* ';
		} else {
			name = name + ': ';
		}

		var html = [];
		html.push('<div class="dpchat-message dpchat-'+type+'">');
			html.push('<div class="dpchat-author">' + name + '</div>');
			html.push('<div class="dpchat-msg">' + message + '</div>');
		html.push('</div>');

		var el = $(html.join(''));

		el.appendTo(messageWrapper);

		messageWrapper.scrollTop(100000);

		return el;
	};

	this.destroy = function() {
		chatBox.remove();
		chatBoxBtn.remove();
	};

	return this;
})();
DpChat.setDisplay(DpChat_Display);
