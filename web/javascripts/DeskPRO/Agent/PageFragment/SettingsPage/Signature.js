Orb.createNamespace('DeskPRO.Agent.PageFragment.SettingsPage');

DeskPRO.Agent.PageFragment.SettingsPage.Signature = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'settings_signature';
	},

	initPage: function(el) {
		var self = this;
		this.el = el;

		var form = $('form', this.el);

		var textarea = $('#agent_settings_signature');

		if (DeskPRO_Window.canUseAgentReplyRte() && textarea.data('rte') == 1) {
			textarea.val($('#agent_settings_signature_html').val());
			DeskPRO_Window.initRteAgentReply(textarea, {
				defaultIsHtml: true,
				inlineHiddenPosition: $('#agent_settings_is_html_signature'),
				minHeight: 100
			});
			$('#agent_settings_is_html_signature').val(1);
		} else {
			textarea.val($('#agent_settings_signature_text').val());
		}

		form.on('submit', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			if (textarea.syncCode) {
				textarea.syncCode();
			}

			var data = $(this).serializeArray();
			$.ajax({
				url: $(this).attr('action'),
				type: 'POST',
				data: data,
				dataType: 'json',
				success: function(data) {
					self.settingsWindow.showSavePuff();
				}
			});
		});
	}
});
