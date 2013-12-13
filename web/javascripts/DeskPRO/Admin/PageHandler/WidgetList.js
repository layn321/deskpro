Orb.createNamespace('DeskPRO.Admin.PageHandler');

DeskPRO.Admin.PageHandler.WidgetList = new Class({
	Extends: DeskPRO.Admin.PageHandler.Basic,

	initPage: function() {
		$(document.body).delegate('#widget-list input.widget-toggle', 'change', function() {
			var $this = $(this), form = $this.closest('form'), auth = form.find('input[name="_dp_security_token"]'),
				data = {};

			data[$this.attr('name')] = $this.is(':checked') ? 1 : 0;
			data['_dp_security_token'] = auth.val();

			$.ajax({
				url: form.attr('action'),
				type: 'POST',
				dataType: 'json',
				data: data
			});
		});
	}
});
