Orb.createNamespace('DeskPRO.Admin.ElementHandler');

DeskPRO.Admin.ElementHandler.RadioExpander = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var self = this;

		var groupClass  = this.el.data('group-class');
		var expandClass = this.el.data('expand-class');
		var radios = $('.option-trigger', this.el);

		var firstSubRadio = this.el.data('select-first-subradio');
		var currentGroup = null;

		function switchtoradio(radio) {
			self.el.find('.' + groupClass + '.on').removeClass('on');

			if (currentGroup && expandClass) {
				$('.' + expandClass, currentGroup).hide();
			}

			var group = radio.closest('.' + groupClass).addClass('on');
			if (expandClass) {
				$('.' + expandClass, group).show();
			}
			currentGroup = group;

			if (firstSubRadio) {
				group.find(':radio.' + firstSubRadio).first().click();
			}
		}

		$(':radio.option-trigger:checked', this.el).each(function() {
			switchtoradio($(this));
		});

		this.el.on('click', ':radio.option-trigger', function() {
			switchtoradio($(this));
		});

		this.el.on('click', '.' + groupClass + ':not(.on)', function(e) {
			var radio = $(this).find('.option-trigger');
			if (radio.length) {
				radio.prop('checked', 'checked');
				switchtoradio(radio);

				if (!$(e.target).is('input[type=radio]'))
				{
					// clicked something outside radio - need to manually fire change event
					radio.change();
				}
			}
		});
	}
});
