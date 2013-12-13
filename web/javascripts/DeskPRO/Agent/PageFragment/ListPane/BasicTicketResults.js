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
