Orb.createNamespace('DeskPRO.Admin.ElementHandler');

DeskPRO.Admin.ElementHandler.TicketTriggersNewChoosePage = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var self = this;

		var form = $('#choice_form');

		var quitit = false;
		form.on('click', 'input[name="trigger_group"]', function(ev) {
			if (quitit) return;
			quitit = true;

			var li = $(this).closest('li');
			$('li', form).removeClass('on');
			li.addClass('on');

			quitit = false;
		});

		form.on('submit', function(ev) {
			// Remove the time stuff for options not selected so they dont get submitted
			$('input[name="event_trigger_time"], select', $('li', form).not('.on')).remove();
		});
	}
});
