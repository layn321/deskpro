Orb.createNamespace('DeskPRO.Agent.PageHelper');

DeskPRO.Agent.PageHelper.AutoSave = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(options) {
		var self = this;
		this.options = {
			/**
			 * The field to attach autosave to
			 * @option {jQuery}
			 */
			field: null,

			/**
			 * The name to send in the POST request. Defaults
			 * to the name of the field.
			 * @option {String}
			 */
			fieldName: null,

			/**
			 * The URL to POST data to
			 * @option {String}
			 */
			saveUrl: null,

			/**
			 * Extra data to send
			 * @option {Array}
			 */
			postData: [],

			/**
			 * Whether to show the save puff when a save completes
			 * @option {Boolean}
			 */
			showSavePuff: true
		};

		this.setOptions(options);

		this.field = $(this.options.field);

		if (this.field.is('select, :checkbox, :radio')) {
			this.field.on('change', this.save.bind(this));
		} else {
			this.intervalCaller = new DeskPRO.IntervalCaller({
				resetTimeForce: 2500,
				timeout: 1500,
				callback: this.save.bind(this)
			});

			var touchFn = function() {
				self.fireEvent('touch', [self.field, self]);
				if (self.intervalCaller) {
					self.intervalCaller.touch();
				}
			}
			this.field.on('change', touchFn).on('keypress', touchFn);
		}
	},

	save: function() {
		var self = this;
		var postData = Array.clone(this.options.postData);

		var fieldName = this.options.fieldName || this.field.attr('name');
		var fieldVal  = this.field.val();

		postData.push({
			name: fieldName,
			value: fieldVal
		});

		self.fireEvent('preSave', [postData, self.field, self]);

		$.ajax({
			url: this.options.url,
			type: 'POST',
			data: postData,
			success: function(data) {
				self.fireEvent('save', [data, postData, self.field, self]);
				if (self.options.showSavePuff) {
					DeskPRO_Window.util.showSavePuff(self.field);
				}
			}
		});
	},

	destroy: function() {
		this.intervalCaller.destroy();
		this.field = null;
		this.options = null;
	}
});
