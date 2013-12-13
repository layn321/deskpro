Orb.createNamespace('DeskPRO.Admin.ElementHandler');

DeskPRO.Admin.ElementHandler.AgentTeamEditPage = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	initPage: function() {
		this.toolsMenu = new DeskPRO.UI.Menu({
			triggerElement: $('#tools_menu_trigger'),
			menuElement: $('#tools_menu')
		});
		this.deleteOverlay = new DeskPRO.UI.Overlay({
			triggerElement: '#delete_overlay_trigger',
			contentElement: '#delete_overlay'
		});
	}
});
