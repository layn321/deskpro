Orb.createNamespace('DeskPRO.Agent.ElementHandler');

DeskPRO.Agent.ElementHandler.SettingsWindow = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	initPage: function() {
		window.SETTINGS_WINDOW = this;
		this.el.bind('dp_open', this.open.bind(this));
		this.el.bind('dp_close', this.close.bind(this));
	},

	_lazyInit: function() {
		var self = this;

		this.updatePos();

		if (this._hasInit) return;
		this._hasInit = true;

		$('.close-trigger', this.el).first().on('click', function(ev) {
			ev.stopPropagation();
			ev.preventDefault();
			self.el.trigger('dp_close');
		});

		this.topTabs = new DeskPRO.UI.SimpleTabs({
			triggerElements: $('#settingswin_nav > li'),
			context: this.el,
			onTabSwitch: function(ev) {
				var wrapper = $(ev.tabContent);
				$('#settingswin_pages').find('> section.on').removeClass('on');

				wrapper.addClass('on');

				if (!wrapper.data('page-fragment')) {
					self._loadPageForTabTarget(wrapper);
				} else {
					wrapper.data('page-fragment').fireEvent('activate');
				}
			}
		});

		this.backdrop = $('<div class="backdrop fade" />').hide().appendTo('body').css('z-index', 32000);
		this.backdrop.click(this.close.bind(this));

		DeskPRO_Window.layout.addEvent('resized', function() {
			self.updatePos();
		});
	},

	updatePos: function() {
		this.el.css({
			top: 54,
			bottom: 10,
			width: 850,
			left: ($(window).width() - 700) / 2
		});

		var overlay = this.el.find('.dp-overlay');
		var winH = Math.min($(window).height() - 150, 700);

		overlay.css({
			'max-height': winH,
			'height': 'auto'
		});
	},

	_loadPageForTabTarget: function(wrapper) {
		var self = this;
		$.ajax({
			dataType: 'text',
			url: wrapper.data('page-url'),
			type: 'GET',
			context: this,
			success: function(html) {
				var page = DeskPRO_Window.createPageFragment(html);
				page.settingsWindow = self;
				wrapper.html(page.html);
				delete page.html;

				page.fireEvent('render', [wrapper]);
				page.fireEvent('activate');

				wrapper.data('page-fragment', page);
			}
		});
	},

	reloadTab: function(name) {
		var tab = $('#settingswin_nav li.tab-' + name);
		var target = $(tab.data('tab-for'));

		var page = target.data('page-fragment');
		if (page) {
			page.fireEvent('destroy');
		}

		target.empty();
		target.append('<div class="page-loading"></div>');

		if (tab.is('.on')) {
			this._loadPageForTabTarget(target);
		}
	},

	_cleanupOld: function() {
		// TODO
		// after settings window is hidden for a while, clear all of the page fragments to reduce memory
	},

	showSavePuff: function() {
		$('#settingswin_saved_overlay').fadeIn(250, function() {
			$('#settingswin_pages .on').scrollTop(0);

			window.setTimeout(function() {
				$('#settingswin_saved_overlay').fadeOut(250);
			}, 1200);
		});
	},

	open: function(ev, tabName, activateView) {
		this.el.data('activateView', activateView);
		if (tabName) {
			var tabEl = $('#settingswin_nav > li.tab-' + tabName);
		}
		if (tabName && !this._hasInit) {
			$('#settingswin_nav > li').removeClass('on');
			tabEl.addClass('on');
		}

		this._lazyInit();

		if (tabName && tabEl) {
			this.topTabs.activateTab(tabEl);
		}

		if (this._cleanupTimer) {
			window.clearTimeout(this._cleanupTimer);
			this._cleanupTimer = null;
		}

		this.el.show();
		this.backdrop.show();

		if ($('#userSetting').data('backdrop')) {
			$('#userSetting').data('backdrop').remove();
			$('#userSetting').hide();
		}
	},

	isOpen: function() {
		if (this._hasInit && this.el.is(':visible')) {
			return true;
		}

		return false;
	},

	close: function() {
		if (this.isOpen()) {
			this.el.hide();
			this.backdrop.hide();

			this._cleanupTimer = window.setTimeout(this._cleanupOld.bind(this), 180000); // three minutes

			if (this.reloadInterface) {
				DeskPRO_Window.util.reloadInterface();
			}
		}
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.SettingsPage');

DeskPRO.Agent.PageFragment.SettingsPage.Profile = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'settings_profile';
	},

	initPage: function(el) {
		var self = this;
		this.el = el;

		var form = $('form', this.el);

		if (this.el.find('.dp-form-row.new-picture')[0]) {
			DeskPRO_Window.util.fileupload(this.el.find('.dp-form-row.new-picture'));
			this.el.find('.dp-form-row.new-picture').bind('fileuploadadd', function() {
				$('.files', form).empty();
			});
		}

		var startEmail = $('#settings_profile_email').val();

		var changePass = false;
		var changeEmail = false;

		var verifyPasswords = function() {
			var pass1 = $('input.password1', form);
			var pass2 = $('input.password2', form);

			// Form might not have password fields if agent is from a usersource
			if (!pass1[0]) {
				changePass = false;
				return true;
			}

			if (pass1.val().length) {
				changePass = true;
				if (pass1.val() != pass2.val()) {
					DeskPRO_Window.showAlert('Please enter the same password into both password fields', 'error');
					return false;
				}
			} else {
				changePass = false;
			}
			return true;
		};

		var checkEmailChange = function() {
			if ($('#settings_profile_email').val() != startEmail) {
				changeEmail = true;
			} else {
				changeEmail = false;
			}
		};

		var passCode = null;

		form.on('submit', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			if (!verifyPasswords()) {
				return;
			}
			checkEmailChange();

			if ((changePass || changeEmail) && !passCode) {
				$('#password_confirm').trigger('dp_open', {
					explain: "Confirm these changes to your profile by entering your current password.",
					success: function(code) {
						passCode = code;
						form.submit();
					}
				});
				return;
			}

			var data = $(this).serializeArray();
			if (passCode) {
				data.push({
					name: 'authcode',
					value: passCode
				});
			}
			$.ajax({
				url: $(this).attr('action'),
				type: 'POST',
				data: data,
				dataType: 'json',
				complete: function() {
					changePass = false;
					changeEmail = false;
					passCode = null;
					$('input.password1', form).val('');
					$('input.password2', form).val('');
				},
				success: function(data) {

					if (data.form_errors) {

						$('#agent_settings_win_errors').find('li').hide();
						Array.each(data.form_errors, function (code) {
							var classname = code.replace(/\./g, '_');
							$('#agent_settings_win_errors').find('li.' + classname).show();
						});
						$('#agent_settings_win_errors').show();

						return;
					}

					$('#agent_settings_win_errors').hide();

					if (data.login) {
						DeskPRO_Window.util.reloadInterface();
						return;
					}

					self.settingsWindow.showSavePuff();
					startEmail = $('#settings_profile_email').val();
					self.settingsWindow.reloadInterface = true;
					self.settingsWindow.reloadTab('profile');
				}
			});
		});

		if (window.webkitNotifications) {
			var notificationsRow = el.find('.dp-desktop-notifications');
			notificationsRow.show();

			var enableButton = notificationsRow.find('.enable-desktop-notifications');

			var permissionCallback = function() {
				var permission = window.webkitNotifications.checkPermission();

				if (permission == 0) {
					// granted
					enableButton.hide();
					notificationsRow.find('.dp-desktop-notifications-enabled').show();
					notificationsRow.find('.dp-desktop-notifications-disabled').hide();
				} else if (permission == 1) {
					// no action
					enableButton.show();
					notificationsRow.find('.dp-desktop-notifications-enabled').hide();
					notificationsRow.find('.dp-desktop-notifications-disabled').hide();
				} else {
					// explicitly denied
					enableButton.hide();
					notificationsRow.find('.dp-desktop-notifications-enabled').hide();
					notificationsRow.find('.dp-desktop-notifications-disabled').show();
				}
			};

			permissionCallback();

			this.addEvent('updateUi', function() {
				permissionCallback();
			});

			enableButton.click(function(e) {
				e.preventDefault();
				window.webkitNotifications.requestPermission(permissionCallback);
			});

			notificationsRow.find('.generate-test-notification').click(function(e){
				e.preventDefault();

				if (window.webkitNotifications.checkPermission() != 0) {
					return;
				}

				var notification = window.webkitNotifications.createNotification(
					'', 'DeskPRO', 'This is a test DeskPRO desktop notification.'
				);
				notification.ondisplay = function() {
					setTimeout(function() {
						notification.cancel();
					}, 60 * 1000);
				};
				notification.show();
			});
		}

		// Email Addresses
		el.find('.more_emails_empty').find('a').on('click', function(ev) {
			Orb.cancelEvent(ev);
			el.find('.more_emails_empty').hide();
			el.find('.more_emails').show();
		});

		var moreEmails  = el.find('.more_emails');
		var addEmailTxt = el.find('.more_emails_txt');

		el.find('.more_emails_trigger').on('click', function(ev) {
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
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.SettingsPage');

DeskPRO.Agent.PageFragment.SettingsPage.Signature = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'settings_signature';
	},

	initPage: function(el) {
		var self = this;
		this.el = el;

		var form = $('form', this.el);

		var textarea = $('#agent_settings_signature');

		if (DeskPRO_Window.canUseAgentReplyRte() && textarea.data('rte') == 1) {
			textarea.val($('#agent_settings_signature_html').val());
			DeskPRO_Window.initRteAgentReply(textarea, {
				defaultIsHtml: true,
				inlineHiddenPosition: $('#agent_settings_is_html_signature'),
				minHeight: 100
			});
			$('#agent_settings_is_html_signature').val(1);
		} else {
			textarea.val($('#agent_settings_signature_text').val());
		}

		form.on('submit', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			if (textarea.syncCode) {
				textarea.syncCode();
			}

			var data = $(this).serializeArray();
			$.ajax({
				url: $(this).attr('action'),
				type: 'POST',
				data: data,
				dataType: 'json',
				success: function(data) {
					self.settingsWindow.showSavePuff();
				}
			});
		});
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.SettingsPage');

DeskPRO.Agent.PageFragment.SettingsPage.TicketNotifications = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'settings_ticket_notifications';
	},

	initPage: function(el) {
		var self = this;
		this.el = el;

		this.typeTabs = new DeskPRO.UI.SimpleTabs({
			triggerElements: $('.pageheader li', el),
			effect: 'fade'
		});

		var form = $('form', this.el);

		form.on('submit', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			var data = $(this).serializeArray();
			$.ajax({
				url: $(this).attr('action'),
				type: 'POST',
				data: data,
				dataType: 'json',
				success: function() {
					self.settingsWindow.showSavePuff();
				}
			});
		});

		var checks = this.el.find(':checkbox');
		Array.each(['email', 'alert'], function(type) {
			checks.filter('[name^="filter_sub[5]['+type+'"]').each(function() {
				$(this).on('click', function() {
					if (this.checked) {
						var name = $(this).attr('name').replace(/^.*?\[([a-zA-Z_]+)\]$/, '$1');
						checks.filter('[name$="['+name+']"]').prop('checked', true);
					}
				});
			});
		});
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.SettingsPage');

