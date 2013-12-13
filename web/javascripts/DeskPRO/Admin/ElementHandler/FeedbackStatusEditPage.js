Orb.createNamespace('DeskPRO.Admin.ElementHandler');

DeskPRO.Admin.ElementHandler.FeedbackStatusEditPage = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var self = this;

		$('#delete_btn').on('click', function(ev) {
			ev.preventDefault();
			self.openDeleteOverlay();
		})
	},

	openDeleteOverlay: function() {
		var self = this;
		if (!this.deleteOverlay) {
			this.deleteOverlay = new DeskPRO.UI.Overlay({
				contentElement: $('#delete_status_overlay')
			});
		}

		this.deleteOverlay.open();
	}
});
