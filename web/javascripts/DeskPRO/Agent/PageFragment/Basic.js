Orb.createNamespace('DeskPRO.Agent.PageFragment');

/**
 * A generic page fragment is any kind of page we'll spit into the content
 * area of the currently loaded page. For example, tabs, lightbox content etc.
 *
 * Each page can have it's own resources that should be loaded before the HTML
 * for it is rendered (though that responsibility is up to whatever uses the PageFragment).
 */
DeskPRO.Agent.PageFragment.Basic = new Orb.Class({

	Implements: [Orb.Util.Events],

	initializeProperties: function() {

	},

	updateUi: function() {
		var x;
		if (!this.IS_ACTIVE) {
			return;
		}
		if (this.wrapper) {
			if (!this.scrollHandlers) {
				this.scrollHandlers = this.wrapper.find('div.with-scroll-handler');
			}
			for (x = 0; x < this.scrollHandlers.length; x++) {
				var sh = $(this.scrollHandlers[x]).data('scroll_handler');
				if (sh && sh.updateSize) {
					sh.updateSize();
				}
			};
		}

		this.fireEvent('updateUi');
	},

	initialize: function(html) {
		var self = this;

		this.pageUid = Orb.uuid();
		this.ZONE = 'agent';
		this.TYPENAME = 'basic';
		this.IS_ACTIVE = false;

		this.allowDupe = false;
		this.scripts = [];
		this.stylesheets = [];
		this.html = '';
		this.meta = {};
		this.urls = {};

		this.destroyObjects = [];

		this.featureSelectors = {
			routes: [],
			times: []
		};

		this.resizerInterval = window.setInterval(function() {
			self.updateUi();
		}, 1100);

		this.initializeProperties();

		if (html) {
			this.html = html;
		}

		this.addEvent('activate', function() {
			this.IS_ACTIVE = true;
			DeskPRO_Window.getMessageBroker().sendMessage('page-fragment.activated', { page: this });
			this.updateUi();
		}, this);
		this.addEvent('deactivate', function() {
			this.IS_ACTIVE = false;
			DeskPRO_Window.getMessageBroker().sendMessage('page-fragment.deactivated', { page: this });
			if (this.wrapper) {
				this.wrapper.find('.with-handler').trigger('dp_hide');
			}
		}, this);

		// Auto-init
		this.addEvent('render', function(wrapper) {
			self.wrapper = wrapper;
			wrapper.data('page-fragment', self);
			wrapper.addClass('with-page-fragment');

			DeskPRO_Window.initInterfaceServices(wrapper);

			if (!this.noDeleteHtmlString) {
				delete this.html;
			}

			this.initPage(wrapper);

			DeskPRO_Window.getMessageBroker().sendMessage('agent.ui.tabinit.' + this.TYPENAME, this);
		}, this);

		var self = this;

		// Standard hook methods
		this.addEvent('activate', this.activate);
		this.addEvent('deactivate', this.deactivate);
		this.addEvent('destroy', this.destroyPage);

		this.init();

		this.addEvent('activate', function() {
			this.clearAlerts();
		}, this);

		this.addEvent('destroy', function() {
			this.scrollHandlers = [];
			if (self.resizerInterval) {
				window.clearInterval(self.resizerInterval);
			}

			if (self.wrapper) {
				self.wrapper.find('.with-scroll-handler').each(function() {
					var sh = $(this).data('scroll_handler');
					if (sh) {
						sh.destroy();
						$(this).data('scroll_handler', null);
					}
				});
				self.wrapper.find('.with-select2').each(function() {
					$(this).select2('destroy');
				});
				self.wrapper.find('textarea.with-redactor').each(function() {
					var obj = $(this).getObject();
					if (obj) {
						$(this).getObject().destroy();
					}
				});

				self.wrapper.data('with-page-fragment', null);
			}

			if (self.destroyObjects) {
				var i;
				for (i = 0; i < self.destroyObjects.length; i++) {
					self.destroyObjects[i].destroy();
				}
				self.destroyObjects = null;
			}

			DeskPRO_Window.getMessageBroker().removeTaggedListeners(self.OBJ_ID);
			if (self.wrapper) {
				self.wrapper.find('.with-handler').each(function() {
					var h = $(this).data('handler');
					if (h) {
						h.destroy();
					}
				});
				self.wrapper.empty();
			}
		});
		this.addEvent('destroy', this.destroy);

		if (this.meta.routeData && this.meta.routeData.routeTriggerEl && this.meta.routeData.toggleOpenClass) {
			this.addEvent('destroy', function() {
				this.meta.routeData.routeTriggerEl.removeClass(this.meta.routeData.toggleOpenClass);
			}, this);
		}
	},

	/**
	 * Empty hook method for children
	 */
	init: function() { },

	/**
	 * Called when the fragment has been activated (comes into view).
	 */
	activate: function() { },

	/**
	 * Called when the fragment is deactivated (hidden from view)
	 */
	deactivate: function() { },

	/**
	 * Register an object that we "own."
	 *
	 * When this page is destroyed, all of these owned objects
	 * are destroyed as well.
	 *
	 * @param obj
	 */
	ownObject: function(obj) {
		if (obj.destroy && this.destroyObjects) {
			this.destroyObjects.push(obj);
		}
	},

	/**
	 * Set metadata about this page.
	 *
	 * @param mixed name Either a string name to use with value, or an object of key/value pairs
	 * @param mixed value Only used if name is a string, the value to set
	 */
	setMetaData: function(name, value) {
		// Assigning multiple values from a hash
		if (value === undefined && typeOf(name) == 'object') {
			this.meta = Object.merge(this.meta, name);
			this.initMetaData();
		} else {
			this.meta[name] = value;
		}
	},

	initMetaData: function() {

	},

	/**
	 * Get a hash of all the metadata.
	 *
	 * @return {Object}
	 */
	getAllMetaData: function() {
		return this.meta;
	},



	/**
	 * Get a specific piece of metadata.
	 *
	 * @param {String} name The name of the data you want
	 * @param mixed default_value The value to return if the metadata is undefined
	 */
	getMetaData: function(name, default_value) {
		if (default_value === undefined) {
			default_value = null;
		}

		if (this.meta[name] === undefined) {
			return default_value;
		}

		return this.meta[name];
	},


	/**
	 * Get a URL pattern
	 */
	getUrl: function(name, vars) {

		if (!this.meta.urls) {
			DP.console.error('Unknown url name %s (no urls set)', name);
			return null;
		}

		if (!this.meta.urls[name]) {
			DP.console.error('Unknown url name %s', name);
			return null;
		}

		var url = this.meta.urls[name];
		if (vars) {
			Object.each(vars, function(v,k) {
				url = url.replace('{'+k+'}', v);
			});
		}

		return url;
	},



	/**
	 * Get the scripts required by this fragment.
	 *
	 * @return {Array}
	 */
	getScripts: function() {
		return this.scripts;
	},



	/**
	 * Get stylesheets required by this fragment
	 *
	 * @return {Array}
	 */
	getStylesheets: function() {
		return this.stylesheets;
	},



	/**
	 * Get the HTML source for this fragment.
	 *
	 * @return {String}
	 */
	getHtml: function() {
		return this.html;
	},



	/**
	 * Should be called after all resources are laoded and after the
	 * HTML is in the dom.
	 *
	 * @param {jQuery} el The wrapper element
	 */
	initPage: function(el) {
		this.wrapper = el;
	},



	/**
	 * Called after the page should be destroyed. Any specific cleanup required can be done
	 * here if for example an element was moved during initPage etc.
	 */
	destroyPage: function() {

	},


	/**
	 * Get an element within this page by ID, using the baseId set in metadata if avail
	 *
	 * @param id
	 */
	getEl: function(id) {
		if (this.meta && this.meta.baseId) {
			id = this.meta.baseId + '_' + id;
		}

		return $('#' + id);
	},


	/**
	 * If this page is part of a tabstrip, return its tab id
	 *
	 * @return {String}
	 */
	getTabId: function() {
		if (this.meta.tabId) {
			return this.meta.tabId;
		}

		return null;
	},

	/**
	 * If this page is part of a tabstrip, return the tab object its
	 * attached to.
	 *
	 * @return {Object}
	 */
	getTab: function() {
		var tabId = this.getTabId();
		if (!tabId) return null;

		return DeskPRO_Window.TabBar.getTab(tabId);
	},


	/**
	 * Activates flashing on the tab to alert of a change or something that requires attention
	 */
	alertTab: function() {
		var tab = this.getTab();
		if (!tab) return;

		DeskPRO_Window.TabBar.alertTab(tab);
	},


	/**
	 * Close this tab
	 */
	closeSelf: function() {
		DeskPRO_Window.removePage(this);
	},


	/**
	 * Sroll to top
	 */
	goTabTop: function() {
		if (this.wrapper) {
			this.wrapper.find('div.layout-content').trigger('goscrolltop');
		}
	},


	/**
	 * Scroll to bottom
	 */
	goTabBottom: function() {
		if (this.wrapper) {
			this.wrapper.find('div.layout-content').trigger('goscrollbottom');
		}
	},

	getAlertId: function() {
		if (this.meta && this.meta.alert_id) {
			return this.meta.alert_id;
		}
		return null;
	},

	clearAlerts: function() {
		var id = this.getAlertId();
		if (!id) {
			return;
		}

		DeskPRO_Window.notifications.removeRowById(id);
		DeskPRO_Window.notifications.removeRowByClass(id);
	},

	destroy: function() {

	}
});
