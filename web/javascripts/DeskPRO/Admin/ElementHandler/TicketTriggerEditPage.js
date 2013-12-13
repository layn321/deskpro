Orb.createNamespace('DeskPRO.Admin.ElementHandler');

DeskPRO.Admin.ElementHandler.TicketTriggerEditPage = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var self = this;

		// Criteria builder
		this.criteriaEditor = new DeskPRO.Form.RuleBuilder($('.criteria-tpl'));

		$('.criteria-form .add-term').data('add-count', 0).on('click', function() {
			var basename = 'terms['+Orb.uuid()+']';
			self.criteriaEditor.addNewRow($('.criteria-form .search-terms'), basename);
		});

		// Actions builder
		this.actionsEditor = new DeskPRO.Form.RuleBuilder($('.actions-tpl'));
		this.actionsEditor.addEvent('newRow', function(new_row) {
			var a = $('.use_custom_template', new_row);

			if (a.length) {
				var id = Orb.getUniqueId('id');
				$(new_row).attr('id', id);

				var href = a.attr('href');
				href = href.replace('_opener_id_', id);
				href = href.replace('_template_', escape($('.custom_template_name', new_row).val() || ''));
				href = href.replace('_template_orig_', escape($('.custom_template_default', new_row).val() || ''));
				a.data('opener-id', id + '_iframe');

				$(a).attr('href', href);

				self.initPopoutTriggers(new_row);
			}
		});

		$('.actions-form .add-term').data('add-count', 0).on('click', function() {
			var basename = 'actions['+Orb.uuid()+']';

			self.actionsEditor.addNewRow($('.actions-form .search-terms'), basename);
		});

		if (TicketTriggerEditPage_initTerms) {
			TicketTriggerEditPage_initTerms(this);
		}
	}
});
