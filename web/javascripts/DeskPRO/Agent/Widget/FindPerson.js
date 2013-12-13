Orb.createNamespace('DeskPRO.Agent.Widget');

DeskPRO.Agent.Widget.FindPerson = new Orb.Class({

	Implements: [Orb.Util.Options, Orb.Util.Events],

	initialize: function(options) {
		this.options = {

		};

		this.setOptions(options);
	},

	_initOverlay: function() {
		if (this.overlay) return this.overlay;

		this.overlay = new DeskPRO.UI.Overlay({
			contentMethod: 'ajax',
			contentAjax: {
				url: BASE_URL + 'agent/people-search/quick-find'
			}
		});

		this.overlay.addEvent('ajaxDone', this._initElements, this);

		var event = {
			findPerson: this,
			overlay: this.overlay
		};

		this.fireEvent('initOverlay', [event]);
	},

	_initElements: function() {
		this.wrapper = this.overlay.getWrapper();

		this.headerNav   = $('header > nav', this.wrapper);
		this.simpleBtn   = $('.simple', this.headerNav).on('click', this.switchSimple.bind(this));
		this.advancedBtn = $('.advanced', this.headerNav).on('click', this.switchAdvanced.bind(this));

		this.results = $('section.deskpro-results-list', this.wrapper);
		this.loading = $('section.results-loading', this.wrapper);
		this.info = $('section.no-results-info', this.wrapper);

		this.searchArea   = $('section.search-area:first', this.wrapper);
		this.simpleForm   = $('form.simple:first', this.searchArea);
		this.advancedForm = $('form.advanced:first', this.searchArea);

		var self = this;
		this.simpleForm.on('submit', function(ev) {
			ev.preventDefault();
			self.submitSearch($(this));
		});
		this.advancedForm.on('submit', function(ev) {
			ev.preventDefault();
			self.submitSearch($(this));
		});
	},

	_initRuleBuilder: function() {

		if (this.ruleBuilder) return;

		var criteriaList  = $('.search-form', this.advancedForm);
		var criteriaTerms = $('.search-builder-tpl', this.wrapper);

		var editor = new DeskPRO.Form.RuleBuilder(criteriaTerms);
		$('.add-term', criteriaList).data('add-count', 0).on('click', function() {
			var count = parseInt($(this).data('add-count'));
			var basename = 'terms['+count+']';

			$(this).data('add-count', count+1);

			editor.addNewRow($('.search-terms', criteriaList), basename);
		});

		this.ruleBuilder = editor;
	},

	open: function() {
		this._initOverlay();
		this.overlay.open();
	},

	close: function() {
		this.overlay.close();
	},

	switchDisplayElement: function(el, subEl) {
		if (el == 'results') {
			this.results.show();
			this.loading.hide();
			this.info.hide();
		} else if (el == 'info') {
			this.info.show();
			$('> *', this.info).hide();
			$(subEl, this.info).show();

			this.results.hide();
			this.loading.hide();
		} else if (el == 'loading') {
			this.loading.show();
			this.results.hide();
			this.info.hide();
		}
	},

	switchSimple: function() {
		if (this.simpleBtn.is('.on')) return;

		this.switchDisplayElement('info', 'no-search');

		$('.on', this.headerNav).removeClass('on');
		this.simpleBtn.addClass('on');

		this.advancedForm.slideUp();
		this.simpleForm.slideDown();
	},

	switchAdvanced: function() {
		if (this.advancedBtn.is('.on')) return;

		this._initRuleBuilder();
		this.switchDisplayElement('info', 'no-search');

		$('.on', this.headerNav).removeClass('on');
		this.advancedBtn.addClass('on');

		this.simpleForm.slideUp();
		this.advancedForm.slideDown();
	},

	submitSearch: function(form) {
		var formData = form.serializeArray();

		this.switchDisplayElement('loading');

		$.ajax({
			url: BASE_URL + 'agent/people-search/quick-find-search.json',
			data: formData,
			dataType: 'json',
			type: 'POST',
			context: this,
			success: this.handleSearchSuccess
		});
	},

	handleSearchSuccess: function(data) {
		if (data.no_results) {
			this.switchDisplayElement('info', 'no-results');
			return;
		}

		this.results.empty().html(data.html);
		this._initNewResults();

		this.switchDisplayElement('results');
	},

	_initNewResults: function() {
		var self = this;
		$('.choose-trigger', this.results).on('click', function(ev) {

			ev.preventDefault();

			var el = $(this);
			var personId = el.data('person-id');

			var eventData = {
				el: el,
				personId: personId,
				event: ev,
				doCloseOverlay: true
			};

			self.fireEvent('choosePerson', [eventData]);

			if (eventData.doCloseOverlay) {
				self.close();
			}
		});
	},

	destroy: function() {
		if (this.overlay) {
			this.overlay.destroy();
		}

		if (this.ruleBuilder) {
			this.ruleBuilder.destroy();
		}
	}
});
