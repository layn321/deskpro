Orb.createNamespace('DeskPRO.Agent.ElementHandler');

DeskPRO.Agent.ElementHandler.TwitterFeed = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		this.twitterUsername = this.el.data('twitter-username');
		this.tpl = DeskPRO_Window.util.getPlainTpl($('.twitter-list-item-tpl', this.el));
		this.list = $('.twitter-list', this.el);
		this.limit = parseInt(this.el.data('tweet-limit')) || 5;
	},

	initPage: function() {
		var ify = function() {
			return {
			  entities: function (t) {
				return t.replace(/(&[a-z0-9]+;)/g, function (m) {
				  return ENTITIES[m];
				});
			  },
			  link: function(t) {
				return t.replace(/[a-z]+:\/\/([a-z0-9-_]+\.[a-z0-9-_:~\+#%&\?\/.=]+[^:\.,\)\s*$])/ig, function(m, link) {
				  return '<a title="' + m + '" href="' + m + '">' + ((link.length > 36) ? link.substr(0, 35) + '&hellip;' : link) + '</a>';
				});
			  },
			  at: function(t) {
				return t.replace(/(^|[^\w]+)\@([a-zA-Z0-9_]{1,15}(\/[a-zA-Z0-9-_]+)*)/g, function(m, m1, m2) {
				  return m1 + '@<a href="http://twitter.com/' + m2 + '">' + m2 + '</a>';
				});
			  },
			  hash: function(t) {
				return t.replace(/(^|[^&\w'"]+)\#([a-zA-Z0-9_^"^<]+)/g, function(m, m1, m2) {
				  return m.substr(-1) === '"' || m.substr(-1) == '<' ? m : m1 + '#<a href="http://search.twitter.com/search?q=%23' + m2 + '">' + m2 + '</a>';
				});
			  },
			  clean: function(tweet) {
				return this.hash(this.at(this.link(tweet)));
			  }
			};
		}();

		$.ajax({
			url: 'http://api.twitter.com/1/statuses/user_timeline.json?screen_name=' + this.twitterUsername,
			dataType: 'jsonp',
			context: this,
			success: function(data) {
				Array.each(data, function(tweet, index) {
					if (index > this.limit) {
						return false;
					}

					var item = $(this.tpl);
					var text = tweet.text;
					text = ify.clean(text);

					$('.tweet', item).html(text);

					this.list.append(item);

					this.el.show();
				}, this);
			}
		});
	}
});
