Orb.createNamespace('DeskPRO.Agent.PageHelper');

DeskPRO.Agent.PageHelper.Popover_Instances = {};

DeskPRO.Agent.PageHelper.Popover = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(options) {
		this.options = {
			/**
			 * How long after this is initialized to wait before
			 * auto-loading the person page in the bg. 0 disables
			 * and loads on-demand.
			 */
			loadTimeout: 0,

			/**
			 * The page to load. False to not use the loader in this class,
			 * you can use setHtml() instead.
			 */
			pageUrl: '',

			/**
			 * Callback method for loading the page instead of using default ajax loader.
			 */
			pageCallback: null,

			/**
			 * The route to load when clicking "move to tab"
			 */
			tabRoute: false,

			/**
			 * Destroy the popover when it closes?
			 */
			destroyOnClose: true,

			overFrom: '#dp_content',

			/**
			 * 'side' or 'over'
			 */
			positionMode: 'side',

			/**
			 * 'top' or 'bottom'
			 */
			sidePosition: 'top'
		};

		DeskPRO.Agent.PageHelper.Popover_Instances[this.OBJ_ID] = this;

		this.setOptions(options);

		/**
		 * The page source if the page isnt initialized yet.
		 * This deferres processing the page fragment until the user wants to
		 * see the page and the source is loaded in the bg
		 */
		this.pageSource = null;

		if (this.options.pageSource) {
			this.pageSource = this.options.pageSource;
			delete this.options.pageSource;
		}

		/**
		 * The page fragment once its initialized
		 */
		this.page = null;

		/**
		 * True if the user has requested to see the page but it hasnt been loaded
		 * yet. After its loaded, this flag is checked to see if the page should
		 * be immediately displayed.
		 */
		this.isWaiting = false;

		// Various popover element handlers
		this.popover = null;
		this.popoverOuter = null;

		if (this.options.loadTimeout) {
			this.autoloadTimeout = window.setTimeout(this._loadPage.bind(this), this.options.loadTimeout);
		}

		this.formString = '';
	},

	_loadPage: function() {

		if (this.isDestroyed) return;

		if (this.options.pageCallback) {
			return this.options.pageCallback(this.setHtml.bind(this));
		}

		if (!this.options.pageUrl) {
			return;
		}

		if (this._isLoading) return;
		this._isLoading = true;

		if (this.autoloadTimeout) {
			window.clearTimeout(this.autoloadTimeout);
			this.autoloadTimeout = null;
		}

		this.loadingAjax = $.ajax({
			dataType: 'text',
			url: this.options.pageUrl,
			type: 'GET',
			context: this,
			cache: false,
			complete: function() {
				this.loadingAjax = null;
				delete this.loadingAjax;
			},
			success: function(html) {
				this._isLoading = false;
				this.setHtml(html);
			}
		});
	},

	setHtml: function(html) {
		this.pageSource = html;
		if (this.isWaiting) {
			this.isWaiting = false;
			this._initFragment();
			this.open();
		}
	},

	_initPopover: function() {

		var self = this;

		if (this._hasInit) return;
		this._hasInit = true;

		this.popoverOuter = $($('#popover_tpl').get(0).innerHTML);
		this.popover = $('.popover-inner', this.popoverOuter).first();

		this.popoverOuter.detach().appendTo('body');

		// We want to contain clicks and such to this layer,
		// so we'll init events on it and then...
		DeskPRO_Window.initInterfaceLayerEvents(this.popoverOuter);

		// ...prevent bubbling so it doesnt activate anything below
		this.popoverOuter.on('click', function(ev) {
			ev.stopPropagation();
		});

		this.updatePositions();

		$('.close', this.popoverOuter).first().on('click', (function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			this.isWaiting = false;

			var ev = {pop: this, cancel: false};
			this.fireEvent('closeTabClick', ev);
			if (ev.cancel) {
				return;
			}

			this.close();
		}).bind(this));

		if (this.options.tabRoute) {
			$('.move-to-tab:first', this.popoverOuter).on('click', (function(ev) {

				ev.preventDefault();
				ev.stopPropagation();

				var doXfer = function() {
					this.isWaiting = false;
					DeskPRO_Window.runPageRoute(self.options.tabRoute);
					self.close(true);
				};

				if (self.hasFormsChanged()) {
					DeskPRO_Window.showConfirm(
						'This will re-load a new form in the tabbed area to the right. You will lose all unsaved changes. Do you want to continue?',
						function() {
							doXfer();
						}
					);
				} else {
					doXfer();
				}
			}).bind(this));
		} else {
			$('.move-to-tab:first', this.popoverOuter).remove();
		}

		// Handle window resizes
		if (this.options.positionMode == 'side') {
			DeskPRO_Window.layout.addEvent('resized', this.updatePositions, this);
		}
	},

	_initFragment: function() {
		if (this.page) return;
		if (!this.pageSource) return;
		if (!this.popover) return;

		var self = this;


		this.page = DeskPRO_Window.createPageFragment(this.pageSource);
		this.page.addEvent('updateUi', this.updatePositions.bind(this));

		var preparedOutput = DeskPRO_Window.prepareWidgetedHtml(this.page.getHtml());
		this.popover.html(preparedOutput.html);
		DeskPRO_Window.runWidgetedJs(this.page, preparedOutput.jsSource, preparedOutput.jsInline);

		this.pageSource = null;

		if (this.page.meta.title) {
			$('h1.tab-title', this.popoverOuter).text(this.page.meta.title);
		}

		this.page.meta.isPopover = true;
		this.page.meta.popover = this;

		this.page.fireEvent('render', [this.popover]);
		this.fireEvent('pageInit', [this, this.page]);

		var foot = $('footer.pop-footer', this.popover);
		if (foot.length) {
			foot.detach().appendTo($('> section', this.popoverOuter));
		}

		var data = self.popover.find('input, select, textarea').serializeArray();
		self.formString = JSON.stringify(data);

		this.updatePositions();
	},

	hasFormsChanged: function(do_resave) {
		var data = this.popover.find('input, select, textarea').serializeArray();
		var newFormString = JSON.stringify(data);
		var ret = false;

		if (this.formString != newFormString) {
			ret = true;
		}

		if (do_resave) {
			this.formString = newFormString;
		}

		return ret;
	},

	updatePositions: function() {

		if (this.isDestroyed) {
			return;
		}

		var changeVis = false;
		if (!this.popoverOuter.is(':visible')) {
			changeVis = true;
			this.popoverOuter.css({
				'visibility': 'hidden',
				'display': 'block'
			});
		}

		var pos = $(this.options.overFrom).offset();
		var top = pos.top - 4;
		var width = pos.left - 9;
		var bottom = 10;
		var height = '';

		var contentH = false;
		var hasHeader = !!($('> section > header', this.popoverOuter).length);
		var hasFooter = !!($('> section > footer', this.popoverOuter).length);
		var scrollContentHeight = false;

		$('.scroll-content', this.popoverOuter).each(function() {
			var $this = $(this);
			var height = $this.height();
			if ($this.data('extra-height')) {
				height += parseInt($this.data('extra-height'), 10);
			}
			if (scrollContentHeight === false || height > scrollContentHeight) {
				scrollContentHeight = height;
			}
		})

		if (scrollContentHeight !== false) {
			contentH = scrollContentHeight;
			if (hasHeader) {
				contentH += 36;
			}
			if (hasFooter) {
				contentH += 45;
			}

			contentH += 31;
		}

		if (hasHeader) $('> section > article', this.popoverOuter).removeClass('no-header');
		else $('> section > article', this.popoverOuter).addClass('no-header');

		if (hasFooter) $('> section > article', this.popoverOuter).removeClass('no-footer');
		else $('> section > article', this.popoverOuter).addClass('no-footer');

		if (contentH < 350) {
			contentH = 350;
		}

		var maxH = $(window).height() - top - 10;

		if (contentH && contentH < maxH) {
			bottom = '';
			height = contentH;
		}

		// Beside
		if (this.options.positionMode == 'side') {
			if (DeskPRO_Window.paneVis.source && DeskPRO_Window.paneVis.list) {
				if (this.options.sidePosition == 'bottom') {
					// Only calc if we dont have a bottom calculated, else it means the thing is full height
					if (!bottom) {
						top = '';
						bottom = 10;
					}
				}

				this.popoverOuter.css({
					'position': 'absolute',
					'z-index': 30001,
					'width': width+2+6, //2px for thi sborder, 6px for the popover border
					'overflow': 'auto',
					'top': top ? top-3 : '',
					'left': 9,
					'bottom': bottom,
					'height': height
				});
			} else {
				width = $(window).width() / 2;
				this.popoverOuter.css({
					'position': 'absolute',
					'z-index': 30001,
					'width': width+2+6, //2px for thi sborder, 6px for the popover border
					'overflow': 'auto',
					'top': top ? top-3 : '',
					'left': 9,
					'bottom': 10,
					'height': height
				});
			}

		// Over
		} else {
			this.popoverOuter.css({
				'position': 'absolute',
				'z-index': 30001,
				'overflow': 'auto',
				top: pos.top - 4,
				left: pos.left + 8,
				right: 3,
				'bottom': bottom,
				'height': height
			});
		}

		if (changeVis) {
			this.popoverOuter.css({
				'display': 'none',
				'visibility': 'visible'
			});
		}
	},

	isOpen: function() {
		if (this.isDestroyed || !this._hasInit) return false;

		if (this.popover && this._isOpen) {
			return true;
		}

		return false;
	},

	open: function() {
		if (this.isDestroyed) return;

		this._initPopover();
		this._initFragment();

		if (!this.page && !this.pageSource) {
			this.isWaiting = true;
			this._loadPage();
		}

		var tabAnchor = $('a.tab-anchor', this.popover);
		tabAnchor.focus();

		// Already open
		if (this.isOpen()) {
			if (this.page && !this.page.IS_ACTIVE) {
				// The initial active after the page has been init
				this.page.fireEvent('activate', [this.page]);
				this.page.fireEvent('popover-open', [this]);
			}
			return;
		}

		// Go through other instances and make sure the others arent open on the same side
		Object.each(DeskPRO.Agent.PageHelper.Popover_Instances, function(inst) {
			if (inst.isOpen() && inst.options.positionMode == this.options.positionMode) {
				inst.close();
			}
		}, this);

		this._isOpen = true;
		this.popoverOuter.show();

		var self = this;
		setTimeout(function() {
			self.updatePositions();
		}, 1000);

		if (this.page) {
			this.page.fireEvent('activate', [this.page]);
			this.page.fireEvent('popover-open', [this]);
		}
	},

	toggle: function() {
		if (this.isOpen()) {
			this.close();
		} else {
			this.open();
		}
	},

	close: function(ignoreForms) {

		if (this.isDestroyed) return;
		if (!this.isOpen()) return;

		if (!ignoreForms && this.hasFormsChanged() && this.page && !this.page.noIgnoreForm) {
			var self = this;
			DeskPRO_Window.showConfirm('Are you sure you want to close the form? Your changes will be lost.', function() {
				self.close(true);
			});
			return;
		}

		this._isOpen = false;

		this.formString = '';

		var ev = {pop: this, cancel: false};
		this.fireEvent('close', ev);
		if (ev.cancel) {
			return;
		}

		this.popoverOuter.hide();

		if (this.options.destroyOnClose && this.hasFormsChanged()) {
			if (this.page) {
				this.page.closeSelf();
			}
		} else {
			if (this.page) {
				this.page.fireEvent('deactivate');
			}
			if (this.page) {
				this.page.fireEvent('popover-closed');
			}
		}
	},

	destroy: function() {

		if (this.isDestroyed) return;

		this.isDestroyed = true;
		if (this.page) {
			this.page.fireEvent('destroy');
			this.page = null;
		}

		if (this.popover) {
			this.popoverOuter.remove();
			this.popover.remove();
		}

		if (this.autoloadTimeout) {
			window.clearTimeout(this.autoloadTimeout);
			this.autoloadTimeout = null;
		}

		if (this.loadingAjax) {
			this.loadingAjax.abort();
			this.loadingAjax = null;
		}

		this.popoverOuter = null;
		this.popover = null;
		this.options = null;

		delete DeskPRO.Agent.PageHelper.Popover_Instances[this.OBJ_ID];

		this.fireEvent('destroy', [this]);
	}
});