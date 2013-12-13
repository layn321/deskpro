Orb.createNamespace('DeskPRO.UI.Select');

/**
 * @type {Orb.Class}
 */
DeskPRO.UI.Select.Menu = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(widget, options) {
		this.widget  = widget;
		this.hasInit = false;
		this._isOpen = false;

		this.setOptions(options);

		this._doInitialize();
	},

	/**
	 * Get the main widget
	 *
	 * @returns {DeskPRO.UI.Select.Widget}
	 */
	getWidget: function() {
		return this.widget;
	},

	_doInitialize: function() { },
	_doInitMenu: function() { },
	_doOpenMenu: function() { },
	_doCloseMenu: function() { },
	_doDestroy: function() { },

	initMenu: function() {
		if (this.hasInit) return;
		this.hasInit = true;
		this._doInitMenu();
	},

	isOpen: function() {
		return this._isOpen;
	},

	open: function() {
		if (this._isOpen) return;
		this._isOpen = true;
		this.initMenu();
		this._doOpenMenu();
		this.fireEvent('opened');
	},

	close: function() {
		if (!this._isOpen) return;
		this._isOpen = false;
		this._doCloseMenu();
		this.fireEvent('closed');
	},

	destroy: function() {
		this._doDestroy();
	}
});