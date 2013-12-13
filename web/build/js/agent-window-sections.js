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
Orb.createNamespace('DeskPRO.Agent.WindowElement.Section');

DeskPRO.Agent.WindowElement.Section.Tickets = new Orb.Class({
	Extends: DeskPRO.Agent.WindowElement.Section.AbstractSection,

	init: function() {
		var self = this;
		this.archiveFilterIds = [];
		this.buttonEl = $('#tickets_section');
		this.filterTicketIds = {};
		this.filterCounts = {};
		this.archiveTableFilterIds = [13, 14, 15, 16];

		this.urlFragmentName = 'tickets';

		this.runningRefreshFilterGrouping = [];
		this.rerunRefreshFilterGrouping = [];

		this.collectedFilterUpdates = [];
		this.collectedFilterUpdateOps = {};
		this.queueRefreshFilterGrouping = [];
		this.changedFilterGrouping = [];
		this.hasInitialGroupingLoaded = false;

		this.lastArchiveUpdate = new Date();
		this.loadHighlightNavEl = null;

		this.setSectionElement($('<section id="tickets_outline"></section>'));

		DeskPRO_Window.getSectionData('tickets_section', this._initSection.bind(this));
		DeskPRO_Window.getMessageBroker().addMessageListener('agent.filter-update', this.filterUpdated, this);

		DeskPRO_Window.getMessageChanneler().addEvent('postMessageSend', function() {

			if (!self.collectedFilterUpdates.length && !self.queueRefreshFilterGrouping.length) {
				return;
			}

			var filterIds = self.collectedFilterUpdates;
			if (self.queueRefreshFilterGrouping.length) {
				filterIds.append(self.queueRefreshFilterGrouping);
			}

			var filterOps = self.collectedFilterUpdateOps;

			self.collectedFilterUpdates = [];
			self.collectedFilterUpdateOps = {};
			self.queueRefreshFilterGrouping = [];

			if (filterIds.length) {
				self.refreshFilterGrouping(filterIds, false, filterOps);
				self._recountHold();
			}
		});
	},

	_initSection: function(data) {
		var self = this;
		this.setHasInitialLoaded();

		this.contentEl.html(data.section_html);

		var searchPane = this.contentEl.find('.source-pane-search');
		if (searchPane[0]) {
			this.searchForm = new DeskPRO.Agent.SourcePane.SearchForm(searchPane);
		}

		$('.find-button').on('click', function(ev) {
			$(this).toggleClass('on');
			if ($(this).hasClass('on')) {
				self.contentEl.find('.source-search-area').show();
				self.contentEl.find('.source-main-area').hide();
			} else {
				self.contentEl.find('.source-search-area').hide();
				self.contentEl.find('.source-main-area').show();
			}
		});
		var searchArea = self.contentEl.find('.source-search-area');
		DP.select(searchArea.find('select'));


		searchArea.find('header').on('click', function() {
			$(this).closest('.row').toggleClass('on');
		});

		this.tabs = new DeskPRO.UI.SimpleTabs({
			context: this.sectionEl,
			triggerElements: $('#tickets_outline_tabstrip li'),
			onPostTabClick: function(info) {
				self.updateUi();
			}
		});

		this.filterTicketIds = data.filter_id_matches;
		var archiveFilterIds = [];
		$('#tickets_outline_archive').find('li.is-archive-filter').each(function() {
			archiveFilterIds.push($(this).data('filter-id'));
		});
		this.archiveFilterIds = archiveFilterIds;

		this._initFilters();
		this._initFlagged();

		$('#user_settings_filters_link').on('click', function() {
			var overlay = new DeskPRO.UI.Overlay({
				contentMethod: 'iframe',
				iframeUrl: BASE_URL + 'agent/settings/ticket-filters'
			});

			overlay.openOverlay();
		});

		this.activeNavClass = null;

		$('.hold-ticket-count', this.sectionEl).on('click', function() {
			self.toggleHoldDisplay();
		});

		if ($('#tickets_outline_custom_filters .filter').not('.filter-hidden').length) {
			$('#tickets_outline_custom_filters .no-data').hide();
		} else {
			$('#tickets_outline_custom_filters .no-data').show();
		}

		$('#ticket_customfilters_launch_editor').on('click', function() {
			$('#settingswin').trigger('dp_open', 'filters');
		});
		$('#ticket_slas_launch_editor').on('click', function() {
			$('#settingswin').trigger('dp_open', 'ticketslas');
		});

		if ($('#ticket_slas_header').length) {
			var header = $('#ticket_slas_header');

			DeskPRO_Window.getMessageBroker().addMessageListener('agent.ticket-sla-updated', function(info) {
				self.getUpdatedSlaCounts();
				if (self.listPage && self.listPage.updateSlaListForTicket) {
					self.listPage.updateSlaListForTicket(info);
				} else {
					DeskPRO_Window.getMessageBroker().sendMessage('agent.ui.ticket_updated', { ticket_id: info.ticket_id });
				}
			});

			DeskPRO_Window.getMessageBroker().addMessageListener('agent.ticket-updated', function(info) {
				if (!info.sla_ids || !info.sla_ids.length) {
					return;
				}

				var refresh = false;

				for (var i = 0; i < info.changed_fields.length; i++) {
					switch (info.changed_fields[i]) {
						case 'status':
							refresh = true;
							break;

						case 'agent':
							if (header.data('sla-filter') == 'agent') {
								refresh = true;
							}
							break;

						case 'agent_team':
							if (header.data('sla-filter') == 'team') {
								refresh = true;
							}
					}
				}

				if (refresh) {
					self.getUpdatedSlaCounts();
					if (self.listPage && self.listPage.refreshSlaTicketList && self.listPage.meta.sla_id && $.inArray(self.listPage.meta.sla_id, info.sla_ids) != -1) {
						self.listPage.refreshSlaTicketList();
					}
				}
			});

			this.updateSlaDescription();
		}

		DeskPRO_Window.getMessageBroker().addMessageListener('agent.ticket-draft-updated', function (data) {
			var ticketId = data.ticket_id;

			if (!data.via_person || data.via_person != DESKPRO_PERSON_ID) {
				var tab = DeskPRO_Window.getTabWatcher().findTab('ticket', function(tab) {
					if (tab && tab.page && tab.page.wrapper && tab.page.meta.ticket_id == ticketId) {
						return true;
					}

					return false;
				});

				if (tab) {
					var wrapper = tab.page.wrapper;
					if (data.via_person) {
						wrapper.find('.agent-draft-message.agent-' + data.via_person).remove();
					}
					if (data.draft_html) {
						if (tab.page.meta.ticket_reverse_order) {
							wrapper.find('.ticket-messages .messages-wrap').prepend(data.draft_html);
						} else {
							wrapper.find('.ticket-messages .messages-wrap').append(data.draft_html);
						}
					}
				}
			}
		});

		DeskPRO.ElementHandler_Exec(this.wrapper);


		if (this.loadHighlightNavEl) {
			this.highlightFilterNav(this.loadHighlightNavEl[0], this.loadHighlightNavEl[1]);
		}

		this.sectionEl.find('.dp-toggle-icon').on('click', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();
			ev.stopImmediatePropagation();

			var $me    = $(this);
			var $li    = $me.closest('li');
			var $group = $li.find('> .item-form');
			var $groupList = $li.find('> .nav-list-small');
			var sel = $group.find('select');
			if ($group[0]) {
				if ($me.hasClass('icon-caret-right')) {
					$me.removeClass('icon-caret-right');
					$me.addClass('icon-caret-down');
					$group.show();
					$groupList.show();

					if (!sel.hasClass('with-select2')) {
						DP.select(sel);
						sel.on('change', function(ev) {
							self.refreshFilterGrouping([sel.data('filter-id')], true);
						});
					}
				} else {
					// Remove grouping
					sel.select2('val', '');
					sel.trigger('change');
					self.refreshFilterGrouping([sel.data('filter-id')], true);

					$me.addClass('icon-caret-right');
					$me.removeClass('icon-caret-down');
					$group.hide();
					$groupList.hide();
				}
			}
		});

		var groupingFilterIds = [];
		this.sectionEl.find('.filter_grouping_select').each(function() {
			var sel = $(this);
			var grouping = $(this).val();

			if (grouping && grouping != "") {
				sel.closest('li').find('.icon-caret-right').removeClass('icon-caret-right').addClass('icon-caret-down');
				groupingFilterIds.push($(this).data('filter-id'));

				if (!sel.hasClass('with-select2')) {
					DP.select(sel);

					sel.on('change', function(ev) {
						var filterEl = $(this).closest('li');
						self.changedFilterGrouping.push(parseInt(sel.data('filter-id')));

						if (self.hasInitialGroupingLoaded && $(this).val()) {
							var countEl = filterEl.find('.counter').first();

							if (parseInt(countEl.text().trim()) == 0) {
								var noteEl = filterEl.find('.none-yet');
								noteEl.show();
								window.setTimeout(function() {
									noteEl.fadeOut(2100, function() {
										noteEl.hide();
									});
								}, 2100);
							}
						}

						self.refreshFilterGrouping([sel.data('filter-id')], true);
					});
				}
			}
		});

		if (groupingFilterIds.length) {
			this.refreshFilterGrouping(groupingFilterIds, false);
		} else {
			this.hasInitialGroupingLoaded = true;
		}

		this.fireEvent('sectionInit');
	},

	onShow: function() {
		if (!this.hasLoaded) {
			DeskPRO_Window.getSectionData('tickets_section', this._initSection.bind(this));
		}
		this.activeNavClass = null;
	},

	highlightNav: function() {

		if (this.activeNavClass) {
			var el = $(this.activeNavClass, this.getSectionElement());
			var childSel = $('.nav-selected', el);

			if (!childSel.length) {
				$('.nav-selected', this.getSectionElement()).removeClass('nav-selected');
				el.addClass('nav-selected');
			}
		}
	},

	highlightFilterNav: function(filterId, groupingOption) {
		this.loadHighlightNavEl = [filterId, groupingOption];
		var navLi = $('#system_filters_wrap').find('li.filter-'+filterId);
		if (navLi[0]) {
			$('#system_filters_wrap').find('.nav-selected').removeClass('nav-selected');
			if (groupingOption !== null) {
				navLi.find('li.grouping-' + groupingOption).addClass('nav-selected');
			} else {
				navLi.addClass('nav-selected');
			}
		}
	},

	highlightNavItem: function(el, topGroupingOption) {

		if (!el.is('li')) {
			el = el.closest('li');
		}

		if (topGroupingOption) {
			el = $('.grouping-' + topGroupingOption, el);
		}

		$('.nav-selected', this.getSectionElement()).removeClass('nav-selected');
		el.addClass('nav-selected');
	},

	//#########################################################################
	// Filters/Inbox
	//#########################################################################

	_initFilters: function() {
		var self = this;
		DeskPRO_Window.getPoller().addData(
			function() {
				var filters_data_counts = {};
				Object.each(self.filterTicketIds, function(v, k) {
					filters_data_counts[k] = v.length;
				});
				Object.each(self.filterCounts, function(v, k) {
					if (!filters_data_counts[k]) {
						filters_data_counts[k] = v;
					}
				});

				filters_data_counts = JSON.stringify(filters_data_counts);

				return [
					{name: 'do[]', value: 'get-sys-filters-data'},
					{name: 'filters_data_counts', value: filters_data_counts}
				]
			},
			'filters.filter_data',
			{recurring: true, minDelay: 33000/*33sec*/ }
		);
		DeskPRO_Window.getPoller().addData(
			[{name: 'do[]', value: 'get-custom-filters-data'}],
			'filters.filter_data',
			{recurring: true, minDelay: 68000/*68sec*/, minDelayAfterOne:true }
		);

		DeskPRO_Window.getMessageBroker().addMessageListener('filters.filter_data', this.updateFilterData, this);

		DeskPRO_Window.getMessageBroker().addMessageListener('agent.ticket-updated', function (data) {
			var ticketId = data.ticket_id;

			if (!data.via_person || data.via_person != DESKPRO_PERSON_ID) {
				var tab = DeskPRO_Window.getTabWatcher().findTab('ticket', function(tab) {
					if (tab && tab.page && tab.page && tab.page.meta.ticket_id == ticketId) {
						return true;
					}

					return false;
				});

				if (tab && data.changed_fields) {
					tab.page.doTicketUpdate();
				}
			}

			// And if we're viewing any groups affected by the changed field, then we need to reload the group
			if ($('.show-hold-check', self.getSectionElement()).hasClass('checked')) {
				var filterIds = self.archiveFilterIds;
			} else {
				var filterIds = self.filterTicketIds;
			}

			var refreshFilterIds = [];

			$.each(filterIds, function(filterId) {
				var grouping = self.getGroupingVar(filterId);
				if (!grouping) {
					return;
				}

				if (data.changed_fields.indexOf(grouping) !== -1) {
					refreshFilterIds.push(filterId);
				}
			});

			if (refreshFilterIds.length) {
				self.queueRefreshFilterGrouping.append(refreshFilterIds);
			}
		});

		$('#tickets_outline_inbox_list .sub-toggle').on('click', function(ev) {
			ev.stopPropagation();
			var li = $(this).parent();
			var sub = $('ul.sub-group', li);

			if (sub.is(':visible')) {
				sub.slideUp();
				$(this).removeClass('open');
			} else {
				sub.slideDown();
				$(this).addClass('open');
			}
		});

		// Init counts based on IDs we have cached
		$('li.filter', this.sectionEl).not('.is-archive-filter').each((function(i, el) {
			el = $(el);

			var filterId = parseInt(el.data('filter-id'));
			if (this.filterTicketIds[filterId]) {
				this.setFilterCount(filterId, this.filterTicketIds[filterId].length);
			} else {
				this.setFilterCount(filterId, 0);
			}
		}).bind(this));

		this._recountHold();
	},

	getFilterCount: function(filter_id) {
		return parseInt($('#ticket_filter_' + filter_id + '_count').data('count') || 0);
	},

	modFilterCount: function(filter_id, op) {
		filter_id = parseInt(filter_id);
		var isTilde = $('#ticket_filter_' + filter_id + '_count').text().indexOf('~') !== -1;
		var isPlus = $('#ticket_filter_' + filter_id + '_count').text().indexOf('+') !== -1;
		var count = parseInt($('#ticket_filter_' + filter_id + '_count').data('count'));

		if (op == 'add') {
			count++;
		} else {
			count--;
		}

		if (count < 0) {
			count = 0;
		}

		var countStr = count;
		if (isTilde) {
			countStr = '~' + count;
		}

		// "10000+" should not change when +1'ing
		if (isPlus) {
			$('#ticket_filter_' + filter_id + '_count').data('count', count);
		} else {
			$('#ticket_filter_' + filter_id + '_count').html(countStr).data('count', count);
			$('#ticket_filter_' + filter_id + '_count2').html(count);
		}
	},

	setFilterCount: function(filter_id, count) {

		var count_str = count;
		filter_id = parseInt(filter_id);

		var count_str_real = count_str;
		if (count >= 10000) count_str = '10000+';

		if (this.archiveTableFilterIds.indexOf(filter_id) != -1 && count > 0 && count < 10000) {
			count_str = '~' + count;
		}

		var system_name = DeskPRO_Window.getData('systemFilters')[filter_id];
		if (system_name) {

			if (system_name == 'all') {
				this.updateBadge(count);
			}

			var el = $('#ticket_filter_' + filter_id + '_count').html(count_str).data('count', count);
			$('#ticket_filter_' + filter_id + '_count2').html(count_str_real);

			if (el.is('.is-hold-filter')) {
				this._recountHold();
			}
		} else {
			var el = $('#ticket_filter_' + filter_id + '_count').html(count_str).data('count', count);
			$('#ticket_filter_' + filter_id + '_count2').html(count_str_real);
		}
	},

	_recountHold: function() {
		var total = 0;
		$('#tickets_outline_sys_hold_filters li.filter', this.sectionEl).each((function(i, el) {
			el = $(el);

			var filterId = parseInt(el.data('filter-id'));
			if (this.filterTicketIds[filterId]) {
				if (el.hasClass('filter-all_w_hold')) {
					total += this.filterTicketIds[filterId].length;
				}
				this.setFilterCount(filterId, this.filterTicketIds[filterId].length);
			} else {
				this.setFilterCount(filterId, 0);
			}
		}).bind(this));

		$('#tickets_outline_sys_filters li.filter', this.sectionEl).each((function(i, el) {
			el = $(el);

			var filterId = parseInt(el.data('filter-id'));
			if (this.filterTicketIds[filterId]) {
				this.setFilterCount(filterId, this.filterTicketIds[filterId].length);
			} else {
				this.setFilterCount(filterId, 0);
			}
		}).bind(this));

		var hold = $('.hold-ticket-count', this.sectionEl);
		if (total < 1) {
			hold.hide();
			if ($('.show-hold-check', this.getSectionElement()).hasClass('checked')) {
				this.toggleHoldDisplay();
			}
		} else {
			$('.count', hold).text(total);
			hold.show();
		}
	},

	updateFilterData: function(rawdata) {

		var data = rawdata.ids;
		var datacounts = rawdata.counts;

		var viewingFilterId = null;
		var refreshUrl = null;
		if (this.listPage && this.listPage.meta) {
			if (this.listPage.meta.filter_id) {
				viewingFilterId = this.listPage.meta.filter_id;
			}
			if (this.listPage.meta.refreshUrl) {
				refreshUrl = this.listPage.meta.refreshUrl;
			}
		}

		// The message only contains a list when the counts are off
		// So unchanged filters dont send a large payload
		Object.each(this.filterTicketIds, function(v, k) {
			if (!data[k]) {
				data[k] = v;
			}
		});

		Object.each(data, function(ticketIds, filterId) {
			filterId = parseInt(filterId);

			var oldCount = 0;
			if (this.filterTicketIds[filterId]) {
				oldCount = this.filterTicketIds[filterId].length;
			}
			var newCount = ticketIds.length;

			this.filterTicketIds[filterId] = ticketIds;
			this.filterCounts[filterId] = ticketIds.length;

			if (oldCount != newCount) {
				this.setFilterCount(filterId, newCount);

				// If we are currently viewing this filter that is out of date, we need to refresh it now
				if (viewingFilterId == filterId && refreshUrl) {
					DeskPRO_Window.runPageRoute('listpane:' + refreshUrl);
				}
			}
		}, this);

		Object.each(datacounts, function(count, filterId) {
			filterId = parseInt(filterId);

			var oldCount = 0;
			if (this.filterCounts[filterId]) {
				oldCount = this.filterCounts[filterId];
			}
			var newCount = count;

			this.filterCounts[filterId] = newCount;

			if (oldCount != newCount) {
				this.setFilterCount(filterId, newCount);

				// If we are currently viewing this filter that is out of date, we need to refresh it now
				if (viewingFilterId == filterId && refreshUrl) {
					DeskPRO_Window.runPageRoute('listpane:' + refreshUrl);
				}
			}
		}, this);
	},

	filterUpdated: function(data) {

		var filterId = parseInt(data.filter_id);
		var ticketId = parseInt(data.ticket_id);
		var filterOps = {};

		if (!this.filterTicketIds[filterId]) {
			this.filterTicketIds[filterId] = [];
		}

		var page = null;
		if (this.listPage && parseInt(this.listPage.meta.filter_id) == filterId) {
			page = this.listPage;
		}

		if (data.op == 'add') {
			if (this.archiveFilterIds.indexOf(filterId) != -1) {
				this.modFilterCount(filterId, 'add');
			} else {
				this.filterTicketIds[filterId].include(ticketId);

				var count = this.filterTicketIds[filterId].length;
				this.setFilterCount(filterId, count);
			}

			filterOps = {ticketId: ticketId, op: 'add'};

			if (page && ticketId) {
				page.handleAutoAdd(ticketId);
			}

		} else if (data.op == 'del') {
			if (this.archiveFilterIds.indexOf(filterId) != -1) {
				this.modFilterCount(filterId, 'del');
			} else {
				this.filterTicketIds[filterId].erase(ticketId);

				var count = this.filterTicketIds[filterId].length;
				this.setFilterCount(filterId, count);
			}

			filterOps = {ticketId: ticketId, op: 'del'};

			if (page && ticketId) {
				page.delTicket(ticketId);
			}
		}

		this.collectedFilterUpdates.push(filterId);
		this.collectedFilterUpdateOps[filterId] = filterOps;
	},

	refreshFilterGrouping: function(filterIds, doSave, filterOps) {

		if (this.queueRefreshFilterGrouping.length) {
			filterIds.append(this.queueRefreshFilterGrouping);
			this.queueRefreshFilterGrouping = [];
		}

		var postData = [];

		var els = [];

		if (this.runningRefreshFilterGrouping && this.runningRefreshFilterGrouping.length) {
			var setFilterIds = [];
			Array.each(filterIds, function(filterId) {
				if (this.runningRefreshFilterGrouping.indexOf(filterId) !== -1) {
					this.rerunRefreshFilterGrouping.include(filterId);
				} else {
					setFilterIds.push(filterId);
				}
			}, this);

			filterIds = setFilterIds;
			if (!filterIds || !filterIds.length) {
				return;
			}
		}

		Array.each(filterIds, function(filterId) {
			filterId = parseInt(filterId);
			var filterEl = $('li.filter-' + filterId, this.sectionEl);
			els.push(filterEl.get(0));

			var boundFilterEl = null;
			var boundFilterId = null;
			if (filterEl.data('filter-name')) {
				boundFilterEl = $('.filter-' + filterEl.data('filter-name') + '_w_hold', this.sectionEl);
				boundFilterId = parseInt(boundFilterEl.data('filter-id'));
			}

			if (!this.filterTicketIds[filterId] && (!boundFilterId || !this.filterTicketIds[boundFilterId])) {
				return;
			}

			var grouping = this.getGroupingVar(filterId);

			if (!grouping || !grouping.length) {
				if (!doSave) {
					this.setFilterGroupingContent(filterId, '', grouping);

					if (boundFilterId) {
						this.setFilterGroupingContent(boundFilterId, '', grouping);
					}
					return;
				}
			}

			postData.push({
				name: 'batches['+filterId+'][grouping]',
				value: grouping
			});

			if (grouping) {
				postData.push({
					name: 'batches['+filterId+'][ticket_ids]',
					value: this.filterTicketIds[filterId].join(',')
				});
			}

			if (boundFilterId) {
				if (this.filterTicketIds[boundFilterId]) {
					postData.push({
						name: 'batches['+boundFilterId+'][grouping]',
						value: grouping
					});

					if (grouping) {
						postData.push({
							name: 'batches['+boundFilterId+'][ticket_ids]',
							value: this.filterTicketIds[boundFilterId].join(',')
						});
					}
				} else {
					this.setFilterGroupingContent(boundFilterId, '', grouping);
				}
			}
		}, this);

		if (doSave) {
			postData.push({
				name: 'save_pref',
				value: 1
			});
		}

		// Nothing to do
		if (!postData.length) {
			return;
		}

		var countEls = $('.list-counter', $(els)).first();
		countEls.addClass('loading');

		this.runningRefreshFilterGrouping = filterIds;

		$.ajax({
			url: BASE_URL + 'agent/ticket-search/group-tickets.json',
			type: 'POST',
			dataType: 'json',
			data: postData,
			context: this,
			complete: function() {
				countEls.removeClass('loading');
			},
			success: function(batches) {

				Object.each(batches, function(html,filterId) {

					var filterEl = $('.filter-' + filterId, this.sectionEl);
					var name = filterEl.data('filter-name');
					if (name.indexOf('_w_hold') !== -1) {
						name = name.replace(/_w_hold$/, '');
						parentFilterEl = $('.filter-' + name, this.sectionEl);
						parentFilterId = parseInt(parentFilterEl.data('filter-id'));
						var grouping = this.getGroupingVar(parentFilterId);
					} else {
						var grouping = this.getGroupingVar(filterId);
					}

					var selectedGrouping = filterEl.find('ul.nav-list-small').first().find('li.nav-selected').data('grouping-option');
					var li = filterEl.find('li.grouping-' + selectedGrouping);
					var currentRoute = null;

					if (li.data('route')) {
						currentRoute = li.data('route');
					} else {
						currentRoute = li.find('[data-route]').data('route');
					}

					var count = parseInt(li.find('span.list-counter').text());

					this.setFilterGroupingContent(filterId, html, grouping);

					var hasCurrentSelection = filterEl.find('ul.sub-group').find('li.grouping-' + selectedGrouping);

					// New view doestn have the grouping anymore, meaning it just
					// went to 0
					if (!hasCurrentSelection[0]) {

						if (currentRoute) {
							DeskPRO_Window.runPageRoute(currentRoute, { noChangePaneVis: true });
						}

					// Update currently viewed list if we're viewing a
					// subgrouping and its a non-delete update.
					// - If its a delete, then the ticket is simply removed,
					// any other update would require a server call to see
					// if its visible in this group at all
					} else if (selectedGrouping != 'undefined') {
						filterEl = $('.filter-' + filterId, this.sectionEl);
						li = filterEl.find('li.grouping-' + selectedGrouping);
						if (li[0]) {
							var count2 = parseInt(li.find('span.list-counter').text());
							if (count != count2) {
								// See DeskPRO/Agent/PageFragment/ListPane/BasicTicketResults.js
								// Used to signify that the counts were updated, so the list might need refreshing
								li.addClass('is-stale');

								// Try to find the list
								var listPage = DeskPRO_Window.getListPage();
								if (
										listPage.meta.filter_id
										&& (
											(listPage.meta.filter_id == parseInt(filterId) && listPage.reloadIfStale)
											|| (listPage.meta.filter_id == '5')
											|| (listPage.meta.topGroupingOption) // We are dumb to any grouping, so only way to know if view should be updated is by refreshing
										)
								) {
									if (filterOps && filterOps[filterId] && filterOps[filterId].ticketId && filterOps[filterId].op == 'del') {
										listPage.delTicket(filterOps[filterId].ticketId);
									} else {
										if (li.data('route')) {
											DeskPRO_Window.runPageRouteFromElement(li, { noChangePaneVis: true });
										} else {
											DeskPRO_Window.runPageRouteFromElement(li.find('[data-route]'), { noChangePaneVis: true });
										}
									}
								}
							}
							li.addClass('nav-selected');
						}
					}
				}, this);

				this.runningRefreshFilterGrouping = [];
				if (this.rerunRefreshFilterGrouping && this.rerunRefreshFilterGrouping.length) {
					var refreshIds = this.rerunRefreshFilterGrouping;
					this.rerunRefreshFilterGrouping = [];
					this.refreshFilterGrouping(refreshIds);
				}

				this.hasInitialGroupingLoaded = true;
			}
		});
	},

	getGroupingVar: function(filterId) {
		return $('.filter-' + filterId, this.sectionEl).find('select').val();
	},

	setFilterGroupingContent: function(filterId, html, grouping) {
		var filterEl = $('.filter-' + filterId, this.sectionEl);
		var subgroupEl = $('ul.nav-list-small', filterEl);

		var baseRoute = $('.item', filterEl).first().data('route');

		var existGroupingEl = subgroupEl.find('.nav-selected');
		var existGrouping = existGroupingEl[0] ? existGroupingEl.data('grouping-option') : null;

		subgroupEl.empty();
		if (html.length) {
			subgroupEl.html(html);
		}

		if (existGrouping) {
			subgroupEl.find('.grouping-' + existGrouping).addClass('nav-selected');
		}

		var li = subgroupEl.closest('li');
		var note = filterEl.find('.none-yet');

		var lis = $('> li', subgroupEl);
		if (lis.length) {
			note.stop().hide();
			if (li.find('.icon-caret-down')[0]) {
				subgroupEl.show();
			}

			// Add the proper route to each row
			lis.each(function() {
				var setRoute = Orb.appendQueryData(baseRoute, 'set_group_term', grouping);
				setRoute = Orb.appendQueryData(setRoute, 'set_group_option', $(this).data('grouping-option'));
				$('.item', this).first().data('route', setRoute);
				$('.item', this).first().attr('data-route', setRoute);
			});
		} else {
			subgroupEl.hide();
		}

		this.changedFilterGrouping.erase(parseInt(filterId));

		if ($(this).data('grouping-option') != '') {
			li.find('.item-form').hide();
		}

		this.updateUi();
	},

	toggleHoldDisplay: function() {
		var check = $('.show-hold-check', this.getSectionElement());
		check.toggleClass('checked');

		// Go through each one and figure out which ones change
		// We dont need ot do any filter matching, they are always in the same order

		var counts1 = $('#tickets_outline_sys_filters > li > .title > .list-counter');
		var counts2 = $('#tickets_outline_sys_hold_filters > li > .title > .list-counter');

		var els = [];

		var selectedIndex = null;

		counts1.each(function(i) {

			var other = counts2.eq(i);

			if (selectedIndex === null && ($(this).parent().parent().is('.nav-selected') || other.parent().parent().is('.nav-selected'))) {
				selectedIndex = i;
			}

			var val1 = parseInt($(this).text().trim());
			var val2 = parseInt(other.text().trim());

			if (val1 != val2) {
				$('.list-counter', $(this).parent().parent()).each(function() {
					els.push(this);
				});
				$('.list-counter', other.parent().parent()).each(function() {
					els.push(this);
				});
			}
		});

		els = $(els);

		// Fake "loading" indicator gives impression
		// of change
		els.addClass('loading');

		if (selectedIndex !== null) {
			if (check.is('.checked')) {
				var runEl = $('#tickets_outline_sys_hold_filters li').eq(selectedIndex).addClass('nav-selected');
			} else {
				var runEl = $('#tickets_outline_sys_filters li').eq(selectedIndex).addClass('nav-selected');
			}

			DeskPRO_Window.runPageRouteFromElement($('h3', runEl).first());
		}

		window.setTimeout(function() {

			if (check.is('.checked')) {
				$('#tickets_outline_sys_filters').hide();
				$('#tickets_outline_sys_hold_filters').show();

				$('#tickets_outline_sys_filters li.nav-selected').removeClass('nav-selected');
			} else {
				$('#tickets_outline_sys_hold_filters').hide();
				$('#tickets_outline_sys_filters').show();

				$('#tickets_outline_sys_hold_filters li.nav-selected').removeClass('nav-selected');
			}

			els.removeClass('loading');
		}, 310);
	},

	//#########################################################################
	// Flagged
	//#########################################################################

	_initFlagged: function() {
		DeskPRO_Window.getMessageBroker().addMessageListener('filter-flagged.counts', this.updateFlagCounts, this);
		DeskPRO_Window.getMessageBroker().addMessageListener('filter-flagged.flag-changed', this.changeFlagCountsForSwitch, this);

		//------------------------------
		// Reorder flags
		//------------------------------

		var self = this;
		$('#tickets_outline_flagged ul').sortable({
			'axis': 'y',
			'distance': 8,
			'update': function() {
				var data = [];

				$('#tickets_outline_flagged ul > li').each(function() {
					var flag = $(this).data('flag');
					if (flag) {
						data.push({ name: 'prefs[agent.ui.ticket-flag-order][]', value: flag });
					}
				});

				$.ajax({
					timeout: 20000,
					type: 'POST',
					url: BASE_URL + 'agent/misc/ajax-save-prefs',
					data: data
				});
			}
		});

		//------------------------------
		// Renaming flags
		//------------------------------

		$('#tickets_outline_flagged li').on('dblclick', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			var li = $(this);
			var inputEl = li.find('.flag-label-input');
			var labelEl = li.find('.flag-label');
			if (!inputEl.hasClass('init')) {
				inputEl.addClass('init');
				inputEl.on('blur', function() {
					closeFn();
				});

				inputEl.on('click', function(ev) {
					Orb.cancelEvent(ev);
				});

				inputEl.on('keypress keydown', function(ev) {
					if (ev.keyCode == 13) {
						closeFn();
					}
				});
			}
			var closeFn = function() {

				var newTitle = inputEl.val().trim();
				if (newTitle.length) {
					$.ajax({
						type: 'POST',
						url: BASE_URL + 'agent/misc/ajax-save-prefs',
						data: [{
							name: 'prefs[agent.ui.flag.' + li.data('flag') + ']',
							value: newTitle
						}]
					});

					labelEl.text(newTitle);
				}

				inputEl.hide();
				labelEl.show();
				backdrop.remove();
			};

			var backdrop = $('<div class="backdrop"></div>');
			backdrop.css('left', 270);
			backdrop.appendTo('body');
			backdrop.on('click', closeFn);

			inputEl.val(labelEl.text().trim());

			labelEl.hide();
			inputEl.show();
			inputEl.focus().val(labelEl.text().trim()).focus();
		});
	},

	updateFlagCounts: function(counts) {

		$('ol#ticket_flagged_list span.list-counter').html('0');

		Object.each(counts, (function (count, flag) {
			this.updateFlagCountFor(flag, count);
		}).bind(this))
	},

	updateFlagCountFor: function(flag, count) {
		var count_str = count;
		if (count >= 10000) {
			count_str = '10000+';
		} else if (count < 0) {
			count = 0;
			count_str = '0';
		}

		var el = $('#ticket_flag_' + flag + '_count').text(count_str);
	},

	changeFlagCountsForSwitch: function(info) {
		if (info.old_flag) {
			var old_flag_count = parseInt($('#ticket_flag_' + info.old_flag + '_count').text());
			this.updateFlagCountFor(info.old_flag, old_flag_count-1);
		}

		var new_flag_count = parseInt($('#ticket_flag_' + info.new_flag + '_count').text());
		this.updateFlagCountFor(info.new_flag, new_flag_count+1);
	},

	//#########################################################################
	// Misc
	//#########################################################################

	/**
	 * Remove a filter row. This remove doesnt need to be perfect, its used from the settings window
	 * when you update filters. After the settings overlay is closed, the page is refreshed, this is just
	 * instant feedback.
	 */
	removeCustomFilter: function(id) {
		$('#tickets_outline_custom_filters').find('li.filter-' + id).remove();
		if (!$('#tickets_outline_custom_filters').find('li.filter')[0]) {
			$('#tickets_outline_custom_filters').find('li.no-data').show();
		}
	},

	updateCustomFilterTitle: function(id, title) {
		$('#tickets_outline_custom_filters').find('li.filter-' + id).find('h3').text(title);
	},

	updateSlaDescription: function() {
		var row = $('#ticket_slas_header');
		var filter = row.data('sla-filter');

		row.find('h1 span.sla-filter-type').hide();
		$('#ticket_sla_filter_' + filter).show();
	},

	getUpdatedSlaCounts: function(callback) {
		$.ajax({
			url: BASE_URL + 'agent/ticket-search/get-sla-counts.json',
			dataType: 'json',
			context: this,
			success: function(data) {
				this.updateSlaCounts(data);
				if ($.isFunction(callback)) {
					callback(data);
				}
			}
		});
	},

	updateSlaCounts: function(data) {
		if (!data.counts) {
			return;
		}

		var header = $('#ticket_slas_header');
		header.data('sla-filter', data.sla_filter);
		this.updateSlaDescription();

		Object.each(data.counts, function (counts, sla_id) {
			this.setSlaCounts(sla_id, counts.ok, counts.warning, counts.fail);
		}, this);
	},

	setSlaCounts: function(sla_id, ok, warning, fail) {
		sla_id = parseInt(sla_id);

		var list = $('#tickets_outline_slas');
		var row = list.find('.sla-' + sla_id);

		if (row.length) {
			var okCount = row.find('.list-counter.ok');

			okCount.text(ok || 0);
			if (ok > 0) {
				okCount.addClass('not-empty');
			} else {
				okCount.removeClass('not-empty');
			}

			var warningCount = row.find('.list-counter.warning');

			warningCount.text(warning || 0);
			if (warning > 0) {
				warningCount.addClass('not-empty');
			} else {
				warningCount.removeClass('not-empty');
			}

			var failCount = row.find('.list-counter.fail');

			failCount.text(fail || 0);
			if (fail > 0) {
				failCount.addClass('not-empty');
			} else {
				failCount.removeClass('not-empty');
			}
		}
	}
});

