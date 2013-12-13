Orb.createNamespace('DeskPRO.Agent.Ticket');

/**
 * Handles changes to a ticket.
 */
DeskPRO.Agent.Ticket.ChangeManager = new Class({

	Implements: [Events],

	ticketPage: null,
	ticketId: null,
	updateUrl: null,

	mode: 'single',
	oldValues: {},
	changes: {},

	/**
	 * @param {DeskPRO.Agent.PageFragment.Page.Ticket} ticketPage
	 */
	initialize: function(ticketPage) {
		this.ticketPage = ticketPage;
		this.ticketId   = ticketPage.getMetaData('ticket_id');
		this.updateUrl  = ticketPage.getMetaData('saveActionsUrl');
	},

	propertyManagers: {},

	getPropertyManager: function(type, type_id) {

		if (this.propertyManagers[type]) {
			return this.propertyManagers[type];
		}

		var manager = null;
		switch (type) {
		 	case 'category_id':
			case 'product_id':
			case 'workflow_id':
			case 'priority_id':
			case 'language_id':
				manager = new DeskPRO.Agent.Ticket.Property.StandardOption(this.ticketPage, { optionName: type });
				break;
			case 'agent_id':
				manager = new DeskPRO.Agent.Ticket.Property.Agent(this.ticketPage);
				break;
			case 'agent_team_id':
				manager = new DeskPRO.Agent.Ticket.Property.AgentTeam(this.ticketPage);
				break;
			case 'department_id':
				manager = new DeskPRO.Agent.Ticket.Property.Department(this.ticketPage);
				break;
			case 'status':
				manager = new DeskPRO.Agent.Ticket.Property.Status(this.ticketPage);
				break;
			case 'add_labels':
				manager = new DeskPRO.Agent.Ticket.Property.Labels(this.ticketPage, { mode: 'add' });
				break;
			case 'remove_labels':
				manager = new DeskPRO.Agent.Ticket.Property.Labels(this.ticketPage, { mode: 'remove' });
				break;
			case 'flag':
				manager = new DeskPRO.Agent.Ticket.Property.Flag(this.ticketPage);
				break;
			case 'reply':
				manager = new DeskPRO.Agent.Ticket.Property.Reply(this.ticketPage);
				break;
			case 'ticket_field':
				manager = new DeskPRO.Agent.Ticket.Property.TicketField(this.ticketPage, { fieldId: type_id });
				break;
			case 'is_hold':
				manager = new DeskPRO.Agent.Ticket.Property.Hold(this.ticketPage);
				break;
			case 'urgency':
				manager = new DeskPRO.Agent.Ticket.Property.Urgency(this.ticketPage);
				break;
		}

		if (manager === null && type.indexOf('_id') == -1) {
			type += '_id';
			return this.getPropertyManager(type, type_id);
		}

		if (manager === null) {
			return null;
		}

		this.propertyManagers[type] = manager;

		return manager;
	},


	hasChanges: function() {
		if (Object.getLength(this.changes)) {
			return true;
		}

		return false;
	},


	/**
	 * Add a change to the set of changes
	 */
	addChange: function(property, newValue, applyNow) {

		if (property.isSameValue(newValue)) {
			return;
		}

		this.mode = 'multi';
		this.changes[property.getName()] = [property, newValue];

		if (applyNow) {
			this.applyChangeForProperty(property, newValue);
		}
	},



	/**
	 * Apply a certain new value in the interface
	 */
	applyChangeForProperty: function (property, newValue) {
		this.oldValues[property.getName()] = property.getValue();
		property.setValue(newValue);

		if (this.mode == 'multi') {
			property.highlightInterfaceElement();
		}
	},



	/**
	 * Apply all queued changes in the interface
	 */
	applyChanges: function() {

		Object.each(this.changes, function (change) {
			var property = change[0];
			var newValue = change[1];

			this.applyChangeForProperty(property, newValue);
		}, this);

		this.fireEvent('changesApplied', { changes: this.changes });
		window.setTimeout(this.ticketPage.updateUi.bind(this.ticketPage), 450);
	},


	/**
	 * Check if a change is currently in the queue waiting to be saved
	 *
	 * @param type
	 */
	hasChangedProperty: function(type) {
		if (this.changes[type]) {
			return true;
		}

		return false;
	},



	/**
	 * Revert all queuued changes in the interface to their previuos values
	 */
	revertChanges: function() {
		Object.each(this.changes, function (change) {
			var property = change[0];
			var name = property.getName();

			if (this.oldValues[name] !== undefined) {
				property.setValue(this.oldValues[name]);
				property.unhighlightInterfaceElement();
			}

			property.changeReverted();
		}, this);

		this.oldValues = {};

		$('.change-on, .highlight-change-on', this.ticketPage.wrapper).removeClass('change-on').removeClass('highlight-change-on');

		this.mode = 'single';
	},



	/**
	 * Set a property change now, no queueing. This will fall back into
	 * queue mode if we're already in a queued state.
	 */
	setInstantChange: function(property, newValue, callback) {

		// We're already in multi-mode, add this to queue the changes
		if (this.mode == 'multi') {
			this.addChange(property, newValue, true);
			return;
		}

		// Otherwise change and send one
		var oldVal = property.getValue();
		property.setValue(newValue);
		property.changePersisted();

		var data = [];
		this._addPropertyValueToData(data, property.getName(), property.getValue());

		this.fireEvent('changesApplied', { changes: [property, newValue] });

		var classname = 'saving-' + property.getName().replace('.', '_');
		this.ticketPage.wrapper.addClass(classname);

		(function() {
			this.ticketPage.wrapper.removeClass(classname);
		}).delay(650, this);

		var self = this;

		if (this.updateUrl) {
			DeskPRO_Window.util.ajaxWithClientMessages({
				type: 'POST',
				url: this.updateUrl,
				data: data,
				dataType: 'json',
				context: this,
				success: function(data) {

					if (data.error_messages) {
						property.setValue(oldVal);

						var list = self.ticketPage.getEl('field_errors').find('ul').empty();
						Array.each(data.error_messages, function(msg) {
							var li = $('<li/>');
							li.text(msg);
							li.appendTo(list);
						});

						self.ticketPage.getEl('field_errors').show().addClass('on');

						self.ticketPage.getEl('field_edit_start').click();
						self.ticketPage.getEl('field_edit_cancel').show();
						self.ticketPage.getEl('field_edit_save').show();
						self.ticketPage.getEl('field_edit_controls').removeClass('loading');
						return;
					}

					if (data.data && data.data.perm_errors) {
						var div = $('<div/>');
						div.append('<strong>You do not have permission to change some fields. The following changes were not saved:</strong>');

						var list = $('<ul />');
						list.appendTo(div);

						Array.each(data.data.perm_errors, function(err) {
							var li = $('<li/>');
							li.text(err.capitalize());
							li.appendTo(list);
						});

						DeskPRO_Window.showAlert(div);
					}

					if (callback) {
						callback(data);
					}

					this.fireEvent('updateResult', [data]);
				}
			});
		}

		window.setTimeout(this.ticketPage.updateUi.bind(this.ticketPage), 450);
	},


	/**
	 * Save the changes for all queued items
	 */
	saveChanges: function(data, callback) {
		data = data || [];

		var saving_classes = [];

		var saveReply = false;

		Object.each(this.changes, function (change) {
			var property = change[0];
			var name = property.getName();

			// Reply is made through the ReplyBox helper instead
			if (name == 'reply') {
				property.unhighlightInterfaceElement();
				saveReply = true;
				return;
			}

			var classname = 'saving-' + name.replace('.', '_');
			saving_classes.push(classname);
			this.ticketPage.wrapper.addClass(classname);

			if (!property.isDisplayOnly()) {
				this._addPropertyValueToData(data, property.getName(), property.getValue());
			}
			property.unhighlightInterfaceElement();
			property.changePersisted();
		}, this);

		this.mode = 'single';

		(function() {
			var classname = '';
			while (classname = saving_classes.pop()) {
				this.ticketPage.wrapper.removeClass(classname);
			}
		}).delay(650, this);

		var self = this;

		if (this.updateUrl) {
			DeskPRO_Window.util.ajaxWithClientMessages({
				type: 'POST',
				url: this.updateUrl,
				data: data,
				dataType: 'json',
				context: this,
				success: function(data) {

					if (data.error_messages) {

						var list = self.ticketPage.getEl('field_errors').find('ul').empty();
						Array.each(data.error_messages, function(msg) {
							var li = $('<li/>');
							li.text(msg);
							li.appendTo(list);
						});

						self.ticketPage.getEl('field_errors').show().addClass('on');

						self.ticketPage.getEl('field_edit_cancel').show();
						self.ticketPage.getEl('field_edit_save').show();
						self.ticketPage.getEl('field_edit_controls').removeClass('loading');
						return;
					}

					self.ticketPage.wrapper.removeClass('field-error');

					this.changes = {};
					this.oldValues = {};

					if (data && data.properties) {
						Object.each(data, function (returnValue, type) {
							var property = this.getPropertyManager(type);
							property.setIncomingValue(returnValue);
						}, this);
					}

					if (saveReply) {
						this.ticketPage.getEl('replybox_wrap').find('.ticket-reply-form').first().data('handler').getElById('send_btn').click();
					}

					if (data.data && data.data.perm_errors) {
						var div = $('<div/>');
						div.append('<strong>You do not have permission to change some fields. The following changes were not saved:</strong>');

						var list = $('<ul />');
						list.appendTo(div);

						Array.each(data.data.perm_errors, function(err) {
							var li = $('<li/>');
							li.text(err.capitalize());
							li.appendTo(list);
						});

						DeskPRO_Window.showAlert(div);
					}

					if (callback) {
						callback(data);
					}

					this.fireEvent('updateResult', [data]);
				}
			});
		}
	},

	_addPropertyValueToData: function(data, name, propertyValue) {

		// An array of items
		if (typeOf(propertyValue) == 'array') {
			for (var x = 0; x < propertyValue.length; x++) {
				var val = propertyValue[x];

				// Specific name means its taking care of the actions[] array prefix itself,
				// used when theres a composite field like status/hidden_status
				if (typeOf(val) == 'object' && val.full_name !== undefined) {
					data.push({
						name: val.full_name,
						value: val.value
					});

				// Looks like its already a k:v like from serializeArray
				} else if (typeOf(val) == 'object' && val.name !== undefined) {
					data.push({
						name: 'actions['+name+']['+val.name+']',
						value: val.value
					});

				// We'll just make it an array of values then
				} else {
					data.push({
						name: 'actions['+name+'][]',
						value: val
					});
				}
			}

		// A k:v pair of items
		} else if (typeOf(propertyValue) == 'object') {
			Object.each(propertyValue, function(v, k) {
				data.push({
					name: 'actions['+name+']['+k+']',
					value: v
				});
			}, this);

		// A single value
		} else {
			data.push({
				name: 'actions['+name+']',
				value: propertyValue
			});
		}
	},



	/**
	 * Called when we detect if a value was updated automatically from somewhere.
	 */
	setPropertyUpdated: function(property, newValue) {
		if (typeOf(property) == 'string') {
			property = this.getPropertyManager(property);
		}

		property.setIncomingValue(newValue);
		property.pulseInterfaceElement();
	}
});
