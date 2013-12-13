Orb.createNamespace('DeskPRO.Agent.PageFragment.SettingsPage');

DeskPRO.Agent.PageFragment.SettingsPage.TicketSlas = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.Basic,

	initPage: function(el) {

		this.el = el;
		var self = this;

		var runningAjax = null;
		var sendUpdate = function() {

			if (runningAjax) {
				runningAjax.abort();
				runningAjax = null;
			}

			var postData = [];
			self.el.find('.sla-hidden-check').each(function() {
				var id = parseInt($(this).val());
				var v;

				if (this.checked) {
					v = 'hidden';
				} else {
					v = '';
				}

				postData.push({
					name: 'prefs[agent.ui.sla.filter-visibility.' + id + ']',
					value: v
				});
			});

			postData.push({
				name: 'prefs[agent.ui.sla.ticket-filter]',
				value: el.find('.grouping-option').val()
			});

			runningAjax = $.ajax({
				type: 'POST',
				url: BASE_URL + 'agent/misc/ajax-save-prefs',
				data: postData
			});
		}

		el.find('.grouping-option').on('change', function() {
			$('#ticket_slas_header').data('sla-filter', $(this).val());
			sendUpdate();

			if (!DeskPRO_Window || !DeskPRO_Window.sections.tickets_section) {
				return;
			}

			DeskPRO_Window.sections.tickets_section.getUpdatedSlaCounts();
		});

		this.el.find('.sla-hidden-check').on('click', function(ev) {
			if (!DeskPRO_Window || !DeskPRO_Window.sections.tickets_section) {
				return;
			}

			var filterId = $(this).val();

			didChangeFilterVis = true;
			if (this.checked) {
				$('#tickets_outline_slas').find('.sla-' + filterId).addClass('filter-hidden');
				if ($('#tickets_outline_slas').find('li').not('.filter-hidden')[0]) {
					$('#tickets_outline_slas').find('.no-data').show();
				}
			} else {
				$('#tickets_outline_slas').find('.sla-' + filterId).removeClass('filter-hidden');
				$('#tickets_outline_slas').find('.no-data').hide();
			}

			sendUpdate();
		});

		var activateView = $('#settingswin').data('activateView');
		if (activateView) {
			this.activateView(activateView);
		}
	},

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'settings_slas';
	}
});
