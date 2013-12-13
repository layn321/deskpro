Orb.createNamespace('DeskPRO.Admin.PageHandler');

DeskPRO.Admin.PageHandler.SettingsAdvanced = new Class({
	Extends: DeskPRO.Admin.PageHandler.Basic,

	initPage: function() {
		this.filterBox = $('#settings_filter');
		this.filterUpdateTimeout = null;
		this.filterBox.on('keypress', this.handleFilterChange, this);

		this.settingsRows = $('#settings_rows');

		var self = this;
		$('input.dp-set-value', this.settingsRows).on('change', function() {
			self.updateSettingFromInput($(this));
		}).on('keydown', function(ev) {
			if (ev.keyCode == 13) { // enter saves
				ev.preventDefault();
				$(this).blur();
			}
		});

		$(document).on('click', '.revert', function() {
			var row = $(this).closest('tr');
			self.revertSetting(row)
			self.updateSettingFromInput(row.find('input.dp-set-value'));
		});

		var revertOverlay = new DeskPRO.UI.Overlay({
			contentElement: '#revert_all_warn',
			triggerElement: '#revert_all_btn'
		});
	},

	handleFilterChange: function() {

		if (this.fitlerUpdateTimeout) {
			window.clearTimeout(this.fitlerUpdateTimeout);
		}

		if (this.filterBox.val().trim() === '') {
			return;
		}

		this.fitlerUpdateTimeout = window.setTimeout(this.doFilterUpdate.bind(this), 1000);
	},

	doFilterUpdate: function() {
		var find = this.filterBox.val().trim();

		$('tr', this.settingsRows).each(function() {
			var el = $(this);

			if (el.html().indexOf(find) === -1) {
				el.hide();
			} else {
				el.show();
			}
		});
	},

	updateSettingFromInput: function(input) {
		var value = input.val().trim();
		var name = input.data('setting-name');

		$.ajax({
			url: BASE_URL + 'admin/settings/advanced-set/' + name,
			data: {value: value},
			dataType: 'json',
			type: 'POST',
			success: function() {
				DeskPRO_Window.util.showSavePuff(input);

				var row = input.closest('tr');
				if (row.find('input.dp-set-default').val() == value) {
					input.closest('tr').removeClass('changed');
				} else {
					input.closest('tr').addClass('changed');
				}
			}
		});
	},

	revertSetting: function(row) {
		row.find('input.dp-set-value').val(row.find('input.dp-set-default').val());
	}
});