Orb.createNamespace('DeskPRO.Agent.WindowElement.Section');

DeskPRO.Agent.WindowElement.Section.People = new Orb.Class({
	Extends: DeskPRO.Agent.WindowElement.Section.AbstractSection,

	init: function() {

		this.buttonEl = $('#people_section');

		this.urlFragmentName = 'people';

		this.setSectionElement($('<section id="people_outline"></section>'));

		DeskPRO_Window.getMessageBroker().addMessageListener('agent-notify.new_registration', function(info) { this.reloadCounts(); }, this);
		DeskPRO_Window.getMessageBroker().addMessageListener('agent.person.added', function(info) { this.reloadCounts(); }, this);
		DeskPRO_Window.getMessageBroker().addMessageListener('agent.person.confirmed', function(info) { this.reloadCounts(); }, this);
		DeskPRO_Window.getMessageBroker().addMessageListener('agent.person.removed', function(info) { this.reloadCounts(); }, this);

		this.reload();
	},

	reload: function() {
		DeskPRO_Window.getSectionData('people_section', (function(data) {
			var wasLaoded = false;
			if (this.hasLoaded) {
				wasLoaded = true;

				this.peopleTabs.destroy();
				delete this.peopleTabs;

				this.orgTabs.destroy();
				delete this.orgTabs;
			}

			this._initSection(data);

			if (!wasLaoded) {
				this.fireEvent('sectionInit');
			}
		}).bind(this));
	},

	reloadCounts: function() {
		$.ajax({
			url: BASE_URL + 'agent/people/get-section-data/reload-counts.json',
			dataType: 'json',
			context: this,
			success: function(countData) {
				this.setCountData(countData);
			}
		});
	},

	setCountData: function(countData) {
		if (typeof countData.people_count != 'undefined') {
			$('#people_nav_all').find('span.list-counter').text(countData.people_count + '');
		}

		if (typeof countData.usergroup_counts != 'undefined') {
			Object.each(countData.usergroup_counts, function(count, uid) {
				var el = $('#people_nav_ug_' + uid);
				el.find('span.list-counter').text(count+'');

				if (!parseInt(count)) {
					el.hide();
				} else {
					el.show();
				}
			});
		}

		if (typeof countData.validating_count != 'undefined') {
			var el = $('#people_nav_awaiting_validation');
			el.find('span.list-counter').text(countData.validating_count+'');

			if (!parseInt(countData.validating_count)) {
				el.hide();
			} else {
				el.show();
			}
		}
		if (typeof countData.validating_count_agent != 'undefined') {
			var el = $('#people_nav_awaiting_agent_validation');
			el.find('span.list-counter').text(countData.validating_count_agent+'');

			if (!parseInt(countData.validating_count_agent)) {
				el.hide();
			} else {
				el.show();
			}
		}

		if (!parseInt(countData.validating_count) && !parseInt(countData.validating_count_agent)) {
			$('#people_pending_section').hide();
		} else {
			$('#people_pending_section').show();
		}
	},

	reloadLabels: function() {
		$.ajax({
			url: BASE_URL + 'agent/people/get-section-data/labels.json',
			context: this,
			success: function(data) {
				$('#people_outline_tagcloud').empty().html(data.people_label_cloud);
				$('#people_outline_taglist').empty().html(data.people_label_list);

				if ($('.no-labels', '#people_outline_taglist').length) {
					$('#people_outline_tabstrip').hide();
				} else {
					$('#people_outline_tabstrip').show();
				}

				$('#people_outline_org_tagcloud').empty().html(data.org_label_cloud);
				$('#people_outline_org_taglist').empty().html(data.org_label_list);

				if ($('.no-labels', '#people_outline_org_taglist').length) {
					$('#people_outline_org_tabstrip').hide();
				} else {
					$('#people_outline_org_tabstrip').show();
				}
			}
		});
	},

	_initSection: function(data) {
		this.setHasInitialLoaded();

		this.contentEl.empty().html(data.section_html);

		this._initSectionSearch();

		var self = this;
		this.peopleTabs = new DeskPRO.UI.SimpleTabs({
			context: this.sectionEl,
			triggerElements: $('#people_outline_tabstrip li'),
			onTabSwitch: function(info) {

			}
		});

		this.orgTabs = new DeskPRO.UI.SimpleTabs({
			context: this.sectionEl,
			triggerElements: $('#people_outline_org_tabstrip li'),
			onTabSwitch: function(info) {

			}
		});
	},

	_initSectionSearch: function() {
		var searchPane = this.contentEl.find('.source-pane-search');
		if (!searchPane[0]) {
			return;
		}

		this.searchForm = new DeskPRO.Agent.SourcePane.SearchForm(searchPane);

		var typeSwitcher = searchPane.find('.content_type');
		typeSwitcher.on('change', function(ev) {
			searchPane.find('.content_type_wrap').hide().filter('.content_type-'+$(this).val()).show();
		});
	}
});

