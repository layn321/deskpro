Orb.createNamespace('DeskPRO.UI.Select');

/**
 * The PageWidget is the visible element on the page. Typically this is a button or rendered
 * version of the selected value and accepts a click to open the SelectWidget.
 *
 * The PageWidget is main page controller that connects a select box with the menu.
 *
 * There are a few standard classnames in the widget:
 * - trigger-open-widget: Click events on these elements will open the SelectWidget
 *
 * @type {Orb.Class}
 */
DeskPRO.UI.Select.WidgetSimple = new Orb.Class({
	Extends: DeskPRO.UI.Select.Widget,

	_doRenderWidget: function() {
		var el = $('<div class="dp-ui-select-widget trigger-open-menu"><i class="icon-caret-down"></i><div class="val-list"></div></div>');
		return el;
	},

	_doInitWidget: function() {
		this.renderSelectedOptions();
	},

	_doChanged: function() {
		this.renderSelectedOptions();
	},

	_doGetmenu: function() {
		if (this.$select.data('target-menu')) {
			var menu = new DeskPRO.UI.Select.MenuHtml(this);
			return menu;
		}
	},

	renderSelectedOptions: function() {
		var opts = this.getSelectedOptions();

		var valList = this.getEl().find('.val-list');
		valList.empty();

		if (!opts[0]) {
			return;
		}

		var lastIdx = opts.length-1;
		opts.each(function(idx) {
			var span = $('<span class="val"></span>');

			var title = $.trim($(this).text());
			if (title) {
				span.text(title);
			} else {
				span.html('&nbsp;');
			}
			span.appendTo(valList);

			if (idx != lastIdx) {
				span = $('<span class="sep">,</span>');
				span.appendTo(valList);
			}
		});
	}
});