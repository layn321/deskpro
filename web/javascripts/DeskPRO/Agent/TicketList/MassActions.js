Orb.createNamespace('DeskPRO.Agent.TicketList');

DeskPRO.Agent.TicketList.MassActions = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(page, options)  {
		this.page = page;

		this.options = {
			/**
			 * The object that will handle updating the actual
			 * list for the user
			 */
			viewHandler: null,

			/**
			 * The selection bar we can hook into to figure out
			 * when an item is selected or deselected
			 */
			selectionBar: null,

			/**
			 * The element within the list pane that contains the ticket results
			 * Defaults to 'wrapper .list-listing'
			 * @option {jQUery}
			 */
			listWrapper: null,

			/**
			 * The URL we'll post IDs to to get updated items
			 */
			fetchPreviewUrl: null,

			/**
			 * The button to trigger openeing the overlay.
			 * Null: defaults to 'wrapper .perform-actions-trigger',
			 * False: dont auto-assign trigger
			 */
			triggerElement: null,

			/**
			 * The HTML element with the actual controls etc we'll use for this
			 * Defaults to 'wrapper .mass-actions-overlay'
			 */
			templateElement: null,

			/**
			 * Disable the previewing feature and handle list view
			 */
			isListView: false,

			/**
			 * Reset the widget every time its closed
			 */
			resetOnClose: true
		};

		this.setOptions(options);

		this.viewHandler     = this.options.viewHandler;
		this.selectionBar    = this.options.selectionBar || page.selectionBar;
		this.fetchPreviewUrl = this.options.fetchPreviewUrl || page.meta.fetchResultsUrl;
		this.listWrapper     = this.options.listWrapper;

		if (!this.listWrapper) {
			if (this.options.isListView) {
				this.listWrapper = $('.table-result-list table', page.wrapper);
			} else {
				this.listWrapper = $('.list-listing', page.wrapper);
			}
		}

		this.wrapperEl = this.options.templateElement;
		if (!this.wrapperEl || !this.wrapperEl[0]) {
			this.wrapperEl = page.wrapper.find('.mass-actions-overlay-tpl');
			if (!this.wrapperEl[0]) {
				this.wrapperEl = null;
			}
		}
		if (!this.wrapperEl) {
			$('div.mass-actions-overlay-container', page.wrapper);
		}

		if(!this.wrapperEl.length) {
			return;
		}

		this._resetWrapper();

		this.backdropEls = null;

		this.countEl = $('.selected-tickets-count', this.getElement());

		var trigger = null;
		if (this.options.triggerElement === null) {
			trigger = $('.perform-actions-trigger', page.wrapper);
		} else if (this.options.triggerElement) {
			trigger = this.options.triggerElement;
		}

		if (trigger) {
			trigger.on('click', (function(ev) {
				ev.preventDefault();
				ev.stopPropagation();
				this.open();
			}).bind(this));
		}
	},

	updateUi: function() {
		if (this.scrollerHandler) {
			this.scrollerHandler.updateSize();
		}
	},

	_resetWrapper: function() {
		if (this.wrapper) {
			this.wrapper.remove();
		}
		if (this.wrapperContainer) {
			this.wrapperContainer.remove();
		}

		if (!this.wrapperEl.is('script')) {
			this.wrapper = $('<div/>').addClass('mass-actions-overlay-container mass-actions').data('base-id', this.wrapperEl.data('base-id')).data('upload-url', this.wrapperEl.data('upload-url'));
			var wrapperHtml = this.wrapperEl.html();
			this.wrapper.html(wrapperHtml);
		} else {
			var wrapperHtml = DeskPRO_Window.util.getPlainTpl(this.wrapperEl);
			this.wrapper = $(wrapperHtml);
			this.wrapper.detach().appendTo('body');
		}

		this.wrapper.find('.with-scroll-handler, .scroll-setup, .scroll-draw').removeClass('with-scroll-handler scroll-setup scroll-draw');

		this.countEl = $('.selected-tickets-count', this.wrapper);
		DP.select($('select.macro', this.wrapper));

		DeskPRO_Window.initInterfaceLayerEvents(this.wrapper);
		var scrollEl = $('.with-scrollbar', this.wrapper).first();
		if (scrollEl.length) {
			this.scrollerHandler = new DeskPRO.Agent.ScrollerHandler(null, scrollEl, {
				showEvent: 'show',
				hideEvent: 'hide'
			});
		}
	},

	/**
	 * Resets the wrapper back to the original, and then runs all of the init again.
	 */
	reset: function() {
		var wasopen = this.isOpen();
		this.close();

		this._resetWrapper();

		this._hasInit = false;
		this.hasAnyChange = false;

		if (wasopen) {
			this.open();
		}
	},


	/**
	 * Get the main wrapper element around the mass actions UI controls.
	 *
	 * @return {jQuery}
	 */
	getElement: function() {
		return this.wrapper;
	},


	/**
	 * Inits the overlay controls lazily on first open
	 */
	_initOverlay: function() {
		var self = this;
		if (this._hasInit) return;
		this._hasInit = true;

		this.wrapper.detach().appendTo('body');
		this.wrapper.css('z-index', '21001');

		this.baseId = this.wrapper.data('base-id');

		// These events registered first because hasAnyChange flag must be set before updatePreview()
		// is called
		$('select, :radio, :checkbox', this.wrapper).on('change', function() { if (!$(this).hasClass('macro')) self.hasAnyChange = true; });
		$('input, textarea', this.wrapper).on('change keypress', function() { self.hasAnyChange = true; });

		this.wrapper.on('click', function(ev) {
			ev.stopPropagation();
		});

		if (this.options.isListView) {
			this.backdropEls = $('<div class="backdrop fade" />');

		} else {
			// Three backdrops to surround each side of the list pane: left, right, top
			var back1 = $('<div class="backdrop mass-actions" />');
			var back2 = $('<div class="backdrop mass-actions" />');
			var back3 = $('<div class="backdrop mass-actions" />');
			this.backdropEls = $([back1.get(0), back2.get(0), back3.get(0)]);
		}

		this.backdropEls.css('z-index', '21000').hide().appendTo('body');

		this.backdropEls.on('click', (function(ev) {
			ev.stopPropagation();
			this.close();
		}).bind(this));

		$('header .close-trigger', this.wrapper).first().on('click', (function(ev) {
			ev.stopPropagation();
			ev.preventDefault();
			this.close();
		}).bind(this));

		//------------------------------
		// Convert radios
		//------------------------------

		var tpl = DeskPRO_Window.util.getPlainTpl($('.radio-tpl', this.wrapper));

		var groupedRadios = {};
		$(':radio.button-toggle', this.wrapper).each(function() {
			var name = $(this).attr('name');
			if (!groupedRadios[name]) {
				groupedRadios[name] = [];
			}

			groupedRadios[name].push(this);
		});

		Object.each(groupedRadios, function(els) {
			var newEls = [];
			els = $(els);

			var clickFn = function() {
				var boundId = $(this).data('bound-id');
				var radio = $('#' + boundId);

				// Toggle off already checked (ie none selected now)
				if (radio.is(':checked')) {
					radio.attr('checked', false);
					newEls.removeClass('radio-on');

				// Normal radio behavior
				} else {
					radio.attr('checked', true);
					newEls.removeClass('radio-on');
					$(this).addClass('radio-on');
				}

				self.updatePreview(null, true);
			};

			els.each(function() {

				var wrapper = $(this).parent();
				var title = $('.radio-title', wrapper).text().trim();

				var newEl = $(tpl)
				newEl.addClass($(this).data('attach-class'));
				$('.radio-title', newEl).text(title);

				if (!$(this).attr('id')) {
					$(this).attr('id', Orb.getUniqueId());
				}

				newEl.data('bound-id', $(this).attr('id'));

				newEl.on('click', clickFn);

				wrapper.hide();
				newEl.insertAfter(wrapper);

				newEls.push(newEl.get(0));
			});

			newEls = $(newEls);
		});

		//------------------------------
		// Attach change listeners
		//------------------------------

		$('input, select, textarea', this.wrapper).on('change', (function() {
			this.updatePreview();
		}).bind(this));

		this.selectionBar.addEvent('checkChange', function(el, is_checked, count) {
			if (!this.isOpen()) return;
			this.updateCount(count);
			this.handleCheckChange(el, is_checked);
		}, this);
		this.selectionBar.addEvent('checkAll', function(count) {
			if (!this.isOpen()) return;
			this.updateCount(count);
			this.updatePreview();
		}, this);
		this.selectionBar.addEvent('checkNone', function() {
			if (!this.isOpen()) return;
			this.updateCount(0);
			this.clearPreview();
		}, this);

		$('select.macro', this.wrapper).on('change', function() {
			self.loadMacro($(this).val());
			self.updatePreview(null, true);
		});

		$('.apply-actions', this.wrapper).on('click', (function(ev) {
			this.apply();
		}).bind(this));

		//------------------------------
		// Reply Box
		//------------------------------

		var textarea = this.getElById('replybox_txt'), isWysiwyg = false;
		this.textarea = textarea;

		if (DeskPRO_Window.canUseAgentReplyRte()) {
			isWysiwyg = true;

			DeskPRO_Window.initRteAgentReply(textarea, {
				defaultIsHtml: true,
				inlineHiddenPosition: this.getElById('is_html_reply'),
				minHeight: 120,
				callback: function(obj) {
					obj.addBtnFirst('dp_attach', 'Click here to attach a file. You may also drag a file from your computer desktop into this reply area to upload attachments faster.', function(){});
					obj.addBtnAfter('dp_attach', 'dp_snippets', 'Open snippets', function(){});
					obj.addBtnSeparatorAfter('dp_attach');

					snippetBtn = obj.$toolbar.find('.redactor_btn_dp_snippets').closest('li');
					snippetBtn.addClass('snippets').find('a').html('<span class="show-key-shortcut">S</span>nippets');
					snippetBtn.on('click', function(ev) {
						Orb.cancelEvent(ev);
						self.snippetsViewer.open();
					});

					var attachBtn = obj.$toolbar.find('.redactor_btn_dp_attach').closest('li');
					attachBtn.addClass('attach');
					attachBtn.find('a').text('Attach').append('<input type="file" class="file" name="file-upload" />');

					obj.addBtnSeparatorAfter('dp_snippets');
				}
			});
			this.getElById('is_html_reply').val(1);
		}

		//------------------------------
		// Snippets Viewer
		//------------------------------

		this.snippetsViewer = new DeskPRO.Agent.Widget.SnippetViewer({
			driver: DeskPRO_Window.ticketSnippetDriver,
			onBeforeOpen: function() {
				if (isWysiwyg && textarea.data('redactor')) {
					try {
						textarea.data('redactor').saveSelection();
					} catch (e) {}
				}
			},
			onSnippetClick: function(info) {

				var snippetId    = info.snippetId;
				var snippetCode  = info.snippetCode;

				var agentText;
				var defaultText;
				var useText;
				var result;

				Array.each(snippetCode, function(info) {
					if (info.value) {
						if (info.language_id == DESKPRO_PERSON_LANG_ID) {
							agentText = info.value;
						}
						if (info.language_id == DESKPRO_DEFAULT_LANG_ID) {
							defaultText = info.value;
						}
						useText = info.value;
					}
				});

				if (agentText) {
					useText = agentText;
				} else if (defaultText) {
					useText = defaultText;
				}

				result = useText;

				if (isWysiwyg && textarea.data('redactor')) {
					try {
						textarea.data('redactor').restoreSelection();
						textarea.data('redactor').setBuffer();
					} catch (e) {}

					var html = result;
					html = html.replace(/<\/p>\s*<p>/g, '<br/>');
					html = html.replace(/^<p>/, '');
					html = html.replace(/<\/p>$/, '');
					textarea.data('redactor').insertHtml(html);
				} else {
					self.page.insertTextInReply(result);
				}

				self.snippetsViewer.close();
			}
		});

		/*
		this.snippetsViewer = new DeskPRO.Agent.Widget.SnippetViewer({
			sidePosition: 'top',
			triggerElement: this.getElById('text_snippets_btn'),
			onSnippetClick: this._onSnippetClick.bind(this)
		});
		*/

		//------------------------------
		// Upload handling
		//------------------------------

		DeskPRO_Window.util.fileupload(this.wrapper, {
			page: this.page,
			url: this.wrapper.data('upload-url'),
			uploadTemplate: $('.template-upload', this.replyBox),
			downloadTemplate: $('.template-download', this.replyBox)
		});

		var sels = this.wrapper.find('select.dpe_select');

		window.setTimeout(function() {
			sels.each(function() {
				DP.select($(this));
			});
		}, 150);

		this.wrapper.bind('fileuploaddone', function() {
			self.getElById('attach_row').fadeIn();
			self.wrapper.find('[name="attach\\[\\]"]').each(function() {
				$(this).name('actions[reply][attach_ids][]');
			});
		});
		this.wrapper.bind('fileuploadstart', function() {
			self.getElById('attach_row').fadeIn();
			self.updatePositions();
		});

		this.wrapper.on('click', '.remove-attach-trigger', function() {

			var row = $(this).closest('li');
			row.remove();

			var rows = $('ul.files li', self.getElById('attach_row'));
			if (!rows.length) {
				self.getElById('attach_row').hide().addClass('is-hidden');
			}

			self.updatePositions();
		});

		var noneRow = $('li.no-changes', this.wrapper);
		var agentRow = $('li.assign-agent', this.wrapper);
		var teamRow = $('li.assign-team', this.wrapper);
		var followersRow = $('li.add-followers', this.wrapper);

		if (this.assignOptionBox) {
			this.assignOptionBox.destroy();
		}

		var add = $('.other-properties-wrapper', this.wrapper);

		// Remove all the stuff we have layed out in a different way
		// on this popup
		$('div.type', add).each(function() {
			var type = $(this).data('rule-type');
			if (!type) return;

			if (type == 'add_labels' || type == 'remove_labels' || type.indexOf('ticket_field[') !== -1 || type.indexOf('people_field[') !== -1) {

			} else {
				$(this).remove();
			}
			self.updatePositions();
		});

		this.actionsEditor = new DeskPRO.Form.RuleBuilder($('.actions-builder-tpl', add));

		var actList = $('.other-properties-wrapper', this.wrapper);
		$('.add-term-row', add).show().on('click', function() {
			var x = Orb.getUniqueId();
			var basename = 'actions_set['+x+']';
			self.actionsEditor.addNewRow($('.search-terms', actList), basename);
			self.updatePositions();
		});
	},

	updateAssignmentsDisplay: function() {

	},

	getElById: function(id) {
		return $('#' + this.baseId + '_' + id);
	},

	_onSnippetClick: function(info) {
		var txt = this.getElById('replybox_txt');
		var val = txt.val();
		if (val.length) {
			val += " ";
		}
		val += info.snippet;

		txt.val(val);
	},

	updateCount: function(num) {
		if (num === undefined || num === null) {
			num = this.selectionBar.getCount();
		}
		this.countEl.text(num);
	},

	getActionFormValues: function(appendArray, isApply, info) {
		appendArray = appendArray || [];

		if (!info) info = {};
		info.actionsCount = 0;

		if (this.wrapper.find('select.macro_id')[0] && this.wrapper.find('select.macro_id').val() != '0') {
			appendArray.push({
				name: 'run_macro_id',
				value: this.wrapper.find('select.macro_id').val()
			});
			info.actionsCount = 1;
			return appendArray;
		}

		$('input, select, textarea', this.wrapper).filter('[name^="actions["], [name^="actions_set["]').each(function() {

			var val = $(this).val(), name = $(this).attr('name');

			if (!val) {
				val = '';
			}

			if (val == '-1') {
				val = '';
			}

			if ($(this).is(':radio, :checkbox')) {
				if (!$(this).is(':checked')) {
					return;
				}
			}

			if (val === '') {
				return;
			}

			// Dont send reply type when we're just fetching previews
			if (!isApply && name == 'actions[reply]') {
				return;
			}

			appendArray.push({
				name: name,
				value: val
			});

			info.actionsCount++;
		});

		return appendArray;
	},

	/**
	 * Apply the changes
	 */
	apply: function() {
		var formData, rows = [];

		var formDataInfo = {
			checkedCount: 0,
			actionsCount: 0
		};

		formData = this.selectionBar.getCheckedFormValues('result_ids[]', null, formDataInfo);

		this.selectionBar.getChecked().each(function() {
			rows.push($(this).closest('.row-item').get(0));
		});

		this.getActionFormValues(formData, true, formDataInfo);

		// If we dont have any tickets or actions then theres nothing to do
		if (!formDataInfo.checkedCount || !formDataInfo.actionsCount) {
			return;
		}

		rows = $(rows);
		rows.addClass('loading');

		this.wrapper.addClass('loading');

		var statusUpdate = this.wrapper.find('input[name="actions[status]"]:checked').val();

		DeskPRO_Window.util.ajaxWithClientMessages({
			url: BASE_URL + 'agent/ticket-search/ajax-save-actions',
			type: 'POST',
			data: formData,
			dataType: 'json',
			context: this,
			complete: function() {
				rows.removeClass('loading');
			},
			success: function(data) {

				if (this.options.isListView) {
					this.close();
					this.page.meta.pageReloader();
					return;
				}

				$('.preview-edit', this.listWrapper).removeClass('preview-edit');
				$('.preview-edit-hide', this.listWrapper).remove();
				$('.row-item.changed, .prop-val.changed', this.listWrapper).removeClass('changed');

				this.wrapper.removeClass('loading');

				this.close();

				this.fireEvent('postApply', [this, data, formDataInfo]);

				if (data && data.failed_tickets && data.failed_tickets.length) {
					DeskPRO_Window.showAlert('Note: ' + data.failed_tickets.length + ' tickets were not updated because you do not have permission to make the requested changed.');
				}

				if (statusUpdate === 'hidden.deleted' || statusUpdate === 'hidden.spam') {
					// hide any open tickets
					$.each(data.success_tickets, function(k, ticketId) {
						var tab = DeskPRO_Window.getTabWatcher().findTab('ticket', function(tab) {
							return (tab && tab.page && tab.page && tab.page.meta.ticket_id == ticketId);
						});
						if (tab) {
							DeskPRO_Window.removePage(tab.page);
						}
					});
				}
			}
		});

	},


	/**
	 * Clear all pending previews
	 */
	clearPreview: function() {
		$('.preview-edit', this.listWrapper).remove();
		$('.preview-edit-hide', this.listWrapper).show().removeClass('preview-edit-hide');
	},


	/**
	 * Updates the listing with a preview of the changes we're making
	 */
	updatePreview: function(specific_id, force) {

		// No changes detected
		if (!this.hasAnyChange && !force) {
			return;
		}

		// No previews on list view
		if (this.options.isListView) {
			return;
		}

		if (this.runningAjax) {
			this.runningAjax.abort();
			this.runningAjax = null;
		}

		var formData, rows = [];
		var formDataInfo = {
			checkedCount: 0,
			actionsCount: 0
		};

		if (!specific_id) {
			formData = this.selectionBar.getCheckedFormValues('result_ids[]', null, formDataInfo);
			this.selectionBar.getChecked().each(function() {
				rows.push($(this).closest('.row-item').get(0));
			});
		} else {
			formData = [{ name: 'result_ids[]', value: specific_id }];
			formDataInfo.checkedCount = 1;
			rows = [$('.ticket-' + specific_id + '.row-item').get(0)];
		}

		this.getActionFormValues(formData, false, formDataInfo);

		// If we dont have any tickets or actions then theres nothing to do
		if (!force && (!formDataInfo.checkedCount || !formDataInfo.actionsCount)) {
			//return;
		}

		rows = $(rows);
		rows.addClass('loading');

		var runningAjax = $.ajax({
			url: BASE_URL + 'agent/ticket-search/get-page',
			type: 'POST',
			data: formData,
			dataType: 'html',
			context: this,
			complete: function() {
				rows.removeClass('loading');
				this.runningAjax = null;
			},
			success: function(html) {
				this.updatePreviewDisplay(html);
			}
		});

		// Only save running ajax if theres more than one
		if (!specific_id) {
			this.runningAjax = runningAjax;
		}
	},

	/**
	 * Update the preview display data with an HTML block returned from the server
	 *
	 * @param html
	 */
	updatePreviewDisplay: function(html) {
		var resultWrap = $(html);
		var listWrapper = this.listWrapper;

		$('.row-item', resultWrap).each(function() {
			var ticketId = $(this).data('ticket-id');
			var row = $('.ticket-' + ticketId + '.row-item', listWrapper);

			// Clear existing preview edits if there are any
			var existPrev = $('.preview-edit', row);
			existPrev.remove();

			var topRowRight = $('.top-row-right', this).addClass('preview-edit');
			var extraFields = $('.extra-fields', this).addClass('preview-edit');

			var origTopRowRight = $('.top-row-right', row).addClass('preview-edit-hide');
			var origExtraFields = $('.extra-fields', row).addClass('preview-edit-hide');

			topRowRight.insertAfter(origTopRowRight);
			extraFields.insertAfter(origExtraFields);

			origTopRowRight.hide();
			origExtraFields.hide();
		});
	},


	/**
	 * When a ticket has been checked or uncheck, need to update the preview status of that ticket.
	 */
	handleCheckChange: function(el, is_checked) {
		var row = $(el).closest('.row-item');

		if (is_checked) {
			this.updatePreview(row.data('ticket-id'));
		} else {
			$('.preview-edit', row).remove();
			$('.preview-edit-hide', row).show().removeClass('preview-edit-hide');
		}
	},


	/**
	 * Update the positions of the elements
	 */
	updatePositions: function() {

		//------------------------------
		// The wrapper overlaps the content pane section
		//------------------------------

		var pos = $('#dp_content').offset();
		var top = pos.top - 4;

		var bottom = 10;
		var height = '';

		var scrollContent = $('.scroll-content', this.wrapper).first();
		var contentH = false;
		var hasHeader = !!($('> section > header', this.wrapper).length);
		var hasFooter = !!($('> section > footer', this.wrapper).length);

		if (scrollContent.length) {
			contentH = scrollContent.height();
			if (hasHeader) {
				contentH += 36;
			}
			if (hasFooter) {
				contentH += 45;
			}

			contentH += 31;
		}

		if (hasHeader) $('> section > article', this.wrapper).removeClass('no-header');
		else $('> section > article', this.wrapper).addClass('no-header');

		if (hasFooter) $('> section > article', this.wrapper).removeClass('no-footer');
		else $('> section > article', this.wrapper).addClass('no-footer');

		if (contentH < 350) {
			contentH = 350;
		}

		var maxH = $(window).height() - top - 10;

		if (contentH && contentH < maxH) {
			bottom = '';
			height = contentH;
		}

		this.wrapper.css({
			top: pos.top - 4,
			left: pos.left + 8,
			right: 3,
			bottom: bottom,
			height: height
		});

		//------------------------------
		// The backdrops surround each side of the list pane
		//------------------------------

		var leftEnd = 269; // Where the left ends (aka where listpane starts)
		var topEnd = 50; // Where the top ends (aka header height)
		var contentStart = pos.left;

		if (!this.options.isListView) {
			this.backdropEls.eq(0).css({
				top: 0,
				width: leftEnd,
				bottom: 0,
				left: 0
			});

			this.backdropEls.eq(1).css({
				top: 0,
				height: topEnd,
				width: contentStart - leftEnd,
				left: leftEnd
			});

			this.backdropEls.eq(2).css({
				top: 0,
				right: 0,
				bottom: 0,
				left: contentStart
			});
		}

		this.updateUi();
	},


	/**
	 * Load a macro into the form
	 */
	loadMacro: function(macro_id) {

		var macroEl = $('.macro-options', this.wrapper);
		var inputActionsEl = $('.actions-input', this.wrapper);

		macro_id = parseInt(macro_id);
		if (!macro_id) {
			macroEl.hide();
			macroEl.find('ul.actions-list').empty();
			macroEl.find('input.macro_id').remove();
			inputActionsEl.show();
			this.updateUi();
			this.updatePositions();
			return;
		}

		var macroBtnEl = $('div.macro-load', this.wrapper).addClass('loading');

		$.ajax({
			url: BASE_URL + 'agent/ticket-search/ajax-get-macro-actions',
			data: { macro_id: macro_id },
			type: 'GET',
			dataType: 'json',
			context: this,
			success: function(data) {

				inputActionsEl.hide();
				macroEl.show();

				var input = $('<input type="hidden" class="macro_id" name="run_macro_id" />');
				input.val(macro_id);
				input.appendTo(macroEl);

				var ul = macroEl.find('ul.actions-list');
				ul.empty();

				Array.each(data.descriptions, function(desc) {
					var li = $('<li />');
					li.html(desc);

					ul.append(li);
				});

				macroBtnEl.removeClass('loading');

				self.hasAnyChange = true;
				this.updateUi();
				this.updatePositions();
			}
		});

		this.updatePositions();
	},


	/**
	 * Is the overlay currently open?
	 *
	 * @return {Boolean}
	 */
	isOpen: function() {
		if (!this._hasInit || !this.wrapper.is('.open')) {
			return false;
		}

		return true;
	},


	/**
	 * Open this overlay
	 */
	open: function() {
		if (!this.wrapper || !this.wrapper[0]) {
			this._resetWrapper();
		}

		this._initOverlay();

		//this.scrollerHandler = new DeskPRO.Agent.ScrollerHandler(this, $('> section > article', this.wrapper), {});
		this.updatePositions();
		DeskPRO_Window.layout.addEvent('resized', this.updatePositions, this);
		this.wrapper.addClass('open');
		this.backdropEls.show();

		this.updateCount(null);
		this.wrapper.addClass('open').show();
		//this.updatePreview();

		this.updatePositions();
	},


	/**
	 * Close the overlay
	 */
	close: function() {
		if (!this.isOpen()) {
			return false;
		}

		DeskPRO_Window.layout.removeEvent('resized', this.updatePositions, this);
		this.wrapper.removeClass('open');
		this.backdropEls.hide();
		this.fireEvent('closed', [this]);

		this.clearPreview();

		if (this.options.resetOnClose) {
			this.reset();
		}
	},


	destroy: function() {
		if (this._hasInit) {
			this.wrapper.remove();
			this.backdropEls.remove();
		}

		if (this.wrapperContainer) {
			this.wrapperContainer.remove();
		}

		if (this.textarea && this.textarea.data('redactor')) {
			this.textarea.redactor('destroy');
		}
	}
});
