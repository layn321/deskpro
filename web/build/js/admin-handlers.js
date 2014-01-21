Orb.createNamespace('DeskPRO.Admin.Departments');

DeskPRO.Admin.Departments.AjaxSave = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	initPage: function() {
		var self = this;
		this.el.on('change', '.set-tickets-state, .set-chat-state', function(ev) {
			var tr = $(this).closest('.dp-grid-row');
			self.saveFeatureState(tr.data('department-id'));
		});

		this.el.on('click', 'a.edit-trigger', function(ev) {
			ev.preventDefault();

			var row = $(this).closest('article.dp-grid-row');
			self.showEditDep(row);
		});

		$('#newdep_open').on('click', function(ev) {
			ev.preventDefault();
			self.showNewDep();
		});

		// Reordering parents
		var list = $('.dep-list');
		list.sortable({
			items: '.department-group',
			handle: 'article.top',
			update: function() {
				self.updateOrders();
			}
		});
		$('.department-group', list).each(function() {
			var group = $(this);
			group.sortable({
				items: 'article.child',
				update: function() {
					self.updateOrders();
				}
			});
		});

		this.notDisabledOverlay = new DeskPRO.UI.Overlay({
			contentElement: '#dep_not_disabled_overlay'
		});

		$('#dep_not_disabled_overlay').find('.overlay-close-trigger').on('click', function() {
			self.notDisabledOverlay.close();
		});
	},

	registerChildHandler: function(handler, handlerName, el) {
		switch (handlerName) {
			case 'agent_selector':
				handler.addEvent('updated', this.saveAgentPermissions.bind(this));
				break;
		}
	},

	saveFeatureState: function(department_id) {

		if (this.isChecking) return;
		this.isChecking = true;

		var tr = $('article.department-' + department_id, this.el);

		var chat = $(':checkbox.set-chat-state', tr).is(':checked') ? 1 : 0;
		var tickets = $(':checkbox.set-tickets-state', tr).is(':checked') ? 1 : 0;

		var countChat    = $(':checkbox.set-chat-state').filter(':checked').length;
		var countTickets = $(':checkbox.set-tickets-state').filter(':checked').length;

		this.isChecking = false;

		var showAlert = false;
		if (!countChat) {
			chat = 1;
			showAlert = true;
			$(':checkbox.set-chat-state', tr).prop('checked', true);
		}
		if (!countTickets) {
			tickets = 1;
			showAlert = true;
			$(':checkbox.set-tickets-state', tr).prop('checked', true);
		}

		if (showAlert) {
			this.notDisabledOverlay.open();
			return;
		}

		if (chat) {
			$('button.label-chat-perms', tr).show();
		} else {
			$('button.label-chat-perms', tr).hide();
		}

		if (tickets) {
			$('button.label-tickets-perms', tr).show();
		} else {
			$('button.label-tickets-perms', tr).hide();
		}

		var postData = [];
		postData.push({
			name: 'chat',
			value: chat
		});
		postData.push({
			name: 'tickets',
			value: tickets
		});

		var url = BASE_URL + 'admin/departments/' + department_id + '/save-feature-state.json';

		$.ajax({
			url: url,
			type: 'POST',
			dataType: 'json',
			data: postData
		});
	},

	saveAgentPermissions: function(department_id, app, agents) {
		var url = BASE_URL + 'admin/departments/' + department_id + '/save-agents.json';

		var postData = [];
		postData.push({name: 'app', value: app});

		for (var i in agents) {
			for (var j = 0; j < agents[i].length; j++) {
				postData.push({
					name: 'agents[' + i + '][]',
					value: agents[i][j]
				});
			}
		}

		$.ajax({
			url: url,
			type: 'POST',
			dataType: 'json',
			data: postData
		});
	},

	showEditDep: function(row) {
		var self = this;
		var depId = row.data('department-id');
		var currentTitle = row.find('a.edit-trigger').text().trim();
		var currentUserTitle = row.find('a.edit-trigger').data('user-title');

		/**
		 * The Other Guys
		 * #20140115 @ Frankie -- Load currentRate variable
		 */
		var currentRate = row.find('a.edit-trigger').data('rate');

		var currentGatewayId = parseInt(row.find('a.edit-trigger').data('linked-gateway-id'));
		var currentGatewayTitle = row.find('a.edit-trigger').data('linked-gateway-title');

		this.currentEditDep = depId;
		$('#editdep_title').val(currentTitle);
		$('#editdep_user_title').val(currentUserTitle);

		/**
		 * The Other Guys
		 * Fill in the department rate textbox
		 * #201401200001 @ Frankie -- Added parseFloat function to render trailing zeros
		 */
		$('#editdep_rate').val(parseFloat(currentRate).toFixed(2));

		$('#editdep_depid').val(depId);

		if (!this.editDepParentOpt) {
			this.editDepParentOpt = $('#editcat_parent_id').find('option').clone();
		}

		$('#editcat_parent_id').empty();
		$('#editcat_parent_id').append(this.editDepParentOpt.clone());
		$('#editcat_parent_id').find('option[value="'+depId+'"]').remove();

		var group = row.closest('.department-group');
		var parentId = group.find('> article.top').data('department-id');
		if (parentId != depId) {
			$('#editcat_parent_row').show();
			$('#editcat_parent_id').find('option[value="'+parentId+'"]').prop('selected', true);
			
			/**
			* The Other Guys
			* #20140119 @Frankie -- Show the rate textbox when the department has no child departments
			*/
			$('#editcat_rate_row').show();

		} else {
			if (group.find('> article.child')[0]) {
				$('#editcat_parent_row').hide();
				$('#editcat_parent_id').find('option[value="0"]').prop('selected', true);
				
				/**
				 * The Other Guys
				 * #20140119 @Frankie -- Hide the rate textbox when the department is a parent department
				 */
			    $('#editcat_rate_row').hide();

			} else {
				$('#editcat_parent_row').show();
				$('#editcat_parent_id').find('option[value="0"]').prop('selected', true);
				
				/**
				 * The Other Guys
				 * #20140119 @Frankie -- Show the rate textbox when the department is a child department
				 */
			    $('#editcat_rate_row').show();

			}
		}

		if (!this.editOverlay) {
			this.editOverlay = new DeskPRO.UI.Overlay({
				contentElement: $('#editdep_overlay')
			});
			$('#editdep_savebtn').on('click', function(ev) {
				ev.preventDefault();
				$('#editdep_overlay form').submit();
			});
		}

		var gatesel = $('#editdep_gateway_account_id');
		gatesel.find('option.current').remove();
		if (currentGatewayId) {
			var opt = $('<option />');
			opt.text(currentGatewayTitle);
			opt.val(currentGatewayId);
			opt.addClass('current');

			opt.prependTo(gatesel);
			gatesel.closest('.dp-form-row').show();
		} else {
			if (!gatesel.find('option')[0]) {
				gatesel.closest('.dp-form-row').hide();
			} else {
				gatesel.closest('.dp-form-row').show();
			}
		}

		gatesel.find('option').first().prop('selected', true);

		this.editOverlay.open();
	},

	showNewDep: function() {
		var self = this;

		if (!this.newOverlay) {
			this.newOverlay = new DeskPRO.UI.Overlay({
				contentElement: $('#newdep_overlay')
			});
			$('#newdep_savebtn').on('click', function(ev) {
				ev.preventDefault();
				$('#newdep_overlay form').submit();
			});
		}

		this.newOverlay.open();
	},

	updateOrders: function() {
		var postData = [];
		$('article.dp-grid-row[data-department-id]', this.el).each(function() {
			if ($(this).data('department-id')) {
				postData.push({
					name: 'display_order[]',
					value: $(this).data('department-id')
				});
			}
		});

		$.ajax({
			url: this.el.data('reorder-url'),
			type: 'POST',
			data: postData
		});

		// Reset last class, its needed for proper borders
		$('.department-group').each(function() {
			$(this).find('article.dp-grid-row').removeClass('last').last().addClass('last');
		});
	}
});

