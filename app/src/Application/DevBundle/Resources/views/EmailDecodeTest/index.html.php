<?php $view->extend('DevBundle::layout.html.php') ?>

<form action="<?php echo $view['router']->generate('dev_emaildecodetest_run') ?>" method="POST">

<table width="100%">
	<tr class="alt">
		<td colspan="2">Options</td>
	</tr>
	<tr>
		<td colspan="2">
			Cutter: <select name="cutter_type">
				<option value="">No Cutter</option>
				<option value="normal">Normal Cutter</option>
				<option value="forward">Forward Cutter</option>
			</select>
		</td>
	</tr>
	<tr class="alt">
		<td colspan="2">Input Email Source</td>
	</tr>
	<tr>
		<td>
			<textarea name="email_source" style="width: 98%; height: 200px; font-family: 'Monaco', 'Courier New', monospace; font-size: 11px;"></textarea>
		</td>
		<td width="50">
			<button type="submit" value="input" name="source_from">Test</button>
		</td>
	</tr>
	<tr class="alt">
		<td colspan="2">Saved Sources (<?php echo $email_sources_dir ?>)</td>
	</tr>
	<?php foreach ($email_sources as $filename => $filepath): ?>
		<tr>
			<td><?php echo $filename ?></td>
			<td width="50"><button type="submit" name="source_from" value="<?php echo $filepath ?>">Test</button></td>
		</tr>
	<?php endforeach ?>
</table>

</form>