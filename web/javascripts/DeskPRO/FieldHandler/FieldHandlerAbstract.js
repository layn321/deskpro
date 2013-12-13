Orb.createNamespace('DeskPRO.FieldHandler');

/**
 * A base class for custom field handlers.
 *
 * Extend and write your own initPage() that runs when the page
 * has been initiated and the form elements are ready.
 *
 * Remember there are two elemets per item, first is the actual
 * form field, and the second is the rendered state.
 * Both of these fields are available on the agent side.
 * On the user side, display is on viewticket and form is on
 * editticket.
 *
 * You can use this.pageObj to check which page you're currently on.
 *
 * <code>
 * if (this.pageObj && this.pageObj.ZONE && this.pageObj.ZONE == 'agent') {
 *     // agent interface
 * }
 * </code>
 *
 * You'll need to understand pageObj (for tickets it's DeskPRO.Agent.PageFragment.Page.Ticket)
 * in order to do things like save fields via JS, because you'll have to send the proper AJAX
 * requests etc.
 */
DeskPRO.FieldHandler.FieldHandlerAbstract = new Orb.Class({
	Implements: [Orb.Util.Events],

	initialize: function(form_wrapper_id, rendered_wrapper_id, page_obj) {
		this.form_wrapper_id     = form_wrapper_id;
		this.rendered_wrapper_id = rendered_wrapper_id;
		this.pageObj = page_obj;

		this.initObj();
	},

	initializePage: function() {
		this.form_wrapper     = $(this.form_wrapper_id);
		this.rendered_wrapper = $(this.rendered_wrapper_id);

		this.initPage();
	},

	initObj: function() {},

	initPage: function() {}
});