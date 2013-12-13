Orb.createNamespace('DeskPRO.Admin');

/**
 * A popout window just has a basic layout for header/footer
 */
DeskPRO.Admin.PopoutWindow = new Class({
	Extends: DeskPRO.Admin.Window,

	initialize: function() {
		$('body').layout({
			applyDefaultStyles: false
		});
	}
});