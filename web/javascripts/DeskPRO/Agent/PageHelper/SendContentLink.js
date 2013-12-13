Orb.createNamespace('DeskPRO.Agent.PageHelper');

/**
 * Used on listings to send links to a RTE editor
 */
DeskPRO.Agent.PageHelper.SendContentLink = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(page, options) {
		var self = this;
		this.page = page;

		this.options = {
			/**
			 * The main list element that contains rows.
			 */
			contentListEl: null
		};

		this.setOptions(options);

		this.contentListEl = $(this.options.contentListEl);

		DeskPRO_Window.getTabWatcher().addTabTypeWatcher('*', this);
		var types = [
			'article', 'newarticle',
			//'download', 'newdownload',
			//'news', 'newnews',
			//'feedback', 'newfeedback'
		];

		this.addEvent('watchedTabActivated', function(tab) {
			var tabtype = DeskPRO_Window.getTabWatcher().getTabType(tab);
			if (types.indexOf(tabtype) !== -1) {
				if (tab.page && tab.page.acceptContentLink) {
					if (tab.page.acceptContentLink.isReady()) {
						$('body').addClass('content-link-control-on');
					} else {
						$('body').removeClass('content-link-control-on');
					}
				} else {
					$('body').removeClass('content-link-control-on');
				}
			} else {
				$('body').removeClass('content-link-control-on');
			}
		}, this);
		this.addEvent('watchedTabDeactivated', function(tab, isLast) {
			if (isLast) {
				$('body').removeClass('content-link-control-on');
			}
		}, this);

		this.contentListEl.on('click', '.insert-content-link', function(ev) {
			ev.stopPropagation();
			ev.preventDefault();

			var tab = DeskPRO_Window.getTabWatcher().getActiveTab();
			if (!tab || !tab.page || !tab.page.acceptContentLink) {
				return;
			}

			tab.page.acceptContentLink.sendLink($(this).data('link-title'), $(this).data('link'));
		});
	},

	updateList: function() {
		var tab = DeskPRO_Window.getTabWatcher().getActiveTab();
		if (!tab || !tab.page || !tab.page.acceptContentLink || !tab.page.acceptContentLink.isReady()) {
			$('body').removeClass('content-link-control-on');
		} else {
			$('body').addClass('content-link-control-on');
		}
	}
});
