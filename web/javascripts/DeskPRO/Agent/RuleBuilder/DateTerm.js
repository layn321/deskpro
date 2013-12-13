Orb.createNamespace('DeskPRO.Agent.RuleBuilder');

DeskPRO.Agent.RuleBuilder.DateTerm = new Orb.Class({
	Extends: DeskPRO.Agent.RuleBuilder.TermAbstract,

	initRow: function() {
		this._initUi();
	},

	initValues: function() {
		var timestamp = null, date = null;

		timestamp = this.date1Input.val();
		if (timestamp) {
			date = new Date(timestamp * 1000);
			this.date1Widget.datepicker('setDate', date);
		}

		timestamp = this.date2Input.val();
		if (timestamp) {
			date = new Date(timestamp * 1000);
			this.date2Widget.datepicker('setDate', date);
		}

		//------------------------------
		// Existing values
		//------------------------------

		if (parseInt($('.date1-relative-input', this.rowEl).val())) {
			$('.relative1-input', this.date1).val($('.date1-relative-input', this.rowEl).val());
			$('.relative1-type', this.date1).val($('.date1-relative-type', this.rowEl).val());

			$('.date', this.date1).hide();
			$('.relative', this.date1).show().addClass('on');
		}

		if (parseInt($('.date2-relative-input', this.rowEl).val())) {
			$('.relative2-input', this.date2).val($('.date2-relative-input', this.rowEl).val());
			$('.relative2-type', this.date2).val($('.date2-relative-type', this.rowEl).val());

			$('.date', this.date2).hide();
			$('.relative', this.date2).show().addClass('on');
		}

		this.updateStatus();
	},

	_initUi: function() {

		//------------------------------
		// References to elements and move
		// overlay into body
		//------------------------------

		this.opInput = $('select.op', this.rowEl);

		this.date1Input = $('input.date1-input', this.rowEl);
		this.date2Input = $('input.date2-input', this.rowEl);

		this.date1Display = $('input.date1-display', this.rowEl);
		this.date2Display = $('input.date2-display', this.rowEl);

		this.currentValue = $('.status-value', this.rowEl);
		this.currentValue.text('(click to set)');
		this.currentValue.on('click', this.show.bind(this));

		this.dateWrap = $('.date-wrap', this.rowEl);

		this.backdrop = $('<div class="backdrop" style="display: none"></div>');
		this.backdrop.appendTo('body');
		this.backdrop.on('click', this.hide.bind(this));

		this.wrapper = $('<div class="field-overlay" style="display:none"><div class="close-trigger"></div></div>');
		$('.close-trigger', this.wrapper).on('click', this.hide.bind(this));

		this.dateWrap.detach().appendTo(this.wrapper).css('display', 'block');
		this.wrapper.appendTo('body');

		this.date1 = $('.date1', this.dateWrap);
		this.date2 = $('.date2', this.dateWrap);

		//------------------------------
		// Init date elements
		//------------------------------

		var self = this;
		this.date1Widget = $('.widget', this.date1).datepicker({
			dateFormat: 'M d, yy',
			onSelect: function(dateText, inst) {

				self.date1Input.val(self.date1Widget.datepicker('getDate').getTime() / 1000);

				self.date1Display.val(dateText);
				self.updateStatus();
			}
		});

		this.date2Widget = $('.widget', this.date2).datepicker({
			dateFormat: 'M d, yy',
			onSelect: function(dateText, inst) {

				self.date2Input.val(self.date2Widget.datepicker('getDate').getTime() / 1000);

				self.date2Display.val(dateText);
				self.updateStatus();
			}
		});

		var getDate = function (el) {
			var timestamp = strtotime(el.val());
			if (!timestamp) {
				return null;
			}

			var date = new Date(timestamp * 1000);
			return date;
		};

		//------------------------------
		// Detect changes to text fields for
		// human times "1 day ago" etc
		//------------------------------

		this.date1Display.on('change', function() {
			var date = getDate($(this));
			if (!date) {
				$(this).val('');
				return;
			}
			self.date1Widget.datepicker('setDate', date);
		});

		this.date2Display.on('change', function() {
			var date = getDate($(this));
			if (!date) {
				$(this).val('');
				return;
			}
			self.date2Widget.datepicker('setDate', date);
		});

		//------------------------------
		// Switcher between relative input
		//------------------------------

		$('.switcher', this.date1).on('click', (function() {
			var date = $('.date', this.date1);
			var rel  = $('.relative', this.date1);

			if (date.is(':visible')) {
				date.hide();
				rel.show().addClass('on');
			} else {
				rel.hide().removeClass('on');
				date.show();
			}
		}).bind(this));

		$('.switcher', this.date2).on('click', (function() {
			var date = $('.date', this.date2);
			var rel  = $('.relative', this.date2);

			if (date.is(':visible')) {
				date.hide();
				rel.show().addClass('on');
			} else {
				rel.hide().removeClass('on');
				date.show();
			}
		}).bind(this));
	},

	show: function() {

		if (this.opInput.val() == 'between') {
			this.dateWrap.addClass('two');
		} else {
			this.dateWrap.removeClass('two');
		}

		this.wrapper.css({
			left: this.currentValue.offset().left,
			top: this.currentValue.offset().top
		});

		this.backdrop.show();
		this.wrapper.show();
	},

	updateStatus: function() {

		var str1 = '', str2 = '', status = '';

		// If we're using the relative times, update the values on close
		var relative1 = $('.relative1', this.date1);
		var relative2 = $('.relative2', this.date2);

		if ($('.relative', this.date1).hasClass('on')) {
			$('.date1-relative-input', this.rowEl).val($('.relative1-input', this.date1).val());
			$('.date1-relative-type', this.rowEl).val($('.relative1-type', this.date1).val());

			// Erase any calendar time we mightve set before
			this.date1Input.val('');

			if ($('.relative1-input', this.date1).val().trim().length) {
				str1 = $('.relative1-input', this.date1).val() + ' ' + $('.relative1-type', this.date1).val() + ' ago';
			}
		} else {
			var date1 = this.date1Widget.datepicker('getDate');
			if (date1) {
				str1 = $.datepicker.formatDate('M d, yy', date1);
			}
		}

		if ($('.relative', this.date2).hasClass('on')) {
			$('.date2-relative-input', this.rowEl).val($('.relative2-input', this.date2).val());
			$('.date2-relative-type', this.rowEl).val($('.relative2-type', this.date2).val());

			this.date2Input.val('');

			if ($('.relative2-input', this.date2).val().trim().length) {
				str2 = $('.relative2-input', this.date2).val() + ' ' + $('.relative2-type', this.date2).val() + ' ago';
			}
		} else {
			var date2 = this.date2Widget.datepicker('getDate');
			if (date2) {
				str2 = $.datepicker.formatDate('M d, yy', date2);
			}
		}

		if (!str1.length) str1 = '(click to set)';
		if (!str2.length) str1 = '(click to set)';

		if (this.opInput.val() == 'between') {
			status = str1 + ' and ' + str2;
		} else {
			status = str1;
		}

		this.currentValue.text(status);
	},

	hide: function() {
		this.updateStatus();
		this.backdrop.hide();
		this.wrapper.hide();
	},

	destroy: function() {
		this.wrapper.remove();
		this.backdrop.remove();
	}
});
