Orb.createNamespace('DeskPRO.Admin');

DeskPRO.Admin.TicketEditor = new Orb.Class({

	Implements: [Orb.Util.Options],

	initialize: function(options) {

		$('#display_item_ticket_category_tpl').template('display_item_ticket_category_tpl');
		$('#display_item_ticket_product_tpl').template('display_item_ticket_product_tpl');
		$('#display_item_ticket_workflow_tpl').template('display_item_ticket_workflow_tpl');
		$('#display_item_ticket_priority_tpl').template('display_item_ticket_priority_tpl');
		$('#display_item_ticket_field_tpl').template('display_item_ticket_field_tpl');
		$('#display_item_people_field_tpl').template('display_item_people_field_tpl');

		$('#field_options_text').template('field_options_text');
		$('#field_options_select').template('field_options_select');
		$('#field_options_multiselect').template('field_options_multiselect');

		this.options = {
			department_id: 0,
			url: '',
			context: null,
			name: 'name'
		};

		if (options) {
			this.setOptions(options);
		}

		this.department_id = this.options.department_id;
		this.url = this.options.url;
		this.context = $(this.options.context);

		var self = this;
		$('.display_item_list', this.context).sortable({
			items: "li:not(#no_elements_message)",
			axis: 'y',
			sort: function() {
				// gets added unintentionally by droppable interacting with sortable
				// using connectWithSortable fixes this, but doesn't allow you to customize active/hoverClass options
				$( this ).removeClass("drop-active");
			},
			stop: function(event, ui) {
				$('li.original', this).each(function() {
					var el = $(this);
					self.addDisplayItemFromEl(el, $(event.target), true);
				});
			}
		});

		$('.field-list li', this.context).draggable({
			appendTo: 'body',
			helper: 'clone',
			connectToSortable: $('.display_item_list', this.context)
		});
	},

	initPage: function() {

	},

	addDisplayItemFromEl: function(el, target, do_replace) {

		$('.no_elements_message', target).hide();

		var itemName = el.html();
		var itemId   = el.data('item-id') || '';
		var itemType = el.data('item-type') || '';
		var idClass  = itemType + '_' + (itemId+'').replace(/[^a-zA-Z0-9_]/g, '_');
		var fieldName = el.data('field-name');
		var elId   = Orb.getUniqueId();

		var rendered = $('#custom_field_rendered > .' + idClass).clone();

		var data = {name: itemName, itemId: itemId, idClass: idClass, itemType: itemType, elId: elId, fieldName: fieldName, rendered: rendered };

		var item = $.tmpl('display_item_' + itemType + '_tpl', data);
		if (rendered.length) {
			$('.rendered', item).append(rendered);
		}

		var fieldType = item.data('field-name');
		if (fieldType) {
			var options_el = null;
			if (fieldType == 'text' || fieldType == 'textarea') {
				options_el = $.tmpl('field_options_text', data);
			} else if (fieldType == 'choice') {
				options_el = $.tmpl('field_options_select', data);
			}

			if (options_el) {
				item.append(options_el);
			} else {
				$('.field-options', item).remove();
			}
		}

		if (do_replace) {
			el.replaceWith(item);
		} else {
			$('.display_item_list', target).append(item);
		}

		var orig = $('li.field-item.' + idClass, target);
		orig.addClass('disabled');

		data.itemEl = item;

		this.initDisplayItemRuleEditor(data);

		var self = this;
		$('.remove', item).on('click', function() { self.removeDisplayItem(item); });

		return item;
	},

	initDisplayItemRuleEditor: function(itemInfo) {

		var itemEl   = itemInfo.itemEl;
		var itemId = itemInfo.itemId;

		var editor = new DeskPRO.Form.RuleBuilder($('#criteria_tpl'));
		editor.addEvent('newRow', function(new_row) {
			$('.remove', new_row).on('click', function() {
				new_row.remove();
			});
		});
		var to_el = $('.search-form.ruletype-all .rule-list', itemEl);

		var self = this;

		$('.search-form.ruletype-all .add-term', this.context).data('add-count', 0).on('click', function() {
			var count = parseInt(itemEl.data('editor-all-add-count'));
			var basename = 'terms_all['+count+']';
			itemEl.data('editor-all-add-count', count+1);
			editor.addNewRow(to_el, basename);
		});
		itemEl.data('editor-all', editor);

		var editor2 = new DeskPRO.Form.RuleBuilder($('#criteria_tpl'));
		editor2.addEvent('newRow', function(new_row) {
			$('.remove', new_row).on('click', function() {
				new_row.remove();
			});
		});
		var to_el2 = $('.search-form.ruletype-any .rule-list', itemEl);

		$('.search-form.ruletype-any .add-term', this.context).data('add-count', 0).on('click', function() {
			var count = parseInt(itemEl.data('editor-any-add-count'));
			var basename = 'terms_any['+count+']';
			itemEl.data('editor-all-any-count', count+1);
			editor2.addNewRow(to_el2, basename);
		});
		itemEl.data('editor-any', editor2);
	},

	removeDisplayItem: function(itemEl) {

		itemEl.remove();
		$('.available-display-items .' + itemEl.data('id-class'), this.context).show();
		if ($('.display_item_list > li', this.context).length == 1) {
			$('.no_elements_message', this.context).show();
		}

		var className = itemEl.data('id-class');
		var els = $('.agent-sections-wrap li.' + className, this.context);
		if (!els.length) {
			var orig = $('li.field-item.' + className, this.context);
			orig.removeClass('disabled');
		}
	},

	getName: function() {
		return this.options.name;
	}
});
