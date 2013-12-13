Orb.createNamespace('DeskPRO.Agent');

DeskPRO.Agent.TextSnippetAjaxDriver = new Orb.Class({

	Extends: DeskPRO.BasicWindow,

	initialize: function(typename) {
		this.typename = typename;
		this.driverName = 'ajax';
		this.loadData();
	},

	/**
	 * Gets (or reloads) the plain template used to construct a new shell
	 *
	 * @param reload
	 */
	getWidgetShellTemplate: function(reload) {
		var id = this.typename + '_snippet_shell_tpl';
		var el = document.getElementById(id);
		if (reload || !el) {
			$.ajax({
				url: BASE_URL + 'agent/text-snippets/' + this.typename + '/widget-shell.txt',
				type: 'GET',
				dataType: 'text',
				success: function(txt) {
					if (el) {
						el.parentNode.removeChild(el);
					}

					var $el = $('<script type="text/x-deskpro-plain" id="'+id+'"/>');
					$el.html(txt);
					$el.appendTo('body');

					el = $el.get(0);
				}
			});
		}

		return DeskPRO_Window.util.getPlainTpl(el);
	},

	/**
	 * Preload data
	 */
	loadData: function() {
		// AJAX is on-demand, there is no preloading of data
	},


	/**
	 * Load snippets that match a certain criteria
	 *
	 * @param filter
	 * @param callback
	 * @param mutator
	 */
	loadSnippets: function(filter, callback, mutator) {
		var snippets = [];

		filter = filter || {};
		var categoryId   = filter.categoryId || 0;
		var filterString = filter.filterString || '';
		var languageId   = filter.languageId || 0;
		var page         = filter.page || 1;

		$.ajax({
			url: BASE_URL + 'agent/text-snippets/'+this.typename+'/filter.json',
			data: {
				category_id: categoryId,
				language_id: languageId,
				filter_string: filterString
			},
			type: 'GET',
			dataType: 'json',
			success: function(snippet_data) {
				var snippets = [];

				if (snippet_data && snippet_data.snippets) {
					if (mutator) {
						Array.each(snippet_data.snippets, function(s) {
							snippets.push(mutator(item));
						});
					} else {
						snippets = snippet_data.snippets;
					}
				}

				callback(snippets);
			}
		});
	},


	/**
	 * Fetches a specific snippet from the db
	 *
	 * @param id
	 * @param callback
	 */
	getSnippet: function(id, callback) {
		$.ajax({
			url: BASE_URL + 'agent/text-snippets/'+  this.typename + '/' + id + '.json',
			dataType: 'json',
			success: function(snippet) {
				callback(snippet.snippet);
			}
		});
	},


	/**
	 * Saves a snippet to the db.
	 *
	 * @param snippet
	 * @param callback
	 * @param error_callback
	 */
	saveSnippet: function(snippet, callback, error_callback) {
		// Encode for form
		var postData = [];
		postData.push({name: 'snippet_id', value: snippet.id || 0});
		postData.push({name: 'category_id', value: parseInt(snippet.category_id) || 0});
		for (var i = 0; i < snippet.title.length; i++) {
			postData.push({name: 'title['+snippet.title[i].language_id+']', value: snippet.title[i].value || ''});
		}
		for (var i = 0; i < snippet.snippet.length; i++) {
			postData.push({name: 'snippet['+snippet.snippet[i].language_id+']', value: snippet.snippet[i].value || ''});
		}

		postData.push({name: 'shortcut_code', value: snippet.shortcut_code});

		var snippetsDb = this.snippetsDb;

		$.ajax({
			url: BASE_URL+'agent/text-snippets/'+this.typename+'/'+(snippet.id||0)+'/save.json',
			type: 'POST',
			dataType: 'json',
			data: postData,
			content: this,
			error: function() {
				if (error_callback) error_callback();
			},
			success: function(data) {
				if (callback) callback(data.snippet);
			}
		});
	},


	/**
	 * Delete a snippet
	 *
	 * @param snippetId
	 * @param callback
	 * @param error_callback
	 */
	deleteSnippet: function(snippetId, callback, error_callback) {
		$.ajax({
			url: BASE_URL+'agent/text-snippets/'+this.typename+'/'+(snippetId||0)+'/delete.json',
			type: 'POST',
			dataType: 'json',
			content: this,
			error: function() {
				if (error_callback) error_callback(snippetId);
			},
			success: function(data) {
				if (callback) callback(snippetId);
			}
		});
	}
});