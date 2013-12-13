Orb.createNamespace('DeskPRO.Agent.PageFragment.Page.PersonHelper');

DeskPRO.Agent.PageFragment.Page.PersonHelper.ChangePic = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(page, options) {
		var self = this;

		this.options = {
			loadUrl: '',
			saveUrl: ''
		};

		this.setOptions(options);
		this.page = page;

		this.page.getEl('change_user_picture').on('click', this.open.bind(this));

		this.page.addEvent('destroy', this.destroy, this);
	},

	_initOverlay: function() {
		var self = this;
		if (this.overlay) {
			return;
		}

		this.wrapperEl = $('<div class="change-picture-overlay"><div class="overlay-content" style="width: 400px; height: 300px; "/><div>Loading...</div></div>');

		this.overlay = new DeskPRO.UI.Overlay({
			contentElement: this.wrapperEl,
			destroyOnClose: true,
			zIndex: 'top',
			onOverlayClosed: function() {
				self.overlay = null;
			}
		});

		$.ajax({
			url: this.options.loadUrl,
			type: 'GET',
			dataType: 'html',
			context: this,
			success: function(html) {
				if (this.overlay) {
					this.overlay.setContent($(html));
					this.wrapperEl = this.overlay.getWrapper();
					this._initControls();
				}
			}
		});
	},

	_initControls: function() {
		var wrapper = this.overlay.getWrapper();

		DeskPRO_Window.util.fileupload(wrapper, {
			page: this.page,
			uploadTemplate: $('.template-upload', wrapper),
			downloadTemplate: $('.template-download', wrapper),
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

		wrapper.on('click', '.save-trigger', this._doSave.bind(this));
	},

	_doSave: function(e) {
		e.preventDefault();

		var type = $('input[name=set_pic_opt]:checked', this.overlay.getWrapper()).val();

		var newImgSrc = null;
		var action = null;

		var formData = [];

		switch (type) {
			case 'nochange':
				this.close();
				return;

			case 'remove':
				formData.push({ name: 'action', value: 'delete-picture' });
				formData.push({ name: 'disable_picture', value: '1' });
				newImgSrc = $('img.pic-default', this.wrapperEl).attr('src');
				break;

			case 'gravatar':
				formData.push({ name: 'action', value: 'delete-picture' });
				newImgSrc = $('img.pic-gravatar', this.wrapperEl).attr('src');
				break;

			case 'newpic':
				formData.push({ name: 'action', value: 'set-picture' });
				var blobId = $('input.new_blob_id', this.wrapperEl).val();

				if (!blobId) {
					return;
				}

				formData.push({ name: 'blob_id', value: blobId });
				newImgSrc = $('img.pic-new', this.wrapperEl).data('setted-size');

				break;

			default:
				return;
		}

		$.ajax({
			url: this.options.saveUrl,
			type: 'POST',
			dataType: 'json',
			data: formData
		});

		this.page.getEl('picture_display').attr('src', newImgSrc);

		this.close();
	},

	open: function() {
		this._initOverlay();
		this.overlay.open();
	},

	close: function() {
		if (this.overlay) {
			this.overlay.destroy();
			this.overlay = null;
		}
	},

	destroy: function() {
		if (this.overlay) {
			this.overlay.destroy();
		}
	}
});
