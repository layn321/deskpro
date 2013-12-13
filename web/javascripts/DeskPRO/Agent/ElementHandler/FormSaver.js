Orb.createNamespace('DeskPRO.Agent.ElementHandler');

DeskPRO.Agent.ElementHandler.FormSaver = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var self = this;
		this.textarea = $('textarea', this.el);
                this.inout = $('input:text', this.el); // Added by Abdullah Kiser inorder to catch the touch event.
		this.list = null;
		this.resultHtmlKey = this.el.data('form-result-html-key') || 'html';

		// Optionally append ajax result to a list
		if (this.el.data('form-list-selector')) {
			this.list = this.el.closest(this.el.data('form-list-selector'));
		}

		DP.console.log(this.list);

		this.url = this.el.data('form-save-url');

		this.statusSave   = $('header .save', this.el);
		this.statusSaved  = $('header .saved', this.el);
		this.statusSaving = $('header .is-loading', this.el);

		this.statusSave.on('click', function(ev) {
			ev.preventDefault();
			self.save();
		});

		this.textarea.on('change', this.touch.bind(this));
		this.textarea.on('keypress', this.touch.bind(this));

                // Added by Abdullah Kiser inorder to catch the touch event.
                this.inout.on('change', this.touch.bind(this));
		this.inout.on('keypress', this.touch.bind(this));


		this.countEl = null;
		if (this.el.data('form-count-el')) {
			this.countEl = $(this.el.data('form-count-el'));
		}
	},

	touch: function() {
		this.statusSave.show();
		this.statusSaved.hide();
		this.statusSaving.hide();
	},

	save: function() {
		this.statusSave.hide();
		this.statusSaved.hide();
		this.statusSaving.show();

		var formEls  = $('input, textarea, select', this.el);
		var postData = formEls.serializeArray();

		var doSend = true;
		var checkBlankEls = formEls.filter('[data-not-blank]').each(function(){
			if ($(this).val().trim() === '') {
				doSend = false;
				return false;
			}
		});

		if (!doSend) {
			this.statusSave.hide();
			this.statusSaved.hide();
			this.statusSaving.hide();
			return;
		}


		$.ajax({
			url: this.url,
			type: 'POST',
			data: postData,
			dataType: 'json',
			context: this,
			complete: function() {
				this.statusSave.hide();
				this.statusSaved.show();
				this.statusSaving.hide();

				window.setTimeout((function() {
					this.statusSaved.fadeOut('slow');
				}).bind(this), 1000);
			},
			success: function(data) {

				if (data.error) {
					return;
				}

				if (this.list) {
					var newRow = $(data[this.resultHtmlKey]);
					DeskPRO_Window.initInterfaceServices(newRow);

					if (this.el.parent().get(0) == this.list.get(0)) {
						newRow.insertBefore(this.el);
					} else {
						this.list.append(newRow);
					}
					this.textarea.val('');
				}

				if (this.countEl) {
					DeskPRO_Window.util.modCountEl(this.countEl, '+');
				}
			}
		});
	}
});
