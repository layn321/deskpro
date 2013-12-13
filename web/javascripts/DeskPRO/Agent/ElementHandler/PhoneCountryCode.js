Orb.createNamespace('DeskPRO.Agent.ElementHandler');

DeskPRO.Agent.ElementHandler.PhoneCountryCode = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	initPage: function() {
		this.optionsList = $(this.el.data('options-list'));

		var options = [];
		$('li', this.optionsList).each(function(){
			options.push({
				country_code: $(this).data('country-code'),
				value: $(this).data('calling-code'),
				label: $(this).html()
			});
		});

		var countryCodeSel = $('.country-code', this.el);
		var flagEl = $('.icon-flag', this.el);

		var updateIcon = function() {
			if (countryCodeSel.is('.cancel-next-update')) {
				countryCodeSel.removeClass('cancel-next-update');
				return;
			}

			var val = countryCodeSel.val().trim();
			var cc = null;
			if (val) {
				Array.each(options, function(opt) {
					if ((opt.value+'') == (val+'')) {
						cc = opt.country_code;
					}
				});
			}

			if (cc) {
				flagEl.attr('class', '').addClass('icon-flag').addClass('icon-flag-' + cc.toLowerCase()).show();
			} else {
				flagEl.hide();
			}

			countryCodeSel.autocomplete('close');
		};

		countryCodeSel.autocomplete({
			minLength: 0,
			source: function(req, callback) {
				var term = req.term.trim();

				if (term === '') {
					return options;
				}

				var ret = [];
				Array.each(options, function(opt) {
					if ((opt.value+'').indexOf(term) === 0) {
						ret.push(opt);
					}
				});
				callback(ret);
			},
			delay: 0
		})
		.on('change', updateIcon)
		.data( "autocomplete" )._renderItem = function(ul, item) {
			return $("<li></li>")
				.addClass('ui-menu-item')
				.data("item.autocomplete", item)
				.html(item.label)
				.appendTo(ul);
		};

		updateIcon();
	}
});
