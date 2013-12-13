Orb.createNamespace('DeskPRO.User.WebsiteWidget');

DeskPRO.User.WebsiteWidget.OverlayWin = new Orb.Class({
	Implements: [Orb.Util.Options, Orb.Util.Events],

	initialize: function(options) {

		this.options = {
			parentUrl: null
		};
		this.setOptions(options);

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
			recieveCallback: null,
			resetHashTimeout: null,
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
						}, 60);
					}
				}
			}
		};

		if (this.options.parentUrl) {
			this.parentUrl = this.options.parentUrl;
		} else {
			var hash = window.location.hash + '';
			this.parentUrl = decodeURIComponent(hash.replace(/^#/, ''));
		}
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

		$(".widget-deskpro select:not('.no-uniform'),.file").uniform();

		$('.with-handler[data-element-handler]').each(function() {
			var el = $(this);
			var className = el.data('element-handler');
			var classObj = Orb.getNamespacedObject(className);

			if (!classObj) {
				DP.console.error("Unknown portal handler `%s` on element %o", className, this);
				return;
			}

			if (!el.attr('id')) {
				el.attr('id', Orb.getUniqueId('portal_'));
			}

			var obj = new classObj({ el: el });
		});

		this.activeTabBody = null;

		this.winNav = new DeskPRO.UI.SimpleTabs({
			triggerElements: $('#dp_overlay_navtabs').find('> a'),
			activeClassname: 'active',
			onTabSwitch: function(ev) {
				self.activeTabBody = ev.tabContent;
			}
		});

		this._initSearch();
		this._initNewTicket();
		this._initNewFeedback();
		this._initNewChat();

		$('form.with-form-validator').each(function() {
			var v = new DeskPRO.Form.FormValidator($(this));
			$(this).data('form-validator-inst', v);
		});

		$(document).on('click', '.view-item', function(ev) {
			ev.preventDefault();

			var origUrl = $(this).get(0).href;
			if (origUrl.indexOf('gourl') !== -1) {
				var url = origUrl + '%3F_partial=overlayWidget';
			} else {
				var url = Orb.appendQueryData(origUrl, '_partial', 'overlayWidget');
			}
			url = url.replace(/:/g, '__DP_COL__');

			self.tellParent('showContentPage', [url]);
		});

		// Sync name and email fields, and save them to cookies for next time too
		var names = $('input.name-field');
		var emails = $('input.email-field');

		if ($.cookie('dp_uname')) {
			names.val($.cookie('dp_uname'));
		}
		if ($.cookie('dp_uemail')) {
			names.val($.cookie('dp_uemail'));
		}

		names.on('change', function() {
			var val = $(this).val().trim();
			names.val(val);

			$.cookie('dp_uname', val);
		});

		emails.on('change', function() {
			var val = $(this).val().trim();
			emails.val(val);

			$.cookie('dp_uemail', val);
		});

		$('.auth-popup').click(function(ev) {
			ev.preventDefault();
			window.open($(this).attr('href'),'dpauth','width=600,height=400,location=0,menubar=0,scrollbars=0,status=0,toolbar=0,resizable=0');

			window.DP_LOGIN_NOTIFY = function() {
				window.location.href = window.location.href;
			};
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

		$('#cancel_related').on('click', function() { self.disableRelatedMode(); });

		$('input, textarea, select').on('change keyup', function() {
			$(this).parent().removeClass('error');
			$(this).closest('.fields-row').removeClass('error');
		});

		var searchCollect = $('.search-collect');
		this.searchCollect = searchCollect;
		var collectToucher = new DeskPRO.TouchCaller({
			timeout: 300,
			callback: function() {
				self.enableRelatedMode();
			}
		});
		searchCollect.on('keyup', function() {
			collectToucher.touch();
		});

		this.tellParent('ready');

		var lastHeight, currentHeight;
		lastHeight = $('#widget_deskpro').height();
		self.tellParent('requestHeight', [lastHeight]);

		window.setInterval(function() {
			currentHeight = $('div.tab.active').height();
			currentHeight += 20 + 70 + 74 + 11 + 11 + 5 + 21;

			if (lastHeight != currentHeight) {
				self.tellParent('requestHeight', [currentHeight]);
			}
			lastHeight = currentHeight;
		}, 80);
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

		console.log('[OverlayWin] comms.send: %s %o', messageId, data);

		return null;
	},


	//##################################################################################################################
	//# Search Bar
	//##################################################################################################################

	setSearchMode: function(mode) {
		this.searchMode = mode;
	},

	enableRelatedMode: function() {
		this.searchMode = 'related';
		$('#left_pane').addClass('showing-related');
		this.searchBox.val('');
		this.updateResults();
	},

	disableRelatedMode: function() {
		this.searchMode = 'latest';
		$('#left_pane').removeClass('showing-related');
		this.updateResults();
	},

	_initSearch: function() {
		var self = this;
		this.searchBox = $('#search_box');

		$('#search_box_go').on('click', function() {
			self.updateResults();
		});
		$('#search_box_clear').on('click', function() {
			self.clearSearch();
			self.searchBox.val('').focus();
		});

		var touchCaller = new DeskPRO.TouchCaller({
			timeout: 300,
			callback: function() {
				self.updateResults();
				$('#search_box_clear').show();
			}
		});

		this.searchBox.on('keyup', function(ev) {
			self.setSearchMode('search');
			if (!$(this).val().trim().length) {
				self.clearSearch();
				self.searchBox.val('').focus();
			} else {
				touchCaller.touch();
			}
		}).on('keydown', function(ev) {
			if (ev.keyCode == 13 && !ev.metaKey) {
				ev.preventDefault();//dont enter enter key
				self.updateResults();
				return;
			}
		});
	},

	clearSearch: function() {
		$('#left_pane').removeClass('showing-related');
		$('#search_content_list').empty().hide();
		$('#new_content_list').show();
		$('#search_box_clear').hide();

		$('#no_results').hide();

		if (this.searchAjax) {
			this.searchAjax.abort();
			this.searchAjax = null;
		}
	},

	updateResults: function() {

		if (this.searchMode == 'search' || this.searchMode == 'related') {
			$('#new_content_list').hide();
			$('#search_content_list').show();
		} else {
			$('#new_content_list').show();
			$('#search_content_list').hide();
			$('#search_loading').hide();
			$('#left_pane').removeClass('loading');
			return;
		}

		if (this.searchMode == 'related') {
			$('#left_pane').addClass('showing-related');
		} else {
			$('#left_pane').removeClass('showing-related');
		}

		var self = this;
		var q = this.searchBox.val().trim();

		if (this.searchMode == 'related') {
			q = [];
			this.searchCollect.each(function() {
				q.push($(this).val());
			});

			q = q.join(' ').trim();
		}

		if (!q.length) {
			this.clearSearch();
			return;
		}

		if (this.searchAjax) {
			this.searchAjax.abort();
			this.searchAjax = null;
		}

		if (this.searchMode == 'related') {
			var hasResults = $('#search_content_list').find('li').length;
			if (!hasResults) {
				$('#left_pane').addClass('loading');
				$('#search_loading').show();
			}
		} else {
			$('#left_pane').addClass('loading');
			$('#search_loading').show();
		}

		this.searchAjax = $.ajax({
			url: BASE_URL + 'search/omnisearch/' + encodeURI(q),
			dataType: 'html',
			context: this,
			complete: function() {
				$('#left_pane').removeClass('loading');
				$('#search_loading').hide();
			},
			success: function(html) {
				var ul = $(html);
				ul.find('a').addClass('view-item');
				var lis = ul.find('> li');

				$('#search_content_list').empty().append(lis).show();

				if (!$('#search_content_list li').length) {
					$('#no_results').show();
				} else {
					$('#no_results').hide();
				}
			}
		});
	},

	//##################################################################################################################
	//# New Ticket
	//##################################################################################################################

	_initNewTicket: function() {
		var self = this;
		this.newTicketForm = $('#dp_newticket_form');
		this.newTicketForm.find('[required]').prop('required', false);

		this.newTicketForm.on('submit', function(ev) {
			ev.preventDefault();
			var data = self.newTicketForm.find('select, input, textarea').serializeArray();

			self.newTicketForm.addClass('dp-mark-loading');
			$('.error').removeClass('error');
			$.ajax({
				url: $(this).attr('action'),
				type: 'POST',
				data: data,
				dataType: 'json',
				complete: function() {
					self.newTicketForm.removeClass('dp-mark-loading');
				},
				success: function(data) {
					if (data.is_error) {
						Object.each(data.errors, function(v,k) {
							var find = '.dp-form-row-' + k.replace(/\./g, '_');
							console.log(find);
							self.newTicketForm.find(find).addClass('error');
						});

						return;
					}

					$('#dp_newticket_done').show();
					self.newTicketForm.hide();
				}
			});
		});
	},


	//##################################################################################################################
	//# New Feedback
	//##################################################################################################################

	_initNewFeedback: function() {
		var self = this;
		this.newFeedbackForm = $('#dp_newfeedback_form');
		this.newFeedbackForm.find('[required]').prop('required', false);

		this.feedbackCatSelect = this.newFeedbackForm.find('select.category_id');
		this.feedbackCatId = 0;

		this.feedbackCatSelect.on('change', function() {
			self.handleFeedbackCatChange();
		});
		this.feedbackCatSelect.data('original-name', this.feedbackCatSelect.attr('name'));

		this.newFeedbackForm.on('submit', function(ev) {
			ev.preventDefault();
			var data = self.newFeedbackForm.find('select, input, textarea').serializeArray();

			$('.error').removeClass('error');
			self.newFeedbackForm.addClass('dp-mark-loading');
			$.ajax({
				url: $(this).attr('action'),
				type: 'POST',
				data: data,
				dataType: 'json',
				complete: function() {
					self.newFeedbackForm.removeClass('dp-mark-loading');
				},
				success: function(data) {
					if (data.is_error) {
						Object.each(data.errors, function(v,k) {
							var find = '.dp-form-row-' + k.replace(/\./g, '_');
							self.newFeedbackForm.find(find).addClass('error');
						});

						return;
					}

					self.newFeedbackForm.hide();
					$('#dp_newfeedback_done').show();
				}
			});
		});

		this.handleFeedbackCatChange();
	},

	handleFeedbackCatChange: function() {
		var wrapper = this.newFeedbackForm.find('.feedbackCat_id_wrapper');

		var allSubs = $('.dp-sub-options', wrapper).hide();
		$('select', allSubs).attr('name', '');

		var feedbackCatId = this.feedbackCatSelect.val();
		var sub = $('.sub-options-' + feedbackCatId, wrapper);

		if (!sub.length) {
			this.feedbackCatSelect.attr('name', this.feedbackCatSelect.data('original-name'));
			return;
		} else {
			this.feedbackCatSelect.attr('name', '');
			$('select', sub).attr('name', this.feedbackCatSelect.data('original-name'));
		}

		sub.show();
	},

	//##################################################################################################################
	//# New Chat
	//##################################################################################################################

	_initNewChat: function() {
		var self = this;
		this.newChatForm = $('#dp_newchat_form');
		this.newChatForm.find('[required]').prop('required', false);

		this.newChatForm.on('submit', function(ev) {
			ev.preventDefault();
			var data = [
				(self.newChatForm.find('input[name="name"]').val() || '').replace(/:/g, '__DP_COL__'),
				(self.newChatForm.find('input[name="email"]').val() || '').replace(/:/g, '__DP_COL__'),
				(self.newChatForm.find('select[name="department_id"]').val() || '').replace(/:/g, '__DP_COL__')
			];

			self.tellParent('requestChat', data);

			$('#dp_newchat_form').hide();
			$('#dp_newchat_done').show();
		});
	}
});
