Orb.createNamespace('DeskPRO.Report.ElementHandler.Builder');

DeskPRO.Report.ElementHandler.Builder.BuilderTabs = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var el = this.el,
			form = el.closest('form'),
			triggerElements = $(el.data('trigger-elements') || 'ul:first li', el),
			inputTypeInput = $(el.data('input-type-input')),
			querySwitchUrl = el.data('query-switch-url'),
			errorContainer = $(el.data('query-error-container')),
			errorMessageElement = $(el.data('query-error-message')),
			internallyTriggered = false,
			tabs;

		tabs = new DeskPRO.UI.SimpleTabs({
			triggerElements: triggerElements,
			activeClassname: 'current'
		});
		tabs.addEvent('beforeTabSwitch', function(event) {
			if (internallyTriggered) {
				return;
			}

			var lastTabEl = event.lastTabEl,
				tabEl = event.tabEl,
				tabContent = event.tabContent,
				currentType = (lastTabEl.length ? lastTabEl.data('query-type') : inputTypeInput.val()),
				newType = tabEl.data('query-type');

			if (currentType === newType) {
				return;
			}

			event.cancel = true;

			errorContainer.fadeOut();
			
			$.ajax({
				url: querySwitchUrl,
				type: 'POST',
				dataType: 'json',
				data: form.serialize() + '&currentType=' + encodeURIComponent(currentType)
					+ '&newType=' + encodeURIComponent(newType)
			}).done(function(data) {
				if (data.error) {
					errorMessageElement.text(data.error);
					errorContainer.fadeIn();
					return;
				}

				internallyTriggered = true;
				tabs.activateTab(tabEl);
				internallyTriggered = false;

				inputTypeInput.val(tabEl.data('query-type'));

				for (var key in data) {
					if ($.isPlainObject(data[key])) {
						for (var subKey in data[key]) {
							form.find('[name="' + key + '[' + subKey + ']"]').val(data[key][subKey]);
						}
					} else {
						form.find('[name="' + key + '"]').val(data[key]);
					}
				}

				tabContent.find('textarea.expander').trigger('textareaexpander_fire');
			});
		});

		this.simpleTabs = tabs;
	}
});
