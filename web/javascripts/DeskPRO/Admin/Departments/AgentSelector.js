Orb.createNamespace('DeskPRO.Admin.Departments');

DeskPRO.Admin.Departments.AgentSelector = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	initPage: function() {
		var self = this;

		this.department_id = this.el.data('department-id');
		var btn = this.el;

		this.optionbox = new DeskPRO.UI.OptionBox({
			element: $('#optionbox_dep_' + this.department_id + '_' + self.el.data('app')),
			trigger: this.el,
			onClose: function(optionbox) {
				var countAgents = optionbox.getCount('agents');

				var words = [];
				if (countAgents > 0) {
					words.push(countAgents + ' agents');
				}

				if (!countAgents) {
					words = ['No agents'];
				}

				btn.text(words.join(', '));

				var agents = {};

				var selected = optionbox.getSelected('agents');
				for (var i = 0; i < selected.length; i++) {
					if (!agents[selected[i]]) {
						agents[selected[i]] = [];
					}
					agents[selected[i]].push('full');
				}

				selected = optionbox.getSelected('agents_assign');
				for (var i = 0; i < selected.length; i++) {
					if (!agents[selected[i]]) {
						agents[selected[i]] = [];
					}
					agents[selected[i]].push('assign');
				}

				self.fireEvent('updated', [self.department_id, self.el.data('app'), agents, self]);
			}
		});
	},

	getHandlerName: function() {
		return 'agent_selector';
	}
});
