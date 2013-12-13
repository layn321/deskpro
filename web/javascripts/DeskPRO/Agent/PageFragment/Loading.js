Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');

/**
 * Special loading fragment that just shows "Loading..." until a page has actually loaded
 */
DeskPRO.Agent.PageFragment.Page.Loading = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.allowDupe = true;
		this.TYPENAME ='loading';
	},

	initPage: function(el) {
		this.wrapper = $(el);

		if (this.meta.routeData && this.meta.routeData.routeTriggerEl && this.meta.routeData.toggleOpenClass) {
			this.addEvent('destroy', function() {
				this.meta.routeData.routeTriggerEl.removeClass(this.meta.routeData.toggleOpenClass);
			}, this);
		}
	}
});
