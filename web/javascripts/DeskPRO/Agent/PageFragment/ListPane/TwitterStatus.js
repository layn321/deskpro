Orb.createNamespace('DeskPRO.Agent.PageFragment.ListPane');

DeskPRO.Agent.PageFragment.ListPane.TwitterStatus = new Orb.Class({
	Extends: DeskPRO.Agent.PageFragment.ListPane.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'twitter-status-list';
		this.countReflected = {};
	},

	initPage: function(el) {
		this.wrapper = $(el);
		var self = this;

		DeskPRO_Window.getMessageBroker().sendMessage('twitter-section.list-activated', {
			listUrl: this.meta.statusListUrl
		});

		this.meta.fetchResultsUrl = this.meta.statusListUrl;

		this.header = $('.header', this.wrapper);
		this.content = $('.content', this.wrapper);

		this.twitterHelper = new DeskPRO.Agent.PageHelper.Twitter(this.content, this, {
			statusArchiveHideCallback: function(row) {
				var id = parseInt(row.data('status-id'), 10);
				if (id && !self.countReflected[id]) {
					self.countReflected[id] = true;
					self.resultsHelper.adjustResultCount(-1);
				}
				self._afterTweetRemoved(0);
			}
		});

		this._initHeader();
		this._initContent(this.content);
		this._initControls(this.content);

		this.wrapper.on('click', '.new-tweet-list-indicator', function() {
			self.reload();
		});

		var opt = {
			perPage: this.meta.perPage || 25,
			currentPage: this.meta.currentPage,
			totalCount: this.meta.totalCount,
			resultRowSelector: 'article.twitter-status',
			resultsContainer: this.content,
			preFetchCallback: function(data) {
				$.each(self._getDisplayOptions(), function(k, v) {
					if (/boolean|number|string/.test(typeof v)) {
						data.push({name: k, value: v});
					} else {
						$.each(v, function(kk, vv) {
							data.push({name: k + '[' + kk + ']', value: vv});
						});
					}
				});
				return data;
			},
			onPostSetNewResults: function(x, y, results) {
				self._afterLoading(results);
			}/*,
			infiniteScroll: true,
			infiniteScrollTarget: this.content.find('.twitter-status-list'),
			infiniteScrollLoadFilter: function(results) {
				return results.find('.row-item.twitter-status');
			}*/
		};
		this.resultsHelper = new DeskPRO.Agent.PageHelper.Results(this, opt);
		this.ownObject(this.resultsHelper);

		DeskPRO_Window.getMessageBroker().addMessageListener('agent.tweet-added', function (data) {
			self.adjustTweetCountsFromClientMessage(data, 1);
			self.adjustShownTweetsForTweetAdded(data);

			self.countReflected = {};
		});

		DeskPRO_Window.getMessageBroker().addMessageListener('agent.tweet-updated', function (data) {
			if (data.trigger_user_id && data.trigger_user_id == DESKPRO_PERSON_ID) {
				return;
			}

			if (typeof data.change_archived !== 'undefined') {
				if (data.change_archived) {
					// moved to archived, reduce counts
					self.adjustTweetCountsFromClientMessage(data, -1);
				} else {
					// moved to unarchived, increase counts
					self.adjustTweetCountsFromClientMessage(data, 1);
				}
			} else if (data.deleted) {
				self.adjustTweetCountsFromClientMessage(data, -1);
			}

			self.adjustShownTweetsForTweetUpdated(data);

			self.countReflected = {};
		});
	},

	adjustTweetCountsFromClientMessage: function(data, adjustAmount) {
		var accountId = data.account_id;

		if (this.countReflected[data.account_status_id]) {
			return;
		}

		if (this._tweetAppliesToPage(data) && this.resultsHelper && this.resultsHelper.options) {
			this.countReflected[data.account_status_id] = true;
			this.resultsHelper.adjustResultCount(adjustAmount);
		}
	},

	adjustShownTweetsForTweetAdded: function(data) {
		if (this.content.find('.twitter-status-' + data.account_status_id).length) {
			// tweet already shown
			return;
		}

		if (!this.resultsHelper || !this.resultsHelper.options) {
			// page destroyed
			return;
		}

		if (this._tweetAppliesToPage(data)) {
			if (this.resultsHelper.getCurrentPage() > 1) {
				this.adjustNewTweetIndicator(1);
			} else {
				this.addTweetToPage(data.account_status_id, data.tweet_html);
			}
		}
	},

	adjustNewTweetIndicator: function(adjust) {
		var newIndicator = this.wrapper.find('.new-tweet-list-indicator');
		var newCount = (newIndicator.data('new-count') || 0) + adjust;
		newIndicator.data('new-count', newCount);
		newIndicator.text(newCount == 1 ? '1 new tweet' : newCount + ' new tweets').show();
	},

	_tweetAppliesToPage: function(data) {
		if (!this.meta.accountId || this.meta.accountId != data.account_id) {
			return false;
		}

		if (data.is_from_self) {
			return (
				this.menuOptions.filter('[name=account]').is(':checked')
				|| this.meta.listRoute == 'agent_twitter_sent_list'
			);
		}

		var isInInbox = (
			$.inArray(data.status_type, ['direct', 'reply', 'mention', 'retweet']) !== -1
			|| data.is_favorited
		);

		switch (this.meta.listRoute) {
			case 'agent_twitter_mine_list':
				if (data.assignment !== 'agent:' + DESKPRO_PERSON_ID) {
					return false;
				}
				break;

			case 'agent_twitter_team_list':
				var hasOwnTeam = false;
				for (var i = 0; i < DESKPRO_TEAM_IDS.length; i++) {
					var teamId = DESKPRO_TEAM_IDS[i];
					if (data.assignment === 'agent_team:' + teamId) {
						// my teams' tweets
						hasOwnTeam = true;
						break;
					}
				}
				if (!hasOwnTeam) {
					return false;
				}
				break;

			case 'agent_twitter_unassigned_list':
				if (data.assignment !== '') {
					return false;
				}
				if (!isInInbox) {
					return false;
				}
				break;

			case 'agent_twitter_all_list':
				if (!isInInbox) {
					return false;
				}
				break;

			case 'agent_twitter_timeline_list':
				if (data.status_type !== 'timeline') {
					return false;
				}
				break;

			case 'agent_twitter_sent_list':
				// the true case is handled above
				return false;

			case 'agent_twitter_run_search':
				return false;

			default:
				return false;
		}

		if (this.meta.group) {
			switch (this.meta.group) {
				case 'type':
					if (this.meta.groupValue == 'favorite') {
						if (!data.is_favorited) {
							return false;
						}
					} else {
						if (data.status_type !== this.meta.groupValue) {
							return false;
						}
					}
					break;

				case 'agent':
					if (data.agent_id != this.meta.groupValue) {
						return false;
					}
					break;

				case 'team':
					if (data.agent_team_id != this.meta.groupValue) {
						return false;
					}
					break;
			}
		}

		return true;
	},

	adjustShownTweetsForTweetUpdated: function(data) {
		var row = this.content.find('.twitter-status-' + data.account_status_id);

		if (row.length) {
			if (typeof data.change_archived !== 'undefined') {
				var showArchived = this.menuOptions.filter('[name=archived]').is(':checked');
				if (data.change_archived && !showArchived) {
					this.removeTweetFromPage(data.account_status_id);
				}
			}
			if (data.deleted) {
				this.removeTweetFromPage(data.account_status_id);
			}
			if (data.reply_added_html && data.reply_added_id) {
				if (!row.find('.twitter-reply-' + data.reply_added_id).length) {
					var html = $(data.reply_added_html);
					row.find('.twitter-replies').append(html);
					$('.timeago', html).timeago();

					row.find('.reply-list').show();
				}
			}
			if (data.note_added_html  && data.note_added_id) {
				if (!row.find('.twitter-note-' + data.note_added_id).length) {
					var html = $(data.note_added_html);
					row.find('.note-list').append(html);
					$('.timeago', html).timeago();

					row.find('.status-notes').show();
				}
			}
			if (data.edited_html) {
				row.find('.main-status-body .status-text').html(data.edited_html);
			}
			if (data.retweeted) {
				var link = row.find('li.opt-trigger.retweet, li.opt-trigger.retweeted');
				link.addClass('retweeted').removeClass('retweet');
				link.find('label').text('Retweeted');
			}
			if (data.unretweeted) {
				var link = row.find('li.opt-trigger.retweet, li.opt-trigger.retweeted');
				link.addClass('retweet').removeClass('retweeted');
				link.find('label').text('Retweet');
			}
			if (data.favorited) {
				row.find('.add-favorite, .favorited').addClass('favorited').removeClass('add-favorite');
			}
			if (data.unfavorited) {
				row.find('.add-favorite, .favorited').addClass('add-favorite').removeClass('favorited');
			}
			if (typeof data.change_assignment !== 'undefined') {
				var opt = row.find('.agents_sel option[value="' + data.change_assignment + '"]');
				if (opt.length) {
					opt.closest('select').val(data.change_assignment);
					var label = opt.text().trim();
					if (data.change_assignment == 'agent:' + DESKPRO_PERSON_ID) {
						label = 'Me';
					}

					var labelEl = row.find('li.opt-trigger.agent label');
					if (data.assignment_picture) {
						labelEl.text(' ' + label).prepend($('<img class="agent-assign-icon" />').attr('src', data.assignment_picture));
					} else {
						labelEl.text(label);
					}
				}
			}
		} else {
			if (typeof data.change_archived !== 'undefined' && !data.change_archived) {
				if (this.resultsHelper.getCurrentPage() == 1) {
					this.addTweetToPage(data.account_status_id, data.tweet_html);
				}
			}
		}

		if (this.content.find('.twitter-reply-' + data.account_status_id).length) {
			if (data.deleted) {
				this.removeReplyFromPage(data.account_status_id);
			}
			if (data.edited_html) {
				var row = this.content.find('.twitter-reply-' + data.account_status_id);
				row.find('.status-text').html(data.edited_html);
			}
		}
	},

	addTweetToPage: function(account_status_id, html) {
		if (!this.resultsHelper || !this.resultsHelper.options) {
			// page destroyed
			return;
		}

		if (!this.countReflected[account_status_id]) {
			this.resultsHelper.adjustResultCount(1);
			this.countReflected[account_status_id] = true;
		}

		var $html = $(html);
		this.content.find('.twitter-status-list').prepend($html);
		this._afterLoading($html);

		this.adjustShownTweets();
	},

	adjustShownTweets: function() {
		var count = this.resultsHelper.updateShowingCount();

		if (count === false) {
			return;
		}

		if (count == 0) {
			this.wrapper.find('.list-listing.no-results').show();
		} else {
			this.wrapper.find('.list-listing.no-results').hide();
		}

		if (count > this.resultsHelper.options.perPage) {
			$(this.resultsHelper.options.resultRowSelector, this.resultsHelper.resultsContainer)
				.slice(this.resultsHelper.options.perPage - count)
				.remove();

			this.resultsHelper.updateShowingCount()
		}
	},

	removeTweetFromPage: function(account_status_id) {
		var el = this.content.find('.twitter-status-' + account_status_id).filter(':not(:animated)');
		if (el.length) {
			el.remove();
			if (!this.countReflected[account_status_id]) {
				this.resultsHelper.adjustResultCount(-1);
				this.countReflected[account_status_id] = true;
			}
			this._afterTweetRemoved(0);
		}
	},

	removeReplyFromPage: function(account_status_id) {
		var el = this.content.find('.twitter-reply-' + account_status_id);
		if (el.length) {
			var row = this.twitterHelper.closestRow(el);
			el.remove();
			if (!row.find('.twitter-replies .twitter-reply').length) {
				row.find('.reply-list').hide();
			}
		}
	},

	_afterTweetRemoved: function(delay) {
		var pageHelper = this.resultsHelper,
			page = pageHelper.getCurrentPage(),
			numPages = pageHelper.getNumPages();
		var self = this;

		if (!this.resultsHelper || !this.resultsHelper.options) {
			// page destroyed
			return;
		}

		pageHelper.updateShowingCount();

		if (page < numPages) {
			var data = this._getDisplayOptions();
			data.last = 1;
			data.page = page;

			setTimeout(function() {
				$.ajax({
					url: self.getMetaData('statusListUrl'),
					dataType: 'html',
					data: data,
					success: function(html) {
						var $html = $(html);
						self.content.find('.twitter-status-list').append($html);
						self._afterLoading($html);
					}
				});
			}, delay || 0);
		} else if (pageHelper.resultCount <= 0) {
			this.wrapper.find('.list-listing.no-results').show();
			this.wrapper.find('.results-nav').hide();
		}
	},

	_afterLoading: function(content) {
		if (!content) { content = this.content; }
		this._initContent(content);
		this._initControls(content);

		this.wrapper.find('.new-tweet-list-indicator').data('new-count', 0).hide();

		if (this.selectionBar) {
			this.selectionBar.updateCount();
		}
		if (this.resultsHelper && this.resultsHelper.options) {
			this.resultsHelper.updateShowingCount();
		}
	},

	_initHeader: function() {
		this._initSortByFields();
		this._initIncludeFields();

		var self = this;

		this.selectionBar = new DeskPRO.Agent.PageHelper.SelectionBar(this, {
			onButtonClick: function() {
				self.massActions.open();
			},
			onCountChange: function(count) {
				var isOpen = self.massActions.isOpen();

				if (count > 0 && !isOpen) {
					self.massActions.open();
				} else if (count <= 0 && isOpen) {
					self.massActions.close();
				}
			},
			checkSelector: '.twitter-status:not(.archived) input.item-select'
		});
		this.ownObject(this.selectionBar);

		this.massActions = new DeskPRO.Agent.PageHelper.MassActions(this, {
			isListView: false,
			applyAction: function(wrapper, formData) {
				var data = formData,
					myFormData = $('input, textarea, select', wrapper).serializeArray();

				$(myFormData).each(function(index, param) {
					data[param.name] = param.value;
				});

				wrapper.addClass('loading');

				$.ajax({
					type: 'POST',
					url: BASE_URL + "agent/twitter/status/ajax-mass-save.json",
					'data': data,
					'dataType': 'json',
					success: function() {
						self.massActions.close();
						self.reload();
					}
				}).done(function() {
					wrapper.removeClass('loading');
				});
			},
			closeOnApply: false,
			openAction: function(wrapper) {
				if (!wrapper.data('twitter-helper')) {
					wrapper.data('twitter-helper',
						new DeskPRO.Agent.PageHelper.Twitter($('#twitter-mass-action-overlay'), self)
					);
				}
			}
		});
		this.ownObject(this.massActions);
	},

	_initContent: function(content) {
		var self = this;

		$('.timeago', content).timeago();
		var list = content.find('.twitter-status-list');
		if (list.length && list.data('page') && this.resultsHelper) {
			this.resultsHelper.setPage(parseInt(list.data('page'), 10), true);
			this.resultsHelper.setResultCount(parseInt(list.data('total-count'), 10));
		}
	},

	_initControls: function(content) {
	},

	_initSortByFields: function() {
		var self = this;

		var sortMenuBtn = $('.order-by-menu-trigger', this.header).first();
		this.sortingMenu = new DeskPRO.UI.Menu({
			triggerElement: sortMenuBtn,
			menuElement: $('.order-by-menu', this.header).first(),
			onItemClicked: function(info) {
				var item = $(info.itemEl);

				var prop = item.data('order-by');
				var label = item.find('.label').text().trim();

				// Change the displayed label for some visual feedback
				$('.label label', sortMenuBtn).text(label);
				sortMenuBtn.find('.order-dir').hide();
				sortMenuBtn.find('.order-dir.' + prop.split('_').pop()).show();

				sortMenuBtn.data('dir', prop);

				self.sortingMenu.close();
				self.reload();
			}
		});
		this.ownObject(this.sortingMenu);
	},

	_initIncludeFields: function() {
		var self = this;

		this.menuOptions = this.header.find('.btn-controls input:checkbox');
		this.menuOptions.click(function() { self.reload(); });
	},

	_getDisplayOptions: function() {
		var options = {
			include: {}
		};

		if (this.menuOptions) {
			this.menuOptions.each(function() {
				var field = $(this);
				options.include[field.attr('name')] = field.attr('checked') ? 1 : 0;
			});
		}

		return options;
	},

	reload: function() {
		$.ajax({
			url: this.getMetaData('statusListUrl'),
			dataType: 'html',
			data: this._getDisplayOptions(),
			context: this,
			success: function(html) {
				this.content.html(html);
				this._afterLoading();
			}
		});
	},

	highlightStatus: function(id) {
		$('.twitter-status', this.content).removeClass('highlight');
		$('.status-'+id, this.content).addClass('highlight');
	},

	downlightStatus: function(id) {
		$('.twitter-status-'+id, this.content).removeClass('highlight');
	}
});
