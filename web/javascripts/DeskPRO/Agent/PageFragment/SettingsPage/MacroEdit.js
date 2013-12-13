Orb.createNamespace('DeskPRO.Agent.PageFragment.SettingsPage');

DeskPRO.Agent.PageFragment.SettingsPage.MacroEdit = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'settings_macro_edit';
	},

	initPage: function(el) {
		var self = this;
		this.el = el;

		var actTpl  = this.getEl('actions_tpl');
		var actList = this.getEl('actions_list');

		var editor = new DeskPRO.Form.RuleBuilder(actTpl);
		editor.addEvent('newRow', function(new_row) {
			$('.remove', new_row).on('click', function() {
				new_row.remove();
			});
		});
		$('.add-term', actList).data('add-count', 0).on('click', function() {
			var count = parseInt($(this).data('add-count'));
			var basename = 'actions['+count+']';

			$(this).data('add-count', count+1);

			editor.addNewRow($('.search-terms', actList), basename);
			self.el.find('article').first().scrollTop(10000);
		});

		var count = 0;
		var actions = this.meta.actions;
		if (actions) {
			Array.each(actions, function(info, x) {
				var basename = 'actions[initial_' + x + ']';
				editor.addNewRow($('.search-terms', actList), basename, {
					type: info.type,
					op: info.op,
					options: info.options
				});
			});
		}

		this.getEl('save_btn').on('click', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			var form = self.getEl('form');
			var postData = form.serializeArray();

			$.ajax({
				url: form.attr('action'),
				type: 'POST',
				data: postData,
				dataType: 'json',
				success: function() {
					$('#settingswin').trigger('dp_settings_macrosupdated');
					self.fragmentOverlay.close();
				}
			});
		});
	}
});
