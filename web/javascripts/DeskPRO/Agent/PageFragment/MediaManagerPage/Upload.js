Orb.createNamespace('DeskPRO.Agent.PageFragment.MediaManagerPage');

DeskPRO.Agent.PageFragment.MediaManagerPage.Upload = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'mediawin_upload';
	},

	initPage: function(wrapper) {
		var self = this;
		this.wrapper = wrapper;

		var el = wrapper.find('.manager-window-content');

		DeskPRO_Window.util.fileupload(el, {
			page: this,
			saveMedia: 1,
			uploadTemplate: $('.template-upload', el),
			downloadTemplate: $('.template-download', el)
		}).bind('fileuploadstart', function() {
			wrapper.find('.upload-control').hide();
		}).bind('fileuploadadd', function(e,data) {
			$('.files', wrapper).empty();
		}).bind('fileuploadcompleted', function(e,data) {
			wrapper.find('.insert-trigger').each(function() {
				var btn = $(this);
				btn.on('click', function(ev) {
					ev.preventDefault();

					if (!window.MEDIA_MANAGER_WINDOW || !MEDIA_MANAGER_WINDOW.boundEditor || !MEDIA_MANAGER_WINDOW.boundEditor.selection) {
						return;
					}

					if (btn.data('is-image') == '1') {
						MEDIA_MANAGER_WINDOW.boundEditor.selection.setContent('<img src="' + btn.data('download-url') + '" />');
					} else {
						MEDIA_MANAGER_WINDOW.boundEditor.selection.setContent('<a href="' + btn.data('download-url') + '">' + btn.data('file-name') + '</a>');
					}

					MEDIA_MANAGER_WINDOW.close();
				});
			});

			// For some reason IE10 will not process click events on the buttons
			// until some sort of action is done. E.g., highlighting some random text or focusing the input box.
			// Focusing the input box is the simplest fix that doesn't alter any behaviour
			wrapper.find('.file-url').focus();
		});

		wrapper.on('click', '.cancel-trigger', function(ev) {
			ev.preventDefault();

			wrapper.find('.upload-control').show();
			wrapper.find('.files').hide();
			self.mediaWindow.reloadTab('upload');
		});
	}
});