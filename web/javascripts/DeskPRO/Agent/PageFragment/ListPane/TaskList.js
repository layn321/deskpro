Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.TaskList = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'task-list';
	},

	initPage: function(el) {
		var self = this;

		if (DeskPRO_Window.sections.tasks_section) {
			DeskPRO_Window.sections.tasks_section.doRelaodPage = false;
		}

		var control = new DeskPRO.Agent.PageHelper.TaskListControl(el, {
			menuVis:  this.getEl('menu_vis'),
			assignOb: this.getEl('assign_ob'),
			completeCountEl: this.getEl('complete_count')
		});

		control.addEvent('updateUi', function() {
			self.updateUi();
		});
	}
});
