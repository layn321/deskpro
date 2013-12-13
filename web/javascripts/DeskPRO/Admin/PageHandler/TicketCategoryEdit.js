Orb.createNamespace('DeskPRO.Admin.PageHandler');

DeskPRO.Admin.PageHandler.TicketCategoryEdit = new Class({
	Extends: DeskPRO.Admin.PageHandler.Basic,

	category_id: 0,
	initialize: function(category_id, parent_id) {
		this.category_id = category_id;
		this.parent_id = parent_id;
	},

	initPage: function() {
		var self = this;
		$('.save-trigger').on('click', function() {
			$('form:first').submit();
		});
		$('.cancel-trigger').on('click', function() {
			self.closeThisPopout();
		});
	},

	updateParentListRow: function(row_html) {
		var parent_win = this.getOpenerDeskPRO('DeskPRO_Page_TicketCategories');
		if (!parent_win) return;

		DP.console.log(parent_win);
		parent_win.getMessageBroker().sendMessage('category.list.change', {
			category_id: this.category_id,
			parent_id: this.parent_id,
			row_html: row_html,
			typename: 'category'
		});
	}
});
