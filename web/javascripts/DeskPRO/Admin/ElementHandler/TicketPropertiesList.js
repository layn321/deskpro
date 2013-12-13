Orb.createNamespace('DeskPRO.Admin.ElementHandler');

DeskPRO.Admin.ElementHandler.TicketPropertiesList = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var self = this;

		$(':checkbox[data-setting-name], :checkbox[data-enable-url]', this.el).on('change', function(ev) {
			var me = $(this);
			var val = me.is(':checked') ? 1 : 0;
			var mode, url;

			if (val && me.data('enable-url')) {
				url = me.data('enable-url');
				mode = 'redirect';
			} else if (!val && me.data('disable-url')) {
				url = me.data('disable-url');
				mode = 'redirect';
			} else {
				url = self.el.data('set-setting-url').replace(/_SETTING_NAME_/g, $(this).data('setting-name'));
				mode = 'ajax';
			}

			if (mode == 'redirect') {
				url = Orb.appendQueryData(url, 'return', window.location.href);
				console.log(url);
				window.location.href = url;
			} else if (mode == 'ajax') {
				$.ajax({
					url: url,
					type: 'POST',
					data: { value: val },
					dataType: 'json'
				});
			}
		});

		$(':checkbox[data-field-id]', this.el).on('change', function() {
			var val = $(this).is(':checked') ? 1 : 0;
			var url = self.el.data('set-field-url').replace(/_FIELD_ID_/g, $(this).data('field-id'));

			$.ajax({
				url: url,
				type: 'POST',
				data: { is_enabled: val },
				dataType: 'json'
			});
		});
	}
});
