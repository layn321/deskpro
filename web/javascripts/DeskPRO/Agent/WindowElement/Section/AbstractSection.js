Orb.createNamespace('DeskPRO.Agent.WindowElement.Section');

/**
 * A section is button in the first column and a corresponding 'section' in
 * the outline (col2) pane. When a button is clicked, the section element,
 * if it exists, is displayed automatically by the Window object.
 *
 * But its really up to this section handler how things are actually loaded.
 * Use the events if you want to unload/reload things when the section changes.
 *
 * If you set a section element, make sure it's retrievable via getSectionElement
 * or it wont be displayed. By default it'll return this.sectionEl.
 *
 * Generally here's how things work:
 * - The section element is loaded or created on init() and set using setSectionElement()
 * - Some kind of data poller may be set up to fetch updates for the section, but using
 * the onShow/onHide events the frequency might be increased/decreased based on if its in view or not
 */
DeskPRO.Agent.WindowElement.Section.AbstractSection = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function() {
		var self = this;
		this.addEvent('show', this.onShow);
		this.addEvent('show', this._onFirstShowFire);
		this.addEvent('show', this._onShowSetVisible);
		this.addEvent('show', this._onShowActivateList);
		this.addEvent('show', function() {
			DeskPRO_Window.updateWindowUrlFragment();
			if (this.hasLoaded) {
				$('#dp_source_loading').removeClass('on');
				if (DeskPRO_Window.fragLoadingSection && DeskPRO_Window.fragLoadingSection == this.section_id) {
					DeskPRO_Window.fragLoadingSection = null;
				}
			}
		}, this);
		this.addEvent('firstshow', this.onFirstShow);
		this.addEvent('hide', this.onHide);
		this.addEvent('hide', this._onHideSetVisible);
		this.addEvent('hide', this._onHideDeactivateList);

		this.addEvent('sectionInit', function() {
			if (self.contentEl) {
				var scrollEl = $('.with-scrollbar', self.contentEl).first();
				if (scrollEl.length && !scrollEl.is('.scroll-setup')) {
					new DeskPRO.Agent.ScrollerHandler(null, scrollEl);
				}
			}

			if (self.sectionEl) {
				self.sectionEl.find('.pill-two-switcher').each(function() {
					$(this).find('li').each(function() {
						var target = $(this).data('tab-for');
						if (target) {
							$(target).on('dp_simpletabs_show', function() {
								self.updateUi();
							});
						}
					});
				});
			}
		});

		this._isVisible = false;

		this.init();

		// Simulate instant switching when clicking nav items
		this.getSectionElement().on('click', '[data-route]', function(ev) {
			self.highlightNavItem($(this));
		});
	},

	highlightNavItem: function(el) {

		if (!el.is('.is-nav-item')) {
			var el = el.closest('.is-nav-item');
			if (!el) {
				return;
			}
		}

		$('.nav-selected', this.getSectionElement()).removeClass('nav-selected');
		el.addClass('nav-selected');
	},

	/**
	 * Override init method
	 */
	init: function() {},

	onShow: function() { },
	onFirstShow: function() { },
	onHide: function() { },

	setHasInitialLoaded: function() {
		this.hasLoaded = true;
		$('#dp_source_loading').removeClass('on');

		if (DeskPRO_Window.fragLoadingSection && DeskPRO_Window.fragLoadingSection == this.section_id) {
			DeskPRO_Window.fragLoadingSection = null;
		}
	},

	updateUi: function() {
		if (this.sectionEl) {
			this.sectionEl.find('.with-scroll-handler').each(function() {
				var sh = $(this).data('scroll_handler');
				if (sh && sh.updateSize) {
					sh.updateSize();
				}
			});
		}
	},


	/**
	 * Sets the standard button element, and then you can use this.buttonEl thereafter.
	 *
	 * @param {jQuery}
	 */
	setButtonElement: function(el) {
		this.buttonEl = el;
	},


	/**
	 * Get the button element (or use this.buttonEl)
	 *
	 * @return {jQuery}
	 */
	getButtonElement: function() {
		return this.buttonEl;
	},


	/**
	 * Get the section element (this.sectionEl).
	 *
	 * @return {jQuery}
	 */
	getSectionElement: function() {
		if (this.sectionEl) {
			return this.sectionEl;
		}

		return null;
	},


	/**
	 * Get the list element (this.listEl). If it doesn't exist, it'll be created automatically.
	 *
	 * @return {jQuery}
	 */
	getListElement: function() {
		if (!this.listEl) {
			this.setListElement();
		}

		return this.listEl;
	},


	/**
	 * Sets the section element (this.sectionEl), and it's inner content element (this.contentEl).
	 * The content element is where you should actually render content to. Generally a scrollbar
	 * is attached, and the section element is fixed and the content element overflows.
	 *
	 * @param {jQuery} el
	 * @param {jQuery} contentEl
	 */
	setSectionElement: function(el, contentEl) {
		if (this.sectionEl) {
			this.sectionEl.remove();
		}

		if (!el) {
			el = $('<section></section>');
			el.attr('id', Orb.getUniqueId('outline_'));
		}

		this.sectionEl = el;
		if (!el.parent().is('#dp_source')) {
			this.sectionEl.detach().appendTo('#dp_source');
		}

		if (!contentEl) {
			contentEl = $('.source-pane-instance', el);
			if (!contentEl[0]) {
				contentEl = $('section.content', el);
				if (!contentEl.length) {
					var html = [];
					html.push('<div class="source-pane-wrapper"></div>');
					html = html.join('');

					el = $(html);
					this.sectionEl.append(el);
					contentEl = el;
				}
			}
		}
		this.contentEl = contentEl;

		contentEl.on('click', '.pane-tabs li', function(ev) {
			ev.preventDefault();

			contentEl.find('.pane-tabs').find('li').removeClass('active');
			$(this).addClass('active');

			contentEl.find('.pane-content').hide().filter('.'+$(this).data('tab-id')).show().find('.dp-with-activate-listener').triggerHandler('dp_activated');
		});
	},


	/**
	 * Sets the list element (this.listEl) and the inner list content (this.listContentEl). Same feedback
	 * as section element, except its the list column.
	 *
	 * This is a wrapper for 'pages'.
	 *
	 * @param {jQuery} el
	 * @param {jQuery} contentEl
	 */
	setListElement: function(el, contentEl) {
		if (this.listEl) {
			this.listEl.remove();
		}

		if (!el) {
			el = $('<section></section>');
			el.attr('id', Orb.getUniqueId('list_'));
		}

		this.listEl = el;
		if (!el.parent().is('#dp_list')) {
			this.listEl.detach().appendTo('#dp_list');
		}

		if (!contentEl) {
			contentEl = $('<section class="content"></section>');
		}
		this.listEl.append(contentEl);
		this.listContentEl = contentEl;
	},


	/**
	 * Set the Page on the list column
	 *
	 * @param {DeskPRO.Agent.PageFragment.ListPane.Basic} page
	 */
	setListPageFragment: function(page, noswitch) {

		if (this.listPage) {
			this.listPage.fireEvent('destroy');
			this.listPage = null;
		}

		this.listPage = page;

		this.getListElement().remove();
		this.listEl = null;
		this.listContentEl = null;

		var contentEl = $('section.content:first', this.getListElement());
		contentEl.empty();
		contentEl.html(page.html);

		page.fireEvent('render', [contentEl]);

		var scrollEl = $('.with-scrollbar', this.getListElement());
		if (scrollEl.length && !scrollEl.is('.scroll-setup')) {
			page.scrollerHandler = new DeskPRO.Agent.ScrollerHandler(page, scrollEl, {
				showEvent: 'show',
				hideEvent: 'hide'
			});
		}

		$('#dp_list_loading').removeClass('on');

		if (!noswitch) {
			this.getListElement().addClass('on');
			page.fireEvent('activate');
		}
	},


	/**
	 * Check if this section is currently enabled
	 *
	 * @return {Boolean}
	 */
	isVisible: function() {
		return this._isVisible;
	},


	/**
	 * Update the badge number on the icon
	 *
	 * @param {Integer} count
	 */
	updateBadge: function(count) {
		var el = $('.count-badge', this.buttonEl);
		var elCount = $('span', el);

		var count = parseInt(count);
		var countStr = count;
		if (count) {
			if (countStr >= 1000) {
				countStr = '1000+';
			}
			elCount.html(countStr);
			el.show();
		} else {
			count = 0;
			elCount.html('0');
			el.hide();
		}

		this.badgeCount = count;
		DeskPRO_Window.getMessageBroker().sendMessage('agent.ui.badge_updated', {
			section: this,
			sectionId: this.buttonEl.attr('id'),
			count: count
		});
	},

	modBadgeCount: function(op, num) {
		if (!num && num !== 0) {
			num = 1;
		}

		var count = this.getBadgeCount();

		if (op == '-' || op == 'rem' || op == 'del' || op == 'sub') {
			count -= num;
			if (count < 0) count = 0;
		} else if (op == '+' || op == 'add') {
			count += num;
		} else {
			count = num;
		}

		this.updateBadge(count);
	},

	getBadgeCount: function() {
		return this.badgeCount || 0;
	},

	_onShowSetVisible: function() { this._isVisible = true },
	_onHideSetVisible: function() { this._isVisible = false },

	_onFirstShowFire: function(no_load_list) {
		if (this.has_shown) return;
		this.has_shown = true;

		this.fireEvent('firstshow');

		if (!no_load_list && this.hasLoaded) {
			this._loadAutoLoadRoutes();
		}
	},

	_loadAutoLoadRoutes: function(isBackgroundLoad) {

		if (this._hasRunAutoLoadRoutes || this.listPage || !this.hasLoaded) return;
		this._hasRunAutoLoadRoutes = true;

		// Dont autoload if debug setting, or if a page is already loading (e.g., from hash on page refresh)
		if (DeskPRO_Window.getDebug('noAutoLoadList') || (!isBackgroundLoad && DeskPRO_Window.loadingListPage)) {
			return;
		}

		var el = $('.auto-load-route', this.sectionEl).first();
		if (!el[0]) {
			el = this.sectionEl.find('.is-nav-item').first().addClass('auto-load-route');
			if (!el[0]) {
				return;
			}
		}


		// Find the first route
		if (!el.data('route')) {
			el = el.find('[data-route]').first();
			if (!el.length) {
				return;
			}
		}

		if (isBackgroundLoad || DeskPRO_Window.fragLoadingSection) {
			DeskPRO_Window.runPageRoute(el.data('route'), { isBackgroundLoad: true });
		} else {
			DeskPRO_Window.runPageRoute(el.data('route'));
		}

		this.highlightNavItem(el);
	},

	_onShowActivateList: function() {
		if (this.listPage) {
			this.listPage.fireEvent('activate');
		}

		if (this.hasLoaded) {
			$('#deskpro_outline_loading').hide();
		}
	},

	_onHideDeactivateList: function() {
		if (this.listPage) {
			this.listPage.fireEvent('deactivate');
		}
	}
});