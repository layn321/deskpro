Orb.createNamespace('DeskPRO.UI.Select');

/**
 * MenuHtml takes any arbitrary HTML div and positions it exactly above
 * the Widget element with same width, and when it closes,
 * it syncs any elements with the class widget-val to the select box.
 *
 * This allows, for example, to display a multiselect box as a number of checkboxes.
 * If they are checked, then those values will be used to select the same values in the
 * bound select box.
 *
 * @type {Orb.Class}
 */
DeskPRO.UI.Select.MenuHtml = new Orb.Class({
	Extends: DeskPRO.UI.Select.Menu,

	_doInitMenu: function() {
		var self = this;
		var select = this.getWidget().getSelect();
		var isMulti = select.is('[multiple]');
		var zindex = select.data('zindex');
		var closeOnSelect = select.data('select-close');

		if (select.data('target-menu') == 'auto') {
			var name = Orb.uuid();
			var inputType = isMulti ? 'checkbox' : 'radio';
			this.$menu = $('<div class="source-pane-select-menu"></div>');
			this.$menu.append('<i class="icon-caret-up"></i>');

			if (select.find('optgroup')[0]) {
				select.find('optgroup').each(function() {
					var group = $('<div class="group"></div>');

					var groupTitle = $('<strong></strong>');
					groupTitle.text($.trim($(this).attr('label')));
					groupTitle.appendTo(group);

					var checkList = $('<ul class="checkbox-list"></ul>');
					$(this).find('option').each(function() {
						var li = $('<li><label><input type="'+inputType+'" class="widget-val" /> <span></span></label></li>');
						li.find('input').val($(this).val()).attr('name', name);
						li.find('span').text($.trim($(this).data('title') || $(this).text()));
						li.appendTo(checkList);
					});

					checkList.appendTo(group);
					group.appendTo(self.$menu);
				});
			} else {
				var checkList = $('<ul class="checkbox-list"></ul>');
				select.find('option').each(function() {
					var li = $('<li><label><input type="'+inputType+'" class="widget-val" /> <span></span></label></li>');
					li.find('input').val($(this).val()).attr('name', name);
					li.find('span').text($.trim($(this).data('title') || $(this).text()));
					li.appendTo(checkList);
				});

				checkList.appendTo(this.$menu);
			}

		} else {
			this.$menu = $(select.data('target-menu'));
		}

		if (select.data('menu-addclass')) {
			this.$menu.addClass(select.data('menu-addclass'));
		}

		this.$menu.find('trigger-close-menu').on('click', function(ev) {
			Orb.cancelEvent(ev);
			self.close();
		});
		this.$menu.detach().appendTo('body');

		this.$positionOver = this.getWidget().getEl();

		this.$shim = $('<div class="dp-shim zindex-chrome4"></div>');
		if (zindex) {
			this.$shim.css('z-index', zindex);
			this.$menu.css('z-index', zindex+1);
		}
		this.$shim.appendTo('body');
		this.$shim.on('click', function(ev) {
			Orb.cancelEvent(ev);
			self.close();
		});

		// Init selections based on the bound select box
		this.$menu.find('.widget-val').each(function() {
			var el = $(this);
			if (el.is(':checkbox, :radio')) {
				if (self.getWidget().isValueSelected(el.val())) {
					el.prop('checked', true);

					if (closeOnSelect) {
						el.on('click', function() { self.close(); });
					}
				}
			} else if (el.is('select')) {
				el.find('option').each(function() {
					if (self.getWidget().isValueSelected($(this).val())) {
						$(this).prop('selected', true);
					}
				});
			}
		});
	},

	_doOpenMenu: function() {
		var pos = this.$positionOver.offset();
		var w = this.$positionOver.width();

		this.$menu.css({
			top: pos.top,
			left: pos.left,
			'min-width': w
		});

		this.$menu.show();
		this.$shim.show();
	},

	_doCloseMenu: function() {
		this.$menu.hide();
		this.$shim.hide();

		this.getWidget().setSelectedValues(this.getSelectedValues());
	},

	_doDestroy: function() {
		if (this.$menu) {
			this.$menu.detach();
			this.$shim.detach();
		}
	},

	getSelectedValues: function() {
		var optionEls = this.$menu.find('.widget-val');
		var values = [];

		optionEls.each(function() {
			var el = $(this);

			if (el.is(':checkbox, :radio')) {
				if (el.is(':checked')) {
					values.push(el.val());
				}
			} else if (el.is('select')) {
				var opts = el.find('option').find(':selected');
				opts.each(function() {
					values.push($(this).val());
				});
			}
		});

		return values;
	}
});