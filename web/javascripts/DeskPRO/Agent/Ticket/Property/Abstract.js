Orb.createNamespace('DeskPRO.Agent.Ticket.Property');

/**
 * Something that can be fetched or set on the ticket page. These are
 * managers that handle UI changes and getting and setting new values.
 */
DeskPRO.Agent.Ticket.Property.Abstract = new Class({

	Implements: [Events, Options],

	options: {},
	ticketPage: null,

	/**
	 * @param {DeskPRO.Agent.PageFragment.Page.Ticket} ticketPage
	 * @param {Object} options
	 */
	initialize: function(ticketPage, options) {

		if (options) this.setOptions(options);

		this.ticketPage = ticketPage;

		this.init();
	},

	init: function() {},

	/**
	 * Name for the property
	 *
	 * @return {String}
	 */
	getName: function() {

	},



	/**
	 * Used to compare a new value to the current value to see if
	 * we really need to change anything.
	 *
	 * @return {Boolean}
	 */
	isSameValue: function(compare) {
		if (this.getValue() == compare) {
			return true;
		}

		return false;
	},



	/**
	 * Gets the currently set value
	 *
	 * @return mixed
	 */
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
	 * Sets data that we got from the server. This is usually the same
	 * as setValue(), but it might be like a new reply or osmething like that.
	 */
	setIncomingValue: function(value) {
		this.setValue(value);
	},



	/**
	 * Get the UI element used to display the property.
	 */
	getInterfaceElement: function() {
		return this._getInterfaceElement();
	},

	_interfaceEl: null,
	_getInterfaceElement: function() {
		// override
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
		var i = this.getInterfaceElement();

		if (!i || !i.length) {
			return;
		}

		this.getInterfaceElement().addClass('change-on');

		var displayItemWrap = i.parentsUntil(null, '.display-item');
		if (displayItemWrap.length) {
			displayItemWrap.addClass('highlight-change-on');
		} else {
			i.addClass('change-on');
		}
	},

	changePersisted: function() {

	},

	changeReverted: function() {

	},

	/**
	 * Remove the UI highlight
	 */
	unhighlightInterfaceElement: function() {
		this.getInterfaceElement().removeClass('change-on');
	},

	isDisplayOnly: function() {
		return false;
	},

	isAdditionOnly: function() {
		return false;
	}
});