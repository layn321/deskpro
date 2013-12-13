Orb.createNamespace('DeskPRO.Admin.ElementHandler');

DeskPRO.Admin.ElementHandler.LangEditSettings = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var self = this;

		$('#default_lang_setting').on('change', function() {
			$('#save_lang_settings').show();
		});

		$('#save_lang_settings').on('click', function() {
			$('#save_lang_settings').hide();

			var url = self.el.data('set-default-lang-url');
			$.ajax({
				url: url,
				data: {value: $('#default_lang_setting').val() },
				type: 'POST'
			});
		});
	}
});
