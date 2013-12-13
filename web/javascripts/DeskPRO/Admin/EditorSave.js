Orb.createNamespace('DeskPRO.Admin.PageHandler');

/**
 * This handles an editor and reads the values into a proper data structure
 * that we can send to the server for storage.
 */
DeskPRO.Admin.EditorSave = new Orb.Class({

	/**
	 * Get the editor data from a container
	 *
	 * Returns an array of {name: x, value: x} suitable for sending via
	 * ajax with jQuery.
	 *
	 * @param {jQuery} groupEl         The container
	 * @param {String} name_prefix     The prefix to prefix form value names with
	 * @return {Object}
	 */
	getEditorData: function(groupEl, name_prefix) {
		var items = this.doGetEditorData(groupEl, name_prefix);
		$('.save-processed', groupEl).removeClass('save-processed');

		return items;
	},

	doGetEditorData: function(groupEl, name_prefix) {

		if (!name_prefix) name_prefix = 'items';

		var items = [];

		var index = 0;
		$('.display_item.display_item_group:not(.save-processed)', groupEl).each((function(i, el) {

			var el = $(el);
			if (el.is('save-processed')) return;

			el.addClass('save-processed');

			var prefix = name_prefix + '['+index+']';

			var item_data = [];
			item_data.push({name: prefix+'[item_type]', value: 'group'});

			var groupdata = $('.display_item_group_data:first', el);
			if (el.length) {
				item_data.append(this.getFormData(groupdata, prefix));
			}

			item_data.append(this.doGetEditorData(el, prefix+'[items]'));

			items.append(item_data);
			index++;
		}).bind(this));
		
		$('.display_item:not(.display_item_group, .save-processed)', groupEl).each((function(i, el) {

			var el = $(el);
			if (el.is('save-processed')) return;

			el.addClass('save-processed');

			var prefix = name_prefix + '['+index+']';

			var idPart   = $(el).data('el-id');
			var itemType = $(el).data('item-type');
			var itemId   = $(el).data('item-id');

			var item_data = [];
			item_data.push({name: prefix+'[item_type]', value: itemType});
			if (itemId) {
				item_data.push({name: prefix+'[item_id]', value: itemId});
			}

			var formFind = $(el);
			formFind = formFind.add('#rule_builder_' + idPart + ', #option_selection_' + idPart);

			item_data.append(this.getFormData(formFind, prefix));

			items.append(item_data);
			index++;

		}).bind(this));
		
		return items;
	},

	getFormData: function(formFind, prefix) {

		var item_data = [];

		var form_info = $(':input, select, textarea', formFind).serializeArray();

		Array.each(form_info, function(i) {
			var k = i.name;
			var v = i.value;

			if (k.indexOf('[') === -1) {
				k = "[" + k + "]";
			} else {
				k = k.replace(/^(.*?)\[/, "[$1][");
			}
			k = prefix + k;

			item_data.push({name: k, value: v});
		}, this);

		return item_data;
	}
});