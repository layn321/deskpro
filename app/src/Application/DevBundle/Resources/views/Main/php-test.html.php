<?php $view->extend('DevBundle::layout.html.php') ?>
<h1>PHP Test</h1>
<p>Run PHP code in the context of DeskPRO. Alternatively you might want to edit the DevBundle:Test controller at /dev/test.</p>
<form action="<?php echo $view['router']->generate('dev_phptest_run') ?>" target="runframe" method="post">
<textarea style="width: 95%; height: 400px; font-family: 'Monaco', 'Courier New', monospace;" name="code"></textarea>
<input type="submit" value="Run Code" />
</form>
<hr />
<iframe name="runframe" style="width:99%; height: 500px;"></iframe>