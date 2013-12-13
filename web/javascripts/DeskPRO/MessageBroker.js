Orb.createNamespace('DeskPRO');

/**
 * A central way to handle and pass messages around to listeners. Glorified
 * event manager.
 *
 * <code>
 * var messageBroker = new DeskPRO.MessageBroker();
 * messageBroker.addMessageListener('example.message.*', function (data) { alert(data); });
 * messageBroker.sendMessage('example.message.test', "Hello, world!");
 * </code>
 */
DeskPRO.MessageBroker = new Orb.Class({

	Implements: [Orb.Util.Events],

	/**
	 * Send a message to all listeners.
	 *
	 * @param {String} name The message name
	 * @param {Object} data Any data to send
	 */
	sendMessage: function (name, data) {
		this.fireEvent(name, [data, name]);

		var nameparts = name.split('.');
		var cur_name = null;

		while (nameparts.pop()) {
			cur_name = nameparts.join('.') + '.*';
			this.fireEvent(cur_name, [data, cur_name]);
		}
	},

	/**
	 * Add a listener on a message.
	 *
	 * `name` should use dots to separate namespaces/groups of message types.
	 * Use an asterisk at the end of a namespace and all messages of that namespace
	 * will be sent through the same callback: example.*
	 *
	 * @param {String} name Message name
	 * @param {Function} callback Callback to execute with message
	 */
	addMessageListener: function(name, callback, context, tags) {
		this.addEvent(name, callback, context, tags);
	},



	/**
	 * Remove a listener
	 *
	 * @param {String} name Message name
	 * @param {Function} callback Callback to remove
	 */
	removeMessageListener: function (name, callback, context) {
		this.removeEvent(name, callback, context);
	},



	/**
	 * Remove all listeners tagged with a certain tag.
	 *
	 * @param tag
	 */
	removeTaggedListeners: function(tag) {
		this.removeTaggedEvents(tag);
	}
});
