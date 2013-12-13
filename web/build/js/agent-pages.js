Orb.createNamespace('DeskPRO.Agent.PageHelper');

DeskPRO.Agent.PageHelper.NewUserOverlay = new Class({

	Implements: [Options, Events],

	options: {
		contentEl: 'div.new-user-overlay',
		context: document,
		saveUrl: null,
		zIndex: 30001
	},

	contentEl: null,
	overlay: null,

	initialize: function(options) {
		if (options) this.setOptions(options);

		this.contentEl = this.options.contentEl;
		if (typeOf(this.contentEl) == 'string') {
			this.contentEl = $(this.contentEl, this.context);
		}

		Orb.Compat.WebForms.placeholder($('input[name="person\[first_name\]"]', this.contentEl));
		Orb.Compat.WebForms.placeholder($('input[name="person\[last_name\]"]', this.contentEl));
	},

	open: function() {
		this._initOverlay();
		this.overlay.openOverlay();
	},

	_hasInit: false,
	_initOverlay: function() {
		if (this._hasInit) return;
		this._hasInit = true;

		this.overlay = new DeskPRO.UI.Overlay({
			contentElement: this.contentEl
		});

		$('button.save-trigger', this.contentEl).on('click', this._handleSave.bind(this));
	},

	_handleSave: function() {
		var els = this.getFormElements();
		var data = els.serializeArray();

		var eventData = {
			formData: data,
			contentEl: this.contentEl,
			overlay: this.overlay,
			cancel: false
		};

		this.fireEvent('beforeSave', eventData);
		if (eventData.cancel) return;

		$.ajax({
			url: this.options.saveUrl,
			data: data,
			type: 'POST',
			dataType: 'json',
			success: this._handleSaveSuccess.bind(this)
		});
	},

	_handleSaveSuccess: function(data) {


		var eventData = {
			contentEl: this.contentEl,
			overlay: this.overlay,
			data: data
		};

		if (data.isError) {
			$('.error-message', this.contentEl).html(data.errorMessage).show();
			return;
		}

		// Reset incase there's another one
		this._clear();

		this.fireEvent('afterSave', eventData);
	},

	_clear: function() {
		$('.error-message', this.contentEl).hide();
		//this.getFormElements.val('');
	},

	getFormElements: function() {
		var els = $(':input', this.contentEl);
		return els;
	},

	destroy: function() {
		if (this.overlay) {
			this.overlay.destroy();
		} else {
			this.contentEl.remove();
		}
	}
});

Orb.createNamespace('DeskPRO.Agent.PageHelper');

DeskPRO.Agent.PageHelper.ListColDrag = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(options) {
		this.options = {
			table: null,
			onlyRowSel: null,
			onlyRowColOffset: 0
		};
		this.setOptions(options || {});

		this.moveIndicator = $('<div class="col-move"></div>').appendTo('body');

		this.table = $(this.options.table);

		this.resetHeads();

		var self = this;

		this.heads.draggable({
			axis: 'x',
			helper: function(event, ui) {
				var el = $('<div style="background-color:#EEF1F5;padding:3px;border: 1px solid #CAD0D7;" />').text($(this).text());

				return el;
			},
			start: function(event, ui) {
				event.stopPropagation();
				var index = $(this).index();
				$(this).closest('table').data('drag_col_index', index);
			},
			stop: function() {
				self.moveIndicator.hide();
			},
			scope: 'col-drag'
		});

		this.dropHeads.droppable({
			scope: 'col-drag',
			over: function(event, ui) {
				var index = $(this).index();
				var pos = $(this).offset();
				var w = $(this).width();

				var leftPos = pos.left + w;

				self.moveIndicator.css({
					left: leftPos,
					top: pos.top - self.moveIndicator.height(),
					display: 'block'
				});
			},
			out: function(event, ui) {
				//self.moveIndicator.hide();
			},
			drop: function(event, ui) {

				self.moveIndicator.hide();

				var orig_index = self.table.data('drag_col_index');
				self.table.data('drag_col_index', 'null');

				var new_index = $(this).index();

				if (new_index == orig_index) {
					return; // not moving anywhere!
				}

				if (self.options.onlyRowSel) {
					var rows = $(self.options.onlyRowSel, self.table);
				} else {
					var rows = $('tr', self.table);
				}

				rows = rows.filter(':not(.is-head)');

				var method = 'after';
				if (self.options.onlyRowColOffset) {
					orig_index -= self.options.onlyRowColOffset;
					new_index  -= self.options.onlyRowColOffset

					// Our little hack ot allow "before"
					if (new_index < 0) {
						new_index = 0;
						method = 'before';
					}
				}

				// Move the header
				var dragCell = $(ui.draggable);
				var posCell = $(this);
				dragCell.detach().insertAfter(posCell);

				// Move each row
				rows.each(function(row_index, row) {
					var dragCell = $(row).find('td').eq(orig_index);
					var posCell = $(row).find('td').eq(new_index);

					if (method == 'before') {
						dragCell.detach().insertBefore(posCell);
					} else {
						dragCell.detach().insertAfter(posCell);
					}
				});

				self.resetHeads();

				self.fireEvent('orderChanged', [self.heads, this]);
			}
		});
	},

	resetHeads: function() {
		var r = $('> thead:first > tr:first', this.table);
		r.addClass('is-head');

		$('.not-droppable').removeClass('not-droppable');
		$('.not-draggable').addClass('not-droppable');

		this.heads = $('th:not(.not-draggable), td:not(.not-draggable)', r);
		this.heads.eq(0).prev().addClass('first-drop-target').removeClass('not-droppable');

		this.dropHeads = $('th:not(.not-droppable), td:not(.not-droppable)', r);
	}
});
Orb.createNamespace('DeskPRO.Agent.PageHelper');

DeskPRO.Agent.PageHelper.ListColResize = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(options) {
		this.options = {
			table: null
		};
		this.setOptions(options || {});

		this.table = $(this.options.table);

		var r = $('> thead:first > tr:first', this.table);
		var heads = $('> td:not(.not-resizable), > th:not(.not-resizable)', r);

		$('td, th', r).each(function() {
			$(this).css({
				width: $(this).width()
			});
		});

		this.table.css('table-layout', 'fixed');

		var self = this;
		heads.each(function() {
			var resizer = $('<div class="col-resizer"></div>');
			var el = $(this);
			el.prepend(resizer);

			resizer.on('mousedown', function(ev) {
				ev.stopPropagation();
			});

			resizer.draggable({
				helper: function() {
					return $('<div class="col-resizer-helper"></div>');
				},
				cursorAt: { left: 5 },
				axis: 'x',
				start: function(event, ui) {
					event.stopPropagation();
				},
				stop: function(event, ui) {

					var pos = el.offset();
					var helperPos = ui.offset;

					var width = helperPos.left - pos.left;

					DP.console.log('%i %i', el.width(), width);
					el.css('width', width);

					self.fireEvent('widthUpdated', [heads, self])
				}
			});
		});
	}
});

Orb.createNamespace('DeskPRO.Agent.PageHelper');

/**
 * Handles updating display based on department and rules
 */
DeskPRO.Agent.PageHelper.TicketFields = new Orb.Class({
	initialize: function(page) {
		var self = this;
		this.page = page;
		this.display = this.page.getEl('field_holders').find('.field-holders-table');

		this.mode = 'view';
		this.currentDisplay = [];
		this.currentDisplayModify = [];

		this.ticketReader = {
			getDepartmentId: function() {
				if (self.mode == 'edit') {
					var catId = self.page.getEl('department_id').val();
				} else {
					var catId = self.page.getEl('value_form').find('.department_id').val();
				}
				return parseInt(catId) || 0;
			},
			getCategoryId: function() {
				var catId = null;
				if (self.mode == 'edit') {
					catId = self.page.getEl('ticket_category_id').val();
				}
				if (typeof catId == 'undefined' || catId === null) {
					catId = self.page.getEl('value_form').find('.category_id').val();
				}
				return parseInt(catId) || 0;
			},
			getPriorityVal: function() {
				var id = this.getPriorityId();
				if (!id) {
					return -999999999;
				}

				if (!window.DESKPRO_TICKET_PRI_MAP || !window.DESKPRO_TICKET_PRI_MAP[id]) {
					return 0;
				}

				return parseInt(window.DESKPRO_TICKET_PRI_MAP[id]);
			},
			getPriorityId: function() {
				var catId = null;
				if (self.mode == 'edit') {
					catId = self.page.getEl('ticket_priority_id').val();
				}

				if (typeof catId == 'undefined' || catId === null) {
					catId = self.page.getEl('value_form').find('.priority_id').val();
				}
				return parseInt(catId) || 0;
			},
			getProductId: function() {
				var catId = null;
				if (self.mode == 'edit') {
					catId = self.page.getEl('ticket_product_id').val();
				}
				if (typeof catId == 'undefined' || catId === null) {
					catId = self.page.getEl('value_form').find('.product_id').val();
				}
				return parseInt(catId) || 0;
			},
			getOrganizationId: function() {
				return 0;
			},
			getWorkflow: function() {
				var catId = null;
				if (self.mode == 'edit') {
					catId = self.page.getEl('value_form').find('.workflow_id').val();
				}
				if (typeof catId == 'undefined' || catId === null) {
					catId = self.page.getEl('ticket_workflow_id').val();
				}
				return parseInt(catId) || 0;
			}
		};

		this.fieldDisplay = new DeskPRO.Agent.PageHelper.TicketFieldDisplay(this.ticketReader, 'view');
		this.fieldDisplayModify = new DeskPRO.Agent.PageHelper.TicketFieldDisplay(this.ticketReader, 'modify');

		this.page.getEl('department').on('change', function() {
			self.updateDisplay();
		});

		this.page.getEl('field_edit_start').on('click', function(ev) {
			ev.preventDefault();
			self.openEditMode();
		});

		self.page.getEl('field_edit_cancel').on('click', function(ev) {
			ev.preventDefault();
			self.closeEditMode();
		});

		self.page.getEl('field_edit_save').on('click', function(ev) {
			self.page.getEl('field_edit_cancel').hide();
			self.page.getEl('field_edit_save').hide();
			self.page.getEl('field_edit_start').hide();
			self.page.getEl('field_edit_controls').addClass('loading');

			self.page.getEl('field_errors').hide().removeClass('on');

			self.saveChanges();
		});

		this.page.changeManager.addEvent('updateResult', function(data) {
			if (data.holders) {
				if (self.mode == 'view') {
					self.replaceHolders(data.holders);
				}
			}
		});
	},

	openEditMode: function() {
		this.mode = 'edit';

		this.display.addClass('mode-edit-on');
		this.page.getEl('field_edit_start').hide();
		this.page.getEl('field_edit_cancel').show();
		this.page.getEl('field_edit_save').show();
		this.page.getEl('field_edit_controls').removeClass('loading');

		this.display.find('select[multiple]').each(function() {
			var min = $(this).width() + 30;
			var parent = $(this).closest('td').find('> div').first().width();
			if (parent) {
				min = Math.max(min, Math.ceil(parent / 1.75));
			}
			$(this).width(min);
		});
		DP.select(this.display.find('select'));

		this.updateDisplay();

		$('.Date.customfield input', this.display).datepicker({
			dateFormat: 'yy-mm-dd',
			showButtonPanel: true,
			beforeShow: function(input) {
				setTimeout(function() {
					var buttonPane = $(input).datepicker("widget").find(".ui-datepicker-buttonpane");

					buttonPane.find('button:first').remove();

					var btn = $('<button class="ui-datepicker-current ui-state-default ui-priority-secondary ui-corner-all" type="button">Clear</button>');
					btn.unbind("click").bind("click", function () { $.datepicker._clearDate( input ); });
					btn.appendTo( buttonPane );

					$(input).datepicker("widget").css('z-index', 30001);
				},1);
			}
		});

		// Make sure field tab is selected
		this.page.getEl('fields_display_main_wrap_tab').click();
	},

	closeEditMode: function() {
		this.mode = 'view';

		this.display.removeClass('mode-edit-on');
		this.page.getEl('field_edit_save').hide();
		this.page.getEl('field_edit_cancel').hide();
		this.page.getEl('field_edit_start').show();
		this.page.getEl('field_edit_controls').removeClass('loading');
		this.updateDisplay();
	},

	updateDisplay: function() {
		if (this.mode == 'view') {
			this.updateDisplay_view();
		} else {
			this.updateDisplay_modify();
		}
	},

	updateDisplay_modify: function() {
		var fields = this.fieldDisplayModify.getFields(this.ticketReader.getDepartmentId());
		if (!fields || !fields['default']) {
			fields['default'] = [];
		}

		fields = fields['default'];

		// Check to see if the fields are the same and in the same order
		if (fields.length == this.currentDisplayModify.length) {
			var change = false;
			for (var i = 0; i < fields.length; i++) {
				if (this.currentDisplayModify && this.currentDisplayModify[i] && fields[i].field_type == this.currentDisplayModify[i].field_type) {
					if (fields[i].field_type == 'ticket_field' && fields[i].field_id != this.currentDisplayModify[i].field_id) {
						change = true;
						break;
					}
				} else {
					change = true;
					break;
				}
			}
		} else {
			var change = true;
		}

		// No Changes, dont need to do any expensive dom work
		if (!change) {
			console.log("[TicketFields] No change");
			return;
		}

		this.currentDisplayModify = fields;

		this.display.find('tbody.item.item-on').hide().removeClass('item-on');
		var last = this.display.find('tbody.always-bottom');

		Array.each(this.currentDisplayModify, function(f) {
			if (f.field_type == 'ticket_field') {
				var classname = 'ticket_field_' + f.field_id;
			} else {
				var classname = f.field_type;
			}

			this.display.find('.item.' + classname).detach().appendTo(this.display).show().addClass('item-on');
		}, this);

		last.detach().appendTo(this.display);
	},

	updateDisplay_view: function() {
		var fields = this.fieldDisplay.getFields(this.ticketReader.getDepartmentId());
		if (!fields || !fields['default']) {
			fields['default'] = [];
		}

		fields = fields['default'];

		// Check to see if the fields are the same and in the same order
		if (fields.length == this.currentDisplay.length) {
			var change = false;
			for (var i = 0; i < fields.length; i++) {
				if (fields[i].field_type == this.currentDisplay[i].field_type) {
					if (fields[i].field_type == 'ticket_field' && fields[i].field_id != this.currentDisplay[i].field_id) {
						change = true;
						break;
					}
				} else {
					change = true;
					break;
				}
			}
		} else {
			var change = true;
		}

		// No Changes, dont need to do any expensive dom work
		if (!change) {
			console.log("[TicketFields] No change");
			return;
		}

		this.currentDisplay = fields;

		this.display.find('tbody.item.item-on').hide().removeClass('item-on');
		var last = this.display.find('tbody.always-bottom');

		Array.each(this.currentDisplay, function(f) {
			if (f.field_type == 'ticket_field') {
				var classname = 'ticket_field_' + f.field_id;
			} else {
				var classname = f.field_type;
			}

			this.display.find('.item.' + classname).detach().appendTo(this.display).show().addClass('item-on');
		}, this);

		last.detach().appendTo(this.display);
	},

	saveChanges: function() {
		var changeManager = this.page.changeManager;

		this.display.find('[data-prop-id]').each(function() {
			var prop = changeManager.getPropertyManager($(this).data('prop-id'));
			prop.setValue($(this).val());

			changeManager.addChange(prop);
		});

		var customFieldData = this.display.find('.custom-field input, .custom-field textarea, .custom-field select').serializeArray();

		this.display.find('input[type="checkbox"][value="1"]').not(':checked').each(function() {
			customFieldData.push({
				name: $(this).attr('name'),
				value: '0'
			});
		});

		changeManager.saveChanges(customFieldData, (function(data) {
			this.updateDisplay();
			this.closeEditMode();

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

			if (data.data && data.data.reload) {
				this.page.closeSelf();
				DeskPRO_Window.runPageRoute('ticket:' + BASE_URL + 'agent/tickets/' + this.page.meta.ticket_id);
			}
		}).bind(this));
	},

	replaceHolders: function(html) {
		var last = this.display.find('tbody.always-bottom');
		last.detach();

		var old = this.display;
		this.display = $('<table cellspacing="0" cellpadding="0" width="100%" class="field-holders-table">' + html + '</table>');
		old.after(this.display);
		old.remove();
		this.display.append(last);

		this.currentDisplay = [];
		this.currentDisplayModify = [];
		this.updateDisplay();
	}
});
Orb.createNamespace('DeskPRO.Agent.PageHelper');

/**
 * Given a ticket details, runs through rules to fetch which fields should be displayed
 */
DeskPRO.Agent.PageHelper.TicketFieldDisplay = new Orb.Class({

	initialize: function(ticketReader, mode) {
		this.ticketReader = ticketReader;
		this.mode = mode || 'create';
	},

	getFields: function(department_id) {
		department_id = parseInt(department_id);
		DP.console.log('[TicketFieldDisplay] department %i', department_id);

		var depItems = [];
		if (window.DESKPRO_TICKET_DISPLAY && window.DESKPRO_TICKET_DISPLAY[this.mode]) {

			if (this.mode == 'view') {
				if (typeof window.DESKPRO_TICKET_DISPLAY[this.mode][department_id] == 'undefined' && typeof window.DESKPRO_TICKET_DISPLAY[this.mode][department_id] == 'undefined') {
					DP.console.log('[TicketFieldDisplay] Dynamic switch mode to create');
					this.mode = 'create';
				}
			}

			if (typeof window.DESKPRO_TICKET_DISPLAY[this.mode][department_id] == 'undefined') {
				DP.console.log('[TicketFieldDisplay] Dynamic switch to dep 0');
				depItems = window.DESKPRO_TICKET_DISPLAY[this.mode][0] || [];
			} else {
				DP.console.log('[TicketFieldDisplay] Using dep');
				depItems = window.DESKPRO_TICKET_DISPLAY[this.mode][department_id] || [];
			}
		}

		DP.console.log('[TicketFieldDisplay] depItems %o', depItems);

		var items = this.runRules(depItems);
		DP.console.log('[TicketFieldDisplay] items %o', items);

		return items;
	},


	/**
	 * Run through all the rules and show/hide all display items and
	 * sections based on it.
	 */
	runRules: function(depItems) {

		var items = {};

		//------------------------------
		// Run all the rules to fetch on/off of each item in display
		//------------------------------

		Array.each(depItems, function(item) {
			if (!items[item.section]) {
				items[item.section] = [];
			}

			switch (item.section) {
				case 'default':
					var state = this.runCheckForItem(item);
					if (state) {
						items[item.section].push(item);
					}
					break;
			}

		}, this);

		return items;
	},

	/**
	 * Runs the check function for an item to get its visibility.
	 *
	 * @param item
	 */
	runCheckForItem: function(item) {
		var visible = true;

		// If the check function passes, then inverse visibility
		if (item.check && !item.check(this.ticketReader)) {
			visible = false;
		}

		return visible;
	}
});

Orb.createNamespace('DeskPRO.Agent.PageHelper');

/**
 * Handles updating display based on department and rules
 */
DeskPRO.Agent.PageHelper.ChatFields = new Orb.Class({
	initialize: function(page) {
		var self = this;
		this.page = page;
		this.display = this.page.getEl('field_holders').find('.field-holders-table');

		this.mode = 'view';
		this.currentDisplay = [];
		this.currentDisplayModify = [];

		this.chatReader = {
			getDepartmentId: function() {
				var catId = self.page.getEl('department_id').val();
				return parseInt(catId) || 0;
			}
		};

		this.fieldDisplay = new DeskPRO.Agent.PageHelper.ChatFieldDisplay(this.ticketReader, 'create');
		this.fieldDisplayModify = new DeskPRO.Agent.PageHelper.ChatFieldDisplay(this.ticketReader, 'create');

		this.page.getEl('department_id').on('change', function() {
			self.updateDisplay();
		});

		this.page.getEl('field_edit_start').on('click', function(ev) {
			ev.preventDefault();
			self.openEditMode();
		});

		self.page.getEl('field_edit_cancel').on('click', function(ev) {
			ev.preventDefault();
			self.closeEditMode();
		});

		self.page.getEl('field_edit_save').on('click', function(ev) {
			self.page.getEl('field_edit_cancel').hide();
			self.page.getEl('field_edit_save').hide();
			self.page.getEl('field_edit_start').hide();
			self.page.getEl('field_edit_controls').addClass('loading');

			self.page.getEl('field_errors').hide().removeClass('on');

			self.saveChanges();
		});
	},

	openEditMode: function() {
		this.mode = 'edit';

		this.display.addClass('mode-edit-on');
		this.page.getEl('field_edit_start').hide();
		this.page.getEl('field_edit_cancel').show();
		this.page.getEl('field_edit_save').show();
		this.page.getEl('field_edit_controls').removeClass('loading');

		this.display.find('select[multiple]').each(function() {
			var min = $(this).width() + 30;
			var parent = $(this).closest('td').find('> div').first().width();
			if (parent) {
				min = Math.max(min, Math.ceil(parent / 1.75));
			}
			$(this).width(min);
		});
		DP.select(this.display.find('select'));

		this.updateDisplay();

		$('.Date.customfield input', this.display).datepicker({
			dateFormat: 'yy-mm-dd',
			showButtonPanel: true,
			beforeShow: function(input) {
				setTimeout(function() {
					var buttonPane = $(input).datepicker("widget").find(".ui-datepicker-buttonpane");

					buttonPane.find('button:first').remove();

					var btn = $('<button class="ui-datepicker-current ui-state-default ui-priority-secondary ui-corner-all" type="button">Clear</button>');
					btn.unbind("click").bind("click", function () { $.datepicker._clearDate( input ); });
					btn.appendTo( buttonPane );

					$(input).datepicker("widget").css('z-index', 30001);
				},1);
			}
		});

		// Make sure field tab is selected
		this.page.getEl('fields_display_main_wrap_tab').click();
	},

	closeEditMode: function() {
		this.mode = 'view';

		this.display.removeClass('mode-edit-on');
		this.page.getEl('field_edit_save').hide();
		this.page.getEl('field_edit_cancel').hide();
		this.page.getEl('field_edit_start').show();
		this.page.getEl('field_edit_controls').removeClass('loading');
		this.updateDisplay();
	},

	updateDisplay: function() {
		if (this.mode == 'view') {
			this.updateDisplay_view();
		} else {
			this.updateDisplay_modify();
		}
	},

	updateDisplay_modify: function() {
		var fields = this.fieldDisplayModify.getFields(this.chatReader.getDepartmentId());
		if (!fields || !fields['default']) {
			fields['default'] = [];
		}

		fields = fields['default'];

		// Check to see if the fields are the same and in the same order
		if (fields.length == this.currentDisplayModify.length) {
			var change = false;
			for (var i = 0; i < fields.length; i++) {
				if (this.currentDisplayModify && this.currentDisplayModify[i] && fields[i].field_type == this.currentDisplayModify[i].field_type) {
					if (fields[i].field_type == 'chat_field' && fields[i].field_id != this.currentDisplayModify[i].field_id) {
						change = true;
						break;
					}
				} else {
					change = true;
					break;
				}
			}
		} else {
			var change = true;
		}

		// No Changes, dont need to do any expensive dom work
		if (!change) {
			var ons = this.display.find('tbody.item-on');
			ons.removeClass('last');
			ons.last().addClass('last');

			console.log("[ChatFields] No change");
			return;
		}

		this.currentDisplayModify = fields;

		this.display.find('tbody.item.item-on').hide().removeClass('item-on');

		Array.each(this.currentDisplayModify, function(f) {
			if (f.field_type == 'chat_field') {
				var classname = 'chat_field_' + f.field_id;
			} else {
				var classname = f.field_type;
			}

			this.display.find('.item.' + classname).detach().appendTo(this.display).show().addClass('item-on');
		}, this);

		var ons = this.display.find('tbody.item-on');
		if (ons[0]) {
			ons.removeClass('last');
			ons.last().addClass('last');
			this.page.getEl('fields_display_main_wrap_tab').show();
		} else {
			this.page.getEl('fields_display_main_wrap_tab').hide();
			if (this.page.getEl('fields_display_main_wrap_tab').hasClass('on')) {
				this.page.getEl('fields_display_main_wrap_tab').next().trigger('click');
			}
		}
	},

	updateDisplay_view: function() {
		var fields = this.fieldDisplay.getFields(this.chatReader.getDepartmentId());
		if (!fields || !fields['default']) {
			fields['default'] = [];
		}

		fields = fields['default'];

		// Check to see if the fields are the same and in the same order
		if (fields.length == this.currentDisplay.length) {
			var change = false;
			for (var i = 0; i < fields.length; i++) {
				if (fields[i].field_type == this.currentDisplay[i].field_type) {
					if (fields[i].field_type == 'chat_field' && fields[i].field_id != this.currentDisplay[i].field_id) {
						change = true;
						break;
					}
				} else {
					change = true;
					break;
				}
			}
		} else {
			var change = true;
		}

		// No Changes, dont need to do any expensive dom work
		if (!change) {
			console.log("[ChatFields] No change");

			var ons = this.display.find('tbody.item-on');
			ons.removeClass('last');
			ons.last().addClass('last');

			return;
		}

		this.currentDisplay = fields;

		this.display.find('tbody.item.item-on').hide().removeClass('item-on');

		Array.each(this.currentDisplay, function(f) {
			if (f.field_type == 'chat_field') {
				var classname = 'chat_field_' + f.field_id;
			} else {
				var classname = f.field_type;
			}

			this.display.find('.item.' + classname).detach().appendTo(this.display).show().addClass('item-on');
		}, this);

		var ons = this.display.find('tbody.item-on');
		if (ons[0]) {
			ons.removeClass('last');
			ons.last().addClass('last');
			this.page.getEl('fields_display_main_wrap_tab').show();
		} else {
			this.page.getEl('fields_display_main_wrap_tab').hide();
			if (this.page.getEl('fields_display_main_wrap_tab').hasClass('on')) {
				this.page.getEl('fields_display_main_wrap_tab').next().trigger('click');
			}
		}
	},

	saveChanges: function() {
		var data = this.page.getEl('field_holders').find('input, select, textarea').serializeArray();

		$.ajax({
			url: BASE_URL + 'agent/chat/' + this.page.meta.conversation_id + '/save-fields',
			type: 'POST',
			dataType: 'html',
			data: data,
			context: this,
			complete: function() {
				this.page.getEl('field_edit_cancel').hide();
				this.page.getEl('field_edit_save').hide();
				this.page.getEl('field_edit_start').show();
				this.page.getEl('field_edit_controls').removeClass('loading');
			},
			success: function(new_holders) {
				this.replaceHolders(new_holders);
			}
		});
	},

	replaceHolders: function(html) {
		this.display.parent().html(html);
		this.display = this.page.getEl('field_holders').find('.field-holders-table');
		this.currentDisplay = [];
		this.currentDisplayModify = [];
		this.updateDisplay();
	}
});
Orb.createNamespace('DeskPRO.Agent.PageHelper');

/**
 * Given a chat, runs through rules to fetch which fields should be displayed
 */
DeskPRO.Agent.PageHelper.ChatFieldDisplay = new Orb.Class({

	initialize: function(chatReader, mode) {
		this.chatReader = chatReader;
		this.mode = mode || 'create';
	},

	getFields: function(department_id) {
		department_id = parseInt(department_id);
		DP.console.log('[ChatFieldDisplay] department %i', department_id);

		var depItems = [];
		if (window.DESKPRO_CHAT_DISPLAY && window.DESKPRO_CHAT_DISPLAY[this.mode]) {

			if (this.mode == 'view') {
				if (typeof window.DESKPRO_CHAT_DISPLAY[this.mode][department_id] == 'undefined' && typeof window.DESKPRO_CHAT_DISPLAY[this.mode][department_id] == 'undefined') {
					DP.console.log('[ChatFieldDisplay] Dynamic switch mode to create');
					this.mode = 'create';
				}
			}

			if (typeof window.DESKPRO_CHAT_DISPLAY[this.mode][department_id] == 'undefined') {
				DP.console.log('[ChatFieldDisplay] Dynamic switch to dep 0');
				depItems = window.DESKPRO_CHAT_DISPLAY[this.mode][0] || [];
			} else {
				DP.console.log('[ChatFieldDisplay] Using dep');
				depItems = window.DESKPRO_CHAT_DISPLAY[this.mode][department_id] || [];
			}
		}

		DP.console.log('[ChatFieldDisplay] depItems %o', depItems);

		var items = this.runRules(depItems);
		DP.console.log('[ChatFieldDisplay] items %o', items);

		return items;
	},


	/**
	 * Run through all the rules and show/hide all display items and
	 * sections based on it.
	 */
	runRules: function(depItems) {

		var items = {};

		//------------------------------
		// Run all the rules to fetch on/off of each item in display
		//------------------------------

		Array.each(depItems, function(item) {
			if (!items[item.section]) {
				items[item.section] = [];
			}

			switch (item.section) {
				case 'default':
					var state = this.runCheckForItem(item);
					if (state) {
						items[item.section].push(item);
					}
					break;
			}

		}, this);

		return items;
	},

	/**
	 * Runs the check function for an item to get its visibility.
	 *
	 * @param item
	 */
	runCheckForItem: function(item) {
		return true;
	}
});

Orb.createNamespace('DeskPRO.Agent.PageHelper');

DeskPRO.Agent.PageHelper.ListSearchForm = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(page, options) {
		this.page = page;

		this.options = {
			form: null,
			context: null,
			searchData: null
		};

		this.setOptions(options);

		this.form = this.options.form;
		this.topSection = this.options.context;

		this._initSearchOptions();
	},

	_initSearchOptions: function() {
		var editBtn = $('.summary .edit', this.topSection);
		editBtn.on('click', this.showSearchForm.bind(this));

		var form = this.form;
		var self = this;
		form.on('submit', function(ev) {
			ev.preventDefault();

			var url = form.attr('action');
			var data = form.serializeArray();

			self.fireEvent('searchSubmit', [url, data]);
		});
	},

	showSearchForm: function() {
		var criteriaList  = $('.search-form', this.topSection);
		var criteriaTerms = $('.search-builder-tpl', this.topSection);

		var editor = new DeskPRO.Form.RuleBuilder(criteriaTerms);
		$('.add-term', criteriaList).data('add-count', 0).on('click', function() {
			var count = parseInt($(this).data('add-count'));
			var basename = 'terms['+count+']';

			$(this).data('add-count', count+1);

			editor.addNewRow($('.search-terms', criteriaList), basename);
		});

		var searchDataEl = this.searchData;
		if (searchDataEl && searchDataEl.length) {
			var searchData = searchDataEl.get(0).innerHTML;
			searchData = $.parseJSON(searchData);

			if (searchData.terms) {
				Array.each(searchData.terms, function(info, x) {
					var basename = 'terms[initial_' + x + ']';
					editor.addNewRow($('.search-terms', criteriaList), basename, {
						type: info.type,
						op: info.op,
						options: info.options
					});
				});
			}
			searchDataEl.remove();
		}

		$('.summary', this.topSection).slideUp();
		$('.form-panel', this.topSection).slideDown();
	}
});

Orb.createNamespace('DeskPRO.Agent.PageHelper');

/**
 * Allows a user to edit and reorder categories, with double-click to modify titles.
 */
DeskPRO.Agent.PageHelper.CategoryEdit = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(options) {
		this.options = {
			wrapper: null
		};

		this.setOptions(options||{});

		this.wrapper = $(this.options.wrapper);
	},


	/**
	 * Lazy-init object when its first opened.
	 */
	_init: function() {
		if (this.hasInit) return;
		this.hasInit = true;

		this.wrapper.detach().appendTo('body');

		// Usually theres a doc click waiting to close this,
		// so dont propogate clicks on this element
		this.wrapper.on('click', function(ev) {
			ev.stopPropagation();
		});

		$('.close-trigger', this.wrapper).on('click', (function(ev) {
			this.close();
		}).bind(this));

		$('.new-close-trigger', this.wrapper).on('click', (function(ev) {
			this.closeNewDlg();
		}).bind(this));

		$('ul', this.wrapper).sortable({
			axis: 'y',
			items: '> li'
		});

		this.backdrop = $('.backdrop:first', this.wrapper);
		this.newDlg = $('.new-dlg:first', this.wrapper);
		this.newParent = $('select:first', this.newDlg);
		this.newTitle = $('input:first', this.newDlg);
		$('.add-save-trigger', this.newDlg).on('click', (function() {
			this.addNewToList();
		}).bind(this));

		$('.add-trigger:first', this.wrapper).on('click', (function() {
			this.openNewDlg();
		}).bind(this));

		$('.save-all-trigger', this.wrapper).on('click', (function() {
			this.fireEvent('save', [this]);

			this.close();
		}).bind(this));

		var self = this;
		this.wrapper.on('dblclick', '.title', function() {
			self.enableEditTitle($(this));
		});
	},


	/**
	 * Open the overlay
	 */
	open: function() {
		this._init();

		if (this.wrapper.is('.open')) {
			return;
		}

		this.wrapper.css({
			position: 'absolute',
			left: 40,
			top: 150
		});

		this.wrapper.addClass('open').fadeIn();
	},


	/**
	 * Close the overlay
	 */
	close: function() {
		this.closeNewDlg();
		this.wrapper.removeClass('open').fadeOut();
	},


	/**
	 * Open the new category input
	 */
	openNewDlg: function() {

		// Regenerate list
		var html = [];
		$('li > .title', this.wrapper).each(function() {
			var el = $(this);
			var depth = parseInt(el.data('depth'));
			var catId = el.data('cat-id');
			var title = el.text().trim();

			if (depth) {
				title = Orb.strRepeat('--', depth) + ' ' + title;
			}

			html.push('<option value="'+catId+'">' + Orb.escapeHtml(title) + '</option>');
		});

		html = html.join('');

		$('option:not(.none)', this.newParent).remove();
		$('option.none', this.newParent).after($(html));

		var top  = 50;
		var left = 65;

		var dlg = this.newDlg;
		dlg.css({
			display: 'absolute',
			top: top,
			left: left
		});

		this.backdrop.fadeIn('fast');
		dlg.fadeIn();
	},


	/**
	 * Close the new cat input
	 */
	closeNewDlg: function() {
		var dlg = this.newDlg;
		dlg.fadeOut('fast');
		this.backdrop.fadeOut('fast');
	},


	/**
	 * After clicking 'add' on the new cat input, this adds it to the actual list
	 */
	addNewToList: function() {
		var parentId = this.newParent.val();
		var title = this.newTitle.val().trim();

		if (!title.length) {
			return;
		}

		this.newTitle.val('');

		var ul, depth = 0, parent_li = false;
		if (parentId && parentId != '0') {
			parent_li = $('li.cat-' + parentId, this.wrapper);
		}

		if (parent_li && parent_li.length) {
			depth = parseInt($('> .title', parent_li).data('depth')) + 1;

			ul = $('> ul', parent_li);

			if (!ul.length) {
				var new_ul = $('<ul></ul>');
				parent_li.append(new_ul);

				new_ul.sortable({
					axis: 'y',
					items: '> li'
				});
			}
			ul = $('> ul', parent_li);
		} else {
			ul = $('ul.top:first', this.wrapper);
		}

		var tmp_id = 'new_' + Orb.uuid();
		var li = '<li class="cat-'+tmp_id+'"><div class="title new" data-cat-id="' + tmp_id + '" data-depth="'+depth+'">' + Orb.escapeHtml(title) + '</div></li>';

		ul.append(li);

		this.closeNewDlg();
	},


	/**
	 * Enable the editable title
	 *
	 * @param titleEl
	 */
	enableEditTitle: function(titleEl) {
		var input = $('<input />');
		input.val(titleEl.text().trim());

		titleEl.empty().append(input);

		var btn = $('<button class="dp-button x-small">Apply</button>');
		titleEl.append(btn);

		var self = this;
		input.on('keypress', function(ev) {
			if (ev.keyCode == 13) {
				ev.preventDefault();
				self._applyEditTitle(titleEl, input);
			}
		});
		btn.on('click', function() {
			self._applyEditTitle(titleEl, input);
		});
	},

	_applyEditTitle: function(titleEl, input) {
		titleEl.empty().text(input.val());
	},


	/**
	 * Encode the current structure
	 */
	encode: function() {
		var data = [];

		this._encodeSet(data, $('ul.top', this.wrapper), 0);

		return data;
	},

	_encodeSet: function(data, ul, parentId) {
		var self = this;
		$('li', ul).each(function() {
			var li = $(this);
			var title = $('> .title', li);
			var catId = title.data('cat-id');
			var displayOrder = data.length + 1;
			var isNew = title.is('.new');

			data.push({
				id: catId,
				parentId: parentId,
				isNew: isNew,
				displayOrder: displayOrder,
				title: title.text().trim()
			});

			var ul = $('> ul', li);
			if (ul.length) {
				self._encodeSet(data, ul, catId);
			}
		});
	},


	/**
	 * Encode the data as a jQuery form data object
	 *
	 * @param name
	 */
	encodeForm: function(name) {
		if (!name) name = 'cats';
		var rawData = this.encode();

		var data = [];
		var count = 0;

		Array.each(rawData, function(item) {

			Object.each(item, function(v,k) {
				if (k == 'isNew') {
					if (v) v = 1;
					else v = 0;
				}
				data.push({
					name: name + '['+count+']['+k+']',
					value: v
				});
			});

			count++;
		});

		return data;
	},


	/**
	 * Destroy this widget
	 */
	destroy: function() {
		if (this.hasInit) {
			this.wrapper.remove();
		}
	}
});

Orb.createNamespace('DeskPRO.Agent.PageHelper');

DeskPRO.Agent.PageHelper.DisplayOptions = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(page, options)  {

		var self = this;

		this.page = page;

		this.options = {
			triggerElement: null,
			resultId: 0,
			prefSaveResultId: null,
			prefId: '',
			refreshUrl: ''
		};
		this.setOptions(options);

		if (this.options.prefSaveResultId === null) {
			this.options.prefSaveResultId = this.options.resultId;
		}

		if (!this.options.triggerElement) {
			this.options.triggerElement = $('.display-options-trigger', this.page.wrapper);
		}

		$(this.options.triggerElement).on('click', (function(ev) {
			ev.stopPropagation();
			ev.preventDefault();
			this.open();
		}).bind(this));

		// Automatically set up the quick sort menu button
		var menuBtn = $('button.order-by-trigger', this.page.wrapper);
		var menuEl  = $('ul.order-by-menu', this.page.wrapper);
		if (menuBtn.length && menuEl.length) {
			this.orderByMenu = new DeskPRO.UI.Menu({
				triggerElement: menuBtn,
				menuElement: menuEl,
				onItemClicked: (function(info) {
					var item = $(info.itemEl);

					var prop = item.data('field')
					var label = item.text().trim();

					$('.label', menuBtn).text(label);

					var disOptWrap = self.getWrapperElement();
					var sel = $('select.sel-order-by', disOptWrap);
					$('option', sel).prop('selected', false);
					$('option.' + prop.replace('.', '_'), sel).prop('selected', true);

					self.saveAndRefresh();

				}).bind(this)
			});
		}

		this.page.addEvent('destroy', (function() {
			this.destroy();
		}).bind(this));
	},

	_initOverlay: function() {

		if (this._hasInit) return;
		this._hasInit = true;

		var ul = $('ul.display-fields-list.on-list', this.wrapper);

		var makeBogus = function(ul) {
			// Use bogus invisible draggables so when dragging to end of the list, the dragging
			// item is placed between one of these invisible ones. The event handlers
			// make sure they're always at the end.
			// - This is to fix making it too hard to position something at the end.
			var exist = ul.find('> li.bogus').length;
			for (var i = exist; i < 8; i++) {
				var li = $('<li class="bogus">&nbsp;</li>');
				li.css({
					width: 30,
					visibility: 'hidden'
				});

				ul.append(li);
			}
		};

		this.wrapper = $('.display-options', this.page.wrapper).first();
		this.optionsList = $('ul.display-fields-list.on-list', this.wrapper).sortable({
			forceHelperSize:true,
			opacity: 0.6,
			update: function() {
				ul.find('> li.bogus').remove();
				makeBogus(ul);
			}
		});

		var onList = this.optionsList;
		var offList = $('ul.display-fields-list.off-list', this.wrapper);

		onList.find(':checkbox').on('click', function() {
			var check = $(this);
			var li = $(this).closest('li');

			if (check.attr('checked')) {
				li.detach().removeClass('off').appendTo(onList);
				if (!offList.find('> li').length) {
					offList.hide();
				}
			} else {
				li.detach().addClass('off').prependTo(offList);
				offList.show();
			}

			ul.find('> li.bogus').remove();
			makeBogus(ul);
		}).not(':checked').each(function() {
			$(this).closest('li').detach().addClass('off').appendTo(offList);
		});
		if (!offList.find('> li').length) {
			offList.hide();
		}

		this.wrapper.detach().appendTo('body');
		this.wrapper.css('z-index', '10101');

		this.wrapper.on('click', function(ev) {
			ev.stopPropagation();
		});

		this.backdropEl = $('<div class="backdrop dp-overlay-backdrop" />');
		this.backdropEl.css('z-index', '10100').hide().appendTo('body');

		this.backdropEl.on('click', (function(ev) {
			ev.stopPropagation();
			this.close();
		}).bind(this));

		$('header .close-trigger', this.wrapper).on('click', (function(ev) {
			ev.stopPropagation();
			ev.preventDefault();
			this.close();
		}).bind(this));

		$('.save-trigger', this.wrapper).on('click', (function() {
			this.saveDisplayOptions();
		}).bind(this));
	},

	getDisplayFields: function() {
		var fields = [];

		$(':checkbox:checked', this.getWrapperElement()).each(function() {
			fields.push($(this).attr('name'));
		});

		return fields;
	},

	saveDisplayOptions: function() {
		this.wrapper.addClass('loading');
		this.saveAndRefresh();
	},

	saveAndRefresh: function() {

		var self = this;
		var wrap = this.getWrapperElement();

		var data = [];
		var pref_name = 'prefs[agent.ui.'+ this.options.prefId + '-display-fields.' + this.options.prefSaveResultId +'][]';

		var has = false;

		$('input[type="checkbox"]:checked', wrap).each(function() {
			has = true;
			data.push({
				name: pref_name,
				value: $(this).attr('name')
			});
		});

		if (!has) {
			data.push({
				name: pref_name,
				value: 'NONE'
			});
		}

		// and the ordering
		data.push({
			name: 'prefs[agent.ui.'+ this.options.prefId + '-order-by.' + this.options.prefSaveResultId +']',
			value: $('select[name="order_by"]', wrap).val()
		});

		// We reload the same page which will have changes applied
		var url = this.options.refreshUrl;

		if (this.options.isListView) {
			var page = this.page;
			$.ajax({
				timeout: 20000,
				type: 'POST',
				url: BASE_URL + 'agent/misc/ajax-save-prefs',
				data: data,
				context: this,
				complete: function() {
					this.close();
				},
				success: function() {
					page.meta.pageReloader();
				}
			});
		} else {
			$.ajax({
				timeout: 20000,
				type: 'POST',
				url: BASE_URL + 'agent/misc/ajax-save-prefs',
				data: data,
				context: this,
				complete: function() {
					this.close();
				},
				success: function() {
					DeskPRO_Window.loadListPane(url);
				}
			});
		}
	},

	open: function() {
		this._initOverlay();

		this.updatePositions();

		this.wrapper.addClass('open');
		this.backdropEl.show();

		this.wrapper.addClass('open');

		this.fireEvent('opened', [this]);
	},

	isOpen: function() {
		if (!this._hasInit || !this.wrapper.is('.open')) {
			return false;
		}

		return true;
	},

	close: function() {
		if (!this._hasInit || !this.isOpen()) return;

		this.wrapper.removeClass('open');
		this.backdropEl.hide();
		this.fireEvent('closed', [this]);
	},

	/**
	 * Update the positions of the elements
	 */
	updatePositions: function() {

		var elW = this.wrapper.width();
		var elH = this.wrapper.height();

		var pageW = $(window).width();
		var pageH = $(window).height();

		this.wrapper.css({
			top: 55,
			left: (pageW-elW) / 2
		});
	},

	getWrapperElement: function() {
		if (this._hasInit) {
			return this.wrapper;
		} else {
			return $('.display-options:first', this.page.wrapper);
		}
	},

	destroy: function() {
		if (this._hasInit) {
			this.wrapper.remove();
			this.backdropEl.remove();
		}

		delete this.wrapper;
		delete this.backdropEl;
		delete this.options;
		delete this.page;
	}
});

Orb.createNamespace('DeskPRO.Agent.PageHelper');

DeskPRO.Agent.PageHelper.SelectionBar = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(page, options) {
		var self = this;

		this.page = page;

		this.options = {
			selectionBar: null,
			selectedCount: null,
			button: null,
			checkSelector: 'input.item-select',
			saveSelectionId: null
		};
		this.setOptions(options);

		if (this.options.saveSelectionId) {
			this.options.saveSelectionRealId = 'dp.agent.selectionbar.' + this.options.saveSelectionId;
		}

		if (!this.options.selectionBar) {
			this.options.selectionBar = $('.list-selection-bar', this.page.wrapper).first();
		}
		this.selectionBar = $(this.options.selectionBar);

		if (!this.options.selectedCount) {
			this.options.selectedCount = $('.selected-count:first', this.selectionBar);
		}
		this.selectedCount = $(this.options.selectedCount);

		if (!this.options.button) {
			this.options.button = $('.perform-actions-trigger:first', this.selectionBar);
		}
		this.button = $(this.options.button);
        this.button.addClass('disabled');

		this.button.on('click', this.buttonClicked.bind(this));

		this.controlCheck = $('.selection-control', this.page.wrapper).on('click', function() {
			if ($(this).is(':checked')) {
				self.checkAll();
			} else {
				self.checkNone();
			}
		});

		this.lastCheckBox = null;

		this.page.wrapper.on('click', this.options.checkSelector, function(event) {
			var el = $(this);
			self.handleCheckChange(el, el.is(':checked'));

			if (self.lastCheckBox && event.shiftKey) {
				self.checkRange(el, self.lastCheckBox);
			}

			self.lastCheckBox = el;
		});
	},

	buttonClicked: function(ev) {
		if (this.button.is('.disabled')) {
			return;
		}

		this.fireEvent('buttonClick', [ev]);
	},

	getCheckedValues: function() {
		var values = [];

		$(this.options.checkSelector + ':checked', this.page.wrapper).each(function() {
			values.push($(this).val());
		});

		return values;
	},

	getCheckedFormValues: function (form_name, appendArray, info) {
		appendArray = appendArray || [];

		if (!info) info = {};
		info.checkedCount = 0;

		$(this.options.checkSelector + ':checked', this.page.wrapper).each(function() {
			appendArray.push({
				name: form_name,
				value: $(this).val()
			});
			info.checkedCount++;
		});

		return appendArray;
	},

	getChecked: function() {
		return $(this.options.checkSelector + ':checked', this.page.wrapper);
	},

	getCount: function() {
		return $(this.options.checkSelector + ':checked', this.page.wrapper).length;
	},

	checkAll: function() {
		$(this.options.checkSelector, this.page.wrapper).attr('checked', true);

		var count = this.updateCount();

		this.fireEvent('checkAll', [count]);
	},

	checkRange: function(start, end) {
		var is_checked = start.is(':checked');

		var checks = this.page.wrapper.find(this.options.checkSelector);
		var indexStart = checks.index(start);
		var indexEnd = checks.index(end);

		if (indexStart > indexEnd) {
			var tmp = indexEnd;
			indexEnd = indexStart;
			indexStart = tmp;
		}

		checks = checks.slice(indexStart, indexEnd+1);
		checks.prop('checked', is_checked);

		var count = this.updateCount();

		this.fireEvent('checkRange', [count]);
	},

	checkNone: function() {
		$(this.options.checkSelector, this.page.wrapper).filter(':checked').attr('checked', false);

		var count = this.updateCount();

		this.fireEvent('checkNone');
	},

	handleCheckChange: function(el, is_checked) {
		var count = this.updateCount();
		this.fireEvent('checkChange', [el, is_checked, count]);
	},

	restoreFromSessionStorage: function() {
		if (this.options.saveSelectionRealId && window.sessionStorage && window.sessionStorage[this.options.saveSelectionRealId]) {
			var checked = window.sessionStorage[this.options.saveSelectionRealId].split(',');
			if (checked.length) {
				$(this.options.checkSelector, this.page.wrapper).each(function() {
					if (this.value && checked.contains(this.value)) {
						this.checked = true;
					}
				});
				this.updateCount();
			}
		}
	},

	updateCount: function() {
		var oldCount = parseInt(this.selectedCount.text(), 10) || 0;

		var checkedEls = $(this.options.checkSelector, this.page.wrapper).filter(':checked');
		var count = checkedEls.length;
		this.selectedCount.text(count);

		if (count > 0) {
			this.button.removeClass('disabled');
		} else {
			this.button.addClass('disabled');
		}

		if (this.options.saveSelectionRealId && window.sessionStorage) {
			var checked = [];
			if (count) {
				checkedEls.each(function() {
					if (this.value) {
						checked.push(this.value);
					}
				});
			}

			sessionStorage[this.options.saveSelectionRealId] = checked.join(',');
		}

		if (!checkedEls.length || count == 0) {
			this.controlCheck.attr('checked', false);
		} else {
			this.controlCheck.attr('checked', true);
		}

		if (oldCount != count) {
			this.fireEvent('countChange', [count, oldCount]);
		}

		return count;
	},

	resetCountLabel: function() {
		return this.updateCount();
	}
});

Orb.createNamespace('DeskPRO.Agent.PageHelper');

DeskPRO.Agent.PageHelper.Popover_Instances = {};

DeskPRO.Agent.PageHelper.Popover = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(options) {
		this.options = {
			/**
			 * How long after this is initialized to wait before
			 * auto-loading the person page in the bg. 0 disables
			 * and loads on-demand.
			 */
			loadTimeout: 0,

			/**
			 * The page to load. False to not use the loader in this class,
			 * you can use setHtml() instead.
			 */
			pageUrl: '',

			/**
			 * Callback method for loading the page instead of using default ajax loader.
			 */
			pageCallback: null,

			/**
			 * The route to load when clicking "move to tab"
			 */
			tabRoute: false,

			/**
			 * Destroy the popover when it closes?
			 */
			destroyOnClose: true,

			overFrom: '#dp_content',

			/**
			 * 'side' or 'over'
			 */
			positionMode: 'side',

			/**
			 * 'top' or 'bottom'
			 */
			sidePosition: 'top'
		};

		DeskPRO.Agent.PageHelper.Popover_Instances[this.OBJ_ID] = this;

		this.setOptions(options);

		/**
		 * The page source if the page isnt initialized yet.
		 * This deferres processing the page fragment until the user wants to
		 * see the page and the source is loaded in the bg
		 */
		this.pageSource = null;

		if (this.options.pageSource) {
			this.pageSource = this.options.pageSource;
			delete this.options.pageSource;
		}

		/**
		 * The page fragment once its initialized
		 */
		this.page = null;

		/**
		 * True if the user has requested to see the page but it hasnt been loaded
		 * yet. After its loaded, this flag is checked to see if the page should
		 * be immediately displayed.
		 */
		this.isWaiting = false;

		// Various popover element handlers
		this.popover = null;
		this.popoverOuter = null;

		if (this.options.loadTimeout) {
			this.autoloadTimeout = window.setTimeout(this._loadPage.bind(this), this.options.loadTimeout);
		}

		this.formString = '';
	},

	_loadPage: function() {

		if (this.isDestroyed) return;

		if (this.options.pageCallback) {
			return this.options.pageCallback(this.setHtml.bind(this));
		}

		if (!this.options.pageUrl) {
			return;
		}

		if (this._isLoading) return;
		this._isLoading = true;

		if (this.autoloadTimeout) {
			window.clearTimeout(this.autoloadTimeout);
			this.autoloadTimeout = null;
		}

		this.loadingAjax = $.ajax({
			dataType: 'text',
			url: this.options.pageUrl,
			type: 'GET',
			context: this,
			cache: false,
			complete: function() {
				this.loadingAjax = null;
				delete this.loadingAjax;
			},
			success: function(html) {
				this._isLoading = false;
				this.setHtml(html);
			}
		});
	},

	setHtml: function(html) {
		this.pageSource = html;
		if (this.isWaiting) {
			this.isWaiting = false;
			this._initFragment();
			this.open();
		}
	},

	_initPopover: function() {

		var self = this;

		if (this._hasInit) return;
		this._hasInit = true;

		this.popoverOuter = $($('#popover_tpl').get(0).innerHTML);
		this.popover = $('.popover-inner', this.popoverOuter).first();

		this.popoverOuter.detach().appendTo('body');

		// We want to contain clicks and such to this layer,
		// so we'll init events on it and then...
		DeskPRO_Window.initInterfaceLayerEvents(this.popoverOuter);

		// ...prevent bubbling so it doesnt activate anything below
		this.popoverOuter.on('click', function(ev) {
			ev.stopPropagation();
		});

		this.updatePositions();

		$('.close', this.popoverOuter).first().on('click', (function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			this.isWaiting = false;

			var ev = {pop: this, cancel: false};
			this.fireEvent('closeTabClick', ev);
			if (ev.cancel) {
				return;
			}

			this.close();
		}).bind(this));

		if (this.options.tabRoute) {
			$('.move-to-tab:first', this.popoverOuter).on('click', (function(ev) {

				ev.preventDefault();
				ev.stopPropagation();

				var doXfer = function() {
					this.isWaiting = false;
					DeskPRO_Window.runPageRoute(self.options.tabRoute);
					self.close(true);
				};

				if (self.hasFormsChanged()) {
					DeskPRO_Window.showConfirm(
						'This will re-load a new form in the tabbed area to the right. You will lose all unsaved changes. Do you want to continue?',
						function() {
							doXfer();
						}
					);
				} else {
					doXfer();
				}
			}).bind(this));
		} else {
			$('.move-to-tab:first', this.popoverOuter).remove();
		}

		// Handle window resizes
		if (this.options.positionMode == 'side') {
			DeskPRO_Window.layout.addEvent('resized', this.updatePositions, this);
		}
	},

	_initFragment: function() {
		if (this.page) return;
		if (!this.pageSource) return;
		if (!this.popover) return;

		var self = this;


		this.page = DeskPRO_Window.createPageFragment(this.pageSource);
		this.page.addEvent('updateUi', this.updatePositions.bind(this));

		var preparedOutput = DeskPRO_Window.prepareWidgetedHtml(this.page.getHtml());
		this.popover.html(preparedOutput.html);
		DeskPRO_Window.runWidgetedJs(this.page, preparedOutput.jsSource, preparedOutput.jsInline);

		this.pageSource = null;

		if (this.page.meta.title) {
			$('h1.tab-title', this.popoverOuter).text(this.page.meta.title);
		}

		this.page.meta.isPopover = true;
		this.page.meta.popover = this;

		this.page.fireEvent('render', [this.popover]);
		this.fireEvent('pageInit', [this, this.page]);

		var foot = $('footer.pop-footer', this.popover);
		if (foot.length) {
			foot.detach().appendTo($('> section', this.popoverOuter));
		}

		var data = self.popover.find('input, select, textarea').serializeArray();
		self.formString = JSON.stringify(data);

		this.updatePositions();
	},

	hasFormsChanged: function(do_resave) {
		var data = this.popover.find('input, select, textarea').serializeArray();
		var newFormString = JSON.stringify(data);
		var ret = false;

		if (this.formString != newFormString) {
			ret = true;
		}

		if (do_resave) {
			this.formString = newFormString;
		}

		return ret;
	},

	updatePositions: function() {

		if (this.isDestroyed) {
			return;
		}

		var changeVis = false;
		if (!this.popoverOuter.is(':visible')) {
			changeVis = true;
			this.popoverOuter.css({
				'visibility': 'hidden',
				'display': 'block'
			});
		}

		var pos = $(this.options.overFrom).offset();
		var top = pos.top - 4;
		var width = pos.left - 9;
		var bottom = 10;
		var height = '';

		var contentH = false;
		var hasHeader = !!($('> section > header', this.popoverOuter).length);
		var hasFooter = !!($('> section > footer', this.popoverOuter).length);
		var scrollContentHeight = false;

		$('.scroll-content', this.popoverOuter).each(function() {
			var $this = $(this);
			var height = $this.height();
			if ($this.data('extra-height')) {
				height += parseInt($this.data('extra-height'), 10);
			}
			if (scrollContentHeight === false || height > scrollContentHeight) {
				scrollContentHeight = height;
			}
		})

		if (scrollContentHeight !== false) {
			contentH = scrollContentHeight;
			if (hasHeader) {
				contentH += 36;
			}
			if (hasFooter) {
				contentH += 45;
			}

			contentH += 31;
		}

		if (hasHeader) $('> section > article', this.popoverOuter).removeClass('no-header');
		else $('> section > article', this.popoverOuter).addClass('no-header');

		if (hasFooter) $('> section > article', this.popoverOuter).removeClass('no-footer');
		else $('> section > article', this.popoverOuter).addClass('no-footer');

		if (contentH < 350) {
			contentH = 350;
		}

		var maxH = $(window).height() - top - 10;

		if (contentH && contentH < maxH) {
			bottom = '';
			height = contentH;
		}

		// Beside
		if (this.options.positionMode == 'side') {
			if (DeskPRO_Window.paneVis.source && DeskPRO_Window.paneVis.list) {
				if (this.options.sidePosition == 'bottom') {
					// Only calc if we dont have a bottom calculated, else it means the thing is full height
					if (!bottom) {
						top = '';
						bottom = 10;
					}
				}

				this.popoverOuter.css({
					'position': 'absolute',
					'z-index': 30001,
					'width': width+2+6, //2px for thi sborder, 6px for the popover border
					'overflow': 'auto',
					'top': top ? top-3 : '',
					'left': 9,
					'bottom': bottom,
					'height': height
				});
			} else {
				width = $(window).width() / 2;
				this.popoverOuter.css({
					'position': 'absolute',
					'z-index': 30001,
					'width': width+2+6, //2px for thi sborder, 6px for the popover border
					'overflow': 'auto',
					'top': top ? top-3 : '',
					'left': 9,
					'bottom': 10,
					'height': height
				});
			}

		// Over
		} else {
			this.popoverOuter.css({
				'position': 'absolute',
				'z-index': 30001,
				'overflow': 'auto',
				top: pos.top - 4,
				left: pos.left + 8,
				right: 3,
				'bottom': bottom,
				'height': height
			});
		}

		if (changeVis) {
			this.popoverOuter.css({
				'display': 'none',
				'visibility': 'visible'
			});
		}
	},

	isOpen: function() {
		if (this.isDestroyed || !this._hasInit) return false;

		if (this.popover && this._isOpen) {
			return true;
		}

		return false;
	},

	open: function() {
		if (this.isDestroyed) return;

		this._initPopover();
		this._initFragment();

		if (!this.page && !this.pageSource) {
			this.isWaiting = true;
			this._loadPage();
		}

		var tabAnchor = $('a.tab-anchor', this.popover);
		tabAnchor.focus();

		// Already open
		if (this.isOpen()) {
			if (this.page && !this.page.IS_ACTIVE) {
				// The initial active after the page has been init
				this.page.fireEvent('activate', [this.page]);
				this.page.fireEvent('popover-open', [this]);
			}
			return;
		}

		// Go through other instances and make sure the others arent open on the same side
		Object.each(DeskPRO.Agent.PageHelper.Popover_Instances, function(inst) {
			if (inst.isOpen() && inst.options.positionMode == this.options.positionMode) {
				inst.close();
			}
		}, this);

		this._isOpen = true;
		this.popoverOuter.show();

		var self = this;
		setTimeout(function() {
			self.updatePositions();
		}, 1000);

		if (this.page) {
			this.page.fireEvent('activate', [this.page]);
			this.page.fireEvent('popover-open', [this]);
		}
	},

	toggle: function() {
		if (this.isOpen()) {
			this.close();
		} else {
			this.open();
		}
	},

	close: function(ignoreForms) {

		if (this.isDestroyed) return;
		if (!this.isOpen()) return;

		if (!ignoreForms && this.hasFormsChanged() && this.page && !this.page.noIgnoreForm) {
			var self = this;
			DeskPRO_Window.showConfirm('Are you sure you want to close the form? Your changes will be lost.', function() {
				self.close(true);
			});
			return;
		}

		this._isOpen = false;

		this.formString = '';

		var ev = {pop: this, cancel: false};
		this.fireEvent('close', ev);
		if (ev.cancel) {
			return;
		}

		this.popoverOuter.hide();

		if (this.options.destroyOnClose && this.hasFormsChanged()) {
			if (this.page) {
				this.page.closeSelf();
			}
		} else {
			if (this.page) {
				this.page.fireEvent('deactivate');
			}
			if (this.page) {
				this.page.fireEvent('popover-closed');
			}
		}
	},

	destroy: function() {

		if (this.isDestroyed) return;

		this.isDestroyed = true;
		if (this.page) {
			this.page.fireEvent('destroy');
			this.page = null;
		}

		if (this.popover) {
			this.popoverOuter.remove();
			this.popover.remove();
		}

		if (this.autoloadTimeout) {
			window.clearTimeout(this.autoloadTimeout);
			this.autoloadTimeout = null;
		}

		if (this.loadingAjax) {
			this.loadingAjax.abort();
			this.loadingAjax = null;
		}

		this.popoverOuter = null;
		this.popover = null;
		this.options = null;

		delete DeskPRO.Agent.PageHelper.Popover_Instances[this.OBJ_ID];

		this.fireEvent('destroy', [this]);
	}
});
Orb.createNamespace('DeskPRO.Agent.PageHelper');

/**
 * This loads a fragment into an overlay window
 */
DeskPRO.Agent.PageHelper.FragmentOverlay = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(options) {
		this.options = {
			routeData: {},
			positionAbove: true,
			zIndex: 0
		};

		this.setOptions(options);

		/**
		 * The page fragment once its initialized
		 */
		this.page = null;

		this._initOverlay();
		this.reloadFragment();
	},

	reloadFragment: function() {
		var routeData = this.options.routeData;

		if (this.loadingXhr) {
			this.loadingXhr.abort();
			this.loadingXhr = null;
		}

		this.loadingXhr = DeskPRO_Window._doAjaxLoadRoute(routeData.url, routeData, (function(data) {

			if (this.page) {
				this.page.fireEvent('destroy');
				this.page = null;
				this.wrapper.empty();
			}

			this.page = DeskPRO_Window.createPageFragment(data);

			this.page.setMetaData('routeUrl', routeData.url);
			if (routeData) {
				this.page.setMetaData('routeData', routeData);
			}

			this.page.fragmentOverlay = this;

			this.wrapper.removeClass('overlay-loading');
			$('section.dp-overlay', this.wrapper).empty().html(data);

			$('header .close-trigger', this.wrapper).on('click', (function(ev) {
				ev.stopPropagation();
				ev.preventDefault();
				this.close();
			}).bind(this));

			this.updatePositions();
			this.page.fireEvent('render', [this.wrapper]);
			this.fireEvent('pageInit', [this.page, this]);
		}).bind(this));
	},

	_initOverlay: function() {

		if (this._hasInit) return;
		this._hasInit = true;

		this.wrapper = $(DeskPRO_Window.util.getPlainTpl($('#fragment_overlay_tpl')));
		this.wrapper.hide().appendTo('body');

		this.wrapper.on('click', function(ev) {
			ev.stopPropagation();
		});

		this.backdropEl = $('<div class="backdrop dp-overlay-backdrop dp-fragment-overlay-backdrop" />');
		this.backdropEl.hide().appendTo('body');
		this.backdropEl.on('click', (function(ev) {
			ev.stopPropagation();
			this.close();
		}).bind(this));

		$('header .close-trigger', this.wrapper).on('click', (function(ev) {
			ev.stopPropagation();
			ev.preventDefault();
			this.close();
		}).bind(this));

		this.updatePositions();
		this.wrapper.show();
		this.backdropEl.show();
	},

	/**
	 * Update the positions of the elements
	 */
	updatePositions: function() {

		var article = $('section.dp-overlay > article', this.wrapper);

		var w = parseInt(article.data('width'));
		var h = parseInt(article.data('height'));

		if (w) this.wrapper.css('width', w + 5);
		if (h) this.wrapper.css('height', h + 5);

		if (article.data('add-class')) {
			this.wrapper.addClass(article.data('add-class'));
		}

		var w = this.wrapper.outerWidth();
		var pageW = $(window).width();
		var leftForCenter = (pageW - w) / 2;

		var h = this.wrapper.outerHeight();
		var pageH = $(window).height();
		var topForCenter = (pageH - h) / 2;

		this.wrapper.css({
			'top': topForCenter,
			'left': leftForCenter
		});

		if (this.options.zIndex) {
			this.wrapper.css('z-index', this.options.zIndex+1);
			this.backdropEl.css('z-index', this.options.zIndex);
		} else if (this.options.positionAbove) {
			var zIndex = Orb.findHighestZindex() + 10;

			this.wrapper.css('z-index', zIndex+1);
			this.backdropEl.css('z-index', zIndex);
		}

		if ($('section > header', this.wrapper).length) {
			this.wrapper.removeClass('no-header');
		} else {
			this.wrapper.addClass('no-header');
		}

		if ($('section > footer', this.wrapper).length) {
			this.wrapper.removeClass('no-footer');
		} else {
			this.wrapper.addClass('no-footer');
		}

		//this.backdropEl.css('z-index', parseInt(this.wrapper.css('z-index'))-1);
	},

	close: function() {
		this.destroy();
	},

	destroy: function() {

		if (this.loadingXhr) {
			this.loadingXhr.abort();
			this.loadingXhr = null;
		}

		if (this.page) {
			this.page.fireEvent('destroy');
			this.page = null;
		}

		if (this.wrapper) {
			this.wrapper.remove();
			this.backdropEl.remove();
		}

		this.wrapper = null;
		this.backdropEl = null;

		this.fireEvent('destroy', [this]);
	}
});

Orb.createNamespace('DeskPRO.Agent.PageHelper');

DeskPRO.Agent.PageHelper.ValidatingEdit = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(page, options) {
		var self = this;

		this.page = page;

		this.options = {
			typename: '',
			contentId: 0,
			singleType: ''
		};
		this.setOptions(options);

		$('button.approve-trigger', this.page.wrapper).on('click', this.approveEdit.bind(this));
		$('button.disapprove-trigger', this.page.wrapper).on('click', this.showDisapproveForm.bind(this));
		$('button.disapprove2-trigger', this.page.wrapper).on('click', this.disapproveEdit.bind(this));
		$('button.skip-trigger', this.page.wrapper).on('click', this.skipValidateEdit.bind(this));
	},

	//#################################################################
	//# Validation controls
	//#################################################################

	showDisapproveForm: function() {
		$('.validating-bar:first .options', this.page.wrapper).hide();
		$('.validating-bar:first .disapprove-form', this.page.wrapper).show();
	},

	approveEdit: function() {
		$.ajax({
			url: BASE_URL + 'agent/publish/content/approve/' + this.options.typename + '/' + this.options.contentId + '.json?specific_type=' + this.options.singleType,
			type: 'POST',
			context: this,
			dataType: 'json',
			success: function(info) {
				if (info.next_url) {
					DeskPRO_Window.runPageRoute('page:' + info.next_url);
				}

				DeskPRO_Window.getMessageBroker().sendMessage('publish.validating.list-remove', {
					typename: this.options.typename,
					contentId: this.options.contentId
				});

				DeskPRO_Window.removePage(this.page);

				if (DeskPRO_Window.sections.feedback_section) {
					DeskPRO_Window.sections.feedback_section.reload();
				}
			}
		});
	},

	disapproveEdit: function() {

		var reason = $('.validating-bar .disapprove-reason', this.page.wrapper).val().trim();

		$.ajax({
			url: BASE_URL + 'agent/publish/content/disapprove/' + this.options.typename + '/' + this.options.contentId + '.json?specific_type=' + this.options.singleType,
			type: 'POST',
			context: this,
			data: {reason: reason},
			dataType: 'json',
			success: function(info) {
				if (info.next_url) {
					DeskPRO_Window.runPageRoute('page:' + info.next_url);
				}

				DeskPRO_Window.getMessageBroker().sendMessage('publish.validating.list-remove', {
					typename: this.options.typename,
					contentId: this.options.contentId
				});

				if (DeskPRO_Window.sections.feedback_section) {
					DeskPRO_Window.sections.feedback_section.reload();
				}

				DeskPRO_Window.removePage(this.page);
			}
		});
	},

	skipValidateEdit: function() {
		$.ajax({
			url: BASE_URL + 'agent/publish/content/get-next-validating/' + this.options.typename + '/' + this.options.contentId + '.json?specific_type=' + this.options.singleType,
			type: 'POST',
			context: this,
			dataType: 'json',
			success: function(info) {
				if (info.next_url) {
					DeskPRO_Window.runPageRoute('page:' + info.next_url);
				}

				DeskPRO_Window.getMessageBroker().sendMessage('publish.validating.list-remove', {
					typename: this.options.typename,
					contentId: this.options.contentId
				});

				DeskPRO_Window.removePage(this.page);
			}
		});
	}
});

Orb.createNamespace('DeskPRO.Agent.PageHelper');

/**
 * This handles pages that accept related content linking
 */
DeskPRO.Agent.PageHelper.RelatedContent = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(page, options) {
		var self = this;
		this.page = page;

		this.options = {

			/**
			 * These are the ID for *this* content,
			 * used to make sure we dont link ourselves
			 */
			typename: '',
			content_id: 0,

			/**
			 * The main wrapper element content links are displayed
			 */
			listEl: null,

			/**
			 * The template to use when we need to render a new related content item
			 */
			newItemTpl: '#related_content_newitem',

			disabled: false
		};

		this.setOptions(options);

		this.listEl = $(this.options.listEl);

		this.listEl.on('click', '.remove', function(ev) {
			ev.stopPropagation();
			ev.preventDefault();

			var li = $(this);
			var x = 0;
			while (!li.is('.related-content')) {
				if (x++ > 15) return;
				li = li.parent();
			}

			self.removeLinkByElement(li);
		});

		this.page.getEl('count_related').text(this.listEl.find('li.related-content').length);
	},

	isViewing: function() {
		var tab = this.page.bodyTabs.lastActiveTabContent;
		if (tab && tab.is('.related-content')) {
			return true;
		}

		return false;
	},

	_refreshInstructionEl: function() {
		if ($('.related-content:not(.removing):first', this.listEl).length) {
			// There is related content.
			// Just make sure the note is hidden. The sections
			// auto show themselves
			$('.no-related-content', this.listEl).hide();
		} else {
			// There is no related content to list
			// Hide sections, and show note
			$('.related-section', this.listEl).hide();
			$('.no-related-content', this.listEl).show();
		}

		this.page.getEl('count_related').text(this.listEl.find('li.related-content:not(.removing)').length);
	},


	/**
	 * Set the active related list controller
	 *
	 * @param controller
	 */
	setActiveRelatedListController: function(controller) {
		this.relatedContentList = controller;
	},


	/**
	 * Add a new linked content
	 *
	 * @param typename
	 * @param content_id
	 */
	addLink: function(typename, content_id, title, route) {
		if (this.isLinked(typename, content_id)) {
			return;
		}

		var li = $(DeskPRO_Window.util.getPlainTpl(this.options.newItemTpl));
		$('.link-title', li).text(title);
		$('.link-route', li).data('route', route);
		li.addClass('related-content').addClass(typename + '-' + content_id);
		li.data('content-type', typename);
		li.data('content-id', content_id);

		li.hide();

		var wrapper = $('.'+ typename +'.related-section', this.listEl);
		if (wrapper.length) {
			var list = $('.related-list:first', wrapper);
			list.append(li);
			if (!wrapper.is(':visible')) {
				li.show();
				wrapper.slideDown('fast');
			} else {
				li.slideDown('fast');
			}
		} else {
			if (!this.listEl.is(':visible')) {
				li.show();
				this.listEl.append(li).slideDown('fast');
			} else {
				this.listEl.append(li);
				li.slideDown('fast');
			}
		}

		if (this.relatedContentList) {
			this.relatedContentList.elementIsLinked(typename, content_id);
		}

		this._refreshInstructionEl();

		this.fireEvent('contentLinked', [typename, content_id, title, route, this]);
	},


	/**
	 * Same as addLink but with an element reference
	 *
	 * @param el
	 */
	addLinkByElement: function(el) {
		var typename = el.data('content-type');
		var content_id = el.data('content-id');

		if (el.data('route')) {
			var route = el.data('route');
			var title = el.text().trim();
		} else {
			var routeEl = $('[data-route]:first', el);
			var route = routeEl.data('route');
			var title = routeEl.text().trim();
		}

		if (el.data('route-title')) {
			title = el.data('route-title');
			if (title == '@text') {
				title = el.text().trim().replace(/[\n\r]/g, ' ').replace(/\s+/g, ' ');
			} else if (title == '@title') {
				title = el.attr('title');
			} else if (title.test(/^@selector\((.*?)\)$/)) {
				var sel = title.match(/^@selector\((.*?)\)$/)[1];
				var titleEl = null;
				if (sel[0] == "#") {
					titleEl = $(sel);
				} else {
					titleEl = $(sel, el);
				}

				if (titleEl && titleEl.length) {
					title = titleEl.text().trim().replace(/[\n\r]/g, ' ').replace(/\s+/g, ' ');
				} else {
					title = el.text().trim();
				}
			}
		}

		return this.addLink(typename, content_id, title, route);
	},


	/**
	 * Remove a linked content that is described in an element
	 *
	 * @param el
	 */
	removeLinkByElement: function(el) {
		var typename = el.data('content-type');
		var content_id = el.data('content-id');

		if (!typename || !content_id) {
			DP.console.error("No content linked on element: %o", el);
			return false;
		}

		this.removeLink(typename, content_id);

		return true;
	},


	/**
	 * Remove a link based on a typename and content_id.
	 *
	 * @param typename
	 * @param content_id
	 */
	removeLink: function(typename, content_id) {

		var listItem = this.getLinkElementInList(typename, content_id);
		if (!listItem) {
			return;
		}

		listItem.addClass('removing').slideUp('fast', function() {
			listItem.remove();

			var wrapper = $('.'+ typename +'.related-section', this.listEl);
			if (wrapper.length) {
				if (!$('.related-content:first', wrapper).length) {
					wrapper.slideUp('fast');
				}
			}
		});

		if (this.relatedContentList) {
			this.relatedContentList.elementIsUnlinked(typename, content_id);
		}

		this._refreshInstructionEl();

		this.fireEvent('contentUnlinked', [typename, content_id, this]);
	},


	/**
	 * Get the element description for a linked thing
	 *
	 * @param typename
	 * @param content_id
	 */
	getLinkElementInList: function(typename, content_id) {
		var el = $('.' + typename + '-' + content_id + '.related-content:first', this.listEl);

		if (!el.length) {
			return null;
		}

		return el;
	},


	/**
	 * Check if a certain content is already linked
	 *
	 * @param typename
	 * @param content_id
	 */
	isLinked: function(typename, content_id) {
		return $('.' + typename + '-' + content_id + '.related-content:first', this.listEl).length;
	},


	/**
	 *
	 * @param typename
	 * @param content_id
	 */
	isLinkable: function(typename, content_id) {
		if (this.options.disabled) {
			return false;
		}
		if (typename == this.options.typename && content_id == this.options.content_id) {
			return false;
		}

		var ev = {linkable: true};
		this.fireEvent('checkLinkable', [ev, typename, content_id, this]);

		if (ev.linkable) {
			return true;
		} else {
			return false;
		}
	},


	/**
	 * Same as isLinkable but with an element reference
	 *
	 * @param el
	 */
	elementIsLinkable: function(el) {
		if (this.options.disabled) {
			return false;
		}
		return this.isLinkable(el.data('content-type'), el.data('content-id'));
	},


	/**
	 * Same as isLinked but with an element reference.
	 *
	 * @param el
	 */
	elementIsLinked: function(el) {
		return this.isLinked(el.data('content-type'), el.data('content-id'));
	}
});

Orb.createNamespace('DeskPRO.Agent.PageHelper');

/**
 * This handles the listing page of content that can be linked with others
 */
DeskPRO.Agent.PageHelper.RelatedContentList = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(page, options) {
		var self = this;
		this.page = page;

		this.options = {
			/**
			 * The main list element that contains rows.
			 */
			contentListEl: null
		};

		this.setOptions(options);

		this.contentListEl = $(this.options.contentListEl);

		DeskPRO_Window.getTabWatcher().addTabTypeWatcher('*', this);
		var types = ['article', 'download', 'news', 'feedback'];

		this.addEvent('watchedTabActivated', function(tab) {
			// Instant feedback: Enable all buttons again, but disable ourselves
			// when the tab actually loads the proper routines will be called
			// and the correct items will be shown/hidden
			$('.related-is-linkable', this.contentListEl).each(function() {
				var el = $(this);
				el.removeClass('related-not-linkable').removeClass('related-is-linked');
			});
			if (tab.page && tab.page.meta.routeData && tab.page.meta.routeData.routeTriggerEl) {
				var row = $(tab.page.meta.routeData.routeTriggerEl);
				if (row.hasClass('row-item')) {
					row.addClass('related-not-linkable').removeClass('related-is-linked');
				}
			}

			var tabtype = DeskPRO_Window.getTabWatcher().getTabType(tab);
			if (types.indexOf(tabtype) !== -1) {

				if (tab.page && tab.page.relatedContent) {
					if (tab.page.relatedContent.isViewing()) {
						$('body').addClass('related-controls-on');
					} else {
						$('body').removeClass('related-controls-on');
					}
				} else {
					$('body').removeClass('related-controls-on');
				}

				this.enableControls(tab);
			} else {
				this.disableControls();
			}
		}, this);
		this.addEvent('watchedTabDeactivated', function(tab, isLast) {
			if (isLast) {
				this.disableControls();
				$('body').removeClass('related-controls-on');
			}
		}, this);

		var selectedTabType = DeskPRO_Window.getTabWatcher().getActiveTabType();
		if (types.indexOf(selectedTabType) !== -1) {
			this.enableControls(DeskPRO_Window.getTabWatcher().getActiveTab());
		}

		var findLinkable = function(el) {
			var x = 0;
			while (!el.is('.related-is-linkable')) {
				if (x++ > 15) return null;
				el = el.parent();
			}

			return el;
		};

		this.contentListEl.on('click', '.related-link', function(ev) {
			ev.stopPropagation();
			ev.preventDefault();

			var el = findLinkable($(this));
			if (!el) {
				return;
			}

			self.fireEvent('relatedLinkClick', [el, $(this), this]);

			var tab = DeskPRO_Window.getTabWatcher().getActiveTab();
			if (!tab || !tab.page || !tab.page.relatedContent) {
				return;
			}

			tab.page.relatedContent.addLinkByElement(el);
		});

		this.contentListEl.on('click', '.related-unlink', function(ev) {
			ev.stopPropagation();
			ev.preventDefault();

			var el = findLinkable($(this));
			if (!el) {
				return;
			}

			self.fireEvent('relatedUnlinkClick', [el, $(this), this]);

			var tab = DeskPRO_Window.getTabWatcher().getActiveTab();
			if (!tab || !tab.page || !tab.page.relatedContent) {
				return;
			}

			tab.page.relatedContent.removeLinkByElement(el);
		});
	},

	enableControls: function(tab) {

		var page = tab.page;
		if (!page.relatedContent) {
			return;
		}

		this.activePage = page;

		page.relatedContent.setActiveRelatedListController(this);

		var self = this;
		$('.related-is-linkable', this.contentListEl).each(function() {
			var el = $(this);
			el.removeClass('related-not-linkable').removeClass('related-is-linked');

			if (page.relatedContent.elementIsLinkable(el)) {
				if (page.relatedContent.elementIsLinked(el)) {
					el.addClass('related-is-linked');
				}
			} else {
				el.addClass('related-not-linkable');
			}
		});

		this.contentListEl.addClass('with-related-content-controls');

		this.fireEvent('relatedControlsActivated', [this.contentListEl, this]);
	},

	disableControls: function() {
		this.activePage = null;
		this.contentListEl.removeClass('with-related-content-controls');
		this.fireEvent('relatedControlsDeactivated', [this.contentListEl, this]);
	},

	elementIsLinked: function(typename, content_id) {
		$('.' + typename + '-' + content_id + '.related-is-linkable', this.contentListEl).addClass('related-is-linked');
	},

	elementIsUnlinked: function(typename, content_id) {
		$('.' + typename + '-' + content_id + '.related-is-linkable', this.contentListEl).removeClass('related-is-linked');
	},

	destroy: function() {
		if (this.activePage) {
			this.activePage.relatedContent.setActiveRelatedListController(null);
		}
	}
});

Orb.createNamespace('DeskPRO.Agent.PageHelper');

DeskPRO.Agent.PageHelper.Comments = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(page, options)  {
		var self = this;
		this.page = page;

		this.options = {
			/**
			 * The main comments wrapper element
			 */
			commentsWrapper: null
		};

		this.setOptions(options);

		this.commentsWrapper = $(this.options.commentsWrapper);

		this.commentsWrapper.on('click', '.comment-edit-btn', function(ev) {
			ev.stopPropagation();
			ev.preventDefault();
			self.getCommentMenu().open(ev);
		});

		this.commentsWrapper.on('click', '.comment-validate-btn', function(ev) {
			ev.stopPropagation();
			ev.preventDefault();
			self.getCommentValidationMenu().open(ev);
		});
	},

	getCommentMenu: function() {
		if (this._commentMenu) return this._commentMenu;

		var clone = $('#comment_tools_menu').first().clone(false);
		clone.attr('id', Orb.getUniqueId('el'));

		var self = this;
		this._commentMenu = new DeskPRO.UI.Menu({
			menuElement: clone,
			onItemClicked: function(info) {
				var commentEl = $(info.menu.getOpenTriggerElement()).parent().parent().parent();
				var action = $(info.itemEl).data('action');

				switch (action) {
					case 'edit':
						self.editComment(commentEl, commentEl.data('content-type'), commentEl.data('comment-id'));
						break;

					case 'delete':
						self.deleteComment(commentEl, commentEl.data('content-type'), commentEl.data('comment-id'));
						break;

					case 'create-ticket':
						$.ajax({
							url: BASE_URL + 'agent/publish/comments/new-ticket-info/' + commentEl.data('content-type') + '/' + commentEl.data('comment-id') + '.json',
							type: 'GET',
							dataType: 'json',
							success: function(data) {
								DeskPRO_Window.newTicketLoader.open(function(page) {
									page.setNewByComment(data);
								});
							}
						});
						break;
				}
			}
		});

		return this._commentMenu;
	},

	getCommentValidationMenu: function() {
		if (this._commentValidationMenu) return this._commentValidationMenu;

		var clone = $('#comment_validation_menu').first().clone(false);
		clone.attr('id', Orb.getUniqueId('el'));

		var self = this;
		this._commentValidationMenu = new DeskPRO.UI.Menu({
			menuElement: clone,
			onItemClicked: function(info) {
				var commentEl = $(info.menu.getOpenTriggerElement()).closest('article.content-message');
				var action = $(info.itemEl).data('action');

				switch (action) {
					case 'approve':
						self.approveComment(commentEl, commentEl.data('content-type'), commentEl.data('comment-id'));
						break;

					case 'delete':
						self.deleteComment(commentEl, commentEl.data('content-type'), commentEl.data('comment-id'));
						break;
				}
			}
		});

		return this._commentValidationMenu;
	},


	editComment: function(commentEl, typename, commentId) {
		commentEl.addClass('gear-loading');

		var self = this;
		$.ajax({
			url: BASE_URL + 'agent/publish/comments/info/'+typename+'/'+commentId,
			type: 'GET',
			dataType: 'json',
			context: this,
			success: function(data) {
				var editEl = $(DeskPRO_Window.util.getPlainTpl('#comment_edit_tpl'));
				$('.save-trigger', editEl).on('click', function(ev) {
					ev.preventDefault();
					self._saveEditComment(commentEl, editEl, typename, commentId);
				});
				$('.cancel-trigger', editEl).on('click', function(ev) {
					ev.preventDefault();
					self._closeEditComment(commentEl, editEl);
				});
				editEl.hide();

				$('textarea.comment', editEl).val(data.comment_text);

				var rendered = $('.rendered-message', commentEl);
				editEl.insertBefore(rendered);

				rendered.slideUp('fast', function() {
					editEl.slideDown('fast');
				});
			},
			complete: function() {
				commentEl.removeClass('gear-loading');
			}
		});
	},

	_saveEditComment: function(commentEl, editEl, typename, commentId) {
		commentEl.addClass('gear-loading');

		$.ajax({
			url: BASE_URL + 'agent/publish/comments/save-comment/'+typename+'/'+commentId,
			type: 'POST',
			data: {
				comment: $('textarea.comment', editEl).val()
			},
			dataType: 'json',
			context: this,
			error: function() {

			},
			success: function(data) {
				var rendered = $('.rendered-message', commentEl);
				rendered.html(data.comment_html);
				this._closeEditComment(commentEl, editEl);
			},
			complete: function() {
				commentEl.removeClass('gear-loading');
			}
		});
	},

	_closeEditComment: function(commentEl, editEl) {
		var rendered = $('.rendered-message', commentEl);
		editEl.slideUp('fast', function() {
			rendered.slideDown();
			editEl.remove();
		});
	},

	deleteComment: function(commentEl, typename, commentId) {
		commentEl.fadeOut();
		$.ajax({
			url: BASE_URL + 'agent/publish/comments/delete/'+typename+'/'+commentId,
			type: 'POST',
			context: this,
			dataType: 'json',
			error: function() {
				commentEl.show();
			},
			success: function(data) {
				commentEl.remove();

				if (DeskPRO_Window.sections.publish_section) {
					DeskPRO_Window.sections.publish_section.modCommentCount(commentEl.data('content-type'), '-');
				}
			}
		});
	},

	approveComment: function(commentEl, typename, commentId) {
		commentEl.removeClass('validating').addClass('gear-loading');

		$.ajax({
			url: BASE_URL + 'agent/publish/comments/approve/'+typename+'/'+commentId,
			type: 'POST',
			context: this,
			dataType: 'json',
			success: function() {
				commentEl.find('.comment-validate-btn').hide();
			},
			error: function() {
				commentEl.addClass('validating');
			},
			complete: function() {
				commentEl.removeClass('gear-loading');
			}
		});
	}
});

Orb.createNamespace('DeskPRO.Agent.PageHelper');

DeskPRO.Agent.PageHelper.MiscContent = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(page, options)  {
		var self = this;
		this.page = page;

		this.options = {
			revisionCompareUrl: ''
		};

		this.setOptions(options);

		this.wrapper = this.page.wrapper;
		this.getEl   = this.page.getEl;

		this._initCompareRevs();
	},

	//#################################################################
	//# Compare revisions
	//#################################################################

	_initCompareRevs: function() {
		$('.compare-trigger', this.wrapper).on('click', this.showCompareRev.bind(this));

		var all_checks = $('input.rev-compare-check', this.wrapper);
		var counter = 0;

		var table = this.wrapper.find('.revision-compare-table');
		table.on('click', 'input.rev-compare-check', function() {
			if ($(this).is(':checked')) {
				var checked = all_checks.filter(':checked');
				if (checked.length > 2) {
					checked.each(function() {
						if ($(this).data('check-count') == counter) {
							$(this).prop('checked', false);
						}
					});
				}

				$(this).data('check-count', ++counter);
			}
		});

		var count = table.find('input.rev-compare-check').length;
		this.page.getEl('count_revs').text(count);
	},

	showCompareRev: function() {

		var checks = $('.revision-compare-table input.rev-compare-check:checked', this.wrapper);

		var old_id = checks.first().val();
		var new_id = checks.last().val();

		if (!old_id || !new_id || old_id == new_id) {
			console.log('bad compare');
			return;
		}

		var overlay = new DeskPRO.UI.Overlay({
			triggerElement: $('button.compare-trigger', this.wrapper),
			contentMethod: 'ajax',
			contentAjax: {
				url: this.options.revisionCompareUrl.replace('{OLD}', old_id).replace('{NEW}', new_id)
			},
			destroyOnClose: true
		});

		overlay.openOverlay();
	}
});

Orb.createNamespace('DeskPRO.Agent.PageHelper');

DeskPRO.Agent.PageHelper.ListNav = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(page, options) {
		options = options || {};

		this.page = page;
		this.listSelector    = options.listSelector || false;
		this.itemSelector    = options.itemSelector || 'article.row-item';
		this.activeClass     = 'selection-on';
		this.scrollContainer = this.page.wrapper.find('.scroll-content').first();
		this.scrollView      = this.page.wrapper.find('.scroll-viewport').first();
	},

	getCurrentSelection: function() {
		var list = this.listSelector ? this.page.wrapper.find(this.listSelector) : this.page.wrapper;
		var el = list.find(this.itemSelector).filter('.' + this.activeClass);

		if (el[0]) {
			return el;
		}

		return null;
	},

	scrollIntoView: function(row) {
		if (!row || !row.position()) {
			return;
		}

		if (this.scrollContainer.height() <= this.scrollView.height()) {
			return;
		}

		var viewTop = Math.abs(parseInt(this.scrollContainer.css('top')));
		var viewBtm = viewTop + this.scrollContainer.parent().height();

		var elTop = row.position().top;
		var elBtm = elTop + row.height() + row.height() + 20;

		if (viewTop < elTop && viewBtm > elBtm) {
			// Already visble
			return;
		}

		var scrollTo = elBtm - this.scrollContainer.parent().height();
		if (scrollTo < 0) {
			scrollTo = 0;
		}

		var scroll = this.page.wrapper.find('.with-scrollbar').first();
		scroll.trigger('goscrollto', [scrollTo]);
	},

	down: function() {
		var list = this.listSelector ? this.page.wrapper.find(this.listSelector) : this.page.wrapper;

		var current = this.getCurrentSelection();
		var next;
		if (current) {
			next = current.next(this.itemSelector);
			if (!next || !next.closest(list[0])[0]) {
				next = current;
			}
			current.removeClass(this.activeClass);
		} else {
			next = list.find(this.itemSelector).first();
		}

		next.addClass(this.activeClass);
		this.scrollIntoView(next);

		return next;
	},

	up: function() {
		var list = this.listSelector ? this.page.wrapper.find(this.listSelector) : this.page.wrapper;

		var current = this.getCurrentSelection();
		var next;
		if (current) {
			next = current.prev(this.itemSelector);
			if (!next || !next.closest(list[0])[0]) {
				next = current;
			}
			current.removeClass(this.activeClass);
		} else {
			next = list.find(this.itemSelector).first();
		}

		next.addClass(this.activeClass)
		this.scrollIntoView(next);

		return next;
	},

	enter: function() {
		var current = this.getCurrentSelection();
		if (current) {
			DeskPRO_Window.runPageRouteFromElement(current);
		}
	},

	check: function() {
		var current = this.getCurrentSelection();
		if (current) {
			var check = current.find('input.item-select');
			if (check.prop('checked')) {
				check.prop('checked', false);
			} else {
				check.prop('checked', true);
			}
		}
	}
});

Orb.createNamespace('DeskPRO.Agent.PageHelper');

DeskPRO.Agent.PageHelper.AutoSave = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(options) {
		var self = this;
		this.options = {
			/**
			 * The field to attach autosave to
			 * @option {jQuery}
			 */
			field: null,

			/**
			 * The name to send in the POST request. Defaults
			 * to the name of the field.
			 * @option {String}
			 */
			fieldName: null,

			/**
			 * The URL to POST data to
			 * @option {String}
			 */
			saveUrl: null,

			/**
			 * Extra data to send
			 * @option {Array}
			 */
			postData: [],

			/**
			 * Whether to show the save puff when a save completes
			 * @option {Boolean}
			 */
			showSavePuff: true
		};

		this.setOptions(options);

		this.field = $(this.options.field);

		if (this.field.is('select, :checkbox, :radio')) {
			this.field.on('change', this.save.bind(this));
		} else {
			this.intervalCaller = new DeskPRO.IntervalCaller({
				resetTimeForce: 2500,
				timeout: 1500,
				callback: this.save.bind(this)
			});

			var touchFn = function() {
				self.fireEvent('touch', [self.field, self]);
				if (self.intervalCaller) {
					self.intervalCaller.touch();
				}
			}
			this.field.on('change', touchFn).on('keypress', touchFn);
		}
	},

	save: function() {
		var self = this;
		var postData = Array.clone(this.options.postData);

		var fieldName = this.options.fieldName || this.field.attr('name');
		var fieldVal  = this.field.val();

		postData.push({
			name: fieldName,
			value: fieldVal
		});

		self.fireEvent('preSave', [postData, self.field, self]);

		$.ajax({
			url: this.options.url,
			type: 'POST',
			data: postData,
			success: function(data) {
				self.fireEvent('save', [data, postData, self.field, self]);
				if (self.options.showSavePuff) {
					DeskPRO_Window.util.showSavePuff(self.field);
				}
			}
		});
	},

	destroy: function() {
		this.intervalCaller.destroy();
		this.field = null;
		this.options = null;
	}
});

Orb.createNamespace('DeskPRO.Agent.PageHelper');

DeskPRO.Agent.PageHelper.StateSaver = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(options) {
		var self = this;
		this.options = {
			/**
			 * The ID that will be used with the preferences
			 */
			stateId: '',

			/**
			 * How long until the state expires
			 */
			expireTime: '+7 days',

			/**
			 * How often to save
			 */
			time: 1000,

			/**
			 * Theres always a "change". Ie it always saves,
			 * no need to triggerChange()
			 */
			alwaysChanged: false,

			/**
			 * A container to listen in on all form elements for changes.
			 * This automatically calls triggerChange() for you.
			 */
			listenOn: null,

			/**
			 * Callback that fetches the data to be saved.
			 * This will serialzie all form fields in listenOn by default.
			 */
			callback: null
		};

		this.setOptions(options);

		if (this.options.listenOn) {
			var wrap = $(this.options.listenOn);
			$(':input, textarea, select', wrap).on('change', function() {
				self.triggerChange();
			});
			$('input[type=text], textarea', wrap).on('keypress', function() {
				self.triggerChange();
			});

			if (!this.options.callback) {
				this.options.callback = function() {
					if (wrap.is('form')) {
						return wrap.serializeArray();
					} else {
						return $(':input, textarea, select', wrap).serializeArray();
					}
				};
			}
		}

		if (!this.options.callback) {
			this.options.callback = function() {};
			DP.console.error('No callback for state save');
		}

		this.doRestartTimer = false;
		this.hasChanged = false;

		if (this.alwaysChanged) {
			this.restartTimer();
		}
	},

	/**
	 * Declare that something has changed so the state needs to be updated
	 */
	triggerChange: function() {
		this.hasChanged = true;
		this.restartTimer();
	},


	/**
	 * Restart the timer
	 */
	restartTimer: function() {

		if (this.ajax) {
			this.doRestartTimer = true;
		}

		if (this.timer) {
			window.clearTimeout(this.timer);
			this.timer = null;
		}

		this.doRestartTimer = false;

		this.timer = window.setTimeout(this.saveState.bind(this), this.options.time);
	},


	/**
	 * Send the AJAX request that saves the state
	 */
	saveState: function() {
		var setData = this.options.callback();
		this.hasChanged = false;

		this.fireEvent('beforeSaveState', [setData]);

		var namePart = function(name) {
			if (name.indexOf('[') === -1) {
				name = '[' + name + ']';
			}  else {
				name = name.replace(/^([\w\d]+)\[(.*?)$/, '[$1][$2');
			}

			return name;
		};

		var data = [];
		data.push({
			name: 'prefs_expire[agent.ui.state.'+this.options.stateId+']',
			value: this.options.expireTime
		});
		if (typeOf(setData) == 'array') {
			Array.each(setData, function(x) {
				data.push({
					name: 'prefs[agent.ui.state.'+this.options.stateId+']' + namePart(x.name),
					value: x.value
				});
			}, this);
		} else {
			data.push({
				name: 'prefs[agent.ui.state.'+this.options.stateId+']',
				value: text
			});
		}

		$.ajax({
			url: BASE_URL + 'agent/misc/ajax-save-prefs',
			type: 'POST',
			data: data,
			context: this,
			complete: function() {
				this.ajax = null;

				if (this.doRestartTimer || this.alwaysChanged) {
					this.restartTimer();
				}
			}
		});
	},

	resetState: function() {
		var data = [];
		data.push({
			name: 'prefs[agent.ui.state.'+this.options.stateId+']',
			value: ''
		});
		data.push({
			name: 'prefs_expire[agent.ui.state.'+this.options.stateId+']',
			value: ''
		});

		window.setTimeout(function() {
			$.ajax({
				url: BASE_URL + 'agent/misc/ajax-save-prefs',
				type: 'POST',
				data: data,
				context: this
			});
		}, 150);
	},

	stop: function() {
		if (this.ajax) {
			this.ajax.abort();
		}
		if (this.timer) {
			window.clearTimeout(this.timer);
			this.timer = null;
		}
	},

	destroy: function() {
		if (this.ajax) {
			this.ajax.abort();
		}
		if (this.timer) {
			window.clearTimeout(this.timer);
			this.timer = null;
		}
	}
});

Orb.createNamespace('DeskPRO.Agent.PageHelper');

DeskPRO.Agent.PageHelper.Results = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(page, options) {
		var self = this;

		this.page = page;
		this.wrapper = page.wrapper;

		this.options = {
			/**
			 * The display options helper
			 * Defaults to page.displayOptions
			 * @option {DeskPRO.Agent.PageHelper.DisplayOptions}
			 */
			displayOptions: null,

			/**
			 * The container to append results to
			 * Defaults to 'wrapper .list-listing'
			 * @option {jQuery}
			 */
			resultsContainer: null,

			/**
			 * The row that contains the "more" button, "no more results" etc
			 * Defaults to 'wrapper footer.results-nav'
			 * @option {jQuery}
			 */
			navEl: null,

			/**
			 * The [xx] in "Showing [xx] of xx results" line
			 * Defaults to 'wrapper .results-showing-count'
			 * @option {jQuery}
			 */
			showingCountEl: null,

			/**
			 * The IDs of all the results
			 * @option {Array}
			 */
			resultIds: null,

			/**
			 * Total count of results
			 * @option {Integer}
			 */
			totalCount: 0,

			/**
			 * The wrapper around a result row item
			 * @option {String}
			 */
			resultRowSelector: 'article.row-item',

			/**
			 * How many results to show per page
			 * @option {Integer}
			 */
			perPage: 50,

			/**
			 * Refresh mode loads a new page instead of injecting new
			 * rows into the already loaded page
			 *
			 * @option {Boolean}
			 */
			refreshMode: false,

			/**
			 * @option {Integer}
			 */
			currentPage: 1,

			tabResultCountEls: null,

			preFetchCallback: null,

			infiniteScroll: false,

			infiniteScrollTriggerOffset: 800,

			infiniteScrollLoadFilter: null,

			infiniteScrollTarget: null
		};
		this.setOptions(options);

		this.displayOptions    = this.options.displayOptions || page.displayOptions;
		this.resultsContainer  = this.options.resultsContainer || $('.list-listing', this.wrapper);
		this.navEl             = this.options.navEl || $('footer.results-nav', this.wrapper);
		this.showingCountEl    = this.options.showingCountEl || $('.results-showing-count', this.wrapper);
		this.totalCountEl      = this.options.totalCountEl || $('.results-total-count', this.wrapper);
		this.tabResultCountEls = this.options.tabResultCountEls || this.wrapper.find('.results-count-display');

		this.pageNav           = $('ul.pagenav', this.navEl);
		this.prevBtn           = $('> li.prev', this.pageNav);
		this.nextBtn           = $('> li.next', this.pageNav);

		this.resultCount   = this.options.resultIds ? this.options.resultIds.length : this.options.totalCount;

		this.scrollableEl = this.resultsContainer.closest('.with-scrollbar');
		if (this.options.infiniteScroll) {
			this.options.infiniteScroll = this.scrollableEl.length > 0;
		}

		this.resultIds = this.options.resultIds;
		delete this.options.resultIds;

		this.numPages = Math.ceil(this.resultCount / this.options.perPage);
		this.currentPage = this.options.currentPage;

		if (this.currentPage <= 1) {
			this.pageNav.addClass('no-prev');
		} else {
			this.pageNav.removeClass('no-prev');
		}

		if (this.currentPage >= this.numPages) {
			this.pageNav.addClass('no-next');
		} else {
			this.pageNav.removeClass('no-next');
		}

		this.updateShowingCount();

		this.pageNav.on('click', '.prev', this.loadPrevPage.bind(this));
		this.pageNav.on('click', '.next', this.loadNextPage.bind(this));

		if (this.options.infiniteScroll) {
			this.pageNav.closest('.results-nav').hide();

			var onScrollTimer;
			var self = this;

			this.scrollableEl.on('dp_scroll.infinite', function() {
				if (!onScrollTimer) {
					var scrollEl = $(this);
					onScrollTimer = setTimeout(function() {
						onScrollTimer = false;

						if (self.getCurrentPage() + 1 > self.getNumPages()) {
							self.scrollableEl.unbind('dp_scroll.infinite');
							return;
						}

						var scrollBottom = (scrollEl.data('dp-scroll-pos') + scrollEl.data('dp-scroll-viewport')),
							totalHeight = scrollEl.data('dp-scroll-height');

						if (totalHeight - scrollBottom < self.options.infiniteScrollTriggerOffset) {
							self.loadNextPage();
						}
					}, 25);
				}
			})
		}
	},


	/**
	 * Get the current page number (1-based)
	 *
	 * @return {Integer}
	 */
	getCurrentPage: function() {
		return this.currentPage;
	},


	/**
	 * Get the total number of pages
	 *
	 * @return {Integer}
	 */
	getNumPages: function() {
		return this.numPages;
	},


	/**
	 * Get IDs for a page
	 *
	 * @param {Integer} pageNum
	 */
	getPageIds: function(pageNum) {
		if (pageNum < 1 || pageNum > this.numPages) {
			console.warn("Requesting page " + pageNum + " when there are only " + this.numPages);
			return [];
		}

		return this.resultIds.slice((pageNum-1) * this.options.perPage, pageNum * this.options.perPage);
	},

	setResultCount: function(count) {
		if (!this.options) {
			return;
		}

		this.resultCount = count;
		this.numPages = Math.ceil(this.resultCount / this.options.perPage);

		this.pageNav.removeClass('no-prev no-next');
		if (this.currentPage == 1) {
			this.pageNav.addClass('no-prev');
		} else if (this.currentPage >= this.numPages) {
			this.pageNav.addClass('no-next');
		}

		this.totalCountEl.text(this.resultCount);
		this.tabResultCountEls.text(this.resultCount);
		this.updateShowingCount();
	},

	adjustResultCount: function(adjust) {
		this.setResultCount(this.resultCount + adjust);
	},


	/**
	 * Load the next page in the results
	 */
	loadNextPage: function() {
		var nextPage = this.getCurrentPage() + 1;
		if (nextPage > this.getNumPages()) {
			return;
		}

		return this.loadNewPage(nextPage);
	},

	/**
	 * Load the next page in the results
	 */
	loadPrevPage: function() {
		var prevPage = this.getCurrentPage() - 1;
		if (prevPage < 1) {
			return;
		}

		return this.loadNewPage(prevPage);
	},

	/**
	 * Load a new page
	 *
	 * @param {Integer} pageNum
	 */
	loadNewPage: function(pageNum) {

		// Already running
		if (this.navEl.is('.loading')) {
			return;
		}

		if (this.options.refreshMode) {
			var url = this.page.meta.refreshUrl;
			url = Orb.appendQueryData(url, 'p', pageNum);
			DeskPRO_Window.runPageRoute('listpane:' + url);
			return;
		}

		if (this.options.infiniteScroll) {
			pageNum = this.getCurrentPage() + 1; // only thing that can be loaded
		}

		var evData = {html: null}, html = null;

		this.setPage(pageNum);

		this.fireEvent('loadResultPage', [evData]);

		if (evData.html !== null) {
			html = evData.html;
			this.setNewResults(html);
		} else {
			this.showLoading();

			var data = [];
			if (this.resultIds) {
				Array.each(this.getPageIds(pageNum), function(i) {
					data.push({name: 'result_ids[]', value: i});
				});
			} else {
				data.push({name: 'page', value: pageNum});
			}
			if (this.displayOptions) {
				Array.each(this.displayOptions.getDisplayFields(), function(i) {
					data.push({name: 'display_fields[]', value: i });
				});
			}

			if (this.options.preFetchCallback) {
				data = this.options.preFetchCallback(data);
			}

			$.ajax({
				url: this.page.meta.fetchResultsUrl,
				data: data,
				type: 'GET',
				dataType: 'html',
				context: this,
				complete: function() {
					this.hideLoading();
				},
				success: function(html) {
					this.setNewResults(html);
				}
			});
		}
	},

	setPage: function(pageNum, updateShowing) {
		this.currentPage = pageNum;

		this.pageNav.removeClass('no-prev no-next');
		if (pageNum == 1) {
			this.pageNav.addClass('no-prev');
		} else if (pageNum == this.numPages) {
			this.pageNav.addClass('no-next');
		}

		if (updateShowing) {
			this.updateShowingCount();
		}
	},


	/**
	 * Render new results to the page
	 *
	 * @param html
	 */
	setNewResults: function(html) {
		if (!this.options) {
			return;
		}

		var results = $(html);
		DeskPRO_Window.initInterfaceServices(results);

		if (this.options.infiniteScroll) {
			if (this.options.infiniteScrollLoadFilter) {
				results = this.options.infiniteScrollLoadFilter(results);
			}
			if (this.options.infiniteScrollTarget) {
				this.options.infiniteScrollTarget.append(results);
			} else {
				this.resultsContainer.append(results);
			}
			this.scrollableEl.trigger('scrollupdate');
		} else {
			this.resultsContainer.empty().html(results);

			if (this.scrollableEl.length) {
				this.scrollableEl.trigger('goscrolltop');
			}
		}

		if (this.options.postSetNewResults) {
			this.options.postSetNewResults();
		}

		this.updateShowingCount();

		this.fireEvent('postSetNewResults', [this, this.resultsContainer, results]);
	},


	/**
	 * Show the 'loading' message
	 */
	showLoading: function() {
		if (this.page.meta.viewType == 'list') {
			return;
		}
		this.navEl.addClass('loading');
	},


	/**
	 * Remove the 'loading' message
	 */
	hideLoading: function() {
		this.navEl.removeClass('loading');
	},


	/**
	 * Show the 'no more results' element
	 */
	showNoMore: function() {
		this.navEl.removeClass('loading');
		this.navEl.addClass('no-more-results');
	},


	/**
	 * Update the showing xxx of xxx line by counting the rows currently displayed
	 */
	updateShowingCount: function() {
		if (!this.options) {
			return false;
		}

		var showingCount = $(this.options.resultRowSelector, this.resultsContainer).length || 0;

		var start = ((this.currentPage-1) * this.options.perPage);
		var end = start + showingCount;
		start++;

		if (this.resultIds) {
			if (end > this.resultIds.length) {
				end = this.resultIds.length;
			}
		} else {
			if (end > this.resultCount) {
				end = this.resultCount;
			}
		}

		this.showingCountEl.empty().text(start + '-' + end);
		this.tabResultCountEls.text(this.resultCount);

		return showingCount;
	},


	/**
	 * Add a result ID to the beginning of the array
	 *
	 * @param resultId
	 */
	prependResultId: function(resultId) {
		if (this.resultIds) {
			this.resultIds.unshift(resultId);
		}
	},


	/**
	 * Remove an ID from the result set
	 *
	 * @param resultId
	 */
	removeResultId: function(resultId) {
		if (this.resultIds) {
			this.resultIds.erase(resultId);
		}
	},

	destroy: function() {
		this.options = null;
		this.resultPages = null;
	}
});

Orb.createNamespace('DeskPRO.Agent.PageHelper');

DeskPRO.Agent.PageHelper.MassActions = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(page, options)  {
		this.page = page;

		this.options = {
			/**
			 * The object that will handle updating the actual
			 * list for the user
			 */
			viewHandler: null,

			/**
			 * The selection bar we can hook into to figure out
			 * when an item is selected or deselected
			 */
			selectionBar: null,

			/**
			 * The element within the list pane that contains the ticket results
			 * Defaults to 'wrapper .list-listing'
			 * @option {jQUery}
			 */
			listWrapper: null,

			/**
			 * The URL we'll post IDs to to get updated items
			 */
			fetchPreviewUrl: null,

			/**
			 * The button to trigger openeing the overlay.
			 * Null: defaults to 'wrapper .perform-actions-trigger',
			 * False: dont auto-assign trigger
			 */
			triggerElement: null,

			/**
			 * The HTML element with the actual controls etc we'll use for this
			 * Defaults to 'wrapper .mass-actions-overlay'
			 */
			templateElement: null,

			/**
			 * Disable the previewing feature and handle list view
			 */
			isListView: false,

			/**
			 * Reset the widget every time its closed
			 */
			resetOnClose: true,

			/**
			 * If true, closes immediately when calling apply
			 */
			closeOnApply: true,

			noScroll: false,

			/**
			 * Function to call when apply button is clicked.
			 */
			applyAction: function() {
			},

			/**
			 * Function to call when opened
			 */
			openAction: function() {

			}
		};

		this.setOptions(options);

		this.viewHandler     = this.options.viewHandler;
		this.selectionBar    = this.options.selectionBar || page.selectionBar;
		this.fetchPreviewUrl = this.options.fetchPreviewUrl || page.meta.fetchResultsUrl;
		this.listWrapper     = this.options.listWrapper;

		if (!this.listWrapper) {
			if (this.options.isListView) {
				this.listWrapper = $('.table-result-list table', page.wrapper);
			} else {
				this.listWrapper = $('.list-listing', page.wrapper);
			}
		}

		this.wrapperEl = this.options.templateElement || $('div.mass-actions-overlay-container', page.wrapper);
		this.wrapperEl.detach();
		this.wrapper = this.wrapperEl.clone();
		this.wrapper.find('.with-handler').removeClass('with-handler');

		DeskPRO_Window.initInterfaceLayerEvents(this.wrapper);
		var scrollEl = $('.with-scrollbar', this.wrapper).first();
		if (scrollEl.length && !this.options.noScroll) {
			this.scrollerHandler = new DeskPRO.Agent.ScrollerHandler(null, scrollEl, {
				showEvent: 'show',
				hideEvent: 'hide'
			});
		}

        $('.dp-radio-expander-form', this.wrapper).on('click', this.updatePositions.bind(this));
		this.backdropEls = null;

		this.countEl = $('.selected-tickets-count', this.getElement());

		var trigger = null;
		if (this.options.triggerElement === null) {
			trigger = $('.perform-actions-trigger', page.wrapper);
		} else if (this.options.triggerElement) {
			trigger = this.options.triggerElement;
		}

		if (trigger) {
			trigger.on('click', (function(ev) {
				ev.preventDefault();
				ev.stopPropagation();
				this.open();
			}).bind(this));
		}
	},


	/**
	 * Resets the wrapper back to the original, and then runs all of the init again.
	 */
	reset: function() {
		var wasopen = this.isOpen();
		this.close();

		this.backdropEls.remove();
		this.wrapper.remove();

		this.wrapper = this.wrapperEl.clone();
		this.wrapper.find('.with-handler').removeClass('with-handler');

		DeskPRO_Window.initInterfaceLayerEvents(this.wrapper);
		var scrollEl = $('.with-scrollbar', this.wrapper).first();
		scrollEl.removeClass('scroll-draw');
		if (scrollEl.length && !this.options.noScroll) {
			this.scrollerHandler = new DeskPRO.Agent.ScrollerHandler(null, scrollEl, {
				showEvent: 'show',
				hideEvent: 'hide'
			});
		}

		this.countEl = $('.selected-tickets-count', this.wrapper);
        this.updatePositions();
        $('.dp-radio-expander-form', this.wrapper).on('click', this.updatePositions.bind(this));
		this._hasInit = false;

		this.hasAnyChange = false;

		if (wasopen) {
			this.open();
		}
	},


	/**
	 * Get the main wrapper element around the mass actions UI controls.
	 *
	 * @return {jQuery}
	 */
	getElement: function() {
		return this.wrapper;
	},


	/**
	 * Inits the overlay controls lazily on first open
	 */
	_initOverlay: function() {
		var self = this;
		if (this._hasInit) return;
		this._hasInit = true;

		this.wrapper.detach().appendTo('body');
		DeskPRO.ElementHandler_Exec(this.wrapper);
		this.wrapper.css('z-index', '21001');

		this.baseId = this.wrapper.data('base-id');

		this.wrapper.on('click', function(ev) {
			ev.stopPropagation();
		});

		if (this.options.isListView) {
			this.backdropEls = $('<div class="backdrop fade" />');

		} else {
			// Three backdrops to surround each side of the list pane: left, right, top
			var back1 = $('<div class="backdrop mass-actions" />');
			var back2 = $('<div class="backdrop mass-actions" />');
			var back3 = $('<div class="backdrop mass-actions" />');
			this.backdropEls = $([back1.get(0), back2.get(0), back3.get(0)]);
		}

		this.backdropEls.css('z-index', '21000').hide().appendTo('body');

		this.backdropEls.on('click', (function(ev) {
			ev.stopPropagation();
			this.close();
		}).bind(this));

		$('header .close-trigger', this.wrapper).first().on('click', (function(ev) {
			ev.stopPropagation();
			ev.preventDefault();
			this.close();
		}).bind(this));

		$('.apply-actions', this.wrapper).on('click', (function(ev) {
			this.apply();
		}).bind(this));

		if (!this._hasSelectionEvents) {
			this._hasSelectionEvents = true;

			this.selectionBar.addEvent('countChange', function(count) {
				if (!this.isOpen()) return;
				this.updateCount(count);
			}, this);
		}
	},

	updateAssignmentsDisplay: function() {
	},

	getElById: function(id) {
		return $('#' + this.baseId + '_' + id);
	},

	updateCount: function(num) {
		if (num === undefined || num === null) {
			num = this.selectionBar.getCount();
		}
		this.countEl.text(num);
	},

	getActionFormValues: function(appendArray, isApply, info) {
	},

	/**
	 * Apply the changes
	 */
	apply: function() {
		if(this.options.applyAction) {
			var formDataInfo = {
				checkedCount: 0,
				actionsCount: 0
			},
			formData = this.selectionBar.getCheckedFormValues('result_ids[]', null, formDataInfo),
			rows = [];

			$(formData).each(function(index, param) {
				rows.push(param.value);
			});

			if(formDataInfo.checkedCount) {
				this.options.applyAction(this.wrapper, {'result_ids': rows});
			}
		}

		if (this.options.closeOnApply) {
			this.close();
		}
	},


	/**
	 * Clear all pending previews
	 */
	clearPreview: function() {
	},


	/**
	 * Updates the listing with a preview of the changes we're making
	 */
	updatePreview: function(specific_id, force) {
	},

	/**
	 * Update the preview display data with an HTML block returned from the server
	 *
	 * @param html
	 */
	updatePreviewDisplay: function(html) {

	},


	/**
	 * When a ticket has been checked or uncheck, need to update the preview status of that ticket.
	 */
	handleCheckChange: function(el, is_checked) {

	},


	/**
	 * Resets the form back to nothing
	 */
	resetForm: function() {
	},


	/**
	 * Update the positions of the elements
	 */
	updatePositions: function() {
        if(!this.isOpen()) {
            return;
        }
		//------------------------------
		// The wrapper overlaps the content pane section
		//------------------------------

		var pos = $('#dp_content').offset();
		var top = pos.top - 4;

        var bottom = 10;
        var height = '';

        var scrollContent = $('.scroll-content', this.wrapper).first();
        var contentH = false;
        var hasHeader = !!($('> section > header', this.wrapper).length);
        var hasFooter = !!($('> section > footer', this.wrapper).length);

        if (scrollContent.length) {
            contentH = scrollContent.height();
            if (hasHeader) {
                contentH += 36;
            }
            if (hasFooter) {
                contentH += 45;
            }

            contentH += 31;
        }

        if (hasHeader) $('> section > article', this.wrapper).removeClass('no-header');
        else $('> section > article', this.wrapper).addClass('no-header');

        if (hasFooter) $('> section > article', this.wrapper).removeClass('no-footer');
        else $('> section > article', this.wrapper).addClass('no-footer');

        if (contentH < 100) {
            contentH = 100;
        }

        var maxH = $(window).height() - top - 10;

        if (contentH && contentH < maxH) {
            bottom = '';
            height = contentH;
        }

		this.wrapper.css({
			top: pos.top - 4,
			left: pos.left + 8,
            right: 3,
            bottom: bottom,
            height: height
		});

		//------------------------------
		// The backdrops surround each side of the list pane
		//------------------------------

		var leftEnd = 269; // Where the left ends (aka where listpane starts)
		var topEnd = 50; // Where the top ends (aka header height)
		var contentStart = pos.left;

		if (!this.options.isListView) {
			this.backdropEls.eq(0).css({
				top: 0,
				width: leftEnd,
				bottom: 0,
				left: 0
			});

			this.backdropEls.eq(1).css({
				top: 0,
				height: topEnd,
				width: contentStart - leftEnd,
				left: leftEnd
			});

			this.backdropEls.eq(2).css({
				top: 0,
				right: 0,
				bottom: 0,
				left: contentStart
			});
		}

		if (this.scrollerHandler) {
			this.scrollerHandler.updateSize();
		}
	},

	_initMacroOverlay: function() {
	},


	/**
	 * Load a macro into the form
	 */
	loadMacro: function(macro_id) {
	},


	/**
	 * Is the overlay currently open?
	 *
	 * @return {Boolean}
	 */
	isOpen: function() {
		if (!this._hasInit || !this.wrapper.is('.open')) {
			return false;
		}

		return true;
	},


	/**
	 * Open this overlay
	 */
	open: function() {
		this._initOverlay();

		this.wrapper.addClass('open');

		this.backdropEls.show();

		this.updateCount(null);

        this.updatePositions();
        this.updatePositions();
        DeskPRO_Window.layout.addEvent('resized', this.updatePositions, this);
        //this.updatePreview();

		this.options.openAction(this.wrapper);
	},


	/**
	 * Close the overlay
	 */
	close: function() {
		if (!this.isOpen()) {
			return false;
		}

        DeskPRO_Window.layout.removeEvent('resized', this.updatePositions, this);
		this.wrapper.removeClass('open');
		this.backdropEls.hide();
		this.fireEvent('closed', [this]);

		this.clearPreview();

		if (this.options.resetOnClose) {
			this.reset();
		}
	},


	destroy: function() {
		if (this._hasInit) {
			this.wrapper.remove();
			this.backdropEls.remove();
		}
	}
});

Orb.createNamespace('DeskPRO.Agent.PageHelper');

/**
 * Used on tabs with RTE editors to accent links from listings to insert links to articels etc
 */
DeskPRO.Agent.PageHelper.AcceptContentLink = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(options) {
		var self = this;
		this.options = {
			/**
			 * The page fragment
			 */
			page: null,

			/**
			 * The TinyMCE editor to add link to
			 */
			rte: null,

			/**
			 * Called to check if the tab is ready to accept. For example,
			 * in view pages, the editor must be activated first.
			 *
			 * @return {Boolean}
			 */
			isReadyCallback: null
		};

		this.setOptions(options);
	},

	isReady: function() {
		if (this.options.isReadyCallback) {
			return this.options.isReadyCallback();
		}

		return true;
	},

	sendLink: function(linkTitle, url) {
		var tiny = this.options.rte.tinymce();

		var title = tiny.selection.getContent({ format: 'text' });
		if (title) title = title.trim();
		if (!title.length) {
			title = linkTitle;
		}

		tiny.selection.setContent('<a href="' + url + '">' + Orb.escapeHtml(title) + '</a>');
	}
});

Orb.createNamespace('DeskPRO.Agent.PageHelper');

/**
 * Used on listings to send links to a RTE editor
 */
DeskPRO.Agent.PageHelper.SendContentLink = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(page, options) {
		var self = this;
		this.page = page;

		this.options = {
			/**
			 * The main list element that contains rows.
			 */
			contentListEl: null
		};

		this.setOptions(options);

		this.contentListEl = $(this.options.contentListEl);

		DeskPRO_Window.getTabWatcher().addTabTypeWatcher('*', this);
		var types = [
			'article', 'newarticle',
			//'download', 'newdownload',
			//'news', 'newnews',
			//'feedback', 'newfeedback'
		];

		this.addEvent('watchedTabActivated', function(tab) {
			var tabtype = DeskPRO_Window.getTabWatcher().getTabType(tab);
			if (types.indexOf(tabtype) !== -1) {
				if (tab.page && tab.page.acceptContentLink) {
					if (tab.page.acceptContentLink.isReady()) {
						$('body').addClass('content-link-control-on');
					} else {
						$('body').removeClass('content-link-control-on');
					}
				} else {
					$('body').removeClass('content-link-control-on');
				}
			} else {
				$('body').removeClass('content-link-control-on');
			}
		}, this);
		this.addEvent('watchedTabDeactivated', function(tab, isLast) {
			if (isLast) {
				$('body').removeClass('content-link-control-on');
			}
		}, this);

		this.contentListEl.on('click', '.insert-content-link', function(ev) {
			ev.stopPropagation();
			ev.preventDefault();

			var tab = DeskPRO_Window.getTabWatcher().getActiveTab();
			if (!tab || !tab.page || !tab.page.acceptContentLink) {
				return;
			}

			tab.page.acceptContentLink.sendLink($(this).data('link-title'), $(this).data('link'));
		});
	},

	updateList: function() {
		var tab = DeskPRO_Window.getTabWatcher().getActiveTab();
		if (!tab || !tab.page || !tab.page.acceptContentLink || !tab.page.acceptContentLink.isReady()) {
			$('body').removeClass('content-link-control-on');
		} else {
			$('body').addClass('content-link-control-on');
		}
	}
});

Orb.createNamespace('DeskPRO.Agent.PageHelper');
DeskPRO.Agent.PageFragment.Page.EditTitle = new Orb.Class({
	initialize: function(page, saveUrl, data) {
		var namef       = page.getEl('showname');
		var editName    = page.getEl('editname');
		var startBtn    = page.getEl('editname_start');
		var stopBtn     = page.getEl('editname_end');
		var codeid      = page.getMetaData('obj_code') || null;

		var startEditable = function() {
			namef.hide();
			editName.show();
			startBtn.hide();
			stopBtn.show();
		};

		var stopEditable = function() {
			var nametxt = editName.find('input').first();
			var setName = nametxt.val().trim();

			if(!setName) {
				return;
			}

			editName.hide();
			startBtn.show();
			namef.show();
			stopBtn.hide();
			namef.text(setName);

			var postData = data ? Array.clone(data) : [];
			postData.push({
				name: 'action',
				value: 'title'
			});
			postData.push({
				name: 'title',
				value: setName
			});

			if (codeid) {
				$('span.obj-title-' + codeid).text(setName);
			}

			$.ajax({
				url: saveUrl,
				type: 'POST',
				data: postData,
				success: function(retData) {
					if (page.handleUnloadRevisions) {
						page.handleUnloadRevisions(retData.revision_id);
					}
				}
			});
		};

		namef.on('dblclick', startEditable).on('keypress', function(ev) {
			if (ev.keyCode == 13 /* enter key */) {
				ev.preventDefault();
				stopEditable();
			}
		});
		page.getEl('editname_start').on('click', startEditable);
		page.getEl('editname_end').on('click', stopEditable);
	}
});
Orb.createNamespace('DeskPRO.Agent.PageHelper');

DeskPRO.Agent.PageHelper.TaskListControl = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(el, options) {
		var self      = this;
		var openForEl = null;
		var menuVis   = options.menuVis;
		var completeCountEl = options.completeCountEl;

		var updateCount = function(op, row) {
			var els = [];

			var checksub = function(prefix) {
				if (row.data('in-sublist-overdue')) { els.push(document.getElementById(prefix + '_overdue')); }
				if (row.data('in-sublist-today')) { els.push(document.getElementById(prefix + '_today')); }
				if (row.data('in-sublist-future')) { els.push(document.getElementById(prefix + '_future')); }
			};

			if (row.data('in-my')) {
				els.push(document.getElementById('tasks_counter_own_total'));
				checksub('tasks_counter_own');
			}
			if (row.data('in-my-teams')) {
				els.push(document.getElementById('tasks_counter_team_total'));
				checksub('tasks_counter_team');
			}
			if (row.data('in-delegated')) {
				els.push(document.getElementById('tasks_counter_delegated_total'));
				checksub('tasks_counter_delegated');
			}
			els.push(document.getElementById('tasks_counter_all_total'));
			checksub('tasks_counter_all');

			if (completeCountEl) {
				DeskPRO_Window.util.modCountEl(completeCountEl, op, -1);
			}

			Array.each(els, function(el) {
				DeskPRO_Window.util.modCountEl($(el), op);
			});

			DeskPRO_Window.sections.tasks_section.recalcBadge();

			self.fireEvent('updateCount', [op, row]);
		};

		var updateUi = function() {
			self.fireEvent('updateUi');
		};

		var sendUpdate = function(rowEl, prop, val, callback) {
			var taskId = rowEl.data('task-id');
			var url = BASE_URL + 'agent/tasks/'+taskId+'/ajax-save';

			var postData = [];
			postData.push({
				name: 'action',
				value: prop
			});
			postData.push({
				name: 'value',
				value: val
			});

			$.ajax({
				url: url,
				type: 'POST',
				data: postData,
				dataType: 'json',
				success: callback || function() {}
			});
		};

		var statusMenu = new DeskPRO.UI.Menu({
			menuElement: menuVis,
			onItemClicked: function(info) {
				sendUpdate(openForEl, 'visibility', $(info.itemEl).data('vis'));
				$('.opt-trigger.visibility label', openForEl).text($(info.itemEl).text());
			}
		});

		el.on('click', 'input.item-select', function(ev) {
			var row = $(this).closest('article.task');
			var value = $(this).is(':checked');

			if (value) {
				$('.task-sub-wrap', row).hide();
				row.addClass('completed');

				sendUpdate(row, 'completed', 1);

				updateCount('-', row);
			} else {
				row.removeClass('expanded');
				$('.task-sub-wrap', row).show();
				row.removeClass('completed');

				sendUpdate(row, 'completed', 0);

				updateCount('+', row);
			}

			updateUi();
		});
		el.find('li.assigned_agent select.agents_sel').not('.has-init').each(function() {
			var row = $(this).closest('article.task');
			DP.select($(this));

			$(this).on('change', function() {
				var val = $(this).val();
				var label = $(this).find(':selected').text().trim();

				if (!val) {
					val = '';
					label = 'Me';
				}

				row.find('.assigned_agent').find('label').text(label);
				sendUpdate(row, 'assigned', val, function() {
					DeskPRO_Window.getMessageBroker().sendMessage('agent.ui.tasks.refresh-task-list');
				});
			});
		});
		el.on('click', '.opt-trigger.visibility', function(ev) {
			openForEl = $(this).closest('article.task');
			statusMenu.open(ev);
		});
		el.on('click', '.opt-trigger.date_due', function(ev) {
			openForEl = $(this).closest('article.task');

			var label = $('label', this);
			var date = openForEl.data('date-due');
			if (!date) {
				date = new Date();
			}

			openForEl.datepicker('dialog', date, function(date, inst) {
				sendUpdate(openForEl, 'date_due', date);
				label.text(date);
			}, {
				dateFormat: 'yy-mm-dd',
				showButtonPanel: true,
				beforeShow: function(input) {
					setTimeout(function() {
						var buttonPane = $(input).datepicker("widget").find(".ui-datepicker-buttonpane");

						var btn = $('<button class="ui-datepicker-current ui-state-default ui-priority-secondary ui-corner-all" type="button">Clear</button>');
						btn.unbind("click").bind("click", function () { $.datepicker._clearDate( input ); label.text('No due date'); });
						btn.appendTo( buttonPane );

						$(input).datepicker("widget").css('z-index', 30001);
					},1);
				}
			}, ev);
		});
		el.on('click', '.expand-collapse-icon', function(ev) {
			var row = $(this).closest('article.task');
			if (row.is('.expanded')) {
				row.removeClass('expanded');
				$('.task-info', row).hide();
				$('.task-comments', row).hide();
				$('.new-comment', row).hide();
				$('.task-sub-wrap', row).hide();
				updateUi();

			} else {
				row.addClass('expanded');
				$('.task-info', row).show();
				$('.task-comments', row).show();
				$('.new-comment', row).show();
				$('.task-sub-wrap', row).show();
				updateUi();
			}
		});

		el.on('click', '.comment-btn', function(ev) {
			var row = $(this).closest('article.task');
			var input = $('.new-comment', row);
			if (input.is(':visible')) {
				input.hide();
			} else {
				input.show();
			}

			updateUi();
		});
		el.on('click', '.cancel-comment-trigger', function(ev) {
			var row = $(this).closest('article.task');
			var btn = $('.comment-btn', row);
			$('.new-comment', row).hide();
			updateUi();
		});

		el.on('click', '.save-comment-trigger', function(ev) {
			var row = $(this).closest('article.task');
			var commentTxt = $('textarea', row);

			var closefn = function() {
				commentTxt.val('');
				$('.new-comment', row).hide();
			};

			if (!commentTxt.val().trim().length) {
				return;
			}

			var postData = [];
			postData.push({
				name: 'comment',
				value: commentTxt.val().trim()
			});

			row.addClass('loading');

			var taskId = row.data('task-id');
			$.ajax({
				url: BASE_URL + 'agent/tasks/'+taskId+'/ajax-save-comment',
				type: 'POST',
				dataType: 'json',
				data: postData,
				complete: function() {
					row.removeClass('loading');
				},
				success: function(data) {
					closefn();

					if (data.error) {
						return;
					}

					var list = $('ul.task-comment-list', row);
					$(data.comment_li_html).appendTo(list);
					$('.task-comments', row).show();
					updateUi();
				}
			});
		});

		el.on('click', '.task-group header', function() {
			$(this).parent().toggleClass('collapsed')
			updateUi();
		});

		el.on('click', '.delete-task', function(ev) {
			var row = $(this).closest('.row-item');
			var taskId = row.data('task-id');

			if (confirm($(this).data('confirm'))) {
				row.slideUp();
				updateCount('-', row);
				$.ajax({
					url: BASE_URL + 'agent/tasks/' + taskId + '/delete',
					error: function() {
						row.show();
					},
					success: function() {
						row.remove();
					}
				});
			}
		});
	}
});
Orb.createNamespace('DeskPRO.Agent.PageHelper');

DeskPRO.Agent.PageHelper.TicketBilling = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(wrap, baseId, options) {
		this.baseId = baseId;
		this.options = {
			auto_start_bill: false
		};

		this.setOptions(options);

		this.billingStart = false;
		this.billingExtraTime = 0;
		this.billingTimer = null;

		var self = this;

		this.hasBilling = (wrap.length > 0);

		if (!wrap.length) {
			return;
		}

		var form = this.getEl('billing_form');
		var progress = this.getEl('billing_save_progress');
		var typeInputs = form.find('input[name=' + this.baseId + '_billing_type]');
		var billingRows = this.getEl('billing_rows');

		typeInputs.change(function() { self.updateBillingForm(true); });
		this.updateBillingForm(true);

		this.getEl('billing_stop').click(function() {
			self.stopBillingTimer(false);
			$(this).hide();
			self.getEl('billing_start').show();
		});
		this.getEl('billing_start').click(function() {
			if (self.getEl('billing_type_hidden').val() != 'time') {
				return;
			}

			self.startBillingTimer(false);
			$(this).hide();
			self.getEl('billing_stop').show();
		});
		this.getEl('billing_reset').click(function() {
			if (typeInputs.filter(':checked').val() == 'time') {
				if (self.getEl('billing_stop').is(':visible')) {
					self.startBillingTimer(true);
				} else {
					self.stopBillingTimer(true);
				}
			} else {
				self.stopBillingTimer(true);
			}
		});

		this.getEl('billing_save').click(function() {
			progress.show();

			$.ajax({
				url: $(this).data('submit-url'),
				data: form.find('input, textarea, select').serialize(),
				type: 'POST',
				dataType: 'json'
			}).done(function(json) {
				if (json.inserted && self.addBillingRow) {
					self.addBillingRow(json.html);
					self.resetBillingForm();
				}
			}).always(function() {
				progress.hide();
			});
		});

		wrap.on('click', 'a.billing-delete', function(e) {
			var $this = $(this);

			e.preventDefault();

			if (confirm(billingRows.data('delete-confirm'))) {
				$.ajax({
					url: $this.attr('href'),
					type: 'POST',
					dataType: 'json'
				}).done(function (json) {
					if (json.success) {
						var table = $this.closest('table');
						$this.closest('tr').remove();
						if (!table.find('tbody tr').length)
						{
							table.hide();
						}
					}
				});
			}
		});

		if (this.options.auto_start_bill) {
			this.getEl('billing_start').hide();
			this.getEl('billing_stop').show();
			this.getEl('billing_start').click();
		} else {
			this.getEl('billing_stop').hide();
			this.getEl('billing_start').show();
			this.stopBillingTimer(true);
		}
	},

	addBillingRow: function(html) {
		var add = $(html);
		var billingRows = this.getEl('billing_rows');

		billingRows.append(add);
		add.find('.timeago').timeago();
		billingRows.closest('table').show();
	},

	updateBillingForm: function(reset) {
		var form = this.getEl('billing_form');
		var typeInputs = form.find('input[name=' + this.baseId + '_billing_type]');
		var val = typeInputs.filter(':checked').val();

		this.getEl('billing_type_hidden').val(val);

		var replyBaseId = $('form.ticket-reply-form', this.getEl('replybox_wrap')).data('base-id');
		if (replyBaseId) {
			var replyBillingRow = $('#' + replyBaseId + '_billing_reply');
		} else {
			var replyBillingRow = false;
		}

		this.clearTimer();

		if (val == 'time') {
			if (this.getEl('billing_stop').is(':visible')) {
				// "stop" means it was running, so start it again
				this.startBillingTimer(reset);
			}

			if (replyBillingRow) {
				replyBillingRow.show();
				replyBillingRow.find('input[type=checkbox]').attr('disabled', false);
			}
		} else {
			this.stopBillingTimer(reset);

			if (replyBillingRow) {
				replyBillingRow.hide();
				replyBillingRow.find('input[type=checkbox]').attr('disabled', true);
			}
		}
	},

	clearTimer: function() {
		if (this.billingTimer) {
			clearInterval(this.billingTimer);
			this.billingTimer = null;
		}
	},

	resetBillingForm: function() {
		var form = this.getEl('billing_form');

		this.getEl('billing_amount').val('');
		this.getEl('billing_comment').val('');

		if (this.getEl('billing_type_hidden').val() == 'time' && this.options.auto_start_bill) {
			this.startBillingTimer(true);
		} else {
			this.stopBillingTimer(true);
		}
	},

	startBillingTimer: function(reset) {
		if (reset) {
			this.billingStart = new Date();
			this.billingExtraTime = 0;
		} else {
			if (this.billingStart) {
				this.billingExtraTime = Math.floor((new Date() - this.billingStart) / 1000) + this.billingExtraTime;
			}
			this.billingStart = new Date();
		}

		this.clearTimer();
		this.updateBillingTimer(true, true);

		var self = this;
		this.billingTimer = setInterval(function() { self.updateBillingTimer(); }, 1000);
	},

	stopBillingTimer: function(reset) {
		if (reset) {
			this.billingStart = false;
			this.billingExtraTime = 0;
		} else {
			if (this.billingStart) {
				this.billingExtraTime = Math.floor((new Date() - this.billingStart) / 1000) + this.billingExtraTime;
			}
			this.billingStart = false;
		}

		this.clearTimer();
		this.updateBillingTimer(true);
	},

	updateBillingTimer: function(force, showZero) {
		var seconds = 0;
		if (this.billingStart) {
			seconds = Math.floor((new Date() - this.billingStart) / 1000);
		}
		seconds += this.billingExtraTime;

		var rawSeconds = seconds,
			hours = 0,
			minutes = 0;

		var form = this.getEl('billing_form');
		var timeInputs = {
			hours: this.getEl('billing_hours'),
			minutes: this.getEl('billing_minutes'),
			seconds: this.getEl('billing_seconds')
		};

		if (seconds >= 3600) {
			hours = Math.floor(seconds / 3600);
			timeInputs.hours.val(hours);
			seconds -= hours * 3600;
		} else if (force) {
			timeInputs.hours.val('');
		}

		if (seconds >= 60) {
			minutes = Math.floor(seconds / 60);
			timeInputs.minutes.val(minutes);
			seconds -= minutes * 60;
		} else if (force) {
			timeInputs.minutes.val('');
		}

		if (seconds > 0 || minutes > 0 || hours > 0 || showZero) {
			timeInputs.seconds.val(seconds || 0);
		} else if (force) {
			timeInputs.seconds.val('');
		}

		var replyBaseId = $('form.ticket-reply-form', this.getEl('replybox_wrap')).data('base-id');
		if (replyBaseId) {
			var reply = $('#' + replyBaseId + '_billing_reply');
			if (reply.length) {
				reply.find('input[type=checkbox]').val(rawSeconds);

				var text = '';
				if (hours) {
					text += hours + (hours > 1 ? ' hours ' : ' hour ');
				}
				if (minutes) {
					text += minutes + (minutes > 1 ? ' minutes ' : ' minute ');
				}
				text += seconds + ' seconds';

				$('#' + replyBaseId + '_billing_reply_time').text(text);
			}
		}
	},

	getEl: function(id) {
		if (this.baseId) {
			id = this.baseId + '_' + id;
		}

		return $('#' + id);
	}
});
Orb.createNamespace('DeskPRO.Agent.PageHelper');

DeskPRO.Agent.PageHelper.Twitter = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(content, page, options) {
		this.content = $(content);
		this.page = page;
		var self = this;

		this.options = {
			saveMessageCallback: 'default',
			messageUrl: page.getMetaData('saveReplyUrl'),
			statusArchiveHideCallback: null,
			userArchiveHideCallback: null
		};

		options = options || {};
		this.setOptions(options);

		// user links
		this.content.on('click', '.photo, .user', function(e) {
			e.preventDefault();
			DeskPRO_Window.runPageRouteFromElement(this);
			return false;
		});
		this.content.on('click', '.mention', function(e) {
			e.preventDefault();
			var route = 'page:' + BASE_URL + 'agent/twitter/user/' + $(this).data('user-id');
			DeskPRO_Window.runPageRoute(route);
			return false;
		});

		// user follow/unfollow
		this.content.on('click', '.follow', function(e) {
			e.preventDefault();
			e.stopPropagation();

			var row = self.closestRow(this);
			var id = row.attr('data-user-id');

			self.content.find('[data-user-id="' + id + '"] .follow').each(function() {
				$(this).addClass('unfollow').removeClass('follow');

				var label = $(this).find('label');
				if (label.length) {
					label.text('Unfollow');
				} else {
					$(this).text('Unfollow');
				}
			});

			$.ajax({
				url: self.page.getMetaData('saveFollowUrl'),
				type: 'POST',
				data: {
					user_id: id,
					account_id: self.page.getMetaData('accountId')
				}
			});

			if (page.getMetaData('listRoute') == 'agent_twitter_followers_list_new') {
				row.addClass('archived');
				row.find('.status-archived').show();

				if (self.page.getMetaData('hideArchived')) {
					if (self.options.userArchiveHideCallback) {
						self.options.userArchiveHideCallback(row);
					}
					row.fadeOut('fast', function() {
						if (self.options.userArchiveHideCallback) {
							self.options.userArchiveHideCallback(row);
						}
						row.remove();
					});
				}
			}
		});
		this.content.on('click', '.unfollow', function(e) {
			e.preventDefault();
			e.stopPropagation();

			var id = self.closestRow(this).attr('data-user-id');

			self.content.find('[data-user-id="' + id + '"] .unfollow').each(function() {
				$(this).addClass('follow').removeClass('unfollow');

				var label = $(this).find('label');
				if (label.length) {
					label.text('Follow');
				} else {
					$(this).text('Follow');
				}
			});

			$.ajax({
				url: self.page.getMetaData('saveUnfollowUrl'),
				type: 'POST',
				data: {
					user_id: id,
					account_id: self.page.getMetaData('accountId')
				}
			});
		});

		// user archive/unarchive (status class names used)
		this.content.on('click', '.status-archive.user-action', function(e) {
			e.preventDefault();

			var row = $(this).closest('.twitter-user');
			var id = row.attr('data-user-id');

			if (id) {
				$(this).hide();
				row.addClass('archived');
				row.find('.status-archived').show();

				self.doArchiveUser(id, 1);

				if (self.page.getMetaData('hideArchived')) {
					row.fadeOut('fast', function() {
						if (self.options.userArchiveHideCallback) {
							self.options.userArchiveHideCallback(row);
						}
						row.remove();
					});
				}
			}
		});
		this.content.on('click', '.status-archived.user-action', function(e) {
			e.preventDefault();

			var row = $(this).closest('.twitter-user');
			var id = $(this).closest('.twitter-user').attr('data-user-id');

			if (id) {
				$(this).hide();
				row.removeClass('archived');
				row.find('.status-archive').show();

				self.doArchiveUser(id, 0);
			}
		});

		// status favorite/unfavorite
		this.content.on('click', '.add-favorite', function(e) {
			e.preventDefault();

			var id = self.closestRow(this).attr('data-status-id');
			if (id) {
				self.removeTweetNotification(id);

				$(this).addClass('favorited').removeClass('add-favorite');
				self.doFavorite(id, 1);
			}
		});
		this.content.on('click', '.favorited', function(e) {
			e.preventDefault();

			var id = self.closestRow(this).attr('data-status-id');
			if (id) {
				self.removeTweetNotification(id);

				$(this).addClass('add-favorite').removeClass('favorited');
				self.doFavorite(id, 0);
			}
		});

		// status archive/unarchive
		this.content.on('click', '.status-archive.status-action', function(e) {
			e.preventDefault();

			var row = self.closestRow(this);
			var id = row.attr('data-status-id');

			if (id) {
				$(this).hide();
				row.addClass('archived');
				row.find('.status-archived').show();

				self.doArchiveStatus(id, 1);

				self.removeTweetNotification(id);

				if (page.menuOptions && !page.menuOptions.filter('[name=archived]').is(':checked')) {
					row.fadeOut('fast', function() {
						if (self.options.statusArchiveHideCallback) {
							self.options.statusArchiveHideCallback(row);
						}
						row.remove();
					});
				}
			}
		});
		this.content.on('click', '.status-archived.status-action', function(e) {
			e.preventDefault();

			var row = self.closestRow(this);
			var id = row.attr('data-status-id');

			if (id) {
				$(this).hide();
				row.removeClass('archived');
				row.find('.status-archive').show();

				self.removeTweetNotification(id);

				self.doArchiveStatus(id, 0);
			}
		});

		// status delete
		this.content.on('click', '.status-delete.status-action', function(e) {
			e.preventDefault();

			var row = self.closestRow(this);
			var id = row.attr('data-status-id');

			if (id && confirm('Are you sure you want to delete this tweet?')) {
				row.hide();

				self.removeTweetNotification(id);

				$.ajax({
					url: self.page.getMetaData('saveDeleteUrl'),
					type: 'POST',
					dataType: 'json',
					data: { account_status_id: id },
					success: function(json) {
						if (json.success) {
							row.remove();
						} else if (json.error) {
							row.show();
							alert(json.error);
						}
					}
				});
			}
		});

		// reply delete
		this.content.on('click', '.status-delete.reply-action', function(e) {
			e.preventDefault();

			var reply = $(this).closest('.twitter-reply');
			var id = reply.attr('data-status-id');

			if (id && confirm('Are you sure you want to delete this tweet?')) {
				reply.hide();

				$.ajax({
					url: self.page.getMetaData('saveDeleteUrl'),
					type: 'POST',
					dataType: 'json',
					data: { account_status_id: id },
					success: function(json) {
						if (json.success) {
							var row = self.closestRow(reply);
							reply.remove();
							if (!row.find('.twitter-replies .twitter-reply').length) {
								row.find('.reply-list').hide();
							}
						} else if (json.error) {
							reply.show();
							alert(json.error);
						}
					}
				});
			}
		});

		// status/reply edit
		this.content.on('click', '.status-edit.status-action', function(e) {
			e.preventDefault();

			var row = $(this).closest('[data-status-id]');
			var id = row.attr('data-status-id');

			if (id) {
				var overlay = new DeskPRO.UI.Overlay({
					contentMethod: 'ajax',
					contentAjax: {
						url: BASE_URL + 'agent/twitter/status/ajax-edit',
						data: { account_status_id: id }
					},
					zIndex: 40000, // Above floating people windows
					onAjaxDone: function() {
						var wrapper = overlay.getWrapper();
						var textarea = wrapper.find('textarea[name=text]');

						textarea.TextAreaExpander().focus();

						wrapper.find('.save-trigger').click(function() {
							wrapper.addClass('loading');

							$.ajax({
								url: BASE_URL + 'agent/twitter/status/ajax-edit',
								type: 'POST',
								data: { account_status_id: id, text: textarea.val(), process: 1},
								dataType: 'json',
								success: function(data) {
									if (data.success) {
										overlay.close();

										if (row.is('.twitter-reply')) {
											row.find('.status-text').html(data.parsed_text);
										} else {
											row.find('.main-status-body .status-text').html(data.parsed_text);
										}
									} else if (data.error) {
										alert(data.error);
									}
								}
							}).always(function() { wrapper.removeClass('loading'); });
						});
					}
				});
				overlay.open();
			}
		});

		// retweet trigger
		this.content.on('click', 'li.opt-trigger.retweet', function(e) {
			e.preventDefault();

			var row = self.closestRow(this);
			var id = row.attr('data-status-id');

			var retweetContainer = row.find('.new-retweet');
			if (retweetContainer.is(':visible')) {
				retweetContainer.hide();
			} else {
				retweetContainer.show();
				row.find('.new-message').hide();

				self.removeTweetNotification(id);

				var textarea = retweetContainer.find('textarea');
				if (!textarea.hasClass('tae')) {
					textarea.TextAreaExpander();
				}
				self.updateTweetLength(textarea);
				textarea.focus();
			}
		});
		this.content.on('change', '.new-retweet .retweet-type input[type=radio]', function() {
			var $this = $(this), container = $this.closest('.new-retweet');
			if ($this.val() == '1') {
				container.find('.edit-only').hide();
			} else {
				container.find('.edit-only').show();
			}
		});
		this.content.on('keypress keyup change', '.new-retweet textarea', function() {
			var $this = $(this);
			setTimeout(function() {
				self.updateTweetLength($this);
			}, 0);
		});
		this.content.on('click', '.cancel-retweet-trigger', function() {
			var retweetContainer = $(this).closest('.new-retweet');
			retweetContainer.hide();
		});
		this.content.on('click', '.save-retweet-trigger', function(e) {
			e.preventDefault();

			var row = self.closestRow(this);
			var id = row.attr('data-status-id');
			var retweetContainer = $(this).closest('.new-retweet');

			var val = $.trim(retweetContainer.find('textarea').val());
			if (!val.length) {
				retweetContainer.hide();
				return;
			}

			var data = retweetContainer.find('form').serializeArray();

			data.push({
				name: 'account_status_id',
				value: id
			});

			retweetContainer.addClass('loading');

			self.removeTweetNotification(id);

			$.ajax({
				url: page.getMetaData('saveRetweetUrl'),
				type: 'POST',
				dataType: 'json',
				data: data,
				success: function(json) {
					if (json.success) {
						if (json.html && json.html.length) {
							for (var i = 0; i < json.html.length; i++) {
								var html = $(json.html[i]);
								row.find('.twitter-replies').append(html);
								$('.timeago', html).timeago();
							}

							row.find('.reply-list').show();
						}

						if (json.retweet) {
							var link = row.find('li.opt-trigger.retweet');
							link.addClass('retweeted').removeClass('retweet');
							link.find('label').text('Retweeted');
						}

						retweetContainer.hide();

						if (json.archived) {
							row.addClass('archived');
							row.find('.status-archive').hide();
							row.find('.status-archived').show();

							if (page.menuOptions && !page.menuOptions.filter('[name=archived]').is(':checked')) {
								row.fadeOut('fast', function() {
									if (self.options.statusArchiveHideCallback) {
										self.options.statusArchiveHideCallback(row);
									}
									row.remove();
								});
							}
						}
					} else {
						alert(json.error);
					}
				}
			}).always(function() {
				retweetContainer.removeClass('loading');
			});
		});

		// unretweet trigger
		this.content.on('click', 'li.opt-trigger.retweeted', function(e) {
			e.preventDefault();

			var link = $(this);
			var id = self.closestRow(this).attr('data-status-id');

			if (id && confirm('Are you sure you want to un-retweet this?')) {
				self.removeTweetNotification(id);

				$.ajax({
					url: page.getMetaData('saveUnretweetUrl'),
					type: 'POST',
					dataType: 'json',
					data: {
						account_status_id: id
					},
					success: function(json) {
						if (json.success) {
							link.addClass('retweet').removeClass('retweeted');
							link.find('label').text('Retweet');
						} else {
							alert(json.error);
						}
					}
				});
			}
		});

		// message triggers
		this.content.on('click', 'li.opt-trigger.message', function(e) {
			e.preventDefault();

			var row = self.closestRow(this);
			var id = row.attr('data-status-id');

			var newMessage = row.find('.new-message');
			if (newMessage.is(':visible')) {
				newMessage.hide();
			} else {
				newMessage.show();
				row.find('.new-retweet').hide();

				self.removeTweetNotification(id);

				var textarea = newMessage.find('textarea');

				if (!textarea.hasClass('tae')) {
					textarea.TextAreaExpander();
				}

				if (!$.trim(textarea.val()).length && !row.hasClass('dm')) {
					var name = row.find('.main-status-body .screen-name, h4 .screen-name').first().text();

					textarea.val(name + ' ');
				}

				if (self.page.getMetaData('tweetSignature')) {
					textarea.val(textarea.val() + self.page.getMetaData('tweetSignature'));
				}

				self.updateTweetLength(textarea);
				textarea.focus();
			}
		});

		this.content.on('keypress keyup change', '.new-message textarea', function() {
			var $this = $(this);
			setTimeout(function() {
				self.updateTweetLength($this);
			}, 0);
		});
		this.content.on('click', '.new-message .message-type li', function() {
			var $this = $(this);
			var messageContainer = $this.closest('.new-message');
			var first = messageContainer.find('.message-type li').not($this).first();

			$this.removeClass('on');
			first.addClass('on');
			messageContainer.find('.message-type-hidden').val(first.data('type'));
		});
		this.content.on('click', '.cancel-message-trigger', function() {
			var messageContainer = $(this).closest('.new-message');
			messageContainer.hide();
		});
		this.content.on('click', '.save-message-trigger', function(e) {
			e.preventDefault();

			var row = self.closestRow(this);
			var id = row.attr('data-status-id');
			var messageContainer = $(this).closest('.new-message');

			var val = $.trim(messageContainer.find('textarea').val());
			if (!val.length) {
				messageContainer.hide();
				return;
			}

			var data = messageContainer.find('form').serializeArray();

			self.removeTweetNotification(id);

			if (self.options.saveMessageCallback === 'default') {
				messageContainer.addClass('loading');

				$.ajax({
					url: self.options.messageUrl,
					type: 'POST',
					dataType: 'json',
					data: data,
					success: function(json) {
						if (json.success) {
							if (json.html && json.html.length) {
								for (var i = 0; i < json.html.length; i++) {
									var html = $(json.html[i]);
									row.find('.twitter-replies').append(html);
									$('.timeago', html).timeago();
								}

								row.find('.reply-list').show();
							}

							row.find('.message-sent-confirmation .message').text(val);
							row.find('.message-sent-confirmation').show();

							messageContainer.hide();
							messageContainer.find('textarea').val('');

							if (json.archived) {
								row.addClass('archived');
								row.find('.status-archive').hide();
								row.find('.status-archived').show();

								if (page.menuOptions && !page.menuOptions.filter('[name=archived]').is(':checked')) {
									row.fadeOut('fast', function() {
										if (self.options.statusArchiveHideCallback) {
											self.options.statusArchiveHideCallback(row);
										}
										row.remove();
									});
								}
							}
						} else {
							alert(json.error);
						}
					}
				}).always(function() {
					messageContainer.removeClass('loading');
				});
			} else if (self.options.saveMessageCallback) {
				self.options.saveMessageCallback(data, row);
			}
		});

		// note triggers
		this.content.on('click', '.note-btn', function() {
			var row = self.closestRow(this);
			var id = row.attr('data-status-id');

			var newNote = row.find('.new-note');
			if (newNote.is(':visible')) {
				newNote.hide();
			} else {
				var textarea = newNote.find('textarea');
				if (!textarea.data('redactor')) {
					self.initializeNoteEditor(textarea, page.getMetaData('agentMap'));
				}

				newNote.show();

				self.removeTweetNotification(id);

				if (textarea.data('redactor')) {
					textarea.setFocus();
				} else {
					textarea.focus();
				}
			}
		});
		this.content.on('click', '.cancel-note-trigger', function() {
			var noteContainer = $(this).closest('.new-note');
			noteContainer.hide();
		});
		this.content.on('click', '.save-note-trigger', function(e) {
			e.preventDefault();

			var row = self.closestRow(this);
			var id = row.attr('data-status-id');
			var noteContainer = $(this).closest('.new-note');

			var textarea = noteContainer.find('textarea');
			if (textarea.data('redactor')) {
				textarea.data('redactor').syncCode();
			}

			var val = $.trim(textarea.val());
			if (!val.length) {
				noteContainer.hide();
				return;
			}

			noteContainer.addClass('loading');

			self.removeTweetNotification(id);

			$.ajax({
				url: page.getMetaData('saveNoteUrl'),
				type: 'POST',
				dataType: 'json',
				data: {
					account_status_id: id,
					text: val
				},
				success: function(json) {
					if (json.success) {
						if (json.html) {
							var html = $(json.html);
							row.find('.note-list').append(html);
							$('.timeago', html).timeago();

							row.find('.status-notes').show();
						}

						noteContainer.hide();
						textarea.val('');
						if (textarea.data('redactor')) {
							textarea.setCode('');
						}
					} else {
						alert(json.error);
					}
				}
			}).always(function() {
				noteContainer.removeClass('loading');
			});
		});

		// assignment
		this.content.on('click', '.opt-trigger.agent > label, .opt-trigger.agent span', function(ev) {
			var li = $(this).closest('li');
			var row = $(this).closest('article.twitter-status');

			var select = li.find('select');
			if (!select.data('select2')) {
				DP.select(select);

				select.on('change', function() {
					var val = $(this).val();
					var sel = $(this).find(':selected');
					var label = sel.text().trim();

					self.removeTweetNotification(id);

					if (val == 'agent:' + DESKPRO_PERSON_ID) {
						label = 'Me';
					}

					var labelEl = row.find('li.opt-trigger.agent label');
					if (sel.data('icon')) {
						labelEl.text(' ' + label).prepend($('<img class="agent-assign-icon" />').attr('src', sel.data('icon')));
					} else {
						labelEl.text(label);
					}

					var id = $(this).closest('.twitter-status').attr('data-status-id');

					$.ajax({
						url: page.getMetaData('saveAssignUrl'),
						type: 'POST',
						dataType: 'json',
						data: { account_status_id: id, assign: val },
						success: function(json) {
							if (json.error) {
								alert(json.error);
							}
						}
					});
				});

				li.find('.select2-container').css({
					height: 5,
					overflow: 'hidden'
				})
			}

			var oldOpen = select.data('select2').open;
			var oldClose = select.data('select2').close;
			var s2 = select.data('select2');
			s2.open = function() {
				oldOpen.call(s2);
				select.data('select2').container.addClass('select2-dropdown-open');
			};
			s2.close = function() {
				select.data('select2').container.addClass('select2-dropdown-open');
				oldClose.call(s2);
			};

			s2.open();
		});
	},

	updateTweetLength: function(textarea) {
		var text = textarea.val();
		text = text.replace(/\r?\n/g, ' ')
			.replace(/http:\/\/(?=([^ \t\r\n[\]#]+))\1(?!#)/g, '1234567890123456789012')
			.replace(/https:\/\/(?=([^ \t\r\n[\]#]+))\1(?!#)/g, '1234567890123456789013');

		var newMessageArea = textarea.closest('.new-message, .new-retweet'),
			charCount = newMessageArea.find('.character-count'),
			charCountCounter = charCount.find('em'),
			overOptions = newMessageArea.find('.over-options');

		if (text.length > 140) {
			charCount.hide();
			overOptions.show();
		} else {
			charCountCounter.text(140 - text.length);
			charCount.show();
			overOptions.hide();
		}
	},

	closestRow: function(el) {
		return $(el).closest('.row-item, .overlay-content, .twitter-status-inline');
	},

	doArchiveStatus: function(id, archive) {
		$.ajax({
			url: this.page.getMetaData('saveArchiveUrl'),
			type: 'POST',
			dataType: 'json',
			data: { account_status_id: id, archive: archive ? 1 : 0 },
			success: function(json) {
				if (json.error) {
					alert(json.error);
				}
			}
		});
	},

	doFavorite: function(id, favorite) {
		$.ajax({
			url: this.page.getMetaData('saveFavoriteUrl'),
			type: 'POST',
			dataType: 'json',
			data: { account_status_id: id, favorite: favorite ? 1 : 0 },
			success: function(json) {
				if (json.error) {
					alert(json.error);
				}
			}
		});
	},

	doArchiveUser: function(id, archive) {
		$.ajax({
			url: this.page.getMetaData('saveUserArchiveUrl'),
			type: 'POST',
			dataType: 'json',
			data: {
				user_id: id,
				account_id: this.page.getMetaData('accountId'),
				archive: archive ? 1 : 0
			},
			success: function(json) {
				if (json.error) {
					alert(json.error);
				}
			}
		});
	},

	removeTweetNotification: function(id) {
		DeskPRO_Window.notifications.removeRelated('tweet:' + id);
	},

	initializeNoteEditor: function(textarea, agentMap) {
		if (textarea.data('redactor')) {
			return;
		}

		textarea.redactor({
			toolbar: false,
			buttons: [],
			shortcuts: false,
			minHeight: 50
		});

		var api = textarea.data('redactor');
		if (!api) {
			return;
		}

		var editor = textarea.getEditor();
		if (!editor) {
			return false
		}

		editor.bind('keydown', function(ev) {
			ev.stopPropagation();

			if (ev.metaKey && !ev.ctrlKey) { // pressing "cmd" on a mac
				var sel;
				if (window.getSelection && (sel = window.getSelection()) && sel.modify) {
					var adjustmentType = ev.shiftKey ? "extend" : "move";

					switch (ev.keyCode) {
						case 39: // right - act like "end" in windows
							sel.modify(adjustmentType, "right", "lineboundary");
							ev.preventDefault();
							break;

						case 37: // left - act like "home" in windows
							sel.modify(adjustmentType, "left", "lineboundary");
							ev.preventDefault();
							break;
					}
				}
			}
		});

		editor.bind('keypress', function(ev) {
			ev.stopPropagation();
		});

		DeskPRO_Window.initAgentNotifierForRte(
			self, textarea, agentMap || false, true
		);
	}

	/*_initFollow: function() {
		var buttons = $('.follow a', this.content);

		buttons.on('click', $.proxy(function(e) {
			this.doFollow($(e.target).parents('.status').attr('data-user-id'));
		}, this));
	},

	doFollow: function(id) {
		$.ajax({
			url: this.getMetaData('saveFollowUrl'),
			dataType: 'json',
			data: {
				account_id: this.getMetaData('accountId'),
				user_id: id
			},
			context: this,
			success: function(json) {
				if (json.success) {
					this.reload();
				} else {
					alert(json.error);
				}
			}
		});
	},

	_initUnfollow: function() {
		var buttons = $('.unfollow a', this.content);

		buttons.on('click', $.proxy(function(e) {
			this.doUnfollow($(e.target).parents('.status').attr('data-user-id'));
		}, this));
	},

	doUnfollow: function(id) {
		$.ajax({
			url: this.getMetaData('saveUnfollowUrl'),
			dataType: 'json',
			data: {
				account_id: this.getMetaData('accountId'),
				user_id: id
			},
			context: this,
			success: function(json) {
				if (json.success) {
					this.reload();
				} else {
					alert(json.error);
				}
			}
		});
	},*/
});
Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');

DeskPRO.Agent.PageFragment.Page.SnippetViewer = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'snippets';
		this.allowDupe = true;
		this.activeSection = null;
		this.activeSnippets = $([]);
		this.noIgnoreForm = true;
	},

	initPage: function(el) {
		var self = this;
		this.snippet_typename = this.meta.snippet_typename;

		if (this.snippet_typename == 'tickets') {
			var driver = DeskPRO_Window.ticketSnippetDriver;
		} else {
			var driver = DeskPRO_Window.chatSnippetDriver;
		}

		this.snippetDriver = driver;

		//----------------------------------------
		// Browsing snippets
		//----------------------------------------

		var catList = this.getEl('catlist');
		var snippetList = this.getEl('snippet_list');
		var filterInput = this.getEl('filter');
		var langSelect  = this.getEl('show_language_id');

		var rowsTpl = twig({
			data: DeskPRO_Window.util.getPlainTpl($('#snippet_rows_tpl'))
		});

		this.rowsTpl = rowsTpl;

		var pickLangText = function(lang_vals, myLangId, showLangId) {
			if (!showLangId || showLangId == "0") {
				showLangId = DESKPRO_DEFAULT_LANG_ID;
			}

			var ret = { my: '', myId: 0, show: '', showId: 0 };
			var hasShow = false;
			Array.each(lang_vals, function(l) {
				if (l.value && l.language_id == myLangId) {
					ret.my = l.value;
					ret.myId = l.language_id;
				}
				if (l.value && l.language_id == showLangId) {
					ret.show = l.value;
					ret.showId = l.language_id;
				}
				if (!ret.show && l.value) {
					ret.show = l.value;
					ret.showId = l.language_id;
				}
			});

			if ((!ret.show || ret.showId != showLangId) && ret.my && ret.myId) {
				ret.show = ret.my;
				ret.showId = ret.myId;
			}

			return ret;
		};

		this.pickLangText = pickLangText;

		var useLocalCompare  = (typeof String.localeCompare != "undefined");

		var sortSnippets = function(snippets) {
			return snippets.sort(function(a, b) {
				var a_string, b_string;
				a_string = a.title_use.show || '';
				b_string = b.title_use.show || '';

				if (useLocalCompare) {
					var cmp = a_string.localeCompare(b_string, null, {
						usage: 'sort',
						ignorePunctuation: true,
						caseFirst: false
					});
					return cmp;
				} else {
					a_string = a_string.toLowerCase();
					b_string = b_string.toLowerCase();

					if (a_string == b_string) {
						return 0;
					}

					return (a_string < b_string) ? -1 : 1;
				}
			});
		};

		var updateCatList = function(categoryId, filterString, languageId) {

			var myLangId   = DESKPRO_PERSON_LANG_ID;
			var showLangId = langSelect.val();

			if (categoryId) {
				driver.loadSnippets({
					categoryId: categoryId,
					filterString: filterString || null,
					languageId: languageId || null
				}, function(snippets) {
					var newList = $('<ul></ul>');

					Array.each(snippets, function(s) {
						s.title_use   = pickLangText(s.title, myLangId, showLangId);
						s.snippet_use = pickLangText(s.snippet, myLangId, showLangId);
					});

					snippets = sortSnippets(snippets);

					newList.html(rowsTpl.render({
						snippets: snippets
					}));

					snippetList.empty().append(newList);
				});
			} else {
				var catIds = [];
				catList.find('li').each(function() {
					var id = parseInt($(this).data('category-id'));
					if (id) {
						catIds.push(id);
					}
				});

				snippetList.empty();
				if (!catIds.length) {
					return;
				}

				var tick = 0;

				if (driver.driverName == 'client_db') {
					Array.each(catIds, function(cid) {
						driver.loadSnippets({
							categoryId: cid,
							filterString: filterString || null,
							languageId: languageId || null
						}, function(snippets) {
							if (!snippets.length) {
								return;
							}

							Array.each(snippets, function(s) {
								s.title_use   = pickLangText(s.title, myLangId, showLangId);
								s.snippet_use = pickLangText(s.snippet, myLangId, showLangId);
							});

							snippets = sortSnippets(snippets);

							var hasMore = false;
							if (snippets.length > 15) {
								var hasMore = true;
								snippets = snippets.slice(0, 15);
							}

							var newListWrap = $('<div/>');
							var catTitle = $('<div class="cat-title"/>');
							catTitle.text(catList.find('.category-' + cid).text());
							catTitle.appendTo(newListWrap);

							var newList = $('<ul></ul>');

							newList.html(rowsTpl.render({
								snippets: snippets
							}));

							newListWrap.append(newList);

							snippetList.append(newListWrap);
						});
					});
				} else {
					driver.loadSnippets({
						filterString: filterString || null
					}, function(snippets) {
						if (!snippets.length) {
							return;
						}

						Array.each(catIds, function(cid) {

							var catSnippets = snippets.filter(function(s) { return s.category_id == cid; });
							if (!catSnippets.length) {
								return;
							}

							Array.each(catSnippets, function(s) {
								s.title_use   = pickLangText(s.title, myLangId, showLangId);
								s.snippet_use = pickLangText(s.snippet, myLangId, showLangId);
							});

							catSnippets = sortSnippets(catSnippets);

							var hasMore = false;
							if (catSnippets.length > 15) {
								var hasMore = true;
								catSnippets = catSnippets.slice(0, 15);
							}

							var newListWrap = $('<div/>');
							var catTitle = $('<div class="cat-title"/>');
							catTitle.text(catList.find('.category-' + cid).text());
							catTitle.appendTo(newListWrap);

							var newList = $('<ul></ul>');

							newList.html(rowsTpl.render({
								snippets: catSnippets
							}));

							newListWrap.append(newList);
							snippetList.append(newListWrap);
						});
					});
				}
			}
		};

		langSelect.on('change', function(ev) {
			var categoryId = parseInt(catList.find('.on').data('category-id') || 0) || 0;
			var filterString = $.trim(filterInput.val());
			var languageId   = parseInt(langSelect.val()) || 0;

			updateCatList(categoryId, filterString, languageId);
		});

		catList.on('click', 'li', function(ev) {
			Orb.cancelEvent(ev);
			catList.find('.on').removeClass('on');
			var categoryId   = $(this).addClass('on').data('category-id');
			var filterString = $.trim(filterInput.val());
			var languageId   = parseInt(langSelect.val()) || 0;

			updateCatList(categoryId, filterString, languageId);
		});

		var filterTimer = null;
		var sendUpdate = function() {
			filterTimer = null;
			var categoryId = parseInt(catList.find('.on').data('category-id') || 0) || 0;
			var filterString = $.trim(filterInput.val());
			var languageId   = parseInt(langSelect.val()) || 0;

			updateCatList(categoryId, filterString, languageId);
		};

		var cmdEat = false;
		filterInput.on('change keydown keyup', function(ev) {

			if (ev.keyCode == 13 /* enter key */) {
				ev.preventDefault();

				if (cmdEat) { cmdEat = false; return; }
				cmdEat = true;

				var activeSnippets = snippetList.find('li.snippet');
				var current = activeSnippets.filter('.cursor');
				if (!current[0]) {
					if (activeSnippets.length == 1) {
						current = activeSnippets;
					}
				}

				if (current[0]) {
					current.click();
				}

				return;
			} else if (ev.keyCode == 40 /* down key */ || ev.keyCode == 38 /* up key */) {
				ev.preventDefault();

				if (cmdEat) { cmdEat = false; return; }
				cmdEat = true;

				var activeSnippets = snippetList.find('li.snippet');


				var dir = ev.keyCode == 40 ? 'down' : 'up';

				var current = activeSnippets.filter('.cursor');
				if (!current.length) {
					if (dir == 'down') {
						activeSnippets.first().addClass('cursor');
					} else {
						activeSnippets.last().addClass('cursor');
					}
				} else {
					var nextIndex = activeSnippets.index(current);
					if (dir == 'down') {
						nextIndex++;
					} else {
						nextIndex--;
					}

					if (nextIndex < 0) {
						nextIndex = activeSnippets.length-1;
					} else if (nextIndex > (activeSnippets.length-1)) {
						nextIndex = 0;
					}

					current.removeClass('cursor');
					activeSnippets.eq(nextIndex).addClass('cursor');
				}

				return;
			}

			cmdEat = false;

			if (filterTimer) {
				window.clearTimeout(filterTimer);
				filterTimer = null;
			}

			filterTimer = window.setTimeout(function() {
				sendUpdate();
			}, 140);
		});

		//----------------------------------------
		// Inserting snippets
		//----------------------------------------

		snippetList.on('click', 'li', function(ev) {
			if ($(ev.target).hasClass('edit-trigger')) {
				return;
			}

			Orb.cancelEvent(ev);

			self.insertSnippetEl($(this), ev);
		});

		//----------------------------------------
		// Editing categories
		//----------------------------------------

		var catEditor = new (function() {
			var editCatEl = self.getEl('edit_snippet_cat');
			var editCatBack = null;
			var hasInit = false;

			self.wrapper.find('.trigger-newcat').on('click', function(ev) {
				Orb.cancelEvent(ev);
				openCatEditor(0, '');
			});

			var openCatEditor = function(catId, catTitle, shareOpt, openPos) {
				if (!hasInit) {
					hasInit = true;

					editCatEl.detach().appendTo('body');
					editCatBack = $('<div class="dp-popover-backdrop" />').hide();
					editCatBack.appendTo('body');

					editCatBack.on('click', function(ev) {
						Orb.cancelEvent(ev);
						closeCatEditor();
					});

					editCatEl.find('.trigger-close').on('click', function(ev) {
						Orb.cancelEvent(ev);
						closeCatEditor();
					});

					editCatEl.find('.trigger-save').on('click', function(ev) {
						Orb.cancelEvent(ev);
						saveCategory();
					});

					editCatEl.find('.delete-cat-trigger').on('click', function(ev) {
						Orb.cancelEvent(ev);
						delCategory();
					});
				}

				if (!openPos) {
					openPos = {
						of: self.wrapper.find('.trigger-newcat').first(),
						my: 'left top',
						at: 'center right',
						collision: 'flipfit'
					};
				}

				if (catList.find('li').length < 2) {
					editCatEl.find('.no-cats-notice').show();
				} else {
					editCatEl.find('.no-cats-notice').hide();
				}

				editCatEl.css({left: 0, top: 0});
				editCatEl.position(openPos);

				editCatEl.find('.input_id').val(catId || '0');
				editCatEl.find('.input_title').val(catTitle || '');

				shareOpt = shareOpt || 'me';
				editCatEl.find('.perm-type-opt').prop('checked', false).filter('[value="'+shareOpt+'"]').prop('checked', true);

				if (catId && catId != "0") {
					editCatEl.find('.delete-link-wrap').show();
				} else {
					editCatEl.find('.delete-link-wrap').hide();
				}

				editCatEl.show();
				editCatBack.show();
			};
			this.openCatEditor = openCatEditor;

			var closeCatEditor = function() {
				editCatEl.hide();
				editCatBack.hide();
			};

			var saveCategory = function() {
				var catId    = editCatEl.find('.input_id').val();
				var catTitle = $.trim(editCatEl.find('.input_title').val());
				var shareOpt = editCatEl.find('.perm-type-opt').filter(':checked').val();

				if (!catTitle) {
					closeCatEditor();
					return;
				}

				var postData = [];
				postData.push({
					name: 'title',
					value: catTitle
				});
				postData.push({
					name: 'perm_type',
					value: shareOpt
				});

				editCatEl.addClass('dp-loading-on');
				$.ajax({
					url: BASE_URL + 'agent/text-snippets/'+self.snippet_typename+'/categories/'+catId+'/save.json',
					data: postData,
					dataType: 'json',
					type: 'POST',
					complete: function() {
						editCatEl.removeClass('dp-loading-on');
					},
					success: function(data) {
						closeCatEditor();

						var cat = data.category;

						var catEl = catList.find('.category-' + cat.id);
						if (catEl[0]) {
							catEl.find('.label').text(catTitle);
							if (shareOpt == 'global') {
								catEl.data('is-global', 1);
							} else {
								catEl.data('is-global', null);
							}

							self.getEl('editsnippet_category_select').find('option[value="' + catId + '"]').text(catTitle);
						} else {
							catEl = $('<li><a><span class="label"></span></a><span class="trigger-edit-cat"><i class="icon-cog"></i></span></li>');
							catEl.addClass('category category-' + cat.id);
							catEl.data('category-id', cat.id);
							if (shareOpt == 'global') {
								catEl.data('is-global', 1);
							} else {
								catEl.data('is-global', null);
							}
							catEl.find('.label').text(cat.title[0].value);
							catEl.insertAfter(catList.find('.category-0'));

							// Also add cat option
							var catOpt = $('<option/>');
							catOpt.val(cat.id);
							catOpt.text(cat.title[0].value);

							self.getEl('editsnippet_category_select').prepend(catOpt);
						}

						catEl.click();

						// reload the shell
						driver.getWidgetShellTemplate(true);
					}
				});
			};

			var delCategory = function() {
				if (confirm('Are you sure?')) {
					var catId = editCatEl.find('.input_id').val();
					editCatEl.addClass('dp-loading-on');
					$.ajax({
						url: BASE_URL + 'agent/text-snippets/'+self.snippet_typename+'/categories/'+catId+'/delete.json',
						dataType: 'json',
						type: 'POST',
						complete: function() {
							editCatEl.removeClass('dp-loading-on');
						},
						success: function(data) {

							editCatEl.removeClass('dp-loading-on');
							closeCatEditor();

							if (data.error) {
								alert("You cannot delete this category because it still has snippets in it. Delete the snippets first then try again.");
								return;
							}

							var catEl = catList.find('.category-' + catId);
							catEl.remove();

							// reload the shell
							driver.getWidgetShellTemplate(true);
						}
					});
				};
			};

			this.destroy = function() {
				if (hasInit) {
					editCatEl.detach();
					editCatBack.detach();
				}
			};
		})();

		this.ownObject(catEditor);

		catList.on('click', '.trigger-edit-cat', function(ev) {
			Orb.cancelEvent(ev);
			var row = $(this).closest('li');
			var catId = row.data('category-id');
			var catTitle = $.trim(row.find('.label').text());
			var shareOpt = row.data('is-global') ? 'global' : 'me';
			var openPos = {
				of: $(this),
				my: 'left top',
				at: 'center right',
				collision: 'flipfit'
			};

			catEditor.openCatEditor(catId, catTitle, shareOpt, openPos);
		});

		//----------------------------------------
		// Editing snippets
		//----------------------------------------

		this._initEditingSnippets();

		if (!catList.find('.on')[0]) {
			catList.find('li').first().click();
		}

		filterInput.focus();
		this.addEvent('activate', function() {
			if (filterInput) {
				filterInput.focus();
			}
		});
	},

	closeSelf: function() {
		var ev = {cancel: false};
		this.fireEvent('closeSelf', ev);

		if (!ev.cancel) {
			this.parent();
		}
	},

	destroy: function() {
		if (DeskPRO_Window.activeListNav == this.listNav) {
			DeskPRO_Window.activeListNav = null;
		}
		if (this.newCatOverlay) this.newCatOverlay.remove();
	},

	insertSnippetEl: function(el, event, evData) {

		var snippetId = $(el).data('snippet-id');
		var snippetCode = [];

		el.find('.snippet-value').each(function(ev) {
			snippetCode.push({
				language_id: $(this).data('language-id'),
				value: $(this).val()
			});
		});

		evData = evData || {};
		evData = $.extend(evData, {
			event: event || null,
			snippetId: snippetId,
			snippetCode: snippetCode
		});

		this.fireEvent('snippetClick', [evData]);
	},

	//#########################################################################
	// Editing snippets
	//#########################################################################

	_initEditingSnippets: function() {
		var self = this;
		var snippetList = this.getEl('snippet_list');
		var editSnippetEl = this.getEl('edit_snippet');
		var textarea = this.getEl('snippet_input');

		if (!textarea.data('redactor')) {
			DeskPRO_Window.initRteAgentReply(textarea, {
				defaultIsHtml: true,
				autoresize: false
			});
		}

		//------------------------------
		// Starting edit
		//------------------------------

		snippetList.on('click', '.edit-trigger', function(ev) {
			Orb.cancelEvent(ev);
			var snippetId = $(this).closest('li').data('snippet-id');
			self.snippetDriver.getSnippet(snippetId, function(snippet) {
				self.editSnippet(snippet);
			});
		});

		self.wrapper.find('.trigger-new-snippet').on('click', function(ev) {
			Orb.cancelEvent(ev);

			if (self.getEl('catlist').find('li').length < 2) {
				self.wrapper.find('.trigger-newcat').first().click();
				return;
			}

			self.editSnippet();
		});

		//------------------------------
		// Switching between langs
		//------------------------------

		editSnippetEl.find('.language_id').on('change', function(ev) {
			var langId         = $(this).val();
			var inputTitleEl   = self.getEl('title_input');
			var inputSnippetEl = self.getEl('snippet_input');

			var langTitleEl   = editSnippetEl.find('.lang-bound-title.lang-' + langId);
			var langSnippetEl = editSnippetEl.find('.lang-bound-snippet.lang-' + langId);

			textarea.data('redactor').syncCode();

			// The initial fire of this is after opening a new edit window,
			// so we're just setting the defaults but not syncing an empty value back to the lang-x elements
			if ($(this).hasClass('initial')) {
				inputTitleEl.val(langTitleEl.val());
				textarea.data('redactor').setCode(langSnippetEl.val());

			} else if ($(this).hasClass('set-bound')) {
				langTitleEl.val(inputTitleEl.val());
				langSnippetEl.val(inputSnippetEl.val());

			// Else make sure theyre both the same
			} else {
				inputTitleEl.val(langTitleEl.val());
				textarea.data('redactor').setCode(langSnippetEl.val());

				langTitleEl.val(inputTitleEl.val());
				langSnippetEl.val(inputSnippetEl.val());
			}

			$(this).removeClass('initial set-bound');
		});

		//------------------------------
		// Saving snippet
		//------------------------------

		editSnippetEl.find('.save-snippet-trigger').on('click', function(ev) {
			editSnippetEl.find('.language_id').addClass('set-bound').trigger('change');

			Orb.cancelEvent(ev);
			var snippet = self.editingSnippet;
			var oldShortcutCode = snippet.shortcut_code;

			snippet.category_id = editSnippetEl.find('select.category_id').val();

			editSnippetEl.find('.lang-bound-title').each(function() {
				var langId = $(this).data('language-id');
				var value = $(this).val();
				var found = false;

				for (var i = 0; i < snippet.title.length; i++) {
					if (snippet.title[i].language_id == langId) {
						snippet.title[i].value = value;
						found = true;
						break;
					}
				}

				if (!found) {
					snippet.title.push({
						language_id: langId,
						value: value
					})
				}
			});

			editSnippetEl.find('.lang-bound-snippet').each(function() {
				var langId = $(this).data('language-id');
				var value = $(this).val();
				var found = false;

				for (var i = 0; i < snippet.snippet.length; i++) {
					if (snippet.snippet[i].language_id == langId) {
						snippet.snippet[i].value = value;
						found = true;
						break;
					}
				}

				if (!found) {
					snippet.snippet.push({
						language_id: langId,
						value: value
					});
				}
			});

			snippet.shortcut_code = editSnippetEl.find('.shortcut_code').val();

			editSnippetEl.find('.overlay-footer').addClass('loading');
			self.snippetDriver.saveSnippet(snippet, function(snippet) {

				var myLangId   = DESKPRO_PERSON_LANG_ID;
				var showLangId = self.getEl('show_language_id').val();

				snippet.title_use   = self.pickLangText(snippet.title, myLangId, showLangId);
				snippet.snippet_use = self.pickLangText(snippet.snippet, myLangId, showLangId);

				editSnippetEl.find('.overlay-footer').removeClass('loading');
				self.snippetEditOverlay.close();

				var currentCatId = self.getEl('catlist').find('.on').data('category-id') || 0;

				var newList = $('<ul></ul>');
				newList.html(self.rowsTpl.render({
					snippets: [snippet]
				}));

				var row = newList.find('li').first();

				if (!currentCatId || snippet.category_id == currentCatId) {
					var exist = self.getEl('snippet_list').find('.snippet-' + snippet.id);
					if (exist[0]) {
						exist.replaceWith(row);
					} else {
						self.getEl('snippet_list').prepend(row);
					}
				}

				var newShortcutCode = snippet.shortcut_code;

				if (self.snippet_typename == 'tickets') {
					if (oldShortcutCode) {
						delete window.DESKPRO_TICKET_SNIPPET_SHORTCODES[oldShortcutCode];
					}
					if (newShortcutCode) {
						window.DESKPRO_TICKET_SNIPPET_SHORTCODES[newShortcutCode] = snippet.id;
					}
				} else if (self.snippet_typename == 'chat') {
					if (oldShortcutCode) {
						delete window.DESKPRO_CHAT_SNIPPET_SHORTCODES[oldShortcutCode];
					}
					if (newShortcutCode) {
						window.DESKPRO_CHAT_SNIPPET_SHORTCODES[newShortcutCode] = snippet.id;
					}
				}
			}, function() {
				editSnippetEl.find('.overlay-footer').removeClass('loading');
			});

		});

		//------------------------------
		// Deleting snippet
		//------------------------------

		editSnippetEl.find('.delete-snippet-trigger').on('click', function(ev) {
			var snippet = self.editingSnippet;
			editSnippetEl.find('.overlay-footer').addClass('loading');
			self.snippetDriver.deleteSnippet(snippet.id, function(snippet_id) {
				editSnippetEl.find('.overlay-footer').removeClass('loading');
				self.snippetEditOverlay.close();

				var exist = self.getEl('snippet_list').find('.snippet-' + snippet_id);
				exist.remove();
			}, function() {
				editSnippetEl.find('.overlay-footer').removeClass('loading');
			});
		});

		//------------------------------
		// Init overlay
		//------------------------------

		var varSel = editSnippetEl.find('.variables-select');
		editSnippetEl.find('.variables-insert-btn').on('click', function() {
			var text = '{{ ' + varSel.val() + ' }}';

			if (textarea.data('redactor')) {
				textarea.data('redactor').insertHtml(DP.convertTextToWysiwygHtml(text, false));
			} else {
				var pos = textarea.getCaretPosition();
				if (!pos) {
					textarea.setCaretPosition(0);
				}

				textarea.insertAtCaret(text);
			}
		});

		this.snippetEditOverlay = new DeskPRO.UI.Overlay({
			contentElement: editSnippetEl,
			zIndex: 30010
		});
	},

	editSnippet: function(snippet) {

		if (!snippet) {
			snippet = {
				id: 0,
				category_id: this.getEl('catlist').find('.on').data('category-id') || this.getEl('catlist').find('li').eq(1).data('category-id'),
				shortcut_code: '',
				title: [],
				snippet: []
			};
		}

		this.editingSnippet = snippet;
		var editSnippetEl = this.getEl('edit_snippet');
		editSnippetEl.find('input, textarea').val('');
		editSnippetEl.find('input.snippet_id').val(snippet.id);
		editSnippetEl.find('select.category_id').val(snippet.category_id);
		editSnippetEl.find('input.shortcut_code').val(snippet.shortcut_code);

		Array.each(snippet.title, function(trans) {
			editSnippetEl.find('input.title.lang-' + trans.language_id).val(trans.value);
		});
		Array.each(snippet.snippet, function(trans) {
			editSnippetEl.find('input.snippet.lang-' + trans.language_id).val(trans.value);
		});

		editSnippetEl.find('.language_id').addClass('initial').trigger('change');

		if (snippet.id) {
			editSnippetEl.find('.delete-link-wrap').show();
		} else {
			editSnippetEl.find('.delete-link-wrap').hide();
		}

		this.snippetEditOverlay.open();
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');

DeskPRO.Agent.PageFragment.Page.Ticket = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'ticket';
		this.wrapper = null;
		this.changeManager = null;
		this.valueForm = null;
		this.layout = null;
		this.popout = null;
		this.popout_overview = null;
		this.isMouseOverPopout = false;
		this.hasInitPopout = false;
		this.popoutPage = null;
		this.lastActiveDate = null;
		this.ticketReplyBox = null;
	},

	getAlertId: function() {
		return 'ticket-row-' + this.meta.ticket_id;
	},

	initMetaData: function() {
		DeskPRO_Window.recentTabs.add(
			'tickets',
			this.meta.ticket_id,
			this.meta.title,
			BASE_URL + 'agent/tickets/' + this.meta.ticket_id
		);
	},

	initPage: function(el) {
		this.wrapper = el;
		var self = this;
		this.getEl('replybox_wrap').data('page', this);
		this.hasReplyFocused = false;

		if (this.getEl('linked_count').data('count') == "0") {
			this.getEl('linked_wrap_tab').hide();
		}

		try {
			var flashEnabled = !!(navigator.mimeTypes["application/x-shockwave-flash"] || window.ActiveXObject && new ActiveXObject('ShockwaveFlash.ShockwaveFlash'));
			if (flashEnabled) {
				// Set timeout to have it exec in global scope,
				// so errors (eg flash has crashed) can be ignored and dont break the rest of this init
				window.setTimeout(function() {
					self.wrapper.find('.copy-btn').each(function() {
						var btnEl = this;
						var btn = $(this);

						try {
							var clip = new ZeroClipboard(this, {
								btnEl: this,
								savePuffEl: self.getEl('idref_switch')
							});
							clip.on('mouseover', function(client, args) {
								$(client.options.btnEl).addClass('over');
							});
							clip.on('mouseout', function(client, args) {
								$(client.options.btnEl).removeClass('over');
							});
							clip.on('complete', function(client, args) {
								DeskPRO_Window.util.showSavePuff($(this).closest('.id-number'));
							});

							self.addEvent('destroy', function() {
								try {
									clip.unglue(btnEl);
								} catch (e) {}
							});
							self.addEvent('activate', function() {
								try {
									clip.reposition();
								} catch (e) {}
							});
						} catch (e) {}
					});
				}, 100);
			} else {
				this.wrapper.find('.copy-btn').remove();
			}
		} catch (e) {
			this.wrapper.find('.copy-btn').remove();
		}

		this.valueForm = $('form.value-form:first', this.wrapper);
		this.valueForm.on('submit', function(ev) {
			// Never actually submit the form (would load a new page)
			ev.preventDefault();
		});

		this.changeManager = new DeskPRO.Agent.Ticket.ChangeManager(this);
		this.changeManager.addEvent('updateResult', function(data) {
			self.clearAlerts();
			DeskPRO_Window.getMessageBroker().sendMessage('agent.ui.ticket_updated', { ticket_id: self.meta.ticket_id });

			if (data.data && !data.data.can_view) {
				self.closeSelf();
			}

			if (data.data && data.data.refresh) {
				DeskPRO_Window.loadPage(BASE_URL + 'agent/tickets/' + self.getMetaData('ticket_id'), {ignoreExist:true});
				self.closeSelf();
			}
		});

		this.changePic = new DeskPRO.Agent.PageFragment.Page.PersonHelper.ChangePic(this, {
			loadUrl: BASE_URL + "agent/people/" + this.meta.person_id + "/change-picture-overlay",
			saveUrl: BASE_URL + 'agent/people/' + this.meta.person_id + '/ajax-save'
		});
		this.ownObject(this.changePic);

		this.ticketFields = new DeskPRO.Agent.PageHelper.TicketFields(this);
		this.ownObject(this.ticketFields);

		this._initMessage(this.wrapper.find('.messages-wrap'));

		this.getEl('value_form').find('.language_id').on('change', function() {
			var langId     = $(this).val();
			if (!langId) {
				langId = DESKPRO_DEFAULT_LANG_ID;
			}

			var langLocale = DESKPRO_NAME_REGISTRY.lang_data[langId].locale;
			var langTitle  = $.trim(DESKPRO_NAME_REGISTRY.lang_data[langId].title);

			self.getEl('message_page_wrap').find('.translate-from-lang').each(function() {
				$(this).text(langTitle).data(langLocale);
			});
		});

		this._initTicketActionsMenu();
		this._initMessageActionsMenu();
		this._initLabels();
		this._initTicketLocking();
		this._initTasks();
		this._initEditName();
		this._initSlas();

		// Change email menu
		var emailText = this.getEl('user_email_text');
		var emailChangeTrig = this.getEl('user_email_menu_trigger');
		var emailChangeMenu = this.getEl('user_email_menu');
		var emailChangeBackdrop = null;
		if (emailChangeTrig[0]) {

			var closeEmailChangeMenu = function() {
				emailChangeBackdrop.hide();
				emailChangeMenu.hide();
			};

			var updateEmailChangePos = function() {
				var pos = emailChangeTrig.offset();
				emailChangeMenu.css({
					left: pos.left + 3,
					top: pos.top + 32
				});
			};

			var openEmailChangeMenu = function() {
				// Means we're opening fo rhte first time
				if (!emailChangeBackdrop) {
					emailChangeBackdrop = $('<div class="backdrop"></div>');
					emailChangeBackdrop.appendTo('body');
					emailChangeBackdrop.on('click', function(ev) {
						ev.stopPropagation();
						closeEmailChangeMenu();
					});
					emailChangeMenu.detach().appendTo('body');

					emailChangeMenu.find('li').on('click', function(ev) {
						ev.preventDefault();;
						var item = $(this);
						var emailId = item.data('email-id');
						var text = item.text().trim();

						emailText.text(text);
						closeEmailChangeMenu();

						$.ajax({
							url: BASE_URL + 'agent/tickets/'+self.meta.ticket_id+'/ajax-change-email.json',
							data: { email_id : emailId },
							dataType: 'json',
							type: 'POST'
						});
					});
				}

				emailChangeBackdrop.show();
				updateEmailChangePos();
				emailChangeMenu.show();
			};


			emailChangeTrig.on('click', function(ev) {
				ev.preventDefault();
				openEmailChangeMenu();
			});
		}

		this.billing = new DeskPRO.Agent.PageHelper.TicketBilling(this.getEl('billing_wrap'), this.meta.baseId, {
			auto_start_bill: this.meta.auto_start_bill
		});

		this.addEvent('deactivate', function() {
			$('form.ticket-reply-form', this.getEl('replybox_wrap')).trigger('page_deactivate');
		});
		this.addEvent('activate', function() {
			$('form.ticket-reply-form', this.getEl('replybox_wrap')).trigger('page_activate');
		});

		this.addEvent('destroy', function() {
			if (self.meta.unlockOnClose && self.getEl('locked_message').data('locked-self')) {
				$.ajax({
					url: BASE_URL + 'agent/tickets/'+self.meta.ticket_id+'/release-lock.json',
					type: 'POST'
				});
			}
		}, false, false, true);

		if (this.meta.ticket_perms['delete']) {
			this.wrapper.on('click', 'button.undelete-trigger', this.doTicketUndelete.bind(this));
			this.wrapper.on('click', 'button.unspam-trigger', this.doTicketUnspam.bind(this));
		}

		DeskPRO_Window.getMessageBroker().sendMessage('ui.ticket.opened', { ticketId: this.getMetaData('ticket_id') });
		DeskPRO_Window.getMessageBroker().sendMessage('ui.tab.opened', { type: 'tickets', id: this.getMetaData('ticket_id') });

		DeskPRO_Window.getMessageBroker().addMessageListener('tickets.deleted', (function(ticket_ids) {
			if (ticket_ids.indexOf(this.getMetaData('ticket_id')) !== -1) {
				DeskPRO_Window.removePage(this);
			}
		}).bind(this), this.pageUid);

		DeskPRO_Window.getMessageBroker().addMessageListener('agent.ui.reload', function (info) {
			self.getReplyTextArea().trigger('dp_autosave_trigger');
		});

		this.addEvent('shortcutFocusReply', function(ev) {
			ev.preventDefault();

			// Scroll down
			if (!self.meta.ticket_reverse_order) {
				self.wrapper.find('div.layout-content').trigger('goscrollbottom');
			}
			self.focusOnReply();
		});

		this.addEvent('shortcutOpenSnippets', function(ev) {
			ev.preventDefault();
			self.shortcutOpenSnippets();
		});
		this.addEvent('shortcutSendReply', function(ev) {
			ev.preventDefault();
			self.shortcutSendReply();
		});
		this.addEvent('shortcutReplySetAwaitingUser', function(ev) {
			ev.preventDefault();
			self.shortcutReplySetAwaitingUser();
		});
		this.addEvent('shortcutReplySetAwaitingAgent', function(ev) {
			ev.preventDefault();
			self.shortcutReplySetAwaitingAgent();
		});
		this.addEvent('shortcutReplySetResolved', function(ev) {
			ev.preventDefault();
			self.shortcutReplySetResolved();
		});
		this.addEvent('shortcutReplyOpenProperties', function(ev) {
			ev.preventDefault();
			self.shortcutReplyOpenProperties();
		});

		this.addEvent('openUserProfile', function(ev) {
			ev.preventDefault();
			self.getEl('profile_link').trigger('click');
		});

		this.addEvent('openOrgProfile', function(ev) {
			ev.preventDefault();
			self.getEl('org_link').trigger('click');
		});

		if (this.meta.ticket_perms.modify_merge) {
			this.merge = new DeskPRO.Agent.Widget.Merge({
				tabType: 'ticket',
				metaId: self.meta.ticket_id,
				metaIdName: 'ticket_id',
				trigger: $('.merge-menu-trigger', this.wrapper),
				overlayUrl: BASE_URL + 'agent/tickets/{id}/merge-overlay/{other}',
				mergeUrl: BASE_URL + 'agent/tickets/{id}/merge/{other}',
				loadRoute: 'ticket:' + BASE_URL + 'agent/tickets/{id}',
				overlayLoaded: function(overlay, merge) {
					overlay.getWrapper().find('.ticket-finder').bind('ticketsearchboxclick', function(ev, ticketId, subject, sb) {
						sb.close();

						$.ajax({
							url: merge._getOverlayUrl(merge.options.metaId, ticketId),
							type: 'get',
							dataType: 'html',
							success: function(html) {
								merge.resetOverlay(html);
							}
						});
					});
				}
			});
			this.ownObject(this.merge);

			this.mergeMenu = new (function() {
				var menuEl = null;
				var menuElInner = null;
				var backEl = null;
				var hasInitUserTickets = false;
				var lastOvers = null;
				var lastOverId = null;

				var updateOverHighlight = function(ticketId) {
					removeLastOverHighlight();

					if (!DeskPRO_Window.sections.tickets_section || !DeskPRO_Window.sections.tickets_section.isVisible()) {
						return;
					}

					var searchListEl = DeskPRO_Window.sections.tickets_section.getListElement();
					lastOverId = ticketId;
					lastOvers = searchListEl.find('.ticket-' + ticketId);
					lastOvers = lastOvers.add($('#tabNavigationPane').find('.ticket-' + ticketId));
					lastOvers.addClass('item-hover-over');
				};

				var removeLastOverHighlight = function() {
					if (lastOvers) {
						lastOvers.removeClass('item-hover-over');
					}
					lastOvers = null;
					lastOverId = null;
				}

				var renderTicketOption = function(ticket) {
					var row = $('<li><time></time><a><strong></strong><span></span></a></li>');
					row.data('ticket-id', ticket.id);
					row.addClass('ticket-' + ticket.id + ' ticket');

					row.find('strong').text(ticket.id);
					row.find('span').text(ticket.subject);

					var d = new Date(ticket.last_activity*1000);
					row.find('time').attr('datetime', d.toISOString()).timeago();

					row.on('mouseover', function() {
						updateOverHighlight(ticket.id);
					}).on('mouseout', function() {
						if (lastOverId && lastOverId == ticket.id) {
							removeLastOverHighlight();
						}
					});

					return row.get(0);
				};

				var refreshOpenTickets = function() {
					var append = [];
					Array.each(DeskPRO_Window.getTabWatcher().findTabType('ticket'), function(tab) {
						var id = tab.page.getMetaData('ticket_id');
						if (id && id != self.meta.ticket_id) {
							var row = renderTicketOption({
								id: id,
								subject: tab.title,
								last_activity: tab.page.getMetaData('last_activity')
							});

							append.push(row);
						}
					});

					if (append.length) {
						menuEl.find('.open-tickets').show().find('ul').empty().append($(append));
					} else {
						menuEl.find('.open-tickets').hide().find('ul').empty();
					}
				};

				var refreshUserTickets = function() {
					$.ajax({
						url: BASE_URL + 'agent/ticket-search/quick-search',
						data: {
							person_id: self.meta.person_id
						},
						dataType: 'json',
						success: function(data) {
							var append = [];
							Array.each(data, function(t) {
								if (t.id && t.id != self.meta.ticket_id) {
									var row = renderTicketOption(t);
									append.push(row);
								}
							});

							if (append.length) {
								menuEl.find('.users-tickets').show().find('ul').empty().append($(append));
							} else {
								menuEl.find('.users-tickets').hide().find('ul').empty();
							}
						}
					});
				};

				var refreshFilterResults = function() {
					if (!DeskPRO_Window.sections.tickets_section || !DeskPRO_Window.sections.tickets_section.isVisible()) {
						menuEl.find('.filter-tickets').hide().find('ul').empty();
						return;
					}

					var searchListEl = DeskPRO_Window.sections.tickets_section.getListElement();
					var append = [];

					searchListEl.find('.row-item').each(function() {
						var el = $(this);
						var t = {
							id: el.data('ticket-id'),
							subject: $.trim(el.find('.subject').text()),
							last_activity: parseInt(el.data('ticket-lastactivity'))
						};
						if (!t.id) {
							return;
						}

						if (t.id && t.id != self.meta.ticket_id) {
							var row = renderTicketOption(t);
							append.push(row);
						}
					});

					if (append.length) {
						menuEl.find('.filter-tickets').show().find('ul').empty().append($(append));
					} else {
						menuEl.find('.filter-tickets').hide().find('ul').empty();
					}
				};

				var openMenu = function(atEl) {
					var tmp;
					if (!menuEl) {
						menuEl = $('<div/>');
						menuEl.addClass('dp-popover');
						menuEl.css('width', 500);

						menuElInner = $('<div/>').addClass('dp-popover-inner');
						menuElInner.appendTo(menuEl);

						backEl = $('<div/>');
						backEl.addClass('dp-popover-backdrop');

						tmp = $('<div/>').html('<section><header><strong>'+self.meta.lang.find_ticket+'</strong></header><article style="padding: 6px;"><button class="trigger-search dp-btn dp-btn-small">'+self.meta.lang.search+'</button></article></section>');
						tmp.addClass('search-tickets');
						tmp.appendTo(menuElInner);

						tmp = $('<div/>').html('<section><header><strong>'+self.meta.lang.users_tickets+'</strong></header><article><ul></ul></article></section>');
						tmp.addClass('users-tickets').hide();
						tmp.appendTo(menuElInner);

						tmp = $('<div/>').html('<section><header><strong>'+self.meta.lang.open_tickets+'</strong></header><article><ul></ul></article></section>');
						tmp.addClass('open-tickets').hide();
						tmp.appendTo(menuElInner);

						tmp = $('<div/>').html('<section><header><strong>'+self.meta.lang.filter_results+'</strong></header><article><ul></ul></article></section>');
						tmp.addClass('filter-tickets').hide();
						tmp.appendTo(menuElInner);

						menuEl.find('.trigger-search').on('click', function(ev) {
							Orb.cancelEvent(ev);
							self.merge.open();
							closeMenu();
						});

						menuEl.on('click', 'li', function(ev) {
							Orb.cancelEvent(ev);
							closeMenu();
							self.merge.openWithId($(this).data('ticket-id'));
						});

						backEl.on('click', function(ev) {
							Orb.cancelEvent(ev);
							closeMenu();
						});

						menuEl.appendTo('body');
						backEl.appendTo('body')
					}

					if (!hasInitUserTickets) {
						hasInitUserTickets = true;
						refreshUserTickets();
					}
					window.setTimeout(function() { refreshOpenTickets(); }, 1);
					window.setTimeout(function() { refreshFilterResults(); }, 1);

					var maxH = parseInt(($(window).height() / 2) - 40);
					menuEl.find('.dp-popover-inner').css('max-height', maxH);

					menuEl.show();
					menuEl.position({
						of: atEl,
						my: 'center top',
						at: 'center bottom',
						collision: 'flipfit'
					});
					backEl.show();
				};

				var closeMenu = function() {
					menuEl.hide();
					backEl.hide();

					removeLastOverHighlight();
				};

				self.wrapper.find('.merge-menu-trigger').on('click', function(ev) {
					Orb.cancelEvent(ev);
					openMenu($(this));
				});

				this.destroy = function() {
					if (menuEl) {
						menuEl.detach();
					}
					if (backEl) {
						backEl.detach();
					}
				};
			})();
		}

		this.addEvent('deactivate', function() {
			if (self.ticketReplyBox && self.ticketReplyBox.textarea) {
				self.ticketReplyBox.textarea.trigger('dp_autosave_trigger');
			}
		});

		$('form.ticket-reply-form', this.getEl('replybox_wrap')).bind('replyboxsubmit', this.handleReplySave.bind(this));

		this.ticketActions = new DeskPRO.Agent.PageFragment.Page.Ticket.TicketActions(this);
		this.ownObject(this.ticketActions);

		if (this.meta.isLocked) {
			this.ticketLocked = new DeskPRO.Agent.PageFragment.Page.Ticket.TicketLocked(this);
			this.ownObject(this.ticketLocked);
		}

        $('.agent-link.other-agent', this.El).on('click', function() {
            DeskPRO_Window.sections.agent_chat_section.newChatWindow([$(this).data('agent-id')]);
        });

		this.getEl('newtask').on('click', function(ev) {
			ev.preventDefault();
			DeskPRO_Window.newTaskLoader.open();
		});

		this.ticketFields.updateDisplay();

		this.wrapper.find('.lock-overlay').on('click', function(ev) {
			ev.preventDefault();
			self.showLockAlert();
		});

		this.getEl('idref_switch').on('click', function() {
			if ($(this).hasClass('refmode')) {
				$(this).removeClass('refmode')
				self.getEl('ref_num').hide();
				self.getEl('id_num').show();
			} else {
				$(this).addClass('refmode')
				self.getEl('id_num').hide();
				self.getEl('ref_num').show();
			}
		});

		DeskPRO.ElementHandler_Exec(this.wrapper);
		var messageboxTabs = this.getEl('messagebox_tabs').data('simpletabs');
		if (messageboxTabs) {
			messageboxTabs.addEvent('tabSwitch', function(evData) {
				var type = evData.tabEl.data('list-type');

				if (type == 'messages') {
					self.getEl('messages_wrap').removeClass('show-log');
					self.getEl('messages_wrap').find('article.content-message').show();
				} else if (type == 'feedback') {
					self.getEl('messages_wrap').removeClass('show-log');
					self.getEl('messages_wrap').find('article.content-message').show().not('article.with-feedback').hide();
				} else if (type == 'log') {
					self.refreshLogTypes();
				}
			});
		}

		this.getEl('people_box_agent').find('.select2-container-multi').css('width', '90%').find('input.select2-input').css('width', '90%');

		if (this.getEl('field_errors').hasClass('on')) {
			this.wrapper.addClass('field-error');
			this.ticketFields.openEditMode();
		}

		var messagePageWrap = this.getEl('message_page_wrap');
		var messagesWrap = this.getEl('messages_wrap');
		this.getEl('message_prev_page').on('click', function(ev) {
			ev.preventDefault();
			if (self.meta.ticket_reverse_order) {
				var p = parseInt(messagesWrap.data('page')) - 1;
			} else {
				var p = parseInt(messagesWrap.data('page')) + 1;
			}
			self.loadMessagePage(p);
		});

		this.getEl('message_next_page').on('click', function(ev) {
			ev.preventDefault();
			if (self.meta.ticket_reverse_order) {
				var p = parseInt(messagesWrap.data('page')) + 1;
			} else {
				var p = parseInt(messagesWrap.data('page')) - 1;
			}
			self.loadMessagePage(p);
		});

		var head = this.getEl('properties_header');
		var st   = head.find('nav').data('simpletabs');
		if (st) {
			st.addEvent('tabSwitch', function(evData) {
				var id = $(evData.tabEl).attr('id') || '';

				if (id && id.indexOf('fields_display_main_wrap_tab') !== -1) {
					head.removeClass('controls-off');
				} else {
					head.addClass('controls-off');
				}
			});
		}

		this.getEl('cc_list_btn').on('click', function(ev) {
			ev.preventDefault();

			if (self.getEl('cc_list').hasClass('cc-open')) {
				if (!self.getEl('cc_row_list').find('li')[0]) {
					self.getEl('cc_list').hide().removeClass('cc-open');
				} else {
					self.getEl('cc_list').find('.addrow').toggle();
				}
			} else {
				self.getEl('cc_list').show().addClass('cc-open');
				self.getEl('cc_list').find('.addrow').show();
			}
		});

		var logsWrap = this.getEl('logs_wrap');
		logsWrap.on('click', '.trigger-update-filter', function(ev) {
			Orb.cancelEvent(ev);

			var logsNav  = logsWrap.find('nav').first();
			var filter = $(this).data('typename');

			var postData = [];

			if (filter && filter != 'all') {
				postData.push({name: 'filter', value: filter});
			}
			postData.push({name: 'page', value: 1});

			logsNav.addClass('dp-loading-on');
			if (filter == 'attach') {
				$.ajax({
					url: BASE_URL + 'agent/tickets/'+self.meta.ticket_id+'/load-attach-list',
					data: postData,
					complete: function() {
						logsNav.removeClass('dp-loading-on');
					},
					success: function(html) {
						logsWrap.html(html);
						self.updateUi();
					}
				});
			} else {
				$.ajax({
					url: BASE_URL + 'agent/tickets/'+self.meta.ticket_id+'/load-logs',
					data: postData,
					complete: function() {
						logsNav.removeClass('dp-loading-on');
					},
					success: function(html) {
						logsWrap.html(html);
						self.updateUi();
					}
				});
			}
		});

		logsWrap.on('click', '.trigger-next-page', function(ev) {
			var btn = $(this);
			var logsNav  = logsWrap.find('nav').first();

			var filter = logsNav.data('filter');
			var page   = logsNav.data('page');

			var postData = [];
			if (filter && filter != 'all') {
				postData.push({name: 'filter', value: filter});
			}
			if (page) {
				page++;
				postData.push({name: 'page', value: page});
			}

			logsNav.addClass('dp-loading-on');
			btn.addClass('dp-loading-on');
			$.ajax({
				url: BASE_URL + 'agent/tickets/'+self.meta.ticket_id+'/load-logs',
				data: postData,
				complete: function() {
					logsNav.removeClass('dp-loading-on');
					btn.remove();
				},
				success: function(html) {
					var el = $(html);
					var newNav = el.find('nav').first();
					var newPage = el.find('.logs-page').first();

					DeskPRO_Window.initInterfaceServices(newPage);

					logsNav.replaceWith(newNav);
					logsWrap.append(newPage);

					self.updateUi();
				}
			});
		});


		logsWrap.on('click', '.expand', function(ev) {
			var expandBtn = $(this);
			var el = expandBtn.closest('.log-row');

			if (!el[0]) {
				return;
			}

			Orb.cancelEvent(ev);

			var sel = '.expand-set';
			if ($(this).data('set')) {
				sel = $(this).data('set');
			}

			var expandEl = $(sel, el);
			if (expandEl.is(':visible')) {
				expandEl.slideUp();
				expandBtn.removeClass('open');
			} else {
				expandEl.slideDown();
				expandBtn.addClass('open');
			}
		});
	},

	setTicketReplyBox: function(rb) {
		var isFirst = this.ticketReplyBox ? false : true;

		if (this.ticketReplyBox) {
			this.ticketReplyBox.destroy();
			this.ticketReplyBox = null;
		}

		this.ticketReplyBox = rb;

		if (isFirst && this.meta.ticket_reverse_order) {
			this.focusOnReply();
		}
	},

	loadMessagePage: function(page, noShowLoading) {
		var messagePageWrap = this.getEl('message_page_wrap');
		var messagesWrap = this.getEl('messages_wrap');

		// No page means reload current page
		var reload = false;
		if (!page) {
			reload = true;
			page = parseInt(messagesWrap.data('page'));
		}

		var loadDiv = false;
		if (!noShowLoading) {
			loadDiv = $('<div style="padding: 25px;"><div class="loading-icon-big">&nbsp;</div></div>');
			if (!this.meta.ticket_reverse_order) {
				messagePageWrap.empty();
			}
			messagePageWrap.append(loadDiv);
			this.updateUi();
		}

		$.ajax({
			url: BASE_URL + 'agent/tickets/'+ this.meta.ticket_id +'/message-page/' + page,
			type: 'GET',
			dataType: 'html',
			context: this,
			success: function(html) {
				if (loadDiv) {
					loadDiv.remove();
				}
				if (this.meta.ticket_reverse_order && !reload) {
					var div = $('<div></div>');
					div.html(html);
					this._initMessage(div.find('article.content-message'));

					messagePageWrap.append(div);
				} else {
					messagePageWrap.empty();
					messagePageWrap.html(html);
					this._initMessage(messagePageWrap.find('article.content-message'));
				}
				this.updateUi();

				var d = messagePageWrap.find('> div').first();
				if (d[0]) {
					messagesWrap.data('page-count', d.data('page-count'));
					messagesWrap.data('page', d.data('page'));
				}

				messagesWrap.data('page', page);
				var numPages = parseInt(messagesWrap.data('page-count'));

				if (this.meta.ticket_reverse_order) {
					if (page == numPages) {
						this.getEl('message_next_page').hide();
					} else {
						this.getEl('message_next_page').show();
					}
				} else {
					if (page == numPages) {
						this.getEl('message_prev_page').hide();
					} else {
						this.getEl('message_prev_page').show();
					}
					if (page == 1) {
						this.getEl('message_next_page').hide();
					} else {
						this.getEl('message_next_page').show();
					}
				}
			}
		});
	},

	refreshLogTypes: function() {
		var self     = this;
		var logsLi   = this.getEl('messagebox_tabs').find('.logs');
		var logsWrap = this.getEl('logs_wrap');
		var logsNav  = logsWrap.find('nav').first();
		var isActive = logsLi.hasClass('on');

		if (!isActive || !logsLi.hasClass('dirty')) {
			return;
		}

		var filter = logsNav.data('filter');
		var page   = logsNav.data('page');

		var postData = [];
		if (filter && filter != 'all') {
			postData.push({name: 'filter', value: filter});
		}
		if (page && page != 1) {
			postData.push({name: 'page', value: page});
			postData.push({name: 'up_to_page', value: 1});
		}

		$.ajax({
			url: BASE_URL + 'agent/tickets/'+self.meta.ticket_id+'/load-logs',
			data: postData,
			success: function(html) {
				logsWrap.html(html);
				self.updateUi();
			}
		});
	},

	showLockAlert: function() {
		DeskPRO_Window.showAlert('You are not allowed to make any changes to this ticket until it has been unlocked.');
	},

	handleReplySave: function(ev, formData, handler) {

		if (this.pauseSend) {
			window.setTimeout((function() {
				this.handleReplySave(ev, formData, handler);
			}).bind(this), 250);
		}

		if (this.replySaveAjax) {
			return;
		}

		var self = this;
		var closetabTimeoutHit = false;
		var ajaxHit = false;
		var hitRun = false;
		var reply_form = handler.el;

		formData.push({
			name: 'client_messages_since',
			value: DeskPRO_Window.getLastClientMessageId()
		});

		formData.push({
			name: 'last_message_id',
			value: this.getLastMessageId()
		});
		formData.push({
			name: 'last_log_id',
			value: this.getEl('messages_wrap').find('.log-row').last().data('log-id')
		});

		if (this.getReplyTextArea()) {
			this.getReplyTextArea().data('disable-autosave', true);
			if (this.getReplyTextArea().data('autosave-running')) {
				this.getReplyTextArea().data('autosave-running').abort();
				this.getReplyTextArea().data('autosave-running', null);
			}
		}

		var form = this.getEl('replybox_wrap').find('.ticket-reply-form');
		var keepOpen = true;
		if (this.getEl('replybox_wrap').find('[name="options[close_tab]"]').prop('checked')) {
			keepOpen = false;
		}

		var loadingEl = this.getEl('replybox_wrap').find('.ticket-sending-overlay');
		loadingEl.fadeIn();

		this.getEl('replybox_wrap').find('textarea.touched').removeClass('touched');

		DeskPRO_Window.getMessageChanneler().poller.pause();
		function hitDone() {
			hitRun = true;
			DeskPRO_Window.getMessageChanneler().poller.unpause();

			if (!keepOpen) {
				self.closeSelf();

				if (self.getMetaData('goNextOnReply')) {
					var listPage = DeskPRO_Window.getListPage();
					if (listPage && listPage.wrapper) {
						console.log(listPage.wrapper);
						var ticketListEl = listPage.wrapper.find('article.row-item.ticket-' + self.getMetaData('ticket_id'));
						if (ticketListEl.length) {
							var next = ticketListEl.next('article.row-item');
							console.log(ticketListEl);
							console.log(next);
							if (next.length) {
								DeskPRO_Window.runPageRouteFromElement(next);
							}
						}
					}
				}

				return;
			}

			var result = ajaxHit;

			// If the agent cant see the ticket anymore, they dont have permission to
			// view it anymore.
			if (!result.can_view) {
				self.closeSelf();
				return;
			}

			if (result.refresh_tab) {
				// Reload the ticket page
				DeskPRO_Window.loadPage(BASE_URL + 'agent/tickets/' + self.getMetaData('ticket_id'), {ignoreExist:true});
				self.closeSelf();
			}

			loadingEl.hide();

			if (result.error && result.error == 'no_message') {
				DeskPRO_Window.showAlert("Please enter a message");
				return;
			}

			if (result.dupe_message) {
				DeskPRO_Window.showAlert("You have already sent that message.");
				self.loadMessagePage(0, true);
				return;
			}

			self.handleTicketUpdate(result);

			// Reload the message row in results
			//addTicket
			if (DeskPRO_Window.sections.tickets_section && DeskPRO_Window.sections.tickets_section.listPage) {
				var row = DeskPRO_Window.sections.tickets_section.listPage.wrapper.find('article.ticket-' + self.meta.ticket_id);
				if (row[0] && !row.hasClass('removing')) {
					DeskPRO_Window.sections.tickets_section.listPage.addTicket(self.meta.ticket_id, true);
				}
			}
		};

		this.clearAlerts();

		this.replySaveAjax = $.ajax({
			url: reply_form.attr('action'),
			type: 'POST',
			dataType: 'json',
			data: formData,
			context: this,
			noErrorOverride: true,
			complete: function() {
				this.replySaveAjax = null;
				DeskPRO_Window.getMessageChanneler().poller.unpause();

				this.getReplyTextArea().data('disable-autosave', false);
			},
			error: function(event, xhr, ajaxOptions, errorThrown, force) {
				DeskPRO_Window.getMessageChanneler().poller.unpause();
				var loadingEl = this.getEl('replybox_wrap').find('.ticket-sending-overlay');
				loadingEl.hide();

				DeskPRO_Window._globalHandleAjaxError(event, xhr, ajaxOptions, errorThrown, force);
			},
			success: function(result) {

				// Always perform CM processing right now
				DeskPRO_Window.getMessageChanneler().poller.unpause();

				if (result.client_messages) {
					DeskPRO_Window.getMessageChanneler().handleMessageAjax(result.client_messages);

					// null out so handleTicketUpdate called in hitDone doesnt re-process them
					result.client_messages = null;
				}

				if (result.error_messages) {

					var prop = self.changeManager.getPropertyManager('status');
					self.changeManager.setInstantChange(prop, 'awaiting_agent');

					var list = self.getEl('field_errors').find('ul').empty();
					Array.each(result.error_messages, function(msg) {
						var li = $('<li/>');
						li.text(msg);
						li.appendTo(list);
					});

					self.getEl('field_errors').show().addClass('on');

					self.getEl('field_edit_start').click();
					self.getEl('field_edit_cancel').show();
					self.getEl('field_edit_save').show();
					self.getEl('field_edit_controls').removeClass('loading');

					DeskPRO_Window.showAlert('Your reply was saved but the status was not set to resolved because of form errors. You should correct these errors and then you may set the status to resolved.');
					keepOpen = true;
				}

				if (result.notified_agents && DeskPRO.Agent.Widget.AgentChatWin_Registry) {
					Array.each(result.notified_agents, function(aid) {
						aid = parseInt(aid);
						Object.each(DeskPRO.Agent.Widget.AgentChatWin_Registry, function(v,k) {
							if (v.agentIds.length == 1 && v.agentIds.indexOf(aid) !== -1) {
								v.loadLastConvo();
							}
						});
					});
				}

				ajaxHit = result;
				hitDone();
			}
		});
	},

	getLastMessageId: function() {
		var id = this.getEl('messages_wrap').find('article.message').last().data('message-id');
		return id || 0;
	},

	getLastLogId: function() {
		return parseInt($('.log-row', this.getEl('messages_wrap')).last().data('log-id') || 0);
	},

	destroyPage: function() {
		if (this.ticketReplyBox) {
			this.ticketReplyBox.destroy();
			this.ticketReplyBox = null;
		}
		DeskPRO_Window.getMessageBroker().sendMessage('ui.ticket.closed', { ticketId: this.getMetaData('ticket_id') });
	},

	handleTicketUpdate: function(data) {
		var self = this;
		if (data.client_messages) {
			DeskPRO_Window.getMessageChanneler().handleMessageAjax(data.client_messages);
		}

		if (data.active_drafts) {
			this.wrapper.find('.agent-draft-message').remove();
			if (data.active_drafts.length) {
				var insertPos = this.wrapper.find('.ticket-messages .messages-wrap');
				for (var i = 0; i < data.active_drafts.length; i++) {
					if (this.meta.ticket_reverse_order) {
						insertPos.prepend(data.active_drafts[i]);
					} else {
						insertPos.append(data.active_drafts[i]);
					}
				}
			}
		}

		// Might be unloaded by the time this callback is called
		if (!this.changeManager) {
			return;
		}

		var new_messages = null;
		if (data.ticket_messages_block) {
			new_messages = $(data.ticket_messages_block);

			var any = false;
			if (new_messages.hasClass('message')) {
				if (!this.getEl('messages_wrap').find('.message-' + new_messages.data('message-id'))[0]) {
					any = true;
				}
			} else {
				new_messages.find('.message').each(function() {
					if (self.getEl('messages_wrap').find('.message-' + $(this).data('message-id'))[0]) {
						$(this).hide();
					} else {
						any = true;
					}
				});
			}

			if (any) {
				this.wrapper.find('.agent-draft-message').remove();
				self.loadMessagePage(0, true);
			}
		}

		if (data.updated_agent_parts_html) {
			this.getEl('agent_part_list').html(data.updated_agent_parts_html);
			$('.agent-part-count', this.wrapper).text(data.updated_agent_parts_count);
		}

		if (data.replybox_html) {
			// Only refresh the box if we've not begun writing a message
			if (!this.getEl('replybox_wrap').find('textarea.touched')[0]) {
				var textarea = this.getReplyTextArea();
				if (textarea.data('redactor')) {
					textarea.destroyEditor();
				}
				this.getEl('replybox_wrap').empty().append(data.replybox_html);
				DeskPRO_Window.initInterfaceServices(this.getEl('replybox_wrap'));
				$('form.ticket-reply-form', this.getEl('replybox_wrap')).bind('replyboxsubmit', this.handleReplySave.bind(this));
			}
		}

		if (typeof data.cc_list == "string") {
			this.wrapper.find('ul.cc-row-list').empty().html(data.cc_list);
		}

		var billing = this.billing;
		if (billing.hasBilling) {
			if (data.charge_html) {
				billing.addBillingRow(data.charge_html);
				billing.updateBillingForm(true);
				billing.resetBillingForm();
			} else {
				billing.updateBillingForm(false);
				billing.resetBillingForm();
			}
		}

		if (data.locked_by_agent_id && data.locked_by_agent_id != DESKPRO_PERSON_ID) {
			// Reload the ticket page
			DeskPRO_Window.loadPage(BASE_URL + 'agent/tickets/' + self.getMetaData('ticket_id'), {ignoreExist:true});
			self.closeSelf();
			return;
		} else {
			this.wrapper.find('.lock-overlay').remove();
			this.getEl('locked_message').hide();
			this.getEl('locked_message').data('locked-self', false);
			this.getEl('lock_ticket').show();
			this.getEl('unlock_ticket').hide();
		}

		var props = ['status', 'department_id', 'category_id', 'product_id', 'workflow_id', 'priority_id', 'urgency', 'is_hold'];
		if (data.via_reply) {
			if (data.changed_agent) {
				props.push('agent_id');
			}
			if (data.changed_team) {
				props.push('agent_team_id');
			}
		} else {
			props.push('agent_id');
			props.push('agent_team_id');
		}

		Array.each(props, function(propId) {
			var val = '0';
			if (data[propId]) {
				val = data[propId];
			}

			var prop = this.changeManager.getPropertyManager(propId);
			prop.setIncomingValue(val);
		}, this);

		if (data.dupe_message) {
			// If its a dupe then it'd already be added ot the message list,
			// we can just clear out the message box
			var sig = this.getEl('replybox_wrap').find('textarea.signature-value').val();
			if (sig) sig = "\n\n" + sig;

			var textarea = this.getReplyTextArea();
			if (textarea.data('redactor')) {
				textarea.setCode(DP.convertTextToWysiwygHtml(sig, true));
			} else {
				textarea.val(sig);
			}
			return;
		}

		this.getEl('messagebox_tabs').find('.logs').addClass('dirty');
		this.refreshLogTypes();
	},

	updateUi: function(toReplyHeight) {
		var x;
		if (!this.IS_ACTIVE) {
			return;
		}
		if (this.wrapper) {
			if (!this.scrollHandlers) {
				this.scrollHandlers = this.wrapper.find('div.with-scroll-handler');
			}
			for (x = 0; x < this.scrollHandlers.length; x++) {
				var sh = $(this.scrollHandlers[x]).data('scroll_handler');
				if (sh && sh.updateSize) {
					sh.updateSize();
				}
			};

			if (this.doScrollBottom) {
				this.wrapper.find('div.layout-content').trigger('goscrollbottom_stick');
				this.doScrollBottom = false;
			} else if (toReplyHeight) {
				var oWrap = this.wrapper.find('div.layout-content').first();
				oWrap.trigger('goscrollto', toReplyHeight+10);
			}

			this.getEl('labels_wrap').find('.select2-input').width('95%');
		}
	},

	_initMessage: function(messageEl) {
		var self = this;
		var imageEls = $('ul.attachment-list li.is-image a, a.dp-is-image', messageEl);

		DeskPRO_Window.initStickyTips(messageEl);

		$('.timeago', messageEl).timeago();

		imageEls.colorbox({
			title: function(){
				if ($(this).data('deskpro-url')) {
					var url = $(this).data('deskpro-url');
				} else {
					var url = $(this).attr('href');
				}
				var dl_url = Orb.appendQueryData(url, 'dl', '1');
				return '<a href="'+url+'" target="_blank">Open In New Window</a> | <a href="'+dl_url+'" target="_blank">Download</a>'
			},
			onComplete: function() {
				var image = $('#cboxLoadedContent img');
				if (image.length) {
					$('#cboxLoadedContent').append(
						$('<a />').attr('href', $(this).attr('href')).attr('target', '_blank').append(image)
					);
				}
			},
			width: '50%',
			height: '50%',
			initialWidth: '200',
			initialHeight: '150',
			scalePhotos: true,
			photo: true,
			opacity: 0.5,
			transition: 'none'
		});

		var lastCount = 0;
		if (this.lastMessageCount) {
			lastCount = this.lastMessageCount;
		}

		if (messageEl.hasClass('messages-wrap')) {
			var articles = messageEl.find('article.content-message');
		} else {
			var articles = messageEl.filter('article.content-message');
		}
		articles.each(function() {
			var article = $(this);

			if (article.is('.agent-draft-message')) {
				return;
			}

			var fullEl = article.find('.body-text-full-message');
			if (fullEl[0]) {
				var simpleEl = article.find('.body-text-message');
				fullEl.find('.message-toggle-btn > em').on('click', function(ev) {
					ev.preventDefault();
					fullEl.hide();
					simpleEl.show();
					self._initTicketMessageClipped(article);
					self.updateUi();
				});
				simpleEl.find('.message-toggle-btn > em').on('click', function(ev) {
					ev.preventDefault();
					fullEl.show();
					simpleEl.hide();
					self.updateUi();

					if (!fullEl.hasClass('loaded')) {
						var row = $(this).closest('article.content-message');
						var message_id = row.data('message-id');

						$.ajax({
							url: BASE_URL + 'agent/tickets/messages/'+message_id+'/get-full-message.json',
							type: 'GET',
							success: function(data) {
								row.find('.full-message-content').html(data.message_full)
								self._initTicketMessageClipped(article);
								self.updateUi();
							}
						});
					}
				});
			}

			self._initTicketMessageClipped(article);

			var trans = article.find('.message-translate-controls');
			if (trans[0]) {
				var transShow     = article.find('.body-message-translated');
				var existTo       = transShow.data('to-lang-code');
				var existFrom     = transShow.data('from-lang-code');
				var transMenu     = trans.find('.dp-lang-choose');
				var transMenuBack = null;
				var transFromEl   = trans.find('.translate-from-lang');
				var transToEl     = trans.find('.translate-to-lang');

				trans.on('click', '.translate-controls-off', function(ev) {
					Orb.cancelEvent(ev);
					trans.addClass('on');
				});

				trans.find('.dp-dropdown-toggle').on('click', function(ev) {
					Orb.cancelEvent(ev);

					if (!transMenu.hasClass('has-init')) {
						transMenu.detach().appendTo('body');

						transMenu.addClass('has-init');
						transMenuBack = $('<div/>').addClass('dp-popover-backdrop').hide().appendTo('body');

						self.addEvent('destroy', function() {
							transMenu.detach();
							transMenuBack.detach();
						});

						transMenuBack.on('click', function(ev) {
							Orb.cancelEvent(ev);
							transMenu.hide();
							transMenuBack.hide();
						});

						transMenu.find('select').on('change', function(){
							var locale = $(this).val();
							var title = $(this).find(':selected').text();

							if ($(this).attr('name') == 'from') {
								transFromEl.data('locale', locale).text(title);
							} else {
								transToEl.data('locale', locale).text(title);
							}
						});
					}

					transMenu.css({top:0, left:0}).position({
						of: $(this),
						my: 'right top',
						at: 'right bottom',
						collision: 'flipfit'
					}).show();
					transMenuBack.show();
				});

				trans.find('.trans-trigger').on('click', function(ev) {
					Orb.cancelEvent(ev);
					self.refreshMessageTranslation(article);
				});
			}
		});
		this.lastMessageCount = lastCount;

		messageEl.find('img').bind('load', function() {
			var article = $(this).closest('article.content-message');
			self._initTicketMessageClipped(article);
		});

		var wr = this.getEl('messages_wrap');
		wr.find('.message-id-txt').each(function() {
			var findclass = '.message-counter-' + $(this).data('message-id');
			var counterText = wr.find(findclass).text().trim();
			if (counterText.length) {
				$(this).attr('title', $(this).text()).text(counterText).removeClass('message-id-txt');
			}
		});
	},

	refreshMessageTranslation: function(messageEl) {
		var trans     = messageEl.find('.message-translate-controls');
		var transShow = messageEl.find('.body-message-translated');

		var selFrom = trans.find('.translate-from-lang');
		var selTo   = trans.find('.translate-to-lang');

		var formData = {
			message_id: messageEl.data('message-id'),
			from: selFrom.data('locale'),
			to: selTo.data('locale')
		};

		selFrom.parent().find('em').text(selFrom.find(':selected').text());
		selTo.parent().find('em').text(selTo.find(':selected').text());

		trans.addClass('dp-loading-on');
		$.ajax({
			url: window.DESKPRO_TRANSLATE_SERVICE.translate_ticket_message_url,
			data: formData,
			type: 'POST',
			dataType: 'json',
			complete: function() {
				trans.removeClass('dp-loading-on');
			},
			success: function(data) {
				if (data.error_code) {
					DeskPRO_Window.showAlert("Could not translate message: " + data.message);
					return;
				}

				trans.removeClass('on');

				transShow.data('to-lang-code', data.to_lang_code);
				transShow.data('from-lang-code', data.from_lang_code);
				transShow.empty().html(data.message);
				transShow.show();

				transShow.parent().addClass('with-translated');
			}
		});
	},

	_initTicketMessageClipped: function(article) {
		var h = article.find('div.body-text').height();
		if (h >= 600) {
			if (!article.hasClass('with-clipped-body')) {
				article.addClass('with-clipped-body');
				article.find('.fade-bar-longmsg').one('click', function(ev) {
					ev.stopPropagation();
					article.addClass('clipped-show');
				});
			}
		} else {
			if (article.hasClass('with-clipped-body')) {
				article.removeClass('with-clipped-body')
			}
		}
	},

	incCount: function(id) {
		var countEl = $('.'+id+'-count', this.wrapper);
		var count = countEl.data('count') + 1;
		countEl.data('count', count).html('(' + count + ')');
	},

	setCount: function(id, count) {
		var countEl = $('.'+id+'-count', this.wrapper);
		countEl.data('count', count).html('(' + count + ')');
	},

	appendToMessage: function(content, is_html) {

		if (is_html) {
			var textarea = this.getReplyTextArea();
			if (textarea.data('redactor')) {
				try {
					textarea.data('redactor').restoreSelection();
					textarea.data('redactor').setBuffer();
				} catch (e) {}

				var html = content;
				html = html.replace(/<\/p>\s*<p>/g, '<br/>');
				html = html.replace(/^<p>/, '');
				html = html.replace(/<\/p>$/, '');
				textarea.data('redactor').insertHtml(html);
			}
		} else {
			this.insertTextInReply(content);

			// Scroll down
			if (!this.meta.ticket_reverse_order) {
				this.wrapper.find('div.layout-content').trigger('goscrollbottom_stick');
			}

			this.focusOnReply();

			// Resize it by firing change which'll run the resize
			this.getReplyTextArea().trigger('textareaexpander_fire');
		}
	},

	addAttachToList: function(attachInfo) {
		var row = $('.template-download', this.getEl('replybox')).tmpl(attachInfo);
		$('.file-list', this.getEl('replybox')).append(row);
		this.updateUi();
	},

	//#################################################################
	//# Property managers
	//#################################################################

	getPropertyManager: function(type, type_id) {

		DP.console.error('Depreciated');
		return this.changeManager.getPropertyManager(type, type_id);
	},

	//#################################################################
	//# Labels
	//#################################################################

	_initLabels: function() {
		if (this.getEl('labels_input')[0]) {
			this.labelsInput = new DeskPRO.UI.LabelsInput({
				type: 'tickets',
				input: this.getEl('labels_input'),
				onChange: this.saveLabels.bind(this)
			});
			this.ownObject(this.labelsInput);
		}
	},

	saveLabels: function() {
		if (this.changeManager.hasChanges()) {
			// If change manager has changes, we dont save new/removed tags
			return;
		}

		if (this._saveLabelsTimeout) {
			window.clearTimeout(this._saveLabelsTimeout);
		}

		this._labelsData = this.labelsInput.getFormData();
		this._saveLabelsTimeout = this._doSaveLabels.delay(2000, this);
	},

	_doSaveLabels: function() {
		var data = this._labelsData;

		$.ajax({
			url: this.getMetaData('labelsSaveUrl'),
			type: 'POST',
			context: this,
			data: data,
			dataType: 'json'
		});
	},

	//#################################################################
	//# Ticket actions menu
	//#################################################################

	_initTicketLocking: function() {
		var self = this;
		$([this.getEl('unlock_ticket').get(0), this.getEl('unlock_ticket2').get(0)]).on('click', function() {
			self.wrapper.find('.hide-locked').removeClass('hide-locked');
			self.wrapper.find('.lock-overlay').remove();
			self.getEl('locked_message').hide();
			self.getEl('locked_message').data('locked-self', false);
			self.getEl('lock_ticket').show();
			self.getEl('unlock_ticket').hide();
			$.ajax({
				url: BASE_URL + 'agent/tickets/' + self.meta.ticket_id + '/unlock-ticket.json',
				type: 'POST',
				dataType: 'json',
				complete: function() {

				}
			});
		});

		this.getEl('lock_ticket').on('click', function() {
			self.wrapper.find('.lock-overlay').remove();
			self.getEl('locked_message').data('locked-self', true);
			self.getEl('locked_message').show();
			self.getEl('locked_message_self').show();
			self.getEl('locked_message_other').hide();
			self.getEl('lock_ticket').hide();
			self.getEl('unlock_ticket').show();

			$.ajax({
				url: BASE_URL + 'agent/tickets/' + self.meta.ticket_id + '/lock-ticket.json',
				type: 'POST',
				dataType: 'json',
				success: function(data) {
					if (data.error) {
						DeskPRO_Window.showAlert('Someone else has already locked the ticket');
						self.getEl('locked_message').hide();
						self.getEl('lock_ticket').show();

						// Reload the ticket page
						DeskPRO_Window.loadPage(BASE_URL + 'agent/tickets/' + self.getMetaData('ticket_id'), {ignoreExist:true});
						self.closeSelf();
					}
				}
			});
		});
	},

	_initTicketActionsMenu: function() {
		var self = this;

		var removeMenu = new DeskPRO.UI.Menu({
			triggerElement: this.getEl('remove_menu_trigger'),
			menuElement: this.getEl('remove_menu'),
			onItemClicked: function(info) {
				var it = $(info.itemEl);
				var doBan = false;
				if (it.data('action').indexOf('.ban') !== -1) {
					doBan = true;
				}

				switch (it.data('action')) {
					case 'spam':
					case 'spam.ban':
						self.doTicketSpam(doBan);
						break;

					case 'delete':
					case 'delete.ban':
						self.showDeleteOverlay(doBan);
						break;
				}
			}
		});

		var actionsMenu = new DeskPRO.UI.Menu({
			triggerElement: this.getEl('actions_menu_trigger'),
			menuElement: this.getEl('actions_menu'),
			onBeforeMenuOpened: function(info) {
				var status = self.getEl('status_code').val();
				if (status == 'awaiting_agent') {
					if (self.getEl('value_form').find('.is_hold').val()) {
						self.getEl('menu_unset_hold').show();
					} else {
						self.getEl('menu_set_hold').show();
					}
				} else {
					self.getEl('menu_set_hold').hide();
					self.getEl('menu_unset_hold').hide();
				}
			},
			onItemClicked: function(info) {

				var it = $(info.itemEl);

				switch(it.data('action')) {
					case 'change-user':
					var changeUserOverlay = new DeskPRO.Agent.Widget.TicketChangeUser({
							ticketId: self.getMetaData('ticket_id'),
							destroyOnClose: true,
							onSuccess: function(data) {
								self.closeSelf();
								DeskPRO_Window.runPageRoute('ticket:' + BASE_URL + 'agent/tickets/' + data.ticket_id);
								changeUserOverlay.close();
							}
						});
						changeUserOverlay.open();
						break;

					case 'split':
						self.showSplitOverlay('');
						break;

					case 'print':
						window.print();
						break;

					case 'set-hold':
						self.setHold(true);
						break;

					case 'unset-hold':
						self.setHold(false);
						break;

					case 'linked_ticket':
						DeskPRO_Window.newTicketLoader.newLinkedTicket(self.meta.ticket_id);
						break;

					case 'kb-pending':
						if (!self.pendingKbOverlay) {
							var el = self.getEl('pending_add');
							self.pendingKbOverlay = new DeskPRO.UI.Overlay({
								contentElement: self.getEl('pending_add')
							});

							el.find('.save-new-trigger').on('click', function(ev) {
								ev.preventDefault();
								var formData = el.find('input, textarea').serializeArray();

								el.addClass('loading');
								$.ajax({
									url: el.data('save-url'),
									type: 'POST',
									data: formData,
									dataType: 'json',
									complete: function() {
										el.removeClass('loading');
									},
									success: function() {
										self.pendingKbOverlay.close();
										el.find('textarea').val('');
									}
								});
							});
						}
						self.pendingKbOverlay.open();
						break;
				}
			}
		});

		this.getEl('unhold_btn').on('click', function(ev) {
			ev.preventDefault();
			self.setHold(false);
		});
	},

	setHold: function(val) {
		var prop = this.changeManager.getPropertyManager('is_hold');
		this.changeManager.setInstantChange(prop, val ? 1 : 0);
	},

	_initDeleteOverlay: function() {

		if (this.deleteOverlay) return;

		this.deleteOverlayEl = $('.delete-ticket-overlay:first', this.wrapper);
		this.deleteOverlay = new DeskPRO.UI.Overlay({
			contentElement: this.deleteOverlayEl
		});
		this.ownObject(this.deleteOverlay);

		$('.save-trigger', this.deleteOverlayEl).on('click', (function() {
			this.doTicketDelete();
		}).bind(this));
	},

	showDeleteOverlay: function(doBan) {
		this._initDeleteOverlay();
		this.deleteOverlay.doBan = doBan;

		if (doBan) {
			this.getEl('delete_user_list').show();
		} else {
			this.getEl('delete_user_list').hide();
		}

		this.deleteOverlay.openOverlay();
	},

	doTicketDelete: function() {

		$('.loading-off', this.deleteOverlayEl).hide();
		$('.loading-on', this.deleteOverlayEl).show();

		var data = [];
		data.push({
			name: 'reason',
			value: $('.delete-reason', this.deleteOverlayEl).val()
		});

		if (this.deleteOverlay.doBan) {
			data.push({
				name: 'ban',
				value: 1
			})
		}

		var self = this;

		$.ajax({
			url: BASE_URL + 'agent/tickets/' + this.getMetaData('ticket_id') + '/delete',
			type: 'POST',
			data: data,
			dataType: 'json',
			success: function(data) {
				self.deleteOverlay.closeOverlay();
				self.getEl('remove_menu_trigger').hide();

				if (data.hidden_html) {
					self.getEl('page_header').before($(data.hidden_html));
				} else {
					DeskPRO_Window.removePage(self);
					DeskPRO_Window.loadPage(BASE_URL + 'agent/tickets/' + self.getMetaData('ticket_id'), {ignoreExist:true});
				}
			}
		});
	},

	doTicketSpam: function(doBan) {
		var self = this;

		this.getEl('actions_loading').show();

		$.ajax({
			url: BASE_URL + 'agent/tickets/' + this.getMetaData('ticket_id') + '/spam',
			type: 'POST',
			dataType: 'json',
			data: {
				ban: doBan ? 1 : 0
			},
			success: function(data) {
				self.getEl('actions_loading').hide();
				self.getEl('remove_menu_trigger').hide();

				if (data.hidden_html) {
					self.getEl('page_header').before($(data.hidden_html));
				}

				DeskPRO_Window.removePage(self);
			}
		});
	},

	doTicketUndelete: function() {
		var self = this;
		var prop = this.changeManager.getPropertyManager('status');
		this.changeManager.setInstantChange(prop, 'awaiting_agent', function() {
			DeskPRO_Window.removePage(self);
		});
	},

	doTicketUnspam: function() {
		var self = this;
		var prop = this.changeManager.getPropertyManager('status');
		this.changeManager.setInstantChange(prop, 'awaiting_agent', function() {
			DeskPRO_Window.removePage(self);

			// Reload the ticket page
			DeskPRO_Window.loadPage(BASE_URL + 'agent/tickets/' + self.getMetaData('ticket_id'), {ignoreExist:true});
		});
	},

	_initMessageActionsMenu: function() {
		var self = this;
		var menuElement = $('.ticket-message-edit-menu', this.wrapper);
		this.messageActionsMenu = new DeskPRO.UI.Menu({
			triggerElement: null,
			menuElement: menuElement,
			onBeforeMenuOpened: function(info) {
				var message = $(info.menu.getOpenTriggerElement()).closest('article.message');
				if (message.hasClass('note-message')) {
					menuElement.find('li.set-as-message').show();
					menuElement.find('li.set-as-note').hide();
				} else {
					menuElement.find('li.set-as-message').hide();
					menuElement.find('li.set-as-note').show();
				}

				if (message.hasClass('with-attach')) {
					menuElement.find('li.delete-attachments-link').show();
				} else {
					menuElement.find('li.delete-attachments-link').hide();
				}
			},
			onItemClicked: function(info) {
				var itemEl = $(info.itemEl);
				var triggerEl = $(info.menu.getOpenTriggerElement());
				if (!triggerEl.hasClass('ticket-message-edit-btn')) {
					triggerEl = triggerEl.closest('.ticket-message-edit-btn');
				}
				self._doMessageAction(itemEl.data('option-id'), triggerEl.data('message-id'), itemEl);
			}
		});
		this.ownObject(this.messageActionsMenu);

		// We're using a live event because new messages are always
		// added. So we take care of opening the menu manually.
		var menu = this.messageActionsMenu;
		var wrap = $('.messages-wrap', this.wrapper)[0];
		$('.ticket-message-edit-btn', wrap).live('mousedown', function(event) {
			var textarea = self.getReplyTextArea();
			if (textarea.data('redactor')) {
				// save the selection - but don't focus the editor as that can break this
				$.proxy(function() {
					this.savedSel = this.getOrigin();
					this.savedSelObj = this.getFocus();
				}, textarea.data('redactor'))();
			}
		});
		$('.ticket-message-edit-btn', wrap).live('click', function(event) {
			menu.openMenu(event);
		});
	},

	_doMessageAction: function(optionId, messageId, itemEl) {
		if (!messageId) {
			return;
		}

		switch (optionId) {
			case 'quote':
				var quote = $('textarea.message-quote-' + messageId, this.wrapper).val();
				if (!quote) {
					quote = '';
				}

				var textarea = this.getReplyTextArea();
				if (textarea.data('redactor')) {
					textarea.data('redactor').restoreSelection();
				}

				this.insertTextInReply(quote.trim() + "\n");

				// Scroll down
				if (!this.meta.ticket_reverse_order) {
					this.wrapper.find('div.layout-content').trigger('goscrollbottom');
				}

				this.focusOnReply();

				break;

			case 'delete':
				this.showDeleteMessageOverlay(messageId);
				break;

			case 'delete-attachments':
				var self = this;
				var overlay = new DeskPRO.UI.Overlay({
					contentMethod: 'ajax',
					contentAjax: { url: BASE_URL + 'agent/tickets/messages/' + messageId + '/attachments' },
					zIndex: 40000, // Above floating people windows
					onAjaxDone: function() {
						var wrapper = overlay.getWrapper();

						wrapper.on('click', '.delete-trigger', function(e) {
							e.preventDefault();

							var $this = $(this), attachmentId = $this.data('attachment-id');
							var container = $this.closest('.overlay-content');
							var row = $this.closest('.attachment-row');

							if (!confirm(container.data('confirm'))) {
								return;
							}

							row.addClass('loading');

							$.ajax({
								url: BASE_URL + 'agent/tickets/messages/' + messageId + '/attachments/' + attachmentId + '/delete',
								type: 'POST',
								dataType: 'json'
							}).always(function() {
								row.removeClass('loading');
							}).done(function(data) {
								if (data.message_html) {
									self.wrapper.find('article.message-' + messageId).replaceWith(data.message_html);
								}

								row.remove();
								if (!container.find('.attachment-row').length) {
									overlay.close();
								}
							});
						});
					}
				});
				overlay.open();

				break;

			case 'setnote.note':
			case 'setnote.message':

				var is_note = optionId == 'setnote.note' ? '1' : '0';
				var row = this.wrapper.find('article.message-' + messageId);
				row.addClass('gear-loading');

				$.ajax({
					url: BASE_URL + 'agent/tickets/messages/'+messageId+'/set-message-note.json',
					data: {
						is_note: is_note
					},
					complete: function() {
						row.removeClass('gear-loading');
					},
					success: function(info) {
						if (info.is_note) {
							row.addClass('note-message');
						} else {
							row.removeClass('note-message');
						}
					}
				});

				break;

			case 'split':
				this.showSplitOverlay(messageId);
				break;

			case 'linked_ticket':
				DeskPRO_Window.newTicketLoader.newLinkedTicket(this.meta.ticket_id, messageId);
				break;

			case 'fwd':
				this.showFwdOverlay(messageId);
				break;

			case 'edit':
				this.showMessageEditor(messageId);
				break;

			case 'window':
				var url    = itemEl.data('url');
				url = url.replace(/00000/g, this.meta.ticket_id);
				url = url.replace(/11111/g, messageId);
				var width  = 780;
				var height = 600;

				window.open(url, 'msgwin', "status=0,toolbar=0,location=0,menubar=0,directories=0,resizable=1,scrollbars=1,height="+height+",width="+width);
				break;

			case 'debug':
				var url = itemEl.data('url');
				url = url.replace(/00000/g, this.meta.ticket_id);
				var width  = 200;
				var height = 200;

				window.open(url, 'debugwin', "status=0,toolbar=0,location=0,menubar=0,directories=0,resizable=1,scrollbars=1,height="+height+",width="+width);
				break;
		}
	},

	_initDeleteMessageOverlay: function() {
		if (this.deleteMessageOverlay) return;

		this.deleteMessageOverlayEl = $('.delete-message-overlay:first', this.wrapper);
		this.deleteMessageOverlay = new DeskPRO.UI.Overlay({
			contentElement: this.deleteMessageOverlayEl
		});
		this.ownObject(this.deleteMessageOverlay);

		$('.save-trigger', this.deleteMessageOverlayEl).on('click', (function() {
			this.doTicketMessageDelete();
		}).bind(this));
	},

	showDeleteMessageOverlay: function(messageId) {
		this._initDeleteMessageOverlay();
		$('.message-id', this.deleteMessageOverlayEl).val(messageId);

		this.deleteMessageOverlayEl.find('.ticket-messages').empty().html(
			this.wrapper.find('article.message-' + messageId).clone()
		);
		this.deleteMessageOverlayEl.find('.ticket-messages .edit-gear').remove();
		this.deleteMessageOverlay.openOverlay();
	},

	doTicketMessageDelete: function() {
		$('.loading-off', this.deleteMessageOverlayEl).hide();
		$('.loading-on', this.deleteMessageOverlayEl).show();

		var messageId = $('.message-id', this.deleteMessageOverlayEl).val();
		var self = this;

		$.ajax({
			url: BASE_URL + 'agent/tickets/messages/' + messageId + '/delete',
			type: 'POST',
			dataType: 'json',
			success: function(data) {
				self.deleteMessageOverlay.closeOverlay();

				if (data.ticket_deleted) {
					self.getEl('remove_menu_trigger').hide();

					if (data.hidden_html) {
						var html = $(data.hidden_html);
						self.getEl('page_header').before(html);
						html.closest('.with-scrollbar').trigger('goscrolltop');
					} else {
						DeskPRO_Window.removePage(self);
						DeskPRO_Window.loadPage(BASE_URL + 'agent/tickets/' + self.getMetaData('ticket_id'), {ignoreExist:true});
					}
				} else {
					self.wrapper.find('article.message-' + messageId).remove();
				}
			}
		}).always(function() {
			$('.loading-off', this.deleteMessageOverlayEl).show();
			$('.loading-on', this.deleteMessageOverlayEl).hide();
		})
	},

	showSplitOverlay: function(messageId) {
		var self = this;
		var overlay = new DeskPRO.UI.Overlay({
			contentMethod: 'ajax',
			contentAjax: { url: BASE_URL + 'agent/tickets/' + this.meta.ticket_id + '/split/' + messageId },
			zIndex: 40000, // Above floating people windows
			onAjaxDone: function() {
				var wrapper = overlay.getWrapper(),
					form = wrapper.find('form');

				wrapper.on('click', '.body-text', function() {
					var $this = $(this);
					if ($this.find('.fade-bar').is(':visible')) {
						$this.find('.fade-bar').hide();
						$this.find('.body-text-message').css('max-height', '');
					} else {
						$this.find('.fade-bar').show();
						$this.find('.body-text-message').css('max-height', '70px');
					}
				});

				wrapper.on('change', '.message-id-checkbox', function() {
					var $this = $(this), container = $this.closest('.content-message');
					if ($this.is(':checked')) {
						container.removeClass('message-unselected');
					} else {
						container.addClass('message-unselected');
					}
				});
				wrapper.on('click', '.content header', function(e) {
					if ($(e.target).is('.message-id-checkbox')) {
						return;
					}

					var cb = $(this).find('.message-id-checkbox');
					cb.attr('checked', !cb.attr('checked'));
					cb.trigger('change');
				});

				form.on('submit', function(e) {
					e.preventDefault();

					form.addClass('loading');

					$.ajax({
						url: form.attr('action'),
						type: 'POST',
						data: form.serializeArray(),
						dataType: 'json'
					}).always(function() {
						form.removeClass('loading');
					}).done(function(data) {
						overlay.close();

						if (data.ticket_id) {
							DeskPRO_Window.removePage(self);
							if (!data.old_ticket_deleted) {
								DeskPRO_Window.loadPage(BASE_URL + 'agent/tickets/' + self.getMetaData('ticket_id'), {ignoreExist:true});
							}

							DeskPRO_Window.runPageRoute('ticket:' + BASE_URL + 'agent/tickets/' + data.ticket_id);
						}
					});
				});
			}
		});
		overlay.open();
	},

	showFwdOverlay: function(messageId) {
		var self = this;
		var overlay = new DeskPRO.UI.Overlay({
			contentMethod: 'ajax',
			contentAjax: { url: BASE_URL + 'agent/tickets/' + this.meta.ticket_id + '/forward/' + messageId },
			zIndex: 40000, // Above floating people windows
			destroyOnClose: true,
			onAjaxDone: function() {
				var wrapper = overlay.getWrapper(),
					form = wrapper.find('form'),
					sendBtn = wrapper.find('.save-trigger'),
					footer = wrapper.find('.overlay-footer');

				form.on('submit', function(ev) {
					ev.preventDefault();
					ev.stopPropagation();
				});

				sendBtn.on('click', function(ev) {
					ev.preventDefault();
					ev.stopPropagation();

					var formData = form.serializeArray();
					footer.addClass('loading');

					$.ajax({
						url: form.attr('action'),
						type: 'POST',
						data: formData,
						dataType: 'json',
						success: function(data) {
							if (data.error && data.error == 'invalid_to') {
								DeskPRO_Window.showAlert('Please enter a valid To address');
								footer.removeClass('loading');
							} else {
								DeskPRO_Window.showAlert('Your message has been sent.');
								overlay.close();
							}
						}
					})
				});
			}
		});
		overlay.open();
	},

	showMessageEditor: function(message_id) {
		var self = this;
		this.currentOpenMessageId = message_id;
		if (!this.messageEditOverlay) {
			var overlayEl = this.getEl('message_edit_overlay');
			overlayEl.find('.save-text-trigger').on('click', function(ev) {
				ev.preventDefault();

				$(this).hide();
				overlayEl.find('.save-text-loading').show();

				if (overlayEl.find('textarea.message_text').data('redactor')) {
					overlayEl.find('textarea.message_text').data('redactor').syncCode();
				}

				var postData = {
					message_html: overlayEl.find('textarea.message_text').val()
				};

				$.ajax({
					url: BASE_URL + 'agent/tickets/messages/'+self.currentOpenMessageId+'/save-message-text.json',
					type: 'POST',
					data: postData,
					dataType: 'json',
					complete: function() {
						overlayEl.find('.save-text-loading').hide();
						overlayEl.find('.save-text-trigger').show();
					},
					success: function(info) {
						self.messageEditOverlay.close();
						var messageHtml = info.message_html;
						self.wrapper.find('article.message-' + self.currentOpenMessageId).find('.body-text-message').html(messageHtml);
					}
				});
			});

			this.messageEditOverlay = new DeskPRO.UI.Overlay({
				contentElement: this.getEl('message_edit_overlay'),
				fullScreen: true,
				fullScreenMargin: 55,
				onOverlayOpened: function() {
					overlayEl.find('input.message_id').val(self.currentOpenMessageId);

					if (!self.messageEditOverlay.hasInitRte) {
						self.messageEditOverlay.hasInitRte = true;
						overlayEl.find('textarea.message_text').height(overlayEl.find('.overlay-content').height() - 50);
						//DP.rteTextarea(overlayEl.find('textarea.message_text'), {});
						DeskPRO_Window.initRteAgentReply(overlayEl.find('textarea.message_text'), {
							autoresize: false
						});
					}

					//overlayEl.find('textarea.message_text').html('Loading...');
					overlayEl.find('textarea.message_text').setCode('Loading...');
					$.ajax({
						url: BASE_URL + 'agent/tickets/messages/'+self.currentOpenMessageId+'/get-message-text.json',
						dataType: 'json',
						success: function(data) {
							//overlayEl.find('textarea.message_text').html(data.message_html);
							overlayEl.find('textarea.message_text').setCode(data.message_html);
						}
					});
				}
			});
		}

		this.messageEditOverlay.open();
	},

	getReplyTextArea: function() {
		return this.getEl('replybox_wrap').find('textarea[name="message"]');
	},

	insertTextInReply: function(text) {
		var txt = this.getReplyTextArea();

		if (txt.data('redactor')) {

			try {
				txt.data('redactor').restoreSelection();
				txt.data('redactor').setBuffer();
			} catch (e) {}

			var html = DP.convertTextToWysiwygHtml(text, true);
			html = html.replace(/<\/p>\s*<p>/g, '<br/>');
			html = html.replace(/^<p>/, '');
			html = html.replace(/<\/p>$/, '')
			txt.data('redactor').insertHtml(html);
		} else {
			var pos = txt.getCaretPosition();
			if (!pos) {
				txt.setCaretPosition(0);
			}

			txt.insertAtCaret(text);
			txt.trigger('textareaexpander_fire');
		}
	},

	focusOnReply: function() {
		var txt = this.getReplyTextArea();

		if (txt.data('redactor')) {
			var first = !txt.hasClass('touched');
			txt.setFocus();

			if (first) {
				var cursor = txt.data('redactor').$editor.find('> *').first();
				txt.data('redactor').setSelection(cursor[0], 0, cursor[0], 0);
			}
		} else {
			txt.focus();
		}
	},


	doTicketUpdate: function() {
		if (this.doTicketUpdateRunning) {
			this.doTicketUpdateRunning.abort();
			this.doTicketUpdateRunning = null;
		}

		var formData = [];
		formData.push({
			name: 'last_message_id',
			value: this.getLastMessageId()
		});
		formData.push({
			name: 'last_log_id',
			value: this.getEl('messages_wrap').find('.log-row').last().data('log-id')
		});

		this.doTicketUpdateRunning = $.ajax({
			url: BASE_URL + 'agent/tickets/' + this.getMetaData('ticket_id') + '/update-views.json',
			type: 'POST',
			dataType: 'json',
			data: formData,
			context: this,
			success: function(result) {
				this.alertTab();
				this.handleTicketUpdate(result);
			}
		});
	},

	//#################################################################
	//# Tasks
	//#################################################################

	_initTasks: function() {
		var self = this;
		var openForEl = null;

		var menuVis2  = this.getEl('task_menu_vis').clone().appendTo(this.wrapper);

		var statusMenu = new DeskPRO.UI.Menu({
			menuElement: this.getEl('task_menu_vis'),
			onItemClicked: function(info) {
				$('input.input-vis', openForEl).val($(info.itemEl).data('vis'));
				$('.opt-trigger.visibility label', openForEl).text($(info.itemEl).text());
				sendUpdate(openForEl, 'visibility', $(info.itemEl).data('vis'));
			}
		});

		var sendUpdate = function(rowEl, prop, val, callback) {
			var taskId = rowEl.data('task-id');

			if (!taskId) {
				return;
			}

			var url = BASE_URL + 'agent/tasks/'+taskId+'/ajax-save';

			var postData = [];
			postData.push({
				name: 'action',
				value: prop
			});
			postData.push({
				name: 'value',
				value: val
			});

			$.ajax({
				url: url,
				type: 'POST',
				data: postData,
				dataType: 'json',
				success: callback || function() {}
			});
		};

		var rowContainer = this.getEl('tasks_wrap');

		var openForEl = null;
		rowContainer.on('click', '.remove-row-trigger', function(ev) {
			var row = $(this).closest('.row-item');
			if (confirm($(this).data('confirm'))) {
				row.slideUp();
				$.ajax({
					url: BASE_URL + 'agent/tasks/' + row.data('task-id') + '/delete',
					error: function() {
						row.show();
					},
					success: function() {
						row.remove();
						if (DeskPRO_Window.sections.tasks_section) {
							DeskPRO_Window.sections.tasks_section.refresh();
						}
					}
				});
			}
		});
		rowContainer.on('click', '.opt-trigger.visibility', function(ev) {
			openForEl = $(this).closest('.row-item');
			statusMenu.open.open(ev);
		});
		rowContainer.find('li.assigned_agent select.agents_sel').each(function() {
			$(this).addClass('has-init');
			var row = $(this).closest('.row-item');
			DP.select($(this));

			$(this).on('change', function() {
				var val = $(this).val();
				var label = $(this).find(':selected').text().trim();

				if (!val) {
					val = '';
					label = 'Me';
				}

				row.find('.assigned_agent').find('label').text(label);
				$('input.input-agent', row).val(val);

				sendUpdate(row, 'assigned', val, function() {
					DeskPRO_Window.getMessageBroker().sendMessage('agent.ui.tasks.refresh-task-list');
				});
			});
		});
		rowContainer.on('click', '.opt-trigger.date_due', function(ev) {
			var label = $('label', this);
			var row = $(this).closest('.row-item');
			var field = $('input.input-date-due', row);
			var date = $('input.input-date-due', row).val();
			if (!date) {
				date = new Date();
			}

			field.datepicker('dialog', date, function(date, inst) {
				sendUpdate(row, 'date_due', date);
				$('input.input-date-due', row).val(date);
				label.text(date);
			}, {
				dateFormat: 'yy-mm-dd',
				showButtonPanel: true,
				beforeShow: function(input) {
					setTimeout(function() {
						var buttonPane = $(input).datepicker("widget").find(".ui-datepicker-buttonpane");

						$('button', buttonPane).remove();

						var btn = $('<button class="ui-datepicker-current ui-state-default ui-priority-secondary ui-corner-all" type="button">Clear</button>');
						btn.unbind("click").bind("click", function () { $.datepicker._clearDate( input ); label.text('No due date'); });
						btn.appendTo( buttonPane );

						$(input).datepicker("widget").css('z-index', 30101);
					},1);
				}
			}, ev);
		});

		this.getEl('task_save').on('click', function(ev) {
			ev.preventDefault();

			if ($(this).hasClass('saving')) {
				return;
			}

			var title = $.trim(self.getEl('newtask_title').val());
			if (!title) {
				alert('Please enter a description');
				return;
			}

			$(this).addClass('saving').html('<em>Saving</em>');
			var postData = self.getEl('task_row').find('input').serializeArray();
			postData.push({
				name: 'from_ticket',
				value: 1
			});

			$.ajax({
				url: BASE_URL + 'agent/tasks/save',
				data: postData,
				type: 'POST',
				dataType: 'json',
				complete: function() {
					self.getEl('task_save').removeClass('saving').text('Add');
				},
				success: function(data) {

					updateTaskPane();
					self.getEl('newtask_title').val('');

					if (!data.tasks || !data.tasks[0]) {
						return;
					}

					data = data.tasks[0];
					var row = $(data.row_html);

					row.find('li.assigned_agent select.agents_sel').each(function() {
						$(this).addClass('has-init');
						var row = $(this).closest('.row-item');
						DP.select($(this));

						$(this).on('change', function() {
							var val = $(this).val();
							var label = $(this).find(':selected').text().trim();

							if (!val) {
								val = '';
								label = 'Me';
							}

							row.find('.assigned_agent').find('label').text(label);
							$('input.input-agent', row).val(val);
						});
					});

					self.getEl('task_list').show().prepend(row);

					DeskPRO_Window.util.modCountEl(self.getEl('task_count'), '+', 1);

					if (DeskPRO_Window.sections.tasks_section) {
						DeskPRO_Window.sections.tasks_section.refresh();
					}
				}
			});
		});

		var control = new DeskPRO.Agent.PageHelper.TaskListControl(this.wrapper, {
			menuVis:  menuVis2,
			completeCountEl: null
		});

		control.addEvent('updateUi', function() {
			self.updateUi();
			updateTaskPane();
		});
		control.addEvent('updateCount', function() {
			updateTaskPane();
		});

		var updateTaskPane = function() {
			if (DeskPRO_Window.sections.tasks_section) {
				DeskPRO_Window.sections.tasks_section.markUnloadPage();
			}
		};
	},

	//#################################################################
	//# Slas
	//#################################################################

	_initSlas: function() {
		var self = this;
		var form = this.getEl('sla_form');
		var idSelect = form.find('select[name=sla_id]');
		var rows = this.getEl('sla_rows');
		var tabHeader = this.getEl('sla_wrap_tab');

		var addSlaRow = function(html) {
			var add = $(html);

			rows.append(add);
			add.find('.timeago').timeago();
			rows.closest('table').show();

			tabHeader.append(
				$('<span />')
					.addClass('sla-pip')
					.addClass(add.data('sla-status'))
					.data('sla-id', add.data('sla-id'))
			);
		};

		var getVisibleOptions = function(options) {
			return options.filter(function() {
				return $(this).css('display') !== 'none';
			});
		};

		var rowRemoved = function(slaId) {
			var table = rows.closest('table');

			if (!table.find('tbody tr').length) {
				table.hide();
			}

			if (idSelect.length) {
				idSelect.find('option[value="' + slaId + '"]').show();
				if (getVisibleOptions(idSelect.find('option')).length > 1) {
					form.show();
				}
			}

			tabHeader.find('.sla-pip').each(function() {
				var $this = $(this);
				if ($this.data('sla-id') == slaId) {
					$this.remove();
					return false;
				}
			});
		};

		rows.on('click', 'a.sla-delete', function(e) {
			var $this = $(this);

			e.preventDefault();

			if (confirm(rows.data('delete-confirm'))) {
				DeskPRO_Window.util.ajaxWithClientMessages({
					url: $this.attr('href'),
					type: 'POST',
					dataType: 'json'
				}).done(function (json) {
					if (json.success) {
						var slaId = $this.closest('tr').data('sla-id');
						var table = $this.closest('table');

						$this.closest('tr').remove();

						rowRemoved(slaId);
					}
				});
			}
		});

		if (form.length) {
			if (getVisibleOptions(idSelect.find('option')).length <= 1) {
				// only the empty option
				form.hide();
			}

			var progress = this.getEl('sla_save_progress');

			DP.select(idSelect, {
				// todo: try to get it to hide hidden select elements
			});

			form.on('click', 'button', function() {
				var val = idSelect.val();
				if (val.length && val != '0') {
					progress.show();

					DeskPRO_Window.util.ajaxWithClientMessages({
						url: form.data('submit-url'),
						data: form.find('input, textarea, select').serializeArray(),
						type: 'POST',
						dataType: 'json'
					}).done(function(json) {
						if (json.inserted) {
							addSlaRow(json.html);

							idSelect.find('option[value="' + val + '"]').hide();
							if (getVisibleOptions(idSelect.find('option')).length <= 1) {
								// only the empty option
								form.hide();
							} else {
								idSelect.val('0');
							}
						}
					}).always(function() {
						progress.hide();
					});
				}
			});

			// manage sla updates to the ticket
			DeskPRO_Window.getMessageBroker().addMessageListener('agent.ticket-sla-updated', function(info) {
				if (info.ticket_id == self.getMetaData('ticket_id')) {
					rows.find('tr').each(function() {
						var row = $(this);
						if (row.data('sla-id') == info.sla_id) {
							if (info.removed) {
								row.remove();
								rowRemoved(info.sla_id);
							} else {
								row.find('.sla-status-icon').removeClass(info.original_status).addClass(info.sla_status);
								row.data('sla-status', info.sla_status);

								row.find('.warn-date').html(
									info.warn_date
										? $('<time class="timeago" datetime="' + info.warn_date + '"></time>').timeago()
										: 'N/A'
								);
								row.find('.fail-date').html(
									info.fail_date
										? $('<time class="timeago" datetime="' + info.fail_date + '"></time>').timeago()
										: 'N/A'
								);

								if (info.is_completed) {
									row.find('.delete').addClass('completed').removeClass('delete');
								} else {
									row.find('.completed').addClass('delete').removeClass('completed');
								}

								tabHeader.find('.sla-pip').each(function() {
									var pip = $(this);
									if (pip.data('sla-id') == info.sla_id) {
										pip.removeClass(info.original_status).addClass(info.sla_status);
										return false;
									}
								});
							}

							return false;
						}
					});
				}
			}, this.pageUid);
		}
	},

	//#################################################################
	//# Edit name
	//#################################################################

	_initEditName: function() {
		var self = this;
		var namef       = this.getEl('showname');
		var editName    = this.getEl('editname');
		var startBtn    = this.getEl('editname_start');
		var stopBtn     = this.getEl('editname_end');

		var startEditable = function() {
			namef.hide();
			editName.show();
			startBtn.hide();
			stopBtn.show();
		};

		var stopEditable = function() {
			var nametxt = editName.find('input').first();

			var setName = nametxt.val().trim();
			if(!setName) {
				return;
			}

			editName.hide();
			startBtn.show();
			namef.show();
			stopBtn.hide();
			namef.text(setName);

			var postData = [];
			postData.push({
				name: 'subject',
				value: setName
			});

			$.ajax({
				url: BASE_URL + 'agent/tickets/'+self.meta.ticket_id+'/ajax-save-subject.json',
				type: 'POST',
				data: postData
			});
		};

		namef.on('dblclick', startEditable).on('keypress', function(ev) {
			if (ev.keyCode == 13 /* enter key */) {
				ev.preventDefault();
				stopEditable();
			}
		});
		this.getEl('editname_start').on('click', startEditable);
		this.getEl('editname_end').on('click', stopEditable);
	},

	shortcutOpenSnippets: function() {
		if (!this.ticketReplyBox) {
			return;
		}

		if (!this.meta.ticket_reverse_order) {
			this.wrapper.find('div.layout-content').trigger('goscrollbottom');
		}
		this.focusOnReply();
		this.ticketReplyBox.snippetsViewer.open();
	},

	shortcutSendReply: function() {
		if (!this.ticketReplyBox) {
			return;
		}

		if (!this.meta.ticket_reverse_order) {
			this.wrapper.find('div.layout-content').trigger('goscrollbottom');
		}
		this.ticketReplyBox.el.find('.submit-trigger').click();
	},

	shortcutReplySetAwaitingUser: function() {
		if (!this.ticketReplyBox) {
			return;
		}

		this.ticketReplyBox.setReplyAsOptionName('awaiting_user');
		if (!this.meta.ticket_reverse_order) {
			this.wrapper.find('div.layout-content').trigger('goscrollbottom');
		}
		this.focusOnReply();
	},

	shortcutReplySetAwaitingAgent: function() {
		if (!this.ticketReplyBox) {
			return;
		}

		this.ticketReplyBox.setReplyAsOptionName('awaiting_agent');
		if (!this.meta.ticket_reverse_order) {
			this.wrapper.find('div.layout-content').trigger('goscrollbottom');
		}
		this.focusOnReply();
	},

	shortcutReplySetResolved: function() {
		if (!this.ticketReplyBox) {
			return;
		}

		this.ticketReplyBox.setReplyAsOptionName('resolved');
		if (!this.meta.ticket_reverse_order) {
			this.wrapper.find('div.layout-content').trigger('goscrollbottom');
		}
		this.focusOnReply();
	},

	shortcutReplyOpenProperties: function() {
		if (!this.ticketReplyBox) {
			return;
		}

		if (!this.meta.ticket_reverse_order) {
			this.wrapper.find('div.layout-content').trigger('goscrollbottom');
		}
		this.ticketReplyBox.openStatusMenu();
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.Page.Ticket');

/**
 * Overlays content so you cant do anything until dismissed
 */
DeskPRO.Agent.PageFragment.Page.Ticket.TicketLocked = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(page, options) {
		this.page = page;
		this.options = {};

		this.setOptions(options);

		this.lockedBar = this.page.getEl('locked_bar');
		this.lockedOverlay = this.page.getEl('locked_overlay');
		this.dismissBtn = $('button.dismiss', this.lockedBar);

		this.dismissBtn.on('click', this.dismiss.bind(this));
	},

	/**
	 * The user dismisses the message
	 */
	dismiss: function() {
		var wrapper = $('.page-ticket:first', this.page.wrapper);

		var self = this;
		this.lockedOverlay.fadeOut('fast');
		this.lockedBar.fadeOut('fast', function() {
			wrapper.removeClass('locked');
		});
	},

	/**
	 * The ticket becomes unlocked while we're viewing
	 */
	unlock: function() {
		// same as dismiss right now
		this.dismiss();
	},

	destroy: function() {

	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.Page.Ticket');

/**
 * Handles functionality of most of the header bit, such as
 * Assign to Me, assign to my team, status etc.
 */
DeskPRO.Agent.PageFragment.Page.Ticket.TicketActions = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(page, options) {

		var self = this;

		this.macroId = null; // currently open macro id
		this.page = page;
		this.options = {};

		this.setOptions(options);

		this.changeManager = this.page.changeManager;
		this.ticketId = this.page.meta.ticket_id;

		var wrapper = this.page.wrapper;
		var actionsButtons = this.getEl('action_buttons');

		this.page.getEl('flag').on('change', function() {
			var value = $(this).val();
			var prop = self.changeManager.getPropertyManager('flag');
			self.changeManager.setInstantChange(prop, value);
		});

		DP.select(this.getEl('agent_sel'));
		DP.select(this.getEl('agent_team_sel'));
		DP.select(this.getEl('followers_sel'));

		var showSaving = this.getEl('agent_prop_controls').find('.mark-loading');
		var showSaved  = this.getEl('agent_prop_controls').find('.mark-saved');
		var callQueue = new Orb.Util.CallQueue({
			startCallback: function() {
				showSaved.stop().hide();
				showSaving.show();
			},
			endCallback: function() {
				showSaving.hide();
				showSaved.show().fadeOut(1000);
			}
		});

		this.getEl('agent_sel').on('change', function() {

			if ($(this).hasClass('eat-change')) {
				$(this).removeClass('eat-change');
				return;
			}

			var agent_id = parseInt($(this).find(':selected').val()) || 0;
			var agentProp = self.changeManager.getPropertyManager('agent_id');

			if (self.page.ticketReplyBox) {
				self.page.ticketReplyBox.getElById('agent_sel').select2('val', agent_id);
				self.page.ticketReplyBox.getElById('agent_sel').trigger('change');
			}

			callQueue.call(function() {
				self.changeManager.setInstantChange(agentProp, agent_id, function() {
					callQueue.next();
				});
			});
		});

		this.getEl('agent_team_sel').on('change', function() {

			if ($(this).hasClass('eat-change')) {
				$(this).removeClass('eat-change');
				return;
			}

			var agent_team_id = parseInt($(this).find(':selected').val()) || 0;
			var agentTeamProp = self.changeManager.getPropertyManager('agent_team_id');

			if (self.page.ticketReplyBox) {
				self.page.ticketReplyBox.getElById('agent_team_sel').select2('val', agent_team_id);
				self.page.ticketReplyBox.getElById('agent_sel').trigger('change');
			}

			callQueue.call(function() {
				self.changeManager.setInstantChange(agentTeamProp, agent_team_id, function() {
					callQueue.next();
				});
			});
		});

		//------------------------------
		// Followers
		//------------------------------

		var followerSel = this.page.getEl('followers_sel');
		var followersList = this.page.getEl('followers_list');

		this.page.getEl('add_follower_btn').on('click', function(ev) {
			ev.preventDefault();
			self.page.getEl('followers_sel_wrap').toggleClass('on');
			followerSel.select2('val', '0');
		});

		followerSel.on('change', function() {
			var agentId = parseInt($(this).val());
			self.page.getEl('followers_sel_wrap').removeClass('on');

			if (!agentId || followersList.find('.agent-' + agentId)[0]) {
				return;
			}

			var option = followerSel.find('option[value="' + agentId + '"]');

			var li = $('<li class="agent-'+agentId+'" data-agent-id="'+agentId+'"><a class="dp-btn dp-btn-small agent-link" data-agent-id="'+agentId+'"><span class="text"></span><span class="remove-row-trigger"> <i class="icon-remove"></i></span></a></li>');
			li.find('span.text').css('background-image', 'url(' +option.data('icon-small') + ')').text(option.text());

			followersList.append(li);
			updateFollowersList();
		});

		followersList.on('click', '.remove-row-trigger', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();
			ev.stopImmediatePropagation();

			$(this).closest('li').remove();
			updateFollowersList();
		});

		var updateFollowersList = function() {
			var postData = [{
				name: 'with_set_agent_parts',
				value: 1
			}];
			followersList.find('li').each(function() {
				postData.push({
					name: 'set_agent_part_ids[]',
					value: $(this).data('agent-id')
				});
			});

			callQueue.call(function() {
				self.changeManager.saveChanges(postData, function() {
					callQueue.next();
				});
			});
		};

		//------------------------------
		// Status
		//------------------------------

		if (this.page.meta.ticket_perms.modify_set_resolved || this.page.meta.ticket_perms.modify_set_awaiting_agent || this.page.meta.ticket_perms.modify_set_awaiting_user) {
			var statusEl = this.page.getEl('status_code').on('change', function() {
				var prop = self.changeManager.getPropertyManager('status');

				var status = $(this).val();
				self.changeManager.setInstantChange(prop, status);
			});

			if (!this.page.meta.ticket_perms.modify_set_resolved) {
				statusEl.find('option[value="resolved"]').not(':selected').remove();
			}
			if (!this.page.meta.ticket_perms.modify_set_awaiting_agent) {
				statusEl.find('option[value="awaiting_agent"]').not(':selected').remove();
			}
			if (!this.page.meta.ticket_perms.modify_set_awaiting_user) {
				statusEl.find('option[value="awaiting_user"]').not(':selected').remove();
			}
			if (!this.page.meta.ticket_perms.modify_set_closed) {
				statusEl.find('option[value="closed"]').not(':selected').remove();
			}
		}

		//------------------------------
		// Department
		//------------------------------

		if (this.page.meta.ticket_perms.modify_department) {
			this.page.getEl('department_id').on('change', function() {
				var prop = self.changeManager.getPropertyManager('department_id');
				var depId = parseInt($(this).val());
				var currentDepId = prop.getValue();

				if (!depId) {
					return;
				}

				self.changeManager.setInstantChange(prop, depId);
				self.page.ticketFields.updateDisplay();
			});

			this.page.getEl('field_holders').find('select.prop-input-product, select.prop-input-priority_id, select.prop-input-workflow_id, select.prop-input-category_id').on('change', function() {
				self.page.ticketFields.updateDisplay();
			});
		}

		//------------------------------
		// Urgency
		//------------------------------

		if (this.page.meta.ticket_perms.modify_fields) {
			this.page.getEl('urgency').on('change', function() {
				var prop = self.changeManager.getPropertyManager('urgency');

				var urgency = $(this).val();
				self.changeManager.setInstantChange(prop, urgency);
			});
		}

		//------------------------------
		// Macros
		//------------------------------

		var macroMenu = this.getEl('macros_menu');
		this.macrosMenu = new DeskPRO.UI.Menu({
			triggerElement: this.getEl('macros_menu_trigger'),
			menuElement: macroMenu,
			onItemClicked: (function(info) {
				var item = $(info.itemEl);
				if (item.hasClass('open-settings-trigger')) {
					$('#settingswin').trigger('dp_open', 'macros');
				} else {
					this.confirmMacro($(info.itemEl).data('macro-id'));
				}
			}).bind(this)
		});

		$('#settingswin').on('dp_macros_updated', function(ev) {
			macroMenu.find('li').not('.open-settings-trigger').remove();
			Array.each(ev.macroItems, function(x) {
				var li = $('<li />');
				li.data('macro-id', x.id);
				li.text(x.title);

				li.appendTo(macroMenu);
			});
		});


		DP.select(this.page.getEl('flag'));
		DP.select(this.page.getEl('department_id'));
		DP.select(this.page.getEl('status_code'));
		DP.select(this.page.getEl('urgency'));
	},

	_initMacroOverlay: function() {
		var self = this;
		if (this.macroOverlay) {
			return;
		}

		var overlayEl = this.getEl('confirm_macro_overlay');
		this.getEl('apply_macro_btn').on('click', function() {
			self.saveMacro();
		});

		this.macroOverlay = new DeskPRO.UI.Overlay({
			contentElement: overlayEl
		});
	},

	confirmMacro: function(macroId) {
		this.macroActions = null;

		var overlayEl = this.getEl('confirm_macro_overlay');
		$.ajax({
			url: this.page.getMetaData('getMacroUrl').replace('$macro_id', macroId),
			type: 'GET',
			context: this,
			dataType: 'json',
			success: function(data) {
				this._initMacroOverlay();

				console.log(data);

				var ul = overlayEl.find('ul.actions-list');
				ul.empty();

				Array.each(data.descriptions, function(desc) {
					var li = $('<li />');
					li.html(desc);

					ul.append(li);
				});

				this.macroId = macroId;
				this.macroOverlay.open();
			}
		});
	},

	saveMacro: function() {

		this.macroOverlay.close();

		if (!this.macroId) {
			return;
		}

		DP.console.log('Applying macro %d', this.macroId);

		var url = BASE_URL + 'agent/tickets/'+this.ticketId+'/'+this.macroId+'/apply-macro.json';

		$.ajax({
			url: url,
			type: 'POST',
			dataType: 'json',
			context: this,
			success: function(data) {

				if (data.error) {
					DeskPRO_Window.showAlert("The macro was not applied because you do not have permission to perform one or more of the defined actions.");
					return;
				}

				this.page.closeSelf();

				if (!data.close_tab) {
					DeskPRO_Window.runPageRoute('page:' + BASE_URL + 'agent/tickets/' + this.ticketId);
				}
			}
		});


		this.macroId = null;
	},

	/**
	 * Alias for <code>this.page</code>
	 *
	 * @param {HTMLElement}
	 */
	getEl: function(id) {
		return this.page.getEl(id);
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.Page.PersonHelper');

DeskPRO.Agent.PageFragment.Page.PersonHelper.ChangePic = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(page, options) {
		var self = this;

		this.options = {
			loadUrl: '',
			saveUrl: ''
		};

		this.setOptions(options);
		this.page = page;

		this.page.getEl('change_user_picture').on('click', this.open.bind(this));

		this.page.addEvent('destroy', this.destroy, this);
	},

	_initOverlay: function() {
		var self = this;
		if (this.overlay) {
			return;
		}

		this.wrapperEl = $('<div class="change-picture-overlay"><div class="overlay-content" style="width: 400px; height: 300px; "/><div>Loading...</div></div>');

		this.overlay = new DeskPRO.UI.Overlay({
			contentElement: this.wrapperEl,
			destroyOnClose: true,
			zIndex: 'top',
			onOverlayClosed: function() {
				self.overlay = null;
			}
		});

		$.ajax({
			url: this.options.loadUrl,
			type: 'GET',
			dataType: 'html',
			context: this,
			success: function(html) {
				if (this.overlay) {
					this.overlay.setContent($(html));
					this.wrapperEl = this.overlay.getWrapper();
					this._initControls();
				}
			}
		});
	},

	_initControls: function() {
		var wrapper = this.overlay.getWrapper();

		DeskPRO_Window.util.fileupload(wrapper, {
			page: this.page,
			uploadTemplate: $('.template-upload', wrapper),
			downloadTemplate: $('.template-download', wrapper),
			formData: [{
				name: 'is_image',
				value: 1
			}],
			completed: function() {
				$('.files .in', wrapper).css('height', 'auto');
			}
		}).bind('fileuploadstart', function() {
			$('p.explain', wrapper).hide();
		}).bind('fileuploadadd', function() {
			$('.files', wrapper).empty();
			$('input[name=set_pic_opt]', wrapper).each(function() {
				$(this).attr('checked', $(this).val() == 'newpic');
			})
		});

		wrapper.on('click', '.save-trigger', this._doSave.bind(this));
	},

	_doSave: function(e) {
		e.preventDefault();

		var type = $('input[name=set_pic_opt]:checked', this.overlay.getWrapper()).val();

		var newImgSrc = null;
		var action = null;

		var formData = [];

		switch (type) {
			case 'nochange':
				this.close();
				return;

			case 'remove':
				formData.push({ name: 'action', value: 'delete-picture' });
				formData.push({ name: 'disable_picture', value: '1' });
				newImgSrc = $('img.pic-default', this.wrapperEl).attr('src');
				break;

			case 'gravatar':
				formData.push({ name: 'action', value: 'delete-picture' });
				newImgSrc = $('img.pic-gravatar', this.wrapperEl).attr('src');
				break;

			case 'newpic':
				formData.push({ name: 'action', value: 'set-picture' });
				var blobId = $('input.new_blob_id', this.wrapperEl).val();

				if (!blobId) {
					return;
				}

				formData.push({ name: 'blob_id', value: blobId });
				newImgSrc = $('img.pic-new', this.wrapperEl).data('setted-size');

				break;

			default:
				return;
		}

		$.ajax({
			url: this.options.saveUrl,
			type: 'POST',
			dataType: 'json',
			data: formData
		});

		this.page.getEl('picture_display').attr('src', newImgSrc);

		this.close();
	},

	open: function() {
		this._initOverlay();
		this.overlay.open();
	},

	close: function() {
		if (this.overlay) {
			this.overlay.destroy();
			this.overlay = null;
		}
	},

	destroy: function() {
		if (this.overlay) {
			this.overlay.destroy();
		}
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.Page.PersonHelper');

/**
 * Handles the contact editor
 */
DeskPRO.Agent.PageFragment.Page.PersonHelper.ContactEditor = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(page, options) {
		var self = this;

		this.options = {
			displayEl: null,
			outsideEl: null,
			saveUrl: ''
		};

		this.setOptions(options);
		this.page = page;

		this.wrapper = this.page.wrapper;

		this.page.addEvent('destroy', this.destroy, this);

		this.initEditorOverlay();

		var displayEl = $(self.options.displayEl || '.contact-list-wrapper', self.wrapper);
		var outsideEl = $(self.options.outsideEl);
		if ($('div.outside-html', displayEl)) {
			var outside = $('div.outside-display', displayEl).detach();
		} else {
			var outside = $('<div/>');
		}

		outsideEl.empty().append(outside);
	},

	replaceEditorOverlay: function(html) {
		var contactEditor = $('.profile-contact-editor', this.wrapper);
		contactEditor.remove();
		contactEditor = null;

		$(html).appendTo(this.wrapper);

		this.initEditorOverlay();

		this.fireEvent('replaceEditor', [this]);
	},

	initEditorOverlay: function() {

		var self = this;
		if (this.contactOverlay) {
			this.contactOverlay.destroy();
			this.contactOverlay = null;
		}

		if (this.contactNewMenu) {
			this.contactNewMenu.destroy();
			this.contactNewMenu = null;
		}

		var contactEditor = $('.profile-contact-editor-wrapper', this.wrapper);

		this.contactOverlay = new DeskPRO.UI.Overlay({
			customClassname: 'profile-contact-editor',
			triggerElement: $('.contact-edit', this.wrapper),
			contentElement: contactEditor,
			zIndex: 'none'
		});

		$('.save-trigger', contactEditor).on('click', function(ev) {

			var formData = $(':input, select, textarea', contactEditor).serializeArray();

			contactEditor.addClass('loading');

			$.ajax({
				url: self.options.saveUrl,
				type: 'POST',
				dataType: 'json',
				data: formData,
				complete: function() {
					contactEditor.removeClass('loading');
				},
				success: function(data) {
					self.contactOverlay.close();
					var displayEl = $(self.options.displayEl || '.contact-list-wrapper', self.wrapper);
					var outsideEl = $(self.options.outsideEl);
					var newHtml = $(data.display_html);

					if ($('div.outside-html', newHtml)) {
						var outside = $('div.outside-display', newHtml).detach();
					} else {
						var outside = $('<div/>');
					}

					displayEl.empty().append(newHtml);
					outsideEl.empty().append(outside);

					DeskPRO_Window.initInterfaceServices(displayEl);
					DeskPRO_Window.initInterfaceServices(outsideEl);

					self.replaceEditorOverlay(data.editor_overlay_html);

					if (data.errors) {
						var div = $('<div>There were errors with the following changes:</div>');
						var ul = $('<ul></ul>');

						Array.each(data.errors, function(e) {
							var li = $('<li />');
							li.text(e);
							li.appendTo(ul);
						});

						ul.appendTo(div);

						DeskPRO_Window.showAlert(div);
					}

					self.fireEvent('success', data);
				}
			});
		});

		var checkFields = function(rowTypeEl) {
			var row = $('li', rowTypeEl).last();

			var show = false;
			if (row.is('.new')) {
				var fields = $('input, textarea, select', row);
				fields.each(function() {
					if ($(this).val()) {
						show = true;
					}
				});
			} else {
				show = false;
			}

			if (show) {
				$('.with-some', rowTypeEl).show();
			} else {
				$('.with-some', rowTypeEl).hide();
			}
		};

		function doRemove(row, rowTypeEl) {
			var removeName = row.data('remove-name');
			var removeVal  = row.data('remove-value');


			if (removeName && removeVal) {
				var input = $('<input type="hidden" />');
				input.attr('name', removeName);
				input.val(removeVal);

				input.appendTo(contactEditor);
			}

			row.fadeOut('fast', function() {
				row.remove();
				checkFields(rowTypeEl);

				var lis = $('li', rowTypeEl);
				if (lis.length < 1) { /* two because the fade is going now and it hasnt been removed yet */
					rowTypeEl.removeClass('with-values');
				}
			});
		};
		contactEditor.on('click', '.remove', function(ev) {
			var rowTypeEl = $(this).closest('.row-type');
			var row = $(this).closest('li');

			if (row.data('confirm')) {
				DeskPRO_Window.showConfirm(row.data('confirm'), function() {
					doRemove(row, rowTypeEl);
				});
			} else {
				doRemove(row, rowTypeEl);
			}
		});

		contactEditor.on('click', '.add-trigger', function(ev) {
			var rowTypeEl = $(this).closest('.row-type');

			var tpl = DeskPRO_Window.util.getPlainTpl($('.tpl-new-row', rowTypeEl));
			tpl = tpl.replace(/%id%/g, Orb.uuid());

			var el = $(tpl);
			el.addClass('new');
			el.appendTo($('ul', rowTypeEl));

			DeskPRO_Window.initInterfaceServices(el);

			$('input, textarea, select', el).bind('change blur keyup', function() {
				checkFields(rowTypeEl);
			});

			$('.with-some', rowTypeEl).hide();

			rowTypeEl.addClass('with-values');
		});
	},

	destroy: function() {
		if (this.contactOverlay) {
			this.contactOverlay.destroy();
			this.contactOverlay = null;
		}
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.Page.Content');

/**
 * Delete/spam things. Toggles visibility of status section, and notice bar,
 * and sens appropriate save ajax.
 */
DeskPRO.Agent.PageFragment.Page.Content.DeleteControl = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(page, options) {
		var self = this;

		this.options = {
			ajaxSaveUrl: '',
			type: 'delete'
		};

		this.setOptions(options);
		this.page = page;

		this.deleteBtn      = $('.' + this.options.type, this.page.getEl('action_buttons'));
		this.deletedNotice  = $('.' + this.options.type + '-notice:first', this.page.wrapper);
		this.statusBtn      = $('.the-status:first', this.page.wrapper);
		this.undeleteBtn    = $('.un' + this.options.type, this.deletedNotice);

		this.otherDeleteBtns = $('.delete-type:not(.' + this.options.type + ')', this.page.getEl('action_buttons'));

		this.deleteBtn.on('click', function() {
			self.handleDeleted();
			$.ajax({
				url: self.options.ajaxSaveUrl,
				data: { action: self.options.type },
				type: 'GET',
				dataType: 'json',
				error: function() {
					// just revert UI elements
					self.handleUndelete();
				},
				success: function(html) {
					DeskPRO_Window.getMessageBroker().sendMessage('agent.ui.content_deleted.' + page.TYPENAME);
				}
			});
		});
	},

	undelete: function() {

	},

	handleDeleted: function() {
		this.deleteBtn.hide();
		this.statusBtn.hide();
		this.deletedNotice.show();
		this.otherDeleteBtns.hide();
	},

	handleUndelete: function() {
		this.deleteBtn.show();
		this.statusBtn.show();
		this.deletedNotice.hide();
		this.otherDeleteBtns.show();
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.Page.Content');

/**
 * Management of participants in the ticket
 */
DeskPRO.Agent.PageFragment.Page.Content.StickyWords = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(page, options) {
		var self = this;

		this.page = page;

		this.options = {
			saveUrl: null,

			/**
			 * The type of object editing
			 */
			contentType: '',

			/**
			 * The ID of the content
			 */
			contentId: 0,

			/**
			 * The wrapper where tagit will be applied
			 */
			element: null
		};

		this.setOptions(options);

		if (!this.options.saveUrl) {
			this.options.saveUrl = BASE_URL + 'agent/publish/save-sticky-search-words/' + this.options.contentType + '/' + this.options.contentId;
		}

		this.termsInput = new DeskPRO.UI.LabelsInput({
			input: this.options.element,
			placeholder: 'Enter a search word...'
		});
		this.termsInput.addEvent('change', function() {
			self._updated();
		});
	},

	_updated: function() {

		var labels = this.termsInput.getLabels();
		var ev = {
			labels: labels,
			cancel: false
		};
		this.fireEvent('change', [ev]);

		if (ev.cancel) {
			return;
		}

		var data = [];
		Array.each(labels, function(w) {
			data.push({
				name: 'words[]',
				value: w
			});
		});

		$.ajax({
			url: this.options.saveUrl,
			data: data,
			type: 'POST'
		});
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');

DeskPRO.Agent.PageFragment.Page.NewArticle = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'newarticle';
		this.allowDupe = true;
	},

	initPage: function(el) {
		var self = this;
		this.wrapper = el;
		this.parent(el);

		if (!this.getEl('cat').find('option')[0]) {
			this.wrapper.find('.form-header-error').show();
			this.wrapper.find('.form-outer').hide();
			this.markForReload();
		}

		this.form = $('form', this.wrapper).on('submit', function(ev) {
			ev.preventDefault();
		});

		$('button.submit-trigger', this.wrapper).on('click', this.submit.bind(this));

		this._initContentSection();
		this._initOtherSection();

		this.stateSaver = new DeskPRO.Agent.PageHelper.StateSaver({
			stateId: 'newarticle',
			listenOn: this.getEl('newarticle')
		});
		this.ownObject(this.stateSaver);

		window.setTimeout(function() {
			if (self.OBJ_DESTROYED) return;

			self.wrapper.find('select').each(function() {
				if ($(this).prop('multiple')) {
					$(this).width(300);
				}
				DP.select($(this));
			});
			self.updateUi();
		}, 300);

		this.activate();
	},

	activate: function() {
		var selectedCat = $('#publish_outline_articlescat_list').find('.nav-selected').data('cat-id');
		if (selectedCat) {
			this.getEl('cat').find("option[value=\"" + selectedCat + "\"]").prop('selected', true);
			this.getEl('cat').trigger('change');
		}
	},

	markForReload: function() {
		if (!this.markedForReload) {
			this.markedForReload = true;
			this.addEvent('deactivate', this.closeSelf.bind(this));
		}
	},

	closeSelf: function() {
		var ev = {cancel: false};
		this.fireEvent('closeSelf', ev);

		if (!ev.cancel) {
			this.parent();
		}
	},

	destroyPage: function() {
		// Workaround for tinymce bug to do with remove()
		// We'll manually remove the node ourselves
		var el = this.wrapper.find('.article-section');
		if (el[0]) {
			el.get(0).parentNode.removeChild(el.get(0));
		}
	},

	submit: function() {
		var formData = this.form.serializeArray();

		if (this.labelsInput) {
			formData.append(this.labelsInput.getFormData());
		}

		$('div.error.section', this.wrapper).removeClass('error');
		$('.error-message-on', this.wrapper).removeClass('error-message-on');

		this.stateSaver.stop();
		this.stateSaver.resetState();
		this.wrapper.addClass('loading');

		$.ajax({
			url: BASE_URL + 'agent/kb/article/new/save',
			type: 'POST',
			data: formData,
			dataType: 'json',
			context: this,
			complete: function() {
				this.wrapper.removeClass('loading');
			},
			success: function(data) {

				if (data.error) {
					Array.each(data.error_codes, function(code) {
						this.showErrorCode(code);
					}, this);
					this.updateUi();
					return;
				}

				var pending_article_id = this.getEl('pending_article_id').val();
				if (pending_article_id) {
					DeskPRO_Window.getMessageBroker().sendMessage('kb.pending_article_removed', {
						pending_article_id: pending_article_id
					});
				}

				if (data.success) {
					DeskPRO_Window.runPageRoute('page:' + BASE_URL + 'agent/kb/article/' + data.article_id);
					this.markForReload();
					this.closeSelf();
				} else {
					alert('There was an error with the form');
				}
			}
		});
	},

	showErrorCode: function(code) {
		$('.' + code + '.error-message', this.wrapper).addClass('error-message-on');
	},

	setTitle: function(title) {
		this.getEl('title').val(title).change();
	},
	setContent: function(content, is_html) {
		if (!is_html) {
			content = Orb.escapeHtml(content);
		}
		this.getEl('content').html(content);
	},

	setPendingArticle: function(data) {
		this.getEl('pending_article_id').val(data.id);

		if (data.ticket_subject) {
			this.setTitle(data.ticket_subject);
		}
		if (data.initial_message_html) {
			var content = '';
			if (data.initial_message_html) {
				content = "<h3>Question:</h3>";
				content += data.initial_message_html += "<br /><br />";
			}
			this.setContent(content, true);
		} else {
			this.setContent('', true);
		}

		var infoWrap = $('.pending-info:first', this.wrapper);

		if (data.ticket_url) {
			$('.pending-ticket a', infoWrap).text(data.ticket_subject);
			$('.pending-ticket a', infoWrap).data('route', 'page:' + data.ticket_url);
			$('.pending-ticket', infoWrap).show();
		}

		if (data.comment) {
			$('.pending-reason', infoWrap).text(data.comment).show();
		}

		$('.person-name', infoWrap).text(data.person_name);

		infoWrap.show();
	},

	//#################################################################
	//# Content section
	//#################################################################

	_initContentSection: function() {

		var self = this;
		this.getEl('content').css({
			width: this.wrapper.width() - 80
		});

		// Make the size of the message box based off of the height of the window
		var h = $(window).height();
		this.getEl('content').css('height', Math.max(h - 500, 200));

		DP.rteTextarea(this.getEl('content'), {
			setup: function(ed) {
				ed.onKeyPress.add(function() {
					if (self.stateSaver) {
						self.stateSaver.triggerChange();
					}
				});
			}
		});

		this.acceptContentLink = new DeskPRO.Agent.PageHelper.AcceptContentLink({
			page: this,
			rte: this.getEl('content')
		});
	},

	//#########################################################################
	//# Other Section
	//#########################################################################

	_initOtherSection: function() {
		var self = this;
		this.otherTabs = new DeskPRO.UI.SimpleTabs({
			triggerElements: $('li', this.getEl('other_props_tabs')),
			context: this.getEl('other_props_tabs_content'),
			autoSelectFirst: false,
			onTabSwitch: function(eventData) {
				if (!self.labelsInput && eventData.tabContent.hasClass('tab-properties')) {
					self.labelsInput = new DeskPRO.UI.LabelsInput({
						type: 'articles',
						fieldName: 'newarticle[labels]',
						input: $(".tags-wrap input", eventData.tabContent),
						onChange: function() {
							if (self.stateSaver) {
								self.stateSaver.triggerChange();
							}
						}
					});
					self.ownObject(self.labelsInput);
				}
			},
			onTabClick: (function(ev) {
				var contentWrap = this.getEl('other_props_tabs_content');
				var navWrap = this.getEl('other_props_tabs_wrap');
				var tab = ev.tabEl;

				// Toggle content state if we're clicking for the first time,
				// or re-clicking a tab
				if (!$('.on', navWrap).length || tab.is('.on')) {
					if (contentWrap.is(':visible')) {
						contentWrap.hide();
						navWrap.removeClass('on');
					} else {
						contentWrap.show();
						navWrap.addClass('on');
					}
				}
			}).bind(this)
		});
		this.ownObject(this.otherTabs);

		this.getEl('slug').on('focus', function() {
			$(this).addClass('had-focus');
		});

		// Attachments
		var list = $('.file-list', this.wrapper);
		$('input', list[0]).live('click', function() {
			var el = $(this);
			var li = el.parent();
			if (el.is(':checked')) {
				li.removeClass('unchecked');
			} else {
				li.addClass('unchecked');
			}
		});

		DeskPRO_Window.util.fileupload(this.wrapper, {
			page: this
		});
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');

DeskPRO.Agent.PageFragment.Page.NewPerson = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'newperson';
		this.allowDupe = true;
	},

	initPage: function(el) {
		var self = this;
		this.wrapper = el;

		this.form = $('form', this.wrapper).on('submit', function(ev) {
			ev.preventDefault();
		});

		$('button.submit-trigger', this.wrapper).on('click', this.submit.bind(this));

		this._initNameSection();
		this._initOtherSection();

		this.stateSaver = new DeskPRO.Agent.PageHelper.StateSaver({
			stateId: 'newperson',
			listenOn: this.getEl('newperson')
		});
		this.ownObject(this.stateSaver);

		this.getEl('org_searchbox').on('orgsearchboxclick orgsearchboxcreate', function() {
			self.getEl('org_pos').show();
			self.updateUi();
		}).on('orgsearchboxcleared', function() {
			self.getEl('org_pos').hide();
			self.updateUi();
		});

        DeskPRO_Window.util.fileupload(el, {
            uploadTemplate: $('.template-upload', el),
            downloadTemplate: $('.template-download', el),
            url: BASE_URL + 'agent/misc/parse-vcard'
        });

        el.bind('fileuploaddone', function(event, data) {
            for(name in data.result[0].fields) {
                $('[name$="['+name+']"]', el).val(data.result[0].fields[name]);
            }
        });
        el.bind('fileuploadstart', function(event, data) {
        });

		self.wrapper.find('.dpe_select').each(function() {
			// Label input is handled by labels class
			if (!$(this).hasClass('labels-input')) {
				DP.select($(this));
			}
		});
	},

	markForReload: function() {
		if (!this.markedForReload) {
			this.markedForReload = true;
			this.addEvent('deactivate', this.closeSelf.bind(this));
		}
	},

	closeSelf: function() {
		var ev = {cancel: false};
		this.fireEvent('closeSelf', ev);

		if (!ev.cancel) {
			this.parent();
		}
	},

	submit: function() {
		var formData = this.form.serializeArray();

		$.ajax({
			url: BASE_URL + 'agent/people/new/save',
			type: 'POST',
			data: formData,
			dataType: 'json',
			context: this,
			success: function(data) {
				if (data.success) {
					if (this.getEl('org_id').val().length && this.fromCompanyTab) {
						DeskPRO_Window.getMessageBroker().sendMessage('new-org-user', {
							organization_id: this.getEl('org_id').val(),
							person_id: data.person_id
						});
					} else {
						DeskPRO_Window.runPageRoute('person:' + BASE_URL + 'agent/people/' + data.person_id);
					}

					DeskPRO_Window.getMessageBroker().sendMessage('agent.person.added', { person_id: data.person_id });
					this.closeSelf();
				} else {
					var errorMessages = $('<div/>');
					errorMessages.append('<p>Please correct the following errors with your form:</p>');

					Array.each(data.error_messages, function(msg) {
						errorMessages.append('<div>&bull; ' + msg + '</div>');
					});
					DeskPRO_Window.showAlert(errorMessages, 'error');
				}
			}
		});
	},

	setOrganization: function(org_id, org_name) {
		this.getEl('org_id').val(org_id);
		this.getEl('org_name').val(org_name);
		this.getEl('org_pos').show();
		this.updateUi();
		this.fromCompanyTab = true;
	},

	setGuessTerm: function(term) {
		if (term.indexOf('@') !== -1) {
			this.getEl('email').val(term);
		} else {
			this.getEl('name').val(term);
		}
	},

	//#################################################################
	//# Name/email section
	//#################################################################

	_initNameSection: function() {

	},

	//#########################################################################
	//# Other Section
	//#########################################################################

	_initOtherSection: function() {
		var self = this;
		this.otherTabs = new DeskPRO.UI.SimpleTabs({
			triggerElements: $('li', this.getEl('other_props_tabs')),
			context: this.getEl('other_props_tabs_content'),
			autoSelectFirst: false,
			onTabSwitch: function(eventData) {
				if (!self.labelsInput && eventData.tabContent.hasClass('tab-properties') && self.getEl('labels_input')[0]) {
					self.labelsInput = new DeskPRO.UI.LabelsInput({
						type: 'tickets',
						input: self.getEl('labels_input')
					});
					self.ownObject(self.labelsInput);
				}
			},
			onTabClick: (function(ev) {
				var contentWrap = this.getEl('other_props_tabs_content');
				var navWrap = this.getEl('other_props_tabs_wrap');
				var tab = ev.tabEl;

				// Toggle content state if we're clicking for the first time,
				// or re-clicking a tab
				if (!$('.on', navWrap).length || tab.is('.on')) {
					if (contentWrap.is(':visible')) {
						contentWrap.hide();
						navWrap.removeClass('on');
					} else {
						contentWrap.show();
						navWrap.addClass('on');
					}
				}
			}).bind(this)
		});
		this.ownObject(this.otherTabs);
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');

DeskPRO.Agent.PageFragment.Page.NewOrganization = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'neworganization';
		this.allowDupe = true;
	},

	initPage: function(el) {
		var self = this;
		this.wrapper = el;
		this.parent(el);

		this.form = $('form', this.wrapper).on('submit', function(ev) {
			ev.preventDefault();
		});

		$('button.submit-trigger', this.wrapper).on('click', this.submit.bind(this));

		this._initNameSection();
		this._initOtherSection();

		this.stateSaver = new DeskPRO.Agent.PageHelper.StateSaver({
			stateId: 'neworg',
			listenOn: this.getEl('neworg')
		});
		this.ownObject(this.stateSaver);
	},

	markForReload: function() {
		if (!this.markedForReload) {
			this.markedForReload = true;
			this.addEvent('deactivate', this.closeSelf.bind(this));
		}
	},

	closeSelf: function() {
		var ev = {cancel: false};
		this.fireEvent('closeSelf', ev);

		if (!ev.cancel) {
			this.parent();
		}
	},

	submit: function() {
		var self = this;
		var formData = this.form.serializeArray();

		$.ajax({
			url: BASE_URL + 'agent/organizations/new/save',
			type: 'POST',
			data: formData,
			dataType: 'json',
			context: this,
			success: function(data) {
				if (data.success) {
					DeskPRO_Window.runPageRoute('person:' + BASE_URL + 'agent/organizations/' + data.org_id);

					$('select.dp-org-select').each(function() {
						var opt = $('<option />');
						opt.val(data.org_id);
						opt.text(self.getEl('name').val());

						$(this).append(opt);
					});

					this.closeSelf();
				} else {
					if (data && data.error_code && data.error_code == 'invalid_name') {
						DeskPRO_Window.showAlert('Please enter a name for the organization');
					}
				}
			}
		});
	},

	//#################################################################
	//# Name/email section
	//#################################################################

	_initNameSection: function() {

	},

	//#########################################################################
	//# Other Section
	//#########################################################################

	_initOtherSection: function() {

		var self = this;
		this.otherTabs = new DeskPRO.UI.SimpleTabs({
			triggerElements: $('li', this.getEl('other_props_tabs')),
			context: this.getEl('other_props_tabs_content'),
			autoSelectFirst: false,
			onTabSwitch: function(eventData) {
				if (!self.labelsInput && eventData.tabContent.hasClass('tab-properties')) {
					self.labelsInput = new DeskPRO.UI.LabelsInput({
						type: 'org',
						fieldName: 'neworg[labels]',
						textarea: $(".tags-wrap input", eventData.tabContent),
						onChange: function() {
							self.stateSaver.triggerChange();
						}
					});
					self.ownObject(self.labelsInput);
				}
			},
			onTabClick: (function(ev) {
				var contentWrap = this.getEl('other_props_tabs_content');
				var navWrap = this.getEl('other_props_tabs_wrap');
				var tab = ev.tabEl;

				// Toggle content state if we're clicking for the first time,
				// or re-clicking a tab
				if (!$('.on', navWrap).length || tab.is('.on')) {
					if (contentWrap.is(':visible')) {
						contentWrap.hide();
						navWrap.removeClass('on');
					} else {
						contentWrap.show();
						navWrap.addClass('on');
					}
				}
			}).bind(this)
		});
		this.ownObject(this.otherTabs);
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');

DeskPRO.Agent.PageFragment.Page.NewDownload = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'newdownload';
		this.allowDupe = true;
	},

	initPage: function(el) {
		var self = this;
		this.wrapper = el;
		this.parent(el);

		if (!this.getEl('cat').find('option')[0]) {
			this.wrapper.find('.form-header-error').show();
			this.wrapper.find('.form-outer').hide();
			this.markForReload();
		}

		this.form = $('form', this.wrapper).on('submit', function(ev) {
			ev.preventDefault();
		});

		$('button.submit-trigger', this.wrapper).on('click', this.submit.bind(this));

		this._initCategorySection();
		this._initTitleSection();
		this._initFileSection();
		this._initContentSection();
		this._initOtherSection();

		this.stateSaver = new DeskPRO.Agent.PageHelper.StateSaver({
			stateId: 'newdownload',
			listenOn: this.getEl('newdownload')
		});
		this.ownObject(this.stateSaver);

		window.setTimeout(function() {
			if (self.OBJ_DESTROYED) return;

			self.wrapper.find('select').each(function() {
				if ($(this).prop('multiple')) {
					$(this).width(300);
				}
				DP.select($(this));
			});
			self.updateUi();
		}, 300);

		this.wrapper.find('.switch-upload-type').on('click', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			if (self.getEl('file_upload_type').hasClass('on')) {
				self.getEl('file_upload_type').removeClass('on').hide();
				self.getEl('file_url_type').addClass('on').show();
			} else {
				self.getEl('file_url_type').removeClass('on').hide();
				self.getEl('file_upload_type').addClass('on').show();
			}
		});

		this.activate();
	},

	activate: function() {
		var selectedCat = $('#publish_outline_downloadscat_list').find('.nav-selected').data('cat-id');
		if (selectedCat) {
			this.getEl('cat').find("option[value=\"" + selectedCat + "\"]").prop('selected', true);
			this.getEl('cat').trigger('change');
		}
	},

	markForReload: function() {
		if (!this.markedForReload) {
			this.markedForReload = true;
			this.addEvent('deactivate', this.closeSelf.bind(this));
		}
	},

	destroyPage: function() {
		// Workaround for tinymce bug to do with remove()
		// We'll manually remove the node ourselves
		var el = this.wrapper.find('.article-section');
		if (el[0]) {
			el.get(0).parentNode.removeChild(el.get(0));
		}
	},

	closeSelf: function() {
		var ev = {cancel: false};
		this.fireEvent('closeSelf', ev);

		if (!ev.cancel) {
			this.parent();
		}
	},

	submit: function() {
		var formData = this.form.serializeArray();
		if (this.labelsInput) {
			formData.append(this.labelsInput.getFormData());
		}

		$('div.error.section', this.wrapper).removeClass('error');
		$('.error-message-on', this.wrapper).removeClass('error-message-on');

		this.stateSaver.stop();
		this.stateSaver.resetState();
		this.wrapper.addClass('loading');

		$.ajax({
			url: BASE_URL + 'agent/downloads/new/save',
			type: 'POST',
			data: formData,
			dataType: 'json',
			context: this,
			complete: function() {
				this.wrapper.removeClass('loading');
			},
			success: function(data) {

				if (data.error) {
					Array.each(data.error_codes, function(code) {
						this.showErrorCode(code);
					}, this);
					this.updateUi();
					return;
				}

				if (data.success) {
					this.markForReload();
					DeskPRO_Window.runPageRoute('page:' + BASE_URL + 'agent/downloads/file/' + data.download_id);
					this.closeSelf();
				} else {
					alert('There was an error with the form');
				}
			}
		});
	},

	showErrorCode: function(code) {
		$('.' + code + '.error-message', this.wrapper).addClass('error-message-on');
	},

	//#################################################################
	//# Category section
	//#################################################################

	_initCategorySection: function() {
		var self = this;

		this.getEl('cat').on('change', function() {
			if (parseInt($(this).val())) {
				self.getEl('cat_section').addClass('done');
			} else {
				self.getEl('cat_section').removeClass('done');
			}
		});
	},

	//#################################################################
	//# Title section
	//#################################################################

	_initTitleSection: function() {
		var self = this;

		var fn = function() {
			if ($(this).val().trim() == '') {
				self.getEl('title_section').removeClass('done');
			} else {
				self.getEl('title_section').addClass('done');
			}
		};

		this.getEl('title').on('change', fn).on('keypress', fn).on('change', function() {
			var val = $(this).val().trim().toLowerCase();
			val = val.replace(/[^a-z0-9\-_]/g, '-');
			val = val.replace(/-{2,}/g, '-');

			self.getEl('slug').val(val);
		});
	},

	//#################################################################
	//# Download section
	//#################################################################

	_initFileSection: function() {

        var self = this;
		var upinput = this.getEl('file_section_up');

		var list = $('.file-list', this.wrapper);
		list.on('click', '.remove-attach-trigger', function() {
			 $('ul.file-list', self.wrapper).empty();
			upinput.show();
			self.updateUi();
		});

		DeskPRO_Window.util.fileupload(this.wrapper, {
			page: this
		});

        this.wrapper.bind('fileuploaddone', function() {
            self.getEl('file_section').addClass('done');
			upinput.hide();
			self.updateUi();
        });
        this.wrapper.bind('fileuploadadd', function() {
            $('ul.file-list', self.wrapper).empty();
        });
	},

	//#################################################################
	//# Content section
	//#################################################################

	_initContentSection: function() {

		var self = this;

		this.getEl('content').css({
			width: this.wrapper.width() - 80
		});

		this.getEl('content').css('height', 250);

		DP.rteTextarea(this.getEl('content'), {
			setup: function(ed) {
				ed.onClick.add(function() {
					self.getEl('content_section').addClass('done');
				});

				ed.onKeyPress.add(function() {
					if (self.stateSaver) {
						self.stateSaver.triggerChange();
					}
				});
			}
		});
	},

	//#########################################################################
	//# Other Section
	//#########################################################################

	_initOtherSection: function() {
		var self = this;
		this.otherTabs = new DeskPRO.UI.SimpleTabs({
			triggerElements: $('li', this.getEl('other_props_tabs')),
			context: this.getEl('other_props_tabs_content'),
			autoSelectFirst: false,
			onTabSwitch: function(eventData) {
				if (!self.labelsInput && eventData.tabContent.hasClass('tab-properties')) {
					self.labelsInput = new DeskPRO.UI.LabelsInput({
						type: 'downloads',
						fieldName: 'newdownload[labels]',
						input: $(".tags-wrap input", eventData.tabContent),
						onChange: function() {
							if (self.stateSaver) {
								self.stateSaver.triggerChange();
							}
						}
					});
					self.ownObject(self.labelsInput);
				}
			},
			onTabClick: (function(ev) {
				var contentWrap = this.getEl('other_props_tabs_content');
				var navWrap = this.getEl('other_props_tabs_wrap');
				var tab = ev.tabEl;

				// Toggle content state if we're clicking for the first time,
				// or re-clicking a tab
				if (!$('.on', navWrap).length || tab.is('.on')) {
					if (contentWrap.is(':visible')) {
						contentWrap.hide();
						navWrap.removeClass('on');
					} else {
						contentWrap.show();
						navWrap.addClass('on');
					}
				}
			}).bind(this)
		});
		this.ownObject(this.otherTabs);
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');

DeskPRO.Agent.PageFragment.Page.NewNews = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'newnews';
		this.allowDupe = true;
	},

	initPage: function(el) {
		var self = this;
		this.wrapper = el;
		this.parent(el);

		if (!this.getEl('cat').find('option')[0]) {
			this.wrapper.find('.form-header-error').show();
			this.wrapper.find('.form-outer').hide();
			this.markForReload();
		}

		this.form = $('form', this.wrapper).on('submit', function(ev) {
			ev.preventDefault();
		});

		$('button.submit-trigger', this.wrapper).on('click', this.submit.bind(this));

		this._initCategorySection();
		this._initTitleSection();
		this._initContentSection();
		this._initOtherSection();

		this.stateSaver = new DeskPRO.Agent.PageHelper.StateSaver({
			stateId: 'c',
			listenOn: this.getEl('newnews')
		});
		this.ownObject(this.stateSaver);

		window.setTimeout(function() {
			if (self.OBJ_DESTROYED) return;

			self.wrapper.find('select').each(function() {
				if ($(this).prop('multiple')) {
					$(this).width(300);
				}
				DP.select($(this));
			});
			self.updateUi();
		}, 300);

		this.activate();
	},

	activate: function() {
		var selectedCat = $('#publish_outline_newscat_list').find('.nav-selected').data('cat-id');
		if (selectedCat) {
			this.getEl('cat').find("option[value=\"" + selectedCat + "\"]").prop('selected', true);
			this.getEl('cat').trigger('change');
		}
	},

	destroyPage: function() {
		// Workaround for tinymce bug to do with remove()
		// We'll manually remove the node ourselves
		var el = this.wrapper.find('.article-section');
		if (el[0]) {
			el.get(0).parentNode.removeChild(el.get(0));
		}
	},

	markForReload: function() {
		if (!this.markedForReload) {
			this.markedForReload = true;
			this.addEvent('deactivate', this.closeSelf.bind(this));
		}
	},

	closeSelf: function() {
		var ev = {cancel: false};
		this.fireEvent('closeSelf', ev);

		if (!ev.cancel) {
			this.parent();
		}
	},

	submit: function() {
		var formData = this.form.serializeArray();
		if (this.labelsInput) {
			formData.append(this.labelsInput.getFormData());
		}

		$('div.error.section', this.wrapper).removeClass('error');
		$('.error-message-on', this.wrapper).removeClass('error-message-on');

		this.stateSaver.stop();
		this.stateSaver.resetState();
		this.wrapper.addClass('loading');

		$.ajax({
			url: BASE_URL + 'agent/news/new/save',
			type: 'POST',
			data: formData,
			dataType: 'json',
			context: this,
			complete: function() {
				this.wrapper.removeClass('loading');
			},
			success: function(data) {
				if (data.error) {
					Array.each(data.error_codes, function(code) {
						this.showErrorCode(code);
					}, this);
					this.updateUi();
					return;
				}

				if (data.news_id) {
					DeskPRO_Window.runPageRoute('page:' + BASE_URL + 'agent/news/post/' + data.news_id);
				}

				this.markForReload();
				this.closeSelf();
			}
		});
	},

	showErrorCode: function(code) {
		$('.' + code + '.error-message', this.wrapper).addClass('error-message-on');
	},

	//#################################################################
	//# Category section
	//#################################################################

	_initCategorySection: function() {
		var self = this;

		this.getEl('cat').on('change', function() {
			if (parseInt($(this).val())) {
				self.getEl('cat_section').addClass('done');
			} else {
				self.getEl('cat_section').removeClass('done');
			}
		});
	},

	//#################################################################
	//# Title section
	//#################################################################

	_initTitleSection: function() {
		var self = this;

		var fn = function() {
			if ($(this).val().trim() == '') {
				self.getEl('title_section').removeClass('done');
			} else {
				self.getEl('title_section').addClass('done');
			}
		};

		this.getEl('title').on('change', fn).on('keypress', fn).on('change', function() {
			var val = $(this).val().trim().toLowerCase();
			val = val.replace(/[^a-z0-9\-_]/g, '-');
			val = val.replace(/-{2,}/g, '-');

			self.getEl('slug').val(val);
		});
	},

	//#################################################################
	//# Content section
	//#################################################################

	_initContentSection: function() {

		var self = this;

		this.getEl('content').css({
			width: this.wrapper.width() - 80
		});

		// Make the size of the message box based off of the height of the window
		var h = $(window).height();
		this.getEl('content').css('height', Math.max(h - 500, 200));

		DP.rteTextarea(this.getEl('content'), {
			setup: function(ed) {
				ed.onClick.add(function() {
					self.getEl('content_section').addClass('done');
				});
				ed.onKeyPress.add(function() {
					if (self.stateSaver) {
						self.stateSaver.triggerChange();
					}
				});
			}
		});
	},

	//#########################################################################
	//# Other Section
	//#########################################################################

	_initOtherSection: function() {
		var self = this;
		this.otherTabs = new DeskPRO.UI.SimpleTabs({
			triggerElements: $('li', this.getEl('other_props_tabs')),
			context: this.getEl('other_props_tabs_content'),
			autoSelectFirst: false,
			onTabSwitch: function(eventData) {
				if (!self.labelsInput && eventData.tabContent.hasClass('tab-properties')) {
					self.labelsInput = new DeskPRO.UI.LabelsInput({
						type: 'news',
						fieldName: 'newnews[labels]',
						input: $(".tags-wrap input", eventData.tabContent),
						onChange: function() {
							if (self.stateSaver) {
								self.stateSaver.triggerChange();
							}
						}
					});
					self.ownObject(self.labelsInput);
				}
			},
			onTabClick: (function(ev) {
				var contentWrap = this.getEl('other_props_tabs_content');
				var navWrap = this.getEl('other_props_tabs_wrap');
				var tab = ev.tabEl;

				// Toggle content state if we're clicking for the first time,
				// or re-clicking a tab
				if (!$('.on', navWrap).length || tab.is('.on')) {
					if (contentWrap.is(':visible')) {
						contentWrap.hide();
						navWrap.removeClass('on');
					} else {
						contentWrap.show();
						navWrap.addClass('on');
					}
				}
			}).bind(this)
		});
		this.ownObject(this.otherTabs);

		this.getEl('slug').on('focus', function() {
			$(this).addClass('had-focus');
		});

		// Attachments
		var list = $('.file-list', this.wrapper);
		$('input', list[0]).live('click', function() {
			var el = $(this);
			var li = el.parent();
			if (el.is(':checked')) {
				li.removeClass('unchecked');
			} else {
				li.addClass('unchecked');
			}
		});

		DeskPRO_Window.util.fileupload(this.wrapper, {
			page: this
		});
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');

DeskPRO.Agent.PageFragment.Page.NewTicket = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'newticket';
		this.allowDupe = true;
	},

	initPage: function(el) {
		var self = this;
		this.wrapper = el;
		this.el = el;
		this.contentWrapper = this.wrapper.children('.layout-content').attr('id', Orb.getUniqueId());
		this.parent(el);

		el.find('select').addClass('with-select2');

		this.form = $('form', this.wrapper).on('submit', function(ev) {
			ev.preventDefault();
		});

		this._initUserSection();
		this._initMessageSection();
		this._initOtherSection();
		this._initCcSelection();

		this.meta.person_api_data = {};

		this.addEvent('activate', function() {
			window.setTimeout(function() {
				if (!self.getEl('user_searchbox').find('input.person-id').val()) {
					self.getEl('userselect').focus();
				}
			}, 60);
		});

		if (this.getEl('headerbox_box_billing').length) {
			var billing = new DeskPRO.Agent.PageHelper.TicketBilling(this.getEl('headerbox_box_billing'), this.meta.baseId, {
				auto_start_bill: this.meta.auto_start_bill
			});
			this.addEvent('activate', function() {
				if (this.meta.auto_start_bill) {
					billing.startBillingTimer(true);
				}
			});
			this.addEvent('deactivate', function() {
				billing.stopBillingTimer(true);
			});
		}

		$('.submit-trigger', this.wrapper).on('click', this.submit.bind(this));

		//------------------------------
		// Upload handling
		//------------------------------

		DeskPRO_Window.util.fileupload(this.wrapper, {
			dropZone: $('.option-rows', this.wrapper),
			uploadTemplate: $('.template-upload', this.wrapper),
			downloadTemplate: $('.template-download', this.wrapper)
		});
		this.wrapper.bind('fileuploaddone', function() {
			self.getEl('attach_row').slideDown().removeClass('is-hidden');
		});
		this.wrapper.bind('fileuploadstart', function() {
			self.getEl('attach_row').slideDown().removeClass('is-hidden');
		});

		this.wrapper.on('click', '.remove-attach-trigger', function() {

			var row = $(this).closest('li');
			row.fadeOut('fast', function() {
				row.remove();

				var rows = $('ul.files li', self.getEl('attach_row'));
				if (!rows.length) {
					self.getEl('attach_row').slideUp().addClass('is-hidden');
				}
			});
        });

		$('.Date.customfield input', this.wrapper).datepicker({
			dateFormat: 'yy-mm-dd',
			showButtonPanel: true,
			beforeShow: function(input) {
				setTimeout(function() {
					var buttonPane = $(input).datepicker("widget").find(".ui-datepicker-buttonpane");

					buttonPane.find('button:first').remove();

					var btn = $('<button class="ui-datepicker-current ui-state-default ui-priority-secondary ui-corner-all" type="button">Clear</button>');
					btn.unbind("click").bind("click", function () { $.datepicker._clearDate( input ); });
					btn.appendTo( buttonPane );

					$(input).datepicker("widget").css('z-index', 30002);
				},1);
			}
		});

		this.wrapper.find('.pending-info').on('click', '.reset', function(ev) {
			ev.preventDefault();
			self._resetForX();
		});

		this.addEvent('deactivate', function() {
			this._resetForX();
		}, this);

		var messageEl = this.getEl('message');
		var subjectEl = this.getEl('subject');
		var appliedMsgTpl = null;
		var sig = $.trim(self.getEl('signature_value').val());

		messageEl.on('keydown', function() {
			messageEl.addClass('editted');
		});
		subjectEl.on('keydown', function() {
			subjectEl.addClass('editted');
		});
		this.getEl('message_template').on('change', function() {
			var id = $(this).val();

			if (appliedMsgTpl == id) {
				return;
			}

			if (!id) {
				if (!messageEl.hasClass('editted')) {
					self.setMessageText('');
				}
				if (!subjectEl.hasClass('editted')) {
					subjectEl.val('');
				}
				return;
			}

			appliedMsgTpl = id;

			$.ajax({
				url: BASE_URL + 'agent/tickets/get-message-template/'+id+'.json',
				type: 'GET',
				cache: false,
				dataType: 'json',
				success: function(data) {
					if (messageEl.hasClass('editted')) {
						var msgCmp = data.message.replace(/(\r\n|\n|\r)/gm, " ");
						var valCmp = messageEl.val().replace(/(\r\n|\n|\r)/gm, " ");
						if (valCmp.indexOf(msgCmp) === -1) {
							self.insertMessageText(data.message);
						}
					} else {
						var val = data.message;

						if (sig) {
							val += "\n\n";
							val += sig;
						}

						self.setMessageText(val);
					}

					if (subjectEl.hasClass('editted')) {
						if (subjectEl.val().indexOf(data.subject) === -1) {
							subjectEl.insertAtCaret(data.subject);
						}
					} else {
						subjectEl.val(data.subject);
					}
				}
			});
		});

		// This is so the select2 box has proper width for the longest template title
		var w = this.getEl('message_template').width() + 55;
		if (w > 350) w = 350;
		this.getEl('message_template').css('width', w);
		this.getEl('message_template_holder').css({
			visibility: 'visible',
			display: 'none'
		});

		window.setTimeout(function() {
			if (self.OBJ_DESTROYED) return;

			self.wrapper.find('select').each(function() {
				if ($(this).prop('multiple')) {
					$(this).width(300);
				}

				DP.select($(this));
			});
			self.updateUi();
		}, 50);

		self.wrapper.find('select').each(function() {
			var len = 0;
			$(this).find('option').each(function() {
				var ol = $(this).text().length;
				if (ol > len) len = ol;
			});
			$(this).width((10 * len) + 25);
		});

		var depSel = this.getEl('dep');

		var ticketReader = {
			getCategoryId: function() {
				var catId = self.getEl('cat').val();
				return parseInt(catId) || 0;
			},
			getPriorityVal: function() {
				var id = this.getPriorityId();
				if (!id) {
					return -999999999;
				}

				if (!window.DESKPRO_TICKET_PRI_MAP || !window.DESKPRO_TICKET_PRI_MAP[id]) {
					return 0;
				}

				return parseInt(window.DESKPRO_TICKET_PRI_MAP[id]);
			},
			getPriorityId: function() {
				var catId = self.getEl('pri').val();
				return parseInt(catId) || 0;
			},
			getProductId: function() {
				var catId = self.getEl('prod').val();
				return parseInt(catId) || 0;
			},
			getOrganizationId: function() {
				return 0;
			},
			getWorkflow: function() {
				var catId = self.getEl('work').val();
				return parseInt(catId) || 0;
			}
		};

		var tplHolder = this.getEl('message_template_holder');
		var tplSel = this.getEl('message_template');
		var tplSelOrig = this.getEl('message_template_orig');

		var fieldDisplayFetch = new DeskPRO.Agent.PageHelper.TicketFieldDisplay(ticketReader, 'create');
		function updateFields() {
			$('.ticket-field', self.getEl('fields_container')).removeClass('item-on').hide();
			var fieldDisplay = fieldDisplayFetch.getFields(depSel.val());

			Object.each(fieldDisplay, function(fields, section) {
				Array.each(fields, function(f) {
					if (f.field_type == 'ticket_field') {
						var classname = 'ticket-field-' + f.field_id;
					} else {
						var classname = f.field_type;
					}

					$('.ticket-field.' + classname, self.wrapper).not('.error-message').detach().appendTo(self.getEl('fields_container')).show().addClass('item-on');
				});
			});

			var depId = depSel.val();
			var opts = tplSelOrig.find('option.department_' + depId +', option.department_0').clone();
			if (opts[0]) {

				var selected = tplSel.val();

				tplSel.empty();
				tplSel.append('<option value="0" selected="selected">Blank</option>');
				tplSel.append(opts);
				tplHolder.show();
				self.getEl('message_template_holder_row').show();

				var selectedOpt = tplSel.find('[value="'+selected+'"]');

				if (selectedOpt[0]) {
					tplSel.select2('val', selected);
				} else {
					tplSel.select2('val', 0);
					tplSel.change();
				}
			} else {
				tplSel.empty();
				tplHolder.hide();
				self.getEl('message_template_holder_row').hide();
			}

			self.getEl('fields_container').find('tbody').removeClass('last').filter(':visible').last().addClass('last');

			self.updateUi();
		};

		depSel.on('change', function(ev) {
			updateFields();
		});

		$('.ticket-field select', this.wrapper).on('change', function() {
			updateFields();
		});

		updateFields();

		//------------------------------
		// Status menu
		//------------------------------

		var statusMenuTrigger = this.el.find('.status-menu-trigger');
		var footerEl = this.getEl('message_footer');
		var statusMenu = this.getEl('status_menu');
		statusMenu.css('z-index', 999999);
		var statusMenuH = null;
		var statusBackdrop = null;
		var statusMacroFilter = null;
		var statusMacroList = statusMenu.find('.macro-list');
		var statusListItems = null;
		var replyAsType = this.getEl('reply_as_type');

		var closeStatusMenu = function() {
			statusBackdrop.hide();
			statusMenu.hide();
		};

		var updateStatusPos = function() {
			statusMenuH = statusMenu.height();
			if (statusMenu > 500) {
				statusMenu.find('macro-list').css('max-height', 500).css('overflow', 'auto');
				statusMenuH = 500;
			}

			var pos = footerEl.offset();
			statusMenu.css({
				left: pos.left + 6,
				top: pos.top - statusMenuH + 3
			});
		};

		var openStatusMenu = function() {
			statusListItems = statusMenu.find('li[data-type]').not('.off');

			// Means we're opening fo rhte first time
			if (!statusBackdrop) {
				statusBackdrop = $('<div class="backdrop"></div>');
				statusBackdrop.css('z-index', 999998);
				statusBackdrop.appendTo('body');
				statusBackdrop.on('click', function(ev) {
					ev.stopPropagation();
					closeStatusMenu();
				});
				statusMenu.detach().appendTo('body');

				// Handle macro filtering
				statusMacroFilter = statusMenu.find('.macro-filter');

				statusMenu.on('click', 'li[data-type]', function(ev) {
					ev.stopPropagation();
					self.setReplyAsOption($(this));
					closeStatusMenu();
				});

				statusMacroFilter.on('keyup', function(ev) {

					var isCtrl = false;
					if (ev.ctrlKey && DeskPRO_Window.keyboardShortcuts.isMac) {
						isCtrl = true;
					} else if (ev.altKey) {
						isCtrl = true;
					}
					if (isCtrl) {
						if (isCtrl && (ev.which == 85)) {
							closeStatusMenu();
							self.setReplyAsOptionName('awaiting_user');
							return;
						}
						if (isCtrl && (ev.which == 65)) {
							closeStatusMenu();
							self.setReplyAsOptionName('awaiting_agent');
							return;
						}
						if (isCtrl && (ev.which == 68)) {
							closeStatusMenu();
							self.setReplyAsOptionName('resolved');
							return;
						}
					}

					if (ev.keyCode == 13 /* enter key */) {
						ev.preventDefault();
						var current = statusListItems.filter('.cursor');
						if (current[0]) {
							self.setReplyAsOption(current);
							closeStatusMenu();
						}
					} else if (ev.keyCode == 27 /* escape key */) {
						ev.preventDefault();
						closeStatusMenu();
					} else if (ev.keyCode == 40 /* down key */ || ev.keyCode == 38 /* up key */) {
						ev.preventDefault();
						var dir = ev.keyCode == 40 ? 'down' : 'up';

						var current = statusListItems.filter('.cursor');
						if (!current.length) {
							if (dir == 'down') {
								statusListItems.first().addClass('cursor');
							} else {
								statusListItems.last().addClass('cursor');
							}
						} else {
							var nextIndex = statusListItems.index(current);
							if (dir == 'down') {
								nextIndex++;
							} else {
								nextIndex--;
							}

							if (nextIndex < 0) {
								nextIndex = statusListItems.length-1;
							} else if (nextIndex > (statusListItems.length-1)) {
								nextIndex = 0;
							}

							current.removeClass('cursor');
							statusListItems.eq(nextIndex).addClass('cursor');
						}
					}
				});

				statusMacroFilter.on('keyup', function() {
					var val = $.trim($(this).val());

					if (!val) {
						statusMacroList.find('li').show().removeClass('off');
						updateStatusPos();
					} else {
						val = val.toLowerCase();
						statusMacroList.find('li').each(function() {
							if ($(this).text().toLowerCase().indexOf(val) !== -1) {
								$(this).show().removeClass('off');
							} else {
								$(this).hide().addClass('off');
							}
						});
						updateStatusPos();
					}

					statusListItems = statusMenu.find('li[data-type]').not('.off');
					if (!statusListItems.filter('.cursor')[0]) {
						statusMenu.find('li.cursor').removeClass('cursor');
						statusListItems.first().addClass('cursor');
					}
				});
			}

			// Pre-select proper value
			var type = replyAsType.data('type');
			statusMenu.find('li').removeClass('cursor')
				.filter('[data-type]').removeClass('on')
				.filter('[data-type="' + type + '"]').addClass('on');

			var w = self.getEl('reply_btn_group').width() - 3;
			if (w < 200) {
				w = 200;
			}
			statusMenu.width(w);

			statusBackdrop.show();
			updateStatusPos();
			statusMenu.show();

			statusMacroFilter.focus();
		};

		this.openStatusMenu = openStatusMenu;

		statusMenuTrigger.on('click', function(ev) {
			ev.preventDefault();
			openStatusMenu();
		});

		$('#settingswin').on('dp_macros_updated', function(ev) {
			Array.each(ev.macroItems, function(info) {
				var has = statusMacroList.find('.res-ticketmacro-' + info.id);
				if (has[0]) {
					return;
				}

				var li = $('<li><div class="on-icon"><i class="icon-okay"></i></div><span class="macro-title"></span></li>');
				li.data('get-macro-url', BASE_URL + 'agent/tickets/0/ajax-get-macro?macro_id=' + info.id + '&macro_reply_context=1');
				li.data('label', 'Send Reply and ' + info.title);
				li.data('type', 'macro:'+info.id);
				li.attr('data-type', 'macro:'+info.id);
				li.find('.macro-title').text(info.title);

				statusMacroList.append(li);
			});
		});

		this.el.find('.expander').on('click', function() {
			var target = self.el.find($(this).data('target'));
			if (target.is(':visible')) {
				$(this).removeClass('expanded').addClass('is-hidden');
				target.slideUp('fast');
			} else {
				$(this).addClass('expanded').removeClass('is-hidden');
				target.slideDown('fast');
			}
		});
	},

	setReplyAsOptionName: function(name) {
		var item = this.getEl('status_menu').find('li[data-type="' + name + '"]').first();
		if (item[0]) {
			this.setReplyAsOption(item);
		}
	},

	setReplyAsOption: function(item) {
		var replyAsType = this.getEl('reply_as_type');

		var html = Orb.escapeHtml(item.data('label'));
		html = html.replace(/^Send Reply/, 'Send <span class="show-key-shortcut">R</span>eply');
		replyAsType.data('type', item.data('type')).html(html);

		var macroUrl = item.data('get-macro-url');

		var textarea = this.textarea;
		var api = this.textarea.data('redactor');

		if (!macroUrl) {
			this.getEl('actions_row').hide();
			this.updateUi();
			this.wrapper.find('div.layout-content').trigger('goscrollbottom');
		} else {
			var actionsRow = this.getEl('actions_row');
			var actionsRowList = actionsRow.find('ul');
			actionsRowList.empty();
			actionsRowList.append('<li class="load"><i class="flat-spinner"></i></li>');

			actionsRow.show();

			this.updateUi();
			this.wrapper.find('div.layout-content').trigger('goscrollbottom');

			$.ajax({
				url: macroUrl,
				type: 'GET',
				context: this,
				dataType: 'json',
				success: function(data) {
					actionsRowList.empty();
					Array.each(data.descriptions, function(desc) {
						var li = $('<li />');
						li.html(desc);

						actionsRowList.append(li);
					});

					// There's a snippet reply point
					var sig = null;
					if (api) {
						sig = api.$editor.find('.dp-signature-start');
						if (!sig[0]) {
							sig = null;
						}
					}

					actionsRowList.find('.with-reply, .with-snippet').each(function() {
						var pos = $(this).data('reply-pos');
						var html = $(this).find('.reply-text').get(0).innerHTML;

						if (pos) {
							if (api) {
								if (pos == 'overwrite') {
									api.$editor.html(html);
									if (sig) {
										api.$editor.append(sig);
									}
								} else if (pos == 'prepend') {
									api.$editor.prepend(html);
								} else {
									if (sig) {
										var usesig = sig;
										var prev = sig.prev();
										if (prev[0] && prev.is('p') && $.trim(prev.text()) === '') {
											usesig = prev;
											var prev2 = prev.prev();
											if (prev2[0] && prev2.is('p') && $.trim(prev2.text()) === '') {
												prev2.remove()
											}
										}
										usesig.before(html);
									} else {
										api.$editor.append(html);
									}
								}

								api.syncCode();
							} else {
								var text = $('<div>' + html + '</div>');
								text = text.text().trim();
								textarea.val($.trim(textarea.val() + "\n\n" + text));
							}
						}
					});

					var agentId = parseInt(actionsRowList.find('.with-agent').data('agent-id'));
					if (agentId) {
						if (agentId == -1) {
							agentId = DESKPRO_PERSON_ID;
						}

						this.getEl('agent_sel').select2('val', agentId);
					}
					var agentTeamId = parseInt(actionsRowList.find('.with-agent-team').data('agent-team-id'));
					if (agentTeamId) {
						if (agentTeamId == -1) {
							if (!window.DESKPRO_TEAM_IDS || !window.DESKPRO_TEAM_IDS.length) {
								agentTeamId = null;
							} else {
								agentTeamId = window.DESKPRO_TEAM_IDS[0];
							}
						}

						if (agentTeamId) {
							this.getEl('agent_team_sel').select2('val', agentTeamId);
						}
					}

					if (actionsRowList.find('.with-close-tab')) {
						this.getEl('close_tab_opt').prop('checked', true);
					}

					var setSubject = actionsRowList.find('.with-set-subject').text().trim();
					if (setSubject) {
						this.getEl('subject').val(setSubject);
					}

					this.updateUi();
					this.wrapper.find('div.layout-content').trigger('goscrollbottom');
				}
			});
		}
	},

	markForReload: function() {
		if (!this.markedForReload) {
			this.markedForReload = true;
			this.addEvent('deactivate', this.closeSelf.bind(this));
		}
	},

	closeSelf: function() {
		var ev = {cancel: false};
		this.fireEvent('closeSelf', ev);

		if (!ev.cancel) {
			this.parent();
		}
	},

	submit: function() {

		if (this.pauseSend) {
			window.setTimeout(this.submit.bind(this), 250);
		}

		this.getEl('action').val(this.getEl('reply_as_type').data('type'));
		var formData = this.form.serializeArray();

		$('div.error.section', this.wrapper).removeClass('error');
		$('.error-message-on', this.wrapper).removeClass('error-message-on').hide();
		this.getEl('error_section').hide();

		this.wrapper.parent().addClass('loading');
		this.getEl('send_btn').hide();
		this.getEl('send_loading').show();

		$.ajax({
			url: BASE_URL + 'agent/tickets/new/save',
			type: 'POST',
			data: formData,
			dataType: 'json',
			context: this,
			complete: function() {
				this.wrapper.parent().removeClass('loading');
				this.getEl('send_btn').show();
				this.getEl('send_loading').hide();
			},
			success: function(data) {
				if (data.error) {
					if (data.is_dupe) {
						DeskPRO_Window.showConfirm('The ticket you tried to submit is an exact duplicate of an existing ticket. This new ticket was not saved.', function() {
							DeskPRO_Window.runPageRoute('ticket:' + BASE_URL + 'agent/tickets/' + data.dupe_ticket_id)
						}, function() {}, 'View Existing Ticket', 'hidden');
					} else {
						Array.each(data.error_codes, function(code) {
							this.showErrorCode(code);
						}, this);

						if (data.error_messages) {
							this.showErrorCode('free');
							var free = $('<div/>');
							Array.each(data.error_messages, function(msg) {
								var x = $('<div/>');
								x.text('- ' + msg);
								free.append(x);
							});
							this.getEl('freemessage').html(free.html());
						}

						this.updateUi();
					}
				}

				if (data.ticket_id) {
					if (data.comment_id) {
						DeskPRO_Window.getMessageBroker().sendMessage('agent-ui.comment-remove', {
							comment_id: data.comment_id,
							comment_type: data.comment_type
						});
					}

					if (data.can_view && this.getEl('opt_open_tab').is(':checked')) {
						DeskPRO_Window.runPageRoute('ticket:' + BASE_URL + 'agent/tickets/' + data.ticket_id);
					}
					this.closeSelf();
				}
			}
		});
	},

	showErrorCode: function(code) {
		$('.' + code + '.error-message', this.wrapper).addClass('error-message-on').show();
		switch (code) {
			case 'person_id':
			case 'person_no_user':
			case 'person_email_address':
				$('div.user-section.section', this.wrapper).addClass('error');
				break;

			case 'subject':
				$('div.subject-section.section', this.wrapper).addClass('error');
				break;

			case 'message':
				$('div.message-section.section', this.wrapper).addClass('error');
				break;
		}
		this.getEl('error_section').show();
		this.updateUi();
	},

	clearErrorCode: function(code) {
		$('.' + code + '.error-message', this.wrapper).removeClass('error-message-on').hide();
		switch (code) {
			case 'person_id':
			case 'person_no_user':
			case 'person_email_address':
				$('div.user-section.section', this.wrapper).removeClass('error');
				break;

			case 'subject':
				$('div.subject-section.section', this.wrapper).removeClass('error');
				break;

			case 'message':
				$('div.message-section.section', this.wrapper).removeClass('error');
				break;
		}

		if (this.getEl('error_section').find('.error-message-on')[0]) {
			this.getEl('error_section').show();
		} else {
			this.getEl('error_section').hide();
		}

		this.updateUi();
	},

	updateUi: function() {
		var x;
		if (!this.IS_ACTIVE) {
			return;
		}
		if (this.wrapper) {
			if (!this.scrollHandlers) {
				this.scrollHandlers = this.wrapper.find('div.with-scroll-handler');
			}
			for (x = 0; x < this.scrollHandlers.length; x++) {
				var sh = $(this.scrollHandlers[x]).data('scroll_handler');
				if (sh && sh.updateSize) {
					sh.updateSize();
				}
			};

			if (this.doScrollBottom) {
				this.wrapper.find('div.layout-content').trigger('goscrollbottom_stick');
				this.doScrollBottom = false;
			}
		}

		this.fireEvent('updateUi');
	},

	insertMessageText: function(content) {
		var textarea = this.getEl('message');

		if (textarea.data('redactor')) {
			textarea.data('redactor').insertHtml(DP.convertTextToWysiwygHtml(content, true));
		} else {
			var pos = textarea.getCaretPosition();
			if (!pos) {
				textarea.setCaretPosition(0);
			}

			textarea.insertAtCaret(content);
			textarea.trigger('textareaexpander_fire');
		}
	},

	setMessageText: function(content, is_quote) {
		var textarea = this.getEl('message');

		if (is_quote) {
			content = "> " + content.replace(/\r\n|\n/, "\n> ");
		}

		if (textarea.data('redactor')) {
			content = DP.convertTextToWysiwygHtml(content, true);
			if (is_quote) {
				content = "<br/><br/><blockquote>" + content + '</blockquote>';
			}
			textarea.setCode(content);
		} else {
			if (is_quote) {
				content = "\n\n" + content;
			}
			textarea.val(content);
			textarea.trigger('textareaexpander_fire');
		}
	},

	setNewByComment: function(data) {

		this.setMessageText(data.name + " <" + data.email + "> wrote:\n" + data.message, true);
		this.getEl('for_comment_type').val(data.content_type);
		this.getEl('for_comment_id').val(data.comment_id);
		$('.pending-info.comment', this.wrapper).show();

		this.getEl('comment_title').text(data.name + " (" + data.email + ")");
		this.getEl('comment_object_link').data('route', 'page:' + data.object_url).text(data.object_title);

		this.getEl('user_searchbox').find('input.person-id').val(data.person_id);
		this.getEl('usersearch').val(data.email_address);

		this.getEl('user_section').hide();
		this.getEl('choose_user').hide();
		this.getEl('user_searchbox').find('input.person-id').val(data.person_id);

		this.setUser(data.person_id);

		if (data.status == 'validating') {
			$('option[value="approve"]', this.getEl('comment_action')).hide();
		} else {
			$('option[value="approve"]', this.getEl('comment_action')).show();
		}

		this.updateUi();
	},

	setNewByChat: function(data) {
		var self = this;
		this.getEl('for_chat_id').val(data.chat_id);
		this.getEl('chat_title').text(data.chat_title);
		$('.pending-info.chat', this.wrapper).show();

		if (data.person_id) {
			this.setUser(data.person_id, data.session_id);
			this.getEl('user_searchbox').find('input.person-id').val(data.person_id);
			this.getEl('user_section').hide();
			this.getEl('choose_user').hide();
		} else {
			$.ajax({
				type: 'GET',
				url: BASE_URL + 'agent/tickets/new/get-person-row/0',
				data: { 'email': data.email },
				dataType: 'html',
				context: this,
				success: function(html) {
					self.placeUserRow(html);
					self.updateUi();
				}
			});
		}

		this.updateUi();
	},

	setNewByPerson: function(data) {
		this.getEl('user_searchbox').find('input.person-id').val(data.person_id);
		this.getEl('choose_user').hide();
		this.getEl('user_section').show();
		this.getEl('user_choice').show().html('<div style="padding:10px;"><div class="loading-icon-big"></div></div>');

		this.setUser(data.person_id);
		this.updateUi();
	},

	_resetForX: function() {
		this.wrapper.find('.pending-info').hide();
		this.getEl('user_section').show();
		this.getEl('choose_user').show();

		this.getEl('for_chat_id').val('');
		this.getEl('for_comment_type').val('');
		this.getEl('for_comment_id').val('');

		this.updateUi();
	},

	//#########################################################################
	//# User Section
	//#########################################################################

	_initUserSection: function() {
		var self = this;
		var searchbox = this.getEl('user_searchbox');
		var userfields = this.getEl('user_choice');
		var rechooseBtn = this.getEl('switch_user');

		rechooseBtn.on('click', function(ev) {
			ev.preventDefault(); // default would be submitting the ticket form
			showUserChoice();
		});

		var showUserChoice = function() {
			userfields.empty();
			userfields.hide();
			searchbox.show();
			self.getEl('choose_user').show();
			rechooseBtn.hide();
			self.loadSnippetsViewer();
			self.updateUi();
		};

		var placeUserRow = function(html) {
			self.placeUserRow(html);
		};

		searchbox.bind('personsearchboxclick', function(ev, personId, name, email, sb) {
			$.ajax({
				type: 'GET',
				url: BASE_URL + 'agent/tickets/new/get-person-row/' + personId,
				dataType: 'html',
				context: this,
				success: function(html) {
					self.clearErrorCode('person_id');
					self.clearErrorCode('person_email_address');
					self.clearErrorCode('person_no_user');

					$('input.person-id', searchbox).val(personId);
					placeUserRow(html);
					self.loadSnippetsViewer();
					self.updateUi();
				}
			});
			sb.close();
			sb.reset();
		});
		searchbox.bind('personsearchboxclicknew personsearchenter', function(ev, term, sb) {
			$.ajax({
				type: 'GET',
				url: BASE_URL + 'agent/tickets/new/get-person-row/0',
				data: { 'email': term },
				dataType: 'html',
				context: this,
				success: function(html) {
					placeUserRow(html);

					if (term.indexOf('@') !== -1) {
						$('input.email', userfields).val(term);
					} else {
						$('input.name', userfields).val(term);
					}

					var personId = self.getEl('user_choice').find('.set_userid').val();

					if (personId) {
						self.clearErrorCode('person_id');
						self.clearErrorCode('person_email_address');
						self.clearErrorCode('person_no_user');

						$('input.person-id', self.getEl('user_searchbox')).val(personId);
						self.loadSnippetsViewer();
					}

					self.updateUi();
				}
			});
			sb.close();
			sb.reset();
		});
	},

	setUser: function(person_id, session_id) {
		var self = this;
		$.ajax({
			type: 'GET',
			url: BASE_URL + 'agent/tickets/new/get-person-row/0',
			data: { 'person_id': person_id, 'session_id': session_id },
			dataType: 'html',
			context: this,
			success: function(html) {
				this.placeUserRow(html);
			}
		});
	},

	placeUserRow: function(html) {
		var self = this;
		var searchbox = this.getEl('user_searchbox');
		var userfields = this.getEl('user_choice');
		var rechooseBtn = this.getEl('switch_user');

		userfields.empty();
		userfields.html(html);

		self.getEl('choose_user').hide();
		rechooseBtn.show();
		searchbox.hide();
		userfields.show();

		var apiData = userfields.find('.api_data');
		this.meta.person_api_data = {};
		if (apiData[0]) {
			try {
				this.meta.person_api_data = $.parseJSON(apiData.val());
			} catch (e) {}
		}

		var e = $('input.email', userfields);
		if (e && e[0]) {
			var fnCheck = function() {
				if (e.val() && e.val().indexOf('@') !== -1) {
					self.clearErrorCode('person_email_address');
					self.clearErrorCode('person_no_user');
				}
			}
			fnCheck();
			e.on('change', fnCheck);
		}
		var e = $('input.set_person_id', userfields);
		if (e[0]) {
			var person_id = e.val();
			this.getEl('user_searchbox').find('input.person-id').val(person_id);
		}

		this.updateUi();
	},

	//#########################################################################
	//# CC Selection
	//#########################################################################

	_initCcSelection: function() {
		var self = this;
		var ccbox = this.getEl('user_ccbox');

		ccbox.bind('personsearchboxclick', function(ev, personId, name, email, sb) {
			$.ajax({
				type: 'GET',
				url: BASE_URL + 'agent/people/' + personId + '/basic.json',
				dataType: 'json',
				context: this,
				success: function(data) {
					var html = [];
					html.push('<li>');
						html.push('<em class="remove"></em>');
						html.push('<a data-route="page:'+data.url+'">' + data.contact_name + '</a>');
						html.push('<input type="hidden" name="newticket[add_cc_person][]" value="'+personId+'" />');
					html.push('</li>');

					html = html.join('');
					self.getEl('cc_list').append(html);
					self.updateUi();
				}
			});
			sb.close();
			sb.reset();
		});
		ccbox.bind('personsearchboxclicknew personsearchenter', function(ev, term, sb) {

			var rowid = Orb.uuid();

			var html = [];
			html.push('<li>');
				html.push('<em class="remove"></em>');
				html.push('<input type="text" class="name" name="newticket[add_cc_newperson]['+rowid+'][name]" placeholder="Enter a full name" />');
				html.push('<input type="text" class="email" name="newticket[add_cc_newperson]['+rowid+'][email]" placeholder="Enter an email address" />');
			html.push('</li>');

			html = $(html.join(''));

			if (term.indexOf('@') !== -1) {
				$('input.email', html).val(term);
			} else {
				$('input.name', html).val(term);
			}

			self.getEl('cc_list').append(html);
			self.updateUi();

			sb.close();
			sb.reset();
		});

		this.getEl('cc_list').on('click', 'em.remove', function() {
			$(this).closest('li').remove();
			self.updateUi();
		});
	},

	//#########################################################################
	//# Message Section
	//#########################################################################

	_initMessageSection: function() {
		var self = this;
		this.getEl('text_snippets_btn').on('click', function(ev) {
			ev.preventDefault();
			self.openSnippetsViewer();
		});

		this.loadSnippetsViewer();

		var textarea = this.getEl('message');
		this.textarea = textarea;

		if (DeskPRO_Window.canUseAgentReplyRte()) {
			var sig = this.getEl('signature_value_html').val() || "";
			sig = sig.replace(/<div class="dp-signature-start">([\w\W]*)<\/div>/, '<p class="dp-signature-start">$1</p>');
			if (sig && parseInt(this.getEl('parent_ticket_id').val()) === 0) {
				textarea.val(($.browser.msie ? '<p></p><p></p>' : '<p><br></p><p><br></p>') + '\n\n' + sig);
			}

			DeskPRO_Window.initRteAgentReply(textarea, {
				defaultIsHtml: true,
				inlineHiddenPosition: this.getEl('is_html_reply'),
				callback: function(obj) {
					obj.addBtnFirst('dp_attach', 'Click here to attach a file. You may also drag a file from your computer desktop into this reply area to upload attachments faster.', function(){});
					obj.addBtnAfter('dp_attach', 'dp_snippets', 'Open snippets', function(){
						self.openSnippetsViewer();
					});
					obj.addBtnSeparatorAfter('dp_attach');
					obj.addBtnSeparatorAfter('dp_snippets');

					var snippetBtn = obj.$toolbar.find('.redactor_btn_dp_snippets').closest('li');
					snippetBtn.addClass('snippets').find('a').html('<span class="show-key-shortcut">S</span>nippets');

					var attachBtn = obj.$toolbar.find('.redactor_btn_dp_attach').closest('li');
					attachBtn.addClass('attach');
					attachBtn.find('a').text('Attach').append('<input type="file" class="file" name="file-upload" />');
				}
			});
			this.getEl('is_html_reply').val(1);

			var ed = textarea.getEditor();
			var api = textarea.data('redactor');
			var lastH = ed.height();
			ed.on('keyup', function(ev) {
				var isCtrl = false;
				if (ev.ctrlKey && DeskPRO_Window.keyboardShortcuts.isMac) {
					isCtrl = true;
				} else if (ev.altKey) {
					isCtrl = true;
				}

				if (isCtrl) {
					if (isCtrl && (ev.which == 85)) {
						ev.preventDefault();
						self.shortcutReplySetAwaitingUser();
						return;
					}
					if (isCtrl && (ev.which == 65)) {
						ev.preventDefault();
						self.shortcutReplySetAwaitingAgent();
						return;
					}
					if (isCtrl && (ev.which == 68)) {
						ev.preventDefault();
						self.shortcutReplySetResolved();
						return;
					}
					if (isCtrl && (ev.which == 82)) {
						ev.preventDefault();
						self.shortcutSendReply();
						return;
					}
					if (isCtrl && (ev.which == 83)) {
						ev.preventDefault();
						window.setTimeout(function() {
							self.shortcutOpenSnippets();
						}, 10);
						return;
					}
					if (isCtrl && (ev.which == 79)) {
						ev.preventDefault();
						window.setTimeout(function() {
							self.shortcutReplyOpenProperties();
						}, 10);
						return;
					}
				}
			});
			ed.on('keypress change', function() {
				textarea.addClass('touched');

				if (lastH != ed.height()) {
					lastH = ed.height();
					self.doScrollBottom = true;
					window.setTimeout(function() {
						if (self.page) {
							self.page.updateUi();
						}
					}, 50);
				}
			});

			var te = new DeskPRO.TextExpander({
				textarea: ed,
				onCombo: function(combo, ev) {
					combo = combo.replace(/%/g, '');
					if (window.DESKPRO_TICKET_SNIPPET_SHORTCODES && window.DESKPRO_TICKET_SNIPPET_SHORTCODES[combo]) {
						ev.preventDefault();

						var snippetId = window.DESKPRO_TICKET_SNIPPET_SHORTCODES[combo];

						var focus = api.getFocus(),
							focusNode = $(focus[0]),
							testText;

						if (focus[0].nodeType == 3) {
							testText = focusNode.text().substring(0, focus[1]);
						} else {
							focus[0] = focusNode.contents().get(focus[1] - 1);
							focusNode = $(focus[0]);
							testText = focusNode.text();
							focus[1] = testText.length;
						}

						var	lastAt = testText.lastIndexOf('%'), matches = [];

						if (lastAt != -1) {
							api.setSelection(focus[0], lastAt, focus[0], focus[1]);
						}

						// web kit handles content editable without an issue. this prevents the span
						// from being extended unnecessarily
						var editable = $.browser.webkit ? ' contenteditable="false"' : '';
						api.insertHtml('<span class="editor-inserting-var snippet-'+snippetId+'" ' + editable + ' data-snippet-id="' + snippetId + '">Inserting snippet...</span>');

						var personId = self.getEl('user_searchbox').find('input.person-id').val() || 0;
						self.pauseSend = true
						$.ajax({
							url: BASE_URL + 'agent/tickets/0/get-snippet/' + snippetId,
							dataType: 'text',
							data: {person_id: personId},
							complete: function() {
								self.pauseSend = false;
							},
							success: function(data) {
								var el = api.$editor.find('.editor-inserting-var.snippet-' + snippetId);
								data = $('<div>' + data + '</div>');

								// trailing newlines
								var coll = data.find('> br');
								coll.last().remove();

								var cursor = $('<span class="_cursor"></span>');
								var cursorPos = data.find('> p');
								if (!cursorPos[0]) {
									cursorPos = data;
								}

								el.after(data);
								cursorPos.append(cursor);
								el.remove();

								var next = data.next();
								if (next.is('br')) {
									next.remove();
								}
								if (cursor.next().is('br')) {
									cursor.next().remove();
								}
								if (cursor.prev().is('br')) {
									cursor.prev().remove();
								}
								api.setSelection(cursor[0], 0, cursor[0], 0);
								api.syncCode();
							}
						});
					}
				}
			});
		} else {
			var sig = this.getEl('signature_value').val();
			if (sig) {
				textarea.val('\n\n' + sig);
			}

			textarea.css('height', 100);

			textarea.TextAreaExpander(150, 1000).on('textareaexpander_expanded', function() {
				self.updateUi();
				window.setTimeout(function() {
					if (self.wrapper) {
						self.wrapper.find('div.layout-content').trigger('goscrollbottom_stick');
					}
				}, 250);
			});
		}
	},

	loadSnippetsViewer: function() {
		var self = this;
		if (this.snippetsViewer) {
			this.snippetsViewer.destroy();
		}

		var self = this;

		this.snippetsViewer = new DeskPRO.Agent.Widget.SnippetViewer({
			positionMode: this.meta.isPopover ? 'over' : 'side',
			onBeforeOpen: function() {
				var redactor = self.getEl('message').data('redactor');
				if (redactor) {
					redactor.saveSelection();
				}
			},
			onSnippetClick: function(info) {

				var ticketLangId = self.getEl('value_form').find('.language_id').val();
				if (!ticketLangId) {
					ticketLangId = info.language_id == DESKPRO_DEFAULT_LANG_ID;
				}
				var snippetId    = info.snippetId;
				var snippetCode  = info.snippetCode;

				var agentText;
				var defaultText;
				var wantText;
				var useText;
				var result;

				Array.each(snippetCode, function(info) {
					if (info.value) {
						if (info.language_id == ticketLangId) {
							wantText = info.value;
						}
						if (info.language_id == DESKPRO_PERSON_LANG_ID) {
							agentText = info.value;
						}
						if (info.language_id == DESKPRO_DEFAULT_LANG_ID) {
							defaultText = info.value;
						}
						useText = info.value;
					}
				});

				if (wantText) {
					useText = wantText;
				} else if (agentText) {
					useText = agentText;
				} else if (defaultText) {
					useText = defaultText;
				}

				try {
					var tpl = twig({
						data: useText,
						strict_variables: true
					});
					result = tpl.render({
						ticket: {
							person: self.meta.person_api_data
						}
					}, {
						strict_variables: true
					});
				} catch(e) {
					console.log("Snippet render failed: %o", e);
					result = useText;
				}

				var redactor = self.getEl('message').data('redactor');
				if (redactor) {
					var html = result;
					html = html.replace(/<\/p>\s*<p>/g, '<br/>');
					html = html.replace(/^<p>/, '');
					html = html.replace(/<\/p>$/, '');

					redactor.restoreSelection();
					redactor.insertHtml(html);
				} else {
					self.insertMessageText(result);
				}
			}
		});
	},

	openSnippetsViewer: function() {
		this.snippetsViewer.open();
	},

	//#########################################################################
	//# Other Section
	//#########################################################################

	_initOtherSection: function() {
		var self = this;

		this.otherTabs = new DeskPRO.UI.SimpleTabs({
			triggerElements: $('li', this.getEl('other_props_tabs')),
			context: this.getEl('other_props_tabs_content'),
			autoSelectFirst: false,
			onTabSwitch: function(eventData) {
				if (!self.labelsInput && eventData.tabContent.hasClass('tab-properties')) {
					self.labelsInput = new DeskPRO.UI.LabelsInput({
						type: 'tickets',
						textarea: $(".ticket-tags input", eventData.tabContent)
					});
					self.ownObject(self.labelsInput);
				}

				self.updateUi();
			},
			onTabClick: (function(ev) {
				var contentWrap = this.getEl('other_props_tabs_content');
				var navWrap = this.getEl('other_props_tabs_wrap');
				var tab = ev.tabEl;

				// Toggle content state if we're clicking for the first time,
				// or re-clicking a tab
				if (!$('.on', navWrap).length || tab.is('.on')) {
					if (contentWrap.is(':visible')) {
						contentWrap.hide();
						navWrap.removeClass('on');
					} else {
						contentWrap.show();
						navWrap.addClass('on');
					}
				}

				self.updateUi();
			}).bind(this)
		});
		this.ownObject(this.otherTabs);

		// Add CC's
		var self = this;
		$('.add-cc-trigger', this.wrapper).on('click', function() {
			var txt = self.getEl('add_cc_txt');
			var val = txt.val();
			var el = $('<li>' + val + '<input type="hidden" name="newticket[new_parts][]" value="'+val+'" />&nbsp;&nbsp;<span class="remove-trigger" style="cursor: pointer;">x</span></li>');

			$('.remove-trigger', el).on('click', function(ev) {
				ev.preventDefault();
				ev.stopPropagation();
				el.remove();
				self.updateUi();
			});

			el.appendTo(self.getEl('cc_list'));
			self.updateUi();

			txt.val('');
		});

		// Attachments
		var list = $('.file-list', this.wrapper);
		$('input', list[0]).live('click', function() {
			var el = $(this);
			var li = el.parent();
			if (el.is(':checked')) {
				li.removeClass('unchecked');
			} else {
				li.addClass('unchecked');
			}
			self.updateUi();
		});
	},

	focusOnReply: function() {
		var txt = this.textarea;

		if (txt.data('redactor')) {
			var first = !txt.hasClass('touched');
			txt.setFocus();

			if (first) {
				var cursor = txt.data('redactor').$editor.find('> *').first();
				txt.data('redactor').setSelection(cursor[0], 0, cursor[0], 0);
			}
		} else {
			txt.focus();
		}
	},

	shortcutOpenSnippets: function() {
		this.openSnippetsViewer();
	},

	shortcutSendReply: function() {
		this.submit();
	},

	shortcutReplySetAwaitingUser: function() {
		this.setReplyAsOptionName('awaiting_user');
	},

	shortcutReplySetAwaitingAgent: function() {
		this.setReplyAsOptionName('awaiting_agent');
	},

	shortcutReplySetResolved: function() {
		this.setReplyAsOptionName('resolved');
	},

	shortcutReplyOpenProperties: function() {
		this.openStatusMenu();
	}
});
Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');

DeskPRO.Agent.PageFragment.Page.PublishNewCat = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'pubnewcat';
		this.allowDupe = true;
	},

	closeSelf: function() {
		this.fragmentOverlay.close();
	},

	initPage: function(el) {
		var self = this;
		this.wrapper = el;
		this.el = el;
		this.contentWrapper = this.wrapper.children('.layout-content').attr('id', Orb.getUniqueId());
		this.parent(el);

		var form = el.find('form');
		form.on('submit', function(ev) {
			Orb.cancelEvent(ev);
		});

		var footer = el.find('footer');
		footer.find('.submit-trigger').on('click', function(ev) {
			Orb.cancelEvent(ev);
			footer.addClass('dp-loading-on');

			var postData = form.serializeArray();

			$.ajax({
				data: postData,
				url: form.attr('action'),
				type: 'POST',
				complete: function() {
					DeskPRO_Window.sections.publish_section.reload();
					self.closeSelf();
				}
			});
		});
	}
});
Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');

DeskPRO.Agent.PageFragment.Page.NewFeedback = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'newfeedback';
		this.allowDupe = true;
	},

	initPage: function(el) {
		var self = this;
		this.wrapper = el;
		this.parent(el);

		if (!this.getEl('cat').find('option')[0]) {
			this.wrapper.find('.form-header-error').show();
			this.wrapper.find('.form-outer').hide();
			this.markForReload();
		}

		this.form = $('form', this.wrapper).on('submit', function(ev) {
			ev.preventDefault();
		});

		$('button.submit-trigger', this.wrapper).on('click', this.submit.bind(this));

		this._initCategorySection();
		this._initTitleSection();
		this._initContentSection();
		this._initOtherSection();

		this.stateSaver = new DeskPRO.Agent.PageHelper.StateSaver({
			stateId: 'newnews',
			listenOn: this.getEl('newfeedback')
		});
		this.ownObject(this.stateSaver);

		window.setTimeout(function() {
			if (self.OBJ_DESTROYED) return;

			self.wrapper.find('select').each(function() {
				if ($(this).prop('multiple')) {
					$(this).width(300);
				}
				DP.select($(this));
			});
			self.updateUi();
		}, 300);
	},

	destroyPage: function() {
		// Workaround for tinymce bug to do with remove()
		// We'll manually remove the node ourselves
		var el = this.wrapper.find('.article-section');
		if (el[0]) {
			el.get(0).parentNode.removeChild(el.get(0));
		}
	},

	markForReload: function() {
		if (!this.markedForReload) {
			this.markedForReload = true;
			this.addEvent('deactivate', this.closeSelf.bind(this));
		}
	},

	closeSelf: function() {
		var ev = {cancel: false};
		this.fireEvent('closeSelf', ev);

		if (!ev.cancel) {
			this.parent();
		}
	},

	submit: function() {
		var formData = this.form.serializeArray();
		if (this.labelsInput) {
			formData.append(this.labelsInput.getFormData());
		}

		$('div.error.section', this.wrapper).removeClass('error');
		$('.error-message-on', this.wrapper).removeClass('error-message-on');

		this.stateSaver.stop();
		this.wrapper.addClass('loading');

		$.ajax({
			url: BASE_URL + 'agent/feedback/new/save',
			type: 'POST',
			data: formData,
			dataType: 'json',
			context: this,
			complete: function() {
				this.wrapper.removeClass('loading');
			},
			success: function(data) {
				if (data.error) {
					Array.each(data.error_codes, function(code) {
						this.showErrorCode(code);
					}, this);
					this.updateUi();
					return;
				}

				if (data.success) {
					DeskPRO_Window.runPageRoute('page:' + BASE_URL + 'agent/feedback/view/' + data.feedback_id);
					this.markForReload();
					this.closeSelf();
				} else {
					alert('There was an error with the form');
				}
			}
		});
	},

	showErrorCode: function(code) {
		$('.' + code + '.error-message', this.wrapper).addClass('error-message-on');
	},

	//#################################################################
	//# Category section
	//#################################################################

	_initCategorySection: function() {
		var self = this;

		this.getEl('cat').on('change', function() {
			if (parseInt($(this).val())) {
				self.getEl('cat_section').addClass('done');
			} else {
				self.getEl('cat_section').removeClass('done');
			}
		});
	},

	//#################################################################
	//# Title section
	//#################################################################

	_initTitleSection: function() {
		var self = this;

		var fn = function() {
			if ($(this).val().trim() == '') {
				self.getEl('title_section').removeClass('done');
			} else {
				self.getEl('title_section').addClass('done');
			}
		};

		this.getEl('title').on('change', fn).on('keypress', fn).on('change', function() {
			var val = $(this).val().trim().toLowerCase();
			val = val.replace(/[^a-z0-9\-_]/g, '-');
			val = val.replace(/-{2,}/g, '-');

			self.getEl('slug').val(val);
		});
	},

	//#################################################################
	//# Content section
	//#################################################################

	_initContentSection: function() {

		var self = this;

		this.getEl('content').css({
			width: this.wrapper.width() - 150
		});

		// Make the size of the message box based off of the height of the window
		var h = $(window).height();
		this.getEl('content').css('height', Math.max(h - 500, 200));

		DP.rteTextarea(this.getEl('content'), {
			setup: function(ed) {
				ed.onKeyPress.add(function() {
					if (self.stateSaver) {
						self.stateSaver.triggerChange();
					}
				});
			}
		});
	},

	//#########################################################################
	//# Other Section
	//#########################################################################

	_initOtherSection: function() {
		var self = this;
		this.otherTabs = new DeskPRO.UI.SimpleTabs({
			triggerElements: $('li', this.getEl('other_props_tabs')),
			context: this.getEl('other_props_tabs_content'),
			autoSelectFirst: false,
			onTabSwitch: function(eventData) {
				if (!self.labelsInput && eventData.tabContent.hasClass('tab-properties')) {
					self.labelsInput = new DeskPRO.UI.LabelsInput({
						type: 'feedback',
						fieldName: 'newfeedback[labels]',
						input: $(".tags-wrap input", eventData.tabContent),
						onChange: function() {
							if (self.stateSaver) {
								self.stateSaver.triggerChange();
							}
						}
					});
					self.ownObject(self.labelsInput);
				}
			},
			onTabClick: (function(ev) {
				var contentWrap = this.getEl('other_props_tabs_content');
				var navWrap = this.getEl('other_props_tabs_wrap');
				var tab = ev.tabEl;

				// Toggle content state if we're clicking for the first time,
				// or re-clicking a tab
				if (!$('.on', navWrap).length || tab.is('.on')) {
					if (contentWrap.is(':visible')) {
						contentWrap.hide();
						navWrap.removeClass('on');
					} else {
						contentWrap.show();
						navWrap.addClass('on');
					}
				}
			}).bind(this)
		});
		this.ownObject(this.otherTabs);

		// Attachments
		DeskPRO_Window.util.fileupload(this.wrapper, { page: this });
		var list = $('.file-list', this.wrapper);
		$('input', list[0]).live('click', function() {
			var el = $(this);
			var li = el.parent();
			if (el.is(':checked')) {
				li.removeClass('unchecked');
			} else {
				li.addClass('unchecked');
			}
			self.updateUi();
		});
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');
DeskPRO.Agent.PageFragment.Page.Organization = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		var self = this;
		this.parent();
		this.TYPENAME = 'organization';
		this.tabBtn = null;
		this.noIgnoreForm = true;
		this.addEvent('render', function(container, id) {
			if (id) {
				self.tabBtn = $('#tabbtn_' + id);
			}
		});
	},

	initMetaData: function() {
		DeskPRO_Window.recentTabs.add(
			'org',
			this.meta.org_id,
			this.meta.title,
			BASE_URL + 'agent/organizations/' + this.meta.org_id
		);
	},

	initPage: function(el) {
		this.wrapper = el;
		this.contentWrapper = $('div.layout-content:first', el);

		if (this.tabBtn) {
			if (this.getMetaData('orgPicIcon')) {
				this.tabBtn.find('a').find('i').attr('class', '').addClass('image-icon').css('background-image', 'url("' + this.getMetaData('orgPicIcon') + '")').css('background-position', '50% 50%');
			}
		}

		var self = this;

		this.contactEditor = new DeskPRO.Agent.PageFragment.Page.PersonHelper.ContactEditor(this, {
			saveUrl: BASE_URL + 'agent/organizations/' + this.meta.org_id + '/save-contact-data.json',
			onReplaceEditor: function() {
				self.refreshPropBox();
			}
		});
		this.ownObject(this.contactEditor);

		if (this.meta.perms.edit) {
			// Name is editable
			var name = $('h3.name.editable:first', el);
			if (!name.attr('id')) {
				name.attr('id', Orb.getUniqueId());
			}

			var editable = new DeskPRO.Form.InlineEdit({
				baseElement: this.wrapper,
				editableClass: 'person-name-editable',
				ajax: {
					url: BASE_URL + 'agent/organizations/' + this.meta.org_id + '/ajax-save'
				},
				triggers: '.edit-name-gear'
			});

			// Attach click to wrapper because
			// this same code is used on popout on ticket,
			// and clicks dont bubble to document click
			$(this.wrapper).on('click', function (ev) {
				editable.handleDocumentClick(ev);
			});
		}

		this.getEl('delete_btn').on('click', function() {
			var url = $(this).data('delete-url');
			DeskPRO_Window.showConfirm(
				$('<div>Are you sure you want to delete this organization? <strong class="warning">The organization will be permanantly deleted</strong>.'),
				function() {
					$.ajax({
						url: url,
						type: 'POST',
						success: function() {
							DeskPRO_Window.showAlert('The organization was deleted');
						}
					});
					self.closeSelf();
				}
			);
		});

		this.changePic = new DeskPRO.Agent.PageFragment.Page.PersonHelper.ChangePic(this, {
			loadUrl: BASE_URL + "agent/organizations/" + this.meta.org_id + "/change-picture-overlay",
			saveUrl: BASE_URL + 'agent/organizations/' + this.meta.org_id + '/ajax-save'
		});
		this.ownObject(this.changePic);

		this._initLabels();
		this._initCustomFieldsEditor();

		this.getEl('members_list').on('click', '.remove', function() {
			var row = $(this).closest('.member-row');
			var personId = row.data('person-id');
			if (!personId) {
				return;
			}

			row.fadeOut('fast');

			$.ajax({
				url: BASE_URL + 'agent/organizations/' + self.meta.org_id + '/ajax-save',
				data: { action: 'remove-person', person_id: personId },
				type: 'POST',
				context: this,
				error: function() {
					row.show();
				},
				success: function() {
					row.remove();
					DeskPRO_Window.util.modCountEl(self.getEl('members_count'), '-');
				}
			});
		});

		this.getEl('add_searchbox').bind('personsearchboxclicknew', function(ev, term, sb) {
			if (DeskPRO_Window.newPersonLoader) {
				DeskPRO_Window.newPersonLoader.open(function(page) {
					page.setGuessTerm(term);
					page.setOrganization(self.meta.org_id, self.getEl('editname').find('input[name="name"]').val());
				});
			} else {
				DeskPRO_Window.showAlert('You are not allowed to create new people');
			}

			sb.close();
			sb.reset();
		});

		this.getEl('add_searchbox').bind('personsearchboxclick', function(ev, personId, name, email, sb) {
			self.getEl('newmember_person_name').text(name);
			self.getEl('newmember_person_email').text(email);
			self.getEl('newmember_person_id').val(personId);
			self.getEl('newmember_position').val('');

			self.getEl('newmember_row').hide();
			self.getEl('newmember_row_named').show();

			sb.close();
			sb.reset();
		});

		var close_newmember_row = function() {
			self.getEl('add_searchbox_txt').val('');
			self.getEl('newmember_row_named').hide();
			self.getEl('newmember_row').show();
		};

		this.getEl('newmember_cancel_btn').on('click', function() {
			close_newmember_row();
		});

		this.getEl('newmember_btn').on('click', function() {
			var personId = self.getEl('newmember_person_id').val();
			var pos = self.getEl('newmember_position').val();

			$.ajax({
				url: BASE_URL + 'agent/organizations/' + self.meta.org_id + '/ajax-save',
				data: { action: 'add-person', person_id: personId, position: pos },
				type: 'POST',
				context: this,
				success: function(data) {
					self.getEl('newmember_person_input').val('');
					self.getEl('newmember_position').val('');
					self.getEl('newmember_person_id').val('0');

					if (data.already_in_organization) {
						DeskPRO_Window.showAlert('That user is already in an organization');
					} else {
						var row = $(data.row_html);
						row.insertAfter(self.getEl('newmember_row_named'));

						DeskPRO_Window.util.showSavePuff(row);
						DeskPRO_Window.util.modCountEl(self.getEl('members_count'), '+');
					}
				}
			});

			close_newmember_row();
		});


		DeskPRO_Window.getMessageBroker().addMessageListener('new-org-user', function(info) {
			if (!info.organization_id || info.organization_id != self.meta.org_id) {
				return;
			}

			$.ajax({
				url: BASE_URL + 'agent/organizations/' + self.meta.org_id + '/ajax-save',
				data: { action: 'get-person-row', person_id: info.person_id },
				type: 'GET',
				context: this,
				success: function(data) {
					var row = $(data.row_html);
					row.insertAfter(self.getEl('newmember_row_named'));

					DeskPRO_Window.util.showSavePuff(row);
					DeskPRO_Window.util.modCountEl(self.getEl('members_count'), '+');
				}
			});
		}, this);

		this.getEl('members_list').on('click', '.organization-manager-icon', function(ev) {
			ev.stopPropagation();

			var $this = $(this), line = $this.closest('tr');

			line.toggleClass('is-manager');

			$.ajax({
				url: $this.data('save-url'),
				type: 'POST',
				data: { organization_manager: line.hasClass('is-manager') ? 1 : 0 }
			});

			$(this).qtip('hide', ev);
		});

		this.getEl('members_list').on('mouseover', '.organization-manager-icon', function(ev) {
			if ($(this).is('.tipped-inited')) {
				return;
			}

			var qtipOptions = {
				content: {
					text: function() {
						var $this = $(this), line = $this.closest('.line');

						if (line.hasClass('is-manager')) {
							return $this.data('is-manager');
						} else {
							return $this.data('not-manager');
						}
					}
				},
				position: {
					my: 'top center',
					at: 'bottom center',
					viewport: $(window)
				},
				style: {
					classes: 'ui-tooltip-shadow ui-tooltip-rounded'
				}
			};

			$(this).qtip(qtipOptions).qtip('show', ev);
			$(this).addClass('tipped-inited');
		});

		this.getEl('members_list').on('click', '.position-edit-trigger', function(ev) {
			ev.stopPropagation();

			var row = $(this).closest('.member-row');
			var label = $('.position-label', row);
			var input = $('.position-edit', row);
			var inputTxt = $('.position-edit input', row);

			label.fadeOut('fast', function() {
				input.fadeIn('fast');
				input.get(0).focus();
			});

			var done = function() {
				var val = inputTxt.val();
				if (val) {
					label.text(val);
				} else {
					label.empty().append($('<span style="font-size: 11px; color: #959595;">No position set</span>'));
				}

				input.fadeOut('fast', function() {
					label.fadeIn('fast');
				});

				$.ajax({
					url: inputTxt.data('save-url'),
					type: 'POST',
					data: { organization_position: val }
				});
			};

			if (!input.is('.has-init')) {
				input.addClass('has-init');
				input.on('click', function(ev){ev.stopPropagation();});
				input.on('keypress', function() {
					if (ev.keyCode == 13) {
						done();
					}
				});
				var closest = input.closest('.popover-wrapper');
				$(closest.length ? closest : document).on('click', done);
			}
		});

		$('.profile-box-container.tabbed', this.wrapper).each(function() {
			var simpleTabs = new DeskPRO.UI.SimpleTabs({
				triggerElements: '> header li',
				context: this
			});

			self.ownObject(simpleTabs);
		});

		$('.new-note textarea', this.getEl('notes_tab')).TextAreaExpander(40, 225);

		var summaryTxt = this.getEl('summary').TextAreaExpander(40, 225);

		if (this.meta.perms.edit) {
			this._initEmailDomainAssoc();
		}

		this.refreshPropBox();

		var fieldsRendered = this.getEl('custom_fields_rendered');
		var fieldsForm = this.getEl('custom_fields_editable');
		var box = $('.profile-box-container.properties ', el);

		var propToggle = function(what) {
			if (what == 'display') {
				$('.prop-edit-trigger', box).show();
				$('.is-loading', box).hide();
				$('.save', box).hide();
				$('.cancel', box).hide();
				fieldsForm.hide();
				fieldsRendered.show();
			} else {
				if (!fieldsForm.hasClass('dp-has-init')) {
					fieldsForm.addClass('dp-has-init');
					fieldsForm.find('.Date.customfield input').datepicker({
						dateFormat: 'yy-mm-dd',
						showButtonPanel: true,
						beforeShow: function(input) {
							setTimeout(function() {
								var buttonPane = $(input).datepicker("widget").find(".ui-datepicker-buttonpane");

								buttonPane.find('button:first').remove();

								var btn = $('<button class="ui-datepicker-current ui-state-default ui-priority-secondary ui-corner-all" type="button">Clear</button>');
								btn.unbind("click").bind("click", function () { $.datepicker._clearDate( input ); });
								btn.appendTo( buttonPane );

								$(input).datepicker("widget").css('z-index', 30001);
							},1);
						}
					});
				}

				$('.prop-edit-trigger', box).hide();
				$('.is-loading', box).hide();
				$('.save', box).show();
				$('.cancel', box).show();
				fieldsRendered.hide();
				fieldsForm.show();
			}
		};

		$('.prop-edit-trigger', box).on('click', function() {
			propToggle('form');
		});
		$('.save', box).on('click', function() {
			var formData = $('input[type="text"], input[type="password"], input:checked, select, textarea', fieldsForm);

			$('.is-loading', box).show();
			$('.save', box).hide();
			$('.cancel', box).hide();

			$.ajax({
				url: BASE_URL + 'agent/organizations/' + self.meta.org_id + '/ajax-save-custom-fields',
				type: 'POST',
				data: formData,
				dataType: 'html',
				success: function(rendered) {
					fieldsRendered.empty().html(rendered);
					propToggle('display');
				}
			});
		});
		$('.cancel', box).on('click', function() {
			propToggle('display');
		});

		if (!this.meta.perms.edit) {
			var contactBox = $('.profile-box-container.contact', this.el);
			if (!contactBox.find('> section > .table-content > *')[0]) {
				contactBox.hide();
			}
		}

		this.initUgEditor();
		this.initSlaEditor();
	},

	refreshPropBox: function() {

		var contactBox = $('.profile-box-container.contact', this.el);

		var has = false;
		if ($('.contact-data-list > li', contactBox).length) {
			has = true;
		}

		if (!has && $('.outside-display > *', contactBox).length) {
			has = true;
		}

		if (!has && $('.addresses > *', contactBox).length) {
			has = true;
		}

		if (!has) {
			contactBox.addClass('no-section');
		} else {
			contactBox.removeClass('no-section');
		}
	},

	//#########################################################################
	//# Email domain associations
	//###########	##############################################################

	_initEmailDomainAssoc: function(opennow) {

		if (this.emailDomainOverlay) {
			this.emailDomainOverlay.destroy();
		}

		var self = this;

		var contain = this.getEl('email_assoc_box');
		var trigger = $('.email-assoc-edit', contain);
		var overlayEl = $('.email-assoc-overlay', contain);

		var newInput = $('input.new-domain', contain);
		var newContain = $('.profile-box-container.new', contain);

		this.emailDomainOverlay = null;

		var updateNew = function() {
			if (newInput.val().trim()) {
				$('.controls .save', newContain).show();
			} else {
				$('.controls .save', newContain).hide();
			}
		};

		var open = function() {
			if (!self.emailDomainOverlay) {
				self.emailDomainOverlay = new DeskPRO.UI.Overlay({
					triggerElement: trigger,
					contentElement: overlayEl,
					zIndex: 30001
				});

				newInput.on('keyup', updateNew).on('change', updateNew);

				var replaceEditor = function(newDisplayHtml) {
					self.getEl('email_assoc_box').empty().html(newDisplayHtml);
					self._initEmailDomainAssoc(true);
				};

				$('.controls .save', newContain).on('click', function() {
					$.ajax({
						url: BASE_URL + 'agent/organizations/' + self.meta.org_id + '/assign-domain',
						type: 'POST',
						data: { domain: newInput.val().trim() },
						dataType: 'html',
						success: function(newDisplayHtml) {
							replaceEditor(newDisplayHtml);
						}
					});
				});

				var delBtn = $('.delete-button', overlayEl);
				var cancelDelBtn = $('.cancel-delete-button', overlayEl);
				var delSection = $('.delete-controls', overlayEl);

				delBtn.on('click', function() {
					var sect = $(this).closest('tr');
					var delBtn = $('.delete-button', sect);
					var cancelDelBtn = $('.cancel-delete-button', sect);
					var delSection = $('.delete-controls', sect);

					$('.delete-controls', sect).slideDown('fast');
					delBtn.hide();
					cancelDelBtn.show();
					delSection.slideDown('fast');
				});
				cancelDelBtn.on('click', function() {
					var sect = $(this).closest('tr');
					var delBtn = $('.delete-button', sect);
					var cancelDelBtn = $('.cancel-delete-button', sect);
					var delSection = $('.delete-controls', sect);

					cancelDelBtn.hide();
					delBtn.show();
					delSection.slideUp('fast');
				});

				$('.remove-email, .remove-email-users', overlayEl).on('click', function() {
					var domain = $(this).closest('tr').data('org-domain');
					var removeusers = $(this).is('.remove-email-users') ? 1 : 0;

					$.ajax({
						url: BASE_URL + 'agent/organizations/' + self.meta.org_id + '/unassign-domain',
						type: 'POST',
						data: { domain: domain, remove_users: removeusers },
						dataType: 'html',
						success: function(newDisplayHtml) {
							self.emailDomainOverlay.destroy();
							self.closeSelf();
							DeskPRO_Window.runPageRoute('page:' + BASE_URL + 'agent/organizations/' + self.meta.org_id);
						}
					});
				});

				$('.move-users', overlayEl).on('click', function() {
					var domain = $(this).closest('tr').data('org-domain');

					$.ajax({
						url: BASE_URL + 'agent/organizations/' + self.meta.org_id + '/domain/move-users',
						type: 'POST',
						data: { domain: domain },
						dataType: 'html',
						success: function(newDisplayHtml) {
							self.emailDomainOverlay.destroy();
							self.closeSelf();
							DeskPRO_Window.runPageRoute('page:' + BASE_URL + 'agent/organizations/' + self.meta.org_id);
						}
					});
				});

				$('.move-all-users', overlayEl).on('click', function() {
					var domain = $(this).closest('tr').data('org-domain');

					$.ajax({
						url: BASE_URL + 'agent/organizations/' + self.meta.org_id + '/domain/reassign-users',
						type: 'POST',
						data: { domain: domain },
						dataType: 'html',
						success: function(newDisplayHtml) {
							self.emailDomainOverlay.destroy();
							self.closeSelf();
							DeskPRO_Window.runPageRoute('page:' + BASE_URL + 'agent/organizations/' + self.meta.org_id);
						}
					});
				});
			}

			self.emailDomainOverlay.open();
		};

		trigger.on('click', open);
		if(opennow) open();
	},

	//#########################################################################
	//# Custom fields
	//#########################################################################

	_initCustomFieldsEditor: function() {

		var fieldsRenderedWrap, fieldsEditWrap;

		fieldsRenderedWrap = this.fieldsRenderedWrap = this.getEl('custom_fields_rendered');
		fieldsEditWrap = this.fieldsEditWrap = this.getEl('custom_fields_editable');

		var toggle = (function() {
			if (fieldsRenderedWrap.is(':visible')) {
				fieldsRenderedWrap.hide();
				fieldsEditWrap.show();
			} else {
				fieldsEditWrap.hide();
				fieldsRenderedWrap.show();
			}
		}).bind(this);

		$('.show-edit-custom-fields', this.wrapper).on('click', function() {
			toggle();
		});

		$('.save-custom-fields', this.wrapper).on('click', (function() {
			var formData = $('input, select, textarea', fieldsEditWrap).serializeArray();

			$.ajax({
				url: BASE_URL + 'agent/organizations/' + this.meta.org_id + '/ajax-save-custom-fields',
				type: 'POST',
				data: formData,
				dataType: 'html',
				success: function(rendered) {
					fieldsRenderedWrap.empty().html(rendered);
					toggle();
				}
			});
		}).bind(this));
	},

	//#########################################################################
	//# Labels
	//#########################################################################

	_initLabels: function() {
		if (this.getEl('labels_input')[0]) {
			this.labelsInput = new DeskPRO.UI.LabelsInput({
				type: 'tickets',
				input: this.getEl('labels_input'),
				onChange: this.saveLabels.bind(this)
			});
			this.ownObject(this.labelsInput);
		}
	},

	saveLabels: function() {
		this._doSaveLabels();
	},

	_doSaveLabels: function() {
		var data = this.labelsInput.getFormData();

		$.ajax({
			url: this.getMetaData('labelsSaveUrl'),
			type: 'POST',
			context: this,
			data: data,
			dataType: 'json'
		});
	},

	initUgEditor: function() {
		var self = this;
		var ugbox = this.getEl('ug_box');

		var editBtn   = ugbox.find('.edit-trigger');
		var cancelBtn = ugbox.find('.cancel-trigger');
		var saveBtn   = ugbox.find('.save-trigger');

		var displayBox = this.getEl('ug_display_box');
		var editBox    = this.getEl('ug_edit_box');

		var showEdit = function() {
			ugbox.removeClass('loading');
			editBtn.hide();
			saveBtn.show();
			cancelBtn.show();
			displayBox.hide();
			editBox.show();
			ugbox.removeClass('no-section').find('> section').show();
		};
		var showSaving = function() {
			ugbox.addClass('loading');
			editBtn.hide();
			saveBtn.hide();
			cancelBtn.hide();
			displayBox.show();
			editBox.hide();

			if (ugbox.find(':checkbox.ug-check:checked').length) {
				ugbox.find('> section').show();
				ugbox.removeClass('no-section');
			} else {
				ugbox.find('> section').hide();
				ugbox.addClass('no-section');
			}
		};
		var showNormal = function() {
			ugbox.removeClass('loading');
			editBtn.show();
			saveBtn.hide();
			cancelBtn.hide();
			displayBox.show();
			editBox.hide();

			if (ugbox.find(':checkbox.ug-check:checked').length) {
				ugbox.find('> section').show();
				ugbox.removeClass('no-section');
			} else {
				ugbox.find('> section').hide();
				ugbox.addClass('no-section');
			}
		};

		editBtn.on('click', function() {
			showEdit();
		});
		cancelBtn.on('click', function() {
			showNormal();
		});
		saveBtn.on('click', function() {
			var formData = editBox.find(':checkbox.ug-check:checked').serializeArray();
			formData.push({name: 'action', value: 'set-usergroups'});

			displayBox.find('li.ug-row').hide();
			if (ugbox.find(':checkbox.ug-check:checked').length) {
				ugbox.find('> section').show();
				ugbox.removeClass('no-section');
			} else {
				ugbox.find('> section').hide();
				ugbox.addClass('no-section');
			}

			ugbox.find(':checkbox.ug-check:checked').each(function() {
				var id = $(this).val();
				displayBox.find('li.ug-row-' + id).show();
			});

			showSaving();
			$.ajax({
				url: BASE_URL + 'agent/organizations/' + self.meta.org_id + '/ajax-save',
				type: 'POST',
				dataType: 'json',
				data: formData,
				context: this,
				complete: function() {
					showNormal();
				},
				success: function(data) {
					showNormal();
				}
			});
		});
	},

	initSlaEditor: function() {
		var self = this;
		var slaBox = this.getEl('sla_box');
		if (!slaBox.length) {
			return;
		}

		var editBtn   = slaBox.find('.edit-trigger');
		var cancelBtn = slaBox.find('.cancel-trigger');
		var saveBtn   = slaBox.find('.save-trigger');
		var noSlas     = slaBox.find('.no-slas');

		var displayBox = this.getEl('sla_display_box');
		var editBox    = this.getEl('sla_edit_box');

		var showEdit = function() {
			slaBox.removeClass('loading');
			editBtn.hide();
			saveBtn.show();
			cancelBtn.show();
			displayBox.hide();
			editBox.show();
		};
		var showSaving = function() {
			slaBox.addClass('loading');
			editBtn.hide();
			saveBtn.hide();
			cancelBtn.hide();
			displayBox.show();
			editBox.hide();
		};
		var showNormal = function() {
			slaBox.removeClass('loading');
			editBtn.show();
			saveBtn.hide();
			cancelBtn.hide();
			displayBox.show();
			editBox.hide();
		};

		editBtn.on('click', function() {
			showEdit();
		});
		cancelBtn.on('click', function() {
			showNormal();
		});
		saveBtn.on('click', function() {
			var checks = editBox.find(':checkbox.sla-check:checked');

			var formData = checks.serializeArray();
			formData.push({name: 'action', value: 'set-slas'});

			displayBox.find('li.sla-row').hide();

			checks.each(function() {
				var id = $(this).val();
				displayBox.find('li.sla-row-' + id).show();
			});

			if (checks.length) {
				noSlas.hide();
			} else {
				noSlas.show();
			}

			showSaving();
			$.ajax({
				url: BASE_URL + 'agent/organizations/' + self.meta.org_id + '/ajax-save',
				type: 'POST',
				dataType: 'json',
				data: formData,
				context: this,
				complete: function() {
					showNormal();
				},
				success: function(data) {
					showNormal();
				}
			});
		});
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');
DeskPRO.Agent.PageFragment.Page.Person = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		var self = this;
		this.parent();
		this.TYPENAME = 'person';
		this.tabBtn = null;
		this.noIgnoreForm = true;
		this.addEvent('render', function(container, id) {
			if (id) {
				self.tabBtn = $('#tabbtn_' + id);
			}
		});
	},

	initMetaData: function() {
		DeskPRO_Window.recentTabs.add(
			'person',
			this.meta.person_id,
			this.meta.title,
			BASE_URL + 'agent/people/' + this.meta.person_id
		);
	},

	initPage: function(el) {
		var self = this;
		this.wrapper = el;
		this.contentWrapper = $('div.layout-content:first', el);

		try {
			var flashEnabled = !!(navigator.mimeTypes["application/x-shockwave-flash"] || window.ActiveXObject && new ActiveXObject('ShockwaveFlash.ShockwaveFlash'));
			if (flashEnabled) {
				// Set timeout to have it exec in global scope,
				// so errors (eg flash has crashed) can be ignored and dont break the rest of this init
				window.setTimeout(function() {
					self.wrapper.find('.copy-btn').each(function() {
						var btnEl = this;
						var btn = $(this);

						try {
							var clip = new ZeroClipboard(this, {
								btnEl: this,
								savePuffEl: self.getEl('idref_switch')
							});
							clip.on('mouseover', function(client, args) {
								$(client.options.btnEl).addClass('over');
							});
							clip.on('mouseout', function(client, args) {
								$(client.options.btnEl).removeClass('over');
							});
							clip.on('complete', function(client, args) {
								DeskPRO_Window.util.showSavePuff($(this).closest('.id-number'));
							});

							self.addEvent('destroy', function() {
								try {
									clip.unglue(btnEl);
								} catch (e) {}
							});
							self.addEvent('activate', function() {
								try {
									clip.reposition();
								} catch (e) {}
							});
						} catch (e) {}
					});
				}, 100);
			} else {
				this.wrapper.find('.copy-btn').remove();
			}
		} catch (e) {
			this.wrapper.find('.copy-btn').remove();
		}

		this.zIndex = 30001;

		var cw = this.contentWrapper;

		if (this.tabBtn) {
			if (this.getMetaData('personPicIcon')) {
				this.tabBtn.find('a').find('i').attr('class', '').addClass('image-icon').css('background-image', 'url("' + this.getMetaData('personPicIcon') + '")').css('background-position', '50% 50%');
			} else if (this.getMetaData('personGravatarIcon')) {
				var defaultIcon = ASSETS_BASE_URL_FULL + 'images/agent/tabs/tabtype-person.png';
				var url = this.getMetaData('personGravatarIcon');
				url = Orb.appendQueryData(url, 'd', defaultIcon);

				var a = this.tabBtn.find('a').find('i');
				a.attr('class', '').addClass('image-icon');
				a.css('background-image', 'url("' + url + '")').css('background-position', '2px 50%');
			}
		}

		// TextExt doesnt play well with fluid columns
		// so this listens for resizes, and then updates the input width,
		// then forces textext to invalidatebounds
		var propBox = self.getEl('properties_box');

		if (this.meta.perms.edit) {
			this.contactEditor = new DeskPRO.Agent.PageFragment.Page.PersonHelper.ContactEditor(this, {
				saveUrl: BASE_URL + 'agent/people/' + this.meta.person_id + '/save-contact-data.json',
				displayEl: this.getEl('contact_display'),
				outsideEl: this.getEl('contact_outside'),
				onReplaceEditor: function() {
					self.refreshPropBox();
				},
				onSuccess: function(data) {
					if (data.changed_primary_email) {
						DeskPRO_Window.util.updateUserEmailAddressDisplay(self.meta.person_id, data.primary_email_address);
					}
				}
			});
			this.ownObject(this.contactEditor);

			var tzMenu = new DeskPRO.UI.Menu({
				menuElement: this.getEl('timezone')
			});
			this.ownObject(tzMenu);

			var autoResMenu = new DeskPRO.UI.Menu({
				menuElement: this.getEl('disable_autoresponses')
			});
			this.ownObject(autoResMenu);

			this.getEl('timezone').on('change', function(){
				var val = $(this).val();
				$('.timezone-info', this.wrapper).empty();
				$.ajax({
					url: BASE_URL + 'agent/people/' + self.meta.person_id + '/ajax-save',
					type: 'POST',
					dataType: 'json',
					data: {
						action: 'timezone',
						timezone: val
					},
					context: this,
					success: function(data) {
						$('.timezone-info', this.wrapper).empty().html(data.bit_html);
					}
				});
			});

			this.getEl('disable_autoresponses').on('change', function(){
				var val = $(this).val();
				$.ajax({
					url: BASE_URL + 'agent/people/' + self.meta.person_id + '/ajax-save',
					type: 'POST',
					dataType: 'json',
					data: {
						action: 'disable_autoresponses',
						disable_autoresponses: val
					}
				});

				self.getEl('disable_autoresponses_reason').remove();
			});

			var namef       = this.getEl('showname');
			var editName    = this.getEl('editname');
			var editTitle   = this.getEl('edittitle');
			var orgpos      = this.getEl('showorgpos');
			var editOrgpos  = this.getEl('editorgpos');
			var startBtn    = this.getEl('editname_start');
			var stopBtn     = this.getEl('editname_end');

			var editTitleChoicesRaw = editTitle.data('choices').split(',');
			var editTitleChoices = [];
			for (var i = 0; i < editTitleChoicesRaw.length; i++) {
				editTitleChoices.push($.trim(editTitleChoicesRaw[i]));
			}
			editTitle.select2({
				initSelection: function(el, callback) {
					var existingTitle = editTitle.val();
					if (existingTitle.length) {
						callback({id: existingTitle, text: existingTitle});
					} else {
						callback({id: '', text: '\u00A0'});
					}
				},
				query: function(query) {
					var inList = function(term) {
						for (var i = 0; i < editTitleChoices.length; i++) {
							if (editTitleChoices[i] == term) {
								return true;
							}
						}

						return false;
					};

					var results = [];

					if (query.term.length) {
						results.push({id: query.term, text: query.term});
					}

					var val = editTitle.val();
					if (val.length && !inList(val) && val != query.term) {
						results.push({id: val, text: val});
					}

					for (var i = 0; i < editTitleChoices.length; i++) {
						var choice = editTitleChoices[i];
						results.push({id: choice, text: choice});
					}

					results.push({id: '', text: '\u00A0'});

					query.callback({results: results});
				}
			});

			var startEditable = function() {
				namef.hide();
				orgpos.hide();
				editName.show();
				editOrgpos.show();
				startBtn.hide();
				stopBtn.show();
			};

			var stopEditable = function() {
				var nametxt = editName.find('input[name=name]').first();
				var titletxt = editName.find('input[name=title_prefix]').first();
				var postxt  = editOrgpos.find('input').first();

				var setName = nametxt.val().trim();
				var setTitle = titletxt.val().trim();
				if (postxt) {
					var setPos  = '';
				} else {
					var setPos  = postxt.val().trim();
				}

				if(!setName) {
					return;
				}

				if (setPos) {
					orgpos.show().find('.org-pos-display').text(setPos);
				} else {
					orgpos.hide();
				}

				editName.hide();
				editOrgpos.hide();
				startBtn.show();
				namef.show();
				orgpos.hide();
				stopBtn.hide();
				namef.text((setTitle ? setTitle + ' ' : '') + setName);

				var postData = [];
				postData.push({
					name: 'action',
					value: 'quick-edit-name'
				});
				postData.push({
					name: 'name',
					value: setName
				});
				postData.push({
					name: 'title_prefix',
					value: setTitle
				});
				postData.push({
					name: 'organization_position',
					value: setPos
				});

				$.ajax({
					url: BASE_URL + 'agent/people/' + self.meta.person_id + '/ajax-save',
					type: 'POST',
					data: postData
				});
			};

			namef.on('dblclick', startEditable).on('keypress', function(ev) {
				if (ev.keyCode == 13 /* enter key */) {
					ev.preventDefault();
					stopEditable();
				}
			});
			editOrgpos.find('input').first().on('keypress', function(ev) {
				if (ev.keyCode == 13 /* enter key */) {
					ev.preventDefault();
					stopEditable();
				}
			});
			this.getEl('editname_start').on('click', startEditable);

			this.getEl('editname_end').on('click', stopEditable);

			$('.contact-list-wrapper', this.wrapper).first().on('click', '.set-primary', function() {
				var email_id = $(this).data('email-id');
				$('.contact-list-wrapper .email.is-primary', self.wrapper).removeClass('is-primary');
				$('.contact-list-wrapper .email-' + email_id, self.wrapper).addClass('is-primary');

				var val = $(this).val();
				$.ajax({
					url: BASE_URL + 'agent/people/' + self.meta.person_id + '/ajax-save',
					type: 'POST',
					dataType: 'json',
					data: {
						action: 'set-primary-email',
						email_id: email_id
					},
					success: function(data) {
						DeskPRO_Window.util.updateUserEmailAddressDisplay(self.meta.person_id, data.primary_email_address);
					}
				});
			});

			$('.contact-list-wrapper', this.wrapper).first().on('click', '.banned', function() {
				var el = $(this);
				var url = $(this).data('unban-url');
				//msg, callback_yes, callback_no, phrase_yes, phrase_no
				DeskPRO_Window.showConfirm("Do you want to unban this email address?", function() {
					el.remove();
					$.ajax({
						url: url,
						dataType: 'json'
					});
				}, function() {}, "Unban", "Cancel");
			});

			this.changePic = new DeskPRO.Agent.PageFragment.Page.PersonHelper.ChangePic(this, {
				loadUrl: BASE_URL + "agent/people/" + this.meta.person_id + "/change-picture-overlay",
				saveUrl: BASE_URL + 'agent/people/' + this.meta.person_id + '/ajax-save'
			});
			this.ownObject(this.changePic);

		} // can edit

		var self = this;
		$('.create-ticket', this.getEl('action_buttons')).on('click', function() {
			DeskPRO_Window.newTicketLoader.open(function(page) {
				var data = {
					person_id: self.meta.person_id,
					email: self.getEl('contact_display').find('li.is-primary.email').data('email-address'),
					name: self.getEl('editname').find('input[name="name"]').val()
				};
				page.setNewByPerson(data);
			});
		});

		var person_id = this.meta.person_id;

		this.moreactionsMenu = new DeskPRO.UI.Menu({
			triggerElement: $('.more', this.getEl('action_buttons')),
			menuElement: this.getEl('more_actions_menu'),
			onItemClicked: function(info) {
				var itemEl = $(info.itemEl), action = itemEl.data('action');

				if (action == 'reset-password') {
					DeskPRO_Window.showPrompt(
						'<div>Enter a new password. The user will be notified.</div>',
						function(val, wrap) {
							var postData = [];
							postData.push({
								name: 'password',
								value: val
							});

							postData.push({
								name: 'send_email',
								value: $('.send_email', wrap).is(':checked')
							});

							postData.push({
								name: 'action',
								value: 'password'
							});

							$.ajax({
								url: BASE_URL + 'agent/people/' + self.meta.person_id + '/ajax-save',
								type: 'POST',
								dataType: 'json',
								data: postData
							});
						}
					);
				} else if (action == 'delete') {
					var el = self.getEl('delete_confirm').clone();
					DeskPRO_Window.showConfirm(
						el,
						function() {
							$.ajax({
								url: $(info.itemEl).data('delete-url'),
								type: 'POST',
								success: function() {
									DeskPRO_Window.getMessageBroker().sendMessage('agent.person.removed', { person_id: person_id });
									DeskPRO_Window.showAlert('The user was deleted');

									var tabs = DeskPRO_Window.getTabWatcher().findTabs('ticket', function(tab) {
										return (tab && tab.page && tab.page && tab.page.meta.person_id == person_id);
									});
									$.each(tabs, function(k, tab) {
										DeskPRO_Window.removePage(tab.page);
									});
								}
							});
							self.closeSelf();
						},
						null,
						null, null,
						400, 260
					);
				} else if (action == 'ban') {
					var el = self.getEl('ban_confirm').clone();
					DeskPRO_Window.showConfirm(
						el,
						function() {
							$.ajax({
								url: $(info.itemEl).data('delete-url'),
								type: 'POST',
								success: function() {
									DeskPRO_Window.getMessageBroker().sendMessage('agent.person.removed', { person_id: person_id });
									DeskPRO_Window.showAlert('The user was deleted and banned');
								}
							});
							self.closeSelf();
						},
						null,
						null, null,
						400, 260
					);
				} else if (action == 'enable-user') {
					$.ajax({
						url: BASE_URL + 'agent/people/' + self.meta.person_id + '/ajax-save',
						type: 'POST',
						dataType: 'json',
						data: {
							action: 'set-is-disabled',
							is_disabled: 0
						}
					});

					var text = itemEl.text();
					itemEl.text(itemEl.data('flip'));
					itemEl.data('flip', text);
					itemEl.data('action', 'disable-user');
					self.getEl('change_user_picture').find('.person-disabled').remove();
				} else if (action == 'disable-user') {
					$.ajax({
						url: BASE_URL + 'agent/people/' + self.meta.person_id + '/ajax-save',
						type: 'POST',
						dataType: 'json',
						data: {
							action: 'set-is-disabled',
							is_disabled: 1
						}
					});

					var text = itemEl.text();
					itemEl.text(itemEl.data('flip'));
					itemEl.data('flip', text);
					itemEl.data('action', 'enable-user');
					self.getEl('change_user_picture').append($('<span class="person-disabled" />'));
				}
			}
		});
		this.ownObject(this.moreactionsMenu);

		this.merge = new DeskPRO.Agent.Widget.Merge({
			tabType: 'person',
			metaId: self.meta.person_id,
			metaIdName: 'person_id',
			menu: this.getEl('merge_menu'),
			trigger: $('.merge', this.getEl('action_buttons')),
			overlayUrl: BASE_URL + 'agent/people/{id}/merge-overlay/{other}',
			mergeUrl: BASE_URL + 'agent/people/{id}/merge/{other}',
			loadRoute: 'person:' + BASE_URL + 'agent/people/{id}',
			overlayLoaded: function(overlay, merge) {
				overlay.getWrapper().find('.person-finder').bind('personsearchboxclick', function(ev, personId, name, email, sb) {
					sb.close();

					$.ajax({
						url: merge._getOverlayUrl(merge.options.metaId, personId),
						type: 'get',
						dataType: 'html',
						success: function(html) {
							merge.resetOverlay(html);
						}
					});
				});
			}
		});
		this.ownObject(this.merge);

		this._initLabels();

		$('.profile-box-container.tabbed', this.wrapper).each(function() {
			var simpleTabs = new DeskPRO.UI.SimpleTabs({
				triggerElements: '> header li',
				context: this
			});

			self.ownObject(simpleTabs);
		});

		this._initOrgEdit();

		var self = this;
		this.getEl('tickets_viewall').on('click', function(ev){
			var row = $(this).closest('tr').remove();
			self.getEl('tickets_rest').slideDown('fast', function() { self.updateUi(); });
		});

		$('.new-note textarea', this.getEl('notes_tab')).TextAreaExpander(40, 225);

		var summaryTxt = this.getEl('summary').TextAreaExpander(40, 225);

		this.refreshPropBox();


		var fieldsRendered = this.getEl('custom_fields_rendered');
		var fieldsForm = this.getEl('custom_fields_editable');
		var box = $('.profile-box-container.properties ', el);

		var propToggle = function(what) {
			if (what == 'display') {
				$('.prop-edit-trigger', box).show();
				$('.is-loading', box).hide();
				$('.save', box).hide();
				$('.cancel', box).hide();
				fieldsForm.hide();
				fieldsRendered.show();
				self.updateUi();
			} else {
				if (!fieldsForm.hasClass('dp-has-init')) {
					fieldsForm.addClass('dp-has-init');
					fieldsForm.find('.Date.customfield input').datepicker({
						dateFormat: 'yy-mm-dd',
						showButtonPanel: true,
						beforeShow: function(input) {
							setTimeout(function() {
								var buttonPane = $(input).datepicker("widget").find(".ui-datepicker-buttonpane");

								buttonPane.find('button:first').remove();

								var btn = $('<button class="ui-datepicker-current ui-state-default ui-priority-secondary ui-corner-all" type="button">Clear</button>');
								btn.unbind("click").bind("click", function () { $.datepicker._clearDate( input ); });
								btn.appendTo( buttonPane );

								$(input).datepicker("widget").css('z-index', 30001);
							},1);
						}
					});
				}

				$('.prop-edit-trigger', box).hide();
				$('.is-loading', box).hide();
				$('.save', box).show();
				$('.cancel', box).show();
				fieldsRendered.hide();
				fieldsForm.show();
				self.updateUi();
			}
		};

		$('.prop-edit-trigger', box).on('click', function() {
			propToggle('form');
		});
		$('.save', box).on('click', function() {
			var formData = $('input[type="text"], input[type="password"], input:checked, select, textarea', fieldsForm).serializeArray();

			$('.is-loading', box).show();
			$('.save', box).hide();
			$('.cancel', box).hide();

			$.ajax({
				url: BASE_URL + 'agent/person/' + self.meta.person_id + '/ajax-save-custom-fields',
				type: 'POST',
				data: formData,
				dataType: 'html',
				success: function(rendered) {
					fieldsRendered.empty().html(rendered);
					propToggle('display');
				}
			});
		});
		$('.cancel', box).on('click', function() {
			propToggle('display');
		});

		var tabWarn = $('.full-tab-warn', this.el);
		if (tabWarn.length) {
			$('.dismiss-trigger', tabWarn).on('click', function() {
				tabWarn.fadeOut('fast', function() {
					tabWarn.remove();
				});
			});
		}

		if(this.getEl('editname_start').is('.auto-click')) {
			this.getEl('editname_start').click();
		}

		this.initUgEditor();
		this.initSlaEditor();

		if (this.getEl('approve_user')[0]) {

			DeskPRO_Window.getMessageBroker().addMessageListener('agent.person.removed', function(info) {
				DeskPRO_Window.removePage(self);
			}, this);
			DeskPRO_Window.getMessageBroker().addMessageListener('agent.person.confirmed', function(info) {
				if (info.person_id == self.meta.person_id) {
					this.wrapper.find('.validating-bar').remove();
				}
			}, this);

			this.getEl('approve_user').on('click', function(ev) {
				ev.preventDefault();
				DeskPRO_Window.util.ajaxWithClientMessages({
					url: BASE_URL + 'agent/people/validate/approve',
					data: { 'people_ids[]': self.meta.person_id },
					success: function() {
						DeskPRO_Window.removePage(self);
						DeskPRO_Window.runPageRoute('page:'+ BASE_URL + 'agent/people/' + self.meta.person_id);

						DeskPRO_Window.getMessageBroker().sendMessage('agent.person.confirmed', { person_id: self.meta.person_id });
					}
				});
			});
			this.getEl('delete_user').on('click', function(ev) {
				ev.preventDefault();
				DeskPRO_Window.util.ajaxWithClientMessages({
					url: BASE_URL + 'agent/people/validate/delete',
					data: { 'people_ids[]': self.meta.person_id },
					success: function() {
						DeskPRO_Window.getMessageBroker().sendMessage('agent.person.removed', { person_id: self.meta.person_id });
						DeskPRO_Window.removePage(self);
					}
				});
			});
		}

		var vemails = this.getEl('validating_emails');
		if (vemails) {
			vemails.on('click', '.validate-trigger', function() {
				var id = $(this).data('email-id');
				var token = $(this).data('token');

				$.ajax({
					url: BASE_URL + 'agent/people/validate-email/'+id+'/'+token,
					type: 'POST',
					success: function() {
						self.closeSelf();
						DeskPRO_Window.runPageRoute('person:' + BASE_URL + 'agent/people/' + self.meta.person_id);
					}
				})
			});
		}

		this.addEvent('openOrgProfile', function(ev) {
			ev.preventDefault();
			self.getEl('org_box').find('.org_link').trigger('click');
		});
	},

	refreshPropBox: function() {

		var contactBox = $('.profile-box-container.contact', this.el);

		var has = false;
		if ($('.contact-data-list > li', contactBox).length) {
			has = true;
		}

		if (!has && $('.outside-display > *', contactBox).length) {
			has = true;
		}

		if (!has && $('.addresses > *', contactBox).length) {
			has = true;
		}

		if (!has) {
			contactBox.addClass('no-section');
		} else {
			contactBox.removeClass('no-section');
		}

		this.updateUi();
	},

	//#########################################################################
	//# Org Edit
	//#########################################################################

	_initOrgEdit: function() {
		var self = this;
		$('.org-edit-trigger, .cancel', this.getEl('org_display_header')).on('click', function(ev) {
			ev.preventDefault();
			self.toggleOrgEdit();
			refreshBox();

			if ($(this).is('.cancel')) {
				if (!parseInt(self.meta.org_id)) {
					$('.org-name', self.getEl('org_edit_wrap')).val('');
					$('.extra-input', self.getEl('org_edit_wrap')).hide();
				}
				self.getEl('org_searchbox').removeClass('is-new').removeClass('is-set');
				self.toggleOrgEdit('close');
			}
		});

		var orgDisplay = this.getEl('org_display_wrap');
		var orgEdit    = this.getEl('org_edit_wrap');

		//orgEnableBtn
		this.getEl('org_searchbox').bind('orgsearchboxclick', function(ev, orgId, name) {
			$('.extra-input', orgEdit).show();
			self.orgEnableBtn('save');
		}).bind('orgsearchboxcreate', function(ev, term, name) {
			$('.extra-input', orgEdit).show();
			self.orgEnableBtn('save');
		}).bind('orgsearchreverted', function(ev, term, name) {
			if (self.getEl('org_searchbox').is('.is-new') || ($('.org-id', orgEdit).val() && $('.org-id', orgEdit).val() != '0')) {
				self.orgEnableBtn('save');
			} else {
				self.orgEnableBtn('cancel');
			}
		});

		$('.extra-input', orgEdit).on('keyup change', function() {
			if (self.getEl('org_searchbox').is('.is-new') || ($('.org-id', orgEdit).val() && $('.org-id', orgEdit).val() != '0')) {
				self.orgEnableBtn('save');
			}
		});

		var refreshBox = function() {
			var box = self.getEl('org_box');
			box.removeClass('no-section');
			if (orgEdit.is(':visible')) {

			} else {
				if (!$('> *', orgDisplay).length) {
					box.addClass('no-section');
				}
			}
		};

		var saveFn = function() {

			self.orgEnableBtn('saving');

			var postData = [];
			postData.push({
				name: 'action',
				value: 'set-organization'
			});
			postData.push({
				name: 'name',
				value: $('.org-name', self.getEl('org_edit_wrap')).val().trim()
			});
			postData.push({
				name: 'id',
				value: $('.org-id', self.getEl('org_edit_wrap')).val().trim()
			});
			postData.push({
				name: 'position',
				value: $('.org-pos-set', self.getEl('org_edit_wrap')).val().trim()
			});

			postData.push({
				name: 'manager',
				value: $('.org-manager-set', self.getEl('org_edit_wrap')).is(':checked') ? 1 : 0
			});

			$.ajax({
				url: BASE_URL + 'agent/people/' + self.meta.person_id + '/ajax-save',
				type: 'POST',
				data: postData,
				dataType: 'json',
				success: function(data) {
					orgDisplay.empty();
					if (data.organization_id) {
						orgDisplay.html(data.html);
						self.meta.org_id = data.organization_id;
					} else {
						self.meta.org_id = 0;
					}

					self.getEl('org_searchbox').removeClass('is-new').removeClass('is-set');

					self.toggleOrgEdit();

					refreshBox();
				}
			});
		};

		this.getEl('org_edit_save').on('click', saveFn);
		this.getEl('org_edit_remove_org').on('click', function() {
			$('.org-id', self.getEl('org_edit_wrap')).val('0');
			$('.org-name', self.getEl('org_edit_wrap')).val('');
			$('.extra-input', orgEdit).hide();
			saveFn();
		});

		refreshBox();
	},

	orgEnableBtn: function(name) {
		var names = ['org-edit-trigger', 'saved', 'save', 'cancel', 'is-loading', 'remove-org'];
		var els = $('.' + names.join(', .'), this.getEl('org_display_header')).hide();
		els.filter('.' + name).show();

		if (name == 'save') {
			els.filter('.cancel').show();
		} else if (name == 'cancel') {
			var orgid = $('.org-id', this.getEl('org_edit_wrap')).val();
			if (orgid && orgid != '0') {
				els.filter('.remove-org').show();
			}
		}

		this.updateUi();
	},

	toggleOrgEdit: function(force) {
		var orgDisplay = this.getEl('org_display_wrap');
		var orgEdit    = this.getEl('org_edit_wrap');

		if ((force && force == 'close') || orgEdit.is(':visible')) {
			orgEdit.hide();
			this.getEl('org_searchbox').data('org-search-box').close();
			orgDisplay.show();
			this.orgEnableBtn('org-edit-trigger');
		} else if ((force && force == 'open') || !orgEdit.is(':visible')) {
			orgDisplay.hide();
			orgEdit.show();
			this.orgEnableBtn('cancel');
		}

		this.updateUi();
	},

	//#########################################################################
	//# Labels
	//#########################################################################

	_initLabels: function() {
		if (this.getEl('labels_input')[0]) {
			this.labelsInput = new DeskPRO.UI.LabelsInput({
				type: 'tickets',
				input: this.getEl('labels_input'),
				onChange: this.saveLabels.bind(this)
			});
			this.ownObject(this.labelsInput);
		}
	},

	saveLabels: function() {
		this._doSaveLabels();
		this.updateUi();
	},

	_doSaveLabels: function() {
		var data = this.labelsInput.getFormData();

		$.ajax({
			url: this.getMetaData('labelsSaveUrl'),
			type: 'POST',
			context: this,
			data: data,
			dataType: 'json',
			success: function(data) {
				var sect = DeskPRO_Window.sections.people_section;
				if (sect) {
					sect.reloadLabels();
				}
			}
		});
	},

	initUgEditor: function() {
		var self = this;
		var ugbox = this.getEl('ug_box');

		var editBtn   = ugbox.find('.edit-trigger');
		var cancelBtn = ugbox.find('.cancel-trigger');
		var saveBtn   = ugbox.find('.save-trigger');

		var displayBox = this.getEl('ug_display_box');
		var editBox    = this.getEl('ug_edit_box');

		var showEdit = function() {
			ugbox.removeClass('loading');
			editBtn.hide();
			saveBtn.show();
			cancelBtn.show();
			displayBox.hide();
			editBox.show();
			ugbox.removeClass('no-section').find('> section').show();
		};
		var showSaving = function() {
			ugbox.addClass('loading');
			editBtn.hide();
			saveBtn.hide();
			cancelBtn.hide();
			displayBox.show();
			editBox.hide();
		};
		var showNormal = function() {
			ugbox.removeClass('loading');
			editBtn.show();
			saveBtn.hide();
			cancelBtn.hide();
			displayBox.show();
			editBox.hide();
		};

		editBtn.on('click', function() {
			showEdit();
		});
		cancelBtn.on('click', function() {
			showNormal();
		});
		saveBtn.on('click', function() {
			var formData = editBox.find(':checkbox.ug-check:checked').serializeArray();
			formData.push({name: 'action', value: 'set-usergroups'});

			displayBox.find('li.ug-row').hide();

			ugbox.find(':checkbox.ug-check:checked').each(function() {
				var id = $(this).val();
				displayBox.find('li.ug-row-' + id).show();
			});

			showSaving();
			$.ajax({
				url: BASE_URL + 'agent/people/' + self.meta.person_id + '/ajax-save',
				type: 'POST',
				dataType: 'json',
				data: formData,
				context: this,
				complete: function() {
					showNormal();
				},
				success: function(data) {
					showNormal();
				}
			});
		});
	},

	initSlaEditor: function() {
		var self = this;
		var slaBox = this.getEl('sla_box');
		if (!slaBox.length) {
			return;
		}

		var editBtn   = slaBox.find('.edit-trigger');
		var cancelBtn = slaBox.find('.cancel-trigger');
		var saveBtn   = slaBox.find('.save-trigger');
		var noSlas     = slaBox.find('.no-slas');

		var displayBox = this.getEl('sla_display_box');
		var editBox    = this.getEl('sla_edit_box');

		var showEdit = function() {
			slaBox.removeClass('loading');
			editBtn.hide();
			saveBtn.show();
			cancelBtn.show();
			displayBox.hide();
			editBox.show();
		};
		var showSaving = function() {
			slaBox.addClass('loading');
			editBtn.hide();
			saveBtn.hide();
			cancelBtn.hide();
			displayBox.show();
			editBox.hide();
		};
		var showNormal = function() {
			slaBox.removeClass('loading');
			editBtn.show();
			saveBtn.hide();
			cancelBtn.hide();
			displayBox.show();
			editBox.hide();
		};

		editBtn.on('click', function() {
			showEdit();
		});
		cancelBtn.on('click', function() {
			showNormal();
		});
		saveBtn.on('click', function() {
			var checks = editBox.find(':checkbox.sla-check:checked');

			var formData = checks.serializeArray();
			formData.push({name: 'action', value: 'set-slas'});

			displayBox.find('li.sla-row').hide();

			checks.each(function() {
				var id = $(this).val();
				displayBox.find('li.sla-row-' + id).show();
			});

			if (checks.length) {
				noSlas.hide();
			} else {
				noSlas.show();
			}

			showSaving();
			$.ajax({
				url: BASE_URL + 'agent/people/' + self.meta.person_id + '/ajax-save',
				type: 'POST',
				dataType: 'json',
				data: formData,
				context: this,
				complete: function() {
					showNormal();
				},
				success: function(data) {
					showNormal();
				}
			});
		});
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');
DeskPRO.Agent.PageFragment.Page.PersonSession = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'person_session';
	},

	initPage: function(el) {
		this.el = el;

		this.tabs = new DeskPRO.UI.SimpleTabs({
			triggerElements: $('li', this.getEl('chats_tab_triggers'))
		});
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');
DeskPRO.Agent.PageFragment.Page.Visitor = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'visitor';
	},

	initPage: function(el) {
		this.el = el;
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');
DeskPRO.Agent.PageFragment.Page.PersonPopout = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Page.Person,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'person';
	},

	initPage: function(el) {
		this.parent(el);
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');
DeskPRO.Agent.PageFragment.Page.KbViewArticle = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'article';
	},

	initMetaData: function() {
		DeskPRO_Window.recentTabs.add(
			'article',
			this.meta.article_id,
			this.meta.title,
			BASE_URL + 'agent/kb/article/' + this.meta.article_id
		);
	},

	initPage: function(el) {

		var self = this;
		this.wrapper = el;

		this.article_id = this.getMetaData('article_id');

		this._initBasic();
		this._initArticleArea();

		this._initLabels();
		this._initCommentForm();

		if (this.meta.canEdit) {
			this._initMenus();

			this._initPostArea();
			this._initAutoUnpublishOptions();
			this._initAutoPublishOptions();

			var btn = $('.kb-editor-edit', this.wrapper);
			btn.on('click', this.showEditor.bind(this));

			if (this.meta.isValidating) {
				this.validatingEdit = new DeskPRO.Agent.PageHelper.ValidatingEdit(this, {
					typename: 'articles',
					contentId: this.meta.article_id
				});
				this.ownObject(this.validatingEdit);
			}
		}

		this.relatedContent = new DeskPRO.Agent.PageHelper.RelatedContent(this, {
			typename: 'articles',
			content_id: this.meta.article_id,
			listEl: $('section.linked-content:first', this.wrapper),
			disabled: !this.meta.canEdit,
			onContentLinked: function(typename, content_id) {
				$.ajax({
					url: BASE_URL + 'agent/kb/article/' + self.meta.article_id + '/ajax-save',
					type: 'POST',
					data: { content_type: typename, content_id: content_id, action: 'add-related' },
					context: this,
					dataType: 'json'
				});
			},
			onContentUnlinked: function(typename, content_id) {
				$.ajax({
					url: BASE_URL + 'agent/kb/article/' + self.meta.article_id + '/ajax-save',
					type: 'POST',
					data: { content_type: typename, content_id: content_id, action: 'remove-related' },
					context: this,
					dataType: 'json'
				});
			}
		});
		this.ownObject(this.relatedContent);

		this.whoVotedOverlay = new DeskPRO.UI.Overlay({
			triggerElement: '.who-voted-trigger',
			contentMethod: 'ajax',
			contentAjax: {
				url: BASE_URL + 'agent/publish/rating-who-voted/article/' + this.meta.article_id
			}
		});
		this.ownObject(this.whoVotedOverlay);

		this.whoViewedOverlay = new DeskPRO.UI.Overlay({
			triggerElement: '.open-who-viewed',
			contentMethod: 'ajax',
			contentAjax: {
				url: BASE_URL + 'agent/publish/who-viewed/1/' + this.meta.article_id
			}
		});
		this.ownObject(this.whoViewedOverlay);

		this.miscContent = new DeskPRO.Agent.PageHelper.MiscContent(this, {
			revisionCompareUrl: BASE_URL + 'agent/kb/compare-revs/{OLD}/{NEW}'
		});
		this.ownObject(this.miscContent);

		var fieldsRendered = this.getEl('custom_fields_rendered');
		var fieldsForm = this.getEl('custom_fields_editable');

		var buttonsWrap = this.getEl('properties_controls');
		var propToggle = function(what) {
			if (what == 'display') {
				$('.showing-editing-fields', buttonsWrap).hide();
				$('.showing-rendered-fields', buttonsWrap).show();
				fieldsForm.hide();
				fieldsRendered.show();
			} else {
				$('.showing-rendered-fields', buttonsWrap).hide();
				$('.showing-editing-fields', buttonsWrap).show();
				fieldsRendered.hide();
				fieldsForm.show();
			}
		};

		$('.edit-fields-trigger', buttonsWrap).on('click', function() {
			propToggle('edit');
		});

		$('.save-fields-trigger', buttonsWrap).on('click', function() {
			var formData = $('input[type="text"], input[type="password"], input:checked, select, textarea', fieldsForm);

			$.ajax({
				url: BASE_URL + 'agent/kb/article/' + self.meta.article_id + '/ajax-save-custom-fields',
				type: 'POST',
				data: formData,
				dataType: 'html',
				success: function(rendered) {
					fieldsRendered.empty().html(rendered);
					propToggle('display');
				}
			});
		});

		this.scanGlossaryWords();
		this._initTrans();
	},

	handleUnloadRevisions: function(revision_id) {
		if (!revision_id) {
			return;
		}

		if ($('.rev-' + revision_id, this.getEl('revs')).length) {
			return;
		}

		this.getEl('revs').empty().removeClass('loaded');
		DeskPRO_Window.util.modCountEl(this.getEl('count_revs'), '+');
	},

	scanGlossaryWords: function() {
		DeskPRO.WordHighlighter.highlight(this.getEl('content_ed').find('.article-content-wrap').get(0), this.meta.glossaryWords);
		this.getEl('content_ed').find('span.dp-highlight-word').each(function() {
			$(this).addClass('embedded-glossary-word tipped').data('tipped-options', "ajax:true, maxWidth:300").data('tipped', BASE_URL + "agent/glossary/"+$(this).data('word')+"/tip");
		});
	},

	//#################################################################
	//# Basic
	//#################################################################

	_initBasic: function() {
		var self = this;

		if (this.meta.canEdit) {
			$('.edit-trigger', this.wrapper).on('click', function() {
				DeskPRO_Window.runPageRoute('kb_article_edit:' + BASE_URL + 'agent/kb/article/' + self.article_id);
				DeskPRO_Window.removePage(self);
			});

			$('.validate-trigger', this.wrapper).on('click', function() {
				DeskPRO_Window.runPageRoute('kb_article_edit:' + BASE_URL + 'agent/kb/article/' + self.article_id + '?do_validate=1');
				DeskPRO_Window.removePage(self);
			});

			var editTitle = new DeskPRO.Agent.PageFragment.Page.EditTitle(
				this,
				BASE_URL + 'agent/kb/article/' + self.meta.article_id + '/ajax-save'
			);
		}

		// Tabs
		this.bodyTabs = new DeskPRO.UI.SimpleTabs({
			triggerElements: $('li', this.getEl('bodytabs')),
			onTabSwitch: (function(info) {
				if ($(info.tabContent).is('.kb-content')) {
					self.getEl('content_edit_btns').show();
				} else {
					self.getEl('content_edit_btns').hide();
				}
				if ($(info.tabContent).is('.kb-related-content')) {
					$('body').addClass('related-controls-on');
				} else {
					$('body').removeClass('related-controls-on');
					if ($(info.tabContent).is('.search-tab')) {
						self._initSearchTab();
					}
				}

				if ($(info.tabContent).is('.revisions-tab') && !$(info.tabContent).is('.loaded')) {
					$.ajax({
						url: BASE_URL + 'agent/kb/article/' + this.meta.article_id + '/view-revisions',
						type: 'GET',
						dataType: 'html',
						context: self,
						success: function(html) {
							this.getEl('revs').html(html);
							this.miscContent._initCompareRevs();
							$(info.tabContent).addClass('loaded');
						}
					});
				}
			}).bind(this)
		});
		this.ownObject(this.bodyTabs);

		var actions = this.getEl('action_buttons');
		$('.permalink', actions).on('click', function() {
			var html = [];
			html.push('<div>');
			html.push('The permalink to this article on the website is:<br />');
			html.push('<input type="text" style="width:95%;" />');
			html.push('</div>');

			var msg = $(html.join(''));
			$('input', msg).val(self.meta.permalink);

			DeskPRO_Window.showAlert(msg);
		});

		$('.view-user-interface', actions).on('click', function() {
			window.open(self.meta.permalink);
		});

		// Attachments
		var list = $('.file-list', this.wrapper);

		DeskPRO_Window.util.fileupload(this.wrapper, {
			url: BASE_URL + 'agent/misc/accept-upload?attach_to_object=article&object_id=' + this.meta.article_id,
			page: this
		});

		list.on('click', '.delete', function(ev) {
			ev.preventDefault();
			ev.stopImmediatePropagation();

			var blob_id = $(this).data('blob-id');
			$.ajax({
				url: BASE_URL + 'agent/kb/article/' + self.meta.article_id + '/ajax-save',
				type: 'POST',
				data: {action: 'remove-blob', blob_id: blob_id},
				context: self,
				dataType: 'json'
			});

			$(this).closest('li').remove();

			if (!list.find('li')[0]) {
				list.hide();
			}
		});
	},


	//#################################################################
	//# Menus
	//#################################################################

	_initMenus: function() {
		var self = this;

		var statusSel = this.getEl('status');
		DP.select(statusSel);

		statusSel.on('change', function() {
			DeskPRO_Window.sections.publish_section.reload();
			var status = $(this).val();

			self.getEl('auto_unpub').hide();
			self.getEl('auto_pub').hide();

			if (status == 'published') {
				self.getEl('auto_unpub').show();
			} else if (status == 'hidden.unpublished') {
				self.getEl('auto_pub').show();
			}

			$.ajax({
				url: BASE_URL + 'agent/kb/article/' + self.meta.article_id + '/ajax-save',
				type: 'POST',
				data: {action: 'status', status: status},
				context: self,
				dataType: 'json',
				success: function() {
					DeskPRO_Window.sections.publish_section.reload();
				}
			});

		});

		this.deleteHelper = new DeskPRO.Agent.PageFragment.Page.Content.DeleteControl(this, {
			ajaxSaveUrl: BASE_URL + 'agent/kb/article/' + self.meta.article_id + '/ajax-save'
		});
		this.ownObject(this.deleteHelper);

		var lis = $('li:not(.add)', self.getEl('categories'));
		if (lis.length < 2) {
			$('.remove', lis).hide();
		}

		//-----
		// Category manage
		//-----

		this.getEl('categories').on('click', '.remove', function(ev) {
			var li = $(this).parent();
			li.remove();

			var lis = $('li:not(.add)', self.getEl('categories'));
			if (lis.length == 1) {
				// Hide the remove from the last cat
				$('.remove', lis).hide();
			}

			self.sendUpdateCats();
		});

		this.getEl('addcat_trigger').on('click', function(ev) {
			if (!self.newCatTpl) {
				self.newCatTpl = DeskPRO_Window.util.getPlainTpl(self.getEl('addcat_select_tpl'));
			}

			var newLi = $(self.newCatTpl);
			newLi.find('select').on('change', function() {
				self.sendUpdateCats();
			}).prepend('<option></option>');
			newLi.find('select').find('option').first().prop('selected', true);
			self.getEl('addcat_li').before(newLi);

			DP.select(newLi.find('select'));
		});

		this.getEl('categories').on('change', function(ev) {
			self.sendUpdateCats();
		});

		DP.select(this.getEl('categories').find('select'));

		//-----
		// Product manage
		//-----

		this.getEl('products').on('click', '.remove', function(ev) {
			var li = $(this).parent();
			li.remove();

			var lis = $('li:not(.add)', self.getEl('products'));
			if (lis.length == 1) {
				// Hide the remove from the last cat
				$('.remove', lis).hide();
			}

			self.sendUpdateProds();
		});

		this.getEl('addprod_trigger').on('click', function(ev) {
			if (!self.newProdTpl) {
				self.newProdTpl = DeskPRO_Window.util.getPlainTpl(self.getEl('addprod_select_tpl'));
			}

			var newLi = $(self.newProdTpl);
			newLi.find('select').on('change', function() {
				self.sendUpdateProds();
			}).prepend('<option></option>');
			newLi.find('select').find('option').first().prop('selected', true);
			self.getEl('addprod_li').before(newLi);

			DP.select(newLi.find('select'));
		});

		this.getEl('products').on('change', function(ev) {
			self.sendUpdateProds();
		});

		DP.select(this.getEl('products').find('select'));
	},

	sendUpdateCats: function() {

		if (this.sendingCatUpdate) {
			this.resetCatUpdate = true;
			return;
		}
		this.sendingCatUpdate = true;
		this.resetCatUpdate = false;

		var ids = [];
		this.getEl('categories').find('select').each(function() {
			var id = parseInt($(this).val());
			if (id) {
				if (ids.indexOf(id) !== -1) {
					$(this).closest('li').remove()
				} else {
					ids.push(id);
				}
			}
		});

		var formData = $('select', this.getEl('categories')).serializeArray();

		formData.push({
			name: 'action',
			value: 'categories'
		});

		$.ajax({
			url: BASE_URL + 'agent/kb/article/' + this.meta.article_id + '/ajax-save',
			type: 'POST',
			data: formData,
			context: this,
			dataType: 'json',
			success: function(data) {
				this.sendingCatUpdate = false;
				DeskPRO_Window.sections.publish_section.reload();

				// First cat becomes selectable if there are more than one
				if (data && data.category_ids && data.category_ids.length >= 2) {
					this.getEl('categories').find('.remove').show();
				}

				if (this.resetCatUpdate) {
					this.sendUpdateCats();
				}
			}
		});
	},

	sendUpdateProds: function() {
		if (this.sendingProdUpdate) {
			this.resetProdUpdate = true;
			return;
		}
		this.sendingProdUpdate = true;
		this.resetProdUpdate = false;

		var ids = [];
		this.getEl('products').find('select').each(function() {
			var id = parseInt($(this).val());
			if (id) {
				if (ids.indexOf(id) !== -1) {
					$(this).closest('li').remove()
				} else {
					ids.push(id);
				}
			}
		});

		var formData = $('select', this.getEl('products')).serializeArray();

		formData.push({
			name: 'action',
			value: 'products'
		});

		$.ajax({
			url: BASE_URL + 'agent/kb/article/' + this.meta.article_id + '/ajax-save',
			type: 'POST',
			data: formData,
			context: this,
			dataType: 'json',
			success: function(data) {
				this.sendingProdUpdate = false;

				if (this.resetProdUpdate) {
					this.sendUpdateProds();
				}
			}
		});
	},

	//#################################################################
	//# Labels
	//#################################################################

	_initLabels: function() {
		if (this.hasInitLabels) return;
		this.hasInitLabels = true;

		this.labelsInput = new DeskPRO.UI.LabelsInput({
			type: 'article',
			input: this.getEl('labels_input'),
			onChange: this.saveLabels.bind(this)
		});
		this.ownObject(this.labelsInput);
	},

	saveLabels: function() {
		if (this._saveLabelsTimeout) {
			window.clearTimeout(this._saveLabelsTimeout);
		}

		this._labelsData = this.labelsInput.getFormData();
		this._saveLabelsTimeout = this._doSaveLabels.delay(2000, this);
	},

	_doSaveLabels: function() {
		var data = this._labelsData;

		$.ajax({
			url: this.getMetaData('labelsSaveUrl'),
			type: 'POST',
			context: this,
			data: data,
			dataType: 'json',
			success: function(data) {
				this._handleSaveLabelsSuccess(data);
			}
		});
	},

	_handleSaveLabelsSuccess: function(data) {

	},

	_initSearchTab: function() {
		if (this.hasInitSearchTab) return;
		this.hasInitSearchTab = true;

		this.stickyWords = new DeskPRO.Agent.PageFragment.Page.Content.StickyWords(this, {
			contentType: 'articles',
			contentId: this.meta.article_id,
			element: this.getEl('stickysearch_input')
		});
		this.ownObject(this.stickyWords);
	},

	//#################################################################
	//# Automatic Unpublish
	//#################################################################

	_initAutoUnpublishOptions: function() {
		var self = this;

		var optWrap = this.getEl('auto_unpub');

		$('.auto-unpublish-set', optWrap).on('click', function() {
			self.updateAutoUnPubOptions();
			$('.auto-unpublish', optWrap).show();
			$(this).hide();
		});

		$('.remove-auto-unpublish', optWrap).on('click', function() {
			self.removeAutoUnPubOptions();
			$('.auto-unpublish-set', optWrap).show();
			$('.auto-unpublish', optWrap).hide();
		});

		var endOpt = $('.auto-unpublish .end-action.opt', optWrap);
		var m = new DeskPRO.UI.Menu({
			triggerElement: endOpt,
			menuElement: $('.end-action-menu', optWrap),
			onItemClicked: function(info) {
				var val = $(info.itemEl).data('action');
				var label = $(info.itemEl).text().trim();

				endOpt.data('val', val);
				endOpt.text(label);

				self.updateAutoUnPubOptions();
			}
		});
		this.ownObject(m);

		var endDate = $('.auto-unpublish .end-date.opt', optWrap);
		var dateInput = $('.auto-unpublish .end-date-input', optWrap);
		dateInput.datepicker({
			dateFormat: 'M d, yy',
			onSelect: function(dateText, inst) {

				var timestamp = dateInput.datepicker('getDate').getTime() / 1000;

				endDate.data('val', timestamp);
				endDate.text(dateText);

				self.updateAutoUnPubOptions();
			}
		});

		endDate.on('click', function() {
			$('.auto-unpublish .end-date-input', optWrap).datepicker('show');
		});
	},

	removeAutoUnPubOptions: function() {
		$.ajax({
			url: BASE_URL + 'agent/kb/article/' + this.meta.article_id + '/ajax-save',
			type: 'POST',
			data: {action: 'remove-auto-unpub'},
			context: this,
			dataType: 'json'
		});
	},

	updateAutoUnPubOptions: function() {
		var optWrap = this.getEl('auto_unpub');
		var endTimestamp = $('.auto-unpublish .end-date.opt', optWrap).data('val');
		var endAction = $('.auto-unpublish .end-action.opt', optWrap).data('val');

		// Still need them to enter an input
		if (!endTimestamp || !endAction) {
			return;
		}

		var data = [];
		data.push({
			name: 'action',
			value: 'auto-unpub'
		});
		data.push({
			name: 'end_action',
			value: endAction
		});
		data.push({
			name: 'end_timestamp',
			value: endTimestamp
		});

		$.ajax({
			url: BASE_URL + 'agent/kb/article/' + this.meta.article_id + '/ajax-save',
			type: 'POST',
			data: data,
			context: this,
			dataType: 'json'
		});
	},

	//#################################################################
	//# Automatic Publish
	//#################################################################

	_initAutoPublishOptions: function() {
		var self = this;

		var optWrap = this.getEl('auto_pub');

		$('.auto-publish-set', optWrap).on('click', function() {
			self.updateAutoPubOptions();
			$('.auto-publish', optWrap).show();
			$(this).hide();
		});

		$('.remove-auto-publish', optWrap).on('click', function() {
			self.removeAutoPubOptions();
			$('.auto-publish-set', optWrap).show();
			$('.auto-publish', optWrap).hide();
		});

		var pubDate = $('.auto-publish .pub-date.opt', optWrap);
		var dateInput = $('.auto-publish .pub-date-input', optWrap);
		dateInput.datepicker({
			dateFormat: 'M d, yy',
			onSelect: function(dateText, inst) {

				var timestamp = dateInput.datepicker('getDate').getTime() / 1000;

				pubDate.data('val', timestamp);
				pubDate.text(dateText);

				self.updateAutoPubOptions();
			}
		});

		pubDate.on('click', function() {
			$('.auto-publish .pub-date-input', optWrap).datepicker('show');
		});
	},

	removeAutoPubOptions: function() {
		$.ajax({
			url: BASE_URL + 'agent/kb/article/' + this.meta.article_id + '/ajax-save',
			type: 'POST',
			data: {action: 'remove-auto-pub'},
			context: this,
			dataType: 'json'
		});
	},

	updateAutoPubOptions: function() {
		var optWrap = this.getEl('auto_unpub');
		var timestamp = $('.auto-unpublish .end-date.opt', optWrap).data('val');

		// Still need them to enter an input
		if (!timestamp) {
			return;
		}

		var data = [];
		data.push({
			name: 'action',
			value: 'auto-pub'
		});
		data.push({
			name: 'pub_timestamp',
			value: timestamp
		});

		$.ajax({
			url: BASE_URL + 'agent/kb/article/' + this.meta.article_id + '/ajax-save',
			type: 'POST',
			data: data,
			context: this,
			dataType: 'json'
		});
	},

	//#################################################################
	//# Article body
	//#################################################################

	_initArticleArea: function() {
		var loader = this.wrapper.find('.article-loading');

		loader.show();

		var iframe = this.wrapper.find('.article-iframe');
		var iframeLoad = function() {
			if (this.contentWindow && this.contentWindow.document) {
				loader.hide();
				$(this).css({
					overflow: 'hidden',
					border: 'none',
					padding: 0,
					margin: 0
				});
				$(this).height($(this.contentWindow.document).height());

				var doc = this.contentWindow.document, iframeWindow = this.contentWindow;

				var wheel = function(e) {
					e = e || iframeWindow.event;
					var scroller = iframe.closest('.with-scrollbar').get(0), proxyE;
					if (scroller.dispatchEvent) {
						try {
							proxyE = document.createEvent('MouseWheelEvent');
							proxyE.initMouseWheelEvent(
								e.type, e.bubbles, e.cancelable, window, e.detail,
								e.screenX, e.screenY, e.clientX, e.clientY,
								e.button, null, '', e.wheelDelta
							);
						} catch (e) {
							proxyE = null;
						}

						if (!proxyE) {
							proxyE = document.createEvent('MouseEvent');
							proxyE.initMouseEvent(
								e.type, e.bubbles, e.cancelable, window, e.detail,
								e.screenX, e.screenY, e.clientX, e.clientY,
								e.ctrlKey, e.altKey, e.shiftKey, e.metaKey, e.button,
								null
							);
						}
						iframe.closest('.with-scrollbar').get(0).dispatchEvent(proxyE);
					} else {
						proxyE = document.createEventObject(e);
						proxyE.view = window;
						iframe.closest('.with-scrollbar').get(0).fireEvent("onmousewheel", proxyE);
					}
				};
				if (doc.addEventListener){
					doc.addEventListener('DOMMouseScroll', wheel, false);
					doc.addEventListener('mousewheel', wheel, false);
				} else {
					doc.onmousewheel = wheel;
				}
			}
		};

		iframe.on('load', iframeLoad);
		iframeLoad.call(iframe);
	},

	//#################################################################
	//# Editor
	//#################################################################

	_initPostArea: function() {
		this._hasInitEd = false;
		this.getEl('cancel_btn').off('click').on('click', (function() {
			this.hideEditor();

			// Cancel the edit field too, set it back to what it was
			if (!this.wrapper.find('.revert-default')[0]) {
				var def = this.wrapper.find('textarea.edit-content-field-default').val();
				this.wrapper.find('textarea.edit-content-field').val(def);
				if (this.rte) {
					this.rte.val(def);
				}
			}
		}).bind(this));

		var attachList = $('ul.attachment-list:first', this.wrapper);
		if (attachList.length) {

			this.getEl('attachtab').empty().append(attachList);

			var imageEls = $('li.is-image a', attachList);

			imageEls.colorbox({
				title: function(){ var url = $(this).attr('href'); return '<a href="'+url+'" target="_blank">Open In New Window</a>' },
				width: '50%',
				height: '50%',
				initialWidth: '200',
				initialHeight: '150',
				scalePhotos: true,
				photo: true,
				opacity: 0.5,
				transition: 'none'
			});
		}

		if (this.editStateSaver) {
			this.editStateSaver.destroy();
		}

		this.editStateSaver = new DeskPRO.Agent.PageHelper.StateSaver({
			stateId: 'editarticle.' + this.article_id,
			listenOn: $('.article-editor-wrap:first', wrap)
		});
		this.ownObject(this.editStateSaver);

		var wrap = this.wrapper;

		this.getEl('save_btn').off('click').on('click', (function(ev) {
			ev.preventDefault();

			var data = [];
			data.push({
				name: 'action',
				value: 'content'
			});
			data.push({
				name: 'content',
				value: $('.article-editor-wrap textarea:first', wrap).val()
			});
			data.push({
				name: 'language_id',
				value: wrap.find('.article-editor.wrap').find('.language_id').val()
			});

			$('input.edit-content-attach:checked', wrap).each(function() {
				data.push({
					name: 'attach[]',
					value: $(this).val()
				});
			});

			var showSaving = this.getEl('article_save').find('.mark-loading');
			var showSaved  = this.getEl('article_save').find('.mark-saved');

			showSaved.stop().hide();
			showSaving.show();

			$.ajax({
				url: BASE_URL + 'agent/kb/article/' + this.meta.article_id + '/ajax-save',
				type: 'POST',
				context: this,
				data: data,
				dataType: 'json',
				complete: function() {
					showSaving.hide();
				},
				success: function(data) {
					this.getEl('content_ed').html(data.content_html);
					this._initPostArea();
					this._initArticleArea();
					this.handleUnloadRevisions(data.revision_id);

					showSaved.show().fadeOut(2000);
				}
			});

		}).bind(this));

		this.hideEditor();
	},

	destroyPage: function() {
		// Workaround for tinymce bug to do with remove()
		// We'll manually remove the node ourselves
		var el = $('.article-editor-wrap', this.getEl('content_ed'));
		if (el[0]) {
			el.get(0).parentNode.removeChild(el.get(0));
		}
	},

	showEditor: function() {

		$('body').addClass('content-link-control-on');

		var self = this;

		$('.article-content-wrap', this.getEl('content_ed')).hide();
		var edWrap = $('.article-editor-wrap', this.getEl('content_ed')).show();

		$('.revert-default', edWrap).on('click', function() {
			var def = $('textarea.edit-content-field-default').val();
			$('textarea.edit-content-field').val(def);

			$('.revert-message-notice', edWrap).remove();
		});

		if (!this._hasInitEd) {
			this._hasInitEd = true;

			var txt = $('.edit-content-field', this.getEl('content_ed'));
			var w = $(txt.closest('.content-tab-item')).width() - 30;

			// Means the whole thign is visible at once, lets try and max out the viewport
			if (this.wrapper.find('> .layout-content > .scrollbar.disabled')) {
				var h = $(window).height() - 90 - txt.offset().top;
			} else {
				h = 425;
			}

			txt.css({ width: w, height: h });

			this.rte = DP.rteTextarea(txt, {
				setup: function(ed) {
					ed.onKeyPress.add(function() {
						self.editStateSaver.triggerChange();
					});
				}
			});

			var saveBtn = this.getEl('save_btn');
			this.acceptContentLink = new DeskPRO.Agent.PageHelper.AcceptContentLink({
				page: this,
				rte: txt,
				isReadyCallback: function() {
					return saveBtn.is(':visible');
				}
			});

			this._hasInitEdBefore = true;
		}

		this.getEl('edit_btn').hide();
		this.getEl('save_btn').show();
		this.getEl('cancel_btn').show();
		this.updateUi();
	},

	hideEditor: function() {
		$('body').removeClass('content-link-control-on');
		this.getEl('edit_btn').show();
		this.getEl('save_btn').hide();
		this.getEl('cancel_btn').hide();
		$('.article-editor-wrap', this.getEl('content_ed')).hide();
		$('.article-content-wrap', this.getEl('content_ed')).show();
		this.updateUi();
	},

	_initMediaBrowser: function() {
		if (this.mediabrowser_has_init) return;
		this.mediabrowser_has_init = true;

		this.mediaBrowserEl = $('.media-browser', this.wrapper);
		this.mediaBrowserOverlay = new DeskPRO.UI.Overlay({
			contentElement: this.mediaBrowserEl
		});

		this.mediaBrowser = new DeskPRO.Agent.MediaBrowser({
			wrapper: this.mediaBrowserEl,
			additionalDropZone: $('.kb-editor > textarea', this.wrapper)
		});
	},

	showMediaBrowser: function() {
		this._initMediaBrowser();
		this.mediaBrowserOverlay.openOverlay();
	},

	//#################################################################
	//# Comments
	//#################################################################

	_initCommentForm: function() {

		this.commentsController = new DeskPRO.Agent.PageHelper.Comments(this, {
			commentsWrapper: this.getEl('comments_wrap')
		});
		this.ownObject(this.commentsController);

		this.newCommentWrapper = $('.new-note:first', this.wrapper);
		$('button', this.newCommentWrapper).on('click', this.saveNewComment.bind(this));
	},

	saveNewComment: function() {

		var loadingOn = $('.loading-on', this.newCommentWrapper).show();
		var loadingOff = $('.loading-off', this.newCommentWrapper).hide();

		var data = [];
		data.push({
			name: 'content',
			value: $('textarea', this.newCommentWrapper).val()
		});

		$.ajax({
			url: BASE_URL + 'agent/kb/article/' + this.getMetaData('article_id') + '/ajax-save-comment',
			type: 'POST',
			context: this,
			data: data,
			dataType: 'html',
			success: function(html) {
				loadingOn.hide();
				loadingOff.show();

				$('textarea', this.newCommentWrapper).val('');
				var el = $(html);
				this.newCommentWrapper.before(el);

				DeskPRO_Window.util.modCountEl(this.getEl('count_comments'), '+');

				if (DeskPRO_Window.sections.publish_section) {
					DeskPRO_Window.sections.publish_section.modCommentCount('articles', '+');
				}
			}
		});
	},

	//#################################################################
	//# Translations
	//#################################################################

	_initTrans: function() {
		var self = this;
		var transGroup = this.wrapper.find('.trans-input-form');
		if (!transGroup[0]) {
			return;
		}

		transGroup.find('.language_id_switcher').on('click change', function() {
			transGroup.find('.dp-group').removeClass('on');
			$(this).closest('.dp-group').addClass('on');
		});

		transGroup.find('textarea').each(function() {
			var rte = DP.rteTextarea($(this));
			$(this).data('rte', rte);
		});

		transGroup.find('.copy-trigger').on('click', function() {
			var row = $(this).closest('.dp-group');
			var titleInput   = row.find('.title-row').find('input');
			var contentInput = row.find('.editor-row').find('textarea');

			var defaultTitleInput = self.getEl('editname').find('input');
			var defaultContentInput = self.wrapper.find('.article-editor-wrap').find('.edit-content-field-default');

			titleInput.val(defaultTitleInput.val());
			contentInput.tinymce().setContent(defaultContentInput.val());
		});

		transGroup.find('.save-trigger').on('click', function() {
			var row = $(this).closest('.dp-group');
			var titleInput   = row.find('.title-row').find('input');
			var contentInput = row.find('.editor-row').find('textarea');

			var postData = [];
			postData.push({name: titleInput.attr('name'), value: titleInput.val() });
			postData.push({name: contentInput.attr('name'), value: contentInput.val() });
			postData.push({name: 'action', value: 'trans' });

			row.addClass('dp-loading-on');
			$.ajax({
				url: BASE_URL + 'agent/kb/article/' + self.meta.article_id + '/ajax-save',
				type: 'POST',
				data: postData,
				context: this,
				dataType: 'json',
				complete: function() {
					row.removeClass('dp-loading-on');
				},
				success: function(data) {
					row.removeClass('is-status-neg').addClass('is-status-pos');
				}
			});
		});
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');
DeskPRO.Agent.PageFragment.Page.AgentChatTranscript = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.TYPENAME = 'agentchat';
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');
DeskPRO.Agent.PageFragment.Page.UserChat = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.TYPENAME = 'userchat';
	},

	initMetaData: function() {
		DeskPRO_Window.recentTabs.add(
			'userchat',
			this.meta.conversation_id,
			this.meta.title,
			BASE_URL + 'agent/chat/view/' + this.meta.conversation_id
		);
	},

	initPage: function(el) {
		var self = this;

		var OBJ_ID = this.OBJ_ID;
		this.el = el;

		this.chatStatus  = this.meta.status;
		this.chatEndedBy = this.meta.ended_by;

		var messageTextarea = this.getEl('replybox_txt');

		var sendMsg = function() {
			var msg = messageTextarea.val().trim();

			if (messageTextarea.data('redactor')) {
				messageTextarea.setCode('');
				messageTextarea.change();

				self.getEl('replybox').css('height', 40+69);
				self.getEl('messages_box').css('bottom', 40+69);
			} else {
				messageTextarea.val('');
			}

			if (!msg.length) {
				return;
			}

			var tmp_id = Orb.uuid();
			self.addMessageRow(self.meta.youName, msg, 'agent', DeskPRO_Window.canUseAgentReplyRte(), tmp_id, { no_notify: true, person_avatar: self.meta.youPictureUrl });

			self.sendMessage(msg, function(message_id) {
				var d = new Date();

				var a_p = "am";
				var curr_hour = d.getHours();
				if (d.getHours() > 12) {
					a_p = "pm";
				}
				if (curr_hour == 0) {
					curr_hour = 12;
				} else if (curr_hour > 12) {
					curr_hour = curr_hour - 12;
				}

				var curr_min = d.getMinutes();
				curr_min = curr_min + "";
				if (curr_min.length == 1) {
					curr_min = "0" + curr_min;
				}

				var time = curr_hour + ":" + curr_min + "" + a_p;

				// Sets the real message ID after we've come back from ajax
				self.getEl('messages_box').find('.message-' + tmp_id).addClass('message-' + message_id).addClass('server-ack').data('message-id', message_id).attr('title', 'User read message at: ' + time);
			});
		}

		this.doSendMsg = function() {
			sendMsg();
		}

		messageTextarea.on('keypress', function(ev) {
			if (ev.keyCode == 13 && !ev.metaKey) {
				ev.preventDefault();
				sendMsg();
			}
		});

		this.addEvent('destroy', function() {
			DeskPRO_Window.getMessageBroker().removeTaggedListeners(OBJ_ID)
			if (self.chatStatus == 'ended') {
				return;
			} if (self.closeAction == 'unassign') {
				self.leaveConvo('unassign');
			} else if (self.closeAction == 'end') {
				self.leaveConvo('end');
			} else {
				self.leaveConvo(null);
			}
		});

		this._initMenus();
		this._initAssignControl();
		this._initBlock();
		this._initLabels();

		DeskPRO_Window.getMessageBroker().addMessageListener('chat_convo.' + this.meta.conversation_id + '.newmessage', this.handleNewMessageCm, this, [this.OBJ_ID]);
		DeskPRO_Window.getMessageBroker().addMessageListener('chat_convo.' + this.meta.conversation_id + '.hidden_newmessage', this.handleNewMessageCm, this, [this.OBJ_ID]);
		DeskPRO_Window.getMessageBroker().addMessageListener('chat_convo.' + this.meta.conversation_id + '.ended', this.chatHasEnded, this, [this.OBJ_ID]);
		DeskPRO_Window.getMessageBroker().addMessageListener('chat_convo.' + this.meta.conversation_id + '.reassigned', function(data) { this.chatReassignedTo(data.agent_id); }, this, [this.OBJ_ID]);
		DeskPRO_Window.getMessageBroker().addMessageListener('chat_convo.' + this.meta.conversation_id + '.unassigned', function(data) { this.chatReassignedTo(data.agent_id); }, this, [this.OBJ_ID]);
		DeskPRO_Window.getMessageBroker().addMessageListener('chat_convo.' + this.meta.conversation_id + '.usertyping', function(data) { this.userTyping(data); }, this, [this.OBJ_ID]);
		DeskPRO_Window.getMessageBroker().addMessageListener('chat_convo.' + this.meta.conversation_id + '.ack_messages', function(data) { this.ackMessages(data); }, this, [this.OBJ_ID]);

		DeskPRO_Window.getMessageBroker().addMessageListener('chat.ended', function(data) {
			if (this.meta.conversation_id == data.conversation_id) {
				this.chatStatus = 'ended';
			}
		} , this, [this.OBJ_ID]);

		//------------------------------
		// Editor
		//------------------------------

		var textarea = this.getEl('replybox_txt'), isWysiwyg = false;

		if (DeskPRO_Window.canUseAgentReplyRte()) {
			isWysiwyg = true;

			DeskPRO_Window.initRteAgentReply(textarea, {
				defaultIsHtml: true,
				minHeight: 65,
				maxHeight: 40,
				inlineHiddenPosition: this.getEl('is_html_reply'),
				convertLinks: false, // we'll do it ourselves
				callback: function(obj) {
					obj.addBtnFirst('dp_attach', 'Click here to attach a file. You may also drag a file from your computer desktop into this reply area to upload attachments faster.', function(){});
					obj.addBtnAfter('dp_attach', 'dp_snippets', 'Open snippets', function(){});

					obj.addBtnAfter('horizontalrule', 'dp_create_ticket', 'Create Ticket', function(){
						DeskPRO_Window.newTicketLoader.open(function(page) {
							page.setNewByChat({ chat_id: self.meta.conversation_id, chat_title: self.meta.chatTitle, person_id: self.meta.person_id, sesson_id: self.meta.session_id, email: self.meta.email });
						});
					});
					obj.addBtnAfter('dp_create_ticket', 'dp_end_chat', 'End Chat', function(){
						self.endChat();
					});

					obj.addBtnAfter('dp_end_chat', 'dp_send_message', 'Send your message (or press the Enter or Return key on your keyboard)', function(){
						self.doSendMsg();
					});

					obj.addBtnSeparatorAfter('dp_attach');
					obj.addBtnSeparatorAfter('dp_snippets');
					obj.addBtnSeparatorAfter('horizontalrule');

					snippetBtn = obj.$toolbar.find('.redactor_btn_dp_snippets').closest('li');
					snippetBtn.addClass('snippets').find('a').html('<span class="show-key-shortcut">S</span>nippets');

					var tmp = obj.$toolbar.find('.redactor_btn_dp_attach').closest('li');
					tmp.addClass('attach');
					tmp.find('a').text('Attach').append('<input type="file" class="file" name="file-upload" />');

					tmp = obj.$toolbar.find('.redactor_btn_dp_send_message').closest('li');
					tmp.addClass('dp_send_message');
					tmp.find('a').text('Send');
				}
			});
			this.getEl('is_html_reply').val(1);

			if (textarea.data('redactor')) {
				var ed = textarea.getEditor();
				ed.on('keypress', function(ev) {
					if (ev.keyCode === 13 && !ev.shiftKey && !ev.ctrlKey && !ev.metaKey) {
						ev.preventDefault();
						window.setTimeout(function() {
							ed.linkify();
							window.setTimeout(function() {
								sendMsg();
							}, 100);
						}, 10);
					}
				});

				var lastH = ed.height();
				ed.on('keypress change', function() {
					window.setTimeout(function() {
						var tmp = ed.height();
						if (lastH != tmp) {
							lastH = tmp;
							self.getEl('replybox').css('height', lastH+44);
							self.getEl('messages_box').css('bottom', lastH+44);
						}
					}, 50);
				});
			}
		}

		if (textarea.data('redactor')) {
			var ed = textarea.getEditor();
			var api = textarea.data('redactor');

			var te = new DeskPRO.TextExpander({
				textarea: ed,
				onCombo: function(combo, ev) {
					combo = combo.replace(/%/g, '');
					if (window.DESKPRO_CHAT_SNIPPET_SHORTCODES && window.DESKPRO_CHAT_SNIPPET_SHORTCODES[combo]) {
						ev.preventDefault();

						var snippetId = window.DESKPRO_CHAT_SNIPPET_SHORTCODES[combo];

						var focus = api.getFocus(),
							focusNode = $(focus[0]),
							testText;

						if (focus[0].nodeType == 3) {
							testText = focusNode.text().substring(0, focus[1]);
						} else {
							focus[0] = focusNode.contents().get(focus[1] - 1);
							focusNode = $(focus[0]);
							testText = focusNode.text();
							focus[1] = testText.length;
						}

						var	lastAt = testText.lastIndexOf('%'), matches = [];

						if (lastAt != -1) {
							api.setSelection(focus[0], lastAt, focus[0], focus[1]);
						}

						// web kit handles content editable without an issue. this prevents the span
						// from being extended unnecessarily
						var editable = $.browser.webkit ? ' contenteditable="false"' : '';
						api.insertHtml('<span class="editor-inserting-var snippet-'+snippetId+'" ' + editable + ' data-snippet-id="' + snippetId + '">Inserting snippet...</span>');

						$.ajax({
							url: BASE_URL + 'agent/text-snippets/chat/'+snippetId+'.json',
							dataType: 'json',
							success: function(data) {

								var snippet = data.snippet;
								var snippetId    = snippet.id;
								var snippetCode  = snippet.snippet;

								var agentText;
								var defaultText;
								var wantText;
								var useText;
								var result;

								Array.each(snippetCode, function(info) {
									if (info.language_id == DESKPRO_DEFAULT_LANG_ID) {
										defaultText = info.value;
									}
									useText = info.value;
								});

								if (wantText) {
									useText = wantText;
								} else if (agentText) {
									useText = agentText;
								} else if (defaultText) {
									useText = defaultText;
								}

								var el = api.$editor.find('.editor-inserting-var.snippet-' + snippetId);

								var wrapper = $('<div/>');
								wrapper.html(useText);

								if (wrapper.find('> div, > p, > span')[0]) {
									data = wrapper.find('> *');
								} else {
									data = wrapper;
								}

								// trailing newlines
								var coll;
								if (data.length == 1) {
									coll = data;
								} else {
									coll = data.find('> p');
								}
								coll.each(function() {
									var l = $(this).find('> *').last();
									if (l.is('br')) {
										l.remove();
									}
								});

								if (data.find('> div, > span, > p').length == 1) {
									var span = $('<span></span>');
									span.append(data.find('> *'));
									data = span;
								} else if (data.find('> *').length == 0) {
									var span = $('<span></span>');
									span.html(data.html());
									data = span;
								}

								data.append('<span class="_cursor"></span>');
								var cursor = data.find('._cursor');

								el.after(data);
								el.remove();
								api.setSelection(cursor[0], 0, cursor[0], 0);
							}
						});
					}
				}
			});
		}

		//------------------------------
		// Snippets Viewer
		//------------------------------

		this.snippetsViewer = new DeskPRO.Agent.Widget.SnippetViewer({
			triggerElement: snippetBtn,
			snippetType: 'chat',
			onSnippetClick: function(info) {
				var snippetId    = info.snippetId;
				var snippetCode  = info.snippetCode;

				var agentText;
				var defaultText;
				var wantText;
				var useText;
				var result;

				Array.each(snippetCode, function(info) {
					if (info.language_id == DESKPRO_DEFAULT_LANG_ID) {
						defaultText = info.value;
					}
					useText = info.value;
				});

				if (wantText) {
					useText = wantText;
				} else if (agentText) {
					useText = agentText;
				} else if (defaultText) {
					useText = defaultText;
				}

				var val = useText;

				var messageTextarea = self.getEl('replybox_txt')

				var data = $('<div></div>').html(val);
				if (data.find('> span, > div, > p').length == 1) {
					var span = $('<span></span>');
					span.append(data.find('> *'));
					data = span;
				} else if (data.find('> span, > div, > p').length == 0) {
					var span = $('<span></span>');
					span.html(data.html());
					data = span;
				}

				val = data.html();

				if (messageTextarea.data('redactor')) {

					try {
						messageTextarea.data('redactor').restoreSelection();
						messageTextarea.data('redactor').setBuffer();
					} catch (e) {}

					var html = val;
					html = html.replace(/<\/p>\s*<p>/g, '<br/>');
					html = html.replace(/^<p>/, '');
					html = html.replace(/<\/p>$/, '');
					messageTextarea.data('redactor').insertHtml(html);
					messageTextarea.change();
					window.setTimeout(function() {
						var tmp = ed.height();
						if (lastH != tmp) {
							lastH = tmp;
							self.getEl('replybox').css('height', lastH+69);
							self.getEl('messages_box').css('bottom', lastH+69);
						}
					}, 100);
				} else {
					var pos = messageTextarea.getCaretPosition();
					if (!pos) {
						messageTextarea.setCaretPosition(0);
					}

					messageTextarea.insertAtCaret(val);
				}

				self.snippetsViewer.close();
			}
		});

		//------------------------------
		// Intercept close events and cancel, so we
		// can confirm
		//------------------------------

		this.closeAction = false;
		this._confirmCloseOverlay = new DeskPRO.UI.Overlay({
			contentElement: this.getEl('closetab_prompt'),
			addClassname: 'normal-size',
			onPosition: function(evData) {
				var tabId = self.getTabId();
				if (!tabId) return;

				var tabEl = $('#tabbtn_' + tabId);
				if (!tabEl[0]) {
					return;
				}
				var tabW = tabEl.width();

				evData.left = (tabEl.offset().left + (tabW / 2)) - (evData.w / 2);
				evData.top = tabEl.offset().top;

				if ((evData.left+evData.w) > evData.pageW) {
					evData.left = evData.pageW - evData.w - 15;
				}
			},
			onContentSet: function(eventData) {
				$('.unassign-trigger').on('click', function() {
					self._confirmCloseOverlay.close();
					self.closeAction = 'unassign';
					DeskPRO_Window.TabBar.removeTabById(self.meta.tabId);
				});
				$('.end-trigger').on('click', function() {
					self._confirmCloseOverlay.close();
					self.closeAction = 'end';
					DeskPRO_Window.TabBar.removeTabById(self.meta.tabId);
				});
				$('.cancel-trigger').on('click', function() {
					self._confirmCloseOverlay.close();
				});
			}
		});

		this.addEvent('closeTab', function(event) {
			// Already ended or not assigned to us
			if (this.getEl('assign_btn').data('agent-id') != DESKPRO_PERSON_ID || this.chatStatus == 'ended') {
				return;
			}

			if (this.chatStatus == 'ended') {
				return;
			}

			if (this.closeAction) return;
			event.deskpro.cancelClose = true;

			this._confirmCloseOverlay.open();
		}, this);

		this.getEl('create_ticket_btn2').on('click', function() {
			DeskPRO_Window.newTicketLoader.open(function(page) {
				page.setNewByChat({ chat_id: self.meta.conversation_id, chat_title: self.meta.chatTitle, person_id: self.meta.person_id, sesson_id: self.meta.session_id, email: self.meta.email });
			});
		});

		var imposter = this.getEl('imposter');
		if (imposter[0]) {
			imposter.find('button.dismiss').on('click', function() {
				imposter.fadeOut('fast', function() {
					imposter.remove();
				});
			});
		}

		// Dismiss the notification at the same time
		var notifRow = DeskPRO_Window.notifications.findRow('id-chat-' + this.meta.conversation_id);
		if (notifRow) {
			DeskPRO_Window.notifications.removeRow(notifRow);
		}

		this.getEl('replybox_txt').on('focus', function() {
			$(this).addClass('is-focused');
		});
		this.getEl('replybox_txt').on('blur', function() {
			$(this).removeClass('is-focused');
		});

		this.getEl('messages_box').on('click', '.truncated-wrap', function() {
			var content = $(this).find('textarea').val();
			self.showFullMessage(content);
		});

		this.chatFields = new DeskPRO.Agent.PageHelper.ChatFields(this);
		this.chatFields.updateDisplay();
		this.ownObject(this.chatFields);

		var maintabs = this.getEl('main_tabs_nav');
		if (maintabs.data('simpletabs')) {
			maintabs.data('simpletabs').addEvent('tabSwitch', function(evData) {
				var tabEl = $(evData.tabEl);
				if (tabEl[0]) {
					if (tabEl.hasClass('is-fields-tab')) {
						self.getEl('field_edit_controls').show();
					} else {
						self.getEl('field_edit_controls').hide();
					}
				}
			});
		}

		// Init upload after RTE was added (it has an upload field on the 'attach' button)
		this._initUpload();
	},

	handleNewMessageCm: function(data, name) {

		// Ignore our own messages, unless its a file then we have a rendered version from the server
		if (data.author_type && data.author_type == 'agent' && data.author_id && data.author_id == DESKPRO_PERSON_ID && !(data.metadata && data.metadata.type && data.metadata.type == 'file')) {
			return;
		}

		// Fixes when typing indicator gets out of sync with messages
		// Can happen if a CM for new message comes in after typing was started but before it stopped
		if (this.userTypingTime && (new Date()).getTime() > this.userTypingTime) {
			this.userTypingTime = null;
			this.getEl('user_typing').hide();
		}

		this.addMessageRow(data.author_name, data.content, data.author_type, data.is_html, data.message_id, data.metadata, data);
	},

	chatReassignedTo: function(agent_id) {

		var btnEl = this.getEl('assign_btn');

		if (agent_id == "0") {
			var pic = '';
			var agentInfo = {
				name: 'Unassigned'
			};
		} else {
			var agentInfo = DeskPRO_Window.getAgentInfo(agent_id);
			if (!agentInfo) {
				return;
			}

			var pic = agentInfo.pictureUrlSizable.replace('{SIZE}', 20);
		}

		$('li', this.getEl('agent_parts')).show();
		if (agent_id != '0') {
			$('li.agent-' + agent_id, this.getEl('agent_parts')).hide();
		}
		if ($('li:visible', this.getEl).length) {
			this.getEl('agent_parts_none').hide();
		} else {
			this.getEl('agent_parts_none').show();
		}

		this.getEl('agent_assign_ob').data('assigned', agent_id);
		btnEl.css('background-image', pic);
		btnEl.text(agentInfo.name);
		btnEl.data('agent-id', agent_id);
	},

	addPart: function(agent_id) {
		if ($('.agent-' + agent_id, this.getEl('agent_parts')).length) {
			return;
		}

		var agentInfo = DeskPRO_Window.getAgentInfo(agent_id);

		var li = $('<li><a></a></li>');
		li.addClass('agent-' + agent_id);
		$('a', li).text(agentInfo.name).addClass('agent-link').css({
			'background-image': agentInfo.pictureUrlSizable.replace('{SIZE}', 20)
		})

		this.getEl('agent_parts').append(li);

		if (this.getEl('assign_btn').data('agent-id') == agent_id) {
			li.hide();
		}

		if ($('li:visible', this.getEl).length) {
			this.getEl('agent_parts_none').show();
		}
	},

	removePart: function(agent_id) {
		$('.agent-' + agent_id, this.getEl('agent_parts')).remove();

		if (!$('li:visible', this.getEl).length) {
			this.getEl('agent_parts_none').hide();
		}
	},

	updateActiveAgentList: function(assigned, parts) {
		var assigned_name = DeskPRO_Window.getDisplayName('agent', agent_id) || 'Unassigned';
		$('span.agent_id.val', this.el).html(assigned_name);

		var ul = $('.convo_participants ul', this.el);
		ul.empty();

		if (!parts.lenght) {
			ul.append('<li class="agent-0">None</li>');
		} else {
			Array.each(parts, function(agent_id) {
				var name = DeskPRO_Window.getDisplayName('agent', agent_id);
				ul.append('<li class="agent-'+agent_id+'">'+name+'</li>');
			});
		}
	},

	userTyping: function(data) {
		if (!data || !data.preview || !data.preview.length) {
			this.getEl('user_typing').hide();
			return;
		}

		var preview = data.preview;
		if (preview.length > 500) {
			preview = '...' + preview.substring(preview.length - 500);
		}

		this.userTypingTime = (new Date()).getTime();
		var el = this.getEl('user_typing');
		$('.prop-msg', el).text(preview);
		el.detach().appendTo(this.getEl('messages_box'));
		el.show();

		this.updateUi.bind(this);
		this.getEl('messages_box').scrollTop(10000);
	},

	ackMessages: function(data) {
		if (!data || !data.message_ids || !data.message_ids.length) {
			return;
		}

		Array.each(data.message_ids, function (message_id) {
			this.getEl('messages_box').find('.message-' + message_id).addClass('user-ack');
		}, this);
	},

	_initMenus: function() {
		var self = this;

		//------------------------------
		// Department
		//------------------------------

		var dep = this.getEl('department_id');
		DP.select(dep);

		dep.on('change', function() {
			var depId = parseInt($(this).val());

			DeskPRO_Window.util.ajaxWithClientMessages({
				url: BASE_URL + 'agent/chat/change-props/' + self.meta.conversation_id,
				data: [{ name: 'props[department_id]', value: depId }],
				type: 'POST'
			});
		});
	},

	endChat: function() {
		DeskPRO_Window.util.ajaxWithClientMessages({
			url: BASE_URL + 'agent/chat/end-chat/' + this.meta.conversation_id
		});
	},

	leaveChat: function() {
		if (this.chatStatus == 'ended' || this.getEl('assign_btn').data('agent-id') != DESKPRO_PERSON_ID) {
			return;
		}
		$.ajax({
			url: BASE_URL + 'agent/chat/assign/' + this.meta.conversation_id + '/0',
			data: { 'leaving': true },
			context: this,
			contentType: 'json'
		});
	},

	chatHasEnded: function(data) {

	},

	addPart: function(agent_id) {
		$.ajax({
			url: BASE_URL + 'agent/chat/add-part/' + this.meta.conversation_id + '/' + agent_id,
			context: this,
			contentType: 'json'
		});
	},

	syncPars: function(agent_ids) {
		var postData = [];
		Array.each(agent_ids, function(id) {
			postData.push({ name: 'agent_ids[]', value: id });
		});
		$.ajax({
			url: BASE_URL + 'agent/chat/sync-parts/' + this.meta.conversation_id,
			data: postData,
			type: 'POST',
			context: this,
			contentType: 'json'
		});
	},

	reassignConvo: function(agent_id) {
		DeskPRO_Window.util.ajaxWithClientMessages({
			url: BASE_URL + 'agent/chat/assign/' + this.meta.conversation_id + '/' + agent_id
		});
	},

	leaveConvo: function(after) {
		this.quitting = true;
		var self = this;

		var action = '';
		if (after) {
			if (after == 'unassign') {
				action = 'unassign';
			} else if (after == 'end') {
				action = 'end';
			}
		}

		DeskPRO_Window.util.ajaxWithClientMessages({
			url: BASE_URL + 'agent/chat/leave/' + this.meta.conversation_id,
			data: {
				action: action
			}
		});
	},

	addMessageRow: function(name, msg, type, is_html, message_id, metadata, reqData) {

		var notify = true;
		if (message_id && $('.message-' + message_id, this.getEl('messages_box')).length) {
			return;
		}

		if (type == 'sys') {
			name = '* ';
		} else {
			name = '&lt;' + name + '&gt; ';
		}

		var popoutclass = '';
		if (type == 'user') {
			popoutclass = " person-overview";
		}

		var addclass = '';
		if (metadata && metadata.new_user_track) {
			this.userTyping();
			addclass = 'user-track';
			notify = false;

			this.getEl('messages_box').find('.row.agent').addClass('user-ack');
		}

		var avatarHtml = '';
		var person_avatar = metadata.person_avatar || this.meta.userPictureUrl;
		person_avatar = person_avatar.replace(/\/avatar\/\d+\//, "/avatar/25/", person_avatar);
		person_avatar = person_avatar.replace(/\/size\/\d+\//, "/size/25/", person_avatar);
		if (person_avatar.indexOf('gravatar.com') !== -1) {
			person_avatar = person_avatar.replace(/&?s=\d+\//, "", person_avatar);
			person_avatar = Orb.appendQueryData(person_avatar, 's', '25');
		}

		avatarHtml = '<div class="avatar tipped" title="'+ Orb.escapeHtml(metadata.author_name || '') +'"><img src="' + person_avatar + '" /></div>';

		var html = ['<div class="row '+type+' ' + addclass + '"><div class="message-content">'];
			if (type == 'sys') {
				html.push('<div class="message prop-msg"></div><time></time>');
			} else if (type == 'agent') {
				html.push(avatarHtml);
				html.push('<div class="chatSend"><div class="chatMsgSend"><div class="prop-msg"></div><span class="bubbleLeft"></span></div></div><time></time><span class="ack-icon"></span>');
				html.push('<div class="chat-clear"></div>');
			} else if (type == 'user') {
				this.userTyping();
				html.push(avatarHtml);
				html.push('<div class="chatRecieve"><div class="chatMsgRecieve"><div class="prop-msg"></div><span class="bubbleRight"></span></div></div><time></time>');
				html.push('<div class="chat-clear"></div>');
			}
		html.push('</div></div>');

		var row = $(html.join(''));

		var d = new Date();

		var a_p = "am";
		var curr_hour = d.getHours();
		if (d.getHours() > 12) {
			a_p = "pm";
		}
		if (curr_hour == 0) {
			curr_hour = 12;
		} else if (curr_hour > 12) {
			curr_hour = curr_hour - 12;
		}

		var curr_min = d.getMinutes();
		curr_min = curr_min + "";
		if (curr_min.length == 1) {
			curr_min = "0" + curr_min;
		}


		$('time', row).text(curr_hour + ":" + curr_min + "" + a_p);

		if (message_id) {
			row.addClass('message-' + message_id);
		}

		if (type == 'sys') {
			if (msg.indexOf('{"phrase_id":') === 0) {
				try {
					var data = $.parseJSON(msg);
				} catch(e) {
					console.error(e);
					data = {};
				}

				if (data.phrase_id) {
					msg = DeskPRO_Window.getTranslate().phrase('agent.userchat.' + data.phrase_id, data, true);
				} else {
					msg = 'unknown phrase';
				}
			}
		}

		if (is_html) {
			var titleMsg = 'New chat message';
			$('.prop-msg', row).html(msg);
		} else {
			var titleMsg = msg;
			var isTruncated = false;
			var origMsg = msg;
			if (type == 'user' && msg.length > 500) {
				isTruncated = true;
				msg = msg.substring(0, 500);
			}

			msg = Orb.escapeHtml(msg);
			msg = Orb.nl2br(msg);
			msg = DeskPRO_Window.util.linkUrls(msg);

			if (isTruncated) {
				msg += ' <div class="truncated-wrap"><div class="truncated-btn">&bull; &bull; &bull;</div><textarea class="orig-message" style="display:none;">' + Orb.escapeHtml(origMsg) + '</textarea></div>';
			}

			$('.prop-msg', row).html(msg);
		}


		$('time', row).attr('datetime', (new Date()).toString());

		DeskPRO_Window.initInterfaceLayerEvents(row);
		row.appendTo(this.getEl('messages_box'));

		this.getEl('messages_box').scrollTop(10000);

		// Ignore our own messages
		if (notify) {
			if (reqData && reqData.author_type && reqData.author_type == 'agent' && reqData.from_client == DESKPRO_SESSION_ID) {
				notify = false;
			}
		}

		if (metadata && metadata.no_notify) {
			notify = false;
		}

		if (notify && !this.quitting) {
			this.alertTab();

			DeskPRO_Window.faviconBadge.enableCrazyMode(titleMsg);

			// Add 'pop' sound if its not us
			var alertEl = $.tmpl('user_chat_newmsg_sound');
			alertEl.appendTo(this.el);
			DeskPRO_Window.handleSoundElements(alertEl);
		}

		this.updateUi();
	},

	sendMessage: function(msg, success) {
		DeskPRO_Window.util.ajaxWithClientMessages({
			type: 'POST',
			url: BASE_URL + 'agent/chat/send-message/' + this.meta.conversation_id,
			data: {
				content: msg,
				is_html: DeskPRO_Window.canUseAgentReplyRte()
			},
			execSuccessBefore: true,
			success: function(data) {
				if (success && data.message_id) {
					success(data.message_id);
				}
			}
		});
	},

	sendInvite: function(agent_id) {
		DeskPRO_Window.util.ajaxWithClientMessages({
			url: BASE_URL + 'agent/chat/invite/' + this.meta.conversation_id + '/' + agent_id
		});
	},

	showFullMessage: function(msg) {
		if (!this.fullMessageOverlay) {
			this.fullMessageOverlay = new DeskPRO.UI.Overlay({
				contentElement: this.getEl('view_fulle_message_overlay')
			});
		}

		msg = Orb.escapeHtml(msg);
		msg = Orb.nl2br(msg);

		this.getEl('view_full_message_content').html(msg);
		this.fullMessageOverlay.openOverlay();
	},

	//#################################################################
	//# Reassignment
	//#################################################################

	_initAssignControl: function() {

		var self = this;
		var followersList = this.getEl('followers_list');
		var el = this.getEl('agent_assign_ob');
		this.assignOptionBox = new DeskPRO.UI.OptionBoxRevertable({
			element: el,
			trigger: this.getEl('assign_ob_trigger'),
			onSave: function(ob) {
				var selections = ob.getAllSelected();

				var agent_id = parseInt(selections.agents || 0);

				followersList.empty();
				var selections = ob.getAllSelected();

				var part_ids = [];
				Array.each(selections.followers, function(part_id) {
					var label = $('.agent-part-label-' + part_id, ob.getElement()).first().text().trim();

					var li = $('<li />');
					var span = $('<span />');
					span.addClass('agent-link');
					span.data('agent-id', part_id);
					span.attr('data-agent-id', part_id);
					span.text(label);
					span.appendTo(li);

					followersList.append(li);

					part_ids.push(part_id);
				});

				if (part_ids.length) {
					self.syncPars(part_ids);
				}

				if (!selections.followers.length) {
					followersList.append('<li>No followers</li>');
				}

				var current_agent = parseInt(el.data('assigned'));
				if (current_agent != agent_id) {
					self.reassignConvo(agent_id);
				}
			}
		});

		var box1 = self.getEl('people_box_person_container');
		var box2 = self.getEl('people_box_agent_container');
		var box1_in = $('> article', box1);
		var box2_in = $('> article', box2);

		var chatView = this.getEl('chat_view'),
			chatPositioner = this.getEl('chat_positioner'),
			header = self.wrapper.find('.page-header');

		var syncSizes = function() {
			var h1 = box1_in.height();
			var h2 = box2_in.height();

			/*var h = (h1 > h2) ? h1 : h2;

			box2.css('min-height', h);
			box1.css('min-height', h);*/

			box1_in.each(function() { var thisH = $(this).outerHeight(); if (thisH > h1) { h1 = thisH; } });
			box2_in.each(function() { var thisH = $(this).outerHeight(); if (thisH > h2) { h2 = thisH; } });

			var h = (h1 > h2) ? h1 : h2;

			box2.css('min-height', h);
			box1.css('min-height', h);
		};

		var syncChatSize = function() {
			chatView.css('top', chatPositioner.outerHeight() + header.outerHeight());
		};

		// TODO handle resize without element resize monitor
		chatPositioner.on('resize', syncChatSize);
		box1.on('resize', syncSizes);
		box2.on('resize', syncSizes);

		syncSizes();
		syncChatSize();
	},

	//#################################################################
	//# Upload message
	//#################################################################

	_initUpload: function() {

		var self = this;

		DeskPRO_Window.util.fileupload(this.el, {
			uploadTemplate: $('.template-upload', this.el),
			downloadTemplate: $('.template-download', this.el),
			dropZone: this.getEl('replybox').find('nav')
		});
		this.el.bind('fileuploaddone', function(ev, data) {
			if (data.result && data.result.length) {
				var items = data.result, x;
				for (x = 0; x < items.length; x++) {
					DeskPRO_Window.util.ajaxWithClientMessages({
						url: BASE_URL + 'agent/chat/send-file-message/' + self.meta.conversation_id,
						data: {send_blob_id: items[0].blob_id }
					});
				}
			}

			self.getEl('uploading_list').hide().find('> ul').empty();
		});
		this.el.bind('fileuploadstart', function() {
			self.getEl('uploading_list').detach().appendTo(self.getEl('messages_box')).show();
			self.updateUi();
			self.getEl('messages_box').scrollTop(10000);
		});
	},

	//#################################################################
	//# Block controls
	//#################################################################

	_initBlock: function() {
		var self = this;
		this.getEl('block_user').on('click', function(ev) {
			$(this).prop('disabled', true).html('<em>Loading</em>');

			ev.preventDefault();

			var postData = {
				block_ip: self.getEl('block_ip').is(':checked') ? 1 : 0,
				reason: self.getEl('block_reason').val()
			};

			$.ajax({
				url: BASE_URL + 'agent/chat/block-user/' + self.meta.conversation_id,
				type: 'POST',
				data: postData,
				dataType: 'json',
				complete: function() {
					self.closeSelf();
					DeskPRO_Window.loadPage(BASE_URL + 'agent/chat/view/' + self.meta.conversation_id, {ignoreExist:true});
				}
			})
		});

		this.getEl('unblock_user').on('click', function(ev) {
			$(this).prop('disabled', true).html('<em>Loading</em>');
			ev.preventDefault();
			$.ajax({
				url: BASE_URL + 'agent/chat/unblock-user/' + self.meta.conversation_id,
				type: 'POST',
				dataType: 'json',
				complete: function() {
					self.closeSelf();
					DeskPRO_Window.loadPage(BASE_URL + 'agent/chat/view/' + self.meta.conversation_id, {ignoreExist:true});
				}
			})
		});
	},

	//#################################################################
	//# Labels
	//#################################################################

	_initLabels: function() {
		if (this.getEl('labels_input')[0]) {
			this.labelsInput = new DeskPRO.UI.LabelsInput({
				type: 'chat_conversations',
				input: this.getEl('labels_input'),
				onChange: this.saveLabels.bind(this)
			});
			this.ownObject(this.labelsInput);
		}
	},

	saveLabels: function() {
		if (this._saveLabelsTimeout) {
			window.clearTimeout(this._saveLabelsTimeout);
		}

		this._labelsData = this.labelsInput.getFormData();
		this._saveLabelsTimeout = this._doSaveLabels.delay(2000, this);
	},

	_doSaveLabels: function() {
		var data = this._labelsData;

		$.ajax({
			url: BASE_URL + 'agent/chat/' + this.meta.conversation_id + '/ajax-save-labels',
			type: 'POST',
			context: this,
			data: data,
			dataType: 'json'
		});
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');
DeskPRO.Agent.PageFragment.Page.FeedbackView = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'feedback';
	},

	initPage: function(el) {

		var self = this;
		this.wrapper = el;

		this.feedback_id = this.getMetaData('feedback_id');

		this._initBasic();
		if (this.meta.canEdit) {
			this._initMenus();
			this._initPostArea();
		}
		this._initActions();
		this._initLabels();
		this._initCommentForm();

		if (this.meta.canEdit) {
			if (this.meta.isValidating) {
				this.validatingEdit = new DeskPRO.Agent.PageHelper.ValidatingEdit(this, {
					typename: 'feedback',
					contentId: this.feedback_id,
					singleTyle: 'feedback'
				});
				this.ownObject(this.validatingEdit);
			}

			this.getEl('edit_btn').on('click', this.showEditor.bind(this));
		}

		this.relatedContent = new DeskPRO.Agent.PageHelper.RelatedContent(this, {
			typename: 'feedback',
			content_id: this.feedback_id,
			listEl: $('section.linked-content:first', this.wrapper),
			disabled: !this.meta.canEdit,
			onContentLinked: function(typename, content_id) {
				$.ajax({
					url: BASE_URL + 'agent/feedback/view/' + self.feedback_id + '/ajax-save',
					type: 'POST',
					data: { content_type: typename, content_id: content_id, action: 'add-related' },
					context: this,
					dataType: 'json'
				});
			},
			onContentUnlinked: function(typename, content_id) {
				$.ajax({
					url: BASE_URL + 'agent/feedback/view/' + self.feedback_id + '/ajax-save',
					type: 'POST',
					data: { content_type: typename, content_id: content_id, action: 'remove-related' },
					context: this,
					dataType: 'json'
				});
			}
		});
		this.ownObject(this.relatedContent);

		DeskPRO_Window.getMessageBroker().addMessageListener('publish.validating.list-remove', function (info) {
			$('article.' + info.typename + '-' + info.contentId).slideUp();
		});

		this.miscContent = new DeskPRO.Agent.PageHelper.MiscContent(this, {
			revisionCompareUrl: BASE_URL + 'agent/feedback/compare-revs/{OLD}/{NEW}'
		});
		this.ownObject(this.miscContent);

		this.whoVotedOverlay = new DeskPRO.UI.Overlay({
			triggerElement: '.who-voted-trigger',
			contentMethod: 'ajax',
			contentAjax: {
				url: BASE_URL + 'agent/publish/rating-who-voted/feedback/' + this.feedback_id
			}
		});
		this.ownObject(this.whoVotedOverlay);

		this.getEl('my_vote').on('click', function() {
			self.toggleMyVote();
		});


		var fieldsRendered = this.getEl('custom_fields_rendered');
		var fieldsForm = this.getEl('custom_fields_editable');

		var buttonsWrap = this.getEl('properties_controls');
		var propToggle = function(what) {
			if (what == 'display') {
				$('.showing-editing-fields', buttonsWrap).hide();
				$('.showing-rendered-fields', buttonsWrap).show();
				fieldsForm.hide();
				fieldsRendered.show();
			} else {
				$('.showing-rendered-fields', buttonsWrap).hide();
				$('.showing-editing-fields', buttonsWrap).show();
				fieldsRendered.hide();
				fieldsForm.show();
			}
		};

		$('.edit-fields-trigger', buttonsWrap).on('click', function() {
			propToggle('edit');
		});

		$('.save-fields-trigger', buttonsWrap).on('click', function() {
			var formData = $('input[type="text"], input[type="password"], input:checked, select, textarea', fieldsForm);

			$.ajax({
				url: BASE_URL + 'agent/feedback/view/' + self.meta.feedback_id + '/ajax-save-custom-fields',
				type: 'POST',
				data: formData,
				dataType: 'html',
				success: function(rendered) {
					fieldsRendered.empty().html(rendered);
					propToggle('display');
				}
			});
		});

		var namef       = this.getEl('showname');
		var editName    = this.getEl('editname');
		var startBtn    = this.getEl('editname_start');
		var stopBtn     = this.getEl('editname_end');

		var startEditable = function() {
			namef.hide();
			editName.show();
			startBtn.hide();
			stopBtn.show();
		};

		var stopEditable = function() {
			var nametxt = editName.find('input').first();
			var setName = nametxt.val().trim();

			if(!setName) {
				return;
			}

			editName.hide();
			startBtn.show();
			namef.show();
			stopBtn.hide();
			namef.text(setName);

			var postData = [];
			postData.push({
				name: 'action',
				value: 'title'
			});
			postData.push({
				name: 'title',
				value: setName
			});

			$.ajax({
				url: BASE_URL + 'agent/feedback/view/' + self.feedback_id + '/ajax-save',
				type: 'POST',
				data: postData,
				success: function(data) {
					self.handleUnloadRevisions(data.revision_id);
				}
			});
		};

		namef.on('dblclick', startEditable).on('keypress', function(ev) {
			if (ev.keyCode == 13 /* enter key */) {
				ev.preventDefault();
				stopEditable();
			}
		});
		this.getEl('editname_start').on('click', startEditable);
		this.getEl('editname_end').on('click', stopEditable);

		if (this.meta.canEdit) {
			var self = this;
			this.getEl('status').on('change', function() {
				var catId = $(this).val();
				$.ajax({
					url: BASE_URL + 'agent/feedback/view/' + self.feedback_id + '/ajax-save',
					type: 'POST',
					data: {action: 'status', status: catId},
					context: self,
					dataType: 'json',
					success: function() {
						DeskPRO_Window.getMessageBroker().sendMessage('agent.ui.feedback-status-update', {
							feedback_id: self.feedback_id,
							new_status: catId
						});
					}
				});
			});
		} else {
			this.getEl('status').prop('disabled', true);
		}

		DP.select(this.getEl('status'));

		this.deleteHelper = new DeskPRO.Agent.PageFragment.Page.Content.DeleteControl(this, {
			ajaxSaveUrl: BASE_URL + 'agent/feedback/view/' + self.feedback_id + '/ajax-save'
		});
		this.deleteHelper.undeleteBtn.on('click', function() {
			self.getEl('status').find('option').first().prop('selected', true).trigger('change');
			self.deleteHelper.handleUndelete();
		});
		this.ownObject(this.deleteHelper);
	},

	destroyPage: function() {
		// Workaround for tinymce bug to do with remove()
		// We'll manually remove the node ourselves
		var el = $('.feedback-editor-wrap', this.getEl('content_ed'));
		if (el && el.parentNode) {
			el.parentNode.removeChild(el);
		}
	},

	handleUnloadRevisions: function(revision_id) {
		if (!revision_id) {
			return;
		}

		if ($('.rev-' + revision_id, this.getEl('revs')).length) {
			return;
		}

		this.getEl('revs').empty().removeClass('loaded');
		DeskPRO_Window.util.modCountEl(this.getEl('count_revs'), '+');
	},

	//#################################################################
	//# Basic
	//#################################################################

	_initBasic: function() {
		var self = this;

		// Tabs
		this.bodyTabs = new DeskPRO.UI.SimpleTabs({
			triggerElements: $('li.tab-trigger', this.getEl('bodytabs')),
			onTabSwitch: (function(info) {
				if ($(info.tabContent).is('.content-tab')) {
					self.getEl('content_edit_btns').show();
				} else {
					self.getEl('content_edit_btns').hide();
				}

				if ($(info.tabContent).is('.related-content-tab')) {
					$('body').addClass('related-controls-on');
				} else {
					if ($(info.tabContent).is('.search-tab')) {
						self._initSearchTab();
					}
					$('body').removeClass('related-controls-on');
				}
				if ($(info.tabContent).is('.feedback-revs') && !$(info.tabContent).is('.loaded')) {
					$.ajax({
						url: BASE_URL + 'agent/feedback/view/' + this.feedback_id + '/view-revisions',
						type: 'GET',
						dataType: 'html',
						context: self,
						success: function(html) {
							this.getEl('revs').html(html);
							this.miscContent._initCompareRevs();
							$(info.tabContent).addClass('loaded');
						}
					});
				}
			}).bind(this)
		});
		this.ownObject(this.bodyTabs);
	},

	toggleMyVote: function() {
		var action;
		if (this.getEl('my_vote').is('.radio-on')) {
			action = 'clear-vote';
			DeskPRO_Window.util.modCountEl(this.getEl('num_votes'), '-');
			this.getEl('my_vote').removeClass('radio-on');
		} else {
			action = 'vote';
			DeskPRO_Window.util.modCountEl(this.getEl('num_votes'), '+');
			this.getEl('my_vote').addClass('radio-on');
		}

		$.ajax({
			url: BASE_URL + 'agent/feedback/view/' + this.feedback_id + '/ajax-save',
			type: 'POST',
			data: {action: action},
			context: this,
			dataType: 'json'
		});
	},


	//#################################################################
	//# Menus
	//#################################################################

	_initMenus: function() {

		var self = this;

		if (this.meta.canEdit) {
			// Change category menu
			var catOb = new DeskPRO.UI.OptionBoxRevertable({
				trigger: this.getEl('cat_trigger'),
				element: this.getEl('cat_ob'),
				onSave: function(ob) {
					var catEl = ob.getSelectedElements('category');
					var catId = catEl.data('item-id');
					var title = catEl.data('full-title');

					self.getEl('cat_label').text(title);

					$.ajax({
						url: BASE_URL + 'agent/feedback/view/' + self.feedback_id + '/ajax-save',
						type: 'POST',
						data: { action: 'category', category_id: catId },
						dataType: 'json'
					});
				}
			});
		}
	},

	_initActions: function() {
		var self = this;
		var actions = this.getEl('action_buttons');

		$('.permalink', actions).on('click', function() {
			var html = [];
			html.push('<div>');
			html.push('The permalink to this feedback on the website is:<br />');
			html.push('<input type="text" style="width:95%;" />');
			html.push('</div>');

			var msg = $(html.join(''));
			$('input', msg).val(self.meta.permalink);

			DeskPRO_Window.showAlert(msg);
		});

		$('.view-user-interface', actions).on('click', function() {
			window.open(self.meta.permalink);
		});

		this.merge = new DeskPRO.Agent.Widget.Merge({
			tabType: 'feedback',
			metaId: self.meta.feedback_id,
			metaIdName: 'feedback_id',
			menu: this.getEl('merge_menu'),
			trigger: $('.merge', this.getEl('action_buttons')),
			overlayUrl: BASE_URL + 'agent/feedback/merge-overlay/{id}/{other}',
			mergeUrl: BASE_URL + 'agent/feedback/merge/{id}/{other}',
			loadRoute: 'feedback:' + BASE_URL + 'agent/feedback/view/{id}'
		});
		this.ownObject(this.merge);
	},


	//#################################################################
	//# Labels
	//#################################################################

	_initLabels: function() {
		this.labelsInput = new DeskPRO.UI.LabelsInput({
			type: 'feedback',
			input: this.getEl('labels_input'),
			onChange: this.saveLabels.bind(this)
		});
		this.ownObject(this.labelsInput);
	},

	saveLabels: function() {
		if (this._saveLabelsTimeout) {
			window.clearTimeout(this._saveLabelsTimeout);
		}

		this._labelsData = this.labelsInput.getFormData();
		this._saveLabelsTimeout = this._doSaveLabels.delay(2000, this);
	},

	_doSaveLabels: function() {
		var data = this._labelsData;

		$.ajax({
			url: this.getMetaData('labelsSaveUrl'),
			type: 'POST',
			context: this,
			data: data,
			dataType: 'json',
			success: function(data) {
				this._handleSaveLabelsSuccess(data);
			}
		});
	},

	_handleSaveLabelsSuccess: function(data) {

	},

	_initSearchTab: function() {
		if (this.hasInitSearchTab) return;
		this.hasInitSearchTab = true;

		this.stickyWords = new DeskPRO.Agent.PageFragment.Page.Content.StickyWords(this, {
			contentType: 'feedback',
			contentId: this.meta.feedback_id,
			element: this.getEl('stickysearch_input')
		});
		this.ownObject(this.stickyWords);
	},

	//#################################################################
	//# Comments
	//#################################################################

	_initCommentForm: function() {
		this.commentsController = new DeskPRO.Agent.PageHelper.Comments(this, {
			commentsWrapper: this.getEl('comments_wrap')
		});
		this.ownObject(this.commentsController);

		this.newCommentWrapper = $('.new-note:first', this.wrapper);
		$('button', this.newCommentWrapper).on('click', this.saveNewComment.bind(this));
	},

	saveNewComment: function() {

		var loadingOn = $('.loading-on', this.newCommentWrapper).show();
		var loadingOff = $('.loading-off', this.newCommentWrapper).hide();

		var data = [];
		data.push({
			name: 'content',
			value: $('textarea', this.newCommentWrapper).val()
		});

		if (this.getEl('agent_comment_ck').is(':checked')) {
			data.push({
				name: 'agent_only',
				value: 1
			});
		}

		$.ajax({
			url: BASE_URL + 'agent/feedback/view/' + this.getMetaData('feedback_id') + '/ajax-save-comment',
			type: 'POST',
			context: this,
			data: data,
			dataType: 'html',
			success: function(html) {
				loadingOn.hide();
				loadingOff.show();

				$('textarea', this.newCommentWrapper).val('');
				var el = $(html);
				this.newCommentWrapper.before(el);

				// Inc note count
				DeskPRO_Window.util.modCountEl(this.getEl('count_comments'), '+');
			}
		});
	},

	//#################################################################
	//# Editor
	//#################################################################

	_initPostArea: function() {
		this._hasInitEd = false;
		this.getEl('cancel_btn').off('click').on('click', (function() {
			this.hideEditor();

			// Cancel the edit field too, set it back to what it was
			if (!this.wrapper.find('.revert-default')[0]) {
				var def = this.wrapper.find('textarea.edit-content-field-default').val();
				this.wrapper.find('textarea.edit-content-field').val(def);
				if (this.rte) {
					this.rte.val(def);
				}
			}
		}).bind(this));

		var wrap = this.wrapper;

		if (this.editStateSaver) {
			this.editStateSaver.destroy();
		}

		this.editStateSaver = new DeskPRO.Agent.PageHelper.StateSaver({
			stateId: 'editfeedback',
			listenOn: $('.feedback-editor-wrap:first', wrap)
		});
		this.ownObject(this.editStateSaver);

		this.getEl('save_btn').off('click').on('click', (function(ev) {
			ev.preventDefault();

			var data = {
				action: 'content',
				content: $('.feedback-editor-wrap textarea:first', wrap).val(),
				attach: $('.feedback-editor-wrap .edit-content-attach:first', wrap).val()
			};

			$.ajax({
				url: BASE_URL + 'agent/feedback/view/' + this.feedback_id + '/ajax-save',
				type: 'POST',
				context: this,
				data: data,
				dataType: 'json',
				success: function(data) {
					this.getEl('content_ed').html(data.content_html);
					this.handleUnloadRevisions(data.revision_id);
					this._initPostArea();
				}
			});

		}).bind(this));

		this.hideEditor();
	},

	showEditor: function() {

		var self = this;

		var edWrap = $('.feedback-editor-wrap', this.getEl('content_ed')).show();
		$('.revert-default', edWrap).on('click', function() {
			var def = $('textarea.edit-content-field-default').val();
			$('textarea.edit-content-field').val(def);

			$('.revert-message-notice', edWrap).remove();
		});

		$('.feedback-content-wrap', this.getEl('content_ed')).hide();
		$('.feedback-editor-wrap', this.getEl('content_ed')).show();

		if (!this._hasInitEd) {
			this._hasInitEd = true;

			var txt = $('.edit-content-field', this.getEl('content_ed'));
			var w = $(txt.closest('.content-tab-item')).width() - 30;

			// Means the whole thign is visible at once, lets try and max out the viewport
			if (this.wrapper.find('> .layout-content > .scrollbar.disabled')) {
				var h = $(window).height() - 90 - txt.offset().top;
			} else {
				h = 425;
			}

			txt.css({ width: w, height: h });

			DP.rteTextarea($('.edit-content-field', this.getEl('content_ed')), {
				setup: function(ed) {
					ed.onKeyPress.add(function() {
						self.editStateSaver.triggerChange();
					});
				}
			});

			// Attachments
			var list = $('.file-list', this.wrapper);

			DeskPRO_Window.util.fileupload(this.wrapper, {
				url: BASE_URL + 'agent/misc/accept-upload?attach_to_object=feedback&object_id=' + this.meta.feedback_id,
				page: this
			});

			list.on('click', '.delete', function(ev) {
				ev.preventDefault();
				ev.stopImmediatePropagation();

				var blob_id = $(this).data('blob-id');
				$.ajax({
					url: BASE_URL + 'agent/feedback/view/' + self.meta.feedback_id + '/ajax-save',
					type: 'POST',
					data: {action: 'remove-blob', blob_id: blob_id},
					context: self,
					dataType: 'json'
				});

				$(this).parent().fadeOut();
			});
		}

		this.getEl('edit_btn').hide();
		this.getEl('save_btn').show();
		this.getEl('cancel_btn').show();
		this.updateUi();
	},

	hideEditor: function() {
		this.getEl('edit_btn').show();
		this.getEl('save_btn').hide();
		this.getEl('cancel_btn').hide();
		$('.feedback-editor-wrap', this.getEl('content_ed')).hide();
		$('.feedback-content-wrap', this.getEl('content_ed')).show();
		this.updateUi();
	},

	_initMediaBrowser: function() {
		if (this.mediabrowser_has_init) return;
		this.mediabrowser_has_init = true;

		this.mediaBrowserEl = $('.media-browser', this.wrapper);
		this.mediaBrowserOverlay = new DeskPRO.UI.Overlay({
			contentElement: this.mediaBrowserEl
		});

		this.mediaBrowser = new DeskPRO.Agent.MediaBrowser({
			wrapper: this.mediaBrowserEl,
			additionalDropZone: $('.kb-editor > textarea', this.wrapper)
		});
	},

	showMediaBrowser: function() {
		this._initMediaBrowser();
		this.mediaBrowserOverlay.openOverlay();
	},

	//#################################################################
	//# Compare revisions
	//#################################################################

	_initCompareRevs: function() {
		$('.compare-trigger', this.wrapper).on('click', this.showCompareRev.bind(this));
	},

	showCompareRev: function() {
		var old_id = $('.feedback-revs input.old:checked', this.wrapper).val();
		var new_id = $('.feedback-revs input.new:checked', this.wrapper).val();

		if (!old_id || !new_id) {
			return;
		}

		var overlay = new DeskPRO.UI.Overlay({
			triggerElement: $('button.compare-trigger', this.wrapper),
			contentMethod: 'ajax',
			contentAjax: {
				url: BASE_URL + 'agent/feedback/compare-revs/' + old_id + '/' + new_id
			},
			destroyOnClose: true
		});

		overlay.openOverlay();
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');
DeskPRO.Agent.PageFragment.Page.NewsView = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'news';
	},

	initMetaData: function() {
		DeskPRO_Window.recentTabs.add(
			'news',
			this.meta.news_id,
			this.meta.title,
			BASE_URL + 'agent/news/post/' + this.meta.news_id
		);
	},

	initPage: function(el) {

		var self = this;
		this.wrapper = el;

		this.news_id = this.getMetaData('news_id');

		this._initBasic();

		if (this.meta.canEdit) {
			this._initMenus();
			this._initPostArea();
		}
		this._initActions();
		this._initLabels();

		this._initCommentForm();

		if (this.meta.canEdit) {
			if (this.meta.isValidating) {
				this.validatingEdit = new DeskPRO.Agent.PageHelper.ValidatingEdit(this, {
					typename: 'news',
					contentId: this.meta.news_id
				});
				this.ownObject(this.validatingEdit);
			}

			this.getEl('edit_btn').on('click', this.showEditor.bind(this));
		}

		this.relatedContent = new DeskPRO.Agent.PageHelper.RelatedContent(this, {
			typename: 'news',
			content_id: this.meta.news_id,
			listEl: $('section.linked-content:first', this.wrapper),
			disabled: !this.meta.canEdit,
			onContentLinked: function(typename, content_id) {
				$.ajax({
					url: BASE_URL + 'agent/news/post/' + self.meta.news_id + '/ajax-save',
					type: 'POST',
					data: { content_type: typename, content_id: content_id, action: 'add-related' },
					context: this,
					dataType: 'json'
				});
			},
			onContentUnlinked: function(typename, content_id) {
				$.ajax({
					url: BASE_URL + 'agent/news/post/' + self.meta.news_id + '/ajax-save',
					type: 'POST',
					data: { content_type: typename, content_id: content_id, action: 'add-related' },
					context: this,
					dataType: 'json'
				});
			}
		});
		this.ownObject(this.relatedContent);

		this.miscContent = new DeskPRO.Agent.PageHelper.MiscContent(this, {
			revisionCompareUrl: BASE_URL + 'agent/news/compare-revs/{OLD}/{NEW}'
		});
		this.ownObject(this.miscContent);

		this.whoVotedOverlay = new DeskPRO.UI.Overlay({
			triggerElement: '.who-voted-trigger',
			contentMethod: 'ajax',
			contentAjax: {
				url: BASE_URL + 'agent/publish/rating-who-voted/news/' + this.meta.news_id
			}
		});
		this.ownObject(this.whoVotedOverlay);
	},

	destroyPage: function() {
		// Workaround for tinymce bug to do with remove()
		// We'll manually remove the node ourselves
		var el = $('.news-editor-wrap', this.getEl('content_ed'));
		if (el[0]) {
			el.get(0).parentNode.removeChild(el.get(0));
		}
	},

	handleUnloadRevisions: function(revision_id) {
		if (!revision_id) {
			return;
		}

		if ($('.rev-' + revision_id, this.getEl('revs')).length) {
			return;
		}

		this.getEl('revs').empty().removeClass('loaded');
		DeskPRO_Window.util.modCountEl(this.getEl('count_revs'), '+');
	},

	//#################################################################
	//# Basic
	//#################################################################

	_initBasic: function() {
		var self = this;

		// Tabs
		this.bodyTabs = new DeskPRO.UI.SimpleTabs({
			triggerElements: $('li', this.getEl('bodytabs')),
			onTabSwitch: (function(info) {
				if ($(info.tabContent).is('.news-content')) {
					self.getEl('content_edit_btns').show();
				} else {
					self.getEl('content_edit_btns').hide();
				}

				if ($(info.tabContent).is('.related-content-tab')) {
					$('body').addClass('related-controls-on');
				} else {
					if ($(info.tabContent).is('.search-tab')) {
						self._initSearchTab();
					}
					$('body').removeClass('related-controls-on');
				}
				if ($(info.tabContent).is('.revisions-tab') && !$(info.tabContent).is('.loaded')) {
					$.ajax({
						url: BASE_URL + 'agent/news/post/' + this.meta.news_id + '/view-revisions',
						type: 'GET',
						dataType: 'html',
						context: self,
						success: function(html) {
							this.getEl('revs').html(html);
							this.miscContent._initCompareRevs();
							$(info.tabContent).addClass('loaded');
						}
					});
				}
			}).bind(this)
		});
		this.ownObject(this.bodyTabs);

		if (this.meta.canEdit) {
			var editTitle = new DeskPRO.Agent.PageFragment.Page.EditTitle(
				this,
				BASE_URL + 'agent/news/post/' + this.meta.news_id + '/ajax-save'
			);
		}
	},


	//#################################################################
	//# Menus
	//#################################################################

	_initMenus: function() {

		var self = this;

		var statusSel = this.getEl('status');
		DP.select(statusSel);

		statusSel.on('change', function() {
			var status = $(this).val();

			self.getEl('auto_unpub').hide();
			self.getEl('auto_pub').hide();

			if (status == 'published') {
				self.getEl('auto_unpub').show();
			} else if (status == 'hidden.unpublished') {
				self.getEl('auto_pub').show();
			}

			$.ajax({
				url: BASE_URL + 'agent/news/post/' + self.meta.news_id + '/ajax-save',
				type: 'POST',
				data: {action: 'status', status: status},
				context: self,
				dataType: 'json',
				success: function() {
					DeskPRO_Window.sections.publish_section.reload();
				}
			});

		});

		this.deleteHelper = new DeskPRO.Agent.PageFragment.Page.Content.DeleteControl(this, {
			ajaxSaveUrl: BASE_URL + 'agent/news/post/' + self.meta.news_id + '/ajax-save',
			statusMenu: this.statusMenu
		});
		this.ownObject(this.deleteHelper);

		var catSel = this.getEl('cat');
		DP.select(catSel);

		catSel.on('change', function() {
			$.ajax({
				url: BASE_URL + 'agent/news/post/' + self.meta.news_id + '/ajax-save',
				type: 'POST',
				data: { action: 'category', category_id: $(this).val() },
				dataType: 'json',
				success: function() {
					DeskPRO_Window.sections.publish_section.reload();
				}
			});
		});
	},

	_initActions: function() {
		var self = this;
		var actions = this.getEl('action_buttons');

		$('.delete', actions).on('click', function() {

		});

		$('.permalink', actions).on('click', function() {
			var html = [];
			html.push('<div>');
			html.push('The permalink to this post on the website is:<br />');
			html.push('<input type="text" style="width:80%;" />');
			html.push('</div>');

			var msg = $(html.join(''));
			$('input', msg).val(self.meta.permalink);

			DeskPRO_Window.showAlert(msg);
		});

		$('.view-user-interface', actions).on('click', function() {
			window.open(self.meta.permalink);
		});
	},


	//#################################################################
	//# Labels
	//#################################################################

	_initLabels: function() {
		this.labelsInput = new DeskPRO.UI.LabelsInput({
			type: 'news',
			input: this.getEl('labels_input'),
			onChange: this.saveLabels.bind(this)
		});
		this.ownObject(this.labelsInput);
	},

	saveLabels: function() {
		if (this._saveLabelsTimeout) {
			window.clearTimeout(this._saveLabelsTimeout);
		}

		this._labelsData = this.labelsInput.getFormData();
		this._saveLabelsTimeout = this._doSaveLabels.delay(2000, this);
	},

	_doSaveLabels: function() {
		var data = this._labelsData;

		$.ajax({
			url: this.getMetaData('labelsSaveUrl'),
			type: 'POST',
			context: this,
			data: data,
			dataType: 'json',
			success: function(data) {
				this._handleSaveLabelsSuccess(data);
			}
		});
	},

	_handleSaveLabelsSuccess: function(data) {

	},

	_initSearchTab: function() {
		if (this.hasInitSearchTab) return;
		this.hasInitSearchTab = true;

		this.stickyWords = new DeskPRO.Agent.PageFragment.Page.Content.StickyWords(this, {
			contentType: 'news',
			contentId: this.meta.news_id,
			element: this.getEl('stickysearch_input')
		});
		this.ownObject(this.stickyWords);
	},

	//#################################################################
	//# Comments
	//#################################################################

	_initCommentForm: function() {
		this.commentsController = new DeskPRO.Agent.PageHelper.Comments(this, {
			commentsWrapper: this.getEl('comments_wrap')
		});
		this.ownObject(this.commentsController);

		this.newCommentWrapper = $('.new-note:first', this.wrapper);
		$('button', this.newCommentWrapper).on('click', this.saveNewComment.bind(this));
	},

	saveNewComment: function() {

		var loadingOn = $('.loading-on', this.newCommentWrapper).show();
		var loadingOff = $('.loading-off', this.newCommentWrapper).hide();

		var data = [];
		data.push({
			name: 'content',
			value: $('textarea', this.newCommentWrapper).val()
		});

		$.ajax({
			url: BASE_URL + 'agent/news/post/' + this.getMetaData('news_id') + '/ajax-save-comment',
			type: 'POST',
			context: this,
			data: data,
			dataType: 'html',
			success: function(html) {
				loadingOn.hide();
				loadingOff.show();

				$('textarea', this.newCommentWrapper).val('');
				var el = $(html);
				this.newCommentWrapper.before(el);

				DeskPRO_Window.util.modCountEl(this.getEl('count_comments'), '+');

				if (DeskPRO_Window.sections.publish_section) {
					DeskPRO_Window.sections.publish_section.modCommentCount('news', '+');
				}
			}
		});
	},

	//#################################################################
	//# Editor
	//#################################################################

	_initPostArea: function() {
		this._hasInitEd = false;
		this.getEl('cancel_btn').off('click').on('click', (function() {
			this.hideEditor();

			// Cancel the edit field too, set it back to what it was
			if (!this.wrapper.find('.revert-default')[0]) {
				var def = this.wrapper.find('textarea.edit-content-field-default').val();
				this.wrapper.find('textarea.edit-content-field').val(def);
				if (this.rte) {
					this.rte.val(def);
				}
			}
		}).bind(this));

		var wrap = this.wrapper;

		if (this.editStateSaver) {
			this.editStateSaver.destroy();
		}

		this.editStateSaver = new DeskPRO.Agent.PageHelper.StateSaver({
			stateId: 'editarticle',
			listenOn: $('.news-editor-wrap:first', wrap)
		});
		this.ownObject(this.editStateSaver);

		this.getEl('save_btn').off('click').on('click', (function(ev) {
			ev.preventDefault();

			var data = {
				action: 'content',
				content: $('.news-editor-wrap textarea:first', wrap).val(),
				attach: $('.news-editor-wrap .edit-content-attach:first', wrap).val()
			};

			$.ajax({
				url: BASE_URL + 'agent/news/post/' + this.meta.news_id + '/ajax-save',
				type: 'POST',
				context: this,
				data: data,
				dataType: 'json',
				success: function(data) {
					this.getEl('content_ed').html(data.content_html);
					this.handleUnloadRevisions(data.revision_id);
					this._initPostArea();
				}
			});

		}).bind(this));

		this.hideEditor();
	},

	showEditor: function() {

		var self = this;

		var edWrap = $('.news-editor-wrap', this.getEl('content_ed')).show();
		$('.revert-default', edWrap).on('click', function() {
			var def = $('textarea.edit-content-field-default').val();
			$('textarea.edit-content-field').val(def);

			$('.revert-message-notice', edWrap).remove();
		});

		$('.news-content-wrap', this.getEl('content_ed')).hide();
		$('.news-editor-wrap', this.getEl('content_ed')).show();

		if (!this._hasInitEd) {
			this._hasInitEd = true;

			var txt = $('.edit-content-field', this.getEl('content_ed'));
			var w = $(txt.closest('.content-tab-item')).width() - 30;

			// Means the whole thign is visible at once, lets try and max out the viewport
			if (this.wrapper.find('> .layout-content > .scrollbar.disabled')) {
				var h = $(window).height() - 90 - txt.offset().top;
			} else {
				h = 425;
			}

			txt.css({ width: w, height: h });

			DP.rteTextarea($('.edit-content-field', this.getEl('content_ed')), {
				setup: function(ed) {
					ed.onKeyPress.add(function() {
						self.editStateSaver.triggerChange();
					});
				}
			});
		}

		this.getEl('edit_btn').hide();
		this.getEl('save_btn').show();
		this.getEl('cancel_btn').show();
		this.updateUi();
	},

	hideEditor: function() {
		this.getEl('edit_btn').show();
		this.getEl('save_btn').hide();
		this.getEl('cancel_btn').hide();
		$('.news-editor-wrap', this.getEl('content_ed')).hide();
		$('.news-content-wrap', this.getEl('content_ed')).show();
		this.updateUi();
	},

	_initMediaBrowser: function() {
		if (this.mediabrowser_has_init) return;
		this.mediabrowser_has_init = true;

		this.mediaBrowserEl = $('.media-browser', this.wrapper);
		this.mediaBrowserOverlay = new DeskPRO.UI.Overlay({
			contentElement: this.mediaBrowserEl
		});

		this.mediaBrowser = new DeskPRO.Agent.MediaBrowser({
			wrapper: this.mediaBrowserEl,
			additionalDropZone: $('.kb-editor > textarea', this.wrapper)
		});
	},

	showMediaBrowser: function() {
		this._initMediaBrowser();
		this.mediaBrowserOverlay.openOverlay();
	},

	//#################################################################
	//# Compare revisions
	//#################################################################

	_initCompareRevs: function() {
		$('.compare-trigger', this.wrapper).on('click', this.showCompareRev.bind(this));
	},

	showCompareRev: function() {
		var old_id = $('.reivisons input.old:checked', this.wrapper).val();
		var new_id = $('.revisions input.new:checked', this.wrapper).val();

		if (!old_id || !new_id) {
			return;
		}

		var overlay = new DeskPRO.UI.Overlay({
			triggerElement: $('button.compare-trigger', this.wrapper),
			contentMethod: 'ajax',
			contentAjax: {
				url: BASE_URL + 'agent/news/compare-revs/' + old_id + '/' + new_id
			},
			destroyOnClose: true
		});

		overlay.openOverlay();
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');
DeskPRO.Agent.PageFragment.Page.DownloadsView = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'download';
	},

	initMetaData: function() {
		DeskPRO_Window.recentTabs.add(
			'download',
			this.meta.download_id,
			this.meta.title,
			BASE_URL + 'agent/downloads/file/' + this.meta.download_id
		);
	},

	initPage: function(el) {

		var self = this;
		this.wrapper = el;

		this.download_id = this.getMetaData('download_id');

		this._initBasic();
		this._initLabels();
		this._initCommentForm();
		this._initActions();

		if (this.meta.canEdit) {
			this._initPostArea();

			if (this.meta.isValidating) {
				this.validatingEdit = new DeskPRO.Agent.PageHelper.ValidatingEdit(this, {
					typename: 'downloads',
					contentId: this.meta.download_id
				});
				this.ownObject(this.validatingEdit);
			}

			this.getEl('edit_btn').on('click', this.showEditor.bind(this));
		}

        $('time.timeago', this.wrapper).timeago();

		this.relatedContent = new DeskPRO.Agent.PageHelper.RelatedContent(this, {
			typename: 'downloads',
			content_id: this.meta.download_id,
			listEl: $('section.linked-content:first', this.wrapper),
			disabled: !this.meta.canEdit,
			onContentLinked: function(typename, content_id) {
				$.ajax({
					url: BASE_URL + 'agent/downloads/file/' + self.meta.download_id + '/ajax-save',
					type: 'POST',
					data: { content_type: typename, content_id: content_id, action: 'add-related' },
					context: this,
					dataType: 'json'
				});
			},
			onContentUnlinked: function(typename, content_id) {
				$.ajax({
					url: BASE_URL + 'agent/downloads/file/' + self.meta.download_id + '/ajax-save',
					type: 'POST',
					data: { content_type: typename, content_id: content_id, action: 'add-related' },
					context: this,
					dataType: 'json'
				});
			}
		});
		this.ownObject(this.relatedContent);

		this.miscContent = new DeskPRO.Agent.PageHelper.MiscContent(this, {
			revisionCompareUrl: BASE_URL + 'agent/downloads/compare-revs/{OLD}/{NEW}'
		});
		this.ownObject(this.miscContent);

		this.whoVotedOverlay = new DeskPRO.UI.Overlay({
			triggerElement: '.who-voted-trigger',
			contentMethod: 'ajax',
			contentAjax: {
				url: BASE_URL + 'agent/publish/rating-who-voted/download/' + this.meta.download_id
			}
		});
		this.ownObject(this.whoVotedOverlay);

		this.whoViewedOverlay = new DeskPRO.UI.Overlay({
			triggerElement: '.open-who-viewed',
			contentMethod: 'ajax',
			contentAjax: {
				url: BASE_URL + 'agent/publish/who-viewed/2/' + this.meta.download_id
			}
		});
		this.ownObject(this.whoViewedOverlay);

		this.whoDownloadedOverlay = new DeskPRO.UI.Overlay({
			triggerElement: '.open-who-downloaded',
			contentMethod: 'ajax',
			contentAjax: {
				url: BASE_URL + 'agent/publish/who-viewed/2/' + this.meta.download_id + '/2'
			}
		});
		this.ownObject(this.whoViewedOverlay);

		var editBtn    = this.getEl('editfile_controls').find('.edit-trigger');
		var cancelBtn  = this.getEl('editfile_controls').find('.cancel-trigger');
		var saveBtn    = this.getEl('editfile_controls').find('.save-trigger');
		var loadingBtn = this.getEl('editfile_controls').find('.is-loading');

		var editArea = this.getEl('editfile');
		var showArea = this.getEl('showfile');

		editBtn.on('click', function(ev) {
			ev.preventDefault();
			editBtn.hide();
			cancelBtn.show();
			saveBtn.show();

			showArea.hide();
			editArea.show();

			editArea.find('.file-list').empty();
		});

		saveBtn.on('click', function(ev) {
			ev.preventDefault();
			editBtn.hide();
			cancelBtn.hide();
			saveBtn.hide();

			var formData = editArea.find('input, select, textarea').serializeArray();
			formData.push({
				name: 'action',
				value: 'file'
			});

			loadingBtn.show();
			$.ajax({
				url: BASE_URL + 'agent/downloads/file/' + self.meta.download_id + '/ajax-save',
				data: formData,
				error: function() {
					loadingBtn.hide();
					cancelBtn.show();
					saveBtn.show();
				},
				success: function(data) {
					loadingBtn.hide();
					cancelBtn.hide();
					saveBtn.hide();
					editBtn.show();

					self.handleUnloadRevisions(data.revision_id);
					editArea.hide();
					showArea.empty().html(data.file_html).show();
				}
			})
		});

		cancelBtn.on('click', function(ev) {
			ev.preventDefault();
			editBtn.show();
			cancelBtn.hide();
			saveBtn.hide();

			editArea.hide();
			showArea.show();
		});

		var list = $('.file-list', editArea);
		if (list[0]) {
			DeskPRO_Window.util.fileupload(editArea, { page: this });
			this.wrapper.bind('fileuploadadd', function() {
				$('ul.file-list', editArea).empty();
			});
		}
	},

	handleUnloadRevisions: function(revision_id) {
		if (!revision_id) {
			return;
		}

		if ($('.rev-' + revision_id, this.getEl('revs')).length) {
			return;
		}

		this.getEl('revs').empty().removeClass('loaded');
		DeskPRO_Window.util.modCountEl(this.getEl('count_revs'), '+');
	},

	destroyPage: function() {
		// Workaround for tinymce bug to do with remove()
		// We'll manually remove the node ourselves
		var el = $('.download-editor-wrap', this.getEl('content_ed'));
		if (el[0]) {
			el.get(0).parentNode.removeChild(el.get(0));
		}
	},

	//#################################################################
	//# Basic
	//#################################################################

	_initBasic: function() {
		var self = this;

		// Tabs
		this.bodyTabs = new DeskPRO.UI.SimpleTabs({
			triggerElements: $('li', this.getEl('bodytabs')),
			onTabSwitch: (function(info) {
				if ($(info.tabContent).is('.dl-content')) {
					self.getEl('content_edit_btns').show();
				} else {
					self.getEl('content_edit_btns').hide();
				}

				if ($(info.tabContent).is('.related-content-tab')) {
					$('body').addClass('related-controls-on');
				} else {
					if ($(info.tabContent).is('.search-tab')) {
						self._initSearchTab();
					}
					$('body').removeClass('related-controls-on');$('body').addClass('related-controls-off');
				}
				if ($(info.tabContent).is('.revisions-tab') && !$(info.tabContent).is('.loaded')) {
					$.ajax({
						url: BASE_URL + 'agent/downloads/file/' + this.meta.download_id + '/view-revisions',
						type: 'GET',
						dataType: 'html',
						context: self,
						success: function(html) {
							this.getEl('revs').html(html);
							this.miscContent._initCompareRevs();
							$(info.tabContent).addClass('loaded');
						}
					});
				}
			}).bind(this)
		});
		this.ownObject(this.bodyTabs);

		if (this.meta.canEdit) {
			// Name is editable
			var name = $('h3.title.editable:first', this.wrapper);
			if (!name.attr('id')) {
				name.attr('id', Orb.getUniqueId());
			}

			var editable = new DeskPRO.Form.InlineEdit({
				baseElement: this.wrapper,
				ajax: {
					url: BASE_URL + 'agent/downloads/file/' + this.meta.download_id + '/ajax-save',
					success: function(data) {
						self.handleUnloadRevisions(data.revision_id);
					}
				}
			});

			// Change category menu
			var catOb = new DeskPRO.UI.OptionBoxRevertable({
				trigger: this.getEl('cat_trigger'),
				element: this.getEl('cat_ob'),
				onSave: function(ob) {
					var catEl = ob.getSelectedElements('category');
					var catId = catEl.data('item-id');
					var title = catEl.data('full-title');

					self.getEl('cat_label').text(title);

					$.ajax({
						url: BASE_URL + 'agent/downloads/file/' + self.meta.download_id + '/ajax-save',
						type: 'POST',
						data: { action: 'category', category_id: catId },
						dataType: 'json',
						success: function() {
							DeskPRO_Window.sections.publish_section.reload();
						}
					});
				}
			});

			// Status
			var trigger = $('.the-status:first', this.wrapper);
			this.statusMenu = new DeskPRO.UI.Menu({
				triggerElement: trigger,
				menuElement: $('.status-menu:first', this.wrapper),
				onItemClicked: function(info) {
					var status = $(info.itemEl).data('option-value');

					$('.download-status', trigger).attr('title', status);
					$('.download-status span', trigger).attr('class', '').addClass('ticket-' + status.replace(/\./, '_'));

					$.ajax({
						url: BASE_URL + 'agent/downloads/file/' + self.meta.download_id + '/ajax-save',
						type: 'POST',
						data: {action: 'status', status: status},
						context: self,
						dataType: 'json',
						success: function() {
							DeskPRO_Window.sections.publish_section.reload();
						}
					});
				}
			});
			this.ownObject(this.statusMenu);

			this.deleteHelper = new DeskPRO.Agent.PageFragment.Page.Content.DeleteControl(this, {
				ajaxSaveUrl: BASE_URL + 'agent/downloads/file/' + self.meta.download_id + '/ajax-save',
				statusMenu: this.statusMenu
			});
			this.ownObject(this.deleteHelper);
		}
	},

	//#################################################################
	//# Actions menus
	//#################################################################

	_initActions: function() {
		var self = this;
		var actions = this.getEl('action_buttons');

		$('.delete', actions).on('click', function() {

		});

		$('.permalink', actions).on('click', function() {
			var html = [];
			html.push('<div>');
			html.push('The permalink to this download on the website is:<br />');
			html.push('<input type="text" style="width:95%" />');
			html.push('</div>');

			var msg = $(html.join(''));
			$('input', msg).val(self.meta.permalink);

			DeskPRO_Window.showAlert(msg);
		});

		$('.view-user-interface', actions).on('click', function() {
			window.open(self.meta.permalink);
		});
	},

	//#################################################################
	//# Labels
	//#################################################################

	_initLabels: function() {

		this.labelsInput = new DeskPRO.UI.LabelsInput({
			type: 'downloads',
			input: this.getEl('labels_input'),
			onChange: this.saveLabels.bind(this)
		});
		this.ownObject(this.labelsInput);
	},

	saveLabels: function() {
		if (this._saveLabelsTimeout) {
			window.clearTimeout(this._saveLabelsTimeout);
		}

		this._labelsData = this.labelsInput.getFormData();
		this._saveLabelsTimeout = this._doSaveLabels.delay(2000, this);
	},

	_doSaveLabels: function() {
		var data = this._labelsData;

		$.ajax({
			url: this.getMetaData('labelsSaveUrl'),
			type: 'POST',
			context: this,
			data: data,
			dataType: 'json',
			success: function(data) {
				this._handleSaveLabelsSuccess(data);
			}
		});
	},

	_handleSaveLabelsSuccess: function(data) {

	},

	_initSearchTab: function() {
		if (this.hasInitSearchTab) return;
		this.hasInitSearchTab = true;

		this.stickyWords = new DeskPRO.Agent.PageFragment.Page.Content.StickyWords(this, {
			contentType: 'download',
			contentId: this.meta.download_id,
			element: this.getEl('stickysearch_input')
		});
		this.ownObject(this.stickyWords);
	},

	//#################################################################
	//# Comments
	//#################################################################

	_initCommentForm: function() {
		this.commentsController = new DeskPRO.Agent.PageHelper.Comments(this, {
			commentsWrapper: this.getEl('comments_wrap')
		});
		this.ownObject(this.commentsController);

		this.newCommentWrapper = $('.new-note:first', this.wrapper);
		$('button', this.newCommentWrapper).on('click', this.saveNewComment.bind(this));
	},

	saveNewComment: function() {

		var loadingOn = $('.loading-on', this.newCommentWrapper).show();
		var loadingOff = $('.loading-off', this.newCommentWrapper).hide();

		var data = [];
		data.push({
			name: 'content',
			value: $('textarea', this.newCommentWrapper).val()
		});

		$.ajax({
			url: BASE_URL + 'agent/downloads/file/' + this.getMetaData('download_id') + '/ajax-save-comment',
			type: 'POST',
			context: this,
			data: data,
			dataType: 'html',
			success: function(html) {
				loadingOn.hide();
				loadingOff.show();

				$('textarea', this.newCommentWrapper).val('');
				var el = $(html);
				this.newCommentWrapper.before(el);

				DeskPRO_Window.util.modCountEl(this.getEl('count_comments'), '+');

				if (DeskPRO_Window.sections.publish_section) {
					DeskPRO_Window.sections.publish_section.modCommentCount('downloads', '+');
				}
			}
		});
	},

	//#################################################################
	//# Editor
	//#################################################################

	_initPostArea: function() {
		this._hasInitEd = false;
		this.getEl('cancel_btn').off('click').on('click', (function() {
			this.hideEditor();

			// Cancel the edit field too, set it back to what it was
			if (!this.wrapper.find('.revert-default')[0]) {
				var def = this.wrapper.find('textarea.edit-content-field-default').val();
				this.wrapper.find('textarea.edit-content-field').val(def);
				if (this.rte) {
					this.rte.val(def);
				}
			}
		}).bind(this));

		var wrap = this.wrapper;

		if (this.editStateSaver) {
			this.editStateSaver.destroy();
		}

		this.editStateSaver = new DeskPRO.Agent.PageHelper.StateSaver({
			stateId: 'editdownload',
			listenOn: $('.download-editor-wrap:first', wrap)
		});
		this.ownObject(this.editStateSaver);

		this.getEl('save_btn').off('click').on('click', (function(ev) {
			ev.preventDefault();

			var data = {
				action: 'content',
				content: $('.download-editor-wrap textarea:first', wrap).val(),
				attach: $('.download-editor-wrap .edit-content-attach:first', wrap).val()
			};

			$.ajax({
				url: BASE_URL + 'agent/downloads/file/' + this.meta.download_id + '/ajax-save',
				type: 'POST',
				context: this,
				data: data,
				dataType: 'json',
				success: function(data) {
					this.getEl('content_ed').html(data.content_html);
					this.handleUnloadRevisions(data.revision_id);
					this._initPostArea();
				}
			});

		}).bind(this));

		this.hideEditor();
	},

	showEditor: function() {

		var self = this;

		var edWrap = $('.download-editor-wrap', this.getEl('content_ed')).show();
		$('.revert-default', edWrap).on('click', function() {
			var def = $('textarea.edit-content-field-default').val();
			$('textarea.edit-content-field').val(def);

			$('.revert-message-notice', edWrap).remove();
		});

		$('.download-content-wrap', this.getEl('content_ed')).hide();
		$('.download-editor-wrap', this.getEl('content_ed')).show();

		if (!this._hasInitEd) {
			this._hasInitEd = true;

			var txt = $('.edit-content-field', this.getEl('content_ed'));
			var w = $(txt.closest('.content-tab-item')).width() - 30;

			// Means the whole thign is visible at once, lets try and max out the viewport
			if (this.wrapper.find('> .layout-content > .scrollbar.disabled')) {
				var h = $(window).height() - 90 - txt.offset().top;
			} else {
				h = 425;
			}

			txt.css({ width: w, height: h });

			DP.rteTextarea(txt, {
				setup: function(ed) {
					ed.onKeyPress.add(function() {
						self.editStateSaver.triggerChange();
					});
				}
			});

			this._hasInitEdBefore = true;
		}

		this.getEl('edit_btn').hide();
		this.getEl('save_btn').show();
		this.getEl('cancel_btn').show();
		this.updateUi();
	},

	hideEditor: function() {
		$('.download-editor-wrap', this.getEl('content_ed')).hide();
		$('.download-content-wrap', this.getEl('content_ed')).show();
		this.getEl('edit_btn').show();
		this.getEl('save_btn').hide();
		this.getEl('cancel_btn').hide();
		this.updateUi();
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');
DeskPRO.Agent.PageFragment.Page.NewTask = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'newtask';
	},

	initPage: function(el) {

		this.noIgnoreForm = true;
		var self = this;
		this.wrapper = el;

		var nolink = false;
		this.addEvent('popover-open', function() {
			nolink = false;
			rowContainer.empty();
			addTaskRow();
		});

		var statusMenu = new DeskPRO.UI.Menu({
			menuElement: this.getEl('menu_vis'),
			onItemClicked: function(info) {
				$('input.input-vis', openForEl).val($(info.itemEl).data('vis'));
				$('.opt-trigger.visibility label', openForEl).text($(info.itemEl).text());
			}
		});


		var form = this.getEl('form');
		form.on('submit', Orb.cancelEvent);

		var rowContainer = this.getEl('tasks');

		var openForEl = null;
		rowContainer.on('click', '.remove-row-trigger', function(ev) {
			var row = $(this).closest('.task-row');
			row.slideUp('fast', function() {
				row.remove();
				self.updateUi();
			});
		});

		rowContainer.on('click', '.opt-trigger.visibility', function(ev) {
			openForEl = $(this).closest('.task-row');
			statusMenu.open(ev);
		});
		rowContainer.on('click', '.opt-trigger.date_due', function(ev) {
			var label = $('label', this);
			var row = $(this).closest('.task-row');
			var field = $('input.input-date-due', row);
			var date = $('input.input-date-due', row).val();
			if (!date) {
				date = new Date();
			}

			field.datepicker('dialog', date, function(date, inst) {
				$('input.input-date-due', row).val(date);
				label.text(date);
			}, {
				dateFormat: 'yy-mm-dd',
				showButtonPanel: true,
				beforeShow: function(input) {
					setTimeout(function() {
						var buttonPane = $(input).datepicker("widget").find(".ui-datepicker-buttonpane");

						$('button', buttonPane).remove();

						var btn = $('<button class="ui-datepicker-current ui-state-default ui-priority-secondary ui-corner-all" type="button">Clear</button>');
						btn.unbind("click").bind("click", function () { $.datepicker._clearDate( input ); label.text('No due date'); });
						btn.appendTo( buttonPane );

						$(input).datepicker("widget").css('z-index', 30101);
					},1);
				}
			}, ev);
		});

		var addTaskRow = function() {
			var tpl = DeskPRO_Window.util.getPlainTpl(self.getEl('task_row_tpl'));
			var row = $(tpl);

			if (!nolink) {
				var activeTab = DeskPRO_Window.getTabWatcher().getActiveTabIfType('ticket');
				if (activeTab) {
					var linkEl = $('.linked-ticket', row);
					$('label', linkEl).text(activeTab.page.meta.title);
					$('input.input-ticket-id', row).val(activeTab.page.meta.ticket_id);
					linkEl.show();
					$('.remove-link-trigger', row).on('click', function() {
						nolink = true;
						linkEl.hide();
						$('input.input-ticket-id', row).val(0);
						$('.linked-container', row).hide();
					});
				}

				activeTab = DeskPRO_Window.getTabWatcher().getActiveTabIfType('deal');
				if (activeTab) {
					linkEl = $('.linked-deal', row);
					$('label', linkEl).text(activeTab.page.meta.title);
					$('input.input-deal-id', row).val(activeTab.page.meta.deal_id);
					linkEl.show();
					$('.remove-link-trigger', row).on('click', function() {
						nolink = true;
						linkEl.hide();
						$('input.input-ticket-id', row).val(0);
						$('.linked-container', row).hide();
					});
				}

				if (linkEl) {
					$('.linked-container', row).show();
				}
			}

			rowContainer.append(row);

			var agent_sel = row.find('.agents_sel');
			DP.select(agent_sel);

			agent_sel.on('change', function() {
				var val = $(this).val();
				var label = $(this).find(':selected').text().trim();

				if (!val) {
					val = '';
					label = 'Me';
				}

				row.find('.assigned_agent').find('label').text(label);
				$('input.input-agent', row).val(val);
			});

			self.updateUi();
		};

		this.getEl('add_btn').on('click', addTaskRow);

		addTaskRow();

		var footer = $('footer.pop-footer', el);
		$('.submit-trigger', el).on('click', function() {
			var postData = form.serializeArray();

			footer.addClass('loading');

			$.ajax({
				url: form.attr('action'),
				type: 'POST',
				dataType: 'json',
				data: postData,
				complete: function() {
					footer.removeClass('loading');
				},
				success: function(data) {
					self.meta.popover.close();
					if (DeskPRO_Window.sections.tasks_section) {
						DeskPRO_Window.sections.tasks_section.refresh();
					}
				}
			});
		});

		this.addEvent('popover-closed', function() {
			rowContainer.empty();
			addTaskRow();
		});
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');
DeskPRO.Agent.PageFragment.Page.NewTweet = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'newtweet';
		this.allowDupe = true;
	},

	initPage: function(el) {

		this.noIgnoreForm = true;
		var self = this;
		this.wrapper = el;

		var helper = new DeskPRO.Agent.PageHelper.Twitter(this.wrapper, this);

		var textarea = this.wrapper.find('textarea');
		textarea.TextAreaExpander();

		if (this.getMetaData('tweetSignature')) {
			textarea.val(' ' + this.getMetaData('tweetSignature'));
			helper.updateTweetLength(textarea);
		}
		textarea.focus();

		DP.select(this.getEl('from_account'));

		var form = this.getEl('form');
		form.on('submit', Orb.cancelEvent);

		var footer = $('footer.pop-footer', el);
		$('.submit-trigger', el).on('click', function() {
			var postData = form.serializeArray();

			footer.addClass('loading');

			$.ajax({
				url: form.attr('action'),
				type: 'POST',
				dataType: 'json',
				data: postData,
				complete: function() {
					footer.removeClass('loading');
				},
				success: function(data) {
					self.meta.popover.close();
					if (DeskPRO_Window.sections.twitter_section) {
						DeskPRO_Window.sections.twitter_section.refresh();
					}
				}
			});
		});
	},

	markForReload: function() {
		if (!this.markedForReload) {
			this.markedForReload = true;
			this.addEvent('deactivate', this.closeSelf.bind(this));
		}
	},

	closeSelf: function() {
		var ev = {cancel: false};
		this.fireEvent('closeSelf', ev);

		if (!ev.cancel) {
			this.parent();
		}
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');
DeskPRO.Agent.PageFragment.Page.Test = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.allowDupe = true;
		this.TYPENAME = 'test';
	},

	initPage: function(el) {
		var self = this;
		$(el).on('click', function() {
			window.setTimeout(function() { self.alertTab(); }, 3000);
		});
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');
DeskPRO.Agent.PageFragment.Page.Deal = new Orb.Class({

    Extends: DeskPRO.Agent.PageFragment.Basic,

    initializeProperties: function() {
        this.parent();
        this.allowDupe = true;
        this.TYPENAME = 'deal';
    },

    initPage: function(el) {
        var self = this;
        this.wrapper = el;
        this._initLabels();
        this._initDisplayOptions();
        this._initAgentSection();
        this._initAssignPersonSection();
        this._initAssignOrganizationSection();
        this._initUserSection();
        this._initOrgEdit();
        this._initAssignAgentSection();
        this._removePersonAndOrg();
        this._initStatusMenus();
        this._initCustomField();



        this.relatedContent = new DeskPRO.Agent.PageHelper.RelatedContent(this, {
            typename: 'deals',
            content_id: this.meta.deal_id,
            listEl: $('section.linked-content:first', this.wrapper),
            onContentLinked: function(typename, content_id) {
                $.ajax({
                    url: BASE_URL + 'agent/deals/' + self.meta.deal_id + '/ajax-save',
                    type: 'POST',
                    data: {
                        content_type: typename,
                        content_id: content_id,
                        action: 'add-related'
                    },
                    context: this,
                    dataType: 'json'
                });
            },
            onContentUnlinked: function(typename, content_id) {
                $.ajax({
                    url: BASE_URL + 'agent/deals/' + self.meta.deal_id + '/ajax-save',
                    type: 'POST',
                    data: {
                        content_type: typename,
                        content_id: content_id,
                        action: 'remove-related'
                    },
                    context: this,
                    dataType: 'json'
                });
            }
        });
        this.ownObject(this.relatedContent);



        // Add new associated task for deal
        $('.create_deal_task_btn').on('click', function() {
            $('form#newTaskForm input, form#newTaskForm select').val('');
            DeskPRO_Window.newTaskLoader.toggle();
        });

        // Name is editable
        var name = $('h3.name.editable:first', el);
        if (!name.attr('id')) {
            name.attr('id', Orb.getUniqueId());
        }

        var editable = new DeskPRO.Form.InlineEdit({
            baseElement: this.wrapper,
            ajax: {
                url: BASE_URL + 'agent/deals/' + this.meta.deal_id + '/ajax-save'
            },
            triggers: '.edit-name-gear'
        });

        // Attach click to wrapper because
        // this same code is used on popout on ticket,
        // and clicks dont bubble to document click
        $(this.wrapper).on('click', function (ev) {
            editable.handleDocumentClick(ev);
        });

        $('.profile-box-container.tabbed', this.wrapper).each(function() {
            var simpleTabs = new DeskPRO.UI.SimpleTabs({
                triggerElements: '> header li',
                context: this
            });

            self.ownObject(simpleTabs);
        });

        var curncyMenu = new DeskPRO.UI.Menu({
			menuElement: this.getEl('currency')
		});
        this.ownObject(curncyMenu);

        var dltypeMenu = new DeskPRO.UI.Menu({
                menuElement: this.getEl('select_deal_type')
        });
        this.ownObject(dltypeMenu);

        var dlstgMenu = new DeskPRO.UI.Menu({
                menuElement: this.getEl('select_deal_stage')
        });
        this.ownObject(dlstgMenu);



                this.getEl('currency').on('change', function(){
                var val = $(this).val();
                //$('.timezone-info', this.wrapper).empty();
                $.ajax({
                        url: BASE_URL + 'agent/deals/' + dealId + '/ajax-save',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                                action: 'change_deal_currency',
                                deal_currency: val
                        },
                        context: this,
                        success: function(data) {
                                //$('.timezone-info', this.wrapper).empty().html(data.bit_html);
                        }
                });
        });


        this.getEl('select_deal_type').on('change', function(){

            var dealId = self.meta.deal_id;
            if (!dealId) {
                return;
            }

            $.ajax({
                url: BASE_URL + 'agent/deals/' + dealId + '/ajax-save',
                data: {
                    action: 'change-dealtype',
                    deal_type_id: $(this).val()
                },
                type: 'POST',
                context: this,
                error: function() {
                //row.show();
                },
                success: function(data) {
                    $('.set-deal-stage').html(data.deal_stage);

                     var dlstgDyMenu = new DeskPRO.UI.Menu({
                        menuElement: this.getEl('select_deal_stage')
                        });
                     this.ownObject(dlstgDyMenu);

                    DeskPRO_Window.sections.deals_section.refresh();
                }
            });
        }) ;


        $('.select-deal-stage').live('change', function(){

            var dealId = self.meta.deal_id;
            if (!dealId) {
                return;
            }

            $.ajax({
                url: BASE_URL + 'agent/deals/' + dealId + '/ajax-save',
                data: {
                    action: 'change-dealstage',
                    deal_stage_id: $(this).val()
                },
                type: 'POST',
                context: this,
                error: function() {
                //row.show();
                },
                success: function(data) {
                //$('.set-deal-stage').html(data.deal_stage);
                }
            });
        }) ;

    },

    _removePersonAndOrg: function(){
        //var self = this;
        this.getEl('members_list').on('click', '.remove', function() {
            var row = $(this).closest('.member-row');
            var personId = row.data('person-id');
            if (!personId) {
                return;
            }

            row.fadeOut('fast');

            $.ajax({
                url: BASE_URL + 'agent/deals/' + self.meta.deal_id + '/ajax-save',
                data: {
                    action: 'remove-person',
                    person_id: personId
                },
                type: 'POST',
                context: this,
                error: function() {
                    row.show();
                },
                success: function() {
                    row.remove();
                    DeskPRO_Window.util.modCountEl(self.getEl('members_count'), '-');
                    DeskPRO_Window.sections.deals_section.refresh();
                }
            });
        });


        this.getEl('organizations_list').on('click', '.remove', function() {
            var row = $(this).closest('.organization-row');
            var organizationId = row.data('organization-id');
            if (!organizationId) {
                return;
            }

            row.fadeOut('fast');

            $.ajax({
                url: BASE_URL + 'agent/deals/' + self.meta.deal_id + '/ajax-save',
                data: {
                    action: 'remove-organization',
                    organization_id: organizationId
                },
                type: 'POST',
                context: this,
                error: function() {
                    row.show();
                },
                success: function() {
                    row.remove();
                    DeskPRO_Window.util.modCountEl(self.getEl('members_count'), '-');
                    DeskPRO_Window.sections.deals_section.refresh();
                }
            });
        });

    },

    _initDisplayOptions: function() {
        this.displayOptionsList = $('.display-options:first ul.sortable-list', this.contentWrapper);
        var overlay_wrapper = this.displayOptionsWrapper = $('.display-options:first', this.contentWrapper);

        this.displayOptionsOverlay = new DeskPRO.UI.Overlay({
            contentElement: overlay_wrapper,
            triggerElement: $('.display-options-trigger', this.contentWrapper),
            onContentSet: function(eventData) {
                $('ul.sortable-list', eventData.wrapperEl).sortable({
                    'axis': 'y'
                });
            }
        });
        this.ownObject(this.displayOptionsOverlay);

    },

    _initAssignAgentSection: function(){

        var el = this.getEl('agent_assign_ob');
        this.assignOptionBox = new DeskPRO.UI.OptionBoxRevertable({
            element: el,
            trigger: this.getEl('assign_ob_trigger'),
            onSave: function(ob) {
                var selections = ob.getAllSelected();
                var agent_id = parseInt(selections.agents || 0);

                var postData = [];
                postData.push({
                    name: 'agent_part_ids[]',
                    value: selections.agents
                });
                var label = $('.agent-label-' + agent_id, ob.getElement()).first().text().trim();

                var value = selections.agents;

                var el = $('.prop-agent-id');
                if (value == "0") value = 0;
                if (value == 0) {
                    el.text('Unassigned');
                    el.css('background-image', '');
                } else {
                    var agentInfo = DeskPRO_Window.getAgentInfo(value);
                    el.text(label);
                    el.css('background-image', agentInfo.pictureUrlSizable.replace('{SIZE}', 20));
                }
                $('.reply-agent-team-ob').slideUp();
                $.ajax({
                    url: BASE_URL + 'agent/deals/'+self.meta.deal_id+'/'+selections.agents+'/set-agent-parts.json',
                    type: 'POST',
                    dataType: 'json',
                    data: postData,
                    success: function(data) {
                        DeskPRO_Window.sections.deals_section.refresh();
                    }
                });

            }
        });

    },
    _initAgentSection: function(){

        //------------------------------
        // Assign ...
        //------------------------------
        var self = this;
        var obEl = this.getEl('agent_assign_ob');
        this.assignAgentOptionBox = new DeskPRO.UI.OptionBox({
            element: obEl,
            trigger: this.getEl('assign_btn'),
            onClose: function(ob) {
                var selections = ob.getAllSelected();

                // Agent
                var agent_id = parseInt(selections.agents || 0);
                self.getEl('agent_id').val(agent_id);
                var label = $('.agent-label-' + agent_id, obEl).text().trim();
                self.getEl('agent_label').text(label);

            }
        });
    },

    _initLabels: function() {

        // Tags
        this.labelsInput = new DeskPRO.UI.LabelsInput({
            type: 'deal',
            textarea: $(".deal-tags input", this.wrapper),
            onChange: this.saveLabels.bind(this)
        });
        this.ownObject(this.labelsInput);
    },

    saveLabels: function() {
        if (this._saveLabelsTimeout) {
            window.clearTimeout(this._saveLabelsTimeout);
        }

		this._labelsData = this.labelsInput.getFormData();
        this._saveLabelsTimeout = this._doSaveLabels.delay(2000, this);
    },

    _doSaveLabels: function() {
        var data = this._labelsData;

        $.ajax({
            url: this.getMetaData('labelsSaveUrl'),
            type: 'POST',
            context: this,
            data: data,
            dataType: 'json',
            success: function(data) {

            }
        });
    },

    _initAssignPersonSection: function() {

        var newrow = $('li.newpersonrow', this.el);


        newrow.on('click', function() {
            $('.choose-user').toggle();
            $('.add-new-user-container').remove();
        });
    },

    _initAssignOrganizationSection: function() {
        var newrow = $('li.neworgrow', this.el);

        newrow.on('click', function() {
            $('.choose-org').toggle();
            $('.add-new-org-container').remove();
        });
    },

    _initUserSection: function() {
        var self = this;
        var searchbox = this.getEl('user_searchbox');
        var userfields = this.getEl('user_choice');
        var rechooseBtn = this.getEl('switch_user');

        var placeUserRow = function(html) {
            self.placeUserRow(html);
        };

        searchbox.bind('personsearchboxclick', function(ev, personId, name, email, sb) {
            $.ajax({
                type: 'GET',
                url: BASE_URL + 'agent/deals/new/set-person-row/' + personId,
                dataType: 'html',
                data: {
                    'deal_id': self.meta.deal_id
                },
                context: this,
                success: function(html) {
                    $('input.person-id', searchbox).val(personId);
                    placeUserRow(html);
                }
            });
            sb.close();
            sb.reset();
        });
        searchbox.bind('personsearchboxclicknew personsearchenter', function(ev, term, sb) {
            $.ajax({
                type: 'GET',
                url: BASE_URL + 'agent/deals/new/create-person-row/0',
                data: {
                    'email': term
                },
                dataType: 'html',
                context: this,
                success: function(html) {
                    placeUserRow(html);

                    if (term.indexOf('@') !== -1) {
                        $('input.email', userfields).val(term);
                    } else {
                        $('input.name', userfields).val(term);
                    }
                }
            });
            sb.close();
            sb.reset();

            $('.save-trigger').live('click', function(){
                $.ajax({
                    type: 'GET',
                    url: BASE_URL + 'agent/deals/new/set-person-row/0',
                    data: {
                        'email': $('.add-new-user-container input.email').val(),
                        'name' : $('.add-new-user-container input.name').val(),
                        'deal_id': self.meta.deal_id
                    },
                    dataType: 'html',
                    context: this,
                    success: function(html) {
                        $('.add-new-user-container').remove();
                        placeUserRow(html);
                    }
                });

                return false;
            });
            $('.cancel-trigger').live('click', function(){
                $('.add-new-user-container').remove();
            });

        });
    },

    placeUserRow: function(html) {
        var searchbox = this.getEl('user_searchbox');
        var userfields = this.getEl('user_choice');
        var newrow = $('li.newpersonrow', this.el);
        var chooseuser = $('.choose-user');
        var row = $(html);


        row.insertBefore(newrow);
        userfields.empty();
        chooseuser.hide();
        userfields.show();
    },
    _initOrgEdit: function() {
        var self = this;
        var searchbox = this.getEl('org_searchbox');
        var orgfields = this.getEl('org_choice');
        var rechooseBtn = this.getEl('switch_org');

        rechooseBtn.click(function() {
            showOrganizationChoice();
            $('.org-id').val(0);
            return false;
        });

        var showOrganizationChoice = function() {
            orgfields.empty();
            orgfields.hide();
            searchbox.show();
            rechooseBtn.hide();
        };

        var placeOrganizationRow = function(html) {
            self.placeOrganizationRow(html);
        };

        var orgEdit    = this.getEl('org_edit_wrap');

        //orgEnableBtn
        this.getEl('org_searchbox').bind('orgsearchboxclick', function(ev, orgId, name) {

            $('.org-id', self.getEl('org_edit_wrap')).val().trim();
            $.ajax({
                type: 'POST',
                url: BASE_URL + 'agent/deals/new/set-organization-row/'+orgId,
                dataType: 'html',
                data: {
                    'deal_id': self.meta.deal_id
                },
                context: this,
                success: function(html) {
                    placeOrganizationRow(html);
                    $('input.organization_name', orgfields).val(term);
                    self.El('orgselect').val('');
                }
            });


        }).bind('orgsearchboxcreate', function(ev, term, name) {

            $.ajax({
                type: 'GET',
                url: BASE_URL + 'agent/deals/new/create-organization-row/0',
                dataType: 'html',
                context: this,
                success: function(html) {
                    placeOrganizationRow(html);
                    $('input.organization_name', orgfields).val(term);
                }
            });
        }).bind('orgsearchreverted', function(ev, term, name) {

            });

        $('.save-org-trigger').live('click', function(){

            $.ajax({
                type: 'POST',
                url: BASE_URL + 'agent/deals/new/set-organization-row/0',
                dataType: 'html',
                data: {
                    'name': $('.add-new-org-container input.name').val(),
                    'deal_id': self.meta.deal_id
                },
                context: this,
                success: function(html) {
                    placeOrganizationRow(html);
                    $('.add-new-org-container').remove();
                }
            });


        });

        $('.cancel-org-trigger').live('click', function(){
            $('.add-new-org-container').remove();
        });
    },

    placeOrganizationRow: function(html) {
        var searchbox = this.getEl('org_searchbox');
        var orgfields = this.getEl('org_choice');
        var newrow = $('li.neworgrow', this.el);
        var chooseorg = $('.choose-org');
        var row = $(html);


        row.insertBefore(newrow);
        //userfields.empty();
        chooseorg.hide();
        //searchbox.hide();
        orgfields.show();
    },

    _initStatusMenus: function() {

        var self = this;

        // Status
        var statusOb = new DeskPRO.UI.OptionBoxRevertable({
            trigger: this.getEl('status_trigger'),
            element: this.getEl('status_ob'),
            onSave: function(ob) {
                var catEl = ob.getSelectedElements('status');
                var catId = catEl.data('item-id');
                var title = catEl.data('full-title');

                self.getEl('status_label').text(title);

                $.ajax({
                    url: BASE_URL + 'agent/deals/' + self.meta.deal_id + '/ajax-save',
                    type: 'POST',
                    data: {
                        action: 'change-status',
                        status: catId
                    },
                    context: self,
                    dataType: 'json',
                    success: function(data) {
                        DeskPRO_Window.sections.deals_section.refresh();
                    }
                });
            }
        });

        // Visibility
        var visibilityOb = new DeskPRO.UI.OptionBoxRevertable({
            trigger: this.getEl('visibility_trigger'),
            element: this.getEl('visibility_ob'),
            onSave: function(ob) {
                var catEl = ob.getSelectedElements('visibility');
                var catId = catEl.data('item-id');
                var title = catEl.data('full-title');

                self.getEl('visibility_label').text(title);

                $.ajax({
                    url: BASE_URL + 'agent/deals/' + self.meta.deal_id + '/ajax-save',
                    type: 'POST',
                    data: {
                        action: 'change-visibility',
                        visibility: catId
                    },
                    context: self,
                    dataType: 'json',
                    success: function(data) {
                        DeskPRO_Window.sections.deals_section.refresh();
                    }
                });
            }
        });

    },

    _initCustomField: function(){

        var fieldsRendered = this.getEl('custom_fields_rendered');
        var fieldsForm = this.getEl('custom_fields_editable');

        var buttonsWrap = this.getEl('properties_controls');
        var propToggle = function(what) {
            if (what == 'display') {
                $('.showing-editing-fields', buttonsWrap).hide();
                $('.showing-rendered-fields', buttonsWrap).show();
                fieldsForm.hide();
                fieldsRendered.show();
            } else {
                $('.showing-rendered-fields', buttonsWrap).hide();
                $('.showing-editing-fields', buttonsWrap).show();
                fieldsRendered.hide();
                fieldsForm.show();
            }
        };

        $('.edit-fields-trigger', buttonsWrap).on('click', function() {
            propToggle('edit');
        });

        $('.save-fields-trigger', buttonsWrap).on('click', function() {
            var formData = $('input[type="text"], input[type="password"], input:checked, select, textarea', fieldsForm);

            $.ajax({
                url: BASE_URL + 'agent/deals/' + self.meta.deal_id + '/ajax-save-custom-fields',
                type: 'POST',
                data: formData,
                dataType: 'html',
                success: function(rendered) {
                    fieldsRendered.empty().html(rendered);
                    propToggle('display');
                }
            });
        });

    }
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');

DeskPRO.Agent.PageFragment.Page.NewDeal = new Orb.Class({

Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'newdeal';
		this.allowDupe = true;
	},

        initPage: function(el) {
		this.wrapper = el;
		this.contentWrapper = this.wrapper.children('.layout-content').attr('id', Orb.getUniqueId());
		this.parent(el);

                this.form = $('form', this.wrapper).submit(function(ev) {
			ev.preventDefault();
		});


                this._initDepartmentSection();
                this._initUserSection();
                this._initOrgEdit();
                this._initOtherSection();


                var visiMenu = new DeskPRO.UI.Menu({
			menuElement: this.getEl('visibility')
		});
                this.ownObject(visiMenu);

                $('button.submit-trigger', this.wrapper).on('click', this.submit.bind(this));
                $('.select-deal-type').on('change', function(){

                    var dealId = 0;
                    $.ajax({
				url: BASE_URL + 'agent/deals/' + dealId + '/ajax-save',
				data: { action: 'change-dealtype', deal_type_id: $(this).val() },
				type: 'POST',
				context: this,
				error: function() {

				},
				success: function(data) {
                                    $('.set-deal-stage').html(data.deal_stage);
				}
			});
                }) ;

        },

	closeSelf: function() {
		var ev = {cancel: false};
		this.fireEvent('closeSelf', ev);

		if (!ev.cancel) {
			this.parent();
		}
	},

        submit: function() {
		var formData = this.form.serializeArray();

		$.ajax({
			url: BASE_URL + 'agent/deals/new/save',
			type: 'POST',
			data: formData,
			dataType: 'json',
			context: this,
			success: function(data) {
				if (data.success) {

					DeskPRO_Window.runPageRoute('page:' + BASE_URL + 'agent/deal/' + data.deal_id);
                                        DeskPRO_Window.sections.deals_section.refresh();
					this.closeSelf();
				} else {
					alert('There was an error with the form');
				}
			}
		});
	},

        _initDepartmentSection: function() {

            var self = this;
            var el = this.getEl('agent_assign_ob');
            this.assignOptionBox = new DeskPRO.UI.OptionBoxRevertable({
            element: el,
            trigger: this.getEl('assign_btn'),
            onSave: function(ob) {
                var selections = ob.getAllSelected();
                var agent_id = parseInt(selections.agents || 0);

                var label = $('.agent-label-' + agent_id, ob.getElement()).first().text().trim();

                var value = selections.agents;
                if (value == 0) {
                    label = 'Unassigned';
                }

                self.getEl('agent_id').val(agent_id);
		self.getEl('agent_label').text(label);


                $('.reply-agent-team-ob').slideUp();

            }
        });

	},

        _initUserSection: function() {
		var self = this;
		var searchbox = this.getEl('user_searchbox');
		var userfields = this.getEl('user_choice');
		var rechooseBtn = this.getEl('switch_user');

		rechooseBtn.click(function() {
			showUserChoice(); return false;
		});

		var showUserChoice = function() {
			userfields.empty();
			userfields.hide();
			searchbox.show();
			rechooseBtn.hide();
		};

		var placeUserRow = function(html) {
			self.placeUserRow(html);
		};

		searchbox.bind('personsearchboxclick', function(ev, personId, name, email, sb) {
			$.ajax({
				type: 'GET',
				url: BASE_URL + 'agent/deals/new/get-person-row/' + personId,
				dataType: 'html',
				context: this,
				success: function(html) {
					$('input.person-id', searchbox).val(personId);
					placeUserRow(html);
				}
			});
			sb.close();
			sb.reset();
		});
		searchbox.bind('personsearchboxclicknew personsearchenter', function(ev, term, sb) {
			$.ajax({
				type: 'GET',
				url: BASE_URL + 'agent/deals/new/get-person-row/0',
				data: { 'email': term },
				dataType: 'html',
				context: this,
				success: function(html) {
					placeUserRow(html);

					if (term.indexOf('@') !== -1) {
						$('input.email', userfields).val(term);
					} else {
						$('input.name', userfields).val(term);
					}
				}
			});
			sb.close();
			sb.reset();
		});
	},

        placeUserRow: function(html) {
		var searchbox = this.getEl('user_searchbox');
		var userfields = this.getEl('user_choice');
		var rechooseBtn = this.getEl('switch_user');

		userfields.empty();
		userfields.html(html);

		rechooseBtn.show();
		searchbox.hide();
		userfields.show();
	},

       	//#########################################################################
	//# Org Edit
	//#########################################################################

	_initOrgEdit: function() {
		var self = this;
		var searchbox = this.getEl('org_searchbox');
		var orgfields = this.getEl('org_choice');
		var rechooseBtn = this.getEl('switch_org');

		rechooseBtn.click(function() {
			showOrganizationChoice();
                        $('.org-id').val(0);
                        return false;
		});

		var showOrganizationChoice = function() {
			orgfields.empty();
			orgfields.hide();
			searchbox.show();
			rechooseBtn.hide();
		};

                var placeOrganizationRow = function(html) {
			self.placeOrganizationRow(html);
		};

		var orgEdit    = this.getEl('org_edit_wrap');

		//orgEnableBtn
		this.getEl('org_searchbox').bind('orgsearchboxclick', function(ev, orgId, name) {
			 $('.org-id', self.getEl('org_edit_wrap')).val().trim();

		}).bind('orgsearchboxcreate', function(ev, term, name) {

                        $.ajax({
				type: 'GET',
				url: BASE_URL + 'agent/deals/new/get-organization-row/0',
				dataType: 'html',
				context: this,
				success: function(html) {
					placeOrganizationRow(html);
					$('input.organization_name', orgfields).val(term);
				}
			});
		}).bind('orgsearchreverted', function(ev, term, name) {

		});
	},

        placeOrganizationRow: function(html) {
		var searchbox = this.getEl('org_searchbox');
		var orgfields = this.getEl('org_choice');
		var rechooseBtn = this.getEl('switch_org');

		orgfields.empty();
		orgfields.html(html);

		rechooseBtn.show();
		searchbox.hide();
		orgfields.show();
	},

        //#########################################################################
	//# Other Section
	//#########################################################################

	_initOtherSection: function() {

		this.otherTabs = new DeskPRO.UI.SimpleTabs({
			triggerElements: $('li', this.getEl('other_props_tabs')),
			context: this.getEl('other_props_tabs_content'),
			autoSelectFirst: false,
			onTabClick: (function(ev) {
				var contentWrap = this.getEl('other_props_tabs_content');
				var navWrap = this.getEl('other_props_tabs_wrap');
				var tab = ev.tabEl;

				// Toggle content state if we're clicking for the first time,
				// or re-clicking a tab
				if (!$('.on', navWrap).length || tab.is('.on')) {
					if (contentWrap.is(':visible')) {
						contentWrap.hide();
						navWrap.removeClass('on');
					} else {
						contentWrap.show();
						navWrap.addClass('on');
					}
				}
			}).bind(this)
		});
		this.ownObject(this.otherTabs);

		// Attachments
		var list = $('.file-list', this.wrapper);
		$('input', list[0]).live('click', function() {
			var el = $(this);
			var li = el.parent();
			if (el.is(':checked')) {
				li.removeClass('unchecked');
			} else {
				li.addClass('unchecked');
			}
		});

		DeskPRO_Window.util.fileupload(this.wrapper, {
			page: this
		});
	}


})

Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');

DeskPRO.Agent.PageFragment.Page.TwitterUser = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'twitter-user';
	},

	initPage: function(el) {
		this.el = $(el);

		var self = this;

		// user follow/unfollow
		this.el.on('click', '.follow-button', function(e) {
			e.preventDefault();

			$(this).addClass('unfollow-button').removeClass('follow-button')
			$(this).find('.clean-white').text('Unfollow');

			$.ajax({
				url: self.getMetaData('saveFollowUrl'),
				type: 'POST',
				data: {
					user_id: self.getMetaData('userId'),
					account_id: self.getMetaData('accountId')
				}
			});
		});
		this.el.on('click', '.unfollow-button', function(e) {
			e.preventDefault();

			$(this).addClass('follow-button').removeClass('unfollow-button');
			$(this).find('.clean-white').text('Follow');

			$.ajax({
				url: self.getMetaData('saveUnfollowUrl'),
				type: 'POST',
				data: {
					user_id: self.getMetaData('userId'),
					account_id: self.getMetaData('accountId')
				}
			});
		});

		var switchAccountMenu = new DeskPRO.UI.Menu({
			triggerElement: this.getEl('switch_account_trigger'),
			menuElement: this.getEl('switch_account_menu'),
			onItemClicked: function(info) {
				var loadRoute = $(info.itemEl).data('load-route');
				switchAccountMenu.close();

				DeskPRO_Window.removePage(self);
				DeskPRO_Window.runPageRoute('page:' + loadRoute);
			}
		});

		this.el.on('click', '.send-message-button', function(e) {
			e.preventDefault();

			var overlay = new DeskPRO.UI.Overlay({
				contentMethod: 'ajax',
				contentAjax: { url: BASE_URL + 'agent/twitter/user/' + self.getMetaData('userId') + '/message-overlay' },
				zIndex: 40000, // Above floating people windows
				onAjaxDone: function() {
					var wrapper = overlay.getWrapper();
					var textarea = wrapper.find('textarea[name=text]');

					var helper = new DeskPRO.Agent.PageHelper.Twitter(wrapper, self, {
						saveMessageCallback: function(data) {
							wrapper.addClass('loading');

							$.ajax({
								url: self.getMetaData('saveUserMessageUrl'),
								type: 'POST',
								data: data,
								dataType: 'json'
							}).done(function(data) {
								if (data.success) {
									overlay.close();
								} else if (data.error) {
									alert(data.error);
								}
							}).always(function() {
								wrapper.removeClass('loading');
							});
						}
					});

					helper.updateTweetLength(textarea);
					textarea.focus();
				}
			});
			overlay.open();
		});

		this.getEl('user_searchbox').on('personsearchboxclick', function(e, personId, name, email, obj) {
			obj.close();
			self.getEl('userselect').val('');

			$.ajax({
				url: self.getMetaData('saveUserPersonUrl'),
				data: {user_id: self.getMetaData('userId'), person_id: personId},
				type: 'POST',
				dataType: 'json',
				success: function(json) {
					if (json.success) {
						self.getEl('choose_user').before(json.html);
					}
				}
			});
		});

		this.getEl('org_searchbox').on('orgsearchboxclick', function(e, orgId, name, obj) {
			obj.close();
			self.getEl('orgselect').val('');

			$.ajax({
				url: self.getMetaData('saveUserOrganizationUrl'),
				data: {user_id: self.getMetaData('userId'), organization_id: orgId},
				type: 'POST',
				dataType: 'json',
				success: function(json) {
					if (json.success) {
						self.getEl('org_edit_wrap').before(json.html);
					}
				}
			});
		});

		$('.profile-box-container.tabbed', this.wrapper).each(function() {
			var simpleTabs = new DeskPRO.UI.SimpleTabs({
				triggerElements: '> header li',
				context: this
			});
		});

		var statusesLoadPending = false;
		var statusesTab = self.getEl('statuses_tab');
		statusesTab.on('click', '.more-box .more-button', function() {
			if (statusesLoadPending) {
				return;
			}
			statusesLoadPending = true;

			statusesTab.find('.more-box .flat-spinner').show();

			$.ajax({
				url: $(this).data('load-url'),
				success: function(html) {
					statusesTab.find('.more-box').remove();
					statusesTab.append(html);
				},
				complete: function() {
					statusesTab.find('.more-box .flat-spinner').hide();
					statusesLoadPending = false;
				}
			});
		});

		var followingLoadPending = false;
		var followingTab = self.getEl('following_tab');
		followingTab.on('click', '.more-box .more-button', function() {
			if (followingLoadPending) {
				return;
			}
			followingLoadPending = true;

			followingTab.find('.more-box .flat-spinner').show();

			$.ajax({
				url: $(this).data('load-url'),
				success: function(html) {
					followingTab.find('.more-box').remove();
					followingTab.append(html);
				},
				complete: function() {
					followingTab.find('.more-box .flat-spinner').hide();
					followingLoadPending = false;
				}
			});
		});

		var followersLoadPending = false;
		var followersTab = self.getEl('followers_tab');
		followersTab.on('click', '.more-box .more-button', function() {
			if (followersLoadPending) {
				return;
			}
			followersLoadPending = true;

			followersTab.find('.more-box .flat-spinner').show();

			$.ajax({
				url: $(this).data('load-url'),
				success: function(html) {
					followersTab.find('.more-box').remove();
					followersTab.append(html);
				},
				complete: function() {
					followersTab.find('.more-box .flat-spinner').hide();
					followersLoadPending = false;
				}
			});
		});

		$('.timeago', this.el).timeago();

		this.twitterHelper = new DeskPRO.Agent.PageHelper.Twitter(this.el, this);
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');

DeskPRO.Agent.PageFragment.Page.TwitterStatusOverlay = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'twitter-status-overlay';
	},

	initPage: function(el) {
		this.el = $(el);

		DeskPRO_Window.initInterfaceLayerEvents(this.el);

		this.twitterHelper = new DeskPRO.Agent.PageHelper.Twitter(this.el, this);
	}
});
