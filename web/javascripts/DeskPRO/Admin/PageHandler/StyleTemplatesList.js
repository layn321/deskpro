Orb.createNamespace('DeskPRO.Admin.PageHandler');

DeskPRO.Admin.PageHandler.StyleTemplatesList = new Class({
	Extends: DeskPRO.Admin.PageHandler.Basic,

	initPage: function() {
		this.initPopoutTriggers();
		DeskPRO_Window.getMessageBroker().addMessageListener('style_templates.list.change', this.handleListChange, this);
	},

	handleListChange: function(info) {
		var list = $('ul.item-list:first');
		var exist = $('li.'+info.typename+'-'+info.style_id);

		var row = $(info.row_html);
		this.initPopoutTriggers(row);

		if (exist.length) {
			exist.replaceWith(row);
		} else {
			list.prepend(row);
		}
	}
});
