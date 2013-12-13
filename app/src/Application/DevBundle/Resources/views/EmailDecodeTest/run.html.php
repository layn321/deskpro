<?php $view->extend('DevBundle::layout.html.php') ?>

<form action="<?php echo $view['router']->generate('dev_emaildecodetest_run') ?>" method="POST">

<table width="100%">
	<tr class="alt">
		<td colspan="2">Options</td>
	</tr>
	<tr>
		<td colspan="2">
			Cutter: <select name="cutter_type">
				<option <?php if (!$cutter_type) echo 'selected="selected"'; ?> value="">No Cutter</option>
				<option <?php if ($cutter_type == 'normal') echo 'selected="selected"'; ?> value="normal">Normal Cutter</option>
				<option <?php if ($cutter_type == 'forward') echo 'selected="selected"'; ?> value="forward">Forward Cutter</option>
			</select>
		</td>
	</tr>
	<tr class="alt">
		<td colspan="2">Email Source</td>
	</tr>
	<tr>
		<td>
			<textarea name="email_source" style="width: 98%; height: 200px; font-family: 'Monaco', 'Courier New', monospace; font-size: 11px;"><?php echo htmlspecialchars($email_source) ?></textarea>
		</td>
		<td width="50">
			<button type="submit" value="input" name="source_from">Again</button>
		</td>
	</tr>
</table>

</form>

<table width="100%">
	<tr class="alt">
		<td colspan="2">Decoded Email</td>
	</tr>
	<tr>
		<td width="180">
			Read email as
		</td>
		<td>
			<?php if ($body_is_html) echo 'HTML'; else echo 'Text'; ?>
		</td>
	</tr>
	<tr>
		<td>Original Email Charset</td>
		<td>
			<?php if ($body_is_html) $body = $reader->getBodyHtml(); else $body = $reader->getBodyText(); ?>
			<?php echo $body->getOriginalCharset() ?>
		</td>
	</tr>
	<tr>
		<td>Subject</td>
		<td>
			<?php echo $reader->getSubject()->getSubjectUtf8() ?>
		</td>
	</tr>
	<tr>
		<td>To</td>
		<td>
			<?php foreach ($reader->getToAddresses() as $addr): ?>
				<?php echo $addr->getNameUtf8() ?>
				<?php echo $addr->getEmail() ?>
				<br />
			<?php endforeach ?>
		</td>
	</tr>
	<tr>
		<td>Cc</td>
		<td>
			<?php foreach ($reader->getCcAddresses() as $addr): ?>
				<?php echo $addr->getNameUtf8() ?>
				<?php echo $addr->getEmail() ?>
				<br />
			<?php endforeach ?>
		</td>
	</tr>
	<tr>
		<td>From</td>
		<td>
			<?php echo $reader->getFromAddress()->getNameUtf8() ?>
			<?php echo $reader->getFromAddress()->getEmail() ?>
		</td>
	</tr>
	<tr>
		<td>Body</td>
		<td><textarea name="email_source" style="width: 98%; height: 200px; font-family: 'Monaco', 'Courier New', monospace; font-size: 11px;"><?php echo htmlspecialchars($body->getBodyUtf8()) ?></textarea></td>
	</tr>
</table>

<?php if ($cutter_data): ?>
	<table width="100%">
		<tr class="alt">
			<td colspan="2">Cutter</td>
		</tr>
		<tr>
			<td><textarea name="email_source" style="width: 98%; height: 200px; font-family: 'Monaco', 'Courier New', monospace; font-size: 11px;"><?php echo htmlspecialchars(print_r($cutter_data,1)) ?></textarea></td>
		</tr>
	</table>
<?php endif ?>