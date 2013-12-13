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

Orb.createNamespace('DeskPRO.Form');

/**
 * A rule builder is a form widget that lets you add multiple "rules" to a list.
 * A rule conists of a rule type (for example, "category"), an op ("is" or "is not" etc),
 * and then a user input or selection (the actual category choice).
 *
 * This builder handles everything except form naming (eg. rule[0][type] etc), and when
 * to add rows (eg. on a button click). Some other component will figure those parts out.
 *
 * Example:
 *    <script type="text/javascript" charset="utf-8">
 *        $(document).ready(function() {
 *            var editor = new DeskPRO.Form.RuleBuilder($('#rules-tpl'));
 *            $('#add_rule_btn').data('add-count', 0).on('click', function() {
 *                var count = parseInt($(this).data('add-count'));
 *                var basename = 'newrule['+count+']';
 *
 *                $(this).data('add-count', count+1);
 *
 *                editor.addNewRow($('#rules'), basename);
 *            });
 *        });
 *    </script>
 *    <input type="button" value="Add Rule" id="add_rule_btn" />
 *    <div id="rules"></div>
 *    <div id="rules-tpl" style="display:none">
 *        <div class="row"><div class="type"></div><div class="op"></div><div class="choice"></div></div>
 *        <div class="type" title="Department" data-rule-type="department">
 *            <div class="op"><select name="op"><option value="is">is</option><option value="not">is not</option></select></div>
 *            <div class="choice"><select name="department"><option value="1">Sales</option><option value="2">Support</option></select></div>
 *        </div>
 *    </div>
 */
