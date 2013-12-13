Orb.createNamespace('DeskPRO.UI');

/**
 * This is a simple tabbing system where the tab triggers and tab contents can be completely
 * separate, and are linked by attribuets in the source. This tab system simply
 * toggles an 'on' CSS class on elements, so it's up to you to style the elements. For example,
 * content elements without 'on' sholud be display:none etc.
 *
 * There are two elements: tab triggers and tab content. Tab triggers are attached a click event
 * that stitches the 'on' state of all the tab contents. So the tab clicked becoems "on" (and your
 * CSS makes it visible, and the rest invisible).
 *
 * <code>
 *     <li data-tab-for=".some-tab">Some Tab</li>
 *     ...
 *     <div class="some-tab"></div>
 * </code>
 */
DeskPRO.UI.SimpleTabs = new Orb.Class({
	Implements: [Orb.Util.Options, Orb.Util.Events],

	initialize: function(options) {

		// Initial values
		this.options = {
			/**
			 * Elements that will act as tab triggers. Tabs
			 * must have a data-tab-for attribute with a jQuery selector
			 * to define which tab they activate.
			 *
			 * You can add new triggers dynamically with addTriggerElement() later
			 */
			triggerElements: '.tab-trigger',

			/**
			 * This classname is added to tabs and tab content wrappers when they're activated
			 */
			activeClassname: 'on',

			/**
			 * The context for tab contents when executing data-tab-for
			 */
			context: document,

			autoSelectFirst: true,

			/**
			 * The effect to for showing/hiding the tab: slide, fade
			 */
			effect: null
		};

		this.lastActiveTab = null;
		this.triggerEls = null;

		if (options) this.setOptions(options);

		this.triggerEls = this.options.triggerElements;

		if (typeOf(this.triggerEls) == 'string') {
			this.triggerEls = $(this.triggerEls, this.options.context);
		}

		var self = this;
		this.triggerEls.on('click', function(ev) {
			ev.cancel = false;
			ev.tabEl = $(this);

			self.fireEvent('tabClick', [ev]);

			if (!ev.cancel) {
				self._handleTabClick(this, ev);
			}

			self.fireEvent('postTabClick', [ev]);
		});

		if (this.options.autoSelectFirst) {
			var firstTab = this.triggerEls.filter('.' + this.options.activeClassname).filter(':visible').first();
			if (!firstTab.length) {
				firstTab = this.triggerEls.filter(':visible').first();
				if (!firstTab.length) {
					firstTab = this.triggerEls.first();
				}
			}

			// Check again, there might not be any tabs
			if (firstTab.length) {
				// need to hide all others
				var self = this;
				this.triggerEls.each(function() {
					self.getContentElFromTab($(this)).hide();
				});

				this.activateTab(firstTab);
			}
		}
	},

	addTriggerElement: function(el) {
		var self = this;

		this.triggerEls.add(el);
		el.on('click', function(ev) {
			ev.cancel = false;
			ev.tabEl = $(this);

			self.fireEvent('tabClick', [ev]);

			if (!ev.cancel) {
				self._handleTabClick(this, ev);
			}
		});
	},

	_handleTabClick: function(el, event) {
		var tab = $(el);
		this.activateTab(tab, event);
	},

	activateTab: function(tabEl, event) {

		if (!tabEl) {
			return;
		}

		tabEl = $(tabEl);

		if (this.lastActiveTab && this.lastActiveTab.get(0) == tabEl.get(0)) {
			return;
		}

		var eventData = {
			event: event || null,
			tabEl: tabEl,
			lastTabEl: this.lastActiveTab,
			tabContent: this.getContentElFromTab(tabEl),
			manager: this,
			cancel: false
		};

		this.fireEvent('beforeTabSwitch', eventData);

		if (eventData.cancel) {
			return;
		}

		delete eventData['cancel'];

		var showFn = (function() {
			this.lastActiveTab = tabEl;
			this.lastActiveTab.addClass(this.options.activeClassname);
			var x = eventData.tabContent.addClass(this.options.activeClassname);

			this.lastActiveTabContent = eventData.tabContent;

			var parentContainer = eventData.tabContent.closest('.tabViewDetailContent, .with-page-fragment').first();
			if (parentContainer) {
				if (parentContainer.data('page-fragment')) {
					parentContainer.data('page-fragment').updateUi();
				} else {
					parentContainer.find('.with-scroll-handler').each(function() {
						if ($(this).data('scroll_handler')) {
							$(this).data('scroll_handler').updateSize();
						}
					});
				}
			}

			if (this.lastActiveTab && this.lastActiveTab.data('tab-on-show')) {
				this.lastActiveTab.data('tab-on-show')(eventData);
			}
			if (this.lastActiveTabContent && this.lastActiveTabContent.data('tab-on-show')) {
				this.lastActiveTabContent.data('tab-on-show')(eventData);
			}

			this.fireEvent('tabSwitch', eventData);

			if (this.lastActiveTabContent.data('load-url') && !this.lastActiveTabContent.data('tab-loaded')) {
				this._triggerTabAjaxLoad(this.lastActiveTab, this.lastActiveTabContent, eventData);
			}

			if (this.options.effect == 'slide') {
				x.slideDown('fast');
			} else if (this.options.effect == 'fade') {
				x.fadeIn('fast');
			} else {
				x.show();
			}

			x.trigger('dp_simpletabs_show');
		}).bind(this);

		if (this.lastActiveTab) {
			this.lastActiveTab.removeClass(this.options.activeClassname);
			var x = this.getContentElFromTab(this.lastActiveTab).removeClass(this.options.activeClassname);

			this.lastActiveTab = null;

			if (this.lastActiveTab && this.lastActiveTab.data('tab-on-hide')) {
				this.lastActiveTab.data('tab-on-hide')(eventData);
			}
			if (this.lastActiveTabContent && this.lastActiveTabContent.data('tab-on-hide')) {
				this.lastActiveTabContent.data('tab-on-hide')(eventData);
			}

			if (this.options.effect == 'slide') {
				x.slideUp('fast', showFn);
			} else if (this.options.effect == 'fade') {
				x.fadeOut('fast', showFn);
			} else {
				x.hide();
				showFn();
			}

			x.trigger('dp_simpletabs_hide');
		} else {
			showFn();
		}
	},

	_triggerTabAjaxLoad: function(tabEl, contentEl, eventData) {
		var self = this;

		contentEl.data('tab-loaded', true);

		delete eventData['cancel'];

		this.fireEvent('beforeTabLoad', eventData);
		if (eventData.cancel) {
			return;
		}

		$.ajax({
			url: contentEl.data('load-url'),
			method: 'get',
			dataType: 'html',
			success: function(html) {
				delete eventData['cancel'];
				self.fireEvent('beforeTabLoaded', eventData);
				if (eventData.cancel) {
					return;
				}

				contentEl.html(html);
				eventData.tabContent = self.getContentElFromTab(tabEl);

				self.fireEvent('tabLoaded', eventData);
			}
		})
	},

	getActiveTab: function() {
		return this.lastActiveTab;
	},

	getActiveTabContent: function() {
		return this.getContentElFromTab(this.getActiveTab());
	},

	getContentElFromTab: function(tabEl) {
		if (!tabEl || !tabEl.data || !tabEl.data('tab-for')) {
			DP.console.error('tab has no tab-for: %o', tabEl);
			if (console && console.trace) console.trace();
			return $();
		}

		if (tabEl.data('tab-for') == 'NOOP') {
			return $();
		}

		var el = $(tabEl.data('tab-for'), this.options.context);

		if (el.length < 1) {
			DP.console.error('no tab content exists for tab: %o', tabEl);
			console.trace();
		}

		return el;
	},

	destroy: function() {

	}
});
