Orb.createNamespace('DeskPRO.UI');

/**
 * The registry contains a list of labels with the types
 * they apply to, so when a new autocomplete source is set up
 * we need the actual array list for a particular type.
 * This is the pre-computed lists.
 */
DeskPRO.UI.LabelsInput_Grouped = {};

DeskPRO.UI.LabelsInput = new Orb.Class({
	Implements: [Orb.Util.Options, Orb.Util.Events],

	initialize: function(options) {
		var self = this;
		this.options = {
			/**
			 * The labels select
			 */
			input: null,

			/**
			 * The field name the labels should be added (ie labels[])
			 */
			fieldName: 'labels',

			/**
			 * The label type. This is used with autocomplete.
			 * Values: tickets, people, articles, downloads, feedback, news
			 */
			type: '',

			placeholder: false
		};

		this.setOptions(options);

		this.input = $(this.options.input);

		var tagSource = false;

		if (this.options.type) {
			if (DeskPRO.UI.LabelsInput_Grouped[this.options.type]) {
				tagSource = DeskPRO.UI.LabelsInput_Grouped[this.options.type];
			} else if (window.DESKPRO_DATA_REGISTRY && window.DESKPRO_DATA_REGISTRY.labels) {
				tagSource = [];
				Object.each(window.DESKPRO_DATA_REGISTRY.labels, function(types, label) {
					if (types.indexOf(this.options.type) != -1) {
						tagSource.push(label);
					}
				}, this);

				DeskPRO.UI.LabelsInput_Grouped[this.options.type] = tagSource;
			}

			if (!tagSource) {
				console.warn('No type %s', this.options.type);
			}
		}

		if (!tagSource) tagSource = [];

		this.input.on('change', function() {
			self.fireEvent('change', self.getLabels());
		});

		DP.select(this.input, {
			tags: tagSource,
			multiple: true,
			id: function (e) { if (!e) return null; return e.id; },
			formatResult: function(result, container, query) {
				if (!result || !result.text) {
					return '';
				}
				return Orb.escapeHtml(result.text);
			},
			matcher: function(term, text) {
				if (typeOf(text)  != 'string' || typeOf(term) != 'string') {
					return;
				}

				return text.toUpperCase().indexOf(term.toUpperCase()) >= 0;
			}
		});

		this.input.select2('container').on('click', '.select2-search-choice', function(ev) {
			if (ev.target && $(ev.target).is('.select2-search-choice-close')) {
				return;
			}

			ev.preventDefault();
			ev.stopPropagation();

			var label = $(this).text().trim();
			if (label) {
				$('#dp_search_box').data('handler').setSearch('[' + label + ']');
			}
		});
	},

	/**
	 * Get the labels currently added to the list
	 *
	 * @return {Array}
	 */
	getLabels: function() {
		return this.input.select2('val') || [];
	},


	/**
	 * Get labels serialized as a form array suitable with jQuery.ajax
	 *
	 * @return {Array}
	 */
	getFormData: function() {
		var tags = this.getLabels();
		var field = this.options.fieldName;

		var postData = [];
		Array.each(tags, function(x) {
			postData.push({
				name: field + '[]',
				value: x
			});

			// Make sure the group exists
			if (!DeskPRO.UI.LabelsInput_Grouped[this.options.type]) {
				DeskPRO.UI.LabelsInput_Grouped[this.options.type] = [];
			}
			// Add the label to the group if its new
			if (DeskPRO.UI.LabelsInput_Grouped[this.options.type].indexOf(x) === -1) {
				DeskPRO.UI.LabelsInput_Grouped[this.options.type].push(x);
			}
		}, this);

		return postData;
	},

	destroy: function() {
		this.getLabels = function() { return []; };
		this.getFormData = function() { return []; };
		this.input = null;
		this.options = null;
	}
});

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

Orb.createNamespace('DeskPRO.UI');

/**
 * Optionbox
 */
