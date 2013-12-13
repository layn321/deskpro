Orb.createNamespace('DeskPRO.Agent.PageHelper');

/**
 * Allows a user to edit and reorder categories, with double-click to modify titles.
 */
DeskPRO.Agent.PageHelper.CategoryEdit = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(options) {
		this.options = {
			wrapper: null
		};

		this.setOptions(options||{});

		this.wrapper = $(this.options.wrapper);
	},


	/**
	 * Lazy-init object when its first opened.
	 */
	_init: function() {
		if (this.hasInit) return;
		this.hasInit = true;

		this.wrapper.detach().appendTo('body');

		// Usually theres a doc click waiting to close this,
		// so dont propogate clicks on this element
		this.wrapper.on('click', function(ev) {
			ev.stopPropagation();
		});

		$('.close-trigger', this.wrapper).on('click', (function(ev) {
			this.close();
		}).bind(this));

		$('.new-close-trigger', this.wrapper).on('click', (function(ev) {
			this.closeNewDlg();
		}).bind(this));

		$('ul', this.wrapper).sortable({
			axis: 'y',
			items: '> li'
		});

		this.backdrop = $('.backdrop:first', this.wrapper);
		this.newDlg = $('.new-dlg:first', this.wrapper);
		this.newParent = $('select:first', this.newDlg);
		this.newTitle = $('input:first', this.newDlg);
		$('.add-save-trigger', this.newDlg).on('click', (function() {
			this.addNewToList();
		}).bind(this));

		$('.add-trigger:first', this.wrapper).on('click', (function() {
			this.openNewDlg();
		}).bind(this));

		$('.save-all-trigger', this.wrapper).on('click', (function() {
			this.fireEvent('save', [this]);

			this.close();
		}).bind(this));

		var self = this;
		this.wrapper.on('dblclick', '.title', function() {
			self.enableEditTitle($(this));
		});
	},


	/**
	 * Open the overlay
	 */
	open: function() {
		this._init();

		if (this.wrapper.is('.open')) {
			return;
		}

		this.wrapper.css({
			position: 'absolute',
			left: 40,
			top: 150
		});

		this.wrapper.addClass('open').fadeIn();
	},


	/**
	 * Close the overlay
	 */
	close: function() {
		this.closeNewDlg();
		this.wrapper.removeClass('open').fadeOut();
	},


	/**
	 * Open the new category input
	 */
	openNewDlg: function() {

		// Regenerate list
		var html = [];
		$('li > .title', this.wrapper).each(function() {
			var el = $(this);
			var depth = parseInt(el.data('depth'));
			var catId = el.data('cat-id');
			var title = el.text().trim();

			if (depth) {
				title = Orb.strRepeat('--', depth) + ' ' + title;
			}

			html.push('<option value="'+catId+'">' + Orb.escapeHtml(title) + '</option>');
		});

		html = html.join('');

		$('option:not(.none)', this.newParent).remove();
		$('option.none', this.newParent).after($(html));

		var top  = 50;
		var left = 65;

		var dlg = this.newDlg;
		dlg.css({
			display: 'absolute',
			top: top,
			left: left
		});

		this.backdrop.fadeIn('fast');
		dlg.fadeIn();
	},


	/**
	 * Close the new cat input
	 */
	closeNewDlg: function() {
		var dlg = this.newDlg;
		dlg.fadeOut('fast');
		this.backdrop.fadeOut('fast');
	},


	/**
	 * After clicking 'add' on the new cat input, this adds it to the actual list
	 */
	addNewToList: function() {
		var parentId = this.newParent.val();
		var title = this.newTitle.val().trim();

		if (!title.length) {
			return;
		}

		this.newTitle.val('');

		var ul, depth = 0, parent_li = false;
		if (parentId && parentId != '0') {
			parent_li = $('li.cat-' + parentId, this.wrapper);
		}

		if (parent_li && parent_li.length) {
			depth = parseInt($('> .title', parent_li).data('depth')) + 1;

			ul = $('> ul', parent_li);

			if (!ul.length) {
				var new_ul = $('<ul></ul>');
				parent_li.append(new_ul);

				new_ul.sortable({
					axis: 'y',
					items: '> li'
				});
			}
			ul = $('> ul', parent_li);
		} else {
			ul = $('ul.top:first', this.wrapper);
		}

		var tmp_id = 'new_' + Orb.uuid();
		var li = '<li class="cat-'+tmp_id+'"><div class="title new" data-cat-id="' + tmp_id + '" data-depth="'+depth+'">' + Orb.escapeHtml(title) + '</div></li>';

		ul.append(li);

		this.closeNewDlg();
	},


	/**
	 * Enable the editable title
	 *
	 * @param titleEl
	 */
	enableEditTitle: function(titleEl) {
		var input = $('<input />');
		input.val(titleEl.text().trim());

		titleEl.empty().append(input);

		var btn = $('<button class="dp-button x-small">Apply</button>');
		titleEl.append(btn);

		var self = this;
		input.on('keypress', function(ev) {
			if (ev.keyCode == 13) {
				ev.preventDefault();
				self._applyEditTitle(titleEl, input);
			}
		});
		btn.on('click', function() {
			self._applyEditTitle(titleEl, input);
		});
	},

	_applyEditTitle: function(titleEl, input) {
		titleEl.empty().text(input.val());
	},


	/**
	 * Encode the current structure
	 */
	encode: function() {
		var data = [];

		this._encodeSet(data, $('ul.top', this.wrapper), 0);

		return data;
	},

	_encodeSet: function(data, ul, parentId) {
		var self = this;
		$('li', ul).each(function() {
			var li = $(this);
			var title = $('> .title', li);
			var catId = title.data('cat-id');
			var displayOrder = data.length + 1;
			var isNew = title.is('.new');

			data.push({
				id: catId,
				parentId: parentId,
				isNew: isNew,
				displayOrder: displayOrder,
				title: title.text().trim()
			});

			var ul = $('> ul', li);
			if (ul.length) {
				self._encodeSet(data, ul, catId);
			}
		});
	},


	/**
	 * Encode the data as a jQuery form data object
	 *
	 * @param name
	 */
	encodeForm: function(name) {
		if (!name) name = 'cats';
		var rawData = this.encode();

		var data = [];
		var count = 0;

		Array.each(rawData, function(item) {

			Object.each(item, function(v,k) {
				if (k == 'isNew') {
					if (v) v = 1;
					else v = 0;
				}
				data.push({
					name: name + '['+count+']['+k+']',
					value: v
				});
			});

			count++;
		});

		return data;
	},


	/**
	 * Destroy this widget
	 */
	destroy: function() {
		if (this.hasInit) {
			this.wrapper.remove();
		}
	}
});
