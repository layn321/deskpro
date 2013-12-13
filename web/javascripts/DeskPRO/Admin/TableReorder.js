Orb.createNamespace('DeskPRO.Admin');

/**
 * Adds drag+drop reordering to table rows
 */
DeskPRO.Admin.TableReorder = new Orb.Class({

	initialize: function(table) {
		var self = this;

		this.table = table;

		var sendUpdate = function() {
			var postData = [];
			$('tr[data-item-id]', table).each(function() {
				if ($(this).data('item-id')) {
					postData.push({
						name: 'display_order[]',
						value: $(this).data('item-id')
					});
				}
			});

			$.ajax({
				url: table.data('reorder-save-url'),
				type: 'POST',
				data: postData
			});
		};

		this.table.sortable({
			items: 'tbody',
			handle: 'tr.depth-0',
			placeholder: {
				element: function() {
					return $('<tbody class="placeholder"><tr><td colspan="100">&nbsp;</td></tr></tbody>');
				},
				update: function() {
					return;
				}
			},
			helper: function(event, element) {
				var t = self.table.clone(false);
				t.empty();
				t.append(element.clone());
				t.addClass('dragging');

				$('tr td:not(.title)', t).remove();
				t.css('width', 300);
				return t;
			},
			update: function() {
				sendUpdate();
			}
		});

		$('tbody', this.table).each(function() {
			var tbody = $(this);
			tbody.sortable({
				items: 'tr.depth-1',
				placeholder: {
					element: function() {
						return $('<tr class="placeholder"><td colspan="100">&nbsp;</td></tr>');
					},
					update: function() {
						return;
					}
				},
				helper: function(event, element) {
					var t = self.table.clone(false);
					t.empty();
					t.append(element.clone());
					t.addClass('dragging');
					$('tr td:not(.title)', t).remove();
					t.css('width', 300);
					return t;
				},
				update: function() {
					sendUpdate();
				}
			});
		});
	}
});
