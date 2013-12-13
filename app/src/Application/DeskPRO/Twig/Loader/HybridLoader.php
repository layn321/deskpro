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
 * @category Twig
 */

namespace Application\DeskPRO\Twig\Loader;

use Application\DeskPRO\App;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Symfony\Component\Config\FileLocatorInterface;

/**
 * This hybrid loader loads templates from the filesystem first, and then from the
 * database second if a style is being used and has templates that override it.
 */
class HybridLoader extends \Symfony\Bundle\TwigBundle\Loader\FilesystemLoader
{
	/**
	 * @var \Application\DeskPRO\Entity\Style
	 */
	protected $style = null;

	/**
	 * @var array
	 */
	protected $style_template_info = null;

	/**
	 * @var null
	 */
	protected $crashed_custom_templates = array();

	public function __construct(FileLocatorInterface $locator, TemplateNameParserInterface $parser)
	{
		parent::__construct($locator, $parser);
	}

	protected function _initStyle()
	{
		// Already done
		if ($this->style !== null) return;

		if (!defined('DP_BUILDING')) {
			$this->style = App::getSystemService('style');
			$this->style_template_info = App::getDb()->fetchAllKeyed("
				SELECT id, name, UNIX_TIMESTAMP(date_updated) AS date_updated
				FROM templates
				WHERE style_id = ?
			", array($this->style['id']), 'name');
		} else {
			$this->style = new \Application\DeskPRO\Entity\Style();
		}
	}

	public function markCustomTemplateAsCrashed($name)
	{
		$this->crashed_custom_templates[$name] = true;
	}

	public function dbHasTemplate($name)
	{
		if (isset($this->crashed_custom_templates[(string)$name])) {
			return false;
		}

		$this->_initStyle();
		if (isset($this->style_template_info[(string)$name])) {
			return true;
		}
		return false;
	}

	public function isFresh($name, $time)
    {
		$this->_initStyle();

		$str_name = (string)$name;

		// DB templates are always "fresh" because theyre compiled
		// as soon as they're saved
		if (!isset($this->crashed_custom_templates[$str_name]) && isset($this->style_template_info[$str_name])) {
			return true;
		}

        return parent::isFresh($name, $time);
    }

	public function getCacheKey($name)
    {
		$this->_initStyle();
		return md5((string)$name);
    }

	public function getSource($name)
    {
		$this->_initStyle();

		$str_name = (string)$name;
		if (!isset($this->crashed_custom_templates[$str_name]) && isset($this->style_template_info[$str_name])) {
			return App::getDb()->fetchColumn("
				SELECT template_code
				FROM templates
				WHERE id = ?
			", array($this->style_template_info[$name]['id']));
		}

		$source = file_get_contents($this->findTemplate($name));

		if (strpos($name, 'DeskPRO:emails_') !== false) {
			$proc = new \Application\DeskPRO\Twig\PreProcessor\EmailPreProcessor();
			$source = $proc->process($source, $str_name);
		}

		return $source;
    }

	protected function findTemplate($template)
	{
		$this->_initStyle();

		$logicalName = (string)$template;

		if (!isset($this->crashed_custom_templates[$logicalName]) && isset($this->style_template_info[$logicalName])) {
			return false;
		}

		return parent::findTemplate($template);
	}
}
