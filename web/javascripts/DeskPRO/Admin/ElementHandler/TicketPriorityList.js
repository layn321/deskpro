Orb.createNamespace('DeskPRO.Admin.Departments');

DeskPRO.Admin.ElementHandler.TicketPriorityList = new Orb.Class({
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
	},

	showEditCat: function(row) {
		var self = this;
		var catId = row.data('category-id');
		var currentTitle = row.find('a.edit-trigger').text().trim();
		var currentPriority = row.data('priority');

		this.currentEditCat = catId;
		$('#editcat_title').val(currentTitle);
		$('#editcat_catid').val(catId);
		$('#editcat_pri').val(currentPriority);

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
	}
});
