Orb.createNamespace('DeskPRO.Agent.Widget');

DeskPRO.Agent.Widget.TicketChangeUser = new Orb.Class({
	Implements: [Orb.Util.Options, Orb.Util.Events],

	initialize: function(options) {
		this.options = {
			ticketId: 0,
			destroyOnClose: false
		};

		this.setOptions(options);

		this.ticketId = this.options.ticketId;

		this.overlay = null;
	},

	_initOverlay: function() {
		if (this.overlay) return this.overlay;

		var data = [];
		var self = this;

		this.overlay = new DeskPRO.UI.Overlay({
			contentMethod: 'ajax',
			contentAjax: {
				url: BASE_URL + 'agent/tickets/' + this.ticketId + '/change-user-overlay',
				data: data
			},
			onAjaxDone: function() {
				self.wrapper = self.overlay.elements.wrapper;
				DeskPRO_Window.initInterfaceLayerEvents(self.wrapper);

				var searchbox = self.wrapper.find('.person-finder');
				var submitRow = self.wrapper.find('.submit-row');
				var newUserFields = self.wrapper.find('.new-user-fields');

				self.wrapper.on('click', 'button.change-user-trigger', function(ev) {
					ev.preventDefault();
					ev.stopPropagation();

					var data = [];

					var personId = $(this).data('person-id');
					if (personId && personId != '0') {
						data.push({name: 'new_person_id', value: personId});
					} else {
						data.push({name: 'email', value: newUserFields.find('.email').val()});
						data.push({name: 'name', value: newUserFields.find('.name').val()});
					}

					var keepParticipant = self.wrapper.find('.participant-check').is(':checked');
					data.push({name: 'keep', value: (keepParticipant ? 1 : 0)});

					console.log(data);

					$.ajax({
						url: BASE_URL + 'agent/tickets/' + self.ticketId + '/change-user',
						type: 'POST',
						data: data,
						dataType: 'json',
						success: function(data) {
							if (!data.success) {
								if (data.error) {
									DeskPRO_Window.showAlert(data.error);
								}
								return;
							}
							self.fireEvent('success', [data]);
						}
					});
				});

				searchbox.bind('personsearchboxclick', function(ev, personId, name, email, sb) {
					sb.close();

					$.ajax({
						url: BASE_URL + 'agent/tickets/' + self.ticketId + '/change-user-overlay/preview/' + personId,
						type: 'get',
						dataType: 'html',
						success: function(html) {
							self.wrapper.find('button.change-user-trigger').data('person-id', personId);
							self.wrapper.find('.person-preview-content').html(html);
							newUserFields.hide();
							submitRow.show();
						}
					});
				});

				searchbox.bind('personsearchboxclicknew personsearchenter', function(ev, term, sb) {
					sb.close();
					sb.reset();

					if (term.indexOf('@') !== -1) {
						$('input.email', newUserFields).val(term);
					} else {
						$('input.name', newUserFields).val(term);
					}

					self.wrapper.find('.person-preview-content').empty();
					self.wrapper.find('button.change-user-trigger').data('person-id', false);
					newUserFields.show();
					submitRow.show();
				});
			}
		});

		this.overlay.addEvent('ajaxDone', this._initElements.bind(this));
	},

	_initElements: function() {
		this.wrapper = this.overlay.getWrapper();
		var self = this;


	},

	open: function() {
		this._initOverlay();
		this.overlay.open();
	},

	close: function() {
		this.overlay.close();

		if (this.options.destroyOnClose) {
			this.desotry
		}
	},

	destroy: function() {
		if (this.overlay) {
			this.overlay.destroy();
		}
	}
});
