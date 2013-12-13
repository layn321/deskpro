Orb.createNamespace('DeskPRO.UI');

/**
 * Optionbox but this helps build the markup required for it.
 */
DeskPRO.UI.OptionBoxBuilder = new Orb.Class({
	Extends: DeskPRO.UI.OptionBox,

	initialize: function(options) {
		var self = this;
		var selectEl;

		var tpl = [];
		tpl.push('<div class="optionbox">');
		tpl.push('	<section data-section-name="default">');
		tpl.push('		<header>');
		tpl.push('			<h3>&nbsp;</h3>');
		tpl.push('			<input type="text" class="filter-box" placeholder="Filter..." />');
		tpl.push('		</header>');
		tpl.push('		<ul>');
		tpl.push('		</ul>');
		tpl.push('	</section>');
		tpl.push('</div>');

		tpl = tpl.join('');

		var obEl = $(tpl);

		if (options.title) {
			$('header h3', obEl).text(options.title);
		}

		if (options.addClass) {
			obEl.addClass(options.addClass);
		}

		var randid = Orb.uuid();

		var bindEl = null;
		if (options.values.is && options.values.is('select')) {
			if (options.values.attr('multiple')) {
				options.selectType = 'checkbox';
			} else {
				options.selectType = 'radio';
			}

			var hasSelected = false;
			selectEl = options.values;
			options.values = [];

			var selected_text = '';
			var selectoptions = $('option', selectEl);
			var is_sub = false;
			selectoptions.each(function(index, el) {
				el = $(el);
				var label = el.text().trim();

				if (el.parent().is('optgroup')) {
					label = el.parent().attr('label') + ' > ' + label;
				}

				var has_child = false;
				var is_child = (label.indexOf('--') !== -1);
				var child_depth = 0;
				if (is_child) {
					child_depth = 1;
					if (label.indexOf('----') !== -1) child_depth = 2;
					if (label.indexOf('------') !== -1) child_depth = 3;
					if (label.indexOf('--------') !== -1) child_depth = 4;
					if (label.indexOf('----------') !== -1) child_depth = 5;
					if (label.indexOf('------------') !== -1) child_depth = 6;
					if (label.indexOf('--------------') !== -1) child_depth = 7;
					label = label.replace(/^\-\-/, '').trim();
				} else {
					has_child = (el.next().text().trim().indexOf('--') !== -1);
				}

				if (!selected_text || el.is(':selected')) {
					selected_text = el.text();
				}
				if (has_child) {
					is_sub = true;
					options.values.push({
						label: label,
						value: el.val(),
						hasChild: has_child,
						hasParent: is_child,
						childDepth: child_depth,
						isSelected: el.hasClass('start') || el.data('start')
					});
				} else {
					if (!is_child) is_sub = false;
					if (!hasSelected && options.selectDefault) {
						el.addClass('start');
						hasSelected = true;
					}
					options.values.push({
						label: label,
						value: el.val(),
						hasChild: has_child,
						hasParent: is_child,
						childDepth: child_depth,
						isSelected: (el.hasClass('start') || el.data('start') ? true : false)
					});
				}
			});

			var text = selected_text;
			if (!text.length) text = options.noValText || 'Choose...';
			if (options.spanEl) {
				var spanEl = options.spanEl;
			} else {
				var spanEl = $('<span class="menu-trigger">' + Orb.escapeHtml(text) + '</span>').insertAfter(selectEl);
				spanEl.on('click', self.open.bind(self));
			}
			selectEl.hide();

			this.addEvent('checked', function(el) {
				var value = el.val();

				$('option', selectEl).each(function() {
					if ($(this).val() == value) {
						if ($(this).prop('selected')) {
							$(this).prop('selected', false);
						} else {
							$(this).prop('selected', 'selected');
						}
					}
				});

				selectEl.change();
			});

			selectEl.on('change', function() {
				var evData = {select: this, stopDefault: false};
				self.fireEvent('selectChange', evData);
				if (evData.stopDefault) return;

				var opt = $('option:selected', this);
				if (opt.data('full-title')) {
					var text = opt.data('full-title').trim();
				} else {
					var text = opt.text().trim();
				}
				if (!text.length) text = options.noValText || 'Choose...';
				else {
					var prefix = $(this).data('prefix');
					if (prefix) text = prefix + text;
				}

				spanEl.text(text);
			});
		}

		var name = Orb.uuid();
		if (selectEl && selectEl.attr('name')) {
			name = selectEl.attr('name').replace(/[^a-zA-Z_]/, '_');
		}
		Array.each(options.values, function(opt) {
			if (options.selectType == 'radio') {
				var li = $('<li><input type="radio" name="'+name+'" /><label></label></li>');
			} else {
				var li = $('<li><input type="checkbox" /><label></label></li>');
			}

			if (opt.childDepth) {
				li.addClass('depth-' + opt.childDepth);
				li.prepend('<span class="elbow-end"></span>');
			}

			$('label', li).text(opt.label);
			$(':checkbox, :radio', li).first().val(opt.value).data('connected-to', opt);

			if (opt.value == options.selected_value) {
				$(':checkbox, :radio', li).first().prop('checked', true);
			}

			if (opt.extraData) {
				Object.each(opt.extraData, function(v,k) {
					li.data(k, v);
				});
			}

			if (opt.hasChild) {
				li.addClass('group-title');
			}
			if (opt.hasParent) {
				li.addClass('child');
			}

			if (opt.isSelected) {
				li.addClass('on');
				li.find('input').prop('checked', true);
			}

			$('ul', obEl).append(li);
		})

		options.element = obEl;

		this.parent(options);

		if (selectEl) {
			selectEl.find('.start').prop('selected', true);
			selectEl.trigger('change');
		}
	}
});
