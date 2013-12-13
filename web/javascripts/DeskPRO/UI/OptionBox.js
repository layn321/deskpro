Orb.createNamespace('DeskPRO.UI');

/**
 * Optionbox
 */
DeskPRO.UI.OptionBox = new Orb.Class({
	Implements: [Orb.Util.Options, Orb.Util.Events],

	initialize: function(options) {
		this.options = {
			element: null,
			trigger: null
		};

		this.setOptions(options);

		this.el = this.options.element;

		if (this.options.trigger) {
			$(this.options.trigger).on('click', this.open.bind(this));
		}
	},

	getElement: function(type) {
		if (!type || type == 'element') {
			return this.el;
		} else if (type == 'backdrop') {
			return this.backdrop;
		}
	},

	_init: function() {
		var self = this;

		if (this._hasInit) return;
		this._hasInit = true;

		this.fireEvent('preInit', [this]);

		//------------------------------
		// Basic elements
		//------------------------------

		this.backdrop = $('<div class="backdrop optionbox-backdrop" />');
        this.backdrop.appendTo('body').hide();

		if (!this.el.parent().is('body')) {
			this.el.detach().appendTo('body');
		}

		this.el.on('click', function(ev) {
			ev.stopPropagation();
		});

		this.backdrop.on('click', function(ev) {
			ev.stopPropagation();
			self.close();
		});

		$('.close-trigger', this.el).on('click', function(ev) {
			ev.stopPropagation();
			self.close();
		});

		var allSame = this.el.is('.single-option-type');

		//------------------------------
		// Events on checkboxes and filter
		//------------------------------

		$(':checkbox, :radio', this.el).on('change', function() {
			self.clickCheckbox($(this));
		});

		$('section', this.el).each(function() {
			var count = $('ul :checkbox', this).length;
			$(this).data('total-count', count);

			if ($(this).data('section-name')) {
				$(this).addClass($(this).data('section-name'));
			}

			var opt = $(':checkbox, :radio', this).first();
			if (opt.is(':radio')) {
				$(this).data('input-type', 'radio');
			} else {
				$(this).data('input-type', 'checkbox');
			}
		});

		$('section col', this.el).last().addClass('last');

		var amClicking = false;
		var lastClick = false;
		var elList = this.el.find('li');
		this.el.on('click', 'li', function(ev) {
			if (amClicking) return;
			amClicking = true;

			var radio = $(':radio, :checkbox', this);

			if (ev.shiftKey && lastClick) {
				var idxMe = elList.index(this);
				var idxLast = elList.index(lastClick);

				var els = [];
				if (idxMe > idxLast) {
					for (var i = idxLast+1; i <= idxMe; i++) {
						els.push($(elList.eq(i)).find(':radio, :checkbox').get(0));
					}
				} else {
					for (var i = idxMe; i < idxLast; i++) {
						els.push($(elList.eq(i)).find(':radio, :checkbox').get(0));
					}
				}

				radio = $(els);
			}

			if (radio.length) {
				radio.prop('checked', !radio.prop('checked'));
				self.clickCheckbox(radio);

				if (radio.is(':radio')) {
					if (allSame) {
						self.el.find('li.on').removeClass('on');
					} else {
						radio.closest('section').find('li.on').removeClass('on');
					}
				}

				if (radio.prop('checked')) {
					radio.closest('li').addClass('on');
				} else {
					radio.closest('li').removeClass('on');
				}

				if (!self.options.liNoClickClose && (radio.is(':radio') && self.isSingleMode)) {
					self.close();
				}
			}

			lastClick = this;
			amClicking = false;
		});

		$(':radio, :checkbox', this.el).on('change', function() {
			if ($(this).is(':radio')) {
				if (allSame) {
					self.el.find('li.on').removeClass('on');
				} else {
					$(this).closest('section').find('li.on').removeClass('on');
				}
			}

			if ($(this).is(':checked')) {
				$(this).closest('li').addClass('on');
			} else {
				$(this).closest('li').removeClass('on');
			}
		});

		$('header .all-check', this.el).on('click', function() {
			var section = self._findSection($(this));
			if ($(this).is(':checked')) {
				$('ul :checkbox', section).attr('checked', true);
			} else {
				$('ul :checkbox', section).attr('checked', false);
			}
			self.updateCountEls(section);
		});

		$('header input.filter-box', this.el).on('keyup', function() {
			self.updateFilter($(this));
		}).on('change', function() {
			self.updateFilter($(this));
		});

		$('header .toggle-btn', this.el).on('click', function(ev) {
			Orb.cancelEvent(ev);

			var section = self._findSection($(this));
			var checks = section.find(':checkbox');
			if (checks.filter(':checked').length) {
				checks.attr('checked', false).trigger('change');
			} else {
				checks.attr('checked', true).trigger('change');
			}

			self.updateCountEls(section);
		});

		var l = this.el.find('div.col').length;
		if (l == 0 || l == 1) {
			this.isSingleMode = true;
		} else {
			this.isSingleMode = false;
		}

		this.fireEvent('init', [this]);
	},

	clickCheckbox: function(check) {
		var section = this._findSection(check);
		this.updateCountEls(section);

		this.fireEvent('checked', [check, this]);
	},

	updateCountEls: function(section) {

		if (!section) {
			var self = this;
			this.el.find('section').each(function() {
				if ($(this).data('section-name')) {
					self.updateCountEls($(this));
				}
			});
			return;
		}

		var count = $('ul :checkbox:checked', section).length;
		var countEl = $('.selected-count', section);

		if (count) {
			$('.num', countEl).text(count);
			countEl.show();
		} else {
			$('.num', countEl).text('0');
			countEl.hide();
		}

		if (count == section.data('total-count')) {
			$('header .all-check', section).attr('checked', true);
		} else {
			$('header .all-check', section).attr('checked', false);
		}
	},

	getCount: function(section) {
		if (typeof section == 'string') {
			section = $('section.' + section, this.el);
		}

		return parseInt($('.selected-count .num', section).text() || 0);
	},

	getSelectedElements: function(section) {
		if (typeof section == 'string') {
			section = $('section.' + section, this.el);
		}

		var els = [];
		$('input:checked', section).each(function() {
			els.push($(this).closest('li').get(0));
		});

		els = $(els);
		return els;
	},

	getSelected: function(section) {
		if (typeof section == 'string') {
			section = $('section.' + section, this.el);
		}

		var val = [];
		$('li input:checked', section).each(function() {
			val.push($(this).val());
		});

		if (section.data('input-type') == 'radio') {
			val = val.pop();
			if (!val) {
				val = null;
			}
		}

		return val;
	},

	getAllSelected: function() {
		var self = this;
		var ret = {};

		$('section', this.el).each(function() {
			var name = $(this).data('section-name');
			ret[name] = self.getSelected($(this));
		});

		return ret;
	},

	_findSection: function(el) {
		return el.closest('section');
	},

	updateFilter: function(filterEl) {
		var filter = filterEl.val().trim().toLowerCase();
		var section = this._findSection(filterEl);
		var lis = $('li', section);

		if (!filter) {
			lis.show();
			return;
		}

		var show = [];
		var hide = [];

		lis.each(function() {
			var name = $('label', this).text().toLowerCase();
			if (name.indexOf(filter) !== -1) {
				show.push(this);
			} else {
				hide.push(this);
			}
		});

		$(hide).hide();
		$(show).show().each(function() {
			var itemId = $(this).data('item-id');
			if (itemId) {
				$('li.child-of-' + itemId, section).show();
				var parentId = $(this).data('parent-id');
				if (parentId) {
					$('li.item-' + parentId, section).show();
				}
			}
		});
	},

	open: function(event) {
		this._init();

		this.fireEvent('preOpen', [this]);

		var viewportW = $(window).width();
		var viewportH = $(window).height();

		var pageX = $(event.target).offset().top;
		var pageY = $(event.target).offset().left;

		var w = this.el.width() + 5;
		var h = this.el.height();

		this.el.show();
		this.backdrop.show();

		if (pageY + w > viewportW) {
			pageY = viewportW - w - 10;
		}
		if (pageX + h > viewportH) {
			pageX = viewportH - h - 10;
		}

		this.el.css({
			top: pageX,
			left: pageY
		});

		this.el.addClass('open');

		var cols = $('.col', this.el);
		if (cols.length) {
			var max = 0;
			var w = 0;
			cols.each(function() {
				var ul = $('> section > ul', this);
				w += $(this).width();
				max += ul.height();
			});
			$('.col > section > ul', this.el).each(function() {
				if ($(this).height() < max) {
					$(this).height(max);
				}
			});

			w += (1 * cols.length);  // border/margin on the cols
			DP.console.log('setting w %i', w);

			this.el.width(w);
		}

		$(':checkbox, :radio', this.el).each(function() {
			if ($(this).is(':checked')) {
				$(this).closest('li').addClass('on');
			} else {
				$(this).closest('li').removeClass('on');
			}
		});

		this.updateCountEls();
		this.fireEvent('open', [this]);
	},

	close: function() {
		if(this._hasInit) {
        	this.backdrop.hide();
		}

		if (!this.isOpen()) return;

		this.el.hide().removeClass('open');

		this.fireEvent('close', [this]);
	},

	isOpen: function() {
		if (this._hasInit && this.el.is('.open')) {
			return true;
		}

		return false;
	},

	destroy: function() {
		if (this._hasInit) {
			this.el.remove();
			this.backdrop.remove();
		}
	}
});
