Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');
DeskPRO.Agent.PageFragment.Page.FeedbackView = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'feedback';
	},

	initPage: function(el) {

		var self = this;
		this.wrapper = el;

		this.feedback_id = this.getMetaData('feedback_id');

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
					typename: 'feedback',
					contentId: this.feedback_id,
					singleTyle: 'feedback'
				});
				this.ownObject(this.validatingEdit);
			}

			this.getEl('edit_btn').on('click', this.showEditor.bind(this));
		}

		this.relatedContent = new DeskPRO.Agent.PageHelper.RelatedContent(this, {
			typename: 'feedback',
			content_id: this.feedback_id,
			listEl: $('section.linked-content:first', this.wrapper),
			disabled: !this.meta.canEdit,
			onContentLinked: function(typename, content_id) {
				$.ajax({
					url: BASE_URL + 'agent/feedback/view/' + self.feedback_id + '/ajax-save',
					type: 'POST',
					data: { content_type: typename, content_id: content_id, action: 'add-related' },
					context: this,
					dataType: 'json'
				});
			},
			onContentUnlinked: function(typename, content_id) {
				$.ajax({
					url: BASE_URL + 'agent/feedback/view/' + self.feedback_id + '/ajax-save',
					type: 'POST',
					data: { content_type: typename, content_id: content_id, action: 'remove-related' },
					context: this,
					dataType: 'json'
				});
			}
		});
		this.ownObject(this.relatedContent);

		DeskPRO_Window.getMessageBroker().addMessageListener('publish.validating.list-remove', function (info) {
			$('article.' + info.typename + '-' + info.contentId).slideUp();
		});

		this.miscContent = new DeskPRO.Agent.PageHelper.MiscContent(this, {
			revisionCompareUrl: BASE_URL + 'agent/feedback/compare-revs/{OLD}/{NEW}'
		});
		this.ownObject(this.miscContent);

		this.whoVotedOverlay = new DeskPRO.UI.Overlay({
			triggerElement: '.who-voted-trigger',
			contentMethod: 'ajax',
			contentAjax: {
				url: BASE_URL + 'agent/publish/rating-who-voted/feedback/' + this.feedback_id
			}
		});
		this.ownObject(this.whoVotedOverlay);

		this.getEl('my_vote').on('click', function() {
			self.toggleMyVote();
		});


		var fieldsRendered = this.getEl('custom_fields_rendered');
		var fieldsForm = this.getEl('custom_fields_editable');

		var buttonsWrap = this.getEl('properties_controls');
		var propToggle = function(what) {
			if (what == 'display') {
				$('.showing-editing-fields', buttonsWrap).hide();
				$('.showing-rendered-fields', buttonsWrap).show();
				fieldsForm.hide();
				fieldsRendered.show();
			} else {
				$('.showing-rendered-fields', buttonsWrap).hide();
				$('.showing-editing-fields', buttonsWrap).show();
				fieldsRendered.hide();
				fieldsForm.show();
			}
		};

		$('.edit-fields-trigger', buttonsWrap).on('click', function() {
			propToggle('edit');
		});

		$('.save-fields-trigger', buttonsWrap).on('click', function() {
			var formData = $('input[type="text"], input[type="password"], input:checked, select, textarea', fieldsForm);

			$.ajax({
				url: BASE_URL + 'agent/feedback/view/' + self.meta.feedback_id + '/ajax-save-custom-fields',
				type: 'POST',
				data: formData,
				dataType: 'html',
				success: function(rendered) {
					fieldsRendered.empty().html(rendered);
					propToggle('display');
				}
			});
		});

		var namef       = this.getEl('showname');
		var editName    = this.getEl('editname');
		var startBtn    = this.getEl('editname_start');
		var stopBtn     = this.getEl('editname_end');

		var startEditable = function() {
			namef.hide();
			editName.show();
			startBtn.hide();
			stopBtn.show();
		};

		var stopEditable = function() {
			var nametxt = editName.find('input').first();
			var setName = nametxt.val().trim();

			if(!setName) {
				return;
			}

			editName.hide();
			startBtn.show();
			namef.show();
			stopBtn.hide();
			namef.text(setName);

			var postData = [];
			postData.push({
				name: 'action',
				value: 'title'
			});
			postData.push({
				name: 'title',
				value: setName
			});

			$.ajax({
				url: BASE_URL + 'agent/feedback/view/' + self.feedback_id + '/ajax-save',
				type: 'POST',
				data: postData,
				success: function(data) {
					self.handleUnloadRevisions(data.revision_id);
				}
			});
		};

		namef.on('dblclick', startEditable).on('keypress', function(ev) {
			if (ev.keyCode == 13 /* enter key */) {
				ev.preventDefault();
				stopEditable();
			}
		});
		this.getEl('editname_start').on('click', startEditable);
		this.getEl('editname_end').on('click', stopEditable);

		if (this.meta.canEdit) {
			var self = this;
			this.getEl('status').on('change', function() {
				var catId = $(this).val();
				$.ajax({
					url: BASE_URL + 'agent/feedback/view/' + self.feedback_id + '/ajax-save',
					type: 'POST',
					data: {action: 'status', status: catId},
					context: self,
					dataType: 'json',
					success: function() {
						DeskPRO_Window.getMessageBroker().sendMessage('agent.ui.feedback-status-update', {
							feedback_id: self.feedback_id,
							new_status: catId
						});
					}
				});
			});
		} else {
			this.getEl('status').prop('disabled', true);
		}

		DP.select(this.getEl('status'));

		this.deleteHelper = new DeskPRO.Agent.PageFragment.Page.Content.DeleteControl(this, {
			ajaxSaveUrl: BASE_URL + 'agent/feedback/view/' + self.feedback_id + '/ajax-save'
		});
		this.deleteHelper.undeleteBtn.on('click', function() {
			self.getEl('status').find('option').first().prop('selected', true).trigger('change');
			self.deleteHelper.handleUndelete();
		});
		this.ownObject(this.deleteHelper);
	},

	destroyPage: function() {
		// Workaround for tinymce bug to do with remove()
		// We'll manually remove the node ourselves
		var el = $('.feedback-editor-wrap', this.getEl('content_ed'));
		if (el && el.parentNode) {
			el.parentNode.removeChild(el);
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
			triggerElements: $('li.tab-trigger', this.getEl('bodytabs')),
			onTabSwitch: (function(info) {
				if ($(info.tabContent).is('.content-tab')) {
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
				if ($(info.tabContent).is('.feedback-revs') && !$(info.tabContent).is('.loaded')) {
					$.ajax({
						url: BASE_URL + 'agent/feedback/view/' + this.feedback_id + '/view-revisions',
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
	},

	toggleMyVote: function() {
		var action;
		if (this.getEl('my_vote').is('.radio-on')) {
			action = 'clear-vote';
			DeskPRO_Window.util.modCountEl(this.getEl('num_votes'), '-');
			this.getEl('my_vote').removeClass('radio-on');
		} else {
			action = 'vote';
			DeskPRO_Window.util.modCountEl(this.getEl('num_votes'), '+');
			this.getEl('my_vote').addClass('radio-on');
		}

		$.ajax({
			url: BASE_URL + 'agent/feedback/view/' + this.feedback_id + '/ajax-save',
			type: 'POST',
			data: {action: action},
			context: this,
			dataType: 'json'
		});
	},


	//#################################################################
	//# Menus
	//#################################################################

	_initMenus: function() {

		var self = this;

		if (this.meta.canEdit) {
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
						url: BASE_URL + 'agent/feedback/view/' + self.feedback_id + '/ajax-save',
						type: 'POST',
						data: { action: 'category', category_id: catId },
						dataType: 'json'
					});
				}
			});
		}
	},

	_initActions: function() {
		var self = this;
		var actions = this.getEl('action_buttons');

		$('.permalink', actions).on('click', function() {
			var html = [];
			html.push('<div>');
			html.push('The permalink to this feedback on the website is:<br />');
			html.push('<input type="text" style="width:95%;" />');
			html.push('</div>');

			var msg = $(html.join(''));
			$('input', msg).val(self.meta.permalink);

			DeskPRO_Window.showAlert(msg);
		});

		$('.view-user-interface', actions).on('click', function() {
			window.open(self.meta.permalink);
		});

		this.merge = new DeskPRO.Agent.Widget.Merge({
			tabType: 'feedback',
			metaId: self.meta.feedback_id,
			metaIdName: 'feedback_id',
			menu: this.getEl('merge_menu'),
			trigger: $('.merge', this.getEl('action_buttons')),
			overlayUrl: BASE_URL + 'agent/feedback/merge-overlay/{id}/{other}',
			mergeUrl: BASE_URL + 'agent/feedback/merge/{id}/{other}',
			loadRoute: 'feedback:' + BASE_URL + 'agent/feedback/view/{id}'
		});
		this.ownObject(this.merge);
	},


	//#################################################################
	//# Labels
	//#################################################################

	_initLabels: function() {
		this.labelsInput = new DeskPRO.UI.LabelsInput({
			type: 'feedback',
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
			contentType: 'feedback',
			contentId: this.meta.feedback_id,
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

		if (this.getEl('agent_comment_ck').is(':checked')) {
			data.push({
				name: 'agent_only',
				value: 1
			});
		}

		$.ajax({
			url: BASE_URL + 'agent/feedback/view/' + this.getMetaData('feedback_id') + '/ajax-save-comment',
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

				// Inc note count
				DeskPRO_Window.util.modCountEl(this.getEl('count_comments'), '+');
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
			stateId: 'editfeedback',
			listenOn: $('.feedback-editor-wrap:first', wrap)
		});
		this.ownObject(this.editStateSaver);

		this.getEl('save_btn').off('click').on('click', (function(ev) {
			ev.preventDefault();

			var data = {
				action: 'content',
				content: $('.feedback-editor-wrap textarea:first', wrap).val(),
				attach: $('.feedback-editor-wrap .edit-content-attach:first', wrap).val()
			};

			$.ajax({
				url: BASE_URL + 'agent/feedback/view/' + this.feedback_id + '/ajax-save',
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

		var edWrap = $('.feedback-editor-wrap', this.getEl('content_ed')).show();
		$('.revert-default', edWrap).on('click', function() {
			var def = $('textarea.edit-content-field-default').val();
			$('textarea.edit-content-field').val(def);

			$('.revert-message-notice', edWrap).remove();
		});

		$('.feedback-content-wrap', this.getEl('content_ed')).hide();
		$('.feedback-editor-wrap', this.getEl('content_ed')).show();

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

			// Attachments
			var list = $('.file-list', this.wrapper);

			DeskPRO_Window.util.fileupload(this.wrapper, {
				url: BASE_URL + 'agent/misc/accept-upload?attach_to_object=feedback&object_id=' + this.meta.feedback_id,
				page: this
			});

			list.on('click', '.delete', function(ev) {
				ev.preventDefault();
				ev.stopImmediatePropagation();

				var blob_id = $(this).data('blob-id');
				$.ajax({
					url: BASE_URL + 'agent/feedback/view/' + self.meta.feedback_id + '/ajax-save',
					type: 'POST',
					data: {action: 'remove-blob', blob_id: blob_id},
					context: self,
					dataType: 'json'
				});

				$(this).parent().fadeOut();
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
		$('.feedback-editor-wrap', this.getEl('content_ed')).hide();
		$('.feedback-content-wrap', this.getEl('content_ed')).show();
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
		var old_id = $('.feedback-revs input.old:checked', this.wrapper).val();
		var new_id = $('.feedback-revs input.new:checked', this.wrapper).val();

		if (!old_id || !new_id) {
			return;
		}

		var overlay = new DeskPRO.UI.Overlay({
			triggerElement: $('button.compare-trigger', this.wrapper),
			contentMethod: 'ajax',
			contentAjax: {
				url: BASE_URL + 'agent/feedback/compare-revs/' + old_id + '/' + new_id
			},
			destroyOnClose: true
		});

		overlay.openOverlay();
	}
});
