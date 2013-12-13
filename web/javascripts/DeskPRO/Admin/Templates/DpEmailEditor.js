function DpEmailEditor(name, context) {
	var codeHints = new DpCodeHints();
	var activeEditorArea = null;

	context = context || document;

	//##################################################################################################################
	//# Init the editors
	//##################################################################################################################

	$('.template-editor-wrap', context).each(function() {

		$(this).on('click', function(ev) {
			ev.stopPropagation();
		});

		var textarea  = $(this).find('textarea.template-editor');
		var codeCm = CodeMirror.fromTextArea(textarea.get(0), {
			mode: textarea.data('mode') || 'htmlmixed',
			lineNumbers: true,
			indentWithTabs: true,
			onCursorActivity: function() {
				codeHints.show(codeCm, textarea);
			},
			onFocus: function() {
				// When we switch focus to new textarea, close
				// possibly open hints from old one
				codeHints.hide();
				activeEditorArea = textarea;
			}
		});

		textarea.data('cm', codeCm);
	});

	// When clicking off, close any open tops
	$(document).on('click', function(ev) {
		var targetTip = $(ev.target).closest('.code-tip');
		if (targetTip[0]) {
			return;
		}
		codeHints.hide();
	});

	//##################################################################################################################
	//# Saving / reverting current template
	//##################################################################################################################

	var saveCtrl = $('.save-control, #save_control', context).first();
	saveCtrl.find('button.save-trigger').on('click', function(ev) {
		ev.preventDefault();

		if ($('textarea.subject', context)[0]) {
			var subject = $('textarea.subject', context).data('cm').getValue();
			var body    = $('textarea.template', context).data('cm').getValue();
			var code    = "<dp:subject>" + subject + "</dp:subject>\n" + body;
		} else {
			var body = $('textarea.template', context).data('cm').getValue();
			var code = body;
		}

		saveCtrl.addClass('loading');
		$.ajax({
			url: BASE_URL + 'admin/templates/save-template.json',
			context: this,
			type: 'POST',
			data: {
				name: name,
				code: code
			},
			complete: function() {
				saveCtrl.removeClass('loading');
			},
			success: function(data) {
				if (data.error) {
					alert(data.error_message + "\n\nLine: " + data.error_line);
					return;
				}

				$('.view-default, .reset-default', context).show();
			}
		});
	});

	$('.reset-default', context).on('click', function(ev) {
		ev.preventDefault();
		var part = $(this).data('part') || '';

		if (confirm('Are you sure you want to reset back to the default?')) {
			saveCtrl.addClass('loading');
			$.ajax({
				type: 'POST',
				url: BASE_URL + 'admin/templates/revert-template.json?name=' + name + '&part=' + part,
				success: function() {
					window.location = window.location;
				}
			});
		}
	});

	//##################################################################################################################
	//# Adding new phrases
	//##################################################################################################################

	var addOverlayEl = $('.add_phrase_overlay, #add_phrase_overlay', context).first();
	var addOverlay = new DeskPRO.UI.Overlay({
		contentElement: addOverlayEl,
		onBeforeOverlayOpened: function() {
			codeHints.hide();
			addOverlayEl.find('textarea.custom_phrase, input.phrase_id').val('');
		}
	});

	addOverlayEl.find('button.save-trigger').on('click', function(ev) {
		ev.preventDefault();
		$.ajax({
			url: $(this).data('add-url'),
			type: 'POST',
			data: addOverlayEl.find('textarea, input').serializeArray(),
			dataType: 'json',
			success: function(data) {
				addOverlay.close();

				var cm = activeEditorArea.data('cm').replaceSelection('{{ phrase(\'' + data.phrase_id + '\') }}');
			}
		});
	});

	$('.template-toolbar .new-phrase', context).on('click', function() {
		addOverlay.open();
		activeEditorArea = $(this).closest('.template-edit-row').find('textarea.template-editor');
	});

	$('.template-toolbar .view-default', context).on('click', function() {
		var part = $(this).data('part');

		if (part == 'subject') {
			var codeTxt = $('textarea.subject-default-code', context);
			var targetCm = $('textarea.subject', context).data('cm');
		} else {
			var codeTxt = $('textarea.body-default-code', context);
			var targetCm = $('textarea.template', context).data('cm');
		}

		var tmp = codeTxt.val();
		codeTxt.val(targetCm.getValue());
		targetCm.setValue(tmp);

		$(this).toggleClass('on');
	});

	//##################################################################################################################
	//# Adding new variation
	//##################################################################################################################

	var variationOverlayEl = $('.add-variation-overlay, #add_variation_overlay', context);
	var variationOverlay = new DeskPRO.UI.Overlay({
		contentElement: variationOverlayEl,
		triggerElement: $('.add-variation-overlay, #add_variation_trigger', context),
		onBeforeOverlayOpened: function() {
			codeHints.hide();
			variationOverlayEl.find('input.template_name').val('');
		}
	});

	variationOverlayEl.find('button.save-trigger').on('click', function(ev) {
		var new_name = variationOverlayEl.find('input.template_name').val();
		new_name = new_name.replace(/[^a-zA-Z0-9_\-]/g, '_');

		if (!new_name) {
			alert('Please enter a unique ID');
			return;
		}

		var url = BASE_URL + 'admin/templates/email/edit/' + encodeURIComponent(new_name) + '?variant_of=' + encodeURIComponent(name);

		window.location = url;
	});

	//##################################################################################################################
	//# Lang overlay
	//##################################################################################################################

	var langOverlayEl = $('.lang-overlay', context).first();
	var langOveralyContentEl = $('.lang_overlay_content', context).first();
	var langHasLoaded = false;
	var langHasNav = false;
	var langOverlay = new DeskPRO.UI.Overlay({
		contentElement: langOverlayEl,
		onBeforeOverlayOpened: function() {
			codeHints.hide();
			if (!langHasLoaded) {
				langHasLoaded = true;
				loadLangSection('user.general');
			}
		}
	});

	function loadLangSection(group, langId) {
		langId = langId || 1;
		langOveralyContentEl.empty();
		langOveralyContentEl.html('<i class="flat-spinner"></i>');

		$.ajax({
			url: BASE_URL + 'admin/languages/'+langId+'/phrases/' + group + '?_partial=overlay',
			dataType: 'html',
			success: function(html) {
				langOveralyContentEl.html(html);
				DeskPRO.ElementHandler_Exec(langOveralyContentEl);

				if (!langHasNav) {
					langHasNav = true;
					var nav = $('.lang_overlay_nav', langOverlayEl);

					var langSel = langOveralyContentEl.find('select.lang-langs-nav');
					langSel.detach().appendTo(nav);

					var groupSel = langOveralyContentEl.find('select.lang-groups-nav');
					groupSel.detach().appendTo(nav);

					function update() {
						var langId = langSel.val();
						var groupId = groupSel.val();

						loadLangSection(groupId, langId);
					}

					langSel.on('change', update);
					groupSel.on('change', update);

					DP.select(langSel);
					DP.select(groupSel);
				}
			}
		});
	}

	$('.template-toolbar .phrase-editor', context).on('click', function() {
		langOverlay.open();
		activeEditorArea = $(this).closest('.template-edit-row').find('textarea.template-editor');
	});

	langOverlayEl.on('click', '.insert-phrase-id-trigger', function() {
		var phraseId = $(this).data('phrase-id') || '';
		langOverlay.close();
		activeEditorArea.data('cm').replaceSelection('{{ phrase(\'' + phraseId + '\') }}');
	});
}