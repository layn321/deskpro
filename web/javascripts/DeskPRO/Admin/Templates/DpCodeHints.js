function DpCodeHints() {
	var lastTip = null;
	var lastToken = null;

	function hide() {
		if (lastTip) {
			lastTip.remove();
			lastTip = null;
		}

		lastToken = null;
	}

	function show(cm, textarea) {
		var cursor   = cm.getCursor();
		var token    = cm.getTokenAt(cursor);
		var textarea = $(textarea);
		var tokenId  = textarea.attr('id') + ' ' + (token.start + ' ' + token.end);
		var phraseMatch = null;

		var cursor = cm.getCursor();
		var pos = cm.cursorCoords(false, 'page');

		if (token.string) {
			switch (token.string) {
				default:
					phraseMatch = token.string.match(/(\{\{\s*phrase\(')(.*?)\'.*?\)\s*\}\}/);
					if (phraseMatch && !(cursor.ch >= (token.start+phraseMatch[1].length) && cursor.ch <= (token.start+phraseMatch[1].length+phraseMatch[2].length))) {
						if (lastTip) {
							lastTip.remove();
							lastTip = null;
							lastToken = null;
						}
						return;
					}
			}
		}

		// The cursor changed but we are viewing the same token,
		// so dont need to do anything
		if (lastToken == tokenId) {
			return;
		}

		// Clean up the old hint
		if (lastTip) {
			lastTip.remove();
			lastTip = null;
			lastToken = null;
		}

		lastToken = tokenId;

		if (token.string) {
			switch (token.string) {
				case '<dp:ticket-history':
					lastTip = createTemplateTip("Inserts the contents of all previous messages of the ticket.", 'DeskPRO:emails_common:ticket-message.html.twig', pos);
					break;

				case '<dp:agent-ticket-history':
					lastTip = createTemplateTip("Inserts the contents of all previous messages of the ticket. The history is suitable for an agent (for example, notes are included).", 'DeskPRO:emails_common:ticket-message.html.twig', pos);
					break;

				case '<dp:ticket-logs':
					lastTip = createTemplateTip("Inserts a list of changes performed on a ticket. For example, change of status or department.", 'DeskPRO:emails_common:ticket-log-actiontext.html.twig', pos);
					break;

				case '<dp:ticket-rating-links':
					lastTip = createTemplateTip("Inserts links that let the user rate their support as good, bad or okay.", 'DeskPRO:emails_common:ticket-rating-links.html.twig', pos);
					break;

				case '<dp:ticket-properties-table':
					lastTip = createTemplateTip("Inserts a table containing the full ticket details.", 'DeskPRO:emails_common:ticket-props-table.html.twig', pos);
					break;

				case '<dp:agent-reply':
					lastTip = createTemplateTip("Inserts the agents reply.", 'DeskPRO:emails_common:ticket-message-agent.html.twig', pos);
					break;

				case '<dp:user-reply':
					lastTip = createTemplateTip("Inserts the users reply.", 'DeskPRO:emails_common:ticket-message.html.twig', pos);
					break;

				default:
					if (phraseMatch) {

						var phraseId = phraseMatch[2];
						var el = $('<div class="code-tip phrase"><i class="flat-spinner"></i></div>');
						lastTip = el;
						lastTip.appendTo('body');
						lastTip.css({ left: pos.x, top: pos.y + 15});

						$.ajax({
							url: BASE_URL + "admin/languages/get-phrase-text.json",
							data: {
								phrase_id: phraseId
							},
							success: function(data) {
								el.find('i.flat-spinner').remove();

								var row = $('<div class="phrase-texts"></div>');
								Array.each(data.langs, function(info) {
									var phraseRow = $('<div class="phrase-row"></div>');

									if (DP_DEFAULT_LANG_ID && info.language_id == DP_DEFAULT_LANG_ID) {
										phraseRow.addClass('default-lang');
									}

									var label = $('<div class="phrase-label"></div>');
									label.text(info.language_title);
									label.appendTo(phraseRow);

									if (info.phrase.indexOf("\n") !== -1 || info.phrase.indexOf("\r") !== -1) {
										var input = $('<div class="phrase-input"><textarea class="phrase-txt"></textarea></div>');
									} else {
										var input = $('<div class="phrase-input"><input type="text" class="phrase-txt" /></div>');
									}
									input.find('.phrase-txt').val(info.phrase).attr('name', "lang_phrase[" + phraseId + "][" + info.language_id + "]");
									input.appendTo(phraseRow);

									phraseRow.appendTo(row);
								});

								row.find('.default-lang').detach().prependTo(row);

								row.appendTo(el);

								var saveRow = $('<div class="save-row"><button class="save-trigger clean-white">Save</button><i class="flat-spinner" style="display: none;"></i></div>');
								var spinner = saveRow.find('i.flat-spinner');
								var saveBtn = saveRow.find('button.save-trigger');
								saveRow.appendTo(el);

								saveBtn.on('click', function(ev) {
									saveBtn.hide();
									spinner.show();

									var postData = el.find('input, textarea').serializeArray();

									$.ajax({
										url: BASE_URL + 'admin/languages/phrases-save-array.json',
										data: postData,
										type: 'POST',
										dataType: 'json',
										complete: function( ){
											spinner.hide();
											saveBtn.show();
										},
										success: function(data) {
											spinner.hide();
											saveBtn.show();
											DeskPRO_Window.util.showSavePuff(saveBtn);
										}
									});
								});
							}
						});
					}
					break;
			}
		}
	}

	function createTemplateTip(text, name, pos) {
		var el = $('<div class="code-tip"></div>'), btn;
		el.text(text);

		btn = $('<button class="clean-white edit-template-trigger">Edit Template &rarr;</button>');
		btn.css('margin-left', '10px');
		el.append(btn);

		el.appendTo('body');
		el.css({ left: pos.x, top: pos.y + 15});

		btn.on('click', function(ev) {

			hide();

			ev.preventDefault();
			ev.stopPropagation();

			$('#edittpl_overlay').remove();
			var tpl = $('#edittpl_overlay_source').get(0).innerHTML;
			var el = $(tpl);
			el.appendTo('body');

			var template_name = name	;
			var overlay = new DeskPRO.UI.Overlay({
				contentElement: el,
				destroyOnClose: true,
				fullScreen: true,
				onBeforeOverlayOpened: function() {
					if (el.is('.has-init')) return;
					el.addClass('has-init');

					el.find('textarea').val('').addClass('loading');

					$.ajax({
						url: BASE_URL + 'admin/templates/get-template-code?name=' + template_name,
						context: this,
						success: function(val) {
							el.find('textarea').val(val).removeClass('loading');
						}
					});

					$('.revert-trigger', el).on('click', function() {
						el.find('.overlay-footer').addClass('loading');
						$.ajax({
							url: BASE_URL + 'admin/templates/revert-template.json?name=' + template_name,
							type: 'POST',
							context: this,
							success: function(val) {
								overlay.close();
							}
						});
					});

					$('.save-trigger', el).on('click', function() {

						el.find('.overlay-footer').addClass('loading');

						var postData = {
							name: template_name,
							code: el.find('textarea').val().trim()
						};

						if (name == 'head_include') {
							if (!postData.code.length) {
								$.ajax({
									type: 'POST',
									url: BASE_URL + 'admin/templates/revert-template.json?name=' + template_name,
									success: function() {
										window.location.reload(false);
									}
								});
							} else {
								$.ajax({
									url: BASE_URL + 'admin/templates/save-template.json',
									context: this,
									type: 'POST',
									data: postData,
									success: function(data) {
										window.location.reload(false);
									}
								});
							}
							return;
						}

						if (!postData.code.length) {
							$.ajax({
								type: 'POST',
								url: BASE_URL + 'admin/templates/revert-template.json?name=' + template_name
							});

							overlay.close();
						} else {
							$.ajax({
								url: BASE_URL + 'admin/templates/save-template.json',
								context: this,
								type: 'POST',
								data: postData,
								success: function(data) {
									el.find('.overlay-footer').removeClass('loading');

									if (data.error) {
										alert(data.error_message + "\n\nLine: " + data.error_line);
										return;
									}

									overlay.close();
								}
							});
						}
					});
				}
			});
			overlay.open();
		});

		return el;
	}

	this.show = show;
	this.hide = hide;
}