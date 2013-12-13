Orb.createNamespace('DeskPRO.Admin.ElementHandler');

DeskPRO.Admin.ElementHandler.WelcomeBox = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var self = this;

		var formWrap = $('#admin_contact_form');
		var messageTxt = formWrap.find('textarea.message');
		var emailTxt   = formWrap.find('input.email_address');
		var submitUrl  = formWrap.data('submit-url');

		var profilePicture = this.el.find('.welcome-box .picture');
		profilePicture.click(function() {
			var overlay = new DeskPRO.UI.Overlay({
				contentMethod: 'ajax',
				contentAjax: { url: BASE_URL + 'admin/misc/change-picture' },
				zIndex: 40000, // Above floating people windows
				onAjaxDone: function() {
					var wrapper = overlay.getWrapper(),
						form = wrapper.find('form');

					var idbase = 'ed_' + Orb.uuidRand();
					$('.template-upload', wrapper).attr('id', idbase + 'up');
					$('.template-download', wrapper).attr('id', idbase + 'down');
					wrapper.fileupload({
						url: BASE_URL + 'admin/misc/accept-upload',
						dropZone: wrapper,
						autoUpload: true,
						uploadTemplateId: idbase + 'up',
						downloadTemplateId: idbase + 'down',
						formData: [{
							name: 'is_image',
							value: 1
						}],
						completed: function() {
							$('.files .in', wrapper).css('height', 'auto');
						}
					}).bind('fileuploadstart', function() {
						$('p.explain', wrapper).hide();
					}).bind('fileuploadadd', function() {
						$('.files', wrapper).empty();
						$('input[name=set_pic_opt]', wrapper).each(function() {
							$(this).attr('checked', $(this).val() == 'newpic');
						})
					});

					wrapper.on('click', '.save-trigger', function(e) {
						e.preventDefault();

						var formData = form.serializeArray();

						$.ajax({
							url: BASE_URL + 'admin/misc/change-picture/save',
							type: 'POST',
							dataType: 'json',
							data: formData
						});

						overlay.close();

						var newImgSrc = $('img.pic-new', wrapper).data('setted-size');
						if (newImgSrc) {
							profilePicture.find('img').attr('src', newImgSrc);
						}
					});
				}
			});
			overlay.open();
		});

		messageTxt.one('focus', function() {
			$(this).animate({height: '100px', width: '622px'}, 500);
			formWrap.find('button.send-mock').hide();
			formWrap.find('button.send-trigger').fadeIn(250);
			formWrap.find('.email-addy-wrap').fadeIn(250);
		});

		formWrap.find('.default-addy').on('click', function() {
			$(this).hide();
			formWrap.find('.input-addy').show().find('input').focus();
		});

		formWrap.find('button.send-trigger').on('click', function(ev) {

			ev.preventDefault();

			if (!messageTxt.val().trim()) {
				alert('Please enter a message');
				return;
			}

			var me = $(this);
			var load = formWrap.find('.send-loading');

			me.hide();
			load.show();
			$.ajax({
				url: submitUrl,
				type: 'POST',
				data: {
					message: messageTxt.val(),
					email_address: emailTxt.val()
				},
				complete: function() {
					me.show();
					load.hide();
				},
				success: function() {
					formWrap.find('.form-input').fadeOut(600, function() {
						formWrap.find('.form-success').fadeIn(300);
					});
				}
			});
		});
	}
});