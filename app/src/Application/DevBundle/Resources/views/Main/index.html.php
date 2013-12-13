<?php $view->extend('DevBundle::layout.html.php') ?>

<table width="100%">
	<tr class="alt">
		<td width="180">Place</td>
		<td>Description</Td>
	</tr>
	<tr>
		<td><a href="<?php echo $view['router']->generate('dev_models') ?>">Models</a></td>
		<td>
			<p>Get information about models, and easily generate SQL for models.</p>
			<hr />
			<form action="<?php echo $view['router']->generate('dev_models_getsql') ?>" method="get">
				Get SQL for model: <input type="text" name="model" value="DeskPRO:Ticket" /> <input type="submit" value="Get SQL" />
			</form>
			<hr />
			<a href="<?php echo $view['router']->generate('dev_models_regenerateproxies') ?>">Regenerate proxies</a>: Whenever you update a model class,
			you must regenerate the Doctrine proxies or they won't work.
		</td>
	</tr>
	<tr>
		<td><a href="<?php echo $view['router']->generate('dev_phptest') ?>">PHP Test</a></td>
		<td>
			<p>
				Run arbitrary PHP from a textarea within the context of DeskPRO. Useful when you need to test classes etc
				within DeskPRO where all the autoloading is set up.
			</p>
		</td>
	</tr>
	<tr>
		<td>Quick Tools</td>
		<td>
			<ul>
				<li><a href="<?php echo $view['router']->generate('dev_phpinfo') ?>">PHP Info</a></li>
				<li><a href="<?php echo $view['router']->generate('dev_cm') ?>">Insert Client Messages</a></li>
			</ul>
		</td>
	</tr>
</table>
