Orb.createNamespace('DeskPRO.Agent.WindowElement.Section');

DeskPRO.Agent.WindowElement.Section.People = new Orb.Class({
	Extends: DeskPRO.Agent.WindowElement.Section.AbstractSection,

	init: function() {

		this.buttonEl = $('#people_section');

		this.urlFragmentName = 'people';

		this.setSectionElement($('<section id="people_outline"></section>'));

		DeskPRO_Window.getMessageBroker().addMessageListener('agent-notify.new_registration', function(info) { this.reloadCounts(); }, this);
		DeskPRO_Window.getMessageBroker().addMessageListener('agent.person.added', function(info) { this.reloadCounts(); }, this);
		DeskPRO_Window.getMessageBroker().addMessageListener('agent.person.confirmed', function(info) { this.reloadCounts(); }, this);
		DeskPRO_Window.getMessageBroker().addMessageListener('agent.person.removed', function(info) { this.reloadCounts(); }, this);

		this.reload();
	},

	reload: function() {
		DeskPRO_Window.getSectionData('people_section', (function(data) {
			var wasLaoded = false;
			if (this.hasLoaded) {
				wasLoaded = true;

				this.peopleTabs.destroy();
				delete this.peopleTabs;

				this.orgTabs.destroy();
				delete this.orgTabs;
			}

			this._initSection(data);

			if (!wasLaoded) {
				this.fireEvent('sectionInit');
			}
		}).bind(this));
	},

	reloadCounts: function() {
		$.ajax({
			url: BASE_URL + 'agent/people/get-section-data/reload-counts.json',
			dataType: 'json',
			context: this,
			success: function(countData) {
				this.setCountData(countData);
			}
		});
	},

	setCountData: function(countData) {
		if (typeof countData.people_count != 'undefined') {
			$('#people_nav_all').find('span.list-counter').text(countData.people_count + '');
		}

		if (typeof countData.usergroup_counts != 'undefined') {
			Object.each(countData.usergroup_counts, function(count, uid) {
				var el = $('#people_nav_ug_' + uid);
				el.find('span.list-counter').text(count+'');

				if (!parseInt(count)) {
					el.hide();
				} else {
					el.show();
				}
			});
		}

		if (typeof countData.validating_count != 'undefined') {
			var el = $('#people_nav_awaiting_validation');
			el.find('span.list-counter').text(countData.validating_count+'');

			if (!parseInt(countData.validating_count)) {
				el.hide();
			} else {
				el.show();
			}
		}
		if (typeof countData.validating_count_agent != 'undefined') {
			var el = $('#people_nav_awaiting_agent_validation');
			el.find('span.list-counter').text(countData.validating_count_agent+'');

			if (!parseInt(countData.validating_count_agent)) {
				el.hide();
			} else {
				el.show();
			}
		}

		if (!parseInt(countData.validating_count) && !parseInt(countData.validating_count_agent)) {
			$('#people_pending_section').hide();
		} else {
			$('#people_pending_section').show();
		}
	},

	reloadLabels: function() {
		$.ajax({
			url: BASE_URL + 'agent/people/get-section-data/labels.json',
			context: this,
			success: function(data) {
				$('#people_outline_tagcloud').empty().html(data.people_label_cloud);
				$('#people_outline_taglist').empty().html(data.people_label_list);

				if ($('.no-labels', '#people_outline_taglist').length) {
					$('#people_outline_tabstrip').hide();
				} else {
					$('#people_outline_tabstrip').show();
				}

				$('#people_outline_org_tagcloud').empty().html(data.org_label_cloud);
				$('#people_outline_org_taglist').empty().html(data.org_label_list);

				if ($('.no-labels', '#people_outline_org_taglist').length) {
					$('#people_outline_org_tabstrip').hide();
				} else {
					$('#people_outline_org_tabstrip').show();
				}
			}
		});
	},

	_initSection: function(data) {
		this.setHasInitialLoaded();

		this.contentEl.empty().html(data.section_html);

		this._initSectionSearch();

		var self = this;
		this.peopleTabs = new DeskPRO.UI.SimpleTabs({
			context: this.sectionEl,
			triggerElements: $('#people_outline_tabstrip li'),
			onTabSwitch: function(info) {

			}
		});

		this.orgTabs = new DeskPRO.UI.SimpleTabs({
			context: this.sectionEl,
			triggerElements: $('#people_outline_org_tabstrip li'),
			onTabSwitch: function(info) {

			}
		});
	},

	_initSectionSearch: function() {
		var searchPane = this.contentEl.find('.source-pane-search');
		if (!searchPane[0]) {
			return;
		}

		this.searchForm = new DeskPRO.Agent.SourcePane.SearchForm(searchPane);

		var typeSwitcher = searchPane.find('.content_type');
		typeSwitcher.on('change', function(ev) {
			searchPane.find('.content_type_wrap').hide().filter('.content_type-'+$(this).val()).show();
		});
	}
});
