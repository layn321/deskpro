Orb.createNamespace('DeskPRO.Agent.PageHelper');

DeskPRO.Agent.PageHelper.ListColDrag = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(options) {
		this.options = {
			table: null,
			onlyRowSel: null,
			onlyRowColOffset: 0
		};
		this.setOptions(options || {});

		this.moveIndicator = $('<div class="col-move"></div>').appendTo('body');

		this.table = $(this.options.table);

		this.resetHeads();

		var self = this;

		this.heads.draggable({
			axis: 'x',
			helper: function(event, ui) {
				var el = $('<div style="background-color:#EEF1F5;padding:3px;border: 1px solid #CAD0D7;" />').text($(this).text());

				return el;
			},
			start: function(event, ui) {
				event.stopPropagation();
				var index = $(this).index();
				$(this).closest('table').data('drag_col_index', index);
			},
			stop: function() {
				self.moveIndicator.hide();
			},
			scope: 'col-drag'
		});

		this.dropHeads.droppable({
			scope: 'col-drag',
			over: function(event, ui) {
				var index = $(this).index();
				var pos = $(this).offset();
				var w = $(this).width();

				var leftPos = pos.left + w;

				self.moveIndicator.css({
					left: leftPos,
					top: pos.top - self.moveIndicator.height(),
					display: 'block'
				});
			},
			out: function(event, ui) {
				//self.moveIndicator.hide();
			},
			drop: function(event, ui) {

				self.moveIndicator.hide();

				var orig_index = self.table.data('drag_col_index');
				self.table.data('drag_col_index', 'null');

				var new_index = $(this).index();

				if (new_index == orig_index) {
					return; // not moving anywhere!
				}

				if (self.options.onlyRowSel) {
					var rows = $(self.options.onlyRowSel, self.table);
				} else {
					var rows = $('tr', self.table);
				}

				rows = rows.filter(':not(.is-head)');

				var method = 'after';
				if (self.options.onlyRowColOffset) {
					orig_index -= self.options.onlyRowColOffset;
					new_index  -= self.options.onlyRowColOffset

					// Our little hack ot allow "before"
					if (new_index < 0) {
						new_index = 0;
						method = 'before';
					}
				}

				// Move the header
				var dragCell = $(ui.draggable);
				var posCell = $(this);
				dragCell.detach().insertAfter(posCell);

				// Move each row
				rows.each(function(row_index, row) {
					var dragCell = $(row).find('td').eq(orig_index);
					var posCell = $(row).find('td').eq(new_index);

					if (method == 'before') {
						dragCell.detach().insertBefore(posCell);
					} else {
						dragCell.detach().insertAfter(posCell);
					}
				});

				self.resetHeads();

				self.fireEvent('orderChanged', [self.heads, this]);
			}
		});
	},

	resetHeads: function() {
		var r = $('> thead:first > tr:first', this.table);
		r.addClass('is-head');

		$('.not-droppable').removeClass('not-droppable');
		$('.not-draggable').addClass('not-droppable');

		this.heads = $('th:not(.not-draggable), td:not(.not-draggable)', r);
		this.heads.eq(0).prev().addClass('first-drop-target').removeClass('not-droppable');

		this.dropHeads = $('th:not(.not-droppable), td:not(.not-droppable)', r);
	}
});