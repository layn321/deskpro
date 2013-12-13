Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');
DeskPRO.Agent.PageFragment.Page.PersonPopout = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Page.Person,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'person';
	},

	initPage: function(el) {
		this.parent(el);
	}
});