Orb.createNamespace('DeskPRO.Agent.WindowElement.Section');

DeskPRO.Agent.WindowElement.Section.Publish = new Orb.Class({
	Extends: DeskPRO.Agent.WindowElement.Section.AbstractSection,

	init: function() {
		var self = this;
		this.expanded_ids = [];
		this.selected_id = null;
		this.buttonEl = $('#publish_section');
		this.lastLoad = null;

		this.urlFragmentName = 'publish';

		this.setSectionElement($('<section id="publish_outline"></section>'));

		DeskPRO_Window.getSectionData('publish_section', this._initSection.bind(this));

		window.setInterval(function() {
			self.reloadIfStale();
		}, 420000); // update every 7 mins auto-reload

		this.addEvent('show', function() {
			self.reloadIfStale();
		});

		this.buttonEl.on('dblclick', function() {
			self.reload();
		});

		DeskPRO_Window.getMessageBroker().addMessageListener('agent.ui.content_deleted.*', function() {
			self.reload();
		});

		DeskPRO_Window.getMessageBroker().addMessageListener('agent.ui.new-pending', function() {
			self.reload();
		});
	},

	modCommentCount: function(type, op, num) {
		if (!this.commentCountEls) {
			this.commentCountEls = {
				'all':       $('#publish_all_comments_count'),
				'articles':  $('#publish_articles_comments_count'),
				'news':      $('#publish_news_comments_count'),
				'downloads': $('#publish_downloads_comments_count')
			};
		}

		var el = this.commentCountEls[type];
		if (!el || !el[0]) {
			return;
		}

		DeskPRO_Window.util.modCountEl(el, op, num);
		DeskPRO_Window.util.modCountEl(this.commentCountEls.all, op, num);

		var elNum = parseInt(el.text());
		if (elNum != 0) {
			el.closest('li').show();
		} else {
			el.closest('li').hide();
		}

		var allNum = parseInt(this.commentCountEls.all.text());
		if (allNum) {
			$('#publish_section_comments').show();
		} else {
			$('#publish_section_comments').hide();
		}
	},

	reloadIfStale: function() {
		var now = new Date();
		if (!this.lastLoad || (now.getTime() - this.lastLoad.getTime() > 120000)) {
			this.reload();
			return true;
		}

		return false;
	},

	reload: function() {
		this.lastLoad = new Date();
		var expanded_ids = [];

		this.selected_id = null;

		if (this.contentEl && this.contentEl.length) {
			this.contentEl.find('.dp-collapsible-open').each(function() {
				var id = $(this).attr('id');
				if (id) {
					expanded_ids.push(id);
				}
			});

			this.contentEl.find('li.dp-list-open').each(function() {
				var id = this.id;
				if (id) {
					expanded_ids.push(id);
				}
			});

			var sel = this.contentEl.find('.nav-selected').first();
			if (sel[0] && sel.attr('id')) {
				this.selected_id = sel.attr('id');
			}
		}

		this.expanded_ids = expanded_ids;

		DeskPRO_Window.getSectionData('publish_section', (function(data) {
			this._initSection(data);

			if (this.sectionEl) {
				var scroller = this.sectionEl.find('.with-scroll-handler').data('scroll_handler');
				if (scroller) {
					scroller.updateSize();
				}
			}
		}).bind(this));
	},

	_initSection: function(data) {

		if(this.hasSectionInitialised) {
			this.contentEl.empty();
		} else {
			DeskPRO_Window.getMessageBroker().addMessageListener('publish.drafts.list-remove', function (info) {
				DeskPRO_Window.util.modCountEl('#publish_drafts_count', '-');
				self.modBadgeCount('-');
			});

			DeskPRO_Window.getMessageBroker().addMessageListener('publish.drafts.list-add', function (info) {
				DeskPRO_Window.util.modCountEl('#publish_drafts_count', '+');
				self.modBadgeCount('+');
			});
		}

		this.hasSectionInitialised = true;

		var self = this;
		this.setHasInitialLoaded();

		this.contentEl.html(data.section_html);

		if (this.selected_id) {
			$('#' + this.selected_id).addClass('nav-selected');
		}

		if (this.expanded_ids && this.expanded_ids.length) {
			Array.each(this.expanded_ids, function(id) {
				var el = $('#' + id);
				if (el.is('li')) {
					var $me = el.find('> i');
					var $li = el;
					var $groupList = el.find('> .nav-list-small');

					$me.removeClass('icon-caret-right');
					$me.addClass('icon-caret-down');
					$groupList.show();
					$li.addClass('dp-list-open');
				} else {
					el.addClass('dp-collapsible-open');
				}
			});
		}

		this._initSectionSearch();

		var self = this;

		this.contentEl.find('.pane-section').filter('.dp-collapsible').each(function() {
			var section = $(this);
			var header  = section.find('> header');
			var article = section.find('> article');

			header.css('cursor', 'pointer').on('click', function(ev) {
				Orb.cancelEvent(ev);
				section.toggleClass('dp-collapsible-open');
				self.updateUi();
			});
		});

		this.contentEl.find('.dp-toggle-icon').on('click', function(ev) {
			Orb.cancelEvent(ev);
			var $me    = $(this);
			var $li    = $me.closest('li');
			var $groupList = $li.find('> .nav-list-small');

			if ($me.hasClass('icon-caret-right')) {
				$me.removeClass('icon-caret-right');
				$me.addClass('icon-caret-down');
				$groupList.show();
				$li.addClass('dp-list-open');
			} else {
				$me.addClass('icon-caret-right');
				$me.removeClass('icon-caret-down');
				$groupList.hide();
				$li.removeClass('dp-list-open');
			}
		});

		this._initGlossary();
		this.recountBadge();

		this.fireEvent('sectionInit');
		this.updateUi();
	},

	_initSectionSearch: function() {
		var searchPane = this.contentEl.find('.source-pane-search');
		if (searchPane[0]) {
			this.searchForm = new DeskPRO.Agent.SourcePane.SearchForm(searchPane);
		}

		var catSelectTypes = searchPane.find('.cat-select-type');
		searchPane.find('.content_type').on('change', function() {
			catSelectTypes.hide();
			catSelectTypes.filter('.cat-select-'+$(this).val()).show();
		});
	},

	recountBadge: function() {
		var count = 0;
		count += parseInt($('#kb_pending_count').text().trim()) || 0;
		count += parseInt($('#publish_validating_count').text().trim()) || 0;
		count += parseInt($('#publish_validating_comments_count').text().trim()) || 0;
		this.updateBadge(count);
	},

	recountChildCounts: function(ul) {
		var self = this;
		$('> li', ul).each(function() {
			var li = $(this);
			var countEl = $('.list-counter:first', li);
			var count = parseInt(countEl.data('count'));
			var totalCount = count;

			var subUl = $('> ul', li);
			var subLis = null;
			if (subUl.length) {
				subLis = $('> li', subUl);
			}

			if (subLis && subLis.length) {
				self.recountChildCounts(subUl);

				subLis.each(function() {
					totalCount += parseInt($('.list-counter:first', this).data('total-count'));
				});

				countEl.text(count + '/' + totalCount);
			} else {
				countEl.text(count);
			}

			countEl.data('total-count', totalCount);
		});
	},

	//#########################################################################
	//# Glossary
	//#########################################################################

	_initGlossary: function() {

		this.glossaryWrapper = $('#publish_outline_glossary');

		var self = this;
		$('.glossary-new-trigger', this.glossaryWrapper).on('click', this.showGlossaryAddDlg.bind(this));
		$('.glossary-word-trigger', this.glossaryWrapper).on('click', function(ev) {
			ev.preventDefault();
			self.showGlossaryEditDlg($(this).data('word-id'));
		});
	},

	showGlossaryAddDlg: function() {
		var addDlg = this.getGlossaryAddDlg();
		addDlg.openOverlay();
	},

	showGlossaryEditDlg: function(id) {
		var editDlg = this.getGlossaryEditDlg();

		var form = $('.form', editDlg.elements.wrapper);
		var loading = $('.loading', editDlg.elements.wrapper);

		form.hide();
		loading.show();

		editDlg.openOverlay();

		$.ajax({
			url: BASE_URL + 'agent/glossary/' + id + '.json',
			type: 'GET',
			context: this,
			dataType: 'json',
			success: function(info) {
				form.find('input.word').select2('val', info.words);
				$('input.word_id', form).val(info.id);
				$('textarea.definition', form).val(info.definition);

				loading.hide();
				form.show();
			}
		});
	},

	getGlossaryAddDlg: function() {
		if (this.addDlg) return this.addDlg;

		var el = $('.glossary-add-dlg:first', this.glossaryWrapper);
		this.addDlg = new DeskPRO.UI.Overlay({
			contentElement: el,
			customClassname: 'normal-size',
			onBeforeOverlayOpened: function() {
				el.find('textarea.definition').val('');
				el.find('input.word').val('').select2('val', []);
			}
		});

		$('.save-trigger', el).on('click', this.saveNewWord.bind(this));

		DP.select(el.find('input.word'), {
			tags: [],
			id: function (e) { if (!e) return null; return e.id; },
			formatResult: function(result, container, query) {
				if (!result || !result.text) {
					return '';
				}
				return Orb.escapeHtml(result.text);
			},
			matcher: function(term, text) {
				if (typeOf(text)  != 'string' || typeOf(term) != 'string') {
					return;
				}

				return text.toUpperCase().indexOf(term.toUpperCase()) >= 0;
			}
		});

		return this.addDlg;
	},

	getGlossaryEditDlg: function() {
		if (this.editDlg) return this.editDlg;

		var el = $('.glossary-edit-dlg:first', this.glossaryWrapper);
		this.editDlg = new DeskPRO.UI.Overlay({
			contentElement: el,
			customClassname: 'normal-size'
		});

		DP.select(el.find('input.word'), {
			tags: [],
			id: function (e) { if (!e) return null; return e.id; },
			formatResult: function(result, container, query) {
				if (!result || !result.text) {
					return '';
				}
				return Orb.escapeHtml(result.text);
			},
			matcher: function(term, text) {
				if (typeOf(text)  != 'string' || typeOf(term) != 'string') {
					return;
				}

				return text.toUpperCase().indexOf(term.toUpperCase()) >= 0;
			}
		});

		$('.save-trigger', el).on('click', this.saveEditWord.bind(this));
		$('.delete-trigger', el).on('click', this.deleteEditWord.bind(this));

		return this.editDlg;
	},

	saveNewWord: function() {
		var data = [];

		var words = $('input.word', this.addDlg.elements.wrapperOuter).select2('val');
		for (var i = 0; i < words.length; i++) {
			data.push({
				name: 'words[]',
				value: words[i]
			});
		}
		data.push({
			name: 'definition',
			value: $('textarea.definition', this.addDlg.elements.wrapperOuter).val().trim()
		});

		$.ajax({
			url: BASE_URL + 'agent/glossary/new-word.json',
			type: 'POST',
			data: data,
			context: this,
			dataType: 'json',
			success: function(data) {
				this.addDlg.closeOverlay();
				this.reload();
			}
		});
	},

	saveEditWord: function() {

		var word_id = $('input.word_id', this.editDlg.elements.wrapperOuter).val().trim();

		var data = [];
		data.push({
			name: 'word_id',
			value: word_id
		});

		var words = $('input.word', this.editDlg.elements.wrapperOuter).select2('val');
		for (var i = 0; i < words.length; i++) {
			data.push({
				name: 'words[]',
				value: words[i]
			});
		}

		data.push({
			name: 'definition',
			value: $('textarea.definition', this.editDlg.elements.wrapperOuter).val().trim()
		});

		$.ajax({
			url: BASE_URL + 'agent/glossary/' + word_id + '/edit.json',
			type: 'POST',
			data: data,
			context: this,
			dataType: 'json',
			success: function(counts) {
				this.getGlossaryEditDlg().close();
				this.reload();
			}
		});
	},

	deleteEditWord: function() {

		var word_id = $('input.word_id', this.editDlg.elements.wrapperOuter).val().trim();

		$.ajax({
			url: BASE_URL + 'agent/glossary/' + word_id + '/delete.json',
			type: 'POST',
			context: this,
			dataType: 'json',
			success: function(counts) {
				this.getGlossaryEditDlg().close();
				this.reload();
			}
		});
	}
});

