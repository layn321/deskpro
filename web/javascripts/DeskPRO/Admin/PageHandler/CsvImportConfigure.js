Orb.createNamespace('DeskPRO.Admin.PageHandler');

DeskPRO.Admin.PageHandler.CsvImportConfigure = new Class({
	Extends: DeskPRO.Admin.PageHandler.Basic,

	initialize: function(multipleOptions) {
		this.multipleOptions = {};
		this.multipleOptions[''] = true;

		if ($.isArray(multipleOptions)) {
			for (var i = 0; i < multipleOptions.length; i++) {
				this.multipleOptions[multipleOptions[i]] = true;
			}
		}
	},

	initPage: function() {
		var self = this;
		var selects = $('.import-map-select');
		var extras = $('#import-map-extras');

		selects.change(function() {
			var disable = {}, $this = $(this);

			selects.each(function() {
				var val = $(this).val();
				if (!self.multipleOptions[val]) {
					// single use field, remove from options
					disable[val] = true;
				}
			});

			selects.each(function() {
				$(this).find('option').each(function() {
					var $option = $(this);
					if (!$option.is(':selected') && disable[$option.attr('value')]) {
						// option is not selected and used elsewhere, so disable it
						$option.hide();
					} else {
						$option.show();
					}
				});
			});

			$this.next('span').remove();

			var extra = extras.find('li[data-map-type="' + $this.val() + '"]');
			if (extra.length) {
				var html = extra.html().replace(/%prefix%/g, $this.attr('name').replace('[map]', ''));
				$('<span />').html(html).insertAfter($this);
			}

			if ($this.val() == 'primary_email') {
				$('#errors_container').hide();
			}
		});

		$('#import-form').submit(function() {
			var hasEmail = false;
			selects.each(function() {
				if ($(this).val() == 'primary_email') {
					hasEmail = true;
				}
			});

			if (hasEmail) {
				$('#primary-email-error').hide();
			} else {
				$('#primary-email-error').show();
			}

			var welcomeValid = true;

			if ($(this).find('input[name=welcome_email]:checked').val() == 1) {
				$('input, textarea', '#welcome-email-inputs').each(function() {
					if ($(this).val() == '') {
						welcomeValid = false;
					} else if ($(this).attr('name') == 'from_email' && $(this).val().indexOf('@') == -1) {
						welcomeValid = false;
					}
				});
			}

			if (welcomeValid) {
				$('#welcome-email-error').hide();
			} else {
				$('#welcome-email-error').show();
			}

			return hasEmail && welcomeValid;
		});

		$('#welcome-email-textarea').TextAreaExpander();
	}
});
