function DpTplSearch(template_set) {
	var $btn     = $('#tplsearch_trigger');
	var $terms   = $('#tplsearch_term');
	var $spin    = $('#tplsearch_loading');
	var $status  = $('#tplsearch_status');
	var runningAjax = null;

	$btn.on('click', function(ev) {
		ev.preventDefault();

		if (runningAjax) {
			runningAjax.abort();
			runningAjax = null;
		}

		var postData = {
			template_set: template_set,
			term: $('#tplsearch_term').val().trim()
		};

		$status.empty().text('').hide();
		$spin.hide();

		if (!postData.term) {
			$('tr.search-match').removeClass('search-match');
			return;
		}

		$spin.show();
		runningAjax = $.ajax({
			url: BASE_URL + 'admin/templates/search.json',
			data: postData,
			dataType: 'json',
			complete: function() {
				$spin.hide();
			},
			error: function() {
				$status.empty().hide();
			},
			success: function(data) {
				$('tr.search-match').removeClass('search-match');

				if (data.matches && data.matches.length) {
					if (data.matches.length == 1) {
						$status.empty().text('1 match found');
					} else {
						$status.empty().text(data.matches.length + ' matches found');
					}

					$status.show();

					Array.each(data.matches, function(tpl) {
						var tpl_match = tpl.replace(/[.:]/g, '_').toLowerCase();
						$('tr.' + tpl_match).addClass('search-match');
					});
				} else {
					$status.empty().text('No matches found.');
				}
			}
		});
	});
};