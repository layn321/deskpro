Orb.createNamespace('DeskPRO.Admin.ElementHandler');

DeskPRO.Admin.ElementHandler.EditEmailTransportPage = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var self = this;

		this.el.addClass('edit-email-transport-page');

		$(document).on('click', ':radio.transport-type-backup', function() {
			if ($(this).is('.none')) {
				$('.test-account-settings.backup', this.el).hide();
			} else {
				$('.test-account-settings.backup', this.el).show();
			}
		});

		this.el.on('click', '.test-account-settings', function() {

			if ($('#default_from_email')[0] && $('#default_from_email').val().indexOf('@') === -1) {
				$('#default_from_email').parent().find('.dp-error').show();
				return;
			}

			self.mode = '';
			if ($(this).is('.backup')) {
				self.mode = 'backup';
			}

			self.overlay.open();
		});

		$('#transport_gmail_options_username').on('keyup', function(ev) {
			if ($('input[name="transport[transport_type]"]:checked').val() == 'gmail') {
				$('#default_from_email').val($(this).val());
			}
		});
		$('#default_from_email').on('keyup', function(ev) {
			if ($('input[name="transport[transport_type]"]:checked').val() == 'gmail') {
				$('#transport_gmail_options_username').val($(this).val());
			}
		});

		var overlayId = 'test_settings_overlay';
		if (this.el.data('overlay-id')) {
			overlayId = this.el.data('overlay-id');
		}
		this.overlay = new DeskPRO.UI.Overlay({
			contentElement: $('#' + overlayId),
			onBeforeOverlayOpened: function() {
				var el = self.overlay.getElement();

				$('.result', el).hide().removeClass('loading');
				$('.success', el).hide();
				$('.error', el).hide();

				if ($('#default_from_email')[0]) {
					$('#test_send_from_tr').val($('#default_from_email').val());
					$('#test_send_from_tr').on('keyup', function(ev) {
						$('#default_from_email').val($(this).val());
					});
				}

				if ($('#transport_form').length) {
					var postData = $('#transport_form').serializeArray();
				} else {
					var postData = self.el.find('input, select, textarea').serializeArray();
				}
				if (self.mode == 'backup') {
					postData.push({name: 'backup', value: 1});
				}

				var type = $('.email-address-type:checked').val();
				var setmail = false;
				if (type == 'exact') {
					setmail = $('.email-address-pattern').val();
				} else if (type == 'domain') {
					setmail = 'test@' + $('.email-domain-pattern').val();
				} else if ($('#default_from_email').length) {
					setmail = $('#default_from_email').val();
				}

				if ($('#gateway_address').length) {
					setmail = $('#gateway_address').val();
				}

				if (setmail) {
					$('#test_send_from_tr').val(setmail);
				}

				self.testPostData = postData;
			}
		});

		$('button.test-trigger', '#'+overlayId).on('click', function() {
			var el = $('#'+overlayId);
			$('.result', el).show().addClass('loading');

			var postData = self.testPostData;
			postData.push({name: 'send_to', value: $('input[name="send_to"]', el).val()});
			postData.push({name: 'send_from', value: $('input[name="send_from"]', el).val()});

			$.ajax({
				url: $(this).data('url'),
				type: 'POST',
				data: postData,
				dataType: 'json',
				complete: function() {
					$('.result', el).removeClass('loading');
				},
				error: function() {
					$('.success', el).hide();
					$('.error', el).show();
					$('.error-msg .error-msg-text', el).text('We could not connect to the SMTP server. Check the server hostname and port are set correctly and that you have enabled any required security options.');
					$('.error-msg .error-msg-log', el).text('timeout');
				},
				success: function(data) {
					if (data.success) {
						$('.error', el).hide();
						$('.success', el).show();
					} else {
						$('.success', el).hide();
						$('.error', el).show();
						$('.error-msg .error-msg-text', el).text('[' + data.error_code + '] ' + data.error_message);
						$('.error-msg .error-msg-log', el).text(data.log);
					}
				}
			});
		});

		$('#backup_toggle').on('change', function() {
			var on = $(this).is(':checked');

			if (on) {
				$('#backup_form_php').attr('checked', 'checked').click();
				var group = $('#backup_form_php').closest('.dp-input-group');
				group.closest('.dp-form-row-group').find('.on.dp-input-group').removeClass('on').find('.dp-group-options').hide();
				group.addClass('on').find('.dp-group-options').show();

				$('#backup_form').slideDown();
			} else {
				$('#backup_form :radio').prop('checked', false);
				$('#backup_form_none_radio').prop('checked', true);
				$('#backup_form').slideUp();
			}
		});

		var errorBox = $('#page_error_box');
		if (errorBox[0]) {
			errorBox.hide();
			$('#transport_form').on('submit', function (ev) {
				var errors = self.getFormErrors();
				if (errors && errors.length) {

					console.log('Errors: %o', errors);

					errorBox.find('li.error-item').hide();

					Array.each(errors, function(code) {
						errorBox.find('li.error_' + code).show();
					});

					errorBox.show();

					ev.preventDefault();
				}
			});
		}
	},

	getFormErrors: function() {
		var errors = this.getGeneralFormErrors();

		var type = this.el.find(':radio[name="transport[transport_type]"]:checked').val();
		if (type == 'smtp') {
			errors.combine(this.getSmtpFormErrors());
		} else if (type == 'gmail') {
			errors.combine(this.getGappsFormErrors());
		}

		return errors;
	},

	getSmtpFormErrors: function() {
		var host = $('#transport_smtp_options_host').val();
		var port = $('#transport_smtp_options_port').val();
		var require_auth = this.el.find('.smtp-requires-auth-check').is(':checked');
		var username = $('#transport_smtp_options_username').val();

		var errors = [];
		if (!host.length) {
			errors.push('smtp_host');
		}
		if (!parseInt(port) || !parseInt(port)) {
			errors.push('smtp_port');
		}

		if (require_auth) {
			if (!username.length) {
				errors.push('smtp_username');
			}
		}

		return errors;
	},

	getGeneralFormErrors: function() {
		var default_el = $('#default_from_email');
		if (default_el[0]) {
			if (!default_el.val().length || default_el.val().indexOf('@') === -1) {
				return ['default_email'];
			}
		}

		return [];
	},

	getGappsFormErrors: function() {
		var username = $('#transport_gmail_options_username').val();
		var password = $('#transport_gmail_options_password').val();

		var errors = [];
		if (!username.length) {
			errors.push('gapps_username');
		}
		if (!password.length) {
			errors.push('gapps_password');
		}

		return errors;
	}
});