DeskPRO.Agent.PageFragment.SettingsPage.OtherNotifications = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'settings_other_notifications';
	},

	initPage: function(el) {
		var self = this;
		this.el = el;

		this.typeTabs = new DeskPRO.UI.SimpleTabs({
			triggerElements: $('.pageheader li', el)
		});

		var form = $('form', this.el);

		form.on('submit', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			var data = $(this).serializeArray();
			$.ajax({
				url: $(this).attr('action'),
				type: 'POST',
				data: data,
				dataType: 'json',
				success: function() {
					self.settingsWindow.showSavePuff();
				}
			});
		});
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.SettingsPage');

DeskPRO.Agent.PageFragment.SettingsPage.Macros = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'settings_macros';
	},

	initPage: function(el) {
		var self = this;
		this.el = el;

		$('#settingswin').bind('dp_settings_macrosupdated', function() {
			self.settingsWindow.reloadTab('macros');
			window.DESKPRO_MACRO_LABELS = null;
		});

		var event = jQuery.Event("dp_macros_updated");
		event.macroItems = [];
		this.el.find('.obj-macro').each(function(ev) {
			event.macroItems.push({
				id: $(this).data('macro-id'),
				title: $(this).text()
			});
		});
		$('#settingswin').trigger(event);

		this.el.on('click', '.delete-macro', function() {
			var row = $(this).closest('tr');
			var url = $(this).data('delete-url');
			var macroId = $(this).data('macro-id');

			DeskPRO_Window.showConfirm('Are you sure you want to permanantly delete this macros?', function() {
				$.ajax({
					url: url,
					success: function() {
						row.fadeOut(function() {
							row.remove();
						});

						$('.res-ticketmacro-'+ macroId).remove();
					}
				});
			});
		});
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.SettingsPage');

DeskPRO.Agent.PageFragment.SettingsPage.MacroEdit = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'settings_macro_edit';
	},

	initPage: function(el) {
		var self = this;
		this.el = el;

		var actTpl  = this.getEl('actions_tpl');
		var actList = this.getEl('actions_list');

		var editor = new DeskPRO.Form.RuleBuilder(actTpl);
		editor.addEvent('newRow', function(new_row) {
			$('.remove', new_row).on('click', function() {
				new_row.remove();
			});
		});
		$('.add-term', actList).data('add-count', 0).on('click', function() {
			var count = parseInt($(this).data('add-count'));
			var basename = 'actions['+count+']';

			$(this).data('add-count', count+1);

			editor.addNewRow($('.search-terms', actList), basename);
			self.el.find('article').first().scrollTop(10000);
		});

		var count = 0;
		var actions = this.meta.actions;
		if (actions) {
			Array.each(actions, function(info, x) {
				var basename = 'actions[initial_' + x + ']';
				editor.addNewRow($('.search-terms', actList), basename, {
					type: info.type,
					op: info.op,
					options: info.options
				});
			});
		}

		this.getEl('save_btn').on('click', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			var form = self.getEl('form');
			var postData = form.serializeArray();

			$.ajax({
				url: form.attr('action'),
				type: 'POST',
				data: postData,
				dataType: 'json',
				success: function() {
					$('#settingswin').trigger('dp_settings_macrosupdated');
					self.fragmentOverlay.close();
				}
			});
		});
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.SettingsPage');