DeskPRO.Form.RuleBuilder = new Orb.Class({
	Implements: [Orb.Util.Events],

	/**
	 * @param {jQuery} ruleTpl This is the wrapper element that contains the templates used for each rule type
	 */
	initialize: function(ruleTpl) {
		this.ruleTpl = null;
		this.typeSelectHtml = null;
		this.types = {};
		this.rowDestroy = {};
		this.enableTypes = {};

		this.ruleTpl = ruleTpl;
		var self = this;

		this.genTypeSel();
	},

	enableType: function(type) {
		this.enableTypes[type] = true;
		this.genTypeSel();
	},

	disableType: function(type) {
		this.enableTypes[type] = false;
		this.genTypeSel();
	},

	genTypeSel: function() {
		var groups = {};
		var self = this;
		if (this.typeSel) {
			this.typeSel.remove();
			this.typeSel = null;
		}

		if (document.getElementById('dp_admin_page')) {
			var html = ['<select name="type" style="max-width: 400px;"><option>&nbsp;</option>'];
		} else {
			var html = ['<select name="type" style="max-width: 320px;"><option>&nbsp;</option>'];
		}
		$('.builder-type', this.ruleTpl).each(function(i,el) {
			var type = $(el).data('rule-type');
			if ($(el).data('type-off') && !self.enableTypes[type]) {
				return;
			}

			var title = $(el).attr('title');
			var subgroup = $(el).data('rule-group');

			self.types[type] = title;

			if (subgroup) {
				var id = Orb.uuid();
				if (!groups[subgroup]) {
					groups[subgroup] = {'id': id, types: []};
					html.push('<optgroup label="'+subgroup+'" class="'+id+'"></optgroup>');
				}

				groups[subgroup]['types'].push([type, title]);
			} else {
				html.push('<option value="' + type + '">' + title + '</option>');
			}
		});
		html.push('</select>');
		html = html.join('');

		var typeSel = $(html);

		Object.each(groups, function(info, group) {
			var ul = $('optgroup.' + info.id, typeSel);
			var lis = [];
			Array.each(info.types, function(type) {
				lis.push('<option value="' + type[0] + '">' + type[1] + '</option>');
			});

			var lis = $(lis.join(''));
			ul.append(lis);
		});

		this.typeSel = typeSel;
		this.typeSel.css({
			position: 'absolute',
			bottom: 0,
			left: 0,
			visibility: 'hidden'
		});
		this.typeSel.appendTo('body');
		this.typeSel.css('width', this.typeSel.width() + 35);
		this.typeSel.hide();
	},

	destroy: function() {
		Object.each(this.rowDestroy, function (rowDestroy) {
			Array.each(rowDesotry, function (item) {
				if (item.destroy) {
					item.destroy();
				} else if (item.remove) {
					item.remove();
				}
			});
		});

		this.typeSel.remove();
	},

	/**
	 * Add a new rule row
	 *
	 * @param  {jQuery} addToEl The element to append the new rule to
	 * @param  {String} formBaseName The base name for the form. For example, newrule[0], and after you might use newrule[1] etc.
	 * @param  {Object} existing Existing data to set
	 * @return {jQuery} The newly added row
	 */
	addNewRow: function(addToEl, formBaseName, existing) {

		var isStatic = addToEl.is('.static-list');

		if (existing && !this.types[existing.type]) {
			return null;
		}

		var rowId = Orb.uuid();

		var new_row = $('.row', this.ruleTpl).children().clone();
		new_row.data('row-id', rowId);

		if (isStatic) {
			new_row.addClass('static-list');
		}

		// Add select
		var select = this.typeSel.clone();
		select.css({
			position: 'static',
			left: '',
			bottom: ''
		});
		$('.builder-type-choice', new_row).append(select);

		var self = this;

		$('.builder-remove', new_row).on('click', function() {
			self.removeRow(new_row);
		});

		// Update its name
		if (formBaseName) {
			new_row.data('form-base-name', formBaseName);
			this.updateFormName(new_row, formBaseName);
		}

		// Handle when its type is changed
		select.on('change', (function() {
			this.handleSelectChange(new_row);
		}).bind(this));

		$(addToEl).append(new_row);

		if (!isStatic) {
			DP.select(select);
		} else {
			var lbl = $('<span />');
			lbl.text(select.find('option:selected').text());
			select.hide().after(lbl);
		}

		var opt = false;
		if (existing && this.types[existing.type]) {

			select.val(existing.type).change();

			var label = $('.builder-type .current-value', new_row);
			label.text(this.types[existing.type]);

			this.handleSelectChange(new_row);
			$('.builder-op select', new_row)
				.val(existing.op)
				.addClass('op')
				.change();

			if (typeof existing.options == 'string' || typeof existing.options == 'number' || typeOf(existing.options) != 'object') {
				// If its just one item, then we'll just assume its the first field
				var els = $(':input, textarea, select', new_row).filter(':not(.op, .type)').first().val(existing.options);
			} else {
				// Otherwise we'll assume its a k=>v array
				Object.each(existing.options, function(val, name) {
					if (!name || !name.length) return;

					var name_safe = name.replace(/\[/, '\\[').replace(/\]/, '\\]');
					if (typeof val == 'string' || typeof val == 'number') {
						var el = $('[name="'+name_safe+'"], [name$="'+this.makeArrayName(name,true)+'"]', new_row).first().val(val).change();
						if (el.is('select')) {
							el.find('option').each(function() {
								if (this.value == val) {
									$(this).prop('selected', true);
								}
							});
						}
					} else if (typeOf(val) == 'object') {
						Object.each(val, function(subval, subname) {
							var sub_name = name_safe + "["+subname+"]";
							var sub_name_safe = name_safe + "\\["+subname+"\\]";

							if (typeOf(subval) == 'object') {
								Object.each(subval, function(v, k) {
									var k_name = sub_name + "[" + k + "]";
									var el = $('[name$="'+this.makeArrayName(k_name,true)+'"]', new_row).first();
									if (el.is(':checkbox')) {
										el.prop('checked', true).change();
									} else {
										el.val(v).change();
									}
								}, this);
							} else if (typeOf(subval) == 'array') {
								Array.each(subval, function(v) {
									var k_name = sub_name + "[]";
									var el = $('[name$="'+this.makeArrayName(k_name,true)+'"]', new_row);
									if (el.is('select')) {
										el.find('[value="' + v + '"]').prop('selected', true);
									}
								}, this);
							} else {
								var el = $('[name="'+sub_name_safe+'"], [name$="'+this.makeArrayName(sub_name,true)+'"]', new_row).first().val(subval).change();
							}
						}, this);
					} else if (typeOf(val) == 'array') {
						if (name == 'labels') {
							var texts = [];
							var labelval = $('.builder-options select.label-values', new_row);
							Array.each(val, function(subval) {
								texts.push(subval);
							});

							$('.builder-options .menu-trigger', new_row).text(texts.join(', ')).data('select-texts', texts);
						}

						Array.each(val, function(subval) {
							var el = $('option[value="'+subval+'"]', new_row).first().get(0);
							if (el) el.selected = true;
						}, this);
					} else {
						var el = $('[name="'+name_safe+'"], [name$="'+this.makeArrayName(name,true)+'"]', new_row).first().val(val).change();
					}
				}, this);
			}

			var ruleHandler = new_row.data('rule-handler-inst');
			if (ruleHandler) {
				ruleHandler.initValues();
			}
		} else {
			existing = null;
		}

		this.fireEvent('newRow', [new_row, addToEl, existing]);

		return new_row;
	},



	/**
	 * When a select element is changed we need to update the op and choices.
	 *
	 * @param {jQuery} row The row that we need to update
	 */
	handleSelectChange: function(row) {

		// Destroy previous
		this.destroyRow(row);

		var isStatic = row.is('.static-list');

		var rowId = row.data('row-id');
		var rowDestroy = [];

		var type = $('.builder-type-choice select', row).val();

		var rule_tpl = $('.builder-type[data-rule-type="'+type+'"]', this.ruleTpl);

		var op = $('div.builder-op', rule_tpl).children().clone();

		var rule_options_tpl = $('.builder-options', rule_tpl);
		var choice = rule_options_tpl.clone();
		if (!rule_options_tpl.hasClass('newline')) {
			choice.css('display', 'inline');
		}

		$('.builder-op', row).empty().append(op);
		$('.builder-op', row).find('select').addClass('op');
		$('.builder-options', row).empty().append(choice);

		row.find('select.op').css('visibility', 'hidden');
		if (!isStatic) {
			DP.select(row.find('select.op'));
		} else {
			var lbl = $('<span />');
			lbl.text(row.find('select.op').find('option:selected').text());
			row.find('select.op').hide().after(lbl);
		}

		var ruleHandlerName = rule_tpl.data('rule-handler');
		var ruleHandler = null;
		if (ruleHandlerName) {
			ruleHandlerObj = Orb.getNamespacedObject(ruleHandlerName);
			ruleHandler = new ruleHandlerObj({
				ruleBuilder: this,
				rowEl: row,
				rowId: rowId,
				opMenu: row.find('select.op')
			});

			rowDestroy.push(ruleHandler);
		}

		var numChilds = choice.children().length;

		if (numChilds == 1) {
			var choiceSel = row.find('.builder-options').find('select').not('.no-auto');
			if (choiceSel.length) {
				choiceSel.css('visibility', 'hidden');
				choiceSel.each(function() {
					if (!isStatic) {
						var sel = $(this);
						sel.css('visibility', 'hidden');
						window.setTimeout(function() {
							if (sel.attr('multiple')) {
								var cellWidth = sel.closest('td').find('> .builder-options').width();
								sel.width(cellWidth - 10);
							}
							DP.select(sel);
						}, 150);
					} else {
						var lbl = $('<span />');
						lbl.text($(this).find('option:selected').text());
						$(this).hide().after(lbl);
					}
				});
			}
		}
		DP.select(row.find('.select2'));

		if (row.data('form-base-name')) {
			this.updateFormName($('.builder-op', row), row.data('form-base-name'));
			this.updateFormName($('.builder-options', row), row.data('form-base-name'));
		}

		if (ruleHandler) {
			ruleHandler.initRow();
			row.data('rule-handler-inst', ruleHandler);
		}

		if (rowDestroy.length) {
			this.rowDestroy[rowId] = rowDestroy;
		}

		var opSel = $('.builder-op select', row);
		var updateOp = function() {
			var val = opSel.val();
			if (val == 'changed') {
				$('.builder-options', row).hide();
			} else {
				$('.builder-options', row).show();
			}
		};
		opSel.on('change', updateOp);

		this.fireEvent('selectChange', [row, type]);
	},

	destroyRow: function(row) {
		var rowId = row.data('row-id');
		if (this.rowDestroy[rowId]) {
			Array.each(this.rowDestroy[rowId], function(item) {
				if (item.destroy) {
					item.destroy();
				} else if (item.remove) {
					item.remove();
				}
			});
			delete this.rowDestroy[rowId];
		}
	},

	removeRow: function(row) {
		this.destroyRow(row);
		row.remove();
	},



	/**
	 * This updates the form name to prepend a basename, and turns it into an array usable
	 * by php. For example, if the name was before type and formbaseName is newrule[1],
	 * the new form name is newrule[1][type].
	 *
	 * @param {jQuery} el The element to look within to change ALL names of
	 * @param {String} formBaseName The base form name to set
	 */
	updateFormName: function(el, formBaseName) {
		$('[name]', el).each(function() {
			var name = $(this).attr('name');
			name = name.replace(/^([\w\d]*)/, '[$1]');
			name = formBaseName + name;

			$(this).attr('name', name);
		});
	},

	makeArrayName: function(name, safe) {
		if (name.indexOf('[') === -1) {
			name = '[' + name + ']';
		}  else {
			name = name.replace(/^([\w\d]+)\[(.*?)$/, '[$1][$2');
		}

		if (safe) {
			name = name.replace(/\[/g, '\\[').replace(/\]/g, '\\]');
		}

		return name;
	}
});

Orb.createNamespace('DeskPRO');

DeskPRO.FaviconBadge = new Orb.Class({

	Implements: [Orb.Util.Options],

	initialize: function(options) {
		var self = this;
		this.options = {};

		this.origWindowTitle = document.title;

		this.options.strokeColor = 'rgb(255,0,0)';
		this.options.color = '#FFFFFF';

		this.options.strokeColorAlt = 'rgb(0,0,0)';
		this.options.colorAlt = '#FFFFFF';

		$(document).bind('windowshow', this.disableCrazyMode.bind(this));
		$(window).bind('mousemove', this.disableCrazyMode.bind(this));
		$(window).bind('keypress', this.disableCrazyMode.bind(this));

		this.setOptions(options);

		this.tinyconOptions = {
			font: '10px arial',
			fallback: false
		};

		this.animateTimeout = null;
		this.animateCount = 0;
		this.crazyMode = false;
		this.crazyTitle = null;
		this.lastNum = 0;
	},

	clearAnimate: function() {
		if (this.animateTimeout) {
			window.clearTimeout(this.animateTimeout)
			this.animateTimeout = null;
			this.animateTimeoutCount = 0;
		}

		$(document).unbind('windowshow.faviconbadge');
		$(window).unbind('mousemove.faviconbadge');
		$(window).unbind('keypress.faviconbadge');
	},

	enableCrazyMode: function(title) {

		if ($('html').hasClass('window-active')) {
			return;
		}

		this.crazyTitle = title || null;
		this.crazyMode = true;
		this.updateBadge(this.lastNum, true);
	},

	disableCrazyMode: function() {
		if (!this.crazyMode) {
			return;
		}
		this.crazyMode = false;
		this.crazyTitle = null;
		document.title = this.origWindowTitle;
		this.updateBadge(this.lastNum, false);
	},

	updateBadge: function(num, do_animate) {
		var self = this;

		this.clearAnimate();

		// We have only two digits to play with
		var num = parseInt(num);
		if (num > 99) {
			num = 99;
		}

		this.lastNum = num;

		// 0 means no number
		if (!num && !this.crazyMode) {
			Tinycon.setBubble('');
			return;
		}

		if (do_animate) {
			this.animateTimeout = window.setInterval(function() {
				self.animateCount++;
				if (self.crazyMode) {
					self.tinyconOptions.width = 7;
					self.tinyconOptions.height = 8;

					if (self.animateCount % 2 == 0) {
						self.tinyconOptions.colour = '#000000';
						self.tinyconOptions.background = '#FFFFFF';
						Tinycon.setOptions(self.tinyconOptions);
						Tinycon.setBubble('◎');
						if (self.crazyTitle) {
							document.title = self.origWindowTitle;
						}
					} else {
						self.tinyconOptions.colour = '#FF0000';
						self.tinyconOptions.background = '#FFFFFF';
						Tinycon.setOptions(self.tinyconOptions);
						Tinycon.setBubble('◉');
						if (self.crazyTitle) {
							document.title = self.crazyTitle;
						}
					}
				} else {
					self.tinyconOptions.width = 7;
					self.tinyconOptions.height = 9;

					if (self.animateCount % 2 == 0) {
						self.tinyconOptions.colour = self.options.color;
						self.tinyconOptions.background = self.options.strokeColor;
						Tinycon.setOptions(self.tinyconOptions);
						Tinycon.setBubble(num+'');
					} else {
						self.tinyconOptions.colour = self.options.colorAlt;
						self.tinyconOptions.background = self.options.strokeColorAlt;
						Tinycon.setOptions(self.tinyconOptions);
						Tinycon.setBubble(num+'');
					}
				}
			}, 800);
		} else {
			self.tinyconOptions.width = 7;
			self.tinyconOptions.height = 9;
			self.tinyconOptions.colour = self.options.color;
			self.tinyconOptions.background = self.options.strokeColor;
			Tinycon.setOptions(self.tinyconOptions);
			Tinycon.setBubble(num+'');
		}
	}
});

Orb.createNamespace('DeskPRO.Agent');

DeskPRO.Agent.InterfaceEffects = new Orb.Class({
	Implements: [Orb.Util.Events],
	initialize: function() {

	},

	initPage: function() {
		
	}
});
Orb.createNamespace('DeskPRO.Agent.Widget');

DeskPRO.Agent.Widget.FindPerson = new Orb.Class({

	Implements: [Orb.Util.Options, Orb.Util.Events],

	initialize: function(options) {
		this.options = {

		};

		this.setOptions(options);
	},

	_initOverlay: function() {
		if (this.overlay) return this.overlay;

		this.overlay = new DeskPRO.UI.Overlay({
			contentMethod: 'ajax',
			contentAjax: {
				url: BASE_URL + 'agent/people-search/quick-find'
			}
		});

		this.overlay.addEvent('ajaxDone', this._initElements, this);

		var event = {
			findPerson: this,
			overlay: this.overlay
		};

		this.fireEvent('initOverlay', [event]);
	},

	_initElements: function() {
		this.wrapper = this.overlay.getWrapper();

		this.headerNav   = $('header > nav', this.wrapper);
		this.simpleBtn   = $('.simple', this.headerNav).on('click', this.switchSimple.bind(this));
		this.advancedBtn = $('.advanced', this.headerNav).on('click', this.switchAdvanced.bind(this));

		this.results = $('section.deskpro-results-list', this.wrapper);
		this.loading = $('section.results-loading', this.wrapper);
		this.info = $('section.no-results-info', this.wrapper);

		this.searchArea   = $('section.search-area:first', this.wrapper);
		this.simpleForm   = $('form.simple:first', this.searchArea);
		this.advancedForm = $('form.advanced:first', this.searchArea);

		var self = this;
		this.simpleForm.on('submit', function(ev) {
			ev.preventDefault();
			self.submitSearch($(this));
		});
		this.advancedForm.on('submit', function(ev) {
			ev.preventDefault();
			self.submitSearch($(this));
		});
	},

	_initRuleBuilder: function() {

		if (this.ruleBuilder) return;

		var criteriaList  = $('.search-form', this.advancedForm);
		var criteriaTerms = $('.search-builder-tpl', this.wrapper);

		var editor = new DeskPRO.Form.RuleBuilder(criteriaTerms);
		$('.add-term', criteriaList).data('add-count', 0).on('click', function() {
			var count = parseInt($(this).data('add-count'));
			var basename = 'terms['+count+']';

			$(this).data('add-count', count+1);

			editor.addNewRow($('.search-terms', criteriaList), basename);
		});

		this.ruleBuilder = editor;
	},

	open: function() {
		this._initOverlay();
		this.overlay.open();
	},

	close: function() {
		this.overlay.close();
	},

	switchDisplayElement: function(el, subEl) {
		if (el == 'results') {
			this.results.show();
			this.loading.hide();
			this.info.hide();
		} else if (el == 'info') {
			this.info.show();
			$('> *', this.info).hide();
			$(subEl, this.info).show();

			this.results.hide();
			this.loading.hide();
		} else if (el == 'loading') {
			this.loading.show();
			this.results.hide();
			this.info.hide();
		}
	},

	switchSimple: function() {
		if (this.simpleBtn.is('.on')) return;

		this.switchDisplayElement('info', 'no-search');

		$('.on', this.headerNav).removeClass('on');
		this.simpleBtn.addClass('on');

		this.advancedForm.slideUp();
		this.simpleForm.slideDown();
	},

	switchAdvanced: function() {
		if (this.advancedBtn.is('.on')) return;

		this._initRuleBuilder();
		this.switchDisplayElement('info', 'no-search');

		$('.on', this.headerNav).removeClass('on');
		this.advancedBtn.addClass('on');

		this.simpleForm.slideUp();
		this.advancedForm.slideDown();
	},

	submitSearch: function(form) {
		var formData = form.serializeArray();

		this.switchDisplayElement('loading');

		$.ajax({
			url: BASE_URL + 'agent/people-search/quick-find-search.json',
			data: formData,
			dataType: 'json',
			type: 'POST',
			context: this,
			success: this.handleSearchSuccess
		});
	},

	handleSearchSuccess: function(data) {
		if (data.no_results) {
			this.switchDisplayElement('info', 'no-results');
			return;
		}

		this.results.empty().html(data.html);
		this._initNewResults();

		this.switchDisplayElement('results');
	},

	_initNewResults: function() {
		var self = this;
		$('.choose-trigger', this.results).on('click', function(ev) {

			ev.preventDefault();

			var el = $(this);
			var personId = el.data('person-id');

			var eventData = {
				el: el,
				personId: personId,
				event: ev,
				doCloseOverlay: true
			};

			self.fireEvent('choosePerson', [eventData]);

			if (eventData.doCloseOverlay) {
				self.close();
			}
		});
	},

	destroy: function() {
		if (this.overlay) {
			this.overlay.destroy();
		}

		if (this.ruleBuilder) {
			this.ruleBuilder.destroy();
		}
	}
});

Orb.createNamespace('DeskPRO.Agent.Widget');

DeskPRO.Agent.Widget.AgentSelector = new Orb.Class({

	Implements: [Orb.Util.Options, Orb.Util.Events],

	initialize: function(options) {
		this.options = {
			triggerElement: null,
			agentList: null,
			multipleChoice: false,
			showNone: false,
			noneLabel: 'Unassigned',
			zIndex: 30001,
			startWith: []
		};

		if (options) this.setOptions(options);

		this.previousSelection = '';

		if (this.options.triggerElement) {
			var self = this;
			$(this.options.triggerElement).on('click', function(ev) {
				ev.preventDefault();
				self.open(ev);
			});
		}
	},

	_initWrapper: function() {
		if (this.wrapper) return;

		var agentListItems = $('li', this.options.agentList);

		this.backdrop = $('<div class="backdrop"></div>').appendTo('body');
		this.backdrop.on('click', this.close.bind(this));

		this.wrapper = $('<div class="field-overlay agent-selector" style="display:none;"><div class="close-trigger"></div></div>');
		$('.close-trigger', this.wrapper).on('click', this.close.bind(this));

		var listWrapper = $(
			'<div class="with-scrollbar">' +
			'<div class="scrollbar disable"><div class="track"><div class="thumb"><div class="end"></div></div></div></div>' +
			'<div class="scroll-viewport"><div class="scroll-content">' +
			'</div></div></div>'
		);

		if (agentListItems.length >= 10) {
			this.filter = $('<div class="filter"><div class="input-wrap"><input type="text" value="" placeholder="Find an agent" /></div></div>').appendTo(this.wrapper);

			$('input', this.filter).on('keyup', this.updateFilter.bind(this));
		} else {
			this.filter = null;
		}

		var isMulti = this.options.multipleChoice;

		var startWith = this.options.startWith;
		var agentList = $('<ul />');

		if (this.options.showNone) {
			var newLi = $('<li class="agent-0" data-agent-id="0"><div class="name"><a>' + this.options.noneLabel + '</a></div></li>');
			var choiceContainer = $('<div class="choice" />');

			var checked = '';
			if (!startWith.length) {
				checked = 'checked="checked"';
			}
			if (isMulti) {
				var choice = $('<input type="checkbox" name="agents[]" '+checked+' value="0" class="agent-choice-0" />');
			} else {
				var choice = $('<input type="radio" name="agents[]" '+checked+' value="0" class="agent-choice-0" />');
			}
			choice.appendTo(choiceContainer);
			newLi.append(choiceContainer);
			newLi.append($('<br style="clear:left;height: 1px;overflow: hidden;"/>'));
			agentList.append(newLi);
		}

		agentListItems.each(function() {
			var li = $(this);

			var agentId = li.data('agent-id');
			var agentName = $('a:first', li).text();
			var image = $('img:first', li);

			var newLi = $('<li class="agent-' + agentId + '" data-agent-id="' + agentId + '" />');

			if (image.length) {
				var imgContainer = $('<div class="avatar" />');
				image.clone().appendTo(imgContainer);
				imgContainer.appendTo(newLi);
			}

			var nameContainer = $('<div class="name" />');
			nameContainer.append('<a>' + Orb.escapeHtml(agentName) + '</a>');
			nameContainer.appendTo(newLi);

			var checked = '';
			if (startWith.indexOf(agentId+'') !== -1 || startWith.indexOf(parseInt(agentId)) !== -1) {
				checked = 'checked="checked"';
			}

			var choiceContainer = $('<div class="choice" />');
			if (isMulti) {
				var choice = $('<input type="checkbox" name="agents[]" '+checked+' value="'+agentId+'" class="agent-choice-' + agentId + '" />');
			} else {
				var choice = $('<input type="radio" name="agents[]" '+checked+' value="'+agentId+'" class="agent-choice-' + agentId + '" />');
			}
			choice.appendTo(choiceContainer);
			choiceContainer.appendTo(newLi);

			newLi.append($('<br style="clear:left;height: 1px;overflow: hidden;"/>'));

			newLi.appendTo(agentList);

			newLi.on('click', function(ev) {
				ev.stopPropagation();
				if (!$(ev.target).is('input')) {
					choice.click();
				}
			});
		});

		delete agentListItems;

		this.agentList = agentList;

		var self = this;
		$('input[type="checkbox"], input[type="radio"]', agentList).on('click', function(ev) {
			var agentId = $(this).val();
			var checked = $(this).is(':checked');

			var eventData = {
				agentSelector: self,
				element: $(this),
				agentId: agentId,
				checked: checked,
				event: ev
			};

			self.fireEvent('selectionClick', [eventData]);
		});

		agentList.appendTo($('div.scroll-content', listWrapper));
		listWrapper.appendTo(this.wrapper);

		this.listWrapper = listWrapper;

		this.wrapper.appendTo('body');

		var selectionString = this.getSelection();
		if (this.options.multipleChoice) {
			selectionString = selectionString.join(',');
		}
		this.previousSelection = selectionString;

		var eventData = {
			agentSelector: this,
			wrapper: this.wrapper
		};

		this.fireEvent('initWrapper', [eventData]);
	},

	updateFilter: function() {
		var input = $('input', this.filter);
		var filter = input.val().trim().toLowerCase();

		var lis = $('> li', this.agentList);

		if (!filter) {
			lis.show();
			return;
		}

		lis.each(function() {
			var name = $('a:first', this).text().toLowerCase();
			if (name.indexOf(filter) !== -1) {
				$(this).show();
			} else {
				$(this).hide();
			}
		});
	},

	open: function(event) {

		this._initWrapper();

		var target = $(event.target);

		var width = this.wrapper.outerWidth();
		var height = this.wrapper.outerHeight();

		var pageWidth = $(document).width();
		var pageHeight = $(document).height();

		var pageX = target.offset().left;
		var pageY = target.offset().top;

		// Determine which way to open the menu,
		// We do this so the menu doesn't go off-screen if
		// its near the edge
		if (pageX+width < pageWidth) {
			var left = pageX+6;
		} else {
			var left = pageX - width - 4;
		}

		if (pageY+height < pageHeight) {
			var top = pageY - 6;
		} else {
			var top = pageY - height + 4;
		}

		if (top < 0) {
			top = 5;
		}

		this.backdrop.show();
		this.wrapper.addClass('open');
		this.wrapper.css({
			'z-index': this.options.zIndex,
			'position': 'absolute',
			'top': top,
			'left': left,
			'display': 'block'
		});

		this.listWrapper.tinyscrollbar();

		var eventData = {
			agentSelector: this,
			event: event
		};

		this.fireEvent('open', [eventData]);
	},

	close: function() {

		if (!this.wrapper.is('.open')) return;

		var eventData = {
			agentSelector: this,
			cancelClose: false
		};

		this.fireEvent('beforeClose', [eventData]);

		if (eventData.cancelClose) {
			return;
		}

		delete eventData.cancelClose;

		this.backdrop.hide();
		this.wrapper.hide().removeClass('open');

		this.fireEvent('close', [eventData]);

		var selectionString = this.getSelection();
		if (this.options.multipleChoice) {
			selectionString = selectionString.join(',');
		}

		if (this.previousSelection != selectionString) {
			eventData.selection = this.getSelection();
			this.fireEvent('selectionChanged', [eventData]);

			this.previousSelection = selectionString;
		}
	},

	isOpen: function() {
		return this.wrapper.is('.open');
	},

	getWrapper: function() {
		return this.wrapper;
	},

	getSelection: function() {
		if (this.options.multipleChoice) {
			var ids = [];
			$('input[type="checkbox"]:checked', this.agentList).each(function() {
				ids.push($(this).val());
			});

			return ids;

		} else {
			var id = $('input[type="radio"]:checked:first', this.agentList).val();
			return id;
		}
	}
});

Orb.createNamespace('DeskPRO.Agent.Widget');

DeskPRO.Agent.Widget.SnippetViewer = new Orb.Class({

	Implements: [Orb.Util.Options, Orb.Util.Events],

	initialize: function(options) {
		this.options = {
			viewUrl: null,
			triggerElement: null,
			positionMode: 'side',
			sidePosition: 'bottom',
			destroyOnClose: false,
			snippetType: 'tickets'
		};

		var self = this;
		this.setOptions(options);

		if (this.options.triggerElement) {
			$(this.options.triggerElement).on('click', function(ev) {
				ev.preventDefault();
				ev.stopPropagation();

				self.open();
			});
		}

		var pageSourceId = this.options.snippetType + '_snippet_shell_tpl';

		this.pop = new DeskPRO.Agent.PageHelper.Popover({
			positionMode: this.options.positionMode,
			sidePosition: this.options.sidePosition,
			pageUrl: this.options.viewUrl,
			pageSource: DeskPRO_Window.util.getPlainTpl($('#' + pageSourceId)),
			destroyOnClose: false,
			onPageInit: function(pop, page) {
				page.addEvent('closeSelf', function(ev) {
					ev.cancel = true;
					self.close();
				});

				page.addEvent('snippetClick', function(ev) {
					ev.page = page;
					self.fireEvent('snippetClick', [ev]);
				});
			}
		});
	},

	open: function() {
		this.fireEvent('onBeforeOpen');
		this.pop.open();
		DeskPRO.Agent.Widget.SnippetViewer.HasOpen = true;
	},

	close: function() {
		if (this.pop) {
			this.pop.close();
		}
		DeskPRO.Agent.Widget.SnippetViewer.HasOpen = false;

		if (this.options.destroyOnClose) {
			this.destroy();
		}
	},

	destroy: function() {
		this.pop.destroy();
		DeskPRO.Agent.Widget.SnippetViewer.HasOpen = false;
	}
});
DeskPRO.Agent.Widget.SnippetViewer.HasOpen = false;
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

Orb.createNamespace('DeskPRO.Agent.Widget');

DeskPRO.Agent.Widget.Merge = new Orb.Class({
	Implements: [Orb.Util.Options, Orb.Util.Events],

	initialize: function(options) {
		this.options = {
			tabType: null,
			metaId: 0,
			metaIdName: null,
			menu: null,
			trigger: null,
			overlayUrl: null,
			mergeUrl: null,
			overlayLoaded: null
		};
		this.setOptions(options);

		this.mergeMenu = null;
		if (this.options.menu) {
			this.mergeMenu = new DeskPRO.UI.Menu({
				triggerElement: this.options.trigger,
				menuElement: this.options.menu,
				onBeforeMenuOpened: this._menuPopulate.bind(this),
				onItemClicked: this._menuItemClick.bind(this)
			});
		}
	},

	_getOverlayUrl: function(id, otherId) {
		return this.options.overlayUrl.replace('{id}', id).replace('{other}', otherId);
	},

	_getMergeUrl: function(id, otherId) {
		return this.options.mergeUrl.replace('{id}', id).replace('{other}', otherId);
	},

	_menuPopulate: function() {
		var menu = this.options.menu, self = this;

		menu.find('.tab-reference').remove();

		var tabInsert = menu.find('.tab-insert'), insertPosition = tabInsert;
		if (!insertPosition.length) {
			insertPosition = false;
		}

		Array.each(DeskPRO_Window.getTabWatcher().findTabType(this.options.tabType), function(tab) {
			var id = tab.page.getMetaData(self.options.metaIdName);
			if (id && id != self.options.metaId) {
				var li = $('<li />').addClass('tab-reference').data('merge-id', id).text(tab.title);

				if (insertPosition) {
					insertPosition.after(li);
				} else {
					menu.prepend(li);
				}
				insertPosition = li;
			}
		});

		if (menu.find('li.tab-reference').length == 0) {
			if (!tabInsert.hasClass('always-show')) { tabInsert.hide(); }
			menu.find('li.no-choice').show();
		} else {
			tabInsert.show();
			menu.find('li.no-choice').hide();
		}
	},

	_menuItemClick: function(info) {
		var el = $(info.itemEl);

		if (el.hasClass('elm')) {
			return false;
		}

		var otherId = el.data('merge-id');
		if (!otherId) {
			otherId = 0;
		}

		if (this.overlay) {
			this.overlay.destroy();
		}

		this.overlay = new DeskPRO.UI.Overlay({
			contentMethod: 'ajax',
			contentAjax: { url: this._getOverlayUrl(this.options.metaId, otherId) },
			zIndex: 40000 // Above floating people windows
		});
		this.overlay.addEvent('ajaxDone', this._overlayLoaded.bind(this));
		this.overlay.open();
	},

	openWithId: function(otherId) {
		if (this.overlay) {
			this.overlay.destroy();
		}

		this.overlay = new DeskPRO.UI.Overlay({
			contentMethod: 'ajax',
			contentAjax: { url: this._getOverlayUrl(this.options.metaId, otherId) },
			zIndex: 40000 // Above floating people windows
		});
		this.overlay.addEvent('ajaxDone', this._overlayLoaded.bind(this));
		this.overlay.open();
	},

	open: function() {
		if (this.overlay) {
			this.overlay.destroy();
		}

		this.overlay = new DeskPRO.UI.Overlay({
			contentMethod: 'ajax',
			contentAjax: { url: this._getOverlayUrl(this.options.metaId, 0) },
			zIndex: 40000 // Above floating people windows
		});
		this.overlay.addEvent('ajaxDone', this._overlayLoaded.bind(this));
		this.overlay.open();
	},

	resetOverlay: function(html) {
		this.overlay.setContent($(html));
		this._overlayLoaded();
	},

	_overlayLoaded: function() {
		var overlay = this.overlay, wrapper = overlay.getWrapper(), self = this;

		DeskPRO.ElementHandler_Exec(wrapper);

		if (this.options.overlayLoaded) {
			this.options.overlayLoaded(this.overlay, this);
		}

		var buttons = wrapper.find('.merge-data .merge-target-button');

		if (buttons.length != 2) {
			return;
		}

		this.mergeButtons = buttons;

		var keepHtml = '', mergeHtml = '';

		buttons.each(function() {
			var $this = $(this);
			if ($this.data('keep')) {
				keepHtml = $this.html();
			} else {
				mergeHtml = $this.html();
			}
		});

		var rows = wrapper.find('.merge-data .merge-data-rows tr:not(.mergeable)');

		var getCmpVal = function(el) {
			var val = $.trim(el.text());
			val = val.toLowerCase();
			val = val.replace(/\s/g, val);
			return val;
		};
		var setMergeDataLostClasses = function() {
			var keepCol = 0, mergeCol = 0;

			if (wrapper.find('.left-text').data('keep')) {
				keepCol = 0;
				mergeCol = 1;
			} else {
				mergeCol = 0;
				keepCol = 1;
			}

			rows.each(function() {
				var $row = $(this);
				var tds = $row.find('td'),
					keep = tds.eq(keepCol),
					merge = tds.eq(mergeCol);

				keep.removeClass('merge-data-lost merge-data-keep');
				merge.removeClass('merge-data-lost merge-data-keep');

				if ($row.hasClass('always-keep')) {
					// always lose the merge data
					merge.addClass('merge-data-lost');
					keep.addClass('merge-data-keep');
				} else {
					if (getCmpVal(keep) != getCmpVal(merge)) {
						merge.addClass('merge-data-lost');
						keep.addClass('merge-data-keep');
					}
				}
			});
		};

		setMergeDataLostClasses();

		wrapper.find('.switch-trigger').on('click', function(ev) {
			ev.preventDefault();
			buttons.each(function() {
				if (!$(this).data('keep')) {
					$(this).click();
					return false;
				}
			});
		});

		buttons.click(function() {
			buttons.data('keep', false).html(mergeHtml);
			$(this).data('keep', 1).html(keepHtml);
			setMergeDataLostClasses();
		});

		wrapper.find('.merge-trigger').click(this._mergeTriggerClick.bind(this));
	},

	_mergeTriggerClick: function() {
		var mergeId = 0, otherMergeId = 0, self = this;

		this.mergeButtons.each(function() {
			var $this = $(this);
			if ($this.data('keep')) {
				mergeId = $this.data('merge-id');
			} else {
				otherMergeId = $this.data('merge-id');
			}
		});

		if (!mergeId || !otherMergeId) {
			return;
		}

		var footerEl = this.overlay.getWrapper().find('.overlay-footer').addClass('loading');

		$.ajax({
			url: this._getMergeUrl(mergeId, otherMergeId),
			type: 'POST',
			dataType: 'json',
			complete: function() {
				footerEl.removeClass('loading');
			},
			success: function(data) {
				if (data.success) {
					// remove old tabs, theyre outdated
					Array.each(DeskPRO_Window.getTabWatcher().findTabType(self.options.tabType), function(tab) {
						var id = tab.page.getMetaData(self.options.metaIdName);
						if (id == data.old_id || id == data.id) {
							DeskPRO_Window.TabBar.removeTabById(tab.id);
						}
					});

					DeskPRO_Window.runPageRoute(self.options.loadRoute.replace('{id}', data.id));
				}
				self.overlay.close();
			},
			error: function(xhr, textStatus, errorThrown) {
				self.overlay.close();

				var status = (xhr.status || '') + ' ' + (errorThrown || '') + ' ' + (xhr.statusText || '');
				DeskPRO_Window._showAjaxError('<div class="error-details">Here is the raw output returned from the server error:<textarea class="raw">' + status + "\n\n" + Orb.escapeHtml(xhr.responseText) + '</textarea></div>');
			}
		});
	},

	destroy: function() {
		if (this.overlay) {
			this.overlay.destroy();
		}
		if (this.mergeMenu) {
			this.mergeMenu.destroy();
		}
	}
});

Orb.createNamespace('DeskPRO.Agent.Widget');

DeskPRO.Agent.Widget.AgentChatWin_Registry = {};

DeskPRO.Agent.Widget.AgentChatWin_Find = function(chatId) {
	var found = null;

	Object.each(DeskPRO.Agent.Widget.AgentChatWin_Registry, function(chatWin) {
		if (chatWin && !found && chatWin.getConvoId() == chatId) {
			found = chatWin;
		}
	});

	return found;
};

DeskPRO.Agent.Widget.AgentChatWin_FindAgents = function(agent_ids) {
	var found = null;

	agent_ids = agent_ids.sort(function(a,b) {
		return parseInt(a) - parseInt(b);
	});

	agent_ids_str = agent_ids.join(',');

	Object.each(DeskPRO.Agent.Widget.AgentChatWin_Registry, function(chatWin) {
		if (chatWin && !found && chatWin.agentIdsStr == agent_ids_str) {
			found = chatWin;
		}
	});

	return found;
};

/**
 * An agent chat window handles send/rec of chat messages to a particular
 * "window" (conversation) between at least one agent.
 */
DeskPRO.Agent.Widget.AgentChatWin = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(options) {

		this.options = {
			convoId: 0,
			agentIds: [],
			title: null
		};

		this.setOptions(options);

		this.uuid = this.OBJ_ID;
		this.convoId = this.options.convoId;

		this.chatsWrapper = $('#agent_chats_wrapper');

		DeskPRO.Agent.Widget.AgentChatWin_Registry[this.uuid] = this;

		this.agentIds = [];
		Array.each(this.options.agentIds, function(i) {
			this.agentIds.push(parseInt(i));
		}, this);

		this.agentIds = this.agentIds.sort(function(a,b) {
			return a-b;
		});

		// Used with find
		this.agentIdsStr = this.agentIds.join(',');

		this.wrapper = null;

		this._initWindow();
	},

	/**
	 * Inits the actual chat window elements on the page
	 */
	_initWindow: function() {
		if (this._hasInitWin) return;
		this._hasInitWin = true;

		var self = this;

		if (this.convoId) {
			// Make sure it doesnt already exist
			var exist = $('#agent_chat_conversation_' + this.convoId);
			if (exist.length) {
				return;
			}
		}

		// One agent: we're sending a new one, we dont define ourselves
		// Two agents with us: incoming new message and got agentids from server, which includes us
		if (!this.options.title && (this.agentIds.length == 1 || (this.agentIds.length == 2 && this.agentIds.indexOf(parseInt(DESKPRO_PERSON_ID)) != -1) )) {

			if (this.agentIds[0] == DESKPRO_PERSON_ID) {
				var agentInfo = DeskPRO_Window.getAgentInfo(this.agentIds[1]);
			} else {
				var agentInfo = DeskPRO_Window.getAgentInfo(this.agentIds[0]);
			}

			if (!agentInfo) {
				return;
			}

			var newContainer = $.tmpl('agent_chat_conversation', {
				local_id: this.uuid,
				to_agent_name: agentInfo.name,
				to_agent_shortname: agentInfo.shortName,
				to_agent_id: agentInfo.id,
				to_agent_picture: agentInfo.pictureUrlSizable.replace(/_SIZE_/g, 15)
			});
		} else {
			var newContainer = $.tmpl('agent_groupchat_conversation', {
				local_id: this.uuid,
				title: this.options.title || 'Group'
			});
		}

		this.wrapper = newContainer;

		newContainer.find('> .window').find('> header, > div.messages-box, > .input-message-wrap').on('click', function(ev) {
			var count = 0;
			Object.each(DeskPRO.Agent.Widget.AgentChatWin_Registry, function(win) {
				if (win) {
					win.wrapper.css('z-index', count++);
				}
			});
			self.wrapper.css('z-index', count+1);
		});
		newContainer.find('> nav').on('click', function() {
			var count = 0;
			Object.each(DeskPRO.Agent.Widget.AgentChatWin_Registry, function(win) {
				if (win) {
					win.wrapper.css('z-index', count++);
				}
			});
			self.wrapper.css('z-index', count+1);
		});

		// Accept clicks on routes
		newContainer.on('click', '[data-route]', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			DeskPRO_Window.runPageRouteFromElement($(this));
		});

		this.chatsWrapper.append(newContainer);
		this.resetPosition();

		$('textarea', newContainer).on('keypress', (function(ev) {
			// Enter, but not when meta key (alt, ctrl etc) are pressed
			if (ev.keyCode == 13 && !ev.metaKey) {
				ev.preventDefault();//dont enter enter key
				this._fireSendMessage();
			}
		}).bind(this));

		var nav = $('> nav', newContainer);
		nav.on('click', function(ev) {
			if (newContainer.is('.open')) {
				newContainer.removeClass('open');
			} else {
				newContainer.addClass('open');
			}
		});

		$('.close-trigger', nav).on('click', function(ev) {
			ev.stopPropagation();
			self.fireEvent('close');
			self.destroy();
		});

		$('.minimize', newContainer).on('click', function(ev) {
			ev.stopPropagation();
			self.fireEvent('minimize');
			self.close();
		});
		$('.close', newContainer).on('click', function(ev) {
			ev.stopPropagation();
			self.fireEvent('close');
			self.destroy();
		});

		this.loadLastConvo();
	},

	loadLastConvo: function() {
		var data = [];
		Array.each(this.agentIds, function(id) {
			data.push({
				name: 'agent_ids[]',
				value: id
			});
		});

		$.ajax({
			url: BASE_URL + 'agent/agent-chat/get-last-convo',
			data: data,
			contentType: 'json',
			context: this,
			success: function(data) {
				if (data.conversation_id) {
					this.convoId = data.conversation_id;
				}

				if (data.messages) {
					Array.each(data.messages, function(messageInfo) {
						if (messageInfo.agent_id == DESKPRO_PERSON_ID) {
							this.showMyMessage(messageInfo.message);
						} else {
							this.showMessage(messageInfo.agent_id, messageInfo.message, messageInfo.time);
						}
					}, this);
				}
			}
		});
	},

	resetPosition: function() {
		if (!this.wrapper) {
			return;
		}
		var chats = $('> section.agent-chat', this.chatsWrapper);
		if (chats.length > 1) {
			var lastChat = chats.eq(-2);
			var leftPos = lastChat.position().left + $('> nav', lastChat).outerWidth() + 8;
			this.wrapper.css('left', leftPos);
		} else {
			this.wrapper.css('left', 0);
		}
	},

	_fireSendMessage: function() {
		var txt = $('textarea', this.wrapper);
		var msg = txt.val().trim();
		txt.val('');

		if (!msg.length) {
			return;
		}

		var messageBlock = this.showMyMessage(msg);
		this.sendMessage(msg, messageBlock);
	},


	/**
	 * Get the convo ID
	 *
	 * @return {Integer}
	 */
	getConvoId: function() {
		return this.convoId;
	},


	/**
	 * Get the local uuid we've given the chat. Useful for things like element IDs.
	 *
	 * @return {String}
	 */
	getConvoLocalId: function() {
		return this.uuid;
	},


	/**
	 * Send a new message
	 *
	 * @param {String} message
	 * @param messageBlock
	 */
	sendMessage: function(message, messageBlock) {
		var data = [];
		data.push({
			name: 'content',
			value: message
		});
		data.push({
			name: 'local_id',
			value: this.uuid
		});

		Array.each(this.agentIds, function(id) {
			data.push({
				name: 'agent_ids[]',
				value: id
			});
		});

		var messageLocalId = Orb.uuid();
		var info = {
			message: message,
			localMessageId: messageLocalId,
			convoId: this.convoId,
			convoLocalId: this.uuid
		};

		this.fireEvent('sendMessage', [this, info]);

		$.ajax({
			url: BASE_URL + 'agent/agent-chat/send-agent-message/' + this.convoId,
			data: data,
			contentType: 'json',
			context: this,
			success: function(data) {
				this.convoId = data.conversation_id;

				info.messageId = data.message_id;
				info.convoId = this.convoId;
				this.fireEvent('sendMessageDone', [this, info]);

				if (messageBlock) {
					messageBlock.find('time').text(data.time);
				}
			}
		});
	},


	/**
	 * Show a new incoming message from someone
	 *
	 * @param agent_id
	 * @param message
	 * @param time
	 */
	showMessage: function(agent_id, message, time) {

		var agentInfo = DeskPRO_Window.getAgentInfo(agent_id);

		if (!agentInfo) {
			return;
		}

		var newMessage = $.tmpl('agent_chat_message', {
			author_id: agent_id,
			author_name: agentInfo.name,
			author_picture: agentInfo.pictureUrlSizable.replace(/_SIZE_/g, 25),
			message: '',
			time: time || ''
		});

		newMessage.find('span.message-text').html(this.formatMessage(message));

		$('.messages-container', this.wrapper).append(newMessage);
		$('.messages-box').scrollTop(100000);
	},


	formatMessage: function(message) {
		var message = Orb.escapeHtml(message);
		var idMap = {
			't': {title: 'Ticket', url: BASE_URL + 'agent/tickets/'},
			'p': {title: 'Person', url: BASE_URL + 'agent/people/'},
			'o': {title: 'Organization', url: BASE_URL + 'agent/organizations/'},
			'a': {title: 'Article', url: BASE_URL + 'agent/kb/article/'},
			'n': {title: 'News', url: BASE_URL + 'agent/news/post/'},
			'd': {title: 'Download', url: BASE_URL + 'agent/downloads/file/'},
			'i': {title: 'Feedback', url: BASE_URL + 'agent/feedback/view/'}
		};
		Object.each(idMap, function(info, prefix) {
			var re = new RegExp('\{\{\s*' + prefix + '\-([0-9]+)\s*\}\}', 'g');
			message = message.replace(re, '<a data-route="page:'+info.url+'$1">'+info.title+' #$1</a>');
		});

		var re = new RegExp('\{\{\s*tw\-([0-9]+)\s*\}\}', 'g');
		message = message.replace(re, '<a data-route="poppage:' + BASE_URL + 'agent/twitter/status/tweet-overlay?account_status_id=$1">Tweet #$1</a>');

		message = message.replace(/(https?:\/\/[^\s]+)/gi, '<a href="$1" target="_blank">$1</a>');

		return message;
	},


	/**
	 * Add own messages to the window immediately
	 *
	 * @param message
	 */
	showMyMessage: function(message) {
		var newMessage = $.tmpl('agent_chat_message_me', { message: '', time: '...' });
		newMessage.find('span.message-text').html(this.formatMessage(message));

		$('.messages-container', this.wrapper).append(newMessage);
		$('.messages-box').scrollTop(100000);

		return newMessage;
	},


	/**
	 * Open the chat tab
	 */
	open: function() {
		if (this.wrapper) {
			this.wrapper.addClass('open');
			this.wrapper.find('textarea').focus();
		}
	},


	/**
	 * Close the chat tab
	 */
	close: function() {
		if (this.wrapper) {
			this.wrapper.removeClass('open');
		}
	},


	/**
	 * Remove the chat window
	 */
	destroy: function() {
		if (this.wrapper) {
			this.wrapper.remove();
			this.wrapper = null;
		}

		DeskPRO.Agent.Widget.AgentChatWin_Registry[this.uuid] = null;
		delete DeskPRO.Agent.Widget.AgentChatWin_Registry[this.uuid];
		this.fireEvent('destroy', [this]);
	}
});

