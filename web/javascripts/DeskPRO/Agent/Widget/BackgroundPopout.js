Orb.createNamespace('DeskPRO.Agent.Widget');

DeskPRO.Agent.Widget.BackgroundPopout = new Orb.Class({

	Implements: [Orb.Util.Options, Orb.Util.Events],

	initialize: function(options) {
		this.options = {
			/**
			 * The URL that'll load the newticket page
			 */
			loadUrl: null,

			/**
			 * The route to load when clicking on move to tab
			 */
			tabRoute: null,

			/**
			 * The initial timeout before we load the newticket tpl
			 * in the background
			 */
			initialTimeout: 12000, // 12 seconds

			/**
			 * Periodically update the template to account for changes
			 * (usergroups/companies? new custom fields?)
			 */
			periodicalTimeout: 600000, // 10 minutes

			/**
			 * Auto-start the timeout timer to load in the bg
			 */
			autostart: true,

			/**
			 * Maximum amount of jitter, to prevent all popouts from triggering
			 * simultaneously.
			 */
			maxJitter: 2000
		};

		this.setOptions(options);

		/**
		 * The latest Page code
		 */
		this.template = null;

		/**
		 * Any active ajax request
		 */
		this.xhr = null; //active ajax

		/**
		 * The current timeout
		 */
		this.timeout = null;

		/**
		 * The currently created popover
		 */
		this.pop = null;

		/**
		 * True when the template has been reset
		 */
		this.doReset = false;

		/**
		 * Next time the window is opened, the request will be sent with these params.
		 *
		 * @type {null}
		 */
		this.nextParams = null;

		if (this.options.autostart) {
			this.startTimeout();
		}
	},


	/**
	 * Start the auto-update timer
	 */
	startTimeout: function() {
		var t;

		if (this.timeout) return;

		if (this.template) {
			t = this.options.periodicalTimeout;
		} else {
			t = this.options.initialTimeout;
		}

		t += Math.random()*this.options.maxJitter;

		this.timeout = window.setTimeout(this.loadTemplate.bind(this, null), t);
	},


	/**
	 * Reloads the template
	 */
	loadTemplate: function(callback) {

		DP.console.debug('[BackgroundPopout] Loading: %s', this.options.loadUrl);
		if (this.xhr) {
			return;
		}

		if (this.timeout) {
			window.clearTimeout(this.timeout);
			this.timeout = null;
		}

		this.xhr = $.ajax({
			url: this.options.loadUrl,
			data: this.nextParams || null,
			type: 'GET',
			dataType: 'html',
			context: this,
			errorDp: function() {
				this.template = null;
				this.destroyPop();
				this.close();
				this.startTimeout();
			},
			success: function(html) {
				this.nextParams = null;
				this.template = html;

				if (callback) {
					if (typeof callback != 'function') {
						DP.console.error("Not a valid callback: %o", callback);
						return;
					}
					callback(html);
				}
			},
			complete: function() {
				this.xhr = null;
				this.startTimeout();
			}
		});
	},


	/**
	 * Call when you know the template is stale.
	 */
	invalidateTemplate: function() {
		this.loadTemplate();
	},


	/**
	 * Get the template
	 *
	 * @return {String}
	 */
	getTemplate: function() {
		return this.template;
	},


	/**
	 * Opens the page in the popout
	 */
	open: function(callback) {

		var self = this;
		var withNextParams = false;

		if (this.nextParams) {
			withNextParams = true;
			this.clear();
		}

		if (this.options.tabRoute && !DeskPRO_Window.paneVis.list) {
			DeskPRO_Window.runPageRoute(this.options.tabRoute);
			return;
		}

		if (this.pop) {
			if (this.doReset) {
				this.pop.setHtml(this.getTemplate());
				this.doReset = false;
			}
			this.pop.open();
			if (callback && this.pop.page) {
				callback(this.pop.page);
			}
			return;
		}

		this.doReset = false;

		var self = this;
		var pop = new DeskPRO.Agent.PageHelper.Popover({
			tabRoute: this.options.tabRoute,
			onPageInit: function(pop, page) {
				page.addEvent('closeSelf', function(ev) {
					ev.cancel = true;
					self.clear();
				});

				if (callback) {
					callback(page);
				}
			}
		});

		var tpl = this.getTemplate();
		if (tpl) {
			pop.setHtml(tpl);
		} else {
			this.loadTemplate(function(html) {
				pop.setHtml(html);

				if (withNextParams) {
					self.template = null;
				}
			});
		}

		this.pop = pop;
		pop.open();
	},

	isOpen: function() {
		if (!this.pop) return false;
		if (this.pop.isOpen()) return true;
		return false;
	},

	toggle: function() {
		if (!this.pop) {
			this.open();
			return;
		}
		this.pop.toggle();
	},

	close: function() {
		if (!this.pop) return;
		this.pop.close();
	},

	/**
	 * Clears the currently loaded page (it'll be reloaded next time this is opened)
	 * Same as desotryPop except the template is also removed, so it means
	 * a new ajax request to fetch the page is needed.
	 */
	clear: function() {
		if (this.pop) {
			this.pop.destroy();
			this.pop = null;
		}
		this.template = null;
	},


	destroyPop: function() {
		if (!this.pop) return;
		this.pop.destroy();
		this.pop = null;
		this.template = null;
	}
});