DeskPRO.UI.OptionBox = new Orb.Class({
	Implements: [Orb.Util.Options, Orb.Util.Events],

	initialize: function(options) {
		this.options = {
			element: null,
			trigger: null
		};

		this.setOptions(options);

		this.el = this.options.element;

		if (this.options.trigger) {
			$(this.options.trigger).on('click', this.open.bind(this));
		}
	},

	getElement: function(type) {
		if (!type || type == 'element') {
			return this.el;
		} else if (type == 'backdrop') {
			return this.backdrop;
		}
	},

	_init: function() {
		var self = this;

		if (this._hasInit) return;
		this._hasInit = true;

		this.fireEvent('preInit', [this]);

		//------------------------------
		// Basic elements
		//------------------------------

		this.backdrop = $('<div class="backdrop optionbox-backdrop" />');
        this.backdrop.appendTo('body').hide();

		if (!this.el.parent().is('body')) {
			this.el.detach().appendTo('body');
		}

		this.el.on('click', function(ev) {
			ev.stopPropagation();
		});

		this.backdrop.on('click', function(ev) {
			ev.stopPropagation();
			self.close();
		});

		$('.close-trigger', this.el).on('click', function(ev) {
			ev.stopPropagation();
			self.close();
		});

		var allSame = this.el.is('.single-option-type');

		//------------------------------
		// Events on checkboxes and filter
		//------------------------------

		$(':checkbox, :radio', this.el).on('change', function() {
			self.clickCheckbox($(this));
		});

		$('section', this.el).each(function() {
			var count = $('ul :checkbox', this).length;
			$(this).data('total-count', count);

			if ($(this).data('section-name')) {
				$(this).addClass($(this).data('section-name'));
			}

			var opt = $(':checkbox, :radio', this).first();
			if (opt.is(':radio')) {
				$(this).data('input-type', 'radio');
			} else {
				$(this).data('input-type', 'checkbox');
			}
		});

		$('section col', this.el).last().addClass('last');

		var amClicking = false;
		var lastClick = false;
		var elList = this.el.find('li');
		this.el.on('click', 'li', function(ev) {
			if (amClicking) return;
			amClicking = true;

			var radio = $(':radio, :checkbox', this);

			if (ev.shiftKey && lastClick) {
				var idxMe = elList.index(this);
				var idxLast = elList.index(lastClick);

				var els = [];
				if (idxMe > idxLast) {
					for (var i = idxLast+1; i <= idxMe; i++) {
						els.push($(elList.eq(i)).find(':radio, :checkbox').get(0));
					}
				} else {
					for (var i = idxMe; i < idxLast; i++) {
						els.push($(elList.eq(i)).find(':radio, :checkbox').get(0));
					}
				}

				radio = $(els);
			}

			if (radio.length) {
				radio.prop('checked', !radio.prop('checked'));
				self.clickCheckbox(radio);

				if (radio.is(':radio')) {
					if (allSame) {
						self.el.find('li.on').removeClass('on');
					} else {
						radio.closest('section').find('li.on').removeClass('on');
					}
				}

				if (radio.prop('checked')) {
					radio.closest('li').addClass('on');
				} else {
					radio.closest('li').removeClass('on');
				}

				if (!self.options.liNoClickClose && (radio.is(':radio') && self.isSingleMode)) {
					self.close();
				}
			}

			lastClick = this;
			amClicking = false;
		});

		$(':radio, :checkbox', this.el).on('change', function() {
			if ($(this).is(':radio')) {
				if (allSame) {
					self.el.find('li.on').removeClass('on');
				} else {
					$(this).closest('section').find('li.on').removeClass('on');
				}
			}

			if ($(this).is(':checked')) {
				$(this).closest('li').addClass('on');
			} else {
				$(this).closest('li').removeClass('on');
			}
		});

		$('header .all-check', this.el).on('click', function() {
			var section = self._findSection($(this));
			if ($(this).is(':checked')) {
				$('ul :checkbox', section).attr('checked', true);
			} else {
				$('ul :checkbox', section).attr('checked', false);
			}
			self.updateCountEls(section);
		});

		$('header input.filter-box', this.el).on('keyup', function() {
			self.updateFilter($(this));
		}).on('change', function() {
			self.updateFilter($(this));
		});

		$('header .toggle-btn', this.el).on('click', function(ev) {
			Orb.cancelEvent(ev);

			var section = self._findSection($(this));
			var checks = section.find(':checkbox');
			if (checks.filter(':checked').length) {
				checks.attr('checked', false).trigger('change');
			} else {
				checks.attr('checked', true).trigger('change');
			}

			self.updateCountEls(section);
		});

		var l = this.el.find('div.col').length;
		if (l == 0 || l == 1) {
			this.isSingleMode = true;
		} else {
			this.isSingleMode = false;
		}

		this.fireEvent('init', [this]);
	},

	clickCheckbox: function(check) {
		var section = this._findSection(check);
		this.updateCountEls(section);

		this.fireEvent('checked', [check, this]);
	},

	updateCountEls: function(section) {

		if (!section) {
			var self = this;
			this.el.find('section').each(function() {
				if ($(this).data('section-name')) {
					self.updateCountEls($(this));
				}
			});
			return;
		}

		var count = $('ul :checkbox:checked', section).length;
		var countEl = $('.selected-count', section);

		if (count) {
			$('.num', countEl).text(count);
			countEl.show();
		} else {
			$('.num', countEl).text('0');
			countEl.hide();
		}

		if (count == section.data('total-count')) {
			$('header .all-check', section).attr('checked', true);
		} else {
			$('header .all-check', section).attr('checked', false);
		}
	},

	getCount: function(section) {
		if (typeof section == 'string') {
			section = $('section.' + section, this.el);
		}

		return parseInt($('.selected-count .num', section).text() || 0);
	},

	getSelectedElements: function(section) {
		if (typeof section == 'string') {
			section = $('section.' + section, this.el);
		}

		var els = [];
		$('input:checked', section).each(function() {
			els.push($(this).closest('li').get(0));
		});

		els = $(els);
		return els;
	},

	getSelected: function(section) {
		if (typeof section == 'string') {
			section = $('section.' + section, this.el);
		}

		var val = [];
		$('li input:checked', section).each(function() {
			val.push($(this).val());
		});

		if (section.data('input-type') == 'radio') {
			val = val.pop();
			if (!val) {
				val = null;
			}
		}

		return val;
	},

	getAllSelected: function() {
		var self = this;
		var ret = {};

		$('section', this.el).each(function() {
			var name = $(this).data('section-name');
			ret[name] = self.getSelected($(this));
		});

		return ret;
	},

	_findSection: function(el) {
		return el.closest('section');
	},

	updateFilter: function(filterEl) {
		var filter = filterEl.val().trim().toLowerCase();
		var section = this._findSection(filterEl);
		var lis = $('li', section);

		if (!filter) {
			lis.show();
			return;
		}

		var show = [];
		var hide = [];

		lis.each(function() {
			var name = $('label', this).text().toLowerCase();
			if (name.indexOf(filter) !== -1) {
				show.push(this);
			} else {
				hide.push(this);
			}
		});

		$(hide).hide();
		$(show).show().each(function() {
			var itemId = $(this).data('item-id');
			if (itemId) {
				$('li.child-of-' + itemId, section).show();
				var parentId = $(this).data('parent-id');
				if (parentId) {
					$('li.item-' + parentId, section).show();
				}
			}
		});
	},

	open: function(event) {
		this._init();

		this.fireEvent('preOpen', [this]);

		var viewportW = $(window).width();
		var viewportH = $(window).height();

		var pageX = $(event.target).offset().top;
		var pageY = $(event.target).offset().left;

		var w = this.el.width() + 5;
		var h = this.el.height();

		this.el.show();
		this.backdrop.show();

		if (pageY + w > viewportW) {
			pageY = viewportW - w - 10;
		}
		if (pageX + h > viewportH) {
			pageX = viewportH - h - 10;
		}

		this.el.css({
			top: pageX,
			left: pageY
		});

		this.el.addClass('open');

		var cols = $('.col', this.el);
		if (cols.length) {
			var max = 0;
			var w = 0;
			cols.each(function() {
				var ul = $('> section > ul', this);
				w += $(this).width();
				max += ul.height();
			});
			$('.col > section > ul', this.el).each(function() {
				if ($(this).height() < max) {
					$(this).height(max);
				}
			});

			w += (1 * cols.length);  // border/margin on the cols
			DP.console.log('setting w %i', w);

			this.el.width(w);
		}

		$(':checkbox, :radio', this.el).each(function() {
			if ($(this).is(':checked')) {
				$(this).closest('li').addClass('on');
			} else {
				$(this).closest('li').removeClass('on');
			}
		});

		this.updateCountEls();
		this.fireEvent('open', [this]);
	},

	close: function() {
		if(this._hasInit) {
        	this.backdrop.hide();
		}

		if (!this.isOpen()) return;

		this.el.hide().removeClass('open');

		this.fireEvent('close', [this]);
	},

	isOpen: function() {
		if (this._hasInit && this.el.is('.open')) {
			return true;
		}

		return false;
	},

	destroy: function() {
		if (this._hasInit) {
			this.el.remove();
			this.backdrop.remove();
		}
	}
});

