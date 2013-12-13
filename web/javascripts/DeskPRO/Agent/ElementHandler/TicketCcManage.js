Orb.createNamespace('DeskPRO.Agent.ElementHandler');

DeskPRO.Agent.ElementHandler.TicketCcManage = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	initPage: function() {
		var self = this;
		var addUrl = this.el.data('add-url');
		this.deleteUrl = this.el.data('delete-url');

		var list = $('ul', this.el).first();
		var newrow = $('li.newrow', this.el);

		this.el.find('li').each(function() {
			self.initRow($(this));
		})

		var addRow = $('.addrow', this.el);
		if (addRow.length) {
			addRow.autoCompleteElement = new DeskPRO.Agent.ElementHandler.SimpleAutoComplete(addRow);

			addRow.on('click', '.cc-saverow-trigger', function(ev) {
				var email = $('input', addRow).val().trim();

				if (!email) {
					return;
				}

				addRow.addClass('loading');

				$.ajax({
					url: addUrl,
					type: 'POST',
					data: { email_address: email },
					dataType: 'json',
					complete: function() {
						addRow.removeClass('loading');
					},
					success: function(data) {
						if (data.error) {
							if (data.error_code == 'invalid_email') {
								DeskPRO_Window.showAlert('Please enter a valid email address');
							} else if (data.error_code == 'invalid_email_gatewayaccount') {
								DeskPRO_Window.showAlert('The email address you entered belongs to a an account in Admin > Tickets > Email Accounts. You cannot add email accounts as CCs.');
							} else if (data.error_code == 'is_agent') {
								DeskPRO_Window.showAlert('The user you specified is an agent. To add an agent to this ticket, use the "Add a follower" button in the Properties box.');
								self.el.closest('.tabViewDetailContent').find('ul.cc-row-list').each(function() {
									$(this).empty().html(data.cc_list || '');
									$(this).find('li').each(function() {
										self.initRow($(this));
									});
								});
							}
							return;
						}

						if (data.is_dupe) {
							DeskPRO_Window.showAlert('The user you specified is already on this ticket.');
							return;
						}

						addRow.find('input').val('');

						self.el.find('ul.cc-row-list').each(function() {
							$(this).empty().html(data.cc_list || '');
							$(this).find('li').each(function() {
								self.initRow($(this));
							});
						});
					}
				});
			});
		}
	},

	initRow: function(row) {
		var self = this;
		row.find('.remove-row-trigger').on('click', function(ev) {
			ev.stopPropagation();
			ev.preventDefault();

			var personId = row.data('person-id');
			var email = row.data('email-address');

			if (personId) {
				$.ajax({
					url: self.deleteUrl,
					type: 'POST',
					data: { person_id: personId },
					dataType: 'json',
					success: function(data) {
						self.el.closest('.tabViewDetailContent').find('ul.cc-row-list').each(function() {
							$(this).empty().html(data.cc_list || '');
							self.el.find('ul.cc-row-list').each(function() {
								$(this).empty().html(data.cc_list || '');
								$(this).find('li').each(function() {
									self.initRow($(this));
								});
							});
						});
					},
					error: function() {
						row.show();
					}
				});

				row.fadeOut('fast');
			}

			row.fadeOut('fast', function() {
				row.remove();
			});
		});
	}
});
