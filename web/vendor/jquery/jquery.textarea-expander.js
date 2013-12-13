// Written by DeskPRO
// http://www.deskpro.com/
(function($) {
	$.fn.TextAreaExpander = function (settings) {

		var config = {
			classmodifier: "tae"
		};

		if (settings) {
			$.extend(config, settings);
		}

		function update(element) {
			var $elem  = $(element);
			var offset = ($elem.css('box-sizing') == 'border-box' || $elem.css('-moz-box-sizing') == 'border-box') ? $elem.outerHeight() - $elem.height() : 0;
			var height = $elem.height(), current = height;
			var max    = $elem.data('expander-max-height') || 1000;
			var min    = $elem.data('expander-min-height') || 50;

			while (element.clientHeight < element.scrollHeight || element.clientHeight + offset < min) {
				height += 5;
				$elem.height(height);
				if (height + offset > max) {
					break;
				}
			}

			var last = height;
			while (element.clientHeight >= element.scrollHeight) {
				last = height;
				height -= 5;
				$elem.height(height + offset);
				if (height + offset < min) {
					break;
				}
			}

			$elem.height(last + offset);
			if (height + offset > max && current + offset < max) {
				$elem.css('overflow', 'auto');
			} else if (height + offset < max && current + offset > max) {
				$elem.css('overflow', 'hidden');
			}
		}

		return this.each(function () {
			$(this).addClass(config.classmodifier).css({ overflow: "hidden" });
			if (!$(this).data('expander-min-height')) {
				$(this).data('expander-min-height', $(this).height());
			}

			$(this).bind("textareaexpander_fire", function () {
				update(this);
				$(this).trigger('textareaexpander_expanded');
			});
			$(this).bind("keyup", function () {
				update(this);
				$(this).trigger('textareaexpander_expanded');
			});
		});
	};
})(jQuery);