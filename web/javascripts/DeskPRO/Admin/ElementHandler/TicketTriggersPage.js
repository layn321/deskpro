Orb.createNamespace('DeskPRO.Admin.ElementHandler');

DeskPRO.Admin.ElementHandler.TicketTriggersPage = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var self = this;
		var uls = $('ul.trigger-set');

		$('span.term-error').each(function() {
			var row = $(this).closest('li');
			row.addClass('has-term-error')
		});

		var updateRunOrderDisplay = function() {
			uls.each(function() {
				var x = 0;
				$(this).find('li.is-trigger').each(function() {
					if (!$(this).hasClass('off')) {
						x++;
						$(this).find('.run-order label').text(x + '');
					} else {
						$(this).find('.run-order label').text('-');
					}
				});
			});
		};

		$('.trigger-toggle').on('click', function() {
			var row = $(this).closest('.trigger-row');

			if (!row.hasClass('off')) {
				row.addClass('off');
				$(this).find('.on').hide();
				$(this).find('.off').show();
				var mode = 0;
			} else {
				row.removeClass('off');
				$(this).find('.off').hide();
				$(this).find('.on').show();
				var mode = 1;
			}

			updateRunOrderDisplay();

			var tid = row.data('trigger-id');
			$.ajax({
				url: BASE_URL + 'admin/tickets/triggers/toggle-enabled.json',
				type: 'POST',
				dataType: 'json',
				data: {
					trigger_id: tid,
					onoff: mode
				}
			});
		});

		uls.each(function() {
			var el = $(this);
			$(this).sortable({
				items: '> li.orderable:not(.trigger-val)',
				update: function() {
					var postData = [];

					$('li.is-trigger').each(function(i) {
						var id = $(this).data('trigger-id');
						if (id) {
							postData.push({name: 'trigger_ids[]', value: id});
						}
					});

					updateRunOrderDisplay();

					$.ajax({
						url: UPDATE_ORDER_URL,
						type: 'POST',
						dataType: 'json',
						data: postData
					});
				}
			});
		});
	}
});
