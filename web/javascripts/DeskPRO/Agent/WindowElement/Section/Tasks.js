Orb.createNamespace('DeskPRO.Agent.WindowElement.Section');

DeskPRO.Agent.WindowElement.Section.Tasks = new Orb.Class({
	Extends: DeskPRO.Agent.WindowElement.Section.AbstractSection,

	init: function() {
		this.buttonEl = $('#tasks_section');

		this.urlFragmentName = 'tasks';

		this.setSectionElement($('<section id="task_outline"></section>'));
		this.refresh();

		DeskPRO_Window.getMessageBroker().addMessageListener('agent.ui.tasks.refresh-task-list', function() {
			this.refresh();
		}, this);

		this.doRelaodPage = false;
		this.addEvent('show', function() {
			if (this.doRelaodPage) {
				this.refreshPage();
			}
		}, this);
	},

	refreshPage: function() {
		this.doRelaodPage = false;
		if (this.listPage && this.listPage.meta.routeUrl) {
			DeskPRO_Window.runPageRoute('listpane:' + this.listPage.meta.routeUrl);
		}
	},

	refresh: function() {
		var countmap = {};
		$('span.list-counter', this.contentEl).each(function() {
			countmap[$(this).attr('id')] = $(this).text().trim();
		});

		var selected = $('.nav-selected', this.contentEl);
		var selectedCountId = null;
		if (selected) {
			var e = $('span.list-counter', selected).first();
			if (e.length) {
				selectedCountId = e.attr('id');
			}
		}

		DeskPRO_Window.getSectionData('tasks_section', (function(data) {
			this._initSection(data);

			this.recalcBadge();

			if (selectedCountId) {
				var countEl = $('#' + selectedCountId);
				var newCount = countEl.text().trim();
				var nav = countEl.closest('.is-nav-item');

				// Re-select the proper nav item
				nav.addClass('nav-selected');

				// And reload the view if its changed
				if (newCount != countmap[selectedCountId]) {
					var routeEl;
					if (nav.data('route')) {
						routeEl = nav;
					} else {
						routeEl = $('[data-route]', nav).first();
					}
					DeskPRO_Window.runPageRouteFromElement(routeEl);
				}

			}
		}).bind(this));
	},

	recalcBadge: function() {
		var total = parseInt($.trim($('#tasks_counter_all_total').text()));
		this.modBadgeCount('=', total);
	},

	markUnloadPage: function() {
		this.doRelaodPage = true;
		if ($('#task_outline').is('.on')) {
			this.refreshPage();
		}
	},

	_initSection: function(data) {
		this.setHasInitialLoaded();
		this.contentEl.html(data.section_html);
		this.fireEvent('sectionInit');
	}
});
