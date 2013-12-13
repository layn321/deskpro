Orb.createNamespace('DeskPRO.Agent.PageFragment.SettingsPage');

DeskPRO.Agent.PageFragment.SettingsPage.Filters = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.Basic,

	initPage: function(el) {

		var ticketsSection = DeskPRO_Window.sections.tickets_section;

		window.settings_filters_page = this;
		this.el = el;
		var self = this;
		var didChangeFilterVis = false;

		$('#settingswin').bind('dp_settings_filtersupdated', function() {
			self.settingsWindow.reloadInterface = true;
			self.settingsWindow.reloadTab('filters');
		});

		var runningAjax = null;
		var sendUpdate = function() {

			if (runningAjax) {
				runningAjax.abort();
				runningAjax = null;
			}

			var postData = [];
			self.el.find('.filter-hidden-check').each(function() {
				var id = parseInt($(this).val());
				var v;

				if (this.checked) {
					v = 'hidden';
				} else {
					v = '';
				}

				postData.push({
					name: 'prefs[agent.ui.filter-visibility.' + id + ']',
					value: v
				});
			});

			runningAjax = $.ajax({
				type: 'POST',
				url: BASE_URL + 'agent/misc/ajax-save-prefs',
				data: postData
			});
		}

		this.el.find('.filter-hidden-check').on('click', function(ev) {
			if (!DeskPRO_Window || !DeskPRO_Window.sections.tickets_section) {
				return;
			}

			var filterId = $(this).val();

			didChangeFilterVis = true;
			if (this.checked) {
				$('#tickets_outline_custom_filters').find('.filter-' + filterId).addClass('filter-hidden');
				if (!$('#tickets_outline_custom_filters').find('li.filter').not('.filter-hidden')[0]) {
					$('#tickets_outline_custom_filters').find('.no-data').show();
				}
			} else {
				$('#tickets_outline_custom_filters').find('.filter-' + filterId).removeClass('filter-hidden');
				$('#tickets_outline_custom_filters').find('.no-data').hide();
			}

			$('#tickets_outline_custom_filters').find('li').removeClass('first').not('.filter-hidden').first().addClass('first');

			sendUpdate();
		});

		var activateView = $('#settingswin').data('activateView');
		if (activateView) {
			this.activateView(activateView);
		}
	},

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'settings_filters';
	}
});
