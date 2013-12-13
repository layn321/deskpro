Orb.createNamespace('DeskPRO.Agent.ElementHandler');

DeskPRO.Agent.ElementHandler.PasswordPrompt = new Orb.Class({
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

		this.el.detach().hide().appendTo('body');

		this.passwordField = $('input.password-input', this.el);
		this.explainEl     = $('.explain-wrap', this.el);

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

		$('button.continue-trigger').on('click', function() {
			var password = self.passwordField.val();
			if (password === "") {
				return;
			}

			$.ajax({
				url: BASE_URL + 'agent/password-confirm-code.json',
				type: 'POST',
				dataType: 'json',
				data: { password: password },
				context: self,
				success: function(res) {
					if (res.invalid) {
						this.failedFn(this);
						this.el.addClass('with-error');
					} else {
						this.cancelFn = function() {};// dont fire for success
						this.close();
						this.successFn(res.code, this);
					}
				}
			});
		});
	},

	open: function(options) {
		this.successFn = options.success || function() {};
		this.failedFn  = options.failed  || function() {};
		this.cancelFn  = options.cancel  || function() {};

		this._initOverlay();

		if (options.explain) {
			if (typeof options.explain === "string") {
				this.explainEl.text(options.explain).show();
			} else {
				this.explainEl.append(options.explain).show();
			}
		}

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
		this.cancelFn();

		this.el.hide();
		this.backdropEl.hide();
	},

	reset: function() {
		this.cancelFn();

		this.cancelFn  = null;
		this.successFn = null;
		this.failedFn  = null;

		this.passwordField.val('');
		this.explainEl.empty().hide();
		this.el.removeClass('with-error');
	}
});
