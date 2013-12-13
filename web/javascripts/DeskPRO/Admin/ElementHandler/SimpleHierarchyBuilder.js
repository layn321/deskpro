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