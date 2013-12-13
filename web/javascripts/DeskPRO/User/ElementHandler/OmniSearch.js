Orb.createNamespace('DeskPRO.User.ElementHandler');

DeskPRO.User.ElementHandler.OmniSearch = new Orb.Class({

	Extends: DeskPRO.User.ElementHandler.ElementHandlerAbstract,

	init: function() {
		var self = this;

		this.backdrop    = $('<div class="dp-backdrop" />').hide().appendTo('#dp');
		this.assistEl    = $('#dp_search_assist');
		this.searchboxEl = $('#dp_search');

		this.resultsEl   = $('div.results', this.assistEl);
		$('#dp_search_assist').on('click', function() {
			$('#dp_omnisearch').submit();
		});

		this.searchboxEl.on('focus', this.activateAssist.bind(this));

		this.backdrop.on('click', function(ev) {
			ev.stopPropagation();
			self.deactivateAssist();
		});

		this.isActivated = false;

		this.searchTimer = new DeskPRO.IntervalCaller({
			touchResets: true,
			touchRequired: true,
			resetTimeForce: 1500,
			timeout: 750,
			autostart: true,
			callback: this.updateResults.bind(this)
		});

		this.searchboxEl.on('keyup', function(ev) {
			if (!$(this).val().trim().length) {
				self.close();
			} else {
				self.activateAssist();
				if (ev.keyCode == '32') {
					self.searchTimer.exec(true);
				} else {
					self.searchTimer.touch();
				}
			}
		});

		this.searchboxEl.on('focus', function() {
			self.activateAssist();
		});

		this.lastTerms = null;

		$('.foot a', this.assistEl).on('click', function(ev) {
			var el = $(this);
			if (el.is('.no-omni-trigger') || el.closest('li').is('.no-omni-trigger')) {
				return;
			}

			ev.preventDefault();
			ev.stopPropagation();

			var url = el.attr('href');
			url = Orb.appendQueryData(url, 'q', self.searchboxEl.val().trim());

			window.location = url;
		});
	},

	activateAssist: function() {
		var wasActive = this.isActivated;

		this.isActivated = true;
		this.updatePosition();

		if (!this.searchboxEl.val().trim().length) {
			this.deactivateAssist();
		} else {
			this.open();
			if (!wasActive) {
				this.searchTimer.execNow();
			}
		}
	},

	open: function() {
		this.assistEl.show();
		this.backdrop.show();
	},

	deactivateAssist: function() {
		this.isActivated = false;
		this.close();
	},

	close: function() {
		this.assistEl.hide();
		this.backdrop.hide();
	},

	updatePosition: function() {
		var pos = this.searchboxEl.offset();

		var w = this.searchboxEl.outerWidth();
		var h = this.searchboxEl.outerHeight();

		this.assistEl.css({
			top: pos.top + h + 1,
			left: pos.left,
			width: w - 1
		});
	},

	updateResults: function() {
		if (!this.isActivated) {
			return;
		}

		var terms = this.searchboxEl.val().trim();

		if (terms == this.lastTerms || terms === '') {
			return;
		}

		this.lastTerms = terms;

		this.searchboxEl.addClass('loading');
		$.ajax({
			url: BASE_URL + 'search/omnisearch/' + encodeURI(terms),
			dataType: 'html',
			context: this,
			complete: function() {
				this.searchboxEl.removeClass('loading');
			},
			success: function(html) {
				if (html.indexOf('<body') !== -1 || html.indexOf('<html') !== -1) {
					// If its a full document then its not a proper
					// response. (e.g., was a redirect to login)
					return;
				}

				var wrap = $(html);
				this.resultsEl.empty();

				if (!$('li', wrap).length) {

				} else {
					this.resultsEl.append(wrap);

					if (this.resultsEl.find('li').length >= 10) {
						$('#dp_search_assist').find('.dp-more-link').show();
					} else {
						$('#dp_search_assist').find('.dp-more-link').hide();
					}

					this.open();
				}
			}
		});
	}
});