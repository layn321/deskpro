Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');
DeskPRO.Agent.PageFragment.Page.NewTweet = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'newtweet';
		this.allowDupe = true;
	},

	initPage: function(el) {

		this.noIgnoreForm = true;
		var self = this;
		this.wrapper = el;

		var helper = new DeskPRO.Agent.PageHelper.Twitter(this.wrapper, this);

		var textarea = this.wrapper.find('textarea');
		textarea.TextAreaExpander();

		if (this.getMetaData('tweetSignature')) {
			textarea.val(' ' + this.getMetaData('tweetSignature'));
			helper.updateTweetLength(textarea);
		}
		textarea.focus();

		DP.select(this.getEl('from_account'));

		var form = this.getEl('form');
		form.on('submit', Orb.cancelEvent);

		var footer = $('footer.pop-footer', el);
		$('.submit-trigger', el).on('click', function() {
			var postData = form.serializeArray();

			footer.addClass('loading');

			$.ajax({
				url: form.attr('action'),
				type: 'POST',
				dataType: 'json',
				data: postData,
				complete: function() {
					footer.removeClass('loading');
				},
				success: function(data) {
					self.meta.popover.close();
					if (DeskPRO_Window.sections.twitter_section) {
						DeskPRO_Window.sections.twitter_section.refresh();
					}
				}
			});
		});
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
	}
});