Orb.createNamespace('DeskPRO.Agent.WindowElement.Section');

DeskPRO.Agent.WindowElement.Section.AgentChat = new Orb.Class({
	Extends: DeskPRO.Agent.WindowElement.Section.AbstractSection,

	init: function() {
		this.buttonEl = $('#agent_chat_section');
		this.chatsWrapper = $('#agent_chats_wrapper');
		this.setSectionElement($('<section id="agent_chat_outline"></section>'));

		this.urlFragmentName = 'agentchat';

		$('#agent_chat_conversation').template('agent_chat_conversation');
		$('#agent_groupchat_conversation').template('agent_groupchat_conversation');
		$('#agent_chat_message').template('agent_chat_message');
		$('#agent_chat_message_me').template('agent_chat_message_me');

		this._initMessageHandlers();
		this._initInterface();

		DeskPRO_Window.getSectionData('agent_chat_section', (function(data) {
			this.setHasInitialLoaded();
			this.contentEl.html(data.section_html);
			this.initSection();
		}).bind(this));

		DeskPRO_Window.getMessageBroker().addMessageListener('agentchat-section.list-activated', function (info) {
			this.highlightNavItem($('.agent-' + info.id, this.getSectionElement()));
		}, this);

		DeskPRO_Window.getPoller().addData(
			[{name: 'do[]', value: 'get-online-agents'}],
			'agent.online-agents',
			{recurring: true, minDelay: 210000/*3.5 mintues*/, minDelayAfterOne:true }
		);
	},

	onShow: function() {
		DeskPRO_Window.getSectionData('agent_chat_section', (function(data) {
			this.setHasInitialLoaded();
			this.contentEl.html(data.section_html);
			this.initSection();
		}).bind(this));
	},

	initSection: function() {

	},

	_initMessageHandlers: function() {
		var self = this;
		DeskPRO_Window.getMessageBroker().addMessageListener('agent_chat.new-message', this.newIncomingMessage, this);
		DeskPRO_Window.getMessageBroker().addMessageListener('agent.new-agent-online', function(info) {
			var agent_id = info.agent_id;
			this.addOnlineAgent(agent_id);
		}, this);
		DeskPRO_Window.getMessageBroker().addMessageListener('agent.online-agents', function(info) {
			$('#agent_online_list').find('li').not('.no-agents').remove();
			$('#agent_online_list').find('li.no-agents').show();
			$('#agent_offline_list').find('li').show();
			self.onlineCountEl.html('0');

			Array.each(info.online_agents, function(agent_id) {
				self.addOnlineAgent(agent_id);
			});
		}, this);
	},

	_initInterface: function() {
		this.panelEl = $('#agent_chat_panel');
		this.onlineListEl = $('#agent_online_list');
		this.offlineListEl = $('#agent_offline_list');
		this.onlineCountEl = $('#chat_online_count');
		this.agentTeamList = $('#agent_team_list');

		$('.show-offline-opt', this.panelEl).on('click', function() {
			if ($(this).is(':checked')) {
				$('#agent_chat_panel').addClass('show-offline');
			} else {
				$('#agent_chat_panel').removeClass('show-offline');
			}
		});

		this.panelEl.on('click', function(ev) {
			// dont bubble to doc which will close the panel again
			ev.stopPropagation();
		});

		$('.show-section', this.panelEl).on('click', function() {
			DeskPRO_Window.switchToSection('agent_chat_section');
		});

		$('#agent_chat_section').on('click', (function(ev) {
			ev.stopPropagation();
			this.panelEl.toggleClass('open');
		}).bind(this));
		$('#agent_chat_panel .close-trigger').on('click', (function() {
			this.close();
		}).bind(this));

		// stop propagation for clicks on the chat wrapper
		// so it dorsnt bubble up and close the open chat window
		this.chatsWrapper.on('click', function(ev) {
			ev.stopPropagation();
		});

		var self = this;

		var openChatFn = function (ev) {
			ev.stopPropagation();
			var agent_id = $(this).data('agent-id');
			if (agent_id) {
				self.newChatWindow([agent_id]);
			}
		};

		this.onlineListEl.on('click', 'li', openChatFn);
		this.offlineListEl.on('click', 'li', openChatFn);
		this.agentTeamList.on('click', 'li', function(ev) {
			ev.stopPropagation();
			var agentIds = $(this).data('member-ids') || '';
			agentIds = (agentIds+"").split(',');
			agentIds.include(window.DESKPRO_PERSON_ID);
			agentIds = agentIds.filter(function(x) {
				if (x) return true;
			});

			var name = $(this).data('team-name');

			self.newChatWindow(agentIds, name);
		});

		// Agents/teams tabs
		this.listTabs = new DeskPRO.UI.SimpleTabs({
			triggerElements: $('#agent_chat_panel_listviews > li'),
			onTabSwitch: function(info) {
				if (info.tabEl.is('.teams')) {
					$('.show-offline', self.panelEl).hide();
				} else {
					$('.show-offline', self.panelEl).show();
				}
			}
		});

		$('#agent_chat_panel .view-history-trigger').on('click', function(ev) {
			DeskPRO_Window.switchToSection('agent_chat_section');
			self.close();
		});

		$('li.online-now', '#agent_offline_list').each(function() {
			self.addOnlineAgent($(this).removeClass('online-now').data('agent-id'));
		});

		this.panelEl.on('click', function() {

		});
	},

	close: function() {
		this.panelEl.removeClass('open');
	},

	newChatWindow: function(agent_ids, title) {
		var self = this;

		var chatWin = DeskPRO.Agent.Widget.AgentChatWin_FindAgents(agent_ids);
		if (!chatWin) {
			chatWin = new DeskPRO.Agent.Widget.AgentChatWin({
				agentIds: agent_ids,
				title: title || null,
				onDestroy: function() { self.reflowButtons(); }
			});
		}

		this.close();

		chatWin.open();
	},

	newIncomingMessage: function(info) {
		var self = this;

		var chatWin = DeskPRO.Agent.Widget.AgentChatWin_Find(info.conversation_id);
		if (!chatWin) {
			chatWin = new DeskPRO.Agent.Widget.AgentChatWin({
				convoId: info.conversation_id,
				agentIds: info.participant_ids,
				onDestroy: function() { self.reflowButtons(); }
			});
		}

		chatWin.showMessage(info.author_id, info.message, info.time);
		chatWin.open();
	},

	/**
	 * Re-positions buttons after one is closed
	 */
	reflowButtons: function() {
		var lastChat = null;
		$('> section.agent-chat', this.chatsWrapper).each(function() {
			var left = 0;
			if (lastChat) {
				left += lastChat.position().left + $('> nav', lastChat).outerWidth() + 8;
			}

			$(this).css('left', left);
			lastChat = $(this);
		});
	},

	//#########################################################################
	//# Online agent handling
	//#########################################################################

	addOnlineAgent: function(agent_id) {

		if (typeof agent_id.agent_id != 'undefined') {
			agent_id = agent_id.agent_id;
		}

		var origLi = $('.agent-' + agent_id, this.offlineListEl);

		if (!origLi.length) {
			DP.console.error('No agent element for %i', agent_id);
			return;
		}

		// Make sure they aren't already there (ie logged out/logged in before we could see theyre gone)
		if ($('.agent-' + agent_id, this.onlineListEl).length) {
			return;
		}

		var li = origLi.clone();
		li.show();
		this.onlineListEl.append(li);

		// Offline one is hidden now
		origLi.hide();

		var countInt = parseInt(this.onlineCountEl.html());
		countInt++;
		this.onlineCountEl.html(countInt);

		$('li.no-agents', this.onlineListEl).hide();
	},

	removeOnlineAgent: function(agent_id) {
		if (DESKPRO_PERSON_ID && agent_id == DESKPRO_PERSON_ID) {
			return;
		}

		var li = $('.agent-' + agent_id, this.onlineListEl);
		var offlineLi = $('.agent-' + agent_id, this.offlineListEl);

		if (!li.length) {
			return;
		}

		li.remove();

		// Show them in offline again
		offlineLi.show();

		var countInt = parseInt(this.onlineCountEl.html());
		countInt--;
		this.onlineCountEl.html(countInt);

		if (countInt < 1) {
			$('li.no-agents', this.onlineListEl).show();
		}
	}
});

