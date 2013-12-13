Orb.createNamespace('DeskPRO.User.ElementHandler');

DeskPRO.User.SuggestedContentOverlay = new Orb.Class({
	Implements: [Orb.Util.Options, Orb.Util.Events],

	initialize: function(options) {
		this.options = {

		};

		this.setOptions(options);

		this.overlayEl = null;
		this.backdropEl = null;
		this.runningAjax = false;
	},
});
