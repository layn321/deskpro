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
