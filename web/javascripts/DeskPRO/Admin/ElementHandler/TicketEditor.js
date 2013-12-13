Orb.createNamespace('DeskPRO.Admin.ElementHandler');

DeskPRO.Admin.ElementHandler.TicketEditor = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var self = this;

		var drawRendered = this.el.data('section') != 'view' ? true : false;

		window.TicketEditor = this;
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

		$('#alternative_view_toggle').on('change', function() {
			if ($(this).is(':checked')) {
				console.log('enable');
				var url = $(this).data('enable');
			} else {
				console.log('enable');
				var url = $(this).data('disable');
			}

			window.location = url;
		});

		//------------------------------
		// Handle dragging/dropping between the two lists
		//------------------------------

		var draggingSidebarEl = false;
		$('#ticket_elements li.draggable').draggable({
			connectToSortable: $('#admin_ticket_editor_items'),
			appendTo: 'body',
			containment: 'body',
			cursorAt: { left: 140, top: 15 },
			cursor: 'move',
			start: function() {
				$(this).css({ width: '280px'});
				$('#admin_ticket_editor').addClass('is-dragging');
			},
			stop: function() {
				$('#admin_ticket_editor').removeClass('is-dragging');
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

		$('#admin_ticket_editor_items').sortable({
			items: '> li.form-item',
			start: function() {
				$('#admin_ticket_editor').addClass('is-dragging');
			},
			stop: function() {
				$('#admin_ticket_editor').removeClass('is-dragging');
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

				$('#admin_ticket_editor').addClass('changed');

				$('#admin_ticket_editor_items .no-items-notice, #admin_ticket_editor_items .is-default-notice').hide();

				var formItem = $(DeskPRO_Window.util.getPlainTpl($('#editor_row_tpl')));
				formItem.data('item-id', el.data('item-id'));
				$('label.field-title', formItem).text($('label', el).text());
				if (el.data('is-agent-field')) {
					formItem.find('.field-agent-only').show().addClass('is-agent-field');
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
			$('#admin_ticket_editor').addClass('changed');
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
				if (!$('#admin_ticket_editor_items .form-item').length) {
					$('#admin_ticket_editor_items .no-items-notice').show();
				}

				self.save();
			});

			$('#admin_ticket_editor').addClass('changed');
			$('#admin_ticket_editor_items .is-default-notice').hide();
		});

		//------------------------------
		// Display options
		//------------------------------

		this.el.on('click', '.edit-field-trigger', function() {
			var el = $(this).closest('li.form-item');
			el.addClass('with-rendered-rules');
			var overlay = el.data('options-overlay');

			if (!overlay) {
				var overlayEl = $('.field-options-overlay', el);
				overlayEl.attr('id', Orb.getUniqueId());

				el.data('options-id', overlayEl.attr('id'));

				if (el.find('.field-agent-only').hasClass('is-agent-field')) {
					overlayEl.find('.agent_only_opt').hide();
					overlayEl.find('.not_agent_only_opt').show();
				} else {
					overlayEl.find('.not_agent_only_opt').hide();
					overlayEl.find('.agent_only_opt').show();
				}

				overlayEl.find('input.agent_only').on('click', function() {
					if (this.checked) {
						el.find('.field-agent-only').show();
					} else {
						el.find('.field-agent-only').hide();
					}
				});
				overlayEl.find('input.not_agent_only').on('click', function() {
					if (this.checked) {
						el.find('.field-agent-only').hide();
					} else {
						el.find('.field-agent-only').show();
					}
				});

				var tpl = $('<div></div>').addClass('.search-builder-tpl');
				tpl.html($('#criteria_tpl').html());

				var editor = new DeskPRO.Form.RuleBuilder(tpl);
				editor.addEvent('newRow', function(new_row) {
					$('.remove', new_row).on('click', function() {
						new_row.remove();
					});
				});
				var to_el = $('.criteria-form .search-terms', overlayEl);

				$('.criteria-form .add-term', overlayEl).data('add-count', 0).on('click', function() {
					var basename = 'terms_all['+Orb.uuid()+']';
					editor.addNewRow(to_el, basename);
				});

				if (el.data('rules')) {
					var terms = el.data('rules');
					el.data('terms', false);

					Array.each(terms, function(termitem) {
						var basename = 'terms_all['+Orb.uuid()+']';
						editor.addNewRow(to_el, basename, {
							type: termitem.type,
							op: termitem.op,
							options: termitem.options
						});
					});
				}

				overlay = new DeskPRO.UI.Overlay({
					contentElement: overlayEl,
					onClose: function() {
						self.save();
					}
				});
				el.data('options-overlay', overlay);

				var select = $('.custom-options select', el).first();
				if (select.length) {
					var select = select.clone();
					select.attr('multiple', 'multiple');
					select.css({width: '80%', maxHeight: '80px', minHeight: '30px'});
					$('option:not([value])', select).remove();

					var choiceOpts = $(DeskPRO_Window.util.getPlainTpl($('#field_options_choices_tpl')));
					var inputRow = $('.dp-form-input', choiceOpts).append(select);

					choiceOpts.appendTo($('.choices-container', overlayEl));

					$('input.custom_options', choiceOpts).on('click', function() {
						if (this.checked) {
							inputRow.slideDown();
						} else {
							inputRow.slideUp();
						}
					});
				}
			}

			overlay.open();
		});

		var embedOverlay = false;
		$('#embed_form_overlay_trigger').click(function(ev) {
			ev.preventDefault();

			if (!embedOverlay) {
				embedOverlay = new DeskPRO.UI.Overlay({
					contentMethod:'element',
					contentElement: $('#embed_form_overlay'),
					destroyOnClose: false
				});

				var url = $(this).data('load-url');
				hasLoadedEmbed = true;

				$.ajax({
					url: url,
					type: 'GET',
					dataType: 'html',
					success: function(html) {
						$('#embed_form_overlay').find('.overlay-content').html(html);
					}
				});
			}

			embedOverlay.open();
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

		$('#admin_ticket_editor').removeClass('changed');

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
			$('#admin_ticket_editor_items .no-items-notice').hide();

			var draggingSidebarEl = $('li[data-item-id="' + item.id + '"]', '#ticket_elements');
			if (!draggingSidebarEl.length) {
				return;
			}

			var formItem = $(DeskPRO_Window.util.getPlainTpl($('#editor_row_tpl')));
			formItem.data('item-id', draggingSidebarEl.data('item-id'));
			$('label.field-title', formItem).text($('label', draggingSidebarEl).text());
			if (draggingSidebarEl.data('is-agent-field')) {
				formItem.find('.field-agent-only').show().addClass('is-agent-field');
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

			$('#admin_ticket_editor_items').append(formItem);
			draggingSidebarEl.hide();

			// Add terms object to the element too, so they're redrawn when the overlay is opened
			if (item.rules) {
				if (item.rule_match_type) {
					$('select[name="term_match_type"]', formItem).val(item.rule_match_type);
				}
				formItem.data('item-data', item);
				formItem.data('rules', item.rules);
			}

			if (item.agent_only) {
				formItem.find('input.agent_only').prop('checked', true);
				formItem.find('.field-agent-only').show();
			}
			if (item.not_agent_only) {
				formItem.find('input.not_agent_only').prop('checked', true);
				formItem.find('.field-agent-only').hide();
			}

            $('.Date.customfield input', formItem).datepicker();
		}

		if (TICKET_DISPLAY_DATA && TICKET_DISPLAY_DATA.length) {
			Array.each(TICKET_DISPLAY_DATA, function(item) {
				drawItem(item);
			});
		}

		$('.irremovable', '#ticket_elements').each(function() {
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

		var items = $('li.form-item', '#admin_ticket_editor_items');
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

			// Rules
			if (el.hasClass('with-rendered-rules')) {
				var termRows = $('.search-terms .term', optionsEl);
				if (termRows.length) {
					data.push({ name: baseKey+'[rule_match_type]', value: $('select[name="term_match_type"]', optionsEl).val() });

					termRows.each(function(index) {
						var type = $('.builder-type-choice select', this).val();
						data.push({ name: baseKey+'[rules]['+index+'][type]', value: type });

						var op = $('.builder-op select', this).val();
						data.push({ name: baseKey+'[rules]['+index+'][op]', value: op });

						$('input, select, textarea', $('.builder-options', this)).each(function() {
							var name = $(this).attr('name');
							if (name) {
								name = name.replace(/^(.*)\[(.*?)\]$/, '$2');
								data.push({ name: baseKey+'[rules]['+index+'][options]['+name+']', value: $(this).val() });
							}
						});
					});
				}
			} else if (el.data('item-data')) {
				var item = el.data('item-data');

				if (item.rule_match_type) {
					data.push({ name: baseKey+'[rule_match_type]', value: item.rule_match_type });
				}
				if (item.agent_only) {
					data.push({ name: baseKey+'[agent_only]', value: 1 });
				}

				if (item.rules) {
					var rules = item.rules;

					var index = 0;
					Array.each(rules, function(termitem) {
						data.push({ name: baseKey+'[rules]['+index+'][type]', value: termitem.type });
						data.push({ name: baseKey+'[rules]['+index+'][op]', value: termitem.op });

						Object.each(termitem.options, function(v, k) {
							data.push({ name: baseKey+'[rules]['+index+'][options]['+k+']', value: v });
						});
						index++;
					});
				}
			}

			if (optionsEl.find('input.agent_only').is(':checked')) {
				data.push({ name: baseKey+'[agent_only]', value: 1 });
			} else if (optionsEl.find('input.not_agent_only').is(':checked')) {
				data.push({ name: baseKey+'[not_agent_only]', value: 1 });
			}
		}

		return data;
	}
});
