Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');

DeskPRO.Agent.PageFragment.Page.NewArticle = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'newarticle';
		this.allowDupe = true;
	},

	initPage: function(el) {
		var self = this;
		this.wrapper = el;
		this.parent(el);

		if (!this.getEl('cat').find('option')[0]) {
			this.wrapper.find('.form-header-error').show();
			this.wrapper.find('.form-outer').hide();
			this.markForReload();
		}

		this.form = $('form', this.wrapper).on('submit', function(ev) {
			ev.preventDefault();
		});

		$('button.submit-trigger', this.wrapper).on('click', this.submit.bind(this));

		this._initContentSection();
		this._initOtherSection();

		this.stateSaver = new DeskPRO.Agent.PageHelper.StateSaver({
			stateId: 'newarticle',
			listenOn: this.getEl('newarticle')
		});
		this.ownObject(this.stateSaver);

		window.setTimeout(function() {
			if (self.OBJ_DESTROYED) return;

			self.wrapper.find('select').each(function() {
				if ($(this).prop('multiple')) {
					$(this).width(300);
				}
				DP.select($(this));
			});
			self.updateUi();
		}, 300);

		this.activate();
	},

	activate: function() {
		var selectedCat = $('#publish_outline_articlescat_list').find('.nav-selected').data('cat-id');
		if (selectedCat) {
			this.getEl('cat').find("option[value=\"" + selectedCat + "\"]").prop('selected', true);
			this.getEl('cat').trigger('change');
		}
	},

	markForReload: function() {
		if (!this.markedForReload) {
			this.markedForReload = true;
			this.addEvent('deactivate', this.closeSelf.bind(this));
		}
	},

	closeSelf: function() {
		var ev = {cancel: false};
		this.fireEvent('closeSelf', ev);

		if (!ev.cancel) {
			this.parent();
		}
	},

	destroyPage: function() {
		// Workaround for tinymce bug to do with remove()
		// We'll manually remove the node ourselves
		var el = this.wrapper.find('.article-section');
		if (el[0]) {
			el.get(0).parentNode.removeChild(el.get(0));
		}
	},

	submit: function() {
		var formData = this.form.serializeArray();

		if (this.labelsInput) {
			formData.append(this.labelsInput.getFormData());
		}

		$('div.error.section', this.wrapper).removeClass('error');
		$('.error-message-on', this.wrapper).removeClass('error-message-on');

		this.stateSaver.stop();
		this.stateSaver.resetState();
		this.wrapper.addClass('loading');

		$.ajax({
			url: BASE_URL + 'agent/kb/article/new/save',
			type: 'POST',
			data: formData,
			dataType: 'json',
			context: this,
			complete: function() {
				this.wrapper.removeClass('loading');
			},
			success: function(data) {

				if (data.error) {
					Array.each(data.error_codes, function(code) {
						this.showErrorCode(code);
					}, this);
					this.updateUi();
					return;
				}

				var pending_article_id = this.getEl('pending_article_id').val();
				if (pending_article_id) {
					DeskPRO_Window.getMessageBroker().sendMessage('kb.pending_article_removed', {
						pending_article_id: pending_article_id
					});
				}

				if (data.success) {
					DeskPRO_Window.runPageRoute('page:' + BASE_URL + 'agent/kb/article/' + data.article_id);
					this.markForReload();
					this.closeSelf();
				} else {
					alert('There was an error with the form');
				}
			}
		});
	},

	showErrorCode: function(code) {
		$('.' + code + '.error-message', this.wrapper).addClass('error-message-on');
	},

	setTitle: function(title) {
		this.getEl('title').val(title).change();
	},
	setContent: function(content, is_html) {
		if (!is_html) {
			content = Orb.escapeHtml(content);
		}
		this.getEl('content').html(content);
	},

	setPendingArticle: function(data) {
		this.getEl('pending_article_id').val(data.id);

		if (data.ticket_subject) {
			this.setTitle(data.ticket_subject);
		}
		if (data.initial_message_html) {
			var content = '';
			if (data.initial_message_html) {
				content = "<h3>Question:</h3>";
				content += data.initial_message_html += "<br /><br />";
			}
			this.setContent(content, true);
		} else {
			this.setContent('', true);
		}

		var infoWrap = $('.pending-info:first', this.wrapper);

		if (data.ticket_url) {
			$('.pending-ticket a', infoWrap).text(data.ticket_subject);
			$('.pending-ticket a', infoWrap).data('route', 'page:' + data.ticket_url);
			$('.pending-ticket', infoWrap).show();
		}

		if (data.comment) {
			$('.pending-reason', infoWrap).text(data.comment).show();
		}

		$('.person-name', infoWrap).text(data.person_name);

		infoWrap.show();
	},

	//#################################################################
	//# Content section
	//#################################################################

	_initContentSection: function() {

		var self = this;
		this.getEl('content').css({
			width: this.wrapper.width() - 80
		});

		// Make the size of the message box based off of the height of the window
		var h = $(window).height();
		this.getEl('content').css('height', Math.max(h - 500, 200));

		DP.rteTextarea(this.getEl('content'), {
			setup: function(ed) {
				ed.onKeyPress.add(function() {
					if (self.stateSaver) {
						self.stateSaver.triggerChange();
					}
				});
			}
		});

		this.acceptContentLink = new DeskPRO.Agent.PageHelper.AcceptContentLink({
			page: this,
			rte: this.getEl('content')
		});
	},

	//#########################################################################
	//# Other Section
	//#########################################################################

	_initOtherSection: function() {
		var self = this;
		this.otherTabs = new DeskPRO.UI.SimpleTabs({
			triggerElements: $('li', this.getEl('other_props_tabs')),
			context: this.getEl('other_props_tabs_content'),
			autoSelectFirst: false,
			onTabSwitch: function(eventData) {
				if (!self.labelsInput && eventData.tabContent.hasClass('tab-properties')) {
					self.labelsInput = new DeskPRO.UI.LabelsInput({
						type: 'articles',
						fieldName: 'newarticle[labels]',
						input: $(".tags-wrap input", eventData.tabContent),
						onChange: function() {
							if (self.stateSaver) {
								self.stateSaver.triggerChange();
							}
						}
					});
					self.ownObject(self.labelsInput);
				}
			},
			onTabClick: (function(ev) {
				var contentWrap = this.getEl('other_props_tabs_content');
				var navWrap = this.getEl('other_props_tabs_wrap');
				var tab = ev.tabEl;

				// Toggle content state if we're clicking for the first time,
				// or re-clicking a tab
				if (!$('.on', navWrap).length || tab.is('.on')) {
					if (contentWrap.is(':visible')) {
						contentWrap.hide();
						navWrap.removeClass('on');
					} else {
						contentWrap.show();
						navWrap.addClass('on');
					}
				}
			}).bind(this)
		});
		this.ownObject(this.otherTabs);

		this.getEl('slug').on('focus', function() {
			$(this).addClass('had-focus');
		});

		// Attachments
		var list = $('.file-list', this.wrapper);
		$('input', list[0]).live('click', function() {
			var el = $(this);
			var li = el.parent();
			if (el.is(':checked')) {
				li.removeClass('unchecked');
			} else {
				li.addClass('unchecked');
			}
		});

		DeskPRO_Window.util.fileupload(this.wrapper, {
			page: this
		});
	}
});
