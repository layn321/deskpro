Orb.createNamespace('DeskPRO.Agent.WindowElement');

/**
 * This keeps track of handles to open tabs, and lets you attach functionality
 * to certain types of tabs.
 */
DeskPRO.Agent.TabWatcher = new Orb.Class({
	Implements: [Orb.Util.Options, Orb.Util.Events],

	initialize: function(options) {
		this.options = {
			tabManager: null
		};

		this.setOptions(options);

		this.tabManager = this.options.tabManager;
		this.selectionHistory = [];

		this.tabManager.addEvent('activateTab', this._activateTab, this);
		this.tabManager.addEvent('addTab', this._addTab, this);
		this.tabManager.addEvent('deactivateTab', this._deactivateTab, this);
		this.tabManager.addEvent('removeTab', this._removeTab, this);

		this.watchedTypes = {};
	},

	_activateTab: function(tab, containerEl, tabManager) {
		var id = tab.id;

		this.selectionHistory.erase(id);
		this.selectionHistory.push(id);

		var typename = this.getTabType(tab);
		if (this.watchedTypes[typename]) {
			Array.each(this.watchedTypes[typename], function(watcher) {
				watcher.fireEvent('watchedTabActivated', [tab]);
			});
		}
		if (this.watchedTypes['*']) {
			Array.each(this.watchedTypes['*'], function(watcher) {
				watcher.fireEvent('watchedTabActivated', [tab]);
			});
		}
	},

	_addTab: function(tab, containerEl, tabManager) {
		var typename = this.getTabType(tab);
		if (this.watchedTypes[typename]) {
			Array.each(this.watchedTypes[typename], function(watcher) {
				watcher.fireEvent('watchedTabAdded', [tab]);
			});
		}
		if (this.watchedTypes['*']) {
			Array.each(this.watchedTypes['*'], function(watcher) {
				watcher.fireEvent('watchedTabAdded', [tab]);
			});
		}
	},

	_deactivateTab: function(tab, containerEl, tabManager) {
		var typename = this.getTabType(tab);
		var isLast = false;
		if (this.tabManager.tabCount == 1) {
			isLast = true;
		}

		if (this.watchedTypes[typename]) {
			Array.each(this.watchedTypes[typename], function(watcher) {
				watcher.fireEvent('watchedTabDeactivated', [tab, isLast]);
			});
		}
		if (this.watchedTypes['*']) {
			Array.each(this.watchedTypes['*'], function(watcher) {
				watcher.fireEvent('watchedTabDeactivated', [tab, isLast]);
			});
		}
	},

	_removeTab: function(tab, tabManager) {
		this.selectionHistory.erase(tab.id);

		var typename = this.getTabType(tab);
		if (this.watchedTypes[typename]) {
			Array.each(this.watchedTypes[typename], function(watcher) {
				watcher.fireEvent('watchedTabRemoved', [tab]);
			});
		}
		if (this.watchedTypes['*']) {
			Array.each(this.watchedTypes['*'], function(watcher) {
				watcher.fireEvent('watchedTabRemoved', [tab]);
			});
		}
	},


	/**
	 * Add a type watcher.
	 *
	 * @param string typename  The tabtype or an asterisk to subscribe to all types
	 * @param {Object} watcher
	 * @param {Boolean} notifyOfExisting Cycle through the already open tabs of the type and notify the watcher with the 'watchedTabAdded' event
	 */
	addTabTypeWatcher: function(typename, watcher, notifyOfExisting) {
		if (!this.watchedTypes[typename]) {
			this.watchedTypes[typename] = [];
		}

		this.watchedTypes[typename].push(watcher);

		if (notifyOfExisting) {
			Array.each(DeskPRO_Window.getTabWatcher().findTabType(typename), function(tab) {
				watcher.fireEvent('watchedTabAdded', tab);
			});
		}
	},


	/**
	 * Remove a type watcher.
	 *
	 * @param string typename
	 * @param {Object} watcher
	 */
	removeTabTypeWatcher: function(typename, watcher) {
		if (!this.watchedTypes[typename]) {
			return;
		}

		this.watchedTypes[typename].erase(watcher);
	},


	/**
	 * Return the active tab
	 *
	 * @return {Object}
	 */
	getActiveTab: function() {
		return this.tabManager.getActiveTab();
	},


	/**
	 * Get the active tab iif it's typename
	 *
	 * @param typename
	 * @return {Object}
	 */
	getActiveTabIfType: function(typename) {
		var tab = this.getActiveTab();
		if (this.getTabType(tab) != typename) {
			return null;
		}

		return tab;
	},


	/**
	 * Get the type of the currently active tab
	 *
	 * @return {Object}
	 */
	getActiveTabType: function() {
		return this.getTabType(this.getActiveTab());
	},


	/**
	 * Check if the active tab is a certain type
	 *
	 * @param typename
	 */
	isTabTypeActive: function(typename) {
		return this.getActiveTabType() == typename;
	},


	/**
	 * Get the tabtype of a tab
	 *
	 * @param tab
	 * @return string
	 */
	getTabType: function(tab) {
		if (!tab) {
			return null;
		}

		if (tab.page) {
			if (tab.page.TYPENAME && tab.page.TYPENAME != 'loading') {
				return tab.page.TYPENAME;
			} else if (tab.page.TYPENAME_FOR) {
				return tab.page.TYPENAME_FOR;
			}
		}

		return 'general';
	},


	/**
	 * Get the tab that was selected before the one that is currently selected
	 *
	 * @param int steps How far back to go in the selection history
	 * @return {Object}
	 */
	getLastSelectedTab: function(steps) {
		var l = this.selectionHistory.length - 1;
		l -= steps;

		if (l < 0) {
			return null;
		}

		return this.getTab(this.selectionHistory[l]);
	},


	/**
	 * Get the last selected tab of a certain type.
	 *
	 * @param string typename
	 * @return {Object}
	 */
	getLastSelectedTabType: function(typename) {

		var len = this.selectionHistory.length - 1;
		if (!len) {
			return null;
		}

		while (len-- > 0) {
			var tab = this.getTab(len);
			if (this.getTabType(tab) == typename) {
				return tab;
			}
		}

		return null;
	},


	/**
	 * Get all the tabs in the order they were last selected.
	 *
	 * @return {Array}
	 */
	getSelectionHistory: function() {
		var tabs = [];

		Array.each(this.selectionHistory, function(id) {
			tabs.push(this.getTab(id));
		}, this);

		tabs.reverse();

		return tabs;
	},


	/**
	 * Find all tabs of a certain type (use getLastSelectedTabType if you only need one).
	 *
	 * @param typename
	 */
	findTabType: function(typename) {
		var tabs = [];

		Object.each(this.tabManager.getTabs(), function(tab) {
			if (this.getTabType(tab) == typename) {
				tabs.push(tab);
			}
		}, this);

		return tabs;
	},


	/**
	 * Find a tab by a function filter
	 *
	 * @param typename
	 * @param filter
	 * @return {Object}
	 */
	findTab: function(typename, filter) {

		var found = null;
		Object.each(this.tabManager.getTabs(), function(tab) {
			if (!typename || this.getTabType(tab) == typename) {
				if (!filter || filter(tab)) {
					found = tab;
					return false;
				}
			}
		}, this);

		return found;
	},

	/**
	 * Finds multiple tabs that match a filter
	 *
	 * @param typename
	 * @param filter
	 * @return {Array}
	 */
	findTabs: function(typename, filter) {
		var tabs = [];
		Object.each(this.tabManager.getTabs(), function(tab) {
			if (!typename || this.getTabType(tab) == typename) {
				if (!filter || filter(tab)) {
					tabs.push(tab);
				}
			}
		}, this);

		return tabs;
	}
});