Orb.createNamespace('DeskPRO.Admin.Departments');

DeskPRO.Admin.Departments.AgentSelector = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	initPage: function() {
		var self = this;

		this.department_id = this.el.data('department-id');
		var btn = this.el;

		this.optionbox = new DeskPRO.UI.OptionBox({
			element: $('#optionbox_dep_' + this.department_id + '_' + self.el.data('app')),
			trigger: this.el,
			onClose: function(optionbox) {
				var countAgents = optionbox.getCount('agents');

				var words = [];
				if (countAgents > 0) {
					words.push(countAgents + ' agents');
				}

				if (!countAgents) {
					words = ['No agents'];
				}

				btn.text(words.join(', '));

				var agents = {};

				var selected = optionbox.getSelected('agents');
				for (var i = 0; i < selected.length; i++) {
					if (!agents[selected[i]]) {
						agents[selected[i]] = [];
					}
					agents[selected[i]].push('full');
				}

				selected = optionbox.getSelected('agents_assign');
				for (var i = 0; i < selected.length; i++) {
					if (!agents[selected[i]]) {
						agents[selected[i]] = [];
					}
					agents[selected[i]].push('assign');
				}

				self.fireEvent('updated', [self.department_id, self.el.data('app'), agents, self]);
			}
		});
	},

	getHandlerName: function() {
		return 'agent_selector';
	}
});

Orb.createNamespace('DeskPRO.Admin.Departments');

DeskPRO.Admin.Departments.UsergroupSelector = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	initPage: function() {
		var self = this;

		this.department_id = this.el.data('department-id');
		var btn = this.el;

		this.optionbox = new DeskPRO.UI.OptionBox({
			element: $('#optionbox_ug_' + this.department_id),
			trigger: this.el,
			onClose: function(optionbox) {
				var count = optionbox.getCount('usergroups');

				if (count > 0) {
					btn.text(count + ' usergroups');
				} else {
					btn.text('No usergroups');
				}

				self.fireEvent('updated', [self.department_id, optionbox.getSelected('usergroups'), self]);
			}
		});
	},

	getHandlerName: function() {
		return 'usergroup_selector';
	}
});

Orb.createNamespace('DeskPRO.Admin.ElementHandler');

DeskPRO.Admin.ElementHandler.HeaderSetupGuide = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var self = this;
	},

	initPage: function() {
		var self = this;
	}
});

Orb.createNamespace('DeskPRO.Admin.ElementHandler');

DeskPRO.Admin.ElementHandler.TicketPropertiesList = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var self = this;

		$(':checkbox[data-setting-name], :checkbox[data-enable-url]', this.el).on('change', function(ev) {
			var me = $(this);
			var val = me.is(':checked') ? 1 : 0;
			var mode, url;

			if (val && me.data('enable-url')) {
				url = me.data('enable-url');
				mode = 'redirect';
			} else if (!val && me.data('disable-url')) {
				url = me.data('disable-url');
				mode = 'redirect';
			} else {
				url = self.el.data('set-setting-url').replace(/_SETTING_NAME_/g, $(this).data('setting-name'));
				mode = 'ajax';
			}

			if (mode == 'redirect') {
				url = Orb.appendQueryData(url, 'return', window.location.href);
				console.log(url);
				window.location.href = url;
			} else if (mode == 'ajax') {
				$.ajax({
					url: url,
					type: 'POST',
					data: { value: val },
					dataType: 'json'
				});
			}
		});

		$(':checkbox[data-field-id]', this.el).on('change', function() {
			var val = $(this).is(':checked') ? 1 : 0;
			var url = self.el.data('set-field-url').replace(/_FIELD_ID_/g, $(this).data('field-id'));

			$.ajax({
				url: url,
				type: 'POST',
				data: { is_enabled: val },
				dataType: 'json'
			});
		});
	}
});

Orb.createNamespace('DeskPRO.Admin.ElementHandler');

