<?php $view->extend('DevBundle::layout.html.php') ?>

<table width="100%">
	<tr class="alt">
		<td width="180">Model</td>
		<td>Info</Td>
	</tr>
	<?php foreach ($all_metadata as $metadata): ?>
		<?php $alias = preg_replace('#^Application\\\\(.*?)\\\\Entity\\\\(.*?)$#', '$1:$2', $metadata->name); ?>
		<tr>
			<td><?php echo $alias ?></td>
			<td>
				<ul>
					<li>Classname: <a href="<?php echo $view['router']->generate('dev_seefile', array('file' => $metadata->getReflectionClass()->getFileName())) ?>"><?php echo $metadata->name ?></a></li>
					<li><a href="<?php echo $view['router']->generate('dev_models_getsql', array('model' => $alias)) ?>">Get SQL</a></li>
				</ul>
			</td>
		</tr>
	<?php endforeach ?>
</table>