Orb.createNamespace('DeskPRO.Agent.SourcePane');

/**
 * A SearchForm represents an entire source pane search form:
 * - Many inputs
 * - Many panels
 *
 * @type {Orb.Class}
 */
DeskPRO.Agent.SourcePane.SearchForm = new Orb.Class({
	Implements: [Orb.Util.Events],

	initialize: function(el) {
		var self = this;
		this.el = el;
		this.origHtml = el.html();
		this.hasInit = false;

		this.formPanels = [];
		this.widgets = [];

		this.el.addClass('dp-with-activate-listener');
		this.el.on('dp_activated', function() {
			self.initPanel();
		});
	},

	reset: function() {
		this.el.html(this.origHtml);
		Array.each(this.formPanels, function(formPanel) {
			formPanel.destroy();
		});
		this.formPanels = [];
		this.widgets = [];
		this.hasInit = false;
		this.initPanel();
	},

	getFormData: function() {
		var postData = [];
		var visitedEls = [];

		postData = Orb.serializeFormElements(this.el.find('.add-to-search'), visitedEls);
		Array.each(this.formPanels, function(panel) {
			postData = postData.append(Orb.serializeFormElements(panel.el.find('.add-to-search'), visitedEls));
		});

		return postData;
	},

	initPanel: function() {
		if (this.hasInit) return;
		this.hasInit = true;

		var self = this;

		this.el.find('.trigger-open-panel').each(function() {
			if ($(this).hasClass('has-init-trigger-open-panel')) {
				return;
			}
			$(this).addClass('has-init-trigger-open-panel');
			var panelTrigger = $(this);
			var panelEl      = self.el.find('.' + panelTrigger.data('panel-id')).first();
			var panelSummary = self.el.find(panelTrigger.data('target-summary')).first();
			var panel        = new DeskPRO.Agent.SourcePane.SearchFormPanel(panelEl);

			panel.targetSummaryEl = panelSummary;

			panelTrigger.on('click', function(ev) {
				Orb.cancelEvent(ev);
				panel.open(this);
			});

			self.formPanels.push(panel);
		});

		var submitBtn = this.el.find('.trigger-submit-search').on('click', function(ev) {
			Orb.cancelEvent(ev);
			var postData = self.getFormData();

			DeskPRO_Window.loadListPane(
				$(this).data('search-url'),
				{postData: postData}
			);
		});
		submitBtn = submitBtn.first();

		this.el.find('.reset-form-trigger').on('click', function(ev) {
			Orb.cancelEvent(ev);
			self.reset();
		});

		this.el.find('input[type="text"]').on('keypress', function(ev) {
			if (ev.keyCode == 13 /* enter key */) {
				submitBtn.click();
			}
		});

		this.initStandardFormElements(this.el);
	},


	/**
	 * Inits standard form elements and controls
	 *
	 * @param context
	 */
	initStandardFormElements: function(context) {
		var self = this;

		context.find('.trigger-clone-row').each(function(ev) {
			var btn    = $(this);
			var target = $(btn.data('target'));
			btn.data('tpl', target.html());
		});

		context.find('.trigger-clone-row').on('click', function(ev) {
			Orb.cancelEvent(ev);

			var btn        = $(this);
			var target     = $(btn.data('target'));
			var targetList = $(btn.data('target-list'));
			var clone      = $('<div/>').html(btn.data('tpl')).addClass('pane-row add-to-search');

			clone.removeClass('row-orig');

			self.initStandardFormElements(clone);
			clone.insertAfter(targetList);
		});

		context.find('.trigger-remove-row').on('click', function(ev) {
			Orb.cancelEvent(ev);
			$(this).closest('.pane-row').remove();
		});

		context.find('.dp-select-widget-simple').each(function() {
			var widget = new DeskPRO.UI.Select.WidgetSimple($(this));
			self.widgets.push(widget);
		});

		context.find('.date-term-wrap').each(function() {
			var el = $(this);
			var status = el.find('.status-value-outer').hide();;
			var dateTerm = new DeskPRO.Agent.RuleBuilder.DateTerm({
				ruleBuilder: null,
				rowEl: el,
				rowId: null,
				opMenu: null
			});
			dateTerm.initRow();

			el.find('select.op').on('change', function() {
				if ($(this).val() != "0") {
					dateTerm.updateStatus();
					status.show();
				} else {
					status.hide();
				}
			});

			if (el.data('base-name')) {
				var baseName = el.data('base-name');
				el.find('input').each(function() {
					$(this).attr('name', baseName + '[' + $(this).attr('name') + '][]');
				});
			}
		});
	},


	/**
	 * Destroys els that were detached
	 */
	destroy: function() {
		Array.each(this.formPanels, function(formPanel) {
			formPanel.destroy();
		});
		this.formPanels = [];
	}
});


/**
 * A "panel" is a form that opens to the side of the source pane.
 *
 * @type {Orb.Class}
 */
