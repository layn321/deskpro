Orb.createNamespace('DeskPRO.Admin.PageHandler');

DeskPRO.Admin.PageHandler.GeneralSortableList = new Class({
	Extends: DeskPRO.Admin.PageHandler.Basic,

	initPage: function() {
		this.initPopoutTriggers(this.contextEl);
		this.getMessageBroker().addMessageListener(this.contextEl.attr('id') + '.change', this.handleListChange, this);

		var self = this;
		$('ul.item-list:first', this.contextEl).sortable({
			'opacity': 0.8,
			'axis': 'y',
			'handle': '.drag-handle:first',
			'items': '> li',
			update: function() { self.sendOrderUpdate() }
		});
	},

	sendOrderUpdate: function() {
		var ids = [];

		var elname = this.options.orderIdAttr;
		var updateurl = this.options.orderUpdateUrl;

		$('ul.item-list:first > li', this.contextEl).each(function() {
			ids.push({name: 'display_order[]', value: $(this).data(elname)});
		});

		$.ajax({
			url: updateurl,
			dataType: 'json',
			data: ids,
			type: 'POST'
		});
	},

	handleListChange: function(info) {
		var list = $('ul.item-list:first', this.contextEl);
		var exist = $(info.item_selector, list);

		var row = $(info.row_html);
		this.initPopoutTriggers(row);

		if (exist.length) {
			exist.replaceWith(row);
		} else {
			list.prepend(row);
		}
	}
});
