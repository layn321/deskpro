Orb.createNamespace('DeskPRO.Agent.ElementHandler');

DeskPRO.Agent.ElementHandler.FirstLogin = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	initPage: function() {
		var self = this;
		var el = this.el;
		window.FIRST_LOGIN_WINDOW = this;

		DeskPRO_Window.util.fileupload(this.el.find('.profile-picture'));
		this.el.find('.profile-picture').bind('fileuploadadd', function() {
			$('.files', el).empty();
		});


		$('<div style="position:absolute;top:0;left:0;right:0;bottom:0;z-index:51001;background-color:rgba(255,255,255,0.55);"></div>').appendTo('body');

		this.el.find('.save-trigger').on('click', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			var name = el.find('input.name').val().trim();
			if (!name) {
				alert('Please enter your name');
				return;
			}

			$(this).after($('<i class="flat-spinner"></i>'));
			$(this).hide();

			$.ajax({
				url: el.find('form').attr('action'),
				type: 'POST',
				data: {
					new_blob_id: el.find('input.new_blob_id').val(),
					name: el.find('input.name').val(),
					timezone: el.find('select.timezone').val()
				},
				dataType: 'json',
				complete: function() {
					window.location.reload(false);
				}
			});
		});

		DP.select(this.el.find('select'));

		this.updatePositions();
		this.el.show();
	},

	updatePositions: function() {
		var left = ($(window).width() / 2) - (this.el.outerWidth() / 2);
		this.el.css('left', left);

		this.el.show();
	}
});
