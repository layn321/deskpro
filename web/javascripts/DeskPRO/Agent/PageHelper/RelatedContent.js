Orb.createNamespace('DeskPRO.Agent.PageHelper');

/**
 * This handles pages that accept related content linking
 */
DeskPRO.Agent.PageHelper.RelatedContent = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(page, options) {
		var self = this;
		this.page = page;

		this.options = {

			/**
			 * These are the ID for *this* content,
			 * used to make sure we dont link ourselves
			 */
			typename: '',
			content_id: 0,

			/**
			 * The main wrapper element content links are displayed
			 */
			listEl: null,

			/**
			 * The template to use when we need to render a new related content item
			 */
			newItemTpl: '#related_content_newitem',

			disabled: false
		};

		this.setOptions(options);

		this.listEl = $(this.options.listEl);

		this.listEl.on('click', '.remove', function(ev) {
			ev.stopPropagation();
			ev.preventDefault();

			var li = $(this);
			var x = 0;
			while (!li.is('.related-content')) {
				if (x++ > 15) return;
				li = li.parent();
			}

			self.removeLinkByElement(li);
		});

		this.page.getEl('count_related').text(this.listEl.find('li.related-content').length);
	},

	isViewing: function() {
		var tab = this.page.bodyTabs.lastActiveTabContent;
		if (tab && tab.is('.related-content')) {
			return true;
		}

		return false;
	},

	_refreshInstructionEl: function() {
		if ($('.related-content:not(.removing):first', this.listEl).length) {
			// There is related content.
			// Just make sure the note is hidden. The sections
			// auto show themselves
			$('.no-related-content', this.listEl).hide();
		} else {
			// There is no related content to list
			// Hide sections, and show note
			$('.related-section', this.listEl).hide();
			$('.no-related-content', this.listEl).show();
		}

		this.page.getEl('count_related').text(this.listEl.find('li.related-content:not(.removing)').length);
	},


	/**
	 * Set the active related list controller
	 *
	 * @param controller
	 */
	setActiveRelatedListController: function(controller) {
		this.relatedContentList = controller;
	},


	/**
	 * Add a new linked content
	 *
	 * @param typename
	 * @param content_id
	 */
	addLink: function(typename, content_id, title, route) {
		if (this.isLinked(typename, content_id)) {
			return;
		}

		var li = $(DeskPRO_Window.util.getPlainTpl(this.options.newItemTpl));
		$('.link-title', li).text(title);
		$('.link-route', li).data('route', route);
		li.addClass('related-content').addClass(typename + '-' + content_id);
		li.data('content-type', typename);
		li.data('content-id', content_id);

		li.hide();

		var wrapper = $('.'+ typename +'.related-section', this.listEl);
		if (wrapper.length) {
			var list = $('.related-list:first', wrapper);
			list.append(li);
			if (!wrapper.is(':visible')) {
				li.show();
				wrapper.slideDown('fast');
			} else {
				li.slideDown('fast');
			}
		} else {
			if (!this.listEl.is(':visible')) {
				li.show();
				this.listEl.append(li).slideDown('fast');
			} else {
				this.listEl.append(li);
				li.slideDown('fast');
			}
		}

		if (this.relatedContentList) {
			this.relatedContentList.elementIsLinked(typename, content_id);
		}

		this._refreshInstructionEl();

		this.fireEvent('contentLinked', [typename, content_id, title, route, this]);
	},


	/**
	 * Same as addLink but with an element reference
	 *
	 * @param el
	 */
	addLinkByElement: function(el) {
		var typename = el.data('content-type');
		var content_id = el.data('content-id');

		if (el.data('route')) {
			var route = el.data('route');
			var title = el.text().trim();
		} else {
			var routeEl = $('[data-route]:first', el);
			var route = routeEl.data('route');
			var title = routeEl.text().trim();
		}

		if (el.data('route-title')) {
			title = el.data('route-title');
			if (title == '@text') {
				title = el.text().trim().replace(/[\n\r]/g, ' ').replace(/\s+/g, ' ');
			} else if (title == '@title') {
				title = el.attr('title');
			} else if (title.test(/^@selector\((.*?)\)$/)) {
				var sel = title.match(/^@selector\((.*?)\)$/)[1];
				var titleEl = null;
				if (sel[0] == "#") {
					titleEl = $(sel);
				} else {
					titleEl = $(sel, el);
				}

				if (titleEl && titleEl.length) {
					title = titleEl.text().trim().replace(/[\n\r]/g, ' ').replace(/\s+/g, ' ');
				} else {
					title = el.text().trim();
				}
			}
		}

		return this.addLink(typename, content_id, title, route);
	},


	/**
	 * Remove a linked content that is described in an element
	 *
	 * @param el
	 */
	removeLinkByElement: function(el) {
		var typename = el.data('content-type');
		var content_id = el.data('content-id');

		if (!typename || !content_id) {
			DP.console.error("No content linked on element: %o", el);
			return false;
		}

		this.removeLink(typename, content_id);

		return true;
	},


	/**
	 * Remove a link based on a typename and content_id.
	 *
	 * @param typename
	 * @param content_id
	 */
	removeLink: function(typename, content_id) {

		var listItem = this.getLinkElementInList(typename, content_id);
		if (!listItem) {
			return;
		}

		listItem.addClass('removing').slideUp('fast', function() {
			listItem.remove();

			var wrapper = $('.'+ typename +'.related-section', this.listEl);
			if (wrapper.length) {
				if (!$('.related-content:first', wrapper).length) {
					wrapper.slideUp('fast');
				}
			}
		});

		if (this.relatedContentList) {
			this.relatedContentList.elementIsUnlinked(typename, content_id);
		}

		this._refreshInstructionEl();

		this.fireEvent('contentUnlinked', [typename, content_id, this]);
	},


	/**
	 * Get the element description for a linked thing
	 *
	 * @param typename
	 * @param content_id
	 */
	getLinkElementInList: function(typename, content_id) {
		var el = $('.' + typename + '-' + content_id + '.related-content:first', this.listEl);

		if (!el.length) {
			return null;
		}

		return el;
	},


	/**
	 * Check if a certain content is already linked
	 *
	 * @param typename
	 * @param content_id
	 */
	isLinked: function(typename, content_id) {
		return $('.' + typename + '-' + content_id + '.related-content:first', this.listEl).length;
	},


	/**
	 *
	 * @param typename
	 * @param content_id
	 */
	isLinkable: function(typename, content_id) {
		if (this.options.disabled) {
			return false;
		}
		if (typename == this.options.typename && content_id == this.options.content_id) {
			return false;
		}

		var ev = {linkable: true};
		this.fireEvent('checkLinkable', [ev, typename, content_id, this]);

		if (ev.linkable) {
			return true;
		} else {
			return false;
		}
	},


	/**
	 * Same as isLinkable but with an element reference
	 *
	 * @param el
	 */
	elementIsLinkable: function(el) {
		if (this.options.disabled) {
			return false;
		}
		return this.isLinkable(el.data('content-type'), el.data('content-id'));
	},


	/**
	 * Same as isLinked but with an element reference.
	 *
	 * @param el
	 */
	elementIsLinked: function(el) {
		return this.isLinked(el.data('content-type'), el.data('content-id'));
	}
});
