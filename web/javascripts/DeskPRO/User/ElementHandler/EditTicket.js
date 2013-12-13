Orb.createNamespace('DeskPRO.User.ElementHandler');

DeskPRO.User.ElementHandler.EditTicket = new Orb.Class({

	Extends: DeskPRO.User.ElementHandler.ElementHandlerAbstract,

	init: function() {
		this._initFields();
	},

	//#########################################################################
	//# Department and field stuff
	//#########################################################################

	_initFields: function() {
		this.depSelect = $('select.department_id, input.department_id', this.el).first();
		this.departmentId = -1;

		var self = this;
		this.depSelect.on('change', function() {
			self.handleDepChange();
		});
		this.depSelect.data('original-name', this.depSelect.attr('name'));

		$('select.category_id, select.priority_id, select.product_id').on('change', function() {
			self.runChecks();
		});

		this.handleDepChange();
	},

	handleDepChange: function() {
		this.setDepartment(this.depSelect.val());
	},

	setDepartment: function(department_id) {

		if (department_id == this.departmentId) {
			// nochange
			return;
		}

		this.clearAll();

		this.departmentId = department_id;
		var activeDepId = this.departmentId;

		if (!window.DESKPRO_TICKET_DISPLAY) {
			return;
		}
		if (!activeDepId || !window.DESKPRO_TICKET_DISPLAY[activeDepId]) {
			activeDepId = 0;
		}

		var depItems = window.DESKPRO_TICKET_DISPLAY[activeDepId];
		this.depItems = depItems;
		this.depItemsWithChecked = false;

		DP.console.log('depItems %o', depItems);

		$('.ticket-display-field').hide();

		Array.each(depItems, function(item) {
			var itemId = this.getItemId(item);
			var itemEl = $('.' + itemId).closest('.ticket-display-field');

			// Detach and re-attach to correct ordering
			itemEl.detach().appendTo('#fields_container');

			// Turn on criteria-less fields now
			if (!item.check) {
				itemEl.show();
			} else {
				itemEl.addClass('with-criteria');
				this.depItemsWithChecked = true;
			}
		}, this);

		this.runChecksRecursionCount = 0;
		if (this.depItemsWithChecked) {
			this.runChecks();
		}
	},

	runChecks: function() {
		if (this.runChecksRecursionCount > 30) {
			console.error('runChecks running too many times: %o', this.depItems);
			return;
		}

		var self = this;
		var changed = false;
		$('.with-criteria').each(function() {
			var el = $(this);
			var item = self.findItemForEl(el);
			if (!item) return;

			if (item.check(ticketReader)) {
				if (!el.is(':visible')) {
					changed = true;
					el.show();
				}
			} else {
				if (el.is(':visible')) {
					changed = true;
					el.hide();
				}
			}
		});

		if (changed) {
			this.runChecksRecursionCount++;
			this.runChecks();
			this.runChecksRecursionCount--;
		}
	},

	findItemForEl: function(el) {
		var fieldId = el.data('field-id');
		var theitem = null;
		Array.each(this.depItems, function(item) {
			if (item.id == fieldId) {
				theitem = item;
				return false;
			}
		});

		return theitem;
	},

	clearAll: function() {
		$('.ticket-display-field').hide().removeClass('field-enabled with-criteria');
	},

	getItemId: function(item) {
		var itemId = item.field_type;
		if (item.field_id) {
			itemId += '_' + item.field_id;
		}

		return itemId;
	}
});
