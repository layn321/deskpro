Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.PublishSearch = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.Basic,

	initPage: function(el) {
		var self = this;
		this.wrapper = el;

		this.listWrapper = $('section.list-listing', this.wrapper);
		this.sendContentLink = new DeskPRO.Agent.PageHelper.SendContentLink(this, {
			contentListEl: this.listWrapper
		});
		this.ownObject(this.sendContentLink);
	}
});