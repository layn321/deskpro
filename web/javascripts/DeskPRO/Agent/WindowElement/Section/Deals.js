Orb.createNamespace('DeskPRO.Agent.WindowElement.Section');

DeskPRO.Agent.WindowElement.Section.Deals = new Orb.Class({
	Extends: DeskPRO.Agent.WindowElement.Section.AbstractSection,

	init: function() {
            this.buttonEl = $('#deals_section');

			this.urlFragmentName = 'deals';

            this.setSectionElement($('<section id="deal_outline"></section>'));
            this.refresh();

        },

	refresh: function() {
		DeskPRO_Window.getSectionData('deals_section', this._initSection.bind(this));
	},
	_initSection: function(data) {
		this.setHasInitialLoaded();
		this.contentEl.html(data.section_html);
		this.recountBadge();
	},

	recountBadge: function() {
		var count = 0;
		$('span.badge-count', this.contentEl).each(function() {
			count += parseInt($(this).text().trim()) || 0;
		});

		this.updateBadge(count);
	}
});
