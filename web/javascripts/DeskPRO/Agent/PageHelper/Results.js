Orb.createNamespace('DeskPRO.Agent.PageHelper');

DeskPRO.Agent.PageHelper.Results = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(page, options) {
		var self = this;

		this.page = page;
		this.wrapper = page.wrapper;

		this.options = {
			/**
			 * The display options helper
			 * Defaults to page.displayOptions
			 * @option {DeskPRO.Agent.PageHelper.DisplayOptions}
			 */
			displayOptions: null,

			/**
			 * The container to append results to
			 * Defaults to 'wrapper .list-listing'
			 * @option {jQuery}
			 */
			resultsContainer: null,

			/**
			 * The row that contains the "more" button, "no more results" etc
			 * Defaults to 'wrapper footer.results-nav'
			 * @option {jQuery}
			 */
			navEl: null,

			/**
			 * The [xx] in "Showing [xx] of xx results" line
			 * Defaults to 'wrapper .results-showing-count'
			 * @option {jQuery}
			 */
			showingCountEl: null,

			/**
			 * The IDs of all the results
			 * @option {Array}
			 */
			resultIds: null,

			/**
			 * Total count of results
			 * @option {Integer}
			 */
			totalCount: 0,

			/**
			 * The wrapper around a result row item
			 * @option {String}
			 */
			resultRowSelector: 'article.row-item',

			/**
			 * How many results to show per page
			 * @option {Integer}
			 */
			perPage: 50,

			/**
			 * Refresh mode loads a new page instead of injecting new
			 * rows into the already loaded page
			 *
			 * @option {Boolean}
			 */
			refreshMode: false,

			/**
			 * @option {Integer}
			 */
			currentPage: 1,

			tabResultCountEls: null,

			preFetchCallback: null,

			infiniteScroll: false,

			infiniteScrollTriggerOffset: 800,

			infiniteScrollLoadFilter: null,

			infiniteScrollTarget: null
		};
		this.setOptions(options);

		this.displayOptions    = this.options.displayOptions || page.displayOptions;
		this.resultsContainer  = this.options.resultsContainer || $('.list-listing', this.wrapper);
		this.navEl             = this.options.navEl || $('footer.results-nav', this.wrapper);
		this.showingCountEl    = this.options.showingCountEl || $('.results-showing-count', this.wrapper);
		this.totalCountEl      = this.options.totalCountEl || $('.results-total-count', this.wrapper);
		this.tabResultCountEls = this.options.tabResultCountEls || this.wrapper.find('.results-count-display');

		this.pageNav           = $('ul.pagenav', this.navEl);
		this.prevBtn           = $('> li.prev', this.pageNav);
		this.nextBtn           = $('> li.next', this.pageNav);

		this.resultCount   = this.options.resultIds ? this.options.resultIds.length : this.options.totalCount;

		this.scrollableEl = this.resultsContainer.closest('.with-scrollbar');
		if (this.options.infiniteScroll) {
			this.options.infiniteScroll = this.scrollableEl.length > 0;
		}

		this.resultIds = this.options.resultIds;
		delete this.options.resultIds;

		this.numPages = Math.ceil(this.resultCount / this.options.perPage);
		this.currentPage = this.options.currentPage;

		if (this.currentPage <= 1) {
			this.pageNav.addClass('no-prev');
		} else {
			this.pageNav.removeClass('no-prev');
		}

		if (this.currentPage >= this.numPages) {
			this.pageNav.addClass('no-next');
		} else {
			this.pageNav.removeClass('no-next');
		}

		this.updateShowingCount();

		this.pageNav.on('click', '.prev', this.loadPrevPage.bind(this));
		this.pageNav.on('click', '.next', this.loadNextPage.bind(this));

		if (this.options.infiniteScroll) {
			this.pageNav.closest('.results-nav').hide();

			var onScrollTimer;
			var self = this;

			this.scrollableEl.on('dp_scroll.infinite', function() {
				if (!onScrollTimer) {
					var scrollEl = $(this);
					onScrollTimer = setTimeout(function() {
						onScrollTimer = false;

						if (self.getCurrentPage() + 1 > self.getNumPages()) {
							self.scrollableEl.unbind('dp_scroll.infinite');
							return;
						}

						var scrollBottom = (scrollEl.data('dp-scroll-pos') + scrollEl.data('dp-scroll-viewport')),
							totalHeight = scrollEl.data('dp-scroll-height');

						if (totalHeight - scrollBottom < self.options.infiniteScrollTriggerOffset) {
							self.loadNextPage();
						}
					}, 25);
				}
			})
		}
	},


	/**
	 * Get the current page number (1-based)
	 *
	 * @return {Integer}
	 */
	getCurrentPage: function() {
		return this.currentPage;
	},


	/**
	 * Get the total number of pages
	 *
	 * @return {Integer}
	 */
	getNumPages: function() {
		return this.numPages;
	},


	/**
	 * Get IDs for a page
	 *
	 * @param {Integer} pageNum
	 */
	getPageIds: function(pageNum) {
		if (pageNum < 1 || pageNum > this.numPages) {
			console.warn("Requesting page " + pageNum + " when there are only " + this.numPages);
			return [];
		}

		return this.resultIds.slice((pageNum-1) * this.options.perPage, pageNum * this.options.perPage);
	},

	setResultCount: function(count) {
		if (!this.options) {
			return;
		}

		this.resultCount = count;
		this.numPages = Math.ceil(this.resultCount / this.options.perPage);

		this.pageNav.removeClass('no-prev no-next');
		if (this.currentPage == 1) {
			this.pageNav.addClass('no-prev');
		} else if (this.currentPage >= this.numPages) {
			this.pageNav.addClass('no-next');
		}

		this.totalCountEl.text(this.resultCount);
		this.tabResultCountEls.text(this.resultCount);
		this.updateShowingCount();
	},

	adjustResultCount: function(adjust) {
		this.setResultCount(this.resultCount + adjust);
	},


	/**
	 * Load the next page in the results
	 */
	loadNextPage: function() {
		var nextPage = this.getCurrentPage() + 1;
		if (nextPage > this.getNumPages()) {
			return;
		}

		return this.loadNewPage(nextPage);
	},

	/**
	 * Load the next page in the results
	 */
	loadPrevPage: function() {
		var prevPage = this.getCurrentPage() - 1;
		if (prevPage < 1) {
			return;
		}

		return this.loadNewPage(prevPage);
	},

	/**
	 * Load a new page
	 *
	 * @param {Integer} pageNum
	 */
	loadNewPage: function(pageNum) {

		// Already running
		if (this.navEl.is('.loading')) {
			return;
		}

		if (this.options.refreshMode) {
			var url = this.page.meta.refreshUrl;
			url = Orb.appendQueryData(url, 'p', pageNum);
			DeskPRO_Window.runPageRoute('listpane:' + url);
			return;
		}

		if (this.options.infiniteScroll) {
			pageNum = this.getCurrentPage() + 1; // only thing that can be loaded
		}

		var evData = {html: null}, html = null;

		this.setPage(pageNum);

		this.fireEvent('loadResultPage', [evData]);

		if (evData.html !== null) {
			html = evData.html;
			this.setNewResults(html);
		} else {
			this.showLoading();

			var data = [];
			if (this.resultIds) {
				Array.each(this.getPageIds(pageNum), function(i) {
					data.push({name: 'result_ids[]', value: i});
				});
			} else {
				data.push({name: 'page', value: pageNum});
			}
			if (this.displayOptions) {
				Array.each(this.displayOptions.getDisplayFields(), function(i) {
					data.push({name: 'display_fields[]', value: i });
				});
			}

			if (this.options.preFetchCallback) {
				data = this.options.preFetchCallback(data);
			}

			$.ajax({
				url: this.page.meta.fetchResultsUrl,
				data: data,
				type: 'GET',
				dataType: 'html',
				context: this,
				complete: function() {
					this.hideLoading();
				},
				success: function(html) {
					this.setNewResults(html);
				}
			});
		}
	},

	setPage: function(pageNum, updateShowing) {
		this.currentPage = pageNum;

		this.pageNav.removeClass('no-prev no-next');
		if (pageNum == 1) {
			this.pageNav.addClass('no-prev');
		} else if (pageNum == this.numPages) {
			this.pageNav.addClass('no-next');
		}

		if (updateShowing) {
			this.updateShowingCount();
		}
	},


	/**
	 * Render new results to the page
	 *
	 * @param html
	 */
	setNewResults: function(html) {
		if (!this.options) {
			return;
		}

		var results = $(html);
		DeskPRO_Window.initInterfaceServices(results);

		if (this.options.infiniteScroll) {
			if (this.options.infiniteScrollLoadFilter) {
				results = this.options.infiniteScrollLoadFilter(results);
			}
			if (this.options.infiniteScrollTarget) {
				this.options.infiniteScrollTarget.append(results);
			} else {
				this.resultsContainer.append(results);
			}
			this.scrollableEl.trigger('scrollupdate');
		} else {
			this.resultsContainer.empty().html(results);

			if (this.scrollableEl.length) {
				this.scrollableEl.trigger('goscrolltop');
			}
		}

		if (this.options.postSetNewResults) {
			this.options.postSetNewResults();
		}

		this.updateShowingCount();

		this.fireEvent('postSetNewResults', [this, this.resultsContainer, results]);
	},


	/**
	 * Show the 'loading' message
	 */
	showLoading: function() {
		if (this.page.meta.viewType == 'list') {
			return;
		}
		this.navEl.addClass('loading');
	},


	/**
	 * Remove the 'loading' message
	 */
	hideLoading: function() {
		this.navEl.removeClass('loading');
	},


	/**
	 * Show the 'no more results' element
	 */
	showNoMore: function() {
		this.navEl.removeClass('loading');
		this.navEl.addClass('no-more-results');
	},


	/**
	 * Update the showing xxx of xxx line by counting the rows currently displayed
	 */
	updateShowingCount: function() {
		if (!this.options) {
			return false;
		}

		var showingCount = $(this.options.resultRowSelector, this.resultsContainer).length || 0;

		var start = ((this.currentPage-1) * this.options.perPage);
		var end = start + showingCount;
		start++;

		if (this.resultIds) {
			if (end > this.resultIds.length) {
				end = this.resultIds.length;
			}
		} else {
			if (end > this.resultCount) {
				end = this.resultCount;
			}
		}

		this.showingCountEl.empty().text(start + '-' + end);
		this.tabResultCountEls.text(this.resultCount);

		return showingCount;
	},


	/**
	 * Add a result ID to the beginning of the array
	 *
	 * @param resultId
	 */
	prependResultId: function(resultId) {
		if (this.resultIds) {
			this.resultIds.unshift(resultId);
		}
	},


	/**
	 * Remove an ID from the result set
	 *
	 * @param resultId
	 */
	removeResultId: function(resultId) {
		if (this.resultIds) {
			this.resultIds.erase(resultId);
		}
	},

	destroy: function() {
		this.options = null;
		this.resultPages = null;
	}
});
