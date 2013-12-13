Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');

DeskPRO.Agent.PageFragment.Page.SnippetViewer = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'snippets';
		this.allowDupe = true;
		this.activeSection = null;
		this.activeSnippets = $([]);
		this.noIgnoreForm = true;
	},

	initPage: function(el) {
		var self = this;
		this.snippet_typename = this.meta.snippet_typename;

		if (this.snippet_typename == 'tickets') {
			var driver = DeskPRO_Window.ticketSnippetDriver;
		} else {
			var driver = DeskPRO_Window.chatSnippetDriver;
		}

		this.snippetDriver = driver;

		//----------------------------------------
		// Browsing snippets
		//----------------------------------------

		var catList = this.getEl('catlist');
		var snippetList = this.getEl('snippet_list');
		var filterInput = this.getEl('filter');
		var langSelect  = this.getEl('show_language_id');

		var rowsTpl = twig({
			data: DeskPRO_Window.util.getPlainTpl($('#snippet_rows_tpl'))
		});

		this.rowsTpl = rowsTpl;

		var pickLangText = function(lang_vals, myLangId, showLangId) {
			if (!showLangId || showLangId == "0") {
				showLangId = DESKPRO_DEFAULT_LANG_ID;
			}

			var ret = { my: '', myId: 0, show: '', showId: 0 };
			var hasShow = false;
			Array.each(lang_vals, function(l) {
				if (l.value && l.language_id == myLangId) {
					ret.my = l.value;
					ret.myId = l.language_id;
				}
				if (l.value && l.language_id == showLangId) {
					ret.show = l.value;
					ret.showId = l.language_id;
				}
				if (!ret.show && l.value) {
					ret.show = l.value;
					ret.showId = l.language_id;
				}
			});

			if ((!ret.show || ret.showId != showLangId) && ret.my && ret.myId) {
				ret.show = ret.my;
				ret.showId = ret.myId;
			}

			return ret;
		};

		this.pickLangText = pickLangText;

		var useLocalCompare  = (typeof String.localeCompare != "undefined");

		var sortSnippets = function(snippets) {
			return snippets.sort(function(a, b) {
				var a_string, b_string;
				a_string = a.title_use.show || '';
				b_string = b.title_use.show || '';

				if (useLocalCompare) {
					var cmp = a_string.localeCompare(b_string, null, {
						usage: 'sort',
						ignorePunctuation: true,
						caseFirst: false
					});
					return cmp;
				} else {
					a_string = a_string.toLowerCase();
					b_string = b_string.toLowerCase();

					if (a_string == b_string) {
						return 0;
					}

					return (a_string < b_string) ? -1 : 1;
				}
			});
		};

		var updateCatList = function(categoryId, filterString, languageId) {

			var myLangId   = DESKPRO_PERSON_LANG_ID;
			var showLangId = langSelect.val();

			if (categoryId) {
				driver.loadSnippets({
					categoryId: categoryId,
					filterString: filterString || null,
					languageId: languageId || null
				}, function(snippets) {
					var newList = $('<ul></ul>');

					Array.each(snippets, function(s) {
						s.title_use   = pickLangText(s.title, myLangId, showLangId);
						s.snippet_use = pickLangText(s.snippet, myLangId, showLangId);
					});

					snippets = sortSnippets(snippets);

					newList.html(rowsTpl.render({
						snippets: snippets
					}));

					snippetList.empty().append(newList);
				});
			} else {
				var catIds = [];
				catList.find('li').each(function() {
					var id = parseInt($(this).data('category-id'));
					if (id) {
						catIds.push(id);
					}
				});

				snippetList.empty();
				if (!catIds.length) {
					return;
				}

				var tick = 0;

				if (driver.driverName == 'client_db') {
					Array.each(catIds, function(cid) {
						driver.loadSnippets({
							categoryId: cid,
							filterString: filterString || null,
							languageId: languageId || null
						}, function(snippets) {
							if (!snippets.length) {
								return;
							}

							Array.each(snippets, function(s) {
								s.title_use   = pickLangText(s.title, myLangId, showLangId);
								s.snippet_use = pickLangText(s.snippet, myLangId, showLangId);
							});

							snippets = sortSnippets(snippets);

							var hasMore = false;
							if (snippets.length > 15) {
								var hasMore = true;
								snippets = snippets.slice(0, 15);
							}

							var newListWrap = $('<div/>');
							var catTitle = $('<div class="cat-title"/>');
							catTitle.text(catList.find('.category-' + cid).text());
							catTitle.appendTo(newListWrap);

							var newList = $('<ul></ul>');

							newList.html(rowsTpl.render({
								snippets: snippets
							}));

							newListWrap.append(newList);

							snippetList.append(newListWrap);
						});
					});
				} else {
					driver.loadSnippets({
						filterString: filterString || null
					}, function(snippets) {
						if (!snippets.length) {
							return;
						}

						Array.each(catIds, function(cid) {

							var catSnippets = snippets.filter(function(s) { return s.category_id == cid; });
							if (!catSnippets.length) {
								return;
							}

							Array.each(catSnippets, function(s) {
								s.title_use   = pickLangText(s.title, myLangId, showLangId);
								s.snippet_use = pickLangText(s.snippet, myLangId, showLangId);
							});

							catSnippets = sortSnippets(catSnippets);

							var hasMore = false;
							if (catSnippets.length > 15) {
								var hasMore = true;
								catSnippets = catSnippets.slice(0, 15);
							}

							var newListWrap = $('<div/>');
							var catTitle = $('<div class="cat-title"/>');
							catTitle.text(catList.find('.category-' + cid).text());
							catTitle.appendTo(newListWrap);

							var newList = $('<ul></ul>');

							newList.html(rowsTpl.render({
								snippets: catSnippets
							}));

							newListWrap.append(newList);
							snippetList.append(newListWrap);
						});
					});
				}
			}
		};

		langSelect.on('change', function(ev) {
			var categoryId = parseInt(catList.find('.on').data('category-id') || 0) || 0;
			var filterString = $.trim(filterInput.val());
			var languageId   = parseInt(langSelect.val()) || 0;

			updateCatList(categoryId, filterString, languageId);
		});

		catList.on('click', 'li', function(ev) {
			Orb.cancelEvent(ev);
			catList.find('.on').removeClass('on');
			var categoryId   = $(this).addClass('on').data('category-id');
			var filterString = $.trim(filterInput.val());
			var languageId   = parseInt(langSelect.val()) || 0;

			updateCatList(categoryId, filterString, languageId);
		});

		var filterTimer = null;
		var sendUpdate = function() {
			filterTimer = null;
			var categoryId = parseInt(catList.find('.on').data('category-id') || 0) || 0;
			var filterString = $.trim(filterInput.val());
			var languageId   = parseInt(langSelect.val()) || 0;

			updateCatList(categoryId, filterString, languageId);
		};

		var cmdEat = false;
		filterInput.on('change keydown keyup', function(ev) {

			if (ev.keyCode == 13 /* enter key */) {
				ev.preventDefault();

				if (cmdEat) { cmdEat = false; return; }
				cmdEat = true;

				var activeSnippets = snippetList.find('li.snippet');
				var current = activeSnippets.filter('.cursor');
				if (!current[0]) {
					if (activeSnippets.length == 1) {
						current = activeSnippets;
					}
				}

				if (current[0]) {
					current.click();
				}

				return;
			} else if (ev.keyCode == 40 /* down key */ || ev.keyCode == 38 /* up key */) {
				ev.preventDefault();

				if (cmdEat) { cmdEat = false; return; }
				cmdEat = true;

				var activeSnippets = snippetList.find('li.snippet');


				var dir = ev.keyCode == 40 ? 'down' : 'up';

				var current = activeSnippets.filter('.cursor');
				if (!current.length) {
					if (dir == 'down') {
						activeSnippets.first().addClass('cursor');
					} else {
						activeSnippets.last().addClass('cursor');
					}
				} else {
					var nextIndex = activeSnippets.index(current);
					if (dir == 'down') {
						nextIndex++;
					} else {
						nextIndex--;
					}

					if (nextIndex < 0) {
						nextIndex = activeSnippets.length-1;
					} else if (nextIndex > (activeSnippets.length-1)) {
						nextIndex = 0;
					}

					current.removeClass('cursor');
					activeSnippets.eq(nextIndex).addClass('cursor');
				}

				return;
			}

			cmdEat = false;

			if (filterTimer) {
				window.clearTimeout(filterTimer);
				filterTimer = null;
			}

			filterTimer = window.setTimeout(function() {
				sendUpdate();
			}, 140);
		});

		//----------------------------------------
		// Inserting snippets
		//----------------------------------------

		snippetList.on('click', 'li', function(ev) {
			if ($(ev.target).hasClass('edit-trigger')) {
				return;
			}

			Orb.cancelEvent(ev);

			self.insertSnippetEl($(this), ev);
		});

		//----------------------------------------
		// Editing categories
		//----------------------------------------

		var catEditor = new (function() {
			var editCatEl = self.getEl('edit_snippet_cat');
			var editCatBack = null;
			var hasInit = false;

			self.wrapper.find('.trigger-newcat').on('click', function(ev) {
				Orb.cancelEvent(ev);
				openCatEditor(0, '');
			});

			var openCatEditor = function(catId, catTitle, shareOpt, openPos) {
				if (!hasInit) {
					hasInit = true;

					editCatEl.detach().appendTo('body');
					editCatBack = $('<div class="dp-popover-backdrop" />').hide();
					editCatBack.appendTo('body');

					editCatBack.on('click', function(ev) {
						Orb.cancelEvent(ev);
						closeCatEditor();
					});

					editCatEl.find('.trigger-close').on('click', function(ev) {
						Orb.cancelEvent(ev);
						closeCatEditor();
					});

					editCatEl.find('.trigger-save').on('click', function(ev) {
						Orb.cancelEvent(ev);
						saveCategory();
					});

					editCatEl.find('.delete-cat-trigger').on('click', function(ev) {
						Orb.cancelEvent(ev);
						delCategory();
					});
				}

				if (!openPos) {
					openPos = {
						of: self.wrapper.find('.trigger-newcat').first(),
						my: 'left top',
						at: 'center right',
						collision: 'flipfit'
					};
				}

				if (catList.find('li').length < 2) {
					editCatEl.find('.no-cats-notice').show();
				} else {
					editCatEl.find('.no-cats-notice').hide();
				}

				editCatEl.css({left: 0, top: 0});
				editCatEl.position(openPos);

				editCatEl.find('.input_id').val(catId || '0');
				editCatEl.find('.input_title').val(catTitle || '');

				shareOpt = shareOpt || 'me';
				editCatEl.find('.perm-type-opt').prop('checked', false).filter('[value="'+shareOpt+'"]').prop('checked', true);

				if (catId && catId != "0") {
					editCatEl.find('.delete-link-wrap').show();
				} else {
					editCatEl.find('.delete-link-wrap').hide();
				}

				editCatEl.show();
				editCatBack.show();
			};
			this.openCatEditor = openCatEditor;

			var closeCatEditor = function() {
				editCatEl.hide();
				editCatBack.hide();
			};

			var saveCategory = function() {
				var catId    = editCatEl.find('.input_id').val();
				var catTitle = $.trim(editCatEl.find('.input_title').val());
				var shareOpt = editCatEl.find('.perm-type-opt').filter(':checked').val();

				if (!catTitle) {
					closeCatEditor();
					return;
				}

				var postData = [];
				postData.push({
					name: 'title',
					value: catTitle
				});
				postData.push({
					name: 'perm_type',
					value: shareOpt
				});

				editCatEl.addClass('dp-loading-on');
				$.ajax({
					url: BASE_URL + 'agent/text-snippets/'+self.snippet_typename+'/categories/'+catId+'/save.json',
					data: postData,
					dataType: 'json',
					type: 'POST',
					complete: function() {
						editCatEl.removeClass('dp-loading-on');
					},
					success: function(data) {
						closeCatEditor();

						var cat = data.category;

						var catEl = catList.find('.category-' + cat.id);
						if (catEl[0]) {
							catEl.find('.label').text(catTitle);
							if (shareOpt == 'global') {
								catEl.data('is-global', 1);
							} else {
								catEl.data('is-global', null);
							}

							self.getEl('editsnippet_category_select').find('option[value="' + catId + '"]').text(catTitle);
						} else {
							catEl = $('<li><a><span class="label"></span></a><span class="trigger-edit-cat"><i class="icon-cog"></i></span></li>');
							catEl.addClass('category category-' + cat.id);
							catEl.data('category-id', cat.id);
							if (shareOpt == 'global') {
								catEl.data('is-global', 1);
							} else {
								catEl.data('is-global', null);
							}
							catEl.find('.label').text(cat.title[0].value);
							catEl.insertAfter(catList.find('.category-0'));

							// Also add cat option
							var catOpt = $('<option/>');
							catOpt.val(cat.id);
							catOpt.text(cat.title[0].value);

							self.getEl('editsnippet_category_select').prepend(catOpt);
						}

						catEl.click();

						// reload the shell
						driver.getWidgetShellTemplate(true);
					}
				});
			};

			var delCategory = function() {
				if (confirm('Are you sure?')) {
					var catId = editCatEl.find('.input_id').val();
					editCatEl.addClass('dp-loading-on');
					$.ajax({
						url: BASE_URL + 'agent/text-snippets/'+self.snippet_typename+'/categories/'+catId+'/delete.json',
						dataType: 'json',
						type: 'POST',
						complete: function() {
							editCatEl.removeClass('dp-loading-on');
						},
						success: function(data) {

							editCatEl.removeClass('dp-loading-on');
							closeCatEditor();

							if (data.error) {
								alert("You cannot delete this category because it still has snippets in it. Delete the snippets first then try again.");
								return;
							}

							var catEl = catList.find('.category-' + catId);
							catEl.remove();

							// reload the shell
							driver.getWidgetShellTemplate(true);
						}
					});
				};
			};

			this.destroy = function() {
				if (hasInit) {
					editCatEl.detach();
					editCatBack.detach();
				}
			};
		})();

		this.ownObject(catEditor);

		catList.on('click', '.trigger-edit-cat', function(ev) {
			Orb.cancelEvent(ev);
			var row = $(this).closest('li');
			var catId = row.data('category-id');
			var catTitle = $.trim(row.find('.label').text());
			var shareOpt = row.data('is-global') ? 'global' : 'me';
			var openPos = {
				of: $(this),
				my: 'left top',
				at: 'center right',
				collision: 'flipfit'
			};

			catEditor.openCatEditor(catId, catTitle, shareOpt, openPos);
		});

		//----------------------------------------
		// Editing snippets
		//----------------------------------------

		this._initEditingSnippets();

		if (!catList.find('.on')[0]) {
			catList.find('li').first().click();
		}

		filterInput.focus();
		this.addEvent('activate', function() {
			if (filterInput) {
				filterInput.focus();
			}
		});
	},

	closeSelf: function() {
		var ev = {cancel: false};
		this.fireEvent('closeSelf', ev);

		if (!ev.cancel) {
			this.parent();
		}
	},

	destroy: function() {
		if (DeskPRO_Window.activeListNav == this.listNav) {
			DeskPRO_Window.activeListNav = null;
		}
		if (this.newCatOverlay) this.newCatOverlay.remove();
	},

	insertSnippetEl: function(el, event, evData) {

		var snippetId = $(el).data('snippet-id');
		var snippetCode = [];

		el.find('.snippet-value').each(function(ev) {
			snippetCode.push({
				language_id: $(this).data('language-id'),
				value: $(this).val()
			});
		});

		evData = evData || {};
		evData = $.extend(evData, {
			event: event || null,
			snippetId: snippetId,
			snippetCode: snippetCode
		});

		this.fireEvent('snippetClick', [evData]);
	},

	//#########################################################################
	// Editing snippets
	//#########################################################################

	_initEditingSnippets: function() {
		var self = this;
		var snippetList = this.getEl('snippet_list');
		var editSnippetEl = this.getEl('edit_snippet');
		var textarea = this.getEl('snippet_input');

		if (!textarea.data('redactor')) {
			DeskPRO_Window.initRteAgentReply(textarea, {
				defaultIsHtml: true,
				autoresize: false
			});
		}

		//------------------------------
		// Starting edit
		//------------------------------

		snippetList.on('click', '.edit-trigger', function(ev) {
			Orb.cancelEvent(ev);
			var snippetId = $(this).closest('li').data('snippet-id');
			self.snippetDriver.getSnippet(snippetId, function(snippet) {
				self.editSnippet(snippet);
			});
		});

		self.wrapper.find('.trigger-new-snippet').on('click', function(ev) {
			Orb.cancelEvent(ev);

			if (self.getEl('catlist').find('li').length < 2) {
				self.wrapper.find('.trigger-newcat').first().click();
				return;
			}

			self.editSnippet();
		});

		//------------------------------
		// Switching between langs
		//------------------------------

		editSnippetEl.find('.language_id').on('change', function(ev) {
			var langId         = $(this).val();
			var inputTitleEl   = self.getEl('title_input');
			var inputSnippetEl = self.getEl('snippet_input');

			var langTitleEl   = editSnippetEl.find('.lang-bound-title.lang-' + langId);
			var langSnippetEl = editSnippetEl.find('.lang-bound-snippet.lang-' + langId);

			textarea.data('redactor').syncCode();

			// The initial fire of this is after opening a new edit window,
			// so we're just setting the defaults but not syncing an empty value back to the lang-x elements
			if ($(this).hasClass('initial')) {
				inputTitleEl.val(langTitleEl.val());
				textarea.data('redactor').setCode(langSnippetEl.val());

			} else if ($(this).hasClass('set-bound')) {
				langTitleEl.val(inputTitleEl.val());
				langSnippetEl.val(inputSnippetEl.val());

			// Else make sure theyre both the same
			} else {
				inputTitleEl.val(langTitleEl.val());
				textarea.data('redactor').setCode(langSnippetEl.val());

				langTitleEl.val(inputTitleEl.val());
				langSnippetEl.val(inputSnippetEl.val());
			}

			$(this).removeClass('initial set-bound');
		});

		//------------------------------
		// Saving snippet
		//------------------------------

		editSnippetEl.find('.save-snippet-trigger').on('click', function(ev) {
			editSnippetEl.find('.language_id').addClass('set-bound').trigger('change');

			Orb.cancelEvent(ev);
			var snippet = self.editingSnippet;
			var oldShortcutCode = snippet.shortcut_code;

			snippet.category_id = editSnippetEl.find('select.category_id').val();

			editSnippetEl.find('.lang-bound-title').each(function() {
				var langId = $(this).data('language-id');
				var value = $(this).val();
				var found = false;

				for (var i = 0; i < snippet.title.length; i++) {
					if (snippet.title[i].language_id == langId) {
						snippet.title[i].value = value;
						found = true;
						break;
					}
				}

				if (!found) {
					snippet.title.push({
						language_id: langId,
						value: value
					})
				}
			});

			editSnippetEl.find('.lang-bound-snippet').each(function() {
				var langId = $(this).data('language-id');
				var value = $(this).val();
				var found = false;

				for (var i = 0; i < snippet.snippet.length; i++) {
					if (snippet.snippet[i].language_id == langId) {
						snippet.snippet[i].value = value;
						found = true;
						break;
					}
				}

				if (!found) {
					snippet.snippet.push({
						language_id: langId,
						value: value
					});
				}
			});

			snippet.shortcut_code = editSnippetEl.find('.shortcut_code').val();

			editSnippetEl.find('.overlay-footer').addClass('loading');
			self.snippetDriver.saveSnippet(snippet, function(snippet) {

				var myLangId   = DESKPRO_PERSON_LANG_ID;
				var showLangId = self.getEl('show_language_id').val();

				snippet.title_use   = self.pickLangText(snippet.title, myLangId, showLangId);
				snippet.snippet_use = self.pickLangText(snippet.snippet, myLangId, showLangId);

				editSnippetEl.find('.overlay-footer').removeClass('loading');
				self.snippetEditOverlay.close();

				var currentCatId = self.getEl('catlist').find('.on').data('category-id') || 0;

				var newList = $('<ul></ul>');
				newList.html(self.rowsTpl.render({
					snippets: [snippet]
				}));

				var row = newList.find('li').first();

				if (!currentCatId || snippet.category_id == currentCatId) {
					var exist = self.getEl('snippet_list').find('.snippet-' + snippet.id);
					if (exist[0]) {
						exist.replaceWith(row);
					} else {
						self.getEl('snippet_list').prepend(row);
					}
				}

				var newShortcutCode = snippet.shortcut_code;

				if (self.snippet_typename == 'tickets') {
					if (oldShortcutCode) {
						delete window.DESKPRO_TICKET_SNIPPET_SHORTCODES[oldShortcutCode];
					}
					if (newShortcutCode) {
						window.DESKPRO_TICKET_SNIPPET_SHORTCODES[newShortcutCode] = snippet.id;
					}
				} else if (self.snippet_typename == 'chat') {
					if (oldShortcutCode) {
						delete window.DESKPRO_CHAT_SNIPPET_SHORTCODES[oldShortcutCode];
					}
					if (newShortcutCode) {
						window.DESKPRO_CHAT_SNIPPET_SHORTCODES[newShortcutCode] = snippet.id;
					}
				}
			}, function() {
				editSnippetEl.find('.overlay-footer').removeClass('loading');
			});

		});

		//------------------------------
		// Deleting snippet
		//------------------------------

		editSnippetEl.find('.delete-snippet-trigger').on('click', function(ev) {
			var snippet = self.editingSnippet;
			editSnippetEl.find('.overlay-footer').addClass('loading');
			self.snippetDriver.deleteSnippet(snippet.id, function(snippet_id) {
				editSnippetEl.find('.overlay-footer').removeClass('loading');
				self.snippetEditOverlay.close();

				var exist = self.getEl('snippet_list').find('.snippet-' + snippet_id);
				exist.remove();
			}, function() {
				editSnippetEl.find('.overlay-footer').removeClass('loading');
			});
		});

		//------------------------------
		// Init overlay
		//------------------------------

		var varSel = editSnippetEl.find('.variables-select');
		editSnippetEl.find('.variables-insert-btn').on('click', function() {
			var text = '{{ ' + varSel.val() + ' }}';

			if (textarea.data('redactor')) {
				textarea.data('redactor').insertHtml(DP.convertTextToWysiwygHtml(text, false));
			} else {
				var pos = textarea.getCaretPosition();
				if (!pos) {
					textarea.setCaretPosition(0);
				}

				textarea.insertAtCaret(text);
			}
		});

		this.snippetEditOverlay = new DeskPRO.UI.Overlay({
			contentElement: editSnippetEl,
			zIndex: 30010
		});
	},

	editSnippet: function(snippet) {

		if (!snippet) {
			snippet = {
				id: 0,
				category_id: this.getEl('catlist').find('.on').data('category-id') || this.getEl('catlist').find('li').eq(1).data('category-id'),
				shortcut_code: '',
				title: [],
				snippet: []
			};
		}

		this.editingSnippet = snippet;
		var editSnippetEl = this.getEl('edit_snippet');
		editSnippetEl.find('input, textarea').val('');
		editSnippetEl.find('input.snippet_id').val(snippet.id);
		editSnippetEl.find('select.category_id').val(snippet.category_id);
		editSnippetEl.find('input.shortcut_code').val(snippet.shortcut_code);

		Array.each(snippet.title, function(trans) {
			editSnippetEl.find('input.title.lang-' + trans.language_id).val(trans.value);
		});
		Array.each(snippet.snippet, function(trans) {
			editSnippetEl.find('input.snippet.lang-' + trans.language_id).val(trans.value);
		});

		editSnippetEl.find('.language_id').addClass('initial').trigger('change');

		if (snippet.id) {
			editSnippetEl.find('.delete-link-wrap').show();
		} else {
			editSnippetEl.find('.delete-link-wrap').hide();
		}

		this.snippetEditOverlay.open();
	}
});
