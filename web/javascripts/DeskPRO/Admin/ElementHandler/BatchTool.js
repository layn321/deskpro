Orb.createNamespace('DeskPRO.Admin.ElementHandler');

DeskPRO.Admin.ElementHandler.BatchTool = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	initPage: function() {
		var self = this;
		this.url = this.el.data('url');
		this.page = -1;

		this.logEl = this.el.find('.batch-log');

		this.el.find('.start-trigger').on('click', function(ev) {
			self.el.find('.batch-start').hide();
			ev.preventDefault();
			self.el.addClass('running');
			self.logEl.show();
			self.runBatch();
		});
	},

	runBatch: function() {
		this.page++;
		$.ajax({
			url: this.url + '?p=' + this.page,
			context: this,
			dataType: 'json',
			success: function(data) {
				if (data.log_text) {
					var d = $('<div></div>');
					d.text(data.log_text);
					d.appendTo(this.logEl);
				}

				if (data.has_more) {
					this.runBatch();
				} else {
					this.el.find('.batch-done').show();
					this.el.removeClass('running');
				}
			}
		});
	}
});
