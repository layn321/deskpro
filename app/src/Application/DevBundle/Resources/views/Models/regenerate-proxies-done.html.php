<?php $view->extend('DevBundle::layout.html.php') ?>

Done regenerating proxies!

<hr />
<p>
	Note: If you want to avoid doing this every time, edit <var>sys/config/config.yml</var>
	and <var>enable auto_generate_proxy_classes</var>.
</p>

<p>
	It's disabled because there are many entities at this point, and regenerating proxies on every load is slow and
	unnecessary if you aren't actually working with them.
</p>

<p>
	Proxies are automatically regenerated when you perform an upgrade.
</p>