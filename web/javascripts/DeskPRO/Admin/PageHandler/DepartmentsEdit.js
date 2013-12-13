Orb.createNamespace('DeskPRO.Admin.PageHandler');

DeskPRO.Admin.PageHandler.DepartmentsEdit = new Class({
	Extends: DeskPRO.Admin.PageHandler.Basic,

	department_id: 0,
	initialize: function(department_id, parent_id) {
		this.department_id = department_id;
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

		parent_win.getMessageBroker().sendMessage('departments.list.change', {
			department_id: this.department_id,
			parent_id: this.parent_id,
			row_html: row_html,
			typename: 'department'
		});
	}
});