Orb.createNamespace('DeskPRO.Agent.Widget');

DeskPRO.Agent.Widget.BackgroundPopout = new Orb.Class({

	Implements: [Orb.Util.Options, Orb.Util.Events],

	initialize: function(options) {
		this.options = {
			/**
			 * The URL that'll load the newticket page
			 */
			loadUrl: null,

			/**
			 * The route to load when clicking on move to tab
			 */
			tabRoute: null,

			/**
			 * The initial timeout before we load the newticket tpl
			 * in the background
			 */
			initialTimeout: 12000, // 12 seconds

			/**
			 * Periodically update the template to account for changes
			 * (usergroups/companies? new custom fields?)
			 */
			periodicalTimeout: 600000, // 10 minutes

			/**
			 * Auto-start the timeout timer to load in the bg
			 */
			autostart: true,

			/**
			 * Maximum amount of jitter, to prevent all popouts from triggering
			 * simultaneously.
			 */
			maxJitter: 2000
		};

		this.setOptions(options);

		/**
		 * The latest Page code
		 */
		this.template = null;

		/**
		 * Any active ajax request
		 */
		this.xhr = null; //active ajax

		/**
		 * The current timeout
		 */
		this.timeout = null;

		/**
		 * The currently created popover
		 */
		this.pop = null;

		/**
		 * True when the template has been reset
		 */
		this.doReset = false;

		/**
		 * Next time the window is opened, the request will be sent with these params.
		 *
		 * @type {null}
		 */
		this.nextParams = null;

		if (this.options.autostart) {
			this.startTimeout();
		}
	},


	/**
	 * Start the auto-update timer
	 */
	startTimeout: function() {
		var t;

		if (this.timeout) return;

		if (this.template) {
			t = this.options.periodicalTimeout;
		} else {
			t = this.options.initialTimeout;
		}

		t += Math.random()*this.options.maxJitter;

		this.timeout = window.setTimeout(this.loadTemplate.bind(this, null), t);
	},


	/**
	 * Reloads the template
	 */
	loadTemplate: function(callback) {

		DP.console.debug('[BackgroundPopout] Loading: %s', this.options.loadUrl);
		if (this.xhr) {
			return;
		}

		if (this.timeout) {
			window.clearTimeout(this.timeout);
			this.timeout = null;
		}

		this.xhr = $.ajax({
			url: this.options.loadUrl,
			data: this.nextParams || null,
			type: 'GET',
			dataType: 'html',
			context: this,
			errorDp: function() {
				this.template = null;
				this.destroyPop();
				this.close();
				this.startTimeout();
			},
			success: function(html) {
				this.nextParams = null;
				this.template = html;

				if (callback) {
					if (typeof callback != 'function') {
						DP.console.error("Not a valid callback: %o", callback);
						return;
					}
					callback(html);
				}
			},
			complete: function() {
				this.xhr = null;
				this.startTimeout();
			}
		});
	},


	/**
	 * Call when you know the template is stale.
	 */
	invalidateTemplate: function() {
		this.loadTemplate();
	},


	/**
	 * Get the template
	 *
	 * @return {String}
	 */
	getTemplate: function() {
		return this.template;
	},


	/**
	 * Opens the page in the popout
	 */
	open: function(callback) {

		var self = this;
		var withNextParams = false;

		if (this.nextParams) {
			withNextParams = true;
			this.clear();
		}

		if (this.options.tabRoute && !DeskPRO_Window.paneVis.list) {
			DeskPRO_Window.runPageRoute(this.options.tabRoute);
			return;
		}

		if (this.pop) {
			if (this.doReset) {
				this.pop.setHtml(this.getTemplate());
				this.doReset = false;
			}
			this.pop.open();
			if (callback && this.pop.page) {
				callback(this.pop.page);
			}
			return;
		}

		this.doReset = false;

		var self = this;
		var pop = new DeskPRO.Agent.PageHelper.Popover({
			tabRoute: this.options.tabRoute,
			onPageInit: function(pop, page) {
				page.addEvent('closeSelf', function(ev) {
					ev.cancel = true;
					self.clear();
				});

				if (callback) {
					callback(page);
				}
			}
		});

		var tpl = this.getTemplate();
		if (tpl) {
			pop.setHtml(tpl);
		} else {
			this.loadTemplate(function(html) {
				pop.setHtml(html);

				if (withNextParams) {
					self.template = null;
				}
			});
		}

		this.pop = pop;
		pop.open();
	},

	isOpen: function() {
		if (!this.pop) return false;
		if (this.pop.isOpen()) return true;
		return false;
	},

	toggle: function() {
		if (!this.pop) {
			this.open();
			return;
		}
		this.pop.toggle();
	},

	close: function() {
		if (!this.pop) return;
		this.pop.close();
	},

	/**
	 * Clears the currently loaded page (it'll be reloaded next time this is opened)
	 * Same as desotryPop except the template is also removed, so it means
	 * a new ajax request to fetch the page is needed.
	 */
	clear: function() {
		if (this.pop) {
			this.pop.destroy();
			this.pop = null;
		}
		this.template = null;
	},


	destroyPop: function() {
		if (!this.pop) return;
		this.pop.destroy();
		this.pop = null;
		this.template = null;
	}
});

