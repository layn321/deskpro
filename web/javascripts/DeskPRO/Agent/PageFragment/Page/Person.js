Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');
DeskPRO.Agent.PageFragment.Page.Person = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		var self = this;
		this.parent();
		this.TYPENAME = 'person';
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
			'person',
			this.meta.person_id,
			this.meta.title,
			BASE_URL + 'agent/people/' + this.meta.person_id
		);
	},

	initPage: function(el) {
		var self = this;
		this.wrapper = el;
		this.contentWrapper = $('div.layout-content:first', el);

		try {
			var flashEnabled = !!(navigator.mimeTypes["application/x-shockwave-flash"] || window.ActiveXObject && new ActiveXObject('ShockwaveFlash.ShockwaveFlash'));
			if (flashEnabled) {
				// Set timeout to have it exec in global scope,
				// so errors (eg flash has crashed) can be ignored and dont break the rest of this init
				window.setTimeout(function() {
					self.wrapper.find('.copy-btn').each(function() {
						var btnEl = this;
						var btn = $(this);

						try {
							var clip = new ZeroClipboard(this, {
								btnEl: this,
								savePuffEl: self.getEl('idref_switch')
							});
							clip.on('mouseover', function(client, args) {
								$(client.options.btnEl).addClass('over');
							});
							clip.on('mouseout', function(client, args) {
								$(client.options.btnEl).removeClass('over');
							});
							clip.on('complete', function(client, args) {
								DeskPRO_Window.util.showSavePuff($(this).closest('.id-number'));
							});

							self.addEvent('destroy', function() {
								try {
									clip.unglue(btnEl);
								} catch (e) {}
							});
							self.addEvent('activate', function() {
								try {
									clip.reposition();
								} catch (e) {}
							});
						} catch (e) {}
					});
				}, 100);
			} else {
				this.wrapper.find('.copy-btn').remove();
			}
		} catch (e) {
			this.wrapper.find('.copy-btn').remove();
		}

		this.zIndex = 30001;

		var cw = this.contentWrapper;

		if (this.tabBtn) {
			if (this.getMetaData('personPicIcon')) {
				this.tabBtn.find('a').find('i').attr('class', '').addClass('image-icon').css('background-image', 'url("' + this.getMetaData('personPicIcon') + '")').css('background-position', '50% 50%');
			} else if (this.getMetaData('personGravatarIcon')) {
				var defaultIcon = ASSETS_BASE_URL_FULL + 'images/agent/tabs/tabtype-person.png';
				var url = this.getMetaData('personGravatarIcon');
				url = Orb.appendQueryData(url, 'd', defaultIcon);

				var a = this.tabBtn.find('a').find('i');
				a.attr('class', '').addClass('image-icon');
				a.css('background-image', 'url("' + url + '")').css('background-position', '2px 50%');
			}
		}

		// TextExt doesnt play well with fluid columns
		// so this listens for resizes, and then updates the input width,
		// then forces textext to invalidatebounds
		var propBox = self.getEl('properties_box');

		if (this.meta.perms.edit) {
			this.contactEditor = new DeskPRO.Agent.PageFragment.Page.PersonHelper.ContactEditor(this, {
				saveUrl: BASE_URL + 'agent/people/' + this.meta.person_id + '/save-contact-data.json',
				displayEl: this.getEl('contact_display'),
				outsideEl: this.getEl('contact_outside'),
				onReplaceEditor: function() {
					self.refreshPropBox();
				},
				onSuccess: function(data) {
					if (data.changed_primary_email) {
						DeskPRO_Window.util.updateUserEmailAddressDisplay(self.meta.person_id, data.primary_email_address);
					}
				}
			});
			this.ownObject(this.contactEditor);

			var tzMenu = new DeskPRO.UI.Menu({
				menuElement: this.getEl('timezone')
			});
			this.ownObject(tzMenu);

			var autoResMenu = new DeskPRO.UI.Menu({
				menuElement: this.getEl('disable_autoresponses')
			});
			this.ownObject(autoResMenu);

			this.getEl('timezone').on('change', function(){
				var val = $(this).val();
				$('.timezone-info', this.wrapper).empty();
				$.ajax({
					url: BASE_URL + 'agent/people/' + self.meta.person_id + '/ajax-save',
					type: 'POST',
					dataType: 'json',
					data: {
						action: 'timezone',
						timezone: val
					},
					context: this,
					success: function(data) {
						$('.timezone-info', this.wrapper).empty().html(data.bit_html);
					}
				});
			});

			this.getEl('disable_autoresponses').on('change', function(){
				var val = $(this).val();
				$.ajax({
					url: BASE_URL + 'agent/people/' + self.meta.person_id + '/ajax-save',
					type: 'POST',
					dataType: 'json',
					data: {
						action: 'disable_autoresponses',
						disable_autoresponses: val
					}
				});

				self.getEl('disable_autoresponses_reason').remove();
			});

			var namef       = this.getEl('showname');
			var editName    = this.getEl('editname');
			var editTitle   = this.getEl('edittitle');
			var orgpos      = this.getEl('showorgpos');
			var editOrgpos  = this.getEl('editorgpos');
			var startBtn    = this.getEl('editname_start');
			var stopBtn     = this.getEl('editname_end');

			var editTitleChoicesRaw = editTitle.data('choices').split(',');
			var editTitleChoices = [];
			for (var i = 0; i < editTitleChoicesRaw.length; i++) {
				editTitleChoices.push($.trim(editTitleChoicesRaw[i]));
			}
			editTitle.select2({
				initSelection: function(el, callback) {
					var existingTitle = editTitle.val();
					if (existingTitle.length) {
						callback({id: existingTitle, text: existingTitle});
					} else {
						callback({id: '', text: '\u00A0'});
					}
				},
				query: function(query) {
					var inList = function(term) {
						for (var i = 0; i < editTitleChoices.length; i++) {
							if (editTitleChoices[i] == term) {
								return true;
							}
						}

						return false;
					};

					var results = [];

					if (query.term.length) {
						results.push({id: query.term, text: query.term});
					}

					var val = editTitle.val();
					if (val.length && !inList(val) && val != query.term) {
						results.push({id: val, text: val});
					}

					for (var i = 0; i < editTitleChoices.length; i++) {
						var choice = editTitleChoices[i];
						results.push({id: choice, text: choice});
					}

					results.push({id: '', text: '\u00A0'});

					query.callback({results: results});
				}
			});

			var startEditable = function() {
				namef.hide();
				orgpos.hide();
				editName.show();
				editOrgpos.show();
				startBtn.hide();
				stopBtn.show();
			};

			var stopEditable = function() {
				var nametxt = editName.find('input[name=name]').first();
				var titletxt = editName.find('input[name=title_prefix]').first();
				var postxt  = editOrgpos.find('input').first();

				var setName = nametxt.val().trim();
				var setTitle = titletxt.val().trim();
				if (postxt) {
					var setPos  = '';
				} else {
					var setPos  = postxt.val().trim();
				}

				if(!setName) {
					return;
				}

				if (setPos) {
					orgpos.show().find('.org-pos-display').text(setPos);
				} else {
					orgpos.hide();
				}

				editName.hide();
				editOrgpos.hide();
				startBtn.show();
				namef.show();
				orgpos.hide();
				stopBtn.hide();
				namef.text((setTitle ? setTitle + ' ' : '') + setName);

				var postData = [];
				postData.push({
					name: 'action',
					value: 'quick-edit-name'
				});
				postData.push({
					name: 'name',
					value: setName
				});
				postData.push({
					name: 'title_prefix',
					value: setTitle
				});
				postData.push({
					name: 'organization_position',
					value: setPos
				});

				$.ajax({
					url: BASE_URL + 'agent/people/' + self.meta.person_id + '/ajax-save',
					type: 'POST',
					data: postData
				});
			};

			namef.on('dblclick', startEditable).on('keypress', function(ev) {
				if (ev.keyCode == 13 /* enter key */) {
					ev.preventDefault();
					stopEditable();
				}
			});
			editOrgpos.find('input').first().on('keypress', function(ev) {
				if (ev.keyCode == 13 /* enter key */) {
					ev.preventDefault();
					stopEditable();
				}
			});
			this.getEl('editname_start').on('click', startEditable);

			this.getEl('editname_end').on('click', stopEditable);

			$('.contact-list-wrapper', this.wrapper).first().on('click', '.set-primary', function() {
				var email_id = $(this).data('email-id');
				$('.contact-list-wrapper .email.is-primary', self.wrapper).removeClass('is-primary');
				$('.contact-list-wrapper .email-' + email_id, self.wrapper).addClass('is-primary');

				var val = $(this).val();
				$.ajax({
					url: BASE_URL + 'agent/people/' + self.meta.person_id + '/ajax-save',
					type: 'POST',
					dataType: 'json',
					data: {
						action: 'set-primary-email',
						email_id: email_id
					},
					success: function(data) {
						DeskPRO_Window.util.updateUserEmailAddressDisplay(self.meta.person_id, data.primary_email_address);
					}
				});
			});

			$('.contact-list-wrapper', this.wrapper).first().on('click', '.banned', function() {
				var el = $(this);
				var url = $(this).data('unban-url');
				//msg, callback_yes, callback_no, phrase_yes, phrase_no
				DeskPRO_Window.showConfirm("Do you want to unban this email address?", function() {
					el.remove();
					$.ajax({
						url: url,
						dataType: 'json'
					});
				}, function() {}, "Unban", "Cancel");
			});

			this.changePic = new DeskPRO.Agent.PageFragment.Page.PersonHelper.ChangePic(this, {
				loadUrl: BASE_URL + "agent/people/" + this.meta.person_id + "/change-picture-overlay",
				saveUrl: BASE_URL + 'agent/people/' + this.meta.person_id + '/ajax-save'
			});
			this.ownObject(this.changePic);

		} // can edit

		var self = this;
		$('.create-ticket', this.getEl('action_buttons')).on('click', function() {
			DeskPRO_Window.newTicketLoader.open(function(page) {
				var data = {
					person_id: self.meta.person_id,
					email: self.getEl('contact_display').find('li.is-primary.email').data('email-address'),
					name: self.getEl('editname').find('input[name="name"]').val()
				};
				page.setNewByPerson(data);
			});
		});

		var person_id = this.meta.person_id;

		this.moreactionsMenu = new DeskPRO.UI.Menu({
			triggerElement: $('.more', this.getEl('action_buttons')),
			menuElement: this.getEl('more_actions_menu'),
			onItemClicked: function(info) {
				var itemEl = $(info.itemEl), action = itemEl.data('action');

				if (action == 'reset-password') {
					DeskPRO_Window.showPrompt(
						'<div>Enter a new password. The user will be notified.</div>',
						function(val, wrap) {
							var postData = [];
							postData.push({
								name: 'password',
								value: val
							});

							postData.push({
								name: 'send_email',
								value: $('.send_email', wrap).is(':checked')
							});

							postData.push({
								name: 'action',
								value: 'password'
							});

							$.ajax({
								url: BASE_URL + 'agent/people/' + self.meta.person_id + '/ajax-save',
								type: 'POST',
								dataType: 'json',
								data: postData
							});
						}
					);
				} else if (action == 'delete') {
					var el = self.getEl('delete_confirm').clone();
					DeskPRO_Window.showConfirm(
						el,
						function() {
							$.ajax({
								url: $(info.itemEl).data('delete-url'),
								type: 'POST',
								success: function() {
									DeskPRO_Window.getMessageBroker().sendMessage('agent.person.removed', { person_id: person_id });
									DeskPRO_Window.showAlert('The user was deleted');

									var tabs = DeskPRO_Window.getTabWatcher().findTabs('ticket', function(tab) {
										return (tab && tab.page && tab.page && tab.page.meta.person_id == person_id);
									});
									$.each(tabs, function(k, tab) {
										DeskPRO_Window.removePage(tab.page);
									});
								}
							});
							self.closeSelf();
						},
						null,
						null, null,
						400, 260
					);
				} else if (action == 'ban') {
					var el = self.getEl('ban_confirm').clone();
					DeskPRO_Window.showConfirm(
						el,
						function() {
							$.ajax({
								url: $(info.itemEl).data('delete-url'),
								type: 'POST',
								success: function() {
									DeskPRO_Window.getMessageBroker().sendMessage('agent.person.removed', { person_id: person_id });
									DeskPRO_Window.showAlert('The user was deleted and banned');
								}
							});
							self.closeSelf();
						},
						null,
						null, null,
						400, 260
					);
				} else if (action == 'enable-user') {
					$.ajax({
						url: BASE_URL + 'agent/people/' + self.meta.person_id + '/ajax-save',
						type: 'POST',
						dataType: 'json',
						data: {
							action: 'set-is-disabled',
							is_disabled: 0
						}
					});

					var text = itemEl.text();
					itemEl.text(itemEl.data('flip'));
					itemEl.data('flip', text);
					itemEl.data('action', 'disable-user');
					self.getEl('change_user_picture').find('.person-disabled').remove();
				} else if (action == 'disable-user') {
					$.ajax({
						url: BASE_URL + 'agent/people/' + self.meta.person_id + '/ajax-save',
						type: 'POST',
						dataType: 'json',
						data: {
							action: 'set-is-disabled',
							is_disabled: 1
						}
					});

					var text = itemEl.text();
					itemEl.text(itemEl.data('flip'));
					itemEl.data('flip', text);
					itemEl.data('action', 'enable-user');
					self.getEl('change_user_picture').append($('<span class="person-disabled" />'));
				}
			}
		});
		this.ownObject(this.moreactionsMenu);

		this.merge = new DeskPRO.Agent.Widget.Merge({
			tabType: 'person',
			metaId: self.meta.person_id,
			metaIdName: 'person_id',
			menu: this.getEl('merge_menu'),
			trigger: $('.merge', this.getEl('action_buttons')),
			overlayUrl: BASE_URL + 'agent/people/{id}/merge-overlay/{other}',
			mergeUrl: BASE_URL + 'agent/people/{id}/merge/{other}',
			loadRoute: 'person:' + BASE_URL + 'agent/people/{id}',
			overlayLoaded: function(overlay, merge) {
				overlay.getWrapper().find('.person-finder').bind('personsearchboxclick', function(ev, personId, name, email, sb) {
					sb.close();

					$.ajax({
						url: merge._getOverlayUrl(merge.options.metaId, personId),
						type: 'get',
						dataType: 'html',
						success: function(html) {
							merge.resetOverlay(html);
						}
					});
				});
			}
		});
		this.ownObject(this.merge);

		this._initLabels();

		$('.profile-box-container.tabbed', this.wrapper).each(function() {
			var simpleTabs = new DeskPRO.UI.SimpleTabs({
				triggerElements: '> header li',
				context: this
			});

			self.ownObject(simpleTabs);
		});

		this._initOrgEdit();

		var self = this;
		this.getEl('tickets_viewall').on('click', function(ev){
			var row = $(this).closest('tr').remove();
			self.getEl('tickets_rest').slideDown('fast', function() { self.updateUi(); });
		});

		$('.new-note textarea', this.getEl('notes_tab')).TextAreaExpander(40, 225);

		var summaryTxt = this.getEl('summary').TextAreaExpander(40, 225);

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
				self.updateUi();
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
				self.updateUi();
			}
		};

		$('.prop-edit-trigger', box).on('click', function() {
			propToggle('form');
		});
		$('.save', box).on('click', function() {
			var formData = $('input[type="text"], input[type="password"], input:checked, select, textarea', fieldsForm).serializeArray();

			$('.is-loading', box).show();
			$('.save', box).hide();
			$('.cancel', box).hide();

			$.ajax({
				url: BASE_URL + 'agent/person/' + self.meta.person_id + '/ajax-save-custom-fields',
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

		var tabWarn = $('.full-tab-warn', this.el);
		if (tabWarn.length) {
			$('.dismiss-trigger', tabWarn).on('click', function() {
				tabWarn.fadeOut('fast', function() {
					tabWarn.remove();
				});
			});
		}

		if(this.getEl('editname_start').is('.auto-click')) {
			this.getEl('editname_start').click();
		}

		this.initUgEditor();
		this.initSlaEditor();

		if (this.getEl('approve_user')[0]) {

			DeskPRO_Window.getMessageBroker().addMessageListener('agent.person.removed', function(info) {
				DeskPRO_Window.removePage(self);
			}, this);
			DeskPRO_Window.getMessageBroker().addMessageListener('agent.person.confirmed', function(info) {
				if (info.person_id == self.meta.person_id) {
					this.wrapper.find('.validating-bar').remove();
				}
			}, this);

			this.getEl('approve_user').on('click', function(ev) {
				ev.preventDefault();
				DeskPRO_Window.util.ajaxWithClientMessages({
					url: BASE_URL + 'agent/people/validate/approve',
					data: { 'people_ids[]': self.meta.person_id },
					success: function() {
						DeskPRO_Window.removePage(self);
						DeskPRO_Window.runPageRoute('page:'+ BASE_URL + 'agent/people/' + self.meta.person_id);

						DeskPRO_Window.getMessageBroker().sendMessage('agent.person.confirmed', { person_id: self.meta.person_id });
					}
				});
			});
			this.getEl('delete_user').on('click', function(ev) {
				ev.preventDefault();
				DeskPRO_Window.util.ajaxWithClientMessages({
					url: BASE_URL + 'agent/people/validate/delete',
					data: { 'people_ids[]': self.meta.person_id },
					success: function() {
						DeskPRO_Window.getMessageBroker().sendMessage('agent.person.removed', { person_id: self.meta.person_id });
						DeskPRO_Window.removePage(self);
					}
				});
			});
		}

		var vemails = this.getEl('validating_emails');
		if (vemails) {
			vemails.on('click', '.validate-trigger', function() {
				var id = $(this).data('email-id');
				var token = $(this).data('token');

				$.ajax({
					url: BASE_URL + 'agent/people/validate-email/'+id+'/'+token,
					type: 'POST',
					success: function() {
						self.closeSelf();
						DeskPRO_Window.runPageRoute('person:' + BASE_URL + 'agent/people/' + self.meta.person_id);
					}
				})
			});
		}

		this.addEvent('openOrgProfile', function(ev) {
			ev.preventDefault();
			self.getEl('org_box').find('.org_link').trigger('click');
		});
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

		this.updateUi();
	},

	//#########################################################################
	//# Org Edit
	//#########################################################################

	_initOrgEdit: function() {
		var self = this;
		$('.org-edit-trigger, .cancel', this.getEl('org_display_header')).on('click', function(ev) {
			ev.preventDefault();
			self.toggleOrgEdit();
			refreshBox();

			if ($(this).is('.cancel')) {
				if (!parseInt(self.meta.org_id)) {
					$('.org-name', self.getEl('org_edit_wrap')).val('');
					$('.extra-input', self.getEl('org_edit_wrap')).hide();
				}
				self.getEl('org_searchbox').removeClass('is-new').removeClass('is-set');
				self.toggleOrgEdit('close');
			}
		});

		var orgDisplay = this.getEl('org_display_wrap');
		var orgEdit    = this.getEl('org_edit_wrap');

		//orgEnableBtn
		this.getEl('org_searchbox').bind('orgsearchboxclick', function(ev, orgId, name) {
			$('.extra-input', orgEdit).show();
			self.orgEnableBtn('save');
		}).bind('orgsearchboxcreate', function(ev, term, name) {
			$('.extra-input', orgEdit).show();
			self.orgEnableBtn('save');
		}).bind('orgsearchreverted', function(ev, term, name) {
			if (self.getEl('org_searchbox').is('.is-new') || ($('.org-id', orgEdit).val() && $('.org-id', orgEdit).val() != '0')) {
				self.orgEnableBtn('save');
			} else {
				self.orgEnableBtn('cancel');
			}
		});

		$('.extra-input', orgEdit).on('keyup change', function() {
			if (self.getEl('org_searchbox').is('.is-new') || ($('.org-id', orgEdit).val() && $('.org-id', orgEdit).val() != '0')) {
				self.orgEnableBtn('save');
			}
		});

		var refreshBox = function() {
			var box = self.getEl('org_box');
			box.removeClass('no-section');
			if (orgEdit.is(':visible')) {

			} else {
				if (!$('> *', orgDisplay).length) {
					box.addClass('no-section');
				}
			}
		};

		var saveFn = function() {

			self.orgEnableBtn('saving');

			var postData = [];
			postData.push({
				name: 'action',
				value: 'set-organization'
			});
			postData.push({
				name: 'name',
				value: $('.org-name', self.getEl('org_edit_wrap')).val().trim()
			});
			postData.push({
				name: 'id',
				value: $('.org-id', self.getEl('org_edit_wrap')).val().trim()
			});
			postData.push({
				name: 'position',
				value: $('.org-pos-set', self.getEl('org_edit_wrap')).val().trim()
			});

			postData.push({
				name: 'manager',
				value: $('.org-manager-set', self.getEl('org_edit_wrap')).is(':checked') ? 1 : 0
			});

			$.ajax({
				url: BASE_URL + 'agent/people/' + self.meta.person_id + '/ajax-save',
				type: 'POST',
				data: postData,
				dataType: 'json',
				success: function(data) {
					orgDisplay.empty();
					if (data.organization_id) {
						orgDisplay.html(data.html);
						self.meta.org_id = data.organization_id;
					} else {
						self.meta.org_id = 0;
					}

					self.getEl('org_searchbox').removeClass('is-new').removeClass('is-set');

					self.toggleOrgEdit();

					refreshBox();
				}
			});
		};

		this.getEl('org_edit_save').on('click', saveFn);
		this.getEl('org_edit_remove_org').on('click', function() {
			$('.org-id', self.getEl('org_edit_wrap')).val('0');
			$('.org-name', self.getEl('org_edit_wrap')).val('');
			$('.extra-input', orgEdit).hide();
			saveFn();
		});

		refreshBox();
	},

	orgEnableBtn: function(name) {
		var names = ['org-edit-trigger', 'saved', 'save', 'cancel', 'is-loading', 'remove-org'];
		var els = $('.' + names.join(', .'), this.getEl('org_display_header')).hide();
		els.filter('.' + name).show();

		if (name == 'save') {
			els.filter('.cancel').show();
		} else if (name == 'cancel') {
			var orgid = $('.org-id', this.getEl('org_edit_wrap')).val();
			if (orgid && orgid != '0') {
				els.filter('.remove-org').show();
			}
		}

		this.updateUi();
	},

	toggleOrgEdit: function(force) {
		var orgDisplay = this.getEl('org_display_wrap');
		var orgEdit    = this.getEl('org_edit_wrap');

		if ((force && force == 'close') || orgEdit.is(':visible')) {
			orgEdit.hide();
			this.getEl('org_searchbox').data('org-search-box').close();
			orgDisplay.show();
			this.orgEnableBtn('org-edit-trigger');
		} else if ((force && force == 'open') || !orgEdit.is(':visible')) {
			orgDisplay.hide();
			orgEdit.show();
			this.orgEnableBtn('cancel');
		}

		this.updateUi();
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
		this.updateUi();
	},

	_doSaveLabels: function() {
		var data = this.labelsInput.getFormData();

		$.ajax({
			url: this.getMetaData('labelsSaveUrl'),
			type: 'POST',
			context: this,
			data: data,
			dataType: 'json',
			success: function(data) {
				var sect = DeskPRO_Window.sections.people_section;
				if (sect) {
					sect.reloadLabels();
				}
			}
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
		};
		var showNormal = function() {
			ugbox.removeClass('loading');
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
			var formData = editBox.find(':checkbox.ug-check:checked').serializeArray();
			formData.push({name: 'action', value: 'set-usergroups'});

			displayBox.find('li.ug-row').hide();

			ugbox.find(':checkbox.ug-check:checked').each(function() {
				var id = $(this).val();
				displayBox.find('li.ug-row-' + id).show();
			});

			showSaving();
			$.ajax({
				url: BASE_URL + 'agent/people/' + self.meta.person_id + '/ajax-save',
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
				url: BASE_URL + 'agent/people/' + self.meta.person_id + '/ajax-save',
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
