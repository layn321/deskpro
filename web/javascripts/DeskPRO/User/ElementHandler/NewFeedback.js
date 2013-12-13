Orb.createNamespace('DeskPRO.User.ElementHandler');

DeskPRO.User.ElementHandler.NewFeedback = new Orb.Class({

	Extends: DeskPRO.User.ElementHandler.ElementHandlerAbstract,

	init: function() {
		this._initSuggestionsBox();
		this._initLoginForm();

		$('.with-sub-options', this.el).each(function() {
			var parentSel = $('.parent-option', this);
			parentSel.data('original-name', parentSel.attr('name'));

			var wrapper = this;

			parentSel.on('change', function() {
				var val = $(this).val();
				var sub = $('.sub-options-' + val, wrapper);

				var allSubs = $('.dp-sub-options', wrapper).hide();
				$('select', allSubs).attr('name', '');

				sub.show();

				if (sub.length) {
					// If there is a sub, zero out the parent name and give it to the child
					parentSel.attr('name', '');
					$('select', sub).attr('name', parentSel.data('original-name'));
				} else {
					// Otherwise make sure the parent has the proper name
					parentSel.attr('name', parentSel.data('original-name'));
				}
			});
		});
	},

	//#########################################################################
	//# Suggestions
	//#########################################################################

	_initSuggestionsBox: function() {
		this.inlineSuggestions = new DeskPRO.User.InlineSuggestions({
			elementWrapper: this.el,
			titleText: '#feedback_title',
			contentText: '#feedback_content'
			//onResolved: this.setTicketSolvedAjax.bind(this),
			//onResolvedRedirect: this.setTicketSolvedRedirect.bind(this),
			//onNotResolved: this.setTicketUnsolvedAjax.bind(this)
		});
	},

	//#########################################################################
	// In-page login form
	//#########################################################################

	_initLoginForm: function(context) {
		this.inlineLogin = new DeskPRO.User.InlineLoginForm({
			context: this.el
		});
	}
});
