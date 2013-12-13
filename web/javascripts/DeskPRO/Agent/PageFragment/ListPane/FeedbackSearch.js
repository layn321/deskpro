Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.FeedbackSearch = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.Basic,

	initPage: function(el) {
		var self = this;
		this.wrapper = el;
	}
});