Orb.createNamespace('DeskPRO.Report.ElementHandler.Builder');

DeskPRO.Report.ElementHandler.Builder.ReportList = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var el = this.el;

		// ************************* replaceable [group] types in reports

		el.find('.report-list-groupable').each(function() {
			var $this = $(this),
				lookup = {};

			$this.find('li').each(function() {
				var $item = $(this),
					textEl = $item.find('a.report-list-title'),
					title = textEl.text(),
					testTitle = title,
					plainTitle = title,
					regex = /\[(.+?)\]/g,
					match,
					matches = [];

				while (match = regex.exec(title)) {
					testTitle = testTitle.replace(match[0], '[]');
					plainTitle = plainTitle.replace(match[0], match[1]);
					matches.push(match[1]);
				}

				if (!matches.length) {
					return;
				}

				$item.data('test-title', testTitle);
				textEl.data('original-html', textEl.html());
				textEl.text(plainTitle);

				if (typeof lookup[testTitle] == 'undefined') {
					lookup[testTitle] = [];
				}
				lookup[testTitle].push({
					matches: matches,
					item: $item,
					textEl: textEl
				});
			});

			for (var testTitle in lookup) {
				if (lookup[testTitle].length > 1) {
					for (var j = 0; j < lookup[testTitle].length; j++) {
						var item = lookup[testTitle][j].item,
							textEl = item.find('a.report-list-title'),
							html = textEl.data('original-html'),
							matchId = 0;

						item.data('lookup-key', j);

						html = html.replace(/\[(.+?)\]/g, function(match, inner) {
							return '<span class="report-list-selector" data-match="' + (matchId++) + '">' + inner + '</span>';
						});
						textEl.html(html);

						if (j > 0) {
							item.hide();
						}
					}
				}
			}

			var popups = {}, visiblePopup;
			var deleteAllPopups = function() {
				for (var i in popups) {
					deletePopup(i, popups[i]);
				}
			};
			var deletePopup = function(popupId, popup) {
				if (popup) {
					popup.remove();
					delete popups[popupId];
				}
				if (visiblePopup == popupId) {
					visiblePopup = false;
				}
			};
			$(document.body).click(deleteAllPopups);

			$this.delegate('.report-list-selector', 'click', function(e) {
				var $this = $(this),
					offset = $this.offset(),
					item = $this.closest('li'),
					testTitle = item.data('test-title'),
					lookupKey = item.data('lookup-key'),
					matchId = $this.data('match'),
					popupId = testTitle + '-' + matchId,
					entries = lookup[testTitle],
					shownLookup = entries[lookupKey],
					popup;

				if (visiblePopup && visiblePopup != popupId) {
					deletePopup(visiblePopup, popups[visiblePopup]);
				}

				if (!popups[popupId]) {
					var affinities = {}, minAffinity = 0, testAffinity = shownLookup.matches.length - 1;
					for (var i = 0; i < entries.length; i++) {
						var entry = entries[i], affinity = 0;
						for (j = 0; j < entry.matches.length; j++) {
							if (shownLookup.matches[j] == entry.matches[j]) {
								affinity++;
							}
						}

						if (!affinities[entry.matches[matchId]] || affinity > affinities[entry.matches[matchId]].affinity) {
							affinities[entry.matches[matchId]] = {affinity: affinity, entry: entry};
						}
					}

					popup = $('<ul class="report-list-popup" />');
					for (var i = 0; i < entries.length; i++) {
						var entry = entries[i];

						if (affinities[entry.matches[matchId]].affinity < minAffinity
							|| affinities[entry.matches[matchId]].entry != entry
						) {
							continue;
						}

						(function(entry, i) {
							var li = $('<li></li>').click(function (e) {
								e.stopPropagation();
								e.preventDefault();

								deleteAllPopups();

								for (var j = 0; j < entries.length; j++) {
									if (i == j) {
										entries[j].item.show().find('a.report-list-title').click();
									} else {
										entries[j].item.hide();
									}
								}
							});

							var a = $('<a />').text(entry.matches[matchId]);

							li.append(a);

							if (affinities[entry.matches[matchId]].affinity < testAffinity) {
								a.addClass('low-affinity');
							}

							popup.append(li);
						})(entry, i);
					}
					popup.css({
						display: 'none'
					});

					$(document.body).append(popup);
					popups[popupId] = popup;
				} else {
					popup = popups[popupId];
				}

				if (popup.is(':visible')) {
					deletePopup(popupId, popup);
					// ...and follow link
				} else {
					e.stopPropagation();
					e.preventDefault();

					popup.css({
						top: offset.top + $this.outerHeight(),
						left: offset.left
					}).show();

					visiblePopup = popupId;
				}
			})
		});

		// ************************* draggable column

		var offset = el.offset(),
			cookie = el.data('cookie'),
			cookieValue = cookie ? $.cookie(cookie) : false,
			widthDiff = el.outerWidth() - el.width(),
			minWidth = el.data('min') || 100,
			maxWidth = el.data('max') || 1000,
			dragger;

		if (cookieValue) {
			el.css('width', Math.min(maxWidth, Math.max(minWidth, cookieValue)) + 'px');
			$('#report-page-body').css('left', Math.min(maxWidth, Math.max(minWidth, cookieValue)) + 21);
		} else {
			$('#report-page-body').css('left', el.width() + 21);
		}

		dragger = $('<div class="report-list-dragger" />').css({
			position: 'absolute',
			left: (offset.left + el.outerWidth()) + 'px'
		}).draggable({
			axis: 'x',
			containment: [offset.left + minWidth + widthDiff, 0, offset.left + maxWidth + widthDiff, 0],
			drag: function(e) {
				var width = Math.max(minWidth, dragger.offset().left - offset.left - widthDiff);
				el.css('width', width + 'px');
				if (cookie) {
					$.cookie(cookie, width, {expires: 7});
				}
				$('#report-page-body').css('left', width + 21);
			}
		}).appendTo(document.body);

		el.resize(function() {
			dragger.css('height', el.outerHeight() + 'px');
		});
	}
});
DeskPRO.Report.ElementHandler.Builder.ReportList.fieldGroups = {};
DeskPRO.Report.ElementHandler.Builder.ReportList.dateGroups = {};