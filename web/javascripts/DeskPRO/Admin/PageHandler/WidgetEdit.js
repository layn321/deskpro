Orb.createNamespace('DeskPRO.Admin.PageHandler');

DeskPRO.Admin.PageHandler.WidgetEdit = new Class({
	Extends: DeskPRO.Admin.PageHandler.Basic,

	initPage: function() {
		var textAreas = $('textarea.expander');

		textAreas.TextAreaExpander().trigger('textareaexpander_fire');

		var widgetTabs = new DeskPRO.UI.SimpleTabs({
			triggerElements: $('#widget-tabs > li')
		});
		widgetTabs.addEvent('tabSwitch', function(e) {
			textAreas.trigger('textareaexpander_fire');
		});

		// ****** SETUP LOCATION SELECTS

		var pageSelect = $('#page-select'),
			pageInsert = $('#page-insert-position-select'),
			pageLocations = $('#page-location-select'),
			pageLocationInsertPosition = pageLocations.find('option[value=""]').first();

		var pageChange = function() {
			var page = pageSelect.val(), haveSelected = false, firstVisible;

			var visibles = [];

			pageLocations.find('option').each(function() {
				var $this = $(this);
				if ($this.data('page') == page) {
					$this.show();
					if ($this.data('page') != '') {
						visibles.push($this);
					}
					if (!firstVisible) {
						firstVisible = $this;
					}
					if ($this.is(':selected')) {
						haveSelected = true;
					}
				} else {
					$this.hide();
				}
			});

			// move visible ones to the top - works around a webkit bug
			$(visibles).insertAfter(pageLocationInsertPosition);

			if (!haveSelected && firstVisible) {
				pageLocations.val(firstVisible.val());
			}

			locationChange();
		};
		var locationChange = function() {
			var haveSelected = false,
				firstVisible,
				positions = pageLocations.find('option:selected:first').data('positions'),
				positionOptions = (positions ? positions.split(',') : []);

			pageInsert.find('option').each(function() {
				var $this = $(this), visible = false;
				if (!positionOptions.length) {
					visible = true;
				} else {
					for (var i = 0; i < positionOptions.length; i++) {
						if ($this.val() == positionOptions[i]) {
							visible = true;
							break;
						}
					}
				}

				if (visible) {
					$this.show();
					if (!firstVisible) {
						firstVisible = $this;
					}
					if ($this.is(':selected')) {
						haveSelected = true;
					}
				} else {
					$this.hide();
				}
			});

			if (!haveSelected && firstVisible) {
				pageInsert.val(firstVisible.val());
			}
		};

		pageLocations.width(pageLocations.outerWidth());
		pageInsert.width(pageInsert.outerWidth());
		pageChange();

		pageLocations.change(locationChange);
		pageSelect.change(pageChange);

		// ***** SETUP WIDGET TYPE

		var blockTitle = $('#widget-block-title'),
			htmlHeader = $('#widget-tab-header-html'),
			htmlBody = $('#widget-html');

		var widgetTypeHandler = function(val) {
			if (val == 'js') {
				pageInsert.hide();
				pageLocations.hide();
				blockTitle.hide();
				htmlHeader.hide();
				htmlBody.hide();
				if (widgetTabs.getActiveTab().attr('id') == htmlHeader.attr('id')) {
					widgetTabs.activateTab($('#widget-tab-header-js'));
				}
			} else {
				pageInsert.show();
				pageLocations.show();
				blockTitle.show();
				htmlHeader.show();
				// htmlBody should not be shown - shown as tab when selected
			}
		};

		widgetTypeHandler($('#widget-type-input input[type=radio]:checked').val());
		$('#widget-type-input input[type=radio]').change(function() {
			widgetTypeHandler($(this).val());
		});
	}
});
