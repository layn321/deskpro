Orb.createNamespace('DeskPRO.Agent.RuleBuilder');

DeskPRO.Agent.RuleBuilder.LabelsTerm = new Orb.Class({
	Extends: DeskPRO.Agent.RuleBuilder.TermAbstract,

	initRow: function() {
		var self = this;
		this.inner = $('.label-chooser-wrap', this.rowEl);
		this.labelType = this.rowEl.data('label-type');
		this.labelsList = $('input.labels-box', this.rowEl).first();

		this.labelsInput = new DeskPRO.UI.LabelsInput({
			type: 'tickets',
			input: this.labelsList,
			onChange: this.updateLabels.bind(this)
		});

		this.currentValue = $('.status-value', this.rowEl);
		this.currentValue.text('(click to set)');
		this.currentValue.on('click', this.show.bind(this));

		this.values = $('.label-values', this.rowEl);

		this.backdrop = $('<div class="backdrop" style="display: none"></div>');
		this.backdrop.appendTo('body');
		this.backdrop.on('click', this.hide.bind(this));

		this.wrapper = $('<div class="field-overlay labels-chooser" style="display:none"><div class="close-trigger"></div></div>');
		$('.close-trigger', this.wrapper).on('click', this.hide.bind(this));

		this.inner.detach().appendTo(this.wrapper).css('display', 'block');
		this.wrapper.appendTo('body');

		window.setTimeout(function() {
			var vals = self.currentValue.data('select-texts');
			if (vals) {
				Array.each(vals, function(val) {
					var input = $('<option value="" selected="selected" />');
					input.val(val);

					input.appendTo(self.values);
				});
			}
		}, 450);
	},

	updateLabels: function() {
		var labels = this.labelsInput.getLabels();
		var status = '(click to set)';

		if (labels.length) {
			status = labels.join(', ');
		}

		this.currentValue.text(status);

		this.values.empty();

		if (labels.length) {
			Array.each(labels, function(label) {
				var input = $('<option value="" selected="selected" />');
				input.val(label);

				input.appendTo(this.values);
			}, this);
		}
	},

	show: function() {
		var vals = [];
		this.values.find('option').each(function() {
			vals.push($(this).val());
		});
		this.labelsList.select2('val', vals);

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
		this.wrapper.remove();
		this.backdrop.remove();
		this.labelsInput.destroy();
	}
});
