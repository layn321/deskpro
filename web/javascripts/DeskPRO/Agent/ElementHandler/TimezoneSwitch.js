Orb.createNamespace('DeskPRO.Agent.ElementHandler');

DeskPRO.Agent.ElementHandler.TimezoneSwitch = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var self = this;

		this.el.bind('dp_open', function(ev, options) {
			self.open(options);
		});
	},

	_initOverlay: function() {
		var self = this;
		if (this._hasInit) return;
		this._hasInit = true;

		var now = new Date();
		var hour = now.getHours();
		var ampm = 'am';
		if (hour > 12) {
			hour -= 12;
			ampm = 'pm';
		}
		var min = now.getMinutes();
		if (min < 10) {
			min = '0' + min;
		}
		var time = hour + ':' + min + ' ' + ampm;

		this.el.find('.js_time').text(time);

		this.el.detach().hide().appendTo('body');

		this.backdropEl = $('<div class="backdrop dp-overlay-backdrop" />');
		this.backdropEl.css('z-index', '40000').hide().appendTo('body');

		this.backdropEl.on('click', (function(ev) {
			ev.stopPropagation();
			this.close();
		}).bind(this));

		$('header .close-trigger', this.el).on('click', (function(ev) {
			ev.stopPropagation();
			ev.preventDefault();
			this.close();
		}).bind(this));

		var tzField = $('select.timezone', this.el);
		DP.select(tzField);

		this.el.find('button.dismiss-trigger').on('click', function() {
			$.ajax({
				url:  BASE_URL + 'agent/misc/ajax-save-prefs',
				type: 'POST',
				dataType: 'json',
				data: [{ name: 'prefs[agent.ui.tz_detect_dismiss]', value: DESKPRO_TIME_OUT_OF_SYNC }]
			});

			self.close();
			return;
		});

		this.el.find('button.continue-trigger').on('click', function() {
			var tz = tzField.find('option:selected').val();

			if (!tz) {
				self.close();
				return;
			}

			$.ajax({
				url: BASE_URL + 'agent/settings/profile/update-timezone.json',
				type: 'POST',
				dataType: 'json',
				data: { timezone: tz },
				context: self,
				success: function(res) {
					self.close();
					DeskPRO_Window.util.reloadInterface();
				}
			});
		});
	},

	open: function(options) {
		this._initOverlay();
		this.updatePositions();
		this.el.show();
	},

	updatePositions: function() {

		var elW = this.el.width();
		var elH = this.el.height();

		var pageW = $(window).width();
		var pageH = $(window).height();

		this.el.css({
			top: (pageH-elH) / 2,
			left: (pageW-elW) / 2
		});
	},

	close: function() {
		this.el.hide();
		this.backdropEl.hide();
	}
});
