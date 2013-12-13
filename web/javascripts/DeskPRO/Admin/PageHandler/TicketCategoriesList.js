Orb.createNamespace('DeskPRO.Admin.PageHandler');

DeskPRO.Admin.PageHandler.TicketCategoriesList = new Class({
	Extends: DeskPRO.Admin.PageHandler.Basic,

	initPage: function() {
		this.initPopoutTriggers();
		//DeskPRO_Window.getMessageBroker().addMessageListener('category.list.change', this.handleListChange.bind(this));

		var self = this;
		$('#ticket_category_parentlist').sortable({
			'opacity': 0.8,
			'axis': 'y',
			'handle': '.drag-handle:first',
			'items': '> ul',
			update: function() { self.sendOrderUpdate() }
		});

		$('#ticket_category_parentlist > ul').sortable({
			'opacity': 0.8,
			'axis': 'y',
			'handle': '.drag-handle:first',
			'items': '> li.sub',
			'tolerance': 'intersect',
			stop: function (event, ui) {
				$('#ticket_category_parentlist > ul > li.sub').attr('style', '');
			},
			update: function() { self.sendOrderUpdate() }
		});
	},

	sendOrderUpdate: function() {
		var ids = [];

		$('#ticket_category_parentlist li.category').each(function() {
			ids.push({name: 'display_order[]', value: $(this).data('category-id')});
		});

		$.ajax({
			url: BASE_URL + 'admin/tickets/categories/update-orders',
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