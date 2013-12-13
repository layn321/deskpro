Orb.createNamespace('DeskPRO.Admin.PageHandler');

DeskPRO.Admin.PageHandler.WorkHoursEdit = new Class({
	Extends: DeskPRO.Admin.PageHandler.Basic,

	initPage: function() {
		var self = this;

		// holidays
		var year = $('#holiday_year').val();
		var setDefault = false;

		$('#holiday_date').datepicker({
			dateFormat: 'yy-mm-dd',
			yearRange: '-0:+3',
			maxDate: new Date(parseInt(year, 10) + 3, 11, 31),
			minDate: new Date(),
			beforeShow: function(input, obj) {
				if (!$(input).val()) {
					var year = $('#holiday_year').val();
					var current = new Date();
					// get month is 0 - 11
					var value = year + '-' + (current.getMonth() + 1) + '-' + current.getDate();

					$(input).datepicker('option', 'defaultDate', value);
					setDefault = true;
				}
			}
		});

		var updateVisibleHolidays = function() {
			var selectedYear = $('#holiday_year').val();

			$('#holiday_options li:not(#holiday_template)').each(function() {
				var $this = $(this);
				if ($this.data('year') && $this.data('year') != selectedYear) {
					$this.hide();
				} else {
					$this.show();
				}
			});
		};

		$('#holiday_year').change(updateVisibleHolidays);

		$('#holiday_add').click(function(e) {
			e.preventDefault();

			var date = $('#holiday_date').val();
			var name = $('#holiday_name').val();
			var repeat = $('#holiday_repeat').is(':checked');
			var addAll = $('#holiday_add_all').is(':checked');

			var list = $('#holiday_options');
			var months = list.data('months');

			if (date && name) {
				var id = parseInt(list.data('count'), 10) + 1;
				list.data('count', id);

				var dateParts = date.split('-');

				var year = parseInt(dateParts[0], 10);
				var month = parseInt(dateParts[1], 10);
				var day = parseInt(dateParts[2], 10);

				var li = $('#holiday_template').clone();
				li.attr('id', '');
				li.find('.name').text(name);
				li.find('.date').text(day + ' ' + months[month] + (!repeat ? ' ' + year : ''));
				if (!repeat) {
					li.data('year', year);
					li.find('.repeat').remove();
				}
				if (!addAll) {
					li.find('.add-all').remove();
				}

				li.data('month', month);
				li.data('day', day);

				li.append('<input type="hidden" name="work_holidays[' + id + '][name]" value="' + name + '" />');
				li.append('<input type="hidden" name="work_holidays[' + id + '][day]" value="' + day + '" />');
				li.append('<input type="hidden" name="work_holidays[' + id + '][month]" value="' + month + '" />');
				if (repeat) {
					li.append('<input type="hidden" name="work_holidays[' + id + '][year]" value="" />');
				} else {
					li.append('<input type="hidden" name="work_holidays[' + id + '][year]" value="' + year + '" />');
				}
				if (addAll) {
					li.append('<input type="hidden" name="work_holidays[' + id + '][add_all]" value="1" />');
				}

				var added = false;

				list.find('li:not(#holiday_template)').each(function() {
					var $this = $(this);
					var add = false;

					if (month < parseInt($this.data('month'), 10)) {
						add = true;
					} else if (month == parseInt($this.data('month'), 10) && day < parseInt($this.data('day'), 10)) {
						add = true;
					}

					if (add) {
						$this.before(li);
						added = true;
						return false;
					}
				});

				if (!added) {
					list.append(li);
				}

				$('#holiday_date').val('');
				$('#holiday_name').val('');
				updateVisibleHolidays();
			}
		});

		$('#holiday_options').on('click', '.remove', function() {
			$(this).closest('li').remove();
		});

		updateVisibleHolidays();
	}
});
