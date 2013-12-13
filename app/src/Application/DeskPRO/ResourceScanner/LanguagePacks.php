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
 * @category Controller
 */

namespace Application\DeskPRO\ResourceScanner;

use Application\DeskPRO\App;
use Orb\Util\Arrays;

class LanguagePacks
{
	/**
	 * @var string
	 */
	protected $pack_root;

	public function __construct($pack_root = null)
	{
		if ($pack_root === null) {
			$pack_root = DP_ROOT.'/languages';
		}

		$this->pack_root = $pack_root;
	}

	public function getPacks()
	{
		$pack_root = str_replace(DIRECTORY_SEPARATOR, '/', $this->pack_root);

		$finder = new \Symfony\Component\Finder\Finder();
		$finder->files()->name('LangPackage.php')->in(array($pack_root));

		$packs = array();

		foreach ($finder as $file) {
			$class = str_replace(DIRECTORY_SEPARATOR, '/', $file->getPathname());
			$class = str_replace($pack_root, '', $class);
			$class = str_replace('/', '\\', $class);
			$class = str_replace('.php', '', $class);
			$class = 'DeskproLanguages' . $class;

			require_once($file->getPathname());
			$name = $class::getTitle();

			$packs[$class] = $name;
		}

		return $packs;
	}
}
