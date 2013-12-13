Orb.createNamespace('DeskPRO.Admin.RuleBuilder');

DeskPRO.Admin.RuleBuilder.TemplateEdit = new Orb.Class({
	Extends: DeskPRO.Agent.RuleBuilder.TermAbstract,

	initRow: function() {
		var self = this;
		this.overlay = null;

		this.tplInput = this.rowEl.find('input.tpl-name');
		this.currentValue = this.rowEl.find('.status-value');
		this.trimCurrentValue();
		this.currentValue.on('click', this.show.bind(this));

		this.tplDir    = this.currentValue.data('tpldir');
		this.tplPrefix = this.currentValue.data('tplprefix')
	},

	trimCurrentValue: function() {
		var text = $.trim(this.currentValue.text());
		text = text.replace(/DeskPRO:custom_emails_user:/, '');
		text = text.replace(/DeskPRO:custom_emails_agent:/, '');
		text = text.replace(/DeskPRO:emails_user:/, '');
		text = text.replace(/DeskPRO:emails_agent:/, '');

		this.currentValue.text(text);
	},

	initValues: function() {
		if (this.tplInput.val()) {
			this.currentValue.text(this.tplInput.val());
			this.trimCurrentValue();
		} else {
			this.currentValue.text('(click to change)');
		}
	},

	hide: function() {
		if (this.overlay) {
			this.overlay.close();
		}
	},

	destroy: function() {
		if (this.tplOverlayEl) {
			this.tplOverlayEl.remove();
			this.tplOverlayBack.remove();
		}

		if (this.overlay) {
			this.overlay.destroy();
		}

		if (this.newOverlay) {
			this.newOverlay.destroy();
		}
	},

	show: function() {
		if (!this.overlay) {
			var self = this;
			this.overlay = new DeskPRO.UI.Overlay({
				fullScreen: true,
				contentMethod: 'ajax',
				contentAjax: {
					url: BASE_URL + 'admin/templates/mini-manager/' + this.tplDir + '/' + this.tplPrefix
				},
				onAjaxDone: function() {
					self.initEditor(self.overlay.elements.wrapper);
				}
			});
		}

		this.overlay.open();
	},

	initEditor: function(el) {
		var self = this;
		this.wrapperEl = el;

		el.on('click', '.edit-tpl-trigger', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			var row = $(this).closest('tr');
			var name = row.data('template-name');
			self.openTemplateEditor(name);
		});

		el.on('click', '.delete-tpl-trigger', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			var row = $(this).closest('tr');
			var name = row.data('template-name');

			row.slideUp('fast', function() {
				row.remove();
			});

			$.ajax({
				url: BASE_URL + 'admin/templates/revert-template.json?name=' + name,
				type: 'POST'
			});
		});

		el.find('.create-new-tpl-trigger').on('click', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			self.openNewOverlay();
		});

		el.find('button.apply-trigger').on('click', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			var val = el.find(':radio:checked').val().trim();
			if (val) {
				self.tplInput.val(val);
				self.currentValue.text(val);
				this.trimCurrentValue();
			} else {
				self.tplInput.val('');
				self.currentValue.text('(click to change)');
			}

			self.overlay.close();
		});
	},

	openNewOverlay: function() {
		if (!this.newOverlay) {
			var self = this;
			var el = self.wrapperEl.find('.dp-tpl-new-overlay');
			this.newOverlay = new DeskPRO.UI.Overlay({
				contentElement: el,
				onBeforeOverlayOpened: function() {
					var html = '<option value="">None (blank template)</option>';

					self.wrapperEl.find('tr.template-row').each(function() {
						html += '<option value="' + $(this).data('template-name') +'">' + $(this).find('h3').text().trim() + '</option>';
					});

					el.find('select.copy_tpl').empty().html(html);
				}
			});

			el.find('button.save-trigger').on('click', function() {
				el.find('.overlay-footer').addClass('loading');

				var postData = {
					name: el.find('input.name').data('tpl-name-prefix') + el.find('input.name').val() + '.html.twig',
					copy_tpl: el.find('select.copy_tpl').val()
				};

				$.ajax({
					url: BASE_URL + 'admin/templates/create-template',
					data: postData,
					complete: function() {
						el.find('.overlay-footer').removeClass('loading');
					},
					success: function(data) {
						var tpl = self.wrapperEl.find('.template-row-tpl').get(0).innerHTML;
						var row = $(tpl);
						row.data('template-name', data.name);
						row.find('.place-title').text(data.name);
						row.find(':radio').val(data.name);

						row.insertBefore(self.wrapperEl.find('.custom-template-make-row'));

						self.newOverlay.close();
					}
				});
			});
		}

		this.newOverlay.open();
	},



	//##################################################################################################################
	//# Template Editing
	//##################################################################################################################

	openTemplateEditor: function(template_name) {
		if (!this.tplOverlayEl) {
			this.tplOverlayBack = $('<div class="backdrop fade" />').hide().appendTo('body').on('click', this.closeTemplateEditor.bind(this));
			this.tplOverlayEl = this.wrapperEl.find('.dp-tpl-edit-overlay').detach().appendTo('body');
			this.tplOverlayEl.find('.close-overlay').on('click', this.closeTemplateEditor.bind(this));
			this.tplOverlayEl.find('.save-trigger').on('click', this.saveTemplateEditor.bind(this));
			this.tplOverlayEl.find('.revert-trigger').on('click', this.revertTemplateEditor.bind(this));

			this.withSubject = this.tplOverlayEl.find('.subject-field');
			if (!this.withSubject[0]) {
				this.withSubject = null;
			}
		}

		this.editingTemplate = template_name;
		this.tplOverlayEl.find('.template-title').text(template_name);
		this.tplOverlayEl.find('textarea.template-code').val('Loading template, please wait...').addClass('loading');
		this.tplOverlayBack.fadeIn('fast');
		this.tplOverlayEl.fadeIn('fast');

		this.tplOverlayEl.find('.overlay-footer').removeClass('loading');
		if (this.withSubject) {
			this.withSubject.hide().find('textarea.template-subject-code').val('');
			this.tplOverlayEl.find('textarea.template-code').css('height', '98%');
		}

		$.ajax({
			url: BASE_URL + 'admin/templates/get-template-code?name=' + template_name,
			context: this,
			error: function() {
				this.closeTemplateEditor();
			},
			success: function(val) {
				this.editingTemplate = template_name;

				code = val;

				if (this.withSubject) {
					var m = code.match(/\s*<dp:subject>[\s\S]*<\/dp:subject>\s*/);
					if (m) {
						this.withSubject.find('textarea.template-subject-code').val($.trim(m[1]));

						code = $.trim(code.replace(m[0], "\n"));
						this.withSubject.show();
						this.tplOverlayEl.find('textarea.template-code').css('height', '79%');
					}
				}

				this.tplOverlayEl.find('textarea.template-code').val(code).removeClass('loading');
			}
		});
	},

	revertTemplateEditor: function() {
		this.tplOverlayEl.find('.overlay-footer').addClass('loading');
		$.ajax({
			url: BASE_URL + 'admin/templates/revert-template.json?name=' + this.editingTemplate,
			type: 'POST',
			context: this,
			success: function(val) {
				this.closeTemplateEditor();
			}
		});
	},

	saveTemplateEditor: function() {
		this.tplOverlayEl.find('.overlay-footer').addClass('loading');

		var code = this.tplOverlayEl.find('textarea.template-code').val();

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
				this.tplOverlayEl.find('.overlay-footer').removeClass('loading');

				if (data.error) {
					alert(data.error_message + "\n\nLine: " + data.error_line);
					return;
				}

				this.closeTemplateEditor();
			}
		});
	},

	closeTemplateEditor: function() {
		if (!this.tplOverlayEl) return;
		this.tplOverlayEl.fadeOut('fast');
		this.tplOverlayBack.fadeOut('fast');
		this.editingTemplate = null;
	}
});
