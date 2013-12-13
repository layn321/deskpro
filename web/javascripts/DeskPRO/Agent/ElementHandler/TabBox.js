Orb.createNamespace('DeskPRO.Agent.ElementHandler');

/**
 * Any wrapper that has 'nav ul' for tabs. The wrapper acts
 * as the context for data-tab-for
 */
DeskPRO.Agent.ElementHandler.TabBox = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	initPage: function() {
		var ul = $('nav ul', this.el).first();
		this.tabs = new DeskPRO.UI.SimpleTabs({
			triggerElements: $('li', ul),
			context: this.el
		});
	},

	destroy: function() {
		if (this.tabs) {
			this.tabs.destroy();
			this.tabs = null;
		}
		this.el = null;
	}
});