DeskPRO.Agent.PageFragment.SettingsPage.Filters = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.Basic,

	initPage: function(el) {

		var ticketsSection = DeskPRO_Window.sections.tickets_section;

		window.settings_filters_page = this;
		this.el = el;
		var self = this;
		var didChangeFilterVis = false;

		$('#settingswin').bind('dp_settings_filtersupdated', function() {
			self.settingsWindow.reloadInterface = true;
			self.settingsWindow.reloadTab('filters');
		});

		var runningAjax = null;
		var sendUpdate = function() {

			if (runningAjax) {
				runningAjax.abort();
				runningAjax = null;
			}

			var postData = [];
			self.el.find('.filter-hidden-check').each(function() {
				var id = parseInt($(this).val());
				var v;

				if (this.checked) {
					v = 'hidden';
				} else {
					v = '';
				}

				postData.push({
					name: 'prefs[agent.ui.filter-visibility.' + id + ']',
					value: v
				});
			});

			runningAjax = $.ajax({
				type: 'POST',
				url: BASE_URL + 'agent/misc/ajax-save-prefs',
				data: postData
			});
		}

		this.el.find('.filter-hidden-check').on('click', function(ev) {
			if (!DeskPRO_Window || !DeskPRO_Window.sections.tickets_section) {
				return;
			}

			var filterId = $(this).val();

			didChangeFilterVis = true;
			if (this.checked) {
				$('#tickets_outline_custom_filters').find('.filter-' + filterId).addClass('filter-hidden');
				if (!$('#tickets_outline_custom_filters').find('li.filter').not('.filter-hidden')[0]) {
					$('#tickets_outline_custom_filters').find('.no-data').show();
				}
			} else {
				$('#tickets_outline_custom_filters').find('.filter-' + filterId).removeClass('filter-hidden');
				$('#tickets_outline_custom_filters').find('.no-data').hide();
			}

			$('#tickets_outline_custom_filters').find('li').removeClass('first').not('.filter-hidden').first().addClass('first');

			sendUpdate();
		});

		var activateView = $('#settingswin').data('activateView');
		if (activateView) {
			this.activateView(activateView);
		}
	},

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'settings_filters';
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.SettingsPage');

