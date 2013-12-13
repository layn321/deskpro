Orb.createNamespace('DeskPRO.Agent.PageHelper');

/**
 * This handles the listing page of content that can be linked with others
 */
DeskPRO.Agent.PageHelper.RelatedContentList = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(page, options) {
		var self = this;
		this.page = page;

		this.options = {
			/**
			 * The main list element that contains rows.
			 */
			contentListEl: null
		};

		this.setOptions(options);

		this.contentListEl = $(this.options.contentListEl);

		DeskPRO_Window.getTabWatcher().addTabTypeWatcher('*', this);
		var types = ['article', 'download', 'news', 'feedback'];

		this.addEvent('watchedTabActivated', function(tab) {
			// Instant feedback: Enable all buttons again, but disable ourselves
			// when the tab actually loads the proper routines will be called
			// and the correct items will be shown/hidden
			$('.related-is-linkable', this.contentListEl).each(function() {
				var el = $(this);
				el.removeClass('related-not-linkable').removeClass('related-is-linked');
			});
			if (tab.page && tab.page.meta.routeData && tab.page.meta.routeData.routeTriggerEl) {
				var row = $(tab.page.meta.routeData.routeTriggerEl);
				if (row.hasClass('row-item')) {
					row.addClass('related-not-linkable').removeClass('related-is-linked');
				}
			}

			var tabtype = DeskPRO_Window.getTabWatcher().getTabType(tab);
			if (types.indexOf(tabtype) !== -1) {

				if (tab.page && tab.page.relatedContent) {
					if (tab.page.relatedContent.isViewing()) {
						$('body').addClass('related-controls-on');
					} else {
						$('body').removeClass('related-controls-on');
					}
				} else {
					$('body').removeClass('related-controls-on');
				}

				this.enableControls(tab);
			} else {
				this.disableControls();
			}
		}, this);
		this.addEvent('watchedTabDeactivated', function(tab, isLast) {
			if (isLast) {
				this.disableControls();
				$('body').removeClass('related-controls-on');
			}
		}, this);

		var selectedTabType = DeskPRO_Window.getTabWatcher().getActiveTabType();
		if (types.indexOf(selectedTabType) !== -1) {
			this.enableControls(DeskPRO_Window.getTabWatcher().getActiveTab());
		}

		var findLinkable = function(el) {
			var x = 0;
			while (!el.is('.related-is-linkable')) {
				if (x++ > 15) return null;
				el = el.parent();
			}

			return el;
		};

		this.contentListEl.on('click', '.related-link', function(ev) {
			ev.stopPropagation();
			ev.preventDefault();

			var el = findLinkable($(this));
			if (!el) {
				return;
			}

			self.fireEvent('relatedLinkClick', [el, $(this), this]);

			var tab = DeskPRO_Window.getTabWatcher().getActiveTab();
			if (!tab || !tab.page || !tab.page.relatedContent) {
				return;
			}

			tab.page.relatedContent.addLinkByElement(el);
		});

		this.contentListEl.on('click', '.related-unlink', function(ev) {
			ev.stopPropagation();
			ev.preventDefault();

			var el = findLinkable($(this));
			if (!el) {
				return;
			}

			self.fireEvent('relatedUnlinkClick', [el, $(this), this]);

			var tab = DeskPRO_Window.getTabWatcher().getActiveTab();
			if (!tab || !tab.page || !tab.page.relatedContent) {
				return;
			}

			tab.page.relatedContent.removeLinkByElement(el);
		});
	},

	enableControls: function(tab) {

		var page = tab.page;
		if (!page.relatedContent) {
			return;
		}

		this.activePage = page;

		page.relatedContent.setActiveRelatedListController(this);

		var self = this;
		$('.related-is-linkable', this.contentListEl).each(function() {
			var el = $(this);
			el.removeClass('related-not-linkable').removeClass('related-is-linked');

			if (page.relatedContent.elementIsLinkable(el)) {
				if (page.relatedContent.elementIsLinked(el)) {
					el.addClass('related-is-linked');
				}
			} else {
				el.addClass('related-not-linkable');
			}
		});

		this.contentListEl.addClass('with-related-content-controls');

		this.fireEvent('relatedControlsActivated', [this.contentListEl, this]);
	},

	disableControls: function() {
		this.activePage = null;
		this.contentListEl.removeClass('with-related-content-controls');
		this.fireEvent('relatedControlsDeactivated', [this.contentListEl, this]);
	},

	elementIsLinked: function(typename, content_id) {
		$('.' + typename + '-' + content_id + '.related-is-linkable', this.contentListEl).addClass('related-is-linked');
	},

	elementIsUnlinked: function(typename, content_id) {
		$('.' + typename + '-' + content_id + '.related-is-linkable', this.contentListEl).removeClass('related-is-linked');
	},

	destroy: function() {
		if (this.activePage) {
			this.activePage.relatedContent.setActiveRelatedListController(null);
		}
	}
});
