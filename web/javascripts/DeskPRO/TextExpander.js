Orb.createNamespace('DeskPRO');

DeskPRO.TextExpander = new Orb.Class({

	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(options) {
		this.options = {
			textarea: null
		};

		this.setOptions(options);

		this.comboString = null;
		this.$txt = $(this.options.textarea);

		var self = this;
		this.$txt.on('keypress', function(ev) {
			// % key
			if (ev.which == 37) {
				if (!self.comboString) {
					self.comboString = '%';
				} else {
					var combo = self.comboString + '%';
					self.comboString = null;
					self.fireEvent('combo', [combo, ev]);
				}

			} else if (ev.which == 8) {
				// nothing
				// some browsers like (firefox) pass backspace event
				// into keypress, while others (webkit, ie) do not

			// Other input keys after 'start'
			// of combo string
			} else if (self.comboString) {
				var chr = String.fromCharCode(ev.which);
				if (chr.match(/[a-zA-Z0-9:\.\-_]/)) {
					self.comboString += chr;
				} else {
					self.comboString = null;
				}
			} else {
				self.comboString = null;
			}
		});

		// Handle backspace
		this.$txt.on('keyup', function(ev) {
			if (self.comboString && ev.which == 8) {
				self.comboString = self.comboString.substring(0, self.comboString.length-1);
			}
		});
	}
});
