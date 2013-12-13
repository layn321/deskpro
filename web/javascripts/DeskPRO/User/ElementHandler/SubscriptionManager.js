Orb.createNamespace('DeskPRO.User.ElementHandler');

DeskPRO.User.ElementHandler.SubscriptionManager = new Orb.Class({

	Extends: DeskPRO.User.ElementHandler.ElementHandlerAbstract,

	init: function() {
		$('a.sub-delete', this.el).on('click', function(ev){
			ev.preventDefault();

			var a = $(this);

			var url = a.attr('href');
			var row = a.parent();
			var section = row.parent().parent();

			a.addClass('loading');

			$.ajax({
				url: url,
				dataType: 'json',
				success: function() {
					if ($('li', section).length <= 1) {
						section.fadeOut('fast', function() {
							section.remove();
						});
					} else {
						row.fadeOut('fast', function() {
							row.remove();
						});
					}
				},
				complete: function() {
					a.removeClass('loading');
				}
			});
		});
	}
});