DeskPRO.Admin.ElementHandler.TaskQueueStatus = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var self = this;
		var el = this.el;

		var taskId = el.data('task-id');
		var taskGroup = el.data('task-group');

		var interval;

		var updateStatus = function() {
			var url;
			if (taskGroup) {
				url = BASE_URL + 'admin/misc/check-task/group/' + taskGroup;
			} else if (taskId) {
				url = BASE_URL + 'admin/misc/check-task/' + taskId;
			} else {
				url = BASE_URL + 'admin/misc/check-task/';
			}

			$.ajax({
				url: url,
				type: 'POST',
				dataType: 'json',
				success: function(data) {
					if (taskId) {
						if (!data || !data.exists) {
							el.text('Task could not be found.');
							//clearInterval(interval);
							return;
						}

						if (data.status == 'completed' || data.status == 'errored') {
							//clearInterval(interval);
						}
					} else {
						if (!data || !data.count) {
							el.text('All tasks completed!');
							//clearInterval(interval);
							return;
						}
					}

					var text = (data.title ? data.title + ' - ' : '');

					if (data.status == 'running') {
						text += 'Running... ' + data.run_status;
					} else if (data.status == 'completed') {
						text += 'Completed! (' + data.run_status + ')';
					} else if (data.status == 'errored') {
						text += 'An error occurred: ' + data.error_text;
					} else {
						text += 'Waiting to start...';

						if (data.count_waiting) {
							if (data.count_waiting == 1) {
								text += ' (1 other task to complete)';
							} else {
								text += ' (' + data.count_waiting + ' other tasks to complete)';
							}
						}
					}

					if (data.count > 1 && !data.count_waiting) {
						if (data.count == 2) {
							text += ' (1 additional task pending)';
						} else {
							text += ' (' + (data.count - 1) + ' additional tasks pending)';
						}
					}

					el.text(text);
				}
			});
		};

		updateStatus();
		interval = setInterval(updateStatus, 10000);
	}
});

Orb.createNamespace('DeskPRO.Admin.ElementHandler');

DeskPRO.Admin.ElementHandler.CustomFieldList = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var self = this;

		$(':checkbox[data-field-id]', this.el).on('change', function() {
			var val = $(this).is(':checked') ? 1 : 0;
			var url = self.el.data('set-field-url').replace(/_FIELD_ID_/g, $(this).data('field-id'));

			$.ajax({
				url: url,
				type: 'POST',
				data: { is_enabled: val },
				dataType: 'json'
			});
		});
	}
});

Orb.createNamespace('DeskPRO.Admin.ElementHandler');

DeskPRO.Admin.ElementHandler.ChoiceBuilder = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var self = this;

		var el = this.el;
		var rowTpl = $('.row-tpl', el).get(0).innerHTML;
		var list = $('ul.list', el);
		var newInput = $('input.new-choice', el);
		var addNewBtn = $('.add-trigger', el);

		function handleRemoveClick(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			var li = $(this).closest('li.item');
			var choiceVal = $('div.label', li).text().trim();

			var found = false;
			$('input.move-val', '#choice_extra_fields').each(function() {
				if ($(this).val() == choiceVal) {
					found = true;
					return false;
				}
			});

			if (found) {
				alert('You cannot delete this option because in a previously deleted option you chose to move existing values to this one. You can refresh the page if you need to start over.');
				return;
			}

			var choiceId = li.data('choice-id');

			if (choiceId) {

				var overlayEl = $(DeskPRO_Window.util.getPlainTpl($('#move_options_overlay')));
				var sel = $('select.move-value', overlayEl);
				sel.append('<option>Remove existing selections</option>');
				$('div.label', list).each(function() {
					if ($(this).closest('li').data('choice-id') == choiceId) {
						return;
					}

					var opt = $('<option />');
					opt.val($(this).text().trim());
					opt.text('Move to: ' + opt.val());

					opt.appendTo(sel);
				});

				$('button.save-trigger', overlayEl).click(function() {
					if (sel.val().length) {
						var hidden = $('<input type="hidden" class="move-val" name="move[' + choiceId + ']" />');
						hidden.val(sel.val());

						hidden.appendTo($('#choice_extra_fields'));
					}

					overlay.close();

					li.fadeOut('fast', function() {
						li.remove();
					});
				});

				var overlay = new DeskPRO.UI.Overlay({
					contentElement: overlayEl,
					destroyOnClose: true
				});
				overlay.open();

			} else {
				li.fadeOut('fast', function() {
					li.remove();
				});
			}
		}

		function handleAdd() {
			var label = newInput.val().trim();
			newInput.val('');

			var newRow = $(rowTpl);
			$('.label', newRow).text(label);
			var i = list.find('li').length;
			$('.row-value', newRow).val('new.'+i+ ':' + label).data('order', i);

			list.append(newRow);
		}

		function handleAddClick(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			handleAdd();
		}

		function handleRename(ev) {
			var label = $(this);
			var row = label.closest('li.item');
			var rowValue = $('input.row-value', row);

			var input = $('<input type="text" class="rename" value="" />');
			input.val(label.text().trim());
			input.hide();
			input.insertAfter(label);

			label.fadeOut('fast', function(){
				input.fadeIn('fast', function() {
					input.focus();
				});
			});

			input.on('blur', function() {
				label.text(input.val());
				if (row.is('.new')) {
					rowValue.val('new.' + rowValue.data('order') + ':' + input.val().trim());
				} else {
					rowValue.val('exist.' + rowValue.data('order') + ':' + row.data('choice-id') + ':' + input.val().trim());
				}

				input.fadeOut('fast', function() {
					input.remove();
					label.fadeIn('fast');
				});
			});
		}

		newInput.on('keypress', function(ev) {
			if (ev.keyCode == 13) {
				ev.preventDefault();//dont enter enter key
				handleAdd();
			}
		});
		addNewBtn.on('click', handleAddClick);
		list.on('click', '.remove', handleRemoveClick);
		list.on('dblclick', '.label', handleRename);

		$(list).sortable({
			axis: 'y',
			handle: '.drag',
			items: '> li',
			start: function() {
				list.addClass('dragging');
			},
			stop: function() {
				list.removeClass('dragging');
			},
			update: function() {
				list.find('input.row-value').each(function(i) {
					$(this).data('order', i).val($(this).val().replace(/^(new|exist)\.([0-9]+):/, "$1." + i + ":"));
				});
			}
		});
	}
});

Orb.createNamespace('DeskPRO.Admin.ElementHandler');

