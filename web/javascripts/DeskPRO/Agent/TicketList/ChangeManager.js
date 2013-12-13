Orb.createNamespace('DeskPRO.Agent.TicketList');

/**
 * Handles changes to many tickets on a ticket page. Somewhat same feedback as with the Ticket.ChangeManager,
 * except we work with many tickets at a time, and we only care about updating the UI and not about
 * getting current values.
 */
DeskPRO.Agent.TicketList.ChangeManager = new Class({

	Implements: [Events],

	ticketPage: null,

	hasChanges: false,
	changes: {}, // changes are { ticketId: [ [property, newValue, hasApplied] ] }

	ticketIdsBatch: null,

	/**
	 * @param {DeskPRO.Agent.PageFragment.Page.BasicTicketResults} ticketPage
	 */
	initialize: function(ticketPage) {
		this.ticketPage = ticketPage;
	},



	/**
	 * Begin a batch of changes to selected tickets
	 *
	 * @param {Array} ticketIds
	 */
	begin: function(ticketIds) {
		this.ticketIdsBatch = ticketIds;
	},



	/**
	 * Add a change to the set of changes. This applies a change to all tickets.
	 */
	addChange: function(property, newValue) {

		var name = property.getName();
		var id = property.getTicketId();

		// Dont care if its the same!
		if (property.isSameValue(newValue)) {
			return;
		}

		if (!this.changes[id]) this.changes[id] = [];
		var info = {'property': property, 'newValue': newValue, 'hasApplied': false};
		this.changes[id].push(info);

		this.hasChanges = true;
	},



	/**
	 * Apply a certain new value in the interface
	 */
	applyChangeForEntry: function (info) {

		var property = info.property;
		var name = property.getName();
		var id = property.getTicketId();

		info.oldValue = property.getValue();
		property.setValue(info.newValue);

		property.highlightInterfaceElement();

		info.hasApplied = true;
	},



	/**
	 * Apply all queued changes in the interface
	 */
	applyChanges: function() {

		if (!this.ticketIdsBatch) return;

		$('tr:not(.on, .line-3)', this.ticketPage.contentWrapper).addClass('faded');
		$('tr.on').removeClass('faded');
		$('table:first', this.ticketPage.contentWrapper).addClass('preview-mode');

		Array.each(this.ticketIdsBatch, function (ticketId) {
			Array.each(this.changes[ticketId], function (change) {
				this.applyChangeForEntry(change);
			}, this);
		}, this);

		this.ticketIdsBatch = null;
	},

	revertChangeForEntry: function (info) {
		var property = info.property;
		if (info.hasApplied && info.oldValue !== undefined) {
			property.setValue(info.oldValue);
			property.unhighlightInterfaceElement();
		}
	},

	/**
	 * Revert all queuued changes in the interface to their previuos values
	 */
	revertChanges: function() {

		Object.each(this.changes, function (changes, ticketId) {
			Array.each(changes, function (change) {
				this.revertChangeForEntry(change);
			}, this);
		}, this);

		this.ticketIdsBatch = null;
		this.changes = {};
		this.hasChanges = false;

		this.fireEvent('changesCleared');
	},



	revertChangesForTicketId: function(ticketId) {
		if (!this.changes[ticketId]) return;

		Array.each(this.changes[ticketId], function (change) {
			this.revertChangeForEntry(change);
		}, this);

		var lines = $('tr.ticket-' + ticketId, this.ticketPage.contentWrapper);
		lines.filter('tr.line-3').hide().find('td > ul').html('')
		lines.filter(':not(.line-3)').addClass('faded').removeClass('with-line-3');

		delete this.changes[ticketId];

		if (Object.getLength(this.changes) == 0) {
			this.revertChanges();
		}
	},


	/**
	 * Just updates the UI to show we accepted the changes
	 */
	commitChanges: function() {
		Array.each(Object.values(this.changes), function (changes) {
			Object.each(changes, function(change) {
				var property = change.property;
				property.unhighlightInterfaceElement();
			}, this);
		}, this);

		this.ticketIdsBatch = null;
		this.changes = {};
		this.hasChanges = false;

		this.fireEvent('changesCleared');
	},



	/**
	 * Called when we detect if a value was updated automatically from somewhere.
	 */
	setPropertyUpdated: function(property, newValue) {
		if (typeOf(property) == 'string') {
			property = this.ticketPage.getPropertyManager(property);
		}

		property.setIncomingValue(newValue);
		property.pulseInterfaceElement();
	}
});