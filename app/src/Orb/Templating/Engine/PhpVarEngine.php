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
use \Symfony\Component\Templating\Storage\FileStorage;
use \Symfony\Component\Templating\Storage\StringStorage;
use \Symfony\Component\Templating\Helper\HelperInterface;
use \Symfony\Component\Templating\Loader\LoaderInterface;

/**
 * This renderer is like a normal PHP renderer except that the value is taken from
 * a special $OUTPUT variable. This is useful in cases where lots PHP processing is
 * taking place, or in cases where you don't want superfluous whitespace etc.
 */
class PhpVarEngine extends \Symfony\Bundle\FrameworkBundle\Templating\PhpEngine
{
	public function evaluate(Storage $template, array $parameters = array())
	{
		$OUTPUT = $this->_preProcess($template, $parameters);
		$__template__ = $template;

		extract($parameters, EXTR_SKIP);
		$view = $this;

		ob_start();

		if ($__template__ instanceof FileStorage) {
			extract($parameters);
			$view = $this;
			require $__template__;
		} elseif ($__template__ instanceof StringStorage) {
			eval('; ?>'.$__template__.'<?php ;');
		}

		ob_end_clean();

		if (!isset($OUTPUT)) {
			$OUTPUT = '';
		}

		return $this->_postProcess($OUTPUT);
	}

	protected function _preProcess(Storage $template, array $parameters = array())
	{
		return '';
	}

	protected function _postProcess($OUTPUT)
	{
		if (is_array($OUTPUT)) {
			$OUTPUT = implode('', $OUTPUT);
		}

		return (string)$OUTPUT;
	}

	public function supports($name)
	{
		return false !== strpos($name, '.phpv');
	}
}
