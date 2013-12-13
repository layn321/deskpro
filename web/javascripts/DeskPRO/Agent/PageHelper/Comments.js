Orb.createNamespace('DeskPRO.Agent.PageHelper');

DeskPRO.Agent.PageHelper.Comments = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(page, options)  {
		var self = this;
		this.page = page;

		this.options = {
			/**
			 * The main comments wrapper element
			 */
			commentsWrapper: null
		};

		this.setOptions(options);

		this.commentsWrapper = $(this.options.commentsWrapper);

		this.commentsWrapper.on('click', '.comment-edit-btn', function(ev) {
			ev.stopPropagation();
			ev.preventDefault();
			self.getCommentMenu().open(ev);
		});

		this.commentsWrapper.on('click', '.comment-validate-btn', function(ev) {
			ev.stopPropagation();
			ev.preventDefault();
			self.getCommentValidationMenu().open(ev);
		});
	},

	getCommentMenu: function() {
		if (this._commentMenu) return this._commentMenu;

		var clone = $('#comment_tools_menu').first().clone(false);
		clone.attr('id', Orb.getUniqueId('el'));

		var self = this;
		this._commentMenu = new DeskPRO.UI.Menu({
			menuElement: clone,
			onItemClicked: function(info) {
				var commentEl = $(info.menu.getOpenTriggerElement()).parent().parent().parent();
				var action = $(info.itemEl).data('action');

				switch (action) {
					case 'edit':
						self.editComment(commentEl, commentEl.data('content-type'), commentEl.data('comment-id'));
						break;

					case 'delete':
						self.deleteComment(commentEl, commentEl.data('content-type'), commentEl.data('comment-id'));
						break;

					case 'create-ticket':
						$.ajax({
							url: BASE_URL + 'agent/publish/comments/new-ticket-info/' + commentEl.data('content-type') + '/' + commentEl.data('comment-id') + '.json',
							type: 'GET',
							dataType: 'json',
							success: function(data) {
								DeskPRO_Window.newTicketLoader.open(function(page) {
									page.setNewByComment(data);
								});
							}
						});
						break;
				}
			}
		});

		return this._commentMenu;
	},

	getCommentValidationMenu: function() {
		if (this._commentValidationMenu) return this._commentValidationMenu;

		var clone = $('#comment_validation_menu').first().clone(false);
		clone.attr('id', Orb.getUniqueId('el'));

		var self = this;
		this._commentValidationMenu = new DeskPRO.UI.Menu({
			menuElement: clone,
			onItemClicked: function(info) {
				var commentEl = $(info.menu.getOpenTriggerElement()).closest('article.content-message');
				var action = $(info.itemEl).data('action');

				switch (action) {
					case 'approve':
						self.approveComment(commentEl, commentEl.data('content-type'), commentEl.data('comment-id'));
						break;

					case 'delete':
						self.deleteComment(commentEl, commentEl.data('content-type'), commentEl.data('comment-id'));
						break;
				}
			}
		});

		return this._commentValidationMenu;
	},


	editComment: function(commentEl, typename, commentId) {
		commentEl.addClass('gear-loading');

		var self = this;
		$.ajax({
			url: BASE_URL + 'agent/publish/comments/info/'+typename+'/'+commentId,
			type: 'GET',
			dataType: 'json',
			context: this,
			success: function(data) {
				var editEl = $(DeskPRO_Window.util.getPlainTpl('#comment_edit_tpl'));
				$('.save-trigger', editEl).on('click', function(ev) {
					ev.preventDefault();
					self._saveEditComment(commentEl, editEl, typename, commentId);
				});
				$('.cancel-trigger', editEl).on('click', function(ev) {
					ev.preventDefault();
					self._closeEditComment(commentEl, editEl);
				});
				editEl.hide();

				$('textarea.comment', editEl).val(data.comment_text);

				var rendered = $('.rendered-message', commentEl);
				editEl.insertBefore(rendered);

				rendered.slideUp('fast', function() {
					editEl.slideDown('fast');
				});
			},
			complete: function() {
				commentEl.removeClass('gear-loading');
			}
		});
	},

	_saveEditComment: function(commentEl, editEl, typename, commentId) {
		commentEl.addClass('gear-loading');

		$.ajax({
			url: BASE_URL + 'agent/publish/comments/save-comment/'+typename+'/'+commentId,
			type: 'POST',
			data: {
				comment: $('textarea.comment', editEl).val()
			},
			dataType: 'json',
			context: this,
			error: function() {

			},
			success: function(data) {
				var rendered = $('.rendered-message', commentEl);
				rendered.html(data.comment_html);
				this._closeEditComment(commentEl, editEl);
			},
			complete: function() {
				commentEl.removeClass('gear-loading');
			}
		});
	},

	_closeEditComment: function(commentEl, editEl) {
		var rendered = $('.rendered-message', commentEl);
		editEl.slideUp('fast', function() {
			rendered.slideDown();
			editEl.remove();
		});
	},

	deleteComment: function(commentEl, typename, commentId) {
		commentEl.fadeOut();
		$.ajax({
			url: BASE_URL + 'agent/publish/comments/delete/'+typename+'/'+commentId,
			type: 'POST',
			context: this,
			dataType: 'json',
			error: function() {
				commentEl.show();
			},
			success: function(data) {
				commentEl.remove();

				if (DeskPRO_Window.sections.publish_section) {
					DeskPRO_Window.sections.publish_section.modCommentCount(commentEl.data('content-type'), '-');
				}
			}
		});
	},

	approveComment: function(commentEl, typename, commentId) {
		commentEl.removeClass('validating').addClass('gear-loading');

		$.ajax({
			url: BASE_URL + 'agent/publish/comments/approve/'+typename+'/'+commentId,
			type: 'POST',
			context: this,
			dataType: 'json',
			success: function() {
				commentEl.find('.comment-validate-btn').hide();
			},
			error: function() {
				commentEl.addClass('validating');
			},
			complete: function() {
				commentEl.removeClass('gear-loading');
			}
		});
	}
});
