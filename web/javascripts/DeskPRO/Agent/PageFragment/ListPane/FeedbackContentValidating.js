Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.FeedbackContentValidating = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.PublishValidatingContent,

	listRemove: function(el) {
		DeskPRO_Window.util.modCountEl($('#feedback_validating_count'), '-');
		DeskPRO_Window.sections.feedback_section.recountBadge();
		this.selectionBar.checkNone();
	}
});
