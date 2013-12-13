Orb.createNamespace('DeskPRO.Admin.PageHandler');

DeskPRO.Admin.PageHandler.StyleEdit = new Class({
	Extends: DeskPRO.Admin.PageHandler.Basic,

	style_id: 0,
	initialize: function(style_id, parent_id) {
		this.style_id = style_id;
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
		var parent_win = this.getOpenerDeskPRO();
		if (!parent_win) return;

		parent_win.getMessageBroker().sendMessage('styles.list.change', {
			style_id: this.style_id,
			parent_id: this.parent_id,
			row_html: row_html,
			typename: 'style'
		});
	}
});
