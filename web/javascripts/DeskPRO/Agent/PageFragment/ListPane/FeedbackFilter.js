Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.FeedbackFilter = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.Basic,

	initializeProperties: function() {
		this.parent();
		this.wrapper = null;
		this.filterSearchForm = null;
	},

	initPage: function(el) {
		var self = this;
		this.wrapper = el;

		this.displayOptions = new DeskPRO.Agent.PageHelper.DisplayOptions(this, {
			prefId: 'feedback-filter',
			resultId: this.meta.resultId,
			refreshUrl: this.meta.refreshUrl,
			prefSaveResultId: '0'
		});
		this.ownObject(this.displayOptions);

		this.selectionBar = new DeskPRO.Agent.PageHelper.SelectionBar(this, {
			/*onCountChange: function(count) {
				var isOpen = self.massActionsMenu.isOpen();

				if (count > 0 && !isOpen) {
					self.massActionsMenu.open();
				} else if (count <= 0 && isOpen) {
					self.massActionsMenu.close();
				}
			}*/
		});
		this.ownObject(this.selectionBar);

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

		this.listWrapper = $('section.feedback-simple-list', this.wrapper);

		this.relatedContentList = new DeskPRO.Agent.PageHelper.RelatedContentList(this, {
			contentListEl: this.listWrapper
		});
		this.ownObject(this.relatedContentList);

		this.massActionsMenu = new DeskPRO.UI.Menu({
			triggerElement: $('.perform-actions-trigger:first', this.wrapper),
			menuElement: $('.actions-menu:first', this.wrapper),
			onItemClicked: function(info) {
				var itemEl = $(info.itemEl);
				var menuEl = itemEl.parent();
				var menuType = menuEl.data('menu-type');

				var postData = self.selectionBar.getCheckedFormValues('ids[]');
				var removeFromList = false;
				var action = '';

				switch (menuType) {
					case 'feedback-status-menu':

						action = 'set-status';

						postData.push({
							name: 'status',
							value: itemEl.data('option-value')
						});
						break;

					case 'feedback-category-menu':

						action = 'set-category';

						postData.push({
							name: 'category_id',
							value: itemEl.data('category-id')
						});

						break;

					case 'feedback-massactions-menu':

						switch (itemEl.data('action')) {
							case 'delete':
								action = 'set-status';
								postData.push({
									name: 'status',
									value: 'hidden.deleted'
								});

								removeFromList = true;

								break;

							case 'spam':
								action = 'set-status';
								postData.push({
									name: 'status',
									value: 'hidden.spam'
								});

								removeFromList = true;

								break;
						}

						break;

					default:
						return;
						break;
				}

				$.ajax({
					url: BASE_URL + 'agent/feedback/filter/mass-actions/' + action,
					data: postData,
					type: 'POST',
					dataType: 'json',
					success: function(data) {
						if (removeFromList) {
							self.selectionBar.getChecked().parent().fadeOut('fast');
						}

						self.selectionBar.checkNone();

						DeskPRO_Window.runPageRoute('listpane:' + self.meta.routeUrl);
						DeskPRO_Window.sections.feedback_section.reload();
					}
				});
			}
		});
		this.ownObject(this.massActionsMenu);

		var opt = {
			resultIds: this.meta.resultIds,
			perPage: this.meta.perPage || 50,
			refreshMode: true,
			currentPage: this.meta.currentPage
		};
		this.resultsHelper = new DeskPRO.Agent.PageHelper.Results(this, opt);
		this.ownObject(this.resultsHelper);

		this.enableHighlightOpenRows('feedback', 'feedback_id', 'article.feedback-');

		this.listNav = new DeskPRO.Agent.PageHelper.ListNav(this);
	}
});