DeskPRO.Admin.ElementHandler.SimpleHierarchyBuilder = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var self = this;
		var el            = this.el;
		var btnTrigger    = el.find('.edit-choices-trigger');
		var resultContain = el.find('.choices-list');

		var editorEl     = $('#define_choices_overlay');
		var builderList   = editorEl.find('ul.options-list');
		var newOptTitle   = editorEl.find('input.new-option-title');
		var newOptParent  = editorEl.find('select.new-option-parent');
		var structureHold = el.find('input.choices_structure');
		var structureDel  = el.find('input.choices_removed');

		var removedList = [];
		var addedList   = [];

		var withDefaultSelect = this.el.data('with-default-select');
		var defaultSelectWrap = this.el.find('.default-select');
		var defaultSelect     = this.el.find('.default-select select');

		var noUpdateParentSelect = true;
		var isInline = parseInt(this.el.data('inline'));

		var overlay       = new DeskPRO.UI.Overlay({
			triggerElement: btnTrigger,
			contentElement: editorEl,
			onClose: function() {
				exportData();
			}
		});

		function exportData() {
			var data = [];

			builderList.find('> li').each(function() {
				data.push({
					id: $(this).data('option-id'),
					title: $(this).data('option-title'),
					parent_id: 0,
					hasChildren: false
				});
				var parent_idx = data.length - 1;
				var parent_id = $(this).data('option-id');
				$(this).find('li').each(function() {
					data.push({
						id: $(this).data('option-id'),
						title: $(this).data('option-title'),
						parent_id: parent_id
					});
					data[parent_idx].hasChildren = true;
				});
			});

			var list = builderList.clone();
			list.find('i').remove();
			resultContain.empty().append(list);

			structureHold.val(JSON.stringify(data));
			structureDel.val(JSON.stringify(removedList));

			if (withDefaultSelect) {
				var previousDefault   = defaultSelect.find(':selected').text();
				defaultSelect.empty();
				defaultSelect.append('<option></option>');

				Array.each(data, function(opt) {
					if (opt.hasChildren) {
						var og = $('<optgroup />');
						og.attr('label', opt.title);

						Array.each(data, function(sub_opt) {
							if (sub_opt.parent_id == opt.id) {
								var o = $('<option />');
								o.attr('value', sub_opt.id);
								o.text(sub_opt.title);

								if (sub_opt.title == previousDefault) {
									o.attr('selected', true);
								}

								og.append(o);
							}
						});

						defaultSelect.append(og);
					} else if (!opt.parent_id) {
						var o = $('<option />');
						o.attr('value', opt.id);
						o.text(opt.title);

						if (opt.title == previousDefault) {
							o.attr('selected', true);
						}

						defaultSelect.append(o);
					}
				});

				if (defaultSelect.find('option').length > 1) {
					defaultSelectWrap.show();

					if (!previousDefault && defaultSelect.data('current-default')) {
						defaultSelect.find('option[value="' + defaultSelect.data('current-default') + '"]').prop('selected', true);
						defaultSelect.data('current-default', null);
					}
				} else {
					defaultSelectWrap.hide();
				}
			}
		};

		newOptTitle.on('keypress', function(ev) {
			// Enter key
			if (ev.keyCode == 13 && !ev.metaKey) {
				ev.preventDefault();
				editorEl.find('.new-option-trigger').click();
			}
		});

		editorEl.find('.new-option-trigger').on('click', function(ev) {
			ev.preventDefault();
			var title = $.trim(newOptTitle.val());
			var parentId = newOptParent.val() || false;

			newOptTitle.val('');

			if (title === "") {
				return;
			}

			addOption(title, parentId);
		});

		builderList.on('click', '.remove-trigger', function() {
			var row = $(this).closest('li.option');
			removeRow(row);
		});

		if (structureHold.val()) {

			var countsEl = el.find('input.choices_counts');
			if (countsEl.val()) {
				var counts = $.parseJSON(countsEl.val());
			} else {
				var counts = {};
			}

			try {
				console.log(structureHold.val());
				var data = eval(structureHold.val());
			} catch (e) {
				console.log(e);
				var data = [];
			}
			if (data && data.length) {
				for (var i = 0; i < data.length; i++) {
					var opt = data[i];
					if (!opt.parent_id) {
						addOption(opt.title, 0, opt.id, counts[parseInt(opt.id)]);
					}
				}
				for (var i = 0; i < data.length; i++) {
					var opt = data[i];
					if (opt.parent_id) {
						addOption(opt.title, opt.parent_id, opt.id, counts[parseInt(opt.id)]);
					}
				}
			}
		}

		editorEl.find('.save-trigger').on('click', function() {
			overlay.close();
		});

		configureSortable(builderList);
		noUpdateParentSelect = false;
		updateParentSelect();

		var list = builderList.clone();
		list.find('i').remove();
		resultContain.empty().append(list);

		function configureSortable(list) {
			list.sortable({
				items: '> li.option',
				handle: 'i.move-grip',
				axis: 'y'
			});
		};

		function generateOptionId() {
			return Orb.uuid();
		};

		function updateParentSelect() {
			if (noUpdateParentSelect) return;

			newOptParent.empty().hide();
			var lis = builderList.find('> li');

			if (lis.length) {
				var opt = $('<option/>');
				opt.val('');
				opt.text('No Parent');
				newOptParent.append(opt);

				lis.each(function() {
					var opt = $('<option/>');
					opt.val($(this).data('option-id'));
					opt.text($(this).data('option-title'));
					newOptParent.append(opt);
				});

				newOptParent.show();
			}

			exportData();
		};

		function removeRow(row) {
			var id = row.data('option-id');
			removedList.push(id);

			row.find('li.option').each(function() {
				removedList.push($(this).data('option-id'));
			});

			row.remove();

			updateParentSelect();

			if (isInline) exportData();
		};

		function addOption(title, parentId, id, count) {
			if (parentId) {
				var parent = editorEl.find('.option-' + parentId).find('ul');
			} else {
				var parent = editorEl.find('.options-list');
			}

			var li = $('<li/>');
			if (!id) {
				id = generateOptionId();
			}
			li.addClass('option-' + id)
				.addClass('option')
				.data('option-id', id)
				.data('option-title', title);

			if (id) {
				$('<span class="field-id" style="float:right">ID: ' + id + '</span>').appendTo(li);
			}

			var label = $('<div class="label">').text(title);
			$('<i class="remove-trigger"></i><i class="move-grip"></i>').appendTo(label);

			label.appendTo(li);

			if (!parentId) {
				var list = $('<ul />');
				list.appendTo(li);
				configureSortable(list);
			}

			parent.append(li);
			updateParentSelect();

			if (isInline) exportData();
		};
	}
});
Orb.createNamespace('DeskPRO.Admin.ElementHandler');

