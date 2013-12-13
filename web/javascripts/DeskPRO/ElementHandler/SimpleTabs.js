Orb.createNamespace('DeskPRO.Agent.PageHelper');

DeskPRO.ElementHandler.SimpleTabs = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var triggerElements, activeClassname = 'on';
		if (this.el.data('trigger-elements')) {
			triggerElements = $(this.el.data('trigger-elements'), this.el);
		} else {
			if (this.el.is('ul')) {
				triggerElements = this.el.find('> li');
			} else {
				triggerElements = this.el.find('ul').first().find('> li');
			}
		}

		if (this.el.data('active-classname')) {
			activeClassname = this.el.data('active-classname');
		}

		this.simpleTabs = new DeskPRO.UI.SimpleTabs({
			triggerElements: triggerElements,
			activeClassname: activeClassname
		});

		this.el.data('simpletabs', this.simpleTabs);
	}
});
