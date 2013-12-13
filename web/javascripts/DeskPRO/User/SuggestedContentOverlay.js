Orb.createNamespace('DeskPRO.User.ElementHandler');

DeskPRO.User.SuggestedContentOverlay = new Orb.Class({
	Implements: [Orb.Util.Options, Orb.Util.Events],

	initialize: function(options) {
		this.options = {
			template: null,

			url: null,
			pageUrl: null,

			contentType: null,
			contentId: null,

			destroyOnClose: true,

			maxHeight: 700,
			openNear: null
		};

		this.setOptions(options);

		this.overlayEl = null;
		this.backdropEl = null;
		this.runningAjax = false;
	},

	_initOverlay: function() {
		if (this._hasInit) return;
		this._hasInit = true;

		var self = this;

		this.overlayEl = $(this.options.template).hide().appendTo('#dp');
		this.controlsWrap = $('.dp-controls', this.overlayEl).hide();
		this.backdropEl = $('<div class="dp-backdrop dp-faded" />').appendTo('#dp');
		this.backdropEl.on('click', function(el) {
			self.close();
		});

		$('.dp-close-btn', this.overlayEl).on('click', function(ev) {
			ev.preventDefault();
			self.close();
		});

		this.runningAjax = $.ajax({
			url: this.options.url,
			type: 'GET',
			context: this,
			error: function() {
				this.close();
			},
			success: function(html) {
				this.controlsWrap.show();
				$('.dp-content-holder', this.overlayEl).empty().html(html);

				$('.dp-section-toggle', this.controlsWrap).on('click', function(ev) {
					ev.preventDefault();
					var toggleSel = $(this).data('toggle-section');
					$('.dp-control-section', self.controlsWrap).fadeOut('fast', function() {
						window.setTimeout(function() {
							$(toggleSel, self.controlsWrap).fadeIn();
						}, 150);
					});
				});

				$('.dp-toggle-sel', this.overlayEl).on('click', function(ev) {
					ev.preventDefault();
					var el = $($(this).data('toggle-sel'), self.overlayEl);

					if ($(this).is('.open')) {
						$(this).removeClass('open');
						el.slideUp();
					} else {
						if ($(this).data('toggle-self')) {
							$(this).slideUp('fast', function() {
								el.slideDown();
							}).addClass('open');
						} else {
							el.slideDown();
							$(this).addClass('open');
						}
					}
				});

				if (self.options.pageUrl) {
					$('.dp-open-full', this.overlayEl).on('click', function(ev) {
						ev.preventDefault();
						window.open(self.options.pageUrl);
						self.close();
					});

					$('.dp-open-full a', this.overlayEl).attr('href', self.options.pageUrl);
				}

				this.fireEvent('init', [this.overlayEl, this.controlsEl, this]);
			}
		});
	},

	open: function() {
		this._initOverlay();

		var pos = {
			top: 40,
			left: 100,
			width: null
		};

		this.fireEvent('preOpen', [pos, this]);
		if (pos.cancel) {
			return;
		}

		var h = this.overlayEl.height();
		var w = this.overlayEl.width();

		var winH = $(window).height();
		var winW = $(window).width();
		h = winH * 0.7;
		w = winW * 0.6;

		if (h < 250) {
			h = 250;
		}
		if (w < 400) {
			w = 400;
		}

		if (h > this.options.maxHeight) {
			h = this.options.maxHeight;
		}

		this.overlayEl.css('height', h);
		this.overlayEl.css('width', w);

		this.overlayEl.css({
			top: (winH / 2) - (h / 2),
			left: (winW / 2) - (w / 2)
		});

		this.overlayEl.fadeIn('fast').addClass('open');
		this.backdropEl.show();
	},

	close: function() {
		if (!this._hasInit) return;
		if (!this.overlayEl.is('.open')) {
			return;
		}

		this.overlayEl.fadeOut('fast', (function() {
			if (this.options.destroyOnClose) {
				this.destroy();
			}
		}).bind(this));
		this.backdropEl.hide();
	},

	destroy: function() {
		if (this._hasInit) return;
		this.overlayEl.remove();
		this.backdropEl.remove();

		this.overlayEl = null;
		this.backdropEl = null;

		if (this.runningAjax) {
			this.runningAjax.abort();
			this.runningAjax = null;
		}
	}
});
