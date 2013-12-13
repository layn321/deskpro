Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');
DeskPRO.Agent.PageFragment.ListPane.Basic = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.Basic,

	initialize: function(html) {
		this.parent(html);

		this.addEvent('activate', function() {
			DeskPRO_Window.updateWindowUrlFragment();
			DeskPRO_Window.getMessageBroker().sendMessage('list-page-fragment.activated', { page: this });
		}, this);
	},

	enableHighlightOpenRows: function(tabtype, id_property, css_prefix) {
		this.addEvent('watchedTabAdded', function(tab) {
			$(css_prefix + tab.page.meta[id_property], this.wrapper || this.el).addClass('open');
		});
		this.addEvent('watchedTabRemoved', function(tab) {
			$(css_prefix + tab.page.meta[id_property], this.wrapper || this.el).removeClass('open');
		});
		DeskPRO_Window.getTabWatcher().addTabTypeWatcher(tabtype, this, true);
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.BasicTicketResults = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.Basic,

	initializeProperties: function() {
		this.parent();
		this.wrapper = null;
		this.contentWrapper = null;
		this.layout = null;
		this.overlay = null;
		this.appendUrl = null;

		this.resultTypeName = 'basic';
		this.resultTypeId = 'general';
	},

	initPage: function(el) {
		var self = this;
		this.autoAddAjax = {};
		this.wrapper = $(el);

		if (this.meta.filter_id) {
			DeskPRO_Window.sections.tickets_section.highlightFilterNav(this.meta.filter_id, this.meta.topGroupingOption || null);
		}

        $('.extra-fields .agent .agent_link', this.el).on('click', function(ev) {
            var agent_id = $(this).parent().data('prop-value');

            if(agent_id) {
                ev.stopPropagation();
                ev.preventDefault();
                DeskPRO_Window.sections.agent_chat_section.newChatWindow([agent_id]);
            }
        });

		DeskPRO_Window.getMessageBroker().addMessageListener('agent-notification.tickets.unlocked', (function(info) {
			var ticketId = info.ticket_id;
			$('.ticket-' + ticketId, this.contentWrapper).removeClass('locked');
		}).bind(this), null, [this.OBJ_ID]);
		DeskPRO_Window.getMessageBroker().addMessageListener('agent-notification.tickets.locked', (function(info) {
			var ticketId = info.ticket_id;
			$('.ticket-' + ticketId, this.contentWrapper).addClass('locked');
		}).bind(this));

		DeskPRO_Window.getMessageBroker().addMessageListener('tickets.deleted', (function(ticket_ids) {
			var sels = [];
			Array.each(ticket_ids, function(val) {
				this.resultsHelper.removeResultId(val);
				sels.push('article.ticket-' + val);
			}, this);

			sels = sels.join(', ');

			var els = $(sels, this.contentWrapper).addClass('removing').fadeOut(400, function() {
				$(this).remove();
				self.updateTicketCountLabels();
				self.updateUi();
			});

			this.countTotal -= els.length;
		}).bind(this), null, [this.OBJ_ID])

		DeskPRO_Window.getMessageBroker().addMessageListener('agent.ui.ticket_updated', function(info) {
			var ticketId = info.ticket_id;
			self.addTicket(ticketId, true);
		}, null, [this.OBJ_ID]);

		this.contentWrapper = $('.layout-content:first', this.wrapper);

		this._initDisplayOptions();
		this._initFlagMenu();
		this._initGroupingOptions();

		if (this.getMetaData('noResults')) {
			this.noMoreResults = true;
			$('.no-more-results', this.contentWrapper).show();
		}

		this.performActionsBtn = $('.perform-actions-trigger', this.wrapper);

		var openMassActions = function() {
			if (!self.massActions) {
				self.massActions = new DeskPRO.Agent.TicketList.MassActions(self, {
					isListView: (self.meta.viewType == 'list' ? true : false),
					onPostApply: function() {
						self.selectionBar.checkNone();
					},
					onClosed: function() {
						if (self.massActions) {
							self.massActions.destroy();
						}

						self.selectionBar.checkNone();
						self.massActions = null;
					}
				});
			}

			self.massActions.open();
		};

		self.getEl('perform_actions_btn').on('click', function(ev) {
			Orb.cancelEvent(ev);
			openMassActions();
		});

		var viewType = this.meta.viewType;
		var opt = {
			saveSelectionId: self.meta.filter_id ? ('filter_'+self.meta.filter_id) : null,
			onButtonClick: function() {
				if (viewType != 'list' && DeskPRO_Window.paneVis.tabs) {
					openMassActions();
				}
			},
			onCountChange: function(count) {
				if (viewType != 'list' && DeskPRO_Window.paneVis.tabs) {
					var isOpen = self.massActions && self.massActions.isOpen();

					if (count > 0 && !isOpen) {
						openMassActions();
					} else if (count <= 0 && isOpen) {
						if (self.massActions) {
							self.massActions.close();
						}
					}
				} else {
					if (count > 0) {
						self.getEl('perform_actions_btn').show();
					} else {
						self.getEl('perform_actions_btn').hide();
					}
				}
			}
		};

		if (viewType == 'list') {
			this.wrapper.find('.perform-actions-trigger').on('click', function(ev) {
				Orb.cancelEvent(ev);
				openMassActions();
			});
		}

		if (this.meta.viewType == 'list') {
			opt.selectionBar = $('thead, .selection-bar', el);
		}
		this.selectionBar = new DeskPRO.Agent.PageHelper.SelectionBar(this, opt);
		this.ownObject(this.selectionBar);

		var m = new DeskPRO.UI.Menu({
			triggerElement: $('button.sub-group-trigger:first', this.contentWrapper),
			menuElement: $('ul.sub-group-menu:first', this.contentWrapper)
		});
		this.ownObject(m);

		if (this.meta.groupingIgnore) {
			var groupByMenu = $('.group-by-menu', this.wrapper);
			Array.each(this.meta.groupingIgnore, function(ig) {
				$('[value="' + ig + '"], [data-group-by="' + ig + '"]', groupByMenu).remove();
			});
		}

		var opt = {
			resultIds: this.meta.ticketResultIds,
			perPage: this.meta.perPage || 50
		};
		if (this.meta.viewType == 'list') {
			opt.resultRowSelector = 'tr.row-item';
			opt.resultsContainer = $('.table-result-list table', el);
			opt.navEl = $('.bottom-action-bar', el);
		} else {
			opt.resultsContainer = $('> .list-listing', this.getEl('is_results'));
		}

		opt.onPostSetNewResults = function() {
			self.selectionBar.resetCountLabel();
		};
		this.resultsHelper = new DeskPRO.Agent.PageHelper.Results(this, opt);
		this.ownObject(this.resultsHelper);

		// We dont need them anymore, and resultsHelper
		// has its own strucutred array anyway,
		// since it could be large we can delete it from memory
		delete this.meta.ticketResultIds;

		if (this.meta.viewType == 'list') {

			this.resultsHelper.options.postSetNewResults = function() {
				self.selectionBar.controlCheck = self.wrapper.find('.selection-control');
				self.selectionBar.controlCheck.on('click', function() {
					if ($(this).is(':checked')) {
						self.selectionBar.checkAll();
					} else {
						self.selectionBar.checkNone();
					}
				});
			};

			$('.list-grouping-bar', this.wrapper).on('click', 'a[data-route]', function(ev) {
				ev.stopPropagation();
				ev.preventDefault();

				var route = $(this).data('route');
				self.loadNewListviewUrl(route.replace('listpane:', '') + '&view_type=list');
			});
		}

		this.enableHighlightOpenRows('ticket', 'ticket_id', '.row-item.ticket-');
		this.countTotal = parseInt(this.getEl('total_count').text().trim()) || 0;

		if (this.meta.viewType != 'list') {
			this.listNav = new DeskPRO.Agent.PageHelper.ListNav(this);
		}

		this.selectionBar.restoreFromSessionStorage();
	},

	handleAutoAdd: function(ticketId) {
		var self = this;
		self.reloadIfStale = false;
		var row = $('article.ticket-' + ticketId, self.contentWrapper);
		if (row[0] && row.hasClass('removing')) {
			return false;
		}

		// If a ticket has been updated, it's not on our list, and we're viewing a sub-groupging,
		// we need to reload the whole list to know if the ticket was added
		if (self.meta.topGroupingTerm) {
			var li = $('#system_filters_wrap').find('.nav-selected');
			if (!li[0]) {
				$('#tickets_outline_custom_filters').find('.nav-selected');
			}

			if (!li[0]) {
				return false;
			}

			// See DeskPRO/Agent/WindowElement/Section/Tickets.js
			// is-stale is added when the counts were updated, which means
			// the list we're looking at is now out of date, meaning we need to relaod
			if (li.hasClass('is-stale')) {
				if (self.meta.routeData && self.meta.routeData.route) {
					DeskPRO_Window.runPageRoute(self.meta.routeData.route);
				}
			}

			// This might be run after is-stale (order is undefined based on event delegation),
			// so the list needs to know if to reload too
			self.reloadIfStale = true;

			if (!row[0]) {
				return false;
			}
		}

		if (self.meta.groupBy) {
			if (self.meta.routeData && self.meta.routeData.route) {
				DeskPRO_Window.runPageRoute(self.meta.routeData.route);
			}
		}

		self.addTicket(ticketId, false);
		return true;
	},

	_handleResize: function() {
		if (!this.layout) return;
		this.layout.resizeAll();
	},

	addTicket: function(ticket_id, replace_existing) {
		var self = this;
		if (!this.meta.loadSingleUrl) {
			return;
		}

		var exist = self.getEl('results_wrap').find('article.ticket-' + ticket_id);
		if (exist[0] && exist.hasClass('removing')) {
			return;
		}

		this.resultsHelper.prependResultId(ticket_id);

		if (this.resultsHelper.getCurrentPage() == 1) {
			var url = this.meta.loadSingleUrl.replace('$ticket_id', ticket_id).replace('$view_type', this.meta.viewType || '');

			if (replace_existing) {
				var exist = self.getEl('results_wrap').find('article.ticket-' + ticket_id);
				if (!exist[0] || exist.hasClass('removing')) {
					return;
				}
			}

			this.autoAddAjax[ticket_id] = $.ajax({
				url: url,
				dataType: 'html',
				context: this,
				complete: function() {
					if (this.autoAddAjax[ticket_id]) delete this.autoAddAjax[ticket_id];
				},
				success: function(html) {
					var el = $(html);

					var ticketRow = el.find('article.row-item');
					var ticketId = ticketRow.data('ticket-id');
					if (ticketId) {
						var tabs = DeskPRO_Window.tabWatcher.findTabType('ticket');
						Array.each(tabs, function(t) {
							if (t.page.meta.ticket_id == ticket_id) {
								ticketRow.addClass('open');
								return false;
							}
						});
					}

					$('.timeago', el).timeago();

					var exist = self.getEl('results_wrap').find('article.ticket-' + ticket_id);

					if (replace_existing && !exist[0]) {
						return;
					}
					if (exist[0] && exist.hasClass('removing')) {
						return;
					}

					if (exist[0] && replace_existing) {

						if (!el.is('.row-item')) {
							el = el.find('.row-item');
							el.hide();
							el.detach();
						}

						exist.after(el);
						exist.remove();
						el.show();
					} else {
						if (exist[0]) {
							exist.remove();
						}

						self.getEl('results_wrap').prepend(el);
						el.slideDown('fast', self.updateUi.bind(self));

						this.countTotal++;
						this.updateTicketCountLabels();
					}
				}
			});
		}
	},

	delTicket: function(ticket_id) {
		var self = this;
		var el = $('.ticket-' + ticket_id, this.contentWrapper);

		if (this.autoAddAjax[ticket_id]) {
			this.autoAddAjax[ticket_id].abort();
			if (this.autoAddAjax[ticket_id]) delete this.autoAddAjax[ticket_id];
		}

		if (!el[0]) {
			return;
		}

		el.addClass('removing');
		el.animate({ height: 'toggle', opacity: 'toggle' }, 'slow', function() {
			self.resultsHelper.removeResultId(ticket_id);
			el.remove();
			self.countTotal--;
			self.updateTicketCountLabels();
			self.updateUi();
		});
	},

	updateTicketCountLabels: function() {

		var showing = $('article.row-item', this.wrapper).length || 0;

		if (this.countTotal < 0) {
			this.countTotal = 0;
		}

		if (this.countTotal < 1) {
			this.getEl('is_results').hide();
			this.getEl('no_results').show();

			this.getEl('total_count').text(this.countTotal);
			this.wrapper.find('.results-count-display').text(this.countTotal);
		} else {

			if (!this.resultsHelper) {
				return;
			}

			// If there is no results element, it means the list was loaded with no results
			// and the various control elements havent been rendered.
			// So we need to refresh the view
			if (!this.getEl('is_results').length) {
				DeskPRO_Window.loadListPane(this.meta.refreshUrl);
				return;
			}

			this.getEl('no_results').hide();
			this.getEl('is_results').show();

			var lowerBounds = ((this.resultsHelper.currentPage-1) * this.resultsHelper.options.perPage) + 1;
			var upperBounds = (lowerBounds-1) + showing;
			if (upperBounds > this.countTotal) {
				upperBounds = this.countTotal;
			}

			this.getEl('showing_count').text(lowerBounds + '-' + upperBounds);
			this.getEl('total_count').text(this.countTotal);
			this.wrapper.find('.results-count-display').text(this.countTotal);
			$('span', this.getEl('total_grouped_count')).text(this.countTotal);
			this.selectionBar.resetCountLabel();
		}
	},

	//#########################################################################
	//# Edit Search buttons
	//#########################################################################

	_initSearchOptions: function() {
		var editBtn = $('.summary .edit', this.topSection);
		editBtn.on('click', this.showSearchForm.bind(this));

		var form = $('form.ticket-search-form', this.topSection);
		form.on('submit', function(ev) {
			ev.preventDefault();

			var url = form.attr('action');
			var data = form.serializeArray();

			DeskPRO_Window.loadListPane(url, { postData: data });
		});
	},

	showSearchForm: function() {
		var criteriaList  = $('.search-form', this.topSection);
		var criteriaTerms = $('.search-builder-tpl', this.topSection);

		var editor = new DeskPRO.Form.RuleBuilder(criteriaTerms);
		this.ownObject(editor);

		$('.add-term', criteriaList).data('add-count', 0).on('click', function() {
			var count = parseInt($(this).data('add-count'));
			var basename = 'terms['+count+']';

			$(this).data('add-count', count+1);

			editor.addNewRow($('.search-terms', criteriaList), basename);
		});

		var searchDataEl = $('.search-form-data:first', this.topSection);
		if (searchDataEl.length) {
			var searchData = searchDataEl.get(0).innerHTML;
			searchData = $.parseJSON(searchData);

			if (searchData.terms) {
				Array.each(searchData.terms, function(info, x) {
					var basename = 'terms[initial_' + x + ']';
					editor.addNewRow($('.search-terms', criteriaList), basename, {
						type: info.type,
						op: info.op,
						options: info.options
					});
				});
			}

			if (searchData.order_by) {
				$('[name="order_by"]', this.topSection).val(searchData.order_by);
			}

			searchDataEl.remove();
		}

		$('.summary', this.topSection).slideUp();
		$('.form-panel', this.topSection).slideDown();
	},

	//#########################################################################
	//# Grouping buttons
	//#########################################################################

	_initGroupingOptions: function() {

		var self = this;
		$('div.search-top ul.grouping-info > li[data-group-id]', this.contentWrapper).on('click', function() {
			self.switchToSubgroup($(this).data('group-id'), $(this));
		});
	},

	switchToSubgroup: function(field_id, el) {

		if (field_id == 'NONE') {
			this.appendUrl = null;
		} else {
			this.appendUrl = '&group_field_id=' + field_id;
		}

		$('table.list tbody', this.contentWrapper).remove();
		this.loadResultPage(1);

		$('div.search-top ul.grouping-info > li', this.contentWrapper).removeClass('on');

		if (el) {
			el.addClass('on');
		}
	},

	//#########################################################################
	//# Flag menu
	//#########################################################################

	_initFlagMenu: function() {
		var self = this;
		this.flagMenu = new DeskPRO.UI.Menu({
			menuElement: $('> ul.ticket-flag-menu:first', this.contentWrapper),
			onItemClicked: function(info) {
				self._handleFlagMenuClick(info);
			}
		});
		this.ownObject(this.flagMenu);

		$('table.list:first', this.contentWrapper).on('click', 'span.ticket-flag', function(ev) {
			self.flagMenu.openMenu(ev);
		});
	},

	_handleFlagMenuClick: function(info) {

		var item = $(info.itemEl);
		var flag = item.data('flag');

		var m = $(info.menu.getOpenTriggerElement());
		var ticketId = m.parent().parent().data('ticket-id');
		if (!ticketId) {
			return;
		}

		var old_flag = m.data('flag');

		m.removeClass('icon-flag-'+old_flag);
		m.addClass('icon-flag-'+flag);
		m.data('flag', flag);

		$.ajax({
			url: BASE_URL + 'agent/tickets/' + ticketId + '/ajax-save-flagged',
			type: 'POST',
			context: this,
			data: { color: flag },
			dataType: 'json',
			success: function(data) {

			}
		});
	},

	//#########################################################################
	//# Display options
	//#########################################################################

	_initDisplayOptions: function() {

		var self = this;

		$('.detail-view-trigger', this.contentWrapper).on('click', function() {
			self.switchViewType('list');
		});

		this.displayOptions = new DeskPRO.Agent.PageHelper.DisplayOptions(this, {
			prefId: 'ticket-' + this.resultTypeName,
			resultId: this.resultTypeId,
			refreshUrl: this.meta.refreshUrl,
			isListView: (this.meta.viewType == 'list' ? true : false)
		});
		this.ownObject(this.displayOptions);

		// Sorting options
		var sortMenuBtn = $('.order-by-menu-trigger', this.wrapper).first();
		this.sortingMenu = new DeskPRO.UI.Menu({
			triggerElement: sortMenuBtn,
			menuElement: $('.order-by-menu', this.wrapper).first(),
			onItemClicked: function(info) {
				var item = $(info.itemEl);

				var prop = item.data('order-by');
				var label = item.find('.label').text().trim();

				// Change the displayed label for some visual feedback
				$('.label label', sortMenuBtn).text(label);
				sortMenuBtn.find('.order-dir').hide();
				sortMenuBtn.find('.order-dir.' + prop.split('_').pop()).show();


				var disOptWrap = self.displayOptions.getWrapperElement();
				var sel = $('select.sel-order-by', disOptWrap);
				$('option', sel).prop('selected', false);
				$('option.' + prop, sel).prop('selected', true);

                if(self.wrapper.find('header.list-grouping-bar').css('display') == 'block') {
                    self.wrapper.find('header.list-grouping-bar').hide();
                    self.getEl('grouping_loading').show();
                }

				self.displayOptions.saveAndRefresh();
			}
		});
		this.ownObject(this.sortingMenu);

		var groupMenuBtn = $('.group-by-menu-trigger', this.wrapper).first();
		this.groupingMenu = new DeskPRO.UI.Menu({
			triggerElement: groupMenuBtn,
			menuElement: $('.group-by-menu', this.wrapper).first(),
			onItemClicked: function(info) {
				var item = $(info.itemEl);

				var prop = item.data('group-by')
				var label = item.text().trim();

				// Change the displayed label for some visual feedback
				$('.label', groupMenuBtn).text(label);

				var url = self.meta.refreshUrl;
				url = Orb.appendQueryData(url, 'group_by', prop);

				if (self.meta.viewType == 'list') {
					self.loadNewListviewUrl(url +'&view_type=list');
				} else {
					self.wrapper.find('header.list-grouping-bar').hide();
					self.getEl('grouping_loading').show();
					DeskPRO_Window.loadListPane(url);
				}
			}
		});
		this.ownObject(this.groupingMenu);
	},

	switchViewType: function(view_type) {

		var new_url = this.meta.viewTypeUrl.replace('$view_type', view_type);

		if (view_type == 'list') {
			var oldlist = this.listview;
			this.listview = new DeskPRO.Agent.TicketList.ListView(this);

			if (oldlist && !oldlist.OBJ_DESTROYED) {
				this.listview.addEvent('ajaxLoaded', function() {
					if (!oldlist.OBJ_DESTROYED) {
						oldlist.destroy();
					}
				});
			}

			this.listview.open();
			return;
		}

		DeskPRO_Window.loadListPane(new_url, null, function() {
			DeskPRO_Window.removePage(self);
		});
	},

	loadNewListviewUrl: function(new_url) {
		var oldlist = this.listview;
		this.listview = new DeskPRO.Agent.TicketList.ListView(this, { load_url: new_url });

		if (oldlist && !oldlist.OBJ_DESTROYED) {
			oldlist.showInnerLoading();
			this.listview.addEvent('ajaxLoaded', function() {
				if (!oldlist.OBJ_DESTROYED) {
					oldlist.destroy();
				}
			});
		}

		oldlist.close();
		this.listview.open();
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.TicketFilter = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.BasicTicketResults,

	initializeProperties: function() {
		this.TYPENAME = 'ticket-filter';

		this.resultTypeName = 'filter';
		this.resultTypeId = 0;
	},

	initPage: function(el) {

		DeskPRO_Window.getMessageBroker().sendMessage('ticket-section.list-activated', { listType: 'filter', id: this.getMetaData('filter_id'), topGroupingOption: this.meta.topGroupingOption || null });
		this.resultTypeId = this.getMetaData('filter_id');
		this.parent(el);
	},

	activate: function() {
		if (this.getMetaData('filter_id')) {
			DeskPRO_Window.getMessageBroker().sendMessage('filter.view-activated', this.getMetaData('filter_id'));
		}
	},

	deactivate: function() {
		if (this.getMetaData('filter_id')) {
			DeskPRO_Window.getMessageBroker().sendMessage('filter.view-deactivated', this.getMetaData('filter_id'));
		}
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.OrganizationList = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'org-list';
		this.wrapper = null;
		this.contentWrapper = null;
		this.overlay = null;
		this.appendUrl = null;
		this.actionsBarHelper = null;
		this.resultTypeName = 'filter';
		this.resultTypeId = 0;
	},

	initPage: function(el) {

		var self = this;

		this.wrapper = $(el);
		this.contentWrapper = $('div.content:first', this.wrapper);

		this.resultTypeId = this.meta.cache_id || 0;

		if (this.getMetaData('noResults')) {
			this.noMoreResults = true;
			$('.no-more-results', this.contentWrapper).show();
		}

		this.displayOptions = new DeskPRO.Agent.PageHelper.DisplayOptions(this, {
			prefId: 'org-filter',
			resultId: this.resultId,
			refreshUrl: this.meta.refreshUrl
		});
		this.ownObject(this.displayOptions);

		this.enableHighlightOpenRows('organization', 'org_id', 'article.org-');

		var opt = {
			resultIds: this.meta.orgResultIds,
			perPage: this.meta.perPage || 50
		};
		if (this.meta.viewType && this.meta.viewType == 'list') {
			opt.resultRowSelector = 'tr.row-item';
			opt.resultsContainer = $('.table-result-list table', el);
			opt.navEl = $('.bottom-action-bar', el);
		}
		this.resultsHelper = new DeskPRO.Agent.PageHelper.Results(this, opt);
		this.ownObject(this.resultsHelper);

		delete this.meta.orgResultIds;

		// Sorting options
		var sortMenuBtn = $('.order-by-menu-trigger', this.wrapper).first();
		this.sortingMenu = new DeskPRO.UI.Menu({
			triggerElement: sortMenuBtn,
			menuElement: $('.order-by-menu', this.wrapper).first(),
			onItemClicked: function(info) {
				var item = $(info.itemEl);

				var prop = item.data('order-by')
				var label = item.text().trim();

				// Change the displayed label for some visual feedback
				$('.label', sortMenuBtn).text(label);

				var disOptWrap = self.displayOptions.getWrapperElement();
				var sel = $('select.sel-order-by', disOptWrap);
				$('option', sel).prop('selected', false);
				$('option.' + prop, sel).prop('selected', true);

				self.displayOptions.saveAndRefresh();
			}
		});
		this.ownObject(this.sortingMenu);
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.PeopleList = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'people-list';
		this.wrapper = null;
		this.contentWrapper = null;
		this.overlay = null;
		this.appendUrl = null;
		this.actionsBarHelper = null;
		this.resultTypeName = 'filter';
		this.resultTypeId = 0;
	},

	initPage: function(el) {

		var self = this;

		this.wrapper = $(el);
		this.contentWrapper = $('div.content:first', this.wrapper);

		this.resultTypeId = this.meta.cache_id || 0;

		if (this.getMetaData('noResults')) {
			this.noMoreResults = true;
			$('.no-more-results', this.contentWrapper).show();
		}

		this.displayOptions = new DeskPRO.Agent.PageHelper.DisplayOptions(this, {
			prefId: 'people-filter',
			resultId: this.resultId,
			refreshUrl: this.meta.refreshUrl,
			isListView: (this.meta.viewType == 'list' ? true : false)
		});
		this.ownObject(this.displayOptions);

		// Sorting options

		var sortMenuBtn = $('.order-by-menu-trigger', this.wrapper).first();
		this.sortingMenu = new DeskPRO.UI.Menu({
			triggerElement: sortMenuBtn,
			menuElement: $('.order-by-menu', this.wrapper).first(),
			onItemClicked: function(info) {
				var item = $(info.itemEl);

				var prop = item.data('order-by')
				var label = item.text().trim();

				// Change the displayed label for some visual feedback
				$('.label', sortMenuBtn).text(label);

				var disOptWrap = self.displayOptions.getWrapperElement();
				var sel = $('select.sel-order-by', disOptWrap);
				$('option', sel).prop('selected', false);
				$('option.' + prop, sel).prop('selected', true);

				self.displayOptions.saveAndRefresh();
			}
		});
		this.ownObject(this.sortingMenu);

		this.selectionBar = new DeskPRO.Agent.PageHelper.SelectionBar(this, {
			/*onCountChange: function(count) {
				var isOpen = self.massActionsMenu.isOpen();

				if (count > 0 && !isOpen) {
					self.massActionsMenu.open();
				} else if (count <= 0 && isOpen) {
					self.massActionsMenu.close();
				}
			}*/
		});
		this.ownObject(this.selectionBar);

		$('.detail-view-trigger', this.wrapper).on('click', (function() {
			this.switchViewType('list');
		}).bind(this));

		this.massActionsMenu = new DeskPRO.UI.Menu({
			triggerElement: $('.perform-actions-trigger:first', this.wrapper),
			menuElement: $('.actions-menu:first', this.wrapper),
			onItemClicked: function(info) {
				var itemEl = $(info.itemEl);
				var menuEl = itemEl.parent();
				var action = itemEl.data('action');

				if (menuEl.is('.submenu')) {
					action = menuEl.data('action');
				}

				var postData = self.selectionBar.getCheckedFormValues('ids');
				var removeFromList = false;

				switch (action) {
					case 'delete':

						break;

					case 'add-to-organization':
						var id = itemEl.data('organization-id');
						if (!id) {
							return;
						}

						postData.push({
							name: 'organization_id',
							value: id
						});
						break;

					case 'del-from-organization':

						break;

					case 'add-to-usergroup':
						var id = itemEl.data('usergroup-id');
						if (!id) {
							return;
						}

						postData.push({
							name: 'usergroup_id',
							value: id
						});
						break;

					case 'del-from-usergroup':
						var id = itemEl.data('usergroup-id');
						if (!id) {
							return;
						}

						postData.push({
							name: 'usergroup_id',
							value: id
						});
						break;

					default:
						return;
						break;
				}

				$.ajax({
					url: BASE_URL + 'agent/feedback/filter/mass-actions/' + action,
					data: postData,
					type: 'POST',
					dataType: 'json',
					success: function(data) {
						if (removeFromList) {
							self.selectionBar.getChecked().parent().fadeOut('fast');
						} else {
							self.selectionBar.getChecked().each(function() {
								var name = $('.subject', $(this).parent());
								DeskPRO_Window.util.showSavePuff(name);
							});
						}

						self.selectionBar.checkNone();
					}
				});
			}
		});
		this.ownObject(this.massActionsMenu);

		this.enableHighlightOpenRows('person', 'person_id', 'article.person-');

		var opt = {
			resultIds: this.meta.peopleResultIds,
			perPage: this.meta.perPage || 50
		};
		if (this.meta.viewType == 'list') {
			opt.resultRowSelector = 'tr.row-item';
			opt.resultsContainer = $('.table-result-list table', el);
			opt.navEl = $('.bottom-action-bar', el);
		}
		this.resultsHelper = new DeskPRO.Agent.PageHelper.Results(this, opt);
		this.ownObject(this.resultsHelper);

		// We dont need them anymore, and resultsHelper
		// has its own strucutred array anyway,
		// since it could be large we can delete it from memory
		delete this.meta.peopleResultIds;

		if (this.meta.viewType != 'list') {
			this.listNav = new DeskPRO.Agent.PageHelper.ListNav(this);
		}

		this.wrapper.on('click', 'button.agent-confirm-approve', function(ev) {
			ev.preventDefault();
			var el = $(this);
			DeskPRO_Window.util.ajaxWithClientMessages({
				url: BASE_URL + 'agent/people/validate/approve',
				data: { 'people_ids[]': el.data('person-id') },
				success: function() {
					DeskPRO_Window.getMessageBroker().sendMessage('agent.person.confirmed', { person_id: el.data('person-id') });
					el.closest('.validation-row').remove();
					self.updateUi();
				}
			});
		});
		this.wrapper.on('click', 'button.agent-confirm-delete', function(ev) {
			ev.preventDefault();
			var el = $(this);
			DeskPRO_Window.util.ajaxWithClientMessages({
				url: BASE_URL + 'agent/people/validate/delete',
				data: { 'people_ids[]': el.data('person-id') },
				success: function() {
					DeskPRO_Window.getMessageBroker().sendMessage('agent.person.removed', { person_id: el.data('person-id') });
					el.closest('article.row-item').remove();
					self.updateUi();
				}
			});
		});

		DeskPRO_Window.getMessageBroker().addMessageListener('agent.person.removed', function(info) {
			var row = self.wrapper.find('article.person-' + info.person_id);
			row.remove();
			self.updateUi();
		});
		DeskPRO_Window.getMessageBroker().addMessageListener('agent.person.confirmed', function(info) {
			var row = self.wrapper.find('article.person-' + info.person_id);
			row.find('.validation-row').remove();
			self.updateUi();
		});
	},

	destroyPage: function() {

	},

	switchViewType: function(view_type) {

		var new_url = this.meta.viewTypeUrl.replace('$view_type', view_type);

		if (view_type == 'list') {
			var oldlist = this.listview;
			this.listview = new DeskPRO.Agent.PageHelper.PeopleList.ListView(this);

			if (oldlist && !oldlist.OBJ_DESTROYED) {
				this.listview.addEvent('ajaxLoaded', function() {
					if (!oldlist.OBJ_DESTROYED) {
						oldlist.destroy();
					}
				});
			}

			this.listview.open();
			return;
		}

		DeskPRO_Window.loadListPane(new_url, null, function() {
			DeskPRO_Window.removePage(self);
		});
	},

	loadNewListviewUrl: function(new_url) {
		var oldlist = this.listview;
		this.listview = new DeskPRO.Agent.PageHelper.PeopleList.ListView({ load_url: new_url });

		if (oldlist && !oldlist.OBJ_DESTROYED) {
			oldlist.showInnerLoading();
			this.listview.addEvent('ajaxLoaded', function() {
				if (!oldlist.OBJ_DESTROYED) {
					oldlist.destroy();
				}
			});
		}

		this.listview.open();
	},

	saveDisplayOptions: function() {

		$('.loading-off', this.displayOptionsWrapper).hide();
		$('.loading-on', this.displayOptionsWrapper).show();

		var data = [];
		var pref_name = 'prefs[agent.ui.people-'+ this.resultTypeName + '-display-fields.' + this.resultTypeId +'][]';

		$('input[type="checkbox"]:checked', this.displayOptionsList).each(function() {
			data.push({
				name: pref_name,
				value: $(this).attr('name')
			});
		});


		// and the ordering
		data.push({
			name: 'prefs[agent.ui.people-'+ this.resultTypeName + '-order-by.' + this.resultTypeId +']',
			value: $('select[name="order_by"]', this.displayOptionsWrapper).val()
		});

		// We reload the same page which will have changes applied
		var url = this.getMetaData('refreshUrl');
		if (this.appendUrl) {
			url += this.appendUrl;
		}

		var self = this;

		$.ajax({
			timeout: 20000,
			type: 'POST',
			url: this.getMetaData('saveListPrefsUrl'),
			data: data,
			success: function() {

				DeskPRO_Window.loadListPane(url, null, function() {
					DeskPRO_Window.removePage(self);
				});

			}
		});
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.TicketFlagged = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.BasicTicketResults,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'ticket-flagged';

		this.resultTypeName = 'flagged';
		this.resultTypeId = 0;
	},

	initPage: function(el) {

		this.meta['view_name'] = 'flag';
		this.meta['view_extra'] = this.getMetaData('flag');

		DeskPRO.Agent.PageFragment.ListPane.BasicTicketResults.prototype.initPage.apply(this, [el]);
		this.resultTypeId = this.getMetaData('flag');
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.TicketDeletedList = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.BasicTicketResults,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'ticket-deleted-list';

		this.resultTypeName = 'filter';
		this.resultTypeId = 0;
	},

	initPage: function(el) {
		this.parent(el);
		this.resultTypeId = this.getMetaData('cache_id');
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.TicketCustomFilter = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.BasicTicketResults,

	initPage: function(el) {

		if (this.getMetaData('view_label')) {
			DeskPRO_Window.getMessageBroker().sendMessage('ticket-section.list-activated', { listType: 'label', id: this.getMetaData('label') });
		} else if (this.getMetaData('view_flag')) {
			DeskPRO_Window.getMessageBroker().sendMessage('ticket-section.list-activated', { listType: 'flag', id: this.getMetaData('flag') });
		} else if (this.getMetaData('view_spam')) {
			DeskPRO_Window.getMessageBroker().sendMessage('ticket-section.list-activated', { listType: 'archive', id: this.getMetaData('spam') });
		} else if (this.getMetaData('view_validating')) {
			DeskPRO_Window.getMessageBroker().sendMessage('ticket-section.list-activated', { listType: 'archive', id: this.getMetaData('validating') });
		} else if (this.getMetaData('view_recycle_bin')) {
			DeskPRO_Window.getMessageBroker().sendMessage('ticket-section.list-activated', { listType: 'archive', id: this.getMetaData('recycle_bin') });
		}


		this.parent(el);
		this.resultTypeId = this.getMetaData('cache_id');
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.TicketCustomFilterForm = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.Basic,

	initPage: function(el) {

		this.wrapper = $(el);

		this._initBasic();
		this._initFilterForm();

		if (this.getMetaData('autorun')) {
			this.submitForm();
		}
	},

	destroyPage: function() {

	},

	_initBasic: function() {
		var self = this;
		$('> .summary > .toggle', this.wrapper).on('click', function() {
			$('> .summary', self.wrapper).hide();
			$('> .criteria', self.wrapper).slideDown();
		});
	},

	_initFilterForm: function() {
		var self = this;

		var editor = new DeskPRO.Form.RuleBuilder($('.search-tpl', this.wrapper));
		editor.addEvent('newRow', function(new_row) {
			$('.remove', new_row).on('click', function() {
				new_row.remove();
			});
		});
		$('.search-form .add-term').data('add-count', 0).on('click', function() {
			var count = parseInt($(this).data('add-count'));
			var basename = 'terms['+count+']';

			$(this).data('add-count', count+1);

			editor.addNewRow($('.search-form .search-terms', self.wrapper), basename);
		});

		var self = this;
		$('button.run-filter-trigger', this.wrapper).on('click', function() {
			self.submitForm();
		});

		if (this.getMetaData('preselectTerms')) {
			var count = 0;
			var preselectTerms = this.getMetaData('preselectTerms');
			for (var i = 0; i < preselectTerms.length; i++) {
				if (!preselectTerms[i]) continue;

				editor.addNewRow(
					$('.search-form .search-terms', self.wrapper),
					'terms['+count+']',
					preselectTerms[i]
				);
			}

			$('.search-form .add-term', this.wrapper).data('add-count', count);
		}
	},

	submitForm: function() {

		var data = $('form.search-form-data', this.wrapper).serializeArray();

		$.ajax({
			cache: false,
			type: 'POST',
			data: data,
			url: this.getMetaData('formSubmitUrl'),
			context: this,
			dataType: 'html',
			success: function (data) {
				$(' .criteria', this.wrapper).hide();
				$('.summary', this.wrapper).show();
				this._handleAjaxResults(data);
			}
		});
	},

	_handleAjaxResults: function(data) {

		DeskPRO_Window.removePage(this);

		var page = DeskPRO_Window.createPageFragment(data);
		DeskPRO_Window.addListPage(page);
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.TicketSla = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.BasicTicketResults,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'ticket-sla';

		this.resultTypeName = 'sla';
		this.resultTypeId = 0;
	},

	initPage: function(el) {

		DeskPRO_Window.getMessageBroker().sendMessage('ticket-section.list-activated', { listType: 'sla', id: this.getMetaData('sla_id'), topGroupingOption: this.meta.topGroupingOption || null });
		this.resultTypeId = this.getMetaData('sla_id');
		this.parent(el);
	},

	activate: function() {
		if (this.getMetaData('sla_id')) {
			DeskPRO_Window.getMessageBroker().sendMessage('sla.view-activated', this.getMetaData('sla_id'));
		}
	},

	deactivate: function() {
		if (this.getMetaData('sla_id')) {
			DeskPRO_Window.getMessageBroker().sendMessage('sla.view-deactivated', this.getMetaData('sla_id'));
		}
	},

	updateSlaListForTicket: function(info) {
		if (!info.ticket_id || !info.sla_id) {
			return;
		}

		// run this for every SLA change, as a ticket may have multiple SLAs
		// and the general status could change
		// todo: in the future we could possibly resolve this without always
		// refreshing if we look at the list and only update if there's a ticket
		// with this SLA
		this.refreshSlaTicketList();
	},

	refreshSlaTicketList: function() {
		var self = this;

		if (this.isRefreshing) {
			return;
		}
		this.isRefreshing = true;

		setTimeout(function() {
			self.isRefreshing = false;
			DeskPRO_Window.loadListPane(self.meta.refreshUrl);
		}, 0);
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.RecycleBin = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'recyclebin';
		this.wrapper = null;
		this.contentWrapper = null;
		this.barWrapper = null;
		this.layout = null;
		this.overlay = null;
		this.appendUrl = null;
		this.actionsBarHelper = null;
		this.resultTypeName = 'basic';
		this.resultTypeId = 'general';
		this.changeManager = null;
		this.loadFirst = false;
	},

	initPage: function(el) {

		this.wrapper = el;

		var self = this;
		$('.type-list', el).each(function() {
			self.initTypeList($(this));
		});

		$('time.timeago', this.wrapper).timeago();

	},

	initTypeList: function(listWrap) {
		var type = listWrap.data('load-name');

		var self = this;
		$('.list-load-more', listWrap).on('click', function() {
			self.loadMore(type);
		});
	},

	loadMore: function(loadName) {
		var wrap = $('.' + loadName + '-list.type-list', this.wrapper);
		var table = $('table:first', wrap);
		var loadBtn  = $('.list-load-more', wrap);

		var lastTbody = $('tbody:last', table);
		var nextPage = parseInt(lastTbody.data('page')) + 1;

		$.ajax({
			url: BASE_URL + 'agent/recycle-bin/' + loadName + '/' + nextPage,
			dataType: 'json',
			success: function(data) {
				if (data.no_more_results) {
					loadBtn.hide();
				}

				if (data.count < 1) {
					return;
				}

				var rows = $(data.htmls);
				$('time.timeago', rows).timeago();

				table.append(rows);
			}
		});
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.KbPendingArticles = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.Basic,

	initPage: function(el) {
		var self = this;
		this.wrapper = el;

		this.actionsMenu = new DeskPRO.UI.Menu({
			menuElement: $('ul.actions-menu:first', this.wrapper),
			triggerElement: $('.perform-actions-trigger:first', this.wrapper),
			onItemClicked: function(info) {
				var ids = self.selectionBar.getCheckedValues();
				var els = [];

				var formData = [];
				Array.each(ids, function(id) {
					formData.push({
						name: 'ids[]',
						value: id
					});

					els.push($('article.pending-article-' + id + ':first', self.wrapper).get(0));
				});

				$(els).fadeOut();

				var action = $(info.itemEl).data('action');

				$.ajax({
					url: BASE_URL + 'agent/kb/pending-articles/mass-actions/' + action,
					data: formData,
					type: 'POST',
					dataType: 'json',
					error: function() {
						$(els).show();
					},
					success: function() {
						$(els).remove();
						DeskPRO_Window.util.modCountEl('#kb_pending_count', '-', els.length);
					}
				});
			}
		});
		this.ownObject(this.actionsMenu);

		this.selectionBar = new DeskPRO.Agent.PageHelper.SelectionBar(this, {
			onButtonClick: function(ev) {
				self.actionsMenu.open(ev);
			}/*,
			onCountChange: function(count) {
				var isOpen = self.actionsMenu.isOpen();

				if (count > 0 && !isOpen) {
					self.actionsMenu.open();
				} else if (count <= 0 && isOpen) {
					self.actionsMenu.close();
				}
			}*/
		});
		this.ownObject(this.selectionBar);

		DeskPRO_Window.getMessageBroker().addMessageListener('kb.pending_article_removed', function(data) {
			self.removeFromList(data.pending_article_id);
		});

		var newFormOverlay = new DeskPRO.UI.Overlay({
			contentElement: this.getEl('add_new_overlay'),
			zIndex: 'top'
		});

		$('.add-new-trigger', this.el).on('click', function() {
			newFormOverlay.open();
		});
		$('.save-new-trigger', this.getEl('add_new_overlay')).on('click', function() {
			self.saveNewPendingArticle();
			newFormOverlay.close();
		});

		$('section.pending-articles-list', this.wrapper).on('click', '.pending-delete', function(ev) {
			ev.stopPropagation();
			var row = $(this);
			var x = 0;
			while (!row.is('article')) {
				if (x++ > 10) return;
				row = row.parent();
			}

			row.slideUp('fast');

			var id = $('input.item-select', row).val();

			$.ajax({
				url: BASE_URL + 'agent/kb/pending-articles/' + id + '/remove',
				type: 'POST',
				dataType: 'json',
				error: function() {
					row.show();
				},
				success: function() {
					row.remove();
					DeskPRO_Window.util.modCountEl('#kb_pending_count', '-');
				}
			});
		});

		$('section.pending-articles-list', this.wrapper).on('click', '.pending-create', function(ev) {
			ev.stopPropagation();
			var row = $(this);
			var x = 0;
			while (!row.is('article')) {
				if (x++ > 10) return;
				row = row.parent();
			}

			var id = $('input.item-select', row).val();
			var ticketRoute = $('input.item-select', row).data('ticket-route');

			$.ajax({
				url: BASE_URL + 'agent/kb/pending-articles/' + id + '/info',
				type: 'POST',
				dataType: 'json',
				success: function(data) {
					if (ticketRoute) {
						DeskPRO_Window.runPageRoute(ticketRoute);
					}

					if (DeskPRO_Window.newArticleLoader) {
						DeskPRO_Window.newArticleLoader.open(function(page) {
							page.setPendingArticle(data);

							if (data.ticket_id) {
								var closeTicketId = data.ticket_id;
								page.addEvent('destroy', function() {
									Object.each(DeskPRO_Window.TabBar.getTabs(), function(tab, id) {
										if (tab.page && tab.page.meta.ticket_id == closeTicketId) {
											DeskPRO_Window.removePage(tab.page);
										}
									});
								});
							}
						});
					}
				}
			});
		});
	},

	saveNewPendingArticle: function() {
		var formWrap = this.getEl('add_new_overlay');
		var val = $('textarea', this.getEl('add_new_overlay')).val().trim();

		if (!val) {
			formWrap.slideUp();
			return;
		}

		var data = [];
		data.push({
			name: 'comment',
			value: val
		});

		$.ajax({
			url: BASE_URL + 'agent/kb/pending-articles/new',
			type: 'POST',
			data: data,
			context: this,
			dataType: 'json',
			success: function(info) {

				$('textarea:first', formWrap).val('');
				formWrap.slideUp();

				var addEl = $(info.row_html);
				$('section.pending-articles-list', this.wrapper).prepend(addEl);

				DeskPRO_Window.util.modCountEl('#kb_pending_count', '+');
			}
		});
	},

	removeFromList: function(id) {
		$('article.pending-article-' + id, this.wrapper).slideUp('fast');
		DeskPRO_Window.util.modCountEl('#kb_pending_count', '-');
		DeskPRO_Window.util.modCountEl(this.getEl('count'), '-');
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.KbValidatingArticles = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.Basic,

	initPage: function(el) {
		this.wrapper = el;

		var self = this;
		$('a.view-link.edit', this.wrapper).on('click', function(ev) {
			ev.preventDefault();
			self.loadPreviewEdit($(this).attr('href'));
		});

		$('a.view-link.article', this.wrapper).on('click', function(ev) {
			ev.preventDefault();
			self.loadPreviewArticle($(this).attr('href'));
		});
	},

	loadPreviewEdit: function(url) {

		var self = this;

		var overlay = new DeskPRO.UI.Overlay({
			destroyOnClose: true,
			contentMethod: 'ajax',
			contentAjax: {
				url: url,
				type: 'GET',
				context: this,
				dataType: 'html'
			},
			maxWidth: 500,
			maxHeight: 700,
			onContentSet: function(ev) {
				var contentEl = ev.contentEl;
				var overlay = ev.overlay;
				$('button.approve-trigger', contentEl).on('click', function(ev) {
					ev.preventDefault();
					self.approveEdit($('input.article_id', contentEl).val());
					overlay.closeOverlay();
				});
				$('button.disapprove-trigger', contentEl).on('click', function(ev) {
					ev.preventDefault();
					self.disapproveEdit($('input.article_id', contentEl).val());
					overlay.closeOverlay();
				});
			}
		});
		overlay.openOverlay();
	},

	approveEdit: function(article_id) {
		$.ajax({
			url: BASE_URL + 'agent/kb/validating-articles/validate/'+article_id+'.json',
			type: 'POST',
			context: this,
			dataType: 'json',
			success: function(info) {
				var article_id = info.article_id;
				$('.article-' + article_id, this.wrapper).remove();

				this.removeFromList(article_id, info.type);
			}
		});
	},

	disapproveEdit: function(article_id) {
		$.ajax({
			url: BASE_URL + 'agent/kb/validating-articles/disapprove/'+article_id+'.json',
			type: 'POST',
			context: this,
			dataType: 'json',
			success: function(info) {
				var article_id = info.article_id;
				$('.article-' + article_id, this.wrapper).remove();

				this.removeFromList(article_id, info.type);
			}
		});
	},

	removeFromList: function(article_id, type) {
		var count_total = $('.counter-total', this.wrapper);
		var count_type = $('.counter-' + type, this.wrapper);

		var total_int = parseInt(count_total.html());
		var type_int = parseInt(count_total.html());

		count_total.html(total_int - 1);
		count_type.html(type_int - 1);

		var wrap_type = $('.wrap-' + type, this.wrapper);
		if (!$('tr.article', wrap_type).length) {
			wrap_type.remove();
		}
	},

	loadPreviewArticle: function(url) {

		var self = this;

		var overlay = new DeskPRO.UI.Overlay({
			destroyOnClose: true,
			contentMethod: 'ajax',
			contentAjax: {
				url: url,
				type: 'GET',
				context: this,
				dataType: 'html'
			},
			maxWidth: 500,
			maxHeight: 700,
			onContentSet: function(ev) {
				var contentEl = ev.contentEl;
				var overlay = ev.overlay;
				$('button.approve-trigger', contentEl).on('click', function(ev) {
					ev.preventDefault();
					self.approveEdit($('input.article_id', contentEl).val());
					overlay.closeOverlay();
				});
				$('button.disapprove-trigger', contentEl).on('click', function(ev) {
					ev.preventDefault();
					self.disapproveEdit($('input.article_id', contentEl).val());
					overlay.closeOverlay();
				});
			}
		});
		overlay.openOverlay();
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.KbList = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.Basic,

	initPage: function(el) {
		var self = this;
		this.wrapper = el;

		this.displayOptions = new DeskPRO.Agent.PageHelper.DisplayOptions(this, {
			prefId: 'kb-filter',
			resultId: this.meta.resultId,
			refreshUrl: this.meta.refreshUrl,
			prefSaveResultId: '0'
		});
		this.ownObject(this.displayOptions);

		this.selectionBar = new DeskPRO.Agent.PageHelper.SelectionBar(this, {
			onCountChange: function(count) {
				var isOpen = self.massActions.isOpen();

				if (count > 0 && !isOpen) {
					self.massActions.open();
				} else if (count <= 0 && isOpen) {
					self.massActions.close();
				}
			}
		});
		this.ownObject(this.selectionBar);

		this.listWrapper = $('section.kb-simple-list', this.wrapper);

		DeskPRO_Window.getTabWatcher().addTabTypeWatcher('ticket', this);
		this.addEvent('watchedTabActivated', function(tab) {
			if (DeskPRO_Window.getTabWatcher().getTabType(tab) == 'ticket') {
				self.initVisibleTicket();
			}
		});
		this.addEvent('watchedTabDeactivated', function(tab) {
			if (DeskPRO_Window.getTabWatcher().getTabType(tab) == 'ticket') {
				self.removeVisibleTicket();
			}
		});

		// Or if we're already viewing a tab ticket...c
		if (DeskPRO_Window.getTabWatcher().isTabTypeActive('ticket')) {
			self.initVisibleTicket();
		}

		$('section.kb-simple-list', this.wrapper)
			.on('click', '.kb-insert-link', function(ev) { ev.stopPropagation(); self.insertIntoTicket($(this).data('article-id'), 'link') })
			.on('click', '.kb-insert-content', function(ev) { ev.stopPropagation(); self.insertIntoTicket($(this).data('article-id'), 'content') })

		this.relatedContentList = new DeskPRO.Agent.PageHelper.RelatedContentList(this, {
			contentListEl: this.listWrapper
		});
		this.ownObject(this.relatedContentList);

		this.sendContentLink = new DeskPRO.Agent.PageHelper.SendContentLink(this, {
			contentListEl: this.listWrapper
		});
		this.ownObject(this.sendContentLink);

		// Sorting options
		var sortMenuBtn = $('.order-by-menu-trigger', this.wrapper).first();
		this.sortingMenu = new DeskPRO.UI.Menu({
			triggerElement: sortMenuBtn,
			menuElement: $('.order-by-menu', this.wrapper).first(),
			onItemClicked: function(info) {
				var item = $(info.itemEl);

				var prop = item.data('order-by')
				var label = item.text().trim();

				// Change the displayed label for some visual feedback
				$('.label', sortMenuBtn).text(label);

				var disOptWrap = self.displayOptions.getWrapperElement();
				var sel = $('select.sel-order-by', disOptWrap);
				$('option', sel).prop('selected', false);
				$('option.' + prop, sel).prop('selected', true);

				self.displayOptions.saveAndRefresh();
			}
		});
		this.ownObject(this.sortingMenu);

		this.enableHighlightOpenRows('article', 'article_id', 'article.article-');

		this.listNav = new DeskPRO.Agent.PageHelper.ListNav(this);

		this.massActions = new DeskPRO.Agent.PageHelper.MassActions(this, {
			isListView: false,
			applyAction: (this.massApplyAction).bind(this)
		});

		DeskPRO.ElementHandler_Exec($('#kb-mass-action-overlay'));

		// Cat editor
		this._initCatEditor();
	},

	massApplyAction: function(wrapper, formData) {
		var data = formData,
			myFormData = $('input, select',wrapper).serializeArray();

		$(myFormData).each(function(index, param) {
			data[param.name] = param.value;
		});

		$.ajax({
			type: 'POST',
			url: BASE_URL + "agent/kb/article/ajax-mass-save",
			'data': data,
			'dataType': 'json',
			success: (this.actionAppliedCallback).bind(this)
		});
	},

	actionAppliedCallback: function(data) {
		if(data && data.success) {
			var category = data.category;

			var section = DeskPRO_Window.sections.publish_section;
			DeskPRO_Window.getSectionData('publish_section', function(data) {
				(section._initSection.bind(section))(data);
				$('#publish_outline_articlescat_list .kb-cat-' + category + ' .is-nav-item').click();
			});
		}

		if(data.error) {
			DeskPRO_Window.showAlert(data.error);
		}
	},

	initVisibleTicket: function() {
		this.listWrapper.addClass('with-visible-ticket');
	},

	removeVisibleTicket: function() {
		this.listWrapper.removeClass('with-visible-ticket');
	},

	insertIntoTicket: function(article_id, action) {

		var ticketTab = DeskPRO_Window.getTabWatcher().getActiveTabIfType('ticket');
		if (!ticketTab) {
			return;
		}

		var ticketPage = ticketTab.page;

		$.ajax({
			url: BASE_URL + 'agent/kb/article/'+article_id+'/info',
			type: 'GET',
			dataType: 'json',
			success: function(data) {
				if (action == 'content') {
					ticketPage.appendToMessage(data.content, true);
				} else {
					ticketPage.appendToMessage(data.permalink);
				}
			}
		});
	},

	_initCatEditor: function() {
		var self = this;
		var catEl = this.getEl('tab_cat');
		if (!catEl[0]) {
			return;
		}

		var tree = this.getEl('cattree');
		var treeData = tree.data('treedata');
		var treeSave = this.getEl('cattree_struct');
		tree.tree({
			data: treeData,
			dragAndDrop: true
		});
		tree.bind('tree.move', function(event) {
			event.move_info.do_move();
			treeSave.val(tree.tree('toJson'));
		});

		this.getEl('catfoot').find('.cat-save-trigger').on('click', function(ev){
			Orb.cancelEvent(ev);

			var postData = catEl.find('input').serializeArray();

			self.getEl('catfoot').addClass('dp-loading-on');
			$.ajax({
				url: $(this).data('save-url'),
				data: postData,
				type: 'POST',
				dataType: 'json',
				complete: function() {
					self.getEl('catfoot').removeClass('dp-loading-on');
				},
				success: function() {
					DeskPRO_Window.sections.publish_section.reload();
				}
			});
		});

		var delCat = this.getEl('del_cat');
		delCat.find('.cat-del-trigger').on('click', function(ev) {
			Orb.cancelEvent(ev);
			delCat.addClass('dp-loading-on');

			$.ajax({
				url: $(this).data('save-url'),
				type: 'POST',
				dataType: 'json',
				complete: function() {
					delCat.removeClass('dp-loading-on');
				},
				success: function(ret) {
					if (ret.error_code && ret.error_code == 'not_empty') {
						DeskPRO_Window.showAlert('The category could not be deleted because it is not empty.');
						return;
					}

					DeskPRO_Window.sections.publish_section.reload();
					DeskPRO_Window.runPageRoute('listpane:' + BASE_URL + 'agent/kb/list/0');
				}
			});
		});
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.AgentChatHistory = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.Basic,

	initializeProperties: function() {
		this.parent();
		this.wrapper = null;
		this.contentWrapper = null;
	},

	initPage: function(el) {

		DeskPRO_Window.getMessageBroker().sendMessage('agentchat-section.list-activated', { id: this.meta.agentId });

		this.wrapper = $(el);
		this.contentWrapper = $('div.content:first', this.wrapper);

		this.enableHighlightOpenRows('agentchat', 'conversation_id', '.row-item.convo-');

		if (this.getMetaData('noResults')) {
			this.noMoreResults = true;
			$('.no-more-results', this.contentWrapper).show();
		}
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.AgentTeamChatHistory = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.Basic,

	initializeProperties: function() {
		this.parent();
		this.wrapper = null;
		this.contentWrapper = null;
	},

	initPage: function(el) {

		DeskPRO_Window.getMessageBroker().sendMessage('agentchat-section.team-list-activated', { id: this.meta.agentTeamId });

		this.wrapper = $(el);
		this.contentWrapper = $('div.content:first', this.wrapper);

		this.enableHighlightOpenRows('agentchat', 'conversation_id', '.row-item.convo-');

		if (this.getMetaData('noResults')) {
			this.noMoreResults = true;
			$('.no-more-results', this.contentWrapper).show();
		}
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.OpenChats = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.Basic,


	initPage: function(el) {
		var self = this;
		this.el = el;

		var agentId = this.meta.agent_id;

		var removeConvo = function(convoId) {
			var row = $('article.convo-' + convoId);
			row.slideUp('fast', function() {
				row.remove();
			});
		};
		var addConvo = function(convoId) {
			// TODO later prepend the row like in tickets,
			// for now this is easiest
			DeskPRO_Window.loadRoute(self.meta.routeData);
		};

		DeskPRO_Window.getMessageBroker().addMessageListener('chat.new', function(data) {
			if (data.agent_id && data.agent_id == agentId) {
				addConvo(data.conversation_id);
			}
		}, this);
		DeskPRO_Window.getMessageBroker().addMessageListener('chat.reassigned', function(data) {
			if (data.old_agent_id && data.old_agent_id == agentId) {
				removeConvo(data.conversation_id);
			} else if (data.agent_id && data.agent_id == agentId) {
				addConvo(data.conversation_id);
			}
		}, this);
		DeskPRO_Window.getMessageBroker().addMessageListener('chat.unassigned', function(data) {
			if (data.old_agent_id && data.old_agent_id == agentId) {
				removeConvo(data.conversation_id);
			}
		}, this);
		DeskPRO_Window.getMessageBroker().addMessageListener('chat.ended', function(data) {
			if (data.agent_id && data.agent_id == agentId) {
				removeConvo(data.conversation_id);
			}
		}, this);

		this.enableHighlightOpenRows('userchat', 'conversation_id', '.row-item.convo-');
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.UserChatFilter = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'chat-list';
		this.wrapper = null;
		this.contentWrapper = null;
		this.overlay = null;
		this.appendUrl = null;
		this.actionsBarHelper = null;
		this.resultTypeName = 'filter';
		this.resultTypeId = 0;
	},

	initPage: function(el) {
		var self = this;
		this.el = el;

		this.enableHighlightOpenRows('userchat', 'conversation_id', '.row-item.convo-');

		this.wrapper = $(el);
		this.contentWrapper = $('div.content:first', this.wrapper);

		this.resultTypeId = this.meta.cache_id || 0;

		if (this.getMetaData('noResults')) {
			this.noMoreResults = true;
			$('.no-more-results', this.contentWrapper).show();
		}

		var opt = {
			resultIds: this.meta.resultIds,
			perPage: this.meta.perPage || 50
		};
		if (this.meta.viewType && this.meta.viewType == 'list') {
			opt.resultRowSelector = 'tr.row-item';
			opt.resultsContainer = $('.table-result-list table', el);
			opt.navEl = $('.bottom-action-bar', el);
		}

		this.listNav = new DeskPRO.Agent.PageHelper.ListNav(this);
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.FeedbackFilter = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.Basic,

	initializeProperties: function() {
		this.parent();
		this.wrapper = null;
		this.filterSearchForm = null;
	},

	initPage: function(el) {
		var self = this;
		this.wrapper = el;

		this.displayOptions = new DeskPRO.Agent.PageHelper.DisplayOptions(this, {
			prefId: 'feedback-filter',
			resultId: this.meta.resultId,
			refreshUrl: this.meta.refreshUrl,
			prefSaveResultId: '0'
		});
		this.ownObject(this.displayOptions);

		this.selectionBar = new DeskPRO.Agent.PageHelper.SelectionBar(this, {
			/*onCountChange: function(count) {
				var isOpen = self.massActionsMenu.isOpen();

				if (count > 0 && !isOpen) {
					self.massActionsMenu.open();
				} else if (count <= 0 && isOpen) {
					self.massActionsMenu.close();
				}
			}*/
		});
		this.ownObject(this.selectionBar);

		// Sorting options
		var sortMenuBtn = $('.order-by-menu-trigger', this.wrapper).first();
		this.sortingMenu = new DeskPRO.UI.Menu({
			triggerElement: sortMenuBtn,
			menuElement: $('.order-by-menu', this.wrapper).first(),
			onItemClicked: function(info) {
				var item = $(info.itemEl);

				var prop = item.data('order-by')
				var label = item.text().trim();

				// Change the displayed label for some visual feedback
				$('.label', sortMenuBtn).text(label);

				var disOptWrap = self.displayOptions.getWrapperElement();
				var sel = $('select.sel-order-by', disOptWrap);
				$('option', sel).prop('selected', false);
				$('option.' + prop, sel).prop('selected', true);

				self.displayOptions.saveAndRefresh();
			}
		});
		this.ownObject(this.sortingMenu);

		this.listWrapper = $('section.feedback-simple-list', this.wrapper);

		this.relatedContentList = new DeskPRO.Agent.PageHelper.RelatedContentList(this, {
			contentListEl: this.listWrapper
		});
		this.ownObject(this.relatedContentList);

		this.massActionsMenu = new DeskPRO.UI.Menu({
			triggerElement: $('.perform-actions-trigger:first', this.wrapper),
			menuElement: $('.actions-menu:first', this.wrapper),
			onItemClicked: function(info) {
				var itemEl = $(info.itemEl);
				var menuEl = itemEl.parent();
				var menuType = menuEl.data('menu-type');

				var postData = self.selectionBar.getCheckedFormValues('ids[]');
				var removeFromList = false;
				var action = '';

				switch (menuType) {
					case 'feedback-status-menu':

						action = 'set-status';

						postData.push({
							name: 'status',
							value: itemEl.data('option-value')
						});
						break;

					case 'feedback-category-menu':

						action = 'set-category';

						postData.push({
							name: 'category_id',
							value: itemEl.data('category-id')
						});

						break;

					case 'feedback-massactions-menu':

						switch (itemEl.data('action')) {
							case 'delete':
								action = 'set-status';
								postData.push({
									name: 'status',
									value: 'hidden.deleted'
								});

								removeFromList = true;

								break;

							case 'spam':
								action = 'set-status';
								postData.push({
									name: 'status',
									value: 'hidden.spam'
								});

								removeFromList = true;

								break;
						}

						break;

					default:
						return;
						break;
				}

				$.ajax({
					url: BASE_URL + 'agent/feedback/filter/mass-actions/' + action,
					data: postData,
					type: 'POST',
					dataType: 'json',
					success: function(data) {
						if (removeFromList) {
							self.selectionBar.getChecked().parent().fadeOut('fast');
						}

						self.selectionBar.checkNone();

						DeskPRO_Window.runPageRoute('listpane:' + self.meta.routeUrl);
						DeskPRO_Window.sections.feedback_section.reload();
					}
				});
			}
		});
		this.ownObject(this.massActionsMenu);

		var opt = {
			resultIds: this.meta.resultIds,
			perPage: this.meta.perPage || 50,
			refreshMode: true,
			currentPage: this.meta.currentPage
		};
		this.resultsHelper = new DeskPRO.Agent.PageHelper.Results(this, opt);
		this.ownObject(this.resultsHelper);

		this.enableHighlightOpenRows('feedback', 'feedback_id', 'article.feedback-');

		this.listNav = new DeskPRO.Agent.PageHelper.ListNav(this);
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.NewCustomFilter = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.Basic,

	initializeProperties: function() {
		this.parent();
		this.wrapper = null;
		this.filterSearchForm = null;
	},

	initPage: function(el) {
		this.wrapper = el;

		this.topSection = $('.list-top-area', this.wrapper);

		var criteriaList  = $('.search-form', this.topSection);
		var criteriaTerms = $('.search-builder-tpl', this.topSection);

		var editor = new DeskPRO.Form.RuleBuilder(criteriaTerms);
		$('.add-term', criteriaList).data('add-count', 0).on('click', function() {
			var count = parseInt($(this).data('add-count'));
			var basename = 'terms['+count+']';

			$(this).data('add-count', count+1);

			editor.addNewRow($('.search-terms', criteriaList), basename);
		});

		var searchDataEl = $('.search-form-data:first', this.topSection);
		if (searchDataEl.length) {
			var searchData = searchDataEl.get(0).innerHTML;
			searchData = $.parseJSON(searchData);

			if (searchData.terms) {
				Array.each(searchData.terms, function(info, x) {
					var basename = 'terms[initial_' + x + ']';
					editor.addNewRow($('.search-terms', criteriaList), basename, {
						type: info.type,
						op: info.op,
						options: info.options
					});
				});
			}

			if (searchData.order_by) {
				$('[name="order_by"]', this.topSection).val(searchData.order_by);
			}

			searchDataEl.remove();
		}

		var form = $('form.ticket-search-form', this.topSection);
		form.on('submit', function(ev) {
			ev.preventDefault();

			var url = form.attr('action');
			var data = form.serializeArray();

			DeskPRO_Window.loadListPane(url, { postData: data });
		});
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.NewsList = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.Basic,

	initializeProperties: function() {
		this.parent();
		this.wrapper = null;
	},

	initPage: function(el) {
		this.wrapper = el;

		this.displayOptions = new DeskPRO.Agent.PageHelper.DisplayOptions(this, {
			prefId: 'news-filter',
			resultId: this.meta.resultId,
			refreshUrl: this.meta.refreshUrl,
			prefSaveResultId: '0'
		});
		this.ownObject(this.displayOptions);

		this.selectionBar = new DeskPRO.Agent.PageHelper.SelectionBar(this, {

		});
		this.ownObject(this.selectionBar);

		this.listWrapper = $('section.news-simple-list', this.wrapper);

		this.relatedContentList = new DeskPRO.Agent.PageHelper.RelatedContentList(this, {
			contentListEl: this.listWrapper
		});
		this.ownObject(this.relatedContentList);

		this.enableHighlightOpenRows('news', 'news_id', 'article.news-');

		this.listNav = new DeskPRO.Agent.PageHelper.ListNav(this);

		// Cat editor
		this._initCatEditor();
	},

	_initCatEditor: function() {
		var self = this;
		var catEl = this.getEl('tab_cat');
		if (!catEl[0]) {
			return;
		}

		var tree = this.getEl('cattree');
		var treeData = tree.data('treedata');
		var treeSave = this.getEl('cattree_struct');
		tree.tree({
			data: treeData,
			dragAndDrop: true
		});
		tree.bind('tree.move', function(event) {
			event.move_info.do_move();
			treeSave.val(tree.tree('toJson'));
		});

		this.getEl('catfoot').find('.cat-save-trigger').on('click', function(ev){
			Orb.cancelEvent(ev);

			var postData = catEl.find('input').serializeArray();

			self.getEl('catfoot').addClass('dp-loading-on');
			$.ajax({
				url: $(this).data('save-url'),
				data: postData,
				type: 'POST',
				dataType: 'json',
				complete: function() {
					self.getEl('catfoot').removeClass('dp-loading-on');
				},
				success: function() {
					DeskPRO_Window.sections.publish_section.reload();
				}
			});
		});

		var delCat = this.getEl('del_cat');
		delCat.find('.cat-del-trigger').on('click', function(ev) {
			Orb.cancelEvent(ev);
			delCat.addClass('dp-loading-on');

			$.ajax({
				url: $(this).data('save-url'),
				type: 'POST',
				dataType: 'json',
				complete: function() {
					delCat.removeClass('dp-loading-on');
				},
				success: function(ret) {
					if (ret.error_code && ret.error_code == 'not_empty') {
						DeskPRO_Window.showAlert('The category could not be deleted because it is not empty.');
						return;
					}

					DeskPRO_Window.sections.publish_section.reload();
					DeskPRO_Window.runPageRoute('listpane:' + BASE_URL + 'agent/kb/list/0');
				}
			});
		});
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.DownloadList = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.Basic,

	initPage: function(el) {
		var self = this;
		this.wrapper = el;

		this.displayOptions = new DeskPRO.Agent.PageHelper.DisplayOptions(this, {
			prefId: 'download-filter',
			resultId: this.meta.resultId,
			refreshUrl: this.meta.refreshUrl,
			prefSaveResultId: '0'
		});
		this.ownObject(this.displayOptions);

		this.selectionBar = new DeskPRO.Agent.PageHelper.SelectionBar(this, {});
		this.ownObject(this.selectionBar);

		this.listWrapper = $('section.downloads-simple-list', this.wrapper)
			.on('click', '.dl-insert-link', function() { self.insertIntoTicket($(this).data('download-id'), 'link') })
			.on('click', '.dl-insert-attach', function() { self.insertIntoTicket($(this).data('download-id'), 'attach') });

		DeskPRO_Window.getTabWatcher().addTabTypeWatcher('ticket', this);
		this.addEvent('watchedTabActivated', function(tab) {
			if (DeskPRO_Window.getTabWatcher().getTabType(tab) == 'ticket') {
				self.initVisibleTicket();
			}
		});
		this.addEvent('watchedTabDeactivated', function(tab) {
			if (DeskPRO_Window.getTabWatcher().getTabType(tab) == 'ticket') {
				self.removeVisibleTicket();
			}
		});

		// Or if we're already viewing a tab ticket...
		if (DeskPRO_Window.getTabWatcher().isTabTypeActive('ticket')) {
			self.initVisibleTicket();
		}

		this.relatedContentList = new DeskPRO.Agent.PageHelper.RelatedContentList(this, {
			contentListEl: this.listWrapper
		});
		this.ownObject(this.relatedContentList);

		// Sorting options
		var sortMenuBtn = $('.order-by-menu-trigger', this.wrapper).first();
		this.sortingMenu = new DeskPRO.UI.Menu({
			triggerElement: sortMenuBtn,
			menuElement: $('.order-by-menu', this.wrapper).first(),
			onItemClicked: function(info) {
				var item = $(info.itemEl);

				var prop = item.data('order-by')
				var label = item.text().trim();

				// Change the displayed label for some visual feedback
				$('.label', sortMenuBtn).text(label);

				var disOptWrap = self.displayOptions.getWrapperElement();
				var sel = $('select.sel-order-by', disOptWrap);
				$('option', sel).prop('selected', false);
				$('option.' + prop, sel).prop('selected', true);

				self.displayOptions.saveAndRefresh();
			}
		});
		this.ownObject(this.sortingMenu);

		this.enableHighlightOpenRows('download', 'download_id', 'article.download-');
		this.listNav = new DeskPRO.Agent.PageHelper.ListNav(this);

		// Cat editor
		this._initCatEditor();
	},

	initVisibleTicket: function() {
		this.listWrapper.addClass('with-visible-ticket');
	},

	removeVisibleTicket: function() {
		this.listWrapper.removeClass('with-visible-ticket');
	},

	insertIntoTicket: function(download_id, action) {

		var ticketTab = DeskPRO_Window.getTabWatcher().getActiveTabIfType('ticket');
		if (!ticketTab) {
			return;
		}

		var ticketPage = ticketTab.page;

		$.ajax({
			url: BASE_URL + 'agent/downloads/file/'+download_id+'/info',
			type: 'GET',
			dataType: 'json',
			success: function(data) {
				if (action == 'attach') {
					ticketPage.addAttachToList(data);
				} else {
					ticketPage.appendToMessage(data.permalink);
				}
			}
		});
	},

	_initCatEditor: function() {
		var self = this;
		var catEl = this.getEl('tab_cat');
		if (!catEl[0]) {
			return;
		}

		var tree = this.getEl('cattree');
		var treeData = tree.data('treedata');
		var treeSave = this.getEl('cattree_struct');
		tree.tree({
			data: treeData,
			dragAndDrop: true
		});
		tree.bind('tree.move', function(event) {
			event.move_info.do_move();
			treeSave.val(tree.tree('toJson'));
		});

		this.getEl('catfoot').find('.cat-save-trigger').on('click', function(ev){
			Orb.cancelEvent(ev);

			var postData = catEl.find('input').serializeArray();

			self.getEl('catfoot').addClass('dp-loading-on');
			$.ajax({
				url: $(this).data('save-url'),
				data: postData,
				type: 'POST',
				dataType: 'json',
				complete: function() {
					self.getEl('catfoot').removeClass('dp-loading-on');
				},
				success: function() {
					DeskPRO_Window.sections.publish_section.reload();
				}
			});
		});

		var delCat = this.getEl('del_cat');
		delCat.find('.cat-del-trigger').on('click', function(ev) {
			Orb.cancelEvent(ev);
			delCat.addClass('dp-loading-on');

			$.ajax({
				url: $(this).data('save-url'),
				type: 'POST',
				dataType: 'json',
				complete: function() {
					delCat.removeClass('dp-loading-on');
				},
				success: function(ret) {
					if (ret.error_code && ret.error_code == 'not_empty') {
						DeskPRO_Window.showAlert('The category could not be deleted because it is not empty.');
						return;
					}

					DeskPRO_Window.sections.publish_section.reload();
					DeskPRO_Window.runPageRoute('listpane:' + BASE_URL + 'agent/downloads/list/0');
				}
			});
		});
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.PublishListComments = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.Basic,

	initPage: function(el) {
		var self = this;
		this.wrapper = el;

		var btn  = this.wrapper.find('.list-selection-bar .perform-actions-trigger');
		var load = this.wrapper.find('.list-selection-bar .ajax-loading');

		this.actionsMenu = new DeskPRO.UI.Menu({
			menuElement: $('ul.actions-menu:first', this.wrapper),
			triggerElement: $('.perform-actions-trigger:first', this.wrapper),
			onItemClicked: function(info) {
				var data = [];
				var lines = [];
				$('input.item-select:checked', this.wrapper).each(function() {
					lines.push($(this).parent().get(0));
					var typename = $(this).data('content-type');
					var id = $(this).data('comment-id');

					data.push({
						name: 'content[' + typename + '][]',
						value: id
					});
				});

				if (!data.length) {
					return;
				}

				btn.hide();
				load.show();

				var action = $(info.itemEl).data('action');

				$.ajax({
					url: BASE_URL + 'agent/publish/comments/validating-mass-actions/' + action,
					data: data,
					type: 'POST',
					dataType: 'json',
					complete: function() {
						load.hide();
						btn.show();
					},
					success: function() {
						// Reload self
						DeskPRO_Window.runPageRoute('listpane:' + BASE_URL + 'agent/publish/comments/list/' + self.meta.viewType)

						if (DeskPRO_Window.sections.publish_section) {
							DeskPRO_Window.sections.publish_section.reload();
						}
					}
				});
			}
		});
		this.ownObject(this.actionsMenu);

		this.selectionBar = new DeskPRO.Agent.PageHelper.SelectionBar(this, {
			onButtonClick: function(ev) {
				self.actionsMenu.open(ev);
			}/*,
			onCountChange: function(count) {
				var isOpen = self.actionsMenu.isOpen();

				if (count > 0 && !isOpen) {
					self.actionsMenu.open();
				} else if (count <= 0 && isOpen) {
					self.actionsMenu.close();
				}
			}*/
		});
		this.ownObject(this.selectionBar);

		var findRowInfo = function(el) {

			var row = $(el);

			var editRow = $(el).closest('div.edit-comment');
			if (editRow.length) {
				var row = $('article.' + editRow.data('content-type') + '-' + editRow.data('comment-id'));
				return findRowInfo(row);
			}

			row = row.closest('article');

			var check = $('input.item-select', row);
			if (!check.length) {
				return;
			}

			var info = {
				row: row,
				contentType: $(check).data('content-type'),
				commentId: $(check).data('comment-id')
			};

			var editRow = $('div.edit-' + info.contentType + '-' + info.commentId, self.wrapper);
			DP.console.log(editRow);
			info.editRow = editRow;

			return info;
		};

		this.wrapper.on('click', '.validate-approve', function(ev) {
			ev.stopPropagation();

			var info = findRowInfo(this);
			self.approveComment(info.contentType, info.commentId, info.row);
		});
		this.wrapper.on('click', '.validate-delete', function(ev) {
			ev.stopPropagation();

			var info = findRowInfo(this);
			self.deleteComment(info.contentType, info.commentId, info.row);
		});

		this.wrapper.on('click', '.validate-edit', function(ev) {
			ev.stopPropagation();

			var info = findRowInfo(this);
			self.editComment(info.contentType, info.commentId, info.row, info);
		});

		this.wrapper.on('click', '.comment-editsave-trigger', function(ev) {
			var info = findRowInfo(this);

			var commentText = $('textarea', info.editRow).val().trim();
			if (!commentText.length) {
				info.row.show();
				info.editRow.hide();
			}

			$.ajax({
				url: BASE_URL + 'agent/publish/comments/save-comment/'+info.contentType+'/'+info.commentId,
				type: 'POST',
				data: {
					comment: commentText
				},
				dataType: 'json',
				success: function(data) {
					var rendered = $('.rendered', info.row);
					rendered.html(data.comment_html);

					info.row.show();
					info.editRow.hide();
				}
			});
		});

		this.wrapper.on('click', '.comment-editcancel-trigger', function(ev) {
			var info = findRowInfo(this);
			var editEl = info.editRow;

			info.row.show();
			editEl.hide();
		});

		this.wrapper.on('click', '.validate-create-ticket', function(ev) {
			var info = findRowInfo(this);
			$.ajax({
				url: BASE_URL + 'agent/publish/comments/new-ticket-info/' + info.contentType + '/' + info.commentId + '.json',
				type: 'GET',
				dataType: 'json',
				success: function(data) {
					DeskPRO_Window.newTicketLoader.open(function(page) {
						page.setNewByComment(data);
					});
				}
			});
		});

		DeskPRO_Window.getMessageBroker().addMessageListener('agent-ui.comment-remove', function(data) {
			$('article.' + data.comment_type + '-' + data.comment_id, this.wrapper).fadeOut();
		});
	},

	deleteComment: function(typename, commentId, el) {
		if (!el) {
			el = $('article.' + typename + '-' + commentId, this.wrapper);
		}
		el.fadeOut();

		this.updateCount('sub');

		$.ajax({
			url: BASE_URL + 'agent/publish/comments/delete/'+typename+'/'+commentId,
			type: 'POST',
			context: this,
			dataType: 'json',
			error: function() {
				this.updateCount('add');
				el.fadeIn();
			},
			success: function(data) {
				el.remove();

				if (DeskPRO_Window.sections.publish_section) {
					DeskPRO_Window.sections.publish_section.modCommentCount(typename, '-');
				}
			}
		});
	},

	approveComment: function(typename, commentId, el) {
		if (!el) {
			el = $('article.' + typename + '-' + commentId, this.wrapper);
		}

		el.fadeOut();

		this.updateCount('sub');

		$.ajax({
			url: BASE_URL + 'agent/publish/comments/approve/'+typename+'/'+commentId,
			type: 'POST',
			context: this,
			dataType: 'json',
			error: function() {
				this.updateCount('add');
				if (el) {
					el.fadeIn();
				}
			},
			success: function(data) {
				if (el) {
					el.remove();
				}
			}
		});
	},

	editComment: function(typename, commentId, el, info) {

		el.hide();
		var editEl = info.editRow;

		editEl.show();
	},

	updateCount: function(action, num) {
		var countEl = $('#publish_validating_comments_count');
		var count = parseInt(countEl.text());

		num = num || 1;

		if (action == 'add') {
			count += num;
		} else {
			count -= num;
		}

		if (count < 0) {
			count = 0;
		}

		var countEl = $('#publish_validating_comments_count').text(count);

		DeskPRO_Window.sections.publish_section.recountBadge();
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.PublishValidatingComments = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.Basic,

	initPage: function(el) {
		var self = this;
		this.wrapper = el;

		var btn  = this.wrapper.find('.list-selection-bar .perform-actions-trigger');
		var load = this.wrapper.find('.list-selection-bar .ajax-loading');

		this.actionsMenu = new DeskPRO.UI.Menu({
			menuElement: $('ul.actions-menu:first', this.wrapper),
			triggerElement: $('.perform-actions-trigger:first', this.wrapper),
			onItemClicked: function(info) {
				var data = [];
				var lines = [];
				$('input.item-select:checked', this.wrapper).each(function() {
					lines.push($(this).parent().get(0));
					var typename = $(this).data('content-type');
					var id = $(this).data('comment-id');

					data.push({
						name: 'content[' + typename + '][]',
						value: id
					});
				});

				if (!data.length) {
					return;
				}

				btn.hide();
				load.show();

				var action = $(info.itemEl).data('action');

				$.ajax({
					url: BASE_URL + 'agent/publish/comments/validating-mass-actions/' + action,
					data: data,
					type: 'POST',
					dataType: 'json',
					complete: function() {
						load.hide();
						btn.show();
					},
					success: function() {
						self.selectionBar.checkNone();
						self.updateCount('sub', lines.length);
						$(lines).fadeOut();
					}
				});
			}
		});
		this.ownObject(this.actionsMenu);

		this.selectionBar = new DeskPRO.Agent.PageHelper.SelectionBar(this, {
			onButtonClick: function(ev) {
				self.actionsMenu.open(ev);
			}/*,
			onCountChange: function(count) {
				var isOpen = self.actionsMenu.isOpen();

				if (count > 0 && !isOpen) {
					self.actionsMenu.open();
				} else if (count <= 0 && isOpen) {
					self.actionsMenu.close();
				}
			}*/
		});
		this.ownObject(this.selectionBar);

		var findRowInfo = function(el) {

			var row = $(el);

			var editRow = $(el).closest('div.edit-comment');
			if (editRow.length) {
				var row = $('article.' + editRow.data('content-type') + '-' + editRow.data('comment-id'));
				return findRowInfo(row);
			}

			row = row.closest('article');

			var check = $('input.item-select', row);
			if (!check.length) {
				return;
			}

			var info = {
				row: row,
				contentType: $(check).data('content-type'),
				commentId: $(check).data('comment-id')
			};

			var editRow = $('div.edit-' + info.contentType + '-' + info.commentId, self.wrapper);
			DP.console.log(editRow);
			info.editRow = editRow;

			return info;
		};

		this.wrapper.on('click', '.validate-approve', function(ev) {
			ev.stopPropagation();

			var info = findRowInfo(this);
			self.approveComment(info.contentType, info.commentId, info.row);
		});
		this.wrapper.on('click', '.validate-delete', function(ev) {
			ev.stopPropagation();

			var info = findRowInfo(this);
			self.deleteComment(info.contentType, info.commentId, info.row);
		});

		this.wrapper.on('click', '.validate-edit', function(ev) {
			ev.stopPropagation();

			var info = findRowInfo(this);
			self.editComment(info.contentType, info.commentId, info.row, info);
		});

		this.wrapper.on('click', '.comment-editsave-trigger', function(ev) {
			var info = findRowInfo(this);

			var commentText = $('textarea', info.editRow).val().trim();
			if (!commentText.length) {
				info.row.show();
				info.editRow.hide();
			}

			$.ajax({
				url: BASE_URL + 'agent/publish/comments/save-comment/'+info.contentType+'/'+info.commentId,
				type: 'POST',
				data: {
					comment: commentText
				},
				dataType: 'json',
				success: function(data) {
					var rendered = $('.rendered', info.row);
					rendered.html(data.comment_html);

					info.row.show();
					info.editRow.hide();
				}
			});
		});

		this.wrapper.on('click', '.comment-editcancel-trigger', function(ev) {
			var info = findRowInfo(this);
			var editEl = info.editRow;

			info.row.show();
			editEl.hide();
		});

		this.wrapper.on('click', '.validate-create-ticket', function(ev) {
			var info = findRowInfo(this);
			$.ajax({
				url: BASE_URL + 'agent/publish/comments/new-ticket-info/' + info.contentType + '/' + info.commentId + '.json',
				type: 'GET',
				dataType: 'json',
				success: function(data) {
					DeskPRO_Window.newTicketLoader.open(function(page) {
						page.setNewByComment(data);
					});
				}
			});
		});

		DeskPRO_Window.getMessageBroker().addMessageListener('agent-ui.comment-remove', function(data) {
			$('article.' + data.comment_type + '-' + data.comment_id, this.wrapper).fadeOut();
		});
	},

	deleteComment: function(typename, commentId, el) {
		if (!el) {
			el = $('article.' + typename + '-' + commentId, this.wrapper);
		}
		el.fadeOut();

		this.updateCount('sub');

		$.ajax({
			url: BASE_URL + 'agent/publish/comments/delete/'+typename+'/'+commentId,
			type: 'POST',
			context: this,
			dataType: 'json',
			error: function() {
				this.updateCount('add');
				el.fadeIn();
			},
			success: function(data) {
				el.remove();

				if (DeskPRO_Window.sections.publish_section) {
					DeskPRO_Window.sections.publish_section.modCommentCount(typename, '-');
				}
			}
		});
	},

	approveComment: function(typename, commentId, el) {
		if (!el) {
			el = $('article.' + typename + '-' + commentId, this.wrapper);
		}

		el.fadeOut();

		this.updateCount('sub');

		$.ajax({
			url: BASE_URL + 'agent/publish/comments/approve/'+typename+'/'+commentId,
			type: 'POST',
			context: this,
			dataType: 'json',
			error: function() {
				this.updateCount('add');
				if (el) {
					el.fadeIn();
				}
			},
			success: function(data) {
				if (el) {
					el.remove();
				}
			}
		});
	},

	editComment: function(typename, commentId, el, info) {

		el.hide();
		var editEl = info.editRow;

		editEl.show();
	},

	updateCount: function(action, num) {
		var countEl = $('#publish_validating_comments_count');
		var count = parseInt(countEl.text());

		num = num || 1;

		if (action == 'add') {
			count += num;
		} else {
			count -= num;
		}

		if (count < 0) {
			count = 0;
		}

		var countEl = $('#publish_validating_comments_count').text(count);

		DeskPRO_Window.sections.publish_section.recountBadge();
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.PublishValidatingContent = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.Basic,

	initPage: function(el) {
		var self = this;
		this.wrapper = el;

		DeskPRO_Window.getMessageBroker().addMessageListener('publish.validating.list-remove', function (info) {
			var el = $('article.' + info.typename + '-' + info.contentId).slideUp();
			self.listRemove(el);
		});

		var btn  = this.wrapper.find('.list-selection-bar .perform-actions-trigger');
		var load = this.wrapper.find('.list-selection-bar .ajax-loading');

		this.actionsMenu = new DeskPRO.UI.Menu({
			menuElement: $('ul.actions-menu:first', this.wrapper),
			triggerElement: $('.perform-actions-trigger:first', this.wrapper),
			onItemClicked: function(info) {
				var data = [];
				var lines = [];
				$('input.item-select:checked', this.wrapper).each(function() {
					lines.push($(this).parent().get(0));
					var typename = $(this).data('content-type');
					var id = $(this).data('content-id');

					data.push({
						name: 'content[' + typename + '][]',
						value: id
					});
				});

				if (!data.length) {
					return;
				}

				var action = $(info.itemEl).data('action');

				btn.hide();
				load.show();

				var sendFn = function() {
					$.ajax({
						url: BASE_URL + 'agent/publish/content/validating-mass-actions/' + action,
						data: data,
						type: 'POST',
						dataType: 'json',
						complete: function() {
							load.hide();
							btn.show();
						},
						success: function() {
							$(lines).fadeOut().each(function() {
								self.listRemove($(this));
							});
						}
					});
				}

				if (action == 'disapprove') {
					DeskPRO_Window.showPrompt("Enter a reason or comment to send to the authors", function(reason) {
						data.push({
							name: 'reason',
							value: reason
						});
						sendFn();
					});
				} else {
					sendFn();
				}
			}
		});
		this.ownObject(this.actionsMenu);

		this.selectionBar = new DeskPRO.Agent.PageHelper.SelectionBar(this, {
			onButtonClick: function(ev) {
				self.actionsMenu.open(ev);
			}/*,
			onCountChange: function(count) {
				var isOpen = self.actionsMenu.isOpen();

				if (count > 0 && !isOpen) {
					self.actionsMenu.open();
				} else if (count <= 0 && isOpen) {
					self.actionsMenu.close();
				}
			}*/
		});
		this.ownObject(this.selectionBar);

		this.enableHighlightOpenRows('feedback', 'feedback_id', '.row-item.feedback-');
	},

	listRemove: function(el) {
		DeskPRO_Window.util.modCountEl($('#publish_validating_count'), '-');
		DeskPRO_Window.sections.publish_section.recountBadge();
		this.selectionBar.checkNone();
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.PublishDraftsList = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.Basic,

	initPage: function(el) {
		var self = this;
		this.wrapper = el;

		DeskPRO_Window.getMessageBroker().addMessageListener('publish.drafts.list-remove', function (info) {
			$('article.' + info.typename + '-' + info.contentId, this.wrapper).slideUp();
		}, this);

		this.actionsMenu = new DeskPRO.UI.Menu({
			triggerElement: $('.perform-actions-trigger:first', this.wrapper),
			menuElement: $('ul.actions-menu:first', this.wrapper),
			onItemClicked: function(info) {
				var data = [];
				var lines = [];
				$('input.item-select:checked', this.wrapper).each(function() {
					lines.push($(this).parent().get(0));
					var typename = $(this).data('content-type');
					var id = $(this).data('content-id');

					if (typename && id) {
						data.push({
							name: 'content[' + typename + '][]',
							value: id
						});
					}
				});

				if (!data.length) {
					return;
				}

				var action = $(info.itemEl).data('action');

				$.ajax({
					url: BASE_URL + 'agent/publish/drafts/mass-actions/' + action,
					data: data,
					type: 'POST',
					dataType: 'json',
					context: this,
					success: function(data) {
						self.selectionBar.checkNone();
						if (data.affected) {
							Array.each(data.affected, function(info) {
								DeskPRO_Window.getMessageBroker().sendMessage('publish.drafts.list-remove', info);
							}, this);
						}
					}
				});
			}
		});
		this.ownObject(this.actionsMenu);

		this.selectionBar = new DeskPRO.Agent.PageHelper.SelectionBar(this, {
			onButtonClick: function(ev) {
				self.actionsMenu.open(ev);
			}/*,
			onCountChange: function(count) {
				var isOpen = self.actionsMenu.isOpen();

				if (count > 0 && !isOpen) {
					self.actionsMenu.open();
				} else if (count <= 0 && isOpen) {
					self.actionsMenu.close();
				}
			}*/
		});
		this.ownObject(this.selectionBar);
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.PublishSearch = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.Basic,

	initPage: function(el) {
		var self = this;
		this.wrapper = el;

		this.listWrapper = $('section.list-listing', this.wrapper);
		this.sendContentLink = new DeskPRO.Agent.PageHelper.SendContentLink(this, {
			contentListEl: this.listWrapper
		});
		this.ownObject(this.sendContentLink);
	}
});
Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.FeedbackSearch = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.Basic,

	initPage: function(el) {
		var self = this;
		this.wrapper = el;
	}
});
Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.PublishSearchLog = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'publish_searchlog';
	},

	initPage: function(el) {
		this.tabs = new DeskPRO.UI.SimpleTabs({
			triggerElements: $('li', this.getEl('tabs'))
		});
		this.ownObject(this.tabs);
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.FeedbackCommentsValidating = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.PublishValidatingComments,

	updateCount: function(action) {
		var countEl = $('#feedback_comments_validating_count');
		var count = parseInt(countEl.text());

		if (action == 'add') {
			count++;
		} else {
			count--;
		}

		if (count < 0) {
			count = 0;
		}

		var countEl = $('#feedback_comments_validating_count').text(count);

		DeskPRO_Window.sections.feedback_section.recountBadge();
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.FeedbackContentValidating = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.PublishValidatingContent,

	listRemove: function(el) {
		DeskPRO_Window.util.modCountEl($('#feedback_validating_count'), '-');
		DeskPRO_Window.sections.feedback_section.recountBadge();
		this.selectionBar.checkNone();
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.TaskList = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'task-list';
	},

	initPage: function(el) {
		var self = this;

		if (DeskPRO_Window.sections.tasks_section) {
			DeskPRO_Window.sections.tasks_section.doRelaodPage = false;
		}

		var control = new DeskPRO.Agent.PageHelper.TaskListControl(el, {
			menuVis:  this.getEl('menu_vis'),
			assignOb: this.getEl('assign_ob'),
			completeCountEl: this.getEl('complete_count')
		});

		control.addEvent('updateUi', function() {
			self.updateUi();
		});
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.Search = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'search';
		this.wrapper = null;
	},

	initPage: function(el) {
		this.wrapper = el;
	},

	initTypeList: function(listWrap) {

	},

	loadMore: function(loadName) {

	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.DealList = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'deal-list';
                this.resultTypeName = 'basic';
		this.resultTypeId = 'general';
	},

	initPage: function(el) {
		var self = this;
		var openForEl = null;
                this._initDisplayOptions();


                this.listWrapper = $('section.deal-simple-list', this.wrapper);

		this.relatedContentList = new DeskPRO.Agent.PageHelper.RelatedContentList(this, {
			contentListEl: this.listWrapper
		});
		this.ownObject(this.relatedContentList);
		

	},

        _initDisplayOptions: function() {

		var self = this;

                // Sorting options
		var sortMenuBtn = $('.order-by-menu-trigger', this.wrapper).first();
		this.sortingMenu = new DeskPRO.UI.Menu({
			triggerElement: sortMenuBtn,
			menuElement: $('.order-by-menu', this.wrapper).first(),
			onItemClicked: function(info) {
				var item = $(info.itemEl);

				var prop = item.data('order-by')
				var label = item.text().trim();

				// Change the displayed label for some visual feedback
				$('.label', sortMenuBtn).text(label);

				var url = self.meta.refreshUrl;
				url = Orb.appendQueryData(url, 'order_by', prop);
                                DeskPRO_Window.loadListPane(url);
			}
		});
		this.ownObject(this.sortingMenu);

                var groupMenuBtn = $('.group-by-menu-trigger', this.wrapper).first();
		this.groupingMenu = new DeskPRO.UI.Menu({
			triggerElement: groupMenuBtn,
			menuElement: $('.group-by-menu', this.wrapper).first(),
			onItemClicked: function(info) {
				var item = $(info.itemEl);

				var prop = item.data('group-by')
				var label = item.text().trim();

				// Change the displayed label for some visual feedback
				$('.label', groupMenuBtn).text(label);

				var url = self.meta.refreshUrl;
				url = Orb.appendQueryData(url, 'group_by', prop);
                                DeskPRO_Window.loadListPane(url);
			
			}
		});
		this.ownObject(this.groupingMenu);

                this.displayOptions = new DeskPRO.Agent.PageHelper.DisplayOptions(this, {
			prefId: 'deal-filter',
			//resultId: this.meta.resultId,
			refreshUrl: this.meta.refreshUrl
		});
		this.ownObject(this.displayOptions);
        }

});

Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.TwitterFollowers = new Orb.Class({
    Extends: DeskPRO.Agent.PageFragment.ListPane.Basic,

    initPage: function(el) {
        this.wrapper = $(el);
		this.content = $('.content', this.wrapper);
		var self = this;

		this.meta.fetchResultsUrl = this.meta.listUrl;

		DeskPRO_Window.getMessageBroker().sendMessage('twitter-section.list-activated', {
			listUrl: this.meta.listUrl
		});

		var helper = new DeskPRO.Agent.PageHelper.Twitter(this.content, this, {
			messageUrl: this.getMetaData('saveUserMessageUrl'),
			userArchiveHideCallback: function(row) {
				var pageHelper = self.resultsHelper,
					page = pageHelper.getCurrentPage(),
					numPages = pageHelper.getNumPages();

				pageHelper.adjustResultCount(-1);

				if (page < numPages) {
					var data = {};
					data.last = 1;
					data.page = page;

					setTimeout(function() {
						$.ajax({
							url: self.getMetaData('listUrl'),
							dataType: 'html',
							data: data,
							success: function(html) {
								var $html = $(html);
								self.content.find('.followers-list').append($html);
							}
						});
					}, 200);
				} else if (pageHelper.resultCount <= 0) {
					self.wrapper.find('.list-listing.no-results').show();
					self.wrapper.find('.results-nav').hide();
				}
			}
		});

		this.content.find('textarea').TextAreaExpander();

		var opt = {
			perPage: this.meta.perPage || 25,
			currentPage: this.meta.currentPage,
			totalCount: this.meta.totalCount,
			resultRowSelector: 'article.twitter-user',
			resultsContainer: this.content
		};
		this.resultsHelper = new DeskPRO.Agent.PageHelper.Results(this, opt);
		this.ownObject(this.resultsHelper);
    }

});

Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.TwitterStatus = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'twitter-status-list';
		this.countReflected = {};
	},

	initPage: function(el) {
		this.wrapper = $(el);
		var self = this;

		DeskPRO_Window.getMessageBroker().sendMessage('twitter-section.list-activated', {
			listUrl: this.meta.statusListUrl
		});

		this.meta.fetchResultsUrl = this.meta.statusListUrl;

		this.header = $('.header', this.wrapper);
		this.content = $('.content', this.wrapper);

		this.twitterHelper = new DeskPRO.Agent.PageHelper.Twitter(this.content, this, {
			statusArchiveHideCallback: function(row) {
				var id = parseInt(row.data('status-id'), 10);
				if (id && !self.countReflected[id]) {
					self.countReflected[id] = true;
					self.resultsHelper.adjustResultCount(-1);
				}
				self._afterTweetRemoved(0);
			}
		});

		this._initHeader();
		this._initContent(this.content);
		this._initControls(this.content);

		this.wrapper.on('click', '.new-tweet-list-indicator', function() {
			self.reload();
		});

		var opt = {
			perPage: this.meta.perPage || 25,
			currentPage: this.meta.currentPage,
			totalCount: this.meta.totalCount,
			resultRowSelector: 'article.twitter-status',
			resultsContainer: this.content,
			preFetchCallback: function(data) {
				$.each(self._getDisplayOptions(), function(k, v) {
					if (/boolean|number|string/.test(typeof v)) {
						data.push({name: k, value: v});
					} else {
						$.each(v, function(kk, vv) {
							data.push({name: k + '[' + kk + ']', value: vv});
						});
					}
				});
				return data;
			},
			onPostSetNewResults: function(x, y, results) {
				self._afterLoading(results);
			}/*,
			infiniteScroll: true,
			infiniteScrollTarget: this.content.find('.twitter-status-list'),
			infiniteScrollLoadFilter: function(results) {
				return results.find('.row-item.twitter-status');
			}*/
		};
		this.resultsHelper = new DeskPRO.Agent.PageHelper.Results(this, opt);
		this.ownObject(this.resultsHelper);

		DeskPRO_Window.getMessageBroker().addMessageListener('agent.tweet-added', function (data) {
			self.adjustTweetCountsFromClientMessage(data, 1);
			self.adjustShownTweetsForTweetAdded(data);

			self.countReflected = {};
		});

		DeskPRO_Window.getMessageBroker().addMessageListener('agent.tweet-updated', function (data) {
			if (data.trigger_user_id && data.trigger_user_id == DESKPRO_PERSON_ID) {
				return;
			}

			if (typeof data.change_archived !== 'undefined') {
				if (data.change_archived) {
					// moved to archived, reduce counts
					self.adjustTweetCountsFromClientMessage(data, -1);
				} else {
					// moved to unarchived, increase counts
					self.adjustTweetCountsFromClientMessage(data, 1);
				}
			} else if (data.deleted) {
				self.adjustTweetCountsFromClientMessage(data, -1);
			}

			self.adjustShownTweetsForTweetUpdated(data);

			self.countReflected = {};
		});
	},

	adjustTweetCountsFromClientMessage: function(data, adjustAmount) {
		var accountId = data.account_id;

		if (this.countReflected[data.account_status_id]) {
			return;
		}

		if (this._tweetAppliesToPage(data) && this.resultsHelper && this.resultsHelper.options) {
			this.countReflected[data.account_status_id] = true;
			this.resultsHelper.adjustResultCount(adjustAmount);
		}
	},

	adjustShownTweetsForTweetAdded: function(data) {
		if (this.content.find('.twitter-status-' + data.account_status_id).length) {
			// tweet already shown
			return;
		}

		if (!this.resultsHelper || !this.resultsHelper.options) {
			// page destroyed
			return;
		}

		if (this._tweetAppliesToPage(data)) {
			if (this.resultsHelper.getCurrentPage() > 1) {
				this.adjustNewTweetIndicator(1);
			} else {
				this.addTweetToPage(data.account_status_id, data.tweet_html);
			}
		}
	},

	adjustNewTweetIndicator: function(adjust) {
		var newIndicator = this.wrapper.find('.new-tweet-list-indicator');
		var newCount = (newIndicator.data('new-count') || 0) + adjust;
		newIndicator.data('new-count', newCount);
		newIndicator.text(newCount == 1 ? '1 new tweet' : newCount + ' new tweets').show();
	},

	_tweetAppliesToPage: function(data) {
		if (!this.meta.accountId || this.meta.accountId != data.account_id) {
			return false;
		}

		if (data.is_from_self) {
			return (
				this.menuOptions.filter('[name=account]').is(':checked')
				|| this.meta.listRoute == 'agent_twitter_sent_list'
			);
		}

		var isInInbox = (
			$.inArray(data.status_type, ['direct', 'reply', 'mention', 'retweet']) !== -1
			|| data.is_favorited
		);

		switch (this.meta.listRoute) {
			case 'agent_twitter_mine_list':
				if (data.assignment !== 'agent:' + DESKPRO_PERSON_ID) {
					return false;
				}
				break;

			case 'agent_twitter_team_list':
				var hasOwnTeam = false;
				for (var i = 0; i < DESKPRO_TEAM_IDS.length; i++) {
					var teamId = DESKPRO_TEAM_IDS[i];
					if (data.assignment === 'agent_team:' + teamId) {
						// my teams' tweets
						hasOwnTeam = true;
						break;
					}
				}
				if (!hasOwnTeam) {
					return false;
				}
				break;

			case 'agent_twitter_unassigned_list':
				if (data.assignment !== '') {
					return false;
				}
				if (!isInInbox) {
					return false;
				}
				break;

			case 'agent_twitter_all_list':
				if (!isInInbox) {
					return false;
				}
				break;

			case 'agent_twitter_timeline_list':
				if (data.status_type !== 'timeline') {
					return false;
				}
				break;

			case 'agent_twitter_sent_list':
				// the true case is handled above
				return false;

			case 'agent_twitter_run_search':
				return false;

			default:
				return false;
		}

		if (this.meta.group) {
			switch (this.meta.group) {
				case 'type':
					if (this.meta.groupValue == 'favorite') {
						if (!data.is_favorited) {
							return false;
						}
					} else {
						if (data.status_type !== this.meta.groupValue) {
							return false;
						}
					}
					break;

				case 'agent':
					if (data.agent_id != this.meta.groupValue) {
						return false;
					}
					break;

				case 'team':
					if (data.agent_team_id != this.meta.groupValue) {
						return false;
					}
					break;
			}
		}

		return true;
	},

	adjustShownTweetsForTweetUpdated: function(data) {
		var row = this.content.find('.twitter-status-' + data.account_status_id);

		if (row.length) {
			if (typeof data.change_archived !== 'undefined') {
				var showArchived = this.menuOptions.filter('[name=archived]').is(':checked');
				if (data.change_archived && !showArchived) {
					this.removeTweetFromPage(data.account_status_id);
				}
			}
			if (data.deleted) {
				this.removeTweetFromPage(data.account_status_id);
			}
			if (data.reply_added_html && data.reply_added_id) {
				if (!row.find('.twitter-reply-' + data.reply_added_id).length) {
					var html = $(data.reply_added_html);
					row.find('.twitter-replies').append(html);
					$('.timeago', html).timeago();

					row.find('.reply-list').show();
				}
			}
			if (data.note_added_html  && data.note_added_id) {
				if (!row.find('.twitter-note-' + data.note_added_id).length) {
					var html = $(data.note_added_html);
					row.find('.note-list').append(html);
					$('.timeago', html).timeago();

					row.find('.status-notes').show();
				}
			}
			if (data.edited_html) {
				row.find('.main-status-body .status-text').html(data.edited_html);
			}
			if (data.retweeted) {
				var link = row.find('li.opt-trigger.retweet, li.opt-trigger.retweeted');
				link.addClass('retweeted').removeClass('retweet');
				link.find('label').text('Retweeted');
			}
			if (data.unretweeted) {
				var link = row.find('li.opt-trigger.retweet, li.opt-trigger.retweeted');
				link.addClass('retweet').removeClass('retweeted');
				link.find('label').text('Retweet');
			}
			if (data.favorited) {
				row.find('.add-favorite, .favorited').addClass('favorited').removeClass('add-favorite');
			}
			if (data.unfavorited) {
				row.find('.add-favorite, .favorited').addClass('add-favorite').removeClass('favorited');
			}
			if (typeof data.change_assignment !== 'undefined') {
				var opt = row.find('.agents_sel option[value="' + data.change_assignment + '"]');
				if (opt.length) {
					opt.closest('select').val(data.change_assignment);
					var label = opt.text().trim();
					if (data.change_assignment == 'agent:' + DESKPRO_PERSON_ID) {
						label = 'Me';
					}

					var labelEl = row.find('li.opt-trigger.agent label');
					if (data.assignment_picture) {
						labelEl.text(' ' + label).prepend($('<img class="agent-assign-icon" />').attr('src', data.assignment_picture));
					} else {
						labelEl.text(label);
					}
				}
			}
		} else {
			if (typeof data.change_archived !== 'undefined' && !data.change_archived) {
				if (this.resultsHelper.getCurrentPage() == 1) {
					this.addTweetToPage(data.account_status_id, data.tweet_html);
				}
			}
		}

		if (this.content.find('.twitter-reply-' + data.account_status_id).length) {
			if (data.deleted) {
				this.removeReplyFromPage(data.account_status_id);
			}
			if (data.edited_html) {
				var row = this.content.find('.twitter-reply-' + data.account_status_id);
				row.find('.status-text').html(data.edited_html);
			}
		}
	},

	addTweetToPage: function(account_status_id, html) {
		if (!this.resultsHelper || !this.resultsHelper.options) {
			// page destroyed
			return;
		}

		if (!this.countReflected[account_status_id]) {
			this.resultsHelper.adjustResultCount(1);
			this.countReflected[account_status_id] = true;
		}

		var $html = $(html);
		this.content.find('.twitter-status-list').prepend($html);
		this._afterLoading($html);

		this.adjustShownTweets();
	},

	adjustShownTweets: function() {
		var count = this.resultsHelper.updateShowingCount();

		if (count === false) {
			return;
		}

		if (count == 0) {
			this.wrapper.find('.list-listing.no-results').show();
		} else {
			this.wrapper.find('.list-listing.no-results').hide();
		}

		if (count > this.resultsHelper.options.perPage) {
			$(this.resultsHelper.options.resultRowSelector, this.resultsHelper.resultsContainer)
				.slice(this.resultsHelper.options.perPage - count)
				.remove();

			this.resultsHelper.updateShowingCount()
		}
	},

	removeTweetFromPage: function(account_status_id) {
		var el = this.content.find('.twitter-status-' + account_status_id).filter(':not(:animated)');
		if (el.length) {
			el.remove();
			if (!this.countReflected[account_status_id]) {
				this.resultsHelper.adjustResultCount(-1);
				this.countReflected[account_status_id] = true;
			}
			this._afterTweetRemoved(0);
		}
	},

	removeReplyFromPage: function(account_status_id) {
		var el = this.content.find('.twitter-reply-' + account_status_id);
		if (el.length) {
			var row = this.twitterHelper.closestRow(el);
			el.remove();
			if (!row.find('.twitter-replies .twitter-reply').length) {
				row.find('.reply-list').hide();
			}
		}
	},

	_afterTweetRemoved: function(delay) {
		var pageHelper = this.resultsHelper,
			page = pageHelper.getCurrentPage(),
			numPages = pageHelper.getNumPages();
		var self = this;

		if (!this.resultsHelper || !this.resultsHelper.options) {
			// page destroyed
			return;
		}

		pageHelper.updateShowingCount();

		if (page < numPages) {
			var data = this._getDisplayOptions();
			data.last = 1;
			data.page = page;

			setTimeout(function() {
				$.ajax({
					url: self.getMetaData('statusListUrl'),
					dataType: 'html',
					data: data,
					success: function(html) {
						var $html = $(html);
						self.content.find('.twitter-status-list').append($html);
						self._afterLoading($html);
					}
				});
			}, delay || 0);
		} else if (pageHelper.resultCount <= 0) {
			this.wrapper.find('.list-listing.no-results').show();
			this.wrapper.find('.results-nav').hide();
		}
	},

	_afterLoading: function(content) {
		if (!content) { content = this.content; }
		this._initContent(content);
		this._initControls(content);

		this.wrapper.find('.new-tweet-list-indicator').data('new-count', 0).hide();

		if (this.selectionBar) {
			this.selectionBar.updateCount();
		}
		if (this.resultsHelper && this.resultsHelper.options) {
			this.resultsHelper.updateShowingCount();
		}
	},

	_initHeader: function() {
		this._initSortByFields();
		this._initIncludeFields();

		var self = this;

		this.selectionBar = new DeskPRO.Agent.PageHelper.SelectionBar(this, {
			onButtonClick: function() {
				self.massActions.open();
			},
			onCountChange: function(count) {
				var isOpen = self.massActions.isOpen();

				if (count > 0 && !isOpen) {
					self.massActions.open();
				} else if (count <= 0 && isOpen) {
					self.massActions.close();
				}
			},
			checkSelector: '.twitter-status:not(.archived) input.item-select'
		});
		this.ownObject(this.selectionBar);

		this.massActions = new DeskPRO.Agent.PageHelper.MassActions(this, {
			isListView: false,
			applyAction: function(wrapper, formData) {
				var data = formData,
					myFormData = $('input, textarea, select', wrapper).serializeArray();

				$(myFormData).each(function(index, param) {
					data[param.name] = param.value;
				});

				wrapper.addClass('loading');

				$.ajax({
					type: 'POST',
					url: BASE_URL + "agent/twitter/status/ajax-mass-save.json",
					'data': data,
					'dataType': 'json',
					success: function() {
						self.massActions.close();
						self.reload();
					}
				}).done(function() {
					wrapper.removeClass('loading');
				});
			},
			closeOnApply: false,
			openAction: function(wrapper) {
				if (!wrapper.data('twitter-helper')) {
					wrapper.data('twitter-helper',
						new DeskPRO.Agent.PageHelper.Twitter($('#twitter-mass-action-overlay'), self)
					);
				}
			}
		});
		this.ownObject(this.massActions);
	},

	_initContent: function(content) {
		var self = this;

		$('.timeago', content).timeago();
		var list = content.find('.twitter-status-list');
		if (list.length && list.data('page') && this.resultsHelper) {
			this.resultsHelper.setPage(parseInt(list.data('page'), 10), true);
			this.resultsHelper.setResultCount(parseInt(list.data('total-count'), 10));
		}
	},

	_initControls: function(content) {
	},

	_initSortByFields: function() {
		var self = this;

		var sortMenuBtn = $('.order-by-menu-trigger', this.header).first();
		this.sortingMenu = new DeskPRO.UI.Menu({
			triggerElement: sortMenuBtn,
			menuElement: $('.order-by-menu', this.header).first(),
			onItemClicked: function(info) {
				var item = $(info.itemEl);

				var prop = item.data('order-by');
				var label = item.find('.label').text().trim();

				// Change the displayed label for some visual feedback
				$('.label label', sortMenuBtn).text(label);
				sortMenuBtn.find('.order-dir').hide();
				sortMenuBtn.find('.order-dir.' + prop.split('_').pop()).show();

				sortMenuBtn.data('dir', prop);

				self.sortingMenu.close();
				self.reload();
			}
		});
		this.ownObject(this.sortingMenu);
	},

	_initIncludeFields: function() {
		var self = this;

		this.menuOptions = this.header.find('.btn-controls input:checkbox');
		this.menuOptions.click(function() { self.reload(); });
	},

	_getDisplayOptions: function() {
		var options = {
			include: {}
		};

		if (this.menuOptions) {
			this.menuOptions.each(function() {
				var field = $(this);
				options.include[field.attr('name')] = field.attr('checked') ? 1 : 0;
			});
		}

		return options;
	},

	reload: function() {
		$.ajax({
			url: this.getMetaData('statusListUrl'),
			dataType: 'html',
			data: this._getDisplayOptions(),
			context: this,
			success: function(html) {
				this.content.html(html);
				this._afterLoading();
			}
		});
	},

	highlightStatus: function(id) {
		$('.twitter-status', this.content).removeClass('highlight');
		$('.status-'+id, this.content).addClass('highlight');
	},

	downlightStatus: function(id) {
		$('.twitter-status-'+id, this.content).removeClass('highlight');
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.TwitterSearch = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.TwitterStatus,

	initPage: function(el) {
		var self = this;

		this.parent(el);

		this.maxTwitterId = el.find('.twitter-status-list').attr('data-twitter-max-id');

		this.updateInterval = setInterval(function() {
			$.ajax({
				url: self.meta.statusListUrl,
				data: {since_id: self.maxTwitterId },
				dataType: 'html',
				success: function(html) {
					var $html = $(html).first();

					if ($html.data('added')) {
						self.maxTwitterId = $html.attr('data-twitter-max-id');
						self.resultsHelper.adjustResultCount($html.data('added'));

						if (self.resultsHelper.getCurrentPage() > 1) {
							self.adjustNewTweetIndicator($html.data('added'));
						} else {
							var adding = $html.find('.row-item.twitter-status');

							self.content.find('.twitter-status-list').prepend(adding);
							self._afterLoading(adding);
							self.adjustShownTweets();
						}
					}
				}
			});
		}, 60 * 1000);
	},

	destroy: function() {
		clearInterval(this.updateInterval);
	}
});
