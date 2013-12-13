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
