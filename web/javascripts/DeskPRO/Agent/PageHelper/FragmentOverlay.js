Orb.createNamespace('DeskPRO.Agent.PageHelper');

/**
 * This loads a fragment into an overlay window
 */
DeskPRO.Agent.PageHelper.FragmentOverlay = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(options) {
		this.options = {
			routeData: {},
			positionAbove: true,
			zIndex: 0
		};

		this.setOptions(options);

		/**
		 * The page fragment once its initialized
		 */
		this.page = null;

		this._initOverlay();
		this.reloadFragment();
	},

	reloadFragment: function() {
		var routeData = this.options.routeData;

		if (this.loadingXhr) {
			this.loadingXhr.abort();
			this.loadingXhr = null;
		}

		this.loadingXhr = DeskPRO_Window._doAjaxLoadRoute(routeData.url, routeData, (function(data) {

			if (this.page) {
				this.page.fireEvent('destroy');
				this.page = null;
				this.wrapper.empty();
			}

			this.page = DeskPRO_Window.createPageFragment(data);

			this.page.setMetaData('routeUrl', routeData.url);
			if (routeData) {
				this.page.setMetaData('routeData', routeData);
			}

			this.page.fragmentOverlay = this;

			this.wrapper.removeClass('overlay-loading');
			$('section.dp-overlay', this.wrapper).empty().html(data);

			$('header .close-trigger', this.wrapper).on('click', (function(ev) {
				ev.stopPropagation();
				ev.preventDefault();
				this.close();
			}).bind(this));

			this.updatePositions();
			this.page.fireEvent('render', [this.wrapper]);
			this.fireEvent('pageInit', [this.page, this]);
		}).bind(this));
	},

	_initOverlay: function() {

		if (this._hasInit) return;
		this._hasInit = true;

		this.wrapper = $(DeskPRO_Window.util.getPlainTpl($('#fragment_overlay_tpl')));
		this.wrapper.hide().appendTo('body');

		this.wrapper.on('click', function(ev) {
			ev.stopPropagation();
		});

		this.backdropEl = $('<div class="backdrop dp-overlay-backdrop dp-fragment-overlay-backdrop" />');
		this.backdropEl.hide().appendTo('body');
		this.backdropEl.on('click', (function(ev) {
			ev.stopPropagation();
			this.close();
		}).bind(this));

		$('header .close-trigger', this.wrapper).on('click', (function(ev) {
			ev.stopPropagation();
			ev.preventDefault();
			this.close();
		}).bind(this));

		this.updatePositions();
		this.wrapper.show();
		this.backdropEl.show();
	},

	/**
	 * Update the positions of the elements
	 */
	updatePositions: function() {

		var article = $('section.dp-overlay > article', this.wrapper);

		var w = parseInt(article.data('width'));
		var h = parseInt(article.data('height'));

		if (w) this.wrapper.css('width', w + 5);
		if (h) this.wrapper.css('height', h + 5);

		if (article.data('add-class')) {
			this.wrapper.addClass(article.data('add-class'));
		}

		var w = this.wrapper.outerWidth();
		var pageW = $(window).width();
		var leftForCenter = (pageW - w) / 2;

		var h = this.wrapper.outerHeight();
		var pageH = $(window).height();
		var topForCenter = (pageH - h) / 2;

		this.wrapper.css({
			'top': topForCenter,
			'left': leftForCenter
		});

		if (this.options.zIndex) {
			this.wrapper.css('z-index', this.options.zIndex+1);
			this.backdropEl.css('z-index', this.options.zIndex);
		} else if (this.options.positionAbove) {
			var zIndex = Orb.findHighestZindex() + 10;

			this.wrapper.css('z-index', zIndex+1);
			this.backdropEl.css('z-index', zIndex);
		}

		if ($('section > header', this.wrapper).length) {
			this.wrapper.removeClass('no-header');
		} else {
			this.wrapper.addClass('no-header');
		}

		if ($('section > footer', this.wrapper).length) {
			this.wrapper.removeClass('no-footer');
		} else {
			this.wrapper.addClass('no-footer');
		}

		//this.backdropEl.css('z-index', parseInt(this.wrapper.css('z-index'))-1);
	},

	close: function() {
		this.destroy();
	},

	destroy: function() {

		if (this.loadingXhr) {
			this.loadingXhr.abort();
			this.loadingXhr = null;
		}

		if (this.page) {
			this.page.fireEvent('destroy');
			this.page = null;
		}

		if (this.wrapper) {
			this.wrapper.remove();
			this.backdropEl.remove();
		}

		this.wrapper = null;
		this.backdropEl = null;

		this.fireEvent('destroy', [this]);
	}
});
