Orb.createNamespace('DeskPRO.Agent.PageFragment.SettingsPage');

DeskPRO.Agent.PageFragment.SettingsPage.TicketNotifications = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'settings_ticket_notifications';
	},

	initPage: function(el) {
		var self = this;
		this.el = el;

		this.typeTabs = new DeskPRO.UI.SimpleTabs({
			triggerElements: $('.pageheader li', el),
			effect: 'fade'
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

		var checks = this.el.find(':checkbox');
		Array.each(['email', 'alert'], function(type) {
			checks.filter('[name^="filter_sub[5]['+type+'"]').each(function() {
				$(this).on('click', function() {
					if (this.checked) {
						var name = $(this).attr('name').replace(/^.*?\[([a-zA-Z_]+)\]$/, '$1');
						checks.filter('[name$="['+name+']"]').prop('checked', true);
					}
				});
			});
		});
	}
});
