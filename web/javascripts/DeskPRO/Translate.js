Orb.createNamespace('DeskPRO');

/**
 * A translate class that works like the PHP Translate class.
 */
DeskPRO.Translate = new Orb.Class({
	/**
	 * @param {Function} loader A function loader to return a phrase when it isnt set locally
	 */
	initialize: function(loader) {
		this.phrases = {};
		this.loader = loader || function(id) { return '['+id+']'; };

		if (window.DESKPRO_LANG) {
			this.phrases = window.DESKPRO_LANG;
		}
	},


	/**
	 * Get the raw phrase text, or load it from the loader if it hasnt been loaded yet
	 *
	 * @param {String} phrase_name
	 * @return {String}
	 */
	getPhraseText: function(phrase_name) {
		if (typeof this.phrases[phrase_name] == 'undefined') {
			this.phrases[phrase_name] = this.loader(phrase_name);
		}

		if (typeof this.phrases[phrase_name] == 'undefined') {
			return null;
		}

		return this.phrases[phrase_name];
	},


	/**
	 * Get a phrase and put in var replacements
	 *
	 * @param {String} phrase_name
	 * @param {Object} vars
	 * @return {String}
	 */
	phrase: function(phrase_name, vars, raw) {
		var text = this.getPhraseText(phrase_name) || '[' + phrase_name + ']';
		return this.phraseWithString(text, vars, raw);
	},


	/**
	 * @param {String} text
	 * @param {Object} vars
	 * @param {Boolean} raw
	 */
	phraseWithString: function(text, vars, raw) {
		if (vars) {
			if (typeof vars.count != 'undefined') {
				text = this.choosePlural(text, vars.count);
			}

			Object.each(vars, function (value, key) {
				var re = new RegExp('\{\{\s*' + Orb.regexQuote(key) + '\s*\}\}' , 'g');

				if (raw) {
					text = text.replace(re, value);
				} else {
					text = text.replace(re, Orb.escapeHtml(value));
				}
			});
		}

		return text;
	},


	/**
	 * Check if we know about a specific phrase
	 *
	 * @param {String} phrase_name
	 * @return {Boolean}
	 */
	hasPhrase: function(phrase_name) {
		if (this.getPhraseText(phrase_name) === null) {
			return false;
		}

		return true;
	},


	/**
	 * Chooses a plural from the string <var>text</var> based on <var>number</var>.
	 *
	 * This is a JS port of the Symfony MessageSelector class.
	 * Syntax described: http://symfony.com/doc/current/book/translation.html#pluralization
	 *
	 * @param text
	 * @param number
	 */
	choosePlural: function(text, number) {
		var parts = text.split('|');
		if (number == 0 || number != 1) {
			return parts[1];
		} else {
			return parts[0];
		}
	},

	testInterval: function(number, interval) {
		var x = 0
		var number = parseInt(number);
		interval = interval.trim();

		var leftDelimIndex  = 1;
		var leftIndex       = 2;
		var rightIndex      = 3;
		var rightDelimIndex = 4;

		var intervalRe = /({\s*(\-?\d+[\s*,\s*\-?\d+]*)\s*})|([\[\]])\s*(-Inf|\-?\d+)\s*,\s*(\+?Inf|\-?\d+)\s*([\[\]])/;

		var match = interval.exec(intervalRe);
		if (!match) {
			if (window.console && window.console.error) {
				console.error("Invalid interval: %s", interval);
			}
			return 'invalid interval';
		}

		if (matches[1]) {
			var nums = matches[2].split(',');
			for (x = 0; x < nums.length; x++) {
				if (number == parseInt(nums[x])) {
					return true;
				}
			}
		} else {
			var leftNum    = match[leftIndex];
			var rightNum   = match[rightIndex];

			if (leftNum == '-Inf') leftNum = Number.NEGATIVE_INFINITY;
			else if (leftNum == 'Inf') leftNum = Number.POSITIVE_INFINITY;
			if (rightNum == '-Inf') rightNum = Number.NEGATIVE_INFINITY;
			else if (rightNum == 'Inf') rightNum = Number.POSITIVE_INFINITY;

			var leftDelim  = match[leftDelimIndex];
			var rightDelim = match[rightDelimIndex];

			if (
				( ('[' == leftDelim && number >= leftNum) || number > leftNum )
				&& ( (']' == rightDelim && number >= rightNum) || number > rightNum )
			)
			{
				return true;
			}
		}

		return false;
	}
});