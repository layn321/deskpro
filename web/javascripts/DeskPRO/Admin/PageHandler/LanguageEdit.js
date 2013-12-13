Orb.createNamespace('DeskPRO.Admin.PageHandler');

DeskPRO.Admin.PageHandler.LanguageEdit = new Class({
	Extends: DeskPRO.Admin.PageHandler.Basic,

	language_id: 0,
	initialize: function(language_id, parent_id) {
		this.language_id = language_id;
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

		parent_win.getMessageBroker().sendMessage('language.list.change', {
			language_id: this.language_id,
			parent_id: this.parent_id,
			row_html: row_html,
			typename: 'langauge'
		});
	}
});
