Orb.createNamespace('DeskPRO.Agent.PageFragment.SettingsPage');

DeskPRO.Agent.PageFragment.SettingsPage.Profile = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'settings_profile';
	},

	initPage: function(el) {
		var self = this;
		this.el = el;

		var form = $('form', this.el);

		if (this.el.find('.dp-form-row.new-picture')[0]) {
			DeskPRO_Window.util.fileupload(this.el.find('.dp-form-row.new-picture'));
			this.el.find('.dp-form-row.new-picture').bind('fileuploadadd', function() {
				$('.files', form).empty();
			});
		}

		var startEmail = $('#settings_profile_email').val();

		var changePass = false;
		var changeEmail = false;

		var verifyPasswords = function() {
			var pass1 = $('input.password1', form);
			var pass2 = $('input.password2', form);

			// Form might not have password fields if agent is from a usersource
			if (!pass1[0]) {
				changePass = false;
				return true;
			}

			if (pass1.val().length) {
				changePass = true;
				if (pass1.val() != pass2.val()) {
					DeskPRO_Window.showAlert('Please enter the same password into both password fields', 'error');
					return false;
				}
			} else {
				changePass = false;
			}
			return true;
		};

		var checkEmailChange = function() {
			if ($('#settings_profile_email').val() != startEmail) {
				changeEmail = true;
			} else {
				changeEmail = false;
			}
		};

		var passCode = null;

		form.on('submit', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			if (!verifyPasswords()) {
				return;
			}
			checkEmailChange();

			if ((changePass || changeEmail) && !passCode) {
				$('#password_confirm').trigger('dp_open', {
					explain: "Confirm these changes to your profile by entering your current password.",
					success: function(code) {
						passCode = code;
						form.submit();
					}
				});
				return;
			}

			var data = $(this).serializeArray();
			if (passCode) {
				data.push({
					name: 'authcode',
					value: passCode
				});
			}
			$.ajax({
				url: $(this).attr('action'),
				type: 'POST',
				data: data,
				dataType: 'json',
				complete: function() {
					changePass = false;
					changeEmail = false;
					passCode = null;
					$('input.password1', form).val('');
					$('input.password2', form).val('');
				},
				success: function(data) {

					if (data.form_errors) {

						$('#agent_settings_win_errors').find('li').hide();
						Array.each(data.form_errors, function (code) {
							var classname = code.replace(/\./g, '_');
							$('#agent_settings_win_errors').find('li.' + classname).show();
						});
						$('#agent_settings_win_errors').show();

						return;
					}

					$('#agent_settings_win_errors').hide();

					if (data.login) {
						DeskPRO_Window.util.reloadInterface();
						return;
					}

					self.settingsWindow.showSavePuff();
					startEmail = $('#settings_profile_email').val();
					self.settingsWindow.reloadInterface = true;
					self.settingsWindow.reloadTab('profile');
				}
			});
		});

		if (window.webkitNotifications) {
			var notificationsRow = el.find('.dp-desktop-notifications');
			notificationsRow.show();

			var enableButton = notificationsRow.find('.enable-desktop-notifications');

			var permissionCallback = function() {
				var permission = window.webkitNotifications.checkPermission();

				if (permission == 0) {
					// granted
					enableButton.hide();
					notificationsRow.find('.dp-desktop-notifications-enabled').show();
					notificationsRow.find('.dp-desktop-notifications-disabled').hide();
				} else if (permission == 1) {
					// no action
					enableButton.show();
					notificationsRow.find('.dp-desktop-notifications-enabled').hide();
					notificationsRow.find('.dp-desktop-notifications-disabled').hide();
				} else {
					// explicitly denied
					enableButton.hide();
					notificationsRow.find('.dp-desktop-notifications-enabled').hide();
					notificationsRow.find('.dp-desktop-notifications-disabled').show();
				}
			};

			permissionCallback();

			this.addEvent('updateUi', function() {
				permissionCallback();
			});

			enableButton.click(function(e) {
				e.preventDefault();
				window.webkitNotifications.requestPermission(permissionCallback);
			});

			notificationsRow.find('.generate-test-notification').click(function(e){
				e.preventDefault();

				if (window.webkitNotifications.checkPermission() != 0) {
					return;
				}

				var notification = window.webkitNotifications.createNotification(
					'', 'DeskPRO', 'This is a test DeskPRO desktop notification.'
				);
				notification.ondisplay = function() {
					setTimeout(function() {
						notification.cancel();
					}, 60 * 1000);
				};
				notification.show();
			});
		}

		// Email Addresses
		el.find('.more_emails_empty').find('a').on('click', function(ev) {
			Orb.cancelEvent(ev);
			el.find('.more_emails_empty').hide();
			el.find('.more_emails').show();
		});

		var moreEmails  = el.find('.more_emails');
		var addEmailTxt = el.find('.more_emails_txt');

		el.find('.more_emails_trigger').on('click', function(ev) {
			Orb.cancelEvent(ev);
			var val = $.trim(addEmailTxt.val());

			if (!val.indexOf('@')) {
				alert('Please enter a valid email address');
				return;
			}

			var li = $('<li class="is-new">&bull; <input type="hidden" name="new_emails[]" /><span></span>&nbsp;&nbsp;&nbsp;<i class="icon-trash remove-trigger" title="Remove email"></i></li>');
			li.addClass('is-new');
			li.find('input').val(val);
			li.find('span').text(val);

			moreEmails.find('ul').prepend(li);

			addEmailTxt.val('');
		});

		moreEmails.on('click', '.remove-trigger', function(ev) {
			Orb.cancelEvent(ev);

			var li = $(this).closest('li');
			if (li.hasClass('is-new')) {
				li.remove();
			} else {
				var input = $('<input type="hidden" name="remove_emails[]" />');
				input.val(li.data('email-id'));
				moreEmails.append(input);
				li.remove();
			}
		});
	}
});
