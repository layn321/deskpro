Orb.createNamespace('DeskPRO.Report.PageHandler');

DeskPRO.Report.PageHandler.ReportBuilder = new Orb.Class({
	Extends: DeskPRO.Report.PageHandler.Basic,

	initialize: function() {
	},

	// Init the page
	initPage: function() {
		var self = this;
		var onLink;

		var pageBody = $('#report-page-body'), initialized = false;

		var initialize = function(context) {
			context.find('textarea.expander').TextAreaExpander().trigger('textareaexpander_fire');
			context.find('select.readonly option:not(:selected)').attr('disabled', true);

			var title = pageBody.find('.report-title-with-params'), params = title.data('params');

			self.replacePlaceholderParams(title);

			title.find('.report-list-query-selector').each(function() {
				self.setupPlaceholderParams(this, params);
			});

			self.updateReportParams(title);
		};
		initialize($(document));

		this.setupReportListItems();

		$(document.body).delegate('a.report-favorite-toggle', 'click', function(e) {
			var $this = $(this), isFavorite = $this.hasClass('favorited'),
				newValue = isFavorite ? 0 : 1,
				url = $this.attr('href'),
				params = $this.data('report-params') || '',
				matches = $('a.report-favorite-toggle[data-report-id="' + $this.data('report-id') + '"]');

			matches.each(function() {
				var $this = $(this);

				if (($this.data('report-params') || '') === params) {
					$this.toggleClass('favorited');
				}
			});

			$.ajax({
				url: url,
				type: 'POST',
				dataType: 'html',
				data: { favorite: newValue, params: params }
			}).done(function(data) {
					var favoriteContainer = $('#report-favorites');
					favoriteContainer.find('ul:first').replaceWith(data);
					if (favoriteContainer.find('li').length) {
						favoriteContainer.show();
					} else {
						favoriteContainer.hide();
					}
				});

			e.preventDefault();
		});

		$(document.body).delegate('.report-editor-controls-show', 'click', function(e) {
			var $this = $(this), hide = $this.data('click-hide');

			e.preventDefault();

			if (!$this.data('show-text')) {
				$this.data('show-text', $this.text());
			}

			if (hide) {
				$($this.data('target')).hide();
				$this.text($this.data('show-text'));
				$this.data('click-hide', false);
			} else {
				$($this.data('target')).show();
				$this.text($this.data('hide-text'));
				$this.data('click-hide', true);
			}
		});

		$.history.init(function(hash) {
			if (hash == '' && !initialized) {
				initialized = true;
				return;
			}

			var loadingBlock = $('#report-loading-block'),
				left = pageBody.offset().left + pageBody.outerWidth() / 2 - loadingBlock.outerWidth() / 2;

			loadingBlock.appendTo(document.body).css('left', left + 'px').show();

			var failure = function() {
				pageBody.html($('#report-failed-block').html());
			};

			$.scrollTo(document.body, 200);

			$.ajax({
				url: hash || window.location.pathname + window.location.search,
				type: 'GET',
				dataType: 'html',
				timeout: 0
			}).done(function(data) {
				if (data.match(/<!--dp:report-page-body-->([\s\S]*)<!--\/dp:report-page-body-->/)) {
					pageBody.html(RegExp.$1);
					DeskPRO.ElementHandler_Exec();
					initialize(pageBody);
					self.updateFavorites();
				} else {
					failure();
				}
			}).fail(failure).always(function() {
				loadingBlock.hide();
			});
		}, {unescape: '/?'});

		$(document.body).delegate('a[rel=report-page-body]', 'click', function(e) {
			var $this = $(this), href = $this.data('report-original-href') || $this.attr('href');

			e.preventDefault();

			if (onLink) {
				onLink.removeClass('link-on');
			}
			if ($this.parent().is('li')) {
				onLink = $this.parent();
				onLink.addClass('link-on');
			}

			if ($this.is('.report-list-title, .report-title-with-params')) {
				var data = self.updateReportParams($this);
				if (data) {
					href += (href.indexOf('?') >= 0 ? '&' : '?') + 'params=' + encodeURIComponent(data);
				}
			}

			$.history.load(href);
		});
	},

	getQueryChoices: function (input) {
		var choices = {}, extras = {}, extrasMatch;

		if (input.match(/^\d+:date group(.*)$/)) {
			choices = window._dpRbGroupParams.dates;
			extrasMatch = RegExp.$1;
		} else if (input.match(/^\d+:field group:([a-zA-Z0-9_]+)(.*)$/)) {
			var type = RegExp.$1;
			if (typeof window._dpRbGroupParams.fields[type] !== 'undefined') {
				choices = window._dpRbGroupParams.fields[type];
				extrasMatch = RegExp.$2;
			}
		} else if (input.match(/^\d+:status group:([a-zA-Z0-9_]+)(.*)$/)) {
			var type = RegExp.$1;
			if (typeof window._dpRbGroupParams.statuses[type] !== 'undefined') {
				choices = window._dpRbGroupParams.statuses[type];
				extrasMatch = RegExp.$2;
			}
		} else if (input.match(/^\d+:order group:([a-zA-Z0-9_]+)(.*)$/)) {
			var type = RegExp.$1;
			if (typeof window._dpRbGroupParams.orders[type] !== 'undefined') {
				choices = window._dpRbGroupParams.orders[type];
				extrasMatch = RegExp.$2;
			}
		}

		if (extrasMatch) {
			var regex = /,([a-zA-Z0-9_ ]+):([^,]+)/g, match;
			while (match = regex.exec(extrasMatch)) {
				extras[$.trim(match[1])] = $.trim(match[2]);
			}
		}

		return {
			choices: choices,
			extras: extras
		};
	},

	setupReportListItems: function() {
		var self = this;

		$('.report-list-groupable').each(function() {
			var $this = $(this),
				lookup = {};

			$this.find('li').each(function() {
				self.replacePlaceholderParams(this);
			});

			$this.find('.report-list-query-selector').each(function() {
				self.setupPlaceholderParams(this);
			});

			$this.find('.report-list-title:has(.report-list-query-selector)').each(function() {
				self.updateReportParams($(this));
			});
		});

		var querySelectorPopup = null;

		$(document.body).delegate('.report-list-query-selector', 'click', function(e) {
			var $selector = $(this),
				offset = $selector.offset(),
				newOffset = {
					top: offset.top + $selector.outerHeight(),
					left: offset.left
				},
				qspOffset = querySelectorPopup ? querySelectorPopup.offset() : false;

			if (querySelectorPopup && querySelectorPopup.is(':visible')
				&& qspOffset.top == newOffset.top && qspOffset.left == newOffset.left) {
				querySelectorPopup.hide();
				return;
			}

			if (!querySelectorPopup) {
				querySelectorPopup = $('<ul class="report-list-popup" />');
				$(document.body).append(querySelectorPopup);
				$(document.body).click(function() {
					querySelectorPopup.hide();
				})
			}

			querySelectorPopup.hide();
			querySelectorPopup.empty();

			var choices = self.getQueryChoices($selector.data('report-list-query'));
			for (var i in choices.choices) {
				(function(i) {
					var li = $('<li></li>').click(function (e) {
						e.preventDefault();
						e.stopPropagation();

						querySelectorPopup.hide();

						$selector.data('report-list-query-selected', i);
						$selector.text(choices.choices[i][0]);
						window.DeskPRO_Page.updateReportParams($selector.closest('a'));
						window.DeskPRO_Page.updateFavorites();
						$selector.closest('a').click();
					});

					var a = $('<a />').text(choices.choices[i][0]);

					li.append(a);
					querySelectorPopup.append(li);
				})(i);
			}

			if (querySelectorPopup.find('li').length == 0) {
				return;
			}

			querySelectorPopup.css(newOffset).show();

			e.stopPropagation();
			e.preventDefault();
		});
	},

	replacePlaceholderParams: function(el) {
		var $item = $(el),
			textEl = $item.find('a.report-list-title');

		if (!textEl.length) {
			textEl = $item;
		}

		var titleHtml = textEl.html(),
			newTitle = titleHtml,
			regex = /\&lt;(\d+:.+?)\&gt;/g,
			match,
			matched = 0;

		if (!titleHtml) {
			return;
		}

		while (match = regex.exec(titleHtml)) {
			newTitle = newTitle.replace(match[0], '<span class="report-list-query-selector">' + match[1] + '</span>');
			matched++;
		}

		newTitle = newTitle.replace(/&lt;chart:([a-z0-9_-]+)&gt;/gi, '<span class="report-chart-icon report-chart-icon-$1"></span>');

		textEl.html(newTitle);
	},

	setupPlaceholderParams: function(el, params) {
		var $selector = $(el),
			text = $selector.text();

		$selector.data('report-list-query', text);

		var choices = this.getQueryChoices(text),
			d = 'default',
			index = text.replace(/^(\d)+:(\s|\S)*$/, '$1'),
			i = (params && params[index]) ? params[index] : choices.extras[d];
		if (!i || !choices.choices[i]) {
			for (i in choices.choices) {
				break;
			}
			if (!i) {
				return;
			}
		}
		$selector.text(choices.choices[i][0]);
		$selector.data('report-list-query-selected', i);
	},

	updateReportParams: function(item) {
		var params = {}, keys = [], value;

		item.find('.report-list-query-selector').each(function (){
			var $selector = $(this),
				id = 0,
				selected = $selector.data('report-list-query-selected');

			if ($selector.data('report-list-query').match(/^(\d+):/)) {
				id = RegExp.$1;
			} else {
				return;
			}

			params[id] = selected;
			keys.push(id);
		});

		if (keys.length) {
			keys.sort();
			var out = [];
			for (var i = 0; i < keys.length; i++) {
				out.push(params[keys[i]]);
			}

			value = out.join(',');
		} else {
			value = '';
		}

		item.data('report-params', value);
		item.siblings('.report-favorite-toggle').data('report-params', value);

		if (item.is('.report-list-title, .report-title-with-params')) {
			var href = item.data('report-original-href');
			if (!href) {
				href = item.attr('href');
				item.data('report-original-href', href);
			}
			if (value) {
				href += (href.indexOf('?') >= 0 ? '&' : '?') + 'params=' + encodeURIComponent(value);
			}
			item.attr('href', href);
		}

		return value;
	},

	updateFavorites: function(update) {
		if ($.isArray(update)) {
			this.favorites = update;
		}

		if (!this.favorites) {
			this.favorites = [];
		}

		var favorites = this.favorites;

		$('a.report-favorite-toggle').each(function() {
			var $this = $(this),
				reportId = $this.data('report-id'),
				params = $this.data('report-params') || '';

			for (var i = 0; i < favorites.length; i++) {
				if (favorites[i].id == reportId && params === favorites[i].params) {
					$this.addClass('favorited');
					return;
				}
			}

			$this.removeClass('favorited');
		});
	}

});
