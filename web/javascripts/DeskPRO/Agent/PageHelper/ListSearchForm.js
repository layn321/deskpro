Orb.createNamespace('DeskPRO.Agent.PageHelper');

DeskPRO.Agent.PageHelper.ListSearchForm = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(page, options) {
		this.page = page;

		this.options = {
			form: null,
			context: null,
			searchData: null
		};

		this.setOptions(options);

		this.form = this.options.form;
		this.topSection = this.options.context;

		this._initSearchOptions();
	},

	_initSearchOptions: function() {
		var editBtn = $('.summary .edit', this.topSection);
		editBtn.on('click', this.showSearchForm.bind(this));

		var form = this.form;
		var self = this;
		form.on('submit', function(ev) {
			ev.preventDefault();

			var url = form.attr('action');
			var data = form.serializeArray();

			self.fireEvent('searchSubmit', [url, data]);
		});
	},

	showSearchForm: function() {
		var criteriaList  = $('.search-form', this.topSection);
		var criteriaTerms = $('.search-builder-tpl', this.topSection);

		var editor = new DeskPRO.Form.RuleBuilder(criteriaTerms);
		$('.add-term', criteriaList).data('add-count', 0).on('click', function() {
			var count = parseInt($(this).data('add-count'));
			var basename = 'terms['+count+']';

			$(this).data('add-count', count+1);

			editor.addNewRow($('.search-terms', criteriaList), basename);
		});

		var searchDataEl = this.searchData;
		if (searchDataEl && searchDataEl.length) {
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
			searchDataEl.remove();
		}

		$('.summary', this.topSection).slideUp();
		$('.form-panel', this.topSection).slideDown();
	}
});
