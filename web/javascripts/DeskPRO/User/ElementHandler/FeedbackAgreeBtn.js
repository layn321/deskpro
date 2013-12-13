Orb.createNamespace('DeskPRO.User.ElementHandler.Helper');

DeskPRO.User.ElementHandler.FeedbackAgreeBtn = new Orb.Class({

	Extends: DeskPRO.User.ElementHandler.ElementHandlerAbstract,

	init: function() {
		var self = this;

		if (this.el.hasClass('dp-feedback-closed')) {
			return;
		}

		this.el.find('.dp-feedback-btn').on('click', function() {
			self.voteOnElement(self.el);
		});
	},

	voteOnElement: function(el) {
		var rating;
		if (el.is('.dp-voted')) {
			rating = 0;
		} else {
			rating = 1;
		}

		var parentRow = el.closest('.dp-feedback-row');

		var old_num = parseInt($('em', el).first().text());

		if (rating) {
			el.addClass('dp-voted');
			parentRow.addClass('dp-voted-row');
			$('em', el).first().text(old_num+1);
		} else {
			el.removeClass('dp-voted');
			parentRow.removeClass('dp-voted-row');
			$('em', el).first().text(old_num-1);
		}

		$.ajax({
			url: el.data('vote-url'),
			data: { rating: rating },
			dataType: 'json',
			context: this,
			type: 'POST',
			success: function(data) {
				if (data.voted) {
					el.addClass('dp-voted');
					parentRow.addClass('dp-voted-row');
				} else {
					el.removeClass('dp-voted');
					parentRow.removeClass('dp-voted-row');
				}

				$('em', el).first().text(data.total_rating);
			}
		});
	}
});
