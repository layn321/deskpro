Orb.createNamespace('DeskPRO.Agent.PageHelper');

DeskPRO.Agent.PageHelper.ListColResize = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(options) {
		this.options = {
			table: null
		};
		this.setOptions(options || {});

		this.table = $(this.options.table);

		var r = $('> thead:first > tr:first', this.table);
		var heads = $('> td:not(.not-resizable), > th:not(.not-resizable)', r);

		$('td, th', r).each(function() {
			$(this).css({
				width: $(this).width()
			});
		});

		this.table.css('table-layout', 'fixed');

		var self = this;
		heads.each(function() {
			var resizer = $('<div class="col-resizer"></div>');
			var el = $(this);
			el.prepend(resizer);

			resizer.on('mousedown', function(ev) {
				ev.stopPropagation();
			});

			resizer.draggable({
				helper: function() {
					return $('<div class="col-resizer-helper"></div>');
				},
				cursorAt: { left: 5 },
				axis: 'x',
				start: function(event, ui) {
					event.stopPropagation();
				},
				stop: function(event, ui) {

					var pos = el.offset();
					var helperPos = ui.offset;

					var width = helperPos.left - pos.left;

					DP.console.log('%i %i', el.width(), width);
					el.css('width', width);

					self.fireEvent('widthUpdated', [heads, self])
				}
			});
		});
	}
});
