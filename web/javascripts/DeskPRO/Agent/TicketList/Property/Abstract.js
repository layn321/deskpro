Orb.createNamespace('DeskPRO.Agent.TicketList.Property');

/**
 * Something that can be changed from the ticket listing page.
 * Unlike the Ticket properties, these only care about changing
 * values.
 */
DeskPRO.Agent.TicketList.Property.Abstract = new Class({

	Implements: [Events, Options],

	displayNameType: null,
	displayCaption: null,

	options: {},
	ticketPage: null,
	ticketId: null,

	/**
	 * @param {DeskPRO.Agent.PageFragment.Page.Ticket} ticketPage
	 * @param {Integer} ticketId
	 * @param {Object} options
	 */
	initialize: function(ticketPage, ticketId, options) {

		if (options) this.setOptions(options);

		this.ticketPage = ticketPage;
		this.ticketId = ticketId;

		this.init();
	},

	init: function() {},

	/**
	 * Name for the property
	 *
	 * @return {String}
	 */
	getName: function() {
		// override
	},

	getTicketId: function() {
		return this.ticketId;
	},

	isSameValue: function(compare) {
		if (this.getValue() == compare) {
			return true;
		}

		return false;
	},

	getValue: function() {
		// override
	},

	/**
	 * Sets a new value. Must also update the UI if needed.
	 *
	 * @param mixed value
	 */
	setValue: function(value) {
		// override
	},



	/**
	 * Sets data that we got from the server.
	 */
	setIncomingValue: function(value) {
		this.setValue(value);
	},



	/**
	 * Get the UI element used to display the property.
	 */
	getInterfaceElement: function() {
		if (this._interfaceEl !== null) return this._interfaceEl;

		this._interfaceEl = this._getInterfaceElement();

		return this._interfaceEl;
	},

	_interfaceEl: null,
	_getInterfaceElement: function() {
		// override
	},



	/**
	 * Helps build a selector to for getInterfaceElement for all the ticket
	 * ids this should affect.
	 */
	_buildSelector: function(base_sel) {
		sel = 'tr.ticket-' + this.ticketId + ' ' + base_sel;

		return sel;
	},

	getSublineElement: function() {

		var line2 = $('tr.ticket-' + this.ticketId + '.line-2', this.ticketPage.actionsBarHelper.tableEl);
		line2.addClass('with-line-3');
		var line3 = $('tr.ticket-' + this.ticketId + '.line-3', this.ticketPage.actionsBarHelper.tableEl);
		line3.show();

		var ul = $('ul', line3);

		var li = $('<li class="generated prop-value ' + this.getName() + '"></li>');

		ul.append(li);

		return li;
	},


	/**
	 * When a property is updated automatically (not from a user action, like in the background),
	 * this pulse action is applied to highlight and fade slowly.
	 */
	pulseInterfaceElement: function() {
		this.getInterfaceElement().effect('highlight', 1200);
	},


	/**
	 * Highlight the UI element to bring attention to some change.
	 */
	highlightInterfaceElement: function() {
		this.getInterfaceElement().addClass('change-on');
	},



	/**
	 * Remove the UI highlight
	 */
	unhighlightInterfaceElement: function() {
		this.getInterfaceElement().removeClass('change-on');
	}
});