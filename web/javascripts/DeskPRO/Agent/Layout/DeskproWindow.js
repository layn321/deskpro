Orb.createNamespace('DeskPRO.Agent.Layout');

DeskPRO.Agent.Layout.DeskproWindow = Orb.Class({
	Implements: [Orb.Util.Events],

	initialize: function() {

		var self = this;

		// This is the width of nav pane + overview pane,
		// aka where the listpane starts
		this.LEFT_START = 215;

		// Where the center section (where all cols are embedded) starts
		this.CENTER_START = 55;

		this.listWidthRatio = 0.40;

		this.enableHashUpdate = true;

		window.onresize = function() {
			self.doResize(true);
		};

		var listSizer = $('#dp_list_resizer').draggable({
			axis: 'x'
		}).on('dragstop', function() {
			var pos = parseInt(listSizer.css('left').replace(/px/, ''));
			self.doResize();
		});
	},

	doResize: function(widthCalc) {
		var rightHide = $('#dp_right_collapsed');
		var listSizer = $('#dp_list_resizer');
		var paneVis = DeskPRO_Window.paneVis;

		var newWidth = $(window).width();

		var totalWidth = newWidth - this.LEFT_START - this.CENTER_START;

		var listWidth;
		if (widthCalc) {
			listWidth = totalWidth * this.listWidthRatio;
			if (typeof Modernizr != 'undefined' && Modernizr.ipad) {
				if (listWidth < 370) {
					listWidth = 370;
				}
			} else {
				if (listWidth < 370) {
					listWidth = 370;
				}
			}
		} else {
			listWidth = parseInt(listSizer.css('left').replace(/px/, '')) - this.LEFT_START;
			this.listWidthRatio = listWidth / totalWidth;
		}

		$('#dp_list').width(listWidth);
		$('#dp_omnibox_wrap').width(listWidth-1); // -1 for border
		$('#dp_omnibox').width(listWidth-56); // -1 for border
		$('#dp_content').css('left', this.LEFT_START + listWidth + 1); //+1 for border

		$('.with-scroll-handler').each(function() {
			if ($(this).data('scroll_handler')) {
				$(this).data('scroll_handler').updateSize();
			}
		});

		if (!paneVis.tabs) {
			var rightEdge = 0;

			if (rightHide.find('li')[0]) {
				rightEdge = 26;
				rightHide.show();
			} else {
				rightHide.hide();
			}

			$('#dp_content').hide();
			$('#dp_list').css({
				width: 'auto',
				right: rightEdge
			});
		} else {
			rightHide.hide();
			$('#dp_content').show();
			$('#dp_list').css({
				right: 'auto'
			});
		}

		var left = 0;
		if (!paneVis.source) {
			$('#dp_source').hide();
			$('#dp_center').css('left', 55);
		} else {
			$('#dp_source').show();
			$('#dp_nav').show();
			$('#dp_center').css('left', 55);
			left += 215;
		}

		if (!paneVis.list) {
			$('#dp_list').hide();
			listSizer.hide();
		} else {
			$('#dp_list').show();
			listSizer.show();
			if (!paneVis.source) {
				$('#dp_list').css('left', 0);
				left += listWidth;
			} else {
				$('#dp_list').css('left', this.LEFT_START);
				left += listWidth;
			}
		}

		if (left) {
			$('#dp_content').css('left', left);
		} else {
			$('#dp_content').css('left', 0);
		}

		listSizer.css('left', left-2);

		var body = $('body');
		if (paneVis.source) body.addClass('panevis-source-on').removeClass('panevis-source-off');
		else body.removeClass('panevis-source-on').addClass('panevis-source-off');

		if (paneVis.list) body.addClass('panevis-list-on').removeClass('panevis-list-off');
		else body.removeClass('panevis-list-on').addClass('panevis-list-off');

		if (paneVis.tabs) body.addClass('panevis-tabs-on').removeClass('panevis-tabs-off');
		else body.removeClass('panevis-tabs-on').addClass('panevis-tabs-off');

		if (this.enableHashUpdate) {
			DeskPRO_Window.updateWindowUrlFragment();
		}

		this.fireEvent('resized', [this]);
	}
});
