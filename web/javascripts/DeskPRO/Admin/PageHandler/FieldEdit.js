Orb.createNamespace('DeskPRO.Admin.PageHandler');

DeskPRO.Admin.PageHandler.FieldEdit = new Class({
	Extends: DeskPRO.Admin.PageHandler.Basic,

	field_id: 0,
	initialize: function(field_id) {
		this.field_id = field_id;
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

	tellParentUpdated: function() {
		var parent_win = this.getOpenerDeskPRO();
		if (!parent_win) return;

		parent_win.getMessageBroker().sendMessage('field.change', {field_id: this.field_id});
	},

	updateParentListRow: function(row_html) {
		var parent_win = this.getOpenerDeskPRO();
		if (!parent_win) return;

		var data = {};
		data['item_selector'] = 'li.field-' + this.field_id;
		data['row_html'] = row_html;

		parent_win.getMessageBroker().sendMessage('list.change', data);
	}
});
