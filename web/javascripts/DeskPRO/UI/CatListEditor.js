Orb.createNamespace('DeskPRO.Agent.UI');

DeskPRO.UI.CatListEditor = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(options) {
		this.options = {
			/**
			 * The main outer list container
			 * @param {jQuery|String}
			 */
			listEl: null,

			/**
			 * Items in a list
			 * @param {String}
			 */
			itemSelector: 'li',

			/**
			 * Selectors that identify sub-groups within an item
			 * @param {String}
			 */
			subListSelector: '> ul',

			/**
			 * Selector that identifies the title within the li
			 * @param {String}
			 */
			titleSelector: '.title-edit:first',

			/**
			 * The data attribute that holds the category ID
			 * @param {String}
			 */
			dataId: 'category-id',

			/**
			 * Selector for new item template
			 * {String}
			 */
			newItemTplSelector: null,

			/**
			 * The basename for the editor elements
			 */
			editorBaseId: '',

			/**
			 * The data-route to add to newly created cats
			 */
			newCatRoute: ''
		};

		this.setOptions(options);

		var self = this;
		var list = $(this.options.listEl);
		this.list = list;

		var lis = $(this.options.itemSelector, this.list);
		this._initLisCollection(lis);

		list.on('click', '.sub-toggle', function() {
			$(this).parent().parent().toggleClass('sub-expanded');
		});

		this.isEditMode = false;

		window.setTimeout(function() {
			self.pristineStructure = self.getStructure();
		}, 300);
	},

	_initLisCollection: function(lis) {

		var self = this;
		var list = this.list;

		lis.addClass('dp-cat-li')
			.wrapInner('<div class="item-wrap dp-cat-item" />')
			.prepend('<div class="dp-cat-dropzone between" />');

		// Move existing subgroups back out of dp-cat-item
		$('.dp-cat-item ' + this.options.subListSelector, lis).each(function() {
			var li = $(this).parent().parent();
			$(this).detach().appendTo(li);
		});

		$('.dp-cat-item, .dp-cat-dropzone', lis).droppable({
			accept: 'li.dp-cat-li',
			tolerance: 'pointer',
			drop: function(e, ui) {
				var movedTree = false;
				var li = $(this).parent();
				var child = !$(this).is('.dp-cat-dropzone');
				if (child && li.children('ul').length == 0) {
					li.append('<ul/>');
				}
				if (child) {
					li.addClass('has-children').children('ul').append(ui.draggable);
				} else {
					li.before(ui.draggable);
				}

				movedTree = true;

				$('li.dp-li-open').not(':has(li:not(.ui-draggable-dragging))').removeClass('sub-expanded');
				li.find('.dp-cat-item, .dp-cat-dropzone').removeClass('dp-cat-over');

				$('li.has-children', list).each(function() {
					var p = $(this);
					if (!$('> ul > li:not(.ui-draggable-dragging):first', p).length) {
						p.removeClass('has-children');
					}
				});

				if (movedTree) {
					self.fireEvent('restructured', [ui.draggable, this]);
				} else {
					self.fireEvent('reordered', [ui.draggable, this]);
				}
			},
			over: function() {
				$(this).addClass('dp-cat-over');
				if ($(this).is('.dp-cat-dropzone')) {
					$('.dp-cat-item:first', $(this).parent()).removeClass('dp-cat-over');
				}
			},
			out: function() {
				$(this).filter('.dp-cat-item, .dp-cat-dropzone').removeClass('dp-cat-over');
			}
		});

		lis.draggable({
			handle: '> .dp-cat-item',
			opacity: 0.5,
			addClasses: false,
			helper: 'clone',
			zIndex: 100
		});

		if (!this.editMode) {
			lis.draggable('disable');
		}
	},

	enableEditMode: function() {
		this.editMode = true;
		this.list.find('li.dp-cat-li').draggable('enable');
	},

	disableEditMode: function() {
		this.editMode = false;
		this.list.find('li.dp-cat-li').draggable('disable');
	},

	/**
	 * Get an array with the order of each item
	 *
	 * @return {Array}
	 */
	getOrder: function() {
		var itemDataId = this.options.dataId;
		var orders = [];
		$('li.dp-cat-li', this.list).each(function() {
			var id = $(this).data(itemDataId);
			if (id) {
				orders.push(id);
			}
		});

		return orders;
	},


	/**
	 * Get the structure.
	 *
	 * @param {jQuery} list From this list. Defaults to the whole list
	 * @return {Object}
	 */
	getStructure: function(list) {
		list = list || this.list;

		var map = {};

		var lis = list.find('li.dp-cat-li');
		lis.each(function() {
			var id = $(this).data('category-id');
			var parentId = 0;

			var ul = $(this).parent();
			if (ul.parent().is('li')) {
				parentId = ul.parent().data('category-id');
			}

			map[id] = parentId;
		});

		return map;
	},


	/**
	 * Are we currently editing?
	 *
	 * @return {Boolean}
	 */
	isTitleEditing: function() {
		return this.list.is('.title-editing');
	},


	/**
	 * Activate the title editor
	 */
	showEditTitles: function() {
		if (this.isTitleEditing()) {
			return;
		}

		this.list.addClass('title-editing');

		var self = this;
		$('.dp-cat-item', this.list).each(function() {
			self._enableEditable($(this));
		});

		this.fireEvent('titlesActivated', [this]);
	},


	/**
	 * Deactivate the title editor
	 */
	endEditTitles: function() {

		if (!this.isTitleEditing()) {
			return;
		}

		var self = this;

		var titles = {};
		$('input.dp-cat-input', this.list).each(function() {
			var item = $(this).parent();
			var dataId = item.parent().data(self.options.dataId);
			var newTitle = self._disableEditable(item);

			if (dataId) {
				titles[dataId] = newTitle;
			}
		});

		this.list.removeClass('title-editing');

		this.fireEvent('titlesUpdated', [titles, this]);
	},

	_enableEditable: function(item) {
		var titleEl = $(this.options.titleSelector, item);
		var title = titleEl.text().trim();

		var inputEl = $('<input type="text" class="dp-cat-input" />');
		inputEl.val(title);

		titleEl.hide();
		inputEl.insertAfter(titleEl);
	},

	_disableEditable: function(item) {
		var titleEl = $(this.options.titleSelector, item);
		var input = $('input.dp-cat-input', item).first();
		var newTitle = input.val().trim();

		input.remove();
		titleEl.text(newTitle).show();

		return newTitle;
	},

	/**
	 * Add a new category
	 *
	 * @param li
	 */
	addNew: function(li) {
		var self = this;

		var tpl = $(this.options.newItemTplSelector).get(0).innerHTML;
		var li = $(tpl);

		var firstLi = $('li.dp-cat-li:first', this.list);
		if (firstLi.length) {
			li.insertBefore(firstLi);
		} else {
			li.appendTo(this.list);
		}

		// init new item
		this._initLisCollection(li);

		this._enableEditable($('.dp-cat-item', li));

		this.fireEvent('newAddEditable', [li, input, this]);

		var input = $('input.dp-cat-input', li);
		var fnDone = function() {
			self.fireEvent('newAdded', [li, input, self]);
			self._disableEditable($('.dp-cat-item', li));
		};
		input.on('blur', fnDone).on('keypress', function(ev) {
			if (ev.which == 13) {
				fnDone();
			}
		});

		var input = $('input:first', li);
		input.focus().select();
	},

	/**
	 * Show an editor for a certain item in the list
	 *
	 * @param {jQuery} li
	 */
	showEditor: function(li) {

		this._initEditor();

		var elPos = li.offset();
		var elWidth = li.width() - 10;

		var title = $('.title-edit:first', li).text().trim();
		$('input.title', this.editTab).val(title);

		elPos.top -= 5;

		this.editTab.css({
			left: elPos.left,
			top: elPos.top,
			width: elWidth
		});

		this.editTabBk.css({
			left: elPos.left + elWidth - 4,
			top: elPos.top + 1
		})

		this.edit.css({
			left: elPos.left + elWidth,
			top: elPos.top - 10
		});

		var ids = ((li.data('usergroup-ids')+'') || '').split(',');
		if (ids.indexOf('1') !== -1) {
			// Everyone means all usergroups should be checked
			$(':checkbox.usergroup', this.edit).prop('checked', true);
		} else {
			$(':checkbox.usergroup', this.edit).prop('checked', false);
			Array.each(ids, function(id) {
				$('input.usergroup-' + id, this.edit).prop('checked', true);
			}, this);
		}

		this.editBack.show();
		this.editTab.show();
		this.edit.show();
		this.editTabBk.show();

		this.edit.data('editing-li', li);
	},

	closeOpenEditor: function() {

		var li = this.edit.data('editing-li');

		if (li) {
			var newTitle = $('input.title', this.editTab).val().trim();
			var titleEl = $('.title-edit:first', li);
			var oldTitle = titleEl.text().trim();
			var dataId = li.data(this.options.dataId);
			var updatedTitle = false;
			var updatedUgs = false;

			if (newTitle != oldTitle) {
				updatedTitle = true;

				titleEl.text(newTitle);

				var titles = {};
				titles[dataId] = newTitle;
				this.fireEvent('titlesUpdated', [titles, this]);
			}

			var ug_ids = [];
			$('input.usergroup:checked', this.edit).each(function() {
				ug_ids.push($(this).val());
			});
			var ug_ids_string = ug_ids.join(',');

			if (li.data('usergroup-ids') != ug_ids_string) {
				updatedUgs = true;
				li.data('usergroup-ids', ug_ids_string);
			}

			if (updatedTitle || updatedUgs) {
				this.fireEvent('catUpdated', [li.data('category-id'), newTitle, ug_ids, li, this]);
			}
		}



		this.editBack.hide();
		this.editTab.hide();
		this.edit.hide().data('editing-li', 0);
		this.editTabBk.hide();
	},

	_initEditor: function() {
		if (this._editorHasInit) return;
		this._editorHasInit = true;

		this.editBack   = $('#' + this.options.editorBaseId + 'cat_editor_backdrop').detach().appendTo('body');
		this.editTab    = $('#' + this.options.editorBaseId + 'cat_editor_tab').detach().appendTo('body');
		this.edit       = $('#' + this.options.editorBaseId + 'cat_editor').detach().appendTo('body');
		this.editTabBk  = $('#' + this.options.editorBaseId + 'cat_editor_shadowbreak').detach().appendTo('body');

		$('.close-trigger', this.edit).on('click', this.closeOpenEditor.bind(this));

		this.edit.on('click', function(ev) {
			ev.stopPropagation();
		});
		this.editTab.on('click', function(ev) {
			ev.stopPropagation();
		});

		this.editBack.on('click', this.closeOpenEditor.bind(this));
	},

	destroy: function() {
		if (this.editBack) {
			this.editBack.remove();
			this.editTab.remove();
			this.edit.remove();
			this.editTabBk.remove();

			this.editBack = this.editTab = this.edit = this.editTabBk = null;
		}
	}
});
