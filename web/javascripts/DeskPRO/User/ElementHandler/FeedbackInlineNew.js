Orb.createNamespace('DeskPRO.User.ElementHandler.Helper');

DeskPRO.User.ElementHandler.FeedbackInlineNew = new Orb.Class({

	Extends: DeskPRO.User.ElementHandler.ElementHandlerAbstract,

	init: function() {
		var self = this;

		this.form = $('#new_suggest_form');
		this.form.on('submit', function() {
			$('#feedback_title').val($('#new_suggest_title').val());
		});
		this.isFormOpen = false;
		this.isSuggestOpen = false;

		this.suggestionSection = this.form.find('.dp-related-section');
		this.suggestionBox = this.suggestionSection.find('.dp-results');

		this.touchCaller = new DeskPRO.TouchCaller({
			timeout: 500,
			callback: function() {
				self.updateSuggestions();
			}
		});

		this.searchBox = $('#new_suggest_title');
		this.searchBox.on('keyup', function() {
			self.openForm();
			self.touchCaller.touch($(this).val().trim());
		});

		this.inlineLogin = new DeskPRO.User.InlineLoginForm({
			context: this.el
		});

		$('#feedback_title').on('keyup change', function() {
			$('#new_suggest_title').val($(this).val());
		});
		$('#new_suggest_title').on('keyup change', function() {
			$('#feedback_title').val($(this).val());
		});
	},

	updateSuggestions: function() {

		var postData = {
			content: this.searchBox.val()
		};

		$.ajax({
			url: this.el.data('suggest-url'),
			data: postData,
			type: 'post',
			dataType: 'html',
			context: this,
			success: function(html) {
				var list = $(html);
				if (!list.find('li')[0]) {
					this.setSuggestions(false);
					return;
				}

				this.setSuggestions(list);
			}
		});
	},

	setSuggestions: function(suggestions) {
		if (!suggestions) {
			if (this.isSuggestOpen) {
				this.suggestionSection.slideUp('fast');
			}
			this.isSuggestOpen = false;
			return;
		}

		this.suggestionBox.html(suggestions);

		if (!this.isSuggestOpen) {
			this.suggestionSection.slideDown('fast');
			this.isSuggestOpen = true;
		}
	},

	openForm: function() {
		if (this.isFormOpen) {
			return;
		}

		this.isFormOpen = true;
		this.form.find('.dp-feedback-form').slideDown();
	},

	closeForm: function() {
		if (!this.isFormOpen) {
			return false;
		}

		this.isFormOpen = false;
		this.form.find('.dp-feedback-form').slideDown();
	}
});
