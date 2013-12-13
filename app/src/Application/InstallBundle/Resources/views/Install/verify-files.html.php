<?php if (!defined('DP_ROOT')) exit('No access'); ?>
<?php $view->extend('InstallBundle:Install:layout.html.php') ?>
<?php $view['slots']->start('subtitle') ?>Step 4: Verifying file integrity<?php $view['slots']->stop() ?>
<script type="text/javascript">
window.DpStatus = {
	allCount: <?php echo $count ?>,
	currentCount: 0,
	hasErrors: false,
	update: function(info, batch) {
		var tr = $('<tr><td></td></tr>');

		if (info.okay) {
			var tr = $('<tr><td></td></tr>');
			$('td', tr).text('Batch ' + (batch+1) + ' of ' + DpStatus.allCount + ': ' + info.okay.length + ' files verified');
			$('#log tbody').append(tr);
		}
		if (info.added) {
			for (var i = 0; i < info.added.length; i++) { var f = info.added[i];
				var tr = $('<tr><td></td></tr>');
				$('td', tr).text('Batch ' + (batch+1) + ' of ' + DpStatus.allCount + ': ' + ' File added: ' + f);
				$('#log tbody').append(tr);
			};
		}
		if (info.changed && info.changed.length) {
			for (var i = 0; i < info.changed.length; i++) { var f = info.changed[i];
				var tr = $('<tr><td></td></tr>');
				$('td', tr).text('Batch ' + (batch+1) + ' of ' + DpStatus.allCount + ': ' + ' File changed: ' + f);

				tr.appendTo();
				$('#log tbody').append(tr);

				var li = $('<li />');
				li.text('CHANGED: ' + f);
				$('#error_list').append(li);
			};

			if (console && console.log) {
				console.log(info.changed);
			}

			DpStatus.hasErrors = true;
		}
		if (info.removed && info.removed.length) {
			for (var i = 0; i < info.removed.length; i++) { var f = info.removed[i];
				var tr = $('<tr><td></td></tr>');
				$('td', tr).text('Batch ' + (batch+1) + ' of ' + DpStatus.allCount + ': ' + ' Missing: ' + f);
				$('#log tbody').append(tr);

				var li = $('<li />');
				li.text('MISSING: ' + f);
				$('#error_list').append(li);
			};

			if (console && console.log) {
				console.log(info.removed);
			}

			DpStatus.hasErrors = true;
		}
	},
	doneBatch: function(batch) {
		DpStatus.currentCount++;

		$('#current_count').text(DpStatus.currentCount);

		if (DpStatus.currentCount >= DpStatus.allCount) {
			DpStatus.done();
		} else {
			$('#progress_done_td').attr('width', Math.ceil((DpStatus.currentCount / DpStatus.allCount) * 100) + '%');
			var url = $('#runner_iframe').data('src-url') + DpStatus.currentCount;
			$('#runner_iframe').attr('src', url);
		}
	},
	done: function() {
		$('#progress_done_td').attr('width', '100%');
		$('#progress_undone_td').remove();

		$('#show_log').show();
		$('#hide_log').hide();
		$('#log').hide();

		if (DpStatus.hasErrors) {
			$('#install_error').show();
		} else {
			$('#install_done').show();
		}
	}
};

$(document).ready(function() {

	$('#preloading').hide();
	$('#install_loading').show();

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

	<h3>Checking files: <span id="current_count">0</span> of <span id="all_count"><?php echo $count ?></span> checks performed</h3>
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
		<strong>There was an error!</strong> We detected some incorrect files. This might be caused by a corrupt upload or some other upload problem:

		<ul id="error_list">
		</ul>

		You should contact DeskPRO Support to get help on how to fix this error. Include the above list with any message you send to us:

		<div class="alert-actions">
			<a class="btn" href="mailto:support@deskpro.com">Email support@deskpro.com</a>
			<a class="btn" href="http://support.deskpro.com/">Visit our helpdesk</a>
		</div>
	</div>

	<div style="margin-top: 10px; text-align: right; font-size: 10px; margin-bottom: -40px;">
		<a href="<?php echo $view['router']->generate('install_create_tables') ?>" onclick="return confirm('We recommend contacting support@deskpro.com to resolve this error. Continuing with the installation process when there were detected abnormalities may result in a corrupt helpdesk. Are you sure you want to continue?');">Continue to the next step anyway</a> &rarr;
	</div>
</div>

<div id="install_done" style="display: none">
	<div class="alert-message block-message success">
		<strong>Done!</strong> You're ready to go to the next step.

		<div class="alert-actions submit-area">
			<a class="btn" href="<?php echo $view['router']->generate('install_create_tables') ?>" onclick="$(this).parent().addClass('clicked');">Go to step 5: Install database</a>
			<span class="next-loading"></span>
		</div>
	</div>
</div>

<iframe
	id="runner_iframe"
	data-src-url="<?php echo $view['router']->generate('install_verify_files_do') ?>/"
	src="<?php echo $view['router']->generate('install_verify_files_do') ?>"
	style="width: 1px; height: 1px; border: none; margin: 0; padding: 0;"
	width="1"
	height="1"
	framemargin="0"
	frameborder="0"
></iframe>
