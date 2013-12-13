Orb.createNamespace('DeskPRO.Admin.ElementHandler');

DeskPRO.Admin.ElementHandler.DashNotice = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var el = this.el;
		$.ajax({
			url: BASE_URL + 'admin/dashboard/load-version-notice.html',
			dataType: 'html',
			success: function(html) {
				if (html) {
					el.html(html);
					el.show();
				}
			}
		});
	}
});