DeskPRO.Agent.SourcePane.SearchFormPanel = new Orb.Class({
	Implements: [Orb.Util.Events],

	initialize: function(el) {
		this.el         = el;
		this._isOpen    = false;
		this.hasInit    = false;
		this.shim       = null;
		this.updateTypesTimer = null;
		this.searchBuilderLists = [];
		this.searchValList = [];
		this.searchStringList = [];
		this.targetSummaryEl = null;
	},

	initPanel: function() {
		if (this.hasInit) return;
		this.hasInit = true;

		this.el.find('.with-select2').each(function() {
			DP.select($(this));
		});

		var self = this;

		// For absolute positioning over things
		this.el.detach().appendTo('body');

		this.el.find('.with-search-builder').each(function() {
			if ($(this).hasClass('has-init-search-builder')) {
				return;
			}
			$(this).addClass('has-init-search-builder');

			var critTpl = $(this).find('.criteria_tpl');
			var critList = $(this).find('.criteria_list');

			self.searchBuilderLists.push(critList.get(0));

			var editor = new DeskPRO.Form.RuleBuilder(critTpl);
			editor.addEvent('newRow', function(new_row) {
				$('.trigger-remove-row', new_row).on('click', function() {
					new_row.remove();
				});
			});
			$('.add-term', critList).on('click', function() {
				var basename = 'terms['+Orb.uuid()+']';
				editor.addNewRow($('.search-terms', critList), basename);
			});
		});

		this.searchValList = self.el.find('.ensure-value');

		if (this.el.data('target-summary')) {
			this.targetSummaryEl = $(this.el.data('target-summary'));
		}

		if (this.targetSummaryEl) {
			this.el.find('.search-string').on('keyup keydown change', function() {
				self.targetSummaryEl.text(($(this).val()));
			});

			if (this.targetSummaryEl.is('input.is-bound')) {
				this.targetSummaryEl.on('focus', function(ev) {
					if (self.el.find('.pane-row').length > 1) {
						$(this).blur();
						self.open();
					}
				});

				this.targetSummaryEl.on('keypress', function(ev) {
					if (self.el.find('.pane-row').length > 1) {
						$(this).val('');
						$(this).blur();
						self.open();
					} else {
						self.el.find('input[type="text"]').val('');
					}
				});
			}
		}

		this.shim = $('<div class="dp-shim"></div>');
		this.shim.appendTo('body');

		this.shim.on('click', function(ev) {
			Orb.cancelEvent(ev);
			self.close();
		});
	},

	updateTypes: function() {
		var self = this;
		var texts = [];

		if (this.searchBuilderLists.length) {
			Array.each(this.searchBuilderLists, function(o_el) {
				$.trim($(o_el).find('.builder-type-choice').each(function() {
					var el = $(this);
					var type = $.trim($(el).find('select').find('option').filter(':selected').text());
					if (type) {
						texts.push(type);
					}
				}));
			});
		}

		if (this.searchValList.length) {
			this.searchValList.each(function() {
				var val = $(this).val() || "";
				if (val.length != 0) {
					texts.push($(this).closest('.pane-row').find('.row-label').text());
				}
			});
		}

		var searchStringList = self.el.find('.search-string');
		if (searchStringList.length) {
			searchStringList.each(function() {
				var val = $(this).val() || "";
				if (val.length) {
					texts.push(val);
				}
			});
		}

		if (self.targetSummaryEl.is('input')) {
			self.targetSummaryEl.val(texts.join(', '));
		} else {
			self.targetSummaryEl.text(texts.join(', '));
		}
	},


	/**
	 * @returns {jQuery}
	 */
	getEl: function() {
		return this.el;
	},


	/**
	 * Is the side panel open?
	 *
	 * @returns {Boolean}
	 */
	isOpen: function() {
		return this._isOpen;
	},


	/**
	 * Open the side panel. If nearEl is specified, then we will try to open the side panel "near" this element.
	 *
	 * @param {HTMLElement} nearEl The element to open the panel near
	 */
	open: function(nearEl) {

		var self = this;

		if (!this.updateTypesTimer) {
			this.updateTypesTimer = window.setInterval(function() {
				self.updateTypes();
			}, 300);
		}

		if (this._isOpen) return;
		this._isOpen = true;

		// Actual panel events are lazy inited on first open
		this.initPanel();

		this.el.show();
		this.shim.show();

		var left = 269;
		var top  = 200;

		if (nearEl) {
			top = $(nearEl).offset().top - 10;
		}

		var winH = $(window).height();
		var maxH = winH - top - 80;

		if (maxH < 250) {
			if (winH > 250) {
				top -= (250 - maxH);
			} else {
				top = 60;
			}

			maxH = winH - top - 80;
		}

		if (winH / 1.5 > 500) {
			maxH = parseInt(Math.min(maxH, winH / 1.5));
		}

		this.el.css({
			left: left,
			top: top,
			'max-height': maxH
		});
	},


	/**
	 * CLose the panel if its open
	 */
	close: function() {
		if (!this._isOpen) return;
		this._isOpen = false;

		if (this.updateTypesTimer) {
			window.clearTimeout(this.updateTypesTimer);
			this.updateTypesTimer = null;
		}

		this.el.hide();
		this.shim.hide();
	},


	/**
	 * Destroy the panel by removing any additionally attached elements
	 */
	destroy: function() {
		this.close();

		if (this.hasInit) {
			this.el.detach();
			this.shim.detach();

			Array.each(this.widgets, function(w) {
				w.destroy();
			});
		}
	}
});