Orb.createNamespace('DeskPRO.Agent.RuleBuilder');

/**
 * A limited verso
 */
DeskPRO.Agent.RuleBuilder.TermAbstract = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(options) {
		this.options = {
			ruleBuilder: null,
			rowEl: null,
			rowId: null,
			opMenu: null
		};

		if (options) this.setOptions(options);

		this.ruleBuilder = this.options.ruleBuilder;
		this.rowEl = $(this.options.rowEl);
		this.rowId = this.options.rowId;
		this.opMenu = this.options.opMenu;

		this.init();
	},

	init: function() { },

	initRow: function() { },

	initValues: function() { }
});
Orb.createNamespace('DeskPRO.Agent.RuleBuilder');

DeskPRO.Agent.RuleBuilder.DateTerm = new Orb.Class({
	Extends: DeskPRO.Agent.RuleBuilder.TermAbstract,

	initRow: function() {
		this._initUi();
	},

	initValues: function() {
		var timestamp = null, date = null;

		timestamp = this.date1Input.val();
		if (timestamp) {
			date = new Date(timestamp * 1000);
			this.date1Widget.datepicker('setDate', date);
		}

		timestamp = this.date2Input.val();
		if (timestamp) {
			date = new Date(timestamp * 1000);
			this.date2Widget.datepicker('setDate', date);
		}

		//------------------------------
		// Existing values
		//------------------------------

		if (parseInt($('.date1-relative-input', this.rowEl).val())) {
			$('.relative1-input', this.date1).val($('.date1-relative-input', this.rowEl).val());
			$('.relative1-type', this.date1).val($('.date1-relative-type', this.rowEl).val());

			$('.date', this.date1).hide();
			$('.relative', this.date1).show().addClass('on');
		}

		if (parseInt($('.date2-relative-input', this.rowEl).val())) {
			$('.relative2-input', this.date2).val($('.date2-relative-input', this.rowEl).val());
			$('.relative2-type', this.date2).val($('.date2-relative-type', this.rowEl).val());

			$('.date', this.date2).hide();
			$('.relative', this.date2).show().addClass('on');
		}

		this.updateStatus();
	},

	_initUi: function() {

		//------------------------------
		// References to elements and move
		// overlay into body
		//------------------------------

		this.opInput = $('select.op', this.rowEl);

		this.date1Input = $('input.date1-input', this.rowEl);
		this.date2Input = $('input.date2-input', this.rowEl);

		this.date1Display = $('input.date1-display', this.rowEl);
		this.date2Display = $('input.date2-display', this.rowEl);

		this.currentValue = $('.status-value', this.rowEl);
		this.currentValue.text('(click to set)');
		this.currentValue.on('click', this.show.bind(this));

		this.dateWrap = $('.date-wrap', this.rowEl);

		this.backdrop = $('<div class="backdrop" style="display: none"></div>');
		this.backdrop.appendTo('body');
		this.backdrop.on('click', this.hide.bind(this));

		this.wrapper = $('<div class="field-overlay" style="display:none"><div class="close-trigger"></div></div>');
		$('.close-trigger', this.wrapper).on('click', this.hide.bind(this));

		this.dateWrap.detach().appendTo(this.wrapper).css('display', 'block');
		this.wrapper.appendTo('body');

		this.date1 = $('.date1', this.dateWrap);
		this.date2 = $('.date2', this.dateWrap);

		//------------------------------
		// Init date elements
		//------------------------------

		var self = this;
		this.date1Widget = $('.widget', this.date1).datepicker({
			dateFormat: 'M d, yy',
			onSelect: function(dateText, inst) {

				self.date1Input.val(self.date1Widget.datepicker('getDate').getTime() / 1000);

				self.date1Display.val(dateText);
				self.updateStatus();
			}
		});

		this.date2Widget = $('.widget', this.date2).datepicker({
			dateFormat: 'M d, yy',
			onSelect: function(dateText, inst) {

				self.date2Input.val(self.date2Widget.datepicker('getDate').getTime() / 1000);

				self.date2Display.val(dateText);
				self.updateStatus();
			}
		});

		var getDate = function (el) {
			var timestamp = strtotime(el.val());
			if (!timestamp) {
				return null;
			}

			var date = new Date(timestamp * 1000);
			return date;
		};

		//------------------------------
		// Detect changes to text fields for
		// human times "1 day ago" etc
		//------------------------------

		this.date1Display.on('change', function() {
			var date = getDate($(this));
			if (!date) {
				$(this).val('');
				return;
			}
			self.date1Widget.datepicker('setDate', date);
		});

		this.date2Display.on('change', function() {
			var date = getDate($(this));
			if (!date) {
				$(this).val('');
				return;
			}
			self.date2Widget.datepicker('setDate', date);
		});

		//------------------------------
		// Switcher between relative input
		//------------------------------

		$('.switcher', this.date1).on('click', (function() {
			var date = $('.date', this.date1);
			var rel  = $('.relative', this.date1);

			if (date.is(':visible')) {
				date.hide();
				rel.show().addClass('on');
			} else {
				rel.hide().removeClass('on');
				date.show();
			}
		}).bind(this));

		$('.switcher', this.date2).on('click', (function() {
			var date = $('.date', this.date2);
			var rel  = $('.relative', this.date2);

			if (date.is(':visible')) {
				date.hide();
				rel.show().addClass('on');
			} else {
				rel.hide().removeClass('on');
				date.show();
			}
		}).bind(this));
	},

	show: function() {

		if (this.opInput.val() == 'between') {
			this.dateWrap.addClass('two');
		} else {
			this.dateWrap.removeClass('two');
		}

		this.wrapper.css({
			left: this.currentValue.offset().left,
			top: this.currentValue.offset().top
		});

		this.backdrop.show();
		this.wrapper.show();
	},

	updateStatus: function() {

		var str1 = '', str2 = '', status = '';

		// If we're using the relative times, update the values on close
		var relative1 = $('.relative1', this.date1);
		var relative2 = $('.relative2', this.date2);

		if ($('.relative', this.date1).hasClass('on')) {
			$('.date1-relative-input', this.rowEl).val($('.relative1-input', this.date1).val());
			$('.date1-relative-type', this.rowEl).val($('.relative1-type', this.date1).val());

			// Erase any calendar time we mightve set before
			this.date1Input.val('');

			if ($('.relative1-input', this.date1).val().trim().length) {
				str1 = $('.relative1-input', this.date1).val() + ' ' + $('.relative1-type', this.date1).val() + ' ago';
			}
		} else {
			var date1 = this.date1Widget.datepicker('getDate');
			if (date1) {
				str1 = $.datepicker.formatDate('M d, yy', date1);
			}
		}

		if ($('.relative', this.date2).hasClass('on')) {
			$('.date2-relative-input', this.rowEl).val($('.relative2-input', this.date2).val());
			$('.date2-relative-type', this.rowEl).val($('.relative2-type', this.date2).val());

			this.date2Input.val('');

			if ($('.relative2-input', this.date2).val().trim().length) {
				str2 = $('.relative2-input', this.date2).val() + ' ' + $('.relative2-type', this.date2).val() + ' ago';
			}
		} else {
			var date2 = this.date2Widget.datepicker('getDate');
			if (date2) {
				str2 = $.datepicker.formatDate('M d, yy', date2);
			}
		}

		if (!str1.length) str1 = '(click to set)';
		if (!str2.length) str1 = '(click to set)';

		if (this.opInput.val() == 'between') {
			status = str1 + ' and ' + str2;
		} else {
			status = str1;
		}

		this.currentValue.text(status);
	},

	hide: function() {
		this.updateStatus();
		this.backdrop.hide();
		this.wrapper.hide();
	},

	destroy: function() {
		this.wrapper.remove();
		this.backdrop.remove();
	}
});

