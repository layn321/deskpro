Orb.createNamespace('DeskPRO.Admin.ElementHandler');

DeskPRO.Admin.ElementHandler.UserModePage = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var self = this;

		var form = $('#mode_form');

		var quitit = false;
		form.on('click', 'li', function(ev) {
			if (quitit) return;
			quitit = true;

			var li = $(this);
			var radio = $('input[name="mode"]', li).click();
			$('li', form).removeClass('on');
			li.addClass('on');

			$('#save_btn_wrap').show();

			quitit = false;
		});

		form.on('submit', function(ev) {

			ev.preventDefault();

			var selectedLi = $('.dp-input-group.on');
			var formData = selectedLi.find('input, textarea, select').serializeArray();

			formData.append($('#general_settings').find('input, textarea, select').serializeArray());

			form.addClass('loading');
			$.ajax({
				url: form.attr('action'),
				data: formData,
				type: 'POST',
				complete: function() {
					window.location.reload(false);
				},
				success: function() {
					$('#save_btn_wrap').hide();
				}
			});
		});

		var reg_urls = $('input[name="reg_url"]');
		reg_urls.on('change', function() {
			reg_urls.val($(this).val());
		});

		var triggerToggles = $('.trigger-toggle');
		var ischanging = false;
		triggerToggles.on('change', function() {
			if (ischanging) return;
			ischanging = true;

			var id = $(this).val();
			var onoff = $(this).is(':checked') ? 1 : 0;

			triggerToggles.filter('[value="' + id + '"]').prop('checked', onoff ? true : false);

			$.ajax({
				url: BASE_URL + 'admin/tickets/triggers/toggle-enabled.json',
				type: 'POST',
				dataType: 'json',
				data: { trigger_id: id, onoff: onoff }
			});

			ischanging = false;
		});
	}
});
