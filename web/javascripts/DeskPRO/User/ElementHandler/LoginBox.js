Orb.createNamespace('DeskPRO.User.ElementHandler');

DeskPRO.User.ElementHandler.LoginBox = new Orb.Class({

	Extends: DeskPRO.User.ElementHandler.ElementHandlerAbstract,

	init: function() {
		this.loginBox = $('#dp_login_box');

		this.loginSection = $('.dp-login-section', this.el);
		this.resetSection = $('.dp-reset-section', this.el);

		this._initResetSection();
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
			url: BASE_URL + 'login/reset-password/send.json',
			type: 'POST',
			data: {
				email: $('#dp_login_email').val()
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