Orb.createNamespace('DeskPRO.Agent.RuleBuilder');

DeskPRO.Agent.RuleBuilder.LabelsTerm = new Orb.Class({
	Extends: DeskPRO.Agent.RuleBuilder.TermAbstract,

	initRow: function() {
		var self = this;
		this.inner = $('.label-chooser-wrap', this.rowEl);
		this.labelType = this.rowEl.data('label-type');
		this.labelsList = $('input.labels-box', this.rowEl).first();

		this.labelsInput = new DeskPRO.UI.LabelsInput({
			type: 'tickets',
			input: this.labelsList,
			onChange: this.updateLabels.bind(this)
		});

		this.currentValue = $('.status-value', this.rowEl);
		this.currentValue.text('(click to set)');
		this.currentValue.on('click', this.show.bind(this));

		this.values = $('.label-values', this.rowEl);

		this.backdrop = $('<div class="backdrop" style="display: none"></div>');
		this.backdrop.appendTo('body');
		this.backdrop.on('click', this.hide.bind(this));

		this.wrapper = $('<div class="field-overlay labels-chooser" style="display:none"><div class="close-trigger"></div></div>');
		$('.close-trigger', this.wrapper).on('click', this.hide.bind(this));

		this.inner.detach().appendTo(this.wrapper).css('display', 'block');
		this.wrapper.appendTo('body');

		window.setTimeout(function() {
			var vals = self.currentValue.data('select-texts');
			if (vals) {
				Array.each(vals, function(val) {
					var input = $('<option value="" selected="selected" />');
					input.val(val);

					input.appendTo(self.values);
				});
			}
		}, 450);
	},

	updateLabels: function() {
		var labels = this.labelsInput.getLabels();
		var status = '(click to set)';

		if (labels.length) {
			status = labels.join(', ');
		}

		this.currentValue.text(status);

		this.values.empty();

		if (labels.length) {
			Array.each(labels, function(label) {
				var input = $('<option value="" selected="selected" />');
				input.val(label);

				input.appendTo(this.values);
			}, this);
		}
	},

	show: function() {
		var vals = [];
		this.values.find('option').each(function() {
			vals.push($(this).val());
		});
		this.labelsList.select2('val', vals);

		this.wrapper.css({
			left: this.currentValue.offset().left,
			top: this.currentValue.offset().top
		});

		this.wrapper.show();
		this.backdrop.show().css('z-index', parseInt(this.wrapper.css('z-index')) - 1);
	},

	hide: function() {
		this.backdrop.hide();
		this.wrapper.hide();
	},

	destroy: function() {
		this.wrapper.remove();
		this.backdrop.remove();
		this.labelsInput.destroy();
	}
});

Orb.createNamespace('DeskPRO.Agent.RuleBuilder');

DeskPRO.Agent.RuleBuilder.SelectNewOption = new Orb.Class({
	Extends: DeskPRO.Agent.RuleBuilder.TermAbstract,

	initRow: function() {
		var self = this;
		this.select = this.rowEl.find('select.template_name');
		this.select.on('change', function() {
			self.updateOption();
		});
		this.input = this.rowEl.find('input.new_option');

		setTimeout(function() {
			self.rowEl.find('select.template_name, select.agents').each(function() {
				DP.select($(this));
			});
		},10);

		this.updateOption();
	},

	updateOption: function() {
		var val = this.select.val();
		if (val == "NEW") {
			this.input.show();
		} else {
			this.input.hide();
		}
	},

	show: function() {
		this.wrapper.css({
			left: this.currentValue.offset().left,
			top: this.currentValue.offset().top
		});

		this.wrapper.show();
		this.backdrop.show().css('z-index', parseInt(this.wrapper.css('z-index')) - 1);
	},

	hide: function() {
		this.backdrop.hide();
		this.wrapper.hide();
	},

	destroy: function() {

	}
});

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

Orb.createNamespace('DeskPRO.Agent.Ticket.Property');

/**
 * Something that can be fetched or set on the ticket page. These are
 * managers that handle UI changes and getting and setting new values.
 */
DeskPRO.Agent.Ticket.Property.Abstract = new Class({

	Implements: [Events, Options],

	options: {},
	ticketPage: null,

	/**
	 * @param {DeskPRO.Agent.PageFragment.Page.Ticket} ticketPage
	 * @param {Object} options
	 */
	initialize: function(ticketPage, options) {

		if (options) this.setOptions(options);

		this.ticketPage = ticketPage;

		this.init();
	},

	init: function() {},

	/**
	 * Name for the property
	 *
	 * @return {String}
	 */
	getName: function() {

	},



	/**
	 * Used to compare a new value to the current value to see if
	 * we really need to change anything.
	 *
	 * @return {Boolean}
	 */
	isSameValue: function(compare) {
		if (this.getValue() == compare) {
			return true;
		}

		return false;
	},



	/**
	 * Gets the currently set value
	 *
	 * @return mixed
	 */
	getValue: function() {
		// override
	},



	/**
	 * Sets a new value. Must also update the UI if needed.
	 *
	 * @param mixed value
	 */
	setValue: function(value) {
		// override
	},



	/**
	 * Sets data that we got from the server. This is usually the same
	 * as setValue(), but it might be like a new reply or osmething like that.
	 */
	setIncomingValue: function(value) {
		this.setValue(value);
	},



	/**
	 * Get the UI element used to display the property.
	 */
	getInterfaceElement: function() {
		return this._getInterfaceElement();
	},

	_interfaceEl: null,
	_getInterfaceElement: function() {
		// override
	},


	/**
	 * When a property is updated automatically (not from a user action, like in the background),
	 * this pulse action is applied to highlight and fade slowly.
	 */
	pulseInterfaceElement: function() {
		this.getInterfaceElement().effect('highlight', 1200);
	},


	/**
	 * Highlight the UI element to bring attention to some change.
	 */
	highlightInterfaceElement: function() {
		var i = this.getInterfaceElement();

		if (!i || !i.length) {
			return;
		}

		this.getInterfaceElement().addClass('change-on');

		var displayItemWrap = i.parentsUntil(null, '.display-item');
		if (displayItemWrap.length) {
			displayItemWrap.addClass('highlight-change-on');
		} else {
			i.addClass('change-on');
		}
	},

	changePersisted: function() {

	},

	changeReverted: function() {

	},

	/**
	 * Remove the UI highlight
	 */
	unhighlightInterfaceElement: function() {
		this.getInterfaceElement().removeClass('change-on');
	},

	isDisplayOnly: function() {
		return false;
	},

	isAdditionOnly: function() {
		return false;
	}
});
Orb.createNamespace('DeskPRO.Agent.Ticket.Property');

DeskPRO.Agent.Ticket.Property.Agent = new Class({
	Extends: DeskPRO.Agent.Ticket.Property.Abstract,

	optionName: 'agent_id',

	init: function() {

	},

	getName: function() {
		return this.optionName;
	},

	getValue: function() {
		return this.getFormEl().val();
	},

	setValue: function(value) {

		// They are the same value,
		// dont try and trigger changes
		if (parseInt(value) == parseInt(this.ticketPage.getEl('value_form').find('.agent_id').val())) {
			return;
		}

		this.getFormEl().select2('val', value);
		this.ticketPage.getEl('value_form').find('.agent_id').val(value);
		this.getInterfaceElement().addClass('eat-change').val(value).change();
	},

	getInterfaceElement: function() {
		return this.ticketPage.getEl('agent_sel');
	},

	_formEl: null,
	getFormEl: function() {
		if (this._formEl !== null) return this._formEl;

		this._formEl = $('input.agent_id:first', this.ticketPage.valueForm);

		return this._formEl;
	}
});

Orb.createNamespace('DeskPRO.Agent.Ticket.Property');

DeskPRO.Agent.Ticket.Property.Department = new Class({
	Extends: DeskPRO.Agent.Ticket.Property.Abstract,

	optionName: 'department_id',

	init: function() {
		this._formEl = null;
	},

	getName: function() {
		return 'department_id';
	},

	getValue: function() {
		return this.getFormEl().val();
	},

	setValue: function(value) {

		// They are the same value,
		// dont try and trigger changes
		if (parseInt(value) == parseInt(this.ticketPage.getEl('value_form').find('.department_id').val())) {
			return;
		}

		this.getFormEl().select2('val', value);

		if (value == "0") value = 0;

		var el = this.getInterfaceElement();

		var name = this.ticketPage.getEl('department_id').find('option:selected').data('full-title');
		this.getInterfaceElement().text(name);
		this.ticketPage.getEl('value_form').find('.department_id').val(value);
	},

	getInterfaceElement: function() {
		return this.ticketPage.getEl('department_txt');
	},


	getFormEl: function() {
		if (this._formEl !== null) return this._formEl;

		this._formEl = this.ticketPage.getEl('department_id');

		return this._formEl;
	}
});

Orb.createNamespace('DeskPRO.Agent.Ticket.Property');

DeskPRO.Agent.Ticket.Property.AgentTeam = new Class({
	Extends: DeskPRO.Agent.Ticket.Property.Abstract,

	optionName: 'agent_team_id',

	init: function() {

	},

	getName: function() {
		return this.optionName;
	},

	getValue: function() {
		return this.getFormEl().val();
	},

	setValue: function(value) {

		// They are the same value,
		// dont try and trigger changes
		if (parseInt(value) == parseInt(this.ticketPage.getEl('value_form').find('.agent_team_id').val())) {
			return;
		}

		this.getFormEl().select2('val', value);
		this.ticketPage.getEl('value_form').find('.agent_team_id').val(value);
		this.getInterfaceElement().addClass('eat-change').val(value).change();
	},

	getInterfaceElement: function() {
		return this.ticketPage.getEl('agent_team_sel');
	},

	_formEl: null,
	getFormEl: function() {
		if (this._formEl !== null) return this._formEl;

		this._formEl = $('input.agent_team_id:first', this.ticketPage.valueForm);

		return this._formEl;
	}
});

Orb.createNamespace('DeskPRO.Agent.Ticket.Property');

/**
 * These standard options are simple values taken from a menu. Since
 * they're so similar, this single property class can handle all of them.
 */
DeskPRO.Agent.Ticket.Property.StandardOption = new Class({
	Extends: DeskPRO.Agent.Ticket.Property.Abstract,

	optionName: null,
	displayNameType: 'standardOption',

	init: function() {
		var valid_options = ['language_id', 'category_id', 'product_id', 'priority_id', 'workflow_id'];

		if (valid_options.indexOf(this.options.optionName) == -1) {
			throw 'invalidOptionName:'+this.options.optionName;
		}

		this.optionName = this.options.optionName;

		switch (this.optionName) {
			case 'language_id': this.displayNameType = 'language'; this.displayCaption = 'Language'; break;
			case 'category_id': this.displayNameType = 'ticket_category_full'; this.displayCaption = 'Category'; break;
			case 'product_id': this.displayNameType = 'product'; this.displayCaption = 'Product'; break;
			case 'priority_id': this.displayNameType = 'ticket_priority'; this.displayCaption = 'Priority'; break;
			case 'workflow_id': this.displayNameType = 'ticket_workflow'; this.displayCaption = 'Workflow'; break;
		}
	},


	getName: function() {
		return this.optionName;
	},

	getValue: function() {
		return this.getFormEl().val();
	},

	setValue: function(value) {
		this.getFormEl().val(value);

		if (value == "0") value = 0;

		// They are the same value,
		// dont try and trigger changes
		if (parseInt(value) == parseInt(this.ticketPage.getEl('value_form').find('.' + this.optionName).val())) {
			return;
		}

		// some elements (agent) have pictures associated with them
		var el = this.getInterfaceElement();
		var pictureEl = null;
		if (el.data('picture-element')) {
			pictureEl = $(el.data('picture-element'), el.parent());
			if (!pictureEl.length) {
				pictureEl = $(el.data('picture-element'), el.parent.parent());
				if (!pictureEl.length) {
					pictureEl = null;
				}
			}
		}

		if (value) {
			var displayName = value;
			if (this.displayNameType) {
				displayName = DeskPRO_Window.getDisplayName(this.displayNameType, value);
				if (!displayName) displayName = value;
			}

			this.getInterfaceElement().removeClass('no-value').html(displayName);

			if (pictureEl) {
				pictureEl.removeClass('no-value').show().attr('src', pictureEl.data('picture-url').replace('{value}', value));
			}

		} else {
			this.getInterfaceElement().addClass('no-value').html(this.getInterfaceElement().data('no-value-label') || 'None');

			if (pictureEl) {
				pictureEl.addClass('no-value').hide();
			}
		}

		var fieldEl = $('.prop-input-' + this.optionName, this.ticketPage.wrapper);

		if (fieldEl.hasClass('with-select2')) {
			fieldEl.select2('val', value);
		} else {
			fieldEl.val(value);
		}

		this.ticketPage.getEl('value_form').find('.' + this.optionName).val(value).trigger('change');
	},

	getInterfaceElement: function() {
		return $('.prop-val.'+this.optionName, this.ticketPage.contentWrapper).first();
	},

	_formEl: null,
	getFormEl: function() {
		if (this._formEl !== null) return this._formEl;

		this._formEl = $('[name="ticket['+this.optionName+']"]', this.ticketPage.wrapper).first();

		return this._formEl;
	}
});

Orb.createNamespace('DeskPRO.Agent.Ticket.Property');

DeskPRO.Agent.Ticket.Property.Status = new Class({
	Extends: DeskPRO.Agent.Ticket.Property.Abstract,

	setValue: function(value) {

		var hidden_status = false;
		var status_classname = value;

		if (value && value.constructor.toString().indexOf("Array") != -1) {
			hidden_status = value[1].value;
			value = value[0].value;
			status_classname = value;
		} else {
			if (value.indexOf('.') != -1) {
				var parts = value.split('.');

				var value = parts[0];
				var hidden_status = parts[1];
				status_classname = value + '_' + hidden_status;

				this.ticketPage.fireEvent('ticketHidden', [hidden_status]);
			}
		}

		this.ticketPage.wrapper.find('div.layout-content').removeClass('awaiting_agent awaiting_user resolved closed hidden_deleted hidden_spam hidden_validating hidden_temp').addClass(status_classname);

		$('input.status:first', this.ticketPage.valueForm).val(value);
		$('input.hidden_status:first', this.ticketPage.valueForm).val(hidden_status);

		// Hold is automatically taken off on PHP side when not awaiting user,
		// so need to reshow the 'set hold' button now incase user toggles status back to awaiting agent
		if (value == 'awaiting_agent') {
			this.ticketPage.getEl('hold_container').css('display', 'inline');
		} else {
			this.ticketPage.getEl('hold_container').css('display', 'none');
			this.ticketPage.getEl('hold_container').find('.hold').show();
			this.ticketPage.getEl('hold_container').find('.unhold').hide();
		}

		this.ticketPage.getEl('status_code').select2('val', value);

		var scode = value;
		if (hidden_status) {
			value += '_' + hidden_status;
		}
		this.ticketPage.getEl('status_code').val(value);

		var txt = this.ticketPage.getEl('status_code').find('option:selected').text().trim();
		this.getInterfaceElement().text(txt);
	},

	getValue: function() {
		var data = [];
		data.push({
			full_name: 'actions[status]',
			value: $('input.status:first', this.ticketPage.valueForm).val()
		});

		data.push({
			full_name: 'actions[hidden_status]',
			value: $('input.hidden_status:first', this.ticketPage.valueForm).val()
		});

		return data;
	},

	getInterfaceElement: function() {
		return this.ticketPage.getEl('status_txt');
	},

	getName: function() {
		return 'status';
	}
});

Orb.createNamespace('DeskPRO.Agent.Ticket.Property');

/**
 * New reply area
 */