Orb.createNamespace('DeskPRO.Agent.WindowElement.Section');

/**
 * The UserChat section also controls the chat status at the top of the window.
 */
DeskPRO.Agent.WindowElement.Section.UserChat = new Orb.Class({
	Extends: DeskPRO.Agent.WindowElement.Section.AbstractSection,

	//##################################################################################################################
	//# Init
	//##################################################################################################################

	init: function() {
		var self = this;
		this.buttonEl = $('#chat_section');
		this.setSectionElement($('<section id="chat_outline"></section>'));
		this.groups = {};
		this.urlFragmentName = 'userchat';
		this.hasSectionInitialised = false;
		this.lastOnlineUserLoad = null;
		this.lastOnlineUserCount = null;

		this.onlineUsersWrap = $('#agent_status_online_users');
		this.statusMenuOpen = false;
		this.dismissedChats = {};
		this.openingChatTimeout = {};
		this.refreshCountsTimeout = null;
		this.onlineAgentIds = [];

		this._initStatusMenu();
		this._initStatusMenuAgents();
		this._initStatusMenuUsers();
		this._initTemplates();
		this._initMessageHandlers();

		this._lastLoaded = new Date();
		DeskPRO_Window.getSectionData('chat_section', (function(data) {
			this._initSection(data);
		}).bind(this));
	},

	_initSection: function(data) {

		var self = this;

		var lastSelectedId = null;
		if (this.contentEl) {
			lastSelectedId = this.contentEl.find('.nav-selected').find('.list-counter').attr('id');
		}

		if(this.hasSectionInitialised) {
			this._lastLoaded = new Date();
			//this.filterGroupEditor.destroy();
			this.contentEl.empty();
		}

		this.hasSectionInitialised = true;
		var self = this;

		this.setHasInitialLoaded();
		this.contentEl.html(data.section_html);

		/*
		this.filterGroupEditor = new DeskPRO.Agent.Widget.FilterGroupEditor({
			containerElement: '#chat_outline',
			listElement: '#chats_outline_sys_filters',
			triggerElement: '#chat_filter_launch_editor',
			controlElement: '#chat_filter_group_editor',
			useIntId: false,
			onGroupingChanged: function(data) {
				self.refreshFilterGrouping(data);
			},
			onSetMarginTop: function(evData) {
				evData.marginTop = $('#chats_outline_sys_filters').position().top;
			}
		});
		this.filterGroupEditor._initControl();
		*/
		this.refreshFilterGrouping(data, lastSelectedId);
		this.updateGroupingVars();

		this._lastLoaded = new Date();
        this.handleUpdateCounts();

		if (lastSelectedId) {
			$('#' + lastSelectedId).closest('.is-nav-item').addClass('nav-selected');
		}

		new DeskPRO.ElementHandler.SimpleTabs($('#chat_outline_labels_switcher'));

		this.fireEvent('sectionInit');
	},

	_initStatusMenu: function() {
		var self = this;

		var statusMenuHandler = {
			getBackdrop: function() {
				if (!this.backdrop) {
					this.backdrop = $('<div class="backdrop" />').appendTo('body');
					this.backdrop.on('click', function() { statusMenuHandler.close(); });
				}
				return this.backdrop;
			},
			getList: function() {
				if (!this.list) {
					this.list = $('#agent_status_menu').hide().detach().appendTo('body');
				}
				return this.list;
			},
			open: function() {
				this.getBackdrop().show();
				this.getList().show();
				self.statusMenuOpen = true;
				self.fireEvent('statusMenuOpened');
			},
			close: function() {
				this.getBackdrop().hide();
				this.getList().hide();
				self.statusMenuOpen = false;
				self.fireEvent('statusMenuClosed')
			},
			init: function() {
				$('#chatStatusWrap').on('click', function() { statusMenuHandler.open(); });
				$('#agent_status_away_overlay').on('click', function() { statusMenuHandler.close(); });
				$('#agent_status_menu').find('.notifHead').on('click', function() { statusMenuHandler.close(); });
			}
		};
		statusMenuHandler.init();

		$('#agent_status_menu_me_list').find('.trigger-toggle-status').on('click', function(ev) {

			ev.preventDefault();
			ev.stopPropagation();

			$('#agent_status_menu_me_list').addClass('dp-loading-on');

			var is_available;
			if (!$('#agent_status_menu_me_list').data('is-online')) {
				// Toggle on
				is_available = true;
			} else {
				// Toggle off
				is_available = false;
			}

			$('#agent_status_menu_me_list').data('is-online', is_available);

			self.sendUpdateAgentStatus(is_available, function() {
				$('#agent_status_menu_me_list').removeClass('dp-loading-on');

				if (!is_available) {
					self.onlineAgentIds.erase(DESKPRO_PERSON_ID);
				} else {
					self.onlineAgentIds.include(DESKPRO_PERSON_ID);
				}

				self.refreshOnlineAgentsList();
				self.refreshOnlineAgentDepGroups();
			});
		});
	},

	_initStatusMenuAgents: function() {
		var self = this;
		var status_menu_el = $('#agent_status_menu_onlinelist');
		this.onlineAgentsGroupDepCheck = status_menu_el.find('.agents-list-groupdep');
		this.onlineAgentsList = status_menu_el.find('ul.list.normal');
		this.onlineAgentsListGrouped = status_menu_el.find('ul.list.department-grouped');

		status_menu_el.find('.agents-list-groupdep').on('click', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();
			self.onlineAgentsGroupDepCheck.toggleClass('toggle-on');

			if (self.onlineAgentsGroupDepCheck.hasClass('toggle-on')) {
				self.refreshOnlineAgentDepGroups();
				self.onlineAgentsListGrouped.show();
				self.onlineAgentsList.hide();
			} else {
				self.onlineAgentsListGrouped.hide();
				self.onlineAgentsList.show();
			}
		});

		// We broadcast this when setting the status manually
		// It means any other locations we're signed in under get the same
		// message and we all sync our status properly
		DeskPRO_Window.getMessageBroker().addMessageListener('agent.ui.user-chat-status', function(info) {
			if (info.is_online) {
				self.onlineAgentIds.include(DESKPRO_PERSON_ID);
			} else {
				self.onlineAgentIds.erase(DESKPRO_PERSON_ID);
			}
		});

		this.getSectionElement().on('click', '.sub-toggle', function(ev) {
			var row = $(this).closest('li');
			var sub = $('> ul.sub-group', row);
			if (sub.length) {
				if (sub.is(':visible')) {
					row.removeClass('sub-expanded');
					sub.slideUp('fast');
				} else {
					row.addClass('sub-expanded');
					sub.slideDown('fast');
				}
			}
		});

		DeskPRO_Window.getMessageBroker().addMessageListener('agent.online-agents-userchat', function(info) {
			self.onlineAgentIds = [];

			if (info.online_agents && info.online_agents.length) {
				Array.each(info.online_agents, function(agent_id) {
					self.onlineAgentIds.push(parseInt(agent_id));
				});
			}

			self.refreshOnlineAgentsList();
			self.refreshOnlineAgentDepGroups();
		}, this);
	},

	_initStatusMenuUsers: function() {
		var self = this;

		this.addEvent('statusMenuOpened', function() {
			this.refreshOnlineUsers();
		}, this);

		this.addEvent('statusMenuOpened', function() {
			if (this.onlineUsersRefreshTimer) {
				window.clearTimeout(this.onlineUsersRefreshTimer);
				this.onlineUsersRefreshTimer = null;
			}
		}, this);

		DeskPRO_Window.getMessageBroker().addMessageListener('agent.online-users-count', function(info) {
			var count = parseInt(info.online_count) || 0;

			Orb.phraseTextEl($('.agent_chrome_chat_online_users'), {count: count});
			DeskPRO_Window.util.modCountEl($('.userchat-online-users-count'), '=', count);

			if (count != self.lastOnlineUserCount) {
				self.lastOnlineUserLoad = null;
			}
			self.lastOnlineUserCount = count;
		});

		this.onlineUsersWrap.on('click', '.reload-table-btn', function() {
			self.onlineUsersWrap.addClass('refreshing refreshing-clicked');
			self.refreshOnlineUsers();
		});
	},

	_initTemplates: function() {
		$('#new_user_chat_alert').template('new_user_chat_alert');
		$('#invite_chat_alert').template('invite_chat_alert');
		$('#new_user_chat_alert_message').template('new_user_chat_alert_message');
		$('#added_part_user_chat_alert').template('added_part_user_chat_alert');
		$('#user_chat_newmsg_sound').template('user_chat_newmsg_sound');
	},

	_initMessageHandlers: function() {
		DeskPRO_Window.getMessageBroker().addMessageListener('chat.new', this.handleNewChat, this);
		DeskPRO_Window.getMessageBroker().addMessageListener('chat.reassigned', this.handleReassignedChat, this);
		DeskPRO_Window.getMessageBroker().addMessageListener('chat.unassigned', this.handleUnassignedChat, this);
		DeskPRO_Window.getMessageBroker().addMessageListener('chat.ended', this.handleChatEnded, this);
		DeskPRO_Window.getMessageBroker().addMessageListener('chat.depchange', this.handleDepChange, this);
		DeskPRO_Window.getMessageBroker().addMessageListener('chat.invited', this.handleInvited, this);
		DeskPRO_Window.getMessageBroker().addMessageListener('chat_user_agent.chat-parts-updated', this.handlePartsUpdated, this);
	},

	//##################################################################################################################
	//# Window: Online users / tracking
	//##################################################################################################################

	startRefreshingWhileOpenTimer: function() {
		var self = this;
		if (!this.statusMenuOpen) {
			return;
		}

		if (this.onlineUsersRefreshAjax) {
			return;
		}

		if (this.onlineUsersRefreshTimer) {
			window.clearTimeout(this.onlineUsersRefreshTimer);
			this.onlineUsersRefreshTimer = null;
		}

		this.onlineUsersRefreshTimer = window.setTimeout(function() {
			self.refreshOnlineUsers();
		}, 5000);
	},

	refreshOnlineUsersIfNeeded: function() {
		var now = new Date();

		if (!this.lastOnlineUserLoad || (now.getTime() - this.lastOnlineUserLoad.getTime()) > 15000) {
			this.refreshOnlineUsers();
		}
	},

	refreshOnlineUsers: function() {
		this.onlineUsersWrap.addClass('refreshing');

		this.onlineUsersRefreshAjax = $.ajax({
			url: BASE_URL + 'agent/user-track/win-header-table.html',
			type: 'GET',
			dataType: 'html',
			context: this,
			complete: function() {
				this.onlineUsersRefreshAjax = null;
				this.onlineUsersWrap.removeClass('refreshing refreshing-clicked');
				this.startRefreshingWhileOpenTimer();
			},
			success: function(html) {
				$('#agent_status_online_users').empty().html(html);
				var count = parseInt($.trim($('#agent_status_online_users').find('.count-online-users').text()));
				count = count || 0;

				Orb.phraseTextEl($('.agent_chrome_chat_online_users'), {count: count});
				$('.userchat-online-users-count').text(count);
				this.lastOnlineUserLoad = new Date();
			}
		});
	},


	//##################################################################################################################
	//# Window: Online agents / status
	//##################################################################################################################

	refreshOnlineAgentsList: function() {
		var self = this;
		var list = this.onlineAgentsList;
		var count;
		var hasme = false;
		list.find('li').hide().removeClass('on last');

		Array.each(this.onlineAgentIds, function(agent_id) {
			if (parseInt(agent_id) === DESKPRO_PERSON_ID) {
				hasme = true;
			}

			list.find('li.agent-' + agent_id).show().addClass('on');
		});

		list.find('li.on').last().addClass('last');

		count = self.onlineAgentIds.length;

		if (hasme) {
			$('#agent_status_menu_me_list').data('is-online', true);
			Orb.enablePhraseEl('agent.chrome.chat_sign-out', $('#agent_status_menu_me_list'));
			Orb.enablePhraseEl('agent.chrome.chat_logged-in', $('#dp_header_userchat_btn').find('.status'));
		} else {
			$('#agent_status_menu_me_list').data('is-online', false);
			Orb.enablePhraseEl('agent.chrome.chat_sign-in', $('#agent_status_menu_me_list'));
			Orb.enablePhraseEl('agent.chrome.chat_logged-out', $('#dp_header_userchat_btn').find('.status'));
		}


		Orb.phraseTextEl($('.agent_chrome_chat_online_agents'), {count: count});
		DeskPRO_Window.util.modCountEl($('.userchat-online-agents-count'), '=', count);

		var userchatBtn = $('#dp_header_userchat_btn');
		userchatBtn.removeClass('me-offline all-offline');
		if (count) {
			if (!hasme) {
				$('#dp_header_userchat_btn').addClass('me-offline');
			}
		} else {
			$('#dp_header_userchat_btn').addClass('all-offline');
		}
	},

	refreshOnlineAgentDepGroups: function() {
		var self = this;
		if (!this.onlineAgentsGroupDepCheck.hasClass('toggle-on')) {
			return;
		}

		this.onlineAgentsListGrouped.find('li.dep').hide();
		this.onlineAgentsListGrouped.find('ul').empty();

		Array.each(this.onlineAgentIds, function(agentId) {
			var li = this.onlineAgentsList.find('li.agent-' + agentId);
			var depIds = (li.data('department-ids') || '') + '';
			depIds = depIds.split(',');

			if (depIds.length) {
				depIds.each(function(depId) {
					var depRow = self.onlineAgentsListGrouped.find('li.dep-' + depId);
					var depList = depRow.find('ul');
					depList.append(li.clone());
					depRow.show();
				});
			}
		}, this);
	},

	sendUpdateAgentStatus: function(is_available, callback) {

		var status   = 'available';
		var postData = [];

		if (is_available) {
			postData.push({
				name: 'is_chat_available',
				value: 1
			});
		} else {
			postData.push({
				name: 'is_chat_available',
				value: 0
			});
		}

		if (status == 'available') {
			$.ajax({
				url: BASE_URL + 'agent/misc/set-agent-status/available',
				type: 'POST',
				data: postData,
				complete: function() {
					if (!is_available) {
						$('#chatStatusWrap').addClass('offline');
					} else {
						$('#chatStatusWrap').removeClass('offline');
					}
					if (callback) {
						callback();
					}
				}
			});
		} else if (status == 'away') {
			$.ajax({
				url: BASE_URL + 'agent/misc/set-agent-status/away',
				type: 'POST',
				complete: function() {
					if (callback) {
						callback();
					}
				}
			});
		}
	},

	//##################################################################################################################
	//# Source pane
	//##################################################################################################################

	refreshOpenCounts: function(now) {

		var self = this;
		if (!now && this.refreshCountsTimeout) {
			return;
		}

		var fn = function() {
			self.refreshCountsTimeout = null;
			$.ajax({
				url: BASE_URL + 'agent/chat/open-counts.json',
				dataType: 'json',
				success: function(data) {

					$('#userchat_deplist_all').find('span.list-counter').each(function() {
						var key, subkey, count;
						key = $(this).data('count-key').split('.');
						subkey = key[1];
						key = key[0];

						if (data && data[key] && data[key][subkey]) {
							count = data[key][subkey];
						} else {
							count = 0;
						}

						$(this).text(count);
						if (!$(this).parent().is('h3')) {
							$(this).closest('li').hide();
						} else {
							$(this).closest('li').show();
						}
					});

					$('#userchat_deplist_all').find('> ul.nav-list').each(function() {
						if ($(this).find('> li').filter(':visible')) {
							$(this).show();
						} else {
							$(this).hide();
						}
					});

					self.handleUpdateCounts();
				}
			});
		};

		if (now) {
			if (this.refreshCountsTimeout) {
				window.clearTimeout(refreshCountsTimeout);
			}
			fn();
		} else {
			this.refreshCountsTimeout = window.setTimeout(fn, 4500);
		}
	},

	refreshFilterGrouping: function(filterId, lastSelectedId) {
		var self = this;
		this.groups[filterId] = this.getGroupingVar(filterId);

		$.ajax(
			{
				type: 'POST',
				url: BASE_URL + 'agent/chat/group-count.json',
				data: { filters: this.groups },
				dataType: 'json',
				success: function(data) {
					self.updateFilterGrouping(data, self);

					if (lastSelectedId) {
						$('#' + lastSelectedId).closest('.is-nav-item').addClass('nav-selected');
					}
				}
			}
		);
	},

	updateFilterGrouping: function(data, self) {
		var container = $('#chats_outline_sys_filters');

		for(filterId in data) {
			var element = $('.filter-' + filterId + ' .sub-group', container);
			element.html(data[filterId]);

			if(data[filterId])
				element.show();
			else
				element.hide();
		}

		//self.filterGroupEditor.updatePositions();
	},

	getGroupingVar: function(filterId) {
		return $('#chat_filter_group_editor .filter-' + filterId + ' .field-option').val();
	},

	updateGroupingVars: function() {
		var filterId;

		if (!this.groups) {
			return;
		}

		for(filterId in this.groups) {
			$('#chat_filter_group_editor').find('.filter-' + filterId + ' .field-option').val(this.groups[filterId] || '');
		}
	},

	onShow: function() {
		var self = this;
		window.setTimeout(function() {
			if (!self.isVisible()) {
				DeskPRO_Window.getSectionData('chat_section', self._initSection.bind(self));
				var el = self.getSectionElement().find('.nav-selected');
				if (el[0]) {
					if (el.data('route')) {
						DeskPRO_Window.runPageRouteFromElement(el);
					} else {
						DeskPRO_Window.runPageRouteFromElement(el.closest('[data-route]'));
					}
				}
			}
		}, 250);
	},

	onHide: function() {
	},

	handleUpdateCounts: function(data) {
		var count = parseInt($('#userchat_deplist_0_counter').text());
		this.updateBadge(count);

		if (!count) {
			$('#userchat_deplist_all').find('.nav-list').removeClass('show');
		} else {
			$('#userchat_deplist_all').find('.nav-list').addClass('show');
		}
	},

	isChatOpen: function(convoId) {
		var chatTabs = DeskPRO_Window.getTabWatcher().findTabType('userchat');
		var isOpen = false;
		Array.each(chatTabs, function(tab) {
			if (parseInt(tab.page.meta.conversation_id) == parseInt(convoId)) {
				isOpen = tab;
				return false;
			}
		}, this);

		return isOpen;
	},

	modListingCount: function(id, op, count) {
		var el = $('#userchat_list_' + (id != '0'?id:'allagents') + '_counter');
		var oldCount = parseInt(el.text().trim()) || 0;
		var newCount = DeskPRO_Window.util.modCountEl(el, op, count);

		if (id != '0') {
			var row = el.closest('li');

			if(newCount) {
				row.show();
			}
			else {
				row.hide();
			}

			this.modListingCount(0, op, count);
		}
		else {
			this.handleUpdateCounts();
		}

		var newCount = parseInt(el.text().trim()) || 0;

		if (oldCount != newCount) {
			if (this.isVisible()) {
				DeskPRO_Window.runPageRouteFromElement(el.closest('[data-route]'));
			}
		}
	},

	modDepListingCount: function(id, op, count) {
		var el = $('#userchat_deplist_' + id + '_counter');
		var newCount = DeskPRO_Window.util.modCountEl(el, op, count);

		if(id != '0') {
			var row = el.closest('li');

			if (el.data('parentid')) {
				this.modDepListingCount(el.data('parentid'), op, count);
			}
			else {
				this.modDepListingCount(0, op, count);
			}

			if(newCount) {
				row.show();
			}
			else {
				row.hide();
			}
		} else {
			this.handleUpdateCounts();
		}
	},

	handleDepChange: function(data) {
		if (!data.agent_id) {
			if (data.old_department_id) {
				this.modDepListingCount(data.old_department_id, '-');
			}

			if (data.department_id) {
				this.modDepListingCount(data.department_id, '+');
			}
		}

		this.handleUpdateCounts();
	},

	handleChatEnded: function(data) {
		if (!this.isDepAllowed(data.department_id)) {
			return;
		}

		this.refreshOpenCounts();

		$('#new_user_chat_alert_' + data.conversation_id).remove();
		this.modListingCount(data.agent_id, '-');
		DeskPRO_Window.getMessageBroker().sendMessage('chat_convo.' + data.conversation_id + '.ended', data);

		if (!data.agent_id) {
			this.modDepListingCount(data.department_id, '-');
		}

		if (this.dismissedChats[data.conversation_id]) {
			delete this.dismissedChats[data.conversation_id];
		}

		// See handleNewChat comment about this
		if (this.openingChatTimeout[data.conversation_id]) {
			window.clearTimeout(this.openingChatTimeout[data.conversation_id]);
			delete this.openingChatTimeout[data.conversation_id];
			DeskPRO_Window.faviconBadge.disableCrazyMode();
		}

		if (data.agent_id) {
			if (parseInt(data.agent_id) == DESKPRO_PERSON_ID) {
				DeskPRO_Window.util.modCountEl($('#userchat_mine_count'), '+');
			} else {
				DeskPRO_Window.util.modCountEl($('#userchat_assigned_count'), '+');
			}
		} else {
			DeskPRO_Window.util.modCountEl($('#userchat_missed_count'), '+');
		}

		this.handleUpdateCounts();
	},

	handlePartsUpdated: function(data) {
		DeskPRO_Window.getMessageBroker().sendMessage('chat_user_agent.chat-parts-updated-' + data.conversation_id, data);

		if (data && data.participant_ids && data.participant_ids.contains(DESKPRO_PERSON_ID)) {
			if (!this.isChatOpen(data.conversation_id)) {
				DeskPRO_Window.runPageRoute('page:' + BASE_URL + 'agent/chat/view/' + data.conversation_id, {noToggle:true});
			}
		}
	},

	handleNewChat: function(data) {
		var self = this;

		if (!this.isDepAllowed(data.department_id)) {
			return;
		}

		this.handleUpdateCounts();

		var openTab = this.isChatOpen(data.conversation_id);
		if (openTab && data.restarted) {
			openTab.page.closeSelf();
			DeskPRO_Window.runPageRoute('page:' + BASE_URL + 'agent/chat/view/' + data.conversation_id, {noToggle:true});
			return;
		}

		if (!data.agent_id) {
			if (!this.dismissedChats[data.conversation_id]) {
				var info_line = [];
				this.showNewChatAlert(data);
			}
		} else {
			if (data.agent_id == DESKPRO_PERSON_ID && !this.isChatOpen(data.conversation_id)) {
				var self = this;
				// Its possible we opened the chat, then closed+unassigned ourselves before the last
				// poll was done. This would create a series of client messages like:
				// - Assigned (from opening the chat)
				// - Unassigned (from leaving)
				// Then the CM would be delievered, and right here we'd see the assigned-to-me message
				// and attempt to re-open the chat we just closed.
				// So we timeout so we can add some logic to see if the chat was closed before running this,
				// this is just a easy way to process CM messages before running the open (since they're executed in sequence)
				this.openingChatTimeout[data.conversation_id] = window.setTimeout(function() {
					DeskPRO_Window.runPageRoute('page:' + BASE_URL + 'agent/chat/view/' + data.conversation_id, {noToggle:true});
					delete self.openingChatTimeout[data.conversation_id];
					DeskPRO_Window.faviconBadge.disableCrazyMode();
				}, 1000);
			}

			DeskPRO_Window.getSectionData('chat_section', self._initSection.bind(self));
		}

		if (!data.agent_id) {
			this.modDepListingCount(data.department_id, '+');
		}

		this.refreshOpenCounts();
	},

	handleUnassignedChat: function(data) {
		if (!this.isDepAllowed(data.department_id)) {
			return;
		}

		this.modListingCount(data.old_agent_id, '-');
		this.handleUpdateCounts();

		if (data.old_agent_id) {
			this.modDepListingCount(data.department_id, '+');
		}

		// Means we were the agent, but unassassigned ourselves
		if (data.old_agent_id && data.old_agent_id == DESKPRO_PERSON_ID) {
			this.dismissedChats[data.conversation_id] = true;
		}

		if (!this.dismissedChats[data.conversation_id]) {
			this.showNewChatAlert(data, {
				name: data.author_name,
				message: data.subject_line
			});
		}

		// See handleNewChat comment about this
		if (data.old_agent_id == DESKPRO_PERSON_ID && this.openingChatTimeout[data.conversation_id]) {
			window.clearTimeout(this.openingChatTimeout[data.conversation_id]);
			delete this.openingChatTimeout[data.conversation_id];
			DeskPRO_Window.faviconBadge.disableCrazyMode();
		}

		this.handleUpdateCounts();
	},

	handleInvited: function(data) {
		$('#invite_chat_alert_' + data.conversation_id).remove();
		this.showInviteAlert(data);
	},

	showInviteAlert: function(data) {
		var conversation_id = data.conversation_id;
		var alertEl = $.tmpl('invite_chat_alert', data);
		alertEl.appendTo('body');
		DeskPRO_Window.handleSoundElements(alertEl);

		var audio = $('audio', alertEl).get(0);
		var self = this;

		$('.dismiss-trigger', alertEl).on('click', function() {
			if (audio && audio.pause) {
				try {
					audio.pause();
				} catch(e) {}
			}
			alertEl.remove();
		});
		$('.accept-trigger', alertEl).on('click', function(ev) {
			ev.stopPropagation();
			DeskPRO_Window.runPageRouteFromElement(this);
			if (audio && audio.pause) {
				audio.pause();
			}
			alertEl.remove();
		}).data('route', 'page:' + BASE_URL + 'agent/chat/view/' + conversation_id);
	},

	handleReassignedChat: function(data) {
		var self = this;

		if (!this.isDepAllowed(data.department_id) && data.agent_id != DESKPRO_PERSON_ID) {
			return;
		}

		this.handleUpdateCounts();
		this.modListingCount(data.agent_id, '+');

		if (data.agent_id && !data.old_agent_id) {
			this.modDepListingCount(data.department_id, '-');
		}

		// Means we were the agent, but unassassigned ourselves
		if (data.old_agent_id && data.old_agent_id == DESKPRO_PERSON_ID) {
			this.dismissedChats[data.conversation_id] = true;
		}

		var notifyWin = $('#new_user_chat_alert_' + data.conversation_id);
		if (notifyWin[0]) {
			if (DP_USERCHAT_HIDE_CLAIMED_CHAT) {
				notifyWin.remove();
				return;
			}

			notifyWin.addClass('claimed');
			notifyWin.find('.waiting.row').hide();
			notifyWin.find('.taken.row').show().find('.place-assigned-name').text(data.new_agent_name);
			notifyWin.find('button.accept-trigger').hide();
			notifyWin.find('button.join-trigger').show();
			notifyWin.find('audio').remove();

			notifyWin.data('dismiss-count', 100);
			var updateFn = function() {
				notifyWin.find();
				var count = parseInt(notifyWin.data('dismiss-count')) - 1;
				notifyWin.data('dismiss-count', count);
				notifyWin.find('button.dismiss-trigger').find('.place-countdown').show().text('(' + count + ')');
				if (count == 0) {
					notifyWin.remove();
				} else {
					window.setTimeout(updateFn, 1000);
				}
			}
			updateFn();
		}
		DeskPRO_Window.getMessageBroker().sendMessage('chat_convo.' + data.conversation_id + '.reassigned', data);

		// See handleNewChat comment about this
		if (data.agent_id == DESKPRO_PERSON_ID && !this.isChatOpen(data.conversation_id)) {
			this.openingChatTimeout[data.conversation_id] = window.setTimeout(function() {
				DeskPRO_Window.runPageRoute('page:' + BASE_URL + 'agent/chat/view/' + data.conversation_id, {noToggle:true});
				delete self.openingChatTimeout[data.conversation_id];
				DeskPRO_Window.faviconBadge.disableCrazyMode();
			}, 1000);
		}

		this.handleUpdateCounts();
	},

	showNewChatAlert: function(data) {

		if (!this.isDepAllowed(data.department_id)) {
			return;
		}

		this.refreshOpenCounts();

		if (!this.onlineAgentIds.contains(DESKPRO_PERSON_ID)) {
			return;
		}

		var conversation_id = data.conversation_id;

		// If we already have the chat tab open, it probably means the chat was timed out
		// but the user came back
		var chatTabs = DeskPRO_Window.getTabWatcher().findTabType('userchat');
		var found = false;
		Array.each(chatTabs, function(t) {
			if (t.meta && t.meta.conversation_id && parseInt(t.meta.conversation_id) == parseInt(conversation_id)) {
				found = t;
				return false;
			}
		});

		if (found) {
			DeskPRO_Window.loadPage(BASE_URL + 'agent/chat/view/' + conversation_id, {ignoreExist:true});
			found.closeSelf();
			return;
		}

		var alertEl = $(data.html);
		alertEl.appendTo('body');
		DeskPRO_Window.handleSoundElements(alertEl);

		var titles = this.getNewChatTitles();
		if (titles.length == 1) {
			var winTitle = 'New chat: ' + titles[0];
		} else {
			var winTitle = titles.length + ' New chats: ' + titles.join(', ');
		}
		DeskPRO_Window.faviconBadge.enableCrazyMode(winTitle);

		var audio = $('audio', alertEl).get(0);
		var self = this;

		var secEl = alertEl.find('span.wait-timer');
		function up() {
			var secs = parseInt(secEl.data('time'));
			secs++;
			secEl.data('time', secs);

			if (secs > 60) {
				secEl.text((Math.floor(secs / 60)) + " minutes");
			} else {
				secEl.text(secs + " seconds");
			}
		};
		var waitTimer = window.setInterval(up, 1000);

		DeskPRO_Window.notifications.addMessage('chat', 'New chat by ' + alertEl.find('.label-by-name').text(), 'page:' + BASE_URL + 'agent/chat/view/' + conversation_id, 'chat-' + conversation_id)

		$('.dismiss-trigger', alertEl).on('click', function() {
			if (audio && audio.pause) {
				try {
					audio.pause();
				} catch(e) {}
			}
			alertEl.remove();
			self.dismissedChats[data.conversation_id] = true;
			DeskPRO_Window.faviconBadge.disableCrazyMode();
			window.clearTimeout(waitTimer);
		});
		$('.accept-trigger, .join-trigger', alertEl).on('click', function(ev) {
			ev.stopPropagation();
			DeskPRO_Window.runPageRouteFromElement(this);
			if (audio) {
				try {
					audio.pause();
				} catch(e) {}
			}
			alertEl.remove();
			window.clearTimeout(waitTimer);

			if (!DeskPRO_Window.paneVis.tabs) {
				DeskPRO_Window.setPaneVis('tabs', true);
			}
		}).data('route', 'page:' + BASE_URL + 'agent/chat/view/' + conversation_id);
	},

	getNewChatTitles: function() {
		var titles = [];
		$('body > section.new-user-chat-alert').each(function() {
			titles.push($(this).find('span.label-by-name').text().trim());
		});

		return titles;
	},

	isDepAllowed: function(id) {
		id = parseInt(id);

		console.log("%d in %o", id, window.DESKPRO_PERSON_PERMS.chat_dep_ids);

		return window.DESKPRO_PERSON_PERMS.chat_dep_ids.indexOf(id) !== -1;
	}
});

