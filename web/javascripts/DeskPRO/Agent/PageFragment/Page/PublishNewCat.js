Orb.createNamespace('DeskPRO.Agent.PageFragment.Page');

DeskPRO.Agent.PageFragment.Page.PublishNewCat = new Orb.Class({

	Extends: DeskPRO.Agent.PageFragment.Basic,

	initializeProperties: function() {
		this.parent();
		this.TYPENAME = 'pubnewcat';
		this.allowDupe = true;
	},

	closeSelf: function() {
		this.fragmentOverlay.close();
	},

	initPage: function(el) {
		var self = this;
		this.wrapper = el;
		this.el = el;
		this.contentWrapper = this.wrapper.children('.layout-content').attr('id', Orb.getUniqueId());
		this.parent(el);

		var form = el.find('form');
		form.on('submit', function(ev) {
			Orb.cancelEvent(ev);
		});

		var footer = el.find('footer');
		footer.find('.submit-trigger').on('click', function(ev) {
			Orb.cancelEvent(ev);
			footer.addClass('dp-loading-on');

			var postData = form.serializeArray();

			$.ajax({
				data: postData,
				url: form.attr('action'),
				type: 'POST',
				complete: function() {
					DeskPRO_Window.sections.publish_section.reload();
					self.closeSelf();
				}
			});
		});
	}
});