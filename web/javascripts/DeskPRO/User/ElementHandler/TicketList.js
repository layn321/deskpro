Orb.createNamespace('DeskPRO.User.ElementHandler');

DeskPRO.User.ElementHandler.TicketList = new Orb.Class({

	Extends: DeskPRO.User.ElementHandler.ElementHandlerAbstract,

	init: function() {
		$('a.close-ticket-trigger').on('click', function(ev) {
			var text = "Once a ticket is closed, our agents will not reply to it anymore. Are you sure you want to close this ticket?";
			if (!confirm(text)) {
				ev.preventDefault();
			}
		});
	}
});
