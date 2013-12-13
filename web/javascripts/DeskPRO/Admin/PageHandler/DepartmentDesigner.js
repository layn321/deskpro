Orb.createNamespace('DeskPRO.Admin.PageHandler');

DeskPRO.Admin.PageHandler.DepartmentDesigner = new Class({
	Extends: DeskPRO.Admin.PageHandler.Basic,

	department_id: 0,
	initialize: function(department_id) {
		this.department_id = department_id;

		$('#display_item').template('display_item');
		DeskPRO_Window.getMessageBroker().addMessageListener('field.change', this.fetchNewlyCreatedField, this);
		DeskPRO_Window.getMessageBroker().addMessageListener('widget.change', this.fetchNewlyCreatedWidget, this);
	},

	initPage: function() {
		this.initPopoutTriggers();

		var self = this;

		$('#display_item_list').sortable({
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
					self.addDisplayItemFromEl(el, true);
				});
			}
		});

		$('.available-display-items li.display-item').draggable({
			appendTo: 'body',
			helper: 'clone',
			connectToSortable: '#display_item_list'
		});
	},

	addDisplayItemFromEl: function(el, do_replace) {

		$('#no_elements_message').hide();

		var itemName = el.data('item-name');
		var itemId = el.data('item-id');
		var idClass = itemId.replace(/[^a-zA-Z0-9_]/g, '_');
		var rendered = false;
		if ($('div.rendered', parent).length) {
			rendered = $('div.rendered', el).clone();
		}

		var data = {name: itemName, itemId: itemId, idClass: idClass };

		var item = $.tmpl('display_item', data);
		if (rendered) {
			$('div.rendered', item).replaceWith(rendered);
			rendered.show();
		} else {
			$('div.rendered', item).remove();
		}

		$('.available-display-items .' + idClass).hide();

		if (do_replace) {
			el.replaceWith(item);
		} else {
			$('#display_item_list').append(item);
		}

		this.initDisplayItem(item, itemId);

		return item;
	},

	initDisplayItem: function(itemEl, itemId) {
		var editor = new DeskPRO.Form.RuleBuilder($('#criteria_tpl'));
		editor.addEvent('newRow', function(new_row) {
			$('.remove', new_row).on('click', function() {
				new_row.remove();
			});
		});
		var to_el = $('.search-form.ruletype-all .rule-list', itemEl);

		var self = this;
		$('.remove', itemEl).on('click', function() { self.removeDisplayItem(itemEl); });

		$('.search-form.ruletype-all .add-term').data('add-count', 0).on('click', function() {
			var count = parseInt(itemEl.data('editor-all-add-count'));
			var basename = 'terms_all['+itemId+']['+count+']';
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

		$('.search-form.ruletype-any .add-term').data('add-count', 0).on('click', function() {
			var count = parseInt(itemEl.data('editor-any-add-count'));
			var basename = 'terms_any['+itemId+']['+count+']';
			itemEl.data('editor-all-any-count', count+1);
			editor2.addNewRow(to_el2, basename);
		});
		itemEl.data('editor-any', editor2);
	},

	removeDisplayItem: function(itemEl) {
		itemEl.remove();
		$('.available-display-items .' + itemEl.data('id-class')).show();
		if ($('#display_item_list > li').length == 1) {
			$('#no_elements_message').show();
		}
	},

	resetFormItems: function(content, element) {
		var el = $('.rules-wrap', content);
		var idclass = el.data('id-class');
		el.detach().hide().appendTo($('#display_item_'+idclass));
	},

	fetchNewlyCreatedField: function (info) {
		var field_id = info.field_id;
		$.ajax({
			url: DeskPRO_Window.getUrl('admin_departments_designer_ajaxfetchfield', {field_id: field_id}),
			dataType: 'json',
			type: 'GET',
			success: function(data) {
				var ul = $('ul.field-list:first');
				var last = $('li:last', ul);
				var exist = $('li.field-' + field_id);
				if (exist.length) {
					exist.replaceWith(data.html);
				} else {
					last.before(data.html);
				}
			}
		});
	},

	fetchNewlyCreatedWidget: function (info) {
		var widget_id = info.widget_id;
		$.ajax({
			url: DeskPRO_Window.getUrl('admin_departments_designer_ajaxfetchwidget', {widget_id: widget_id}),
			dataType: 'json',
			type: 'GET',
			success: function(data) {
				var ul = $('ul.widget-list:first');
				var last = $('li:last', ul);
				var exist = $('li.widget-' + widget_id);
				if (exist.length) {
					exist.replaceWith(data.html);
				} else {
					last.before(data.html);
				}
			}
		});
	},

	addDefinedDisplayElement: function(display_element) {
		var el = $('.available-display-items li.' + display_element.idClass);
		var item = this.addDisplayItemFromEl(el, false);

		$('input[name^="initial_display"]', item).val(display_element.initial_display);
		if (display_element.is_agent_only) {
			$('input[name^="agent_only"]', item).attr('checked', true);
		}

		var itemId = item.data('item-id');

		if (display_element.conds_all) {
			var editor = item.data('editor-all');
			var to_el = $('.search-form.ruletype-all .rule-list', item);
			Array.each(display_element.conds_all, function(v) {
				var count = parseInt(item.data('editor-all-add-count'));
				var basename = 'terms_all['+itemId+']['+count+']';
				item.data('editor-all-all-count', count+1);
				editor.addNewRow(to_el, basename, { rule_type: v[0], choice: v[1]});
			});
		}
		if (display_element.conds_any) {
			var editor = item.data('editor-any');
			var to_el = $('.search-form.ruletype-any .rule-list', item);
			Array.each(display_element.conds_any, function(v) {
				var count = parseInt(item.data('editor-any-add-count'));
				var basename = 'terms_any['+itemId+']['+count+']';
				item.data('editor-all-any-count', count+1);
				editor.addNewRow(to_el, basename, { rule_type: v[0], choice: v[1]});
			});
		}
	}
});
