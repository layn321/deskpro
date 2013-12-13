Orb.createNamespace('DeskPRO.Agent.RuleBuilder');

DeskPRO.Agent.RuleBuilder.SelectNewOption = new Orb.Class({
	Extends: DeskPRO.Agent.RuleBuilder.TermAbstract,

	initRow: function() {
		var self = this;
		this.select = this.rowEl.find('select.template_name');
		this.select.on('change', function() {
			self.updateOption();
		});
		this.input = this.rowEl.find('input.new_option');

		setTimeout(function() {
			self.rowEl.find('select.template_name, select.agents').each(function() {
				DP.select($(this));
			});
		},10);

		this.updateOption();
	},

	updateOption: function() {
		var val = this.select.val();
		if (val == "NEW") {
			this.input.show();
		} else {
			this.input.hide();
		}
	},

	show: function() {
		this.wrapper.css({
			left: this.currentValue.offset().left,
			top: this.currentValue.offset().top
		});

		this.wrapper.show();
		this.backdrop.show().css('z-index', parseInt(this.wrapper.css('z-index')) - 1);
	},

	hide: function() {
		this.backdrop.hide();
		this.wrapper.hide();
	},

	destroy: function() {

	}
});
