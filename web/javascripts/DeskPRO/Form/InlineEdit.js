Orb.createNamespace('DeskPRO.Form');

/**
 * @option {HTMLElement} baseElement The base element to activate this inline edit on
 * @option {String} editableClass The class that denotes an editable thing
 * @option {Object} ajax The AJAX options to pass to jQuery.ajax. Only the ajax.url item is required.
 */
DeskPRO.Form.InlineEdit = new Class({
	Implements: Options,

	/**
	 * Hash of options
	 * @var {Object}
	 */
	options: {
		baseElement: window.document,
		editableClass: 'editable',
		triggers: null,
		ajax: {
			timeout: 20000,
			type: 'POST',
			url: ''
		},
		saveFinishCallback: function() { }
	},

	/**
	 * An array of 'editinfo's that are currently open
	 * @var {Array}
	 */
	activeEdits: [],

	/**
	 * ajax_id=>editinfo of changes that are currenly being sent via ajax
	 * @var {Object}
	 */
	sendingEdits: {},

	/**
	 * If a document click should send open edits. This is used
	 * with the double-click. Also controls if the Escape key cancels.
	 * @var {Boolean}
	 */
	documentClickSubmitOn: false,



	initialize: function (options) {

		this.setOptions(options);

		this.options.baseElement = $(this.options.baseElement);

		var sel = '.' + this.options['editableClass'];
		var self = this;
		$(sel, this.options['baseElement']).each(function() { self.initEditable(this); });

		this.options.baseElement.on('click', function(ev) {
			self.handleDocumentClick(ev);
		})

		$(document).on('keydown', function(ev) {
			// Escape key
			if (ev.keyCode == 27) {
				self.closeEditables();
			}
		});
	},



	/**
	 * Initialize an editable by attaching new triggers
	 *
	 * If the element has the class parent-trigger, then the parent
	 * will have the dblclick even listener. This is useful for example
	 * in tables, where you want the entire table row to accept the click.
	 *
	 * @param {HTMLElement} el
	 */
	initEditable: function(el) {
		var self = this;

		var j_el = $(el);

		if (j_el.is('.parent-trigger')) {
			var parent = j_el.parent();
			var evname = parent.hasClass('single-click-activate') ? 'clcik' : 'dblclick';
			parent.on(evname, function() {
				self.startEditable(j_el);
			});
		} else {
			var evname = j_el.hasClass('single-click-activate') ? 'clcik' : 'dblclick';
			j_el.on(evname, function() { self.startEditable(this); });
		}

		if (this.options.triggers) {
			$(this.options.triggers, j_el.parent()).on('click', function(ev) {
				ev.stopPropagation();
				self.startEditable(j_el);
			});
		}
	},



	/**
	 * When anywhere on the page is clicked, we need to see
	 * if that means we should submit changes.
	 */
	handleDocumentClick: function(event) {
		if (!this.documentClickSubmitOn) {
			return;
		}

		// Dont listen if the click was inside the editable area
		if ($(event.target).parents().is('.editable')) {
			return;
		}

		this.submitOpen();
		this.documentClickSubmitOn = false;
	},



	/**
	 * Start editing an element.
	 *
	 * This detatches (but doesn't remove) rendered elements from the DOM
	 * from inside the editable. Then it moves (not clones) the form fields
	 * from the hidden container into the editable.
	 *
	 * @param {HTMLElement} el
	 */
	startEditable: function(editable) {

		editable = $(editable);

		// Rendered els are whatever is inside the editable.
		// We wrap the inside with a rendered-value div so we can easily
		// just move the nodes using that one wrapper
		var rendered_els = $('div.rendered-value', editable);
		if (!rendered_els.size()) {
			editable.wrapInner('<div class="rendered-value" />');
			rendered_els = $('div.rendered-value', editable);
		}

		// Form elements is the whole thing, they are already in a wrapper of some kind
		var form_elements = $($(editable).data('editable-for'), this.options.baseElement);
		var form_elements_container = form_elements.parent();

		rendered_els.fadeOut('fast', function() {
			rendered_els.detach();
			form_elements.addClass('editable-fields-on').hide().appendTo(editable).fadeIn('fast', function() {
				form_elements.find('input, textarea, select').first().focus();
			});

			$('input, textarea, select', form_elements)
				.addClass('unchanged')
				.on('change', function() { $(this).removeClass('unchanged'); })
				.on('keypress', function() { $(this).removeClass('unchanged'); })
				.filter(':visible').first().focus();
		});

		var editinfo = {
			'editable': editable,
			'rendered_els': rendered_els,
			'form_elements': form_elements,
			'form_elements_container': form_elements_container
		};

		editable.addClass('editing').parent().addClass('editing');

		this.documentClickSubmitOn = true;
		this.activeEdits.push(editinfo);
	},



	/**
	 * Submits all the fields that are currently 'open'.
	 */
	submitOpen: function() {

		if (!this.activeEdits.length) {
			return;
		}

		var data = $('.editable-fields-on :input, .editable-ajax-data :input', this.options['baseElement'])
			.serializeArray();

		var is_multi = this.activeEdits.length;

		var sending_edits = [];

		// Move all open to pending
		var editinfo = null;
		while (editinfo = this.activeEdits.pop()) {
			this.setEditinfoLoading(editinfo);
			sending_edits.push(editinfo);
		}

		var ajax_id = Orb.uuid();
		this.sendingEdits[ajax_id] = sending_edits;

		if (data.length) {
			var self = this;
			var ajax_options = Object.merge({
				success: function(data, textStatus, XMLHttpRequest) {
					DP.console.log('ajax-save data: %o', data);
					self.handleAjaxSuccess(ajax_id, data);
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					DP.console.log('ajax-save error: %s', textStatus);
					self.handleAjaxFailure(ajax_id);
				},
				context: this,
				dataType: 'json',
				data: data
			}, this.options['ajax']);

			DP.console.log('ajax-save: %s', ajax_options.url);
			DP.console.log('ajax-save data: %o', ajax_options.data);

			$.ajax(ajax_options);
		} else {
			this.handleAjaxSuccess(ajax_id, {});
		}
	},



	/**
	 * Handles a successful AJAX.
	 *
	 * data is expected to be a hash of field_id: {info}
	 * where info is either a string that represents the rendered field,
	 * or an array of error codes we'll use with the error handler.
	 *
	 * @param {Integer} ajax_id
	 * @param {Object} data
	 */
	handleAjaxSuccess: function(ajax_id, data) {

		var all_sending_edits = this.sendingEdits[ajax_id];
		delete this.sendingEdits[ajax_id];

		var sending_edit = null;
		var editinfo = null;
		while (editinfo = all_sending_edits.pop()) {
			var field_data = this._findDataFromEditinfo(editinfo, data);

			var html = null;

			// We got something back
			if (field_data) {
				if (field_data.errors) {
					// TODO handle errors
					continue; // continue because we dont want to process back into rendered
				} else if (field_data.html) {
					html = field_data.html;
				}
			}

			// We dont have HTML, we'll have to guess what the rendered value is
			if (!html) {
				var value_arr = $('input[type="text"], textarea, select', editinfo.form_elements).serializeArray();
				var value_bits = [];
				value_arr.each(function (v) {
					value_bits.push(v.value);
				});

				html = value_bits.join(', ');
			}

			// Remove old rendered value
			editinfo.rendered_els.remove();

			// Set a new rendered value by creating a new element
			// The closeEditInfo call next will actually attach the
			// node to the correct DOM editable
			editinfo.rendered_els = $('<div class="rendered-value" />').html(html);

			this.closeEditinfo(editinfo);
		}

		this.options.saveFinishCallback(data);
	},



	/**
	 * Try to match an editinfo which contains fields, to data returned
	 * from ajax.
	 *
	 * A single 'field' can be made up of more than one actual form element
	 * So the data we get back is often ID'd by the parent.
	 * For example, date[mm] and date[yy] might be the real form elements,
	 * but AJAX would return data for the field with the identifier simply 'date'.
	 *
	 * Since each editable is for a single field, they must all share the same
	 * prefix/group. So we can simply try to find the common prefix by removing
	 * each sub-field one at a time.
	 * 'date_mm': not found, so we cut down to just 'date': and its found
	 */
	_findDataFromEditinfo: function(editinfo, data) {

		var id = $(':input', editinfo.form_elements).eq(0).attr('id');
		if (!id) {
			return data;
		}
		var id_parts = id.split('_');

		do {
			var check_part = id_parts.join('_');
			if (data[check_part] != undefined) {
				return data[check_part];
			}
		} while (id_parts.pop());

		return data;
	},


	/**
	 * When AJAX fails
	 *
	 * @param {Integer} ajax_id
	 */
	handleAjaxFailure: function(ajax_id) {
		// TODO retry? show error?
	},



	/**
	 * Called on submit so we can show a loading indicator of some kind.
	 *
	 * @param {Object} editinfo
	 * @param {Boolean} is_multi True if multiple fields are sending at once, may affect the design
	 */
	setEditinfoLoading: function (editinfo, is_multi) {
		// TODO loading el?
	},


	/**
	 * Close all open editables.
	 */
	closeEditables: function() {
		var editinfo = null;
		while (editinfo = this.activeEdits.pop()) {
			this.closeEditinfo(editinfo);
		}
	},



	/**
	 * Close a specific editable.
	 *
	 * This moves the form elements back to its old hidden container.
	 * Then it reattaches the rendered elements to the editable container
	 * (the rendered elements may be changed by the ajax save, so the new value
	 * would appear).
	 */
	closeEditinfo: function(editinfo) {

		// 1. Move form back to old location
		// 2. Put rendered data bc

		var editable = editinfo.editable;
		var rendered_els = editinfo.rendered_els;
		var form_elements = editinfo.form_elements;
		var form_elements_container = editinfo.form_elements_container;

		form_elements.fadeOut('fast', function() {
			form_elements.removeClass('editable-fields-on').appendTo(form_elements_container);
			if (rendered_els.parent().get(0) != editable.get(0)) {
				rendered_els.hide().appendTo(editable).fadeIn('fast');

				editable.removeClass('editing').parent().removeClass('editing');
			}
		});
	}
});
