Orb.createNamespace('DeskPRO.Agent.ElementHandler');

/**
 * TODO: Refactor out common bits from PersonSearchBox
 *
 * Data options:
 * touch-focus: When the box is in focus, show the list immediately (even if no text and no change)
 * super-container: The selector that represents the master container. Whena click bubbles to this container, the box is closed. Defaults to document
 * position-bound: The selector of the element to bind the box position to. Defaults to this element.
 * search-url: The AJAX search url
 * search-term: The name of the 'term' to pass in the querystring
 * highlight-term: Attempt to highlight the searched text string in the results
 */
DeskPRO.Agent.ElementHandler.OrgSearchBox = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	initPage: function() {
		var self = this;
		this.el.data('org-search-box', this);

		this.termInput   = $('input.org-name', this.el);
		this.idInput     = $('input.org-id', this.el);
		this.resultsBox  = $('.person-search-box', this.el);
		this.resultsList = $('.results-list', this.resultsBox);

		// Always show results when the box is in focus
		this.termInput.on('focus', function() {
			self.open();
			if (self.el.data('touch-focus')) {
				// double touch forces an update
				self.updateCaller.touch(self.getTerm(), true);
			}
		});

		this.el.on('dp_hide', function() {
			self.close();
		});
	},


	/**
	 * Inits the search box to ensre it can be positioned properly
	 */
	_initResultsBox: function() {
		var self = this;

		if (this._hasInitResultsBox) return;
		this._hasInitResultsBox = true;

		this.tplHtml = DeskPRO_Window.util.getPlainTpl($('.user-row-tpl', this.el));

		//------------------------------
		// Update caller schedules the update requests
		//------------------------------

		var updateCaller;
		this.updateCaller = new DeskPRO.TouchCaller({
			timeout: 500,
			callback: this.updateResults,
			context: this
		});
		updateCaller = this.updateCaller;

		//------------------------------
		// Input events
		//------------------------------

		// Touch the timer so we will search in a few seconds
		this.termInput.on('keyup', function() { updateCaller.touch(self.getTerm()); }).on('change', function() { updateCaller.touch(self.getTerm()); });

		// Stop bubbling so it doesnt reach the document and close itself
		this.termInput.on('click', function(ev) { ev.stopPropagation(); });
		this.resultsBox.on('click', function(ev) { ev.stopPropagation(); });

		if (this.el.data('super-container')) {
			this.el.closest(this.el.data('super-container')).on('click', this.close.bind(this));
		} else {
			$(document).on('click', this.close.bind(this));
		}

		//------------------------------
		// Clicking on an item fires an event that
		// the page controller can listen to
		//------------------------------

		this.resultsList.on('click', 'li', function(ev) {
			ev.preventDefault();
			var orgId = $(this).data('org-id');
			var name  = $('.org-name', this).text().trim();

			self.termInput.val(name);
			self.idInput.val(orgId);
			self.el.removeClass('is-new').addClass('is-set');

			self.wasSet = true;
			self.close();

			self.el.trigger('orgsearchboxclick', [orgId, name, self]);
		});

		$('.create-org', this.resultsBox).on('click', function(ev) {
			ev.preventDefault();

			self.idInput.val('0');
			self.el.addClass('is-new').removeClass('is-set');

			self.wasSet = true;
			self.close();

			self.el.trigger('orgsearchboxcreate', [self.getTerm(), self]);
		});

		//------------------------------
		// Bound element: The element to show the results box under
		//------------------------------

		// Figure out the element the resultsbox is bound to
		this.boundEl = this.termInput;
		if (this.el.data('position-bound')) {
			var boundDesc = this.el.data('position-bound');
			if (boundDesc[0] == '#') {
				this.boundEl = $(boundDesc);
			} else if (boundDesc == '@self') {
				this.boundEl = this.el;
			} else if (boundDesc.test(/^@parent\((.*?)\)$/)) {
				var sel = boundDesc.match(/^@parent\((.*?)\)$/)[1];
				this.boundEl = this.el.closest(sel);
			} else {
				this.boundEl = $(boundDesc, this.el);
			}
		}

		if (!this.boundEl || !this.boundEl.length) {
			DP.console.error('Could not find position-bound element %s on %o', this.el.data('position-bound'), this);
		}

		this.resultsBox.detach().hide().appendTo('body');
	},


	/**
	 * Reset the box back to empty
	 */
	reset: function() {
		this.termInput.val('');
		this.resultsList.empty();
	},


	/**
	 * Refresh the position of the search box relative to its bound element.
	 */
	refreshPosition: function() {
		var termPos = this.boundEl.offset();
		var termW   = this.boundEl.outerWidth() + 6;
		var termH   = this.boundEl.outerHeight();

		this.resultsBox.css({
			top: termPos.top + termH - 1,
			left: termPos.left - 1,
			width: termW
		});
	},


	/**
	 * Get the search term in the box
	 *
	 * @return {String}
	 */
	getTerm: function() {
		return this.termInput.val().trim();
	},


	/**
	 * Sends the ajax request to find users that match the term in the search box
	 */
	updateResults: function() {

		var url = this.el.data('search-url');
		var term = this.getTerm();

		var postData = [];
		postData.push({
			name: this.el.data('search-param') || 'term',
			value: term
		});

		this.runningAjax = $.ajax({
			type: 'GET',
			url: url,
			data: postData,
			dataType: 'json',
			context: this,
			complete: function() {
				this.runningAjax = null;
			},
			success: function(data) {
				this.resultsList.empty();

				Array.each(data, function(org) {
					var row = $(this.tplHtml);

					row.data('org-id', org.id);
					row.attr('org-id', org.id);

					if (this.el.data('highlight-term')) {
						var term  = Orb.escapeHtml(this.getTerm());
						var name  = Orb.escapeHtml(org.name);

						term = (term+'').replace(/([\\\.\+\*\?\[\^\]\$\(\)\{\}\=\!\<\>\|\:])/g, "\\$1");
						name = name.replace( new RegExp( "(" + term + ")", 'gi' ), '<span class="highlight">$1</span>' );

						$('.org-name', row).html(name);

					} else {
						$('.org-name', row).text(org.name);
					}

					this.resultsList.append(row);
				}, this);
			}
		});
	},


	/**
	 * Opens the results box
	 */
	open: function() {
		this.origValue = this.termInput.val();
		this.wasSet = false;

		this._initResultsBox();

		this.refreshPosition();
		this.resultsBox.show();
	},


	/**
	 * Closes the results box and stops any updating stuff
	 */
	close: function() {
		if (this.resultsBox) {
			this.resultsBox.hide();
		}

		if (!this.idInput) {
			return;
		}

		if (!this.wasSet) {
			if (!this.termInput.val().trim().length) {
				this.idInput.val('0');
				this.el.removeClass('is-new').removeClass('is-set');
				this.el.trigger('orgsearchboxcleared', [this]);
			} else {
				this.termInput.val(this.origValue);
				this.el.trigger('orgsearchreverted', [this.getTerm(), this]);
			}
		}
	},


	/**
	 * Destroys the widget
	 */
	destroy: function() {
		if (this._hasInitResultsBox) {
			this.resultsBox.remove();
		}

		this.resultsBox = null;
		this.idInput = null;
		this.resultsBox = null;
	}
});
