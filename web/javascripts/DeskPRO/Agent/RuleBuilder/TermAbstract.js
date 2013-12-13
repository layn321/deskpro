Orb.createNamespace('DeskPRO.Agent.RuleBuilder');

/**
 * A limited verso
 */
DeskPRO.Agent.RuleBuilder.TermAbstract = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(options) {
		this.options = {
			ruleBuilder: null,
			rowEl: null,
			rowId: null,
			opMenu: null
		};

		if (options) this.setOptions(options);

		this.ruleBuilder = this.options.ruleBuilder;
		this.rowEl = $(this.options.rowEl);
		this.rowId = this.options.rowId;
		this.opMenu = this.options.opMenu;

		this.init();
	},

	init: function() { },

	initRow: function() { },

	initValues: function() { }
});