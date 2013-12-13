Orb.createNamespace('DeskPRO.Admin.ElementHandler');

DeskPRO.Admin.ElementHandler.UpgradeWatch = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var self = this;
		this.startTime = parseInt(this.el.data('start-time'));
		this.hasInitialPoll = false;

		// This determines the poll mode
		this.hasStarted = false;

		this.startCheckTimeout = window.setInterval(function() {
			var now = (new Date()).getTime();
			now /= 1000;

			if (now > (self.startTime - 10)) {
				self.begin();
			}
		}, 1000);
	},

	begin: function() {
		var self = this;
		window.clearTimeout(this.startCheckTimeout);
		this.startCheckTimeout = null;

		$('#waiting').hide();
		$('#upgrade_started').show();
		this.startPollTimer();
	},

	startPollTimer: function() {

		var self = this;

		//------------------------------
		// Polling status file
		//------------------------------

		if (this.hasStarted) {
			var url = BASE_URL;
			url = url.replace(/\/index\.php\//, '/');

			var min_time = parseFloat(this.startTime);

			this.pollFileTimeout = window.setTimeout(function() {
				$.ajax({
					url: url + 'auto-update-status.php',
					cache: false,
					error: function() {
						self.startPollTimer();
					},
					dataType: 'text',
					success: function(content) {
						content = content.trim();
						var lines = content.split(/\n+/);

						if (!content.length) {
							self.startPollTimer();
							return;
						}

						var last_time = self.hasInitialPoll || min_time;
						var restart_timer = true;
						var is_error = false;
						Array.each(lines, function(last) {
							var m = /^STATUS\((.*?)\)@([0-9\.]+)#(.*?)$/.exec(last);
							if (m) {
								var time = parseFloat(m[2]);
								if (time >= last_time) {
									console.log("Line: %s", last);
									self.updateStatus(m[1], m[3], m[2]);
									if (m[1] == 'done' || m[1].indexOf('error_') === 0) {
										is_error = m[1].indexOf('error_') === 0;
										restart_timer = false;
									}
									last_time = time;
								}
							}
						});
						self.hasInitialPoll = last_time;

						if (restart_timer) {
							self.startPollTimer();
						}
					}
				})
			}, 1000);

		//------------------------------
		// Polling has started
		//------------------------------

		} else {
			var url = this.el.data('check-started-url');

			this.pollFileTimeout = window.setTimeout(function() {
				$.ajax({
					url: url,
					data: { start_time: self.startTime },
					cache: false,
					error: function() {
						self.startPollTimer();
					},
					dataType: 'json',
					success: function(data) {
						if (data.write_perm_error) {
							self.handleError('error_write_perm');
							return;
						}

						if (data.started) {
							self.hasStarted = true;
						}

						self.startPollTimer();
					}
				})
			}, 1000);
		}
	},

	updateStatus: function(code, message, time) {

		console.log("Code: %s, Message: %s, Time: %d", code, message, time);

		$('li.on').removeClass('on');
		$('em.on').removeClass('on');

		if (code.indexOf('error_') === 0) {
			this.handleError(code, message);
			return;
		}

		switch (code) {
			case 'runner_start':
				$('li.step-start').addClass('done on');
				break;
			case 'start':
				$('li.step-start').addClass('done on');
				break;
			case 'basic_checks_start':
				$('li.step-checks').addClass('done on');
				break;
			case 'helpdesk_offline':
				$('li.step-disable-hd').addClass('done');
				break;
			case 'helpdesk_online':
				$('li.step-enable-hd').addClass('done');
				break;
			case 'file_backup_start':
				$('li.step-backup-files').addClass('done on');
				break;
			case 'file_backup_copy_start':
				$('em.step-backup-files-copy').addClass('done on');
				break;
			case 'file_backup_zip_start':
				$('em.step-backup-files-zip').addClass('done on');
				break;
			case 'file_backup_cleanup_start':
				$('em.step-backup-files-cleanup').addClass('done on');
				break;
			case 'database_backup_start':
				$('li.step-backup-db').addClass('done on');
				break;
			case 'downloading_update_start':
				$('li.step-download').addClass('done on');
				break;
			case 'installing_files_start':
				$('li.step-install-files').addClass('done on');
				break;
			case 'updating_db_start':
				$('li.step-install-db').addClass('done on');
				break;
			case 'file_backup_loc':
				$('#backup_path').show().find('em').text(message);
				$('#file_backup_loc').text(message);
				break;
			case 'database_backup_loc':
				$('#backup_path2').show().find('em').text(message);
				$('#db_backup_loc').text(message);
				break;
			case 'done':
				$('li.step-done').addClass('done');
				break;
		}

		if (code == 'done') {
			$('#done').show();

			// ping apc cache clear
			var img = $('<img/>');
			img.attr('width', 1).attr('height', 1).attr('src', this.el.data('clear-cache-url'));
			img.appendTo('body');
		}
	},

	handleError: function(code, message) {
		$('#error').show();

		$('.' + code).show();
		if (message && message.trim().length) {
			$('.' + code).find('.place-message').text(message);
		}
	}
});