Orb.createNamespace('DeskPRO.UI');

/**
 * Optionbox that remembers the selected options when it was opened, so you can
 * revert back to them
 */
DeskPRO.UI.OptionBoxRevertable = new Orb.Class({
	Extends: DeskPRO.UI.OptionBox,

	initialize: function(options) {
		var self = this;
		options = options || {};
		options.liNoClickClose = true;
		this.parent(options);

		this.addEvent('preOpen', this.handlePreOpen, this);
		this.addEvent('close', this.handleClose, this);

		this.hasApplied = false;

		$('.save-trigger', this.el).on('click', function(ev) {
			self.hasApplied = true;

			self.fireEvent('save', [self]);

			self.close();
		});
	},

	handlePreOpen: function() {
		this.preSelections = $('li.on', this.el);
	},

	revertOptions: function() {
		$('li', this.el).removeClass('on');
		this.preSelections.each(function() {
			$(this).addClass('on');
			$(':radio, :checkbox', this).prop('checked', true);
		});
	},

	handleClose: function() {
		if (!this.hasApplied && this.preSelections) {
			this.revertOptions();
		}

		this.hasApplied = false;
		this.preSelections = null;
	}
});

Orb.createNamespace('DeskPRO.UI');

/**
 * Optionbox but this helps build the markup required for it.
 */
DeskPRO.UI.OptionBoxBuilder = new Orb.Class({
	Extends: DeskPRO.UI.OptionBox,

	initialize: function(options) {
		var self = this;
		var selectEl;

		var tpl = [];
		tpl.push('<div class="optionbox">');
		tpl.push('	<section data-section-name="default">');
		tpl.push('		<header>');
		tpl.push('			<h3>&nbsp;</h3>');
		tpl.push('			<input type="text" class="filter-box" placeholder="Filter..." />');
		tpl.push('		</header>');
		tpl.push('		<ul>');
		tpl.push('		</ul>');
		tpl.push('	</section>');
		tpl.push('</div>');

		tpl = tpl.join('');

		var obEl = $(tpl);

		if (options.title) {
			$('header h3', obEl).text(options.title);
		}

		if (options.addClass) {
			obEl.addClass(options.addClass);
		}

		var randid = Orb.uuid();

		var bindEl = null;
		if (options.values.is && options.values.is('select')) {
			if (options.values.attr('multiple')) {
				options.selectType = 'checkbox';
			} else {
				options.selectType = 'radio';
			}

			var hasSelected = false;
			selectEl = options.values;
			options.values = [];

			var selected_text = '';
			var selectoptions = $('option', selectEl);
			var is_sub = false;
			selectoptions.each(function(index, el) {
				el = $(el);
				var label = el.text().trim();

				if (el.parent().is('optgroup')) {
					label = el.parent().attr('label') + ' > ' + label;
				}

				var has_child = false;
				var is_child = (label.indexOf('--') !== -1);
				var child_depth = 0;
				if (is_child) {
					child_depth = 1;
					if (label.indexOf('----') !== -1) child_depth = 2;
					if (label.indexOf('------') !== -1) child_depth = 3;
					if (label.indexOf('--------') !== -1) child_depth = 4;
					if (label.indexOf('----------') !== -1) child_depth = 5;
					if (label.indexOf('------------') !== -1) child_depth = 6;
					if (label.indexOf('--------------') !== -1) child_depth = 7;
					label = label.replace(/^\-\-/, '').trim();
				} else {
					has_child = (el.next().text().trim().indexOf('--') !== -1);
				}

				if (!selected_text || el.is(':selected')) {
					selected_text = el.text();
				}
				if (has_child) {
					is_sub = true;
					options.values.push({
						label: label,
						value: el.val(),
						hasChild: has_child,
						hasParent: is_child,
						childDepth: child_depth,
						isSelected: el.hasClass('start') || el.data('start')
					});
				} else {
					if (!is_child) is_sub = false;
					if (!hasSelected && options.selectDefault) {
						el.addClass('start');
						hasSelected = true;
					}
					options.values.push({
						label: label,
						value: el.val(),
						hasChild: has_child,
						hasParent: is_child,
						childDepth: child_depth,
						isSelected: (el.hasClass('start') || el.data('start') ? true : false)
					});
				}
			});

			var text = selected_text;
			if (!text.length) text = options.noValText || 'Choose...';
			if (options.spanEl) {
				var spanEl = options.spanEl;
			} else {
				var spanEl = $('<span class="menu-trigger">' + Orb.escapeHtml(text) + '</span>').insertAfter(selectEl);
				spanEl.on('click', self.open.bind(self));
			}
			selectEl.hide();

			this.addEvent('checked', function(el) {
				var value = el.val();

				$('option', selectEl).each(function() {
					if ($(this).val() == value) {
						if ($(this).prop('selected')) {
							$(this).prop('selected', false);
						} else {
							$(this).prop('selected', 'selected');
						}
					}
				});

				selectEl.change();
			});

			selectEl.on('change', function() {
				var evData = {select: this, stopDefault: false};
				self.fireEvent('selectChange', evData);
				if (evData.stopDefault) return;

				var opt = $('option:selected', this);
				if (opt.data('full-title')) {
					var text = opt.data('full-title').trim();
				} else {
					var text = opt.text().trim();
				}
				if (!text.length) text = options.noValText || 'Choose...';
				else {
					var prefix = $(this).data('prefix');
					if (prefix) text = prefix + text;
				}

				spanEl.text(text);
			});
		}

		var name = Orb.uuid();
		if (selectEl && selectEl.attr('name')) {
			name = selectEl.attr('name').replace(/[^a-zA-Z_]/, '_');
		}
		Array.each(options.values, function(opt) {
			if (options.selectType == 'radio') {
				var li = $('<li><input type="radio" name="'+name+'" /><label></label></li>');
			} else {
				var li = $('<li><input type="checkbox" /><label></label></li>');
			}

			if (opt.childDepth) {
				li.addClass('depth-' + opt.childDepth);
				li.prepend('<span class="elbow-end"></span>');
			}

			$('label', li).text(opt.label);
			$(':checkbox, :radio', li).first().val(opt.value).data('connected-to', opt);

			if (opt.value == options.selected_value) {
				$(':checkbox, :radio', li).first().prop('checked', true);
			}

			if (opt.extraData) {
				Object.each(opt.extraData, function(v,k) {
					li.data(k, v);
				});
			}

			if (opt.hasChild) {
				li.addClass('group-title');
			}
			if (opt.hasParent) {
				li.addClass('child');
			}

			if (opt.isSelected) {
				li.addClass('on');
				li.find('input').prop('checked', true);
			}

			$('ul', obEl).append(li);
		})

		options.element = obEl;

		this.parent(options);

		if (selectEl) {
			selectEl.find('.start').prop('selected', true);
			selectEl.trigger('change');
		}
	}
});

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

