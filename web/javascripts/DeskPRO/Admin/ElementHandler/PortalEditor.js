Orb.createNamespace('DeskPRO.Admin.ElementHandler');

/**
 * The portal editor is made of up the admin page, and then a specially
 * loaded page in the user interface loaded through an iframe.
 *
 * We call the admin page the PortalEditor, and the user page the PortalClient.
 *
 * Messages, like click events that need an editor, are handled byt he PortalClient
 * and are pssed up to this PortalEditor which takes care of opening editors and saving
 * data. Then in some cases, data is passed back down to the PortalClient to update
 * the live display.
 *
 * Generally: PortalEditor handles saving/changing of data, PortalClient handles displaying data
 * and interaction with the UI.
 */
DeskPRO.Admin.ElementHandler.PortalEditor = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	init: function() {
		var self = this;

		// This is so userland can send us messages
		window.PortalEditor = this;

		$('#portal_iframe').attr('src', this.el.data('portal-url'));

		$(':checkbox.section-toggle').on('change', function() {
			var type = $(this).attr('name');

			if ($(this).is(':checked')) {
				self.tellPortal('app_enabled', {name: type});
			} else {
				self.tellPortal('app_disabled', {name: type});
			}
		});

		this._initColorPicker();
		$('#portal_iframe').css('opacity', '0');
	},

	/**
	 * Send a message to the portal client
	 *
	 * @param id
	 * @param data
	 */
	tellPortal: function(id, data) {
		this.iframeWindow.PortalAdmin.acceptMessage(id, data);
	},


	/**
	 * Call a method on the portal client
	 *
	 * @param id
	 * @param data
	 */
	callPortal: function(id, data) {
		return this.iframeWindow.PortalAdmin[id](data);
	},


	/**
	 * Accepts a message passed from the portal client
	 *
	 * @param id
	 * @param data
	 */
	acceptMessage: function(id, data) {

		var self = this;

		data = data || {};
		DP.console.log("New message: %s %o", id, data);

		switch (id) {
			case 'loaded':
				this.iframeLoaded(data.height);
				break;
			case 'update_height':
				this.updateHeight(data.height);
				break;
			case 'switch_page':
				window.location = BASE_URL + 'admin/portal?portal_path=' + encodeURI(data.path);
				break;
			case 'enable_logo_area':
				$.ajax({
					type: 'POST',
					url: BASE_URL + 'admin/portal/save-editor/enable_logo_area',
					type: 'POST'
				});
				break;
			case 'disable_logo_area':
				$.ajax({
					type: 'POST',
					url: BASE_URL + 'admin/portal/save-editor/disable_logo_area',
					type: 'POST'
				});
				break;
			case 'open_placeholder_editor':
				var controller = data.controller;

				this.showHtmlEditor(controller.name, function(action) {
					switch (action) {
						case 'update': controller.update(); break;
						case 'reset': controller.reset(); break;
					}
				});
				break;
			case 'reset_placeholder':
				var controller = data.controller;
				var template_name;
				if (controller.name == 'header') {
					template_name = 'UserBundle::custom-header.html.twig';
				} else if (controller.name == 'head_include') {
					template_name = 'UserBundle::custom-headinclude.html.twig';
				} else if (controller.name == 'welcome') {
					template_name = 'UserBundle:Portal:welcome-block.html.twig';
				} else {
					template_name = 'UserBundle::custom-footer.html.twig';
				}

				$.ajax({
					type: 'POST',
					url: BASE_URL + 'admin/templates/revert-template.json?name=' + template_name
				});

				break;
			case 'update_orders':
				var ids = data.orderedIds;

				var postData = [];
				for (var i = 0; i < ids.length; i++) {
					postData.push({
						name: 'display_order[]',
						value: ids[i]
					});
				}

				$.ajax({
					url: this.el.data('url-update-orders'),
					type: 'POST',
					dataType: 'json',
					data: postData
				});

				break;
			case 'block_toggled':

				var url = this.el.data('url-block-toggle').replace(/_PID_/g, data.pid);
				var postData = {
					enabled: data.enabled ? 1 : 0
				};

				$.ajax({
					url: url,
					type: 'POST',
					dataType: 'json',
					data: postData
				});

				break;
			case 'toggle_tab':

				var tabName = data.tabName;
				var on = data.on ? 1 : 0;

				$.ajax({
					url: BASE_URL + 'admin/portal/save-editor/toggle_tab',
					type: 'POST',
					data: {
						tab: tabName,
						on: on
					}
				});
				break;
			case 'reorder_tabs':
				var order = data.order;

				var postData = [];
				for (var i = 0; i < order.length; i++) {
					postData.push({
						name: 'display_order[]',
						value: order[i]
					});
				}

				$.ajax({
					url: BASE_URL + 'admin/portal/save-editor/reorder_tabs',
					type: 'POST',
					dataType: 'json',
					data: postData
				});

				break;
			case 'open_logo_editor':
				var controller = data.controller;
				var overlay = new DeskPRO.UI.Overlay({
					contentMethod: 'ajax',
					destroyOnClose: true,
					contentAjax: {
						url: BASE_URL + 'admin/portal/get-editor/logo'
					},
					onContentSet: function(ev) {
						var wrapper = ev.wrapperEl;

						var idbase = 'ed_' + Orb.uuidRand();
						$('.template-upload', wrapper).attr('id', idbase + 'up')
						$('.template-download', wrapper).attr('id', idbase + 'down')
						wrapper.fileupload({
							url: BASE_URL + 'admin/misc/accept-upload',
							dropZone: wrapper,
							autoUpload: true,
							uploadTemplateId: idbase + 'up',
							downloadTemplateId: idbase + 'down'
						}).bind('fileuploadstart', function() {
							$('p.explain', wrapper).hide();
						}).bind('fileuploadadd', function() {
							$('.files', wrapper).empty();
						});

						$('.save-logo-trigger', wrapper).on('click', function() {
							var url = $('input.new_logo_url', wrapper).val();
							if (!url) {
								alert('You need to upload an image');
								return;
							}

							controller.setLogo(url);

							$.ajax({
								url: BASE_URL + 'admin/portal/save-editor/header_logo',
								type: 'POST',
								data: {
									blob_authid: wrapper.find('input.new_blob_auth_id').val()
								}
							});

							ev.overlay.close();
						});

						$('.save-text-trigger').on('click', function() {
							controller.setLogoText($('input[name="title"]', wrapper).val(), $('input[name="tagline"]', wrapper).val());

							$.ajax({
								url: BASE_URL + 'admin/portal/save-editor/header_title',
								type: 'POST',
								data: {
									title: wrapper.find('input.title').val(),
									tagline: wrapper.find('input.tagline').val()
								}
							});

							ev.overlay.close();
						});
					}
				});
				overlay.open();
				break;
			case 'open_portal_title':
				var controller = data.controller;
				var overlay = new DeskPRO.UI.Overlay({
					contentMethod: 'ajax',
					destroyOnClose: true,
					contentAjax: {
						url: BASE_URL + 'admin/portal/get-editor/portal-title'
					},
					onContentSet: function(ev) {
						var wrapper = ev.wrapperEl;
						$('.save-text-trigger').on('click', function() {
							controller.setTitle($('input[name="title"]', wrapper).val());

							$.ajax({
								url: BASE_URL + 'admin/portal/save-editor/portal_title',
								type: 'POST',
								data: {
									title: wrapper.find('input.title').val()
								}
							});

							ev.overlay.close();
						});
					}
				});
				overlay.open();
				break;

			case 'edit_twitter_sidebar_block':
				var controller = data.controller;
				var overlay = new DeskPRO.UI.Overlay({
					contentMethod: 'ajax',
					destroyOnClose: true,
					contentAjax: {
						url: BASE_URL + 'admin/portal/get-editor/twitter-sidebar'
					},
					onContentSet: function(ev) {
						var wrapper = ev.wrapperEl;
						$('.save-trigger').on('click', function() {
							$.ajax({
								url: BASE_URL + 'admin/portal/save-editor/twitter_sidebar',
								type: 'POST',
								data: {
									twitter_name: wrapper.find('input.twitter_name').val(),
									max_items: wrapper.find('input.max_items').val()
								}
							});

							ev.overlay.close();

							controller.update()
						});
					}
				});
				overlay.open();
				break;

			case 'new_sidebar_block':
				this.showHtmlEditor('NEW_SIDEBAR_BLOCK', function(action, data) {
					switch (action) {
						case 'update': self.tellPortal('new_sidebar_block', {
							pid: data.pid
						});
					}
				});
				break;

			case 'edit_template_block':
				var controller = data.controller;
				this.showHtmlEditor('EDIT_SIDEBAR_BLOCK:' + data.pid, function(action, data) {
					switch (action) {
						case 'update': controller.update();
						break;
					}
				});
				break;

			case 'new_sidebar_block_simple':
				this.showHtmlEditorSimple(0, function(action, data) {
					switch (action) {
						case 'update': self.tellPortal('new_sidebar_block_simple', {
							pid: data.pid
						});
					}
				});
				break;

			case 'edit_sidebar_block_simple':
				var controller = data.controller;
				this.showHtmlEditorSimple(data.pid, function(action, data) {
					switch (action) {
						case 'update': controller.update();
						break;
					}
				});
				break;

			case 'delete_sidebar_block_simple':
				var controller = data.controller;
				var el = controller.getEl();
				el.hide();

				$.ajax({
					type: 'POST',
					url: BASE_URL + 'admin/portal/sideblock-simple/'+data.pid+'/delete.json',
					error: function() {
						el.show();
					},
					success: function() {
						controller.remove();
					}
				});
				break;

			case 'delete_template_block':
				var controller = data.controller;
				var el = controller.getEl();
				el.hide();

				$.ajax({
					type: 'POST',
					url: BASE_URL + 'admin/portal/blocks/' + data.pid + '/delete-template-block.json',
					error: function() {
						el.show();
					},
					success: function() {
						controller.remove();
					}
				});
				break;
		}
	},


	/**
	 * Shows a generic HTML editor
	 *
	 * @param callback
	 */
	showHtmlEditor: function(name, callback) {
		var el = $(DeskPRO_Window.util.getPlainTpl($('#admin_portal_block_html_edit_tpl')));

		var template_name;
		if (name == 'header') {
			template_name = 'UserBundle::custom-header.html.twig';
		} else if (name == 'head_include') {
			template_name = 'UserBundle::custom-headinclude.html.twig';
		} else if (name == 'welcome') {
			template_name = 'UserBundle:Portal:welcome-block.html.twig';
		} else if (name == 'articles_header') {
			template_name = 'UserBundle:Articles:section-header.html.twig';
		} else if (name == 'downloads_header') {
			template_name = 'UserBundle:Downloads:section-header.html.twig';
		} else if (name == 'feedback_header') {
			template_name = 'UserBundle:Feedback:section-header.html.twig';
		} else if (name == 'NEW_SIDEBAR_BLOCK') {
			// TemplatesController::saveTemplateAction knows to treat this special
			template_name = 'UserBundle:Portal:new-sidebar-block.html.twig';
		} else if (name.indexOf('EDIT_SIDEBAR_BLOCK:') !== -1) {
			// TemplatesController knows to treat this special
			template_name = name;
		} else {
			template_name = 'UserBundle::custom-footer.html.twig';
		}

		var overlay = new DeskPRO.UI.Overlay({
			contentElement: el,
			destroyOnClose: true,
			fullScreen: true,
			onBeforeOverlayOpened: function() {
				if (el.is('.has-init')) return;
				el.addClass('has-init');

				el.find('textarea').val('').addClass('loading');

				$.ajax({
					url: BASE_URL + 'admin/templates/get-template-code?name=' + template_name,
					context: this,
					success: function(val) {
						el.find('textarea').val(val).removeClass('loading');
					}
				});

				$('.save-trigger', el).on('click', function() {

					el.find('.overlay-footer').addClass('loading');

					var postData = {
						name: template_name,
						code: el.find('textarea').val().trim()
					};

					if (name == 'head_include') {
						if (!postData.code.length) {
							$.ajax({
								type: 'POST',
								url: BASE_URL + 'admin/templates/revert-template.json?name=' + template_name,
								success: function() {
									window.location.reload(false);
								}
							});
						} else {
							$.ajax({
								url: BASE_URL + 'admin/templates/save-template.json',
								context: this,
								type: 'POST',
								data: postData,
								success: function(data) {
									window.location.reload(false);
								}
							});
						}
						return;
					}

					if (!postData.code.length) {
						$.ajax({
							type: 'POST',
							url: BASE_URL + 'admin/templates/revert-template.json?name=' + template_name
						});

						callback('reset');
						overlay.close();
					} else {
						$.ajax({
							url: BASE_URL + 'admin/templates/save-template.json',
							context: this,
							type: 'POST',
							data: postData,
							success: function(data) {
								el.find('.overlay-footer').removeClass('loading');

								if (data.error) {
									alert(data.error_message + "\n\nLine: " + data.error_line);
									return;
								}

								callback('update', data);
								overlay.close();
							}
						});
					}
				});
			}
		});
		overlay.open();
	},

	/**
	 * Shows a simple editor for title/content
	 *
	 * @param callback
	 */
	showHtmlEditorSimple: function(pid, callback) {
		pid = parseInt(pid) || 0;

		var el = $(DeskPRO_Window.util.getPlainTpl($('#admin_portal_block_simple_html_edit_tpl')));

		var overlay = new DeskPRO.UI.Overlay({
			contentElement: el,
			destroyOnClose: true,
			fullScreen: true,
			onBeforeOverlayOpened: function(evData) {
				var el = evData.overlay.elements.wrapper;

				if (el.is('.has-init')) return;
				el.addClass('has-init');

				if (pid) {
					el.find('textarea.content').val('').addClass('loading');

					$.ajax({
						url: BASE_URL + 'admin/portal/sideblock-simple/' + pid + '.json',
						context: this,
						dataType: 'json',
						success: function(data) {
							el.find('input.title').val(data.title);
							el.find('textarea.content').val(data.content).removeClass('loading');
						}
					});
				}

				el.find('textarea.content').height($(window).height() - 250);

				$('.save-text-trigger', el).on('click', function() {

					el.find('.overlay-footer').addClass('loading');

					var postData = [];
					postData.push({
						name: 'title',
						value: el.find('input.title').val()
					});
					postData.push({
						name: 'content',
						value: el.find('textarea.content').val()
					});

					$.ajax({
						url: BASE_URL + 'admin/portal/sideblock-simple/'+pid+'/save.json',
						context: this,
						type: 'POST',
						data: postData,
						success: function(data) {
							el.find('.overlay-footer').removeClass('loading');
							callback('update', data);
							overlay.close();
						}
					});
				});
			}
		});
		overlay.open();
	},


	/**
	 * Whent the portal client is loaded, it sends a message to us and we invoke
	 * this method to set up the messages channel.
	 *
	 * @param height
	 */
	iframeLoaded: function(height) {
		var iframe = $('#portal_iframe').get(0);

		if (iframe.contentDocument) {
			this.iframeDocument = iframe.contentDocument;
		} else if (iframe.contentWindow) {
			this.iframeDocument = iframe.contentWindow.document;
		} else if (iframe.document) {
			this.iframeDocument = iframe.document;
		} else {
			this.iframeDocument = null;
			DP.console.error("Could not get iframe document");
		}

		this.iframeWindow = this.iframeDocument.window;

		$('#portal_iframe').height(height + 25);
		$('#dp_fauxbrowser').height(height + 188);
		this.iframeQuery('html').css('overflow', 'hidden');
		this.iframeQuery('body').css('overflow', 'hidden');

		// Show the frame now
		$('#portal_iframe_loading').fadeOut(300, function() {
			$('#portal_iframe').css('opacity', '1');
		});
	},

	updateHeight: function(height) {
		$('#portal_iframe').height(height + 25);
		$('#dp_fauxbrowser').height(height + 188);
	},


	/**
	 * Execute a jQuery query from in the context of the portal client
	 *
	 * @param query
	 */
	iframeQuery: function(query) {
		return this.iframeWindow.jQuery(query);
	},


	_initColorPicker: function() {
		var self     = this;
		var panel    = $('#portal_colors');
		var trigger  = $('#portal_colors_trigger');
		var backdrop = $('<div class="backdrop" style="z-index: 999" />').hide().appendTo('body');

		panel.detach().appendTo('body');

		trigger.on('click', function() {
			if (panel.is(':visible')) {
				closeColorPanel();
			} else {
				openColorPanel();
			}
		});

		backdrop.on('click', function() {
			closeColorPanel();
		});

		var openColorPanel = function() {
			var triggerPos = trigger.offset();

			var top  = triggerPos.top  + trigger.height();
			var left = (triggerPos.left + trigger.width() - 20);

			panel.css({
				top: top ,
				left: left
			});

			panel.slideDown();
			backdrop.show();
		};

		var closeColorPanel = function() {
			panel.slideUp();
			backdrop.hide();
		};

		var colorSwatches = $('.color-swatch', panel);
		colorSwatches.each(function() {
			var swatchEl = $(this);
			swatchEl.on('click', function() {
				swatchEl.ColorPickerShow();
			});

			swatchEl.ColorPicker({
				onSubmit: function(hsb, hex, rgb, el) {
					swatchEl.data('color', hex);
					$(el).ColorPickerHide();
				},
				onBeforeShow: function () {
					$(this).ColorPickerSetColor(swatchEl.data('color'));
				},
				onChange: function (hsb, hex, rgb) {
					$('div', swatchEl).css('backgroundColor', '#' + hex);
					swatchEl.data('color', '#' + hex);

					swatchEl.closest('.style-row').find('.color-reset').addClass('enabled');
				}
			});
		});

		var colorResets = $('.color-reset', panel);
		colorResets.on('click', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			var swatchEl = $(this).closest('.style-row').find('.color-swatch');
			var setColor = $(this).data('color').replace('#', '');

			console.log('Setting %s on %e', setColor, swatchEl);
			swatchEl.ColorPickerSetColor(setColor);
			swatchEl.data('color', '#' + setColor);
			$('div', swatchEl).css('backgroundColor', '#' + setColor);

			$(this).removeClass('enabled');
		});

		$('button.apply-trigger', panel).on('click', function() {
			closeColorPanel();

			var formData = [];

			colorSwatches.each(function() {
				formData.push({
					name: 'vars[' + $(this).data('color-id') + ']',
					value: $(this).data('color')
				});
			});

			$.ajax({
				url: BASE_URL + 'admin/portal/save-editor/css_var',
				type: 'POST',
				data: formData,
				success: function() {
					self.tellPortal('reload_css');
				}
			});
		});
	}
});
