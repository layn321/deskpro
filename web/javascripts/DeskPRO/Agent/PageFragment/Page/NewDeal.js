Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');

DeskPRO.Agent.PageFragment.Page.NewDeal = new Orb.Class({

Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'newdeal';
		this.allowDupe = true;
	},

        initPage: function(el) {
		this.wrapper = el;
		this.contentWrapper = this.wrapper.children('.layout-content').attr('id', Orb.getUniqueId());
		this.parent(el);

                this.form = $('form', this.wrapper).submit(function(ev) {
			ev.preventDefault();
		});


                this._initDepartmentSection();
                this._initUserSection();
                this._initOrgEdit();
                this._initOtherSection();


                var visiMenu = new DeskPRO.UI.Menu({
			menuElement: this.getEl('visibility')
		});
                this.ownObject(visiMenu);

                $('button.submit-trigger', this.wrapper).on('click', this.submit.bind(this));
                $('.select-deal-type').on('change', function(){

                    var dealId = 0;
                    $.ajax({
				url: BASE_URL + 'agent/deals/' + dealId + '/ajax-save',
				data: { action: 'change-dealtype', deal_type_id: $(this).val() },
				type: 'POST',
				context: this,
				error: function() {

				},
				success: function(data) {
                                    $('.set-deal-stage').html(data.deal_stage);
				}
			});
                }) ;

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
			url: BASE_URL + 'agent/deals/new/save',
			type: 'POST',
			data: formData,
			dataType: 'json',
			context: this,
			success: function(data) {
				if (data.success) {

					DeskPRO_Window.runPageRoute('page:' + BASE_URL + 'agent/deal/' + data.deal_id);
                                        DeskPRO_Window.sections.deals_section.refresh();
					this.closeSelf();
				} else {
					alert('There was an error with the form');
				}
			}
		});
	},

        _initDepartmentSection: function() {

            var self = this;
            var el = this.getEl('agent_assign_ob');
            this.assignOptionBox = new DeskPRO.UI.OptionBoxRevertable({
            element: el,
            trigger: this.getEl('assign_btn'),
            onSave: function(ob) {
                var selections = ob.getAllSelected();
                var agent_id = parseInt(selections.agents || 0);

                var label = $('.agent-label-' + agent_id, ob.getElement()).first().text().trim();

                var value = selections.agents;
                if (value == 0) {
                    label = 'Unassigned';
                }

                self.getEl('agent_id').val(agent_id);
		self.getEl('agent_label').text(label);


                $('.reply-agent-team-ob').slideUp();

            }
        });

	},

        _initUserSection: function() {
		var self = this;
		var searchbox = this.getEl('user_searchbox');
		var userfields = this.getEl('user_choice');
		var rechooseBtn = this.getEl('switch_user');

		rechooseBtn.click(function() {
			showUserChoice(); return false;
		});

		var showUserChoice = function() {
			userfields.empty();
			userfields.hide();
			searchbox.show();
			rechooseBtn.hide();
		};

		var placeUserRow = function(html) {
			self.placeUserRow(html);
		};

		searchbox.bind('personsearchboxclick', function(ev, personId, name, email, sb) {
			$.ajax({
				type: 'GET',
				url: BASE_URL + 'agent/deals/new/get-person-row/' + personId,
				dataType: 'html',
				context: this,
				success: function(html) {
					$('input.person-id', searchbox).val(personId);
					placeUserRow(html);
				}
			});
			sb.close();
			sb.reset();
		});
		searchbox.bind('personsearchboxclicknew personsearchenter', function(ev, term, sb) {
			$.ajax({
				type: 'GET',
				url: BASE_URL + 'agent/deals/new/get-person-row/0',
				data: { 'email': term },
				dataType: 'html',
				context: this,
				success: function(html) {
					placeUserRow(html);

					if (term.indexOf('@') !== -1) {
						$('input.email', userfields).val(term);
					} else {
						$('input.name', userfields).val(term);
					}
				}
			});
			sb.close();
			sb.reset();
		});
	},

        placeUserRow: function(html) {
		var searchbox = this.getEl('user_searchbox');
		var userfields = this.getEl('user_choice');
		var rechooseBtn = this.getEl('switch_user');

		userfields.empty();
		userfields.html(html);

		rechooseBtn.show();
		searchbox.hide();
		userfields.show();
	},

       	//#########################################################################
	//# Org Edit
	//#########################################################################

	_initOrgEdit: function() {
		var self = this;
		var searchbox = this.getEl('org_searchbox');
		var orgfields = this.getEl('org_choice');
		var rechooseBtn = this.getEl('switch_org');

		rechooseBtn.click(function() {
			showOrganizationChoice();
                        $('.org-id').val(0);
                        return false;
		});

		var showOrganizationChoice = function() {
			orgfields.empty();
			orgfields.hide();
			searchbox.show();
			rechooseBtn.hide();
		};

                var placeOrganizationRow = function(html) {
			self.placeOrganizationRow(html);
		};

		var orgEdit    = this.getEl('org_edit_wrap');

		//orgEnableBtn
		this.getEl('org_searchbox').bind('orgsearchboxclick', function(ev, orgId, name) {
			 $('.org-id', self.getEl('org_edit_wrap')).val().trim();

		}).bind('orgsearchboxcreate', function(ev, term, name) {

                        $.ajax({
				type: 'GET',
				url: BASE_URL + 'agent/deals/new/get-organization-row/0',
				dataType: 'html',
				context: this,
				success: function(html) {
					placeOrganizationRow(html);
					$('input.organization_name', orgfields).val(term);
				}
			});
		}).bind('orgsearchreverted', function(ev, term, name) {

		});
	},

        placeOrganizationRow: function(html) {
		var searchbox = this.getEl('org_searchbox');
		var orgfields = this.getEl('org_choice');
		var rechooseBtn = this.getEl('switch_org');

		orgfields.empty();
		orgfields.html(html);

		rechooseBtn.show();
		searchbox.hide();
		orgfields.show();
	},

        //#########################################################################
	//# Other Section
	//#########################################################################

	_initOtherSection: function() {

		this.otherTabs = new DeskPRO.UI.SimpleTabs({
			triggerElements: $('li', this.getEl('other_props_tabs')),
			context: this.getEl('other_props_tabs_content'),
			autoSelectFirst: false,
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

		// Attachments
		var list = $('.file-list', this.wrapper);
		$('input', list[0]).live('click', function() {
			var el = $(this);
			var li = el.parent();
			if (el.is(':checked')) {
				li.removeClass('unchecked');
			} else {
				li.addClass('unchecked');
			}
		});

		DeskPRO_Window.util.fileupload(this.wrapper, {
			page: this
		});
	}


})
