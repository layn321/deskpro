Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.KbPendingArticles = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.Basic,

	initPage: function(el) {
		var self = this;
		this.wrapper = el;

		this.actionsMenu = new DeskPRO.UI.Menu({
			menuElement: $('ul.actions-menu:first', this.wrapper),
			triggerElement: $('.perform-actions-trigger:first', this.wrapper),
			onItemClicked: function(info) {
				var ids = self.selectionBar.getCheckedValues();
				var els = [];

				var formData = [];
				Array.each(ids, function(id) {
					formData.push({
						name: 'ids[]',
						value: id
					});

					els.push($('article.pending-article-' + id + ':first', self.wrapper).get(0));
				});

				$(els).fadeOut();

				var action = $(info.itemEl).data('action');

				$.ajax({
					url: BASE_URL + 'agent/kb/pending-articles/mass-actions/' + action,
					data: formData,
					type: 'POST',
					dataType: 'json',
					error: function() {
						$(els).show();
					},
					success: function() {
						$(els).remove();
						DeskPRO_Window.util.modCountEl('#kb_pending_count', '-', els.length);
					}
				});
			}
		});
		this.ownObject(this.actionsMenu);

		this.selectionBar = new DeskPRO.Agent.PageHelper.SelectionBar(this, {
			onButtonClick: function(ev) {
				self.actionsMenu.open(ev);
			}/*,
			onCountChange: function(count) {
				var isOpen = self.actionsMenu.isOpen();

				if (count > 0 && !isOpen) {
					self.actionsMenu.open();
				} else if (count <= 0 && isOpen) {
					self.actionsMenu.close();
				}
			}*/
		});
		this.ownObject(this.selectionBar);

		DeskPRO_Window.getMessageBroker().addMessageListener('kb.pending_article_removed', function(data) {
			self.removeFromList(data.pending_article_id);
		});

		var newFormOverlay = new DeskPRO.UI.Overlay({
			contentElement: this.getEl('add_new_overlay'),
			zIndex: 'top'
		});

		$('.add-new-trigger', this.el).on('click', function() {
			newFormOverlay.open();
		});
		$('.save-new-trigger', this.getEl('add_new_overlay')).on('click', function() {
			self.saveNewPendingArticle();
			newFormOverlay.close();
		});

		$('section.pending-articles-list', this.wrapper).on('click', '.pending-delete', function(ev) {
			ev.stopPropagation();
			var row = $(this);
			var x = 0;
			while (!row.is('article')) {
				if (x++ > 10) return;
				row = row.parent();
			}

			row.slideUp('fast');

			var id = $('input.item-select', row).val();

			$.ajax({
				url: BASE_URL + 'agent/kb/pending-articles/' + id + '/remove',
				type: 'POST',
				dataType: 'json',
				error: function() {
					row.show();
				},
				success: function() {
					row.remove();
					DeskPRO_Window.util.modCountEl('#kb_pending_count', '-');
				}
			});
		});

		$('section.pending-articles-list', this.wrapper).on('click', '.pending-create', function(ev) {
			ev.stopPropagation();
			var row = $(this);
			var x = 0;
			while (!row.is('article')) {
				if (x++ > 10) return;
				row = row.parent();
			}

			var id = $('input.item-select', row).val();
			var ticketRoute = $('input.item-select', row).data('ticket-route');

			$.ajax({
				url: BASE_URL + 'agent/kb/pending-articles/' + id + '/info',
				type: 'POST',
				dataType: 'json',
				success: function(data) {
					if (ticketRoute) {
						DeskPRO_Window.runPageRoute(ticketRoute);
					}

					if (DeskPRO_Window.newArticleLoader) {
						DeskPRO_Window.newArticleLoader.open(function(page) {
							page.setPendingArticle(data);

							if (data.ticket_id) {
								var closeTicketId = data.ticket_id;
								page.addEvent('destroy', function() {
									Object.each(DeskPRO_Window.TabBar.getTabs(), function(tab, id) {
										if (tab.page && tab.page.meta.ticket_id == closeTicketId) {
											DeskPRO_Window.removePage(tab.page);
										}
									});
								});
							}
						});
					}
				}
			});
		});
	},

	saveNewPendingArticle: function() {
		var formWrap = this.getEl('add_new_overlay');
		var val = $('textarea', this.getEl('add_new_overlay')).val().trim();

		if (!val) {
			formWrap.slideUp();
			return;
		}

		var data = [];
		data.push({
			name: 'comment',
			value: val
		});

		$.ajax({
			url: BASE_URL + 'agent/kb/pending-articles/new',
			type: 'POST',
			data: data,
			context: this,
			dataType: 'json',
			success: function(info) {

				$('textarea:first', formWrap).val('');
				formWrap.slideUp();

				var addEl = $(info.row_html);
				$('section.pending-articles-list', this.wrapper).prepend(addEl);

				DeskPRO_Window.util.modCountEl('#kb_pending_count', '+');
			}
		});
	},

	removeFromList: function(id) {
		$('article.pending-article-' + id, this.wrapper).slideUp('fast');
		DeskPRO_Window.util.modCountEl('#kb_pending_count', '-');
		DeskPRO_Window.util.modCountEl(this.getEl('count'), '-');
	}
});
