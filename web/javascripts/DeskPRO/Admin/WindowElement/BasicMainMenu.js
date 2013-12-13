Orb.createNamespace('DeskPRO.Admin.WindowElement');

DeskPRO.Admin.WindowElement.BasicMainMenu = new Class({
	Extends: DeskPRO.Agent.WindowElement.MainMenu.Abstract,

	initialize: function(buttonEl, options) {
		this.parent(buttonEl, options);

		var self = this;
		$('ol.icon-menu > li', this.menuEl).on('click', function(ev) {
			var a = $('a:first', this);
			if (a && a.attr('href')) {
				ev.stopPropagation();
				window.location = a.attr('href');
			}
		});
	}
});
