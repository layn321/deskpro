Orb.createNamespace('DeskPRO.Admin.PageHandler');

DeskPRO.Admin.PageHandler.SlaEdit = new Class({
	Extends: DeskPRO.Admin.PageHandler.Basic,

	initPage: function() {
		var self = this;

		$('select.select2').each(function() {
			DP.select($(this));
		});

		// person and organization search
		var person_ids = $('#person_ids');
		DP.select(person_ids, {
			multiple: true,
			minimumInputLength: 2,
			ajax: {
				url: BASE_URL + 'admin/misc/quick-person-search',
				dataType: 'json',
				quietMillis: 250,
				data: function(term, page) {
					return {
						q: term,
						with_agents: 1
					};
				},
				results: function(data, page) {
					var results = [];
					for (var i = 0; i < data.length; i++) {
						results.push({
							id: data[i].id,
							text: data[i].name
						});
					}

					return {
						more: false,
						results: results
					};
				}
			},
			initSelection: function(element, callback) {
				var data = [];
				if (element.val().length) {
					var ids = element.val().split(',');
					var selected = element.data('selected');

					for (var i = 0; i < ids.length; i++) {
						var id = $.trim(ids[i]);
						if (id.length && id != '0') {
							data.push({id: id, text: selected[id]});
						}
					}
				}

				callback(data);
			}
		});

		var organization_ids = $('#organization_ids');
		DP.select(organization_ids, {
			multiple: true,
			ajax: {
				url: BASE_URL + 'admin/misc/quick-organization-search',
				dataType: 'json',
				quietMillis: 250,
				data: function(term, page) {
					return {
						q: term
					};
				},
				results: function(data, page) {
					var results = [];
					for (var i = 0; i < data.length; i++) {
						results.push({
							id: data[i].id,
							text: data[i].name
						});
					}

					return {
						more: false,
						results: results
					};
				}
			},
			initSelection: function(element, callback) {
				var data = [];
				if (element.val().length) {
					var ids = element.val().split(',');
					var selected = element.data('selected');

					for (var i = 0; i < ids.length; i++) {
						var id = $.trim(ids[i]);
						if (id.length && id != '0') {
							data.push({id: id, text: selected[id]});
						}
					}
				}

				callback(data);
			}
		});

		// triggers and criteria
		this.actionEditors = {};
		this.criteriaEditors = {};

		$('.trigger-actions .add-term-row').on('click', function() {
			var $this = $(this);

			var basename = self.getBaseName($this.data('name'));
			self.getActionEditor(basename).addNewRow(
				$this.closest('.terms-wrap').find('.terms-input'),
				basename
			);
		});

		$('.trigger-criteria .add-term-row').on('click', function() {
			var $this = $(this);

			var basename = self.getBaseName($this.data('name'));
			self.getCriteriaEditor(basename).addNewRow(
				$this.closest('.terms-wrap').find('.terms-input'),
				basename
			);
		});

		// apply options
		$('.force-checked').change(function() {
			$(this).attr('checked', true);
		});

		$('#apply_priority').change(function() {
			if (this.checked) {
				$('#apply_priority_id_container').show();
			} else {
				$('#apply_priority_id_container').hide();
			}
		});

		$('#apply_trigger').change(function() {
			if (this.checked) {
				$('#apply_trigger_container').show();
			} else {
				$('#apply_trigger_container').hide();
			}
		});

		var year = $('#holiday_year').val();

		var setDefault = false;

		// holidays
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
					} else if (month == parseInt($this.data('month'), 10) && day <  parseInt($this.data('day'), 10)) {
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

		// types
		var typeChange = function() {
			var val = $('input[name=sla_type]:checked').val();
			$('.sla-type-text').hide();
			if (val) {
				$('.sla-type-text.' + val).show();
			}
		};
		$('input[name=sla_type]').change(typeChange);
		typeChange();

		// apply list
		var applyAllChange = function() {
			var val = $('#apply_type_select').val();
			$('.apply_type_row').hide();
			$('#apply_type_show_' + val).show();
		};
		 $('#apply_type_select').change(applyAllChange);
		applyAllChange();

		// terms/criteria init
		if (window.SlaEditPage_initTerms) {
			window.SlaEditPage_initTerms.call(this);
		}
	},

	getActionEditor: function(name) {
		if (!this.actionEditors[name]) {
			this.actionEditors[name] = new DeskPRO.Form.RuleBuilder($('.actions-tpl'));
		}

		return this.actionEditors[name];
	},

	getCriteriaEditor: function(name) {
		if (!this.criteriaEditors[name]) {
			this.criteriaEditors[name] = new DeskPRO.Form.RuleBuilder($('.criteria-tpl'));
		}

		return this.criteriaEditors[name];
	},

	getBaseName: function(prefix) {
		return prefix + '['+Orb.uuid()+']';
	}
});
