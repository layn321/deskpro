Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.KbValidatingArticles = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.Basic,

	initPage: function(el) {
		this.wrapper = el;

		var self = this;
		$('button.view-trigger', this.wrapper).on('click', function(ev) {
			ev.preventDefault();
			self.viewComment($(this).parent().parent().parent().parent());
		});

		$('button.ignore-trigger', this.wrapper).on('click', function(ev) {
			ev.preventDefault();
			self.ignoreComment($(this).parent().parent().parent().parent());
		});

		$('button.delete-trigger', this.wrapper).on('click', function(ev) {
			ev.preventDefault();
			self.deleteComment($(this).parent().parent().parent().parent());
		});
	},

	viewComment: function(commentEl) {
		var content = $('div.content', commentEl);
		content.show();

		var buttonsClosed = $('.show-closed', commentEl);
		var buttonsOpened = $('.show-opened', commentEl);

		buttonsClosed.hide();
		buttonsOpened.show();
	},

	ignoreComment: function(commentEl) {
		commentEl.fadeOut();
	},

	deleteComment: function(commentEl) {
		$.ajax({
			url: this.getMetaData('deleteCommentUrl'),
			type: 'POST',
			context: this,
			data: {comment_id: commentEl.data('comment-id')},
			dataType: 'json',
			success: function(data) {
				commentEl.fadeOut();
			}
		});
	},

	validateComment: function(commentEl) {
		$.ajax({
			url: this.getMetaData('validateCommentUrl'),
			type: 'POST',
			context: this,
			data: {comment_id: commentEl.data('comment-id')},
			dataType: 'json',
			success: function(data) {
				commentEl.fadeOut();
			}
		});
	}
});
