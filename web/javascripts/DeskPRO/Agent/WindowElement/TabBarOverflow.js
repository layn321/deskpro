Orb.createNamespace('DeskPRO.Agent.WindowElement');

/**
 * The tabbar handles adding and removing tabs in the right pane of the window.
 */
DeskPRO.Agent.WindowElement.TabBarOverflow = new Orb.Class({
	initialize: function() {
		var self = this;
		this.tabList     = $('#tabNavigationPane ul.dp-tab-list');
		this.tabPane     = $('#tabNavigationPane');
		this.goLeft      = $('#tabNavSelectorLeft');
		this.goRight     = $('#tabNavSelectorRight');
		this.menuBtn     = $('#tabDropdownPicker');
		this.scrollable  = $('#tabNavigationPane > .deskproTabList');

		// Padding to the left side of the tabs
		this.padLeft = 10;

		// Padding to the left side when theres the nav control
		// This is room so you see the control and it doesnt overlap the tab
		this.padLeftCtrl = 10;

		this.padRight = 0;
		this.padRightCtrl = 10;

		this.goLeft.on('click', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			self.scrollLeft();
		});
		this.goRight.on('click', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			self.scrollRight();
		});

		this.overflowEnabled = false;
	},


	/**
	 * Update the overflow
	 */
	update: function() {
		if (this.isOverflowRequired()) {
			this.enableOverflow();
		} else {
			this.disableOverflow();
		}
	},

	/**
	 *
	 */
	getBounds: function() {
		var left = -this.tabList.parent().position().left;

		// Make sure tab edges hidden under the button are taken into account.
		if(this.tabPane.is('with-leftbar')) {
			left += 18;
		}

		var right = left + this.tabPane.width();

		// Again, don't hide tab edges under the scroll button.
		if(this.tabPane.is('with-rightbar')) {
			right -= 18;
		}
		console.log('left: ' + left + ' right: ' + right);
		return {'left': left, 'right': right};
	},

	/**
	 * Check if overflow controls are required right now
	 */
	isOverflowRequired: function() {
		var paneW = this.tabPane.width();
		var tabW = this.tabList.width() + this.padLeft + this.padRightCtrl;

		if (tabW > paneW) {
			return true;
		} else {
			return false;
		}
	},

	/**
	 * Enable the overflow controls
	 */
	enableOverflow: function() {
		this.tabPane.addClass('with-overflow with-rightbar');
		this.maxScroll = this.tabList.width() - this.tabPane.width();
		this.maxScroll += 9 + this.padLeft + this.padRightCtrl;

		if (this.overflowEnabled) {
			// If its already enabled then we should check
			// if a tab was removed, so we dont have empty space to the right
			if (this.scrollable.scrollLeft() > this.maxScroll) {
				this.scrollable.scrollLeft(this.maxScroll);
			}
		}

		this.overflowEnabled = true;
	},

	/**
	 *
	 */
	disableOverflow: function() {
		this.scrollable.scrollLeft(0);
		this.tabPane.removeClass('with-overflow');
		this.overflowEnabled = false;
	},

	/**
	 * Scrolls all the way to the left.
	 *
	 * @param amount
	 */
	resetScroll: function() {
		if(this.overflowEnabled) {
			this.scrollable.scrollLeft(0);
			this.tabPane.removeClass('with-leftbar');
		}
	},

	/**
	 * Scroll to the left
	 *
	 * @param {Integer} [amount]
	 */
	scrollLeft: function(amount) {
		var self = this;
		if (!amount) amount = 200;

		self.tabPane.addClass('with-rightbar');

		this.scrollable.animate({scrollLeft: '-=' + amount }, { duration: 200, complete: function() {
			var scrollPos = self.scrollable.scrollLeft();
			if (scrollPos <= 0) {
				self.tabPane.removeClass('with-leftbar');
			} else {
				self.tabPane.addClass('with-leftbar');
			}
		} });
	},


	/**
	 * Scroll to the left
	 *
	 * @param {Integer} [amount]
	 */
	scrollRight: function(amount) {
		var self = this;
		if (!amount) amount = 200;

		var current = this.scrollable.scrollLeft();
		if ((current+amount) > this.maxScroll) {
			amount = this.maxScroll - current;
		}

		self.tabPane.addClass('with-leftbar');

		this.scrollable.animate({scrollLeft: '+=' + amount }, { duration: 200, complete: function() {
			var scrollPos = self.scrollable.scrollLeft();
			if (scrollPos >= self.maxScroll) {
				self.tabPane.removeClass('with-rightbar');
			} else {
				self.tabPane.addClass('with-rightbar');
			}
		} });
	}
});
