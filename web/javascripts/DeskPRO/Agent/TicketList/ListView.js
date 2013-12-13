Orb.createNamespace('DeskPRO.Agent.PageHelper');

DeskPRO.Agent.TicketList.ListView = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(page, options)  {

		var self = this;

		this.page = page;

		this.options = {

		};
		this.setOptions(options);
		this.page.addEvent('destroy', (function() {
			this.destroy();
		}).bind(this));
	},

	_initOverlay: function() {
		var self = this;
		if (this._isIniting) return;
		if (this._hasInit) return
		this._isIniting = true;

		var new_url = this.page.meta.viewTypeUrl.replace('$view_type', 'list');
		if (this.options.load_url) {
			new_url = this.options.load_url.replace('$view_type', 'list');
		}

		this.wrapper = $('<div class="dp-overlay-container ticketlist" />').appendTo('body');
		this.backdropEl = $('<div class="backdrop dp-overlay-backdrop" />');
		this.backdropEl.css('z-index', '10000').hide().appendTo('body');
		this.backdropEl.on('click', (function(ev) {
			ev.stopPropagation();
			this.close();
		}).bind(this));

		this.wrapper.html('<section class="dp-overlay"><div class="overlay-title"><span class="close-overlay"></span></div><div class="loading"></div></section>');
		this.wrapper.find('.close-overlay').on('click', function(ev) {
			ev.stopPropagation();
			self.close();
		});

		this.updatePositions();

		this.wrapper.addClass('open');
		this.backdropEl.show();

		this.runningAjax = $.ajax({
			url: new_url,
			dataType: 'html',
			context:  this,
			done: function() {
				this.runningAjax = null;
			},
			success: function(html) {

				this.wrapper.html(html);

				$('header .close-trigger', this.wrapper).first().on('click', (function(ev) {
					ev.stopPropagation();
					ev.preventDefault();
					this.close();
				}).bind(this));

				var page = DeskPRO_Window.createPageFragment(html);
				page.listview = this;
				page.setMetaData('routeUrl', new_url);
				page.setMetaData('pageReloader', this.reload.bind(this));
				page.setMetaData('overlay', this);
				page.fireEvent('render', [this.wrapper]);
				page.fireEvent('activate');

				this._isIniting = false;
				this._hasInit = true;

				this.fireEvent('ajaxLoaded', [this]);

				this.open();
			}
		});
	},

	reload: function() {
		this.showInnerLoading();

		var page = this.page;
		window.setTimeout(function() {
			if (page && page.switchViewType) {
				page.switchViewType('list');
			}
		}, 50);
	},

	open: function() {
		this._initOverlay();
		if (!this._hasInit) return;

		this.updatePositions();

		this.wrapper.addClass('open');
		this.backdropEl.show();

		$('body').addClass('print-overlay');
		this.fireEvent('opened', [this]);
	},

	isOpen: function() {
		if (!this._hasInit || !this.wrapper.is('.open')) {
			return false;
		}

		return true;
	},

	close: function() {
		if (!(this._hasInit || this._isIniting || this.isOpen())) return;
		$('body').removeClass('print-overlay');
		this.destroy();
	},

	showInnerLoading: function() {
		$('.full-loading', this.wrapper).show();
	},

	hideInnerLoading: function() {
		$('.full-loading', this.wrapper).hide();
	},

	/**
	 * Update the positions of the elements
	 */
	updatePositions: function() {
		this.wrapper.css({
			top: 20,
			right: 20,
			bottom: 20,
			left: 20
		});
	},


	destroy: function() {
		if (this._isIniting) {
			this.runningAjax.abort();
			this.runningAjax = null;
			this._isIniting = false;
		}
		if (this._hasInit) {
			this.page.destroy();
		}
		if (this.wrapper) {
			this.wrapper.remove();
		}
		if (this.backdropEl) {
			this.backdropEl.remove();
		}

		delete this.wrapper;
		delete this.backdropEl;
		delete this.options;
		delete this.page;
	}
});
