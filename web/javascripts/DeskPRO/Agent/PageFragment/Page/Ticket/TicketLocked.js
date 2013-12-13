Orb.createNamespace('DeskPRO.Agent.PageFragment.Page.Ticket');

/**
 * Overlays content so you cant do anything until dismissed
 */
DeskPRO.Agent.PageFragment.Page.Ticket.TicketLocked = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(page, options) {
		this.page = page;
		this.options = {};

		this.setOptions(options);

		this.lockedBar = this.page.getEl('locked_bar');
		this.lockedOverlay = this.page.getEl('locked_overlay');
		this.dismissBtn = $('button.dismiss', this.lockedBar);

		this.dismissBtn.on('click', this.dismiss.bind(this));
	},

	/**
	 * The user dismisses the message
	 */
	dismiss: function() {
		var wrapper = $('.page-ticket:first', this.page.wrapper);

		var self = this;
		this.lockedOverlay.fadeOut('fast');
		this.lockedBar.fadeOut('fast', function() {
			wrapper.removeClass('locked');
		});
	},

	/**
	 * The ticket becomes unlocked while we're viewing
	 */
	unlock: function() {
		// same as dismiss right now
		this.dismiss();
	},

	destroy: function() {

	}
});
