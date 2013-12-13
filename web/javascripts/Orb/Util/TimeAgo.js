Orb.createNamespace('Orb.Util');

Orb.Util.TimeAgo = {

	_watchTimer: null,

	/**
	 * How often to update the elements
	 */
	refreshPeriod: 60000,//1min

	phrases: {
		'sec_less': '1 second',
		'sec':      '1 second',
		'secs':     '{0} seconds',
		'min':      '1 minute',
		'mins':     '{0} minutes',
		'hour':     '1 hour',
		'hours':    '{0} hours',
		'day':      '1 day',
		'days':     '{0} days',
		'week':     '1 week',
		'weeks':    '{0} weeks',
		'month':    '1 month',
		'months':   '{0} months',
		'year':     '1 year',
		'years':    '{0} years',
		'ago':      'ago'
	},


	/**
	 * Get the full words for a given date.
	 *
	 * @param date
	 */
	get: function(date, ago, relativeCutoff, title) {
		return this.getForMs(this.getDateDiff(date), ago, relativeCutoff, title);
	},


	/**
	 * Apply to an array of elements.
	 *
	 * @param $els
	 */
	applyToElements: function(els) {

		var self = this;

		els.each(function(el) {
			$(el).addClass('timeago-auto-update');
			self.refreshElements([el]);
		});

		if (this._watchTimer === null) {
			this._watchTimer = window.setInterval(this.refreshElements.bind(this), this.refreshPeriod);
		}
	},


	/**
	 * APply to a jQuery collection
	 *
	 * @param $els
	 */
	applyToJquery: function($els) {
		this.applyToElements($els.toArray());
	},


	refreshElements: function(els) {
		if (!els) els = $('.timeago-auto-update').toArray();

		var self = this;

		els.each(function(el) {

			// Could be removed, just skip it
			// might be reinserted later
			if (!el || !el.parentNode) {
				return;
			}

			el = $(el);
			el.addClass('with-timeago');

			if (!el.data("timeago")) {

				var isTime = el.get(0).tagName.toLowerCase() == 'time';
				var iso8601 = isTime && el.attr('datetime') ? el.attr('datetime') : el.attr('title');

				if (!iso8601 || typeof iso8601 != 'string') {
					return;
				}

				var s = iso8601.replace(/\.\d\d\d+/,""); // remove milliseconds
				s = s.replace(/-/,"/").replace(/-/,"/");
				s = s.replace(/T/," ").replace(/Z/," UTC");
				s = s.replace(/([\+-]\d\d)\:?(\d\d)/," $1$2"); // -04:00 -> -0400

				var titleText = $.trim(el.text());
				if (titleText.length > 0) el.attr("title", titleText);

				el.data("timeago", { datetime: new Date(s), relativeCutoff: el.data('relative-cutoff') || 0, title: titleText });
			}

			var data = el.data('timeago');
			if (!isNaN(data.datetime)) {
				var ago = true;
				if (el.data('timeago-no-ago') == "1") {
					ago = false;
				} else {
					if (data.datetime > (new Date())) {
						ago = false;
					}
				}
                var text = self.get(data.datetime, ago, data.relativeCutoff, data.title);
				el.text(text);
			}
		});
	},


	/**
	 * Get the relative date info for ms.
	 *
	 * @param int ms
	 */
	getRelativeInfo: function(ms) {
		var secs = 0, mins = 0, hours = 0, days = 0, years = 0;

		secs = parseInt(ms / 1000);

		years = parseInt(secs / 29030400);
		secs -= years * 29030400;

		days = parseInt(secs / 86400);
		secs -= days * 86400;

		hours = parseInt(secs / 3600);
		secs -= hours * 3600;

		mins = parseInt(secs / 60);
		secs -= mins * 60;

		return {
			'secs':    secs,
			'mins':    mins,
			'hours':   hours,
			'days':    days,
			'years':   years
		}
	},


	/**
	 * Get the full words for given ms.
	 *
	 * @param ms
	 */
	getForMs: function(ms, ago, relativeCutoff, title) {
		var info = this.getRelativeInfo(ms);
		var total_secs = parseInt(ms / 1000);

		if (title && relativeCutoff && total_secs > relativeCutoff) {
			return title;
		}

		// less than 60 secons: 20 seconds
		if (total_secs < 60) {
			return this.getPhraseFor('sec', total_secs, ago);

		// less than 120 minutes: 20 minutes
		} else if (total_secs <= 7200) {
			return this.getPhraseFor('min', info.mins + (info.hours * 60), ago);

		// less than 24 hours: 2 1/2 hours
		} else if (total_secs <= 86400) {
			var fraction;
			if (info.mins <= 15) {
				fraction = '';
			} else if (info.mins <= 30) {
				fraction = '¼';
			} else if (info.mins <= 45) {
				fraction = '½';
			} else if (info.mins <= 60) {
				fraction = '¾';
			}

			var phrase_num = info.hours;
			var phrase_hours = info.hours + '';

			// Inc to override plural for ex 1 1/2 hours
			if (fraction !== '') {
				phrase_num += 1;
				phrase_hours = (info.hours || 1) + '' + fraction;
			}

			return this.getPhraseFor('hour', phrase_hours, ago, phrase_num);

		// less than 3 days: 2 days 2 hours
		} else if (total_secs <= 259200) {
			var phrase_days = this.getPhraseFor('day', info.days, ago);
			if (info.hours > 0) {
				phrase_days += ' ' + this.getPhraseFor('hour', info.hours, ago);
			}

			return phrase_days;

		// less than 1 month: 5 days
		} else if (total_secs <= 2419200) {
			return this.getPhraseFor('day', info.days, ago);

		// less than 3 months: 5 weeks
		} else if (total_secs <= 7257600) {
			var weeks = parseInt(info.days / 7);
			return this.getPhraseFor('week', weeks, ago);

		// less than a year: 8 months
		} else if (total_secs <= 29030400) {
			var months = parseInt(info.days / 30);
			return this.getPhraseFor('month', months, ago);

		// less than 5 years: 1 year 3 months
		} else if (total_secs <= 145152000) {
			var phrase_years = this.getPhraseFor('year', info.years, ago);
			if (info.months > 0) {
				phrase_years += ' ' + this.getPhraseFor('month', info.months, ago);
			}

			return phrase_years;

		// more than 5 years: 8 years
		} else {
			return this.getPhraseFor('year', info.years, ago);
		}
	},


	/**
	 * Get the difference in ms or s for a date and current date.
	 *
	 * @param date
	 * @param bool secs True to return seconds instead of ms
	 */
	getDateDiff: function(date, secs) {
		var now_ts  = (new Date()).getTime();
		var date_ts = date.getTime();
		var diff;

		if (now_ts < date_ts) {
			diff = date_ts - now_ts;
		} else {
			diff = now_ts - date_ts;
		}

		if (diff < 0) {
			return 0;
		}

		if (secs) {
			diff /= 1000;
		}
		return diff;
	},

	/**
	 * Get the phrase for a time denomination
	 *
	 * @param string type
	 * @param int num
	 */
	getPhraseFor: function(type, num, ago, num_exact) {

		if (!num_exact) {
			num_exact = num;
		}

		if (window.Orb_Util_TimeAgo_getPhraseFor) {
			if (num < 0) {
				num = 0;
			}

			return window.Orb_Util_TimeAgo_getPhraseFor(type, num, ago);
		}

		if (type == 'sec' && num_exact <= 0) {
			return this.phrases['sec_less'];
		}

		var k = type;
		if (num_exact != 1) {
			k += 's';
		}

		var phrase = this.phrases[k];

		if (ago) {
			phrase += ' ' + this.phrases['ago'];
		}

		phrase = phrase.replace(/\{0\}/g, num);

		return phrase;
	}
};

if (jQuery) {
	jQuery.fn.timeago = function() {
		Orb.Util.TimeAgo.applyToJquery(this);
		return this;
	};
}
