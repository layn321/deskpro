Orb.createNamespace('DeskPRO.Agent.ElementHandler');

DeskPRO.Agent.ElementHandler.SettingsWindow = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	initPage: function() {
		window.SETTINGS_WINDOW = this;
		this.el.bind('dp_open', this.open.bind(this));
		this.el.bind('dp_close', this.close.bind(this));
	},

	_lazyInit: function() {
		var self = this;

		this.updatePos();

		if (this._hasInit) return;
		this._hasInit = true;

		$('.close-trigger', this.el).first().on('click', function(ev) {
			ev.stopPropagation();
			ev.preventDefault();
			self.el.trigger('dp_close');
		});

		this.topTabs = new DeskPRO.UI.SimpleTabs({
			triggerElements: $('#settingswin_nav > li'),
			context: this.el,
			onTabSwitch: function(ev) {
				var wrapper = $(ev.tabContent);
				$('#settingswin_pages').find('> section.on').removeClass('on');

				wrapper.addClass('on');

				if (!wrapper.data('page-fragment')) {
					self._loadPageForTabTarget(wrapper);
				} else {
					wrapper.data('page-fragment').fireEvent('activate');
				}
			}
		});

		this.backdrop = $('<div class="backdrop fade" />').hide().appendTo('body').css('z-index', 32000);
		this.backdrop.click(this.close.bind(this));

		DeskPRO_Window.layout.addEvent('resized', function() {
			self.updatePos();
		});
	},

	updatePos: function() {
		this.el.css({
			top: 54,
			bottom: 10,
			width: 850,
			left: ($(window).width() - 700) / 2
		});

		var overlay = this.el.find('.dp-overlay');
		var winH = Math.min($(window).height() - 150, 700);

		overlay.css({
			'max-height': winH,
			'height': 'auto'
		});
	},

	_loadPageForTabTarget: function(wrapper) {
		var self = this;
		$.ajax({
			dataType: 'text',
			url: wrapper.data('page-url'),
			type: 'GET',
			context: this,
			success: function(html) {
				var page = DeskPRO_Window.createPageFragment(html);
				page.settingsWindow = self;
				wrapper.html(page.html);
				delete page.html;

				page.fireEvent('render', [wrapper]);
				page.fireEvent('activate');

				wrapper.data('page-fragment', page);
			}
		});
	},

	reloadTab: function(name) {
		var tab = $('#settingswin_nav li.tab-' + name);
		var target = $(tab.data('tab-for'));

		var page = target.data('page-fragment');
		if (page) {
			page.fireEvent('destroy');
		}

		target.empty();
		target.append('<div class="page-loading"></div>');

		if (tab.is('.on')) {
			this._loadPageForTabTarget(target);
		}
	},

	_cleanupOld: function() {
		// TODO
		// after settings window is hidden for a while, clear all of the page fragments to reduce memory
	},

	showSavePuff: function() {
		$('#settingswin_saved_overlay').fadeIn(250, function() {
			$('#settingswin_pages .on').scrollTop(0);

			window.setTimeout(function() {
				$('#settingswin_saved_overlay').fadeOut(250);
			}, 1200);
		});
	},

	open: function(ev, tabName, activateView) {
		this.el.data('activateView', activateView);
		if (tabName) {
			var tabEl = $('#settingswin_nav > li.tab-' + tabName);
		}
		if (tabName && !this._hasInit) {
			$('#settingswin_nav > li').removeClass('on');
			tabEl.addClass('on');
		}

		this._lazyInit();

		if (tabName && tabEl) {
			this.topTabs.activateTab(tabEl);
		}

		if (this._cleanupTimer) {
			window.clearTimeout(this._cleanupTimer);
			this._cleanupTimer = null;
		}

		this.el.show();
		this.backdrop.show();

		if ($('#userSetting').data('backdrop')) {
			$('#userSetting').data('backdrop').remove();
			$('#userSetting').hide();
		}
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

			this._cleanupTimer = window.setTimeout(this._cleanupOld.bind(this), 180000); // three minutes

			if (this.reloadInterface) {
				DeskPRO_Window.util.reloadInterface();
			}
		}
	}
});
