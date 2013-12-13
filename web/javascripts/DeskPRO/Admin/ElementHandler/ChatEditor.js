Orb.createNamespace('DeskPRO.Admin.ElementHandler');

DeskPRO.Admin.ElementHandler.ChatEditor = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var self = this;

		var drawRendered = this.el.data('section') != 'view' ? true : false;

		window.ChatEditor = this;
		$('#department_switcher').on('change', function() {
			var url = $('#department_switcher').find('option:selected').first().data('refresh-url');
			window.location = url;
		});
		DP.select($('#department_switcher'), {
			formatResult: function (result) {
				var opt = $('#department_switcher').find('option[value="' + result.id + '"]');
				var name = Orb.escapeHtml(opt.text());

				if (opt.hasClass('custom') ){
					return '<div class="orange-pip">' + name + '</div>';
				} else {
					return '<div class="">' + name + '</div>';
				}
			}
		});

		this.el.submit(function(ev) {
			ev.preventDefault();
		});

		$('#per_department_check').on('change', function() {
			var val = 0;
			if ($(this).is(':checked')) {
				val = 1;
			}

			window.location = $(this).data('update-url') + '?enable=' + val;
		});

		//------------------------------
		// Handle dragging/dropping between the two lists
		//------------------------------

		var draggingSidebarEl = false;
		$('#chat_elements li.draggable').draggable({
			connectToSortable: $('#admin_chat_editor_items'),
			appendTo: 'body',
			containment: 'body',
			cursorAt: { left: 140, top: 15 },
			cursor: 'move',
			start: function() {
				$(this).css({ width: '280px'});
				$('#admin_chat_editor').addClass('is-dragging');
			},
			stop: function() {
				$('#admin_chat_editor').removeClass('is-dragging');
			},
			helper: function(event) {
				var el = $(this);
				draggingSidebarEl = el;

				var helper = $('<div class="admin-ticket-editor-dragging"><label></label></div>');
				$('label', helper).text($('label', el).text());
				helper.data('item-id', el.data('item-id'));

				return helper;
			},
			revert: 'invalid',
			zIndex: 1000
		});

		$('#admin_chat_editor_items').sortable({
			items: '> li.form-item',
			start: function() {
				$('#admin_chat_editor').addClass('is-dragging');
			},
			stop: function() {
				$('#admin_chat_editor').removeClass('is-dragging');
				self.save();
			},
			helper: function(event, el) {
				var helper = $('<div class="admin-ticket-editor-dragging"><label></label></div>');
				$('label', helper).text($('label.field-title', el).text());
				helper.data('item-id', el.data('item-id'));
				helper.css({ width: '280px'});

				return helper;
			},
			update: function(event, ui) {
				var el = $(ui.item);
				if (!draggingSidebarEl) {
					return;
				}
				if (!el.is('li.draggable')) {
					return;
				}

				$('#admin_chat_editor').addClass('changed');

				$('#admin_chat_editor_items .no-items-notice, #admin_chat_editor_items .is-default-notice').hide();

				var formItem = $(DeskPRO_Window.util.getPlainTpl($('#editor_row_tpl')));
				formItem.data('item-id', el.data('item-id'));
				$('label.field-title', formItem).text($('label', el).text());
				if (el.data('is-agent-field')) {
					formItem.find('.field-agent-only').show();
				}

				formItem.data('sidebar-item', draggingSidebarEl);

				if (drawRendered) {
					var tplEl = $('#rendered_field_' + formItem.data('item-id').replace(/[^a-zA-Z0-9_\-]/g, '_').replace(/_$/, ''));
					if (tplEl.length) {
						var renderedField = $(DeskPRO_Window.util.getPlainTpl(tplEl));
						$('article', formItem).append(renderedField);
					}
				} else {
					$('.edit-field-trigger', formItem).hide();
				}

				if (draggingSidebarEl.is('.irremovable')) {
					$('.dp-block-controls li', formItem).not('.dp-move, .dp-edit-fields').remove();
				}
				if (draggingSidebarEl.data('edit-title')) {
					$('.dp-block-controls li.dp-edit-fields > span', formItem).text(draggingSidebarEl.data('edit-title')).data('edit-url', draggingSidebarEl.data('edit-url')).on('click', function(ev) {
						ev.preventDefault();
						ev.stopPropagation();
						window.location = $(this).data('edit-url');
					});
				} else {
					$('.dp-block-controls li.dp-edit-fields', formItem).remove();
				}

				if (!document.getElementById('criteria_tpl')) {
					$('.dp-block-controls li.edit-field-trigger', formItem).remove();
				}

				formItem.insertAfter(el);
                $('.Date.customfield input', formItem).datepicker();

				draggingSidebarEl.hide();
				el.remove();

				draggingSidebarEl = false;
			}
		}).on('change keypress', function() {
			$('#admin_chat_editor').addClass('changed');
		});


		//------------------------------
		// removing items
		//------------------------------

		this.el.on('click', '.remove-field-trigger', function() {
			var el = $(this).closest('li.form-item');
			var sidebarEl = el.data('sidebar-item');
			el.fadeOut('fast', function() {
				el.remove();
				sidebarEl.show();
				if (!$('#admin_chat_editor_items .form-item').length) {
					$('#admin_chat_editor_items .no-items-notice').show();
				}

				self.save();
			});

			$('#admin_chat_editor').addClass('changed');
			$('#admin_chat_editor_items .is-default-notice').hide();
		});

		this.redraw();
	},

	addItem: function(item_id) {

	},

	save: function() {

		if (this.currentSave) {
			this.currentSave.abort();
			this.currentSave = null;
		}

		$('#admin_chat_editor').removeClass('changed');

		var postData = this.encode();
		var saveUrl = this.el.attr('action');

		$('#saving_text').fadeIn('fast');
		this.currentSave = $.ajax({
			url: saveUrl,
			type: 'POST',
			data: postData,
			context: this,
			complete: function() {
				$('#saving_text').fadeOut('slow');
			}
		});
	},

	redraw: function() {
		var drawRendered = this.el.data('section') != 'view' ? true : false;

		function drawItem(item) {
			$('#admin_chat_editor_items .no-items-notice').hide();

			var draggingSidebarEl = $('li[data-item-id="' + item.id + '"]', '#chat_elements');
			if (!draggingSidebarEl.length) {
				return;
			}

			var formItem = $(DeskPRO_Window.util.getPlainTpl($('#editor_row_tpl')));
			formItem.data('item-id', draggingSidebarEl.data('item-id'));
			$('label.field-title', formItem).text($('label', draggingSidebarEl).text());
			if (draggingSidebarEl.data('is-agent-field')) {
				formItem.find('.field-agent-only').show();
			}

			formItem.data('sidebar-item', draggingSidebarEl);

			if (drawRendered) {
				var tplEl = $('#rendered_field_' + formItem.data('item-id').replace(/[^a-zA-Z0-9_\-]/g, '_').replace(/_$/, ''));
				if (tplEl.length) {
					var renderedField = $(DeskPRO_Window.util.getPlainTpl(tplEl));
					$('article', formItem).append(renderedField);
				}
			} else {
				$('.edit-field-trigger', formItem).hide();
			}

			if (draggingSidebarEl.is('.irremovable')) {
				$('.dp-block-controls li', formItem).not('.dp-move, .dp-edit-fields').remove();
			}
			if (draggingSidebarEl.data('edit-title')) {
				$('.dp-block-controls li.dp-edit-fields > span', formItem).text(draggingSidebarEl.data('edit-title')).on('click', function(ev) {
					ev.preventDefault();
					ev.stopPropagation();
					window.location = draggingSidebarEl.data('edit-url');
				});
			} else {
				$('.dp-block-controls li.dp-edit-fields', formItem).remove();
			}

			if (!document.getElementById('criteria_tpl')) {
				$('.dp-block-controls li.edit-field-trigger', formItem).remove();
			}

			$('#admin_chat_editor_items').append(formItem);
			draggingSidebarEl.hide();

            $('.Date.customfield input', formItem).datepicker();
		}

		if (CHAT_DISPLAY_DATA && CHAT_DISPLAY_DATA.length) {
			Array.each(CHAT_DISPLAY_DATA, function(item) {
				drawItem(item);
			});
		}

		$('.irremovable', '#chat_elements').each(function() {
			if ($(this).is(':visible')) {
				var item = {
					id: $(this).data('item-id')
				};

				drawItem(item);
			}
		})
	},

	encode: function() {
		var data = [];

		var items = $('li.form-item', '#admin_chat_editor_items');
		for (var x = 0; x < items.length; x++) {
			var el = items.eq(x);

			var baseKey = 'items[' + x + ']';

			data.push({ name: baseKey+'[id]', value: el.data('item-id') });

			if (el.data('options-id')) {
				var optionsEl = $('#' + el.data('options-id'));
			} else {
				var optionsEl = $('.field-options-overlay', el);
			}

			// Custom options
			if ($(':checkbox.custom_options', optionsEl).is(':checked')) {
				var customOptions = [];
				$('.custom-options select option:selected').each(function(index) {
					data.push({ name: baseKey+'[custom_options]['+index+']', value: $(this).val() });
				});
			}
		}

		return data;
	}
});