DeskPRO.Agent.Ticket.Property.Reply = new Class({
	Extends: DeskPRO.Agent.Ticket.Property.Abstract,

	optionName: null,
	menuRepository: null,

	getName: function() {
		return 'reply';
	},

	getValue: function() {
		this.ticketPage.getEl('replybox_wrap').find('textarea[name="message"]').val();
	},

	highlightInterfaceElement: function() {
		this.ticketPage.getEl('replybox_wrap').find('textarea[name="message"]').addClass('highlight-change-on');
	},

	unhighlightInterfaceElement: function() {
		this.ticketPage.getEl('replybox_wrap').find('textarea[name="message"]').removeClass('highlight-change-on');
	},

	setValue: function(value) {
		if (value.reply_text) {
			value = value.reply_text;
		}

		return this.ticketPage.getEl('replybox_wrap').find('textarea[name="message"]').val(value);
	},

	setIncomingValue: function(value) {

	},

	getInterfaceElement: function() {
		return this.ticketPage.getEl('replybox_wrap').find('textarea[name="message"]');
	}
});

Orb.createNamespace('DeskPRO.Agent.Ticket.Property');

DeskPRO.Agent.Ticket.Property.TicketField = new Class({
	Extends: DeskPRO.Agent.Ticket.Property.Abstract,

	optionName: '',

	init: function() {
		this.optionName = 'ticket_field.' + this.options.fieldId;
	},

	getName: function() {
		return this.optionName;
	},

	getValue: function() {
		return this.getInterfaceElement().html();
	},

	setValue: function(value) {
		this.getInterfaceElement().html(value);
	},

	getInterfaceElement: function() {
		return $('.show-fields .custom-field-' + this.options.fieldId + ' .field-input', this.ticketPage.contentWrapper);
	},

	isDisplayOnly: function() {
		return true;
	}
});

Orb.createNamespace('DeskPRO.Agent.Ticket.Property');

DeskPRO.Agent.Ticket.Property.Urgency = new Class({
	Extends: DeskPRO.Agent.Ticket.Property.Abstract,

	setValue: function(value) {
		this.ticketPage.getEl('urgency').select2('val', value);

		var layoutDiv = this.ticketPage.wrapper.find('div.layout-content');

		if (!this.getInterfaceElement()[0]) {
			return;
		}

		this.getInterfaceElement().text(value || 1);
		this.getInterfaceElement().get(0).className = this.getInterfaceElement().get(0).className.replace(/urgency\-value\-\d+/g, '');
		layoutDiv.get(0).className = layoutDiv.get(0).className.replace(/urgency\-\d+/g, '');
		this.getInterfaceElement().addClass('urgency-value-' + value);
		layoutDiv.addClass('urgency-' + value);
	},

	getValue: function() {
		return this.ticketPage.getEl('urgency').val();
	},

	getInterfaceElement: function() {
		return this.ticketPage.getEl('urgency_txt');
	},

	getName: function() {
		return 'urgency';
	}
});

Orb.createNamespace('DeskPRO.Agent.Ticket.Property');

DeskPRO.Agent.Ticket.Property.Flag = new Class({
	Extends: DeskPRO.Agent.Ticket.Property.Abstract,

	optionName: 'flag',

	init: function() {

	},

	getName: function() {
		return this.optionName;
	},

	getValue: function() {
		return this.ticketPage.getEl('flag').val();
	},

	getInterfaceElement: function() {
		return this.ticketPage.getEl('flag');
	},

	setValue: function(value) {
		var old_flag = this.ticketPage.getEl('flag_old').val();
		this.ticketPage.getEl('flag_old').val(value);
		this.ticketPage.getEl('flag').val(value);

		if (old_flag == value) {
			return;
		}

		if (old_flag && old_flag != "") {
			DeskPRO_Window.util.modCountEl($('#ticket_flag_'+old_flag+'_count'), '-', 1);
		}

		var winCountEl = $('#ticket_flag_'+value+'_count');
		DeskPRO_Window.util.modCountEl(winCountEl, '+', 1);

		var label = winCountEl.closest('li').find('.flag-label').text().trim();
		this.ticketPage.getEl('flagicon').get(0).className = this.ticketPage.getEl('flagicon').get(0).className.replace(/flag\-color\-\w+/g, '');
		if (value) {
			this.ticketPage.getEl('flagicon').addClass('flag-color-' + value);
			this.ticketPage.getEl('flagtext').text(label);
		} else {
			this.ticketPage.getEl('flagtext').text('');
		}
	}
});

Orb.createNamespace('DeskPRO.Agent.Ticket.Property');

DeskPRO.Agent.Ticket.Property.Labels = new Class({
	Extends: DeskPRO.Agent.Ticket.Property.Abstract,

	optionName: 'labels',
	mode: 'add',

	init: function() {
		this.mode = this.options.mode;
	},

	getName: function() {
		return this.mode + '_' + this.optionName; // add_labels or remove_labels
	},

	getValue: function() {
		return this._values;
	},

	_values: null,

	setValue: function(values) {
		this._values = values;
		if (this.mode == 'add') {
			Array.each(values, function(val) {
				this.ticketPage.labelsInput.tagit.add(val, '<span class="new">'+val+'</span>');
			}, this);
		} else {
			Array.each(values, function(val) {
				var el = $('input[value="'+val+'"]', this.getInterfaceElement());
				if (el.length) {
					el = el.parent(); // input -> li
					li.hide();
				}
			}, this);
		}
	},

	changePersisted: function() {
		if (!this._values) return;

		if (this.mode == 'add') {
			$('li span.new', this.getInterfaceElement()).removeClass('new');
		} else {
			$('li.pending-remove', this.getInterfaceElement()).remove();
		}

		this._values = null;
	},

	changeReverted: function() {
		if (!this._values) return;

		if (this.mode == 'add') {
			$('li:has(span.new)', this.getInterfaceElement()).remove();
		} else {
			Array.each(values, function(val) {
				var el = $('input[value="'+val+'"]', this.getInterfaceElement());
				if (el.length) {
					el = el.parent(); // input -> li
					li.show().addClass('pending-remove');
				}
			}, this);
		}

		this._values = null;
	},

	getInterfaceElement: function() {
		return $('ul.tagit', this.ticketPage.contentWrapper);
	}
});

Orb.createNamespace('DeskPRO.Agent.Ticket.Property');

DeskPRO.Agent.Ticket.Property.Hold = new Class({
	Extends: DeskPRO.Agent.Ticket.Property.Abstract,

	optionName: 'is_hold',

	init: function() {

	},

	getName: function() {
		return this.optionName;
	},

	getValue: function() {
		return this.getFormEl().val();
	},

	setValue: function(value) {
		if (typeof value === 'boolean') {
			value = value ? 1 : 0;
		} else {
			value = parseInt(value);
		}

		this.getFormEl().val(value);

		if (value) {
			this.ticketPage.getEl('hold_message').show();
			this.ticketPage.getEl('menu_set_hold').hide();
			this.ticketPage.getEl('menu_unset_hold').show();
		} else {
			this.ticketPage.getEl('hold_message').hide();
			this.ticketPage.getEl('menu_set_hold').show();
			this.ticketPage.getEl('menu_unset_hold').hide();
		}
	},

	_formEl: null,
	getFormEl: function() {
		if (this._formEl !== null) return this._formEl;

		this._formEl = $('input.is_hold:first', this.ticketPage.valueForm);

		return this._formEl;
	}
});

Orb.createNamespace('DeskPRO.Agent.TicketList');

