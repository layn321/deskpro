Orb.createNamespace('DeskPRO.Agent');

DeskPRO.Agent.RecentTabs = new Orb.Class({
	initialize: function() {
		var self = this;
		this.recentTabIds = {};
		this.recent  = [];
		this.recentPendingSync = [];
		this.list = $('#recent_tabs_list');
		this.idW = 0;

		var eatNext = false;
		$('#recent_tabs_list').on('click', function(ev) {
			Orb.shimClickCallbackPop();
		});
		$('#recent_tabs_list_filter').on('keydown', function(ev) {
			if (ev.keyCode == 13 /* enter key */) {
				var current = self.list.find('.dp-cursor');
				eatNext = true;
				if (current[0]) {
					DeskPRO_Window.runPageRouteFromElement(current.find('a'));
					Orb.shimClickCallbackPop();
				}

			} else if (ev.keyCode == 40 /* down key */ || ev.keyCode == 38 /* up key */) {
				eatNext = true;
				var current = self.list.find('.dp-cursor');
				current.removeClass('dp-cursor');
				var dir = ev.keyCode == 40 ? 'down' : 'up';
				var next;

				if (!current.length) {
					if (dir == 'down') {
						self.list.find('.dp-vis').first().addClass('dp-cursor');
					} else {
						self.list.find('.dp-vis').last().addClass('dp-cursor');
					}
				} else {
					if (dir == 'down') {
						next = current.next('li.dp-vis');
						if (!next.length) {
							next = self.list.find('.dp-vis').first().addClass('dp-cursor');
						}
					} else {
						next = current.prev('li.dp-vis');
						if (!next.length) {
							next = self.list.find('.dp-vis').last().addClass('dp-cursor');
						}
					}

					next.addClass('dp-cursor');
				}
			}
		}).on('keyup', function(ev) {
			if (eatNext) {
				return;
			}
			var val = $.trim($(this).val());

			if (!val) {
				self.list.find('li').show().addClass('dp-vis');
				return;
			}

			val = val.toLowerCase();

			self.list.find('li').each(function() {
				if ($(this).data('string-match').indexOf(val) !== -1) {
					$(this).show().addClass('dp-vis');
				} else {
					$(this).hide().removeClass('dp-vis');;
				}
			});
		});

		this.reloadRecentTabs();
	},

	reloadRecentTabs: function() {
		$.ajax({
			url: BASE_URL + 'agent/ui/load-recent-tabs.json',
			type: 'GET',
			dataType: 'JSON',
			context: this,
			success: function(data) {
				// Any tabs opened before the last list was re-loaded
				var readd = false;
				if (this.recent.length) {
					readd = this.recent;
				}

				var pending = this.recentPendingSync;

				this.recent = [];

				// Regen tab IDs lookup map
				Array.each(data, function(item) {
					this.add(item[0], item[1], item[2], item[3], item[4]);
				}, this);

				// Reset the proper pending list (dont re-sync the ones we just loaded)
				this.recentPendingSync = pending;

				if (readd) {
					Array.each(readd, function(item) {
						this.add(item[0], item[1], item[2], item[3], item[4]);
					}, this);
				}
			}
		});
	},

	open: function() {
		// Backwards compat
	},

	close: function() {
		// Backwards compat
	},


	/**
	 * Add a new item to the list
	 *
	 * @param {String} type
	 * @param {Integer} id
	 * @param {String} title
	 * @param {String} url
	 * @param {Integer} ts
	 */
	add: function(type, id, title, url, ts) {

		$('#recent_tabs_list_li_none').remove();

		if (!ts) {
			ts = parseInt((new Date()).getTime() / 1000);
		}

		var idString = type + '-' + id, idx = null;

		// If we already have the tab, remove it so it will be
		// re-added to the front of the array
		if (this.recentTabIds[idString]) {
			delete this.recentTabIds[idString];
			Array.each(this.recent, function(item, i) {
				if ((item[0] + '-' + item[1]) == idString) {
					idx = i;
					return false;
				}
			});

			if (idx !== null) {
				this.recent.splice(idx, 1);
				this.list.find('li.' + idString).remove();
			}
		}

		this.recent.unshift([type, id, title, url, ts]);
		this.recentTabIds[idString] = true;

		while (this.recent.length > 350) {
			var last = this.recent.pop();
			this.list.find('li.' + last[0] + '-' + last[1]).remove();
		}

		var itm = [type, id, title, url, ts];
		this.recentPendingSync.unshift(itm);
		this.renderRow(itm);

		this.length = this.recent.length;
	},


	/**
	 * Render an item onto the beginning of the list
	 *
	 * @param {Array} item
	 * @returns {jQuery}
	 */
	renderRow: function(item) {
		var row = $(DeskPRO_Window.util.getPlainTpl('#recent_tabs_list_tpl'));
		var stringMatch = item[2].toLowerCase();

		row.addClass(item[0] + '-' + item[1] + ' ' + item[0]);
		row.data('string-match', stringMatch);
		row.find('a')
			.data('route', 'page:'+item[3])
			.attr('data-route', 'page:'+item[3])
			.data('route-notabreload', '1')
			.attr('data-route-notabreload', '1')
			.find('span').text(item[2]);
		row.find('a').find('strong').text(item[1]);

		var d = new Date(item[4]*1000);
		row.find('time').attr('datetime', d.toISOString());

		Orb.Util.TimeAgo.refreshElements(row.find('time').toArray());

		if (this.idW) {
			row.find('strong').css('min-width', this.idW);
		}

		var filterVal = $.trim($('#recent_tabs_list_filter').val());
		if (!filterVal || stringMatch.indexOf(filterVal.toLowerCase()) !== -1) {
			row.addClass('dp-vis');
		} else {
			row.hide();
		}

		this.list.prepend(row);

		var w = row.find('strong').width();
		if (w > this.idW) {
			this.idW = w;
			this.list.find('strong').css('min-width', w);
		}

		return row;
	},


	/**
	 * @return {Array}
	 */
	getAll: function() {
		return this.recent;
	},


	/**
	 * Clears recent list
	 */
	clear: function() {
		this.recent = [];
		this.recentPendingSync = [];
		this.list.clear();
		this.length = 0;
	},


	/**
	 * Gets info for the last (oldest) item in the list
	 *
	 * @returns {Array}
	 */
	getLast: function() {
		if (this.recent.length) {
			return this.recent[this.recent.length-1];
		}

		return null;
	},


	/**
	 * Gets info for the first (latest) item in the list
	 *
	 * @returns {Array}
	 */
	getFirst: function() {
		if (this.recent.length) {
			return this.recent[0];
		}

		return null;
	}
});