Orb.createNamespace('DeskPRO.UI');

/**
 * This is a simple tabbing system where the tab triggers and tab contents can be completely
 * separate, and are linked by attribuets in the source. This tab system simply
 * toggles an 'on' CSS class on elements, so it's up to you to style the elements. For example,
 * content elements without 'on' sholud be display:none etc.
 *
 * There are two elements: tab triggers and tab content. Tab triggers are attached a click event
 * that stitches the 'on' state of all the tab contents. So the tab clicked becoems "on" (and your
 * CSS makes it visible, and the rest invisible).
 *
 * <code>
 *     <li data-tab-for=".some-tab">Some Tab</li>
 *     ...
 *     <div class="some-tab"></div>
 * </code>
 */
DeskPRO.UI.SimpleTabs = new Orb.Class({
	Implements: [Orb.Util.Options, Orb.Util.Events],

	initialize: function(options) {

		// Initial values
		this.options = {
			/**
			 * Elements that will act as tab triggers. Tabs
			 * must have a data-tab-for attribute with a jQuery selector
			 * to define which tab they activate.
			 *
			 * You can add new triggers dynamically with addTriggerElement() later
			 */
			triggerElements: '.tab-trigger',

			/**
			 * This classname is added to tabs and tab content wrappers when they're activated
			 */
			activeClassname: 'on',

			/**
			 * The context for tab contents when executing data-tab-for
			 */
			context: document,

			autoSelectFirst: true,

			/**
			 * The effect to for showing/hiding the tab: slide, fade
			 */
			effect: null
		};

		this.lastActiveTab = null;
		this.triggerEls = null;

		if (options) this.setOptions(options);

		this.triggerEls = this.options.triggerElements;

		if (typeOf(this.triggerEls) == 'string') {
			this.triggerEls = $(this.triggerEls, this.options.context);
		}

		var self = this;
		this.triggerEls.on('click', function(ev) {
			ev.cancel = false;
			ev.tabEl = $(this);

			self.fireEvent('tabClick', [ev]);

			if (!ev.cancel) {
				self._handleTabClick(this, ev);
			}

			self.fireEvent('postTabClick', [ev]);
		});

		if (this.options.autoSelectFirst) {
			var firstTab = this.triggerEls.filter('.' + this.options.activeClassname).filter(':visible').first();
			if (!firstTab.length) {
				firstTab = this.triggerEls.filter(':visible').first();
				if (!firstTab.length) {
					firstTab = this.triggerEls.first();
				}
			}

			// Check again, there might not be any tabs
			if (firstTab.length) {
				// need to hide all others
				var self = this;
				this.triggerEls.each(function() {
					self.getContentElFromTab($(this)).hide();
				});

				this.activateTab(firstTab);
			}
		}
	},

	addTriggerElement: function(el) {
		var self = this;

		this.triggerEls.add(el);
		el.on('click', function(ev) {
			ev.cancel = false;
			ev.tabEl = $(this);

			self.fireEvent('tabClick', [ev]);

			if (!ev.cancel) {
				self._handleTabClick(this, ev);
			}
		});
	},

	_handleTabClick: function(el, event) {
		var tab = $(el);
		this.activateTab(tab, event);
	},

	activateTab: function(tabEl, event) {

		if (!tabEl) {
			return;
		}

		tabEl = $(tabEl);

		if (this.lastActiveTab && this.lastActiveTab.get(0) == tabEl.get(0)) {
			return;
		}

		var eventData = {
			event: event || null,
			tabEl: tabEl,
			lastTabEl: this.lastActiveTab,
			tabContent: this.getContentElFromTab(tabEl),
			manager: this,
			cancel: false
		};

		this.fireEvent('beforeTabSwitch', eventData);

		if (eventData.cancel) {
			return;
		}

		delete eventData['cancel'];

		var showFn = (function() {
			this.lastActiveTab = tabEl;
			this.lastActiveTab.addClass(this.options.activeClassname);
			var x = eventData.tabContent.addClass(this.options.activeClassname);

			this.lastActiveTabContent = eventData.tabContent;

			var parentContainer = eventData.tabContent.closest('.tabViewDetailContent, .with-page-fragment').first();
			if (parentContainer) {
				if (parentContainer.data('page-fragment')) {
					parentContainer.data('page-fragment').updateUi();
				} else {
					parentContainer.find('.with-scroll-handler').each(function() {
						if ($(this).data('scroll_handler')) {
							$(this).data('scroll_handler').updateSize();
						}
					});
				}
			}

			if (this.lastActiveTab && this.lastActiveTab.data('tab-on-show')) {
				this.lastActiveTab.data('tab-on-show')(eventData);
			}
			if (this.lastActiveTabContent && this.lastActiveTabContent.data('tab-on-show')) {
				this.lastActiveTabContent.data('tab-on-show')(eventData);
			}

			this.fireEvent('tabSwitch', eventData);

			if (this.lastActiveTabContent.data('load-url') && !this.lastActiveTabContent.data('tab-loaded')) {
				this._triggerTabAjaxLoad(this.lastActiveTab, this.lastActiveTabContent, eventData);
			}

			if (this.options.effect == 'slide') {
				x.slideDown('fast');
			} else if (this.options.effect == 'fade') {
				x.fadeIn('fast');
			} else {
				x.show();
			}

			x.trigger('dp_simpletabs_show');
		}).bind(this);

		if (this.lastActiveTab) {
			this.lastActiveTab.removeClass(this.options.activeClassname);
			var x = this.getContentElFromTab(this.lastActiveTab).removeClass(this.options.activeClassname);

			this.lastActiveTab = null;

			if (this.lastActiveTab && this.lastActiveTab.data('tab-on-hide')) {
				this.lastActiveTab.data('tab-on-hide')(eventData);
			}
			if (this.lastActiveTabContent && this.lastActiveTabContent.data('tab-on-hide')) {
				this.lastActiveTabContent.data('tab-on-hide')(eventData);
			}

			if (this.options.effect == 'slide') {
				x.slideUp('fast', showFn);
			} else if (this.options.effect == 'fade') {
				x.fadeOut('fast', showFn);
			} else {
				x.hide();
				showFn();
			}

			x.trigger('dp_simpletabs_hide');
		} else {
			showFn();
		}
	},

	_triggerTabAjaxLoad: function(tabEl, contentEl, eventData) {
		var self = this;

		contentEl.data('tab-loaded', true);

		delete eventData['cancel'];

		this.fireEvent('beforeTabLoad', eventData);
		if (eventData.cancel) {
			return;
		}

		$.ajax({
			url: contentEl.data('load-url'),
			method: 'get',
			dataType: 'html',
			success: function(html) {
				delete eventData['cancel'];
				self.fireEvent('beforeTabLoaded', eventData);
				if (eventData.cancel) {
					return;
				}

				contentEl.html(html);
				eventData.tabContent = self.getContentElFromTab(tabEl);

				self.fireEvent('tabLoaded', eventData);
			}
		})
	},

	getActiveTab: function() {
		return this.lastActiveTab;
	},

	getActiveTabContent: function() {
		return this.getContentElFromTab(this.getActiveTab());
	},

	getContentElFromTab: function(tabEl) {
		if (!tabEl || !tabEl.data || !tabEl.data('tab-for')) {
			DP.console.error('tab has no tab-for: %o', tabEl);
			if (console && console.trace) console.trace();
			return $();
		}

		if (tabEl.data('tab-for') == 'NOOP') {
			return $();
		}

		var el = $(tabEl.data('tab-for'), this.options.context);

		if (el.length < 1) {
			DP.console.error('no tab content exists for tab: %o', tabEl);
			console.trace();
		}

		return el;
	},

	destroy: function() {

	}
});

