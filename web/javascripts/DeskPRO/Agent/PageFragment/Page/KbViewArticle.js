Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');
DeskPRO.Agent.PageFragment.Page.KbViewArticle = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'article';
	},

	initMetaData: function() {
		DeskPRO_Window.recentTabs.add(
			'article',
			this.meta.article_id,
			this.meta.title,
			BASE_URL + 'agent/kb/article/' + this.meta.article_id
		);
	},

	initPage: function(el) {

		var self = this;
		this.wrapper = el;

		this.article_id = this.getMetaData('article_id');

		this._initBasic();
		this._initArticleArea();

		this._initLabels();
		this._initCommentForm();

		if (this.meta.canEdit) {
			this._initMenus();

			this._initPostArea();
			this._initAutoUnpublishOptions();
			this._initAutoPublishOptions();

			var btn = $('.kb-editor-edit', this.wrapper);
			btn.on('click', this.showEditor.bind(this));

			if (this.meta.isValidating) {
				this.validatingEdit = new DeskPRO.Agent.PageHelper.ValidatingEdit(this, {
					typename: 'articles',
					contentId: this.meta.article_id
				});
				this.ownObject(this.validatingEdit);
			}
		}

		this.relatedContent = new DeskPRO.Agent.PageHelper.RelatedContent(this, {
			typename: 'articles',
			content_id: this.meta.article_id,
			listEl: $('section.linked-content:first', this.wrapper),
			disabled: !this.meta.canEdit,
			onContentLinked: function(typename, content_id) {
				$.ajax({
					url: BASE_URL + 'agent/kb/article/' + self.meta.article_id + '/ajax-save',
					type: 'POST',
					data: { content_type: typename, content_id: content_id, action: 'add-related' },
					context: this,
					dataType: 'json'
				});
			},
			onContentUnlinked: function(typename, content_id) {
				$.ajax({
					url: BASE_URL + 'agent/kb/article/' + self.meta.article_id + '/ajax-save',
					type: 'POST',
					data: { content_type: typename, content_id: content_id, action: 'remove-related' },
					context: this,
					dataType: 'json'
				});
			}
		});
		this.ownObject(this.relatedContent);

		this.whoVotedOverlay = new DeskPRO.UI.Overlay({
			triggerElement: '.who-voted-trigger',
			contentMethod: 'ajax',
			contentAjax: {
				url: BASE_URL + 'agent/publish/rating-who-voted/article/' + this.meta.article_id
			}
		});
		this.ownObject(this.whoVotedOverlay);

		this.whoViewedOverlay = new DeskPRO.UI.Overlay({
			triggerElement: '.open-who-viewed',
			contentMethod: 'ajax',
			contentAjax: {
				url: BASE_URL + 'agent/publish/who-viewed/1/' + this.meta.article_id
			}
		});
		this.ownObject(this.whoViewedOverlay);

		this.miscContent = new DeskPRO.Agent.PageHelper.MiscContent(this, {
			revisionCompareUrl: BASE_URL + 'agent/kb/compare-revs/{OLD}/{NEW}'
		});
		this.ownObject(this.miscContent);

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
				url: BASE_URL + 'agent/kb/article/' + self.meta.article_id + '/ajax-save-custom-fields',
				type: 'POST',
				data: formData,
				dataType: 'html',
				success: function(rendered) {
					fieldsRendered.empty().html(rendered);
					propToggle('display');
				}
			});
		});

		this.scanGlossaryWords();
		this._initTrans();
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

	scanGlossaryWords: function() {
		DeskPRO.WordHighlighter.highlight(this.getEl('content_ed').find('.article-content-wrap').get(0), this.meta.glossaryWords);
		this.getEl('content_ed').find('span.dp-highlight-word').each(function() {
			$(this).addClass('embedded-glossary-word tipped').data('tipped-options', "ajax:true, maxWidth:300").data('tipped', BASE_URL + "agent/glossary/"+$(this).data('word')+"/tip");
		});
	},

	//#################################################################
	//# Basic
	//#################################################################

	_initBasic: function() {
		var self = this;

		if (this.meta.canEdit) {
			$('.edit-trigger', this.wrapper).on('click', function() {
				DeskPRO_Window.runPageRoute('kb_article_edit:' + BASE_URL + 'agent/kb/article/' + self.article_id);
				DeskPRO_Window.removePage(self);
			});

			$('.validate-trigger', this.wrapper).on('click', function() {
				DeskPRO_Window.runPageRoute('kb_article_edit:' + BASE_URL + 'agent/kb/article/' + self.article_id + '?do_validate=1');
				DeskPRO_Window.removePage(self);
			});

			var editTitle = new DeskPRO.Agent.PageFragment.Page.EditTitle(
				this,
				BASE_URL + 'agent/kb/article/' + self.meta.article_id + '/ajax-save'
			);
		}

		// Tabs
		this.bodyTabs = new DeskPRO.UI.SimpleTabs({
			triggerElements: $('li', this.getEl('bodytabs')),
			onTabSwitch: (function(info) {
				if ($(info.tabContent).is('.kb-content')) {
					self.getEl('content_edit_btns').show();
				} else {
					self.getEl('content_edit_btns').hide();
				}
				if ($(info.tabContent).is('.kb-related-content')) {
					$('body').addClass('related-controls-on');
				} else {
					$('body').removeClass('related-controls-on');
					if ($(info.tabContent).is('.search-tab')) {
						self._initSearchTab();
					}
				}

				if ($(info.tabContent).is('.revisions-tab') && !$(info.tabContent).is('.loaded')) {
					$.ajax({
						url: BASE_URL + 'agent/kb/article/' + this.meta.article_id + '/view-revisions',
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

		var actions = this.getEl('action_buttons');
		$('.permalink', actions).on('click', function() {
			var html = [];
			html.push('<div>');
			html.push('The permalink to this article on the website is:<br />');
			html.push('<input type="text" style="width:95%;" />');
			html.push('</div>');

			var msg = $(html.join(''));
			$('input', msg).val(self.meta.permalink);

			DeskPRO_Window.showAlert(msg);
		});

		$('.view-user-interface', actions).on('click', function() {
			window.open(self.meta.permalink);
		});

		// Attachments
		var list = $('.file-list', this.wrapper);

		DeskPRO_Window.util.fileupload(this.wrapper, {
			url: BASE_URL + 'agent/misc/accept-upload?attach_to_object=article&object_id=' + this.meta.article_id,
			page: this
		});

		list.on('click', '.delete', function(ev) {
			ev.preventDefault();
			ev.stopImmediatePropagation();

			var blob_id = $(this).data('blob-id');
			$.ajax({
				url: BASE_URL + 'agent/kb/article/' + self.meta.article_id + '/ajax-save',
				type: 'POST',
				data: {action: 'remove-blob', blob_id: blob_id},
				context: self,
				dataType: 'json'
			});

			$(this).closest('li').remove();

			if (!list.find('li')[0]) {
				list.hide();
			}
		});
	},


	//#################################################################
	//# Menus
	//#################################################################

	_initMenus: function() {
		var self = this;

		var statusSel = this.getEl('status');
		DP.select(statusSel);

		statusSel.on('change', function() {
			DeskPRO_Window.sections.publish_section.reload();
			var status = $(this).val();

			self.getEl('auto_unpub').hide();
			self.getEl('auto_pub').hide();

			if (status == 'published') {
				self.getEl('auto_unpub').show();
			} else if (status == 'hidden.unpublished') {
				self.getEl('auto_pub').show();
			}

			$.ajax({
				url: BASE_URL + 'agent/kb/article/' + self.meta.article_id + '/ajax-save',
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
			ajaxSaveUrl: BASE_URL + 'agent/kb/article/' + self.meta.article_id + '/ajax-save'
		});
		this.ownObject(this.deleteHelper);

		var lis = $('li:not(.add)', self.getEl('categories'));
		if (lis.length < 2) {
			$('.remove', lis).hide();
		}

		//-----
		// Category manage
		//-----

		this.getEl('categories').on('click', '.remove', function(ev) {
			var li = $(this).parent();
			li.remove();

			var lis = $('li:not(.add)', self.getEl('categories'));
			if (lis.length == 1) {
				// Hide the remove from the last cat
				$('.remove', lis).hide();
			}

			self.sendUpdateCats();
		});

		this.getEl('addcat_trigger').on('click', function(ev) {
			if (!self.newCatTpl) {
				self.newCatTpl = DeskPRO_Window.util.getPlainTpl(self.getEl('addcat_select_tpl'));
			}

			var newLi = $(self.newCatTpl);
			newLi.find('select').on('change', function() {
				self.sendUpdateCats();
			}).prepend('<option></option>');
			newLi.find('select').find('option').first().prop('selected', true);
			self.getEl('addcat_li').before(newLi);

			DP.select(newLi.find('select'));
		});

		this.getEl('categories').on('change', function(ev) {
			self.sendUpdateCats();
		});

		DP.select(this.getEl('categories').find('select'));

		//-----
		// Product manage
		//-----

		this.getEl('products').on('click', '.remove', function(ev) {
			var li = $(this).parent();
			li.remove();

			var lis = $('li:not(.add)', self.getEl('products'));
			if (lis.length == 1) {
				// Hide the remove from the last cat
				$('.remove', lis).hide();
			}

			self.sendUpdateProds();
		});

		this.getEl('addprod_trigger').on('click', function(ev) {
			if (!self.newProdTpl) {
				self.newProdTpl = DeskPRO_Window.util.getPlainTpl(self.getEl('addprod_select_tpl'));
			}

			var newLi = $(self.newProdTpl);
			newLi.find('select').on('change', function() {
				self.sendUpdateProds();
			}).prepend('<option></option>');
			newLi.find('select').find('option').first().prop('selected', true);
			self.getEl('addprod_li').before(newLi);

			DP.select(newLi.find('select'));
		});

		this.getEl('products').on('change', function(ev) {
			self.sendUpdateProds();
		});

		DP.select(this.getEl('products').find('select'));
	},

	sendUpdateCats: function() {

		if (this.sendingCatUpdate) {
			this.resetCatUpdate = true;
			return;
		}
		this.sendingCatUpdate = true;
		this.resetCatUpdate = false;

		var ids = [];
		this.getEl('categories').find('select').each(function() {
			var id = parseInt($(this).val());
			if (id) {
				if (ids.indexOf(id) !== -1) {
					$(this).closest('li').remove()
				} else {
					ids.push(id);
				}
			}
		});

		var formData = $('select', this.getEl('categories')).serializeArray();

		formData.push({
			name: 'action',
			value: 'categories'
		});

		$.ajax({
			url: BASE_URL + 'agent/kb/article/' + this.meta.article_id + '/ajax-save',
			type: 'POST',
			data: formData,
			context: this,
			dataType: 'json',
			success: function(data) {
				this.sendingCatUpdate = false;
				DeskPRO_Window.sections.publish_section.reload();

				// First cat becomes selectable if there are more than one
				if (data && data.category_ids && data.category_ids.length >= 2) {
					this.getEl('categories').find('.remove').show();
				}

				if (this.resetCatUpdate) {
					this.sendUpdateCats();
				}
			}
		});
	},

	sendUpdateProds: function() {
		if (this.sendingProdUpdate) {
			this.resetProdUpdate = true;
			return;
		}
		this.sendingProdUpdate = true;
		this.resetProdUpdate = false;

		var ids = [];
		this.getEl('products').find('select').each(function() {
			var id = parseInt($(this).val());
			if (id) {
				if (ids.indexOf(id) !== -1) {
					$(this).closest('li').remove()
				} else {
					ids.push(id);
				}
			}
		});

		var formData = $('select', this.getEl('products')).serializeArray();

		formData.push({
			name: 'action',
			value: 'products'
		});

		$.ajax({
			url: BASE_URL + 'agent/kb/article/' + this.meta.article_id + '/ajax-save',
			type: 'POST',
			data: formData,
			context: this,
			dataType: 'json',
			success: function(data) {
				this.sendingProdUpdate = false;

				if (this.resetProdUpdate) {
					this.sendUpdateProds();
				}
			}
		});
	},

	//#################################################################
	//# Labels
	//#################################################################

	_initLabels: function() {
		if (this.hasInitLabels) return;
		this.hasInitLabels = true;

		this.labelsInput = new DeskPRO.UI.LabelsInput({
			type: 'article',
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
			contentType: 'articles',
			contentId: this.meta.article_id,
			element: this.getEl('stickysearch_input')
		});
		this.ownObject(this.stickyWords);
	},

	//#################################################################
	//# Automatic Unpublish
	//#################################################################

	_initAutoUnpublishOptions: function() {
		var self = this;

		var optWrap = this.getEl('auto_unpub');

		$('.auto-unpublish-set', optWrap).on('click', function() {
			self.updateAutoUnPubOptions();
			$('.auto-unpublish', optWrap).show();
			$(this).hide();
		});

		$('.remove-auto-unpublish', optWrap).on('click', function() {
			self.removeAutoUnPubOptions();
			$('.auto-unpublish-set', optWrap).show();
			$('.auto-unpublish', optWrap).hide();
		});

		var endOpt = $('.auto-unpublish .end-action.opt', optWrap);
		var m = new DeskPRO.UI.Menu({
			triggerElement: endOpt,
			menuElement: $('.end-action-menu', optWrap),
			onItemClicked: function(info) {
				var val = $(info.itemEl).data('action');
				var label = $(info.itemEl).text().trim();

				endOpt.data('val', val);
				endOpt.text(label);

				self.updateAutoUnPubOptions();
			}
		});
		this.ownObject(m);

		var endDate = $('.auto-unpublish .end-date.opt', optWrap);
		var dateInput = $('.auto-unpublish .end-date-input', optWrap);
		dateInput.datepicker({
			dateFormat: 'M d, yy',
			onSelect: function(dateText, inst) {

				var timestamp = dateInput.datepicker('getDate').getTime() / 1000;

				endDate.data('val', timestamp);
				endDate.text(dateText);

				self.updateAutoUnPubOptions();
			}
		});

		endDate.on('click', function() {
			$('.auto-unpublish .end-date-input', optWrap).datepicker('show');
		});
	},

	removeAutoUnPubOptions: function() {
		$.ajax({
			url: BASE_URL + 'agent/kb/article/' + this.meta.article_id + '/ajax-save',
			type: 'POST',
			data: {action: 'remove-auto-unpub'},
			context: this,
			dataType: 'json'
		});
	},

	updateAutoUnPubOptions: function() {
		var optWrap = this.getEl('auto_unpub');
		var endTimestamp = $('.auto-unpublish .end-date.opt', optWrap).data('val');
		var endAction = $('.auto-unpublish .end-action.opt', optWrap).data('val');

		// Still need them to enter an input
		if (!endTimestamp || !endAction) {
			return;
		}

		var data = [];
		data.push({
			name: 'action',
			value: 'auto-unpub'
		});
		data.push({
			name: 'end_action',
			value: endAction
		});
		data.push({
			name: 'end_timestamp',
			value: endTimestamp
		});

		$.ajax({
			url: BASE_URL + 'agent/kb/article/' + this.meta.article_id + '/ajax-save',
			type: 'POST',
			data: data,
			context: this,
			dataType: 'json'
		});
	},

	//#################################################################
	//# Automatic Publish
	//#################################################################

	_initAutoPublishOptions: function() {
		var self = this;

		var optWrap = this.getEl('auto_pub');

		$('.auto-publish-set', optWrap).on('click', function() {
			self.updateAutoPubOptions();
			$('.auto-publish', optWrap).show();
			$(this).hide();
		});

		$('.remove-auto-publish', optWrap).on('click', function() {
			self.removeAutoPubOptions();
			$('.auto-publish-set', optWrap).show();
			$('.auto-publish', optWrap).hide();
		});

		var pubDate = $('.auto-publish .pub-date.opt', optWrap);
		var dateInput = $('.auto-publish .pub-date-input', optWrap);
		dateInput.datepicker({
			dateFormat: 'M d, yy',
			onSelect: function(dateText, inst) {

				var timestamp = dateInput.datepicker('getDate').getTime() / 1000;

				pubDate.data('val', timestamp);
				pubDate.text(dateText);

				self.updateAutoPubOptions();
			}
		});

		pubDate.on('click', function() {
			$('.auto-publish .pub-date-input', optWrap).datepicker('show');
		});
	},

	removeAutoPubOptions: function() {
		$.ajax({
			url: BASE_URL + 'agent/kb/article/' + this.meta.article_id + '/ajax-save',
			type: 'POST',
			data: {action: 'remove-auto-pub'},
			context: this,
			dataType: 'json'
		});
	},

	updateAutoPubOptions: function() {
		var optWrap = this.getEl('auto_unpub');
		var timestamp = $('.auto-unpublish .end-date.opt', optWrap).data('val');

		// Still need them to enter an input
		if (!timestamp) {
			return;
		}

		var data = [];
		data.push({
			name: 'action',
			value: 'auto-pub'
		});
		data.push({
			name: 'pub_timestamp',
			value: timestamp
		});

		$.ajax({
			url: BASE_URL + 'agent/kb/article/' + this.meta.article_id + '/ajax-save',
			type: 'POST',
			data: data,
			context: this,
			dataType: 'json'
		});
	},

	//#################################################################
	//# Article body
	//#################################################################

	_initArticleArea: function() {
		var loader = this.wrapper.find('.article-loading');

		loader.show();

		var iframe = this.wrapper.find('.article-iframe');
		var iframeLoad = function() {
			if (this.contentWindow && this.contentWindow.document) {
				loader.hide();
				$(this).css({
					overflow: 'hidden',
					border: 'none',
					padding: 0,
					margin: 0
				});
				$(this).height($(this.contentWindow.document).height());

				var doc = this.contentWindow.document, iframeWindow = this.contentWindow;

				var wheel = function(e) {
					e = e || iframeWindow.event;
					var scroller = iframe.closest('.with-scrollbar').get(0), proxyE;
					if (scroller.dispatchEvent) {
						try {
							proxyE = document.createEvent('MouseWheelEvent');
							proxyE.initMouseWheelEvent(
								e.type, e.bubbles, e.cancelable, window, e.detail,
								e.screenX, e.screenY, e.clientX, e.clientY,
								e.button, null, '', e.wheelDelta
							);
						} catch (e) {
							proxyE = null;
						}

						if (!proxyE) {
							proxyE = document.createEvent('MouseEvent');
							proxyE.initMouseEvent(
								e.type, e.bubbles, e.cancelable, window, e.detail,
								e.screenX, e.screenY, e.clientX, e.clientY,
								e.ctrlKey, e.altKey, e.shiftKey, e.metaKey, e.button,
								null
							);
						}
						iframe.closest('.with-scrollbar').get(0).dispatchEvent(proxyE);
					} else {
						proxyE = document.createEventObject(e);
						proxyE.view = window;
						iframe.closest('.with-scrollbar').get(0).fireEvent("onmousewheel", proxyE);
					}
				};
				if (doc.addEventListener){
					doc.addEventListener('DOMMouseScroll', wheel, false);
					doc.addEventListener('mousewheel', wheel, false);
				} else {
					doc.onmousewheel = wheel;
				}
			}
		};

		iframe.on('load', iframeLoad);
		iframeLoad.call(iframe);
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

		var attachList = $('ul.attachment-list:first', this.wrapper);
		if (attachList.length) {

			this.getEl('attachtab').empty().append(attachList);

			var imageEls = $('li.is-image a', attachList);

			imageEls.colorbox({
				title: function(){ var url = $(this).attr('href'); return '<a href="'+url+'" target="_blank">Open In New Window</a>' },
				width: '50%',
				height: '50%',
				initialWidth: '200',
				initialHeight: '150',
				scalePhotos: true,
				photo: true,
				opacity: 0.5,
				transition: 'none'
			});
		}

		if (this.editStateSaver) {
			this.editStateSaver.destroy();
		}

		this.editStateSaver = new DeskPRO.Agent.PageHelper.StateSaver({
			stateId: 'editarticle.' + this.article_id,
			listenOn: $('.article-editor-wrap:first', wrap)
		});
		this.ownObject(this.editStateSaver);

		var wrap = this.wrapper;

		this.getEl('save_btn').off('click').on('click', (function(ev) {
			ev.preventDefault();

			var data = [];
			data.push({
				name: 'action',
				value: 'content'
			});
			data.push({
				name: 'content',
				value: $('.article-editor-wrap textarea:first', wrap).val()
			});
			data.push({
				name: 'language_id',
				value: wrap.find('.article-editor.wrap').find('.language_id').val()
			});

			$('input.edit-content-attach:checked', wrap).each(function() {
				data.push({
					name: 'attach[]',
					value: $(this).val()
				});
			});

			var showSaving = this.getEl('article_save').find('.mark-loading');
			var showSaved  = this.getEl('article_save').find('.mark-saved');

			showSaved.stop().hide();
			showSaving.show();

			$.ajax({
				url: BASE_URL + 'agent/kb/article/' + this.meta.article_id + '/ajax-save',
				type: 'POST',
				context: this,
				data: data,
				dataType: 'json',
				complete: function() {
					showSaving.hide();
				},
				success: function(data) {
					this.getEl('content_ed').html(data.content_html);
					this._initPostArea();
					this._initArticleArea();
					this.handleUnloadRevisions(data.revision_id);

					showSaved.show().fadeOut(2000);
				}
			});

		}).bind(this));

		this.hideEditor();
	},

	destroyPage: function() {
		// Workaround for tinymce bug to do with remove()
		// We'll manually remove the node ourselves
		var el = $('.article-editor-wrap', this.getEl('content_ed'));
		if (el[0]) {
			el.get(0).parentNode.removeChild(el.get(0));
		}
	},

	showEditor: function() {

		$('body').addClass('content-link-control-on');

		var self = this;

		$('.article-content-wrap', this.getEl('content_ed')).hide();
		var edWrap = $('.article-editor-wrap', this.getEl('content_ed')).show();

		$('.revert-default', edWrap).on('click', function() {
			var def = $('textarea.edit-content-field-default').val();
			$('textarea.edit-content-field').val(def);

			$('.revert-message-notice', edWrap).remove();
		});

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

			this.rte = DP.rteTextarea(txt, {
				setup: function(ed) {
					ed.onKeyPress.add(function() {
						self.editStateSaver.triggerChange();
					});
				}
			});

			var saveBtn = this.getEl('save_btn');
			this.acceptContentLink = new DeskPRO.Agent.PageHelper.AcceptContentLink({
				page: this,
				rte: txt,
				isReadyCallback: function() {
					return saveBtn.is(':visible');
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
		$('body').removeClass('content-link-control-on');
		this.getEl('edit_btn').show();
		this.getEl('save_btn').hide();
		this.getEl('cancel_btn').hide();
		$('.article-editor-wrap', this.getEl('content_ed')).hide();
		$('.article-content-wrap', this.getEl('content_ed')).show();
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
			url: BASE_URL + 'agent/kb/article/' + this.getMetaData('article_id') + '/ajax-save-comment',
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
					DeskPRO_Window.sections.publish_section.modCommentCount('articles', '+');
				}
			}
		});
	},

	//#################################################################
	//# Translations
	//#################################################################

	_initTrans: function() {
		var self = this;
		var transGroup = this.wrapper.find('.trans-input-form');
		if (!transGroup[0]) {
			return;
		}

		transGroup.find('.language_id_switcher').on('click change', function() {
			transGroup.find('.dp-group').removeClass('on');
			$(this).closest('.dp-group').addClass('on');
		});

		transGroup.find('textarea').each(function() {
			var rte = DP.rteTextarea($(this));
			$(this).data('rte', rte);
		});

		transGroup.find('.copy-trigger').on('click', function() {
			var row = $(this).closest('.dp-group');
			var titleInput   = row.find('.title-row').find('input');
			var contentInput = row.find('.editor-row').find('textarea');

			var defaultTitleInput = self.getEl('editname').find('input');
			var defaultContentInput = self.wrapper.find('.article-editor-wrap').find('.edit-content-field-default');

			titleInput.val(defaultTitleInput.val());
			contentInput.tinymce().setContent(defaultContentInput.val());
		});

		transGroup.find('.save-trigger').on('click', function() {
			var row = $(this).closest('.dp-group');
			var titleInput   = row.find('.title-row').find('input');
			var contentInput = row.find('.editor-row').find('textarea');

			var postData = [];
			postData.push({name: titleInput.attr('name'), value: titleInput.val() });
			postData.push({name: contentInput.attr('name'), value: contentInput.val() });
			postData.push({name: 'action', value: 'trans' });

			row.addClass('dp-loading-on');
			$.ajax({
				url: BASE_URL + 'agent/kb/article/' + self.meta.article_id + '/ajax-save',
				type: 'POST',
				data: postData,
				context: this,
				dataType: 'json',
				complete: function() {
					row.removeClass('dp-loading-on');
				},
				success: function(data) {
					row.removeClass('is-status-neg').addClass('is-status-pos');
				}
			});
		});
	}
});
