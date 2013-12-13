Orb.createNamespace('DeskPRO.Agent.WindowElement.Section');

DeskPRO.Agent.WindowElement.Section.Twitter = new Orb.Class({
	Extends: DeskPRO.Agent.WindowElement.Section.AbstractSection,

	init: function() {
		this.buttonEl = $('#twitter_section');
		this.urlFragmentName = 'twitter';
		var self = this;

		this.setSectionElement($('<section id="twitter_outline"></section>'));

		this.refresh();

		DeskPRO_Window.getMessageBroker().addMessageListener('agent.tweet-added', function (data) {
			self.adjustTweetCountsFromClientMessage(data, 1);
		});

		DeskPRO_Window.getMessageBroker().addMessageListener('agent.tweet-updated', function (data) {
			if (typeof data.change_archived !== 'undefined') {
				if (data.change_archived) {
					// moved to archived, reduce counts
					self.adjustTweetCountsFromClientMessage(data, -1);
				} else {
					// moved to unarchived, increase counts
					self.adjustTweetCountsFromClientMessage(data, 1);
				}
			}
			if (data.deleted) {
				self.adjustTweetCountsFromClientMessage(data, -1);
			}
			if (data.favorited) {
				self.adjustTweetCountsFromClientMessage(data, 1);
				self.adjustTweetCountsFromClientMessage($.extend({}, data, {is_favorited: 0}), -1);
			}
			if (data.unfavorited) {
				self.adjustTweetCountsFromClientMessage(data, 1);
				self.adjustTweetCountsFromClientMessage($.extend({}, data, {is_favorited: 1}), -1);
			}
			if (typeof data.change_assignment !== 'undefined') {
				self.adjustTweetCountsFromClientMessage(data, 1);
				self.adjustTweetCountsFromClientMessage($.extend(
					{}, data, {assignment: data.old_assignment, agent_id: data.old_agent_id, agent_team_id: data.old_agent_team_id}), -1
				);
			}
		});

		DeskPRO_Window.getMessageBroker().addMessageListener('agent.twitter-follower', function (data) {
			var newCount = $('#twitter_' + data.account_id + '_new_followers_count');
			var totalCount = $('#twitter_' + data.account_id + '_followers_count');
			var totalCountHeader = $('#twitter_' + data.account_id + '_followers_count_header');

			switch (data.action) {
				case 'new':
					newCount.text(parseInt(newCount.text().trim(), 10) + 1);
					totalCount.text(parseInt(totalCount.text().trim(), 10) + 1);
					totalCountHeader.text(totalCount.text());
					break;

				case 'new-archived':
					totalCount.text(parseInt(totalCount.text().trim(), 10) + 1);
					totalCountHeader.text(totalCount.text());
					break;

				case 'archived':
					newCount.text(Math.max(0, parseInt(newCount.text().trim(), 10) - 1));
					break;

				case 'unarchived':
					newCount.text(parseInt(newCount.text().trim(), 10) + 1);
					break;
			}
		});

		DeskPRO_Window.getMessageBroker().addMessageListener('agent.twitter-friend', function (data) {
			var totalCount = $('#twitter_' + data.account_id + '_following_count');
			var totalCountHeader = $('#twitter_' + data.account_id + '_following_count_header');

			switch (data.action) {
				case 'new':
					totalCount.text(parseInt(totalCount.text().trim(), 10) + 1);
					totalCountHeader.text(totalCount.text());
					break;

				case 'removed':
					totalCount.text(Math.max(0, parseInt(totalCount.text().trim(), 10) - 1));
					totalCountHeader.text(totalCount.text());
					break;
			}
		});

		DeskPRO_Window.getMessageBroker().addMessageListener('twitter-section.list-activated', function (info) {
			var url = 'listpane:' + info.listUrl.replace('?partial=1', '');

			var interval;

			var f = function() {
				if (self.contentEl && self.contentEl.find('.is-nav-item').length) {
					clearInterval(interval);
					interval = false;
					self.contentEl.find('.is-nav-item').each(function() {
						var $this = $(this);
						if ($this.data('route') === url) {
							self.highlightNavItem($this);
							return false;
						}
					});
				}
			};
			if (self.contentEl && self.contentEl.find('.is-nav-item').length) {
				f();
			} else {
				interval = setInterval(f, 1000);
			}
		});

		window.setInterval(function() {
			self.refresh();
		}, 420000); // update every 7 mins
	},

	adjustTweetCountsFromClientMessage: function(data, adjustAmount) {
		if (data.is_from_self) {
			return;
		}

		var accountId = data.account_id;

		var types = {};
		this.getSectionElement().find('#twitter-section-counts-' + accountId + ' > li').each(function() {
			var $this = $(this);
			types[$this.data('type')] = $this;
		});

		if (data.assignment === 'agent:' + DESKPRO_PERSON_ID) {
			// my tweets
			this._adjustSectionCount(types.mine, data, adjustAmount);
		}

		for (var i = 0; i < DESKPRO_TEAM_IDS.length; i++) {
			var teamId = DESKPRO_TEAM_IDS[i];
			if (data.assignment === 'agent_team:' + teamId) {
				// my teams' tweets
				this._adjustSectionCount(types.team, data, adjustAmount);
				break;
			}
		}

		if (data.assignment === '') {
			// unassigned
			if (data.is_favorited) {
				this._adjustSectionCount(types.unassigned, data, adjustAmount);
			} else {
				switch (data.status_type) {
					case 'direct':
						if (data.is_from_self) {
							// own DM, consider as sent
							break;
						}
						// break missing intentionally

					case 'reply':
					case 'mention':
					case 'retweet':
						this._adjustSectionCount(types.unassigned, data, adjustAmount);
				}
			}
		}

		if (data.is_favorited) {
			this._adjustSectionCount(types.all, data, adjustAmount);
		} else {
			switch (data.status_type) {
				case 'direct':
					if (data.is_from_self) {
						// own DM, consider as sent
						break;
					}
					// break missing intentionally

				case 'reply':
				case 'mention':
				case 'retweet':
					// all tweets
					this._adjustSectionCount(types.all, data, adjustAmount);
					break;
			}
		}
	},

	_adjustSectionCount: function(el, data, adjustAmount) {
		if (!el) {
			return;
		}

		var groupType = el.data('initial-grouping');
		var type = el.data('type');

		var counter = el.find('h3 .list-counter');
		counter.text(parseInt(counter.text().trim(), 10) + adjustAmount);

		var subGroup = el.find('.sub-group');
		if (subGroup.length && groupType) {
			var value = '';

			switch (groupType) {
				case 'type':
					if (data.is_favorited) {
						value = 'favorite';
					} else {
						switch (data.status_type) {
							case 'reply':
							case 'mention':
							case 'retweet':
							case 'direct':
								value = data.status_type;
								break;

							default:
								value = 'other';
						}
					}
					break;

				case 'agent':
					value = data.agent_id;
					break;

				case 'team':
					value = data.agent_team_id;
					break;
			}

			var specificValue = subGroup.find('.twitter-sub-group-' + data.account_id + '-' + type + '-' + groupType + '-' + value);
			var count;
			if (specificValue.length) {
				counter = specificValue.find('.list-counter');
				count = Math.max(0, parseInt(counter.text().trim(), 10) + adjustAmount);
				counter.text(count);
				var li = specificValue.closest('li');
				if (count == 0) {
					li.hide();
				} else {
					li.show();
				}
			}

			if (subGroup.find('li').filter(function() { return $(this).css('display') !== 'none'; }).length) {
				subGroup.show();
			} else {
				subGroup.hide();
			}
		}

		this.recountBadge();
	},

	refresh: function(extraData) {
		var self = this;
		DeskPRO_Window.getSectionData('twitter_section', function(data) {
			self._initSection(data);

			if (self.sectionEl) {
				var scroller = self.sectionEl.find('.with-scroll-handler').data('scroll_handler');
				if (scroller) {
					scroller.updateSize();
				}
			}
		}, extraData);
	},

	_initSection: function(data) {
		var self = this;

		this.setHasInitialLoaded();
		this.contentEl.html(data.section_html);

		var contentEl = this.contentEl;

		contentEl.find('.source-list .sub-group').each(function() {
			var $this = $(this);
			if (!$this.find('li').filter(function() { return $(this).css('display') !== 'none'; }).length) {
				$this.css('display', 'none');
			}
		});

		contentEl.on('click', '.twitter-account-add-status', function() {
			if (DeskPRO_Window.newTweetLoader) {
				var accountId = $(this).data('account-id');
				DeskPRO_Window.newTweetLoader.open(function(page) {
					var select = page.getEl('from_account');
					if (select.length && select.is('.with-select2')) {
						select.select2('val', [accountId]);
					}
				});
			}
		});

		contentEl.on('click', '.twitter-search-delete-trigger', function(e) {
			e.preventDefault();
			e.stopPropagation();

			var $this = $(this);

			if (confirm($this.data('confirm'))) {
				$.ajax({
					url: $this.attr('href'),
					type: 'POST'
				});

				$this.closest('li').remove();
			}
		});

		this.searchBoxes = {};
		contentEl.find('input.twitter-search-add-box').each(function() {
			var searchBox = $(this);
			var tabHolder = $(this).closest('.deskpro-tab-item');
			var accountId = tabHolder.data('account-id');

			self.searchBoxes[accountId] = searchBox;
			searchBox.on('keypress', function(e) {
				if (e.which != 13) {
					return true;
				}

				e.preventDefault();
				self.doSearch(searchBox.val(), accountId);
				searchBox.val('');
			});
			tabHolder.find('.twitter-search-add-button').click(function(e) {
				e.preventDefault();
				self.doSearch(searchBox.val(), accountId);
				searchBox.val('');
			});
		});

		contentEl.find('input.twitter-person-find-box').each(function() {
			var personBox = $(this);
			var tabHolder = $(this).closest('.deskpro-tab-item');
			var accountId = tabHolder.data('account-id');

			personBox.on('keypress', function(e) {
				if (e.which != 13) {
					return true;
				}

				e.preventDefault();
				self.doFindPerson(personBox.val(), accountId);
				personBox.val('');
			});
			tabHolder.find('.twitter-person-find-button').click(function(e) {
				e.preventDefault();
				self.doFindPerson(personBox.val(), accountId);
				personBox.val('');
			});
		});

		contentEl.find('.twitter-tab-container').each(function() {
			var container = $(this);

			new DeskPRO.UI.SimpleTabs({
				context: container,
				triggerElements: '.pane-section-tabs li',
				onTabSwitch: function(info) {
					self.updateUi();
				}
			});
		});

		var onGroupingChanged = function(accountId, group_by, el) {
			el.data('initial-grouping', group_by);

			$.ajax({
				url: BASE_URL + 'agent/twitter/update-grouping.json',
				data: {account_id: accountId, type: el.data('type'), group: group_by },
				dataType: 'json',
				success: function(json) {
					el.find('.item-form').hide();

					if (json.group == el.data('initial-grouping')) {
						var html = $(json.html);
						if (!html.find('li').filter(function() { return $(this).css('display') !== 'none'; }).length) {
							html.css('display', 'none');
						}
						el.find('.nav-list-small').first().replaceWith(html);
						el.find('.nav-list-small').show();
					}
				}
			});
		};

		this.sectionEl.find('.dp-toggle-icon').on('click', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();
			ev.stopImmediatePropagation();

			var $me    = $(this);
			var $li    = $me.closest('li');
			var $group = $li.find('> .item-form');
			var $groupList = $li.find('> .nav-list-small');
			var sel = $group.find('select');
			var $account = $me.closest('.twitter-account-section');
			var accountId = parseInt($account.data('account-id'));
			var $counter  = $account.find('.counter').first();

			if ($group[0]) {
				if ($me.hasClass('icon-caret-right')) {
					$me.removeClass('icon-caret-right');
					$me.addClass('icon-caret-down');
					$group.show();
					$groupList.show();

					if (!sel.hasClass('with-select2')) {
						DP.select(sel);
						sel.on('change', function(ev) {
							onGroupingChanged(accountId, sel.val(), $li);
						});
					}
				} else {
					// Remove grouping
					sel.select2('val', '');
					sel.trigger('change');

					$me.addClass('icon-caret-right');
					$me.removeClass('icon-caret-down');
					$group.hide();
					$groupList.hide();
					onGroupingChanged(accountId, '', $li);
				}
			}
		});

		this.recountBadge();
	},

	doSearch: function(searchTerm, accountId) {
		searchTerm = $.trim(searchTerm);
		if (!searchTerm.length) {
			return;
		}

		var templateLi = this.searchBoxes[accountId].closest('.nav-list').find('.twitter-delete-template');

		var templateHtml = templateLi.clone().wrap('<div>').parent().html();
		templateHtml = templateHtml.replace(/__placeholder-url__/g, encodeURIComponent(searchTerm)).replace(/__placeholder__/g, searchTerm);
		templateLi.after($(templateHtml).show());

		DeskPRO_Window.runPageRoute('listpane:' + BASE_URL + 'agent/twitter/' + accountId + '/search/new?search_term=' + encodeURIComponent(searchTerm));
	},

	doFindPerson: function(name, accountId) {
		name = $.trim(name);
		if (!name.length) {
			return;
		}

		DeskPRO_Window.runPageRoute('page:' + BASE_URL + 'agent/twitter/user/find?tab=1&name='
			+ encodeURIComponent(name) + '&account_id=' + accountId
		);
	},

	recountBadge: function() {
		var count = 0;
		this.contentEl.find('.twitter-all-counter').each(function() {
			count += parseInt($(this).text().trim(), 10) || 0;
		});
		this.updateBadge(count);
	}
});
