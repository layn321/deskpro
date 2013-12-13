Orb.createNamespace('DeskPRO.User.ElementHandler');

DeskPRO.User.ElementHandler.MoreLoader = new Orb.Class({

	Extends: DeskPRO.User.ElementHandler.ElementHandlerAbstract,

	init: function() {

		this.listEl   = $('.content-list:first ul:first', this.el);
		this.moreWrap = $('.content-more:first', this.el);
		this.moreBtn  = $('button:first, a.button:first', this.el).first();

		this.loadUrl = this.el.data('load-url');

		this.currentPage = 1;

		var self = this;
		this.moreBtn.on('click', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();
			self.loadNextPage();
		});
	},

	loadNextPage: function() {

		if (this.moreWrap.is('.loading')) {
			return;
		}

		this.moreWrap.addClass('loading');

		var url = this.loadUrl.replace('{page}', this.currentPage + 1);

		if (url.indexOf('?') === -1) {
			url += '?_partial=more';
		} else {
			url += '&_partial=more';
		}

		$.ajax({
			url: url,
			dataType: 'html',
			context: this,
			success: function(html) {
				this.insertNewItems(html.trim());
			}
		});
	},

	insertNewItems: function(html) {
		if (html.length) {
			var els = $(html);
		} else {
			var els = null;
		}

		this.currentPage++;

		this.moreWrap.removeClass('loading');
		if (!els || !els.length || !els.last().is('.has-more')) {
			this.moreWrap.remove();
		}

		this.listEl.append(els);
	}
});
