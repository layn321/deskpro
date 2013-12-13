Orb.createNamespace('DeskPRO.Admin.ElementHandler');

DeskPRO.Admin.ElementHandler.FeedbackCategoryEditPage = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var self = this;

		$('#delete_btn').on('click', function(ev) {
			ev.preventDefault();
			self.openDeleteOverlay();
		})

		if (window.leaf_ids) {
			$('#feedback_cat_parent').change(function() {
				var val = $(this).val();
				if (window.leaf_ids.indexOf(val) !== -1) {
					$('#move_note').show();
				} else {
					$('#move_note').hide();
				}
			});
		}
	},

	openDeleteOverlay: function() {
		var self = this;
		if (!this.deleteOverlay) {
			this.deleteOverlay = new DeskPRO.UI.Overlay({
				contentElement: $('#delete_cat_overlay')
			});
		}

		this.deleteOverlay.open();
	}
});
