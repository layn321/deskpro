Orb.createNamespace('DeskPRO.Admin.ElementHandler');

DeskPRO.Admin.ElementHandler.EditEmailGatewayPage = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var self = this;

		this.el.addClass('edit-email-gateway-page');

		$(document).on('click', '.test-gateway-account-settings', function() {
			self.overlay.open();
		});

		$('input.connection-type').on('click', function(ev) {
			if ($(this).val() == 'pop3') {
				self.el.find('.show-pop3').show();
				self.el.find('.hide-pop3').hide();
			} else if ($(this).val() == 'gmail') {
				self.el.find('.show-gmail').show();
				self.el.find('.hide-gmail').hide();
			} else if ($(this).val() == 'mail') {
				self.el.find('.show-phpmail').show();
				self.el.find('.hide-phpmail').hide();
			}
		});

		DP.select($('#linked_department_id'));
		DP.select($('.category_id'));

		this.overlay = new DeskPRO.UI.Overlay({
			contentElement: $('#test_gateway_settings_overlay'),
			onBeforeOverlayOpened: function() {
				var el = self.overlay.getElement();

				$('.result', el).hide().removeClass('loading');
				$('.success', el).hide();
				$('.error, .error .error-msg', el).hide();
				$('.show-error-message', el).show().on('click', function() {
					$(this).hide();
					el.find('.error-msg').show();
				});
			},
			onOverlayOpened: function() {
				if (!waitingSend) {
					$('button.test-trigger', '#test_gateway_settings_overlay').click();
				}
			}
		});

		$('#add_addr_link').on('click', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();
			$(this).hide();
			$('#gateway_addresses').slideDown();
		});
		this._initAddresses();

		$('#pop3_btn').on('click', function(ev) {
			$('#smtp_options_default').hide();
			$('#smtp_options').show();
			$('#define_transport').val('1');
		});
		$('#gapps_btn').on('click', function(ev) {
			$('#smtp_options').hide();
			$('#smtp_options_default').show();
			$('#define_transport').val('0');
		});

		$('.toggle-custom-smtp').click(function(ev) {
			ev.preventDefault();

			if ($('#smtp_options_default').is(':visible')) {
				$('#smtp_options_default').slideUp('fast', function() {
					$('#smtp_options').slideDown();
					$('#define_transport').val('1');
				});
			} else {
				$('#smtp_options').slideUp('fast', function() {
					$('#smtp_options_default').slideDown();
					$('#define_transport').val('0');
				});
			}
		});

		$('#gapps_btn, #pop3_btn').on('click', function() {
			if ($(this).is('#gapps_btn')) {
				$('.show-non-gapps').hide();
				$('.show-gapps').show();
			} else {
				$('.show-non-gapps').show();
				$('.show-gapps').hide();
			}
		});

		var errorBox = $('#page_error_box');
		if (errorBox[0]) {
			errorBox.hide();
			$('#gateway_form').on('submit', function (ev) {
				// GMail: Sync address they entered into the 'email address' standard field
				if ($('#gapps_btn').is(':checked')) {
					$('#gateway_address').val($('#gateway_gmail_options_username').val());
				}

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

		var waitingSend = false;
		var passTest = false;

		$('#gateway_form').on('submit', function(ev) {
			if (passTest) {
				return;
			}

			var smtpType = $('#gateway_form').find('[name="transport[transport_type]"]').filter(':checked').val();
			if (smtpType == 'smtp' && $('#smtp_requires_auth_check').is(':checked') && (!$('#transport_smtp_options_username').val().length || !$('#transport_smtp_options_password').val().length)) {
				$('#transport_smtp_options_username').focus();
				alert('You did not enter authentication details for your SMTP server.');
				ev.preventDefault();
				return;
			}

			ev.preventDefault();
			$('#gateway_form').addClass('loading');
			waitingSend = true;
			$('button.test-trigger', '#test_gateway_settings_overlay').trigger('click');
		});

		$('button.test-trigger', '#test_gateway_settings_overlay').on('click', function() {
			var el = $('#test_gateway_settings_overlay');
			$('.result', el).show().addClass('loading');

			// GMail: Sync address they entered into the 'email address' standard field
			if ($('#gapps_btn').is(':checked')) {
				$('#gateway_address').val($('#gateway_gmail_options_username').val());
			}

			var postData = $('#gateway_form').serializeArray();

			$.ajax({
				url: $(this).data('url'),
				type: 'POST',
				data: postData,
				dataType: 'json',
				complete: function() {
					$('.result', el).removeClass('loading');
				},
				error: function() {
					$('#gateway_form').removeClass('loading');
					$('.result', el).removeClass('loading');
					passTest = false;
					waitingSend = false;
					self.overlay.open();

					$('.success', el).hide();
					$('.error', el).show();
					$('.error-explain', el).text('Could not connect');
					$('.error-msg', el).hide();
					$('.show-error-message', el).show();
					$('.error-msg .error-msg-text', el).text('Could not connect');
					$('.error-msg .error-msg-log', el).text('Could not connect');

					if (data.error_code == '0') {
						$('.show-error-message', el).hide();
					} else {
						$('.show-error-message', el).show();
					}
				},
				success: function(data) {
					if (data.success) {
						passTest = true;
						if (waitingSend) {
							waitingSend = false;
							$('#gateway_form').submit();
						}
						$('.success', el).show().find('.placeholder-num').text(data.count+'');
					} else {
						$('.result', el).removeClass('loading');
						$('#gateway_form').removeClass('loading');
						passTest = false;
						waitingSend = false;
						self.overlay.open();

						$('.success', el).hide();
						$('.error', el).show();
						$('.error-explain', el).text(data.error_explain);
						$('.error-msg', el).hide();
						$('.show-error-message', el).show();
						$('.error-msg .error-msg-text', el).text(data.error_code + ' ' + data.error_message);
						$('.error-msg .error-msg-log', el).text(data.log);

						if (data.error_code == '0') {
							$('.show-error-message', el).hide();
						} else {
							$('.show-error-message', el).show();
						}
					}
				}
			});
		});
	},

	_initAddresses: function() {
		return;
		var self = this;

		var el = $('#gateway_addresses');

		var rowTpl = $('.row-tpl', el).get(0).innerHTML;
		var list = $('ul.list', el);
		var newInput = $('input.new-choice', el);
		var newInputType = $('select.new-choice-type', el);
		var addNewBtn = $('.add-trigger', el);

		function handleRemoveClick(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			var li = $(this).closest('li.item');

			if (li.data('address-id')) {
				var rem = $('<input type="hidden" name="remove_address[]" />').val(li.data('address-id'));
				rem.appendTo('#gateway_addresses');
			}

			li.fadeOut('fast', function() {
				li.remove();

				if (!list.find('li').length) {
					$('#gateway_addresses').slideUp('fast', function() {
						$('#add_addr_link').show();
					});
				}
			});
		}

		function handleAdd() {
			var label, newId;

			var pattern = newInput.val().trim();
			newInput.val('');

			if (!pattern.length) {
				return;
			}

			var type = newInputType.val();
			newInputType.val('');

			label = pattern;

			// We only support full addresses now, so we know this must be a real address
			if (!Orb.strIsEmail(pattern)) {
				alert('Please only enter valid email addresses here');
				return;
			}

			newId = Orb.getUniqueId();

			var newRow = $(rowTpl.replace(/%id%/g, newId));
			$('.label', newRow).html(label);
			$('.row-value-type', newRow).val(type);
			$('.row-value', newRow).val(pattern);

			list.append(newRow);
		}

		function handleAddClick(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			handleAdd();
		}

		newInput.on('keypress', function(ev) {
			if (ev.keyCode == 13) {
				ev.preventDefault();//dont enter enter key
				handleAdd();
			}
		});
		addNewBtn.on('click', handleAddClick);
		list.on('click', '.remove', handleRemoveClick);

		$(list).sortable({
			axis: 'y',
			handle: '.drag',
			items: '> li',
			start: function() {
				list.addClass('dragging');
			},
			stop: function() {
				list.removeClass('dragging');
			}
		});
	},

	getFormErrors: function() {

		var errors = this.getGeneralFormErrors();

		var type = this.el.find(':radio[name="gateway[connection_type]"]:checked').val();
		if (type == 'pop3') {
			errors.combine(this.getPop3Errors());
		} else if (type == 'gmail') {
			errors.combine(this.getGappsFormErrors());
		}

		return errors;
	},

	getGeneralFormErrors: function() {
		var gateway_address = $('#gateway_address').val();
		if (!gateway_address.length || gateway_address.indexOf('@') === -1) {
			return ['gateway_address'];
		}

		return [];
	},

	getPop3Errors: function() {
		var host = $('#gateway_pop3_options_host').val();
		var port = $('#gateway_pop3_options_port').val();
		var username = $('#gateway_pop3_options_username').val();
		var password = $('#gateway_pop3_options_password').val();

		var errors = [];
		if (!host.length) {
			errors.push('pop3_host');
		}

		if (!port.length || !parseInt(port)) {
			errors.push('pop3_port');
		}

		if (!username.length) {
			errors.push('pop3_username');
		}

		if (!password.length) {
			errors.push('pop3_password');
		}

		return errors;
	},

	getGappsFormErrors: function() {

		var username = $('#gateway_gmail_options_username').val();
		var password = $('#gateway_gmail_options_password').val();

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
