Orb.createNamespace('DeskPRO.Agent.WindowElement.Section');

DeskPRO.Agent.WindowElement.Section.Publish = new Orb.Class({
	Extends: DeskPRO.Agent.WindowElement.Section.AbstractSection,

	init: function() {
		var self = this;
		this.expanded_ids = [];
		this.selected_id = null;
		this.buttonEl = $('#publish_section');
		this.lastLoad = null;

		this.urlFragmentName = 'publish';

		this.setSectionElement($('<section id="publish_outline"></section>'));

		DeskPRO_Window.getSectionData('publish_section', this._initSection.bind(this));

		window.setInterval(function() {
			self.reloadIfStale();
		}, 420000); // update every 7 mins auto-reload

		this.addEvent('show', function() {
			self.reloadIfStale();
		});

		this.buttonEl.on('dblclick', function() {
			self.reload();
		});

		DeskPRO_Window.getMessageBroker().addMessageListener('agent.ui.content_deleted.*', function() {
			self.reload();
		});

		DeskPRO_Window.getMessageBroker().addMessageListener('agent.ui.new-pending', function() {
			self.reload();
		});
	},

	modCommentCount: function(type, op, num) {
		if (!this.commentCountEls) {
			this.commentCountEls = {
				'all':       $('#publish_all_comments_count'),
				'articles':  $('#publish_articles_comments_count'),
				'news':      $('#publish_news_comments_count'),
				'downloads': $('#publish_downloads_comments_count')
			};
		}

		var el = this.commentCountEls[type];
		if (!el || !el[0]) {
			return;
		}

		DeskPRO_Window.util.modCountEl(el, op, num);
		DeskPRO_Window.util.modCountEl(this.commentCountEls.all, op, num);

		var elNum = parseInt(el.text());
		if (elNum != 0) {
			el.closest('li').show();
		} else {
			el.closest('li').hide();
		}

		var allNum = parseInt(this.commentCountEls.all.text());
		if (allNum) {
			$('#publish_section_comments').show();
		} else {
			$('#publish_section_comments').hide();
		}
	},

	reloadIfStale: function() {
		var now = new Date();
		if (!this.lastLoad || (now.getTime() - this.lastLoad.getTime() > 120000)) {
			this.reload();
			return true;
		}

		return false;
	},

	reload: function() {
		this.lastLoad = new Date();
		var expanded_ids = [];

		this.selected_id = null;

		if (this.contentEl && this.contentEl.length) {
			this.contentEl.find('.dp-collapsible-open').each(function() {
				var id = $(this).attr('id');
				if (id) {
					expanded_ids.push(id);
				}
			});

			this.contentEl.find('li.dp-list-open').each(function() {
				var id = this.id;
				if (id) {
					expanded_ids.push(id);
				}
			});

			var sel = this.contentEl.find('.nav-selected').first();
			if (sel[0] && sel.attr('id')) {
				this.selected_id = sel.attr('id');
			}
		}

		this.expanded_ids = expanded_ids;

		DeskPRO_Window.getSectionData('publish_section', (function(data) {
			this._initSection(data);

			if (this.sectionEl) {
				var scroller = this.sectionEl.find('.with-scroll-handler').data('scroll_handler');
				if (scroller) {
					scroller.updateSize();
				}
			}
		}).bind(this));
	},

	_initSection: function(data) {

		if(this.hasSectionInitialised) {
			this.contentEl.empty();
		} else {
			DeskPRO_Window.getMessageBroker().addMessageListener('publish.drafts.list-remove', function (info) {
				DeskPRO_Window.util.modCountEl('#publish_drafts_count', '-');
				self.modBadgeCount('-');
			});

			DeskPRO_Window.getMessageBroker().addMessageListener('publish.drafts.list-add', function (info) {
				DeskPRO_Window.util.modCountEl('#publish_drafts_count', '+');
				self.modBadgeCount('+');
			});
		}

		this.hasSectionInitialised = true;

		var self = this;
		this.setHasInitialLoaded();

		this.contentEl.html(data.section_html);

		if (this.selected_id) {
			$('#' + this.selected_id).addClass('nav-selected');
		}

		if (this.expanded_ids && this.expanded_ids.length) {
			Array.each(this.expanded_ids, function(id) {
				var el = $('#' + id);
				if (el.is('li')) {
					var $me = el.find('> i');
					var $li = el;
					var $groupList = el.find('> .nav-list-small');

					$me.removeClass('icon-caret-right');
					$me.addClass('icon-caret-down');
					$groupList.show();
					$li.addClass('dp-list-open');
				} else {
					el.addClass('dp-collapsible-open');
				}
			});
		}

		this._initSectionSearch();

		var self = this;

		this.contentEl.find('.pane-section').filter('.dp-collapsible').each(function() {
			var section = $(this);
			var header  = section.find('> header');
			var article = section.find('> article');

			header.css('cursor', 'pointer').on('click', function(ev) {
				Orb.cancelEvent(ev);
				section.toggleClass('dp-collapsible-open');
				self.updateUi();
			});
		});

		this.contentEl.find('.dp-toggle-icon').on('click', function(ev) {
			Orb.cancelEvent(ev);
			var $me    = $(this);
			var $li    = $me.closest('li');
			var $groupList = $li.find('> .nav-list-small');

			if ($me.hasClass('icon-caret-right')) {
				$me.removeClass('icon-caret-right');
				$me.addClass('icon-caret-down');
				$groupList.show();
				$li.addClass('dp-list-open');
			} else {
				$me.addClass('icon-caret-right');
				$me.removeClass('icon-caret-down');
				$groupList.hide();
				$li.removeClass('dp-list-open');
			}
		});

		this._initGlossary();
		this.recountBadge();

		this.fireEvent('sectionInit');
		this.updateUi();
	},

	_initSectionSearch: function() {
		var searchPane = this.contentEl.find('.source-pane-search');
		if (searchPane[0]) {
			this.searchForm = new DeskPRO.Agent.SourcePane.SearchForm(searchPane);
		}

		var catSelectTypes = searchPane.find('.cat-select-type');
		searchPane.find('.content_type').on('change', function() {
			catSelectTypes.hide();
			catSelectTypes.filter('.cat-select-'+$(this).val()).show();
		});
	},

	recountBadge: function() {
		var count = 0;
		count += parseInt($('#kb_pending_count').text().trim()) || 0;
		count += parseInt($('#publish_validating_count').text().trim()) || 0;
		count += parseInt($('#publish_validating_comments_count').text().trim()) || 0;
		this.updateBadge(count);
	},

	recountChildCounts: function(ul) {
		var self = this;
		$('> li', ul).each(function() {
			var li = $(this);
			var countEl = $('.list-counter:first', li);
			var count = parseInt(countEl.data('count'));
			var totalCount = count;

			var subUl = $('> ul', li);
			var subLis = null;
			if (subUl.length) {
				subLis = $('> li', subUl);
			}

			if (subLis && subLis.length) {
				self.recountChildCounts(subUl);

				subLis.each(function() {
					totalCount += parseInt($('.list-counter:first', this).data('total-count'));
				});

				countEl.text(count + '/' + totalCount);
			} else {
				countEl.text(count);
			}

			countEl.data('total-count', totalCount);
		});
	},

	//#########################################################################
	//# Glossary
	//#########################################################################

	_initGlossary: function() {

		this.glossaryWrapper = $('#publish_outline_glossary');

		var self = this;
		$('.glossary-new-trigger', this.glossaryWrapper).on('click', this.showGlossaryAddDlg.bind(this));
		$('.glossary-word-trigger', this.glossaryWrapper).on('click', function(ev) {
			ev.preventDefault();
			self.showGlossaryEditDlg($(this).data('word-id'));
		});
	},

	showGlossaryAddDlg: function() {
		var addDlg = this.getGlossaryAddDlg();
		addDlg.openOverlay();
	},

	showGlossaryEditDlg: function(id) {
		var editDlg = this.getGlossaryEditDlg();

		var form = $('.form', editDlg.elements.wrapper);
		var loading = $('.loading', editDlg.elements.wrapper);

		form.hide();
		loading.show();

		editDlg.openOverlay();

		$.ajax({
			url: BASE_URL + 'agent/glossary/' + id + '.json',
			type: 'GET',
			context: this,
			dataType: 'json',
			success: function(info) {
				form.find('input.word').select2('val', info.words);
				$('input.word_id', form).val(info.id);
				$('textarea.definition', form).val(info.definition);

				loading.hide();
				form.show();
			}
		});
	},

	getGlossaryAddDlg: function() {
		if (this.addDlg) return this.addDlg;

		var el = $('.glossary-add-dlg:first', this.glossaryWrapper);
		this.addDlg = new DeskPRO.UI.Overlay({
			contentElement: el,
			customClassname: 'normal-size',
			onBeforeOverlayOpened: function() {
				el.find('textarea.definition').val('');
				el.find('input.word').val('').select2('val', []);
			}
		});

		$('.save-trigger', el).on('click', this.saveNewWord.bind(this));

		DP.select(el.find('input.word'), {
			tags: [],
			id: function (e) { if (!e) return null; return e.id; },
			formatResult: function(result, container, query) {
				if (!result || !result.text) {
					return '';
				}
				return Orb.escapeHtml(result.text);
			},
			matcher: function(term, text) {
				if (typeOf(text)  != 'string' || typeOf(term) != 'string') {
					return;
				}

				return text.toUpperCase().indexOf(term.toUpperCase()) >= 0;
			}
		});

		return this.addDlg;
	},

	getGlossaryEditDlg: function() {
		if (this.editDlg) return this.editDlg;

		var el = $('.glossary-edit-dlg:first', this.glossaryWrapper);
		this.editDlg = new DeskPRO.UI.Overlay({
			contentElement: el,
			customClassname: 'normal-size'
		});

		DP.select(el.find('input.word'), {
			tags: [],
			id: function (e) { if (!e) return null; return e.id; },
			formatResult: function(result, container, query) {
				if (!result || !result.text) {
					return '';
				}
				return Orb.escapeHtml(result.text);
			},
			matcher: function(term, text) {
				if (typeOf(text)  != 'string' || typeOf(term) != 'string') {
					return;
				}

				return text.toUpperCase().indexOf(term.toUpperCase()) >= 0;
			}
		});

		$('.save-trigger', el).on('click', this.saveEditWord.bind(this));
		$('.delete-trigger', el).on('click', this.deleteEditWord.bind(this));

		return this.editDlg;
	},

	saveNewWord: function() {
		var data = [];

		var words = $('input.word', this.addDlg.elements.wrapperOuter).select2('val');
		for (var i = 0; i < words.length; i++) {
			data.push({
				name: 'words[]',
				value: words[i]
			});
		}
		data.push({
			name: 'definition',
			value: $('textarea.definition', this.addDlg.elements.wrapperOuter).val().trim()
		});

		$.ajax({
			url: BASE_URL + 'agent/glossary/new-word.json',
			type: 'POST',
			data: data,
			context: this,
			dataType: 'json',
			success: function(data) {
				this.addDlg.closeOverlay();
				this.reload();
			}
		});
	},

	saveEditWord: function() {

		var word_id = $('input.word_id', this.editDlg.elements.wrapperOuter).val().trim();

		var data = [];
		data.push({
			name: 'word_id',
			value: word_id
		});

		var words = $('input.word', this.editDlg.elements.wrapperOuter).select2('val');
		for (var i = 0; i < words.length; i++) {
			data.push({
				name: 'words[]',
				value: words[i]
			});
		}

		data.push({
			name: 'definition',
			value: $('textarea.definition', this.editDlg.elements.wrapperOuter).val().trim()
		});

		$.ajax({
			url: BASE_URL + 'agent/glossary/' + word_id + '/edit.json',
			type: 'POST',
			data: data,
			context: this,
			dataType: 'json',
			success: function(counts) {
				this.getGlossaryEditDlg().close();
				this.reload();
			}
		});
	},

	deleteEditWord: function() {

		var word_id = $('input.word_id', this.editDlg.elements.wrapperOuter).val().trim();

		$.ajax({
			url: BASE_URL + 'agent/glossary/' + word_id + '/delete.json',
			type: 'POST',
			context: this,
			dataType: 'json',
			success: function(counts) {
				this.getGlossaryEditDlg().close();
				this.reload();
			}
		});
	}
});
