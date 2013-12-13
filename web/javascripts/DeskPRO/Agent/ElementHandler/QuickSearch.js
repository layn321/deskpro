Orb.createNamespace('DeskPRO.Agent.ElementHandler');

DeskPRO.Agent.ElementHandler.QuickSearch = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	initPage: function() {
		var searchBox  = $('#dp_search_box');
		var searchHelp = $('#dp_header_search_help');
		var searchUrl  = this.el.data('search-url');
		var listWrap   = $('#dp_search_box_list_wrap');
		var list       = $('#dp_search_box_list');
		var eatClick   = false;

		searchBox.data('handler', {
			setSearch: function(term) {
				searchBox.focus();
				searchBox.val('');
				searchBox.val(term);
				updateSearch(true);
			}
		});

		list.on('click', '.show-more', function(ev) {
			Orb.cancelEvent(ev);
			var type = $(this).closest('.title').data('type');
			if (type) {
				list.find('li.type-' + type).show().addClass('dp-vis');
			}
			$(this).hide();

			searchBox.addClass('dp-focus');
			searchBox.removeClass('dp-stick-open');
			searchBox.focus();
		});

		listWrap.on('mousedown', function(ev) {
			$(this).addClass('dp-focus');
			searchBox.focus();
			Orb.cancelEvent(ev);
			ev.stopImmediatePropagation();
		});

		list.on('mousedown', function(ev) {
			$(this).addClass('dp-focus');
			searchBox.focus();
			Orb.cancelEvent(ev);
			ev.stopImmediatePropagation();
		});

		//------------------------------
		// Helpbox trigger
		//------------------------------

		$('#dp_search_box_help_trigger').on('mousedown', function(ev) {
			Orb.cancelEvent(ev);
			searchBox.addClass('dp-stick-open dp-regaining-focus');
			searchHelp.addClass('active');

			var closeFn = function() {
				searchBox.addClass('dp-focus');
				searchBox.removeClass('dp-stick-open');
				searchHelp.removeClass('active');
				searchBox.focus();

				window.setTimeout(function() {
					searchBox.removeClass('dp-regaining-focus');
				}, 200);
			};

			if (!searchHelp.data('has-init')) {
				searchHelp.on('click', function(ev) {
					ev.stopPropagation();
				});
			}

			Orb.shimClickCallback(closeFn, 'zindex-chrome0');
		});

		$('#search_icons_nav').find('li').on('click', function(ev) {
			Orb.cancelEvent(ev);
			var section = $(this).data('target-section');
			DeskPRO_Window.switchToSection(section);
			DeskPRO_Window.sections[section].getSectionElement().find('.pane-tabs').find('[data-tab-id="pane-content-search"]').click();
			Orb.shimClickCallbackPop();
		});

		//------------------------------
		// Expanding the search box
		//------------------------------

		var lastWinW = 0;
		searchBox.on('focus', function() {

			var winW = $(window).width();
			$(this).addClass('dp-focus');
			if ($(this).hasClass('expanded')) {

				if (list.find('li')[0]) {
					openResults();
				}

				return;
			}

			var txt, wrap, w, addToW;

			addToW = 125;
			if (!$('#dp_header_logo_wrap').is(':visible')) {
				addToW = 0;
			}

			$('#dp_header_logo_wrap').hide();

			wrap = $('#dp_header_search_wrap');
			if (wrap.data('orig-width') && winW == lastWinW) {
				w = wrap.data('orig-width');
			} else {
				w = wrap.width();
				wrap.data('orig-width', w);
			}
			wrap.width(w + addToW);

			if (addToW) {
				wrap.css('margin-right', '8px');
			}

			txt = $(this);
			if (txt.data('orig-width') && winW == lastWinW) {
				w = txt.data('orig-width');
			} else {
				w = txt.width();
				txt.data('orig-width', w);
			}
			txt.addClass('expanded');

			if (addToW) {
				txt.stop();
				txt.animate({ width: w+addToW }, 300, function() {
					$('#dp_search_box_help_trigger').show();
					$('#dp_search_box_help_trigger').css('opacity', 0);
					$('#dp_search_box_help_trigger').animate({opacity: 100}, 1500);
				});
			} else {
				$('#dp_search_box_help_trigger').show();
				$('#dp_search_box_help_trigger').css('opacity', 0);
				$('#dp_search_box_help_trigger').stop();
				$('#dp_search_box_help_trigger').animate({opacity: 100}, 1500);
			}

		}).on('blur', function() {
			$(this).removeClass('dp-focus');

			if ($(this).hasClass('dp-regaining-focus')) {
				return;
			}

			window.setTimeout(function() {
				if (!searchBox.hasClass('dp-focus')) {
					closeResults();
				}

				if (searchBox.val().length || searchBox.hasClass('dp-stick-open')) {
					return;
				}

				var txt, wrap, w;

				wrap = $('#dp_header_search_wrap');
				wrap.width(wrap.data('orig-width'));
				wrap.css('margin-right', '0');

				txt = searchBox;
				txt.stop();
				$('#dp_search_box_help_trigger').stop();
				txt.width(txt.data('orig-width')).removeClass('expanded');
				$('#dp_search_box_help_trigger').hide();

				$('#dp_header_logo_wrap').show();
			}, 130);
		});

		//------------------------------
		// Updating and selecting search results
		//------------------------------

		var clearResults = function() {
			list.empty();
		};

		var closeResults = function() {
			listWrap.hide();
		};

		var openResults = function() {
			listWrap.show();
		};

		var setResults = function(results, clear) {
			var self = this;
			clearResults();
			if (!results) {
				closeResults();
				return;
			}

			var count = 0;

			Object.each(results, function(typeResults, type) {

				var sectionEl, listEl, resultEl, hasMore, res, subList, subResultEl, subRes;

				if (!typeResults || !typeResults.length || !$.isArray(typeResults)) {
					return;
				}

				sectionEl = $(DeskPRO_Window.util.getPlainTpl('#dp_header_search_row_title_tpl'));
				sectionEl.data('type', type);
				sectionEl.find('.type-icon').addClass($('#dp_header_search_row_title_tpl').data('icon-' + type) || 'icon-caret-right');
				sectionEl.find('.type-title').text($('#dp_header_search_row_title_tpl').data('title-' + type) || type);
				sectionEl.find('.show-more').hide();
				sectionEl.appendTo(list);

				hasMore = false;
				for (var ri = 0; ri < typeResults.length; ri++) {
					count++;
					res = typeResults[ri];

					resultEl = $(DeskPRO_Window.util.getPlainTpl('#dp_header_search_row_tpl'))
					resultEl.addClass(type + ' ' + 'type-' + type).data('type', type);
					resultEl.find('.row-id').text(res.id)
					resultEl.find('.row-title').html(res.title);
					resultEl.data('route', res.route).attr('data-route', res.route);
					resultEl.data('route-notabreload', '1').attr('data-route-notabreload', '1');

					if (res.subs) {
						subList = $('<ul></ul>');
						for (var sub_ri = 0; sub_ri < res.subs.length; sub_ri++) {
							subRes = res.subs[sub_ri];
							subResultEl = $(DeskPRO_Window.util.getPlainTpl('#dp_header_search_row_tpl'))
							subResultEl.addClass(subRes.type + ' ' + 'type-' + subRes.type).data('type', subRes.type);
							subResultEl.find('.row-id').text(subRes.id)
							subResultEl.find('.row-title').html(subRes.title);
							subResultEl.data('route', subRes.route).attr('data-route', subRes.route);
							subResultEl.data('route-notabreload', '1').attr('data-route-notabreload', '1');
							subResultEl.appendTo(subList);
						}

						subList.hide();
						subList.appendTo(resultEl);

						resultEl.find('.row-title').first().before('<span class="sublist-toggle"></span>');
					}


					if (ri >= 5) {
						resultEl.hide();
						hasMore = true;
					} else {
						resultEl.addClass('dp-vis')
					}

					resultEl.appendTo(list);
				}
				if (hasMore) {
					var showMoreEl = sectionEl.find('.show-more');
					Orb.phraseTextEl(showMoreEl, {count: typeResults.length - 5});
					showMoreEl.show();
				}
			}, this);

			list.find('.sublist-toggle').on('click', function(ev) {
				Orb.cancelEvent(ev);
				eatClick = true;

				$(this).addClass('dp-focus');
				searchBox.focus();

				if ($(this).hasClass('expanded')) {
					$(this).removeClass('expanded');
					$(this).closest('li').find('ul').hide();
				} else {
					$(this).addClass('expanded');
					$(this).closest('li').find('ul').show();
				}
			});

			list.find('[data-route]').on('click', function(ev) {
				console.log("CLick");
				Orb.cancelEvent(ev);
				ev.stopImmediatePropagation();
				DeskPRO_Window.runPageRouteFromElement($(this));
				searchBox.blur();
				closeResults();
				eatClick = false;
			});

			if (count) {
				openResults();
			} else {
				closeResults();
			}
		};

		var updateSearch = function(force) {
			if (updateTimeout) {
				window.clearTimeout(updateTimeout);
				updateTimeout = null;
			}

			if (runningAjax) {
				if (force) {
					runningAjax.abort();
				} else {
					return;
				}
			}

			var input = $.trim(searchBox.val());

			if (input === "") {
				clearResults();
				closeResults();
			}

			if (input == prevInput && !force) {
				return;
			}

			prevInput = input;

			$('#dp_search_box_help_trigger').hide();
			$('#dp_search_box_loading').show();
			$.ajax({
				url: searchUrl,
				data: { q: input },
				type: 'GET',
				dataType: 'json',
				complete: function() {
					$('#dp_search_box_help_trigger').show();
					$('#dp_search_box_loading').hide();

					runningAjax = null;
					if (updateTimeout) {
						window.clearTimeout(updateTimeout);
						updateTimeout = null;
					}
					updateSearch(); // re-run, nothing will happen if no changes
				},
				success: function(results) {
					setResults(results);
				}
			});
		};

		var runningAjax = null;
		var eatNext = false;
		var prevInput = null;
		var updateTimeout = null;
		searchBox.on('keydown', function(ev) {
			if (ev.keyCode == 13 /* enter key */) {
				Orb.cancelEvent(ev);
				var current = list.find('.dp-cursor');
				eatNext = true;

				// Selected item
				if (current[0]) {
					DeskPRO_Window.runPageRouteFromElement(current);
					searchBox.blur();
					closeResults();

				// Or else just re-run the current search
				} else {
					updateSearch(true);
				}

			} else if (ev.keyCode == 40 /* down key */ || ev.keyCode == 38 /* up key */) {
				Orb.cancelEvent(ev);
				eatNext = true;
				var current = list.find('.dp-cursor');
				current.removeClass('dp-cursor');
				var dir = ev.keyCode == 40 ? 'down' : 'up';
				var next;

				if (!current.length) {
					if (dir == 'down') {
						list.find('.dp-vis').first().addClass('dp-cursor');
					} else {
						list.find('.dp-vis').last().addClass('dp-cursor');
					}
				} else {
					if (dir == 'down') {
						next = current.next('li.dp-vis');
						if (!next.length) {
							// could be a title to skip
							next = current.next().next('li.dp-vis');
						}
						if (!next.length) {
							next = list.find('.dp-vis').first().addClass('dp-cursor');
						}
					} else {
						next = current.prev('li.dp-vis');
						if (!next.length) {
							// could be a title to skip
							next = current.prev().prev('li.dp-vis');
						}
						if (!next.length) {
							next = list.find('.dp-vis').last().addClass('dp-cursor');
						}
					}

					next.addClass('dp-cursor');
				}
			} else {
				if (!updateTimeout) {
					updateTimeout = window.setTimeout(updateSearch, 520);
				}
			}
		}).on('keypress', function(ev) {
			if (!updateTimeout) {
				updateTimeout = window.setTimeout(updateSearch, 520);
			}
		});
	}
});