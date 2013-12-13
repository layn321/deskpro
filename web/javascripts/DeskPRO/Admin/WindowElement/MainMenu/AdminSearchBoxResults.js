Orb.createNamespace('DeskPRO.Admin.WindowElement.MainMenu');

DeskPRO.Admin.WindowElement.MainMenu.AdminSearchBoxResults = new Class({
	Extends: DeskPRO.Agent.WindowElement.MainMenu.Abstract,

	menuHasResults: false,
	searchEls: null,

	init: function () {
		var self = this;
		$('#window_search_box').on('focus', function(ev) {
			if (self.shouldShowMenu() && !$('#window_search_form .search-drop:first').is(':visible')) {
				self.options.mainMenuOpener.openMenu($('#window_search_form'), ev);
			}
		}).on('click', function(ev) {
			// stop propgation because the focus above will open
			// the menu, dont want to bubble click into the container
			// to re-close it again
			ev.stopPropagation();
		}).on('keypress', this.queryChanged.bind(this));

		this.searchEls = $('a[data-search-keywords]', '#admin_header_menus');
	},

	shouldShowMenu: function() {
		var check = $('#window_search_form .results, #window_search_loading');
		if (!check.is('.is-vis')) {
			return false;
		}

		return true;
	},

	queryChanged_timeout: null,
	queryChanged: function() {

		if (!$('#window_search_box').val().trim().length) {
			this.clearList();
			return;
		}

		$('#window_search_loading').show().addClass('is-vis');
		if (!$('#window_search_form .search-drop:first').is(':visible')) {
			this.options.mainMenuOpener.openMenu($('#window_search_form'));
		}

		// If we already have a timeout going, we'll
		// let the time run out.
		// We dont reset it because we'd rather the user
		// see some results as they type, instead of only when they pause
		if (this.queryChanged_timeout) {
			return;
		}

		this.queryChanged_timeout = this.updateResults.delay(300, this);
	},

	updateResults: function() {

		if (this.queryChanged_timeout) {
			window.clearTimeout(this.queryChanged_timeout);
			this.queryChanged_timeout = null;
		}

		var val = $('#window_search_box').val().trim();

		if (!val.length) {
			this.clearList();
			return;
		}

		val = val.toLowerCase();

		this._handleMenuItemResults();

		var items = [];
		this.searchEls.each(function() {
			if ($(this).data('search-keywords').indexOf(val) != -1) {
				items.push('<li><a href="' + $(this).attr('href') + '">' + $(this).data('search-name') + '</a></li>');
			}
		});

		items = items.join('');
		this._handleMenuItemResults(items);
	},

	_handleMenuItemResults: function(items) {

		if (!items) items = '';
		if (!items.length) {
			items = '<li>No items found</li>';
		}

		this.updateList('menuitems', items);
	},

	updateList: function(type, items) {
		var wrap = $('#window_search_' + type).addClass('is-vis');
		var list = $('> .results-list > ul', wrap);

		list.html(items);
		wrap.show();
	},

	clearList: function() {
		$('#window_search_loading').hide().removeClass('is-vis');
		$('#window_search_form .results').hide().removeClass('is-vis');
		$('#window_search_form .results-list > ul').html('');

		if (this.queryChanged_timeout) {
			window.clearTimeout(this.queryChanged_timeout);
			this.queryChanged_timeout = null;
		}

		this.closeMenu();
	}
});
