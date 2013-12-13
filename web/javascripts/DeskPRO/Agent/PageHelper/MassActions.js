Orb.createNamespace('DeskPRO.Agent.PageHelper');

DeskPRO.Agent.PageHelper.MassActions = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(page, options)  {
		this.page = page;

		this.options = {
			/**
			 * The object that will handle updating the actual
			 * list for the user
			 */
			viewHandler: null,

			/**
			 * The selection bar we can hook into to figure out
			 * when an item is selected or deselected
			 */
			selectionBar: null,

			/**
			 * The element within the list pane that contains the ticket results
			 * Defaults to 'wrapper .list-listing'
			 * @option {jQUery}
			 */
			listWrapper: null,

			/**
			 * The URL we'll post IDs to to get updated items
			 */
			fetchPreviewUrl: null,

			/**
			 * The button to trigger openeing the overlay.
			 * Null: defaults to 'wrapper .perform-actions-trigger',
			 * False: dont auto-assign trigger
			 */
			triggerElement: null,

			/**
			 * The HTML element with the actual controls etc we'll use for this
			 * Defaults to 'wrapper .mass-actions-overlay'
			 */
			templateElement: null,

			/**
			 * Disable the previewing feature and handle list view
			 */
			isListView: false,

			/**
			 * Reset the widget every time its closed
			 */
			resetOnClose: true,

			/**
			 * If true, closes immediately when calling apply
			 */
			closeOnApply: true,

			noScroll: false,

			/**
			 * Function to call when apply button is clicked.
			 */
			applyAction: function() {
			},

			/**
			 * Function to call when opened
			 */
			openAction: function() {

			}
		};

		this.setOptions(options);

		this.viewHandler     = this.options.viewHandler;
		this.selectionBar    = this.options.selectionBar || page.selectionBar;
		this.fetchPreviewUrl = this.options.fetchPreviewUrl || page.meta.fetchResultsUrl;
		this.listWrapper     = this.options.listWrapper;

		if (!this.listWrapper) {
			if (this.options.isListView) {
				this.listWrapper = $('.table-result-list table', page.wrapper);
			} else {
				this.listWrapper = $('.list-listing', page.wrapper);
			}
		}

		this.wrapperEl = this.options.templateElement || $('div.mass-actions-overlay-container', page.wrapper);
		this.wrapperEl.detach();
		this.wrapper = this.wrapperEl.clone();
		this.wrapper.find('.with-handler').removeClass('with-handler');

		DeskPRO_Window.initInterfaceLayerEvents(this.wrapper);
		var scrollEl = $('.with-scrollbar', this.wrapper).first();
		if (scrollEl.length && !this.options.noScroll) {
			this.scrollerHandler = new DeskPRO.Agent.ScrollerHandler(null, scrollEl, {
				showEvent: 'show',
				hideEvent: 'hide'
			});
		}

        $('.dp-radio-expander-form', this.wrapper).on('click', this.updatePositions.bind(this));
		this.backdropEls = null;

		this.countEl = $('.selected-tickets-count', this.getElement());

		var trigger = null;
		if (this.options.triggerElement === null) {
			trigger = $('.perform-actions-trigger', page.wrapper);
		} else if (this.options.triggerElement) {
			trigger = this.options.triggerElement;
		}

		if (trigger) {
			trigger.on('click', (function(ev) {
				ev.preventDefault();
				ev.stopPropagation();
				this.open();
			}).bind(this));
		}
	},


	/**
	 * Resets the wrapper back to the original, and then runs all of the init again.
	 */
	reset: function() {
		var wasopen = this.isOpen();
		this.close();

		this.backdropEls.remove();
		this.wrapper.remove();

		this.wrapper = this.wrapperEl.clone();
		this.wrapper.find('.with-handler').removeClass('with-handler');

		DeskPRO_Window.initInterfaceLayerEvents(this.wrapper);
		var scrollEl = $('.with-scrollbar', this.wrapper).first();
		scrollEl.removeClass('scroll-draw');
		if (scrollEl.length && !this.options.noScroll) {
			this.scrollerHandler = new DeskPRO.Agent.ScrollerHandler(null, scrollEl, {
				showEvent: 'show',
				hideEvent: 'hide'
			});
		}

		this.countEl = $('.selected-tickets-count', this.wrapper);
        this.updatePositions();
        $('.dp-radio-expander-form', this.wrapper).on('click', this.updatePositions.bind(this));
		this._hasInit = false;

		this.hasAnyChange = false;

		if (wasopen) {
			this.open();
		}
	},


	/**
	 * Get the main wrapper element around the mass actions UI controls.
	 *
	 * @return {jQuery}
	 */
	getElement: function() {
		return this.wrapper;
	},


	/**
	 * Inits the overlay controls lazily on first open
	 */
	_initOverlay: function() {
		var self = this;
		if (this._hasInit) return;
		this._hasInit = true;

		this.wrapper.detach().appendTo('body');
		DeskPRO.ElementHandler_Exec(this.wrapper);
		this.wrapper.css('z-index', '21001');

		this.baseId = this.wrapper.data('base-id');

		this.wrapper.on('click', function(ev) {
			ev.stopPropagation();
		});

		if (this.options.isListView) {
			this.backdropEls = $('<div class="backdrop fade" />');

		} else {
			// Three backdrops to surround each side of the list pane: left, right, top
			var back1 = $('<div class="backdrop mass-actions" />');
			var back2 = $('<div class="backdrop mass-actions" />');
			var back3 = $('<div class="backdrop mass-actions" />');
			this.backdropEls = $([back1.get(0), back2.get(0), back3.get(0)]);
		}

		this.backdropEls.css('z-index', '21000').hide().appendTo('body');

		this.backdropEls.on('click', (function(ev) {
			ev.stopPropagation();
			this.close();
		}).bind(this));

		$('header .close-trigger', this.wrapper).first().on('click', (function(ev) {
			ev.stopPropagation();
			ev.preventDefault();
			this.close();
		}).bind(this));

		$('.apply-actions', this.wrapper).on('click', (function(ev) {
			this.apply();
		}).bind(this));

		if (!this._hasSelectionEvents) {
			this._hasSelectionEvents = true;

			this.selectionBar.addEvent('countChange', function(count) {
				if (!this.isOpen()) return;
				this.updateCount(count);
			}, this);
		}
	},

	updateAssignmentsDisplay: function() {
	},

	getElById: function(id) {
		return $('#' + this.baseId + '_' + id);
	},

	updateCount: function(num) {
		if (num === undefined || num === null) {
			num = this.selectionBar.getCount();
		}
		this.countEl.text(num);
	},

	getActionFormValues: function(appendArray, isApply, info) {
	},

	/**
	 * Apply the changes
	 */
	apply: function() {
		if(this.options.applyAction) {
			var formDataInfo = {
				checkedCount: 0,
				actionsCount: 0
			},
			formData = this.selectionBar.getCheckedFormValues('result_ids[]', null, formDataInfo),
			rows = [];

			$(formData).each(function(index, param) {
				rows.push(param.value);
			});

			if(formDataInfo.checkedCount) {
				this.options.applyAction(this.wrapper, {'result_ids': rows});
			}
		}

		if (this.options.closeOnApply) {
			this.close();
		}
	},


	/**
	 * Clear all pending previews
	 */
	clearPreview: function() {
	},


	/**
	 * Updates the listing with a preview of the changes we're making
	 */
	updatePreview: function(specific_id, force) {
	},

	/**
	 * Update the preview display data with an HTML block returned from the server
	 *
	 * @param html
	 */
	updatePreviewDisplay: function(html) {

	},


	/**
	 * When a ticket has been checked or uncheck, need to update the preview status of that ticket.
	 */
	handleCheckChange: function(el, is_checked) {

	},


	/**
	 * Resets the form back to nothing
	 */
	resetForm: function() {
	},


	/**
	 * Update the positions of the elements
	 */
	updatePositions: function() {
        if(!this.isOpen()) {
            return;
        }
		//------------------------------
		// The wrapper overlaps the content pane section
		//------------------------------

		var pos = $('#dp_content').offset();
		var top = pos.top - 4;

        var bottom = 10;
        var height = '';

        var scrollContent = $('.scroll-content', this.wrapper).first();
        var contentH = false;
        var hasHeader = !!($('> section > header', this.wrapper).length);
        var hasFooter = !!($('> section > footer', this.wrapper).length);

        if (scrollContent.length) {
            contentH = scrollContent.height();
            if (hasHeader) {
                contentH += 36;
            }
            if (hasFooter) {
                contentH += 45;
            }

            contentH += 31;
        }

        if (hasHeader) $('> section > article', this.wrapper).removeClass('no-header');
        else $('> section > article', this.wrapper).addClass('no-header');

        if (hasFooter) $('> section > article', this.wrapper).removeClass('no-footer');
        else $('> section > article', this.wrapper).addClass('no-footer');

        if (contentH < 100) {
            contentH = 100;
        }

        var maxH = $(window).height() - top - 10;

        if (contentH && contentH < maxH) {
            bottom = '';
            height = contentH;
        }

		this.wrapper.css({
			top: pos.top - 4,
			left: pos.left + 8,
            right: 3,
            bottom: bottom,
            height: height
		});

		//------------------------------
		// The backdrops surround each side of the list pane
		//------------------------------

		var leftEnd = 269; // Where the left ends (aka where listpane starts)
		var topEnd = 50; // Where the top ends (aka header height)
		var contentStart = pos.left;

		if (!this.options.isListView) {
			this.backdropEls.eq(0).css({
				top: 0,
				width: leftEnd,
				bottom: 0,
				left: 0
			});

			this.backdropEls.eq(1).css({
				top: 0,
				height: topEnd,
				width: contentStart - leftEnd,
				left: leftEnd
			});

			this.backdropEls.eq(2).css({
				top: 0,
				right: 0,
				bottom: 0,
				left: contentStart
			});
		}

		if (this.scrollerHandler) {
			this.scrollerHandler.updateSize();
		}
	},

	_initMacroOverlay: function() {
	},


	/**
	 * Load a macro into the form
	 */
	loadMacro: function(macro_id) {
	},


	/**
	 * Is the overlay currently open?
	 *
	 * @return {Boolean}
	 */
	isOpen: function() {
		if (!this._hasInit || !this.wrapper.is('.open')) {
			return false;
		}

		return true;
	},


	/**
	 * Open this overlay
	 */
	open: function() {
		this._initOverlay();

		this.wrapper.addClass('open');

		this.backdropEls.show();

		this.updateCount(null);

        this.updatePositions();
        this.updatePositions();
        DeskPRO_Window.layout.addEvent('resized', this.updatePositions, this);
        //this.updatePreview();

		this.options.openAction(this.wrapper);
	},


	/**
	 * Close the overlay
	 */
	close: function() {
		if (!this.isOpen()) {
			return false;
		}

        DeskPRO_Window.layout.removeEvent('resized', this.updatePositions, this);
		this.wrapper.removeClass('open');
		this.backdropEls.hide();
		this.fireEvent('closed', [this]);

		this.clearPreview();

		if (this.options.resetOnClose) {
			this.reset();
		}
	},


	destroy: function() {
		if (this._hasInit) {
			this.wrapper.remove();
			this.backdropEls.remove();
		}
	}
});
