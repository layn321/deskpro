Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.PublishDraftsList = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.Basic,

	initPage: function(el) {
		var self = this;
		this.wrapper = el;

		DeskPRO_Window.getMessageBroker().addMessageListener('publish.drafts.list-remove', function (info) {
			$('article.' + info.typename + '-' + info.contentId, this.wrapper).slideUp();
		}, this);

		this.actionsMenu = new DeskPRO.UI.Menu({
			triggerElement: $('.perform-actions-trigger:first', this.wrapper),
			menuElement: $('ul.actions-menu:first', this.wrapper),
			onItemClicked: function(info) {
				var data = [];
				var lines = [];
				$('input.item-select:checked', this.wrapper).each(function() {
					lines.push($(this).parent().get(0));
					var typename = $(this).data('content-type');
					var id = $(this).data('content-id');

					if (typename && id) {
						data.push({
							name: 'content[' + typename + '][]',
							value: id
						});
					}
				});

				if (!data.length) {
					return;
				}

				var action = $(info.itemEl).data('action');

				$.ajax({
					url: BASE_URL + 'agent/publish/drafts/mass-actions/' + action,
					data: data,
					type: 'POST',
					dataType: 'json',
					context: this,
					success: function(data) {
						self.selectionBar.checkNone();
						if (data.affected) {
							Array.each(data.affected, function(info) {
								DeskPRO_Window.getMessageBroker().sendMessage('publish.drafts.list-remove', info);
							}, this);
						}
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
	}
});
