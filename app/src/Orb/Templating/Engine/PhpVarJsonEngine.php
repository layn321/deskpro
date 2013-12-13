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
 * Orb
 *
 * @package Orb
 * @subpackage Templating
 */

namespace Orb\Templating\Engine;

use \Symfony\Component\Templating\Storage\Storage;


/**
 * Works in the same was as PhpVarRenderer except that $OUTPUT is expected to be an
 * array (k=>v), which is then encoded as JSON.
 */
class PhpVarJsonEngine extends PhpVarEngine
{
	protected function _preProcess(Storage $template, array $parameters = array())
	{
		return array();
	}

	protected function _postProcess($OUTPUT)
	{
		if (!is_array($OUTPUT)) {
			$OUTPUT = array((string)$OUTPUT);
		}

		return json_encode($OUTPUT);
	}

	public function supports($name)
	{
		return false !== strpos($name, '.jsonphp');
	}
}
