Orb.createNamespace('DeskPRO.Admin.PageHandler');

DeskPRO.Admin.PageHandler.GeneralEdit = new Class({
	Extends: DeskPRO.Admin.PageHandler.Basic,

	record_id: 0,
	record_type: '',
	parent_name: 'DeskPRO_Window',
	initialize: function(record_type, record_id, parent_name) {
		this.record_type = record_type,
		this.record_id = record_id;
		this.parent_name = parent_name || 'DeskPRO_Window';
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
		var parent_win = this.getOpenerDeskPRO(this.parent_name);
		if (!parent_win) return;

		var data = {};
		data['item_selector'] = 'li.'+this.record_type+'.'+this.record_type+'-' + this.record_id;
		data['row_html'] = row_html;

		parent_win.getMessageBroker().sendMessage('list.change', data);
	}
});