Orb.createNamespace('DeskPRO.Agent.UI');

DeskPRO.UI.DateChooser = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(options) {
		this.options = {
			rowEl: null
		};

		if (options) this.setOptions(options);

		this.rowEl = $(this.options.rowEl);
	},

	initValues: function() {
		var timestamp = null, date = null;

		timestamp = this.date1Input.val();
		if (timestamp) {
			date = new Date(timestamp * 1000);
			this.date1Widget.datepicker('setDate', date);
		}

		timestamp = this.date2Input.val();
		if (timestamp) {
			date = new Date(timestamp * 1000);
			this.date2Widget.datepicker('setDate', date);
		}

		this.updateStatus();
	},

	_initUi: function() {

		if (this._hasInit) return;
		this._hasInit = true;

		//------------------------------
		// References to elements and move
		// overlay into body
		//------------------------------

		this.opInput = $('.op', this.rowEl);

		this.date1Input = $('input.date1-input', this.rowEl);
		this.date2Input = $('input.date2-input', this.rowEl);

		this.date1Display = $('input.date1-display', this.rowEl);
		this.date2Display = $('input.date2-display', this.rowEl);

		this.currentValue = $('.display-value', this.rowEl);

		this.dateWrap = $('.date-wrap', this.rowEl);

		this.backdrop = $('<div class="backdrop" style="display: none"></div>');
		this.backdrop.appendTo('body');
		this.backdrop.on('click', this.hide.bind(this));

		this.wrapper = $('<div class="field-overlay" style="display:none"><div class="close-trigger"></div></div>');
		$('.close-trigger', this.wrapper).on('click', this.hide.bind(this));

		this.dateWrap.detach().appendTo(this.wrapper).css('display', 'block');
		this.wrapper.appendTo('body');

		this.date1 = $('.date1', this.dateWrap);
		this.date2 = $('.date2', this.dateWrap);

		//------------------------------
		// Init date elements
		//------------------------------

		var self = this;
		this.date1Widget = $('.widget', this.date1).datepicker({
			dateFormat: 'M d, yy',
			onSelect: function(dateText, inst) {

				self.date1Input.val(self.date1Widget.datepicker('getDate').getTime() / 1000);

				self.date1Display.val(dateText);
				self.updateStatus();
			}
		});

		this.date2Widget = $('.widget', this.date2).datepicker({
			dateFormat: 'M d, yy',
			onSelect: function(dateText, inst) {

				self.date2Input.val(self.date2Widget.datepicker('getDate').getTime() / 1000);

				self.date2Display.val(dateText);
				self.updateStatus();
			}
		});

		var getDate = function (el) {
			var timestamp = strtotime(el.val());
			if (!timestamp) {
				return null;
			}

			var date = new Date(timestamp * 1000);
			return date;
		};

		//------------------------------
		// Detect changes to text fields for
		// human times "1 day ago" etc
		//------------------------------

		this.date1Display.on('change', function() {
			var date = getDate($(this));
			if (!date) {
				$(this).val('');
				return;
			}
			self.date1Widget.datepicker('setDate', date);
		});

		this.date2Display.on('change', function() {
			var date = getDate($(this));
			if (!date) {
				$(this).val('');
				return;
			}
			self.date2Widget.datepicker('setDate', date);
		});

		//------------------------------
		// Switcher between relative input
		//------------------------------

		$('.switcher', this.date1).on('click', (function() {
			var date = $('.date', this.date1);
			var rel  = $('.relative', this.date1);

			if (date.is(':visible')) {
				date.hide();
				rel.show();
			} else {
				rel.hide();
				date.show();
			}
		}).bind(this));

		$('.switcher', this.date2).on('click', (function() {
			var date = $('.date', this.date2);
			var rel  = $('.relative', this.date2);

			if (date.is(':visible')) {
				date.hide();
				rel.show();
			} else {
				rel.hide();
				date.show();
			}
		}).bind(this));
	},

	open: function() {

		this._initUi();

		if (this.opInput.val() == 'between') {
			this.dateWrap.addClass('two');
		} else {
			this.dateWrap.removeClass('two');
		}

		this.wrapper.css({
			left: this.currentValue.offset().left,
			top: this.currentValue.offset().top
		});

		this.backdrop.show();
		this.wrapper.show();
	},

	updateStatus: function() {

		var str1 = '', str2 = '', status = '';

		// If we're using the relative times, update the values on close
		var relative1 = $('.relative1', this.date1);
		var relative2 = $('.relative2', this.date2);

		if (relative1.is(':visible')) {
			$('.date1-relative-input', this.rowEl).val($('.relative1-input', this.date1).val());
			$('.date1-relative-type', this.rowEl).val($('.relative1-type', this.date1).val());

			// Erase any calendar time we mightve set before
			this.date1Input.val('');

			if ($('.relative1-input', this.date1).val().trim().length) {
				str1 = $('.relative1-input', this.date1).val() + ' ' + $('.relative1-type', this.date1).val() + ' ago';
			}
		} else {
			var date1 = this.date1Widget.datepicker('getDate');
			if (date1) {
				str1 = $.datepicker.formatDate('M d, yy', date1);
			}
		}

		if (relative2.is(':visible')) {
			$('.date2-relative-input', this.rowEl).val($('.relative2-input', this.date2).val());
			$('.date2-relative-type', this.rowEl).val($('.relative2-type', this.date2).val());

			this.date2Input.val('');

			if ($('.relative2-input', this.date2).val().trim().length) {
				str2 = $('.relative2-input', this.date2).val() + ' ' + $('.relative2-type', this.date2).val() + ' ago';
			}
		} else {
			var date2 = this.date2Widget.datepicker('getDate');
			if (date2) {
				str2 = $.datepicker.formatDate('M d, yy', date2);
			}
		}

		if (!str1.length) str1 = '(click to set)';
		if (!str2.length) str1 = '(click to set)';

		if (this.opInput.val() == 'between') {
			status = str1 + ' and ' + str2;
		} else {
			status = str1;
		}

		this.currentValue.text(status);
	},

	hide: function() {
		this.updateStatus();
		this.backdrop.hide();
		this.wrapper.hide();
	},

	destroy: function() {
		this.wrapper.remove();
		this.backdrop.remove();
	}
});

