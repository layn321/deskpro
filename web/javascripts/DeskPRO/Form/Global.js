Orb.createNamespace('DeskPRO.Form');

/**
 * Global form handling code like initialization etc
 */
DeskPRO.Form.Global = new Class({
	initialize: function() {

		var self = this;
		$('.deskpro-repeatable-field').each(function() {
			self.setupRepeatableField(this);
		});
	},

	setupRepeatableField: function(field) {

		var tpl_html = $('.deskpro-repeatable-field-tpl', field).val();

		$('.deskpro-repeatable-field-addbtn', field).on('click', function() {
			var html = tpl_html.replace(/new_ID/, 'new_' + Orb.uuid());
			$('.deskpro-repeatable-field-wrap', field).append(html);
		});
	}
});