Orb.createNamespace('DeskPRO.Agent.WindowElement.Section');

DeskPRO.Agent.WindowElement.Section.Feedback = new Orb.Class({
	Extends: DeskPRO.Agent.WindowElement.Section.AbstractSection,

	init: function() {
		this.buttonEl = $('#feedback_section');
		var self = this;

		this.urlFragmentName = 'feedback';

		this.setSectionElement($('<section id="feedback_outline"></section>'));

		DeskPRO_Window.getSectionData('feedback_section', this._initSection.bind(this));

		DeskPRO_Window.getMessageBroker().addMessageListener('agent.ui.new-feedback', this.reload, this);
		DeskPRO_Window.getMessageBroker().addMessageListener('agent.ui.feedback-status-update', this.reload, this);

		window.setInterval(function() {
			self.reload();
		}, 420000);

		this.currentNavSelection = null;
		this.currentNavSelectionOldCount = null;
	},

	reload: function() {
		if (this.contentEl) {
			// ID'ing based off the ID of the counter, the actual nav row doesnt have any id
			var selectedNav = this.contentEl.find('.nav-selected').find('.list-counter');
			if (selectedNav[0]) {
				this.currentNavSelection = selectedNav.attr('id');
				this.currentNavSelectionOldCount = parseInt(selectedNav.text());
			}
		}
		DeskPRO_Window.getSectionData('feedback_section', this._initSection.bind(this));
	},

	_initSection: function(data) {

		this.setHasInitialLoaded();

		this.contentEl.html(data.section_html);

		this._initSectionSearch();

		var self = this;
		this.catTabs = new DeskPRO.UI.SimpleTabs({
			context: this.sectionEl,
			triggerElements: $('#feedback_outline_tabstrip li'),
			onTabSwitch: function(info) {

			}
		});

		this.recountBadge();

		if (this.currentNavSelection) {
			var el = $('#' + this.currentNavSelection).parent();
			if (el[0]) {
				var count = parseInt($('#' + this.currentNavSelection).text());
				// If the count is different, then the one we're viewing in the pane is updated
				// so we need to refresh that too
				if (count != this.currentNavSelectionOldCount) {
					DeskPRO_Window.runPageRouteFromElement(el);
				}

				el.addClass('nav-selected');
			}
		}
		this.currentNavSelection = null;
		this.currentNavSelectionOldCount = null;

		this.fireEvent('sectionInit');
	},

	_initSectionSearch: function() {
		var searchPane = this.contentEl.find('.source-pane-search');
		if (searchPane[0]) {
			this.searchForm = new DeskPRO.Agent.SourcePane.SearchForm(searchPane);
		}
	},

	recountBadge: function() {
		var count = 0;
		count += parseInt($('#feedback_validating_count').text().trim()) || 0;
		count += parseInt($('#feedback_comments_validating_count').text().trim()) || 0;
		this.updateBadge(count);
	}
});

