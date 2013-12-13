Orb.createNamespace('DeskPRO.Agent');

//######################################################################################################################
//# JS Scroll Handler
//######################################################################################################################

if (!window.DP_NO_JS_SCROLL) {
	DeskPRO.Agent.ScrollerHandler = new Orb.Class({
		Implements: [Orb.Util.Options],

		initialize: function(pageObject, element, options) {
			var hasInit = false;

			$.extend(options, {
				'showEvent': false,
				'hideEvent': false
			});

			element = $(element);
			element.data('scroll_handler', this);
			element.addClass('with-scroll-handler');

			var scrollTrack = null;

			var onScrollTimer = false;

			function initScroll() {
				if (!element) return;
				if (hasInit) return;
				hasInit = true;

				element.tinyscrollbar();
				element.on('dp_scroll', function() {
					if (!onScrollTimer) {
						onScrollTimer = setTimeout(function() {
							onScrollTimer = false;
							if (element) {
								element.find('.select2-dropdown-open').select2("positionDropdown");
							}
						}, 25);
					}
				});
			}

			function updateSize() {
				if (!element) return;
				initScroll();
				if (element.tinyscrollbar_update) {
					element.tinyscrollbar_update();
				}
			}

			function isScrollEnabled() {
				if (!scrollTrack) {
					element.find('> .scrollbar');
				}

				return element.hasClass('disable');
			};

			function restorePosition() {
				if (!element) return;
				if (hasInit && element) {
					element.trigger('restorescroll');
				}
			}

			function destroy() {
				if (!element) return;
				if (hasInit && element.tinyscrollbar_destroy) {
					element.tinyscrollbar_destroy();
				}
				element = null;
				options = null;
				pageObject = null;
			};

			if (pageObject && pageObject.addEvent && options.showEvent) {
				pageObject.addEvent(options.showEvent, updateSize);
			} else {
				initScroll();
			}

			this.updateSize = updateSize;
			this.restorePosition = restorePosition;
			this.destroy = destroy;
			this.isScrollEnabled = isScrollEnabled;
			this.isInitialized = function() { return hasInit };
			this.getElement = function() { return element; }
		}
	});

//######################################################################################################################
//# JS Scroll Handler
//######################################################################################################################

} else {
	DeskPRO.Agent.ScrollerHandler = new Orb.Class({
		Implements: [Orb.Util.Options],

		initialize: function(pageObject, $element, options) {
			var scroller;
			var resetLastX = null;
			var resetLastY = null;
			var resetTimeout = null;

			$.extend(options, {
				'showEvent': false,
				'hideEvent': false
			});

			$element = $($element);
			$element.data('scroll_handler', this);
			$element.addClass('with-scroll-handler');

			$element.on('goscrolltop', function() {

			});

			$element.on('scrollupdate', function() {

			});

			$element.on('goscrollbottom', function() {

			});

			$element.on('goscrollbottom_stick', function() {

			});

			function initScroll() {

			}

			function updateSize() {
				return;
			}

			function restorePosition() {
				return;
			}

			function destroy() {
				if (!$element) return;
				$element = null;
				options = null;
				pageObject = null;
			};

			function scrollToElement(el) {
				el = $(el);
				if (scroller) {
					scroller.scrollToElement(el.get(0));
				}
			}

			this.updateSize = updateSize;
			this.restorePosition = restorePosition;
			this.destroy = destroy;
			this.scrollToElement = scrollToElement;
			this.isInitialized = function() { return !!scroller; };
			this.getElement = function() { return $element; }
		}
	});
}