DeskPRO.Agent.PageFragment.SettingsPage.FilterEdit = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'settings_filter_edit';
	},

	initPage: function(el) {
		var self = this;
		this.el = el;

		var ticketsSection = DeskPRO_Window.sections.tickets_section;

		var critTpl = this.getEl('criteria_tpl');
		var critList = this.getEl('criteria_list');

		var editor = new DeskPRO.Form.RuleBuilder(critTpl);
		editor.addEvent('newRow', function(new_row) {
			$('.remove', new_row).on('click', function() {
				new_row.remove();
			});
		});
		$('.add-term', critList).data('add-count', 0).on('click', function() {
			var count = parseInt($(this).data('add-count'));
			var basename = 'terms['+count+']';

			$(this).data('add-count', count+1);

			editor.addNewRow($('.search-terms', critList), basename);
			self.el.find('article').first().scrollTop(10000);
		});

		var count = 0;
		var terms = this.meta.terms;
		if (terms) {
			Array.each(terms, function(info, x) {
				var basename = 'terms[initial_' + x + ']';
				editor.addNewRow($('.search-terms', critList), basename, {
					type: info.type,
					op: info.op,
					options: info.options
				});
			});
		}

		this.getEl('delete_btn').on('click', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			var url = $(this).data('delete-url');
			var filterId = $(this).data('filter-id');

			DeskPRO_Window.showConfirm('Are you sure you want to permanantly delete this filter?', function() {
				$.ajax({
					url: url,
					success: function() {
						var ticketsSection = DeskPRO_Window.sections.tickets_section;
						if (ticketsSection) {
							ticketsSection.removeCustomFilter(filterId);
						}
						self.fragmentOverlay.close();
					}
				});
			});
		});

		this.getEl('save_btn').on('click', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			var form = self.getEl('form');
			var postData = form.serializeArray();

			if (!$('input[name="filter[title]"]').val().trim().length) {
				alert('Enter a filter title');
				return;
			}

			if (!$('select[name^="terms["]').length) {
				alert('Choose at least one criteria');
				return;
			}

			$.ajax({
				url: form.attr('action'),
				type: 'POST',
				data: postData,
				dataType: 'json',
				success: function(data) {

					if (ticketsSection && data) {
						if (data.is_new) {

						} else {
							ticketsSection.updateCustomFilterTitle(data.filter_id, data.filter_title);
						}
					}

					$('#settingswin').trigger('dp_settings_filtersupdated');
					self.fragmentOverlay.close();
				}
			});
		});
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.SettingsPage');