DeskPRO.Agent.TicketList.MassActions = new Orb.Class({
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
			resetOnClose: true
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

		this.wrapperEl = this.options.templateElement;
		if (!this.wrapperEl || !this.wrapperEl[0]) {
			this.wrapperEl = page.wrapper.find('.mass-actions-overlay-tpl');
			if (!this.wrapperEl[0]) {
				this.wrapperEl = null;
			}
		}
		if (!this.wrapperEl) {
			$('div.mass-actions-overlay-container', page.wrapper);
		}

		if(!this.wrapperEl.length) {
			return;
		}

		this._resetWrapper();

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

	updateUi: function() {
		if (this.scrollerHandler) {
			this.scrollerHandler.updateSize();
		}
	},

	_resetWrapper: function() {
		if (this.wrapper) {
			this.wrapper.remove();
		}
		if (this.wrapperContainer) {
			this.wrapperContainer.remove();
		}

		if (!this.wrapperEl.is('script')) {
			this.wrapper = $('<div/>').addClass('mass-actions-overlay-container mass-actions').data('base-id', this.wrapperEl.data('base-id')).data('upload-url', this.wrapperEl.data('upload-url'));
			var wrapperHtml = this.wrapperEl.html();
			this.wrapper.html(wrapperHtml);
		} else {
			var wrapperHtml = DeskPRO_Window.util.getPlainTpl(this.wrapperEl);
			this.wrapper = $(wrapperHtml);
			this.wrapper.detach().appendTo('body');
		}

		this.wrapper.find('.with-scroll-handler, .scroll-setup, .scroll-draw').removeClass('with-scroll-handler scroll-setup scroll-draw');

		this.countEl = $('.selected-tickets-count', this.wrapper);
		DP.select($('select.macro', this.wrapper));

		DeskPRO_Window.initInterfaceLayerEvents(this.wrapper);
		var scrollEl = $('.with-scrollbar', this.wrapper).first();
		if (scrollEl.length) {
			this.scrollerHandler = new DeskPRO.Agent.ScrollerHandler(null, scrollEl, {
				showEvent: 'show',
				hideEvent: 'hide'
			});
		}
	},

	/**
	 * Resets the wrapper back to the original, and then runs all of the init again.
	 */
	reset: function() {
		var wasopen = this.isOpen();
		this.close();

		this._resetWrapper();

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
		this.wrapper.css('z-index', '21001');

		this.baseId = this.wrapper.data('base-id');

		// These events registered first because hasAnyChange flag must be set before updatePreview()
		// is called
		$('select, :radio, :checkbox', this.wrapper).on('change', function() { if (!$(this).hasClass('macro')) self.hasAnyChange = true; });
		$('input, textarea', this.wrapper).on('change keypress', function() { self.hasAnyChange = true; });

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

		//------------------------------
		// Convert radios
		//------------------------------

		var tpl = DeskPRO_Window.util.getPlainTpl($('.radio-tpl', this.wrapper));

		var groupedRadios = {};
		$(':radio.button-toggle', this.wrapper).each(function() {
			var name = $(this).attr('name');
			if (!groupedRadios[name]) {
				groupedRadios[name] = [];
			}

			groupedRadios[name].push(this);
		});

		Object.each(groupedRadios, function(els) {
			var newEls = [];
			els = $(els);

			var clickFn = function() {
				var boundId = $(this).data('bound-id');
				var radio = $('#' + boundId);

				// Toggle off already checked (ie none selected now)
				if (radio.is(':checked')) {
					radio.attr('checked', false);
					newEls.removeClass('radio-on');

				// Normal radio behavior
				} else {
					radio.attr('checked', true);
					newEls.removeClass('radio-on');
					$(this).addClass('radio-on');
				}

				self.updatePreview(null, true);
			};

			els.each(function() {

				var wrapper = $(this).parent();
				var title = $('.radio-title', wrapper).text().trim();

				var newEl = $(tpl)
				newEl.addClass($(this).data('attach-class'));
				$('.radio-title', newEl).text(title);

				if (!$(this).attr('id')) {
					$(this).attr('id', Orb.getUniqueId());
				}

				newEl.data('bound-id', $(this).attr('id'));

				newEl.on('click', clickFn);

				wrapper.hide();
				newEl.insertAfter(wrapper);

				newEls.push(newEl.get(0));
			});

			newEls = $(newEls);
		});

		//------------------------------
		// Attach change listeners
		//------------------------------

		$('input, select, textarea', this.wrapper).on('change', (function() {
			this.updatePreview();
		}).bind(this));

		this.selectionBar.addEvent('checkChange', function(el, is_checked, count) {
			if (!this.isOpen()) return;
			this.updateCount(count);
			this.handleCheckChange(el, is_checked);
		}, this);
		this.selectionBar.addEvent('checkAll', function(count) {
			if (!this.isOpen()) return;
			this.updateCount(count);
			this.updatePreview();
		}, this);
		this.selectionBar.addEvent('checkNone', function() {
			if (!this.isOpen()) return;
			this.updateCount(0);
			this.clearPreview();
		}, this);

		$('select.macro', this.wrapper).on('change', function() {
			self.loadMacro($(this).val());
			self.updatePreview(null, true);
		});

		$('.apply-actions', this.wrapper).on('click', (function(ev) {
			this.apply();
		}).bind(this));

		//------------------------------
		// Reply Box
		//------------------------------

		var textarea = this.getElById('replybox_txt'), isWysiwyg = false;
		this.textarea = textarea;

		if (DeskPRO_Window.canUseAgentReplyRte()) {
			isWysiwyg = true;

			DeskPRO_Window.initRteAgentReply(textarea, {
				defaultIsHtml: true,
				inlineHiddenPosition: this.getElById('is_html_reply'),
				minHeight: 120,
				callback: function(obj) {
					obj.addBtnFirst('dp_attach', 'Click here to attach a file. You may also drag a file from your computer desktop into this reply area to upload attachments faster.', function(){});
					obj.addBtnAfter('dp_attach', 'dp_snippets', 'Open snippets', function(){});
					obj.addBtnSeparatorAfter('dp_attach');

					snippetBtn = obj.$toolbar.find('.redactor_btn_dp_snippets').closest('li');
					snippetBtn.addClass('snippets').find('a').html('<span class="show-key-shortcut">S</span>nippets');
					snippetBtn.on('click', function(ev) {
						Orb.cancelEvent(ev);
						self.snippetsViewer.open();
					});

					var attachBtn = obj.$toolbar.find('.redactor_btn_dp_attach').closest('li');
					attachBtn.addClass('attach');
					attachBtn.find('a').text('Attach').append('<input type="file" class="file" name="file-upload" />');

					obj.addBtnSeparatorAfter('dp_snippets');
				}
			});
			this.getElById('is_html_reply').val(1);
		}

		//------------------------------
		// Snippets Viewer
		//------------------------------

		this.snippetsViewer = new DeskPRO.Agent.Widget.SnippetViewer({
			driver: DeskPRO_Window.ticketSnippetDriver,
			onBeforeOpen: function() {
				if (isWysiwyg && textarea.data('redactor')) {
					try {
						textarea.data('redactor').saveSelection();
					} catch (e) {}
				}
			},
			onSnippetClick: function(info) {

				var snippetId    = info.snippetId;
				var snippetCode  = info.snippetCode;

				var agentText;
				var defaultText;
				var useText;
				var result;

				Array.each(snippetCode, function(info) {
					if (info.value) {
						if (info.language_id == DESKPRO_PERSON_LANG_ID) {
							agentText = info.value;
						}
						if (info.language_id == DESKPRO_DEFAULT_LANG_ID) {
							defaultText = info.value;
						}
						useText = info.value;
					}
				});

				if (agentText) {
					useText = agentText;
				} else if (defaultText) {
					useText = defaultText;
				}

				result = useText;

				if (isWysiwyg && textarea.data('redactor')) {
					try {
						textarea.data('redactor').restoreSelection();
						textarea.data('redactor').setBuffer();
					} catch (e) {}

					var html = result;
					html = html.replace(/<\/p>\s*<p>/g, '<br/>');
					html = html.replace(/^<p>/, '');
					html = html.replace(/<\/p>$/, '');
					textarea.data('redactor').insertHtml(html);
				} else {
					self.page.insertTextInReply(result);
				}

				self.snippetsViewer.close();
			}
		});

		/*
		this.snippetsViewer = new DeskPRO.Agent.Widget.SnippetViewer({
			sidePosition: 'top',
			triggerElement: this.getElById('text_snippets_btn'),
			onSnippetClick: this._onSnippetClick.bind(this)
		});
		*/

		//------------------------------
		// Upload handling
		//------------------------------

		DeskPRO_Window.util.fileupload(this.wrapper, {
			page: this.page,
			url: this.wrapper.data('upload-url'),
			uploadTemplate: $('.template-upload', this.replyBox),
			downloadTemplate: $('.template-download', this.replyBox)
		});

		var sels = this.wrapper.find('select.dpe_select');

		window.setTimeout(function() {
			sels.each(function() {
				DP.select($(this));
			});
		}, 150);

		this.wrapper.bind('fileuploaddone', function() {
			self.getElById('attach_row').fadeIn();
			self.wrapper.find('[name="attach\\[\\]"]').each(function() {
				$(this).name('actions[reply][attach_ids][]');
			});
		});
		this.wrapper.bind('fileuploadstart', function() {
			self.getElById('attach_row').fadeIn();
			self.updatePositions();
		});

		this.wrapper.on('click', '.remove-attach-trigger', function() {

			var row = $(this).closest('li');
			row.remove();

			var rows = $('ul.files li', self.getElById('attach_row'));
			if (!rows.length) {
				self.getElById('attach_row').hide().addClass('is-hidden');
			}

			self.updatePositions();
		});

		var noneRow = $('li.no-changes', this.wrapper);
		var agentRow = $('li.assign-agent', this.wrapper);
		var teamRow = $('li.assign-team', this.wrapper);
		var followersRow = $('li.add-followers', this.wrapper);

		if (this.assignOptionBox) {
			this.assignOptionBox.destroy();
		}

		var add = $('.other-properties-wrapper', this.wrapper);

		// Remove all the stuff we have layed out in a different way
		// on this popup
		$('div.type', add).each(function() {
			var type = $(this).data('rule-type');
			if (!type) return;

			if (type == 'add_labels' || type == 'remove_labels' || type.indexOf('ticket_field[') !== -1 || type.indexOf('people_field[') !== -1) {

			} else {
				$(this).remove();
			}
			self.updatePositions();
		});

		this.actionsEditor = new DeskPRO.Form.RuleBuilder($('.actions-builder-tpl', add));

		var actList = $('.other-properties-wrapper', this.wrapper);
		$('.add-term-row', add).show().on('click', function() {
			var x = Orb.getUniqueId();
			var basename = 'actions_set['+x+']';
			self.actionsEditor.addNewRow($('.search-terms', actList), basename);
			self.updatePositions();
		});
	},

	updateAssignmentsDisplay: function() {

	},

	getElById: function(id) {
		return $('#' + this.baseId + '_' + id);
	},

	_onSnippetClick: function(info) {
		var txt = this.getElById('replybox_txt');
		var val = txt.val();
		if (val.length) {
			val += " ";
		}
		val += info.snippet;

		txt.val(val);
	},

	updateCount: function(num) {
		if (num === undefined || num === null) {
			num = this.selectionBar.getCount();
		}
		this.countEl.text(num);
	},

	getActionFormValues: function(appendArray, isApply, info) {
		appendArray = appendArray || [];

		if (!info) info = {};
		info.actionsCount = 0;

		if (this.wrapper.find('select.macro_id')[0] && this.wrapper.find('select.macro_id').val() != '0') {
			appendArray.push({
				name: 'run_macro_id',
				value: this.wrapper.find('select.macro_id').val()
			});
			info.actionsCount = 1;
			return appendArray;
		}

		$('input, select, textarea', this.wrapper).filter('[name^="actions["], [name^="actions_set["]').each(function() {

			var val = $(this).val(), name = $(this).attr('name');

			if (!val) {
				val = '';
			}

			if (val == '-1') {
				val = '';
			}

			if ($(this).is(':radio, :checkbox')) {
				if (!$(this).is(':checked')) {
					return;
				}
			}

			if (val === '') {
				return;
			}

			// Dont send reply type when we're just fetching previews
			if (!isApply && name == 'actions[reply]') {
				return;
			}

			appendArray.push({
				name: name,
				value: val
			});

			info.actionsCount++;
		});

		return appendArray;
	},

	/**
	 * Apply the changes
	 */
	apply: function() {
		var formData, rows = [];

		var formDataInfo = {
			checkedCount: 0,
			actionsCount: 0
		};

		formData = this.selectionBar.getCheckedFormValues('result_ids[]', null, formDataInfo);

		this.selectionBar.getChecked().each(function() {
			rows.push($(this).closest('.row-item').get(0));
		});

		this.getActionFormValues(formData, true, formDataInfo);

		// If we dont have any tickets or actions then theres nothing to do
		if (!formDataInfo.checkedCount || !formDataInfo.actionsCount) {
			return;
		}

		rows = $(rows);
		rows.addClass('loading');

		this.wrapper.addClass('loading');

		var statusUpdate = this.wrapper.find('input[name="actions[status]"]:checked').val();

		DeskPRO_Window.util.ajaxWithClientMessages({
			url: BASE_URL + 'agent/ticket-search/ajax-save-actions',
			type: 'POST',
			data: formData,
			dataType: 'json',
			context: this,
			complete: function() {
				rows.removeClass('loading');
			},
			success: function(data) {

				if (this.options.isListView) {
					this.close();
					this.page.meta.pageReloader();
					return;
				}

				$('.preview-edit', this.listWrapper).removeClass('preview-edit');
				$('.preview-edit-hide', this.listWrapper).remove();
				$('.row-item.changed, .prop-val.changed', this.listWrapper).removeClass('changed');

				this.wrapper.removeClass('loading');

				this.close();

				this.fireEvent('postApply', [this, data, formDataInfo]);

				if (data && data.failed_tickets && data.failed_tickets.length) {
					DeskPRO_Window.showAlert('Note: ' + data.failed_tickets.length + ' tickets were not updated because you do not have permission to make the requested changed.');
				}

				if (statusUpdate === 'hidden.deleted' || statusUpdate === 'hidden.spam') {
					// hide any open tickets
					$.each(data.success_tickets, function(k, ticketId) {
						var tab = DeskPRO_Window.getTabWatcher().findTab('ticket', function(tab) {
							return (tab && tab.page && tab.page && tab.page.meta.ticket_id == ticketId);
						});
						if (tab) {
							DeskPRO_Window.removePage(tab.page);
						}
					});
				}
			}
		});

	},


	/**
	 * Clear all pending previews
	 */
	clearPreview: function() {
		$('.preview-edit', this.listWrapper).remove();
		$('.preview-edit-hide', this.listWrapper).show().removeClass('preview-edit-hide');
	},


	/**
	 * Updates the listing with a preview of the changes we're making
	 */
	updatePreview: function(specific_id, force) {

		// No changes detected
		if (!this.hasAnyChange && !force) {
			return;
		}

		// No previews on list view
		if (this.options.isListView) {
			return;
		}

		if (this.runningAjax) {
			this.runningAjax.abort();
			this.runningAjax = null;
		}

		var formData, rows = [];
		var formDataInfo = {
			checkedCount: 0,
			actionsCount: 0
		};

		if (!specific_id) {
			formData = this.selectionBar.getCheckedFormValues('result_ids[]', null, formDataInfo);
			this.selectionBar.getChecked().each(function() {
				rows.push($(this).closest('.row-item').get(0));
			});
		} else {
			formData = [{ name: 'result_ids[]', value: specific_id }];
			formDataInfo.checkedCount = 1;
			rows = [$('.ticket-' + specific_id + '.row-item').get(0)];
		}

		this.getActionFormValues(formData, false, formDataInfo);

		// If we dont have any tickets or actions then theres nothing to do
		if (!force && (!formDataInfo.checkedCount || !formDataInfo.actionsCount)) {
			//return;
		}

		rows = $(rows);
		rows.addClass('loading');

		var runningAjax = $.ajax({
			url: BASE_URL + 'agent/ticket-search/get-page',
			type: 'POST',
			data: formData,
			dataType: 'html',
			context: this,
			complete: function() {
				rows.removeClass('loading');
				this.runningAjax = null;
			},
			success: function(html) {
				this.updatePreviewDisplay(html);
			}
		});

		// Only save running ajax if theres more than one
		if (!specific_id) {
			this.runningAjax = runningAjax;
		}
	},

	/**
	 * Update the preview display data with an HTML block returned from the server
	 *
	 * @param html
	 */
	updatePreviewDisplay: function(html) {
		var resultWrap = $(html);
		var listWrapper = this.listWrapper;

		$('.row-item', resultWrap).each(function() {
			var ticketId = $(this).data('ticket-id');
			var row = $('.ticket-' + ticketId + '.row-item', listWrapper);

			// Clear existing preview edits if there are any
			var existPrev = $('.preview-edit', row);
			existPrev.remove();

			var topRowRight = $('.top-row-right', this).addClass('preview-edit');
			var extraFields = $('.extra-fields', this).addClass('preview-edit');

			var origTopRowRight = $('.top-row-right', row).addClass('preview-edit-hide');
			var origExtraFields = $('.extra-fields', row).addClass('preview-edit-hide');

			topRowRight.insertAfter(origTopRowRight);
			extraFields.insertAfter(origExtraFields);

			origTopRowRight.hide();
			origExtraFields.hide();
		});
	},


	/**
	 * When a ticket has been checked or uncheck, need to update the preview status of that ticket.
	 */
	handleCheckChange: function(el, is_checked) {
		var row = $(el).closest('.row-item');

		if (is_checked) {
			this.updatePreview(row.data('ticket-id'));
		} else {
			$('.preview-edit', row).remove();
			$('.preview-edit-hide', row).show().removeClass('preview-edit-hide');
		}
	},


	/**
	 * Update the positions of the elements
	 */
	updatePositions: function() {

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

		if (contentH < 350) {
			contentH = 350;
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

		this.updateUi();
	},


	/**
	 * Load a macro into the form
	 */
	loadMacro: function(macro_id) {

		var macroEl = $('.macro-options', this.wrapper);
		var inputActionsEl = $('.actions-input', this.wrapper);

		macro_id = parseInt(macro_id);
		if (!macro_id) {
			macroEl.hide();
			macroEl.find('ul.actions-list').empty();
			macroEl.find('input.macro_id').remove();
			inputActionsEl.show();
			this.updateUi();
			this.updatePositions();
			return;
		}

		var macroBtnEl = $('div.macro-load', this.wrapper).addClass('loading');

		$.ajax({
			url: BASE_URL + 'agent/ticket-search/ajax-get-macro-actions',
			data: { macro_id: macro_id },
			type: 'GET',
			dataType: 'json',
			context: this,
			success: function(data) {

				inputActionsEl.hide();
				macroEl.show();

				var input = $('<input type="hidden" class="macro_id" name="run_macro_id" />');
				input.val(macro_id);
				input.appendTo(macroEl);

				var ul = macroEl.find('ul.actions-list');
				ul.empty();

				Array.each(data.descriptions, function(desc) {
					var li = $('<li />');
					li.html(desc);

					ul.append(li);
				});

				macroBtnEl.removeClass('loading');

				self.hasAnyChange = true;
				this.updateUi();
				this.updatePositions();
			}
		});

		this.updatePositions();
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
		if (!this.wrapper || !this.wrapper[0]) {
			this._resetWrapper();
		}

		this._initOverlay();

		//this.scrollerHandler = new DeskPRO.Agent.ScrollerHandler(this, $('> section > article', this.wrapper), {});
		this.updatePositions();
		DeskPRO_Window.layout.addEvent('resized', this.updatePositions, this);
		this.wrapper.addClass('open');
		this.backdropEls.show();

		this.updateCount(null);
		this.wrapper.addClass('open').show();
		//this.updatePreview();

		this.updatePositions();
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

		if (this.wrapperContainer) {
			this.wrapperContainer.remove();
		}

		if (this.textarea && this.textarea.data('redactor')) {
			this.textarea.redactor('destroy');
		}
	}
});

Orb.createNamespace('DeskPRO.Agent.PageHelper');

DeskPRO.Agent.TicketList.ListView = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(page, options)  {

		var self = this;

		this.page = page;

		this.options = {

		};
		this.setOptions(options);
		this.page.addEvent('destroy', (function() {
			this.destroy();
		}).bind(this));
	},

	_initOverlay: function() {
		var self = this;
		if (this._isIniting) return;
		if (this._hasInit) return
		this._isIniting = true;

		var new_url = this.page.meta.viewTypeUrl.replace('$view_type', 'list');
		if (this.options.load_url) {
			new_url = this.options.load_url.replace('$view_type', 'list');
		}

		this.wrapper = $('<div class="dp-overlay-container ticketlist" />').appendTo('body');
		this.backdropEl = $('<div class="backdrop dp-overlay-backdrop" />');
		this.backdropEl.css('z-index', '10000').hide().appendTo('body');
		this.backdropEl.on('click', (function(ev) {
			ev.stopPropagation();
			this.close();
		}).bind(this));

		this.wrapper.html('<section class="dp-overlay"><div class="overlay-title"><span class="close-overlay"></span></div><div class="loading"></div></section>');
		this.wrapper.find('.close-overlay').on('click', function(ev) {
			ev.stopPropagation();
			self.close();
		});

		this.updatePositions();

		this.wrapper.addClass('open');
		this.backdropEl.show();

		this.runningAjax = $.ajax({
			url: new_url,
			dataType: 'html',
			context:  this,
			done: function() {
				this.runningAjax = null;
			},
			success: function(html) {

				this.wrapper.html(html);

				$('header .close-trigger', this.wrapper).first().on('click', (function(ev) {
					ev.stopPropagation();
					ev.preventDefault();
					this.close();
				}).bind(this));

				var page = DeskPRO_Window.createPageFragment(html);
				page.listview = this;
				page.setMetaData('routeUrl', new_url);
				page.setMetaData('pageReloader', this.reload.bind(this));
				page.setMetaData('overlay', this);
				page.fireEvent('render', [this.wrapper]);
				page.fireEvent('activate');

				this._isIniting = false;
				this._hasInit = true;

				this.fireEvent('ajaxLoaded', [this]);

				this.open();
			}
		});
	},

	reload: function() {
		this.showInnerLoading();

		var page = this.page;
		window.setTimeout(function() {
			if (page && page.switchViewType) {
				page.switchViewType('list');
			}
		}, 50);
	},

	open: function() {
		this._initOverlay();
		if (!this._hasInit) return;

		this.updatePositions();

		this.wrapper.addClass('open');
		this.backdropEl.show();

		$('body').addClass('print-overlay');
		this.fireEvent('opened', [this]);
	},

	isOpen: function() {
		if (!this._hasInit || !this.wrapper.is('.open')) {
			return false;
		}

		return true;
	},

	close: function() {
		if (!(this._hasInit || this._isIniting || this.isOpen())) return;
		$('body').removeClass('print-overlay');
		this.destroy();
	},

	showInnerLoading: function() {
		$('.full-loading', this.wrapper).show();
	},

	hideInnerLoading: function() {
		$('.full-loading', this.wrapper).hide();
	},

	/**
	 * Update the positions of the elements
	 */
	updatePositions: function() {
		this.wrapper.css({
			top: 20,
			right: 20,
			bottom: 20,
			left: 20
		});
	},


	destroy: function() {
		if (this._isIniting) {
			this.runningAjax.abort();
			this.runningAjax = null;
			this._isIniting = false;
		}
		if (this._hasInit) {
			this.page.destroy();
		}
		if (this.wrapper) {
			this.wrapper.remove();
		}
		if (this.backdropEl) {
			this.backdropEl.remove();
		}

		delete this.wrapper;
		delete this.backdropEl;
		delete this.options;
		delete this.page;
	}
});

Orb.createNamespace('DeskPRO.Agent.PageHelper.PeopleList');

