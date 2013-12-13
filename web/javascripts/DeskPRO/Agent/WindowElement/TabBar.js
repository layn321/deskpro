Orb.createNamespace('DeskPRO.Agent.WindowElement');

/**
 * The tabbar handles adding and removing tabs in the right pane of the window.
 */
DeskPRO.Agent.WindowElement.TabBar = new Orb.Class({

	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(options) {
		this.options = {
			/**
			 * The element that contains the tabs. This is the pane or wrapper element that
			 * contains the actual list. It must include a list with class '.dp-tab-list'
			 */
			tabPane: null,

			/**
			 * The element the append body pages to
			 */
			bodyPane: null
		};

		this.setOptions(options);

		this.tabPane = $(this.options.tabPane);
		this.tabList = this.tabPane.find('ul.dp-tab-list').first();
		this.tabList2 = $('#dp_collapsed_tabs');
		this.bodyPane = $(this.options.bodyPane);
		this.menuBtn = $(this.options.menuBtn);

		this.tabCount = 0;

		this.tabs = {};
		this.currentTabId = null;

		this.tabPane.on('mouseup', this._tabStripClick.bind(this));
		$('#dp_collapsed_tabs').on('mouseup', this._tabStripClick.bind(this));

		this.tabBarOverflow = new DeskPRO.Agent.WindowElement.TabBarOverflow();

		var self = this;
		this.tabList2.on('click', function(ev) {
			ev.preventDefault();
			DeskPRO_Window.setPaneVis('tabs', true);

			var el = $(ev.target);
			if (el.data('tab')) {
				self.activateTab(el.data('tab'));
			}
		});
	},


	//##################################################################################################################
	// Methods to fetch tabs
	//##################################################################################################################

	/**
	 * Get the active tab ID
	 *
	 * @return {String}
	 */
	getActiveTabId: function() {
		return this.currentTabId;
	},


	/**
	 * Get the active tab object
	 *
	 * @return {Object}
	 */
	getActiveTab: function() {
		return this.getTab(this.currentTabId);
	},


	/**
	 * Get a tab by its id
	 *
	 * @param {String} id
	 * @return {Object}
	 */
	getTab: function(id) {
		if (this.tabs[id] == undefined) {
			return null;
		}

		return this.tabs[id];
	},


	/**
	 * Get all tabs
	 *
	 * @return {Object}
	 */
	getTabs: function() {
		return this.tabs;
	},


	/**
	 * Get an array of all tab ids
	 *
	 * @return {Array}
	 */
	getTabIds: function() {
		return Object.keys(this.tabs);
	},


	/**
	 * Find a tab by pages fragment.
	 *
	 * @param {String} fragment
	 * @return {Object}
	 */
	findTabByFragment: function(fragment) {
		var retTab = null;

		Object.each(this.tabs, function(tab) {
			if (tab.page && tab.page.getMetaData('url_fragment') == fragment) {
				retTab = tab;
				return false;
			}
		});

		return retTab;
	},


	/**
	 * Find a tab by its pages route url
	 *
	 * @param {String} routeUrl
	 * @return {String}
	 */
	findTabByRouteUrl: function(routeUrl) {
		var retTab = null;

		Object.each(this.tabs, function(tab) {
			if (tab.page.getMetaData('routeUrl') == routeUrl) {
				retTab = tab;
				return false;
			}
		});

		return retTab;
	},


	//##################################################################################################################
	// Adding a removing tabs
	//##################################################################################################################

	/**
	 * Add a page to the tabbar
	 *
	 * @param {Object} page
	 */
	addTab: function(page) {

		this.isAdding = true;

		var id = Orb.uuid();
		page.meta.tabId = id;

		var data = {};
		data.id = id;
		data.page = page;
		data.title = page.getMetaData('title', 'Untitled');
		data.callback_render = function(container) {
			container = $(container);
			page.fireEvent('render', [container.first(), id]);
		};
		data.callback_remove_content = function(data, container) {
			if (data.isInited) {
				page.fireEvent('destroy');
			}
		};
		data.callback_activate = function() {
			page.fireEvent('activate');
		};
		data.callback_deactivate = function() {
			page.fireEvent('deactivate');
		};

		data.isInited = false;

		this.tabs[id] = data;
		this.tabCount++;

		//----------
		// Render content to dom
		//----------

		// The tab content
		data.wrapperId = 'tabcontent_' + id;

		if (page.meta.existingWrapper) {
			data.wrapper = page.meta.existingWrapper;
			data.wrapper.attr('id', data.wrapperId);
			data.wrapper.attr('class', 'tabViewDetailContent test');
			data.wrapper.css('display', 'none');
			data.wrapper.appendTo(this.bodyPane);
		} else {
			var preparedOutput = DeskPRO_Window.prepareWidgetedHtml(page.getHtml());

			data.wrapper = $('<div id="'+data.wrapperId+'" class="tabViewDetailContent" style="display: none">' + preparedOutput.html + '</div>').appendTo(this.bodyPane);

			DeskPRO_Window.runWidgetedJs(data.page, preparedOutput.jsSource, preparedOutput.jsInline);
		}

		//----------
		// Render tab button
		//----------

		data.tabBtnId = 'tabbtn_' + id;

		var tabIdClass = data.page.getMetaData('tabIdClass', '');
		var html = '<li id="'+data.tabBtnId+'" data-tab-id="'+data.id+'" class="' + tabIdClass;

			if (data.page.TYPENAME != 'basic') {
				html += ' ' + data.page.TYPENAME;
			}

			if (data.page.LOADING_TYPENAME) {
				html += ' ' + data.page.LOADING_TYPENAME;
			}

			html += '"><div class="item-hover-over-indicator"></div>';
			html += '<a>';
				html += '<i class="icon-globe dp-icon-placeholder"></i>'+Orb.escapeHtml(data.title)+'';
			html += '</a>';
			html += '<span class="bound-fade"></span>';
			html += '<span class="close"></span>';
		html += '</li>';

		var html2 = '<li id="'+data.tabBtnId+'_2" data-tab-id="'+data.id+'" class="' + tabIdClass;
			html2 += '">';
			html2 += '<span class="tab-title"><label>'+Orb.escapeHtml(data.title)+'</label> <i class="icon-remove-sign close trigger-close-tab"></i></span>';
		html2 += '</li>';

		data.tabBtn = $(html);
		data.tabBtn.data('tab', data);

		if (data.page && data.page.meta.alert_id) {
			data.tabBtn.addClass(data.page.meta.alert_id);
		}

		data.tabBtn2 = $(html2);
		data.tabBtn2.data('tab', data);

		var wasActive = false;
		var otherTab = null;
		if (data.page && data.page.meta.tabPlaceholderId) {
			otherTab = this.getTab(data.page.meta.tabPlaceholderId);
		}

		if (otherTab) {
			// We may have had a placeholder, in which case we want to place
			// the new tab where the old one was while also removing the placeholder
			// content in the body pane

			var otherTab = this.getTab(data.page.meta.tabPlaceholderId);
			data.tabBtn.insertAfter(otherTab.tabBtn);
			otherTab.tabBtn.remove();

			data.tabBtn2.insertAfter(otherTab.tabBtn2);
			otherTab.tabBtn2.remove();

			if (this.currentTabId == otherTab.id) {
				wasActive = true;
				this.currentTabId = null;
			}

			this.removeTab(otherTab, true);

		} else {
			data.tabBtn.prependTo(this.tabList);
			data.tabBtn2.appendTo(this.tabList2);
		}

		// If tabs are collapsed, then we need to re-calc
		// the layout when adding a new tab in case the side navstrip is hidden (it was empty and now is not)
		if (!DeskPRO_Window.paneVis.tabs) {
			DeskPRO_Window.layout.doResize(true);
		}

		//----------
		// Just about done
		//----------

		this.fireEvent('addTab', [data, this]);

		if (!this.currentTabId || wasActive) {
			this.activateTabById(id);
		} else {
			DeskPRO_Window.updateWindowUrlFragment();
		}

		this.isAdding = false;

		this.tabBarOverflow.update();

		return id;
	},

	/**
	 * Like addTab except the tab is marked as "loading"
	 *
	 * @param url
	 * @param routeData
	 */
	addTabPlaceholder: function(url, routeData) {
		var html = DeskPRO_Window.util.getPlainTpl($('#tab_loading_template'));

		var page = DeskPRO_Window.createPageFragment(html, 'DeskPRO.Agent.PageFragment.Page.Loading');
		page.meta.routeUrl = url;
		page.meta.routeData = routeData;
		page.TYPENAME_FOR = routeData.master;
		page.TAB_FOR_ID = routeData.masterTag;

		if (routeData.url_fragment) {
			page.meta.url_fragment = routeData.url_fragment;
		}

		if (routeData.title) {
			page.meta.title = routeData.title;
		}
		if (routeData.forTypename) {
			page.LOADING_TYPENAME = routeData.forTypename;
		}

		var id = this.addTab(page);
		this.activateTabById(id);

		if (routeData.tabLoad) {
			routeData.tabLoad();
		}

		return id;
	},


	/**
	 * Activate a tab in the tabbar
	 *
	 * @param {Object} id
	 */
	activateTab: function(tab) {

		if (!tab) {
			return;
		}

		var id = tab.id;

		// Already the current tab
		if (id == this.currentTabId) {
			return;
		}

		this.isActivating = true;

		if (this.currentTabId) {
			this.deactivateCurrentTab();
		}

		var data = this.tabs[id];
		if (!data || !data.wrapper) {
			this.removeTab(tab, true);
		}
		var wrapper = data.wrapper.show();

		if (!data.isInited) {
			data.isInited = true;

			if (data.callback_render !== undefined) {
				data.callback_render(wrapper);
			}

			this.fireEvent('activateTabRender', [data, $('#' + data.wrapperId), this]);
		}

		if (data.callback_activate !== undefined) {
			data.callback_activate(data, wrapper, this);
		}

		this.tabList.find('li').removeClass('activeTabList');
		data.tabBtn.addClass('activeTabList');
		this.clearAlertTab(data);

		this.currentTabId = id;

		this.fireEvent('activateTab', [data, wrapper, this]);

		this.isActivating = false;
		data.isActive = true;

		DeskPRO_Window.updateWindowUrlFragment();
	},


	/**
	 * Deactivate the currently selected tab
	 */
	deactivateCurrentTab: function() {

		if (!this.currentTabId) {
			return;
		}

		var data = this.tabs[this.currentTabId];
		data.isActive = false;

		// Chance to hook in before the nodes are actually removed
		this.fireEvent('deactivateTabBefore', [data, this.containerEl, this.isActivating, this]);

		if (data.callback_deactivate !== undefined) {
			data.callback_deactivate(data, $('#' + data.wrapperId), this);
		}

		DP.console.log('Hiding tab content: %o, id: %s', this.currentTabId, data.wrapperId);
		$('#' + data.wrapperId).hide();

		if (data.callback_hide_content !== undefined) {
			data.callback_hide_content(data, $('#' + data.wrapperId), this);
		}

		this.fireEvent('deactivateTab', [data, $('#' + data.wrapperId), this.isActivating, this]);

		this.currentTabId = null;
	},

	/**
	 * Determines if a tab is visible in relation to scrolling.
	 *
	 * @param tab
	 */
	isTabVisible: function(tab) {
		var left = tab.tabBtn.position().left;

		// Attempt to ignore margin and border. Lets hope they're the same on both sides.
		var guess_slack = Math.round((tab.tabBtn.outerWidth() - tab.tabBtn.innerWidth()) / 2);
		var right = left + tab.tabBtn.innerWidth() + guess_slack;

		var bounds = this.tabBarOverflow.getBounds();

		return !(right < bounds.left || left > bounds.right);
	},

	/**
	 * Remove a tab
	 *
	 * @param {Object} tab
	 * @param {Boolean} [silent]
	 */
	removeTab: function(tab, silent) {

		var id = tab.id;
		var wasActive = false;

		if (this.currentTabId == id) {
			wasActive = true;
			if (!silent) {
				this.deactivateCurrentTab();
			}

			this.currentTabId = null;
		}

		var data = this.tabs[id];
		delete this.tabs[id];
		this.tabCount--;

		if (data.callback_remove_content !== undefined) {
			data.callback_remove_content(data, $('#' + data.wrapperId), this);
		}

		if (data.wrapper) {
			data.wrapper.empty();
			data.wrapper.remove();
		}

		if (data.page) {
			if (data.page.meta.routeData && data.page.meta.routeData.xhr) {
				data.page.meta.routeData.xhr.abort();
			}

			if (data.page.meta.routeData && data.page.meta.routeData.dataUnload) {
				data.page.meta.routeData.dataUnload();
			}
		}

		if (!silent) {

			this.fireEvent('removeTab', [data, this]);

			if (wasActive) {
				var last_tab_id = Object.keys(this.tabs).getLast();
				if (last_tab_id) {
					this.activateTabById(last_tab_id);
				} else {
					if (!DeskPRO_Window.paneVis.list) {
						// If list view isnt active, then after a small timeout
						// make it visiable.
						// The timeout is in case we have other routines that auto-open
						// a new tab (e.g., after ticket reply)
						var self = this;
						window.setTimeout(function(){
							var last_tab_id = Object.keys(this.tabs).getLast();
							if (!last_tab_id) {
								DeskPRO_Window.paneVis.list = true;
								DeskPRO_Window.paneVis.tabs = false;
								DeskPRO_Window.layout.doResize(true);
							}
						}, 100);
					}
				}
			}

			if (data.tabBtn)  data.tabBtn.remove();
			if (data.tabBtn2) data.tabBtn2.remove();
		}

		DeskPRO_Window.updateWindowUrlFragment();
		this.tabBarOverflow.update();

		// Trigger a resize so the sidebar tabs can be hidden
		// if there are now no tabs
		if (!DeskPRO_Window.paneVis.tabs) {
			DeskPRO_Window.layout.doResize(true);
		}
	},


	/**
	 * Remove a tab via id
	 *
	 * @param {String} id
	 */
	removeTabById: function(id) {
		var tab = this.getTab(id);
		if (!tab) {
			DP.console.log("Cannot remove, unknown tab %s", id);
			DP.console.trace();
			return null;
		}
		this.removeTab(tab);
	},


	/**
	 * Activate a tab by id
	 *
	 * @param {String} id
	 */
	activateTabById: function(id) {
		var tab = this.getTab(id);
		if (!tab) {
			DP.console.log("Cannot activate, unknown tab %s", id);
		}
		this.activateTab(tab);
	},

	/**
	 * Activate a tab by id
	 *
	 * @param {String} id
	 */
	tabToFrontTabById: function(id, noalert) {
		var tab = this.getTab(id);

		if (!tab) {
			DP.console.log("Cannot activate, unknown tab %s", id);
		}

		var btn = tab.tabBtn;
		tab.tabBtn.detach();
		tab.tabBtn = btn;

		var otherTab = null;
		if (tab.page && tab.page.meta.tabPlaceholderId) {
			otherTab = this.getTab(tab.page.meta.tabPlaceholderId);
		}

		if (otherTab && otherTab != tab) {
			tab.tabBtn.insertAfter(otherTab.tabBtn);
			otherTab.tabBtn.remove();

			if (this.currentTabId == otherTab.id) {
				wasActive = true;
				this.currentTabId = null;
			}

			this.removeTab(otherTab, true);

		} else {
			tab.tabBtn.prependTo(this.tabList);
		}

		this.tabBarOverflow.resetScroll();

		if(!noalert) {
			tab.tabBtn.effect("pulsate", { times:4 }, 500);
		}
	},

	//##################################################################################################################
	// Tab functionality
	//##################################################################################################################

	alertTab: function(tab) {
		var el = tab.tabBtn;
		if (!el.length || el.is('.activeTabList') || el.is('.is-alerting')) return;

		if(!this.isTabVisible(tab)) {
			this.tabToFrontTabById(tab.id, true);
		}

		el.addClass('is-alerting');
		var timeout = this._alertTabDoHighlight.periodical(700, this, [el]);
		el.data('alerting-timeout', timeout);
	},

	clearAlertTab: function(tab) {
		var el = tab.tabBtn;
		if (!el.length) return;

		el.removeClass('alert-highlight').removeClass('is-alerting');

		var timeout = el.data('alerting-timeout');
		if (timeout) {
			window.clearTimeout(timeout);
		}

		el.data('alerting-timeout', null);
	},

	_alertTabDoHighlight: function(el) {
		el.toggleClass('alert-highlight');
	},


	//##################################################################################################################
	// Handling events
	//##################################################################################################################

	_tabStripClick: function(event) {

		if (this.cancelClickActivate) {
			this.cancelClickActivate = false;
			return;
		}

		this.cancelClickActivate = true;

		var el_click = $(event.target);

		if (el_click.is('li')) {
			var el = el_click;
		} else {
			var el = el_click.closest('li');
		}

		// If its not a tab, we can just ignore the event
		if (!el[0] || !el.is('li')) {
			DP.console.log('not click %o', event.target);
			this.cancelClickActivate = false;
			return;
		}

		event.preventDefault();
		event.stopPropagation();

		var tabId = el.data('tab-id');

		// If the clicked thing was the close button, or if its a middle-click...
		if (el_click.is('.close') || event.which == 2 || event.isDbl) {
			var tab = this.getTab(tabId);
			if (!tab) {
				return;
			}

			if (tab.page && tab.page.fireEvent) {
				event.deskpro = {cancelClose: false};
				tab.page.fireEvent('closeTab', [event, tab]);

				if (event.deskpro.cancelClose) {
					this.cancelClickActivate = false;
					return;
				}
			}

			tab.isCloseClick = true;
			this.removeTabById(tabId);
			tab.isCloseClick = false;

			this.cancelClickActivate = false;

			return;
		}

		// Otherwise activate the tab
		this.activateTabById(tabId);

		this.cancelClickActivate = false;
	}
});