Orb.createNamespace('DeskPRO.Agent.UI');

DeskPRO.UI.CatListEditor = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(options) {
		this.options = {
			/**
			 * The main outer list container
			 * @param {jQuery|String}
			 */
			listEl: null,

			/**
			 * Items in a list
			 * @param {String}
			 */
			itemSelector: 'li',

			/**
			 * Selectors that identify sub-groups within an item
			 * @param {String}
			 */
			subListSelector: '> ul',

			/**
			 * Selector that identifies the title within the li
			 * @param {String}
			 */
			titleSelector: '.title-edit:first',

			/**
			 * The data attribute that holds the category ID
			 * @param {String}
			 */
			dataId: 'category-id',

			/**
			 * Selector for new item template
			 * {String}
			 */
			newItemTplSelector: null,

			/**
			 * The basename for the editor elements
			 */
			editorBaseId: '',

			/**
			 * The data-route to add to newly created cats
			 */
			newCatRoute: ''
		};

		this.setOptions(options);

		var self = this;
		var list = $(this.options.listEl);
		this.list = list;

		var lis = $(this.options.itemSelector, this.list);
		this._initLisCollection(lis);

		list.on('click', '.sub-toggle', function() {
			$(this).parent().parent().toggleClass('sub-expanded');
		});

		this.isEditMode = false;

		window.setTimeout(function() {
			self.pristineStructure = self.getStructure();
		}, 300);
	},

	_initLisCollection: function(lis) {

		var self = this;
		var list = this.list;

		lis.addClass('dp-cat-li')
			.wrapInner('<div class="item-wrap dp-cat-item" />')
			.prepend('<div class="dp-cat-dropzone between" />');

		// Move existing subgroups back out of dp-cat-item
		$('.dp-cat-item ' + this.options.subListSelector, lis).each(function() {
			var li = $(this).parent().parent();
			$(this).detach().appendTo(li);
		});

		$('.dp-cat-item, .dp-cat-dropzone', lis).droppable({
			accept: 'li.dp-cat-li',
			tolerance: 'pointer',
			drop: function(e, ui) {
				var movedTree = false;
				var li = $(this).parent();
				var child = !$(this).is('.dp-cat-dropzone');
				if (child && li.children('ul').length == 0) {
					li.append('<ul/>');
				}
				if (child) {
					li.addClass('has-children').children('ul').append(ui.draggable);
				} else {
					li.before(ui.draggable);
				}

				movedTree = true;

				$('li.dp-li-open').not(':has(li:not(.ui-draggable-dragging))').removeClass('sub-expanded');
				li.find('.dp-cat-item, .dp-cat-dropzone').removeClass('dp-cat-over');

				$('li.has-children', list).each(function() {
					var p = $(this);
					if (!$('> ul > li:not(.ui-draggable-dragging):first', p).length) {
						p.removeClass('has-children');
					}
				});

				if (movedTree) {
					self.fireEvent('restructured', [ui.draggable, this]);
				} else {
					self.fireEvent('reordered', [ui.draggable, this]);
				}
			},
			over: function() {
				$(this).addClass('dp-cat-over');
				if ($(this).is('.dp-cat-dropzone')) {
					$('.dp-cat-item:first', $(this).parent()).removeClass('dp-cat-over');
				}
			},
			out: function() {
				$(this).filter('.dp-cat-item, .dp-cat-dropzone').removeClass('dp-cat-over');
			}
		});

		lis.draggable({
			handle: '> .dp-cat-item',
			opacity: 0.5,
			addClasses: false,
			helper: 'clone',
			zIndex: 100
		});

		if (!this.editMode) {
			lis.draggable('disable');
		}
	},

	enableEditMode: function() {
		this.editMode = true;
		this.list.find('li.dp-cat-li').draggable('enable');
	},

	disableEditMode: function() {
		this.editMode = false;
		this.list.find('li.dp-cat-li').draggable('disable');
	},

	/**
	 * Get an array with the order of each item
	 *
	 * @return {Array}
	 */
	getOrder: function() {
		var itemDataId = this.options.dataId;
		var orders = [];
		$('li.dp-cat-li', this.list).each(function() {
			var id = $(this).data(itemDataId);
			if (id) {
				orders.push(id);
			}
		});

		return orders;
	},


	/**
	 * Get the structure.
	 *
	 * @param {jQuery} list From this list. Defaults to the whole list
	 * @return {Object}
	 */
	getStructure: function(list) {
		list = list || this.list;

		var map = {};

		var lis = list.find('li.dp-cat-li');
		lis.each(function() {
			var id = $(this).data('category-id');
			var parentId = 0;

			var ul = $(this).parent();
			if (ul.parent().is('li')) {
				parentId = ul.parent().data('category-id');
			}

			map[id] = parentId;
		});

		return map;
	},


	/**
	 * Are we currently editing?
	 *
	 * @return {Boolean}
	 */
	isTitleEditing: function() {
		return this.list.is('.title-editing');
	},


	/**
	 * Activate the title editor
	 */
	showEditTitles: function() {
		if (this.isTitleEditing()) {
			return;
		}

		this.list.addClass('title-editing');

		var self = this;
		$('.dp-cat-item', this.list).each(function() {
			self._enableEditable($(this));
		});

		this.fireEvent('titlesActivated', [this]);
	},


	/**
	 * Deactivate the title editor
	 */
	endEditTitles: function() {

		if (!this.isTitleEditing()) {
			return;
		}

		var self = this;

		var titles = {};
		$('input.dp-cat-input', this.list).each(function() {
			var item = $(this).parent();
			var dataId = item.parent().data(self.options.dataId);
			var newTitle = self._disableEditable(item);

			if (dataId) {
				titles[dataId] = newTitle;
			}
		});

		this.list.removeClass('title-editing');

		this.fireEvent('titlesUpdated', [titles, this]);
	},

	_enableEditable: function(item) {
		var titleEl = $(this.options.titleSelector, item);
		var title = titleEl.text().trim();

		var inputEl = $('<input type="text" class="dp-cat-input" />');
		inputEl.val(title);

		titleEl.hide();
		inputEl.insertAfter(titleEl);
	},

	_disableEditable: function(item) {
		var titleEl = $(this.options.titleSelector, item);
		var input = $('input.dp-cat-input', item).first();
		var newTitle = input.val().trim();

		input.remove();
		titleEl.text(newTitle).show();

		return newTitle;
	},

	/**
	 * Add a new category
	 *
	 * @param li
	 */
	addNew: function(li) {
		var self = this;

		var tpl = $(this.options.newItemTplSelector).get(0).innerHTML;
		var li = $(tpl);

		var firstLi = $('li.dp-cat-li:first', this.list);
		if (firstLi.length) {
			li.insertBefore(firstLi);
		} else {
			li.appendTo(this.list);
		}

		// init new item
		this._initLisCollection(li);

		this._enableEditable($('.dp-cat-item', li));

		this.fireEvent('newAddEditable', [li, input, this]);

		var input = $('input.dp-cat-input', li);
		var fnDone = function() {
			self.fireEvent('newAdded', [li, input, self]);
			self._disableEditable($('.dp-cat-item', li));
		};
		input.on('blur', fnDone).on('keypress', function(ev) {
			if (ev.which == 13) {
				fnDone();
			}
		});

		var input = $('input:first', li);
		input.focus().select();
	},

	/**
	 * Show an editor for a certain item in the list
	 *
	 * @param {jQuery} li
	 */
	showEditor: function(li) {

		this._initEditor();

		var elPos = li.offset();
		var elWidth = li.width() - 10;

		var title = $('.title-edit:first', li).text().trim();
		$('input.title', this.editTab).val(title);

		elPos.top -= 5;

		this.editTab.css({
			left: elPos.left,
			top: elPos.top,
			width: elWidth
		});

		this.editTabBk.css({
			left: elPos.left + elWidth - 4,
			top: elPos.top + 1
		})

		this.edit.css({
			left: elPos.left + elWidth,
			top: elPos.top - 10
		});

		var ids = ((li.data('usergroup-ids')+'') || '').split(',');
		if (ids.indexOf('1') !== -1) {
			// Everyone means all usergroups should be checked
			$(':checkbox.usergroup', this.edit).prop('checked', true);
		} else {
			$(':checkbox.usergroup', this.edit).prop('checked', false);
			Array.each(ids, function(id) {
				$('input.usergroup-' + id, this.edit).prop('checked', true);
			}, this);
		}

		this.editBack.show();
		this.editTab.show();
		this.edit.show();
		this.editTabBk.show();

		this.edit.data('editing-li', li);
	},

	closeOpenEditor: function() {

		var li = this.edit.data('editing-li');

		if (li) {
			var newTitle = $('input.title', this.editTab).val().trim();
			var titleEl = $('.title-edit:first', li);
			var oldTitle = titleEl.text().trim();
			var dataId = li.data(this.options.dataId);
			var updatedTitle = false;
			var updatedUgs = false;

			if (newTitle != oldTitle) {
				updatedTitle = true;

				titleEl.text(newTitle);

				var titles = {};
				titles[dataId] = newTitle;
				this.fireEvent('titlesUpdated', [titles, this]);
			}

			var ug_ids = [];
			$('input.usergroup:checked', this.edit).each(function() {
				ug_ids.push($(this).val());
			});
			var ug_ids_string = ug_ids.join(',');

			if (li.data('usergroup-ids') != ug_ids_string) {
				updatedUgs = true;
				li.data('usergroup-ids', ug_ids_string);
			}

			if (updatedTitle || updatedUgs) {
				this.fireEvent('catUpdated', [li.data('category-id'), newTitle, ug_ids, li, this]);
			}
		}



		this.editBack.hide();
		this.editTab.hide();
		this.edit.hide().data('editing-li', 0);
		this.editTabBk.hide();
	},

	_initEditor: function() {
		if (this._editorHasInit) return;
		this._editorHasInit = true;

		this.editBack   = $('#' + this.options.editorBaseId + 'cat_editor_backdrop').detach().appendTo('body');
		this.editTab    = $('#' + this.options.editorBaseId + 'cat_editor_tab').detach().appendTo('body');
		this.edit       = $('#' + this.options.editorBaseId + 'cat_editor').detach().appendTo('body');
		this.editTabBk  = $('#' + this.options.editorBaseId + 'cat_editor_shadowbreak').detach().appendTo('body');

		$('.close-trigger', this.edit).on('click', this.closeOpenEditor.bind(this));

		this.edit.on('click', function(ev) {
			ev.stopPropagation();
		});
		this.editTab.on('click', function(ev) {
			ev.stopPropagation();
		});

		this.editBack.on('click', this.closeOpenEditor.bind(this));
	},

	destroy: function() {
		if (this.editBack) {
			this.editBack.remove();
			this.editTab.remove();
			this.edit.remove();
			this.editTabBk.remove();

			this.editBack = this.editTab = this.edit = this.editTabBk = null;
		}
	}
});

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
DeskPRO.UI.Select.WidgetSimple = new Orb.Class({
	Extends: DeskPRO.UI.Select.Widget,

	_doRenderWidget: function() {
		var el = $('<div class="dp-ui-select-widget trigger-open-menu"><i class="icon-caret-down"></i><div class="val-list"></div></div>');
		return el;
	},

	_doInitWidget: function() {
		this.renderSelectedOptions();
	},

	_doChanged: function() {
		this.renderSelectedOptions();
	},

	_doGetmenu: function() {
		if (this.$select.data('target-menu')) {
			var menu = new DeskPRO.UI.Select.MenuHtml(this);
			return menu;
		}
	},

	renderSelectedOptions: function() {
		var opts = this.getSelectedOptions();

		var valList = this.getEl().find('.val-list');
		valList.empty();

		if (!opts[0]) {
			return;
		}

		var lastIdx = opts.length-1;
		opts.each(function(idx) {
			var span = $('<span class="val"></span>');

			var title = $.trim($(this).text());
			if (title) {
				span.text(title);
			} else {
				span.html('&nbsp;');
			}
			span.appendTo(valList);

			if (idx != lastIdx) {
				span = $('<span class="sep">,</span>');
				span.appendTo(valList);
			}
		});
	}
});
Orb.createNamespace('DeskPRO.UI.Select');

