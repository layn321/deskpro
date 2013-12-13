Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.Search = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'search';
		this.wrapper = null;
	},

	initPage: function(el) {
		this.wrapper = el;
	},

	initTypeList: function(listWrap) {

	},

	loadMore: function(loadName) {

	}
});
