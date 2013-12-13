Orb.createNamespace('DeskPRO.Agent.PageHelper');

DeskPRO.Agent.PageHelper.ValidatingEdit = new Orb.Class({
	Implements: [Orb.Util.Events, Orb.Util.Options],

	initialize: function(page, options) {
		var self = this;

		this.page = page;

		this.options = {
			typename: '',
			contentId: 0,
			singleType: ''
		};
		this.setOptions(options);

		$('button.approve-trigger', this.page.wrapper).on('click', this.approveEdit.bind(this));
		$('button.disapprove-trigger', this.page.wrapper).on('click', this.showDisapproveForm.bind(this));
		$('button.disapprove2-trigger', this.page.wrapper).on('click', this.disapproveEdit.bind(this));
		$('button.skip-trigger', this.page.wrapper).on('click', this.skipValidateEdit.bind(this));
	},

	//#################################################################
	//# Validation controls
	//#################################################################

	showDisapproveForm: function() {
		$('.validating-bar:first .options', this.page.wrapper).hide();
		$('.validating-bar:first .disapprove-form', this.page.wrapper).show();
	},

	approveEdit: function() {
		$.ajax({
			url: BASE_URL + 'agent/publish/content/approve/' + this.options.typename + '/' + this.options.contentId + '.json?specific_type=' + this.options.singleType,
			type: 'POST',
			context: this,
			dataType: 'json',
			success: function(info) {
				if (info.next_url) {
					DeskPRO_Window.runPageRoute('page:' + info.next_url);
				}

				DeskPRO_Window.getMessageBroker().sendMessage('publish.validating.list-remove', {
					typename: this.options.typename,
					contentId: this.options.contentId
				});

				DeskPRO_Window.removePage(this.page);

				if (DeskPRO_Window.sections.feedback_section) {
					DeskPRO_Window.sections.feedback_section.reload();
				}
			}
		});
	},

	disapproveEdit: function() {

		var reason = $('.validating-bar .disapprove-reason', this.page.wrapper).val().trim();

		$.ajax({
			url: BASE_URL + 'agent/publish/content/disapprove/' + this.options.typename + '/' + this.options.contentId + '.json?specific_type=' + this.options.singleType,
			type: 'POST',
			context: this,
			data: {reason: reason},
			dataType: 'json',
			success: function(info) {
				if (info.next_url) {
					DeskPRO_Window.runPageRoute('page:' + info.next_url);
				}

				DeskPRO_Window.getMessageBroker().sendMessage('publish.validating.list-remove', {
					typename: this.options.typename,
					contentId: this.options.contentId
				});

				if (DeskPRO_Window.sections.feedback_section) {
					DeskPRO_Window.sections.feedback_section.reload();
				}

				DeskPRO_Window.removePage(this.page);
			}
		});
	},

	skipValidateEdit: function() {
		$.ajax({
			url: BASE_URL + 'agent/publish/content/get-next-validating/' + this.options.typename + '/' + this.options.contentId + '.json?specific_type=' + this.options.singleType,
			type: 'POST',
			context: this,
			dataType: 'json',
			success: function(info) {
				if (info.next_url) {
					DeskPRO_Window.runPageRoute('page:' + info.next_url);
				}

				DeskPRO_Window.getMessageBroker().sendMessage('publish.validating.list-remove', {
					typename: this.options.typename,
					contentId: this.options.contentId
				});

				DeskPRO_Window.removePage(this.page);
			}
		});
	}
});
