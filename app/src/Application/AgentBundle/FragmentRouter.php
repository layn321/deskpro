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
 * @subpackage AgentBundle
 */

namespace Application\AgentBundle;

use Application\DeskPRO\App;
use Symfony\Component\Routing\RouterInterface;

/**
 * This generates JS hash router
 */
class FragmentRouter
{
	protected $paths = array();
	protected $non_unique = array();

	protected $generator;
	
	public function __construct($generator)
	{
		$this->generator = $generator;
	}

	public function compile($js_classname = null)
	{
		if (!$js_classname) {
			$js_classname = 'window.DeskPRO_FragmentRouter';
		}

		$js = array();
		$js[] = "$js_classname = {\n\n";

		$js[] = "\tbaseUrl: '',\n\n";
		$js[] = "\tfragments: " . json_encode($this->generator->getFragmentInforArray()) . ",\n\n";

		$js[] = <<<EOF
	setBaseUrl: function(baseUrl) {
		this.baseUrl = baseUrl.replace(/\/$/, '');
	},

	hasFragment: function(fragment_name) {
		if (this.fragments[fragment_name] !== undefined) {
			return true;
		}

		return false;
	},

	getFragmentPattern: function(fragment_name) {
		if (!this.hasFragment(fragment_name)) return '';

		return this.fragments[fragment_name]['pattern'] || '';
	},

	getFragmentType: function(fragment_name) {
		if (!this.hasFragment(fragment_name)) return '';

		return this.fragments[fragment_name]['type'] || '';
	},

	getUrl: function(fragment_name, args) {
		var pattern = this.getFragmentPattern(fragment_name);

		var matches = pattern.match(/\{(.*?)\}/g);
		var m = null;
		var val = null;
		for (var i = 0; i < matches.length; i++) {
			m = matches[i];
			if (args[i] === undefined) {
				console.warn('Fragment %s was not provided with enough args: %o', fragment_name, args);
				break;
			}

			var val = args[i];
			if (typeof val == 'function') {
				val = val();
			}

			pattern = pattern.replace(m+'', args[i]);
		}

		return this.baseUrl + pattern;
	},

	getUrlNamedArgs: function(fragment_name, args) {
		var pattern = this.getFragmentPattern(fragment_name);

		Object.each(args, function(v,k) {
			if (typeof v == 'function') {
				v = v();
			}

			pattern = pattern.replace('{' + k + '}', v);
		});

		return this.baseUrl + pattern;
	}
}
EOF;

		return implode('', $js);
	}
}
