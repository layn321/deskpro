Orb.createNamespace('DeskPRO.User.ElementHandler');

DeskPRO.User.InlineSuggestions = new Orb.Class({
	Implements: [Orb.Util.Options, Orb.Util.Events],

	initialize: function(options) {
		this.options = {
			elementWrapper: null,
			titleText: '#__dp_nomatch',
			contentText: '#__dp_nomatch'
		};
		this.setOptions(options);

		this.el = $(this.options.elementWrapper);
		this.titleTxt = $(this.options.titleText);
		this.messageTxt = $(this.options.contentText);

		this._initSuggestionsBox();
	},

	_initSuggestionsBox: function() {
		this.suggestionsBox = $('.dp-related-search', this.el);
		this.resultsEl = $('.results', this.suggestionsBox);
		this.moreLink = $('.more-link', this.suggestionsBox);
		this.lastSuggestions = null;
		this.lastString = null;
		this.notAnsweredResults = [];

		this.hasStartedSearch = false;

		this.moreLink.on('click', (function(ev) {
			this.moreLink.hide();
			$('li', this.resultsEl).show();
		}).bind(this));

		this.suggestionsUrl = this.el.data('suggestions-url');

		this.sugTitleTimer = null;
		this.sugMessageTimer = null;

		this.titleTxt.on('keypress', (function() {
			if (!this.hasStartedSearch) return;
			if (this.sugTitleTimer) return;
			this.sugTitleTimer = this.updateSuggestions.delay(400, this);
		}).bind(this));

		this.titleTxt.on('blur', (function() {
			this.hasStartedSearch = true;
			this.updateSuggestions();
		}).bind(this));

		this.messageTxt.on('keypress', (function() {
			if (!this.hasStartedSearch) return;
			if (this.sugMessageTimer) return;
			this.sugMessageTimer = this.updateSuggestions.delay(1200, this);
		}).bind(this));
	},

	updateSuggestions: function() {

		if (this.sugTitleTimer) {
			window.clearTimeout(this.sugTitleTimer);
			this.sugTitleTimer = null;
		}
		if (this.sugMessageTimer) {
			window.clearTimeout(this.sugMessageTimer);
			this.sugMessageTimer = null;
		}

		var content = (this.titleTxt.val().trim() + ' ' + this.messageTxt.val().trim()).trim();

		if (this.lastSearchString && this.lastSearchString == content) {
			return;
		}

		this.lastSearchString = content;

		if (!content.length) {
			this.suggestionsBox.hide();
			return;
		}

		// Already set to repeat
		if (this.doSuggestResend) {
			return;
		}

		if (this.isSuggestActive) {
			this.doSuggestResend = true;
			return;
		}

		this.isSuggestActive = true;

		$.ajax({
			url: this.suggestionsUrl,
			dataType: 'html',
			data: {'content': content},
			context: this,
			success: function(html) {
				this.isSuggestActive = false;

				if (this.doSuggestResend) {
					this.doSuggestResend = false;
					this.updateSuggestions();
				}

				if (this.lastSuggestions && this.lastSuggestions == html) {
					return;
				}

				this.lastSuggestions = html;

				this.resultsEl.empty().html(html);

				// Make sure unsolved results dont reappear
				if (this.notAnsweredResults.length) {
					var x;
					for (x = 0; x < this.notAnsweredResults.length; x++) {
						$('li.' + this.notAnsweredResults[x], this.resultsEl).remove();
					}
				}

				if (!$('li:first', this.resultsEl).length) {
					this.suggestionsBox.hide();
					this.lastSuggestions = null;
				} else {

					if (!this.moreLink.data('has-mored')) {
						this.moreLink.data('has-mored', true);
						var count = $('li', this.resultsEl).length;

						if (count > 6) {
							var remainCount = count - 6;
							$('.count', this.moreLink).text(remainCount);
							this.moreLink.show();

							$('li', this.resultsEl).slice(5).hide();
						} else {
							this.moreLink.hide();
						}
					} else {
						this.moreLink.hide();
					}

					var self = this;
					$('li a[href]', this.suggestionsBox).on('click', function(ev) {
						ev.preventDefault();
						self.openSuggestedContent($(this));
					});

					this.suggestionsBox.show();
				}
			}
		});
	},

	openSuggestedContent: function(aEl) {

		var origUrl = aEl.attr('href');
		var url = Orb.appendQueryData(origUrl, '_partial', 'overlaySuggest');
		var contentType = aEl.data('content-type');
		var contentId = aEl.data('content-id');
		var self = this;

		var tplEl = $('.related-content-overlay-tpl', this.el);

		if (!tplEl[0]) {
			window.open(origUrl);
			return;
		}

		var overlay = new DeskPRO.User.SuggestedContentOverlay({
			template: tplEl.get(0).innerHTML,
			url: url,
			pageUrl: origUrl,
			contentType: aEl.data('content-type'),
			contentId: aEl.data('content-id'),
			destroyOnClose: true,
			openNear: $('.dp-related-search', this.el),
			onInit: (function(overlayEl, controls, overlay) {
				// As soon as they click we subimt the request to record it
				$('.dp-set-answered', controls).on('click', function(ev) {
					ev.preventDefault();
					self.fireEvent('resolved', [contentType, contentId, true]);
				});

				// But we still send them through the redirect, so they
				// can visit the article quickly without waiting for the save to return
				$('.dp-answererd', controls).on('click', function(ev) {
					ev.preventDefault();
					var type = $(this).data('type');

					if (type == 'close') {
						self.fireEvent('resolvedRedirect', [origUrl, contentType, contentId, this]);
					} else {
						overlay.close();
					}
				});

				$('.dp-not-answered', controls).on('click', function(ev) {
					ev.preventDefault();
					self.fireEvent('notResolved', [contentType, contentId, true]);

					aEl.parent().addClass('not-answered');
					self.notAnsweredResults.push(contentType + '-' + contentId);
					overlay.close();
				});
			}).bind(this)
		});
		overlay.open();
	}
});
