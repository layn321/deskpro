Orb.createNamespace('DeskPRO.Admin.ElementHandler');

DeskPRO.Admin.ElementHandler.FeedbackStatusListPage = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var self = this;

		$('#create_status_btn').on('click', function(ev) {
			ev.preventDefault();
			self.openNewOverlay();
		})
	},

	openNewOverlay: function() {
		var self = this;
		if (!this.newOverlay) {
			this.newOverlay = new DeskPRO.UI.Overlay({
				contentElement: $('#create_status_overlay'),
				onOverlayClosed: function() {
					$('#create_status_overlay input[name="cat[title]"]').val('');
				}
			});

			$('#create_status_save_btn').on('click', function(ev) {
				var data = $('#create_status_overlay').find('input, select').serializeArray();
				var url = $(this).data('save-url');

				var type = $('#create_status_overlay select[name="cat[status_type]"]').val();

				var btn = $(this);
				var btnText = btn.text();
				btn.text('...');

				$.ajax({
					url: url,
					data: data,
					type: 'POST',
					dataType: 'html'
				}).done(function(row) {
					var row = $(row);
					var table = $('#' + type + '_statuses_table');
					table.append(row);

					self.newOverlay.close();
					btn.text(btnText);
				});
			});
		}

		this.newOverlay.open();
	}
});
