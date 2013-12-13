Orb.createNamespace('DeskPRO.Admin.PageHandler');

DeskPRO.Admin.PageHandler.ProductsList = new Class({
	Extends: DeskPRO.Admin.PageHandler.Basic,

	initPage: function() {
		this.initPopoutTriggers();

		var self = this;
		$('#ticket_product_parentlist').sortable({
			'opacity': 0.8,
			'axis': 'y',
			'handle': '.drag-handle:first',
			'items': '> ul',
			update: function() { self.sendOrderUpdate() }
		});

		$('#ticket_product_parentlist > ul').sortable({
			'opacity': 0.8,
			'axis': 'y',
			'handle': '.drag-handle:first',
			'items': '> li.sub',
			'tolerance': 'intersect',
			stop: function (event, ui) {
				$('#ticket_product_parentlist > ul > li.sub').attr('style', '');
			},
			update: function() { self.sendOrderUpdate() }
		});

		$('.delete-trigger').on('click', function() {
			var id = $(this).data('product-id');
			$.ajax({
				url: BASE_URL + 'admin/products/' + id + '/delete',
				dataType: 'json',
				type: 'POST',
				success: function(data) {
					if (data.deleted_ids) {
						Array.each(data.deleted_ids, function(id) {
							$('li.product-' + id).fadeOut();
						});
					}
				}
			});
		});
	},

	sendOrderUpdate: function() {
		var ids = [];

		$('#ticket_product_parentlist li.category').each(function() {
			ids.push({name: 'display_order[]', value: $(this).data('product-id')});
		});

		$.ajax({
			url: BASE_URL + 'admin/tickets/products/update-orders',
			dataType: 'json',
			data: ids,
			type: 'POST'
		});
	},

	handleListChange: function(info) {
		var exist = $('li.'+info.typename+'-'+info.category_id, list);

		if (exist.length) {
			var row = $(info.row_html);
			exist.replaceWith(row);
		} else {
			var list = $('ul.item-list.category-list-'+info.parent_id+':first');

			if (list.length) {
				var row = $(info.row_html);
				list.append(row);
			} else {
				var row = $('<ul class="item-list category-list-'+info.category_id+'">'+info.row_html+'</ul>');
				$('div.cat-lists').prepend(row);
			}
		}

		this.initPopoutTriggers(row);
	}
});
