Orb.createNamespace('DeskPRO.Admin.PageHandler');

DeskPRO.Admin.PageHandler.LocaleEdit = new Class({
	Extends: DeskPRO.Admin.PageHandler.Basic,

	locale_id: 0,
	initialize: function(locale_id) {
		this.locale_id = locale_id;
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

		parent_win.getMessageBroker().sendMessage('locale.list.change', {
			locale_id: this.locale_id,
			row_html: row_html
		});
	}
});
