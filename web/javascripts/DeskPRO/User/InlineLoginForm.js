Orb.createNamespace('DeskPRO.User.ElementHandler');

DeskPRO.User.InlineLoginForm = new Orb.Class({
	Implements: [Orb.Util.Options, Orb.Util.Events],

	initialize: function(options) {
		var self = this;

		this.options = {
			emailSel: '#dp_inline_login_email',
			passwordSel: '#dp_inline_login_pass',
			context: null
		};
		this.setOptions(options);

		this.context = this.options.context || document;

		this._initLoginForm($('.dp-inline-login', this.context));
	},

	_initLoginForm: function(wrapper) {
		var self = this;

		this.el = wrapper;
		this.loginWrapper    = wrapper;
		this.passwordRow     = $('.dp-inline-login-pass', wrapper);
		this.nonloginWrapper = $('.dp-inline-non-login', this.context);
		this.loginBtn        = $('.dp-login-trigger', wrapper);

		this.loginSection = $('.dp-login-section', this.el);
		this.resetSection = $('.dp-reset-section', this.el);

		$('.dp-inline-login-open', wrapper).on('click', (function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			if (this.loginWrapper.is('.open')) {
				this.closeLogin();
			} else {
				this.openLogin();
			}
		}).bind(this));

		this.loginBtn.on('click', (function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			this.processLogin();
		}).bind(this));

		$(this.options.passwordSel, wrapper).on('keypress', function(ev) {
			if (ev.keyCode == 13) {
				ev.preventDefault();

				if (self.isOpen()) {
					self.processLogin();
				}
			}
		});
		$(this.options.emailSel, wrapper).on('keypress', function(ev) {
			if (ev.keyCode == 13) {
				ev.preventDefault();

				if (self.isOpen()) {
					self.processLogin();
				}
			}
		});

		this._initResetSection();
	},

	isOpen: function() {
		return this.loginWrapper.is('.open');
	},

	openLogin: function() {
		this.loginWrapper.addClass('open');
		this.passwordRow.slideDown('fast');
		this.nonloginWrapper.animate({ opacity: '0.4', duration: 'fast' });
	},

	closeLogin: function() {
		this.passwordRow.slideUp('fast', (function() {
			this.loginWrapper.removeClass('open');
		}).bind(this));
		this.nonloginWrapper.animate({ opacity: '1', duration: 'fast' });
	},

	processLogin: function() {
		var postData = [];
		postData.push({
			name: 'email',
			value: $(this.options.emailSel, this.context).val()
		});
		postData.push({
			name: 'password',
			value: $(this.options.passwordSel, this.context).val()
		});

		if (this.loginBtn.is('.mode-advanced')) {
			postData.push({
				name: 'mode',
				value: 'advanced'
			});
		}

		$.ajax({
			url: BASE_URL + 'login/inline-login',
			type: 'POST',
			data: postData,
			dataType: 'json',
			context: this,
			success: function(data) {
				var newEl = $(data.html);
				if (data.person_id) {
					DeskPRO_Window.initFeatures(newEl);
					$('#dp_inline_login_row').replaceWith(newEl);
					this.nonloginWrapper.css({ opacity: '1'});
				} else {
					$('#dp_inline_login_row').replaceWith(newEl);
					$('.dp-inline-login-pass', newEl).show();
				}

				if (data.sections_replace) {
					Object.each(data.sections_replace, function(html, id) {
						$('#' + id).empty().replaceWith(html);
					});
				}

				this._initLoginForm(newEl);

				this.fireEvent('success', [data, this]);
			}
		})
	},

	//#########################################################################
	//# Reset Stuff
	//#########################################################################

	_initResetSection: function() {
		$('.forgot', this.el).on('click', (function(ev) {
			ev.preventDefault();
			this.showReset();
		}).bind(this));

		$('.back', this.resetSection).on('click', (function(ev) {
			this.hideReset();
		}).bind(this));

		$('.dp-do-send', this.resetSection).on('click', (function(ev) {
			ev.preventDefault();
			this.sendReset();
		}).bind(this));
	},

	sendReset: function() {

		this.resetSection.addClass('loading');

		$.ajax({
			url: BASE_URL + 'login/reset-password/send',
			type: 'POST',
			data: {
				email: $(this.options.emailSel, this.el).val()
			},
			dataType: 'json',
			context: this,
			success: function() {
				this.resetSection.removeClass('loading');

				var descEl = $('.dp-reset-desc', this.resetSection);
				var sentEl = $('.dp-reset-sent', this.resetSection);

				descEl.slideUp('fast', function() {
					sentEl.slideDown();
				});
			}
		});
	},

	showReset: function() {
		this.loginSection.slideUp('fast', (function() {
			this.resetSection.slideDown('fast');
		}).bind(this));
	},

	hideReset: function(quick) {
		if (quick) {
			this.resetSection.hide();
			this.loginSection.show();
			$('.dp-reset-desc', this.resetSection).show();
			$('.dp-reset-sent', this.resetSection).hide();
		} else {
			this.resetSection.slideUp('fast', (function() {
				this.loginSection.slideDown('fast');

				// Also reset view on others
				$('.dp-reset-desc', this.resetSection).show();
				$('.dp-reset-sent', this.resetSection).hide();
			}).bind(this));
		}
	}
});
