(function($) {
$.fn.dpTwoSelect = function(options) {
	$(this).each(function() {
		dpTwoSelectInit($(this), options);
	});
	return this;
}

function dpTwoSelectInit(el, options) {
	var structure = [],
		has_optgroup = false,
		uid = 0,
		blankOption,
		debugMessge,
		setValue,
		findValue,
		placeholder,
		parentElWrapper,
		parentEl,
		childElWrapper,
		childEl;

	options = $.extend({
		debug: Dp_EnableDebug || false,
		renderPlaceholders: function() {
			var wrap = $('<div class="dp-two-select" />');
			wrap.append('<div class="dp-parent"><select /></div>')
			wrap.append('<div class="dp-child"><select /></div>')

			return wrap;
		}
	}, options || {});

	if (options.debug) {
		debugMessge = function() {
			if (console.log && console.log.apply) {
				console.log.apply(console, arguments);
			}
		}
	} else {
		debugMessge = function() {};
	}

	debugMessge("dpTwoSelect: %o %o", el, options);

	//------------------------------
	// Build up structure
	//------------------------------

	el.find('> optgroup, > option').each(function() {
		var opt = $(this), sub_structure, has_selected, id;

		if (opt.is('optgroup')) {
			sub_structure = [];
			has_optgroup = true;
			has_selected = false;

			opt.find('> option').each(function() {
				id = 'dpid_' + (uid++);
				sub_structure.push({
					id: id,
					value: $(this).val(),
					label: $.trim($(this).text()),
					isSelected: $(this).is(':selected'),
					el: $(this)
				});

				if ($(this).is(':selected')) {
					has_selected = true;
				}
			});

			id = 'dpid_' + (uid++);
			structure.push({
				id: id,
				label: $.trim(opt.attr('label')),
				isGroup: true,
				value: id,
				isSelected: has_selected,
				subs: sub_structure,
				el: opt
			});
		} else {

			// Enables blank option
			if (opt.val() === "") {
				blankOption = opt;
				return;
			}

			id = 'dpid_' + (uid++);
			structure.push({
				id: id,
				value: opt.val(),
				label: $.trim(opt.text()),
				isSelected: opt.is(':selected'),
				el: opt
			});
		}
	});

	debugMessge("dpTwoSelect structure: %o", structure);

	// If this select box has no optgroups then its not a two-level select
	if (!has_optgroup) {
		debugMessge("dpTwoSelect no optgroup, skipping");
		return;
	}

	//------------------------------
	// Methods
	//------------------------------

	/**
	 * Find a value in the structure
	 *
	 * @param {String} val
	 * @return {Array}
	 */
	findValue = function(val) {
		var iParent = null, iChild = null;

		$.each(structure, function(iP, optInfo) {
			if (optInfo.isGroup) {
				if (optInfo.value == val) {
					iParent = iP;
					return false;
				}

				$.each(optInfo.subs, function(iC, subOptInfo) {
					if (subOptInfo.value == val) {
						iParent = iP;
						iChild = iC;
						return false;
					}
				});

				if (iParent) {
					return false;
				}
			} else {
				if (optInfo.value == val) {
					iParent = iP;
					iChild = null;
					return false;
				}
			}
		});

		if (iParent === null) {
			return null;
		}

		return [iParent, iChild];
	};

	/**
	 * Set the current value
	 * @param {String} val
	 */
	setValue = function(val) {
		var idx = findValue(val),
			parentOptInfo,
			subBlankOption,
			selectedVal,
			childOptInfo = false;

		if (!idx) {
			if (blankOption) {
				placeholder.removeClass('dp-show-child');
				parentEl.find('option').first().prop('selected', true);
				el.find('option[value=""], option:not([value])').first().prop('selected', true).trigger('change');
			} else {
				setValue(parentEl.find('option').first().data('dp-id'));
			}
			return false;
		}

		parentOptInfo = structure[idx[0]];

		if (idx && idx[1] !== null && typeof idx[1] !== 'undefined') {
			childOptInfo = parentOptInfo.subs[idx[1]];
		}

		parentEl.find('option.' + parentOptInfo.id).prop('selected', true);

		if (parentOptInfo.isGroup) {
			childEl.get(0).innerHTML = '';

			if (blankOption) {
				subBlankOption = blankOption.clone();

				if (!childOptInfo) {
					subBlankOption.prop('selected', true);
					selectedVal = subBlankOption.val();
				}

				childEl.append(subBlankOption.clone());
			}

			$.each(parentOptInfo.subs, function(i, optInfo) {
				var opt = $('<option />');
				opt.addClass(optInfo.id);
				opt.attr('value', optInfo.value);
				opt.text(optInfo.label);

				if (childOptInfo && optInfo.id == childOptInfo.id) {
					opt.prop('selected', true);
					selectedVal = opt.val();
				}

				childEl.append(opt);
			});

			// this toggle is required for IE to update the display of the select box,
			// or else you'll end up with a select box that looks like it has the same options
			// even though it doesnt
			childEl.hide().show();
			placeholder.addClass('dp-show-child');
		} else {
			selectedVal = parentOptInfo.value;
			placeholder.removeClass('dp-show-child');
		}

		// Update the bound original select box
		el.find('option[value="' + selectedVal + '"]').prop('selected', true).trigger('change');
	};

	//------------------------------
	// Init
	//------------------------------

	el.hide().addClass('dp-two-select').data('dp-two-select', this);
	placeholder = options.renderPlaceholders(el, options);
	placeholder.insertAfter(el);

	parentElWrapper = placeholder.find('.dp-parent');
	parentEl        = parentElWrapper.find('select');
	childElWrapper  = placeholder.find('.dp-child');
	childEl         = childElWrapper.find('select');

	if (blankOption) {
		parentEl.append(blankOption.clone());
	}

	// Process parent select options
	$.each(structure, function(i, optInfo) {
		var opt = $('<option />');
		opt.addClass(optInfo.id).data('dp-id', optInfo.id);
		if (optInfo.isGroup) {
			opt.attr('value', optInfo.id);
			opt.text(optInfo.label);
		} else {
			opt.attr('value', optInfo.value);
			opt.text(optInfo.label);
		}

		parentEl.append(opt);
	});

	parentEl.on('change', function() {
		var opt = $(this).find('option:selected');
		var val = opt.val();

		setValue(val);
	});

	childEl.on('change', function() {
		var val = $(this).val();

		// Update the bound original select box
		el.find('option[value="' + val + '"]').prop('selected', true).trigger('change');
	});

	// Set initial value
	setValue(el.find('option:selected').val());

	//------------------------------
	// Public accessors
	//------------------------------

	this.setValue = function(val) {
		setValue(val);
	};
	this.getOptions = function() {
		return options;
	};
	this.getPlaceholder = function() {
		return placeholder;
	};
	this.getParentEl = function() {
		return parentEl;
	};
	this.getChildEl = function() {
		return childEl;
	};
}
})(jQuery);