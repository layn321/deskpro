Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');

DeskPRO.Agent.PageFragment.Page.TwitterUser = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'twitter-user';
	},

	initPage: function(el) {
		this.el = $(el);

		var self = this;

		// user follow/unfollow
		this.el.on('click', '.follow-button', function(e) {
			e.preventDefault();

			$(this).addClass('unfollow-button').removeClass('follow-button')
			$(this).find('.clean-white').text('Unfollow');

			$.ajax({
				url: self.getMetaData('saveFollowUrl'),
				type: 'POST',
				data: {
					user_id: self.getMetaData('userId'),
					account_id: self.getMetaData('accountId')
				}
			});
		});
		this.el.on('click', '.unfollow-button', function(e) {
			e.preventDefault();

			$(this).addClass('follow-button').removeClass('unfollow-button');
			$(this).find('.clean-white').text('Follow');

			$.ajax({
				url: self.getMetaData('saveUnfollowUrl'),
				type: 'POST',
				data: {
					user_id: self.getMetaData('userId'),
					account_id: self.getMetaData('accountId')
				}
			});
		});

		var switchAccountMenu = new DeskPRO.UI.Menu({
			triggerElement: this.getEl('switch_account_trigger'),
			menuElement: this.getEl('switch_account_menu'),
			onItemClicked: function(info) {
				var loadRoute = $(info.itemEl).data('load-route');
				switchAccountMenu.close();

				DeskPRO_Window.removePage(self);
				DeskPRO_Window.runPageRoute('page:' + loadRoute);
			}
		});

		this.el.on('click', '.send-message-button', function(e) {
			e.preventDefault();

			var overlay = new DeskPRO.UI.Overlay({
				contentMethod: 'ajax',
				contentAjax: { url: BASE_URL + 'agent/twitter/user/' + self.getMetaData('userId') + '/message-overlay' },
				zIndex: 40000, // Above floating people windows
				onAjaxDone: function() {
					var wrapper = overlay.getWrapper();
					var textarea = wrapper.find('textarea[name=text]');

					var helper = new DeskPRO.Agent.PageHelper.Twitter(wrapper, self, {
						saveMessageCallback: function(data) {
							wrapper.addClass('loading');

							$.ajax({
								url: self.getMetaData('saveUserMessageUrl'),
								type: 'POST',
								data: data,
								dataType: 'json'
							}).done(function(data) {
								if (data.success) {
									overlay.close();
								} else if (data.error) {
									alert(data.error);
								}
							}).always(function() {
								wrapper.removeClass('loading');
							});
						}
					});

					helper.updateTweetLength(textarea);
					textarea.focus();
				}
			});
			overlay.open();
		});

		this.getEl('user_searchbox').on('personsearchboxclick', function(e, personId, name, email, obj) {
			obj.close();
			self.getEl('userselect').val('');

			$.ajax({
				url: self.getMetaData('saveUserPersonUrl'),
				data: {user_id: self.getMetaData('userId'), person_id: personId},
				type: 'POST',
				dataType: 'json',
				success: function(json) {
					if (json.success) {
						self.getEl('choose_user').before(json.html);
					}
				}
			});
		});

		this.getEl('org_searchbox').on('orgsearchboxclick', function(e, orgId, name, obj) {
			obj.close();
			self.getEl('orgselect').val('');

			$.ajax({
				url: self.getMetaData('saveUserOrganizationUrl'),
				data: {user_id: self.getMetaData('userId'), organization_id: orgId},
				type: 'POST',
				dataType: 'json',
				success: function(json) {
					if (json.success) {
						self.getEl('org_edit_wrap').before(json.html);
					}
				}
			});
		});

		$('.profile-box-container.tabbed', this.wrapper).each(function() {
			var simpleTabs = new DeskPRO.UI.SimpleTabs({
				triggerElements: '> header li',
				context: this
			});
		});

		var statusesLoadPending = false;
		var statusesTab = self.getEl('statuses_tab');
		statusesTab.on('click', '.more-box .more-button', function() {
			if (statusesLoadPending) {
				return;
			}
			statusesLoadPending = true;

			statusesTab.find('.more-box .flat-spinner').show();

			$.ajax({
				url: $(this).data('load-url'),
				success: function(html) {
					statusesTab.find('.more-box').remove();
					statusesTab.append(html);
				},
				complete: function() {
					statusesTab.find('.more-box .flat-spinner').hide();
					statusesLoadPending = false;
				}
			});
		});

		var followingLoadPending = false;
		var followingTab = self.getEl('following_tab');
		followingTab.on('click', '.more-box .more-button', function() {
			if (followingLoadPending) {
				return;
			}
			followingLoadPending = true;

			followingTab.find('.more-box .flat-spinner').show();

			$.ajax({
				url: $(this).data('load-url'),
				success: function(html) {
					followingTab.find('.more-box').remove();
					followingTab.append(html);
				},
				complete: function() {
					followingTab.find('.more-box .flat-spinner').hide();
					followingLoadPending = false;
				}
			});
		});

		var followersLoadPending = false;
		var followersTab = self.getEl('followers_tab');
		followersTab.on('click', '.more-box .more-button', function() {
			if (followersLoadPending) {
				return;
			}
			followersLoadPending = true;

			followersTab.find('.more-box .flat-spinner').show();

			$.ajax({
				url: $(this).data('load-url'),
				success: function(html) {
					followersTab.find('.more-box').remove();
					followersTab.append(html);
				},
				complete: function() {
					followersTab.find('.more-box .flat-spinner').hide();
					followersLoadPending = false;
				}
			});
		});

		$('.timeago', this.el).timeago();

		this.twitterHelper = new DeskPRO.Agent.PageHelper.Twitter(this.el, this);
	}
});
