Orb.createNamespace('DeskPRO.Admin.PageHandler');

DeskPRO.Admin.PageHandler.DepartmentsList = new Class({
	Extends: DeskPRO.Admin.PageHandler.Basic,

	initPage: function() {
		this.initPopoutTriggers();
		DeskPRO_Window.getMessageBroker().addMessageListener('departments.list.change', this.handleListChange, this);

		var self = this;
		$('#departments_parentlist').sortable({
			'opacity': 0.8,
			'axis': 'y',
			'handle': '.drag-handle:first',
			'items': '> ul',
			update: function() { self.sendOrderUpdate() }
		});

		$('#departments_parentlist > ul').sortable({
			'opacity': 0.8,
			'axis': 'y',
			'handle': '.drag-handle:first',
			'items': '> li.sub',
			'tolerance': 'intersect',
			stop: function (event, ui) {
				$('#departments_parentlist > ul > li.sub').attr('style', '');
			},
			update: function() { self.sendOrderUpdate() }
		});
	},

	sendOrderUpdate: function() {
		var ids = [];

		$('#departments_parentlist li.department').each(function() {
			ids.push({name: 'display_order[]', value: $(this).data('department-id')});
		});

		$.ajax({
			url: BASE_URL + 'admin/departments/update-orders',
			dataType: 'json',
			data: ids,
			type: 'POST'
		});
	},


	handleListChange: function(info) {
		var exist = $('li.'+info.typename+'-'+info.department_id, list);

		if (exist.length) {
			var row = $(info.row_html);
			exist.replaceWith(row);
		} else {
			var list = $('ul.item-list.department-list-'+info.parent_id+':first');

			if (list.length) {
				var row = $(info.row_html);
				list.append(row);
			} else {
				var row = $('<ul class="item-list department-list-'+info.department_id+'">'+info.row_html+'</ul>');
				$('div.dep-lists').prepend(row);
			}
		}

		this.initPopoutTriggers(row);
	}
});
