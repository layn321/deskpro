Orb.createNamespace('DeskPRO.User.ElementHandler');

DeskPRO.User.ElementHandler.FeedbackView = new Orb.Class({

	Extends: DeskPRO.User.ElementHandler.ElementHandlerAbstract,

	init: function() {
		var voteHelper = new DeskPRO.User.ElementHandler.Helper.FeedbackVote();

		this.btnEl = $('#submit_vote_trigger');
		this.btnEl.on('click', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();
			voteHelper.openMenu($(this));
		});
	}
});
