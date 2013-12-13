Orb.createNamespace('DeskPRO.Admin.ElementHandler');

DeskPRO.Admin.ElementHandler.CloudEmailGatewayEditAccount = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var moreEmailsSection, addEmailRow;
		var self = this;

		//------------------------------
		// More email addresses section
		//------------------------------

		moreEmailsSection = this.el.find('.more-email-addresses');
		addEmailRow = moreEmailsSection.find('li.add-row');

		this.el.find('.reveal-more-addresses-btn').on('click', function(ev) {
			ev.preventDefault();
			moreEmailsSection.show();
			$(this).hide();
		});

		moreEmailsSection.on('click', '.remove-trigger', function(ev) {
			ev.preventDefault();
			$(this).closest('li').remove();
		});

		moreEmailsSection.find('.add-trigger').on('click', function(ev) {
			ev.preventDefault();
			var input = addEmailRow.find('input');
			var addr = $.trim(input.val());
			input.val('');

			if (!addr) {
				return;
			}

			var row = $('<li/>');
			var rowIn = $('<input type="hidden" name="email_addresses[]" />');
			rowIn.val(addr);
			rowIn.appendTo(row);

			var rowLabel = $('<span/>');
			rowLabel.text(addr);
			rowLabel.appendTo(row);

			var removeBtn = $('<i/>');
			removeBtn.addClass('icon-remove remove-trigger');
			removeBtn.css({
				cursor: 'pointer',
				'margin-left': '4px',
				color: '#999'
			});
			removeBtn.appendTo(row);

			row.insertBefore(addEmailRow);
		});

		//------------------------------
		// Submit save
		//------------------------------

		this.el.find('.save-trigger').on('click', function(ev) {
			ev.preventDefault();
			var formData = self.el.find('input, select, textarea').serializeArray();

			self.el.addClass('loading');
			$.ajax({
				url: self.el.data('save-url'),
				dataType: 'json',
				type: 'POST',
				data: formData,
				complete: function() {
					window.location.reload(false);
				}
			});
		});
	}
});