DeskPRO.Agent.PageFragment.SettingsPage.TicketSlas = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.Basic,

	initPage: function(el) {

		this.el = el;
		var self = this;

		var runningAjax = null;
		var sendUpdate = function() {

			if (runningAjax) {
				runningAjax.abort();
				runningAjax = null;
			}

			var postData = [];
			self.el.find('.sla-hidden-check').each(function() {
				var id = parseInt($(this).val());
				var v;

				if (this.checked) {
					v = 'hidden';
				} else {
					v = '';
				}

				postData.push({
					name: 'prefs[agent.ui.sla.filter-visibility.' + id + ']',
					value: v
				});
			});

			postData.push({
				name: 'prefs[agent.ui.sla.ticket-filter]',
				value: el.find('.grouping-option').val()
			});

			runningAjax = $.ajax({
				type: 'POST',
				url: BASE_URL + 'agent/misc/ajax-save-prefs',
				data: postData
			});
		}

		el.find('.grouping-option').on('change', function() {
			$('#ticket_slas_header').data('sla-filter', $(this).val());
			sendUpdate();

			if (!DeskPRO_Window || !DeskPRO_Window.sections.tickets_section) {
				return;
			}

			DeskPRO_Window.sections.tickets_section.getUpdatedSlaCounts();
		});

		this.el.find('.sla-hidden-check').on('click', function(ev) {
			if (!DeskPRO_Window || !DeskPRO_Window.sections.tickets_section) {
				return;
			}

			var filterId = $(this).val();

			didChangeFilterVis = true;
			if (this.checked) {
				$('#tickets_outline_slas').find('.sla-' + filterId).addClass('filter-hidden');
				if ($('#tickets_outline_slas').find('li').not('.filter-hidden')[0]) {
					$('#tickets_outline_slas').find('.no-data').show();
				}
			} else {
				$('#tickets_outline_slas').find('.sla-' + filterId).removeClass('filter-hidden');
				$('#tickets_outline_slas').find('.no-data').hide();
			}

			sendUpdate();
		});

		var activateView = $('#settingswin').data('activateView');
		if (activateView) {
			this.activateView(activateView);
		}
	},

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'settings_slas';
	}
});

