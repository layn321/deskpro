Orb.createNamespace('DeskPRO.UI');

DeskPRO.UI.TableGrid = new Orb.Class({
	Implements: [Orb.Util.Options, Orb.Util.Events],

	initialize: function(table, options) {
		this.table = $(table);
		if (options) {
			this.setOptions(options);
		}

		this.theadCells = $('thead:first td', this.table);

		var self = this;
		this.theadCells.each(function() {
			self._drawDragger($(this));
		});
	},

	_initReorder: function(td) {
		td.draggable({
			axis: 'y',
			stop: this.handleTdReorder.bind(this)
		})
	},

	_drawDragger: function(td) {
		var drag = $('<div class="td-resizer"></div>');
		drag.data('resizer-for', drag);
		drag.appendTo('body');

		drag.draggable({
			axis: 'x',
			start: function() {
				drag.data('pos-start', drag.offset().left);
			},
			stop: (function() {
				this.handleResize(drag);
			}).bind(this)
		});
	},

	handleResize: function(drag) {
		var oldLeft = drag.data('pos-start');
		drag.data('pos-start', '');

		var newLeft = drag.offset().left();

		var diff = oldLeft - newLeft;

		var td = drag.data('resizer-for');
		if (td.next().length) {
			var nextTd = td.next();
		} else {
			var nextTd = td.prev();
		}

		td.width(td.width() + diff);
		nextTd.width(nextTd.width() + (diff * -1));
	},

	handleTdReorder: function (event, ui) {
		var oldPos = this.theadCells.filter('td.dragging');
		var newPos = 1;

		$('tbody > tr > td:nth('+oldPos+')').each(function() {
			if (oldPos > newPos) {
				var moveTo = $(this).next();
			} else {
				var moveTo = $(this).prev();
			}
			$(this).detatch().after(moveTo);
		});
	}
});