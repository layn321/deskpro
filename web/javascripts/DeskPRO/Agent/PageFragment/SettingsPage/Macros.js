Orb.createNamespace('DeskPRO.Agent.PageFragment.SettingsPage');

DeskPRO.Agent.PageFragment.SettingsPage.Macros = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'settings_macros';
	},

	initPage: function(el) {
		var self = this;
		this.el = el;

		$('#settingswin').bind('dp_settings_macrosupdated', function() {
			self.settingsWindow.reloadTab('macros');
			window.DESKPRO_MACRO_LABELS = null;
		});

		var event = jQuery.Event("dp_macros_updated");
		event.macroItems = [];
		this.el.find('.obj-macro').each(function(ev) {
			event.macroItems.push({
				id: $(this).data('macro-id'),
				title: $(this).text()
			});
		});
		$('#settingswin').trigger(event);

		this.el.on('click', '.delete-macro', function() {
			var row = $(this).closest('tr');
			var url = $(this).data('delete-url');
			var macroId = $(this).data('macro-id');

			DeskPRO_Window.showConfirm('Are you sure you want to permanantly delete this macros?', function() {
				$.ajax({
					url: url,
					success: function() {
						row.fadeOut(function() {
							row.remove();
						});

						$('.res-ticketmacro-'+ macroId).remove();
					}
				});
			});
		});
	}
});