DeskPRO.Admin.ElementHandler.PortalNav = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var self = this;

		var toggles = $('header :checkbox', this.el);
		toggles.on('change', function() {
			var checked = $(this).is(':checked');
			var section = $(this).closest('section');

			if (checked) {
				$('article', section).slideDown('fast');
			} else {
				$('article', section).slideUp('fast');
			}
		});
	}
});

Orb.createNamespace('DeskPRO.Admin.ElementHandler');

DeskPRO.Admin.ElementHandler.PortalToggle = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var check = $(':checkbox', this.el);
		check.on('change', function() {
			if ($(this).is(':checked')) {
				$('#portal_nav').removeClass('disabled');
			} else {
				$('#portal_nav').addClass('disabled');
			}
		});
	}
});

Orb.createNamespace('DeskPRO.Admin.ElementHandler');

/**
 * The portal editor is made of up the admin page, and then a specially
 * loaded page in the user interface loaded through an iframe.
 *
 * We call the admin page the PortalEditor, and the user page the PortalClient.
 *
 * Messages, like click events that need an editor, are handled byt he PortalClient
 * and are pssed up to this PortalEditor which takes care of opening editors and saving
 * data. Then in some cases, data is passed back down to the PortalClient to update
 * the live display.
 *
 * Generally: PortalEditor handles saving/changing of data, PortalClient handles displaying data
 * and interaction with the UI.
 */
