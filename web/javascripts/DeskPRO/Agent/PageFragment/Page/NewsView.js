Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');
DeskPRO.Agent.PageFragment.Page.NewsView = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'news';
	},

	initMetaData: function() {
		DeskPRO_Window.recentTabs.add(
			'news',
			this.meta.news_id,
			this.meta.title,
			BASE_URL + 'agent/news/post/' + this.meta.news_id
		);
	},

	initPage: function(el) {

		var self = this;
		this.wrapper = el;

		this.news_id = this.getMetaData('news_id');

		this._initBasic();

		if (this.meta.canEdit) {
			this._initMenus();
			this._initPostArea();
		}
		this._initActions();
		this._initLabels();

		this._initCommentForm();

		if (this.meta.canEdit) {
			if (this.meta.isValidating) {
				this.validatingEdit = new DeskPRO.Agent.PageHelper.ValidatingEdit(this, {
					typename: 'news',
					contentId: this.meta.news_id
				});
				this.ownObject(this.validatingEdit);
			}

			this.getEl('edit_btn').on('click', this.showEditor.bind(this));
		}

		this.relatedContent = new DeskPRO.Agent.PageHelper.RelatedContent(this, {
			typename: 'news',
			content_id: this.meta.news_id,
			listEl: $('section.linked-content:first', this.wrapper),
			disabled: !this.meta.canEdit,
			onContentLinked: function(typename, content_id) {
				$.ajax({
					url: BASE_URL + 'agent/news/post/' + self.meta.news_id + '/ajax-save',
					type: 'POST',
					data: { content_type: typename, content_id: content_id, action: 'add-related' },
					context: this,
					dataType: 'json'
				});
			},
			onContentUnlinked: function(typename, content_id) {
				$.ajax({
					url: BASE_URL + 'agent/news/post/' + self.meta.news_id + '/ajax-save',
					type: 'POST',
					data: { content_type: typename, content_id: content_id, action: 'add-related' },
					context: this,
					dataType: 'json'
				});
			}
		});
		this.ownObject(this.relatedContent);

		this.miscContent = new DeskPRO.Agent.PageHelper.MiscContent(this, {
			revisionCompareUrl: BASE_URL + 'agent/news/compare-revs/{OLD}/{NEW}'
		});
		this.ownObject(this.miscContent);

		this.whoVotedOverlay = new DeskPRO.UI.Overlay({
			triggerElement: '.who-voted-trigger',
			contentMethod: 'ajax',
			contentAjax: {
				url: BASE_URL + 'agent/publish/rating-who-voted/news/' + this.meta.news_id
			}
		});
		this.ownObject(this.whoVotedOverlay);
	},

	destroyPage: function() {
		// Workaround for tinymce bug to do with remove()
		// We'll manually remove the node ourselves
		var el = $('.news-editor-wrap', this.getEl('content_ed'));
		if (el[0]) {
			el.get(0).parentNode.removeChild(el.get(0));
		}
	},

	handleUnloadRevisions: function(revision_id) {
		if (!revision_id) {
			return;
		}

		if ($('.rev-' + revision_id, this.getEl('revs')).length) {
			return;
		}

		this.getEl('revs').empty().removeClass('loaded');
		DeskPRO_Window.util.modCountEl(this.getEl('count_revs'), '+');
	},

	//#################################################################
	//# Basic
	//#################################################################

	_initBasic: function() {
		var self = this;

		// Tabs
		this.bodyTabs = new DeskPRO.UI.SimpleTabs({
			triggerElements: $('li', this.getEl('bodytabs')),
			onTabSwitch: (function(info) {
				if ($(info.tabContent).is('.news-content')) {
					self.getEl('content_edit_btns').show();
				} else {
					self.getEl('content_edit_btns').hide();
				}

				if ($(info.tabContent).is('.related-content-tab')) {
					$('body').addClass('related-controls-on');
				} else {
					if ($(info.tabContent).is('.search-tab')) {
						self._initSearchTab();
					}
					$('body').removeClass('related-controls-on');
				}
				if ($(info.tabContent).is('.revisions-tab') && !$(info.tabContent).is('.loaded')) {
					$.ajax({
						url: BASE_URL + 'agent/news/post/' + this.meta.news_id + '/view-revisions',
						type: 'GET',
						dataType: 'html',
						context: self,
						success: function(html) {
							this.getEl('revs').html(html);
							this.miscContent._initCompareRevs();
							$(info.tabContent).addClass('loaded');
						}
					});
				}
			}).bind(this)
		});
		this.ownObject(this.bodyTabs);

		if (this.meta.canEdit) {
			var editTitle = new DeskPRO.Agent.PageFragment.Page.EditTitle(
				this,
				BASE_URL + 'agent/news/post/' + this.meta.news_id + '/ajax-save'
			);
		}
	},


	//#################################################################
	//# Menus
	//#################################################################

	_initMenus: function() {

		var self = this;

		var statusSel = this.getEl('status');
		DP.select(statusSel);

		statusSel.on('change', function() {
			var status = $(this).val();

			self.getEl('auto_unpub').hide();
			self.getEl('auto_pub').hide();

			if (status == 'published') {
				self.getEl('auto_unpub').show();
			} else if (status == 'hidden.unpublished') {
				self.getEl('auto_pub').show();
			}

			$.ajax({
				url: BASE_URL + 'agent/news/post/' + self.meta.news_id + '/ajax-save',
				type: 'POST',
				data: {action: 'status', status: status},
				context: self,
				dataType: 'json',
				success: function() {
					DeskPRO_Window.sections.publish_section.reload();
				}
			});

		});

		this.deleteHelper = new DeskPRO.Agent.PageFragment.Page.Content.DeleteControl(this, {
			ajaxSaveUrl: BASE_URL + 'agent/news/post/' + self.meta.news_id + '/ajax-save',
			statusMenu: this.statusMenu
		});
		this.ownObject(this.deleteHelper);

		var catSel = this.getEl('cat');
		DP.select(catSel);

		catSel.on('change', function() {
			$.ajax({
				url: BASE_URL + 'agent/news/post/' + self.meta.news_id + '/ajax-save',
				type: 'POST',
				data: { action: 'category', category_id: $(this).val() },
				dataType: 'json',
				success: function() {
					DeskPRO_Window.sections.publish_section.reload();
				}
			});
		});
	},

	_initActions: function() {
		var self = this;
		var actions = this.getEl('action_buttons');

		$('.delete', actions).on('click', function() {

		});

		$('.permalink', actions).on('click', function() {
			var html = [];
			html.push('<div>');
			html.push('The permalink to this post on the website is:<br />');
			html.push('<input type="text" style="width:80%;" />');
			html.push('</div>');

			var msg = $(html.join(''));
			$('input', msg).val(self.meta.permalink);

			DeskPRO_Window.showAlert(msg);
		});

		$('.view-user-interface', actions).on('click', function() {
			window.open(self.meta.permalink);
		});
	},


	//#################################################################
	//# Labels
	//#################################################################

	_initLabels: function() {
		this.labelsInput = new DeskPRO.UI.LabelsInput({
			type: 'news',
			input: this.getEl('labels_input'),
			onChange: this.saveLabels.bind(this)
		});
		this.ownObject(this.labelsInput);
	},

	saveLabels: function() {
		if (this._saveLabelsTimeout) {
			window.clearTimeout(this._saveLabelsTimeout);
		}

		this._labelsData = this.labelsInput.getFormData();
		this._saveLabelsTimeout = this._doSaveLabels.delay(2000, this);
	},

	_doSaveLabels: function() {
		var data = this._labelsData;

		$.ajax({
			url: this.getMetaData('labelsSaveUrl'),
			type: 'POST',
			context: this,
			data: data,
			dataType: 'json',
			success: function(data) {
				this._handleSaveLabelsSuccess(data);
			}
		});
	},

	_handleSaveLabelsSuccess: function(data) {

	},

	_initSearchTab: function() {
		if (this.hasInitSearchTab) return;
		this.hasInitSearchTab = true;

		this.stickyWords = new DeskPRO.Agent.PageFragment.Page.Content.StickyWords(this, {
			contentType: 'news',
			contentId: this.meta.news_id,
			element: this.getEl('stickysearch_input')
		});
		this.ownObject(this.stickyWords);
	},

	//#################################################################
	//# Comments
	//#################################################################

	_initCommentForm: function() {
		this.commentsController = new DeskPRO.Agent.PageHelper.Comments(this, {
			commentsWrapper: this.getEl('comments_wrap')
		});
		this.ownObject(this.commentsController);

		this.newCommentWrapper = $('.new-note:first', this.wrapper);
		$('button', this.newCommentWrapper).on('click', this.saveNewComment.bind(this));
	},

	saveNewComment: function() {

		var loadingOn = $('.loading-on', this.newCommentWrapper).show();
		var loadingOff = $('.loading-off', this.newCommentWrapper).hide();

		var data = [];
		data.push({
			name: 'content',
			value: $('textarea', this.newCommentWrapper).val()
		});

		$.ajax({
			url: BASE_URL + 'agent/news/post/' + this.getMetaData('news_id') + '/ajax-save-comment',
			type: 'POST',
			context: this,
			data: data,
			dataType: 'html',
			success: function(html) {
				loadingOn.hide();
				loadingOff.show();

				$('textarea', this.newCommentWrapper).val('');
				var el = $(html);
				this.newCommentWrapper.before(el);

				DeskPRO_Window.util.modCountEl(this.getEl('count_comments'), '+');

				if (DeskPRO_Window.sections.publish_section) {
					DeskPRO_Window.sections.publish_section.modCommentCount('news', '+');
				}
			}
		});
	},

	//#################################################################
	//# Editor
	//#################################################################

	_initPostArea: function() {
		this._hasInitEd = false;
		this.getEl('cancel_btn').off('click').on('click', (function() {
			this.hideEditor();

			// Cancel the edit field too, set it back to what it was
			if (!this.wrapper.find('.revert-default')[0]) {
				var def = this.wrapper.find('textarea.edit-content-field-default').val();
				this.wrapper.find('textarea.edit-content-field').val(def);
				if (this.rte) {
					this.rte.val(def);
				}
			}
		}).bind(this));

		var wrap = this.wrapper;

		if (this.editStateSaver) {
			this.editStateSaver.destroy();
		}

		this.editStateSaver = new DeskPRO.Agent.PageHelper.StateSaver({
			stateId: 'editarticle',
			listenOn: $('.news-editor-wrap:first', wrap)
		});
		this.ownObject(this.editStateSaver);

		this.getEl('save_btn').off('click').on('click', (function(ev) {
			ev.preventDefault();

			var data = {
				action: 'content',
				content: $('.news-editor-wrap textarea:first', wrap).val(),
				attach: $('.news-editor-wrap .edit-content-attach:first', wrap).val()
			};

			$.ajax({
				url: BASE_URL + 'agent/news/post/' + this.meta.news_id + '/ajax-save',
				type: 'POST',
				context: this,
				data: data,
				dataType: 'json',
				success: function(data) {
					this.getEl('content_ed').html(data.content_html);
					this.handleUnloadRevisions(data.revision_id);
					this._initPostArea();
				}
			});

		}).bind(this));

		this.hideEditor();
	},

	showEditor: function() {

		var self = this;

		var edWrap = $('.news-editor-wrap', this.getEl('content_ed')).show();
		$('.revert-default', edWrap).on('click', function() {
			var def = $('textarea.edit-content-field-default').val();
			$('textarea.edit-content-field').val(def);

			$('.revert-message-notice', edWrap).remove();
		});

		$('.news-content-wrap', this.getEl('content_ed')).hide();
		$('.news-editor-wrap', this.getEl('content_ed')).show();

		if (!this._hasInitEd) {
			this._hasInitEd = true;

			var txt = $('.edit-content-field', this.getEl('content_ed'));
			var w = $(txt.closest('.content-tab-item')).width() - 30;

			// Means the whole thign is visible at once, lets try and max out the viewport
			if (this.wrapper.find('> .layout-content > .scrollbar.disabled')) {
				var h = $(window).height() - 90 - txt.offset().top;
			} else {
				h = 425;
			}

			txt.css({ width: w, height: h });

			DP.rteTextarea($('.edit-content-field', this.getEl('content_ed')), {
				setup: function(ed) {
					ed.onKeyPress.add(function() {
						self.editStateSaver.triggerChange();
					});
				}
			});
		}

		this.getEl('edit_btn').hide();
		this.getEl('save_btn').show();
		this.getEl('cancel_btn').show();
		this.updateUi();
	},

	hideEditor: function() {
		this.getEl('edit_btn').show();
		this.getEl('save_btn').hide();
		this.getEl('cancel_btn').hide();
		$('.news-editor-wrap', this.getEl('content_ed')).hide();
		$('.news-content-wrap', this.getEl('content_ed')).show();
		this.updateUi();
	},

	_initMediaBrowser: function() {
		if (this.mediabrowser_has_init) return;
		this.mediabrowser_has_init = true;

		this.mediaBrowserEl = $('.media-browser', this.wrapper);
		this.mediaBrowserOverlay = new DeskPRO.UI.Overlay({
			contentElement: this.mediaBrowserEl
		});

		this.mediaBrowser = new DeskPRO.Agent.MediaBrowser({
			wrapper: this.mediaBrowserEl,
			additionalDropZone: $('.kb-editor > textarea', this.wrapper)
		});
	},

	showMediaBrowser: function() {
		this._initMediaBrowser();
		this.mediaBrowserOverlay.openOverlay();
	},

	//#################################################################
	//# Compare revisions
	//#################################################################

	_initCompareRevs: function() {
		$('.compare-trigger', this.wrapper).on('click', this.showCompareRev.bind(this));
	},

	showCompareRev: function() {
		var old_id = $('.reivisons input.old:checked', this.wrapper).val();
		var new_id = $('.revisions input.new:checked', this.wrapper).val();

		if (!old_id || !new_id) {
			return;
		}

		var overlay = new DeskPRO.UI.Overlay({
			triggerElement: $('button.compare-trigger', this.wrapper),
			contentMethod: 'ajax',
			contentAjax: {
				url: BASE_URL + 'agent/news/compare-revs/' + old_id + '/' + new_id
			},
			destroyOnClose: true
		});

		overlay.openOverlay();
	}
});
