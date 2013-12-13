Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.NewsList = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.Basic,

	initializeProperties: function() {
		this.parent();
		this.wrapper = null;
	},

	initPage: function(el) {
		this.wrapper = el;

		this.displayOptions = new DeskPRO.Agent.PageHelper.DisplayOptions(this, {
			prefId: 'news-filter',
			resultId: this.meta.resultId,
			refreshUrl: this.meta.refreshUrl,
			prefSaveResultId: '0'
		});
		this.ownObject(this.displayOptions);

		this.selectionBar = new DeskPRO.Agent.PageHelper.SelectionBar(this, {

		});
		this.ownObject(this.selectionBar);

		this.listWrapper = $('section.news-simple-list', this.wrapper);

		this.relatedContentList = new DeskPRO.Agent.PageHelper.RelatedContentList(this, {
			contentListEl: this.listWrapper
		});
		this.ownObject(this.relatedContentList);

		this.enableHighlightOpenRows('news', 'news_id', 'article.news-');

		this.listNav = new DeskPRO.Agent.PageHelper.ListNav(this);

		// Cat editor
		this._initCatEditor();
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
					DeskPRO_Window.runPageRoute('listpane:' + BASE_URL + 'agent/kb/list/0');
				}
			});
		});
	}
});
