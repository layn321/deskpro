Orb.createNamespace('DeskPRO.UI.Select');

/**
 * The PageWidget is the visible element on the page. Typically this is a button or rendered
 * version of the selected value and accepts a click to open the SelectWidget.
 *
 * The PageWidget is main page controller that connects a select box with the menu.
 *
 * There are a few standard classnames in the widget:
 * - trigger-open-widget: Click events on these elements will open the SelectWidget
 *
 * @type {Orb.Class}
 */
DeskPRO.UI.Select.Widget = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	/**
	 * @param {jQuery} $select
	 * @param {Object} options
	 */
	initialize: function($select, options) {
		var self = this;

		this.$select    = $select;
		this.$el        = null;
		this.selections = [];
		this.isMulti    = this.$select.is('[multiple]');
		this.menu       = null;

		this.options = {
			autoRender: true,
			menu: null
		};

		this.setOptions(options);

		if (this.options.autoRender) {
			this.renderWidget();
		}

		self.selections = [];
		this.$select.find('option').each(function() {
			var opt = $(this);
			var uid = Orb.uuid();

			opt.data('dp-uid', uid);
			opt.addClass(uid);

			if (opt.is(':selected')) {
				self.selections.push(uid);
			}
		});

		this._doInitialize();
	},

	// Required: Get the menu widget
	_doGetmenu: function() { },

	// Required: Create the page widget
	_doRenderWidget: function() {},

	// Optional: Hook into initialize
	_doInitialize: function() {},

	// Optional: Hook into post render
	_doInitWidget: function() {},

	// Optional: Hook into post-changed events
	_doChanged: function() { },

	// Optional: Hook into post-menu-created
	_doMenuInit: function() { },

	// Optional: Hook into post-destroy
	_doDestroy: function() { },

	/**
	 * Renders the new widget element into place
	 *
	 * @private
	 */
	renderWidget: function() {
		if (this.$el) {
			return;
		}

		var self = this;

		this.$el = this._doRenderWidget();

		var evData = {
			select: this.$select,
			el: this.$el,
			pageWidget: this
		};
		this.fireEvent('renderWidget', [evData]);

		// If the renderWidget event didnt place the element,
		// place it after the select automatically
		if (this.$el.get(0).parentNode) {
			this.$select.after(this.$el);
		}
		this.$select.hide();

		this.$el.find('.trigger-open-menu').each(function() {
			$(this).on('click', function(ev) {
				Orb.cancelEvent(ev);
				self.getMenu().open(ev);
			});
		});
		if (this.$el.hasClass('trigger-open-menu')) {
			this.$el.on('click', function(ev) {
				Orb.cancelEvent(ev);
				self.getMenu().open(ev);
			});
		}

		self.selections = [];
		this.$select.find('option').each(function() {
			var opt = $(this);
			var uid = Orb.uuid();

			opt.data('dp-uid', uid);
			opt.addClass(uid);

			if (opt.is(':selected')) {
				self.selections.push(uid);
			}
		});

		this._doInitWidget();
		this.fireEvent('initWidget', [evData]);
	},


	/**
	 * Gets the menu controller. If its not yet initiaeted it will be now.
	 *
	 * @returns {DeskPRO.UI.Select.Menu}
	 */
	getMenu: function() {
		if (this.menu) {
			return this.menu;
		}

		if (this.options.menu) {
			if (typeOf(this.options.menu) == 'function') {
				this.menu = this.options.menu();
			}
		}

		if (!this.menu) {
			this.menu = this._doGetmenu();
		}

		this._doMenuInit();

		return this.menu;
	},

	/**
	 * Gets the root select element
	 *
	 * @return {jQuery}
	 */
	getSelect: function() {
		return this.$select;
	},


	/**
	 * Gets the page element. If it hasnt been rendered yet, it will be rendered now.
	 *
	 * @returns {jQuery}
	 */
	getEl: function() {
		this.renderWidget();
		return this.$el;
	},


	/**
	 * Get a collection of selected option elements
	 *
	 * @returns {jQuery}
	 */
	getSelectedOptions: function() {
		if (!this.selections.length) {
			return $([]);
		}

		var selectedOpts = [];
		var options = this.$select.find('option');

		Array.each(this.selections, function(uid) {
			var opt = options.filter('.' + uid);
			if (opt[0]) {
				selectedOpts.push(opt.get(0));
			}
		});

		return $(selectedOpts);
	},


	/**
	 * Returns current value. If this is a multi-select then only the first value is returned (use getValuesArray for all of them).
	 * A null is returned for no selection.
	 *
	 * @return {String}
	 */
	getValue: function() {
		var selected = this.getSelectedOptions();
		if (!selected[0]) {
			return null;
		}

		return selected.first().val();
	},


	/**
	 * Returns all current selected options values.
	 *
	 * @return {Array}
	 */
	getValuesArray: function() {
		var values = [];

		this.getSelectedOptions().each(function() {
			values.push($(this).val());
		});

		return values;
	},


	/**
	 * Return all currently selected options uids.
	 *
	 * @returns {Array}
	 */
	getSelectedUids: function() {
		var values = [];

		this.getSelectedOptions().each(function() {
			values.push($(this).data('dp-uid'));
		});

		return values;
	},


	/**
	 * Returns a single comma-separated list of alpha-ordered uids.
	 * This is generally used to compare selections to see if an event changed selections.
	 *
	 * @return {String}
	 */
	getSelectedUidsString: function() {
		var ids = this.getSelectedUids().sort().join(',');
		return ids;
	},


	/**
	 * @returns {Integer}
	 */
	countSelectedOptions: function() {
		return this.selections.length;
	},


	/**
	 * @param {jQuery} opts
	 * @param {Boolean} add  Add to selections rather than reset
	 */
	setSelectedOptions: function(opts, add) {
		var idsBefore = this.getSelectedUidsString();
		var self = this;
		this.selections = [];

		if (!add) {
			this.$select.find('option').prop('selected', false);
		}

		opts.each(function() {
			$(this).prop('selected', true);
			self.selections.push($(this).data('dp-uid'));
		});

		var idsAfter = this.getSelectedUidsString();

		if (idsBefore != idsAfter) {
			this._doChanged();
			this.$select.trigger('change');
			this.fireEvent('changed');
		}
	},


	/**
	 * Syncs selected options from the select box. Use this is you have manually
	 * modified selected options outside of this option.
	 */
	syncFromSelect: function() {
		var selected = this.$select.find('option').filter(':selected');
		this.setSelectedOptions(selected);
	},


	/**
	 * Set selections based on option values
	 *
	 * @param {Array} values
	 * @param {Boolean} add  Add to selections rather than reset
	 */
	setSelectedValues: function(values, add) {
		var idsBefore = this.getSelectedUidsString();

		var self = this;
		this.selections = [];

		var opts = this.$select.find('option');

		if (!add) {
			opts.prop('selected', false);
		}

		opts.each(function() {
			var optVal = $(this).val();
			var selected = false;

			if (typeOf(values) == 'array') {
				Array.each(values, function(val) {
					if (optVal == val) {
						selected = true;
						return false;
					}
				});
			} else {
				if (optVal == values) {
					selected = true;
				}
			}

			if (selected) {
				$(this).prop('selected', true);
				self.selections.push($(this).data('dp-uid'));
			}
		});

		var idsAfter = this.getSelectedUidsString();

		if (idsBefore != idsAfter) {
			this._doChanged();
			this.$select.trigger('change');
			this.fireEvent('changed');
		}
	},


	/**
	 * Set selections based on option uids
	 *
	 * @param {Array} values
	 * @param {Boolean} add  Add to selections rather than reset
	 */
	setSelectedUids: function(values, add) {
		var idsBefore = this.getSelectedUidsString();

		var self = this;
		this.selections = [];

		var opts = this.$select.find('option');

		if (!add) {
			opts.prop('selected', false);
		}

		Array.each(values, function(uid) {
			opts.filter('.' + uid).prop('selected', true);
			self.selections.push(uid);
		});

		var idsAfter = this.getSelectedUidsString();

		if (idsBefore != idsAfter) {
			this._doChanged();
			this.$select.trigger('change');
			this.fireEvent('changed');
		}
	},


	/**
	 * Unselect an option element
	 *
	 * @param {HTMLElement} opt
	 */
	unselectOption: function(opt) {
		opt = $(opt);
		opt.prop('selected', false);

		var uid = opt.data('dp-uid');
		var uidPos = this.selections.indexOf(uid);
		if (uidPos !== -1) {
			this.selections = this.selections.splice(uidPos, 1);

			this._doChanged();
			this.$select.trigger('change');
			this.fireEvent('changed');
		}
	},


	/**
	 * Unselect a specific value. If multiple options of a single value exist, then they are all unselected
	 *
	 * @param {String} value
	 */
	unselectValue: function(value) {
		var didChange = false;
		var self = this;
		this.getSelectedOptions().each(function() {
			var opt = $(opt);

			if (opt.val() == value) {
				opt.prop('selected', false);

				var uid = opt.data('dp-uid');
				var uidPos = self.selections.indexOf(uid);
				if (uidPos !== -1) {
					self.selections = self.selections.splice(uidPos, 1);
					didChange = true;
				}
			}
		});

		if (didChange) {
			this._doChanged();
			this.$select.trigger('change');
			this.fireEvent('changed');
		}
	},


	/**
	 * Unselect a specific uid.
	 *
	 * @param {String} uid
	 */
	unselectUid: function(uid) {
		var uidPos = this.selections.indexOf(uid);
		if (uidPos !== -1) {
			this.selections = this.selections.splice(uidPos, 1);
			this.$select.find('.' + uid).prop('selected', false);

			this._doChanged();
			this.$select.trigger('change');
			this.fireEvent('changed');
		}
	},


	/**
	 * @param {String} uid
	 * @returns {Boolean}
	 */
	isUidSelected: function(uid) {
		return this.selections.indexOf(uid) !== -1;
	},


	/**
	 * @param {String} value
	 * @return {Boolean}
	 */
	isValueSelected: function(value) {
		var is = false;
		this.getSelectedOptions().each(function() {
			if ($(this).val() == value) {
				is = true;
				return false;
			}
		});

		return is;
	},


	/**
	 * Destroys this object
	 */
	destroy: function() {
		if (this.menu) {
			this.menu.destroy();
		}

		this._doDestroy();
	}
});