DeskPRO.Admin.ElementHandler.PortalEditor = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var self = this;

		// This is so userland can send us messages
		window.PortalEditor = this;

		$('#portal_iframe').attr('src', this.el.data('portal-url'));

		$(':checkbox.section-toggle').on('change', function() {
			var type = $(this).attr('name');

			if ($(this).is(':checked')) {
				self.tellPortal('app_enabled', {name: type});
			} else {
				self.tellPortal('app_disabled', {name: type});
			}
		});

		this._initColorPicker();
		$('#portal_iframe').css('opacity', '0');
	},

	/**
	 * Send a message to the portal client
	 *
	 * @param id
	 * @param data
	 */
	tellPortal: function(id, data) {
		this.iframeWindow.PortalAdmin.acceptMessage(id, data);
	},


	/**
	 * Call a method on the portal client
	 *
	 * @param id
	 * @param data
	 */
	callPortal: function(id, data) {
		return this.iframeWindow.PortalAdmin[id](data);
	},


	/**
	 * Accepts a message passed from the portal client
	 *
	 * @param id
	 * @param data
	 */
	acceptMessage: function(id, data) {

		var self = this;

		data = data || {};
		DP.console.log("New message: %s %o", id, data);

		switch (id) {
			case 'loaded':
				this.iframeLoaded(data.height);
				break;
			case 'update_height':
				this.updateHeight(data.height);
				break;
			case 'switch_page':
				window.location = BASE_URL + 'admin/portal?portal_path=' + encodeURI(data.path);
				break;
			case 'enable_logo_area':
				$.ajax({
					type: 'POST',
					url: BASE_URL + 'admin/portal/save-editor/enable_logo_area',
					type: 'POST'
				});
				break;
			case 'disable_logo_area':
				$.ajax({
					type: 'POST',
					url: BASE_URL + 'admin/portal/save-editor/disable_logo_area',
					type: 'POST'
				});
				break;
			case 'open_placeholder_editor':
				var controller = data.controller;

				this.showHtmlEditor(controller.name, function(action) {
					switch (action) {
						case 'update': controller.update(); break;
						case 'reset': controller.reset(); break;
					}
				});
				break;
			case 'reset_placeholder':
				var controller = data.controller;
				var template_name;
				if (controller.name == 'header') {
					template_name = 'UserBundle::custom-header.html.twig';
				} else if (controller.name == 'head_include') {
					template_name = 'UserBundle::custom-headinclude.html.twig';
				} else if (controller.name == 'welcome') {
					template_name = 'UserBundle:Portal:welcome-block.html.twig';
				} else {
					template_name = 'UserBundle::custom-footer.html.twig';
				}

				$.ajax({
					type: 'POST',
					url: BASE_URL + 'admin/templates/revert-template.json?name=' + template_name
				});

				break;
			case 'update_orders':
				var ids = data.orderedIds;

				var postData = [];
				for (var i = 0; i < ids.length; i++) {
					postData.push({
						name: 'display_order[]',
						value: ids[i]
					});
				}

				$.ajax({
					url: this.el.data('url-update-orders'),
					type: 'POST',
					dataType: 'json',
					data: postData
				});

				break;
			case 'block_toggled':

				var url = this.el.data('url-block-toggle').replace(/_PID_/g, data.pid);
				var postData = {
					enabled: data.enabled ? 1 : 0
				};

				$.ajax({
					url: url,
					type: 'POST',
					dataType: 'json',
					data: postData
				});

				break;
			case 'toggle_tab':

				var tabName = data.tabName;
				var on = data.on ? 1 : 0;

				$.ajax({
					url: BASE_URL + 'admin/portal/save-editor/toggle_tab',
					type: 'POST',
					data: {
						tab: tabName,
						on: on
					}
				});
				break;
			case 'reorder_tabs':
				var order = data.order;

				var postData = [];
				for (var i = 0; i < order.length; i++) {
					postData.push({
						name: 'display_order[]',
						value: order[i]
					});
				}

				$.ajax({
					url: BASE_URL + 'admin/portal/save-editor/reorder_tabs',
					type: 'POST',
					dataType: 'json',
					data: postData
				});

				break;
			case 'open_logo_editor':
				var controller = data.controller;
				var overlay = new DeskPRO.UI.Overlay({
					contentMethod: 'ajax',
					destroyOnClose: true,
					contentAjax: {
						url: BASE_URL + 'admin/portal/get-editor/logo'
					},
					onContentSet: function(ev) {
						var wrapper = ev.wrapperEl;

						var idbase = 'ed_' + Orb.uuidRand();
						$('.template-upload', wrapper).attr('id', idbase + 'up')
						$('.template-download', wrapper).attr('id', idbase + 'down')
						wrapper.fileupload({
							url: BASE_URL + 'admin/misc/accept-upload',
							dropZone: wrapper,
							autoUpload: true,
							uploadTemplateId: idbase + 'up',
							downloadTemplateId: idbase + 'down'
						}).bind('fileuploadstart', function() {
							$('p.explain', wrapper).hide();
						}).bind('fileuploadadd', function() {
							$('.files', wrapper).empty();
						});

						$('.save-logo-trigger', wrapper).on('click', function() {
							var url = $('input.new_logo_url', wrapper).val();
							if (!url) {
								alert('You need to upload an image');
								return;
							}

							controller.setLogo(url);

							$.ajax({
								url: BASE_URL + 'admin/portal/save-editor/header_logo',
								type: 'POST',
								data: {
									blob_authid: wrapper.find('input.new_blob_auth_id').val()
								}
							});

							ev.overlay.close();
						});

						$('.save-text-trigger').on('click', function() {
							controller.setLogoText($('input[name="title"]', wrapper).val(), $('input[name="tagline"]', wrapper).val());

							$.ajax({
								url: BASE_URL + 'admin/portal/save-editor/header_title',
								type: 'POST',
								data: {
									title: wrapper.find('input.title').val(),
									tagline: wrapper.find('input.tagline').val()
								}
							});

							ev.overlay.close();
						});
					}
				});
				overlay.open();
				break;
			case 'open_portal_title':
				var controller = data.controller;
				var overlay = new DeskPRO.UI.Overlay({
					contentMethod: 'ajax',
					destroyOnClose: true,
					contentAjax: {
						url: BASE_URL + 'admin/portal/get-editor/portal-title'
					},
					onContentSet: function(ev) {
						var wrapper = ev.wrapperEl;
						$('.save-text-trigger').on('click', function() {
							controller.setTitle($('input[name="title"]', wrapper).val());

							$.ajax({
								url: BASE_URL + 'admin/portal/save-editor/portal_title',
								type: 'POST',
								data: {
									title: wrapper.find('input.title').val()
								}
							});

							ev.overlay.close();
						});
					}
				});
				overlay.open();
				break;

			case 'edit_twitter_sidebar_block':
				var controller = data.controller;
				var overlay = new DeskPRO.UI.Overlay({
					contentMethod: 'ajax',
					destroyOnClose: true,
					contentAjax: {
						url: BASE_URL + 'admin/portal/get-editor/twitter-sidebar'
					},
					onContentSet: function(ev) {
						var wrapper = ev.wrapperEl;
						$('.save-trigger').on('click', function() {
							$.ajax({
								url: BASE_URL + 'admin/portal/save-editor/twitter_sidebar',
								type: 'POST',
								data: {
									twitter_name: wrapper.find('input.twitter_name').val(),
									max_items: wrapper.find('input.max_items').val()
								}
							});

							ev.overlay.close();

							controller.update()
						});
					}
				});
				overlay.open();
				break;

			case 'new_sidebar_block':
				this.showHtmlEditor('NEW_SIDEBAR_BLOCK', function(action, data) {
					switch (action) {
						case 'update': self.tellPortal('new_sidebar_block', {
							pid: data.pid
						});
					}
				});
				break;

			case 'edit_template_block':
				var controller = data.controller;
				this.showHtmlEditor('EDIT_SIDEBAR_BLOCK:' + data.pid, function(action, data) {
					switch (action) {
						case 'update': controller.update();
						break;
					}
				});
				break;

			case 'new_sidebar_block_simple':
				this.showHtmlEditorSimple(0, function(action, data) {
					switch (action) {
						case 'update': self.tellPortal('new_sidebar_block_simple', {
							pid: data.pid
						});
					}
				});
				break;

			case 'edit_sidebar_block_simple':
				var controller = data.controller;
				this.showHtmlEditorSimple(data.pid, function(action, data) {
					switch (action) {
						case 'update': controller.update();
						break;
					}
				});
				break;

			case 'delete_sidebar_block_simple':
				var controller = data.controller;
				var el = controller.getEl();
				el.hide();

				$.ajax({
					type: 'POST',
					url: BASE_URL + 'admin/portal/sideblock-simple/'+data.pid+'/delete.json',
					error: function() {
						el.show();
					},
					success: function() {
						controller.remove();
					}
				});
				break;

			case 'delete_template_block':
				var controller = data.controller;
				var el = controller.getEl();
				el.hide();

				$.ajax({
					type: 'POST',
					url: BASE_URL + 'admin/portal/blocks/' + data.pid + '/delete-template-block.json',
					error: function() {
						el.show();
					},
					success: function() {
						controller.remove();
					}
				});
				break;
		}
	},


	/**
	 * Shows a generic HTML editor
	 *
	 * @param callback
	 */
	showHtmlEditor: function(name, callback) {
		var el = $(DeskPRO_Window.util.getPlainTpl($('#admin_portal_block_html_edit_tpl')));

		var template_name;
		if (name == 'header') {
			template_name = 'UserBundle::custom-header.html.twig';
		} else if (name == 'head_include') {
			template_name = 'UserBundle::custom-headinclude.html.twig';
		} else if (name == 'welcome') {
			template_name = 'UserBundle:Portal:welcome-block.html.twig';
		} else if (name == 'articles_header') {
			template_name = 'UserBundle:Articles:section-header.html.twig';
		} else if (name == 'downloads_header') {
			template_name = 'UserBundle:Downloads:section-header.html.twig';
		} else if (name == 'feedback_header') {
			template_name = 'UserBundle:Feedback:section-header.html.twig';
		} else if (name == 'NEW_SIDEBAR_BLOCK') {
			// TemplatesController::saveTemplateAction knows to treat this special
			template_name = 'UserBundle:Portal:new-sidebar-block.html.twig';
		} else if (name.indexOf('EDIT_SIDEBAR_BLOCK:') !== -1) {
			// TemplatesController knows to treat this special
			template_name = name;
		} else {
			template_name = 'UserBundle::custom-footer.html.twig';
		}

		var overlay = new DeskPRO.UI.Overlay({
			contentElement: el,
			destroyOnClose: true,
			fullScreen: true,
			onBeforeOverlayOpened: function() {
				if (el.is('.has-init')) return;
				el.addClass('has-init');

				el.find('textarea').val('').addClass('loading');

				$.ajax({
					url: BASE_URL + 'admin/templates/get-template-code?name=' + template_name,
					context: this,
					success: function(val) {
						el.find('textarea').val(val).removeClass('loading');
					}
				});

				$('.save-trigger', el).on('click', function() {

					el.find('.overlay-footer').addClass('loading');

					var postData = {
						name: template_name,
						code: el.find('textarea').val().trim()
					};

					if (name == 'head_include') {
						if (!postData.code.length) {
							$.ajax({
								type: 'POST',
								url: BASE_URL + 'admin/templates/revert-template.json?name=' + template_name,
								success: function() {
									window.location.reload(false);
								}
							});
						} else {
							$.ajax({
								url: BASE_URL + 'admin/templates/save-template.json',
								context: this,
								type: 'POST',
								data: postData,
								success: function(data) {
									window.location.reload(false);
								}
							});
						}
						return;
					}

					if (!postData.code.length) {
						$.ajax({
							type: 'POST',
							url: BASE_URL + 'admin/templates/revert-template.json?name=' + template_name
						});

						callback('reset');
						overlay.close();
					} else {
						$.ajax({
							url: BASE_URL + 'admin/templates/save-template.json',
							context: this,
							type: 'POST',
							data: postData,
							success: function(data) {
								el.find('.overlay-footer').removeClass('loading');

								if (data.error) {
									alert(data.error_message + "\n\nLine: " + data.error_line);
									return;
								}

								callback('update', data);
								overlay.close();
							}
						});
					}
				});
			}
		});
		overlay.open();
	},

	/**
	 * Shows a simple editor for title/content
	 *
	 * @param callback
	 */
	showHtmlEditorSimple: function(pid, callback) {
		pid = parseInt(pid) || 0;

		var el = $(DeskPRO_Window.util.getPlainTpl($('#admin_portal_block_simple_html_edit_tpl')));

		var overlay = new DeskPRO.UI.Overlay({
			contentElement: el,
			destroyOnClose: true,
			fullScreen: true,
			onBeforeOverlayOpened: function(evData) {
				var el = evData.overlay.elements.wrapper;

				if (el.is('.has-init')) return;
				el.addClass('has-init');

				if (pid) {
					el.find('textarea.content').val('').addClass('loading');

					$.ajax({
						url: BASE_URL + 'admin/portal/sideblock-simple/' + pid + '.json',
						context: this,
						dataType: 'json',
						success: function(data) {
							el.find('input.title').val(data.title);
							el.find('textarea.content').val(data.content).removeClass('loading');
						}
					});
				}

				el.find('textarea.content').height($(window).height() - 250);

				$('.save-text-trigger', el).on('click', function() {

					el.find('.overlay-footer').addClass('loading');

					var postData = [];
					postData.push({
						name: 'title',
						value: el.find('input.title').val()
					});
					postData.push({
						name: 'content',
						value: el.find('textarea.content').val()
					});

					$.ajax({
						url: BASE_URL + 'admin/portal/sideblock-simple/'+pid+'/save.json',
						context: this,
						type: 'POST',
						data: postData,
						success: function(data) {
							el.find('.overlay-footer').removeClass('loading');
							callback('update', data);
							overlay.close();
						}
					});
				});
			}
		});
		overlay.open();
	},


	/**
	 * Whent the portal client is loaded, it sends a message to us and we invoke
	 * this method to set up the messages channel.
	 *
	 * @param height
	 */
	iframeLoaded: function(height) {
		var iframe = $('#portal_iframe').get(0);

		if (iframe.contentDocument) {
			this.iframeDocument = iframe.contentDocument;
		} else if (iframe.contentWindow) {
			this.iframeDocument = iframe.contentWindow.document;
		} else if (iframe.document) {
			this.iframeDocument = iframe.document;
		} else {
			this.iframeDocument = null;
			DP.console.error("Could not get iframe document");
		}

		this.iframeWindow = this.iframeDocument.window;

		$('#portal_iframe').height(height + 25);
		$('#dp_fauxbrowser').height(height + 188);
		this.iframeQuery('html').css('overflow', 'hidden');
		this.iframeQuery('body').css('overflow', 'hidden');

		// Show the frame now
		$('#portal_iframe_loading').fadeOut(300, function() {
			$('#portal_iframe').css('opacity', '1');
		});
	},

	updateHeight: function(height) {
		$('#portal_iframe').height(height + 25);
		$('#dp_fauxbrowser').height(height + 188);
	},


	/**
	 * Execute a jQuery query from in the context of the portal client
	 *
	 * @param query
	 */
	iframeQuery: function(query) {
		return this.iframeWindow.jQuery(query);
	},


	_initColorPicker: function() {
		var self     = this;
		var panel    = $('#portal_colors');
		var trigger  = $('#portal_colors_trigger');
		var backdrop = $('<div class="backdrop" style="z-index: 999" />').hide().appendTo('body');

		panel.detach().appendTo('body');

		trigger.on('click', function() {
			if (panel.is(':visible')) {
				closeColorPanel();
			} else {
				openColorPanel();
			}
		});

		backdrop.on('click', function() {
			closeColorPanel();
		});

		var openColorPanel = function() {
			var triggerPos = trigger.offset();

			var top  = triggerPos.top  + trigger.height();
			var left = (triggerPos.left + trigger.width() - 20);

			panel.css({
				top: top ,
				left: left
			});

			panel.slideDown();
			backdrop.show();
		};

		var closeColorPanel = function() {
			panel.slideUp();
			backdrop.hide();
		};

		var colorSwatches = $('.color-swatch', panel);
		colorSwatches.each(function() {
			var swatchEl = $(this);
			swatchEl.on('click', function() {
				swatchEl.ColorPickerShow();
			});

			swatchEl.ColorPicker({
				onSubmit: function(hsb, hex, rgb, el) {
					swatchEl.data('color', hex);
					$(el).ColorPickerHide();
				},
				onBeforeShow: function () {
					$(this).ColorPickerSetColor(swatchEl.data('color'));
				},
				onChange: function (hsb, hex, rgb) {
					$('div', swatchEl).css('backgroundColor', '#' + hex);
					swatchEl.data('color', '#' + hex);

					swatchEl.closest('.style-row').find('.color-reset').addClass('enabled');
				}
			});
		});

		var colorResets = $('.color-reset', panel);
		colorResets.on('click', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			var swatchEl = $(this).closest('.style-row').find('.color-swatch');
			var setColor = $(this).data('color').replace('#', '');

			console.log('Setting %s on %e', setColor, swatchEl);
			swatchEl.ColorPickerSetColor(setColor);
			swatchEl.data('color', '#' + setColor);
			$('div', swatchEl).css('backgroundColor', '#' + setColor);

			$(this).removeClass('enabled');
		});

		$('button.apply-trigger', panel).on('click', function() {
			closeColorPanel();

			var formData = [];

			colorSwatches.each(function() {
				formData.push({
					name: 'vars[' + $(this).data('color-id') + ']',
					value: $(this).data('color')
				});
			});

			$.ajax({
				url: BASE_URL + 'admin/portal/save-editor/css_var',
				type: 'POST',
				data: formData,
				success: function() {
					self.tellPortal('reload_css');
				}
			});
		});
	}
});

