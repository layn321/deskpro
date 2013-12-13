Orb.createNamespace('DeskPRO.Agent.PageFragment.SettingsPage');

DeskPRO.Agent.PageFragment.SettingsPage.OtherNotifications = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'settings_other_notifications';
	},

	initPage: function(el) {
		var self = this;
		this.el = el;

		this.typeTabs = new DeskPRO.UI.SimpleTabs({
			triggerElements: $('.pageheader li', el)
		});

		var form = $('form', this.el);

		form.on('submit', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			var data = $(this).serializeArray();
			$.ajax({
				url: $(this).attr('action'),
				type: 'POST',
				data: data,
				dataType: 'json',
				success: function() {
					self.settingsWindow.showSavePuff();
				}
			});
		});
	}
});
