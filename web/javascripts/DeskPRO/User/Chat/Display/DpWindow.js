/**
 * User chat handler
 */
var DpChat_Display = (function() {

	var $ = null;
	var findingAgentEl = null;

	this.initDisplay = function(options) {

		$ = options.jQuery;

		DpChatConsole.log('DpChat_Display.initDisplay');

		$('#chat_message_tpl').template('chat_message_tpl');
		$('#chat_sys_message_tpl').template('chat_sys_message_tpl');

		$('#dpchat_endchat').on('click', function() {
			if (confirm('Are you sure you want to end this chat?')) {
				DpChat.endChat();
			}
		});

		findingAgentEl = $('.dpchat-finding-agent');

		var html = [];
		html.push('<div id="dpchat_intro" class="dpchat-instruction alt-form">');
		html.push('<input type="hidden" name="is_window" value="1" />');
		if (options.departmentSelect) {
			html.push('Choose a department and type your question into the message box to get started: ' + options.departmentSelect + '');
		} else {
			html.push('Type your question into the message box to get started');
		}
		html.push('</div>');
		var el = $(html.join(''));
		el.appendTo('body');

		var messageTextarea = $('#dpchat_input textarea:first');
		messageTextarea.on('keypress', function(ev) {
			DpChat.userTypingIndicator(messageTextarea.val());

			if (ev.keyCode == 13 && !ev.metaKey) {
				ev.preventDefault();

				var msg = $.trim(messageTextarea.val());
				messageTextarea.val('');

				if (!msg.length) {
					return;
				}

				DpChat.sendMessage(msg, getAltFormData());
				self.addMessageRow('You', msg, 'user');
			}
		});
		messageTextarea.focus();
	};

	var getAltFormData = function() {
		return $('#dpchat_intro :input').serializeArray();
	};

	var overlay = null;
	this.openIframeOverlay = function(url) {
		var html = [];
		html.push('<div id="dpchat_iframe_wrap">');
			html.push('<iframe src="'+url+'" marginheight="0" marginwidth="0" frameborder="0"></iframe>');
		html.push('</div>');

		overlay = $(html.join(''));
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

		$('body').addClass('has-chat');
		$('#dpchat_intro').hide();

		var tpl = 'chat_message_tpl';
		if (type == 'sys') {
			tpl = 'chat_sys_message_tpl';
		}

		message = Orb.escapeHtml(message);
		message = Orb.linkUrls(message);

		var el = $.tmpl(tpl, {
			author: name,
			message: message,
			type: type
		});

		el.appendTo('#dpchat_messages');

		$('#dpchat_messages').scrollTop(100000);

		return el;
	};

	this.showAssignedStatus = function(isAssigned) {
		if (!isAssigned) {
			findingAgentEl.detach().appendTo('#dpchat_messages').show();
		} else {
			findingAgentEl.hide();
		}
	};

	this.destroy = function() {

	};

	this.showChatPanel = function() {};

	return this;
})();
DpChat.setDisplay(DpChat_Display);
