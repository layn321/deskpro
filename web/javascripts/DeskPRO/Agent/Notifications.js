Orb.createNamespace('DeskPRO.Agent');

DeskPRO.Agent.Notifications = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function() {
		var self = this;

		this.fireEvent('init');

		this.dismissedIds = [];
		if (Modernizr.localstorage && window.localStorage['dpa_dissmissalerts']) {
			this.dismissedIds = window.localStorage['dpa_dissmissalerts'].split(',');
			for(var i=0; i<this.dismissedIds.length; i++) { this.dismissedIds[i] = parseInt(this.dismissedIds[i], 10); }
		}

		DeskPRO_Window.getMessageBroker().addMessageListener('agent-notify.tickets', function(info) { this.addRow(info.row, info.alert_id || null); }, this);
		DeskPRO_Window.getMessageBroker().addMessageListener('agent-notify.tasks', function(info) { this.addRow(info.row); }, this);
		DeskPRO_Window.getMessageBroker().addMessageListener('agent-notify.new_comment', function(info) { this.addRow(info.row); }, this);
		DeskPRO_Window.getMessageBroker().addMessageListener('agent-notify.new_feedback', function(info) { this.addRow(info.row); }, this);
		DeskPRO_Window.getMessageBroker().addMessageListener('agent-notify.new_registration', function(info) { this.addRow(info.row); }, this);
		DeskPRO_Window.getMessageBroker().addMessageListener('agent-notify.twitter', function(info) { this.addRow(info.row); }, this);

		$('#dp_header_notify_wrap').on('click', '.trigger-dismiss', function(ev) {
			Orb.cancelEvent(ev);
			self.dismissAll();
			Orb.shimClickCallbackPop();
		}).on('click', '.dismiss', function(ev) {
			Orb.cancelEvent(ev);
			ev.stopImmediatePropagation();

			var ul = $(this).closest('ul');
			var row = $(this).closest('li');

			if (row.data('alert-id')) {
				self.dismissAlertId(row.data('alert-id'));
				DeskPRO_Window.getMessageChanneler().poller.send();
			}

			self.removeRow(row);

			if (!ul.find('li')[0]) {
				Orb.shimClickCallbackPop();
			}
		}).on('click', 'li.inside', function(ev) {
			Orb.cancelEvent(ev);
			ev.stopImmediatePropagation();

			DeskPRO_Window.runPageRouteFromElement($(this));

			var ul = $(this).closest('ul');
			var row = $(this).closest('li');

			if (row.data('alert-id')) {
				self.dismissAlertId(row.data('alert-id'));
				DeskPRO_Window.getMessageChanneler().poller.send();
			}

			self.removeRow(row);

			if (!ul.find('li')[0]) {
				Orb.shimClickCallbackPop();
			}
		}).on('click', '.trigger-notify-prefs', function(ev) {
			Orb.cancelEvent(ev);
			ev.stopImmediatePropagation();
			Orb.shimClickCallbackPop();
			$('#settingswin').trigger('dp_open', 'ticket-notify');
		});
	},

	dismissAll: function() {
		var self = this;
		$('#dp_notify_list').find('li').each(function() {
			var row = $(this);
			self.removeRow(row, true);
		});

		DeskPRO_Window.dismissAlertQueue = [-1];
		DeskPRO_Window.getMessageChanneler().poller.send();
	},

	dismissAlertId: function(alertId) {

		alertId = parseInt(alertId);
		this.dismissedIds.include(alertId);
		DeskPRO_Window.dismissAlertQueue.push(alertId);

		if (this.dismissedIds.length > 1000) {
			while (this.dismissedIds.length > 1000) {
				this.dismissedIds.shift();
			}
		}

		if (Modernizr.localstorage) {
			window.localStorage['dpa_dissmissalerts'] = this.dismissedIds.join(',');
		}

		if ($('#dp_notify_list').find('li').length < 1) {
			DeskPRO_Window.dismissAlertQueue = [-1];
		}
	},

	isDismissedAlready: function(alertId) {
		alertId = parseInt(alertId);
		return this.dismissedIds.indexOf(alertId) !== -1;
	},

	getListTypeByType: function(type) {
		var listType = null;
		if (type == 'tickets') {
			listType = 'tickets';
		} else if (type == 'new_registration') {
			listType = 'people';
		} else if (type == 'chat') {
			listType = 'chat';
		} else if (type == 'tasks') {
			listType = 'tasks';
		} else if (type == 'new_comment' || type == 'new_feedback') {
			listType = 'publish';
		}

		return listType;
	},

	addRow: function(html_or_el, alert_id) {

		if (alert_id && this.isDismissedAlready(alert_id)) {
			this.dismissAlertId(alert_id);
			return;
		}

		var row = $(html_or_el);
		row.addClass('msg-row');
		row.data('route-notabreload', 1).attr('data-route-notabreload', 1);

		if (alert_id) {
			row.data('alert-id', alert_id);
			row.attr('data-alert-id', alert_id);
		}

		var type = row.data('type');

		if (type == 'chat') {
			return;
		}

		var listType = this.getListTypeByType(type);
		var list = $('#dp_header_notify_wrap').find('li.type-row.' + listType).find('ul.notify-list');

		var self = this;

		var time = row.find('time');
		if (time[0]) {
			if (!time.attr('datetime')) {
				time.attr('datetime', (new Date()).toISOString());
			}
			Orb.Util.TimeAgo.refreshElements([time.get(0)]);
		}

		var ev = { row: row, type: type };
		this.fireEvent('addRow');

		list.prepend(row);

		this.modCount(type, '+');

		if (window.webkitNotifications && window.webkitNotifications.checkPermission() == 0 && DeskPRO_Window.getMessageChanneler().hasDoneInitialLoad) {

			var icon = row.data('icon') || '';
			if (icon) {
				icon = ASSETS_BASE_URL + '/' + icon;
			}

			var notification = window.webkitNotifications.createNotification(
				icon, row.find('big').first().text() || 'DeskPRO', row.find('small').first().text()
			);
			notification.onclick = function() {
				window.focus();
				DeskPRO_Window.runPageRouteFromElement(row);
				self.removeRow(row);
			};
			notification.onclose = function() {
				if (!self._isRemoving && !row.data('notification-timeout')) {
					self.removeRow(row);
				}
			};
			if (DESKPRO_PERSON_NOTIFICATION_DISMISS) {
				notification.ondisplay = function() {
					setTimeout(function() {
						row.data('notification-timeout', true);
						notification.cancel();
					}, DESKPRO_PERSON_NOTIFICATION_DISMISS * 1000);
				};
			}
			notification.show();
			row.data('notification', notification);
		}
	},

	addMessage: function(type, message, route, id) {
		var row = $(DeskPRO_Window.util.getPlainTpl('#dp_header_notify_row_tpl'));
		row.data('type', type);
		row.addClass(type);
		row.data('data-route', route || '').attr('data-route', route || '')
			.data('route-notabreload', 1).attr('data-route-notabreload', 1);

		row.find('time').addClass('timeago').text('');
		row.find('big').text(message);

		if (id) {
			row.addClass('id-' + id);
		}

		this.addRow(row);
	},

	findRow: function(id_class) {
		var row = $('#dp_header_notify_wrap').find('li.' + id_class);

		if (!row[0]) {
			return null;
		}

		return row;
	},

	removeRow: function(row, noSendUpdate) {
		this._isRemoving = true;

		var self = this;
		var type = row.data('type');
		var ev = { row: row, type: type };
		var any_alert_ids = false;
		this.fireEvent('removeRow');

		if (row.data('notification')) {
			// Depending on which api is being imlpemented by the browser, it could be close or cancel
			if (row.data('notification').close) {
				try { row.data('notification').close(); } catch (e) {}
			}
			if (row.data('notification').cancel) {
				try { row.data('notification').cancel(); } catch (e) {}
			}
			row.data('notification', false);
		}

		row.remove();
		this.modCount(type, '-');

		if (row.data('class-id')) {
			var related = $('#dp_header_notify_wrap').find('li.' + row.data('class-id'));
			related.each(function() {
				var $related = $(this);
				$related.remove();

				if ($related.data('alert-id')) {
					any_alert_ids = true;
					self.dismissAlertId($related.data('alert-id'));
				}
			});
			this.modCount(type, '-', related.length);
		}

		if (row.data('alert-id')) {
			any_alert_ids = true;
			this.dismissAlertId(row.data('alert-id'));
		}

		if (!noSendUpdate && any_alert_ids) {
			if ($('#dp_notify_list').find('li').length < 1) {
				this.dismissAll();
			} else {
				DeskPRO_Window.getMessageChanneler().poller.send();
			}
		}

		this._isRemoving = false;
	},

	removeRelated: function(related) {
		var self = this;
		var any = false;

		$('#dp_header_notify_wrap').find('li').each(function() {
			var row = $(this);
			if (row.data('related') === related) {
				any = true;
				self.removeRow(row, true);
			}
		});

		if (any) {
			DeskPRO_Window.getMessageChanneler().poller.send();
		}
	},

	removeRowById: function(id) {
		var self = this;
		var row = $('#dp_header_notify_wrap').find('li.id-' + id);
		row.each(function() {
			self.removeRow($(this), true);
		});
	},

	removeRowByClass: function(id) {
		var self = this;
		var row = $('#dp_header_notify_wrap').find('li.' + id);
		row.each(function() {
			self.removeRow($(this), true);
		});
	},

	modCount: function(type, op, count) {
		var listType = this.getListTypeByType(type);
		if (!listType) return;

		var list = $('#dp_header_notify_wrap').find('li.type-row.' + listType);
		var el = list.find('.badge').first();
		var el2 = list.find('.notify-count').first();

		var ev = { notif: this, type: type, op: op, count: count, el: el, el2: el };
		this.fireEvent('beforeModCount', ev);

		newcount = list.find('#dp_notify_list').find('li').length;
		el.text(newcount);
		el2.text(newcount);

		// <3 because the dismiss button and the help note are li's
		if (list.find('#dp_notify_list').find('li').length) {
			list.find('#dp_notify_list_none').hide();
			list.find('#dp_notify_list_dismiss').show();
		} else {
			list.find('#dp_notify_list_none').show();
			list.find('#dp_notify_list_dismiss').hide();
		}

		if (newcount < 1) {
			el.hide();
			list.removeClass('dp-notifications-on');
			list.hide();
			this.fireEvent('typeHide', [type, el]);

			if (!$('#dp_header_notify_wrap').find('.dp-notifications-on')[0]) {
				$('#dp_header_notify_wrap').find('li.none').show();
			}
		} else {
			el.show();
			list.show();
			list.addClass('dp-notifications-on');
			this.fireEvent('typeShow', [type, el]);
			$('#dp_header_notify_wrap').find('li.none').hide();
		}

		this.fireEvent('modCount', ev);
	}
});
