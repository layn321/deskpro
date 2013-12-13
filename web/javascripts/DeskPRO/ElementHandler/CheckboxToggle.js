Orb.createNamespace('DeskPRO.Agent.PageHelper');

DeskPRO.ElementHandler.CheckboxToggle = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var self = this;
		var targets = $(this.el.data('targets'));
		var clearTargets = this.el.data('clear-targets');

		if (!targets.length) return;

		var checkFn = function() {
			if (self.el.is(':checked')) {
				targets.show();
			} else {
				targets.hide();

				if (clearTargets) {
					targets.find('input[type="text"], input[type="password"], textarea').val('');
				}
			}
		}

		this.el.on('click', function() {
			checkFn();
		});

		checkFn();
	}
});
