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
 * @subpackage Plugins
 */

namespace Application\DeskPRO\Plugin\LangPackage;

use Orb\Util\Util;

abstract class AbstractLangPackage
{
	/**
	 * Get the version of this lang pack
	 *
	 * @return mixed
	 */
	public static function getVersion()
	{
		return '0.0.1';
	}


	/**
	 * Get the version of DeskPRO this lang pack was designed for
	 *
	 * @return mixed
	 */
	public static function getSourceVersion()
	{
		return '0000-00-00 00:00:00';
	}


	/**
	 * Get the default locale used with the language
	 *
	 * @return string
	 */
	public static function getLocale()
	{
		return 'en_US';
	}


	/**
	 * Get the unique name for the lang
	 *
	 * @return string
	 */
	public static function getName()
	{
		return str_replace('\\', '_', Util::getClassNamespace(get_called_class()));
	}


	/**
	 * Get the readable title for this plugin
	 *
	 * @return string
	 */
	public static function getTitle()
	{
		return ucwords(str_replace('\\', ' ', Util::getClassNamespace(get_called_class())));
	}


	/**
	 * Get the readable description for this plugin
	 *
	 * @return string
	 */
	public static function getDescription()
	{
		return '';
	}


	/**
	 * Get the path to the lang file directory
	 *
	 * @return string
	 */
	public static function getLangPath()
	{
		$path = dirname(Util::getClassFilename(get_called_class()));
		return $path;
	}
}
