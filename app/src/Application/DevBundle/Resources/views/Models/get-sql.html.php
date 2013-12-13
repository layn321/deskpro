<?php $view->extend('DevBundle::layout.html.php') ?>

<h1>Get Model SQL</h1>
<form action="<?php echo $view['router']->generate('dev_models_getsql') ?>" method="get">
	Model: <input type="text" name="model" value="<?php echo $view->escape($model) ?>" /> <input type="submit" value="Get SQL" />
</form>

<?php if ($model): ?>

<hr />

	<h3><?php echo $model ?></h3>

	<textarea style="width: 95%; height: 200px; font-family: 'Monaco', 'Courier New', monospace;"><?php echo $view->escape(implode("\n\n", $all_sql)) ?></textarea>

<?php elseif ($all_sql): ?>

	<hr />

	<h3>Database</h3>

	<textarea style="width: 95%; height: 200px; font-family: 'Monaco', 'Courier New', monospace;"><?php echo $view->escape(implode("\n\n", $all_sql)) ?></textarea>

<?php endif ?>