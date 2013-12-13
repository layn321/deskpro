Orb.createNamespace('DeskPRO.User.WebsiteWidget');

DeskPRO.User.WebsiteWidget.ChatWin = new Orb.Class({
	Implements: [Orb.Util.Options, Orb.Util.Events],

	initialize: function(options) {
		this.options = {
			findingAgentTimeout: 30000
		};
		this.setOptions(options || {});

		if (!this.options.isWindowMode) {
			var isIE  = (navigator && navigator.appName && navigator.appName == 'Microsoft Internet Explorer');
			var ieVer = 0;
			if (isIE) {
				var re = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
				if (re.exec(navigator.userAgent) != null) ieVer = parseFloat(RegExp.$1);
			}

			this.comms = {
				intervalId: null,
				lastHash: null,
				hasPostMessage: window.postMessage && (!isIE || ieVer > 9),
				cacheBust: 0,
				pollingInterval: 130,
				recieveCallback: null,
				resetHashTimeout: null,
				send: function(message, targetUrl, target) {
					if (this.hasPostMessage) {
						target.postMessage(message, targetUrl.replace(/([^:]+:\/\/[^\/]+).*/, '$1'))
					} else {
						var targetLoc = targetUrl;
						targetLoc = targetLoc.replace(/#.*$/, '') + '#' + (+new Date) + (this.cacheBust++) + '&' + message;
						target.location.replace(targetLoc);

						if (this.resetHashTimeout) {
							window.clearTimeout(this.resetHashTimeout);
						}
						this.resetHashTimeout = window.setTimeout(function() {
							target.location.replace(targetLoc.replace(/#.*$/, '') + '#');
						}, 95);
					}
				},
				setupReciever: function(callback, sourceUrl) {
					// Unset existing
					if (callback && this.recieveCallback) {
						this.recieveCallback = null;
						this.setupReciever(null, '');
					}

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
							}, this.pollingInterval);
						}
					}
				}
			};
		} else {
			this.comms = {
				intervalId: null,
				lastHash: null,
				hasPostMessage: false,
				cacheBust: 0,
				pollingInterval: 10000,
				recieveCallback: null,
				send: function(message, targetUrl, target) {

				},
				setupReciever: function(callback, sourceUrl) {

				}
			};
		}

		if (this.options.parentUrl) {
			this.parentUrl = this.options.parentUrl;
		} else {
			var hash = window.location.hash + '';
			this.parentUrl = decodeURIComponent(hash.replace(/^#/, ''));
		}

		this.sentLoadingIndicator = false;
		this.hasStarted = false;
		this.hasBeenAssigned = false;
		this.sessionCode = options.sessionCode || null;
		this.lastMessageId = 0;
		this.conversationId = options.conversationId || 0;
		this.messageAck = [];

		this._initSysObjects();
	},

	//#########################################################################
	//# Department and field stuff
	//#########################################################################

	_initFields: function() {
		this.depSelect = $('select.department_id, input.department_id').first();
		this.departmentId = -1;

		var self = this;
		this.depSelect.on('change', function() {
			self.handleDepChange();
		});
		this.depSelect.data('original-name', this.depSelect.attr('name'));

		this.handleDepChange();
	},

	handleDepChange: function() {
		this.setDepartment(this.depSelect.val());
	},

	setDepartment: function(department_id) {

		if (department_id == this.departmentId) {
			// nochange
			//return;
		}

		this.clearAll();

		this.departmentId = department_id;
		var activeDepId = this.departmentId;

		if (!window.DESKPRO_CHAT_DISPLAY) {
			return;
		}
		if (!activeDepId || !window.DESKPRO_CHAT_DISPLAY[activeDepId]) {
			activeDepId = 0;
		}

		var depItems = window.DESKPRO_CHAT_DISPLAY[activeDepId];
		this.depItems = depItems;

		DP.console.log('depItems %o', depItems);

		$('.chat-display-field').hide();

		Array.each(depItems, function(item) {
			var itemId = this.getItemId(item);
			var itemEl = $('.' + itemId).closest('.chat-display-field');

			// Detach and re-attach to correct ordering
			itemEl.detach().appendTo('#fields_container');
			itemEl.show();
		}, this);

		if ($('#dp_chat_start_form .inner-wrapper').height() > $('#dp_chat_start_form').height()) {
			$('#dp_chat_start_footer').addClass('dp-relative');
		} else {
			$('#dp_chat_start_footer').removeClass('dp-relative');
		}
	},

	findItemForEl: function(el) {
		var fieldId = el.data('field-id');
		var theitem = null;
		Array.each(this.depItems, function(item) {
			if (item.id == fieldId) {
				theitem = item;
				return false;
			}
		});

		return theitem;
	},

	clearAll: function() {
		$('.chat-display-field').hide().removeClass('field-enabled with-criteria');
	},

	getItemId: function(item) {
		var itemId = item.field_type;
		if (item.field_id) {
			itemId += '_' + item.field_id;
		}

		return itemId;
	},

	setTypedMessage: function(msg) {
		if (typeof sessionStorage == 'undefined') {
			return;
		}
		if (!this.conversationId) {
			return;
		}

		if (!msg || msg === "") {
			if (typeof sessionStorage.dp_chat_id != 'undefined') {
				delete sessionStorage.dp_chat_id;
			}
			if (typeof sessionStorage.dp_chat_msg != 'undefined') {
				delete sessionStorage.dp_chat_msg;
			}
		} else {
			sessionStorage.dp_chat_id = this.conversationId;
			sessionStorage.dp_chat_msg = msg;
		}
	},

	restoreTypedMessage: function() {
		if (typeof sessionStorage == 'undefined') {
			return;
		}
		if (!this.conversationId) {
			return;
		}
		if (typeof sessionStorage.dp_chat_id == 'undefined') {
			return;
		}
		if (typeof sessionStorage.dp_chat_msg == 'undefined') {
			return;
		}
		if (parseInt(this.conversationId) != parseInt(sessionStorage.dp_chat_id)) {
			return;
		}

		return sessionStorage.dp_chat_msg;
	},

	initPage: function() {
		var self = this;

		var loginForm;

		if (loginForm = document.getElementById('overlay_login_form')) {
			loginForm = $(loginForm);

			loginForm.on('submit', function(ev) {
				ev.preventDefault();

				var postData = [];
				postData.push({
					name: 'email',
					value: loginForm.find('input[name="email"]').val()
				});
				postData.push({
					name: 'password',
					value: loginForm.find('input[name="password"]').val()
				});

				loginForm.find('.login-loading').show();
				loginForm.find('.submit-btn').hide();

				$.ajax({
					url: BASE_URL + 'login/inline-login',
					type: 'POST',
					data: postData,
					dataType: 'json',
					context: this,
					success: function() {
						location.reload(false);
					}
				})
			});
		}

		$('.auth-popup').click(function(ev) {
			ev.preventDefault();
			window.open($(this).attr('href'),'dpauth','width=600,height=400,location=0,menubar=0,scrollbars=0,status=0,toolbar=0,resizable=0');

			window.DP_LOGIN_NOTIFY = function() {
				window.location.reload(true);
			};
		});

		self.tellParent('started', [DESKPRO_SESSION_ID]);
		$('#dp_chatwin_min').on('click', function(ev) {
			ev.preventDefault();
			self.tellParent('hide', []);
		});

		$('.dp_chatwin_close').on('click', function(ev) {
			ev.preventDefault();
			if (self.hasEnded || !self.conversationId) {
				self.tellParent('destroy', []);
			} else {
				$('#dp_chat_end_confirm').show();
			}
		});

		$('#dp_chat_end_no').on('click', function(ev) {
			ev.preventDefault();
			$('#dp_chat_end_confirm').hide();
		});
		$('#dp_chat_end_yes').on('click', function(ev) {
			ev.preventDefault();
			$('#dp_chat_end_confirm').hide();
			self.chatEnded(true);
		});

		$('#dp_chat_start_go').on('click', function(ev) {
			ev.preventDefault();
			self.startChat();
		});

		$('#dp_chat_message_send').on('click', function(ev) {
			ev.preventDefault();
			self.sendTypedMessage();
		});

		$('#dp_chat_end_real').on('click', function(ev) {
			ev.preventDefault();
			self.endChatReal();
		});
		$('#dp_chat_end_real_alt').on('click', function(ev) {
			ev.preventDefault();
			$('#no_feedback').val('1');
			self.endChatReal();
		});
		$('#dp_chat_end_real_unassigned, #dp_chat_end_error').on('click', function(ev) {
			ev.preventDefault();
			self.tellParent('destroy', []);
			if (self.options.isWindowMode) {
				window.close();
			}
		});

		$('#dp_chat_message_input').on('keypress', function(ev) {
			if (ev.keyCode == 13 && !ev.metaKey) {
				ev.preventDefault();
				self.sendTypedMessage();
			} else {
				self.setTypedMessage($.trim($('#dp_chat_message_input').val()));
			}
		});

		$('#dp_chat_message_input').on('keyup', function(ev) {
			self.setTypedMessage($.trim($('#dp_chat_message_input').val()));
		});

		if (this.conversationId) {
			if (this.options.initialMessages) {
				$('#dp_chat_start').hide();
				this.foundAgent();
				$('#dp_chat_active').show();

				Array.each(this.options.initialMessages, function(m) {
					self.addMessageRow(m);
				});

			} else {
				$('#dp_chat_start').hide();
				this.startFindingAgent();
				$('#dp_chat_active').hide();
			}

			if (this.options.assignedAgentId) {
				this.chatAssigned(
					this.options.assignedAgentId,
					this.options.assignedAgentName,
					this.options.assignedAgentAvatar
				);
			}

			this.ajaxPoller.options.interval = 2000;
			this.ajaxPoller.disable = false;
			this.ajaxPoller.send();
		}

		if (this.options.autoStart) {
			this.startChat();
		}

		$('body').fileupload({
			url: this.options.uploadTo,
			dropZone: $(document),
			autoUpload: true,
			formData: {
				security_token: this.options.uploadSecurityToken,
				_rt: DP_REQUEST_TOKEN
			},
			start: function() {
				$('document').find('li.attach-error').remove();
			},
			uploadTemplateId: $('.dptpl-attach-upload').attr('id', Orb.getUniqueId('up')).attr('id'),
			downloadTemplateId: $('.dptpl-attach-download').attr('id', Orb.getUniqueId('down')).attr('id')
		});

		$('body').bind('fileuploaddone', function(ev, data) {
			if (data.result && data.result.length) {
				var items = data.result, x;
				for (x = 0; x < items.length; x++) {
					items[0].blob_id
					$.ajax({
						cache: false,
						url: BASE_URL + 'chat/send-attach/' + self.sessionCode + '?__sid=' + self.sessionCode,
						context: this,
						data: { send_blob_id: items[0].blob_id },
						dataType: 'json',
						success: function(data) {
							self.addMessageRow(data);
						}
					});
				}
			}

			$('#uploading_list').hide().find('> ul').empty();
		});
		$('body').bind('fileuploadstart', function() {
			$('#uploading_list').detach().appendTo($('#dp_chat_messages_pane')).show();
			$('#dp_chat_messages_pane').scrollTop(10000);
		});

		// Prevents default browser action of navigating to a dropped file
		// if a drop target isnt configured yet (ie no tab open to accept a file)
		$(document).bind('drop dragover', function (e) {
			e.preventDefault();
		});

		$(document).bind('dragover', function (e) {
			var timeout = window.dropZoneTimeout;
			if (!timeout) {
				$('body').addClass('file-drag-over');
			} else {
				clearTimeout(timeout);
			}

			window.dropZoneTimeout = setTimeout(function () {
				window.dropZoneTimeout = null;
				$('body').removeClass('file-drag-over');
			}, 100);
		});

		$('.cancel-and-newticket-trigger').attr('href', BASE_URL + 'new-ticket').attr('target', '_blank').on('click', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			window.open(BASE_URL + 'new-ticket?nochat', 'dp_newticket');
			self.endChatReal();
		});

		this._initFields();
	},

	startChat: function() {
		if ($('#dp_chat_start').find('input[name="email"]').val().length) {
			this.hasEmailAddress = true;
		} else {
			this.hasEmailAddress = false;
		}
		var data = $('#dp_chat_start').find('input, select, textarea').serializeArray();
		this.sendMessage('', data, { starting: true });

		this.startFindingAgent();
		$('#dp_chat_start').hide();
	},

	startFindingAgent: function() {
		console.log('ChatWin.startFindingAgent');
		var self = this;
		$('#dp_chat_finding_agent').show();
		$('#dp_chat_finding_agent_more').hide();
		this.findingAgentTimer = window.setTimeout(function() {
			console.log('ChatWin.startFindingAgent timeout');
			$('#dp_chat_finding_agent_more').show();
		}, this.options.findingAgentTimeout);

		this.findingAgentProgressTimeout = window.setInterval(function() {
			var bar = $('#dp_chat_finding_agent_loading').find('.dp-bar');
			if (!bar.data('cur-status')) bar.data('cur-status', 1);

			var setWidth = parseInt(bar.data('cur-status'));
			if (setWidth == 100) {
				setWidth = 1;
			} else {
				var pieces = Math.floor(100 / (self.options.findingAgentTimeout / 1000));
				setWidth += pieces;
				if (setWidth > 100) {
					setWidth = 100;
				}
			}

			bar.css('width', setWidth + '%');
			bar.data('cur-status', setWidth);
		}, 1000);
	},

	foundAgent: function() {
		console.log('ChatWin.foundAgent');
		$('#dp_chat_finding_agent').hide();
		$('#dp_chat_finding_agent_more').hide();
		if (this.findingAgentTimer) {
			window.clearTimeout(this.findingAgentTimer);
			this.findingAgentTimer = null;
		}
		if (this.findingAgentProgressTimeout) {
			window.clearTimeout(this.findingAgentProgressTimeout);
			this.findingAgentProgressTimeout = null;
		}
		$('#dp_chat_active').show();

		this.hasBeenAssigned = true;
	},

	chatError: function(error) {
		$('#dp_chat_start').hide();
		$('#dp_chat_finding_agent').hide();
		$('#dp_chat_active').hide();
		$('#dp_chat_error_text').text(error);
		$('#dp_chat_error').show();
	},

	sendTypedMessage: function() {
		this.setTypedMessage(false);
		var message = $('#dp_chat_message_input').val().trim();

		if (!message) {
			return;
		}

		$('#dp_chat_message_input').val('');
		this.sendMessage(message);
		$('#dp_chat_message_input').focus();
	},

	/**
	 * Sends a new chat message. The server decides if the chat should be new or not
	 *
	 * @param message
	 */
	sendMessage: function(message, data, meta) {

		console.log('ChatWin.sendMessage: %s', message);

		this.hasStarted = true;

		data = data || [];
		data.push({
			name: 'content',
			value: message
		});

		this.addMessageRow({
			author_type: 'user',
			content: message
		});

		var self = this;

		$.ajax({
			cache: false,
			url: BASE_URL + 'chat/send-message/' + this.sessionCode + '?__sid=' + this.sessionCode,
			context: this,
			type: 'POST',
			data: data,
			dataType: 'json',
			success: function(data) {
				if (!data.conversation_id && data.error) {
					self.ajaxPoller.disable = true;
					self.chatError(data.error);
					return;
				}

				if (data.conversation_id) {
					conversationId = data.conversation_id;
				}

				if (this.ajaxPoller.disable) {
					this.ajaxPoller.options.interval = 2000;
					this.ajaxPoller.disable = false;
					this.ajaxPoller.send();
				}
			}
		});
	},

	/**
	 * Add a message row
	 */
	addMessageRow: function(data) {

		var tpl = '';
		var message = data.content;
		if (!data.is_html) {
			message = Orb.escapeHtml(message);
			message = Orb.linkUrls(message);
		}

		var d = new Date();

		var a_p = "am";
		var curr_hour = d.getHours();
		if (d.getHours() > 12) {
			a_p = "pm";
		}
		if (curr_hour == 0) {
			curr_hour = 12;
		} else if (curr_hour > 12) {
			curr_hour = curr_hour - 12;
		}

		var curr_min = d.getMinutes();
		curr_min = curr_min + "";
		if (curr_min.length == 1) {
			curr_min = "0" + curr_min;
		}

		var time = curr_hour + ":" + curr_min + "" + a_p;

		//data.author_name, data.content, data.author_type, data.metadata

		if (data.author_type == 'user') {
			if (!message.trim().length) {
				return;
			}

			tpl = document.getElementById('dp_chat_tpl_user_message').innerHTML;
			tpl = tpl.replace(/%message%/g, message);

		} else if (data.author_type == 'agent') {

			tpl = document.getElementById('dp_chat_tpl_agent_message').innerHTML;
			tpl = tpl.replace(/%avatar_url_icon%/g, data.metadata.person_avatar_icon);
			tpl = tpl.replace(/%message%/g, message);
			tpl = tpl.replace(/%name%/g, data.author_name);

		} else if (data.author_type == 'sys') {

			if (data.content.indexOf('{"phrase_id":') === 0) {
				message = data.content;
				try {
					var data = $.parseJSON(message);
				} catch(e) {
					console.error(e);
					data = {};
				}

				if (data.phrase_id && data.phrase_id == 'message_assigned') {
					return;
				}

				if (data.phrase_id) {
					message = DeskPRO_Window.getTranslate().phrase('user.chat.' + data.phrase_id, data, true);
				} else {
					message = 'unknown phrase';
				}

				if (!data.is_html) {
					message = Orb.escapeHtml(message);
					message = Orb.linkUrls(message);
				}

				if (data.phrase_id == 'message_wait-timeout') {
					this.chatEndedWaitTimeout();
				}
			}

			tpl = document.getElementById('dp_chat_tpl_sys_message').innerHTML;
			tpl = tpl.replace(/%message%/g, message);

		}

		var row = $(tpl);
		row.find('a').on('click', function(ev) {
			ev.preventDefault();
			window.open($(this).attr('href'));
		});
		row.find('.time').text(time);

		row.appendTo($('#dp_chat_messages_pane'));
		$('#dp_chat_messages_pane').scrollTop(10000000);
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

		console.log('[ChatWin] comms.send: %s %o', messageId, data);

		return null;
	},

	handleIncomingMessage: function(data) {
		if (!data.metadata) {
			data.metadata = {};
		}
		if (data.is_html) {
			data.metadata.is_html = true;
		}

		console.log('DpChat:handleIncomingMessage: %o', data);

		this.addMessageRow(data);

		if (data.metadata.chat_unassigned) {
			this.chatAssigned(0, null, null);
		}
		if (data.metadata.chat_assigned) {
			this.chatAssigned(
				data.metadata.assigned_to,
				data.metadata.assigned_name,
				data.metadata.assigned_avatar
			);
		}

		if (data.message_id) {
			this.messageAck.push(data.message_id);
		}

		if (data.metadata.chat_ended) {
			this.chatEnded();
		} else {
			this.tellParent('show');
		}
	},

	chatEnded: function(userEnded, callback) {
		this.setTypedMessage(false);

		this.hasEnded = true;
		if (userEnded) {
			$.ajax({
				cache: false,
				url: BASE_URL + 'chat/chat-finished/' + this.sessionCode + '?conversation_id=' + this.conversationId + '&is_ajax=1&__sid=' + this.sessionCode,
				context: this,
				dataType: 'json',
				complete: callback || function() {}
			});
		}

		this.ajaxPoller.disable = true;
		this.ajaxPoller._clearDelays();

		$('#dp_chat_start').hide();
		$('#dp_chat_finding_agent').hide();
		$('#dp_chat_active').hide();


		if (!this.hasBeenAssigned) {
			$('#dp_chat_done_unassigned').show();
		} else {
			$('#dp_chat_done').show();
			if (this.hasEmailAddress) {
				$('#dp_chat_done').find('.form-row.email-field').hide();
			}
		}
	},

	chatEndedWaitTimeout: function() {
		this.ajaxPoller.disable = true;
		this.ajaxPoller._clearDelays();

		$('#dp_chat_start').hide();
		$('#dp_chat_finding_agent').hide();
		$('#dp_chat_active').hide();
		$('#dp_chat_done_unassigned').show();
	},

	endChatReal: function(callback) {
		var self = this;
		var data = $('#dp_chat_done').find('input, select, textarea').serializeArray();

		$('#send_feedback_controls').hide();
		$('#send_feedback_loading').show();
		$.ajax({
			cache: false,
			url: BASE_URL + 'chat/chat-finished-feedback/' + this.sessionCode + '?conversation_id=' + this.conversationId + '&is_ajax=1&__sid=' + this.sessionCode,
			type: 'POST',
			data: data,
			context: this,
			dataType: 'json',
			complete: function() {
				if (callback) {
					callback();
				}
				self.tellParent('destroy', []);
				if (self.options.isWindowMode) {
					window.close();
				}
			}
		});
	},

	chatAssigned: function(agentId, name, avatar) {
		if (parseInt(agentId)) {
			$('#dp_chat_start').hide();
			this.foundAgent();
			$('#dp_chat_active').show();

			var tpl = document.getElementById('dp_chat_tpl_agent_header').innerHTML;
			tpl = tpl.replace(/%avatar_url%/g, avatar.replace(/s=40/g, 's=30').replace(/s=16/g, 's=30'));
			tpl = tpl.replace(/%name%/g, Orb.escapeHtml(name));

			$('#dpchat_without_agent').hide();
			$('#dpchat_with_agent').show().html(tpl);

		} else {
			$('#dpchat_without_agent').show();
			$('#dpchat_with_agent').hide().html('');
		}
	},

	//#################################################################
	//# Simple implementations of message broker and poller
	//#################################################################

	_initSysObjects: function() {
		var self = this;

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
		this.mesageBroker = messageBroker;

		messageBroker.addMessageListener('newmessage', this.handleIncomingMessage.bind(this));

		var ajaxPoller = {
			options: {
				interval: 2000,
				initialDelay: 1500
			},
			filterdData: [],
			disable: true,
			maxDelayTimers: [],

			init: function() {
				this.autoSendTimeout = this.send.delay(this.options.initialDelay, this);
			},

			addData: function(data, name, options) {
				name = name || 'default';
				options = options || {};

				if (options.addedTime === undefined) {
					options.addedTime = new Date();
				}

				if (options.maxDelay) {
					(function() {
						this.send();
					}).delay(options.maxDelay, this);
				}

				this.filterdData.push([name, data, options]);
			},

			send: function() {

				this._clearDelays();

				if (this.disable) {
					this.autoSendTimeout = this.send.delay(this.options.interval, this);
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
					value: self.lastMessageId
				});
				send_data.push({
					name: 'conversation_id',
					value: self.conversationId
				});
				send_data.push({
					name: '__sid',
					value: self.sessionCode
				});

				var typing = $.trim($('#dp_chat_message_input').val());
				if (typing) {
					this.sentLoadingIndicator = true;
					send_data.push({
						name: 'user_typing',
						value: typing
					});
				} else if (this.sentLoadingIndicator) {
					this.sentLoadingIndicator = false;
					send_data.push({
						name: 'user_typing',
						value: '__dpnone__'
					});
				}

				if (self.messageAck.length) {
					for (var i = 0; i < self.messageAck.length; i++) {
						send_data.push({
							name: 'ack_messages[]',
							value: self.messageAck[i]
						});
					}
					self.messageAck = [];
				}

				//------------------------------
				// Send data
				//------------------------------

				$.ajax({
					cache: false,
					url: BASE_URL + 'chat/poll/' + self.sessionCode,
					context: this,
					data: send_data,
					dataType: 'json',
					complete: function() {
						// Start auto timer
						this.autoSendTimeout = this.send.delay(this.options.interval, this);
					},
					success: function (data) {

						if (data.conversation_id) {
							self.conversationId = data.conversation_id;
						}
						if (data.last_id) {
							self.lastMessageId = data.last_id;
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

					console.log('ChatWin Poll Data: %o', data);

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

		this.ajaxPoller = ajaxPoller;

		var restoreMessage = this.restoreTypedMessage();
		if (restoreMessage) {
			window.setTimeout(function() {
				$('#dp_chat_message_input').val(restoreMessage);
			}, 350);
		}
	}
});
