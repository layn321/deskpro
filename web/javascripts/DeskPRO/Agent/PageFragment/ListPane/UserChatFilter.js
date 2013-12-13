Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.UserChatFilter = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'chat-list';
		this.wrapper = null;
		this.contentWrapper = null;
		this.overlay = null;
		this.appendUrl = null;
		this.actionsBarHelper = null;
		this.resultTypeName = 'filter';
		this.resultTypeId = 0;
	},

	initPage: function(el) {
		var self = this;
		this.el = el;

		this.enableHighlightOpenRows('userchat', 'conversation_id', '.row-item.convo-');

		this.wrapper = $(el);
		this.contentWrapper = $('div.content:first', this.wrapper);

		this.resultTypeId = this.meta.cache_id || 0;

		if (this.getMetaData('noResults')) {
			this.noMoreResults = true;
			$('.no-more-results', this.contentWrapper).show();
		}

		var opt = {
			resultIds: this.meta.resultIds,
			perPage: this.meta.perPage || 50
		};
		if (this.meta.viewType && this.meta.viewType == 'list') {
			opt.resultRowSelector = 'tr.row-item';
			opt.resultsContainer = $('.table-result-list table', el);
			opt.navEl = $('.bottom-action-bar', el);
		}

		this.listNav = new DeskPRO.Agent.PageHelper.ListNav(this);
	}
});
