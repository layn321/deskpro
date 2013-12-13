Orb.createNamespace('DeskPRO.Admin.Departments');

DeskPRO.Admin.Departments.AjaxSave = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	initPage: function() {
		var self = this;
		this.el.on('change', '.set-tickets-state, .set-chat-state', function(ev) {
			var tr = $(this).closest('.dp-grid-row');
			self.saveFeatureState(tr.data('department-id'));
		});

		this.el.on('click', 'a.edit-trigger', function(ev) {
			ev.preventDefault();

			var row = $(this).closest('article.dp-grid-row');
			self.showEditDep(row);
		});

		$('#newdep_open').on('click', function(ev) {
			ev.preventDefault();
			self.showNewDep();
		});

		// Reordering parents
		var list = $('.dep-list');
		list.sortable({
			items: '.department-group',
			handle: 'article.top',
			update: function() {
				self.updateOrders();
			}
		});
		$('.department-group', list).each(function() {
			var group = $(this);
			group.sortable({
				items: 'article.child',
				update: function() {
					self.updateOrders();
				}
			});
		});

		this.notDisabledOverlay = new DeskPRO.UI.Overlay({
			contentElement: '#dep_not_disabled_overlay'
		});

		$('#dep_not_disabled_overlay').find('.overlay-close-trigger').on('click', function() {
			self.notDisabledOverlay.close();
		});
	},

	registerChildHandler: function(handler, handlerName, el) {
		switch (handlerName) {
			case 'agent_selector':
				handler.addEvent('updated', this.saveAgentPermissions.bind(this));
				break;
		}
	},

	saveFeatureState: function(department_id) {

		if (this.isChecking) return;
		this.isChecking = true;

		var tr = $('article.department-' + department_id, this.el);

		var chat = $(':checkbox.set-chat-state', tr).is(':checked') ? 1 : 0;
		var tickets = $(':checkbox.set-tickets-state', tr).is(':checked') ? 1 : 0;

		var countChat    = $(':checkbox.set-chat-state').filter(':checked').length;
		var countTickets = $(':checkbox.set-tickets-state').filter(':checked').length;

		this.isChecking = false;

		var showAlert = false;
		if (!countChat) {
			chat = 1;
			showAlert = true;
			$(':checkbox.set-chat-state', tr).prop('checked', true);
		}
		if (!countTickets) {
			tickets = 1;
			showAlert = true;
			$(':checkbox.set-tickets-state', tr).prop('checked', true);
		}

		if (showAlert) {
			this.notDisabledOverlay.open();
			return;
		}

		if (chat) {
			$('button.label-chat-perms', tr).show();
		} else {
			$('button.label-chat-perms', tr).hide();
		}

		if (tickets) {
			$('button.label-tickets-perms', tr).show();
		} else {
			$('button.label-tickets-perms', tr).hide();
		}

		var postData = [];
		postData.push({
			name: 'chat',
			value: chat
		});
		postData.push({
			name: 'tickets',
			value: tickets
		});

		var url = BASE_URL + 'admin/departments/' + department_id + '/save-feature-state.json';

		$.ajax({
			url: url,
			type: 'POST',
			dataType: 'json',
			data: postData
		});
	},

	saveAgentPermissions: function(department_id, app, agents) {
		var url = BASE_URL + 'admin/departments/' + department_id + '/save-agents.json';

		var postData = [];
		postData.push({name: 'app', value: app});

		for (var i in agents) {
			for (var j = 0; j < agents[i].length; j++) {
				postData.push({
					name: 'agents[' + i + '][]',
					value: agents[i][j]
				});
			}
		}

		$.ajax({
			url: url,
			type: 'POST',
			dataType: 'json',
			data: postData
		});
	},

	showEditDep: function(row) {
		var self = this;
		var depId = row.data('department-id');
		var currentTitle = row.find('a.edit-trigger').text().trim();
		var currentUserTitle = row.find('a.edit-trigger').data('user-title');
		var currentGatewayId = parseInt(row.find('a.edit-trigger').data('linked-gateway-id'));
		var currentGatewayTitle = row.find('a.edit-trigger').data('linked-gateway-title');

		this.currentEditDep = depId;
		$('#editdep_title').val(currentTitle);
		$('#editdep_user_title').val(currentUserTitle);
		$('#editdep_depid').val(depId);

		if (!this.editDepParentOpt) {
			this.editDepParentOpt = $('#editcat_parent_id').find('option').clone();
		}

		$('#editcat_parent_id').empty();
		$('#editcat_parent_id').append(this.editDepParentOpt.clone());
		$('#editcat_parent_id').find('option[value="'+depId+'"]').remove();

		var group = row.closest('.department-group');
		var parentId = group.find('> article.top').data('department-id');
		if (parentId != depId) {
			$('#editcat_parent_row').show();
			$('#editcat_parent_id').find('option[value="'+parentId+'"]').prop('selected', true);
		} else {
			if (group.find('> article.child')[0]) {
				$('#editcat_parent_row').hide();
				$('#editcat_parent_id').find('option[value="0"]').prop('selected', true);
			} else {
				$('#editcat_parent_row').show();
				$('#editcat_parent_id').find('option[value="0"]').prop('selected', true);
			}
		}

		if (!this.editOverlay) {
			this.editOverlay = new DeskPRO.UI.Overlay({
				contentElement: $('#editdep_overlay')
			});
			$('#editdep_savebtn').on('click', function(ev) {
				ev.preventDefault();
				$('#editdep_overlay form').submit();
			});
		}

		var gatesel = $('#editdep_gateway_account_id');
		gatesel.find('option.current').remove();
		if (currentGatewayId) {
			var opt = $('<option />');
			opt.text(currentGatewayTitle);
			opt.val(currentGatewayId);
			opt.addClass('current');

			opt.prependTo(gatesel);
			gatesel.closest('.dp-form-row').show();
		} else {
			if (!gatesel.find('option')[0]) {
				gatesel.closest('.dp-form-row').hide();
			} else {
				gatesel.closest('.dp-form-row').show();
			}
		}

		gatesel.find('option').first().prop('selected', true);

		this.editOverlay.open();
	},

	showNewDep: function() {
		var self = this;

		if (!this.newOverlay) {
			this.newOverlay = new DeskPRO.UI.Overlay({
				contentElement: $('#newdep_overlay')
			});
			$('#newdep_savebtn').on('click', function(ev) {
				ev.preventDefault();
				$('#newdep_overlay form').submit();
			});
		}

		this.newOverlay.open();
	},

	updateOrders: function() {
		var postData = [];
		$('article.dp-grid-row[data-department-id]', this.el).each(function() {
			if ($(this).data('department-id')) {
				postData.push({
					name: 'display_order[]',
					value: $(this).data('department-id')
				});
			}
		});

		$.ajax({
			url: this.el.data('reorder-url'),
			type: 'POST',
			data: postData
		});

		// Reset last class, its needed for proper borders
		$('.department-group').each(function() {
			$(this).find('article.dp-grid-row').removeClass('last').last().addClass('last');
		});
	}
});
