Orb.createNamespace('DeskPRO.UI');

/**
 * This creates an overlay (optionally modal) whose contents can be fetched via AJAX,
 * or already exist within the page.
 */
DeskPRO.UI.Overlay = new Orb.Class({
	Implements: [Orb.Util.Options, Orb.Util.Events],

	initialize: function(options) {

		// Init vars
		this.objectId = null;

		this.options = {
			triggerElement: null,
			contentMethod: 'element',
			contentElement: null,
			contentAjax: {
				url: '',
				type	: 'GET',
				dataType: 'html'
			},
			iframeUrl: null,
			iframeId: false,
			maxHeight: 700,
			maxWidth: 900,
			destroyOnClose: false,
			customClassname: '',
			classname: '',
			isModal: true,
			zIndex: 10001,
			escapeClose: true,
			modalClickClose: true,
			objectGroup: 'default',
			addClose: true,
			fullScreen: false,	// Fullscreen overlay, ignores maxHeight, maxWidth options
			fullScreenMargin: '35px'
		};

		this.isThisDestroyed = false;
		this.hasInit = false;
		this.hasSentAjax = false;
		this.elements = {};

		if (options) this.setOptions(options);

		if (this.options.triggerElement) {
			this.setupTriggerElement($(this.options.triggerElement));
		}

		if (this.options.zIndex == 'none') {
			this.options.zIndex = '';
		}

		if (this.options.escapeClose) {
			$(document).on('keydown', (function (ev) {
				if (ev.which == 27) {
					this.closeOverlay();
				}
			}).bind(this));
		}
	},

	/**
	 * Get the main wrapper for the overlay
	 */
	getElement: function() {
		return this.getWrapper();
	},


	/**
	 * Alias for isOverlayOpen.
	 *
	 * @return {Boolean}
	 */
	isOpen: function() {
		return this.isOverlayOpen();
	},


	/**
	 * Check to see if the overlay is currently open.
	 */
	isOverlayOpen: function() {
		if (!this.hasInit) return false;
		return this.elements.wrapper.is(':visible');
	},


	/**
	 * Standard naming for UI elements. Alias for openOverlay.
	 */
	open: function() {
		return this.openOverlay();
	},


	/**
	 * Display the overlay
	 */
	openOverlay: function() {
		if (!this.initOverlay()) {
			return;
		}

		if (this.isOverlayOpen()) {
			return;
		}

		var evData = { overlay: this, cancel: false };
		this.fireEvent('beforeOverlayOpened', evData);
		if (evData.cancel) {
			return;
		}

		var zindex = this.options.zIndex;
		if (zindex == 'top') {
			var zindex = Orb.findHighestZindex()+1;
		}

		this.elements.modal.css({
			'z-index': zindex,
			'position': 'fixed',
			'top': 0,
			'right': 0,
			'bottom': 0,
			'left': 0
		});

		this.elements.modal.fadeIn(200);

		var topOffset = $(document).scrollTop();

		if (this.options.contentMethod == 'iframe') {

			var w = $(window).width() - 250;
			var h = $(window).height() - 150;

			if (w > this.options.maxWidth) w = this.options.maxWidth;
			if (h > this.options.maxHeight) h = this.options.maxHeight;

			this.elements.wrapper.css({ width: w, height: h });
			$('iframe:first', this.elements.wrapper).css({ width: w, height: h-37 });

			var x = ($(window).width() - this.elements.wrapperOuter.outerWidth()) / 2;
			var y = ($(window).height() - this.elements.wrapperOuter.outerHeight()) / 2;

		} else {
			var w = this.elements.wrapperOuter.outerWidth();
			var pageW = $(window).width();
			var leftForCenter = (pageW / 2) - (w / 2);

			var h = this.elements.wrapperOuter.outerHeight();
			var pageH = $(window).height();
			var topForCenter = (pageH / 2) - (h / 2);

			this.elements.wrapperOuter.css({
				'top': topForCenter+topOffset,
				'left': leftForCenter
			});
		}

		if (true == this.getOption('fullScreen')) {
			this.elements.wrapperOuter.css({
				'z-index': (zindex ? zindex+1 : ''),
				'position': 'fixed',
				'left': this.getOption('fullScreenMargin'),
				'right': this.getOption('fullScreenMargin'),
				'top': this.getOption('fullScreenMargin'),
				'bottom': this.getOption('fullScreenMargin')
			});
		}
		else {
			this.elements.wrapperOuter.css({
				'z-index': (zindex ? zindex+1 : ''),
				'position': 'fixed',
				'left': leftForCenter
			});
		}

		this.reposition();
		this.elements.wrapperOuter.fadeIn(450, (function() {
			this.fireEvent('overlayOpened', { overlay: this });
		}).bind(this));
	},


	/**
	 * Recalculate positions
	 */
	reposition: function() {
		if (this.getOption('fullScreen')) {
			return;
		}

		var w = this.elements.wrapperOuter.outerWidth();
		var pageW = $(window).width();
		var leftForCenter = (pageW / 2) - (w / 2);

		var h = this.elements.wrapperOuter.outerHeight();
		var pageH = $(window).height();
		var topForCenter = (pageH / 2) - (h / 2);

		var evData = {
			overlay: this,
			wrapperOuter: this.elements.wrapperOuter,
			pageW: pageW,
			pageH: pageH,
			w: w,
			h: h,
			top: topForCenter,
			left: leftForCenter,
			setLeft: function(x) { this.left = x; },
			setTop: function(x) { this.top = x; }
		};

		this.fireEvent('position', evData);

		this.elements.wrapperOuter.css({
			'top': evData.top,
			'left': evData.left
		});
	},

	recalcForResize: function() {
		if (!this.isOpen()) return;

		this.reposition();
	},


	/**
	 * Standard naming for UI elements. Alias for closeOverlay.
	 */
	close: function() {
		return this.closeOverlay();
	},


	/**
	 * Close/hide the overlay
	 */
	closeOverlay: function() {

		if (!this.isOverlayOpen()) {
			return;
		}

		var eventData = { overlay: this, cancelClose: false };
		this.fireEvent('beforeOverlayClosed', eventData);

		if (eventData.cancelClose) return;

		this.elements.modal.fadeOut(450);
		this.elements.wrapperOuter.fadeOut(200);

		this.fireEvent('overlayClosed', { overlay: this });
		this.fireEvent('close', { overlay: this });

		if (this.options.destroyOnClose) {
			this.destroy();
		}
	},



	/**
	 * Initiate the overlay by created the various elements needed etc.
	 */
	initOverlay: function() {

		if (this.hasInit) return true;

		$(window).bind('resize.' + this.OBJ_ID, this.recalcForResize.bind(this));

		if (this.options.isModal) {
			this.elements.modal = $('<div class="deskpro-overlay-overlay '+this.options.customClassname+'" style="display:none" />');
			this.elements.modal.appendTo('body');

			if (this.options.modalClickClose) {
				this.elements.modal.on('click', (function(ev) {
					if (ev && ev.deskpro && ev.deskpro.cancelClose) return;
					this.closeOverlay();
				}).bind(this));
			}
		}

		this.elements.wrapperOuter = $('<div class="deskpro-overlay-outer '+this.options.customClassname+' ' + this.options.classname + '" style="display:none" />');
		this.elements.wrapperOuter.appendTo('body');

		var overlayStyles = "";
		if (true == this.getOption('fullScreen')) {
			overlayStyles = "height: 100%";
		}
		this.elements.wrapper = $('<div class="deskpro-overlay '+this.options.customClassname+'" style="'+overlayStyles+'">');
		this.elements.wrapper.appendTo(this.elements.wrapperOuter);

		switch (this.options.contentMethod) {
			case 'element':

				var el = $(this.options.contentElement);
				if (el.data('overlay-apply-class')) {
					this.elements.wrapperOuter.addClass(el.data('overlay-apply-class'));
				}
				this._setContent(el);

				this.hasInit = true;

				return true;
				break;

			case 'ajax':

				// Already sending
				if (this.hasSentAjax) return false;
				this.hasSentAjax = true;

				var ajaxConfig = Object.merge(this.options.contentAjax, {
					success: this._handleAjaxSuccess.bind(this)
				});

				$.ajax(ajaxConfig);

				this.fireEvent('ajaxStart', {
					overlay: this
				});

				return false;
				break;

			case 'iframe':

				var name = 'iframe_' + Orb.uuid();
				var el = $('<iframe name="'+name+'" src="'+this.options.iframeUrl+'"></iframe>');
				if (this.options.iframeId) {
					el.attr('id', this.options.iframeId);
				}

				this._setContent(el);
				this.hasInit = true;

				this.elements.wrapper.addClass('no-pad').addClass('iframe');
				this.elements.wrapper.find('> .overlay-content').addClass('no-footer');

				return true;
				break;
		}

		DP.console.error('Unknown content method: %s', this.options.contentMethod);

		return false;
	},



	/**
	 * Callback used with the ajax content setter when the result was
	 * fetched from the server.
	 */
	_handleAjaxSuccess: function(data) {

		var dataEl = $(data);

		// We always want a top-level elements.
		// So if AJAX supplied multiple roots,
		// we'll just wrap in a single container div

		if (dataEl.length != 1) {
			var el = $('<div />');
			el.append(dataEl);
		} else {
			var el = dataEl;
		}

		el.show();
		this._setContent(el);

		this.hasInit = true;

		var eventData = {
			overlay: this,
			ajaxData: data
		};
		this.fireEvent('ajaxDone', eventData);

		this.openOverlay();
	},



	/**
	 * Handle setting the content of the overlay.
	 */
	_setContent: function (el) {
		this.elements.wrapper.empty();
		el.detach().appendTo(this.elements.wrapper);

		// Often pages will hide content by default in the initial page,
		// but our wrapper element is hidden so we dont want the innards to be hidden.
		el.show();

		// If the caller didnt provide full markup for the content, we'll wrap it ourselves
		if (!$('div.overlay-content:first', el).length) {
			var insideEl = el;
			var el = $('<div class="overlay-content" />');
			insideEl.wrap(el);
		}

		if (this.options.title && !this.elements.wrapper.find('.overlay-title')[0]) {
			this.elements.wrapper.prepend('<div class="overlay-title"><h4></h4></div>').find('h4').text(this.options.title);
		}

		if (this.options.addClose && !$('.close-trigger, .close-overlay', this.elements.wrapper).length) {
			$('.overlay-title:first', this.elements.wrapper).prepend('<a class="close close-trigger close-overlay">Close</a>');
		}

		$('.overlay-close-trigger, .close-trigger, .close-overlay', this.elements.wrapper).first().on('click', (function (ev) {
			ev.preventDefault();
			this.closeOverlay();
		}).bind(this));

		if (!$('.overlay-footer:first', el).length) {
			$('div.overlay-content:first', el).addClass('no-footer');
		}

		this.reposition();

		this.fireEvent('contentSet', {
			overlay: this,
			contentEl: el,
			wrapperEl: this.elements.wrapper
		});
	},

	setContent: function(el) {
		this._setContent(el);
	},



	/**
	 * Set up a click trigger on an element (or elements).
	 *
	 * @param mixed el A selector, an element, or a jQuery collection
	 */
	setupTriggerElement: function(el) {
		el = $(el);

		var fn = (function (ev) {
			this.openOverlay();
			ev.preventDefault();
		}).bind(this);

		if (el.is('.dbl-click-trigger')) {
			el.on('dblclick', fn);
		} else {
			el.on('click', fn);
		}
	},


	/**
	 * Get the wrapper element for the overlay
	 *
	 * @return {jQuery}
	 */
	getWrapper: function() {
		if (!this._hasInit) {
			this.initOverlay();
		}
		return $(this.elements.wrapperOuter);
	},


	/**
	 * Destroy this overlay and all of its supporting elements.
	 */
	destroy: function() {

		this.fireEvent('beforeDestroy', [this]);

		if (this.elements.wrapperOuter) {
			this.elements.wrapperOuter.remove();
		}
		if (this.elements.modal) {
			this.elements.modal.remove();
		}
		this.isThisDestroyed = true;

		this.fireEvent('destroyed', [this]);
		$(window).unbind('.' + this.OBJ_ID);
	},


	isDestroyed: function() {
		return this.isThisDestroyed;
	}
});
