Orb.createNamespace('DeskPRO.Admin.ElementHandler');

DeskPRO.Admin.ElementHandler.EmailBanningPage = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var self = this;
	},

	initPage: function() {
		var self = this;

		this.newEmailOverlay = new DeskPRO.UI.Overlay({
			triggerElement: $('#new_email_ban_btn'),
			contentElement: $('#email_ban_overlay'),
			onBeforeOverlayOpened: function() {
				$('#email_ban_overlay input[name="email"]').val('');
			}
		});
		$('#email_ban_overlay button.save-trigger').on('click', this.doNewEmail.bind(this));

		$('#dp_admin_page').on('click', '.delete-trigger', function(ev) {
			ev.preventDefault();
			self.doDeleteEmail($(this).closest('li.item-row'));
		});

		var toucher = new DeskPRO.TouchCaller({
			timeout: 500,
			context: this,
			callback: function() {
				var filter = $('#filter_list').val().trim();
				if (!filter.length) {
					$('#email_rows > li').show();
				} else {
					$('#email_rows > li').hide();
					$('#email_rows > li[data-email*="' + filter + '"]').show();
				}
			}
		});

		$('#filter_list').on('change, keyup', function() {
			toucher.touch();
		});
	},

	doNewEmail: function() {

		var url = $('#email_ban_overlay .save-url').val();
		url = url.replace('{email}', $('#email_ban_overlay input[name="email"]').val());

		this.newEmailOverlay.closeOverlay();

		$.ajax({
			url: url,
			type: 'POST',
			context: this,
			dataType: 'html',
			success: function(html) {
				var row = $(html).hide();
				row.prependTo($('#email_rows')).fadeIn('fast');

				DeskPRO_Window.util.modCountEl($('#ban_count_emails'), '+');
			}
		});
	},

	doDeleteEmail: function(row) {
		var url = $('.delete-trigger', row).data('delete-url');

		$.ajax({
			url: url,
			type: 'POST',
			context: this,
			dataType: 'json'
		}).done(function() {
			row.fadeOut('fast', function() { row.remove(); });
			DeskPRO_Window.util.modCountEl($('#ban_count_emails'), '-');
		});
	}
});
