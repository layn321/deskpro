Orb.createNamespace('DeskPRO.Agent.PageFragment.SettingsPage');

DeskPRO.Agent.PageFragment.SettingsPage.FilterEdit = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'settings_filter_edit';
	},

	initPage: function(el) {
		var self = this;
		this.el = el;

		var ticketsSection = DeskPRO_Window.sections.tickets_section;

		var critTpl = this.getEl('criteria_tpl');
		var critList = this.getEl('criteria_list');

		var editor = new DeskPRO.Form.RuleBuilder(critTpl);
		editor.addEvent('newRow', function(new_row) {
			$('.remove', new_row).on('click', function() {
				new_row.remove();
			});
		});
		$('.add-term', critList).data('add-count', 0).on('click', function() {
			var count = parseInt($(this).data('add-count'));
			var basename = 'terms['+count+']';

			$(this).data('add-count', count+1);

			editor.addNewRow($('.search-terms', critList), basename);
			self.el.find('article').first().scrollTop(10000);
		});

		var count = 0;
		var terms = this.meta.terms;
		if (terms) {
			Array.each(terms, function(info, x) {
				var basename = 'terms[initial_' + x + ']';
				editor.addNewRow($('.search-terms', critList), basename, {
					type: info.type,
					op: info.op,
					options: info.options
				});
			});
		}

		this.getEl('delete_btn').on('click', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			var url = $(this).data('delete-url');
			var filterId = $(this).data('filter-id');

			DeskPRO_Window.showConfirm('Are you sure you want to permanantly delete this filter?', function() {
				$.ajax({
					url: url,
					success: function() {
						var ticketsSection = DeskPRO_Window.sections.tickets_section;
						if (ticketsSection) {
							ticketsSection.removeCustomFilter(filterId);
						}
						self.fragmentOverlay.close();
					}
				});
			});
		});

		this.getEl('save_btn').on('click', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			var form = self.getEl('form');
			var postData = form.serializeArray();

			if (!$('input[name="filter[title]"]').val().trim().length) {
				alert('Enter a filter title');
				return;
			}

			if (!$('select[name^="terms["]').length) {
				alert('Choose at least one criteria');
				return;
			}

			$.ajax({
				url: form.attr('action'),
				type: 'POST',
				data: postData,
				dataType: 'json',
				success: function(data) {

					if (ticketsSection && data) {
						if (data.is_new) {

						} else {
							ticketsSection.updateCustomFilterTitle(data.filter_id, data.filter_title);
						}
					}

					$('#settingswin').trigger('dp_settings_filtersupdated');
					self.fragmentOverlay.close();
				}
			});
		});
	}
});