DeskPRO.Agent.PageHelper.PeopleList.ListView = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(page, options)  {

		var self = this;

		this.page = page;

		this.options = {

		};
		this.setOptions(options);
		this.page.addEvent('destroy', (function() {
			this.destroy();
		}).bind(this));
	},

	_initOverlay: function() {
		var self = this;
		if (this._isIniting) return;
		if (this._hasInit) return
		this._isIniting = true;

		var new_url = this.page.meta.viewTypeUrl.replace('$view_type', 'list');
		if (this.options.load_url) {
			new_url = this.options.load_url.replace('$view_type', 'list');
		}

		this.wrapper = $('<div class="dp-overlay-container ticketlist" />').appendTo('body');
		this.backdropEl = $('<div class="backdrop dp-overlay-backdrop" />');
		this.backdropEl.css('z-index', '10000').hide().appendTo('body');
		this.backdropEl.on('click', (function(ev) {
			ev.stopPropagation();
			this.close();
		}).bind(this));

		this.wrapper.html('<section class="dp-overlay"><div class="overlay-title"><span class="close-overlay"></span></div><div class="loading"></div></section>');
		this.wrapper.find('.close-overlay').on('click', function(ev) {
			ev.stopPropagation();
			self.close();
		});

		this.updatePositions();

		this.wrapper.addClass('open');
		this.backdropEl.show();

		this.runningAjax = $.ajax({
			url: new_url,
			dataType: 'html',
			context:  this,
			done: function() {
				this.runningAjax = null;
			},
			success: function(html) {

				this.wrapper.html(html);

				$('header .close-trigger', this.wrapper).first().on('click', (function(ev) {
					ev.stopPropagation();
					ev.preventDefault();
					this.close();
				}).bind(this));

				var page = DeskPRO_Window.createPageFragment(html);
				page.listview = this;
				page.setMetaData('routeUrl', new_url);
				page.setMetaData('pageReloader', this.reload.bind(this));
				page.setMetaData('overlay', this);
				page.fireEvent('render', [this.wrapper]);
				page.fireEvent('activate');

				this._isIniting = false;
				this._hasInit = true;

				this.fireEvent('ajaxLoaded', [this]);

				this.open();
			}
		});
	},

	reload: function() {
		this.showInnerLoading();

		var page = this.page;
		window.setTimeout(function() {
			if (page && page.switchViewType) {
				page.switchViewType('list');
			}
		}, 50);
	},

	open: function() {
		this._initOverlay();
		if (!this._hasInit) return;

		this.updatePositions();

		this.wrapper.addClass('open');
		this.backdropEl.show();

		$('body').addClass('print-overlay');
		this.fireEvent('opened', [this]);
	},

	isOpen: function() {
		if (!(this._hasInit || this.wrapper.is('.open'))) {
			return false;
		}

		return true;
	},

	close: function() {
		if (!(this._hasInit || this._isIniting || this.isOpen())) return;
		$('body').removeClass('print-overlay');
		this.destroy();
	},

	showInnerLoading: function() {
		$('.full-loading', this.wrapper).show();
	},

	hideInnerLoading: function() {
		$('.full-loading', this.wrapper).hide();
	},

	/**
	 * Update the positions of the elements
	 */
	updatePositions: function() {
		this.wrapper.css({
			top: 20,
			right: 20,
			bottom: 20,
			left: 20
		});
	},


	destroy: function() {
		if (this._isIniting) {
			this.runningAjax.abort();
			this.runningAjax = null;
			this._isIniting = false;
		}
		if (this._hasInit) {
			this.page.destroy();
		}
		if (this.wrapper) {
			this.wrapper.remove();
		}
		if (this.backdropEl) {
			this.backdropEl.remove();
		}

		delete this.wrapper;
		delete this.backdropEl;
		delete this.options;
		delete this.page;
	}
});

Orb.createNamespace('DeskPRO.Agent.TicketList');

/**
 * Handles changes to many tickets on a ticket page. Somewhat same feedback as with the Ticket.ChangeManager,
 * except we work with many tickets at a time, and we only care about updating the UI and not about
 * getting current values.
 */
DeskPRO.Agent.TicketList.ChangeManager = new Class({

	Implements: [Events],

	ticketPage: null,

	hasChanges: false,
	changes: {}, // changes are { ticketId: [ [property, newValue, hasApplied] ] }

	ticketIdsBatch: null,

	/**
	 * @param {DeskPRO.Agent.PageFragment.Page.BasicTicketResults} ticketPage
	 */
	initialize: function(ticketPage) {
		this.ticketPage = ticketPage;
	},



	/**
	 * Begin a batch of changes to selected tickets
	 *
	 * @param {Array} ticketIds
	 */
	begin: function(ticketIds) {
		this.ticketIdsBatch = ticketIds;
	},



	/**
	 * Add a change to the set of changes. This applies a change to all tickets.
	 */
	addChange: function(property, newValue) {

		var name = property.getName();
		var id = property.getTicketId();

		// Dont care if its the same!
		if (property.isSameValue(newValue)) {
			return;
		}

		if (!this.changes[id]) this.changes[id] = [];
		var info = {'property': property, 'newValue': newValue, 'hasApplied': false};
		this.changes[id].push(info);

		this.hasChanges = true;
	},



	/**
	 * Apply a certain new value in the interface
	 */
	applyChangeForEntry: function (info) {

		var property = info.property;
		var name = property.getName();
		var id = property.getTicketId();

		info.oldValue = property.getValue();
		property.setValue(info.newValue);

		property.highlightInterfaceElement();

		info.hasApplied = true;
	},



	/**
	 * Apply all queued changes in the interface
	 */
	applyChanges: function() {

		if (!this.ticketIdsBatch) return;

		$('tr:not(.on, .line-3)', this.ticketPage.contentWrapper).addClass('faded');
		$('tr.on').removeClass('faded');
		$('table:first', this.ticketPage.contentWrapper).addClass('preview-mode');

		Array.each(this.ticketIdsBatch, function (ticketId) {
			Array.each(this.changes[ticketId], function (change) {
				this.applyChangeForEntry(change);
			}, this);
		}, this);

		this.ticketIdsBatch = null;
	},

	revertChangeForEntry: function (info) {
		var property = info.property;
		if (info.hasApplied && info.oldValue !== undefined) {
			property.setValue(info.oldValue);
			property.unhighlightInterfaceElement();
		}
	},

	/**
	 * Revert all queuued changes in the interface to their previuos values
	 */
	revertChanges: function() {

		Object.each(this.changes, function (changes, ticketId) {
			Array.each(changes, function (change) {
				this.revertChangeForEntry(change);
			}, this);
		}, this);

		this.ticketIdsBatch = null;
		this.changes = {};
		this.hasChanges = false;

		this.fireEvent('changesCleared');
	},



	revertChangesForTicketId: function(ticketId) {
		if (!this.changes[ticketId]) return;

		Array.each(this.changes[ticketId], function (change) {
			this.revertChangeForEntry(change);
		}, this);

		var lines = $('tr.ticket-' + ticketId, this.ticketPage.contentWrapper);
		lines.filter('tr.line-3').hide().find('td > ul').html('')
		lines.filter(':not(.line-3)').addClass('faded').removeClass('with-line-3');

		delete this.changes[ticketId];

		if (Object.getLength(this.changes) == 0) {
			this.revertChanges();
		}
	},


	/**
	 * Just updates the UI to show we accepted the changes
	 */
	commitChanges: function() {
		Array.each(Object.values(this.changes), function (changes) {
			Object.each(changes, function(change) {
				var property = change.property;
				property.unhighlightInterfaceElement();
			}, this);
		}, this);

		this.ticketIdsBatch = null;
		this.changes = {};
		this.hasChanges = false;

		this.fireEvent('changesCleared');
	},



	/**
	 * Called when we detect if a value was updated automatically from somewhere.
	 */
	setPropertyUpdated: function(property, newValue) {
		if (typeOf(property) == 'string') {
			property = this.ticketPage.getPropertyManager(property);
		}

		property.setIncomingValue(newValue);
		property.pulseInterfaceElement();
	}
});
Orb.createNamespace('DeskPRO.Agent.TicketList.Property');

/**
 * Something that can be changed from the ticket listing page.
 * Unlike the Ticket properties, these only care about changing
 * values.
 */
DeskPRO.Agent.TicketList.Property.Abstract = new Class({

	Implements: [Events, Options],

	displayNameType: null,
	displayCaption: null,

	options: {},
	ticketPage: null,
	ticketId: null,

	/**
	 * @param {DeskPRO.Agent.PageFragment.Page.Ticket} ticketPage
	 * @param {Integer} ticketId
	 * @param {Object} options
	 */
	initialize: function(ticketPage, ticketId, options) {

		if (options) this.setOptions(options);

		this.ticketPage = ticketPage;
		this.ticketId = ticketId;

		this.init();
	},

	init: function() {},

	/**
	 * Name for the property
	 *
	 * @return {String}
	 */
	getName: function() {
		// override
	},

	getTicketId: function() {
		return this.ticketId;
	},

	isSameValue: function(compare) {
		if (this.getValue() == compare) {
			return true;
		}

		return false;
	},

	getValue: function() {
		// override
	},

	/**
	 * Sets a new value. Must also update the UI if needed.
	 *
	 * @param mixed value
	 */
	setValue: function(value) {
		// override
	},



	/**
	 * Sets data that we got from the server.
	 */
	setIncomingValue: function(value) {
		this.setValue(value);
	},



	/**
	 * Get the UI element used to display the property.
	 */
	getInterfaceElement: function() {
		if (this._interfaceEl !== null) return this._interfaceEl;

		this._interfaceEl = this._getInterfaceElement();

		return this._interfaceEl;
	},

	_interfaceEl: null,
	_getInterfaceElement: function() {
		// override
	},



	/**
	 * Helps build a selector to for getInterfaceElement for all the ticket
	 * ids this should affect.
	 */
	_buildSelector: function(base_sel) {
		sel = 'tr.ticket-' + this.ticketId + ' ' + base_sel;

		return sel;
	},

	getSublineElement: function() {

		var line2 = $('tr.ticket-' + this.ticketId + '.line-2', this.ticketPage.actionsBarHelper.tableEl);
		line2.addClass('with-line-3');
		var line3 = $('tr.ticket-' + this.ticketId + '.line-3', this.ticketPage.actionsBarHelper.tableEl);
		line3.show();

		var ul = $('ul', line3);

		var li = $('<li class="generated prop-value ' + this.getName() + '"></li>');

		ul.append(li);

		return li;
	},


	/**
	 * When a property is updated automatically (not from a user action, like in the background),
	 * this pulse action is applied to highlight and fade slowly.
	 */
	pulseInterfaceElement: function() {
		this.getInterfaceElement().effect('highlight', 1200);
	},


	/**
	 * Highlight the UI element to bring attention to some change.
	 */
	highlightInterfaceElement: function() {
		this.getInterfaceElement().addClass('change-on');
	},



	/**
	 * Remove the UI highlight
	 */
	unhighlightInterfaceElement: function() {
		this.getInterfaceElement().removeClass('change-on');
	}
});
Orb.createNamespace('DeskPRO.Agent.TicketList.Property');

/**
 * These standard options are simple values taken from a menu. Since
 * they're so similar, this single property class can handle all of them.
 */
DeskPRO.Agent.TicketList.Property.StandardOption = new Class({
	Extends: DeskPRO.Agent.TicketList.Property.Abstract,

	optionName: null,

	init: function() {
		var valid_options = ['department', 'category', 'product', 'priority', 'workflow', 'status', 'agent', 'agent_team'];

		if (valid_options.indexOf(this.options.optionName) == -1) {
			throw 'invalidOptionName:'+this.options.optionName;
		}

		this.optionName = this.options.optionName;

		switch (this.optionName) {
			case 'department': this.displayNameType = 'department_full'; this.displayCaption = 'Department'; break;
			case 'category': this.displayNameType = 'ticket_category_full'; this.displayCaption = 'Category'; break;
			case 'product': this.displayNameType = 'product'; this.displayCaption = 'Product'; break;
			case 'priority': this.displayNameType = 'ticket_priority'; this.displayCaption = 'Priority'; break;
			case 'workflow': this.displayNameType = 'ticket_workflow'; this.displayCaption = 'Workflow'; break;
			case 'status': this.displayNameType = 'status'; this.displayCaption = 'Status'; break;
			case 'agent': this.displayNameType = 'agent'; this.displayCaption = 'Agent'; break;
			case 'agent_team': this.displayNameType = 'agent_team'; this.displayCaption = 'Agent Team'; break;
		}
	},

	getValue: function() {
		return this.getInterfaceElement().data('prop-value');
	},

	getName: function() {
		return this.optionName;
	},

	setValue: function(value) {
		if (value == "0") value = 0;

		this.getInterfaceElement().data('prop-value', value);

		if (value) {
			var displayName = value;
			if (this.displayNameType) {
				displayName = DeskPRO_Window.getDisplayName(this.displayNameType, value);
				if (!displayName) displayName = value;
			}

			var text = displayName;

			if (this.getInterfaceElement().is('.generated')) {
				var text = this.displayCaption + ': ' + displayName;
			}

			this.getInterfaceElement().removeClass('no-value').text(text);
		} else {
			this.getInterfaceElement().addClass('no-value').text('none');
		}
	},

	_getInterfaceElement: function() {
		var el = $(this._buildSelector('.prop-val.' + this.optionName+':first'), this.ticketPage.actionsBarHelper.tableEl);

		if (!el.length) {
			el = this.getSublineElement();
		}

		return el;
	}
});
Orb.createNamespace('DeskPRO.Agent.TicketList.Property');

/**
 * New reply area
 */
DeskPRO.Agent.TicketList.Property.NewReply = new Class({
	Extends: DeskPRO.Agent.TicketList.Property.Abstract,

	displayCaption: 'Reply',

	getName: function() {
		return 'new_reply';
	},

	isSameValue: function(compare) {
		return false;
	},

	getValue: function() {
		return null;
	},

	setValue: function(value) {
		if (value) {
			var text = value;
			this.getInterfaceElement().removeClass('no-value').text(text);
		}
	},

	setIncomingValue: function(value) {

	},

	_getInterfaceElement: function() {
		el = this.getSublineElement();
		return el;
	}
});
Orb.createNamespace('DeskPRO.Agent.TicketList.Property');

DeskPRO.Agent.TicketList.Property.TicketField = new Class({
	Extends: DeskPRO.Agent.TicketList.Property.Abstract,

	optionName: null,

	init: function() {
		this.optionName = 'ticket_field.' + this.options.fieldId;
	},

	getName: function() {
		return this.optionName;
	},

	getValue: function() {
		return this.getInterfaceElement().html();
	},

	setValue: function(value) {
		this.getInterfaceElement().html(value);
	},

	_getInterfaceElement: function() {
		var el = $('.prop-val .custom-field-' + this.options.fieldId, this.ticketPage.actionsBarHelper.tableEl);

		if (!el.length) {
			el = this.getSublineElement();
		}

		return el;
	}
});
Orb.createNamespace('DeskPRO.Agent.TicketList.Property');

DeskPRO.Agent.TicketList.Property.Flag = new Class({
	Extends: DeskPRO.Agent.TicketList.Property.Abstract,

	optionName: 'flag',
	displayCaption: 'Flag',

	init: function() {

	},

	getValue: function() {
		return this.getInterfaceElement().data('flag');
	},

	getName: function() {
		return this.optionName;
	},

	setValue: function(value) {
		var last_value = this.getInterfaceElement().data('flag');

		this.getInterfaceElement().data('flag', value);
		this.getInterfaceElement().removeClass('icon-flag-' + last_value).addClass('icon-flag-' + value);
	},

	_getInterfaceElement: function() {
		var el = $(this._buildSelector('.ticket-flag:first'), this.ticketPage.actionsBarHelper.tableEl);
		return el;
	}
});
Orb.createNamespace('DeskPRO.Agent.TicketList.Property');

DeskPRO.Agent.TicketList.Property.Labels = new Class({
	Extends: DeskPRO.Agent.TicketList.Property.Abstract,

	optionName: 'labels',
	displayCaption: 'Flag',

	mode: 'add',

	init: function() {
		this.mode = this.options.mode;

		if (this.mode == 'add') {
			this.displayCaption = 'Add Labels';
		} else {
			this.displayCaption = 'Remove Labels';
		}
	},

	isSameValue: function(compare) {
		return false;
	},

	getName: function() {
		return this.mode + '_' + this.optionName; // add_labels or remove_labels
	},

	getValue: function() {
		return this._values;
	},

	_values: null,

	setValue: function(values) {
		this._values = values;

		var text = this.displayCaption + ': ';

		Array.each(values, function(val) {
			text += ' '+val;
		}, this);

		this.getInterfaceElement().text(text);
	},

	_getInterfaceElement: function() {
		var el = this.getSublineElement();
		return el;
	}
});