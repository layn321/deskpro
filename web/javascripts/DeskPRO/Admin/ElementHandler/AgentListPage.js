Orb.createNamespace('DeskPRO.Admin.ElementHandler');

DeskPRO.Admin.ElementHandler.AgentListPage = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	initPage: function() {
		var self = this;

		this.agentToolsMenu = new DeskPRO.UI.Menu({
			menuElement: $('#agent_tools_menu'),
			onItemClicked: function (info) {
				var row = $(info.menu.getOpenTriggerEvent().target).closest('tr');
				var agentId = row.data('agent-id');
				var agentName = row.find('h4').text().trim();

				if ($(info.itemEl).hasClass('vacation')) {
					self.showAgentVacationOverlay(agentId, agentName);
				} else {
					self.showAgentDeleteOverlay(agentId, agentName);
				}
			}
		});
		this.agentVacationOverlay = new DeskPRO.UI.Overlay({
			contentElement: '#vacation_overlay'
		});
		this.agentDeleteOverlay = new DeskPRO.UI.Overlay({
			contentElement: '#delete_overlay'
		});

		$(document).on('click', '.tools-menu-icon.agent', function(ev) {
			self.agentToolsMenu.open(ev);
		});

		var addUsTrigger = $('#add_from_us_menu_trigger a');
		if (addUsTrigger[0]) {
			var menu = new DeskPRO.UI.Menu({
				triggerElement: addUsTrigger,
				menuElement: $('#add_from_us_menu')
			});
		}

		$('.kill-chat-session').on('click', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			var agentId = $(this).data('agent-id');
			$.ajax({
				url: BASE_URL + 'admin/agents/kill-session/' + agentId,
				type: 'POST'
			});

			$(this).fadeOut();
		});
	},

	showAgentVacationOverlay: function(agentId, agentName) {
		$('#vacation_overlay').find('.agent-name').text(agentName);
		$('#vacation_overlay').find('.agent-id').val(agentId);
		var f = $('#vacation_overlay').find('form');
		f.attr('action', f.attr('action').replace(/00000/g, agentId));

		this.agentVacationOverlay.open();
	},

	showAgentDeleteOverlay: function(agentId, agentName) {
		$('#delete_overlay').find('.agent-name').text(agentName);
		$('#delete_overlay').find('.agent-id').val(agentId);
		var f = $('#delete_overlay').find('form');
		f.attr('action', f.attr('action').replace(/00000/g, agentId));

		this.agentDeleteOverlay.open();
	}
});
