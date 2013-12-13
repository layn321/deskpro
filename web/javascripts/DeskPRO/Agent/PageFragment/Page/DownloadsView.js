Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');
DeskPRO.Agent.PageFragment.Page.DownloadsView = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'download';
	},

	initMetaData: function() {
		DeskPRO_Window.recentTabs.add(
			'download',
			this.meta.download_id,
			this.meta.title,
			BASE_URL + 'agent/downloads/file/' + this.meta.download_id
		);
	},

	initPage: function(el) {

		var self = this;
		this.wrapper = el;

		this.download_id = this.getMetaData('download_id');

		this._initBasic();
		this._initLabels();
		this._initCommentForm();
		this._initActions();

		if (this.meta.canEdit) {
			this._initPostArea();

			if (this.meta.isValidating) {
				this.validatingEdit = new DeskPRO.Agent.PageHelper.ValidatingEdit(this, {
					typename: 'downloads',
					contentId: this.meta.download_id
				});
				this.ownObject(this.validatingEdit);
			}

			this.getEl('edit_btn').on('click', this.showEditor.bind(this));
		}

        $('time.timeago', this.wrapper).timeago();

		this.relatedContent = new DeskPRO.Agent.PageHelper.RelatedContent(this, {
			typename: 'downloads',
			content_id: this.meta.download_id,
			listEl: $('section.linked-content:first', this.wrapper),
			disabled: !this.meta.canEdit,
			onContentLinked: function(typename, content_id) {
				$.ajax({
					url: BASE_URL + 'agent/downloads/file/' + self.meta.download_id + '/ajax-save',
					type: 'POST',
					data: { content_type: typename, content_id: content_id, action: 'add-related' },
					context: this,
					dataType: 'json'
				});
			},
			onContentUnlinked: function(typename, content_id) {
				$.ajax({
					url: BASE_URL + 'agent/downloads/file/' + self.meta.download_id + '/ajax-save',
					type: 'POST',
					data: { content_type: typename, content_id: content_id, action: 'add-related' },
					context: this,
					dataType: 'json'
				});
			}
		});
		this.ownObject(this.relatedContent);

		this.miscContent = new DeskPRO.Agent.PageHelper.MiscContent(this, {
			revisionCompareUrl: BASE_URL + 'agent/downloads/compare-revs/{OLD}/{NEW}'
		});
		this.ownObject(this.miscContent);

		this.whoVotedOverlay = new DeskPRO.UI.Overlay({
			triggerElement: '.who-voted-trigger',
			contentMethod: 'ajax',
			contentAjax: {
				url: BASE_URL + 'agent/publish/rating-who-voted/download/' + this.meta.download_id
			}
		});
		this.ownObject(this.whoVotedOverlay);

		this.whoViewedOverlay = new DeskPRO.UI.Overlay({
			triggerElement: '.open-who-viewed',
			contentMethod: 'ajax',
			contentAjax: {
				url: BASE_URL + 'agent/publish/who-viewed/2/' + this.meta.download_id
			}
		});
		this.ownObject(this.whoViewedOverlay);

		this.whoDownloadedOverlay = new DeskPRO.UI.Overlay({
			triggerElement: '.open-who-downloaded',
			contentMethod: 'ajax',
			contentAjax: {
				url: BASE_URL + 'agent/publish/who-viewed/2/' + this.meta.download_id + '/2'
			}
		});
		this.ownObject(this.whoViewedOverlay);

		var editBtn    = this.getEl('editfile_controls').find('.edit-trigger');
		var cancelBtn  = this.getEl('editfile_controls').find('.cancel-trigger');
		var saveBtn    = this.getEl('editfile_controls').find('.save-trigger');
		var loadingBtn = this.getEl('editfile_controls').find('.is-loading');

		var editArea = this.getEl('editfile');
		var showArea = this.getEl('showfile');

		editBtn.on('click', function(ev) {
			ev.preventDefault();
			editBtn.hide();
			cancelBtn.show();
			saveBtn.show();

			showArea.hide();
			editArea.show();

			editArea.find('.file-list').empty();
		});

		saveBtn.on('click', function(ev) {
			ev.preventDefault();
			editBtn.hide();
			cancelBtn.hide();
			saveBtn.hide();

			var formData = editArea.find('input, select, textarea').serializeArray();
			formData.push({
				name: 'action',
				value: 'file'
			});

			loadingBtn.show();
			$.ajax({
				url: BASE_URL + 'agent/downloads/file/' + self.meta.download_id + '/ajax-save',
				data: formData,
				error: function() {
					loadingBtn.hide();
					cancelBtn.show();
					saveBtn.show();
				},
				success: function(data) {
					loadingBtn.hide();
					cancelBtn.hide();
					saveBtn.hide();
					editBtn.show();

					self.handleUnloadRevisions(data.revision_id);
					editArea.hide();
					showArea.empty().html(data.file_html).show();
				}
			})
		});

		cancelBtn.on('click', function(ev) {
			ev.preventDefault();
			editBtn.show();
			cancelBtn.hide();
			saveBtn.hide();

			editArea.hide();
			showArea.show();
		});

		var list = $('.file-list', editArea);
		if (list[0]) {
			DeskPRO_Window.util.fileupload(editArea, { page: this });
			this.wrapper.bind('fileuploadadd', function() {
				$('ul.file-list', editArea).empty();
			});
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

	destroyPage: function() {
		// Workaround for tinymce bug to do with remove()
		// We'll manually remove the node ourselves
		var el = $('.download-editor-wrap', this.getEl('content_ed'));
		if (el[0]) {
			el.get(0).parentNode.removeChild(el.get(0));
		}
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
				if ($(info.tabContent).is('.dl-content')) {
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
					$('body').removeClass('related-controls-on');$('body').addClass('related-controls-off');
				}
				if ($(info.tabContent).is('.revisions-tab') && !$(info.tabContent).is('.loaded')) {
					$.ajax({
						url: BASE_URL + 'agent/downloads/file/' + this.meta.download_id + '/view-revisions',
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
			// Name is editable
			var name = $('h3.title.editable:first', this.wrapper);
			if (!name.attr('id')) {
				name.attr('id', Orb.getUniqueId());
			}

			var editable = new DeskPRO.Form.InlineEdit({
				baseElement: this.wrapper,
				ajax: {
					url: BASE_URL + 'agent/downloads/file/' + this.meta.download_id + '/ajax-save',
					success: function(data) {
						self.handleUnloadRevisions(data.revision_id);
					}
				}
			});

			// Change category menu
			var catOb = new DeskPRO.UI.OptionBoxRevertable({
				trigger: this.getEl('cat_trigger'),
				element: this.getEl('cat_ob'),
				onSave: function(ob) {
					var catEl = ob.getSelectedElements('category');
					var catId = catEl.data('item-id');
					var title = catEl.data('full-title');

					self.getEl('cat_label').text(title);

					$.ajax({
						url: BASE_URL + 'agent/downloads/file/' + self.meta.download_id + '/ajax-save',
						type: 'POST',
						data: { action: 'category', category_id: catId },
						dataType: 'json',
						success: function() {
							DeskPRO_Window.sections.publish_section.reload();
						}
					});
				}
			});

			// Status
			var trigger = $('.the-status:first', this.wrapper);
			this.statusMenu = new DeskPRO.UI.Menu({
				triggerElement: trigger,
				menuElement: $('.status-menu:first', this.wrapper),
				onItemClicked: function(info) {
					var status = $(info.itemEl).data('option-value');

					$('.download-status', trigger).attr('title', status);
					$('.download-status span', trigger).attr('class', '').addClass('ticket-' + status.replace(/\./, '_'));

					$.ajax({
						url: BASE_URL + 'agent/downloads/file/' + self.meta.download_id + '/ajax-save',
						type: 'POST',
						data: {action: 'status', status: status},
						context: self,
						dataType: 'json',
						success: function() {
							DeskPRO_Window.sections.publish_section.reload();
						}
					});
				}
			});
			this.ownObject(this.statusMenu);

			this.deleteHelper = new DeskPRO.Agent.PageFragment.Page.Content.DeleteControl(this, {
				ajaxSaveUrl: BASE_URL + 'agent/downloads/file/' + self.meta.download_id + '/ajax-save',
				statusMenu: this.statusMenu
			});
			this.ownObject(this.deleteHelper);
		}
	},

	//#################################################################
	//# Actions menus
	//#################################################################

	_initActions: function() {
		var self = this;
		var actions = this.getEl('action_buttons');

		$('.delete', actions).on('click', function() {

		});

		$('.permalink', actions).on('click', function() {
			var html = [];
			html.push('<div>');
			html.push('The permalink to this download on the website is:<br />');
			html.push('<input type="text" style="width:95%" />');
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
			type: 'downloads',
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
			contentType: 'download',
			contentId: this.meta.download_id,
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
			url: BASE_URL + 'agent/downloads/file/' + this.getMetaData('download_id') + '/ajax-save-comment',
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
					DeskPRO_Window.sections.publish_section.modCommentCount('downloads', '+');
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
			stateId: 'editdownload',
			listenOn: $('.download-editor-wrap:first', wrap)
		});
		this.ownObject(this.editStateSaver);

		this.getEl('save_btn').off('click').on('click', (function(ev) {
			ev.preventDefault();

			var data = {
				action: 'content',
				content: $('.download-editor-wrap textarea:first', wrap).val(),
				attach: $('.download-editor-wrap .edit-content-attach:first', wrap).val()
			};

			$.ajax({
				url: BASE_URL + 'agent/downloads/file/' + this.meta.download_id + '/ajax-save',
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

		var edWrap = $('.download-editor-wrap', this.getEl('content_ed')).show();
		$('.revert-default', edWrap).on('click', function() {
			var def = $('textarea.edit-content-field-default').val();
			$('textarea.edit-content-field').val(def);

			$('.revert-message-notice', edWrap).remove();
		});

		$('.download-content-wrap', this.getEl('content_ed')).hide();
		$('.download-editor-wrap', this.getEl('content_ed')).show();

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

			DP.rteTextarea(txt, {
				setup: function(ed) {
					ed.onKeyPress.add(function() {
						self.editStateSaver.triggerChange();
					});
				}
			});

			this._hasInitEdBefore = true;
		}

		this.getEl('edit_btn').hide();
		this.getEl('save_btn').show();
		this.getEl('cancel_btn').show();
		this.updateUi();
	},

	hideEditor: function() {
		$('.download-editor-wrap', this.getEl('content_ed')).hide();
		$('.download-content-wrap', this.getEl('content_ed')).show();
		this.getEl('edit_btn').show();
		this.getEl('save_btn').hide();
		this.getEl('cancel_btn').hide();
		this.updateUi();
	}
});
