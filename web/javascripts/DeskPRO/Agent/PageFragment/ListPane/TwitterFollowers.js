Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.TwitterFollowers = new Orb.Class({
    Extends: DeskPRO.Agent.PageFragment.ListPane.Basic,

    initPage: function(el) {
        this.wrapper = $(el);
		this.content = $('.content', this.wrapper);
		var self = this;

		this.meta.fetchResultsUrl = this.meta.listUrl;

		DeskPRO_Window.getMessageBroker().sendMessage('twitter-section.list-activated', {
			listUrl: this.meta.listUrl
		});

		var helper = new DeskPRO.Agent.PageHelper.Twitter(this.content, this, {
			messageUrl: this.getMetaData('saveUserMessageUrl'),
			userArchiveHideCallback: function(row) {
				var pageHelper = self.resultsHelper,
					page = pageHelper.getCurrentPage(),
					numPages = pageHelper.getNumPages();

				pageHelper.adjustResultCount(-1);

				if (page < numPages) {
					var data = {};
					data.last = 1;
					data.page = page;

					setTimeout(function() {
						$.ajax({
							url: self.getMetaData('listUrl'),
							dataType: 'html',
							data: data,
							success: function(html) {
								var $html = $(html);
								self.content.find('.followers-list').append($html);
							}
						});
					}, 200);
				} else if (pageHelper.resultCount <= 0) {
					self.wrapper.find('.list-listing.no-results').show();
					self.wrapper.find('.results-nav').hide();
				}
			}
		});

		this.content.find('textarea').TextAreaExpander();

		var opt = {
			perPage: this.meta.perPage || 25,
			currentPage: this.meta.currentPage,
			totalCount: this.meta.totalCount,
			resultRowSelector: 'article.twitter-user',
			resultsContainer: this.content
		};
		this.resultsHelper = new DeskPRO.Agent.PageHelper.Results(this, opt);
		this.ownObject(this.resultsHelper);
    }

});
