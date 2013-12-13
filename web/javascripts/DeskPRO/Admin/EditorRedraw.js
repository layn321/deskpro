Orb.createNamespace('DeskPRO.Admin');

/**
 * This hadnles resetting the editor to a saved state with the data provided.
 * For example, data saved to the database and then refreshing the page, this class
 * takes the data saved and redraws the editor with all the options.
 */
DeskPRO.Admin.EditorRedraw = new Orb.Class({
	setItemsForSection: function(data, editor, choice_container, target) {
		//DP.console.log("Args: %o", arguments);

		Array.each(data, function(item) {

			if (item.item_type == 'group') {

				var itemEl = editor.doAddGroup(target);
				$('.display_item_group_data input[name="title"]', itemEl).val(item.title);

				if (item.items) {
					this.setItemsForSection(item.items, editor, choice_container, itemEl);
				}

			} else {
				if (item.item_id) {
					var li_option = $('li[data-item-type="'+item.item_type+'"][data-item-id="'+item.item_id+'"]', choice_container);
				} else {
					var li_option = $('li[data-item-type="'+item.item_type+'"]', choice_container);
				}

				if (!li_option.length) {
					DP.console.error('Invalid choice for %s: %o', item.item_type, item);
					return;
				}

				editor.addDisplayItemFromEl(li_option, target, false);
			}

		}, this);
	}
});
