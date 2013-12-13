Orb.createNamespace('DeskPRO.User.ElementHandler');

DeskPRO.User.ElementHandler.PortalKb = new Orb.Class({

	Extends: DeskPRO.User.ElementHandler.ElementHandlerAbstract,

	init: function() {

		var self = this;

		this.hasDoneSwitch = false;

		var triggers = $('.heading-tabs li', this.el);
		this.headerTabs = new DeskPRO.UI.SimpleTabs({
			triggerElements: $('.heading-tabs li', this.el),
			context: $('.tab-contents', this.el),
			onTabSwitch: function(info) {

				// Hack to not run on initial tab switch,
				// which is already loaded with the page
				if (!self.hasDoneSwitch) {
					self.hasDoneSwitch = true;
					return;
				}

				var tabEl = info.tabEl;
				var is_loaded = tabEl.is('.loaded');
				var tab_for = tabEl.data('tab-for');

				if (!is_loaded) {
					switch (tab_for) {
						case '.browse':
							self.loadBrowseTab(tabEl);
							break;
						case '.find':
							self.loadFindTab(tabEl);
							break;
						case '.recent':
							self.loadRecentTab(tabEl);
							break;
						case '.popular':
							self.loadPopularTab(tabEl);
							break;
					}
				}
			}
		});

		$('.heading-tabs li a', this.el).on('click', function(ev) {
			ev.preventDefault();
		});

		this.loadingTpl = $('.loading-tpl:first', this.el);

		$('.heading-tabs li.on', this.el).addClass('loaded');

		// In case initial tab was find
		this.initFilterForm();
	},

	injectLoadingEl: function(toEl) {
		var loadingEl = this.loadingTpl.clone();
		loadingEl.show();

		toEl.empty().append(loadingEl);
	},

	//#########################################################################
	//# Tab Loaders
	//#########################################################################

	loadBrowseTab: function(tabEl) {
		var content = this.headerTabs.getContentElFromTab(tabEl);
		tabEl.addClass('loaded');

		this.injectLoadingEl(content);

		$.ajax({
			url: BASE_URL + 'kb?_partial',
			dataType: 'html',
			success: function(html) {
				content.empty().html(html);
				DeskPRO_Window.initFeatures(content);
			}
		});
	},

	loadFindTab: function(tabEl) {
		var content = this.headerTabs.getContentElFromTab(tabEl);
		tabEl.addClass('loaded');

		this.injectLoadingEl(content);

		$.ajax({
			url: BASE_URL + 'kb/find?_partial',
			dataType: 'html',
			context: this,
			success: function(html) {
				content.empty().html(html);
				DeskPRO_Window.initFeatures(content);
				this.initFilterForm();
			}
		});
	},

	loadRecentTab: function(tabEl) {
		var content = this.headerTabs.getContentElFromTab(tabEl);
		tabEl.addClass('loaded');

		this.injectLoadingEl(content);

		$.ajax({
			url: BASE_URL + 'kb/recent?_partial',
			dataType: 'html',
			success: function(html) {
				content.empty().html(html);
				DeskPRO_Window.initFeatures(content);
			}
		});
	},

	loadPopularTab: function(tabEl) {
		var content = this.headerTabs.getContentElFromTab(tabEl);
		tabEl.addClass('loaded');

		this.injectLoadingEl(content);

		$.ajax({
			url: BASE_URL + 'kb/popular?_partial',
			dataType: 'html',
			success: function(html) {
				content.empty().html(html);
				DeskPRO_Window.initFeatures(content);
			}
		});
	},

	//#########################################################################
	//# AJAXify find form
	//#########################################################################

	initFilterForm: function() {

		var self = this;

		this.filterForm = $('#kb_find_form, #kb_find_form_btm');
		if (!this.filterForm.length) return;

		this.filterForm.on('submit', function(ev) {
			ev.preventDefault();
			self.submitFilterForm($(this));
		});

		var content = $('.tab-contents:first .find.tab-content:first', this.el);
		$('.page-link', content).on('click', function(ev) {
			ev.preventDefault();
			self.clickFilterPagenav($(this));
		});
	},

	submitFilterForm: function(form) {
		var content = $('.tab-contents:first .find.tab-content:first', this.el);
		this.injectLoadingEl(content);

		var formData = form.serializeArray();

		var formUrl = form.attr('action');
		formUrl = Orb.appendQueryData(formUrl, '_partial');

		$.ajax({
			url: formUrl,
			data: formData,
			dataType: 'html',
			type: 'POST',
			context: this,
			success: function(html) {
				content.empty().html(html);
				DeskPRO_Window.initFeatures(content);
				this.initFilterForm();
			}
		});
	},

	clickFilterPagenav: function(a) {
		var content = $('.tab-contents:first .find.tab-content:first', this.el);
		this.injectLoadingEl(content);

		var url = a.attr('href');
		url = Orb.appendQueryData(url, '_partial');

		var pos = this.el.offset();
		$(document).scrollTop(pos.top);

		$.ajax({
			url: url,
			dataType: 'html',
			type: 'GET',
			context: this,
			success: function(html) {
				content.empty().html(html);
				DeskPRO_Window.initFeatures(content);
				this.initFilterForm();
			}
		});
	}
});
