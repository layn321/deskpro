Orb.createNamespace('DeskPRO.UI');

/**
 * Optionbox that remembers the selected options when it was opened, so you can
 * revert back to them
 */
DeskPRO.UI.OptionBoxRevertable = new Orb.Class({
	Extends: DeskPRO.UI.OptionBox,

	initialize: function(options) {
		var self = this;
		options = options || {};
		options.liNoClickClose = true;
		this.parent(options);

		this.addEvent('preOpen', this.handlePreOpen, this);
		this.addEvent('close', this.handleClose, this);

		this.hasApplied = false;

		$('.save-trigger', this.el).on('click', function(ev) {
			self.hasApplied = true;

			self.fireEvent('save', [self]);

			self.close();
		});
	},

	handlePreOpen: function() {
		this.preSelections = $('li.on', this.el);
	},

	revertOptions: function() {
		$('li', this.el).removeClass('on');
		this.preSelections.each(function() {
			$(this).addClass('on');
			$(':radio, :checkbox', this).prop('checked', true);
		});
	},

	handleClose: function() {
		if (!this.hasApplied && this.preSelections) {
			this.revertOptions();
		}

		this.hasApplied = false;
		this.preSelections = null;
	}
});
