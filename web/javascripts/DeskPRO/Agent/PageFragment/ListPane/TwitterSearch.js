Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.TwitterSearch = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.TwitterStatus,

	initPage: function(el) {
		var self = this;

		this.parent(el);

		this.maxTwitterId = el.find('.twitter-status-list').attr('data-twitter-max-id');

		this.updateInterval = setInterval(function() {
			$.ajax({
				url: self.meta.statusListUrl,
				data: {since_id: self.maxTwitterId },
				dataType: 'html',
				success: function(html) {
					var $html = $(html).first();

					if ($html.data('added')) {
						self.maxTwitterId = $html.attr('data-twitter-max-id');
						self.resultsHelper.adjustResultCount($html.data('added'));

						if (self.resultsHelper.getCurrentPage() > 1) {
							self.adjustNewTweetIndicator($html.data('added'));
						} else {
							var adding = $html.find('.row-item.twitter-status');

							self.content.find('.twitter-status-list').prepend(adding);
							self._afterLoading(adding);
							self.adjustShownTweets();
						}
					}
				}
			});
		}, 60 * 1000);
	},

	destroy: function() {
		clearInterval(this.updateInterval);
	}
});
