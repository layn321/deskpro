Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.PeopleList = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'people-list';
		this.wrapper = null;
		this.contentWrapper = null;
		this.overlay = null;
		this.appendUrl = null;
		this.actionsBarHelper = null;
		this.resultTypeName = 'filter';
		this.resultTypeId = 0;
	},

	initPage: function(el) {

		var self = this;

		this.wrapper = $(el);
		this.contentWrapper = $('div.content:first', this.wrapper);

		this.resultTypeId = this.meta.cache_id || 0;

		if (this.getMetaData('noResults')) {
			this.noMoreResults = true;
			$('.no-more-results', this.contentWrapper).show();
		}

		this.displayOptions = new DeskPRO.Agent.PageHelper.DisplayOptions(this, {
			prefId: 'people-filter',
			resultId: this.resultId,
			refreshUrl: this.meta.refreshUrl,
			isListView: (this.meta.viewType == 'list' ? true : false)
		});
		this.ownObject(this.displayOptions);

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

		$('.detail-view-trigger', this.wrapper).on('click', (function() {
			this.switchViewType('list');
		}).bind(this));

		this.massActionsMenu = new DeskPRO.UI.Menu({
			triggerElement: $('.perform-actions-trigger:first', this.wrapper),
			menuElement: $('.actions-menu:first', this.wrapper),
			onItemClicked: function(info) {
				var itemEl = $(info.itemEl);
				var menuEl = itemEl.parent();
				var action = itemEl.data('action');

				if (menuEl.is('.submenu')) {
					action = menuEl.data('action');
				}

				var postData = self.selectionBar.getCheckedFormValues('ids');
				var removeFromList = false;

				switch (action) {
					case 'delete':

						break;

					case 'add-to-organization':
						var id = itemEl.data('organization-id');
						if (!id) {
							return;
						}

						postData.push({
							name: 'organization_id',
							value: id
						});
						break;

					case 'del-from-organization':

						break;

					case 'add-to-usergroup':
						var id = itemEl.data('usergroup-id');
						if (!id) {
							return;
						}

						postData.push({
							name: 'usergroup_id',
							value: id
						});
						break;

					case 'del-from-usergroup':
						var id = itemEl.data('usergroup-id');
						if (!id) {
							return;
						}

						postData.push({
							name: 'usergroup_id',
							value: id
						});
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
						} else {
							self.selectionBar.getChecked().each(function() {
								var name = $('.subject', $(this).parent());
								DeskPRO_Window.util.showSavePuff(name);
							});
						}

						self.selectionBar.checkNone();
					}
				});
			}
		});
		this.ownObject(this.massActionsMenu);

		this.enableHighlightOpenRows('person', 'person_id', 'article.person-');

		var opt = {
			resultIds: this.meta.peopleResultIds,
			perPage: this.meta.perPage || 50
		};
		if (this.meta.viewType == 'list') {
			opt.resultRowSelector = 'tr.row-item';
			opt.resultsContainer = $('.table-result-list table', el);
			opt.navEl = $('.bottom-action-bar', el);
		}
		this.resultsHelper = new DeskPRO.Agent.PageHelper.Results(this, opt);
		this.ownObject(this.resultsHelper);

		// We dont need them anymore, and resultsHelper
		// has its own strucutred array anyway,
		// since it could be large we can delete it from memory
		delete this.meta.peopleResultIds;

		if (this.meta.viewType != 'list') {
			this.listNav = new DeskPRO.Agent.PageHelper.ListNav(this);
		}

		this.wrapper.on('click', 'button.agent-confirm-approve', function(ev) {
			ev.preventDefault();
			var el = $(this);
			DeskPRO_Window.util.ajaxWithClientMessages({
				url: BASE_URL + 'agent/people/validate/approve',
				data: { 'people_ids[]': el.data('person-id') },
				success: function() {
					DeskPRO_Window.getMessageBroker().sendMessage('agent.person.confirmed', { person_id: el.data('person-id') });
					el.closest('.validation-row').remove();
					self.updateUi();
				}
			});
		});
		this.wrapper.on('click', 'button.agent-confirm-delete', function(ev) {
			ev.preventDefault();
			var el = $(this);
			DeskPRO_Window.util.ajaxWithClientMessages({
				url: BASE_URL + 'agent/people/validate/delete',
				data: { 'people_ids[]': el.data('person-id') },
				success: function() {
					DeskPRO_Window.getMessageBroker().sendMessage('agent.person.removed', { person_id: el.data('person-id') });
					el.closest('article.row-item').remove();
					self.updateUi();
				}
			});
		});

		DeskPRO_Window.getMessageBroker().addMessageListener('agent.person.removed', function(info) {
			var row = self.wrapper.find('article.person-' + info.person_id);
			row.remove();
			self.updateUi();
		});
		DeskPRO_Window.getMessageBroker().addMessageListener('agent.person.confirmed', function(info) {
			var row = self.wrapper.find('article.person-' + info.person_id);
			row.find('.validation-row').remove();
			self.updateUi();
		});
	},

	destroyPage: function() {

	},

	switchViewType: function(view_type) {

		var new_url = this.meta.viewTypeUrl.replace('$view_type', view_type);

		if (view_type == 'list') {
			var oldlist = this.listview;
			this.listview = new DeskPRO.Agent.PageHelper.PeopleList.ListView(this);

			if (oldlist && !oldlist.OBJ_DESTROYED) {
				this.listview.addEvent('ajaxLoaded', function() {
					if (!oldlist.OBJ_DESTROYED) {
						oldlist.destroy();
					}
				});
			}

			this.listview.open();
			return;
		}

		DeskPRO_Window.loadListPane(new_url, null, function() {
			DeskPRO_Window.removePage(self);
		});
	},

	loadNewListviewUrl: function(new_url) {
		var oldlist = this.listview;
		this.listview = new DeskPRO.Agent.PageHelper.PeopleList.ListView({ load_url: new_url });

		if (oldlist && !oldlist.OBJ_DESTROYED) {
			oldlist.showInnerLoading();
			this.listview.addEvent('ajaxLoaded', function() {
				if (!oldlist.OBJ_DESTROYED) {
					oldlist.destroy();
				}
			});
		}

		this.listview.open();
	},

	saveDisplayOptions: function() {

		$('.loading-off', this.displayOptionsWrapper).hide();
		$('.loading-on', this.displayOptionsWrapper).show();

		var data = [];
		var pref_name = 'prefs[agent.ui.people-'+ this.resultTypeName + '-display-fields.' + this.resultTypeId +'][]';

		$('input[type="checkbox"]:checked', this.displayOptionsList).each(function() {
			data.push({
				name: pref_name,
				value: $(this).attr('name')
			});
		});


		// and the ordering
		data.push({
			name: 'prefs[agent.ui.people-'+ this.resultTypeName + '-order-by.' + this.resultTypeId +']',
			value: $('select[name="order_by"]', this.displayOptionsWrapper).val()
		});

		// We reload the same page which will have changes applied
		var url = this.getMetaData('refreshUrl');
		if (this.appendUrl) {
			url += this.appendUrl;
		}

		var self = this;

		$.ajax({
			timeout: 20000,
			type: 'POST',
			url: this.getMetaData('saveListPrefsUrl'),
			data: data,
			success: function() {

				DeskPRO_Window.loadListPane(url, null, function() {
					DeskPRO_Window.removePage(self);
				});

			}
		});
	}
});
