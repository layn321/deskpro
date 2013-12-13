Orb.createNamespace('DeskPRO.User.ElementHandler');

DeskPRO.User.ElementHandler.InlineEmailManage = new Orb.Class({

	Extends: DeskPRO.User.ElementHandler.ElementHandlerAbstract,

	init: function() {
		var self = this;
		this.emailField = $('.dp-email-field', this.el);
		this.newEmailField = $('.dp_inline_email_new', this.el);
		this.emailList = $('.dp-email-manage-list', this.el);
		this.controlsEl = $('.dp-email-manage-controls', this.el);
		$('input[name="dp_inline_email_choice"]', this.controlsEl).on('click', function() {
			var val = $(this).val();
			if (val == 'NEW') {
				self.setNewMode();
			} else {
				self.emailField.val(val);
			}
		});

		this.emailError = $('.error-message', this.el);

		this.newEmailField.on('keypress', function(ev) {
			if (ev.keyCode == 13) {
				ev.preventDefault();
				ev.stopPropagation();
			}
		});
		this.newEmailField.on('keyup', function(ev) {
			if (self.mode == 'new') {
				self.emailField.val(self.newEmailField.val());
			}
			self.newEmailField.removeClass('error');
		});

		this.mode = 'normal';

		if ($('input[name="dp_inline_email_choice_radio"]:checked').val() == 'NEW') {
			this.setNewMode();
		}

		$('input[name="dp_inline_email_choice_radio"]').not('[value="NEW"]').on('click', function() {
			self.setNormalMode();
			self.emailField.val($(this).val());
		});

		$('input[name="dp_inline_email_choice_radio"][value="NEW"]').on('click', function() {
			self.emailField.val(self.newEmailField.val());
			self.setNewMode();
		});

		var changeEmail = $('.change-email', this.el).on('click', function(ev) {
			ev.preventDefault();
			self.controlsEl.slideDown('fast');
			changeEmail.hide();
			changeEmailClose.show();
		});
		var changeEmailClose =  $('.change-email-close', this.el).on('click', function(ev) {
			ev.preventDefault();
			if ($('input[name="dp_inline_email_choice_radio"][value="NEW"]').is(':checked')) {
				var email = self.newEmailField.val();
				if (!email || email.indexOf('@') === -1) {
					self.newEmailField.addClass('error').focus();
					return;
				}
			}

			self.controlsEl.slideUp('fast');
			changeEmail.show();
			changeEmailClose.hide();
		});
	},

	setNormalMode: function() {
		this.mode = 'normal';
	},

	setNewMode: function() {
		this.mode = 'new';
		this.emailField.val(this.newEmailField.val());
	}
});
