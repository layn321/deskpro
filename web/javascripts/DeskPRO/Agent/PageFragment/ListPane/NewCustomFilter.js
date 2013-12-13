Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.NewCustomFilter = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.Basic,

	initializeProperties: function() {
		this.parent();
		this.wrapper = null;
		this.filterSearchForm = null;
	},

	initPage: function(el) {
		this.wrapper = el;

		this.topSection = $('.list-top-area', this.wrapper);

		var criteriaList  = $('.search-form', this.topSection);
		var criteriaTerms = $('.search-builder-tpl', this.topSection);

		var editor = new DeskPRO.Form.RuleBuilder(criteriaTerms);
		$('.add-term', criteriaList).data('add-count', 0).on('click', function() {
			var count = parseInt($(this).data('add-count'));
			var basename = 'terms['+count+']';

			$(this).data('add-count', count+1);

			editor.addNewRow($('.search-terms', criteriaList), basename);
		});

		var searchDataEl = $('.search-form-data:first', this.topSection);
		if (searchDataEl.length) {
			var searchData = searchDataEl.get(0).innerHTML;
			searchData = $.parseJSON(searchData);

			if (searchData.terms) {
				Array.each(searchData.terms, function(info, x) {
					var basename = 'terms[initial_' + x + ']';
					editor.addNewRow($('.search-terms', criteriaList), basename, {
						type: info.type,
						op: info.op,
						options: info.options
					});
				});
			}

			if (searchData.order_by) {
				$('[name="order_by"]', this.topSection).val(searchData.order_by);
			}

			searchDataEl.remove();
		}

		var form = $('form.ticket-search-form', this.topSection);
		form.on('submit', function(ev) {
			ev.preventDefault();

			var url = form.attr('action');
			var data = form.serializeArray();

			DeskPRO_Window.loadListPane(url, { postData: data });
		});
	}
});
