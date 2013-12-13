Orb.createNamespace('DeskPRO.Admin.ElementHandler');

DeskPRO.Admin.ElementHandler.MassAddAgents = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var row = this.el;
		var btn = row.find('button.submit-trigger');
		var loading = row.find('.agents-loading');
		var done = row.find('.agents-done');
		var error = row.find('.agents-error');

		row.find('textarea.address-list').one('focus', function() {
			$(this).height(125);
			btn.show();
		});

		btn.on('click', function() {
			btn.hide();
			loading.show();
			error.hide();

			$.ajax({
				url: btn.data('submit-url'),
				data: {
					email_addresses: row.find('textarea.address-list').val()
				},
				type: 'POST',
				dataType: 'json',
				complete: function() {
					loading.hide();
					btn.show();
				},
				success: function(data) {
					done.hide();
					done.find('ul').empty();

					if (data.error) {
						error.find('.error-text').hide();
						error.show();
						error.find('.' + data.error).show();
					} else {
						row.find('textarea.address-list').val('');
						var agentList = done.find('ul');
						Array.each(data.agents, function(agent) {
							var li = $('<li><a class="editable-link"><span></span></a></li>');
							li.find('span').text(agent.email);
							li.find('a').attr('href', BASE_URL + 'admin/agents/'+agent.id+'/edit');
							li.appendTo(agentList);
						});

						done.show();

						row.find('.agent-input').hide();
					}
				}
			});
		});
	}
});