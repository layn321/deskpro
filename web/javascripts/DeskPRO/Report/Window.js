Orb.createNamespace('DeskPRO.Report');

DeskPRO.Report.Window = new Orb.Class({
	Extends: DeskPRO.Admin.Window,

	initPage: function() {
		this.parent();
		$('#dp_admin_nav ul').sortable({
			axis: 'x',
			forceHelperSize: true,
			appendTo: 'body',
			items: 'li.dashboard',
			update: function() {
				var postData = [];
				$('#dp_admin_nav').find('li.dashboard').each(function() {
					postData.push({
						name: 'dashboard_ids[]',
						value: $(this).data('dashboard-id')
					});
				});

				$.ajax({
					url: $('#dp_admin_nav ul').data('update-orders-url'),
					data: postData,
					type: 'POST',
					dataType: 'json'
				});
			},
			helper: function(ev, el) {
				var helper = $('<div class="dashboard-drag-helper"><span></span></div>');
				helper.find('span').text(el.text().trim());

				return helper;
			}
		});

		var menu = new DeskPRO.UI.Menu({
			triggerElement: '#all_trends_menu_trigger',
			menuElement: '#all_trends_menu'
		});
	},

	/**
	 * Get a URL pattern
	 */
	getUrl: function(name, vars) {
		if (!window.DESKPRO_URL_REGISTRY[name]) {
			DP.console.error('Unknown url name %s', name);
			return null;
		}

		var url = window.DESKPRO_URL_REGISTRY[name];
		if (vars) {
			Object.each(vars, function(v,k) {
				url = url.replace('{'+k+'}', v);
			});
		}

		return url;
	}
});
