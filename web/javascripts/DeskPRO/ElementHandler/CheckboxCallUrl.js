Orb.createNamespace('DeskPRO.Agent.PageHelper');

DeskPRO.ElementHandler.CheckboxCallUrl = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var self = this;
		var url = this.el.data('url');
		var prop = this.el.data('state-property');

		this.el.on('click', function() {
			$.ajax({
				url: url,
				type: 'POST',
				dataType: 'json',
				success: function(data) {
					// The server returned a state with the request, so make sure
					// the toggle is correctly set now
					if (prop) {
						var checked = !!data[prop];
						self.el.attr('checked', !!data[prop]);
					}
				}
			});
		});
	}
});
