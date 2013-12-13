Orb.createNamespace('DeskPRO.Agent.PageHelper');

DeskPRO.Agent.PageHelper.DisplayOptions = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(page, options)  {

		var self = this;

		this.page = page;

		this.options = {
			triggerElement: null,
			resultId: 0,
			prefSaveResultId: null,
			prefId: '',
			refreshUrl: ''
		};
		this.setOptions(options);

		if (this.options.prefSaveResultId === null) {
			this.options.prefSaveResultId = this.options.resultId;
		}

		if (!this.options.triggerElement) {
			this.options.triggerElement = $('.display-options-trigger', this.page.wrapper);
		}

		$(this.options.triggerElement).on('click', (function(ev) {
			ev.stopPropagation();
			ev.preventDefault();
			this.open();
		}).bind(this));

		// Automatically set up the quick sort menu button
		var menuBtn = $('button.order-by-trigger', this.page.wrapper);
		var menuEl  = $('ul.order-by-menu', this.page.wrapper);
		if (menuBtn.length && menuEl.length) {
			this.orderByMenu = new DeskPRO.UI.Menu({
				triggerElement: menuBtn,
				menuElement: menuEl,
				onItemClicked: (function(info) {
					var item = $(info.itemEl);

					var prop = item.data('field')
					var label = item.text().trim();

					$('.label', menuBtn).text(label);

					var disOptWrap = self.getWrapperElement();
					var sel = $('select.sel-order-by', disOptWrap);
					$('option', sel).prop('selected', false);
					$('option.' + prop.replace('.', '_'), sel).prop('selected', true);

					self.saveAndRefresh();

				}).bind(this)
			});
		}

		this.page.addEvent('destroy', (function() {
			this.destroy();
		}).bind(this));
	},

	_initOverlay: function() {

		if (this._hasInit) return;
		this._hasInit = true;

		var ul = $('ul.display-fields-list.on-list', this.wrapper);

		var makeBogus = function(ul) {
			// Use bogus invisible draggables so when dragging to end of the list, the dragging
			// item is placed between one of these invisible ones. The event handlers
			// make sure they're always at the end.
			// - This is to fix making it too hard to position something at the end.
			var exist = ul.find('> li.bogus').length;
			for (var i = exist; i < 8; i++) {
				var li = $('<li class="bogus">&nbsp;</li>');
				li.css({
					width: 30,
					visibility: 'hidden'
				});

				ul.append(li);
			}
		};

		this.wrapper = $('.display-options', this.page.wrapper).first();
		this.optionsList = $('ul.display-fields-list.on-list', this.wrapper).sortable({
			forceHelperSize:true,
			opacity: 0.6,
			update: function() {
				ul.find('> li.bogus').remove();
				makeBogus(ul);
			}
		});

		var onList = this.optionsList;
		var offList = $('ul.display-fields-list.off-list', this.wrapper);

		onList.find(':checkbox').on('click', function() {
			var check = $(this);
			var li = $(this).closest('li');

			if (check.attr('checked')) {
				li.detach().removeClass('off').appendTo(onList);
				if (!offList.find('> li').length) {
					offList.hide();
				}
			} else {
				li.detach().addClass('off').prependTo(offList);
				offList.show();
			}

			ul.find('> li.bogus').remove();
			makeBogus(ul);
		}).not(':checked').each(function() {
			$(this).closest('li').detach().addClass('off').appendTo(offList);
		});
		if (!offList.find('> li').length) {
			offList.hide();
		}

		this.wrapper.detach().appendTo('body');
		this.wrapper.css('z-index', '10101');

		this.wrapper.on('click', function(ev) {
			ev.stopPropagation();
		});

		this.backdropEl = $('<div class="backdrop dp-overlay-backdrop" />');
		this.backdropEl.css('z-index', '10100').hide().appendTo('body');

		this.backdropEl.on('click', (function(ev) {
			ev.stopPropagation();
			this.close();
		}).bind(this));

		$('header .close-trigger', this.wrapper).on('click', (function(ev) {
			ev.stopPropagation();
			ev.preventDefault();
			this.close();
		}).bind(this));

		$('.save-trigger', this.wrapper).on('click', (function() {
			this.saveDisplayOptions();
		}).bind(this));
	},

	getDisplayFields: function() {
		var fields = [];

		$(':checkbox:checked', this.getWrapperElement()).each(function() {
			fields.push($(this).attr('name'));
		});

		return fields;
	},

	saveDisplayOptions: function() {
		this.wrapper.addClass('loading');
		this.saveAndRefresh();
	},

	saveAndRefresh: function() {

		var self = this;
		var wrap = this.getWrapperElement();

		var data = [];
		var pref_name = 'prefs[agent.ui.'+ this.options.prefId + '-display-fields.' + this.options.prefSaveResultId +'][]';

		var has = false;

		$('input[type="checkbox"]:checked', wrap).each(function() {
			has = true;
			data.push({
				name: pref_name,
				value: $(this).attr('name')
			});
		});

		if (!has) {
			data.push({
				name: pref_name,
				value: 'NONE'
			});
		}

		// and the ordering
		data.push({
			name: 'prefs[agent.ui.'+ this.options.prefId + '-order-by.' + this.options.prefSaveResultId +']',
			value: $('select[name="order_by"]', wrap).val()
		});

		// We reload the same page which will have changes applied
		var url = this.options.refreshUrl;

		if (this.options.isListView) {
			var page = this.page;
			$.ajax({
				timeout: 20000,
				type: 'POST',
				url: BASE_URL + 'agent/misc/ajax-save-prefs',
				data: data,
				context: this,
				complete: function() {
					this.close();
				},
				success: function() {
					page.meta.pageReloader();
				}
			});
		} else {
			$.ajax({
				timeout: 20000,
				type: 'POST',
				url: BASE_URL + 'agent/misc/ajax-save-prefs',
				data: data,
				context: this,
				complete: function() {
					this.close();
				},
				success: function() {
					DeskPRO_Window.loadListPane(url);
				}
			});
		}
	},

	open: function() {
		this._initOverlay();

		this.updatePositions();

		this.wrapper.addClass('open');
		this.backdropEl.show();

		this.wrapper.addClass('open');

		this.fireEvent('opened', [this]);
	},

	isOpen: function() {
		if (!this._hasInit || !this.wrapper.is('.open')) {
			return false;
		}

		return true;
	},

	close: function() {
		if (!this._hasInit || !this.isOpen()) return;

		this.wrapper.removeClass('open');
		this.backdropEl.hide();
		this.fireEvent('closed', [this]);
	},

	/**
	 * Update the positions of the elements
	 */
	updatePositions: function() {

		var elW = this.wrapper.width();
		var elH = this.wrapper.height();

		var pageW = $(window).width();
		var pageH = $(window).height();

		this.wrapper.css({
			top: 55,
			left: (pageW-elW) / 2
		});
	},

	getWrapperElement: function() {
		if (this._hasInit) {
			return this.wrapper;
		} else {
			return $('.display-options:first', this.page.wrapper);
		}
	},

	destroy: function() {
		if (this._hasInit) {
			this.wrapper.remove();
			this.backdropEl.remove();
		}

		delete this.wrapper;
		delete this.backdropEl;
		delete this.options;
		delete this.page;
	}
});
