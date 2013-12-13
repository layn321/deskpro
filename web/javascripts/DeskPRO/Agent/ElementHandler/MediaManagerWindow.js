Orb.createNamespace('DeskPRO.Agent.ElementHandler');

DeskPRO.Agent.ElementHandler.MediaManagerWindow = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	initPage: function() {
		window.MEDIA_MANAGER_WINDOW = this;
		this.el.bind('dp_open', this.open.bind(this));
		this.el.bind('dp_close', this.close.bind(this));
		this.boundEditor = null;
	},

	bindToEditor: function(rte) {
		this.boundEditor = rte;
	},

	_lazyInit: function() {
		var self = this;

		if (this._hasInit) return;
		this._hasInit = true;

		$('#mediawin_nav > li').removeClass('on')
		$('#mediawin_pages > section').each(function() {
			var page = $(this).data('page-fragment');
			if (page) {
				page.fireEvent('destroy');
			}
			$(this).data('page-fragment', null);

			$(this).empty();
			$(this).append('<div class="page-loading"></div>');
		});

		this.el.css({
			top: 45,
			bottom: 120,
			width: 780,
			left: ($(window).width() - 780) / 2
		});

		$('.close-trigger', this.el).first().on('click', function(ev) {
			ev.stopPropagation();
			ev.preventDefault();
			self.el.trigger('dp_close');
		});

		this.topTabs = new DeskPRO.UI.SimpleTabs({
			triggerElements: $('#mediawin_nav > li'),
			context: this.el,
			onTabSwitch: function(ev) {
				var wrapper = $(ev.tabContent);
				$('#mediawin_pages .on').removeClass('on');

				wrapper.addClass('on');

				if (!wrapper.data('page-fragment')) {
					self._loadPageForTabTarget(wrapper);
				} else {
					if (wrapper.data('page-fragment').TYPENAME == 'mediawin_browse') {
						self.reloadTab('browser');
					} else {
						self.reloadTab('upload');
					}
				}
			}
		});

		this.backdrop = $('<div class="backdrop fade" />').hide().appendTo('body').css('z-index', 32000);
		this.backdrop.click(this.close.bind(this));
	},

	_loadPageForTabTarget: function(wrapper) {

		if (wrapper.hasClass('loading')) {
			return;
		}

		wrapper.addClass('loading')

		var self = this;
		$.ajax({
			dataType: 'text',
			url: wrapper.data('page-url'),
			type: 'GET',
			context: this,
			complete: function() {
				wrapper.removeClass('loading')
			},
			success: function(html) {
				var page = DeskPRO_Window.createPageFragment(html);
				page.mediaWindow = self;
				wrapper.html(page.html);
				delete page.html;

				page.fireEvent('render', [wrapper]);
				page.fireEvent('activate');

				wrapper.data('page-fragment', page);
			}
		});
	},

	reloadTab: function(name) {
		var tab = $('#mediawin_nav li.tab-' + name);
		var target = $(tab.data('tab-for'));

		var page = target.data('page-fragment');
		if (page) {
			page.fireEvent('destroy');
			page.destroy();
		}

		target.empty();
		target.append('<div class="page-loading"></div>');

		if (tab.is('.on')) {
			this._loadPageForTabTarget(target);
		}
	},

	showSavePuff: function() {
		$('#mediawin_saved_overlay').fadeIn(250, function() {
			$('#mediawin_pages .on').scrollTop(0);

			window.setTimeout(function() {
				$('#mediawin_saved_overlay').fadeOut(250);
			}, 1200);
		});
	},

	open: function(ev, tabName, activateView) {

		this._lazyInit();

		var tabEl = null;
		if (tabName) {
			tabEl = $('#mediawin_nav > li.tab-' + tabName);
		}

		if (tabName && tabEl) {
			this.topTabs.activateTab(tabEl);
		}

		this.el.show();
		this.backdrop.show();
	},

	isOpen: function() {
		if (this._hasInit && this.el.is(':visible')) {
			return true;
		}

		return false;
	},

	close: function() {
		if (this.isOpen()) {
			this.el.hide();
			this.backdrop.hide();
		}

		this.boundEditor = null;
	}
});
