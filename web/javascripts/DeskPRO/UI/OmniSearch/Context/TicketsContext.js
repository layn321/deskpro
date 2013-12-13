Orb.createNamespace('DeskPRO.UI.OmniSearch.Context');

/**
 * A context is a group of search terms
 */
DeskPRO.UI.OmniSearch.Context.TicketsContext = new Orb.Class({
	Extends: DeskPRO.UI.OmniSearch.Context.ContextAbstract,

	init: function() {

	},

	getLabel: function() {
		return 'Tickets';
	}
});