Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.DownloadList = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.Basic,

	initPage: function(el) {
		var self = this;
		this.wrapper = el;

		this.displayOptions = new DeskPRO.Agent.PageHelper.DisplayOptions(this, {
			prefId: 'download-filter',
			resultId: this.meta.resultId,
			refreshUrl: this.meta.refreshUrl,
			prefSaveResultId: '0'
		});
		this.ownObject(this.displayOptions);

		this.selectionBar = new DeskPRO.Agent.PageHelper.SelectionBar(this, {});
		this.ownObject(this.selectionBar);

		this.listWrapper = $('section.downloads-simple-list', this.wrapper)
			.on('click', '.dl-insert-link', function() { self.insertIntoTicket($(this).data('download-id'), 'link') })
			.on('click', '.dl-insert-attach', function() { self.insertIntoTicket($(this).data('download-id'), 'attach') });

		DeskPRO_Window.getTabWatcher().addTabTypeWatcher('ticket', this);
		this.addEvent('watchedTabActivated', function(tab) {
			if (DeskPRO_Window.getTabWatcher().getTabType(tab) == 'ticket') {
				self.initVisibleTicket();
			}
		});
		this.addEvent('watchedTabDeactivated', function(tab) {
			if (DeskPRO_Window.getTabWatcher().getTabType(tab) == 'ticket') {
				self.removeVisibleTicket();
			}
		});

		// Or if we're already viewing a tab ticket...
		if (DeskPRO_Window.getTabWatcher().isTabTypeActive('ticket')) {
			self.initVisibleTicket();
		}

		this.relatedContentList = new DeskPRO.Agent.PageHelper.RelatedContentList(this, {
			contentListEl: this.listWrapper
		});
		this.ownObject(this.relatedContentList);

		// Sorting options
		var sortMenuBtn = $('.order-by-menu-trigger', this.wrapper).first();
		this.sortingMenu = new DeskPRO.UI.Menu({
			triggerElement: sortMenuBtn,
			menuElement: $('.order-by-menu', this.wrapper).first(),
			onItemClicked: function(info) {
				var item = $(info.itemEl);

				var prop = item.data('order-by')
				var label = item.text().trim();

				// Change the displayed label for some visual feedback
				$('.label', sortMenuBtn).text(label);

				var disOptWrap = self.displayOptions.getWrapperElement();
				var sel = $('select.sel-order-by', disOptWrap);
				$('option', sel).prop('selected', false);
				$('option.' + prop, sel).prop('selected', true);

				self.displayOptions.saveAndRefresh();
			}
		});
		this.ownObject(this.sortingMenu);

		this.enableHighlightOpenRows('download', 'download_id', 'article.download-');
		this.listNav = new DeskPRO.Agent.PageHelper.ListNav(this);

		// Cat editor
		this._initCatEditor();
	},

	initVisibleTicket: function() {
		this.listWrapper.addClass('with-visible-ticket');
	},

	removeVisibleTicket: function() {
		this.listWrapper.removeClass('with-visible-ticket');
	},

	insertIntoTicket: function(download_id, action) {

		var ticketTab = DeskPRO_Window.getTabWatcher().getActiveTabIfType('ticket');
		if (!ticketTab) {
			return;
		}

		var ticketPage = ticketTab.page;

		$.ajax({
			url: BASE_URL + 'agent/downloads/file/'+download_id+'/info',
			type: 'GET',
			dataType: 'json',
			success: function(data) {
				if (action == 'attach') {
					ticketPage.addAttachToList(data);
				} else {
					ticketPage.appendToMessage(data.permalink);
				}
			}
		});
	},

	_initCatEditor: function() {
		var self = this;
		var catEl = this.getEl('tab_cat');
		if (!catEl[0]) {
			return;
		}

		var tree = this.getEl('cattree');
		var treeData = tree.data('treedata');
		var treeSave = this.getEl('cattree_struct');
		tree.tree({
			data: treeData,
			dragAndDrop: true
		});
		tree.bind('tree.move', function(event) {
			event.move_info.do_move();
			treeSave.val(tree.tree('toJson'));
		});

		this.getEl('catfoot').find('.cat-save-trigger').on('click', function(ev){
			Orb.cancelEvent(ev);

			var postData = catEl.find('input').serializeArray();

			self.getEl('catfoot').addClass('dp-loading-on');
			$.ajax({
				url: $(this).data('save-url'),
				data: postData,
				type: 'POST',
				dataType: 'json',
				complete: function() {
					self.getEl('catfoot').removeClass('dp-loading-on');
				},
				success: function() {
					DeskPRO_Window.sections.publish_section.reload();
				}
			});
		});

		var delCat = this.getEl('del_cat');
		delCat.find('.cat-del-trigger').on('click', function(ev) {
			Orb.cancelEvent(ev);
			delCat.addClass('dp-loading-on');

			$.ajax({
				url: $(this).data('save-url'),
				type: 'POST',
				dataType: 'json',
				complete: function() {
					delCat.removeClass('dp-loading-on');
				},
				success: function(ret) {
					if (ret.error_code && ret.error_code == 'not_empty') {
						DeskPRO_Window.showAlert('The category could not be deleted because it is not empty.');
						return;
					}

					DeskPRO_Window.sections.publish_section.reload();
					DeskPRO_Window.runPageRoute('listpane:' + BASE_URL + 'agent/downloads/list/0');
				}
			});
		});
	}
});
