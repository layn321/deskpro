Orb.createNamespace('DeskPRO.Agent.PageFragment.MediaManagerPage');

DeskPRO.Agent.PageFragment.MediaManagerPage.Browse = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'mediawin_browse';
	},

	initPage: function(wrapper) {
		this.wrapper = wrapper;

		wrapper.on('click', '.insert-trigger', function(ev) {
			ev.preventDefault();

			if (!window.MEDIA_MANAGER_WINDOW || !MEDIA_MANAGER_WINDOW.boundEditor || !MEDIA_MANAGER_WINDOW.boundEditor.selection) {
				return;
			}

			var btn = $(this);

			if (btn.data('is-image') == '1') {
				MEDIA_MANAGER_WINDOW.boundEditor.selection.setContent('<img src="' + btn.data('download-url') + '" />');
			} else {
				MEDIA_MANAGER_WINDOW.boundEditor.selection.setContent('<a href="' + btn.data('download-url') + '">' + btn.data('file-name') + '</a>');
			}

			MEDIA_MANAGER_WINDOW.close();
		});
	}
});