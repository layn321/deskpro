Orb.createNamespace('DeskPRO.UI');

DeskPRO.UI.Menu_Instances = {};

/**
 * A simple menu handler.
 *
 * Tip: A single menu can be attached to multiple triggers. You can use
 * getOpenTriggerElement to see which element opened a menu, and using
 * the event handlers you can then give any click events context.
 */
DeskPRO.UI.Menu = new Orb.Class({

	DisableParentCall: true,
	Implements: [Orb.Util.Options, Orb.Util.Events],

	initialize: function(options) {

		// element is the standard UI option name
		// so alias it to menuElement that used in this widget
		if (options && options.element) {
			options.menuElement = options.element;
		}
		if (options && options.trigger) {
			options.triggerElement = options.trigger;
		}

		// Initialize
		this.options = {
			triggerElement: null,
			customClassname: '',
			zIndex: 0,
			menuElement: null,
			objectGroup: 'default',
			subMenuConfig: null,
			initSubMenusNow: false,
			initMenuNow: false,
			parentMenu: null
		};

		this.hasInit = false;
		this.elements = {};
		this.openTriggerEvent = null;

		this.openedTime = null;
		this.cachePosInfo = null;

		this.subMenus = [];
		this.openSubMenuId = null;
		this.parentMenu = null;


		this.objectId = Orb.uuid();

		if (options) this.setOptions(options);

		if (this.options.parentMenu) {
			this.parentMenu = this.options.parentMenu;
			delete this.options.parentMenu;
		}

		if (DeskPRO.UI.Menu_Instances[this.options.objectGroup] === undefined) {
			DeskPRO.UI.Menu_Instances[this.options.objectGroup] = {};
		}
		DeskPRO.UI.Menu_Instances[this.options.objectGroup][this.objectId] = this;

		this._setupMenuElement();

		if (this.options.triggerElement) {
			this.setupTriggerElement($(this.options.triggerElement));
		}

		if (this.options.initMenuNow) {
			this._initMenu();
		}
	},

	_setupMenuElement: function() {
		var self = this;
		var origMenuElement = $(this.options.menuElement);

		if (origMenuElement.data('menu-flag') && origMenuElement.data('menu-flag').indexOf('copy-menu') !== -1) {
			origMenuElement = origMenuElement.clone();
			if (origMenuElement.attr('id')) {
				origMenuElement.attr('id', origMenuElement.attr('id') + '_' + Orb.uuid());
			}
		}

		//------------------------------
		// Set up a bound select box
		//------------------------------

		if (origMenuElement.is('select')) {
			var html = [];
			html.push('<ul class="menu" style="display:none">');

			var selected_text = null;
			var options = $('option', origMenuElement);
			var is_sub = false;
			options.each(function(index, el) {
				el = $(el);
				var has_child = (el.next().text().indexOf('--') !== -1);
				var is_child = (el.text().indexOf('--') !== -1);

				if (!selected_text || el.is(':selected')) {
					selected_text = el.text();
				}

				if (index && !is_child && (has_child || is_sub)) {
					html.push('<li class="sep">');
				}

				if (has_child) {
					is_sub = true;
					html.push('<li class="section-title">' + Orb.escapeHtml(el.text()) + '</li>');
				} else {
					if (!is_child) is_sub = false;
					html.push('<li data-value="' + el.val() + '">' + Orb.escapeHtml(el.text()) + '</li>');
				}
			});

			html.push('</ul>');

			var menuElement = $(html.join('')).appendTo('body');
			this.options.menuElement = menuElement;

			origMenuElement.css({
				'display': 'none'
			});

			if (!this.options.triggerElement) {
				var text = selected_text;
				if (!text.length) text = self.options.noValText || 'Choose...';
				var spanEl = this.options.triggerElement = $('<span class="menu-trigger">' + Orb.escapeHtml(text) + '</span>').insertAfter(origMenuElement);

				this.addEvent('itemClicked', function(ev) {
					var itemEl = $(ev.itemEl);
					var text = itemEl.text().trim();
					if (!text.length) text = self.options.noValText || 'Choose...';
					else {
						var prefix = $(ev.itemEl).data('prefix');
						if (prefix) text = prefix + text;
					}

					spanEl.text(text);
				});
			}

			this.addEvent('itemClicked', function(ev) {
				var itemEl = $(ev.itemEl);
				var value = itemEl.data('value');

				if (value != origMenuElement.val()) {
					origMenuElement.val(value);
					origMenuElement.change();
				}
			});

			if (spanEl) {
				origMenuElement.on('change', function() {
					var opt = $('option:selected', this);
					var text = opt.text().trim();
					if (!text.length) text = self.options.noValText || 'Choose...';
					else {
						var prefix = $(this).data('prefix');
						if (prefix) text = prefix + text;
					}

					spanEl.text(text);
				});
			}
		}
	},


	/**
	 * Alias for isMenuOpen.
	 */
	isOpen: function() {
		return this.isMenuOpen();
	},


	/**
	 * Check to see if the overlay is currently open.
	 */
	isMenuOpen: function() {
		if (!this.hasInit) return false;
		return this.elements.wrapper.is(':visible');
	},


	/**
	 * Get the event data that triggered the last menu opener.
	 */
	getOpenTriggerEvent: function() {
		return this.openTriggerEvent;
	},



	/**
	 * Get the element who triggered the opening of the menu.
	 */
	getOpenTriggerElement: function() {
		if (!this.openTriggerEvent) return null;

		return this.openTriggerEvent.target;
	},


	/**
	 * Standard naming for UI elements. Alias for openMenu.
	 *
	 * @param event
	 */
	open: function(event) {
		return this.openMenu(event);
	},


	/**
	 * Display the menu. If the event passed is a mouse-generated event,
	 * then the menu will be displayed where the click took place. If it's
	 * some other event, then the menu will be displayed near the event target.
	 *
	 * @param {jQuery.Event}
	 */
	openMenu: function(event) {
		if (!this._initMenu()) {
			return;
		}

		if (this.isMenuOpen()) {
			return;
		}

		// Close all other instances (only matters for parent instances)
		if (!this.parentMenu) {
			Object.each(DeskPRO.UI.Menu_Instances[this.options.objectGroup], function(v, k) {
				if (v.isMenuOpen()) {
					v.closeMenu();
				}
			});
		}

		this.openTriggerEvent = event;

		if (event && event.stopPropagation) {
			// Stop bubbling up, which would call the document
			// click and immediately close the menu
			event.stopPropagation();
		}

		var eventData = { menu: this, cancelOpen: false };

		if (event && event.customEvents) {
			event.customEvents.fireEvent('beforeMenuOpened', eventData);
		}

		if (!eventData.noFireEvent) {
			this.fireEvent('beforeMenuOpened', eventData);
		}

		if (eventData.cancelOpen) {
			this.openTriggerEvent = null;
			return;
		}

		if (!this.options.zIndex) {
			this.options.zIndex = Orb.findHighestZindex()+1;
		}

		var target;

		if (this.options.triggerElement && this.options.triggerElement[0]) {
			target = this.options.triggerElement;
		} else {
			if (event) {
				target = event.target;
			} else {

			}
		}

		if (target) {
			target = $(target);
			if (!target.is('.menu-fitted')) {
				target = target.closest('.menu-fitted');
			}

			if (!target.is('.menu-fitted')) {
				target = null;
			}
		}

		if (!target && !event && this.options.triggerElement) {
			target = $(this.options.triggerElement);
		}

		if (target) {
			if (target.data('menu-button')) {
				target = target.find(target.data('menu-button'));
			}

			this.targetEl = target;
			target = $(target);
			var tPos = target.offset();
			var tWidth = target.outerWidth();
			var tHeight = target.outerHeight();

			var top = tPos.top + tHeight - 1; //-1 to overlap a bottom border
			var left = tPos.left; // +2 for borders
			var minW = tWidth + 15;

			this.elements.wrapperOuter.css('min-width', minW).addClass('fitted');

			if (this.elements.wrapperOuter.outerWidth() + left > $(document).width()) {
				left = (tPos.left + tWidth) - this.elements.wrapperOuter.outerWidth();
			}

			// Overlapper to erase the line
			this.lineRemover = $('<div />').addClass('menu-fitted-line');
			this.lineRemover.css({
				position: 'absolute',
				width: tWidth - 2,
				height: 4,
				top: tPos.top + tHeight - 4,
				left: tPos.left + 1,
				'z-index': this.options.zIndex+10
			}).appendTo('body');

		} else {

			// If this is a submenu being re-hovered over to re-open it,
			// then we dont have to figure out position stuff again because we already did
			// So we can use the cached info to make it a bit snappier
			if (this.cachePosInfo && this.openedTime && this.parentMenu && this.parentMenu.openedTime && this.parentMenu.openedTime <= this.openedTime) {
				var left  = this.cachePosInfo.left;
				var top   = this.cachePosInfo.top;
				var point = this.cachePosInfo.point;
			} else {
				var width = this.elements.wrapperOuter.outerWidth();
				var height = this.elements.wrapperOuter.outerHeight();

				var pageWidth = $(document).width();
				var pageHeight = $(document).height();

				// If this is a submenu and the parent is open ...
				if (this.parentMenu !== null && this.parentMenu.isMenuOpen()) {

					var pageX = this.options.parentMenuItem.offset().left + this.options.parentMenuItem.outerWidth()-4;
					var pageY = this.options.parentMenuItem.offset().top;

					// Position to the left if theres no room
					if (pageX+width > pageWidth) {
						pageX = this.options.parentMenuItem.offset().left - width;
					}

				// If we have a target (usually a button)
				// we can try a standard spot so it looks a bit cleaner when opening
				} else if (event && event.target && !$(event.target).is('.with-menu-click-position')) {

					var pageX = $(event.target).offset().left + ($(event.target).width() / 2);
					var pageY = $(event.target).offset().top + ($(event.target).outerHeight()) + 2;

				// If its a click event...
				} else if (event && event.pageX) {
					var pageX = event.pageX;
					var pageY = event.pageY;

				// Otherwise we have no choice but to use the element...
				} else if (event && event.target) {
					var pageX = $(event.target).offset().left;
					var pageY = $(event.target).offset().top;
				} else {

				}

				var point = true;

				// Determine which way to open the menu,
				// We do this so the menu doesn't go off-screen if
				// its near the edge
				if (pageX+width < pageWidth) {
					var left = pageX;
				} else {
					var left = pageWidth - width - 4;
					point = false;
				}

				if (pageY+height < pageHeight) {
					var top = pageY;
				} else {
					var top = pageHeight - height - 4;
					point = false;
				}

				if (top < 0) {
					top = 5;
				}

				if (point && left >= 10) {
					left -= 10;
				}

				this.cachePosInfo = {
					left: left,
					top: top,
					point: point
				};
			}
		}

		// If we have a shim, position it.
		// We might not if this is a submenu
		if (this.elements.shim) {
			this.elements.shim.css({
				'z-index': this.options.zIndex+1,
				'position': 'absolute',
				'top': 0,
				'right': 0,
				'bottom': 0,
				'left': 0,
				'background': 'transparent'
			}).show();
		}

		if (point) {
			this.elements.wrapperOuter.addClass('with-point');
		} else {
			this.elements.wrapperOuter.removeClass('with-point');
		}

		if ((top + this.elements.wrapperOuter.height()) > ($(window).height() - 3)) {
			this.elements.wrapper.addClass('with-scrolldown');
			this.elements.wrapperInner.css('max-height', $(window).height() - top - 3);
		} else {
			this.elements.wrapper.removeClass('with-scrolldown with-scrollup');
			this.elements.wrapperInner.css('max-height', 'auto');
		}

		this.elements.wrapperOuter.css({
			'z-index': this.options.zIndex+2,
			'position': 'absolute',
			'top': top,
			'left': left
		});
		this.elements.wrapperOuter.show();

		this.openedTime = new Date();

		if (event && event.customEvents) {
			event.customEvents.fireEvent('menuOpened', { menu: this });
		}

		if (!eventData.noFireEvent) {
			this.fireEvent('menuOpened', { menu: this });
		}

		if (this.targetEl) {
			this.targetEl.addClass('menu-open');
		}
	},


	/**
	 * Standard naming for UI elements. Alias for closeMenu.
	 *
	 * @param event
	 */
	close: function() {
		return this.closeMenu();
	},


	/**
	 * Closes the menu
	 */
	closeMenu: function() {
		if (!this.isMenuOpen()) return false;

		var eventData = { menu: this, cancelClose: false };

		if (this.openTriggerEvent && this.openTriggerEvent.customEvents) {
			this.openTriggerEvent.customEvents.fireEvent('beforeMenuClosed', eventData);
		}

		if (!eventData.noFireEvent) {
			this.fireEvent('beforeMenuClosed', eventData);
		}

		if (eventData.cancelClose) return false;

		this._closeSubMenu();

		if (this.elements.shim) {
			this.elements.shim.hide();
		}

		if (this.parentMenu) {
			// no fade for submenus
			this.elements.wrapperOuter.hide();
		} else {
			this.elements.wrapperOuter.fadeOut(200);
		}

		if (this.openTriggerEvent && this.openTriggerEvent.customEvents) {
			this.openTriggerEvent.customEvents.fireEvent('menuClosed', { menu: this });
		}

		if (!eventData.noFireEvent) {
			this.fireEvent('menuClosed', { menu: this });
		}

		this.openTriggerEvent = null;

		if (this.targetEl) {
			this.targetEl.removeClass('menu-open');
			this.targetEl = null;
		}

		if (this.lineRemover) {
			this.lineRemover.remove();
			this.lineRemover = null;
		}

		return true;
	},


	/**
	 * Fired when a menu item is clicked.
	 */
	_menuItemClicked: function(event) {

		var eventData = { menu: this, event: event, itemEl: event.currentTarget, cancelClose: false };

		// These elements arent selectable
		if ($(eventData.itemEl).is('.sep, .disabled, .section-title')) {
			return false;
		}

		// "element" items arent actual menu items, they some UI thing so dont close for them
		if ($(eventData.itemEl).is('.elm, .sep, .section-title')) {
			eventData.cancelClose = true;
		}

		if (this.openTriggerEvent && this.openTriggerEvent.customEvents) {
			this.openTriggerEvent.customEvents.fireEvent('itemClicked', eventData);
		}

		if (!eventData.noFireEvent) {
			this.fireEvent('itemClicked', eventData);
		}

		event.stopPropagation();

		// Pass it to the parent handler by default
		if (this.parentMenu && this.parentMenu.isMenuOpen()) {
			this.parentMenu._menuItemClicked(event);
		}

		if (eventData.cancelClose) return;

		this.closeMenu();
	},



	/**
	 * Fired when a menu item is mouseover
	 */
	_menuItemMouseover: function(event) {

		var eventData = { menu: this, event: event, itemEl: event.currentTarget };

		if (this.openTriggerEvent && this.openTriggerEvent.customEvents) {
			this.openTriggerEvent.customEvents.fireEvent('itemMouseover', eventData);
		}

		if (!eventData.noFireEvent) {
			this.fireEvent('itemMouseover', eventData);
		}

		event.stopPropagation();

		var itemEl = $(eventData.itemEl);
		var subMenuId = itemEl.data('submenu-id');

		if (this.openSubMenuId == subMenuId) {
			return;
		}

		this._closeSubMenu();

		if (subMenuId === undefined) {
			return;
		}

		var subMenu = this.subMenus[subMenuId];
		subMenu._initMenu();
		subMenu.openMenu(this.openTriggerEvent || event);
		this.openSubMenuId = subMenuId;
		itemEl.addClass('hover');
	},



	/**
	 * Close any open submenu
	 */
	_closeSubMenu: function() {
		if (this.openSubMenuId !== null) {
			this.subMenus[this.openSubMenuId].closeMenu();
			this.subMenus[this.openSubMenuId].options.parentMenuItem.removeClass('hover');
			this.openSubMenuId = null;
		}
	},



	/**
	 * Init the menu by moving the menu list and created the required wrapper elements.
	 */
	_initMenu: function () {
		var self = this;

		if (this.hasInit) return true;
		this.hasInit = true;

		// We dont need a shim if we have a parent, because we'll the parents
		// shim is enough to do whats needed
		if (!this.parentMenu) {
			this.elements.shim = $('<div />').hide().appendTo('body');
			this.elements.shim.on('click', (function (ev) {
				// When we close a menu by clicking off,
				// lets stop proagation so the click doesn't
				// inadvertantly activate something else.
				if (this.closeMenu()) {
					ev.stopPropagation();
				}
			}).bind(this));
		}

		this._initWrapperElements();

		this.elements.list = $(this.options.menuElement);
		this.elements.list.detach().show().appendTo(this.elements.wrapper);

		if (this.options.subMenuConfig) {
			var subMenuConfig = this.options.subMenuConfig;
		} else {
			var subMenuConfig = {};
		}

		$('li', this.elements.list[0]).live('click', this._menuItemClicked.bind(this));
		$('li', this.elements.list[0]).live('mouseover', this._menuItemMouseover.bind(this));

		// Copy referenced submenus into the source
		$('> li[data-submenu-selector]', this.elements.list[0]).each((function(i,el) {
			var subMenuEl = $($(el).data('submenu-selector')).first();
			if (subMenuEl.length) {
				subMenuEl = subMenuEl.clone();
				subMenuEl.data('menu-flag', '');
				subMenuEl.attr('menu-flag', '');
				subMenuEl.attr('id', '');
				subMenuEl.addClass('submenu');

				if ($(el).data('submenu-add-action')) {
					subMenuEl.data('action', $(el).data('submenu-add-action'))
						.attr('data-action', $(el).data('submenu-add-action'));
				}

				subMenuEl.appendTo(el);
				$(el).data('submenu-selector', '').attr('submenu-selector', '');
			}
		}).bind(this));

		var subs = $('> li > ul.submenu', this.elements.list[0]);

		// Set up mouseover events and submenus if we detect any
		if (subs.length) {
			subs.each((function (i, el) {

				var subMenuEl = $(el);
				el = $(subMenuEl.parent());

				// Not using live because specific mouseover events are a bit snappier
				el.on('mouseover', this._menuItemMouseover.bind(this));

				subMenuEl.hide();

				var subMenuId = this.subMenus.length;
				el.addClass('with-submenu');
				el.data('submenu-id', subMenuId);

				//subMenuEl.hide();
				subMenuConfig.parentMenu = this;
				subMenuConfig.subMenuId = subMenuId;
				subMenuConfig.parentMenuItem = el;
				subMenuConfig.menuElement = subMenuEl;
				if (this.options.zIndex) {
					subMenuConfig.zIndex = this.options.zIndex+10;
				}
				var subMenu = new DeskPRO.UI.Menu(subMenuConfig);
				this.subMenus.push(subMenu);

				el.prepend($('<span class="arrow">&#x25B8;</span>'));

				if (this.options.initSubMenusNow) {
					subMenu._initMenu();
				}
			}).bind(this));
		}

		this.elements.wrapper.find('.deskpro-menu-scrolldown').on('click', function(ev) {
			ev.preventDefault();
			ev.stopImmediatePropagation();

			var newpos = self.elements.wrapperInner.scrollTop() + 30;
			var max = self.elements.wrapper.height() - self.elements.wrapperInner.height() + 30;
			if (newpos >= max) {
				self.elements.wrapper.removeClass('with-scrolldown');
				newpos += 10;
			}

			self.elements.wrapperInner.scrollTop(newpos);
			self.elements.wrapper.addClass('with-scrollup');
		});

		this.elements.wrapper.find('.deskpro-menu-scrollup').on('click', function(ev) {
			ev.preventDefault();
			ev.stopImmediatePropagation();

			var newpos = self.elements.wrapperInner.scrollTop() - 30;
			if (newpos < 0) newpos = 0;
			self.elements.wrapperInner.scrollTop(newpos);
			self.elements.wrapper.addClass('with-scrolldown');
			if (newpos == 0) {
				self.elements.wrapper.removeClass('with-scrollup');
			}
		});

		this.fireEvent('menuInit', { menu: this });

		return true;
	},



	/**
	 * Creates the relevant wrapper elements needed for the menu. Certain designs might need different
	 * structures, so it's easy to subclass this class and override just this method.
	 *
	 * Required elements: wrapperOuter which has its display toggled, and wrapper which is where the list is appended.
	 */
	_initWrapperElements: function() {
		this.elements.wrapperOuter = $('<div class="deskpro-menu-outer '+this.options.customClassname+'" style="display:none" />');
		this.elements.wrapperOuter.appendTo('body');

		this.elements.wrapperInner = $('<div class="deskpro-menu-inner '+this.options.customClassname+'" />');
		this.elements.wrapperInner.appendTo(this.elements.wrapperOuter);

		this.elements.wrapper = $('<div class="deskpro-menu '+this.options.customClassname+'"><div class="deskpro-menu-scrollup"></div><div class="deskpro-menu-scrolldown"></div>');
		this.elements.wrapper.appendTo(this.elements.wrapperInner);
	},



	/**
	 * Get the main ul list tag with the menu.
	 *
	 * @return jQuery
	 */
	getListElement: function() {
		// Both of these should refer to the same element
		// but incase after init the list was changed somehow
		// with an event etc, we'll use the one from elements if its there

		if (this.elements.list) {
			return this.elements.list;
		} else {
			return this.options.menuElement;
		}
	},



	/**
	 * Set up a click trigger on an element (or elements).
	 *
	 * @param mixed el A selector, an element, or a jQuery collection
	 */
	setupTriggerElement: function(el) {
		el = $(el);

		el.on('click', (function (ev) {
			ev.preventDefault();
			this.openMenu(ev);
		}).bind(this));
	},


	/**
	 * Get the wrapper element for the menu
	 *
	 * @return {jQuery}
	 */
	getWrapper: function() {
		return $(this.elements.wrapperOuter);
	},


	/**
	 * Destroy this overlay and all of its supporting elements.
	 */
	destroy: function() {

		this.closeMenu();

		if (this.elements && this.elements.shim) {
			this.elements.shim.remove();
		}

		if (this.elements && this.elements.wrapperOuter) {
			this.elements.wrapperOuter.remove();
		}

		if (this.options.menuEl) {
			this.options.menuEl.remove();
		}

		delete DeskPRO.UI.Menu_Instances[this.options.objectGroup][this.objectId];

		Array.each(this.subMenus, function(menuInfo) {
			if (!menuInfo.OBJ_DESTROYED) {
				menuInfo.destroy();
			}
		});

		this.subMenus = [];
	}
});
