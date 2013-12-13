Orb.createNamespace('DeskPRO.Admin.ElementHandler');

DeskPRO.Admin.ElementHandler.AgentEditPage = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	initPage: function() {
		var self = this;

		this.el.on('submit', function(ev) {
			if (self.okSubmit) {
				return;
			}
			ev.preventDefault();

			var errors = [];

			var name = $('input[name="agent[name]"]').val().trim();
			var email  = $('input[name="agent[email]"]').val().trim()

			if (!name.length) errors.push('Enter a name');
			if (!email.length) {
				errors.push('Enter an email address');
			} else if (!email.test(/^.+@.+\..+$/)) {
				errors.push('Enter a valid email address');
			}

			if (errors.length) {
				alert("Please correct the following errors and try again:\n - " + errors.join("\n - "));
				//return;
			}

			self.el.addClass('loading');

			// We also need to send the ajax verify too
			var postData = self.el.serializeArray();
			$('#errors_container').hide();
			$.ajax({
				url: self.el.data('validate-url'),
				type: 'POST',
				dataType: 'json',
				data: postData
			}).always(function() {
				self.el.removeClass('loading');
			}).done(function(data) {
				if (data.success) {
					// When really submitting, still show the spinner to prevent double-posts
					self.el.addClass('loading');
					self.okSubmit = true;
					self.el.submit();
				} else {
					$(document).scrollTop(0);
					$('#errors_container').show().find('ul').empty();
					Array.each(data.error_messages, function(err) {
						if (err == 'show_dupe_confirm') {
							$('#dupe_confirm').show();
						} else {
							var li = $('<li/>');
							li.html('&bull; ' + err);
							$('#errors_container').show().find('ul').append(li);
						}
					});
				}
			});
		});


		this.usergroupChecks = $('#usergroup_checks :checkbox');

		$('#usergroup_checks').on('click', ':checkbox', function() { self.updatePermissionsGrid(); });
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

		this.toolsMenu = new DeskPRO.UI.Menu({
			triggerElement: $('#tools_menu_trigger'),
			menuElement: $('#tools_menu')
		});
		this.vacationOverlay = new DeskPRO.UI.Overlay({
			triggerElement: '#vacation_overlay_trigger',
			contentElement: '#vacation_overlay'
		});
		this.deleteOverlay = new DeskPRO.UI.Overlay({
			triggerElement: '#delete_overlay_trigger',
			contentElement: '#delete_overlay'
		});

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

		var ticket_dep_choices = $('#dep_perms').find('td.ticket-choice :checkbox');
		var ticket_assign_dep_choices = $('#dep_perms').find('td.ticket-assign-choice :checkbox');
		var chat_dep_choices   = $('#dep_perms').find('td.chat-choice :checkbox');
		$('#ticketdep_toggle_all_full').on('click', function(ev) {
			ev.preventDefault();
			if (ticket_dep_choices.is(':checked')) {
				ticket_dep_choices.each(function() {
					$(this).prop('checked', false);
					$(this).closest('td').find('.jquery-checkbox').removeClass('jquery-checkbox-checked');
				});
			} else {
				ticket_dep_choices.each(function() {
					$(this).prop('checked', true);
					$(this).closest('td').find('.jquery-checkbox').addClass('jquery-checkbox-checked');
				});
				ticket_assign_dep_choices.each(function() {
					$(this).prop('checked', true);
					$(this).closest('td').find('.jquery-checkbox').addClass('jquery-checkbox-checked');
				});
			}
		});
		$('#ticketdep_toggle_all_assign').on('click', function(ev) {
			ev.preventDefault();
			if (ticket_assign_dep_choices.is(':checked')) {
				ticket_assign_dep_choices.each(function() {
					$(this).prop('checked', false);
					$(this).closest('td').find('.jquery-checkbox').removeClass('jquery-checkbox-checked');
				});
				ticket_dep_choices.each(function() {
					$(this).prop('checked', false);
					$(this).closest('td').find('.jquery-checkbox').removeClass('jquery-checkbox-checked');
				});
			} else {
				ticket_assign_dep_choices.each(function() {
					$(this).prop('checked', true);
					$(this).closest('td').find('.jquery-checkbox').addClass('jquery-checkbox-checked');
				});
			}
		});
		$('#chatdep_toggle_all').on('click', function(ev) {
			ev.preventDefault();
			if (chat_dep_choices.is(':checked')) {
				chat_dep_choices.each(function() {
					$(this).prop('checked', false);
					$(this).closest('td').find('.jquery-checkbox').removeClass('jquery-checkbox-checked');
				});
			} else {
				chat_dep_choices.each(function() {
					$(this).prop('checked', true);
					$(this).closest('td').find('.jquery-checkbox').addClass('jquery-checkbox-checked');
				});
			}
		});

		$('#depperm_tickets :checkbox').on('change', function() {
			var el = $(this);
			var row = el.closest('tr');

			if (el.hasClass('departments_assign')) {
				var other = row.find(':checkbox.departments');
				if (!el.is(':checked')) {
					other.prop('checked', false).change();
				}
			} else {
				var other = row.find(':checkbox.departments_assign');
				if (el.is(':checked')) {
					other.prop('checked', true).change();
				}
			}
		});

		$('#depperm_tickets :checkbox.departments').each(function() {
			if ($(this).get(0).checked) {
				var row = $(this).closest('tr');
				var other = row.find(':checkbox.departments_assign');
				other.prop('checked', true).change();
			}
		});

		// Email Addresses
		$('#more_emails_empty').find('a').on('click', function(ev) {
			Orb.cancelEvent(ev);
			$('#more_emails_empty').hide();
			$('#more_emails').show();
		});

		var moreEmails  = $('#more_emails');
		var addEmailTxt = $('#more_emails_txt');

		$('#more_emails_trigger').on('click', function(ev) {
			Orb.cancelEvent(ev);
			var val = $.trim(addEmailTxt.val());

			if (!val.indexOf('@')) {
				alert('Please enter a valid email address');
				return;
			}

			var li = $('<li class="is-new">&bull; <input type="hidden" name="new_emails[]" /><span></span>&nbsp;&nbsp;&nbsp;<i class="icon-trash remove-trigger" title="Remove email"></i></li>');
			li.addClass('is-new');
			li.find('input').val(val);
			li.find('span').text(val);

			moreEmails.find('ul').prepend(li);

			addEmailTxt.val('');
		});

		moreEmails.on('click', '.remove-trigger', function(ev) {
			Orb.cancelEvent(ev);

			var li = $(this).closest('li');
			if (li.hasClass('is-new')) {
				li.remove();
			} else {
				var input = $('<input type="hidden" name="remove_emails[]" />');
				input.val(li.data('email-id'));
				moreEmails.append(input);
				li.remove();
			}
		});

		this._pageLoaded = true;
	},

	getUsergroupIds: function() {
		var ids = [];

		this.usergroupChecks.filter(':checked').each(function() {
			ids.push(parseInt($(this).val()));
		});

		return ids;
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
		var ug_ids = this.getUsergroupIds();

		$('#permgroup_table').find('.ug-perm-val').each(function() {
			var ug_id = parseInt($(this).data('ug-id'));
			if (ug_ids.indexOf(ug_id) === -1) {
				$(this).removeClass('in-use');
			} else {
				$(this).addClass('in-use');
			}
		});

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
			var permName = $(this).data('permname');
			if (!$(this).hasClass('on')) {
				$('tr.subperm-' + name).each(function() {
					$(this).removeClass('effective-override');
				});
				return;
			}
			$('tr.subperm-' + permName).each(function() {
				$(this).find('td.prop').addClass('effective-override');
			});

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
