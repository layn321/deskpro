(function() {
Orb.createNamespace('DeskPRO.Report.ElementHandler.Builder');

var collapseState = {};

DeskPRO.Report.ElementHandler.Builder.ListCollapse = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var el = this.el,
			cookieId = el.data('cookie'),
			statusEl = el.find('span:first'),
			targetEl = el.data('target') ? $(el.data('target')) : el.next();

		if (cookieId && typeof collapseState[cookieId] == 'undefined') {
			collapseState[cookieId] = ($.cookie(cookieId)+'').split(',');
		}

		var updateStatusFunc = function() {
			if (targetEl.hasClass('collapsed')) {
				statusEl.addClass('collapsed');
			} else {
				statusEl.removeClass('collapsed');
			}
		};

		var updateCollapsed = function(collapsed) {
			if (!cookieId) {
				return;
			}

			collapseState[cookieId] = collapsed;
			$.cookie(cookieId, collapsed.join(','), {expires: 7});
		};

		var getCollapsed = function() {
			if (!cookieId || typeof collapseState[cookieId] == 'undefined') {
				return [];
			} else {
				return collapseState[cookieId];
			}
		};

		var getCollapseId = function() {
			return targetEl.data('collapse-id');
		};

		var findCollapseEntry = function() {
			var id = getCollapseId(), collapsed = getCollapsed();

			if (!id || !cookieId) {
				return false;
			}

			for (var i = 0; i < collapsed.length; i++) {
				if (collapsed[i] == id) {
					return i;
				}
			}

			return false;
		};

		el.click(function() {
			var id = getCollapseId(), collapsed = getCollapsed();

			if (!id || !cookieId) {
				targetEl.toggleClass('collapsed');
				updateStatusFunc();
				return;
			}

			var entry = findCollapseEntry();

			if (entry !== false) {
				collapsed.splice(entry, 1);
				targetEl.removeClass('collapsed');
			} else {
				collapsed.push(id);
				targetEl.addClass('collapsed');
			}

			updateCollapsed(collapsed);
			updateStatusFunc();
		});

		if (findCollapseEntry() !== false) {
			targetEl.addClass('collapsed');
		}
		updateStatusFunc();
	}
});
})();
