Orb.createNamespace('DeskPRO.Admin.ElementHandler');

DeskPRO.Admin.ElementHandler.Onboarding = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		this.initTasks();
		this.initAsk();
		this.initAgents();
		this.initCloudCustomDomain();
	},

	initTasks: function() {
		var onboard_box = this.el;
		var openArticle = null;

		$('#onboard_wrap').find('.dismiss-all').on('click', function() {
			$('#onboard_wrap').fadeOut('fast');

			$.ajax({
				url: BASE_URL + 'admin/onboard-mark-complete/dismiss/hide_all.json',
				type: 'POST'
			});
		});

		onboard_box.on('click', 'button.btn-dismiss, button.btn-done', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			var row = $(this).closest('li');

			if ($(this).hasClass('btn-done')) {
				var type = 'done';
			} else {
				var type = 'dismiss';
			}

			var id = row.data('task-id');

			$.ajax({
				url: BASE_URL + 'admin/onboard-mark-complete/'+type+'/'+id+'.json',
				type: 'POST'
			});

			if (row.hasClass('expanded')) {
				row.removeClass('expanded');
				openArticle = null;
			}

			row.find('article').slideUp('fast', function() {
				if (type == 'done') {
					row.addClass('finished');
				} else {
					row.addClass('dismissed');
				}
			});
		});

		onboard_box.on('click', 'header', function(ev) {
			var row = $(this).closest('li');
			if (!row.data('task-id')) {
				return;
			}

			$('#onboard_question').removeClass('expanded').find('article').hide();

			ev.preventDefault();
			ev.stopPropagation();

			var togglingSelf = false;
			var article = row.find('article').first();

			if (openArticle) {
				// Toggling self
				if (openArticle.get(0) == article.get(0)) {
					togglingSelf = true;
				}

				openArticle.slideUp('fast');
				openArticle.closest('li').removeClass('expanded');
				openArticle = null;

				if (togglingSelf) {
					return;
				}
			}

			row.addClass('expanded');
			article.slideDown('fast');
			openArticle = article;
		});
	},

	initAgents: function() {
		var row = this.el.find('li.onboard-add-agents');
		var btn = row.find('button.submit-trigger');
		var loading = row.find('.agents-loading');
		var done = row.find('.agents-done');
		var error = row.find('.agents-error');

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
					}
				}
			});
		});
	},

	initAsk: function() {
		var sent = [];

		var row   = $('#onboard_question');
		var input = row.find('input.input-question');
		var btn   = row.find('button.submit-trigger');

		btn.on('click', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			var text = input.val().trim();
			var dupecheck = text.toLowerCase().replace(/\s+/g, '');
			if (!text) {
				return;
			}

			if (sent.indexOf(dupecheck) !== -1) {
				row.addClass('expanded');
				row.find('article').slideDown('fast');
				return;
			}

			sent.push(dupecheck);

			row.removeClass('expanded');
			row.find('article').hide();

			btn.find('em').addClass('flat-spinner');
			$.ajax({
				url: row.data('submit-url'),
				type: 'POST',
				data: {
					message: text,
					type: 'ask'
				},
				complete: function() {
					btn.find('em').removeClass('flat-spinner');
				},
				success: function() {
					btn.find('em').removeClass('flat-spinner');
					row.addClass('expanded');
					row.find('article').slideDown('fast');
				}
			});
		});
	},

	initCloudCustomDomain: function() {
		var el = this.el.find('li.onboard-cloud-custom-domain');

		var btn   = el.find('button.custom_domain_save');
		var load  = el.find('i.custom_domain_loading');
		var done  = el.find('.custom_domain_done');
		var form  = el.find('.custom_domain_form');
		var input = form.find('input.custom_domain');

		btn.on('click', function(ev) {
			ev.preventDefault();

			var val = input.val();

			btn.hide();
			load.show();
			$.ajax({
				url: btn.data('submit-url'),
				type: 'POST',
				data: {
					custom_domain: val
				},
				complete: function() {
					btn.show();
					load.hide();
				},
				success: function() {
					done.show().find('.custom_domain_label').text(val);
					form.hide();
				}
			});
		});
	}
});