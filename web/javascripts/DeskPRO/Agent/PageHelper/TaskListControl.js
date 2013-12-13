Orb.createNamespace('DeskPRO.Agent.PageHelper');

DeskPRO.Agent.PageHelper.TaskListControl = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(el, options) {
		var self      = this;
		var openForEl = null;
		var menuVis   = options.menuVis;
		var completeCountEl = options.completeCountEl;

		var updateCount = function(op, row) {
			var els = [];

			var checksub = function(prefix) {
				if (row.data('in-sublist-overdue')) { els.push(document.getElementById(prefix + '_overdue')); }
				if (row.data('in-sublist-today')) { els.push(document.getElementById(prefix + '_today')); }
				if (row.data('in-sublist-future')) { els.push(document.getElementById(prefix + '_future')); }
			};

			if (row.data('in-my')) {
				els.push(document.getElementById('tasks_counter_own_total'));
				checksub('tasks_counter_own');
			}
			if (row.data('in-my-teams')) {
				els.push(document.getElementById('tasks_counter_team_total'));
				checksub('tasks_counter_team');
			}
			if (row.data('in-delegated')) {
				els.push(document.getElementById('tasks_counter_delegated_total'));
				checksub('tasks_counter_delegated');
			}
			els.push(document.getElementById('tasks_counter_all_total'));
			checksub('tasks_counter_all');

			if (completeCountEl) {
				DeskPRO_Window.util.modCountEl(completeCountEl, op, -1);
			}

			Array.each(els, function(el) {
				DeskPRO_Window.util.modCountEl($(el), op);
			});

			DeskPRO_Window.sections.tasks_section.recalcBadge();

			self.fireEvent('updateCount', [op, row]);
		};

		var updateUi = function() {
			self.fireEvent('updateUi');
		};

		var sendUpdate = function(rowEl, prop, val, callback) {
			var taskId = rowEl.data('task-id');
			var url = BASE_URL + 'agent/tasks/'+taskId+'/ajax-save';

			var postData = [];
			postData.push({
				name: 'action',
				value: prop
			});
			postData.push({
				name: 'value',
				value: val
			});

			$.ajax({
				url: url,
				type: 'POST',
				data: postData,
				dataType: 'json',
				success: callback || function() {}
			});
		};

		var statusMenu = new DeskPRO.UI.Menu({
			menuElement: menuVis,
			onItemClicked: function(info) {
				sendUpdate(openForEl, 'visibility', $(info.itemEl).data('vis'));
				$('.opt-trigger.visibility label', openForEl).text($(info.itemEl).text());
			}
		});

		el.on('click', 'input.item-select', function(ev) {
			var row = $(this).closest('article.task');
			var value = $(this).is(':checked');

			if (value) {
				$('.task-sub-wrap', row).hide();
				row.addClass('completed');

				sendUpdate(row, 'completed', 1);

				updateCount('-', row);
			} else {
				row.removeClass('expanded');
				$('.task-sub-wrap', row).show();
				row.removeClass('completed');

				sendUpdate(row, 'completed', 0);

				updateCount('+', row);
			}

			updateUi();
		});
		el.find('li.assigned_agent select.agents_sel').not('.has-init').each(function() {
			var row = $(this).closest('article.task');
			DP.select($(this));

			$(this).on('change', function() {
				var val = $(this).val();
				var label = $(this).find(':selected').text().trim();

				if (!val) {
					val = '';
					label = 'Me';
				}

				row.find('.assigned_agent').find('label').text(label);
				sendUpdate(row, 'assigned', val, function() {
					DeskPRO_Window.getMessageBroker().sendMessage('agent.ui.tasks.refresh-task-list');
				});
			});
		});
		el.on('click', '.opt-trigger.visibility', function(ev) {
			openForEl = $(this).closest('article.task');
			statusMenu.open(ev);
		});
		el.on('click', '.opt-trigger.date_due', function(ev) {
			openForEl = $(this).closest('article.task');

			var label = $('label', this);
			var date = openForEl.data('date-due');
			if (!date) {
				date = new Date();
			}

			openForEl.datepicker('dialog', date, function(date, inst) {
				sendUpdate(openForEl, 'date_due', date);
				label.text(date);
			}, {
				dateFormat: 'yy-mm-dd',
				showButtonPanel: true,
				beforeShow: function(input) {
					setTimeout(function() {
						var buttonPane = $(input).datepicker("widget").find(".ui-datepicker-buttonpane");

						var btn = $('<button class="ui-datepicker-current ui-state-default ui-priority-secondary ui-corner-all" type="button">Clear</button>');
						btn.unbind("click").bind("click", function () { $.datepicker._clearDate( input ); label.text('No due date'); });
						btn.appendTo( buttonPane );

						$(input).datepicker("widget").css('z-index', 30001);
					},1);
				}
			}, ev);
		});
		el.on('click', '.expand-collapse-icon', function(ev) {
			var row = $(this).closest('article.task');
			if (row.is('.expanded')) {
				row.removeClass('expanded');
				$('.task-info', row).hide();
				$('.task-comments', row).hide();
				$('.new-comment', row).hide();
				$('.task-sub-wrap', row).hide();
				updateUi();

			} else {
				row.addClass('expanded');
				$('.task-info', row).show();
				$('.task-comments', row).show();
				$('.new-comment', row).show();
				$('.task-sub-wrap', row).show();
				updateUi();
			}
		});

		el.on('click', '.comment-btn', function(ev) {
			var row = $(this).closest('article.task');
			var input = $('.new-comment', row);
			if (input.is(':visible')) {
				input.hide();
			} else {
				input.show();
			}

			updateUi();
		});
		el.on('click', '.cancel-comment-trigger', function(ev) {
			var row = $(this).closest('article.task');
			var btn = $('.comment-btn', row);
			$('.new-comment', row).hide();
			updateUi();
		});

		el.on('click', '.save-comment-trigger', function(ev) {
			var row = $(this).closest('article.task');
			var commentTxt = $('textarea', row);

			var closefn = function() {
				commentTxt.val('');
				$('.new-comment', row).hide();
			};

			if (!commentTxt.val().trim().length) {
				return;
			}

			var postData = [];
			postData.push({
				name: 'comment',
				value: commentTxt.val().trim()
			});

			row.addClass('loading');

			var taskId = row.data('task-id');
			$.ajax({
				url: BASE_URL + 'agent/tasks/'+taskId+'/ajax-save-comment',
				type: 'POST',
				dataType: 'json',
				data: postData,
				complete: function() {
					row.removeClass('loading');
				},
				success: function(data) {
					closefn();

					if (data.error) {
						return;
					}

					var list = $('ul.task-comment-list', row);
					$(data.comment_li_html).appendTo(list);
					$('.task-comments', row).show();
					updateUi();
				}
			});
		});

		el.on('click', '.task-group header', function() {
			$(this).parent().toggleClass('collapsed')
			updateUi();
		});

		el.on('click', '.delete-task', function(ev) {
			var row = $(this).closest('.row-item');
			var taskId = row.data('task-id');

			if (confirm($(this).data('confirm'))) {
				row.slideUp();
				updateCount('-', row);
				$.ajax({
					url: BASE_URL + 'agent/tasks/' + taskId + '/delete',
					error: function() {
						row.show();
					},
					success: function() {
						row.remove();
					}
				});
			}
		});
	}
});