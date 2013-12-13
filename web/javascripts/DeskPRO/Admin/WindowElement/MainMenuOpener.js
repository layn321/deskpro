Orb.createNamespace('DeskPRO.Admin.WindowElement');

DeskPRO.Admin.WindowElement.MainMenuOpener = new Orb.Class({
	Extends: DeskPRO.Agent.WindowElement.MainMenuOpener,

	getMenuHandlerClass: function(li) {
		var classname = this.parent(li);

		if (!classname) {
			classname = DeskPRO.Admin.WindowElement.BasicMainMenu;
		}

		return classname;
	}
});