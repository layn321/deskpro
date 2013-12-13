Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.FeedbackCommentsValidating = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.PublishValidatingComments,

	updateCount: function(action) {
		var countEl = $('#feedback_comments_validating_count');
		var count = parseInt(countEl.text());

		if (action == 'add') {
			count++;
		} else {
			count--;
		}

		if (count < 0) {
			count = 0;
		}

		var countEl = $('#feedback_comments_validating_count').text(count);

		DeskPRO_Window.sections.feedback_section.recountBadge();
	}
});
