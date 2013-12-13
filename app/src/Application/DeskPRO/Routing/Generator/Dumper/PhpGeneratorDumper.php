<?php
/**************************************************************************\
| DeskPRO (r) has been developed by DeskPRO Ltd. http://www.deskpro.com/   |
| a British company located in London, England.                            |
|                                                                          |
| All source code and content Copyright (c) 2012, DeskPRO Ltd.             |
|                                                                          |
| The license agreement under which this software is released              |
| can be found at http://www.deskpro.com/license                           |
|                                                                          |
| By using this software, you acknowledge having read the license          |
| and agree to be bound thereby.                                           |
|                                                                          |
| Please note that DeskPRO is not free software. We release the full       |
| source code for our software because we trust our users to pay us for    |
| the huge investment in time and energy that has gone into both creating  |
| this software and supporting our customers. By providing the source code |
| we preserve our customers' ability to modify, audit and learn from our   |
| work. We have been developing DeskPRO since 2001, please help us make it |
| another decade.                                                          |
|                                                                          |
| Like the work you see? Think you could make it better? We are always     |
| looking for great developers to join us: http://www.deskpro.com/jobs/    |
|                                                                          |
| ~ Thanks, Everyone at Team DeskPRO                                       |
\**************************************************************************/

/**
 * DeskPRO
 *
 * @package DeskPRO
 */

namespace Application\DeskPRO\Routing\Generator\Dumper;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Generator\Dumper\PhpGeneratorDumper as BasePhpGeneratorDumper;

use Application\DeskPRO\App;

use Orb\Util\Strings;

class PhpGeneratorDumper extends BasePhpGeneratorDumper
{
    public function dump(array $options = array())
	{
		$class = trim(parent::dump($options));

		list($var_code, $method_code) = $this->getClassCode();

		// First opening brace, as in class {
		$pos = strpos($class, '{') + 1;
		$class = Strings::inject($class, "\n" . $var_code . "\n", $pos);

		// Last closing brace, as in } at the end of the class
		$pos = strrpos($class, '}');
		$class = Strings::inject($class, "\n" . $method_code . "\n", $pos);

		$class = str_replace("\$this->context = \$context;", "\$this->setContext(\$context);", $class);

		return $class;
	}

	protected function getClassCode()
	{
		$route_patterns   = array();
		$route_fragments  = array();
		$fragment_names   = array();
		$fragment_types   = array();

		foreach ($this->getRoutes()->all() as $name => $route) {

			$route_patterns[$name] = $route->getPattern();

			$a_name = $route->getOption('fragment_name');
			$a_type = $route->getOption('fragment_type');
			if ($a_name) {
				$fragment_names[$a_name]  = $name;
				$fragment_types[$a_name]  = $a_type ? $a_type : 'page';
				$route_fragments[$name] = $a_name;
			}
		}

		$var_code = array();
		$var_code['routePatterns'] = 'static private $routePatterns = ' . var_export($route_patterns, true) . ';';
		$var_code['routeFragments'] = 'static private $routeFragments = ' . var_export($route_fragments, true) . ';';
		$var_code['fragmentNames']   = 'static private $fragmentNames = ' . var_export($fragment_names, true) . ';';
		$var_code['fragmentTypes']   = 'static private $fragmentTypes = ' . var_export($fragment_types, true) . ';';
		$var_code = implode("\n", $var_code);

		$method_code = <<<EOF
	public function getRoutePattern(\$route_name)
	{
		return isset(self::\$routePatterns[\$route_name]) ? self::\$routePatterns[\$route_name] : null;
	}

	public function getRoutePatterns()
	{
		return self::\$routePatterns;
	}

	public function getFragmentNames()
	{
		return array_keys(self::\$fragmentNames);
	}

	public function getTypeForFragment(\$fragment_name)
	{
		return isset(self::\$fragmentTypes[\$fragment_name]) ? self::\$fragmentTypes[\$fragment_name] : null;
	}

	public function getRouteForFragment(\$fragment_name)
	{
		return isset(self::\$fragmentNames[\$fragment_name]) ? self::\$fragmentNames[\$fragment_name] : null;
	}

	public function getPatternForFragment(\$fragment_name)
	{
		\$route_name = \$this->getRouteForFragment(\$fragment_name);
		if (!\$route_name) return null;

		return \$this->getRoutePattern(\$route_name);
	}

	public function getFragmentPatternMap()
	{
		\$map = array();
		foreach (\$this->getFragmentNames() as \$fragment_name) {
			\$map[\$fragment_name] = \$this->getPatternForFragment(\$fragment_name);
		}

		return \$map;
	}

	public function getFragmentInforArray()
	{
		\$map = array();
		foreach (\$this->getFragmentNames() as \$fragment_name) {
			\$map[\$fragment_name] = array(
				'pattern' => \$this->getPatternForFragment(\$fragment_name),
				'type'    => \$this->getTypeForFragment(\$fragment_name),
			);
		}

		return \$map;
	}

	public function getFragmentForRoute(\$route_name)
	{
		return isset(self::\$routeFragments[\$route_name]) ? self::\$routeFragments[\$route_name] : null;
	}

	public function generateFragment(\$route_name, \$parameters = array())
	{
		\$fragment_name = \$this->getFragmentForRoute(\$route_name);
		if (\$fragment_name === null) {
			throw new \InvalidArgumentException(sprintf('Fragment "%s" does not exist.', \$route_name));
		}

		if (\$parameters) {
			\$fragment = \$fragment_name . ':' . implode(':', \$parameters);
		} else {
			\$fragment = \$fragment_name;
		}

		return \$fragment;
	}
EOF;

		return array($var_code, $method_code);
	}
}
