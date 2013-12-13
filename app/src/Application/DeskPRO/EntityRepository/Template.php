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
 * @category Entities
 */

namespace Application\DeskPRO\EntityRepository;

use Application\DeskPRO\App;
use \Doctrine\ORM\EntityRepository;

use Orb\Util\Numbers;

class Template extends AbstractEntityRepository
{
	public function getTemplateForStyle($template_name, $style = null)
	{
		try {
			if ($style === null OR $style === 0) {
				$q = $this->getEntityManager()->createQuery("
					SELECT t
					FROM DeskPRO:Template t
					WHERE t.style IS NULL AND t.name = ?1
				")->setParameters(array(1=>$template_name));
			} else {
				$q = $this->getEntityManager()->createQuery("
					SELECT t
					FROM DeskPRO:Template t
					WHERE t.style = ?1 AND t.name = ?2
				")->setParameters(array(1=>$style, 2=>$template_name));
			}

			$r = $q->getSingleResult();
			return $r;
		} catch (\Exception $e) {
			return null;
		}
	}

	public function getCustomTemplateNamesInStyle($style)
	{
		$names = App::getDb()->fetchColumn("
			SELECT name
			FROM templates
			WHERE style_id = ?
		", array($style['id']));

		return $names;
	}

	public function getCustomTemplateInfoInStyle($style)
	{
		$names = App::getDb()->fetchAllKeyed("
			SELECT name, date_updated
			FROM templates
			WHERE style_id = ?
		", array($style['id']), 'name');

		return $names;
	}
}
