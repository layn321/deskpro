Orb.createNamespace('DeskPRO.Agent.PageHelper');

/**
 * Given a chat, runs through rules to fetch which fields should be displayed
 */
DeskPRO.Agent.PageHelper.ChatFieldDisplay = new Orb.Class({

	initialize: function(chatReader, mode) {
		this.chatReader = chatReader;
		this.mode = mode || 'create';
	},

	getFields: function(department_id) {
		department_id = parseInt(department_id);
		DP.console.log('[ChatFieldDisplay] department %i', department_id);

		var depItems = [];
		if (window.DESKPRO_CHAT_DISPLAY && window.DESKPRO_CHAT_DISPLAY[this.mode]) {

			if (this.mode == 'view') {
				if (typeof window.DESKPRO_CHAT_DISPLAY[this.mode][department_id] == 'undefined' && typeof window.DESKPRO_CHAT_DISPLAY[this.mode][department_id] == 'undefined') {
					DP.console.log('[ChatFieldDisplay] Dynamic switch mode to create');
					this.mode = 'create';
				}
			}

			if (typeof window.DESKPRO_CHAT_DISPLAY[this.mode][department_id] == 'undefined') {
				DP.console.log('[ChatFieldDisplay] Dynamic switch to dep 0');
				depItems = window.DESKPRO_CHAT_DISPLAY[this.mode][0] || [];
			} else {
				DP.console.log('[ChatFieldDisplay] Using dep');
				depItems = window.DESKPRO_CHAT_DISPLAY[this.mode][department_id] || [];
			}
		}

		DP.console.log('[ChatFieldDisplay] depItems %o', depItems);

		var items = this.runRules(depItems);
		DP.console.log('[ChatFieldDisplay] items %o', items);

		return items;
	},


	/**
	 * Run through all the rules and show/hide all display items and
	 * sections based on it.
	 */
	runRules: function(depItems) {

		var items = {};

		//------------------------------
		// Run all the rules to fetch on/off of each item in display
		//------------------------------

		Array.each(depItems, function(item) {
			if (!items[item.section]) {
				items[item.section] = [];
			}

			switch (item.section) {
				case 'default':
					var state = this.runCheckForItem(item);
					if (state) {
						items[item.section].push(item);
					}
					break;
			}

		}, this);

		return items;
	},

	/**
	 * Runs the check function for an item to get its visibility.
	 *
	 * @param item
	 */
	runCheckForItem: function(item) {
		return true;
	}
});
