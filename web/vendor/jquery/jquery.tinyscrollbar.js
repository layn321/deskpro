/*!
 * Tiny Scrollbar 1.43
 * http://www.baijs.nl/tinyscrollbar/
 *
 * Copyright 2010, Maarten Baijs
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.opensource.org/licenses/gpl-2.0.php
 *
 * Date: 02 / 24 / 2011
 * Depends on library: jQuery
 */
if (typeof Modernizr != 'undefined' && Modernizr.ipad) {
		(function($){
		$.fn.tinyscrollbar = function(options){
			var defaults = {
				axis: 'y', // vertical or horizontal scrollbar? ( x || y ).
				wheel: 40,  //how many pixels must the mouswheel scroll at a time.
				scroll: true, //enable or disable the mousewheel scrollbar
				size: 'auto', //set the size of the scrollbar to auto or a fixed number.
				sizethumb: 'auto' //set the size of the thumb to auto or a fixed number.
			};
			var options = $.extend(defaults, options);
			var oWrapper = $(this);

			// Handle inserting wrappers etc automatically if the supplied
			// element is the scroll content
			if (oWrapper.is('.scroll-content') && !oWrapper.parent().is('.scroll-viewport')) {
				var parentWrapper = oWrapper.parent();
				oWrapper.wrap('<div class="scroll-viewport" />');
				$('<div class="scrollbar disable"><div class="track"><div class="thumb"><div class="end"></div></div></div></div>').insertBefore(oWrapper);

				parentWrapper.addClass('with-scrollbar');

				return parentWrapper.tinyscrollbar(options);
			}

			if (oWrapper.is('.scroll-setup')) {
				return oWrapper;
			}

			oWrapper.addClass('scroll-setup with-scrollbar');

			oWrapper.on('goscrolltop', function() {
				oThumb.obj.css(sDirection, 0);
				oContent.obj.css(sDirection, 0);
				iScroll = 0;
				iMouse['start'] = oThumb.obj.offset()[sDirection];
				oWrapper.data('dp-scroll-pos', 0);

				oWrapper.trigger('dp_scroll');
			});
			oWrapper.on('scrollupdate', function() {});
			oWrapper.on('goscrollto', function(ev, scrollTo) {
				iScroll = scrollTo;
				iScroll = Math.min((oContent[options.axis] - oViewport[options.axis]), Math.max(0, iScroll));
				iScroll += 10;

				oThumb.obj.css(sDirection, iScroll / oScrollbar.ratio);
				oContent.obj.css(sDirection, -iScroll);
				oWrapper.data('dp-scroll-pos', iScroll);

				oWrapper.trigger('dp_scroll');
			});
			oWrapper.on('goscrollbottom', function() {

				// No scrolling, there is no bottom
				if (oScrollbar.obj.hasClass('disable')) {
					return;
				}

				iScroll = 100000;
				iScroll = Math.min((oContent[options.axis] - oViewport[options.axis]), Math.max(0, iScroll));
				iScroll + 10;

				oThumb.obj.css(sDirection, iScroll / oScrollbar.ratio);
				oContent.obj.css(sDirection, -iScroll);
				oWrapper.data('dp-scroll-pos', iScroll);

				oWrapper.trigger('dp_scroll');
			});
			oWrapper.on('restorescroll', function() {

			});

			var oViewport = { obj: $('.scroll-viewport', oWrapper).first() };
			var oContent = { obj: $('.scroll-content', oWrapper).first() };
			var oScrollbar = { obj: $('.scrollbar', oWrapper).first() };
			var oTrack = { obj: $('.track', oScrollbar.obj) };
			var oThumb = { obj: $('.thumb', oScrollbar.obj) };
			var sAxis = options.axis == 'x', sDirection = sAxis ? 'left' : 'top', sSize = sAxis ? 'Width' : 'Height';
			var iScroll, iPosition = { start: 0, now: 0 }, iMouse = {};
			var wheelStopTimeout = null;
			var mouseoverTimeout = null;

			if (this.length > 1){
				this.each(function(){$(this).tinyscrollbar(options)});
				return this;
			}
			this.initialize = function(){	};
			this.tinyscrollbar_update = function(sScroll){};
			this.tinyscrollbar_destroy = function() {};
			function setSize(){	};
			function setEvents(){};
			function start(oEvent){	return false;};
			function wheel(oEvent){	};

			function end(oEvent){};
			function drag(oEvent){};
			return this.initialize();
		};
	})(jQuery);
} else {
	(function($){
		$.fn.tinyscrollbar = function(options){
			var defaults = {
				axis: 'y', // vertical or horizontal scrollbar? ( x || y ).
				wheel: 130,  //how many pixels must the mouswheel scroll at a time.
				scroll: true, //enable or disable the mousewheel scrollbar
				size: 'auto', //set the size of the scrollbar to auto or a fixed number.
				sizethumb: 'auto' //set the size of the thumb to auto or a fixed number.
			};
			var options = $.extend(defaults, options);
			var oWrapper = $(this);
			var timeout = null;
			var touchEvents = 'ontouchstart' in document.documentElement;
			var msTouchEvents = !!window.navigator.msMaxTouchPoints;
			var fns = [];
			var isTouchEvent = false;

			// Handle inserting wrappers etc automatically if the supplied
			// element is the scroll content
			if (oWrapper.is('.scroll-content') && !oWrapper.parent().is('.scroll-viewport')) {
				var parentWrapper = oWrapper.parent();
				oWrapper.wrap('<div class="scroll-viewport" />');
				$('<div class="scrollbar disable"><div class="track"><div class="thumb"><div class="end"></div></div></div></div>').insertBefore(oWrapper);

				parentWrapper.addClass('with-scrollbar');

				return parentWrapper.tinyscrollbar(options);
			}

			if (oWrapper.is('.scroll-setup')) {
				return oWrapper;
			}

			function goscrollto(ev, scrollTo) {
				iScroll = scrollTo;
				iScroll = Math.min((oContent[options.axis] - oViewport[options.axis]), Math.max(0, iScroll));

				oThumb.obj.css(sDirection, iScroll / oScrollbar.ratio);
				oContent.obj.css(sDirection, -iScroll);
				oWrapper.data('dp-scroll-pos', iScroll);
			}
			function goscrollbottom() {
				// No scrolling, there is no bottom
				if (oScrollbar.obj.hasClass('disable')) {
					return;
				}

				iScroll = oContent[options.axis] - oViewport[options.axis];

				oThumb.obj.css(sDirection, iScroll / oScrollbar.ratio);
				oContent.obj.css(sDirection, -iScroll);
				oWrapper.data('dp-scroll-pos', iScroll);
				oScrollbar.obj.addClass('stuck-btm');
			}
			function goscrollbottom_stick() {
				// No scrolling, there is no bottom
				if (oScrollbar.obj.hasClass('disable') || !oScrollbar.obj.hasClass('stuck-btm')) {
					return;
				}

				goscrollbottom();
			}
			function restorescroll() {
				iScroll = parseInt(oWrapper.data('dp-scroll-pos'));
				if (!iScroll) {
					return;
				}

				oThumb.obj.css(sDirection, iScroll / oScrollbar.ratio);
				oContent.obj.css(sDirection, -iScroll);
				oWrapper.data('dp-scroll-pos', iScroll);
			}
			function _update() {
				scrollbarUpdate();
				oWrapper.addClass('scroll-draw');
			}
			function scrollbarUpdate(sScroll) {
				if (!sScroll) {
					sScroll = 'relative';
				}

				if (!oViewport.obj[0]) {
					return;
				}

				var newViewportS = oViewport.obj[0]['offset'+ sSize];
				var newContentS = oContent.obj[0]['scroll'+ sSize];

				// No change
				if (newViewportS == oViewport[options.axis] && newContentS == oContent[options.axis]) {
					return;
				}

				oViewport[options.axis] = newViewportS;
				oContent[options.axis] = newContentS;
				oWrapper.data('dp-scroll-height', newContentS);
				oWrapper.data('dp-scroll-viewport', newViewportS);
				oContent.ratio = oViewport[options.axis] / oContent[options.axis];
				if (oContent.ratio >= 1) {
					oScrollbar.obj.addClass('disable');
					oViewport.obj.addClass('scroll-disabled');
				} else {
					oScrollbar.obj.removeClass('disable');
					oViewport.obj.removeClass('scroll-disabled');
				}
				oTrack[options.axis] = options.size == 'auto' ? oViewport[options.axis] : options.size;
				oThumb[options.axis] = Math.min(oTrack[options.axis], Math.max(0, ( options.sizethumb == 'auto' ? (oTrack[options.axis] * oContent.ratio) : options.sizethumb )));

				if (oThumb[options.axis] < 30) {
					oThumb[options.axis] = 30;
					options.sizethumb = 30;
				}

				oScrollbar.ratio = options.sizethumb == 'auto' ? (oContent[options.axis] / oTrack[options.axis]) : (oContent[options.axis] - oViewport[options.axis]) / (oTrack[options.axis] - oThumb[options.axis]);

				if (sScroll == 'relative' && oContent.ratio <= 1.0) {
					iScroll = Math.min((oContent[options.axis] - oViewport[options.axis]), Math.max(0, iScroll));
				}

				if (!iScroll) {
					iScroll = 0;
				}

				if (oViewport[options.axis] >= oContent[options.axis]) {
					iScroll = 0;
				}

				setSize();
				oWrapper.trigger('dp_resize');
			}

			oWrapper.addClass('scroll-setup with-scrollbar');

			oWrapper.on('goscrolltop', function() {
				oThumb.obj.css(sDirection, 0);
				oContent.obj.css(sDirection, 0);
				iScroll = 0;
				iMouse['start'] = oThumb.obj.offset()[sDirection];
				oWrapper.data('dp-scroll-pos', 0);
				oWrapper.removeClass('stuck');
			});
			oWrapper.on('goscrollto', goscrollto);
			oWrapper.on('scrollupdate', scrollbarUpdate);
			oWrapper.on('goscrollbottom', goscrollbottom);
			oWrapper.on('goscrollbottom_stick', goscrollbottom_stick);
			oWrapper.on('restorescroll', restorescroll);

			var oViewport = { obj: $('.scroll-viewport', oWrapper).first() };
			var oContent = { obj: $('.scroll-content', oWrapper).first() };
			var oScrollbar = { obj: $('.scrollbar', oWrapper).first() };
			var oTrack = { obj: $('.track', oScrollbar.obj) };
			var oThumb = { obj: $('.thumb', oScrollbar.obj) };
			var sAxis = options.axis == 'x', sDirection = sAxis ? 'left' : 'top', sSize = sAxis ? 'Width' : 'Height';
			var iScroll, iPosition = { start: 0, now: 0 }, iMouse = {};
			var wheelStopTimeout = null;
			var mouseoverTimeout = null;

			// - Sometimes the browser might scroll the view,
			//   which is different than the position offset
			//   used by this JS scroller
			// - So this creates a scroll + our offset,
			//   which screws up the whole thing
			// - A browser modifies the scroll when focusing fields
			//   or links (eg if you were to tab through, or when we auto-focus the reply box)
			oViewport.obj.on('scroll', function(ev) {
				oViewport.obj.scrollTop(0);
			});

			if (this.length > 1){
				this.each(function(){$(this).tinyscrollbar(options)});
				return this;
			}
			this.initialize = function(){
				setEvents();
				timeout = window.setTimeout(_update, 250);
			};
			this.tinyscrollbar_destroy = function() {
				if (timeout) {
					window.clearTimeout(timeout);
					timeout = null;
				}
			};
			this.tinyscrollbar_update = function(sScroll){
				scrollbarUpdate(sScroll);
			};
			function setSize(){
				oThumb.obj.css(sDirection, iScroll / oScrollbar.ratio);
				oContent.obj.css(sDirection, -iScroll);

				iMouse['start'] = oThumb.obj.offset()[sDirection];
				var sCssSize = sSize.toLowerCase();
				oScrollbar.obj.css(sCssSize, oTrack[options.axis]);
				oTrack.obj.css(sCssSize, oTrack[options.axis]);
				oThumb.obj.css(sCssSize, oThumb[options.axis]);
			};

			function setEvents(){
				if (touchEvents) {
					oViewport.obj[0].ontouchstart = function( event )
					{
						if( 1 === event.touches.length )
						{
							start( event.touches[ 0 ] );
							event.stopPropagation();
						}
					};
				} else if (msTouchEvents) {
					var fn = function( event )
					{
						isTouchEvent = true;
						start( event );
						event.stopPropagation();
					};
					fns.push(fn);
					oViewport.obj[0].addEventListener("MSPointerDown", fn,false);

					oThumb.obj.bind('mousedown', start);
					oTrack.obj.bind('mouseup', drag);
				} else {
					oThumb.obj.bind('mousedown', start);
					oTrack.obj.bind('mouseup', drag);
				}

				if(options.scroll && this.addEventListener){
					oWrapper[0].addEventListener('DOMMouseScroll', wheel, false);
					oWrapper[0].addEventListener('mousewheel', wheel, false );
				}
				else if(options.scroll){oWrapper[0].onmousewheel = wheel;}
			};
			function start(oEvent){
				iMouse.start = sAxis ? oEvent.pageX : oEvent.pageY;
				var oThumbDir = parseInt(oThumb.obj.css(sDirection));
				iPosition.start = oThumbDir == 'auto' ? 0 : oThumbDir;
				if (touchEvents) {
					document.ontouchmove = function( event )
					{
						event.preventDefault();
						drag( event.touches[ 0 ] );
					};
					document.ontouchend = end;
				} else if (msTouchEvents && oEvent.pointerType && oEvent.pointerType == oEvent.MSPOINTER_TYPE_TOUCH) {
					var fn = function( event )
					{
						isTouchEvent = true;
						event.preventDefault();
						drag( event );
					};
					fns.push(fn);
					document.addEventListener("MSPointerMove", fn, false);
					document.addEventListener("MSPointerUp", end, false);
				} else {
					$( document ).bind( 'mousemove', drag );
					$( document ).bind( 'mouseup', end );
					oThumb.obj.bind( 'mouseup', end );
					oTrack.obj.addClass('dragging');
				}
				return false;
			};
			function wheel(oEvent){
				oWrapper.removeClass('stuck');

				// Scrolling the rich text editor
				var redactor = null;
				if (oEvent && oEvent.explicitOriginalTarget) {
					redactor = $(oEvent.explicitOriginalTarget).closest('.redactor_editor');
					if (redactor[0]) {
						var maxH = parseInt(redactor.css('max-height'));
						var h = parseInt(redactor.height());

						if (h >= maxH) {
							return;
						}
					}
				}

				if(!(oContent.ratio >= 1)){
					var oEvent = oEvent || window.event;
					var iDelta = oEvent.wheelDelta ? oEvent.wheelDelta/120 : -oEvent.detail/3;
					iScroll -= iDelta * options.wheel;
					iScroll = Math.min((oContent[options.axis] - oViewport[options.axis]), Math.max(0, iScroll));
					oThumb.obj.css(sDirection, iScroll / oScrollbar.ratio);
					oContent.obj.css(sDirection, -iScroll);
					oWrapper.data('dp-scroll-pos', iScroll);

					if (iScroll == oContent[options.axis] - oViewport[options.axis]) {
						oScrollbar.obj.addClass('stuck-btm');
					} else {
						oScrollbar.obj.removeClass('stuck-btm');
					}

					oEvent = $.event.fix(oEvent);
					oEvent.preventDefault();

					// For inner scrollable areas. Dont want scroll
					// to bubble to containing scrollable area too
					if (!oScrollbar.obj.is('.disable') /*&& origScroll > 0 && origScroll < oViewport[options.axis]*/) {
						oEvent.stopPropagation();
					}

					oWrapper.trigger('dp_scroll');
				};
			};

			function end(oEvent){
				oWrapper.removeClass('stuck');
				$(document).unbind('mousemove', drag);
				$(document).unbind('mouseup', end);
				oThumb.obj.unbind('mouseup', end);
				oTrack.obj.removeClass('dragging');

				if (iScroll == oContent[options.axis] - oViewport[options.axis]) {
					oScrollbar.obj.addClass('stuck-btm');
				} else {
					oScrollbar.obj.removeClass('stuck-btm');
				}

				if (fns && fns.length) {
					for (var i = 0; i < fns.length; i++) {
						//oViewport.obj[0].removeEventListener("MSPointerDown", fns[i]);
						document.removeEventListener("MSPointerMove", fns[i]);
						document.removeEventListener("MSPointerUp", fns[i]);
					}
					fns = [];
				}

				isTouchEvent = false;

				return false;
			};
			function drag(oEvent){
				oWrapper.removeClass('stuck');
				if(!(oContent.ratio >= 1)){
					if(isTouchEvent) {
						iPosition.now = Math.min((oTrack[options.axis] - oThumb[options.axis]), Math.max(0, (iPosition.start - ((sAxis ? oEvent.pageX : oEvent.pageY) - iMouse.start))));
					} else {
						iPosition.now = Math.min((oTrack[options.axis] - oThumb[options.axis]), Math.max(0, (iPosition.start + ((sAxis ? oEvent.pageX : oEvent.pageY) - iMouse.start))));
					}
					iScroll = iPosition.now * oScrollbar.ratio;
					oContent.obj.css(sDirection, -iScroll);
					oThumb.obj.css(sDirection, iPosition.now);
					oWrapper.data('dp-scroll-pos', iScroll);

					if (iScroll >= oContent[options.axis] - oViewport[options.axis]) {
						oScrollbar.obj.addClass('stuck-btm');
					} else {
						oScrollbar.obj.removeClass('stuck-btm');
					}

					oWrapper.trigger('dp_scroll');
				}
				return false;
			};
			return this.initialize();
		};
	})(jQuery);
}