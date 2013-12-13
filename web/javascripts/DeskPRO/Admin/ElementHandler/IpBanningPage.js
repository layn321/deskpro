Orb.createNamespace('DeskPRO.Admin.ElementHandler');

DeskPRO.Admin.ElementHandler.IpBanningPage = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var self = this;
	},

	initPage: function() {
		var self = this;

		this.newIpOverlay = new DeskPRO.UI.Overlay({
			triggerElement: $('#new_ip_ban_btn'),
			contentElement: $('#ip_ban_overlay'),
			onBeforeOverlayOpened: function() {
				$('#ip_ban_overlay input[name="ip"]').val('');
			}
		});
		$('#ip_ban_overlay button.save-trigger').on('click', this.doNewIp.bind(this));

		$('#ip_rows').on('click', '.delete-trigger', function(ev) {
			ev.preventDefault();
			self.doDeleteIp($(this).closest('li.item-row'));
		});

		var toucher = new DeskPRO.TouchCaller({
			timeout: 500,
			context: this,
			callback: function() {
				var filter = $('#filter_list').val().trim();
				if (!filter.length) {
					$('#ip_rows > li').show();
				} else {
					$('#ip_rows > li').hide();
					$('#ip_rows > li[data-ip*="' + filter + '"]').show();
				}
			}
		});

		$('#filter_list').on('change, keyup', function() {
			toucher.touch();
		});
	},

	doNewIp: function() {
		var url = $('#ip_ban_overlay .save-url').val();
		url = url.replace('{ip}', $('#ip_ban_overlay input[name="ip"]').val());

		this.newIpOverlay.closeOverlay();

		$.ajax({
			url: url,
			type: 'POST',
			context: this,
			dataType: 'html',
			success: function(html) {
				var row = $(html).hide();
				row.prependTo($('#ip_rows')).fadeIn('fast');

				DeskPRO_Window.util.modCountEl($('#ban_count_ips'), '+');
			}
		});
	},

	doDeleteIp: function(row) {
		var url = $('.delete-trigger', row).data('delete-url');

		$.ajax({
			url: url,
			type: 'POST',
			context: this,
			dataType: 'json'
		}).done(function() {
			row.fadeOut('fast', function() { row.remove(); });
			DeskPRO_Window.util.modCountEl($('#ban_count_ips'), '-');
		});
	}
});
