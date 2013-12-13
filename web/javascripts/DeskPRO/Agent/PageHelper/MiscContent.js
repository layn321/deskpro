Orb.createNamespace('DeskPRO.Agent.PageHelper');

DeskPRO.Agent.PageHelper.MiscContent = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(page, options)  {
		var self = this;
		this.page = page;

		this.options = {
			revisionCompareUrl: ''
		};

		this.setOptions(options);

		this.wrapper = this.page.wrapper;
		this.getEl   = this.page.getEl;

		this._initCompareRevs();
	},

	//#################################################################
	//# Compare revisions
	//#################################################################

	_initCompareRevs: function() {
		$('.compare-trigger', this.wrapper).on('click', this.showCompareRev.bind(this));

		var all_checks = $('input.rev-compare-check', this.wrapper);
		var counter = 0;

		var table = this.wrapper.find('.revision-compare-table');
		table.on('click', 'input.rev-compare-check', function() {
			if ($(this).is(':checked')) {
				var checked = all_checks.filter(':checked');
				if (checked.length > 2) {
					checked.each(function() {
						if ($(this).data('check-count') == counter) {
							$(this).prop('checked', false);
						}
					});
				}

				$(this).data('check-count', ++counter);
			}
		});

		var count = table.find('input.rev-compare-check').length;
		this.page.getEl('count_revs').text(count);
	},

	showCompareRev: function() {

		var checks = $('.revision-compare-table input.rev-compare-check:checked', this.wrapper);

		var old_id = checks.first().val();
		var new_id = checks.last().val();

		if (!old_id || !new_id || old_id == new_id) {
			console.log('bad compare');
			return;
		}

		var overlay = new DeskPRO.UI.Overlay({
			triggerElement: $('button.compare-trigger', this.wrapper),
			contentMethod: 'ajax',
			contentAjax: {
				url: this.options.revisionCompareUrl.replace('{OLD}', old_id).replace('{NEW}', new_id)
			},
			destroyOnClose: true
		});

		overlay.openOverlay();
	}
});
