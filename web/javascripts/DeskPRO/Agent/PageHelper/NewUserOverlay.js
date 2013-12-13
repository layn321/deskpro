Orb.createNamespace('DeskPRO.Agent.PageHelper');

DeskPRO.Agent.PageHelper.NewUserOverlay = new Class({

	Implements: [Options, Events],

	options: {
		contentEl: 'div.new-user-overlay',
		context: document,
		saveUrl: null,
		zIndex: 30001
	},

	contentEl: null,
	overlay: null,

	initialize: function(options) {
		if (options) this.setOptions(options);

		this.contentEl = this.options.contentEl;
		if (typeOf(this.contentEl) == 'string') {
			this.contentEl = $(this.contentEl, this.context);
		}

		Orb.Compat.WebForms.placeholder($('input[name="person\[first_name\]"]', this.contentEl));
		Orb.Compat.WebForms.placeholder($('input[name="person\[last_name\]"]', this.contentEl));
	},

	open: function() {
		this._initOverlay();
		this.overlay.openOverlay();
	},

	_hasInit: false,
	_initOverlay: function() {
		if (this._hasInit) return;
		this._hasInit = true;

		this.overlay = new DeskPRO.UI.Overlay({
			contentElement: this.contentEl
		});

		$('button.save-trigger', this.contentEl).on('click', this._handleSave.bind(this));
	},

	_handleSave: function() {
		var els = this.getFormElements();
		var data = els.serializeArray();

		var eventData = {
			formData: data,
			contentEl: this.contentEl,
			overlay: this.overlay,
			cancel: false
		};

		this.fireEvent('beforeSave', eventData);
		if (eventData.cancel) return;

		$.ajax({
			url: this.options.saveUrl,
			data: data,
			type: 'POST',
			dataType: 'json',
			success: this._handleSaveSuccess.bind(this)
		});
	},

	_handleSaveSuccess: function(data) {


		var eventData = {
			contentEl: this.contentEl,
			overlay: this.overlay,
			data: data
		};

		if (data.isError) {
			$('.error-message', this.contentEl).html(data.errorMessage).show();
			return;
		}

		// Reset incase there's another one
		this._clear();

		this.fireEvent('afterSave', eventData);
	},

	_clear: function() {
		$('.error-message', this.contentEl).hide();
		//this.getFormElements.val('');
	},

	getFormElements: function() {
		var els = $(':input', this.contentEl);
		return els;
	},

	destroy: function() {
		if (this.overlay) {
			this.overlay.destroy();
		} else {
			this.contentEl.remove();
		}
	}
});
