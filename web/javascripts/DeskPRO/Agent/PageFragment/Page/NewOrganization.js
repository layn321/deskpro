Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');

DeskPRO.Agent.PageFragment.Page.NewOrganization = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'neworganization';
		this.allowDupe = true;
	},

	initPage: function(el) {
		var self = this;
		this.wrapper = el;
		this.parent(el);

		this.form = $('form', this.wrapper).on('submit', function(ev) {
			ev.preventDefault();
		});

		$('button.submit-trigger', this.wrapper).on('click', this.submit.bind(this));

		this._initNameSection();
		this._initOtherSection();

		this.stateSaver = new DeskPRO.Agent.PageHelper.StateSaver({
			stateId: 'neworg',
			listenOn: this.getEl('neworg')
		});
		this.ownObject(this.stateSaver);
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
		var self = this;
		var formData = this.form.serializeArray();

		$.ajax({
			url: BASE_URL + 'agent/organizations/new/save',
			type: 'POST',
			data: formData,
			dataType: 'json',
			context: this,
			success: function(data) {
				if (data.success) {
					DeskPRO_Window.runPageRoute('person:' + BASE_URL + 'agent/organizations/' + data.org_id);

					$('select.dp-org-select').each(function() {
						var opt = $('<option />');
						opt.val(data.org_id);
						opt.text(self.getEl('name').val());

						$(this).append(opt);
					});

					this.closeSelf();
				} else {
					if (data && data.error_code && data.error_code == 'invalid_name') {
						DeskPRO_Window.showAlert('Please enter a name for the organization');
					}
				}
			}
		});
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
				if (!self.labelsInput && eventData.tabContent.hasClass('tab-properties')) {
					self.labelsInput = new DeskPRO.UI.LabelsInput({
						type: 'org',
						fieldName: 'neworg[labels]',
						textarea: $(".tags-wrap input", eventData.tabContent),
						onChange: function() {
							self.stateSaver.triggerChange();
						}
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
