Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');
DeskPRO.Agent.PageFragment.Page.Test = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.allowDupe = true;
		this.TYPENAME = 'test';
	},

	initPage: function(el) {
		var self = this;
		$(el).on('click', function() {
			window.setTimeout(function() { self.alertTab(); }, 3000);
		});
	}
});