Orb.createNamespace('DeskPRO.Admin.ElementHandler');

DeskPRO.Admin.ElementHandler.LabelsPage = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var self = this;

		var newForm = $('#new_label_form');
		newForm.on('submit', function(ev) {
			ev.preventDefault();

			var url = newForm.attr('action');
			var formData = newForm.serializeArray();
			var label = $('input[type="text"]', newForm).val().trim();

			if (!label.length) {
				return;
			}

			$.ajax({
				url: url,
				type: 'POST',
				dataType: 'json',
				data: formData,
				complete: function() {
					$('input[type="text"]', newForm).val('');
				},
				success: function(data) {
					if (data.errorMessage) {
						alert(data.errorMessage);
						return;
					}

					self.updateDefCounts(data.def_counts);

					var existing = $('#labels_list li[data-label="' + label + '"]');
					existing.remove();

					var row = $(data.row_html).hide();
					row.prependTo($('#labels_list')).fadeIn('fast');

					DeskPRO_Window.util.showSavePuff($('> div', row));
				}
			});
		});

		var deleteUrl = this.el.data('delete-url');
		$('#labels_list').on('click', '.delete-trigger', function() {
			if (!confirm('Are you sure you want to delete this label?')) {
				return;
			}

			var row = $(this).closest('li.label-row');
			var label = row.data('label');

			$.ajax({
				url: deleteUrl,
				type: 'POST',
				dataType: 'json',
				data: { label: label },
				success: function(data) {
					row.fadeOut('fast', function() {
						row.remove();
					});
					self.updateDefCounts(data.def_counts);
				}
			});
		});

		var renameUrl = this.el.data('rename-url');
		$('#labels_list').on('change', 'input.label-text', function() {
			var row = $(this).closest('li.label-row');
			var label = row.data('label');
			var newLabel = $.trim($(this).val());

			if (!newLabel || label == newLabel) {
				$(this).val(label);
				return;
			}

			$.ajax({
				url: renameUrl,
				type: 'POST',
				dataType: 'json',
				data: { old_label: label, new_label: newLabel },
				success: function(data) {
					row.find('input.label-text').val(newLabel);
					row.data('label', newLabel).attr('data-label', newLabel);
					DeskPRO_Window.util.showSavePuff(row.find('.count'));
				}
			});
		});
	},

	updateDefCounts: function(def_counts) {
		Object.each(def_counts, function(v,k) {
			if (k == "TOTAL") {
				k = 'all';
			}
			$('#labels_count_' + k).text(v);
		});
	}
});

