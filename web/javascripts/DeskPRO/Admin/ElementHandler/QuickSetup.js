Orb.createNamespace('DeskPRO.Admin.Departments');

DeskPRO.Admin.ElementHandler.QuickSetup = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	initPage: function() {
		window.QUICK_SETUP = this;
		var self = this;

		if ($('html').hasClass('no-rgba')) {
			this.showFn = 'show';
		} else {
			this.showFn = 'fadeIn';
		}


		this._initInstallSoftwareSection();
		this._initCronSection();
		this._initLicenseSection();
		this._initOutgoingEmailSection();
		this._initIncomingEmailSection();

		this.totalStepCount = $('.mega-tick').length;

		$('.undo-tick-trigger').on('click', function() {
			var boxId = $(this).attr('id').replace(/_edit$/, '');
			$('#' + boxId).find('.mega-tick').fadeOut();
			$(this).fadeOut();
		});

		self.recountSteps();
		window.setTimeout(function() {
			self.sessionPing();
		}, 300000);
	},

	sendStatusPing: function(type) {
		var data = {
			install_token: INSTALL_TOKEN,
			step: type
		};
		$.ajax({
			url: MA_SERVER + '/api/data-submit/ping-install.json',
			dataType: 'html',
			data: data,
			cache: false,
			timeout: 15000
		});
	},

	recountSteps: function() {
		var checks = $('.mega-tick');
		var total_count = this.totalStepCount;
		var done_count = checks.filter(':visible').length;

		if (done_count >= total_count) {
			$('#section_done').find('button').removeClass('disabled').on('click', function(ev) {
				ev.preventDefault();
				window.location = $(this).data('url');
			});

			$('#section_done').find('em').hide();

			this.sendStatusPing('initial_done');
		} else {
			var remaining = total_count - done_count;
			$('#section_done').find('label').text(remaining+'');
			$('#section_done').find('.steps').text(remaining>1?'steps':'step');
		}
	},

	scrollToSection: function(section) {
		var title = $(section);
		$('html,body').animate({scrollTop: title.offset().top - 45 }, 600);
	},

	/**
	 * Simple request to ping our session to keep it alive while on this page
	 */
	sessionPing: function() {
		var self = this;
		$.ajax({
			url: BASE_URL + 'admin/session-ping.json',
			cache: false,
			complete: function() {
				window.setTimeout(function() {
					self.sessionPing();
				}, 300000);
			}
		});
	},

	//##################################################################################################################
	//# Install Software Section
	//##################################################################################################################

	_initInstallSoftwareSection: function() {
		this._autoDeskproUrl();
		this._autoTimezone();

		var postData = $('#setting_form').serializeArray();
		postData.push({ name: 'set_admin_tz', value:1 });
		var form = $('#setting_form');
		$.ajax({
			url: form.attr('action'),
			type: 'POST',
			data: postData,
			cache: false
		});
	},

	_autoDeskproUrl: function() {
		var field = $('#setting_url');
		if (field.val()) {
			return;//already have a value
		}

		var url = window.location.href + '';
		url = url.replace(/\/admin\/(.*?)$/, '');

		field.val(url + '/');
	},

	_autoTimezone: function() {
		var tz = $('#setting_timezone');
		if (tz.val() && tz.val() != 'UTC') {
			return;//already have a value
		}

		var detected = jstz.determine_timezone();
		console.log("Detected Timezone: %o", detected.name());
		if (detected.name()) {
			tz.find('option').each(function() {
				if ($(this).val() == detected.name()) {
					$(this).prop('selected', 'selected');
					console.log("Selected Timezone: %o", this);
				}
			})
		}
	},

	//##################################################################################################################
	//# Cron
	//##################################################################################################################

	_initCronSection: function() {
		this.hasCronError = false;
		this.doCronCheck();
	},

	doCronCheck: function() {
		var self = this;
		$.ajax({
			url: $('#section_install_cron').data('check-url'),
			cache: false,
			dataType: 'json',
			cache: false,
			success: function(data) {
				console.log(data.cron_okay);
				if (!data || !data.cron_okay) {
					window.setTimeout(function() {
						self.doCronCheck();
					}, 3200);

					if (data.cron_errors) {
						$('#cron_errors').show().html(data.cron_errors);

						if (!self.hasCronError) {
							self.scrollToSection('#section_install_cron');
						}
						self.hasCronError = true;
					}
				} else {
					self.hasCronError = false;
					$('#section_install_cron').find('.mega-tick')[self.showFn]();
					$('#cron_errors').hide();
					self.recountSteps();
					self.sendStatusPing('cron');
				}
			}
		});
	},

	//##################################################################################################################
	//# License
	//##################################################################################################################

	_initLicenseSection: function() {
		var self = this;
		var wrapper = $('#section_enter_license');

		DP.select($('#lic_phone_country'));

		wrapper.find('.page-radio-group').on('click', function(ev) {
			wrapper.find('.page-radio-group').removeClass('open');
			$(this).addClass('open').find(':radio').prop('checked', true);
		});

		var reqlicGroup   = $('#lic_group_get_demo');
		var enterlicGroup = $('#lic_group_enter_license');

		$('#lic_do_send_again').on('click', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			enterlicGroup.find('.demo-sent-message').hide();
			wrapper.find('.page-radio-group').removeClass('open');
			enterlicGroup.removeClass('open');
			reqlicGroup.addClass('open').find(':radio').prop('checked', true);
		});

		//-----
		// Handling license request
		//-----

		reqlicGroup.find('form').on('submit', function(ev) {

			ev.preventDefault();
			ev.stopPropagation();

			enterlicGroup.find('.demo-sent-message').hide();
			enterlicGroup.find('.errors-box').hide();
			reqlicGroup.find('.errors-box').hide().find('.error-item').hide();

			this.licSentTo = $('#lic_email_address').val().trim();
			if (!this.licSentTo.length) {
				reqlicGroup.find('.errors-box').show().find('li.error_email').show();
			}
			if (!$('#lic_website_name').val().trim().length) {
				reqlicGroup.find('.errors-box').show().find('li.error_site_name').show();
			}
			if (!$('#lic_website_url').val().trim().length) {
				reqlicGroup.find('.errors-box').show().find('li.error_site_url').show();
			}
			if (!$('#lic_name').val().trim().length) {
				reqlicGroup.find('.errors-box').show().find('li.error_name').show();
			}
			if (reqlicGroup.find('.errors-box').is(':visible')) {
				return;
			}

			var url = window.location.href + '';
			url = url.replace(/\/index\.php\/(.*?)$/, '');

			var form = $(this);
			var formData = form.serializeArray();
			formData.push({
				name: 'url',
				value: url
			});
			formData.push({
				name: 'build',
				value: $(this).data('build')
			});

			form.addClass('mark-loading');

			$.ajax({
				url: $(this).data('lic-request-url'),
				type: 'POST',
				data: formData,
				dataType: 'jsonp',
				timeout: 12000,
				cache: false,
				complete: function() {
					form.removeClass('mark-loading');
				},
				error: function() {
					var errbox = reqlicGroup.find('.errors-box').show();
					var data = {
						error_codes: {unknown_request_error: 1}
					};
					Object.each(data.error_codes, function(v,code) {
						code = code.replace(/\./g, '_');
						errbox.find('.error_' + code).show();
					});
				},
				success: function(data) {
					if (data.success) {
						enterlicGroup.find('.demo-sent-message').show();
						wrapper.find('.page-radio-group').removeClass('open');
						enterlicGroup.addClass('open').find(':radio').prop('checked', true);
						$('#place_lic_email').text($('#lic_email_address').val().trim());
					} else {
						if (data.error_code && data.error_code == 'invalid_email') {
							var data = {
								error_codes: {email: 1}
							};
						} else if (data.error_code && data.error_code == 'invalid_phone') {
							var data = {
								error_codes: {phone: 1}
							};
						} else {
							var data = {
								error_codes: {unknown_request_error: 1}
							};
						}
						var errbox = reqlicGroup.find('.errors-box').show();

						Object.each(data.error_codes, function(v,code) {
							code = code.replace(/\./g, '_');
							errbox.find('.error_' + code).show();
						});
					}
				}
			});
		});

		//-----
		// Handling license set
		//-----

		enterlicGroup.find('form').on('submit', function(ev) {

			ev.preventDefault();
			ev.stopPropagation();

			enterlicGroup.find('.errors-box').hide();

			var form = $(this);
			var formData = form.serializeArray();

			form.addClass('mark-loading');
			$.ajax({
				url: $(this).attr('action'),
				type: 'POST',
				data: formData,
				dataTyoe: 'json',
				cache: false,
				complete: function() {
					form.removeClass('mark-loading');
				},
				success: function(data) {
					if (data.success) {
						$('#section_enter_license').find('.mega-tick')[self.showFn]();
						self.recountSteps();
						self.sendStatusPing('license');
					} else {
						enterlicGroup.find('.errors-box').show().find('.lic-err-code').text(data.error_code);
					}
				}
			});
		});
	},

	//##################################################################################################################
	//# Outgoing Email
	//##################################################################################################################

	_initOutgoingEmailSection: function() {
		var self = this;
		var wrapper = $('#section_config_smtp');
		var errorBox = wrapper.find('.errors-box');

		var form = wrapper.find('form');

		form.on('submit', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			errorBox.hide();

			var handler = wrapper.find('.edit-email-transport-page').data('handler');
			var errors = handler.getFormErrors();
			if (errors && errors.length) {

				console.log('Errors: %o', errors);

				errorBox.find('li.error-item').hide();

				Array.each(errors, function(code) {
					errorBox.find('li.error_' + code).show();
				});

				errorBox.show();

				return;
			}

			var formData = form.serializeArray();

			form.addClass('mark-loading');
			$.ajax({
				url: form.attr('action'),
				data: formData,
				type: 'POST',
				dataType: 'json',
				cache: false,
				complete: function() {
					form.removeClass('mark-loading');
				},
				success: function(data) {
					if (data.success) {
						wrapper.find('.mega-tick')[self.showFn]();
						$('#section_config_smtp_edit').fadeIn();
						self.recountSteps();
						self.sendStatusPing('outemail');
					}
				}
			});
		});
	},

	//##################################################################################################################
	//# Incoming Email
	//##################################################################################################################

	_initIncomingEmailSection: function() {
		var self = this;
		var wrapper = $('#section_config_pop3');
		var errorBox = wrapper.find('.errors-box');

		$('#pop3_skip_trigger').on('click', function(ev) {
			ev.preventDefault();
			wrapper.find('.mega-tick')[self.showFn]();
			self.recountSteps();
		});

		var form = wrapper.find('form');

		form.on('submit', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			errorBox.hide();
			errorBox.find('li.error-item').hide();

			var handler = wrapper.find('.edit-email-gateway-page').data('handler');
			var errors = handler.getFormErrors();
			if (errors && errors.length) {

				console.log('Errors: %o', errors);

				errorBox.find('li.error-item').hide();

				Array.each(errors, function(code) {
					errorBox.find('li.error_' + code).show();
				});

				errorBox.show();

				return;
			}

			var formData = form.serializeArray();

			form.addClass('mark-loading');
			wrapper.find('.errors-box').hide();
			$.ajax({
				url: form.attr('action'),
				data: formData,
				type: 'POST',
				dataType: 'json',
				cache: false,
				complete: function() {
					form.removeClass('mark-loading');
				},
				success: function(data) {
					if (data.success) {
						wrapper.find('.mega-tick')[self.showFn]();
						$('#section_config_pop3_edit').fadeIn();
						self.recountSteps();
					} else {
						wrapper.find('.errors-box').show().find('.error-message').text(data.error_message).show();
					}
				}
			});
		});
	}
});
