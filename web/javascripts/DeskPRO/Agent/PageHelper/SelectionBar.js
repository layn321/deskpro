Orb.createNamespace('DeskPRO.Agent.PageHelper');

DeskPRO.Agent.PageHelper.SelectionBar = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(page, options) {
		var self = this;

		this.page = page;

		this.options = {
			selectionBar: null,
			selectedCount: null,
			button: null,
			checkSelector: 'input.item-select',
			saveSelectionId: null
		};
		this.setOptions(options);

		if (this.options.saveSelectionId) {
			this.options.saveSelectionRealId = 'dp.agent.selectionbar.' + this.options.saveSelectionId;
		}

		if (!this.options.selectionBar) {
			this.options.selectionBar = $('.list-selection-bar', this.page.wrapper).first();
		}
		this.selectionBar = $(this.options.selectionBar);

		if (!this.options.selectedCount) {
			this.options.selectedCount = $('.selected-count:first', this.selectionBar);
		}
		this.selectedCount = $(this.options.selectedCount);

		if (!this.options.button) {
			this.options.button = $('.perform-actions-trigger:first', this.selectionBar);
		}
		this.button = $(this.options.button);
        this.button.addClass('disabled');

		this.button.on('click', this.buttonClicked.bind(this));

		this.controlCheck = $('.selection-control', this.page.wrapper).on('click', function() {
			if ($(this).is(':checked')) {
				self.checkAll();
			} else {
				self.checkNone();
			}
		});

		this.lastCheckBox = null;

		this.page.wrapper.on('click', this.options.checkSelector, function(event) {
			var el = $(this);
			self.handleCheckChange(el, el.is(':checked'));

			if (self.lastCheckBox && event.shiftKey) {
				self.checkRange(el, self.lastCheckBox);
			}

			self.lastCheckBox = el;
		});
	},

	buttonClicked: function(ev) {
		if (this.button.is('.disabled')) {
			return;
		}

		this.fireEvent('buttonClick', [ev]);
	},

	getCheckedValues: function() {
		var values = [];

		$(this.options.checkSelector + ':checked', this.page.wrapper).each(function() {
			values.push($(this).val());
		});

		return values;
	},

	getCheckedFormValues: function (form_name, appendArray, info) {
		appendArray = appendArray || [];

		if (!info) info = {};
		info.checkedCount = 0;

		$(this.options.checkSelector + ':checked', this.page.wrapper).each(function() {
			appendArray.push({
				name: form_name,
				value: $(this).val()
			});
			info.checkedCount++;
		});

		return appendArray;
	},

	getChecked: function() {
		return $(this.options.checkSelector + ':checked', this.page.wrapper);
	},

	getCount: function() {
		return $(this.options.checkSelector + ':checked', this.page.wrapper).length;
	},

	checkAll: function() {
		$(this.options.checkSelector, this.page.wrapper).attr('checked', true);

		var count = this.updateCount();

		this.fireEvent('checkAll', [count]);
	},

	checkRange: function(start, end) {
		var is_checked = start.is(':checked');

		var checks = this.page.wrapper.find(this.options.checkSelector);
		var indexStart = checks.index(start);
		var indexEnd = checks.index(end);

		if (indexStart > indexEnd) {
			var tmp = indexEnd;
			indexEnd = indexStart;
			indexStart = tmp;
		}

		checks = checks.slice(indexStart, indexEnd+1);
		checks.prop('checked', is_checked);

		var count = this.updateCount();

		this.fireEvent('checkRange', [count]);
	},

	checkNone: function() {
		$(this.options.checkSelector, this.page.wrapper).filter(':checked').attr('checked', false);

		var count = this.updateCount();

		this.fireEvent('checkNone');
	},

	handleCheckChange: function(el, is_checked) {
		var count = this.updateCount();
		this.fireEvent('checkChange', [el, is_checked, count]);
	},

	restoreFromSessionStorage: function() {
		if (this.options.saveSelectionRealId && window.sessionStorage && window.sessionStorage[this.options.saveSelectionRealId]) {
			var checked = window.sessionStorage[this.options.saveSelectionRealId].split(',');
			if (checked.length) {
				$(this.options.checkSelector, this.page.wrapper).each(function() {
					if (this.value && checked.contains(this.value)) {
						this.checked = true;
					}
				});
				this.updateCount();
			}
		}
	},

	updateCount: function() {
		var oldCount = parseInt(this.selectedCount.text(), 10) || 0;

		var checkedEls = $(this.options.checkSelector, this.page.wrapper).filter(':checked');
		var count = checkedEls.length;
		this.selectedCount.text(count);

		if (count > 0) {
			this.button.removeClass('disabled');
		} else {
			this.button.addClass('disabled');
		}

		if (this.options.saveSelectionRealId && window.sessionStorage) {
			var checked = [];
			if (count) {
				checkedEls.each(function() {
					if (this.value) {
						checked.push(this.value);
					}
				});
			}

			sessionStorage[this.options.saveSelectionRealId] = checked.join(',');
		}

		if (!checkedEls.length || count == 0) {
			this.controlCheck.attr('checked', false);
		} else {
			this.controlCheck.attr('checked', true);
		}

		if (oldCount != count) {
			this.fireEvent('countChange', [count, oldCount]);
		}

		return count;
	},

	resetCountLabel: function() {
		return this.updateCount();
	}
});
