Orb.createNamespace('DeskPRO.Admin.ElementHandler');

DeskPRO.Admin.ElementHandler.PortalNav = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var self = this;

		var toggles = $('header :checkbox', this.el);
		toggles.on('change', function() {
			var checked = $(this).is(':checked');
			var section = $(this).closest('section');

			if (checked) {
				$('article', section).slideDown('fast');
			} else {
				$('article', section).slideUp('fast');
			}
		});
	}
});
