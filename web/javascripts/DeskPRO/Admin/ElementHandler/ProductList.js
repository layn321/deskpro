Orb.createNamespace('DeskPRO.Admin.Departments');

DeskPRO.Admin.ElementHandler.ProductList = new Orb.Class({
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
			items: '.department-group',
			handle: 'article.top',
			update: function() {
				self.updateOrders();
			}
		});
		$('.department-group', list).each(function() {
			var group = $(this);
			group.sortable({
				items: 'article.child',
				update: function() {
					self.updateOrders();
				}
			});
		});
	},

	showEditCat: function(row) {
		var self = this;
		var catId = row.data('category-id');
		var currentTitle = row.find('a.edit-trigger').text().trim();

		this.currentEditCat = catId;
		$('#editcat_title').val(currentTitle);
		$('#editcat_catid').val(catId);

		var group = row.closest('.department-group');
		var parentId = group.find('> article.top').data('category-id');
		if (parentId != catId) {
			$('#editcat_parent_row').show();
			$('#editcat_parent_id').find('option[value="'+parentId+'"]').prop('selected', true);
		} else {
			if (group.find('> article.child')[0]) {
				$('#editcat_parent_row').hide();
				$('#editcat_parent_id').find('option[value="0"]').prop('selected', true);
			} else {
				$('#editcat_parent_row').show();
				$('#editcat_parent_id').find('option[value="0"]').prop('selected', true);
			}
		}

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

		// Reset last class, its needed for proper borders
		$('.department-group').each(function() {
			$(this).find('article.dp-grid-row').removeClass('last').last().addClass('last');
		});

		$.ajax({
			url: this.el.data('reorder-url'),
			type: 'POST',
			data: postData
		});
	}
});
