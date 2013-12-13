Orb.createNamespace('DeskPRO.Agent.PageHelper');

DeskPRO.Agent.PageHelper.Twitter = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(content, page, options) {
		this.content = $(content);
		this.page = page;
		var self = this;

		this.options = {
			saveMessageCallback: 'default',
			messageUrl: page.getMetaData('saveReplyUrl'),
			statusArchiveHideCallback: null,
			userArchiveHideCallback: null
		};

		options = options || {};
		this.setOptions(options);

		// user links
		this.content.on('click', '.photo, .user', function(e) {
			e.preventDefault();
			DeskPRO_Window.runPageRouteFromElement(this);
			return false;
		});
		this.content.on('click', '.mention', function(e) {
			e.preventDefault();
			var route = 'page:' + BASE_URL + 'agent/twitter/user/' + $(this).data('user-id');
			DeskPRO_Window.runPageRoute(route);
			return false;
		});

		// user follow/unfollow
		this.content.on('click', '.follow', function(e) {
			e.preventDefault();
			e.stopPropagation();

			var row = self.closestRow(this);
			var id = row.attr('data-user-id');

			self.content.find('[data-user-id="' + id + '"] .follow').each(function() {
				$(this).addClass('unfollow').removeClass('follow');

				var label = $(this).find('label');
				if (label.length) {
					label.text('Unfollow');
				} else {
					$(this).text('Unfollow');
				}
			});

			$.ajax({
				url: self.page.getMetaData('saveFollowUrl'),
				type: 'POST',
				data: {
					user_id: id,
					account_id: self.page.getMetaData('accountId')
				}
			});

			if (page.getMetaData('listRoute') == 'agent_twitter_followers_list_new') {
				row.addClass('archived');
				row.find('.status-archived').show();

				if (self.page.getMetaData('hideArchived')) {
					if (self.options.userArchiveHideCallback) {
						self.options.userArchiveHideCallback(row);
					}
					row.fadeOut('fast', function() {
						if (self.options.userArchiveHideCallback) {
							self.options.userArchiveHideCallback(row);
						}
						row.remove();
					});
				}
			}
		});
		this.content.on('click', '.unfollow', function(e) {
			e.preventDefault();
			e.stopPropagation();

			var id = self.closestRow(this).attr('data-user-id');

			self.content.find('[data-user-id="' + id + '"] .unfollow').each(function() {
				$(this).addClass('follow').removeClass('unfollow');

				var label = $(this).find('label');
				if (label.length) {
					label.text('Follow');
				} else {
					$(this).text('Follow');
				}
			});

			$.ajax({
				url: self.page.getMetaData('saveUnfollowUrl'),
				type: 'POST',
				data: {
					user_id: id,
					account_id: self.page.getMetaData('accountId')
				}
			});
		});

		// user archive/unarchive (status class names used)
		this.content.on('click', '.status-archive.user-action', function(e) {
			e.preventDefault();

			var row = $(this).closest('.twitter-user');
			var id = row.attr('data-user-id');

			if (id) {
				$(this).hide();
				row.addClass('archived');
				row.find('.status-archived').show();

				self.doArchiveUser(id, 1);

				if (self.page.getMetaData('hideArchived')) {
					row.fadeOut('fast', function() {
						if (self.options.userArchiveHideCallback) {
							self.options.userArchiveHideCallback(row);
						}
						row.remove();
					});
				}
			}
		});
		this.content.on('click', '.status-archived.user-action', function(e) {
			e.preventDefault();

			var row = $(this).closest('.twitter-user');
			var id = $(this).closest('.twitter-user').attr('data-user-id');

			if (id) {
				$(this).hide();
				row.removeClass('archived');
				row.find('.status-archive').show();

				self.doArchiveUser(id, 0);
			}
		});

		// status favorite/unfavorite
		this.content.on('click', '.add-favorite', function(e) {
			e.preventDefault();

			var id = self.closestRow(this).attr('data-status-id');
			if (id) {
				self.removeTweetNotification(id);

				$(this).addClass('favorited').removeClass('add-favorite');
				self.doFavorite(id, 1);
			}
		});
		this.content.on('click', '.favorited', function(e) {
			e.preventDefault();

			var id = self.closestRow(this).attr('data-status-id');
			if (id) {
				self.removeTweetNotification(id);

				$(this).addClass('add-favorite').removeClass('favorited');
				self.doFavorite(id, 0);
			}
		});

		// status archive/unarchive
		this.content.on('click', '.status-archive.status-action', function(e) {
			e.preventDefault();

			var row = self.closestRow(this);
			var id = row.attr('data-status-id');

			if (id) {
				$(this).hide();
				row.addClass('archived');
				row.find('.status-archived').show();

				self.doArchiveStatus(id, 1);

				self.removeTweetNotification(id);

				if (page.menuOptions && !page.menuOptions.filter('[name=archived]').is(':checked')) {
					row.fadeOut('fast', function() {
						if (self.options.statusArchiveHideCallback) {
							self.options.statusArchiveHideCallback(row);
						}
						row.remove();
					});
				}
			}
		});
		this.content.on('click', '.status-archived.status-action', function(e) {
			e.preventDefault();

			var row = self.closestRow(this);
			var id = row.attr('data-status-id');

			if (id) {
				$(this).hide();
				row.removeClass('archived');
				row.find('.status-archive').show();

				self.removeTweetNotification(id);

				self.doArchiveStatus(id, 0);
			}
		});

		// status delete
		this.content.on('click', '.status-delete.status-action', function(e) {
			e.preventDefault();

			var row = self.closestRow(this);
			var id = row.attr('data-status-id');

			if (id && confirm('Are you sure you want to delete this tweet?')) {
				row.hide();

				self.removeTweetNotification(id);

				$.ajax({
					url: self.page.getMetaData('saveDeleteUrl'),
					type: 'POST',
					dataType: 'json',
					data: { account_status_id: id },
					success: function(json) {
						if (json.success) {
							row.remove();
						} else if (json.error) {
							row.show();
							alert(json.error);
						}
					}
				});
			}
		});

		// reply delete
		this.content.on('click', '.status-delete.reply-action', function(e) {
			e.preventDefault();

			var reply = $(this).closest('.twitter-reply');
			var id = reply.attr('data-status-id');

			if (id && confirm('Are you sure you want to delete this tweet?')) {
				reply.hide();

				$.ajax({
					url: self.page.getMetaData('saveDeleteUrl'),
					type: 'POST',
					dataType: 'json',
					data: { account_status_id: id },
					success: function(json) {
						if (json.success) {
							var row = self.closestRow(reply);
							reply.remove();
							if (!row.find('.twitter-replies .twitter-reply').length) {
								row.find('.reply-list').hide();
							}
						} else if (json.error) {
							reply.show();
							alert(json.error);
						}
					}
				});
			}
		});

		// status/reply edit
		this.content.on('click', '.status-edit.status-action', function(e) {
			e.preventDefault();

			var row = $(this).closest('[data-status-id]');
			var id = row.attr('data-status-id');

			if (id) {
				var overlay = new DeskPRO.UI.Overlay({
					contentMethod: 'ajax',
					contentAjax: {
						url: BASE_URL + 'agent/twitter/status/ajax-edit',
						data: { account_status_id: id }
					},
					zIndex: 40000, // Above floating people windows
					onAjaxDone: function() {
						var wrapper = overlay.getWrapper();
						var textarea = wrapper.find('textarea[name=text]');

						textarea.TextAreaExpander().focus();

						wrapper.find('.save-trigger').click(function() {
							wrapper.addClass('loading');

							$.ajax({
								url: BASE_URL + 'agent/twitter/status/ajax-edit',
								type: 'POST',
								data: { account_status_id: id, text: textarea.val(), process: 1},
								dataType: 'json',
								success: function(data) {
									if (data.success) {
										overlay.close();

										if (row.is('.twitter-reply')) {
											row.find('.status-text').html(data.parsed_text);
										} else {
											row.find('.main-status-body .status-text').html(data.parsed_text);
										}
									} else if (data.error) {
										alert(data.error);
									}
								}
							}).always(function() { wrapper.removeClass('loading'); });
						});
					}
				});
				overlay.open();
			}
		});

		// retweet trigger
		this.content.on('click', 'li.opt-trigger.retweet', function(e) {
			e.preventDefault();

			var row = self.closestRow(this);
			var id = row.attr('data-status-id');

			var retweetContainer = row.find('.new-retweet');
			if (retweetContainer.is(':visible')) {
				retweetContainer.hide();
			} else {
				retweetContainer.show();
				row.find('.new-message').hide();

				self.removeTweetNotification(id);

				var textarea = retweetContainer.find('textarea');
				if (!textarea.hasClass('tae')) {
					textarea.TextAreaExpander();
				}
				self.updateTweetLength(textarea);
				textarea.focus();
			}
		});
		this.content.on('change', '.new-retweet .retweet-type input[type=radio]', function() {
			var $this = $(this), container = $this.closest('.new-retweet');
			if ($this.val() == '1') {
				container.find('.edit-only').hide();
			} else {
				container.find('.edit-only').show();
			}
		});
		this.content.on('keypress keyup change', '.new-retweet textarea', function() {
			var $this = $(this);
			setTimeout(function() {
				self.updateTweetLength($this);
			}, 0);
		});
		this.content.on('click', '.cancel-retweet-trigger', function() {
			var retweetContainer = $(this).closest('.new-retweet');
			retweetContainer.hide();
		});
		this.content.on('click', '.save-retweet-trigger', function(e) {
			e.preventDefault();

			var row = self.closestRow(this);
			var id = row.attr('data-status-id');
			var retweetContainer = $(this).closest('.new-retweet');

			var val = $.trim(retweetContainer.find('textarea').val());
			if (!val.length) {
				retweetContainer.hide();
				return;
			}

			var data = retweetContainer.find('form').serializeArray();

			data.push({
				name: 'account_status_id',
				value: id
			});

			retweetContainer.addClass('loading');

			self.removeTweetNotification(id);

			$.ajax({
				url: page.getMetaData('saveRetweetUrl'),
				type: 'POST',
				dataType: 'json',
				data: data,
				success: function(json) {
					if (json.success) {
						if (json.html && json.html.length) {
							for (var i = 0; i < json.html.length; i++) {
								var html = $(json.html[i]);
								row.find('.twitter-replies').append(html);
								$('.timeago', html).timeago();
							}

							row.find('.reply-list').show();
						}

						if (json.retweet) {
							var link = row.find('li.opt-trigger.retweet');
							link.addClass('retweeted').removeClass('retweet');
							link.find('label').text('Retweeted');
						}

						retweetContainer.hide();

						if (json.archived) {
							row.addClass('archived');
							row.find('.status-archive').hide();
							row.find('.status-archived').show();

							if (page.menuOptions && !page.menuOptions.filter('[name=archived]').is(':checked')) {
								row.fadeOut('fast', function() {
									if (self.options.statusArchiveHideCallback) {
										self.options.statusArchiveHideCallback(row);
									}
									row.remove();
								});
							}
						}
					} else {
						alert(json.error);
					}
				}
			}).always(function() {
				retweetContainer.removeClass('loading');
			});
		});

		// unretweet trigger
		this.content.on('click', 'li.opt-trigger.retweeted', function(e) {
			e.preventDefault();

			var link = $(this);
			var id = self.closestRow(this).attr('data-status-id');

			if (id && confirm('Are you sure you want to un-retweet this?')) {
				self.removeTweetNotification(id);

				$.ajax({
					url: page.getMetaData('saveUnretweetUrl'),
					type: 'POST',
					dataType: 'json',
					data: {
						account_status_id: id
					},
					success: function(json) {
						if (json.success) {
							link.addClass('retweet').removeClass('retweeted');
							link.find('label').text('Retweet');
						} else {
							alert(json.error);
						}
					}
				});
			}
		});

		// message triggers
		this.content.on('click', 'li.opt-trigger.message', function(e) {
			e.preventDefault();

			var row = self.closestRow(this);
			var id = row.attr('data-status-id');

			var newMessage = row.find('.new-message');
			if (newMessage.is(':visible')) {
				newMessage.hide();
			} else {
				newMessage.show();
				row.find('.new-retweet').hide();

				self.removeTweetNotification(id);

				var textarea = newMessage.find('textarea');

				if (!textarea.hasClass('tae')) {
					textarea.TextAreaExpander();
				}

				if (!$.trim(textarea.val()).length && !row.hasClass('dm')) {
					var name = row.find('.main-status-body .screen-name, h4 .screen-name').first().text();

					textarea.val(name + ' ');
				}

				if (self.page.getMetaData('tweetSignature')) {
					textarea.val(textarea.val() + self.page.getMetaData('tweetSignature'));
				}

				self.updateTweetLength(textarea);
				textarea.focus();
			}
		});

		this.content.on('keypress keyup change', '.new-message textarea', function() {
			var $this = $(this);
			setTimeout(function() {
				self.updateTweetLength($this);
			}, 0);
		});
		this.content.on('click', '.new-message .message-type li', function() {
			var $this = $(this);
			var messageContainer = $this.closest('.new-message');
			var first = messageContainer.find('.message-type li').not($this).first();

			$this.removeClass('on');
			first.addClass('on');
			messageContainer.find('.message-type-hidden').val(first.data('type'));
		});
		this.content.on('click', '.cancel-message-trigger', function() {
			var messageContainer = $(this).closest('.new-message');
			messageContainer.hide();
		});
		this.content.on('click', '.save-message-trigger', function(e) {
			e.preventDefault();

			var row = self.closestRow(this);
			var id = row.attr('data-status-id');
			var messageContainer = $(this).closest('.new-message');

			var val = $.trim(messageContainer.find('textarea').val());
			if (!val.length) {
				messageContainer.hide();
				return;
			}

			var data = messageContainer.find('form').serializeArray();

			self.removeTweetNotification(id);

			if (self.options.saveMessageCallback === 'default') {
				messageContainer.addClass('loading');

				$.ajax({
					url: self.options.messageUrl,
					type: 'POST',
					dataType: 'json',
					data: data,
					success: function(json) {
						if (json.success) {
							if (json.html && json.html.length) {
								for (var i = 0; i < json.html.length; i++) {
									var html = $(json.html[i]);
									row.find('.twitter-replies').append(html);
									$('.timeago', html).timeago();
								}

								row.find('.reply-list').show();
							}

							row.find('.message-sent-confirmation .message').text(val);
							row.find('.message-sent-confirmation').show();

							messageContainer.hide();
							messageContainer.find('textarea').val('');

							if (json.archived) {
								row.addClass('archived');
								row.find('.status-archive').hide();
								row.find('.status-archived').show();

								if (page.menuOptions && !page.menuOptions.filter('[name=archived]').is(':checked')) {
									row.fadeOut('fast', function() {
										if (self.options.statusArchiveHideCallback) {
											self.options.statusArchiveHideCallback(row);
										}
										row.remove();
									});
								}
							}
						} else {
							alert(json.error);
						}
					}
				}).always(function() {
					messageContainer.removeClass('loading');
				});
			} else if (self.options.saveMessageCallback) {
				self.options.saveMessageCallback(data, row);
			}
		});

		// note triggers
		this.content.on('click', '.note-btn', function() {
			var row = self.closestRow(this);
			var id = row.attr('data-status-id');

			var newNote = row.find('.new-note');
			if (newNote.is(':visible')) {
				newNote.hide();
			} else {
				var textarea = newNote.find('textarea');
				if (!textarea.data('redactor')) {
					self.initializeNoteEditor(textarea, page.getMetaData('agentMap'));
				}

				newNote.show();

				self.removeTweetNotification(id);

				if (textarea.data('redactor')) {
					textarea.setFocus();
				} else {
					textarea.focus();
				}
			}
		});
		this.content.on('click', '.cancel-note-trigger', function() {
			var noteContainer = $(this).closest('.new-note');
			noteContainer.hide();
		});
		this.content.on('click', '.save-note-trigger', function(e) {
			e.preventDefault();

			var row = self.closestRow(this);
			var id = row.attr('data-status-id');
			var noteContainer = $(this).closest('.new-note');

			var textarea = noteContainer.find('textarea');
			if (textarea.data('redactor')) {
				textarea.data('redactor').syncCode();
			}

			var val = $.trim(textarea.val());
			if (!val.length) {
				noteContainer.hide();
				return;
			}

			noteContainer.addClass('loading');

			self.removeTweetNotification(id);

			$.ajax({
				url: page.getMetaData('saveNoteUrl'),
				type: 'POST',
				dataType: 'json',
				data: {
					account_status_id: id,
					text: val
				},
				success: function(json) {
					if (json.success) {
						if (json.html) {
							var html = $(json.html);
							row.find('.note-list').append(html);
							$('.timeago', html).timeago();

							row.find('.status-notes').show();
						}

						noteContainer.hide();
						textarea.val('');
						if (textarea.data('redactor')) {
							textarea.setCode('');
						}
					} else {
						alert(json.error);
					}
				}
			}).always(function() {
				noteContainer.removeClass('loading');
			});
		});

		// assignment
		this.content.on('click', '.opt-trigger.agent > label, .opt-trigger.agent span', function(ev) {
			var li = $(this).closest('li');
			var row = $(this).closest('article.twitter-status');

			var select = li.find('select');
			if (!select.data('select2')) {
				DP.select(select);

				select.on('change', function() {
					var val = $(this).val();
					var sel = $(this).find(':selected');
					var label = sel.text().trim();

					self.removeTweetNotification(id);

					if (val == 'agent:' + DESKPRO_PERSON_ID) {
						label = 'Me';
					}

					var labelEl = row.find('li.opt-trigger.agent label');
					if (sel.data('icon')) {
						labelEl.text(' ' + label).prepend($('<img class="agent-assign-icon" />').attr('src', sel.data('icon')));
					} else {
						labelEl.text(label);
					}

					var id = $(this).closest('.twitter-status').attr('data-status-id');

					$.ajax({
						url: page.getMetaData('saveAssignUrl'),
						type: 'POST',
						dataType: 'json',
						data: { account_status_id: id, assign: val },
						success: function(json) {
							if (json.error) {
								alert(json.error);
							}
						}
					});
				});

				li.find('.select2-container').css({
					height: 5,
					overflow: 'hidden'
				})
			}

			var oldOpen = select.data('select2').open;
			var oldClose = select.data('select2').close;
			var s2 = select.data('select2');
			s2.open = function() {
				oldOpen.call(s2);
				select.data('select2').container.addClass('select2-dropdown-open');
			};
			s2.close = function() {
				select.data('select2').container.addClass('select2-dropdown-open');
				oldClose.call(s2);
			};

			s2.open();
		});
	},

	updateTweetLength: function(textarea) {
		var text = textarea.val();
		text = text.replace(/\r?\n/g, ' ')
			.replace(/http:\/\/(?=([^ \t\r\n[\]#]+))\1(?!#)/g, '1234567890123456789012')
			.replace(/https:\/\/(?=([^ \t\r\n[\]#]+))\1(?!#)/g, '1234567890123456789013');

		var newMessageArea = textarea.closest('.new-message, .new-retweet'),
			charCount = newMessageArea.find('.character-count'),
			charCountCounter = charCount.find('em'),
			overOptions = newMessageArea.find('.over-options');

		if (text.length > 140) {
			charCount.hide();
			overOptions.show();
		} else {
			charCountCounter.text(140 - text.length);
			charCount.show();
			overOptions.hide();
		}
	},

	closestRow: function(el) {
		return $(el).closest('.row-item, .overlay-content, .twitter-status-inline');
	},

	doArchiveStatus: function(id, archive) {
		$.ajax({
			url: this.page.getMetaData('saveArchiveUrl'),
			type: 'POST',
			dataType: 'json',
			data: { account_status_id: id, archive: archive ? 1 : 0 },
			success: function(json) {
				if (json.error) {
					alert(json.error);
				}
			}
		});
	},

	doFavorite: function(id, favorite) {
		$.ajax({
			url: this.page.getMetaData('saveFavoriteUrl'),
			type: 'POST',
			dataType: 'json',
			data: { account_status_id: id, favorite: favorite ? 1 : 0 },
			success: function(json) {
				if (json.error) {
					alert(json.error);
				}
			}
		});
	},

	doArchiveUser: function(id, archive) {
		$.ajax({
			url: this.page.getMetaData('saveUserArchiveUrl'),
			type: 'POST',
			dataType: 'json',
			data: {
				user_id: id,
				account_id: this.page.getMetaData('accountId'),
				archive: archive ? 1 : 0
			},
			success: function(json) {
				if (json.error) {
					alert(json.error);
				}
			}
		});
	},

	removeTweetNotification: function(id) {
		DeskPRO_Window.notifications.removeRelated('tweet:' + id);
	},

	initializeNoteEditor: function(textarea, agentMap) {
		if (textarea.data('redactor')) {
			return;
		}

		textarea.redactor({
			toolbar: false,
			buttons: [],
			shortcuts: false,
			minHeight: 50
		});

		var api = textarea.data('redactor');
		if (!api) {
			return;
		}

		var editor = textarea.getEditor();
		if (!editor) {
			return false
		}

		editor.bind('keydown', function(ev) {
			ev.stopPropagation();

			if (ev.metaKey && !ev.ctrlKey) { // pressing "cmd" on a mac
				var sel;
				if (window.getSelection && (sel = window.getSelection()) && sel.modify) {
					var adjustmentType = ev.shiftKey ? "extend" : "move";

					switch (ev.keyCode) {
						case 39: // right - act like "end" in windows
							sel.modify(adjustmentType, "right", "lineboundary");
							ev.preventDefault();
							break;

						case 37: // left - act like "home" in windows
							sel.modify(adjustmentType, "left", "lineboundary");
							ev.preventDefault();
							break;
					}
				}
			}
		});

		editor.bind('keypress', function(ev) {
			ev.stopPropagation();
		});

		DeskPRO_Window.initAgentNotifierForRte(
			self, textarea, agentMap || false, true
		);
	}

	/*_initFollow: function() {
		var buttons = $('.follow a', this.content);

		buttons.on('click', $.proxy(function(e) {
			this.doFollow($(e.target).parents('.status').attr('data-user-id'));
		}, this));
	},

	doFollow: function(id) {
		$.ajax({
			url: this.getMetaData('saveFollowUrl'),
			dataType: 'json',
			data: {
				account_id: this.getMetaData('accountId'),
				user_id: id
			},
			context: this,
			success: function(json) {
				if (json.success) {
					this.reload();
				} else {
					alert(json.error);
				}
			}
		});
	},

	_initUnfollow: function() {
		var buttons = $('.unfollow a', this.content);

		buttons.on('click', $.proxy(function(e) {
			this.doUnfollow($(e.target).parents('.status').attr('data-user-id'));
		}, this));
	},

	doUnfollow: function(id) {
		$.ajax({
			url: this.getMetaData('saveUnfollowUrl'),
			dataType: 'json',
			data: {
				account_id: this.getMetaData('accountId'),
				user_id: id
			},
			context: this,
			success: function(json) {
				if (json.success) {
					this.reload();
				} else {
					alert(json.error);
				}
			}
		});
	},*/
});