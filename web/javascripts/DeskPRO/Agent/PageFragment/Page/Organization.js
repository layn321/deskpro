Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');
DeskPRO.Agent.PageFragment.Page.Organization = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		var self = this;
		this.parent();
		this.TYPENAME = 'organization';
		this.tabBtn = null;
		this.noIgnoreForm = true;
		this.addEvent('render', function(container, id) {
			if (id) {
				self.tabBtn = $('#tabbtn_' + id);
			}
		});
	},

	initMetaData: function() {
		DeskPRO_Window.recentTabs.add(
			'org',
			this.meta.org_id,
			this.meta.title,
			BASE_URL + 'agent/organizations/' + this.meta.org_id
		);
	},

	initPage: function(el) {
		this.wrapper = el;
		this.contentWrapper = $('div.layout-content:first', el);

		if (this.tabBtn) {
			if (this.getMetaData('orgPicIcon')) {
				this.tabBtn.find('a').find('i').attr('class', '').addClass('image-icon').css('background-image', 'url("' + this.getMetaData('orgPicIcon') + '")').css('background-position', '50% 50%');
			}
		}

		var self = this;

		this.contactEditor = new DeskPRO.Agent.PageFragment.Page.PersonHelper.ContactEditor(this, {
			saveUrl: BASE_URL + 'agent/organizations/' + this.meta.org_id + '/save-contact-data.json',
			onReplaceEditor: function() {
				self.refreshPropBox();
			}
		});
		this.ownObject(this.contactEditor);

		if (this.meta.perms.edit) {
			// Name is editable
			var name = $('h3.name.editable:first', el);
			if (!name.attr('id')) {
				name.attr('id', Orb.getUniqueId());
			}

			var editable = new DeskPRO.Form.InlineEdit({
				baseElement: this.wrapper,
				editableClass: 'person-name-editable',
				ajax: {
					url: BASE_URL + 'agent/organizations/' + this.meta.org_id + '/ajax-save'
				},
				triggers: '.edit-name-gear'
			});

			// Attach click to wrapper because
			// this same code is used on popout on ticket,
			// and clicks dont bubble to document click
			$(this.wrapper).on('click', function (ev) {
				editable.handleDocumentClick(ev);
			});
		}

		this.getEl('delete_btn').on('click', function() {
			var url = $(this).data('delete-url');
			DeskPRO_Window.showConfirm(
				$('<div>Are you sure you want to delete this organization? <strong class="warning">The organization will be permanantly deleted</strong>.'),
				function() {
					$.ajax({
						url: url,
						type: 'POST',
						success: function() {
							DeskPRO_Window.showAlert('The organization was deleted');
						}
					});
					self.closeSelf();
				}
			);
		});

		this.changePic = new DeskPRO.Agent.PageFragment.Page.PersonHelper.ChangePic(this, {
			loadUrl: BASE_URL + "agent/organizations/" + this.meta.org_id + "/change-picture-overlay",
			saveUrl: BASE_URL + 'agent/organizations/' + this.meta.org_id + '/ajax-save'
		});
		this.ownObject(this.changePic);

		this._initLabels();
		this._initCustomFieldsEditor();

		this.getEl('members_list').on('click', '.remove', function() {
			var row = $(this).closest('.member-row');
			var personId = row.data('person-id');
			if (!personId) {
				return;
			}

			row.fadeOut('fast');

			$.ajax({
				url: BASE_URL + 'agent/organizations/' + self.meta.org_id + '/ajax-save',
				data: { action: 'remove-person', person_id: personId },
				type: 'POST',
				context: this,
				error: function() {
					row.show();
				},
				success: function() {
					row.remove();
					DeskPRO_Window.util.modCountEl(self.getEl('members_count'), '-');
				}
			});
		});

		this.getEl('add_searchbox').bind('personsearchboxclicknew', function(ev, term, sb) {
			if (DeskPRO_Window.newPersonLoader) {
				DeskPRO_Window.newPersonLoader.open(function(page) {
					page.setGuessTerm(term);
					page.setOrganization(self.meta.org_id, self.getEl('editname').find('input[name="name"]').val());
				});
			} else {
				DeskPRO_Window.showAlert('You are not allowed to create new people');
			}

			sb.close();
			sb.reset();
		});

		this.getEl('add_searchbox').bind('personsearchboxclick', function(ev, personId, name, email, sb) {
			self.getEl('newmember_person_name').text(name);
			self.getEl('newmember_person_email').text(email);
			self.getEl('newmember_person_id').val(personId);
			self.getEl('newmember_position').val('');

			self.getEl('newmember_row').hide();
			self.getEl('newmember_row_named').show();

			sb.close();
			sb.reset();
		});

		var close_newmember_row = function() {
			self.getEl('add_searchbox_txt').val('');
			self.getEl('newmember_row_named').hide();
			self.getEl('newmember_row').show();
		};

		this.getEl('newmember_cancel_btn').on('click', function() {
			close_newmember_row();
		});

		this.getEl('newmember_btn').on('click', function() {
			var personId = self.getEl('newmember_person_id').val();
			var pos = self.getEl('newmember_position').val();

			$.ajax({
				url: BASE_URL + 'agent/organizations/' + self.meta.org_id + '/ajax-save',
				data: { action: 'add-person', person_id: personId, position: pos },
				type: 'POST',
				context: this,
				success: function(data) {
					self.getEl('newmember_person_input').val('');
					self.getEl('newmember_position').val('');
					self.getEl('newmember_person_id').val('0');

					if (data.already_in_organization) {
						DeskPRO_Window.showAlert('That user is already in an organization');
					} else {
						var row = $(data.row_html);
						row.insertAfter(self.getEl('newmember_row_named'));

						DeskPRO_Window.util.showSavePuff(row);
						DeskPRO_Window.util.modCountEl(self.getEl('members_count'), '+');
					}
				}
			});

			close_newmember_row();
		});


		DeskPRO_Window.getMessageBroker().addMessageListener('new-org-user', function(info) {
			if (!info.organization_id || info.organization_id != self.meta.org_id) {
				return;
			}

			$.ajax({
				url: BASE_URL + 'agent/organizations/' + self.meta.org_id + '/ajax-save',
				data: { action: 'get-person-row', person_id: info.person_id },
				type: 'GET',
				context: this,
				success: function(data) {
					var row = $(data.row_html);
					row.insertAfter(self.getEl('newmember_row_named'));

					DeskPRO_Window.util.showSavePuff(row);
					DeskPRO_Window.util.modCountEl(self.getEl('members_count'), '+');
				}
			});
		}, this);

		this.getEl('members_list').on('click', '.organization-manager-icon', function(ev) {
			ev.stopPropagation();

			var $this = $(this), line = $this.closest('tr');

			line.toggleClass('is-manager');

			$.ajax({
				url: $this.data('save-url'),
				type: 'POST',
				data: { organization_manager: line.hasClass('is-manager') ? 1 : 0 }
			});

			$(this).qtip('hide', ev);
		});

		this.getEl('members_list').on('mouseover', '.organization-manager-icon', function(ev) {
			if ($(this).is('.tipped-inited')) {
				return;
			}

			var qtipOptions = {
				content: {
					text: function() {
						var $this = $(this), line = $this.closest('.line');

						if (line.hasClass('is-manager')) {
							return $this.data('is-manager');
						} else {
							return $this.data('not-manager');
						}
					}
				},
				position: {
					my: 'top center',
					at: 'bottom center',
					viewport: $(window)
				},
				style: {
					classes: 'ui-tooltip-shadow ui-tooltip-rounded'
				}
			};

			$(this).qtip(qtipOptions).qtip('show', ev);
			$(this).addClass('tipped-inited');
		});

		this.getEl('members_list').on('click', '.position-edit-trigger', function(ev) {
			ev.stopPropagation();

			var row = $(this).closest('.member-row');
			var label = $('.position-label', row);
			var input = $('.position-edit', row);
			var inputTxt = $('.position-edit input', row);

			label.fadeOut('fast', function() {
				input.fadeIn('fast');
				input.get(0).focus();
			});

			var done = function() {
				var val = inputTxt.val();
				if (val) {
					label.text(val);
				} else {
					label.empty().append($('<span style="font-size: 11px; color: #959595;">No position set</span>'));
				}

				input.fadeOut('fast', function() {
					label.fadeIn('fast');
				});

				$.ajax({
					url: inputTxt.data('save-url'),
					type: 'POST',
					data: { organization_position: val }
				});
			};

			if (!input.is('.has-init')) {
				input.addClass('has-init');
				input.on('click', function(ev){ev.stopPropagation();});
				input.on('keypress', function() {
					if (ev.keyCode == 13) {
						done();
					}
				});
				var closest = input.closest('.popover-wrapper');
				$(closest.length ? closest : document).on('click', done);
			}
		});

		$('.profile-box-container.tabbed', this.wrapper).each(function() {
			var simpleTabs = new DeskPRO.UI.SimpleTabs({
				triggerElements: '> header li',
				context: this
			});

			self.ownObject(simpleTabs);
		});

		$('.new-note textarea', this.getEl('notes_tab')).TextAreaExpander(40, 225);

		var summaryTxt = this.getEl('summary').TextAreaExpander(40, 225);

		if (this.meta.perms.edit) {
			this._initEmailDomainAssoc();
		}

		this.refreshPropBox();

		var fieldsRendered = this.getEl('custom_fields_rendered');
		var fieldsForm = this.getEl('custom_fields_editable');
		var box = $('.profile-box-container.properties ', el);

		var propToggle = function(what) {
			if (what == 'display') {
				$('.prop-edit-trigger', box).show();
				$('.is-loading', box).hide();
				$('.save', box).hide();
				$('.cancel', box).hide();
				fieldsForm.hide();
				fieldsRendered.show();
			} else {
				if (!fieldsForm.hasClass('dp-has-init')) {
					fieldsForm.addClass('dp-has-init');
					fieldsForm.find('.Date.customfield input').datepicker({
						dateFormat: 'yy-mm-dd',
						showButtonPanel: true,
						beforeShow: function(input) {
							setTimeout(function() {
								var buttonPane = $(input).datepicker("widget").find(".ui-datepicker-buttonpane");

								buttonPane.find('button:first').remove();

								var btn = $('<button class="ui-datepicker-current ui-state-default ui-priority-secondary ui-corner-all" type="button">Clear</button>');
								btn.unbind("click").bind("click", function () { $.datepicker._clearDate( input ); });
								btn.appendTo( buttonPane );

								$(input).datepicker("widget").css('z-index', 30001);
							},1);
						}
					});
				}

				$('.prop-edit-trigger', box).hide();
				$('.is-loading', box).hide();
				$('.save', box).show();
				$('.cancel', box).show();
				fieldsRendered.hide();
				fieldsForm.show();
			}
		};

		$('.prop-edit-trigger', box).on('click', function() {
			propToggle('form');
		});
		$('.save', box).on('click', function() {
			var formData = $('input[type="text"], input[type="password"], input:checked, select, textarea', fieldsForm);

			$('.is-loading', box).show();
			$('.save', box).hide();
			$('.cancel', box).hide();

			$.ajax({
				url: BASE_URL + 'agent/organizations/' + self.meta.org_id + '/ajax-save-custom-fields',
				type: 'POST',
				data: formData,
				dataType: 'html',
				success: function(rendered) {
					fieldsRendered.empty().html(rendered);
					propToggle('display');
				}
			});
		});
		$('.cancel', box).on('click', function() {
			propToggle('display');
		});

		if (!this.meta.perms.edit) {
			var contactBox = $('.profile-box-container.contact', this.el);
			if (!contactBox.find('> section > .table-content > *')[0]) {
				contactBox.hide();
			}
		}

		this.initUgEditor();
		this.initSlaEditor();
	},

	refreshPropBox: function() {

		var contactBox = $('.profile-box-container.contact', this.el);

		var has = false;
		if ($('.contact-data-list > li', contactBox).length) {
			has = true;
		}

		if (!has && $('.outside-display > *', contactBox).length) {
			has = true;
		}

		if (!has && $('.addresses > *', contactBox).length) {
			has = true;
		}

		if (!has) {
			contactBox.addClass('no-section');
		} else {
			contactBox.removeClass('no-section');
		}
	},

	//#########################################################################
	//# Email domain associations
	//###########	##############################################################

	_initEmailDomainAssoc: function(opennow) {

		if (this.emailDomainOverlay) {
			this.emailDomainOverlay.destroy();
		}

		var self = this;

		var contain = this.getEl('email_assoc_box');
		var trigger = $('.email-assoc-edit', contain);
		var overlayEl = $('.email-assoc-overlay', contain);

		var newInput = $('input.new-domain', contain);
		var newContain = $('.profile-box-container.new', contain);

		this.emailDomainOverlay = null;

		var updateNew = function() {
			if (newInput.val().trim()) {
				$('.controls .save', newContain).show();
			} else {
				$('.controls .save', newContain).hide();
			}
		};

		var open = function() {
			if (!self.emailDomainOverlay) {
				self.emailDomainOverlay = new DeskPRO.UI.Overlay({
					triggerElement: trigger,
					contentElement: overlayEl,
					zIndex: 30001
				});

				newInput.on('keyup', updateNew).on('change', updateNew);

				var replaceEditor = function(newDisplayHtml) {
					self.getEl('email_assoc_box').empty().html(newDisplayHtml);
					self._initEmailDomainAssoc(true);
				};

				$('.controls .save', newContain).on('click', function() {
					$.ajax({
						url: BASE_URL + 'agent/organizations/' + self.meta.org_id + '/assign-domain',
						type: 'POST',
						data: { domain: newInput.val().trim() },
						dataType: 'html',
						success: function(newDisplayHtml) {
							replaceEditor(newDisplayHtml);
						}
					});
				});

				var delBtn = $('.delete-button', overlayEl);
				var cancelDelBtn = $('.cancel-delete-button', overlayEl);
				var delSection = $('.delete-controls', overlayEl);

				delBtn.on('click', function() {
					var sect = $(this).closest('tr');
					var delBtn = $('.delete-button', sect);
					var cancelDelBtn = $('.cancel-delete-button', sect);
					var delSection = $('.delete-controls', sect);

					$('.delete-controls', sect).slideDown('fast');
					delBtn.hide();
					cancelDelBtn.show();
					delSection.slideDown('fast');
				});
				cancelDelBtn.on('click', function() {
					var sect = $(this).closest('tr');
					var delBtn = $('.delete-button', sect);
					var cancelDelBtn = $('.cancel-delete-button', sect);
					var delSection = $('.delete-controls', sect);

					cancelDelBtn.hide();
					delBtn.show();
					delSection.slideUp('fast');
				});

				$('.remove-email, .remove-email-users', overlayEl).on('click', function() {
					var domain = $(this).closest('tr').data('org-domain');
					var removeusers = $(this).is('.remove-email-users') ? 1 : 0;

					$.ajax({
						url: BASE_URL + 'agent/organizations/' + self.meta.org_id + '/unassign-domain',
						type: 'POST',
						data: { domain: domain, remove_users: removeusers },
						dataType: 'html',
						success: function(newDisplayHtml) {
							self.emailDomainOverlay.destroy();
							self.closeSelf();
							DeskPRO_Window.runPageRoute('page:' + BASE_URL + 'agent/organizations/' + self.meta.org_id);
						}
					});
				});

				$('.move-users', overlayEl).on('click', function() {
					var domain = $(this).closest('tr').data('org-domain');

					$.ajax({
						url: BASE_URL + 'agent/organizations/' + self.meta.org_id + '/domain/move-users',
						type: 'POST',
						data: { domain: domain },
						dataType: 'html',
						success: function(newDisplayHtml) {
							self.emailDomainOverlay.destroy();
							self.closeSelf();
							DeskPRO_Window.runPageRoute('page:' + BASE_URL + 'agent/organizations/' + self.meta.org_id);
						}
					});
				});

				$('.move-all-users', overlayEl).on('click', function() {
					var domain = $(this).closest('tr').data('org-domain');

					$.ajax({
						url: BASE_URL + 'agent/organizations/' + self.meta.org_id + '/domain/reassign-users',
						type: 'POST',
						data: { domain: domain },
						dataType: 'html',
						success: function(newDisplayHtml) {
							self.emailDomainOverlay.destroy();
							self.closeSelf();
							DeskPRO_Window.runPageRoute('page:' + BASE_URL + 'agent/organizations/' + self.meta.org_id);
						}
					});
				});
			}

			self.emailDomainOverlay.open();
		};

		trigger.on('click', open);
		if(opennow) open();
	},

	//#########################################################################
	//# Custom fields
	//#########################################################################

	_initCustomFieldsEditor: function() {

		var fieldsRenderedWrap, fieldsEditWrap;

		fieldsRenderedWrap = this.fieldsRenderedWrap = this.getEl('custom_fields_rendered');
		fieldsEditWrap = this.fieldsEditWrap = this.getEl('custom_fields_editable');

		var toggle = (function() {
			if (fieldsRenderedWrap.is(':visible')) {
				fieldsRenderedWrap.hide();
				fieldsEditWrap.show();
			} else {
				fieldsEditWrap.hide();
				fieldsRenderedWrap.show();
			}
		}).bind(this);

		$('.show-edit-custom-fields', this.wrapper).on('click', function() {
			toggle();
		});

		$('.save-custom-fields', this.wrapper).on('click', (function() {
			var formData = $('input, select, textarea', fieldsEditWrap).serializeArray();

			$.ajax({
				url: BASE_URL + 'agent/organizations/' + this.meta.org_id + '/ajax-save-custom-fields',
				type: 'POST',
				data: formData,
				dataType: 'html',
				success: function(rendered) {
					fieldsRenderedWrap.empty().html(rendered);
					toggle();
				}
			});
		}).bind(this));
	},

	//#########################################################################
	//# Labels
	//#########################################################################

	_initLabels: function() {
		if (this.getEl('labels_input')[0]) {
			this.labelsInput = new DeskPRO.UI.LabelsInput({
				type: 'tickets',
				input: this.getEl('labels_input'),
				onChange: this.saveLabels.bind(this)
			});
			this.ownObject(this.labelsInput);
		}
	},

	saveLabels: function() {
		this._doSaveLabels();
	},

	_doSaveLabels: function() {
		var data = this.labelsInput.getFormData();

		$.ajax({
			url: this.getMetaData('labelsSaveUrl'),
			type: 'POST',
			context: this,
			data: data,
			dataType: 'json'
		});
	},

	initUgEditor: function() {
		var self = this;
		var ugbox = this.getEl('ug_box');

		var editBtn   = ugbox.find('.edit-trigger');
		var cancelBtn = ugbox.find('.cancel-trigger');
		var saveBtn   = ugbox.find('.save-trigger');

		var displayBox = this.getEl('ug_display_box');
		var editBox    = this.getEl('ug_edit_box');

		var showEdit = function() {
			ugbox.removeClass('loading');
			editBtn.hide();
			saveBtn.show();
			cancelBtn.show();
			displayBox.hide();
			editBox.show();
			ugbox.removeClass('no-section').find('> section').show();
		};
		var showSaving = function() {
			ugbox.addClass('loading');
			editBtn.hide();
			saveBtn.hide();
			cancelBtn.hide();
			displayBox.show();
			editBox.hide();

			if (ugbox.find(':checkbox.ug-check:checked').length) {
				ugbox.find('> section').show();
				ugbox.removeClass('no-section');
			} else {
				ugbox.find('> section').hide();
				ugbox.addClass('no-section');
			}
		};
		var showNormal = function() {
			ugbox.removeClass('loading');
			editBtn.show();
			saveBtn.hide();
			cancelBtn.hide();
			displayBox.show();
			editBox.hide();

			if (ugbox.find(':checkbox.ug-check:checked').length) {
				ugbox.find('> section').show();
				ugbox.removeClass('no-section');
			} else {
				ugbox.find('> section').hide();
				ugbox.addClass('no-section');
			}
		};

		editBtn.on('click', function() {
			showEdit();
		});
		cancelBtn.on('click', function() {
			showNormal();
		});
		saveBtn.on('click', function() {
			var formData = editBox.find(':checkbox.ug-check:checked').serializeArray();
			formData.push({name: 'action', value: 'set-usergroups'});

			displayBox.find('li.ug-row').hide();
			if (ugbox.find(':checkbox.ug-check:checked').length) {
				ugbox.find('> section').show();
				ugbox.removeClass('no-section');
			} else {
				ugbox.find('> section').hide();
				ugbox.addClass('no-section');
			}

			ugbox.find(':checkbox.ug-check:checked').each(function() {
				var id = $(this).val();
				displayBox.find('li.ug-row-' + id).show();
			});

			showSaving();
			$.ajax({
				url: BASE_URL + 'agent/organizations/' + self.meta.org_id + '/ajax-save',
				type: 'POST',
				dataType: 'json',
				data: formData,
				context: this,
				complete: function() {
					showNormal();
				},
				success: function(data) {
					showNormal();
				}
			});
		});
	},

	initSlaEditor: function() {
		var self = this;
		var slaBox = this.getEl('sla_box');
		if (!slaBox.length) {
			return;
		}

		var editBtn   = slaBox.find('.edit-trigger');
		var cancelBtn = slaBox.find('.cancel-trigger');
		var saveBtn   = slaBox.find('.save-trigger');
		var noSlas     = slaBox.find('.no-slas');

		var displayBox = this.getEl('sla_display_box');
		var editBox    = this.getEl('sla_edit_box');

		var showEdit = function() {
			slaBox.removeClass('loading');
			editBtn.hide();
			saveBtn.show();
			cancelBtn.show();
			displayBox.hide();
			editBox.show();
		};
		var showSaving = function() {
			slaBox.addClass('loading');
			editBtn.hide();
			saveBtn.hide();
			cancelBtn.hide();
			displayBox.show();
			editBox.hide();
		};
		var showNormal = function() {
			slaBox.removeClass('loading');
			editBtn.show();
			saveBtn.hide();
			cancelBtn.hide();
			displayBox.show();
			editBox.hide();
		};

		editBtn.on('click', function() {
			showEdit();
		});
		cancelBtn.on('click', function() {
			showNormal();
		});
		saveBtn.on('click', function() {
			var checks = editBox.find(':checkbox.sla-check:checked');

			var formData = checks.serializeArray();
			formData.push({name: 'action', value: 'set-slas'});

			displayBox.find('li.sla-row').hide();

			checks.each(function() {
				var id = $(this).val();
				displayBox.find('li.sla-row-' + id).show();
			});

			if (checks.length) {
				noSlas.hide();
			} else {
				noSlas.show();
			}

			showSaving();
			$.ajax({
				url: BASE_URL + 'agent/organizations/' + self.meta.org_id + '/ajax-save',
				type: 'POST',
				dataType: 'json',
				data: formData,
				context: this,
				complete: function() {
					showNormal();
				},
				success: function(data) {
					showNormal();
				}
			});
		});
	}
});
