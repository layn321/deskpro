Orb.createNamespace('DeskPRO.Agent.Widget');

DeskPRO.Agent.Widget.AgentSelector = new Orb.Class({

	Implements: [Orb.Util.Options, Orb.Util.Events],

	initialize: function(options) {
		this.options = {
			triggerElement: null,
			agentList: null,
			multipleChoice: false,
			showNone: false,
			noneLabel: 'Unassigned',
			zIndex: 30001,
			startWith: []
		};

		if (options) this.setOptions(options);

		this.previousSelection = '';

		if (this.options.triggerElement) {
			var self = this;
			$(this.options.triggerElement).on('click', function(ev) {
				ev.preventDefault();
				self.open(ev);
			});
		}
	},

	_initWrapper: function() {
		if (this.wrapper) return;

		var agentListItems = $('li', this.options.agentList);

		this.backdrop = $('<div class="backdrop"></div>').appendTo('body');
		this.backdrop.on('click', this.close.bind(this));

		this.wrapper = $('<div class="field-overlay agent-selector" style="display:none;"><div class="close-trigger"></div></div>');
		$('.close-trigger', this.wrapper).on('click', this.close.bind(this));

		var listWrapper = $(
			'<div class="with-scrollbar">' +
			'<div class="scrollbar disable"><div class="track"><div class="thumb"><div class="end"></div></div></div></div>' +
			'<div class="scroll-viewport"><div class="scroll-content">' +
			'</div></div></div>'
		);

		if (agentListItems.length >= 10) {
			this.filter = $('<div class="filter"><div class="input-wrap"><input type="text" value="" placeholder="Find an agent" /></div></div>').appendTo(this.wrapper);

			$('input', this.filter).on('keyup', this.updateFilter.bind(this));
		} else {
			this.filter = null;
		}

		var isMulti = this.options.multipleChoice;

		var startWith = this.options.startWith;
		var agentList = $('<ul />');

		if (this.options.showNone) {
			var newLi = $('<li class="agent-0" data-agent-id="0"><div class="name"><a>' + this.options.noneLabel + '</a></div></li>');
			var choiceContainer = $('<div class="choice" />');

			var checked = '';
			if (!startWith.length) {
				checked = 'checked="checked"';
			}
			if (isMulti) {
				var choice = $('<input type="checkbox" name="agents[]" '+checked+' value="0" class="agent-choice-0" />');
			} else {
				var choice = $('<input type="radio" name="agents[]" '+checked+' value="0" class="agent-choice-0" />');
			}
			choice.appendTo(choiceContainer);
			newLi.append(choiceContainer);
			newLi.append($('<br style="clear:left;height: 1px;overflow: hidden;"/>'));
			agentList.append(newLi);
		}

		agentListItems.each(function() {
			var li = $(this);

			var agentId = li.data('agent-id');
			var agentName = $('a:first', li).text();
			var image = $('img:first', li);

			var newLi = $('<li class="agent-' + agentId + '" data-agent-id="' + agentId + '" />');

			if (image.length) {
				var imgContainer = $('<div class="avatar" />');
				image.clone().appendTo(imgContainer);
				imgContainer.appendTo(newLi);
			}

			var nameContainer = $('<div class="name" />');
			nameContainer.append('<a>' + Orb.escapeHtml(agentName) + '</a>');
			nameContainer.appendTo(newLi);

			var checked = '';
			if (startWith.indexOf(agentId+'') !== -1 || startWith.indexOf(parseInt(agentId)) !== -1) {
				checked = 'checked="checked"';
			}

			var choiceContainer = $('<div class="choice" />');
			if (isMulti) {
				var choice = $('<input type="checkbox" name="agents[]" '+checked+' value="'+agentId+'" class="agent-choice-' + agentId + '" />');
			} else {
				var choice = $('<input type="radio" name="agents[]" '+checked+' value="'+agentId+'" class="agent-choice-' + agentId + '" />');
			}
			choice.appendTo(choiceContainer);
			choiceContainer.appendTo(newLi);

			newLi.append($('<br style="clear:left;height: 1px;overflow: hidden;"/>'));

			newLi.appendTo(agentList);

			newLi.on('click', function(ev) {
				ev.stopPropagation();
				if (!$(ev.target).is('input')) {
					choice.click();
				}
			});
		});

		delete agentListItems;

		this.agentList = agentList;

		var self = this;
		$('input[type="checkbox"], input[type="radio"]', agentList).on('click', function(ev) {
			var agentId = $(this).val();
			var checked = $(this).is(':checked');

			var eventData = {
				agentSelector: self,
				element: $(this),
				agentId: agentId,
				checked: checked,
				event: ev
			};

			self.fireEvent('selectionClick', [eventData]);
		});

		agentList.appendTo($('div.scroll-content', listWrapper));
		listWrapper.appendTo(this.wrapper);

		this.listWrapper = listWrapper;

		this.wrapper.appendTo('body');

		var selectionString = this.getSelection();
		if (this.options.multipleChoice) {
			selectionString = selectionString.join(',');
		}
		this.previousSelection = selectionString;

		var eventData = {
			agentSelector: this,
			wrapper: this.wrapper
		};

		this.fireEvent('initWrapper', [eventData]);
	},

	updateFilter: function() {
		var input = $('input', this.filter);
		var filter = input.val().trim().toLowerCase();

		var lis = $('> li', this.agentList);

		if (!filter) {
			lis.show();
			return;
		}

		lis.each(function() {
			var name = $('a:first', this).text().toLowerCase();
			if (name.indexOf(filter) !== -1) {
				$(this).show();
			} else {
				$(this).hide();
			}
		});
	},

	open: function(event) {

		this._initWrapper();

		var target = $(event.target);

		var width = this.wrapper.outerWidth();
		var height = this.wrapper.outerHeight();

		var pageWidth = $(document).width();
		var pageHeight = $(document).height();

		var pageX = target.offset().left;
		var pageY = target.offset().top;

		// Determine which way to open the menu,
		// We do this so the menu doesn't go off-screen if
		// its near the edge
		if (pageX+width < pageWidth) {
			var left = pageX+6;
		} else {
			var left = pageX - width - 4;
		}

		if (pageY+height < pageHeight) {
			var top = pageY - 6;
		} else {
			var top = pageY - height + 4;
		}

		if (top < 0) {
			top = 5;
		}

		this.backdrop.show();
		this.wrapper.addClass('open');
		this.wrapper.css({
			'z-index': this.options.zIndex,
			'position': 'absolute',
			'top': top,
			'left': left,
			'display': 'block'
		});

		this.listWrapper.tinyscrollbar();

		var eventData = {
			agentSelector: this,
			event: event
		};

		this.fireEvent('open', [eventData]);
	},

	close: function() {

		if (!this.wrapper.is('.open')) return;

		var eventData = {
			agentSelector: this,
			cancelClose: false
		};

		this.fireEvent('beforeClose', [eventData]);

		if (eventData.cancelClose) {
			return;
		}

		delete eventData.cancelClose;

		this.backdrop.hide();
		this.wrapper.hide().removeClass('open');

		this.fireEvent('close', [eventData]);

		var selectionString = this.getSelection();
		if (this.options.multipleChoice) {
			selectionString = selectionString.join(',');
		}

		if (this.previousSelection != selectionString) {
			eventData.selection = this.getSelection();
			this.fireEvent('selectionChanged', [eventData]);

			this.previousSelection = selectionString;
		}
	},

	isOpen: function() {
		return this.wrapper.is('.open');
	},

	getWrapper: function() {
		return this.wrapper;
	},

	getSelection: function() {
		if (this.options.multipleChoice) {
			var ids = [];
			$('input[type="checkbox"]:checked', this.agentList).each(function() {
				ids.push($(this).val());
			});

			return ids;

		} else {
			var id = $('input[type="radio"]:checked:first', this.agentList).val();
			return id;
		}
	}
});
