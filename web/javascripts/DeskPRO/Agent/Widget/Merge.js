Orb.createNamespace('DeskPRO.Agent.Widget');

DeskPRO.Agent.Widget.Merge = new Orb.Class({
	Implements: [Orb.Util.Options, Orb.Util.Events],

	initialize: function(options) {
		this.options = {
			tabType: null,
			metaId: 0,
			metaIdName: null,
			menu: null,
			trigger: null,
			overlayUrl: null,
			mergeUrl: null,
			overlayLoaded: null
		};
		this.setOptions(options);

		this.mergeMenu = null;
		if (this.options.menu) {
			this.mergeMenu = new DeskPRO.UI.Menu({
				triggerElement: this.options.trigger,
				menuElement: this.options.menu,
				onBeforeMenuOpened: this._menuPopulate.bind(this),
				onItemClicked: this._menuItemClick.bind(this)
			});
		}
	},

	_getOverlayUrl: function(id, otherId) {
		return this.options.overlayUrl.replace('{id}', id).replace('{other}', otherId);
	},

	_getMergeUrl: function(id, otherId) {
		return this.options.mergeUrl.replace('{id}', id).replace('{other}', otherId);
	},

	_menuPopulate: function() {
		var menu = this.options.menu, self = this;

		menu.find('.tab-reference').remove();

		var tabInsert = menu.find('.tab-insert'), insertPosition = tabInsert;
		if (!insertPosition.length) {
			insertPosition = false;
		}

		Array.each(DeskPRO_Window.getTabWatcher().findTabType(this.options.tabType), function(tab) {
			var id = tab.page.getMetaData(self.options.metaIdName);
			if (id && id != self.options.metaId) {
				var li = $('<li />').addClass('tab-reference').data('merge-id', id).text(tab.title);

				if (insertPosition) {
					insertPosition.after(li);
				} else {
					menu.prepend(li);
				}
				insertPosition = li;
			}
		});

		if (menu.find('li.tab-reference').length == 0) {
			if (!tabInsert.hasClass('always-show')) { tabInsert.hide(); }
			menu.find('li.no-choice').show();
		} else {
			tabInsert.show();
			menu.find('li.no-choice').hide();
		}
	},

	_menuItemClick: function(info) {
		var el = $(info.itemEl);

		if (el.hasClass('elm')) {
			return false;
		}

		var otherId = el.data('merge-id');
		if (!otherId) {
			otherId = 0;
		}

		if (this.overlay) {
			this.overlay.destroy();
		}

		this.overlay = new DeskPRO.UI.Overlay({
			contentMethod: 'ajax',
			contentAjax: { url: this._getOverlayUrl(this.options.metaId, otherId) },
			zIndex: 40000 // Above floating people windows
		});
		this.overlay.addEvent('ajaxDone', this._overlayLoaded.bind(this));
		this.overlay.open();
	},

	openWithId: function(otherId) {
		if (this.overlay) {
			this.overlay.destroy();
		}

		this.overlay = new DeskPRO.UI.Overlay({
			contentMethod: 'ajax',
			contentAjax: { url: this._getOverlayUrl(this.options.metaId, otherId) },
			zIndex: 40000 // Above floating people windows
		});
		this.overlay.addEvent('ajaxDone', this._overlayLoaded.bind(this));
		this.overlay.open();
	},

	open: function() {
		if (this.overlay) {
			this.overlay.destroy();
		}

		this.overlay = new DeskPRO.UI.Overlay({
			contentMethod: 'ajax',
			contentAjax: { url: this._getOverlayUrl(this.options.metaId, 0) },
			zIndex: 40000 // Above floating people windows
		});
		this.overlay.addEvent('ajaxDone', this._overlayLoaded.bind(this));
		this.overlay.open();
	},

	resetOverlay: function(html) {
		this.overlay.setContent($(html));
		this._overlayLoaded();
	},

	_overlayLoaded: function() {
		var overlay = this.overlay, wrapper = overlay.getWrapper(), self = this;

		DeskPRO.ElementHandler_Exec(wrapper);

		if (this.options.overlayLoaded) {
			this.options.overlayLoaded(this.overlay, this);
		}

		var buttons = wrapper.find('.merge-data .merge-target-button');

		if (buttons.length != 2) {
			return;
		}

		this.mergeButtons = buttons;

		var keepHtml = '', mergeHtml = '';

		buttons.each(function() {
			var $this = $(this);
			if ($this.data('keep')) {
				keepHtml = $this.html();
			} else {
				mergeHtml = $this.html();
			}
		});

		var rows = wrapper.find('.merge-data .merge-data-rows tr:not(.mergeable)');

		var getCmpVal = function(el) {
			var val = $.trim(el.text());
			val = val.toLowerCase();
			val = val.replace(/\s/g, val);
			return val;
		};
		var setMergeDataLostClasses = function() {
			var keepCol = 0, mergeCol = 0;

			if (wrapper.find('.left-text').data('keep')) {
				keepCol = 0;
				mergeCol = 1;
			} else {
				mergeCol = 0;
				keepCol = 1;
			}

			rows.each(function() {
				var $row = $(this);
				var tds = $row.find('td'),
					keep = tds.eq(keepCol),
					merge = tds.eq(mergeCol);

				keep.removeClass('merge-data-lost merge-data-keep');
				merge.removeClass('merge-data-lost merge-data-keep');

				if ($row.hasClass('always-keep')) {
					// always lose the merge data
					merge.addClass('merge-data-lost');
					keep.addClass('merge-data-keep');
				} else {
					if (getCmpVal(keep) != getCmpVal(merge)) {
						merge.addClass('merge-data-lost');
						keep.addClass('merge-data-keep');
					}
				}
			});
		};

		setMergeDataLostClasses();

		wrapper.find('.switch-trigger').on('click', function(ev) {
			ev.preventDefault();
			buttons.each(function() {
				if (!$(this).data('keep')) {
					$(this).click();
					return false;
				}
			});
		});

		buttons.click(function() {
			buttons.data('keep', false).html(mergeHtml);
			$(this).data('keep', 1).html(keepHtml);
			setMergeDataLostClasses();
		});

		wrapper.find('.merge-trigger').click(this._mergeTriggerClick.bind(this));
	},

	_mergeTriggerClick: function() {
		var mergeId = 0, otherMergeId = 0, self = this;

		this.mergeButtons.each(function() {
			var $this = $(this);
			if ($this.data('keep')) {
				mergeId = $this.data('merge-id');
			} else {
				otherMergeId = $this.data('merge-id');
			}
		});

		if (!mergeId || !otherMergeId) {
			return;
		}

		var footerEl = this.overlay.getWrapper().find('.overlay-footer').addClass('loading');

		$.ajax({
			url: this._getMergeUrl(mergeId, otherMergeId),
			type: 'POST',
			dataType: 'json',
			complete: function() {
				footerEl.removeClass('loading');
			},
			success: function(data) {
				if (data.success) {
					// remove old tabs, theyre outdated
					Array.each(DeskPRO_Window.getTabWatcher().findTabType(self.options.tabType), function(tab) {
						var id = tab.page.getMetaData(self.options.metaIdName);
						if (id == data.old_id || id == data.id) {
							DeskPRO_Window.TabBar.removeTabById(tab.id);
						}
					});

					DeskPRO_Window.runPageRoute(self.options.loadRoute.replace('{id}', data.id));
				}
				self.overlay.close();
			},
			error: function(xhr, textStatus, errorThrown) {
				self.overlay.close();

				var status = (xhr.status || '') + ' ' + (errorThrown || '') + ' ' + (xhr.statusText || '');
				DeskPRO_Window._showAjaxError('<div class="error-details">Here is the raw output returned from the server error:<textarea class="raw">' + status + "\n\n" + Orb.escapeHtml(xhr.responseText) + '</textarea></div>');
			}
		});
	},

	destroy: function() {
		if (this.overlay) {
			this.overlay.destroy();
		}
		if (this.mergeMenu) {
			this.mergeMenu.destroy();
		}
	}
});
