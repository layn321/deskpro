Orb.createNamespace('DeskPRO.Agent.PageFragment.Page.Content');

/**
 * Management of participants in the ticket
 */
DeskPRO.Agent.PageFragment.Page.Content.StickyWords = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(page, options) {
		var self = this;

		this.page = page;

		this.options = {
			saveUrl: null,

			/**
			 * The type of object editing
			 */
			contentType: '',

			/**
			 * The ID of the content
			 */
			contentId: 0,

			/**
			 * The wrapper where tagit will be applied
			 */
			element: null
		};

		this.setOptions(options);

		if (!this.options.saveUrl) {
			this.options.saveUrl = BASE_URL + 'agent/publish/save-sticky-search-words/' + this.options.contentType + '/' + this.options.contentId;
		}

		this.termsInput = new DeskPRO.UI.LabelsInput({
			input: this.options.element,
			placeholder: 'Enter a search word...'
		});
		this.termsInput.addEvent('change', function() {
			self._updated();
		});
	},

	_updated: function() {

		var labels = this.termsInput.getLabels();
		var ev = {
			labels: labels,
			cancel: false
		};
		this.fireEvent('change', [ev]);

		if (ev.cancel) {
			return;
		}

		var data = [];
		Array.each(labels, function(w) {
			data.push({
				name: 'words[]',
				value: w
			});
		});

		$.ajax({
			url: this.options.saveUrl,
			data: data,
			type: 'POST'
		});
	}
});
