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
 * @category Translate
 */

namespace Application\DeskPRO\Translate;

use Application\DeskPRO\App;

use Orb\Util\Util;

/**
 * This takes an object, and then based on its state, produces a phrase ID that
 * we can use to look up a phrase. This is how we translate thigns like category
 * titles. The category itself becomes a "phrase", and is handled like any other.
 */
class ObjectPhraseNamer
{
	public function getPhraseName($object, $property = null)
	{
		$id = null;
		if (method_exists($object, 'getId')) {
			$id = $object->getId();
		} elseif ($object instanceof \ArrayAccess AND isset($object['id'])) {
			$id = $object['id'];
		}

		if ($id) {
			$baseclass = Util::getBaseClassname($object);
			$prefix = 'obj_' . strtolower($baseclass) . '.';
			$name = $prefix . $id;
			if ($property) {
				$name .= '_' . $property;
			}
			return $name;
		}

		return null;
	}

	public function getPhraseDefault($object, $property = null)
	{
		if ($object instanceof \ArrayAccess) {
			if ($property === null) {
				if (isset($object['full_title'])) {
					return $object['full_title'];
				} elseif (isset($object['title'])) {
					return $object['title'];
				} elseif (isset($object['name'])) {
					return $object['title'];
				}
			}

			if (isset($object[$property])) {
				return $object[$property];
			}
		}

		return null;
	}
}
