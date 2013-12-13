Orb.createNamespace('DeskPRO.Admin.ElementHandler');

DeskPRO.Admin.ElementHandler.TemplateEditList = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var self = this;

		this.el.on('click', 'h1.toggle-section', function() {
			$(this).toggleClass('expanded');
			var table = $(this).closest('table');
			var tbody = table.find('> tbody').first();

			if ($(this).hasClass('expanded')) {
				tbody.show();
			} else {
				tbody.hide();
			}
		});

		this.el.on('click', '.edit-tpl-trigger', function() {
			self.openTemplateEditor($(this).closest('tr').data('template-name'));
		});

		this.overlayEl = null;
		this.withSubject = false;
		this.overlayBack = null;
		this.editingTemplate = null;
	},

	markReverted: function(template_name) {
		var row_id = 'row_' + template_name.toLowerCase().replace(/[:\.]/g, '_');
		var row = $('#' + row_id);

		if (!row.hasClass('tpl-changed')) {
			return;
		}

		row.removeClass('tpl-changed tpl-outdated');

		var table = row.closest('table');
		this.updateTableCounts(table);
	},

	markUpdated: function(template_name) {
		var row_id = 'row_' + template_name.toLowerCase().replace(/[:\.]/g, '_');
		var row = $('#' + row_id);

		row.addClass('tpl-changed').removeClass('tpl-outdated');

		var table = row.closest('table');
		this.updateTableCounts(table);
	},

	updateTableCounts: function(table) {
		var thead = table.find('> thead');

		var changed_count  = table.find('tr.tpl-changed').length;
		var outdated_count = table.find('tr.tpl-outdated').length;

		thead.find('.count-changed > i').text(changed_count);
		if (!changed_count) {
			thead.find('.count-changed').hide();
		} else {
			thead.find('.count-changed').show();
		}

		thead.find('.count-outdated > i').text(outdated_count);
		if (!outdated_count) {
			thead.find('.count-outdated').hide();
		} else {
			thead.find('.count-outdated').show();
		}
	},

	openTemplateEditor: function(template_name) {
		if (!this.overlayEl) {
			this.overlayBack = $('<div class="backdrop fade" />').hide().appendTo('body').on('click', this.closeTemplateEditor.bind(this));
			this.overlayEl = $('#edittpl_overlay').detach().appendTo('body');

			this.withSubject = this.overlayEl.find('.subject-field');
			if (!this.withSubject[0]) {
				this.withSubject = null;
			}

			this.overlayEl.find('.close-overlay').on('click', this.closeTemplateEditor.bind(this));
			this.overlayEl.find('.save-trigger').on('click', this.saveTemplateEditor.bind(this));
			this.overlayEl.find('.revert-trigger').on('click', this.revertTemplateEditor.bind(this));
		}

		this.editingTemplate = template_name;
		this.overlayEl.find('.template-title').text(template_name);
		this.overlayEl.find('textarea.template-code').val('Loading template, please wait...').addClass('loading');
		this.overlayBack.fadeIn('fast');
		this.overlayEl.fadeIn('fast');

		this.overlayEl.find('.overlay-footer').removeClass('loading');
		if (this.withSubject) {
			this.withSubject.hide().find('textarea.template-subject-code').val('');
			this.overlayEl.find('textarea.template-code').css('height', '98%');
		}

		$.ajax({
			url: BASE_URL + 'admin/templates/get-template-code?name=' + template_name + '&info=1',
			context: this,
			dataType: 'json',
			error: function() {
				this.closeTemplateEditor();
			},
			success: function(data) {
				this.editingTemplate = template_name;

				if (data.custom) {
					this.overlayEl.find('.revert-trigger').show();
				} else {
					this.overlayEl.find('.revert-trigger').hide();
				}

				var code = data.code;
				if (this.withSubject) {
					var m = code.match(/\s*<dp:subject>([^]*)<\/dp:subject>\s*/);
					if (m) {
						this.withSubject.find('textarea.template-subject-code').val($.trim(m[1]));

						code = $.trim(code.replace(m[0], "\n"));
						this.withSubject.show();
						this.overlayEl.find('textarea.template-code').css('height', '79%');
					}
				}

				this.overlayEl.find('textarea.template-code').val(code).removeClass('loading');
			}
		});
	},

	revertTemplateEditor: function() {
		this.overlayEl.find('.overlay-footer').addClass('loading');
		$.ajax({
			url: BASE_URL + 'admin/templates/revert-template.json?name=' + this.editingTemplate,
			type: 'POST',
			context: this,
			success: function(val) {
				this.markReverted(this.editingTemplate);
				this.closeTemplateEditor();
			}
		});
	},

	saveTemplateEditor: function() {
		this.overlayEl.find('.overlay-footer').addClass('loading');

		var code = this.overlayEl.find('textarea.template-code').val();

		if (this.withSubject) {
			code = '<dp:subject>' + this.withSubject.find('textarea.template-subject-code').val() + "</dp:subject>\n" + code;
		}

		var postData = {
			name: this.editingTemplate,
			code: code
		};

		$.ajax({
			url: BASE_URL + 'admin/templates/save-template.json',
			context: this,
			type: 'POST',
			data: postData,
			success: function(data) {
				this.overlayEl.find('.overlay-footer').removeClass('loading');

				if (data.error) {
					alert(data.error_message + "\n\nLine: " + data.error_line);
					return;
				}

				this.markUpdated(this.editingTemplate);
				this.closeTemplateEditor();
			}
		});
	},

	closeTemplateEditor: function() {
		if (!this.overlayEl) return;
		this.overlayEl.fadeOut('fast');
		this.overlayBack.fadeOut('fast');
		this.editingTemplate = null;
	}
});