/**
 * MenuHtml takes any arbitrary HTML div and positions it exactly above
 * the Widget element with same width, and when it closes,
 * it syncs any elements with the class widget-val to the select box.
 *
 * This allows, for example, to display a multiselect box as a number of checkboxes.
 * If they are checked, then those values will be used to select the same values in the
 * bound select box.
 *
 * @type {Orb.Class}
 */
DeskPRO.UI.Select.MenuHtml = new Orb.Class({
	Extends: DeskPRO.UI.Select.Menu,

	_doInitMenu: function() {
		var self = this;
		var select = this.getWidget().getSelect();
		var isMulti = select.is('[multiple]');
		var zindex = select.data('zindex');
		var closeOnSelect = select.data('select-close');

		if (select.data('target-menu') == 'auto') {
			var name = Orb.uuid();
			var inputType = isMulti ? 'checkbox' : 'radio';
			this.$menu = $('<div class="source-pane-select-menu"></div>');
			this.$menu.append('<i class="icon-caret-up"></i>');

			if (select.find('optgroup')[0]) {
				select.find('optgroup').each(function() {
					var group = $('<div class="group"></div>');

					var groupTitle = $('<strong></strong>');
					groupTitle.text($.trim($(this).attr('label')));
					groupTitle.appendTo(group);

					var checkList = $('<ul class="checkbox-list"></ul>');
					$(this).find('option').each(function() {
						var li = $('<li><label><input type="'+inputType+'" class="widget-val" /> <span></span></label></li>');
						li.find('input').val($(this).val()).attr('name', name);
						li.find('span').text($.trim($(this).data('title') || $(this).text()));
						li.appendTo(checkList);
					});

					checkList.appendTo(group);
					group.appendTo(self.$menu);
				});
			} else {
				var checkList = $('<ul class="checkbox-list"></ul>');
				select.find('option').each(function() {
					var li = $('<li><label><input type="'+inputType+'" class="widget-val" /> <span></span></label></li>');
					li.find('input').val($(this).val()).attr('name', name);
					li.find('span').text($.trim($(this).data('title') || $(this).text()));
					li.appendTo(checkList);
				});

				checkList.appendTo(this.$menu);
			}

		} else {
			this.$menu = $(select.data('target-menu'));
		}

		if (select.data('menu-addclass')) {
			this.$menu.addClass(select.data('menu-addclass'));
		}

		this.$menu.find('trigger-close-menu').on('click', function(ev) {
			Orb.cancelEvent(ev);
			self.close();
		});
		this.$menu.detach().appendTo('body');

		this.$positionOver = this.getWidget().getEl();

		this.$shim = $('<div class="dp-shim zindex-chrome4"></div>');
		if (zindex) {
			this.$shim.css('z-index', zindex);
			this.$menu.css('z-index', zindex+1);
		}
		this.$shim.appendTo('body');
		this.$shim.on('click', function(ev) {
			Orb.cancelEvent(ev);
			self.close();
		});

		// Init selections based on the bound select box
		this.$menu.find('.widget-val').each(function() {
			var el = $(this);
			if (el.is(':checkbox, :radio')) {
				if (self.getWidget().isValueSelected(el.val())) {
					el.prop('checked', true);

					if (closeOnSelect) {
						el.on('click', function() { self.close(); });
					}
				}
			} else if (el.is('select')) {
				el.find('option').each(function() {
					if (self.getWidget().isValueSelected($(this).val())) {
						$(this).prop('selected', true);
					}
				});
			}
		});
	},

	_doOpenMenu: function() {
		var pos = this.$positionOver.offset();
		var w = this.$positionOver.width();

		this.$menu.css({
			top: pos.top,
			left: pos.left,
			'min-width': w
		});

		this.$menu.show();
		this.$shim.show();
	},

	_doCloseMenu: function() {
		this.$menu.hide();
		this.$shim.hide();

		this.getWidget().setSelectedValues(this.getSelectedValues());
	},

	_doDestroy: function() {
		if (this.$menu) {
			this.$menu.detach();
			this.$shim.detach();
		}
	},

	getSelectedValues: function() {
		var optionEls = this.$menu.find('.widget-val');
		var values = [];

		optionEls.each(function() {
			var el = $(this);

			if (el.is(':checkbox, :radio')) {
				if (el.is(':checked')) {
					values.push(el.val());
				}
			} else if (el.is('select')) {
				var opts = el.find('option').find(':selected');
				opts.each(function() {
					values.push($(this).val());
				});
			}
		});

		return values;
	}
});