Orb.createNamespace('DeskPRO.Admin.PageHandler');

DeskPRO.Admin.PageHandler.SettingsLabels = new Class({

	Extends: DeskPRO.Admin.PageHandler.Basic,

	createLi: null,

	initPage: function() {
		this.createLi = $('li.create:first');
		$('button.save-trigger', this.createLi).on('click', this.saveLabel.bind(this));

		// Delete button for each label
		var self = this;
		$('ul.item-list').on('click', 'li.delete-trigger', function(ev) {
			ev.preventDefault();
			self.deleteLabel($(this).parent().parent().parent());
		}).on('click', 'li.rename-trigger', function(ev) {
			ev.preventDefault();
			self.renameLabel($(this).parent().parent().parent());
		});

		$('#rename_label_overlay button.save-trigger').on('click', this.doRenameLabel.bind(this));

		this.renameOverlay = new DeskPRO.UI.Overlay({
			contentElement: $('#rename_label_overlay')
		});
	},

	saveLabel: function() {
		var label = $('input[name="label"]', this.createLi).val().trim();
		if (!label.length) {
			return;
		}

		$('button.save-trigger', this.createLi).html('...');

		$.ajax({
			url: this.getMetaData('newLabelUrl'),
			type: 'POST',
			context: this,
			data: {'label': label},
			dataType: 'json',
			success: function(data) {
				this._handleSaveLabelSuccess(data);
			}
		});
	},

	renameLabel: function(li) {
		var label = li.data('label');

		$('#rename_label_overlay .old-label').text(label);
		$('#rename_label_overlay .new-label').val('');

		this.renameOverlay.openOverlay();
	},

	doRenameLabel: function() {
		var old_label = $('#rename_label_overlay .old-label').text();
		var new_label = $('#rename_label_overlay .new-label').val();

		var url = $('#rename_label_overlay .rename-url').val();
		url = url.replace('{old_label}', escape(old_label));
		url = url.replace('{new_label}', escape(new_label));

		window.location = url;
	},

	_handleSaveLabelSuccess: function(data) {

		$('button.save-trigger', this.createLi).html('Create New Label');

		if (data.errorMessage) {
			alert(data.errorMessage);
			return;
		}

		$(data.html).hide().insertAfter(this.createLi).slideDown();
		$('input[name="label"]', this.createLi).val('');
	},

	deleteLabel: function(li) {
		if (confirm('Are you sure you want to delete this label?')) {
			this._deleteLabel(li);
		}
	},

	_deleteLabel: function(li) {
		var label = li.data('label');
		$.ajax({
			url: this.getMetaData('delLabelUrl'),
			type: 'POST',
			context: this,
			data: {'label': label},
			dataType: 'json',
			success: function(data) {

			}
		});

		li.slideUp(function() {
			li.remove();
		});
	}
});
