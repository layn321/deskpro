Orb.createNamespace('DeskPRO.Admin.ElementHandler');

DeskPRO.Admin.ElementHandler.AgentGroupEditPage = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	initPage: function() {
		var self = this;
		$('#load_agent_perms_btn').on('click', function(ev) {
			ev.preventDefault();
			self.showLoadAgentPermsOb(ev);
		});

		this.toolsMenu = new DeskPRO.UI.Menu({
			triggerElement: $('#tools_menu_trigger'),
			menuElement: $('#tools_menu')
		});
		this.deleteOverlay = new DeskPRO.UI.Overlay({
			triggerElement: '#delete_overlay_trigger',
			contentElement: '#delete_overlay'
		});

		$('#permgroup_table').find(':checkbox').on('change', function(ev) {
			var row = $(this).closest('tr');
			if (row.hasClass('effective-override')) {
				ev.preventDefault();
				ev.stopPropagation();
				ev.stopImmediatePropagation();
				self.suppressChange = true;
				$(this).prop('checked', false);
				self.suppressChange = false;

				if (!$(this).data('tipped')) {
					var tipped = Tipped.create(this, "This permission is granted through one of the selected permissions groups to the left.", {
						showOn: false,
						closeButton: true,
						hideOn: 'click-outside'
					});
					$(this).data('tipped', tipped);
				}
				$(this).data('tipped').show();
				return;
			}
			if (!self.suppressChange) {
				self.updatePermissionsGrid($(this));
			}
		});

		this.updatePermissionsGrid();

		$('#permgroup_table .expand-toggle').on('click', function(ev) {
			ev.preventDefault();

			var section = $(this).closest('tbody');
			var rows = section.find('.' + $(this).data('expand'));

			if ($(this).hasClass('expanded')) {
				$(this).removeClass('expanded');
				rows.hide();
			} else {
				$(this).addClass('expanded');
				rows.show();
			}
		});

		// For every parnet option, if its off but children are checked,
		// expand them
		$('#permgroup_table tr.parentperm').not('.on').each(function() {
			var tbody = $(this).closest('tbody');
			var subs = tbody.find('.' + $(this).find('i').data('expand'));
			if (subs.filter('.on')[0]) {
				$(this).find('i').addClass('expanded');
				subs.show();
			}
		});
	},

	showLoadAgentPermsOb: function(ev) {
		var self = this;
		if (!this.agentob) {
			this.agentob = new DeskPRO.UI.OptionBox({
				element: $('#load_agent_perms_ob')
			});

			$('#load_agent_perms_ob button.save-trigger').on('click', function() {
				self.agentob.close();

				var agentId = parseInt(self.agentob.getSelected('agents'));

				if (agentId) {
					var url = $(this).data('fetch-url');
					url = url.replace(/\{person_id\}/, agentId);

					var old_text = $('#load_agent_perms_btn').text();
					$('#load_agent_perms_btn').text('...');
					$.ajax({
						url: url,
						dataType: 'json'
					}).success(function(data) {
						$('#load_agent_perms_btn').text(old_text);
						$('#permgroups input.permcheck').each(function() {
							var name = $(this).data('perm-name');
							if (data[name] == "1") {
								$(this).prop('checked', true);
							} else {
								$(this).prop('checked', false);
							}
						});
					});
				}
			});
		}

		this.agentob.open(ev);
	},

	updatePermrowEnabled: function(row, isVis) {
		var has = false;
		var override = false;

		if ($('input.override-perm', row).is(':checked')) {
			has = true;
		}

		if (!has) {
			$('input.in-use', row).each(function() {
				if ($(this).val() == '1') {
					has = true;
					override = true;
				}
			});
		}

		if (has) {
			row.addClass('on');

			var ef = row.find('.effective');

			if (!ef.hasClass('effective-on')) {
				ef.addClass('effective-on');
				if (this._pageLoaded && isVis) {
					this.effectiveChanged.push(ef);
				}
			}
			if (override) {
				row.addClass('effective-override');
			} else {
				row.removeClass('effective-override');
			}
		} else {
			row.removeClass('on').removeClass('effective-override');

			var ef = row.find('.effective');

			if (ef.hasClass('effective-on')) {
				ef.removeClass('effective-on');
				if (this._pageLoaded && isVis) {
					ef.stop().css("background-color", '#FFF97E').animate({backgroundColor: '#EBEBEB'}, 350);
				}
			}
		}
	},

	updatePermissionsGrid: function(updatedEl) {
		var self = this;

		$('#permgroup_table tr.permrow').each(function() {
			var vis = $(this).is(':visible');
			self.updatePermrowEnabled($(this), vis);
		});

		if (updatedEl) {
			var elRow = updatedEl.closest('tr');
			if (elRow.hasClass('on')) {
				var i = elRow.find('i');
				var tbody = elRow.closest('tbody');
				tbody.find('.' + i.data('expand')).each(function() {
					var row = $(this);
					row.addClass('on').removeClass('disabled');
					row.find('.effective').addClass('effective-on');
					row.find('.jquery-checkbox-checked').addClass('jquery-checkbox-checked')
					row.find('.onoff-slider').prop('checked', true);
				});
			} else if (elRow.is('.subperm')) {
				var tbody = elRow.closest('tbody');
				var parent = tbody.find('tr.parentperm');
				parent.removeClass('on');
				parent.find('.effective').removeClass('effective-on');
				parent.find('.jquery-checkbox-checked').removeClass('jquery-checkbox-checked')
				parent.find('.onoff-slider').prop('checked', false);
			}
		}

		$('#permgroup_table tr.parentperm').each(function() {
			if (!$(this).hasClass('on')) {
				return;
			}

			var i = $(this).find('i');
			var tbody = $(this).closest('tbody');
			tbody.find('.' + i.data('expand')).each(function() {
				var row = $(this);
				row.addClass('on').removeClass('disabled');
				row.find('.effective').addClass('effective-on');
				row.find('.jquery-checkbox-checked').addClass('jquery-checkbox-checked')
				row.find('.onoff-slider').prop('checked', true);
			});
		});

		$('#permgroup_table tr.permrow').each(function() {
			var vis = $(this).is(':visible');
			self.updatePermrowEnabled($(this), vis);
		});

		this.suppressChange = true;
		this.processDependencies();
		this.suppressChange = false;

		if (this.effectiveChanged && this.effectiveChanged.length) {
			for (var i = 0; i < this.effectiveChanged.length; i++) {
				if (this.effectiveChanged[i].closest('tr').hasClass('on')) {
					this.effectiveChanged[i].stop().css("background-color", '#FFF97E').animate({backgroundColor: '#EBEBEB'}, 350);
				}
			}
		}
		this.effectiveChanged = [];
	},

	/**
	 * Goes through all permissions who show "yes" and make sure they meet dependencies
	 * that affect them.
	 */
	processDependencies: function() {
		var self = this;
		var ons = $('#permgroup_table tr.permrow');
		if (!this.depend_cache) {
			this.depend_cache = {};
		}

		ons.each(function() {
			if (!$(this).attr('id')) {
				$(this).attr('id', Orb.getUniqueId());
			}
			var id = $(this).attr('id');

			if (self.depend_cache[id]) {
				var deps = self.depend_cache[id];
			} else {
				var deps = self.traceDependencies($(this));
				self.depend_cache[id] = deps;
			}

			var pass = true;
			for (var i = 0; i < deps.length; i++) {
				row = $('tr.perm-' + deps[i] + '.on');
				if (!row.length) {
					pass = false;
					break;
				}
			}

			var row = $(this);
			if (!pass) {
				row.removeClass('on').addClass('disabled');
				row.find('.effective').removeClass('effective-on');
				row.find('.jquery-checkbox-checked').removeClass('jquery-checkbox-checked')
				row.find('.onoff-slider').prop('checked', false);
			} else {
				row.removeClass('disabled');
			}
		});
	},

	traceDependencies: function(row) {
		var all = [];
		while (1) {
			var depends_on = row.data('depends-on');
			if (!depends_on) {
				break;
			}

			all.push(depends_on);
			row = $('tr.perm-' + depends_on);
		}

		return all;
	}
});
