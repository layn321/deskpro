Orb.createNamespace('DeskPRO.Admin.ElementHandler');

DeskPRO.Admin.ElementHandler.DashVersion = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		$.ajax({
			url: BASE_URL + 'admin/dashboard/load-version-info.html',
			dataType: 'html',
			success: function(html) {
				$('#version_info_expanded').html(html);
			}
		});
	}
});