Orb.createNamespace('DeskPRO.Agent.ElementHandler');

DeskPRO.Agent.ElementHandler.MediaManagerWindow = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	initPage: function() {
		window.MEDIA_MANAGER_WINDOW = this;
		this.el.bind('dp_open', this.open.bind(this));
		this.el.bind('dp_close', this.close.bind(this));
		this.boundEditor = null;
	},

	bindToEditor: function(rte) {
		this.boundEditor = rte;
	},

	_lazyInit: function() {
		var self = this;

		if (this._hasInit) return;
		this._hasInit = true;

		$('#mediawin_nav > li').removeClass('on')
		$('#mediawin_pages > section').each(function() {
			var page = $(this).data('page-fragment');
			if (page) {
				page.fireEvent('destroy');
			}
			$(this).data('page-fragment', null);

			$(this).empty();
			$(this).append('<div class="page-loading"></div>');
		});

		this.el.css({
			top: 45,
			bottom: 120,
			width: 780,
			left: ($(window).width() - 780) / 2
		});

		$('.close-trigger', this.el).first().on('click', function(ev) {
			ev.stopPropagation();
			ev.preventDefault();
			self.el.trigger('dp_close');
		});

		this.topTabs = new DeskPRO.UI.SimpleTabs({
			triggerElements: $('#mediawin_nav > li'),
			context: this.el,
			onTabSwitch: function(ev) {
				var wrapper = $(ev.tabContent);
				$('#mediawin_pages .on').removeClass('on');

				wrapper.addClass('on');

				if (!wrapper.data('page-fragment')) {
					self._loadPageForTabTarget(wrapper);
				} else {
					if (wrapper.data('page-fragment').TYPENAME == 'mediawin_browse') {
						self.reloadTab('browser');
					} else {
						self.reloadTab('upload');
					}
				}
			}
		});

		this.backdrop = $('<div class="backdrop fade" />').hide().appendTo('body').css('z-index', 32000);
		this.backdrop.click(this.close.bind(this));
	},

	_loadPageForTabTarget: function(wrapper) {

		if (wrapper.hasClass('loading')) {
			return;
		}

		wrapper.addClass('loading')

		var self = this;
		$.ajax({
			dataType: 'text',
			url: wrapper.data('page-url'),
			type: 'GET',
			context: this,
			complete: function() {
				wrapper.removeClass('loading')
			},
			success: function(html) {
				var page = DeskPRO_Window.createPageFragment(html);
				page.mediaWindow = self;
				wrapper.html(page.html);
				delete page.html;

				page.fireEvent('render', [wrapper]);
				page.fireEvent('activate');

				wrapper.data('page-fragment', page);
			}
		});
	},

	reloadTab: function(name) {
		var tab = $('#mediawin_nav li.tab-' + name);
		var target = $(tab.data('tab-for'));

		var page = target.data('page-fragment');
		if (page) {
			page.fireEvent('destroy');
			page.destroy();
		}

		target.empty();
		target.append('<div class="page-loading"></div>');

		if (tab.is('.on')) {
			this._loadPageForTabTarget(target);
		}
	},

	showSavePuff: function() {
		$('#mediawin_saved_overlay').fadeIn(250, function() {
			$('#mediawin_pages .on').scrollTop(0);

			window.setTimeout(function() {
				$('#mediawin_saved_overlay').fadeOut(250);
			}, 1200);
		});
	},

	open: function(ev, tabName, activateView) {

		this._lazyInit();

		var tabEl = null;
		if (tabName) {
			tabEl = $('#mediawin_nav > li.tab-' + tabName);
		}

		if (tabName && tabEl) {
			this.topTabs.activateTab(tabEl);
		}

		this.el.show();
		this.backdrop.show();
	},

	isOpen: function() {
		if (this._hasInit && this.el.is(':visible')) {
			return true;
		}

		return false;
	},

	close: function() {
		if (this.isOpen()) {
			this.el.hide();
			this.backdrop.hide();
		}

		this.boundEditor = null;
	}
});

