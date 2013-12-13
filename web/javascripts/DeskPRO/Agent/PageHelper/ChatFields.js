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