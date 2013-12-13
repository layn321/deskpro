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
