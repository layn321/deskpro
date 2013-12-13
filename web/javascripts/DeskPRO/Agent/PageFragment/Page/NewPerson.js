Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');

DeskPRO.Agent.PageFragment.Page.NewPerson = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'newperson';
		this.allowDupe = true;
	},

	initPage: function(el) {
		var self = this;
		this.wrapper = el;

		this.form = $('form', this.wrapper).on('submit', function(ev) {
			ev.preventDefault();
		});

		$('button.submit-trigger', this.wrapper).on('click', this.submit.bind(this));

		this._initNameSection();
		this._initOtherSection();

		this.stateSaver = new DeskPRO.Agent.PageHelper.StateSaver({
			stateId: 'newperson',
			listenOn: this.getEl('newperson')
		});
		this.ownObject(this.stateSaver);

		this.getEl('org_searchbox').on('orgsearchboxclick orgsearchboxcreate', function() {
			self.getEl('org_pos').show();
			self.updateUi();
		}).on('orgsearchboxcleared', function() {
			self.getEl('org_pos').hide();
			self.updateUi();
		});

        DeskPRO_Window.util.fileupload(el, {
            uploadTemplate: $('.template-upload', el),
            downloadTemplate: $('.template-download', el),
            url: BASE_URL + 'agent/misc/parse-vcard'
        });

        el.bind('fileuploaddone', function(event, data) {
            for(name in data.result[0].fields) {
                $('[name$="['+name+']"]', el).val(data.result[0].fields[name]);
            }
        });
        el.bind('fileuploadstart', function(event, data) {
        });

		self.wrapper.find('.dpe_select').each(function() {
			// Label input is handled by labels class
			if (!$(this).hasClass('labels-input')) {
				DP.select($(this));
			}
		});
	},

	markForReload: function() {
		if (!this.markedForReload) {
			this.markedForReload = true;
			this.addEvent('deactivate', this.closeSelf.bind(this));
		}
	},

	closeSelf: function() {
		var ev = {cancel: false};
		this.fireEvent('closeSelf', ev);

		if (!ev.cancel) {
			this.parent();
		}
	},

	submit: function() {
		var formData = this.form.serializeArray();

		$.ajax({
			url: BASE_URL + 'agent/people/new/save',
			type: 'POST',
			data: formData,
			dataType: 'json',
			context: this,
			success: function(data) {
				if (data.success) {
					if (this.getEl('org_id').val().length && this.fromCompanyTab) {
						DeskPRO_Window.getMessageBroker().sendMessage('new-org-user', {
							organization_id: this.getEl('org_id').val(),
							person_id: data.person_id
						});
					} else {
						DeskPRO_Window.runPageRoute('person:' + BASE_URL + 'agent/people/' + data.person_id);
					}

					DeskPRO_Window.getMessageBroker().sendMessage('agent.person.added', { person_id: data.person_id });
					this.closeSelf();
				} else {
					var errorMessages = $('<div/>');
					errorMessages.append('<p>Please correct the following errors with your form:</p>');

					Array.each(data.error_messages, function(msg) {
						errorMessages.append('<div>&bull; ' + msg + '</div>');
					});
					DeskPRO_Window.showAlert(errorMessages, 'error');
				}
			}
		});
	},

	setOrganization: function(org_id, org_name) {
		this.getEl('org_id').val(org_id);
		this.getEl('org_name').val(org_name);
		this.getEl('org_pos').show();
		this.updateUi();
		this.fromCompanyTab = true;
	},

	setGuessTerm: function(term) {
		if (term.indexOf('@') !== -1) {
			this.getEl('email').val(term);
		} else {
			this.getEl('name').val(term);
		}
	},

	//#################################################################
	//# Name/email section
	//#################################################################

	_initNameSection: function() {

	},

	//#########################################################################
	//# Other Section
	//#########################################################################

	_initOtherSection: function() {
		var self = this;
		this.otherTabs = new DeskPRO.UI.SimpleTabs({
			triggerElements: $('li', this.getEl('other_props_tabs')),
			context: this.getEl('other_props_tabs_content'),
			autoSelectFirst: false,
			onTabSwitch: function(eventData) {
				if (!self.labelsInput && eventData.tabContent.hasClass('tab-properties') && self.getEl('labels_input')[0]) {
					self.labelsInput = new DeskPRO.UI.LabelsInput({
						type: 'tickets',
						input: self.getEl('labels_input')
					});
					self.ownObject(self.labelsInput);
				}
			},
			onTabClick: (function(ev) {
				var contentWrap = this.getEl('other_props_tabs_content');
				var navWrap = this.getEl('other_props_tabs_wrap');
				var tab = ev.tabEl;

				// Toggle content state if we're clicking for the first time,
				// or re-clicking a tab
				if (!$('.on', navWrap).length || tab.is('.on')) {
					if (contentWrap.is(':visible')) {
						contentWrap.hide();
						navWrap.removeClass('on');
					} else {
						contentWrap.show();
						navWrap.addClass('on');
					}
				}
			}).bind(this)
		});
		this.ownObject(this.otherTabs);
	}
});
