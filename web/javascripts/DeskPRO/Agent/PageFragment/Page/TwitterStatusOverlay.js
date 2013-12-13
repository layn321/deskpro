Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');

DeskPRO.Agent.PageFragment.Page.TwitterStatusOverlay = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'twitter-status-overlay';
	},

	initPage: function(el) {
		this.el = $(el);

		DeskPRO_Window.initInterfaceLayerEvents(this.el);

		this.twitterHelper = new DeskPRO.Agent.PageHelper.Twitter(this.el, this);
	}
});
