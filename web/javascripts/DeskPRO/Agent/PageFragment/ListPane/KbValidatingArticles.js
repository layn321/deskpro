Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.KbValidatingArticles = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.Basic,

	initPage: function(el) {
		this.wrapper = el;

		var self = this;
		$('a.view-link.edit', this.wrapper).on('click', function(ev) {
			ev.preventDefault();
			self.loadPreviewEdit($(this).attr('href'));
		});

		$('a.view-link.article', this.wrapper).on('click', function(ev) {
			ev.preventDefault();
			self.loadPreviewArticle($(this).attr('href'));
		});
	},

	loadPreviewEdit: function(url) {

		var self = this;

		var overlay = new DeskPRO.UI.Overlay({
			destroyOnClose: true,
			contentMethod: 'ajax',
			contentAjax: {
				url: url,
				type: 'GET',
				context: this,
				dataType: 'html'
			},
			maxWidth: 500,
			maxHeight: 700,
			onContentSet: function(ev) {
				var contentEl = ev.contentEl;
				var overlay = ev.overlay;
				$('button.approve-trigger', contentEl).on('click', function(ev) {
					ev.preventDefault();
					self.approveEdit($('input.article_id', contentEl).val());
					overlay.closeOverlay();
				});
				$('button.disapprove-trigger', contentEl).on('click', function(ev) {
					ev.preventDefault();
					self.disapproveEdit($('input.article_id', contentEl).val());
					overlay.closeOverlay();
				});
			}
		});
		overlay.openOverlay();
	},

	approveEdit: function(article_id) {
		$.ajax({
			url: BASE_URL + 'agent/kb/validating-articles/validate/'+article_id+'.json',
			type: 'POST',
			context: this,
			dataType: 'json',
			success: function(info) {
				var article_id = info.article_id;
				$('.article-' + article_id, this.wrapper).remove();

				this.removeFromList(article_id, info.type);
			}
		});
	},

	disapproveEdit: function(article_id) {
		$.ajax({
			url: BASE_URL + 'agent/kb/validating-articles/disapprove/'+article_id+'.json',
			type: 'POST',
			context: this,
			dataType: 'json',
			success: function(info) {
				var article_id = info.article_id;
				$('.article-' + article_id, this.wrapper).remove();

				this.removeFromList(article_id, info.type);
			}
		});
	},

	removeFromList: function(article_id, type) {
		var count_total = $('.counter-total', this.wrapper);
		var count_type = $('.counter-' + type, this.wrapper);

		var total_int = parseInt(count_total.html());
		var type_int = parseInt(count_total.html());

		count_total.html(total_int - 1);
		count_type.html(type_int - 1);

		var wrap_type = $('.wrap-' + type, this.wrapper);
		if (!$('tr.article', wrap_type).length) {
			wrap_type.remove();
		}
	},

	loadPreviewArticle: function(url) {

		var self = this;

		var overlay = new DeskPRO.UI.Overlay({
			destroyOnClose: true,
			contentMethod: 'ajax',
			contentAjax: {
				url: url,
				type: 'GET',
				context: this,
				dataType: 'html'
			},
			maxWidth: 500,
			maxHeight: 700,
			onContentSet: function(ev) {
				var contentEl = ev.contentEl;
				var overlay = ev.overlay;
				$('button.approve-trigger', contentEl).on('click', function(ev) {
					ev.preventDefault();
					self.approveEdit($('input.article_id', contentEl).val());
					overlay.closeOverlay();
				});
				$('button.disapprove-trigger', contentEl).on('click', function(ev) {
					ev.preventDefault();
					self.disapproveEdit($('input.article_id', contentEl).val());
					overlay.closeOverlay();
				});
			}
		});
		overlay.openOverlay();
	}
});
