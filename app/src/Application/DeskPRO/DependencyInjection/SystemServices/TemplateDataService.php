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
 * @subpackage
 */

namespace Application\DeskPRO\DependencyInjection\SystemServices;

use Application\DeskPRO\DependencyInjection\DeskproContainer;

class TemplateDataService extends BaseRepositoryService
{
	protected $custom_emails = null;

	public static function create(DeskproContainer $container, array $options = null)
	{
		if (!$options) $options = array();
		$options['entity'] = 'Application\\DeskPRO\\Entity\\Template';

		$em = $container->getEm();
		$o = new static($em, $options);
		return $o;
	}

	/**
	 * Get an array of custom email names by their type.
	 *
	 * @return array
	 */
	public function getCustomEmails()
	{
		if ($this->custom_emails !== null) {
			return $this->custom_emails;
		}

		$this->custom_emails = $this->db->fetchAllGrouped("
			SELECT name, variant_of
			FROM templates
			WHERE variant_of IS NOT NULL
		", array(), 'variant_of', null, 'name');

		return $this->custom_emails;
	}


	/**
	 * @param string $type
	 * @return string
	 */
	public function getCustomEmailsOfType($type)
	{
		$emails = $this->getCustomEmails();
		return isset($emails[$type]) ? $emails[$type] : null;
	}


	/**
	 * Check if a given template is an existing custom email
	 *
	 * @param string $name
	 * @return bool
	 */
	public function customEmailExists($name)
	{
		foreach ($this->getCustomEmails() as $emails) {
			if (in_array($name, $emails)) {
				return true;
			}
		}

		return false;
	}
}