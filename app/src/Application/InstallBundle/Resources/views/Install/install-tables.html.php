<?php if (!defined('DP_ROOT')) exit('No access'); ?>
<?php $view->extend('InstallBundle:Install:layout.html.php') ?>
<?php $view['slots']->start('subtitle') ?>Step 5: Installing database tables<?php $view['slots']->stop() ?>
<script type="text/javascript">
var installStatus = {
	update: function(info) {
		var tr = $('<tr><td></td></tr>');
		$('td', tr).text(info.message);

		$('#install_log').text($('#install_log').text() + "\n\n" + info.message);

		if (info.error) {
			installStatus.hasError = true;
			var err = $('<span />');
			err.addClass('label important');
			err.text(info.error);
			err.prependTo($('td', tr));

			$('#install_log').text($('#install_log').text() + "\n\n" + info.error);

			var li = $('<li>');
			li.text(info.message);
			li.appendTo($('#error_list'));
		}

		if (info.sql || info.skipped) {
			installStatus.currentCount++;
			$('#current_count').text(installStatus.currentCount);
			$('#progress_done_td').attr('width', Math.ceil((installStatus.currentCount / installStatus.allCount) * 100) + '%');
		}

		$('#log tbody').append(tr);
	},
	setCount: function(count) {

		$('#preloading').hide();
		$('#install_loading').show();

		installStatus.allCount = count;
		installStatus.currentCount = 0;
		$('#all_count').text(count);
	},
	doneBatch: function(batch) {
		var url = $('#runner_iframe').data('src-url') + (batch+1);
		$('#runner_iframe').attr('src', url);
	},
	done: function() {
		$('#progress_done_td').attr('width', '100%');
		$('#progress_undone_td').remove();

		$('#show_log').show();
		$('#hide_log').hide();
		$('#log').hide();

		if (installStatus.hasError) {
			$('#install_error').show();
			sendReportError();
		} else {
			$('#install_done').show();
		}
	}
};

$(document).ready(function() {
	$('#show_log').on('click', function() {
		$(this).hide();
		$('#hide_log').show();
		$('#log').fadeIn();
	});
	$('#hide_log').on('click', function() {
		$(this).hide();
		$('#show_log').show();
		$('#log').fadeOut();
	});
});
</script>
<style type="text/css">

	#install_loading .progress {
		border: 2px solid #62CFFC;
		border-radius: 8px;
		-moz-border-radius: 8px;
	}

	#install_loading table {
		margin: 0;
		padding: 0;
		border: none;
	}

	#install_loading table td {
		padding: 0;
		margin: 0;
		background-color: #fff;
		border: none;
		height: 20px;
		overflow: hidden;
		border-radius: 6px;
		-moz-border-radius: 6px;
	}

	#install_loading table td.done {
		background-color: #A3D4FB;
	}

	#install_error textarea {
		width: 80%;
		height: 120px;
		overflow: auto;
	}

	#error_list {
		max-height: 200px;
		overflow: auto;
	}
</style>

<div id="preloading">
	<h3>Starting install ...</h3>
</div>

<div id="install_loading" style="display: none">
	<div id="show_log" style="float: right; cursor: pointer"><span class="label notice">Show Log</span></div>
	<div id="hide_log" style="float: right; cursor: pointer; display: none"><span class="label notice">Hide Log</span></div>

	<h3>Installing Database: <span id="current_count">0</span> of <span id="all_count">0</span> objects inserted</h3>
	<div class="progress">
		<table cellspacing="0" cellpadding="0" width="100%">
			<tr>
				<td id="progress_done_td" class="done" width="1%">&nbsp;</td>
				<td id="progress_undone_td" class="undone">&nbsp;</td>
			</tr>
		</table>
	</div>
</div>

<div id="log" class="well" style="display: none; margin-top: 8px; max-height: 400px; overflow: auto;">
	<table class="condensed-table">
		<tbody>
		</tbody>
	</table>
</div>

<br />

<div id="install_error" style="display: none">
	<div class="alert-message block-message error">
		<strong>There was an error!</strong> An error was detected during the installation.

		<ul id="error_list">
		</ul>

		You should contact DeskPRO Support to get help on how to fix this error. Include this log with any message you send to us:
		<textarea id="install_log"></textarea>

		<div class="alert-actions">
			<a class="btn" href="mailto:support@deskpro.com">Email support@deskpro.com</a>
			<a class="btn" href="http://support.deskpro.com/">Visit our helpdesk</a>
			<a class="btn" href="<?php echo $view['router']->generate('install_install_data') ?>">Ignore the errors and continue to the next step anyway</a>
		</div>
	</div>
</div>

<div id="install_done" style="display: none">
	<div class="alert-message block-message success">
		<strong>Done!</strong> You're ready to go to the next step.

		<div class="alert-actions submit-area">
			<a class="btn" tabindex="2" id="next_btn" href="<?php echo $view['router']->generate('install_install_data') ?>" onclick="if (!$(this).hasClass('disabled')) { $(this).parent().addClass('clicked'); }">Go to step 6: Create your admin account</a>
			<span class="next-loading"></span>
		</div>
	</div>
</div>

<iframe
	id="runner_iframe"
	data-src-url="<?php echo $view['router']->generate('install_create_tables_do') ?>/"
	src="<?php echo $view['router']->generate('install_create_tables_do') ?>"
	style="width: 1px; height: 1px; border: none; margin: 0; padding: 0;"
	width="1"
	height="1"
	framemargin="0"
	frameborder="0"
></iframe>