Orb.createNamespace('DeskPRO.Agent.PageFragment.MediaManagerPage');

DeskPRO.Agent.PageFragment.MediaManagerPage.Upload = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'mediawin_upload';
	},

	initPage: function(wrapper) {
		var self = this;
		this.wrapper = wrapper;

		var el = wrapper.find('.manager-window-content');

		DeskPRO_Window.util.fileupload(el, {
			page: this,
			saveMedia: 1,
			uploadTemplate: $('.template-upload', el),
			downloadTemplate: $('.template-download', el)
		}).bind('fileuploadstart', function() {
			wrapper.find('.upload-control').hide();
		}).bind('fileuploadadd', function(e,data) {
			$('.files', wrapper).empty();
		}).bind('fileuploadcompleted', function(e,data) {
			wrapper.find('.insert-trigger').each(function() {
				var btn = $(this);
				btn.on('click', function(ev) {
					ev.preventDefault();

					if (!window.MEDIA_MANAGER_WINDOW || !MEDIA_MANAGER_WINDOW.boundEditor || !MEDIA_MANAGER_WINDOW.boundEditor.selection) {
						return;
					}

					if (btn.data('is-image') == '1') {
						MEDIA_MANAGER_WINDOW.boundEditor.selection.setContent('<img src="' + btn.data('download-url') + '" />');
					} else {
						MEDIA_MANAGER_WINDOW.boundEditor.selection.setContent('<a href="' + btn.data('download-url') + '">' + btn.data('file-name') + '</a>');
					}

					MEDIA_MANAGER_WINDOW.close();
				});
			});

			// For some reason IE10 will not process click events on the buttons
			// until some sort of action is done. E.g., highlighting some random text or focusing the input box.
			// Focusing the input box is the simplest fix that doesn't alter any behaviour
			wrapper.find('.file-url').focus();
		});

		wrapper.on('click', '.cancel-trigger', function(ev) {
			ev.preventDefault();

			wrapper.find('.upload-control').show();
			wrapper.find('.files').hide();
			self.mediaWindow.reloadTab('upload');
		});
	}
});
Orb.createNamespace('DeskPRO.Agent.PageFragment.MediaManagerPage');

DeskPRO.Agent.PageFragment.MediaManagerPage.Browse = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'mediawin_browse';
	},

	initPage: function(wrapper) {
		this.wrapper = wrapper;

		wrapper.on('click', '.insert-trigger', function(ev) {
			ev.preventDefault();

			if (!window.MEDIA_MANAGER_WINDOW || !MEDIA_MANAGER_WINDOW.boundEditor || !MEDIA_MANAGER_WINDOW.boundEditor.selection) {
				return;
			}

			var btn = $(this);

			if (btn.data('is-image') == '1') {
				MEDIA_MANAGER_WINDOW.boundEditor.selection.setContent('<img src="' + btn.data('download-url') + '" />');
			} else {
				MEDIA_MANAGER_WINDOW.boundEditor.selection.setContent('<a href="' + btn.data('download-url') + '">' + btn.data('file-name') + '</a>');
			}

			MEDIA_MANAGER_WINDOW.close();
		});
	}
});