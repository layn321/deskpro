Orb.createNamespace('DeskPRO.Admin.ElementHandler');

DeskPRO.Admin.ElementHandler.SettingsPage = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	initPage: function() {
		var self = this;

		var form = $('#settings_form');

		Array.each(['user', 'agent', 'sendemail'], function(x) {
			var maxSizeEl = $('#'+x+'_attach_maxsize_notice');
			var maxSize = parseInt(maxSizeEl.data('maxsize'));
			maxSize = (maxSize / 1024 / 1024) * 1000 * 1000; // 1000 based instead of 1024

			function formatSliderVal() {
				var val = parseInt($('#'+x+'_attach_maxsize').val());

				if (val > maxSize) {
					maxSizeEl.show();
					val = maxSize;
					$('#'+x+'_attach_maxsize_slider').slider('value', val);
				} else {
					maxSizeEl.hide();
				}

				if (val) {
					var mb = parseFloat(val / 1000000).toFixed(2);
				} else {
					var mb = 0;
				}

				$('#'+x+'_attach_maxsize_label').text(mb);
				$('#'+x+'_attach_maxsize').val(val);
			}

			if (maxSize) {
				var sliderMax = Math.max(50000000, maxSize + 1000000);
			} else {
				sliderMax = 50000000;
			}

			$('#'+x+'_attach_maxsize_slider').slider({
				min: 0,
				max: sliderMax,
				step: 100000,
				value: $('#'+x+'_attach_maxsize').val(),
				slide: function(event, ui) {
					var val = parseInt(ui.value);
					$('#'+x+'_attach_maxsize').val(val);
					formatSliderVal();
				}
			});
			formatSliderVal();

			var blacklistOpt = $('#'+x+'_attach_limit_type_b');
			var whitelistOpt = $('#'+x+'_attach_limit_type_w');
			var blacklist = $('#'+x+'_attach_limit_list_blacklist');
			var whitelist = $('#'+x+'_attach_limit_list_whitelist');

			blacklistOpt.on('click', function() {
				blacklist.show();
				whitelist.hide();
			});

			whitelistOpt.on('click', function() {
				whitelist.show();
				blacklist.hide();
			});

			if (whitelistOpt.prop('checked')) {
				whitelistOpt.prop('checked', true);
				whitelistOpt.trigger('click');
			} else {
				blacklistOpt.prop('checked', true);
				blacklistOpt.trigger('click');
			}

			form.on('submit', function(ev) {
				if ($('#'+x+'_attach_limit_type_b').is(':checked')) {
					whitelist.val('');
				} else {
					blacklist.val('');
				}
			});
		}, this);

		var settingsWarn = new DeskPRO.UI.Overlay({
			contentElement: '#adv_settings_warn',
			triggerElement: '#adv_settings_btn'
		});

		form.on('submit', function(ev) {
			$('#save_loading').show();
			$('#save_btn').hide();

			var url = $('#helpdesk_url').val().trim();
			var origUrl = $('#helpdesk_url').data('original-value').trim();

			if (url == origUrl && !$('#redirect_correct_url').hasClass('do-check')) {
				form.addClass('do-process');
			}

			if (form.hasClass('do-process')) {
				return;
			}

			ev.preventDefault();

			url = url.replace(/index\.php/, '');
			url = url.replace(/\/*$/, '');
			url += '/';

			if (!url.match(/^https?:\/\//)) {
				url = 'http://' + url;
			}

			$('#helpdesk_url').val(url);

			url += 'index.php?_sys=ping&type=jsonp';

			$('#helpdesk_url_invalid').hide();
			$('#save_loading').show();
			$('#save_btn').hide();

			$.ajax({
				url: url,
				dataType: 'jsonp',
				error: function() {
					$('#helpdesk_url_invalid').show();
					$('#save_loading').hide();
					$('#save_btn').show();
				},
				success: function(res) {
					form.addClass('do-process');
					form.submit();
				}
			});
		});
	}
});
