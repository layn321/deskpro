Orb.createNamespace('DeskPRO.Agent.PageHelper');

DeskPRO.ElementHandler.ListRadio = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var self = this;

		this.list = $('ul, ol', this.el).first();
		this.list.on('click', 'li', function() {
			$('li', self.list).removeClass('on');
			$(this).addClass('on');

			self.el.trigger('listradiochange', [$(this).data('value'), $(this), this]);
		});
	}
});
