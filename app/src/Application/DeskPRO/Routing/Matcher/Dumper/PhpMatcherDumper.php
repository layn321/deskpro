<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\DeskPRO\Routing\Matcher\Dumper;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Matcher\Dumper\PhpMatcherDumper as BasePhpMatcherDumper;

class PhpMatcherDumper extends BasePhpMatcherDumper
{
    public function dump(array $options = array())
    {
		$options['base_class'] = 'Symfony\\Bundle\\FrameworkBundle\\Routing\\RedirectableUrlMatcher';

		$dump = parent::dump($options);
		$dump = str_replace("public function match(\$pathinfo)", "protected function doMatch(\$pathinfo)", $dump);

		$add_match = <<<EOF
public function match(\$pathinfo)
	{
		try {
			return \$this->doMatch(\$pathinfo);
		} catch (ResourceNotFoundException \$e) {
			// Try without trailing
			if (substr(\$pathinfo, -1) == '/') {
				\$pathinfo = rtrim(\$pathinfo, '/');
				\$match = \$this->doMatch(\$pathinfo);
				return \$this->redirect(\$pathinfo, \$match['_route']);
			// Try with trailing slash
			} else {
				\$pathinfo = \$pathinfo . '/';
				\$match = \$this->doMatch(\$pathinfo);
				return \$this->redirect(\$pathinfo, \$match['_route']);
			}
		}
	}
EOF;

		$dump = str_replace("protected function doMatch(", "$add_match\n\n\tprotected function doMatch(", $dump);

		return $dump;
    }
}
