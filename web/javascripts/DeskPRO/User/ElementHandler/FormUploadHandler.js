Orb.createNamespace('DeskPRO.User.ElementHandler');

DeskPRO.User.ElementHandler.FormUploadHandler = new Orb.Class({
	Extends: DeskPRO.User.ElementHandler.ElementHandlerAbstract,

	init: function() {

		var self = this;
		var dropZone = this.el;
		if (this.el.data('drop-document') == '1') {
			dropZone = $(document);
		}

		var options = {
			url: this.el.data('upload-to'),
			dropZone: dropZone,
			autoUpload: true,
			formData: {
				security_token: this.el.data('security-token')
			},
			done: function(e, data) {
				var that = $(this).data('fileupload'),
					template,
					preview;

				if (!that) {
					return;
				}

				if (data.context) {
					data.context.each(function (index) {
						var file = ($.isArray(data.result) &&
								data.result[index]) || {error: 'emptyResult'};
						if (file.error && that._adjustMaxNumberOfFiles) {
							that._adjustMaxNumberOfFiles(1);
						}
						that._transition($(this)).done(
							function () {
								var node = $(this);
								template = that._renderDownload([file])
									.css('height', node.height())
									.replaceAll(node);
								that._forceReflow(template);
								that._transition(template).done(
									function () {
										data.context = $(this);
										that._trigger('completed', e, data);
									}
								);
							}
						);
					});
				} else {
					template = that._renderDownload(data.result)
						.appendTo(that.options.filesContainer);
					that._forceReflow(template);
					that._transition(template).done(
						function () {
							data.context = $(this);
							that._trigger('completed', e, data);
						}
					);
				}

				if (that.options.filesContainer) {
					if (!that.options.filesContainer.find('.uploading')[0]) {
						self.el.trigger('dp_upload_all_done');
					}
				}
			}
		};

		this._handleOptions(options);

		this.el.fileupload(options);

		$('.dp-fallback', this.el).remove();
		$('.dp-good-upload', this.el).show();
	},

	_handleOptions: function(options) {
		var el = this.el;

		if (!options.namespace) {
			options.namespace = Orb.uuid();
		}

		if (!options.dropZone) {
			options.dropZone = $(el);
		}

		if (typeof options.autoUpload == 'undefined') {
			options.autoUpload = true;
		}

		if (options.uploadTemplate) {
			var setel = options.uploadTemplate;
		} else {
			var setel = $('.template-upload', el);
		}
		if (!setel.attr('id')) {
			var id = Orb.getUniqueId('up');
			setel.attr('id', id);
		} else {
			var id = setel.attr('id');
		}
		delete(options.uploadTemplate);
		options.uploadTemplateId = id;

		if (options.downloadTemplate) {
			var setel = options.downloadTemplate;
		} else {
			var setel = $('.template-download', el);
		}
		if (!setel.attr('id')) {
			var id = Orb.getUniqueId('up');
			setel.attr('id', id);
		} else {
			var id = setel.attr('id');
		}
		delete(options.downloadTemplate);
		options.downloadTemplateId = id;

		if (!options.filesContainer) {
			options.filesContainer = $(el).find('.files');
		}

		options.start = function() {
			// Dont stack error messes. Once you upload again, the old one disappears
			$(el).find('.error').remove();
		};

		$(el).on('click', '.remove-attach-trigger', function(ev) {
			// Ignore .delete as they may be items rendered with the page,
			// eg. the list handles delete of existing attachments on its own
			if ($(this).hasClass('delete')) {
				return;
			}
			ev.preventDefault();
			var el = $(this).closest('li');
			el.slideUp('fast', function() {
				el.remove();
			});
		});
	}
});
