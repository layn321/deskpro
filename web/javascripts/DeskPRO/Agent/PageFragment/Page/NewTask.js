Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');
DeskPRO.Agent.PageFragment.Page.NewTask = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'newtask';
	},

	initPage: function(el) {

		this.noIgnoreForm = true;
		var self = this;
		this.wrapper = el;

		var nolink = false;
		this.addEvent('popover-open', function() {
			nolink = false;
			rowContainer.empty();
			addTaskRow();
		});

		var statusMenu = new DeskPRO.UI.Menu({
			menuElement: this.getEl('menu_vis'),
			onItemClicked: function(info) {
				$('input.input-vis', openForEl).val($(info.itemEl).data('vis'));
				$('.opt-trigger.visibility label', openForEl).text($(info.itemEl).text());
			}
		});


		var form = this.getEl('form');
		form.on('submit', Orb.cancelEvent);

		var rowContainer = this.getEl('tasks');

		var openForEl = null;
		rowContainer.on('click', '.remove-row-trigger', function(ev) {
			var row = $(this).closest('.task-row');
			row.slideUp('fast', function() {
				row.remove();
				self.updateUi();
			});
		});

		rowContainer.on('click', '.opt-trigger.visibility', function(ev) {
			openForEl = $(this).closest('.task-row');
			statusMenu.open(ev);
		});
		rowContainer.on('click', '.opt-trigger.date_due', function(ev) {
			var label = $('label', this);
			var row = $(this).closest('.task-row');
			var field = $('input.input-date-due', row);
			var date = $('input.input-date-due', row).val();
			if (!date) {
				date = new Date();
			}

			field.datepicker('dialog', date, function(date, inst) {
				$('input.input-date-due', row).val(date);
				label.text(date);
			}, {
				dateFormat: 'yy-mm-dd',
				showButtonPanel: true,
				beforeShow: function(input) {
					setTimeout(function() {
						var buttonPane = $(input).datepicker("widget").find(".ui-datepicker-buttonpane");

						$('button', buttonPane).remove();

						var btn = $('<button class="ui-datepicker-current ui-state-default ui-priority-secondary ui-corner-all" type="button">Clear</button>');
						btn.unbind("click").bind("click", function () { $.datepicker._clearDate( input ); label.text('No due date'); });
						btn.appendTo( buttonPane );

						$(input).datepicker("widget").css('z-index', 30101);
					},1);
				}
			}, ev);
		});

		var addTaskRow = function() {
			var tpl = DeskPRO_Window.util.getPlainTpl(self.getEl('task_row_tpl'));
			var row = $(tpl);

			if (!nolink) {
				var activeTab = DeskPRO_Window.getTabWatcher().getActiveTabIfType('ticket');
				if (activeTab) {
					var linkEl = $('.linked-ticket', row);
					$('label', linkEl).text(activeTab.page.meta.title);
					$('input.input-ticket-id', row).val(activeTab.page.meta.ticket_id);
					linkEl.show();
					$('.remove-link-trigger', row).on('click', function() {
						nolink = true;
						linkEl.hide();
						$('input.input-ticket-id', row).val(0);
						$('.linked-container', row).hide();
					});
				}

				activeTab = DeskPRO_Window.getTabWatcher().getActiveTabIfType('deal');
				if (activeTab) {
					linkEl = $('.linked-deal', row);
					$('label', linkEl).text(activeTab.page.meta.title);
					$('input.input-deal-id', row).val(activeTab.page.meta.deal_id);
					linkEl.show();
					$('.remove-link-trigger', row).on('click', function() {
						nolink = true;
						linkEl.hide();
						$('input.input-ticket-id', row).val(0);
						$('.linked-container', row).hide();
					});
				}

				if (linkEl) {
					$('.linked-container', row).show();
				}
			}

			rowContainer.append(row);

			var agent_sel = row.find('.agents_sel');
			DP.select(agent_sel);

			agent_sel.on('change', function() {
				var val = $(this).val();
				var label = $(this).find(':selected').text().trim();

				if (!val) {
					val = '';
					label = 'Me';
				}

				row.find('.assigned_agent').find('label').text(label);
				$('input.input-agent', row).val(val);
			});

			self.updateUi();
		};

		this.getEl('add_btn').on('click', addTaskRow);

		addTaskRow();

		var footer = $('footer.pop-footer', el);
		$('.submit-trigger', el).on('click', function() {
			var postData = form.serializeArray();

			footer.addClass('loading');

			$.ajax({
				url: form.attr('action'),
				type: 'POST',
				dataType: 'json',
				data: postData,
				complete: function() {
					footer.removeClass('loading');
				},
				success: function(data) {
					self.meta.popover.close();
					if (DeskPRO_Window.sections.tasks_section) {
						DeskPRO_Window.sections.tasks_section.refresh();
					}
				}
			});
		});

		this.addEvent('popover-closed', function() {
			rowContainer.empty();
			addTaskRow();
		});
	}
});