Orb.createNamespace('DeskPRO.Agent.WindowElement.Section');

DeskPRO.Agent.WindowElement.Section.Tasks = new Orb.Class({
	Extends: DeskPRO.Agent.WindowElement.Section.AbstractSection,

	init: function() {
		this.buttonEl = $('#tasks_section');

		this.urlFragmentName = 'tasks';

		this.setSectionElement($('<section id="task_outline"></section>'));
		this.refresh();

		DeskPRO_Window.getMessageBroker().addMessageListener('agent.ui.tasks.refresh-task-list', function() {
			this.refresh();
		}, this);

		this.doRelaodPage = false;
		this.addEvent('show', function() {
			if (this.doRelaodPage) {
				this.refreshPage();
			}
		}, this);
	},

	refreshPage: function() {
		this.doRelaodPage = false;
		if (this.listPage && this.listPage.meta.routeUrl) {
			DeskPRO_Window.runPageRoute('listpane:' + this.listPage.meta.routeUrl);
		}
	},

	refresh: function() {
		var countmap = {};
		$('span.list-counter', this.contentEl).each(function() {
			countmap[$(this).attr('id')] = $(this).text().trim();
		});

		var selected = $('.nav-selected', this.contentEl);
		var selectedCountId = null;
		if (selected) {
			var e = $('span.list-counter', selected).first();
			if (e.length) {
				selectedCountId = e.attr('id');
			}
		}

		DeskPRO_Window.getSectionData('tasks_section', (function(data) {
			this._initSection(data);

			this.recalcBadge();

			if (selectedCountId) {
				var countEl = $('#' + selectedCountId);
				var newCount = countEl.text().trim();
				var nav = countEl.closest('.is-nav-item');

				// Re-select the proper nav item
				nav.addClass('nav-selected');

				// And reload the view if its changed
				if (newCount != countmap[selectedCountId]) {
					var routeEl;
					if (nav.data('route')) {
						routeEl = nav;
					} else {
						routeEl = $('[data-route]', nav).first();
					}
					DeskPRO_Window.runPageRouteFromElement(routeEl);
				}

			}
		}).bind(this));
	},

	recalcBadge: function() {
		var total = parseInt($.trim($('#tasks_counter_all_total').text()));
		this.modBadgeCount('=', total);
	},

	markUnloadPage: function() {
		this.doRelaodPage = true;
		if ($('#task_outline').is('.on')) {
			this.refreshPage();
		}
	},

	_initSection: function(data) {
		this.setHasInitialLoaded();
		this.contentEl.html(data.section_html);
		this.fireEvent('sectionInit');
	}
});

Orb.createNamespace('DeskPRO.Agent.WindowElement.Section');

