Orb.createNamespace('DeskPRO.Admin.PageHandler');

DeskPRO.Admin.PageHandler.TicketEditor = new Orb.Class({
	Extends: DeskPRO.Admin.PageHandler.Basic,

	initPage: function() {

		this.editors = {};
		this.editorSave = null;
		this.redrawData = null;

		var self = this;
		var depMenu = new DeskPRO.UI.Menu({
			triggerElement: $('#ticket_editor_head em'),
			menuElement: $('#department_switcher_editor'),
			onItemClicked: function(info) {
				window.location = $('a:first', info.itemEl).attr('href');
			}
		});

		var fieldMenu = new DeskPRO.UI.Menu({
			triggerElement: $('#add_field_user, #add_field_agent'),
			menuElement: $('#field_menu'),
			onItemClicked: function(info) {
				var name = $(info.menu.getOpenTriggerElement()).data('name');
				var ed = self.editors[name].addDisplayItemFromEl($(info.itemEl));
			}
		});

		var ed = new DeskPRO.Admin.TicketEditor({
			context: '#user_dep_editor',
			name: 'user'
		});
		this.addEditor(ed);

		var ed = new DeskPRO.Admin.TicketEditorAgent({
			context: '#agent_dep_editor',
			name: 'agent'
		});
		this.addEditor(ed);

		//editor_save_btn
		this.editorSaver = new DeskPRO.Admin.EditorSave();

		$('#editor_save_btn').on('click', this.doSave.bind(this));

		if (this.redrawData) {
			this.redrawState(this.redrawData);
			delete this.redrawData;
			this.redrawData = null;
		}
	},

	redrawState: function(all_data) {

		var redrawer = new DeskPRO.Admin.EditorRedraw();

		if (all_data.user_default) {
			redrawer.setItemsForSection(
				all_data.user_default,
				this.editors['user'],
				$('#user_dep_editor ul.field-list:first'),
				$('#user_dep_editor_default')
			);
		}

		if (all_data.agent_default) {
			redrawer.setItemsForSection(
				all_data.agent_default,
				this.editors['agent'],
				$('#agent_dep_editor ul.field-list:first'),
				$('#agent_dep_editor_default')
			);
		}

		if (all_data.agent_toptabs) {
			redrawer.setItemsForSection(
				all_data.agent_toptabs,
				this.editors['agent'],
				$('#agent_dep_editor ul.field-list:first'),
				$('#agent_dep_editor_toptabs')
			);
		}

		if (all_data.agent_middletabs) {
			redrawer.setItemsForSection(
				all_data.agent_middletabs,
				this.editors['agent'],
				$('#agent_dep_editor ul.field-list:first'),
				$('#agent_dep_editor_middletabs')
			);
		}

		if (all_data.agent_bodytabs) {
			redrawer.setItemsForSection(
				all_data.agent_bodytabs,
				this.editors['agent'],
				$('#agent_dep_editor ul.field-list:first'),
				$('#agent_dep_editor_bodytabs')
			);
		}
	},

	addEditor: function(ed) {
		this.editors[ed.getName()] = ed;
	},

	doSave: function() {

		var data = this.editorSaver.getEditorData($('#agent_dep_editor_default'), 'agent_default');
		data.append(this.editorSaver.getEditorData($('#agent_dep_editor_toptabs'), 'agent_toptabs'));
		data.append(this.editorSaver.getEditorData($('#agent_dep_editor_middletabs'), 'agent_middletabs'));
		data.append(this.editorSaver.getEditorData($('#agent_dep_editor_bodytabs'), 'agent_bodytabs'));
		data.append(this.editorSaver.getEditorData($('#user_dep_editor'), 'user_default'));

		data.push({
			name: 'enable_captcha',
			value: $('#enable_captcha').is(':checked') ? 1 : 0
		});
		data.push({
			name: 'user_dep_name',
			value: $('#user_dep_name').val()
		});

		$.ajax({
			url: this.options.saveUrl,
			data: data,
			dataType: 'json',
			type: 'POST'
		});
	}
});
