Orb.createNamespace('DeskPRO.Agent');

DeskPRO.Agent.RteEditor = {
	initRteAgentReply: function(textarea, options) {
		textarea = $(textarea);
		options = options || {};

		if (!options.defaultIsHtml) {
			var val = textarea.val();
			if (val.length) {
				textarea.val(DP.convertTextToWysiwygHtml(val, true));
			}
		}

		var inlineHiddenPosition = options.inlineHiddenPosition;

		// must be done before initializing
		var dropZone = textarea.siblings('.drop-file-zone');

		if (window.DP_AGENT_RTE_BUTTONS) {
			var b = window.DP_AGENT_RTE_BUTTONS;
			var buttons = [];
			if (b.html) buttons.push('html');

			if ((b.bold || b.italic || b.underline || b.strike) && buttons.length) buttons.push('|');
			if (b.bold) buttons.push('bold');
			if (b.italic) buttons.push('italic');
			if (b.underline) buttons.push('underline');
			if (b.strike) buttons.push('deleted');

			if (b.color) {
				if (buttons.length) buttons.push('|');
				buttons.push('fontcolor');
			}

			if (b.alignment) {
				if (buttons.length) buttons.push('|');
				buttons.push('alignment');
			}

			if (b.list) {
				if (buttons.length) buttons.push('|');
				buttons.push('unorderedlist');
				buttons.push('orderedlist');
				buttons.push('outdent');
				buttons.push('indent');
			}

			if ((b.image || b.link || b.table || b.hr) && buttons.length) buttons.push('|');
			if (b.image) buttons.push('image');
			if (b.link) buttons.push('link');
			if (b.table) buttons.push('table');
			if (b.hr) buttons.push('horizontalrule');

		} else {
			var buttons = ['html', '|', 'bold', 'italic', 'underline', '|',  'unorderedlist', 'orderedlist', 'outdent', 'indent', '|', 'image', 'link', '|', 'alignment'];
		}

		var defaultOptions = {
			direction: textarea.attr('dir') || 'ltr',
			buttons: buttons,
			minHeight: 150,
			observeImages: false,
			cleanup: false,
			imageUpload: BASE_URL + 'agent/misc/accept-redactor-image-upload',
			uploadFields: {
				_rt: window.DP_REQUEST_TOKEN
			},
			plugins: ['clean_text'],
			imageUploadCallback: function(obj, json) {
				if (inlineHiddenPosition) {
					inlineHiddenPosition.after($('<input type="hidden" name="blob_inline_ids[]" />').val(json.blob_id));
				}
			},
			imageUploadErrorCallback: function(obj, json) {
				alert(json.error);
			}
		};

		if (options.autosaveContent && options.autosaveContentId) {
			defaultOptions.autosave = BASE_URL + 'agent/misc/redactor-autosave/' + options.autosaveContent + '/' + options.autosaveContentId;
			defaultOptions.interval = 5;
		}

		options = Object.merge(defaultOptions, options);

		var autosaveUrl = options.autosave,
			autosaveInterval = options.interval || 5,
			preAutosaveCallback = options.preAutosaveCallback;

		options.autosave = false;
		options.cleanup = false; // must always be false for paste of images to work - code below implements default cleanup
		textarea.addClass('with-redactor');
		textarea.redactor(options);

		var api = textarea.data('redactor');
		if (!api) {
			return false;
		}

		var editor = textarea.getEditor();
		if (!editor) {
			return false
		}

		api.$toolbar.find('a').attr('unselectable', 'on').attr('tabindex', '-1');
		api.$editor.addClass('unreset');

		editor.bind('keydown', function(ev) {
			ev.stopPropagation();

			if (ev.metaKey && !ev.ctrlKey) { // pressing "cmd" on a mac
				var sel;
				if (window.getSelection && (sel = window.getSelection()) && sel.modify) {
					var adjustmentType = ev.shiftKey ? "extend" : "move";

					switch (ev.keyCode) {
					case 39: // right - act like "end" in windows
							sel.modify(adjustmentType, "right", "lineboundary");
							ev.preventDefault();
							break;

						case 37: // left - act like "home" in windows
							sel.modify(adjustmentType, "left", "lineboundary");
							ev.preventDefault();
							break;
					}
				}
			}
		});

		editor.bind('keypress', function(ev) {
			ev.stopPropagation();
		});

		editor.bind('dragover drop', function(ev) {
			ev.stopPropagation();
		});

		// setup autosave
		if (autosaveUrl) {
			var getAutosaveData = function(api) {
				var newContent = api.getCode(),
					name = api.$el.attr('name');

				var data = [];
				data.push({
					name: name,
					value: newContent
				});

				if (preAutosaveCallback) {
					data = preAutosaveCallback(textarea, data);
				}

				return data;
			};

			var autosaveContent = api.getCode(),
				autosaveData = getAutosaveData(api);

			var saveFnRunning = false;
			var saveFn = $.proxy(function() {
				if (saveFnRunning) {
					return;
				}
				if (!textarea.data('redactor')) {
					clearInterval(autosaveTimer);
					autosaveTimer = false;
					return;
				}

				if (!api.$editor.is(':visible')) {
					return;
				}

				if (textarea.data('disable-autosave')) {
					return;
				}

				var newContent = this.getCode(),
					newData = getAutosaveData(this);

				if (newData.length) {
					for (var i = 0; i < newData.length; i++) {
						if (newData[i].name == name) {
							newContent = newData[i].value;
							break;
						}
					}
				}

				if (window.JSON && window.JSON.stringify) {
					if (JSON.stringify(newData) === JSON.stringify(autosaveData)) {
						return;
					}
				} else {
					if (newContent == autosaveContent) {
						return;
					}
				}

				autosaveContent = newContent;
				autosaveData = newData;

				saveFnRunning = true;
				var ajax = $.ajax({
					url: autosaveUrl,
					type: 'post',
					data: newData,
					complete: function() {
						saveFnRunning = false;
						textarea.data('autosave-running', null);
					},
					success: $.proxy(function(data) {
						if (typeof this.opts.autosaveCallback === 'function') {
							this.opts.autosaveCallback(data, this);
						}
					}, this)
				});
				textarea.data('autosave-running', ajax);
			}, api);

			var autosaveTimer = setInterval(saveFn, autosaveInterval * 1000);

			textarea.on('dp_autosave_trigger', saveFn);
		}

		// drag onto the editor to upload
		if (api.opts.imageUpload && !$.browser.msie) {
			var dropTarget = dropZone.length ? dropZone : editor;
			dropTarget.bind('drop', function(event) {
				event.preventDefault();

				var file = event.originalEvent.dataTransfer.files[0];
				if (!file) {
					return;
				}
				var fd = new FormData();

				// append file data
				fd.append('file', file);

				$.ajax({
					url: api.opts.imageUpload,
					dataType: 'html',
					data: fd,
					cache: false,
					contentType: false,
					processData: false,
					type: 'POST',
					success: $.proxy(function(data) {
						var json = $.parseJSON(data);

						if (typeof json.error == 'undefined') {
							$.proxy(api.imageUploadCallback, api)(json);
						} else {
							$.proxy(api.opts.imageUploadErrorCallback, api)(api, json);
							$.proxy(api.imageUploadCallback, api)(false);
						}

					}, api)
				});
			});

			if (dropZone.length) {
				textarea.getEditor().after(dropZone);
			}
		} else {
			dropZone.remove();
		}

		// setup paste support for images (Webkit, FireFox only)
		var pasteImageCounter = 1;

		var sendImage = function(pasteId, type, data, encoding) {
			try {
				var form = new FormData();
				if (typeof(data) == 'string') {
					// data URI
					var byteString;
					if (encoding == 'base64') {
						byteString = atob(data);
					} else {
						byteString = unescape(data);
					}

					var array = [];
					for(var i = 0; i < byteString.length; i++) {
						array.push(byteString.charCodeAt(i));
					}
					data = new Blob([new Uint8Array(array)], {type: 'image/' + type});
				}

				form.append('file', data, 'upload.' + type);
				form.append('filename', 'upload.' + type);
			} catch (e) {
				return false;
			}

			$.ajax({
				url: BASE_URL + 'agent/misc/accept-redactor-image-upload',
				type: 'POST',
				dataType: 'json',
				data: form,
				processData: false,
				contentType: false,
				success: function(json) {
					if (!textarea.data('redactor')) {
						return;
					}

					var img = textarea.getEditor().find('img[data-paste-id=' + pasteId + ']');
					if (json.error) {
						img.remove();
					} else {
						img.data('paste-id', '').attr('src', json.filelink);
						if (typeof api.opts.imageUploadCallback === 'function') {
							api.opts.imageUploadCallback(api, json);
						}
					}

					textarea.data('redactor').insertHtml('');
				}
			});

			return true;
		};

		// since our <p> tags only have one linebreak, lets turn them into <divs> since
		// that's how they act
		textarea.getEditor().on('copy', function(e) {
			api.saveSelection();

			var html = api.getSelectedHtml();
			html = html.replace(/<p/gi, '<p data-redactor="1"');
			if (!$.browser.msie) {
				html = html.replace(/<(p|div)[^>]><\/(p|div)>/i, '');
			}

			var div = $('<div data-redactor-wrapper="1" />').html(html).css({
				position: 'absolute',
				left: '-9999px'
			});

			$(document.body).append(div);

			var sel = api.getSelection();
			try {
				sel.selectAllChildren(div.get(0));
			} catch (e) {
				if (document.createRange && sel.removeAllRanges && sel.addRange) {
					var range = document.createRange();
					range.selectNode(div.get(0));
					sel.removeAllRanges();
					sel.addRange(range);
				}
			}

			setTimeout(function() {
				div.remove();
				api.restoreSelection();
			}, 0);
		});

		textarea.getEditor().on('paste', $.proxy(function(ev) {
			this.pasteRunning = true;

			if (ev.originalEvent.clipboardData) {
				var items = ev.originalEvent.clipboardData.items;
				if (items) {
					var hasImage = false;
					for (var i = 0; i < items.length; i++) {
						if (items[i].type.match(/^image\/([a-z0-9_-]+)$/i)) {
							var blob = items[i].getAsFile();
							var URLObj = window.URL || window.webkitURL;
							var source = URLObj.createObjectURL(blob);

							var pasteImageId = pasteImageCounter++;

							if (sendImage(pasteImageId, RegExp.$1, blob)) {
								textarea.insertHtml('<img src="' + source + '" data-paste-id="' + pasteImageId + '">');
								hasImage = true;
							}
						}
					}

					// pasted an image - won't be other content
					if (hasImage) {
						ev.preventDefault();
						ev.stopPropagation();
						return;
					}
				}
			}

			this.setBuffer();

			if (this.opts.autoresize === true) {
				this.saveScroll = document.body.scrollTop;
			} else {
				this.saveScroll = this.$editor.scrollTop();
			}

			var frag = this.extractContent();

			setTimeout($.proxy(function() {
				var pastedFrag = this.extractContent();
				this.$editor.append(frag);

				this.restoreSelection();

				var imgs = pastedFrag.querySelectorAll('img');
				if (imgs) {
					for (var i = 0; i < imgs.length; i++) {
						imgs[i].setAttribute('style', '-x-ignore: 1');
						if (imgs[i].src.match(/^data:image\/([a-z0-9_-]+);([a-z0-9_-]+),([\W\w]+)$/i)) {
							var pasteImageId = pasteImageCounter++;
							imgs[i].setAttribute('data-paste-id',  pasteImageId);

							if (!sendImage(pasteImageId, RegExp.$1, RegExp.$3, RegExp.$2)) {
								imgs[i].parentNode.removeChild(imgs[i]);
							}
						}
					}
				}

				var html = this.getFragmentHtml(pastedFrag);

				// since <p> only counts as one line break, we need to fix that
				html = $.trim(html);
				html = html.replace(/^<div[^>]* data-redactor-wrapper="1"[^>]*>([\w\W]+)<\/div>$/, '$1');
				html = html.replace(/(<p[^>]* data-redactor="1"[^>]*>[\w\W]*?<\/p>)<p>(<br>)?<span><span><\/span><\/span><\/p>/ig, '$1');
				html = html.replace(/<p>(<br>)?<span><span><\/span><\/span><\/p>$/, '');

				// convert divs to p's and keep empty ones
				html = html.replace(/<div/gi, '<p').replace(/<\/div>/g, '</p>');
				html = html.replace(/<p([^>]*)>(\s*|<br\s*\/?>|&nbsp;)<\/p>/gi, '<br/>');
				html = html.replace(/(<p[^>]*) data-redactor="1"/g, '$1');
				html = html.replace(/<\/p>\s*<p>/g, '<\/p><p>');
				html = html.replace(/^<p>/, '');
				html = html.replace(/<\/p>$/, '');

				this.pasteCleanUp(html);

				this.pasteRunning = false;
			}, this), 1);

		}, textarea.data('redactor')));

		return textarea;
	}
};