DeskPRO.Agent.WindowElement.Section.Twitter = new Orb.Class({
	Extends: DeskPRO.Agent.WindowElement.Section.AbstractSection,

	init: function() {
		this.buttonEl = $('#twitter_section');
		this.urlFragmentName = 'twitter';
		var self = this;

		this.setSectionElement($('<section id="twitter_outline"></section>'));

		this.refresh();

		DeskPRO_Window.getMessageBroker().addMessageListener('agent.tweet-added', function (data) {
			self.adjustTweetCountsFromClientMessage(data, 1);
		});

		DeskPRO_Window.getMessageBroker().addMessageListener('agent.tweet-updated', function (data) {
			if (typeof data.change_archived !== 'undefined') {
				if (data.change_archived) {
					// moved to archived, reduce counts
					self.adjustTweetCountsFromClientMessage(data, -1);
				} else {
					// moved to unarchived, increase counts
					self.adjustTweetCountsFromClientMessage(data, 1);
				}
			}
			if (data.deleted) {
				self.adjustTweetCountsFromClientMessage(data, -1);
			}
			if (data.favorited) {
				self.adjustTweetCountsFromClientMessage(data, 1);
				self.adjustTweetCountsFromClientMessage($.extend({}, data, {is_favorited: 0}), -1);
			}
			if (data.unfavorited) {
				self.adjustTweetCountsFromClientMessage(data, 1);
				self.adjustTweetCountsFromClientMessage($.extend({}, data, {is_favorited: 1}), -1);
			}
			if (typeof data.change_assignment !== 'undefined') {
				self.adjustTweetCountsFromClientMessage(data, 1);
				self.adjustTweetCountsFromClientMessage($.extend(
					{}, data, {assignment: data.old_assignment, agent_id: data.old_agent_id, agent_team_id: data.old_agent_team_id}), -1
				);
			}
		});

		DeskPRO_Window.getMessageBroker().addMessageListener('agent.twitter-follower', function (data) {
			var newCount = $('#twitter_' + data.account_id + '_new_followers_count');
			var totalCount = $('#twitter_' + data.account_id + '_followers_count');
			var totalCountHeader = $('#twitter_' + data.account_id + '_followers_count_header');

			switch (data.action) {
				case 'new':
					newCount.text(parseInt(newCount.text().trim(), 10) + 1);
					totalCount.text(parseInt(totalCount.text().trim(), 10) + 1);
					totalCountHeader.text(totalCount.text());
					break;

				case 'new-archived':
					totalCount.text(parseInt(totalCount.text().trim(), 10) + 1);
					totalCountHeader.text(totalCount.text());
					break;

				case 'archived':
					newCount.text(Math.max(0, parseInt(newCount.text().trim(), 10) - 1));
					break;

				case 'unarchived':
					newCount.text(parseInt(newCount.text().trim(), 10) + 1);
					break;
			}
		});

		DeskPRO_Window.getMessageBroker().addMessageListener('agent.twitter-friend', function (data) {
			var totalCount = $('#twitter_' + data.account_id + '_following_count');
			var totalCountHeader = $('#twitter_' + data.account_id + '_following_count_header');

			switch (data.action) {
				case 'new':
					totalCount.text(parseInt(totalCount.text().trim(), 10) + 1);
					totalCountHeader.text(totalCount.text());
					break;

				case 'removed':
					totalCount.text(Math.max(0, parseInt(totalCount.text().trim(), 10) - 1));
					totalCountHeader.text(totalCount.text());
					break;
			}
		});

		DeskPRO_Window.getMessageBroker().addMessageListener('twitter-section.list-activated', function (info) {
			var url = 'listpane:' + info.listUrl.replace('?partial=1', '');

			var interval;

			var f = function() {
				if (self.contentEl && self.contentEl.find('.is-nav-item').length) {
					clearInterval(interval);
					interval = false;
					self.contentEl.find('.is-nav-item').each(function() {
						var $this = $(this);
						if ($this.data('route') === url) {
							self.highlightNavItem($this);
							return false;
						}
					});
				}
			};
			if (self.contentEl && self.contentEl.find('.is-nav-item').length) {
				f();
			} else {
				interval = setInterval(f, 1000);
			}
		});

		window.setInterval(function() {
			self.refresh();
		}, 420000); // update every 7 mins
	},

	adjustTweetCountsFromClientMessage: function(data, adjustAmount) {
		if (data.is_from_self) {
			return;
		}

		var accountId = data.account_id;

		var types = {};
		this.getSectionElement().find('#twitter-section-counts-' + accountId + ' > li').each(function() {
			var $this = $(this);
			types[$this.data('type')] = $this;
		});

		if (data.assignment === 'agent:' + DESKPRO_PERSON_ID) {
			// my tweets
			this._adjustSectionCount(types.mine, data, adjustAmount);
		}

		for (var i = 0; i < DESKPRO_TEAM_IDS.length; i++) {
			var teamId = DESKPRO_TEAM_IDS[i];
			if (data.assignment === 'agent_team:' + teamId) {
				// my teams' tweets
				this._adjustSectionCount(types.team, data, adjustAmount);
				break;
			}
		}

		if (data.assignment === '') {
			// unassigned
			if (data.is_favorited) {
				this._adjustSectionCount(types.unassigned, data, adjustAmount);
			} else {
				switch (data.status_type) {
					case 'direct':
						if (data.is_from_self) {
							// own DM, consider as sent
							break;
						}
						// break missing intentionally

					case 'reply':
					case 'mention':
					case 'retweet':
						this._adjustSectionCount(types.unassigned, data, adjustAmount);
				}
			}
		}

		if (data.is_favorited) {
			this._adjustSectionCount(types.all, data, adjustAmount);
		} else {
			switch (data.status_type) {
				case 'direct':
					if (data.is_from_self) {
						// own DM, consider as sent
						break;
					}
					// break missing intentionally

				case 'reply':
				case 'mention':
				case 'retweet':
					// all tweets
					this._adjustSectionCount(types.all, data, adjustAmount);
					break;
			}
		}
	},

	_adjustSectionCount: function(el, data, adjustAmount) {
		if (!el) {
			return;
		}

		var groupType = el.data('initial-grouping');
		var type = el.data('type');

		var counter = el.find('h3 .list-counter');
		counter.text(parseInt(counter.text().trim(), 10) + adjustAmount);

		var subGroup = el.find('.sub-group');
		if (subGroup.length && groupType) {
			var value = '';

			switch (groupType) {
				case 'type':
					if (data.is_favorited) {
						value = 'favorite';
					} else {
						switch (data.status_type) {
							case 'reply':
							case 'mention':
							case 'retweet':
							case 'direct':
								value = data.status_type;
								break;

							default:
								value = 'other';
						}
					}
					break;

				case 'agent':
					value = data.agent_id;
					break;

				case 'team':
					value = data.agent_team_id;
					break;
			}

			var specificValue = subGroup.find('.twitter-sub-group-' + data.account_id + '-' + type + '-' + groupType + '-' + value);
			var count;
			if (specificValue.length) {
				counter = specificValue.find('.list-counter');
				count = Math.max(0, parseInt(counter.text().trim(), 10) + adjustAmount);
				counter.text(count);
				var li = specificValue.closest('li');
				if (count == 0) {
					li.hide();
				} else {
					li.show();
				}
			}

			if (subGroup.find('li').filter(function() { return $(this).css('display') !== 'none'; }).length) {
				subGroup.show();
			} else {
				subGroup.hide();
			}
		}

		this.recountBadge();
	},

	refresh: function(extraData) {
		var self = this;
		DeskPRO_Window.getSectionData('twitter_section', function(data) {
			self._initSection(data);

			if (self.sectionEl) {
				var scroller = self.sectionEl.find('.with-scroll-handler').data('scroll_handler');
				if (scroller) {
					scroller.updateSize();
				}
			}
		}, extraData);
	},

	_initSection: function(data) {
		var self = this;

		this.setHasInitialLoaded();
		this.contentEl.html(data.section_html);

		var contentEl = this.contentEl;

		contentEl.find('.source-list .sub-group').each(function() {
			var $this = $(this);
			if (!$this.find('li').filter(function() { return $(this).css('display') !== 'none'; }).length) {
				$this.css('display', 'none');
			}
		});

		contentEl.on('click', '.twitter-account-add-status', function() {
			if (DeskPRO_Window.newTweetLoader) {
				var accountId = $(this).data('account-id');
				DeskPRO_Window.newTweetLoader.open(function(page) {
					var select = page.getEl('from_account');
					if (select.length && select.is('.with-select2')) {
						select.select2('val', [accountId]);
					}
				});
			}
		});

		contentEl.on('click', '.twitter-search-delete-trigger', function(e) {
			e.preventDefault();
			e.stopPropagation();

			var $this = $(this);

			if (confirm($this.data('confirm'))) {
				$.ajax({
					url: $this.attr('href'),
					type: 'POST'
				});

				$this.closest('li').remove();
			}
		});

		this.searchBoxes = {};
		contentEl.find('input.twitter-search-add-box').each(function() {
			var searchBox = $(this);
			var tabHolder = $(this).closest('.deskpro-tab-item');
			var accountId = tabHolder.data('account-id');

			self.searchBoxes[accountId] = searchBox;
			searchBox.on('keypress', function(e) {
				if (e.which != 13) {
					return true;
				}

				e.preventDefault();
				self.doSearch(searchBox.val(), accountId);
				searchBox.val('');
			});
			tabHolder.find('.twitter-search-add-button').click(function(e) {
				e.preventDefault();
				self.doSearch(searchBox.val(), accountId);
				searchBox.val('');
			});
		});

		contentEl.find('input.twitter-person-find-box').each(function() {
			var personBox = $(this);
			var tabHolder = $(this).closest('.deskpro-tab-item');
			var accountId = tabHolder.data('account-id');

			personBox.on('keypress', function(e) {
				if (e.which != 13) {
					return true;
				}

				e.preventDefault();
				self.doFindPerson(personBox.val(), accountId);
				personBox.val('');
			});
			tabHolder.find('.twitter-person-find-button').click(function(e) {
				e.preventDefault();
				self.doFindPerson(personBox.val(), accountId);
				personBox.val('');
			});
		});

		contentEl.find('.twitter-tab-container').each(function() {
			var container = $(this);

			new DeskPRO.UI.SimpleTabs({
				context: container,
				triggerElements: '.pane-section-tabs li',
				onTabSwitch: function(info) {
					self.updateUi();
				}
			});
		});

		var onGroupingChanged = function(accountId, group_by, el) {
			el.data('initial-grouping', group_by);

			$.ajax({
				url: BASE_URL + 'agent/twitter/update-grouping.json',
				data: {account_id: accountId, type: el.data('type'), group: group_by },
				dataType: 'json',
				success: function(json) {
					el.find('.item-form').hide();

					if (json.group == el.data('initial-grouping')) {
						var html = $(json.html);
						if (!html.find('li').filter(function() { return $(this).css('display') !== 'none'; }).length) {
							html.css('display', 'none');
						}
						el.find('.nav-list-small').first().replaceWith(html);
						el.find('.nav-list-small').show();
					}
				}
			});
		};

		this.sectionEl.find('.dp-toggle-icon').on('click', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();
			ev.stopImmediatePropagation();

			var $me    = $(this);
			var $li    = $me.closest('li');
			var $group = $li.find('> .item-form');
			var $groupList = $li.find('> .nav-list-small');
			var sel = $group.find('select');
			var $account = $me.closest('.twitter-account-section');
			var accountId = parseInt($account.data('account-id'));
			var $counter  = $account.find('.counter').first();

			if ($group[0]) {
				if ($me.hasClass('icon-caret-right')) {
					$me.removeClass('icon-caret-right');
					$me.addClass('icon-caret-down');
					$group.show();
					$groupList.show();

					if (!sel.hasClass('with-select2')) {
						DP.select(sel);
						sel.on('change', function(ev) {
							onGroupingChanged(accountId, sel.val(), $li);
						});
					}
				} else {
					// Remove grouping
					sel.select2('val', '');
					sel.trigger('change');

					$me.addClass('icon-caret-right');
					$me.removeClass('icon-caret-down');
					$group.hide();
					$groupList.hide();
					onGroupingChanged(accountId, '', $li);
				}
			}
		});

		this.recountBadge();
	},

	doSearch: function(searchTerm, accountId) {
		searchTerm = $.trim(searchTerm);
		if (!searchTerm.length) {
			return;
		}

		var templateLi = this.searchBoxes[accountId].closest('.nav-list').find('.twitter-delete-template');

		var templateHtml = templateLi.clone().wrap('<div>').parent().html();
		templateHtml = templateHtml.replace(/__placeholder-url__/g, encodeURIComponent(searchTerm)).replace(/__placeholder__/g, searchTerm);
		templateLi.after($(templateHtml).show());

		DeskPRO_Window.runPageRoute('listpane:' + BASE_URL + 'agent/twitter/' + accountId + '/search/new?search_term=' + encodeURIComponent(searchTerm));
	},

	doFindPerson: function(name, accountId) {
		name = $.trim(name);
		if (!name.length) {
			return;
		}

		DeskPRO_Window.runPageRoute('page:' + BASE_URL + 'agent/twitter/user/find?tab=1&name='
			+ encodeURIComponent(name) + '&account_id=' + accountId
		);
	},

	recountBadge: function() {
		var count = 0;
		this.contentEl.find('.twitter-all-counter').each(function() {
			count += parseInt($(this).text().trim(), 10) || 0;
		});
		this.updateBadge(count);
	}
});

Orb.createNamespace('DeskPRO.Agent.WindowElement.Section');

DeskPRO.Agent.WindowElement.Section.Deals = new Orb.Class({
	Extends: DeskPRO.Agent.WindowElement.Section.AbstractSection,

	init: function() {
            this.buttonEl = $('#deals_section');

			this.urlFragmentName = 'deals';

            this.setSectionElement($('<section id="deal_outline"></section>'));
            this.refresh();

        },

	refresh: function() {
		DeskPRO_Window.getSectionData('deals_section', this._initSection.bind(this));
	},
	_initSection: function(data) {
		this.setHasInitialLoaded();
		this.contentEl.html(data.section_html);
		this.recountBadge();
	},

	recountBadge: function() {
		var count = 0;
		$('span.badge-count', this.contentEl).each(function() {
			count += parseInt($(this).text().trim()) || 0;
		});

		this.updateBadge(count);
	}
});
