Orb.createNamespace('DeskPRO');

DeskPRO.FaviconBadge = new Orb.Class({

	Implements: [Orb.Util.Options],

	initialize: function(options) {
		var self = this;
		this.options = {};

		this.origWindowTitle = document.title;

		this.options.strokeColor = 'rgb(255,0,0)';
		this.options.color = '#FFFFFF';

		this.options.strokeColorAlt = 'rgb(0,0,0)';
		this.options.colorAlt = '#FFFFFF';

		$(document).bind('windowshow', this.disableCrazyMode.bind(this));
		$(window).bind('mousemove', this.disableCrazyMode.bind(this));
		$(window).bind('keypress', this.disableCrazyMode.bind(this));

		this.setOptions(options);

		this.tinyconOptions = {
			font: '10px arial',
			fallback: false
		};

		this.animateTimeout = null;
		this.animateCount = 0;
		this.crazyMode = false;
		this.crazyTitle = null;
		this.lastNum = 0;
	},

	clearAnimate: function() {
		if (this.animateTimeout) {
			window.clearTimeout(this.animateTimeout)
			this.animateTimeout = null;
			this.animateTimeoutCount = 0;
		}

		$(document).unbind('windowshow.faviconbadge');
		$(window).unbind('mousemove.faviconbadge');
		$(window).unbind('keypress.faviconbadge');
	},

	enableCrazyMode: function(title) {

		if ($('html').hasClass('window-active')) {
			return;
		}

		this.crazyTitle = title || null;
		this.crazyMode = true;
		this.updateBadge(this.lastNum, true);
	},

	disableCrazyMode: function() {
		if (!this.crazyMode) {
			return;
		}
		this.crazyMode = false;
		this.crazyTitle = null;
		document.title = this.origWindowTitle;
		this.updateBadge(this.lastNum, false);
	},

	updateBadge: function(num, do_animate) {
		var self = this;

		this.clearAnimate();

		// We have only two digits to play with
		var num = parseInt(num);
		if (num > 99) {
			num = 99;
		}

		this.lastNum = num;

		// 0 means no number
		if (!num && !this.crazyMode) {
			Tinycon.setBubble('');
			return;
		}

		if (do_animate) {
			this.animateTimeout = window.setInterval(function() {
				self.animateCount++;
				if (self.crazyMode) {
					self.tinyconOptions.width = 7;
					self.tinyconOptions.height = 8;

					if (self.animateCount % 2 == 0) {
						self.tinyconOptions.colour = '#000000';
						self.tinyconOptions.background = '#FFFFFF';
						Tinycon.setOptions(self.tinyconOptions);
						Tinycon.setBubble('◎');
						if (self.crazyTitle) {
							document.title = self.origWindowTitle;
						}
					} else {
						self.tinyconOptions.colour = '#FF0000';
						self.tinyconOptions.background = '#FFFFFF';
						Tinycon.setOptions(self.tinyconOptions);
						Tinycon.setBubble('◉');
						if (self.crazyTitle) {
							document.title = self.crazyTitle;
						}
					}
				} else {
					self.tinyconOptions.width = 7;
					self.tinyconOptions.height = 9;

					if (self.animateCount % 2 == 0) {
						self.tinyconOptions.colour = self.options.color;
						self.tinyconOptions.background = self.options.strokeColor;
						Tinycon.setOptions(self.tinyconOptions);
						Tinycon.setBubble(num+'');
					} else {
						self.tinyconOptions.colour = self.options.colorAlt;
						self.tinyconOptions.background = self.options.strokeColorAlt;
						Tinycon.setOptions(self.tinyconOptions);
						Tinycon.setBubble(num+'');
					}
				}
			}, 800);
		} else {
			self.tinyconOptions.width = 7;
			self.tinyconOptions.height = 9;
			self.tinyconOptions.colour = self.options.color;
			self.tinyconOptions.background = self.options.strokeColor;
			Tinycon.setOptions(self.tinyconOptions);
			Tinycon.setBubble(num+'');
		}
	}
});
