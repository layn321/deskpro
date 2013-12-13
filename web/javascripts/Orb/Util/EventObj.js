Orb.createNamespace('Orb.Util');

Orb.Util.EventObj = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(options) {
		this.setOptions(options);
	}
});
