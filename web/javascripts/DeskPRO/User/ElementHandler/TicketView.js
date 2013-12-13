Orb.createNamespace('DeskPRO.User.ElementHandler');

DeskPRO.User.ElementHandler.TicketView = new Orb.Class({

	Extends: DeskPRO.User.ElementHandler.ElementHandlerAbstract,

	init: function() {
		$('.feedback-link', this.el).each(function() {
			var el = $(this);

			$('a', this).on('click', function(ev) {
				ev.preventDefault();

				window.open(
					$(this).attr('href'),
					'feedback',
					"status=no,toolbar=no,location=no,menubar=no,resizable=no,scrollbars=yes,height=310,width=720"
				);
			}).on('mouseover', function() {
				if ($(this).is('.helpful')) {
					el.addClass('rating-helpful').removeClass('rating-not-helpful');
				} else {
					el.removeClass('rating-helpful').addClass('rating-not-helpful');
				}
			}).on('mouseout', function() {
				el.removeClass('rating-helpful').removeClass('rating-not-helpful');
			});
		});
	}
});
