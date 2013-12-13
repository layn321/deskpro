Orb.createNamespace('DeskPRO.User.ElementHandler');

DeskPRO.User.ElementHandler.CommentFormLogin = new Orb.Class({
	Extends: DeskPRO.User.ElementHandler.ElementHandlerAbstract,

	init: function() {
		var self = this;

		window.DP_LOGIN_NOTIFY = function() {
			$('#comments_form_login_area').empty();
			$.ajax({
				url: BASE_URL + '_misc/comment-form-login-partial',
				dataType: 'html',
				success: function(html) {
					$('#comments_form_login_area').html(html);
				}
			});
		};

		this.el.find('.auth-popup').click(function(ev) {
			ev.preventDefault();
			ev.stopPropagation();
			ev.stopImmediatePropagation();

			window.open($(this).attr('href'),'dpauth','width=600,height=400,location=0,menubar=0,scrollbars=0,status=0,toolbar=0,resizable=0');
		});
	}
});