Orb.createNamespace('DeskPRO.Admin.ElementHandler');

DeskPRO.Admin.ElementHandler.RadioExpander = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var self = this;

		var groupClass  = this.el.data('group-class');
		var expandClass = this.el.data('expand-class');
		var radios = $('.option-trigger', this.el);

		var firstSubRadio = this.el.data('select-first-subradio');
		var currentGroup = null;

		function switchtoradio(radio) {
			self.el.find('.' + groupClass + '.on').removeClass('on');

			if (currentGroup && expandClass) {
				$('.' + expandClass, currentGroup).hide();
			}

			var group = radio.closest('.' + groupClass).addClass('on');
			if (expandClass) {
				$('.' + expandClass, group).show();
			}
			currentGroup = group;

			if (firstSubRadio) {
				group.find(':radio.' + firstSubRadio).first().click();
			}
		}

		$(':radio.option-trigger:checked', this.el).each(function() {
			switchtoradio($(this));
		});

		this.el.on('click', ':radio.option-trigger', function() {
			switchtoradio($(this));
		});

		this.el.on('click', '.' + groupClass + ':not(.on)', function(e) {
			var radio = $(this).find('.option-trigger');
			if (radio.length) {
				radio.prop('checked', 'checked');
				switchtoradio(radio);

				if (!$(e.target).is('input[type=radio]'))
				{
					// clicked something outside radio - need to manually fire change event
					radio.change();
				}
			}
		});
	}
});

Orb.createNamespace('DeskPRO.Admin.ElementHandler');

DeskPRO.Admin.ElementHandler.DashVersion = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		$.ajax({
			url: BASE_URL + 'admin/dashboard/load-version-info.html',
			dataType: 'html',
			success: function(html) {
				$('#version_info_expanded').html(html);
			}
		});
	}
});

Orb.createNamespace('DeskPRO.Admin.ElementHandler');

DeskPRO.Admin.ElementHandler.DashNotice = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var el = this.el;
		$.ajax({
			url: BASE_URL + 'admin/dashboard/load-version-notice.html',
			dataType: 'html',
			success: function(html) {
				if (html) {
					el.html(html);
					el.show();
				}
			}
		});
	}
});
