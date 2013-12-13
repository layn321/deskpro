Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.TicketCustomFilterForm = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.Basic,

	initPage: function(el) {

		this.wrapper = $(el);

		this._initBasic();
		this._initFilterForm();

		if (this.getMetaData('autorun')) {
			this.submitForm();
		}
	},

	destroyPage: function() {

	},

	_initBasic: function() {
		var self = this;
		$('> .summary > .toggle', this.wrapper).on('click', function() {
			$('> .summary', self.wrapper).hide();
			$('> .criteria', self.wrapper).slideDown();
		});
	},

	_initFilterForm: function() {
		var self = this;

		var editor = new DeskPRO.Form.RuleBuilder($('.search-tpl', this.wrapper));
		editor.addEvent('newRow', function(new_row) {
			$('.remove', new_row).on('click', function() {
				new_row.remove();
			});
		});
		$('.search-form .add-term').data('add-count', 0).on('click', function() {
			var count = parseInt($(this).data('add-count'));
			var basename = 'terms['+count+']';

			$(this).data('add-count', count+1);

			editor.addNewRow($('.search-form .search-terms', self.wrapper), basename);
		});

		var self = this;
		$('button.run-filter-trigger', this.wrapper).on('click', function() {
			self.submitForm();
		});

		if (this.getMetaData('preselectTerms')) {
			var count = 0;
			var preselectTerms = this.getMetaData('preselectTerms');
			for (var i = 0; i < preselectTerms.length; i++) {
				if (!preselectTerms[i]) continue;

				editor.addNewRow(
					$('.search-form .search-terms', self.wrapper),
					'terms['+count+']',
					preselectTerms[i]
				);
			}

			$('.search-form .add-term', this.wrapper).data('add-count', count);
		}
	},

	submitForm: function() {

		var data = $('form.search-form-data', this.wrapper).serializeArray();

		$.ajax({
			cache: false,
			type: 'POST',
			data: data,
			url: this.getMetaData('formSubmitUrl'),
			context: this,
			dataType: 'html',
			success: function (data) {
				$(' .criteria', this.wrapper).hide();
				$('.summary', this.wrapper).show();
				this._handleAjaxResults(data);
			}
		});
	},

	_handleAjaxResults: function(data) {

		DeskPRO_Window.removePage(this);

		var page = DeskPRO_Window.createPageFragment(data);
		DeskPRO_Window.addListPage(page);
	}
});
