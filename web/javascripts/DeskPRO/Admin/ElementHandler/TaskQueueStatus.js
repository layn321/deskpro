Orb.createNamespace('DeskPRO.Admin.ElementHandler');

DeskPRO.Admin.ElementHandler.TaskQueueStatus = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var self = this;
		var el = this.el;

		var taskId = el.data('task-id');
		var taskGroup = el.data('task-group');

		var interval;

		var updateStatus = function() {
			var url;
			if (taskGroup) {
				url = BASE_URL + 'admin/misc/check-task/group/' + taskGroup;
			} else if (taskId) {
				url = BASE_URL + 'admin/misc/check-task/' + taskId;
			} else {
				url = BASE_URL + 'admin/misc/check-task/';
			}

			$.ajax({
				url: url,
				type: 'POST',
				dataType: 'json',
				success: function(data) {
					if (taskId) {
						if (!data || !data.exists) {
							el.text('Task could not be found.');
							//clearInterval(interval);
							return;
						}

						if (data.status == 'completed' || data.status == 'errored') {
							//clearInterval(interval);
						}
					} else {
						if (!data || !data.count) {
							el.text('All tasks completed!');
							//clearInterval(interval);
							return;
						}
					}

					var text = (data.title ? data.title + ' - ' : '');

					if (data.status == 'running') {
						text += 'Running... ' + data.run_status;
					} else if (data.status == 'completed') {
						text += 'Completed! (' + data.run_status + ')';
					} else if (data.status == 'errored') {
						text += 'An error occurred: ' + data.error_text;
					} else {
						text += 'Waiting to start...';

						if (data.count_waiting) {
							if (data.count_waiting == 1) {
								text += ' (1 other task to complete)';
							} else {
								text += ' (' + data.count_waiting + ' other tasks to complete)';
							}
						}
					}

					if (data.count > 1 && !data.count_waiting) {
						if (data.count == 2) {
							text += ' (1 additional task pending)';
						} else {
							text += ' (' + (data.count - 1) + ' additional tasks pending)';
						}
					}

					el.text(text);
				}
			});
		};

		updateStatus();
		interval = setInterval(updateStatus, 10000);
	}
});
