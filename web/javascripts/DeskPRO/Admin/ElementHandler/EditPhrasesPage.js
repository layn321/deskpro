Orb.createNamespace('DeskPRO.Admin.ElementHandler');

DeskPRO.Admin.ElementHandler.EditPhrasesPage = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var self = this;
	},

	initPage: function() {
		var self = this;

		$('body').on('focus', 'input.phrase-preview', function(ev) {
			var previewInput = $(this);
			var phraseRow    = previewInput.closest('tr');
			var phraseId     = phraseRow.data('phrase-id');

			ev.preventDefault();
			previewInput.blur();

			var overlayEl = $(DeskPRO_Window.util.getPlainTpl($('#change_phrase_overlay')));
			$('.phrase_id', overlayEl).text(phraseId);
			$('.master_phrase', overlayEl).val($('.master_phrase', phraseRow).val());
			$('.lang_phrase', overlayEl).val($('.lang_phrase', phraseRow).val());
			$('.custom_phrase', overlayEl).val($('.custom_phrase', phraseRow).val());

			var overlay = new DeskPRO.UI.Overlay({
				contentElement: overlayEl,
				destroyOnClose: true
			});

			$('.save-trigger', overlayEl).on('click', function(ev) {
				ev.preventDefault();
				ev.stopPropagation();

				var customPhrase = $('.custom_phrase', overlayEl).val().trim();

				var postData = [];
				postData.push({
					name: 'phrases[' + phraseId + ']',
					value: customPhrase
				});

				if (customPhrase) {
					phraseRow.addClass('custom');
					previewInput.val(customPhrase);
					$('.custom_phrase', phraseRow).val(customPhrase);
				} else {
					$('.custom_phrase', phraseRow).val('');

					phraseRow.removeClass('custom');
					var langPhrase = $('.lang_phrase', phraseRow).val();
					if (langPhrase && langPhrase.length) {
						previewInput.val(langPhrase);
					} else {
						previewInput.val($('.master_phrase', phraseRow).val());
					}
				}

				$.ajax({
					url: $('#save_phrase_form').attr('action'),
					type: 'POST',
					dataType: 'json',
					data: postData
				});

				overlay.close();
			});

			overlay.open();
		});

		var addOverlayEl = $('#add_phrase_overlay');
		if (addOverlayEl) {
			var addOverlay = new DeskPRO.UI.Overlay({
				triggerElement: $('#new_custom_phrase'),
				contentElement: addOverlayEl,
				beforeOverlayOpened: function() {
					addOverlayEl.find('textarea.custom_phrase, input.phrase_id').val('');
				}
			});

			addOverlayEl.find('button.save-trigger').on('click', function(ev) {
				ev.preventDefault();
				$.ajax({
					url: $(this).data('add-url'),
					type: 'POST',
					data: addOverlayEl.find('textarea, input').serializeArray(),
					success: function() {
						window.location.reload(false);
					}
				});
			});
		}
	}
});