if (typeof RedactorPlugins === 'undefined') var RedactorPlugins = {};

// Based on the plugin by João Sardinha
// (https://github.com/johnsardine/redactor-plugins)
RedactorPlugins.clean_text = {

	init: function() {

		// Create button
		this.addBtn('clean_text', 'Clean selection formatting', function(redactor, event, button_key) {

			// Grab selected text
			var html = redactor.getSelectedHtml();

			html = html.replace(/<\/td>/g, "\t");
			html = html.replace(/<\/tr>/g, "\n");
			html = html.replace(/\s*<div[^>]*>\s*/g, "\n");
			html = html.replace(/\s*<\/p>\s*<p[^>]*>\s*/g, "\n\n");
			html = html.replace(/\s*<p[^>]*>\s*/g, "\n");
			html = html.replace(/\s*<br[^>]*\/>\s*/g, "\n");
			html = html.replace(/\s*<br[^>]*>\s*/g, "\n");

			// Strip out html
			html = html.replace(/(<([^>]+)>)/ig,"");
			html = $.trim(html);
			html = html.replace(/\r|\r\n|\n/g, "\n<br/>");

			// Set buffer (allows undo shortcut)
			redactor.setBuffer();

			// Replace selection with clean text
			redactor.insertHtml(html);

			// Sync code
			redactor.syncCode();
		});

		// Add separator before button
		this.addBtnSeparatorBefore('clean_text');

		// Add icon to button
		jQuery('a.redactor_btn_clean_text').css({
			backgroundImage : ' url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAYAAACNMs+9AAAAQ0lEQVQYV2MMDQ39zwAEq1evZgTR6AAmzwhjYFOMLAc2BZtidDG4dcgSyNbDnITiLnTFyO4mXSFRVhPlGaKDh9gABwAJuDgDsQ44aQAAAABJRU5ErkJggg==)',
			backgroundPosition : '7px 8px'
		});
	}
}