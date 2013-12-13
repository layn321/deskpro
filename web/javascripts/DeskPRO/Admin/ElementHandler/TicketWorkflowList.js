Orb.createNamespace('DeskPRO.Admin.ElementHandler');

DeskPRO.Admin.ElementHandler.TicketWorkflowList = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	initPage: function() {
		var self = this;
		this.el.on('click', 'a.edit-trigger', function(ev) {
			ev.preventDefault();

			var row = $(this).closest('article.dp-grid-row');
			self.showEditCat(row);
		});

		$('#newcat_open').on('click', function(ev) {
			ev.preventDefault();
			self.showNewCat();
		});

		// Reordering parents
		var list = $('.dep-list');
		list.sortable({
			items: '.category-row',
			update: function() {
				self.updateOrders();
			}
		});
	},

	showEditCat: function(row) {
		var self = this;
		var catId = row.data('category-id');
		var currentTitle = row.find('a.edit-trigger').text().trim();

		this.currentEditCat = catId;
		$('#editcat_title').val(currentTitle);
		$('#editcat_catid').val(catId);

		if (!this.editOverlay) {
			this.editOverlay = new DeskPRO.UI.Overlay({
				contentElement: $('#editcat_overlay')
			});
			$('#editcat_savebtn').on('click', function(ev) {
				ev.preventDefault();
				$('#editcat_overlay form').submit();
			});
		}

		this.editOverlay.open();
	},

	showNewCat: function() {
		var self = this;

		if (!this.newOverlay) {
			this.newOverlay = new DeskPRO.UI.Overlay({
				contentElement: $('#newcat_overlay')
			});
			$('#newcat_savebtn').on('click', function(ev) {
				ev.preventDefault();
				$('#newcat_overlay form').submit();
			});
		}

		this.newOverlay.open();
	},

	updateOrders: function() {
		var postData = [];
		$('article.dp-grid-row[data-category-id]', this.el).each(function() {
			if ($(this).data('category-id')) {
				postData.push({
					name: 'display_order[]',
					value: $(this).data('category-id')
				});
			}
		});

		$.ajax({
			url: this.el.data('reorder-url'),
			type: 'POST',
			data: postData
		});
	}
});
