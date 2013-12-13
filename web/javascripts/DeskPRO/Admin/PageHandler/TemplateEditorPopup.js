Orb.createNamespace('DeskPRO.Admin.PageHandler');

DeskPRO.Admin.PageHandler.TemplateEditorPopup = new Class({
	Extends: DeskPRO.Admin.PageHandler.Basic,

	TYPE: 'TempalteEditorPopup',

	initialize: function(messenger_id) {
		this.parent();
		this.messenger_id = messenger_id;
	},

	initPage: function() {
		this.template_contents = $('textarea.template_contents');

		var h = $('#content').height();
		this.template_contents.height(h-8);

		var parent_win = this.getOpenerDeskPRO();
		if (parent_win && parent_win.getTemplateContent) {
			var code = parent_win.getTemplateContent(this.messenger_id).trim();
			if (code.length) {
				this.setTemplateCode(code);
			}
		}

		this.save_btn = $('button.save-trigger');
		this.save_btn.on('click', this.sendTemplate.bind(this));
	},

	setTemplateCode: function(code) {
		this.template_contents.val(code);
	},

	/**
	 * Sends the template back to the other windows field
	 */
	sendTemplate: function() {
		var parent_win = this.getOpenerDeskPRO();
		if (parent_win) {
			parent_win.setTemplateContent(this.messenger_id, this.template_contents.val());
		}

		this.closeThisPopout();
	}
});
