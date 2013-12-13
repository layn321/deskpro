Orb.createNamespace('DeskPRO.Admin.Departments');

DeskPRO.Admin.Departments.UsergroupSelector = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	initPage: function() {
		var self = this;

		this.department_id = this.el.data('department-id');
		var btn = this.el;

		this.optionbox = new DeskPRO.UI.OptionBox({
			element: $('#optionbox_ug_' + this.department_id),
			trigger: this.el,
			onClose: function(optionbox) {
				var count = optionbox.getCount('usergroups');

				if (count > 0) {
					btn.text(count + ' usergroups');
				} else {
					btn.text('No usergroups');
				}

				self.fireEvent('updated', [self.department_id, optionbox.getSelected('usergroups'), self]);
			}
		});
	},

	getHandlerName: function() {
		return 'usergroup_selector';
	}
});
