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
 * @subpackage ContentRevision
 */

namespace Application\DeskPRO\ContentRevision;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Person;

use Orb\Util\Arrays;

class Util
{
	private function __construct() {}

	public static function findOrCreate($content, $edit_field, Person $person)
	{
		$entity = self::getRevisionClass($content, true);
		$field  = self::getContentField($content);

		$em = App::getOrm();

		$timesnip = date_create('-1 hours');

		// For us to reuse a rev, it has to:
		// - be the latest one (ie no other revisions by other people)
		// - no more than an hour old
		// - and not a currently set field

		$rev = $em->createQuery("
			SELECT r
			FROM $entity r
			WHERE r.$field = ?1
			ORDER BY r.id DESC
		")->setMaxResults(1)->setParameters(array(1=> $content))->getOneOrNullResult();

		$has_field = false;
		if ($rev) {
			foreach ((array)$edit_field as $f) {
				if ($rev[$f]) {
					$has_field = true;
					break;
				}
			}
		}

		if (!$rev OR $has_field OR $rev->person['id'] != $person['id'] OR $rev['date_created'] < $timesnip) {

			$rev_class = self::getRevisionClass($content);
			$rev = new $rev_class;
			$rev->person = $person;
			$rev->$field = $content;
		}

		return $rev;
	}

	public static function compareRevisions($entity, $rev_old_id, $rev_new_id)
	{
		if ($rev_old_id > $rev_new_id) {
			$tmp = $rev_old_id;
			$rev_old_id = $rev_new_id;
			$rev_new_id = $tmp;
		}

		$rev_old = App::findEntity($entity, $rev_old_id);
		$rev_new = App::findEntity($entity, $rev_new_id);

		if (!$rev_old || !$rev_new) {
			return array('rendered_content_diff' => '', 'rendered_title_diff' => '');
		}

		$old_data = $rev_old->toArray();
		$new_data = $rev_new->toArray();

		$field = self::getContentField($rev_new);

		$rendered_content_diff = null;
		if ($new_data['content']) {

			// We need to go back to find the last change for old
			if (!$old_data['content']) {
				$r = App::getOrm()->createQuery("
					SELECT r
					FROM $entity r
					WHERE r.$field = ?1 AND r.id < ?2 AND r.content != ''
					ORDER BY r.id DESC
				")->setMaxResults(1)->setParameters(array(1=> $rev_new[$field], 2=> $rev_old_id))->getOneOrNullResult();
				if ($r['content']) {
					$old_data['content'] = $r['content'];
				} else {
					$old_data['content'] = '';
				}
			}

			$diff = new \FineDiff(
				$rev_old['content'],
				$rev_new['content'],
				\FineDiff::$wordGranularity
			);
			$rendered_diff = $diff->renderDiffToHTML();

			$rendered_diff = html_entity_decode($rendered_diff);
			$rendered_content_diff = nl2br($rendered_diff);
		}

		$rendered_title_diff = null;
		if ($new_data['title']) {

			if (!$old_data['title']) {
				$r = App::getOrm()->createQuery("
					SELECT r
					FROM $entity r
					WHERE r.$field = ?1 AND r.id < ?2 AND r.title != ''
					ORDER BY r.id DESC
				")->setMaxResults(1)->setParameters(array(1=> $rev_new[$field], 2=> $rev_old_id))->getOneOrNullResult();
				if ($r['title']) {
					$old_data['title'] = $r['title'];
				} else {
					$old_data['title'] = '';
				}
			}

			$diff = new \FineDiff(
				$rev_old['title'],
				$rev_new['title'],
				\FineDiff::$characterGranularity
			);
			$rendered_title_diff = $diff->renderDiffToHTML();

		}

		$use_blob = false;
		if ($field == 'download' && !empty($rev_new['blob'])) {
			$new_data['blob'] = $rev_new['blob'];

			$use_blob = true;
			$r = $rev_old;
			if (!$r['blob']) {
				$r = App::getOrm()->createQuery("
					SELECT r
					FROM $entity r
					WHERE r.$field = ?1 AND r.id < ?2 AND r.blob IS NOT NULL
					ORDER BY r.id DESC
				")->setMaxResults(1)->setParameters(array(1=> $rev_new[$field], 2=> $rev_old_id))->getOneOrNullResult();
			}

			if ($r['blob']) {
				$old_data['blob'] = $r['blob'];
			} else {
				$old_data['blob'] = null;
			}
		}

		$ret = array(
			'rendered_content_diff' => $rendered_content_diff,
			'rendered_title_diff' => $rendered_title_diff,
		);

		if ($use_blob) {
			$ret['old_blob'] = $old_data['blob'];
			$ret['new_blob'] = $new_data['blob'];
		}

		return $ret;
	}

	public static function getContentField($content)
	{
		$type = get_class($content);

		switch ($type) {
			case 'Application\\DeskPRO\\Entity\\Article':
			case 'Application\\DeskPRO\\Entity\\ArticleRevision':
				return 'article';
				break;

			case 'Application\\DeskPRO\\Entity\\News':
			case 'Application\\DeskPRO\\Entity\\NewsRevision':
				return 'news';
				break;

			case 'Application\\DeskPRO\\Entity\\Download':
			case 'Application\\DeskPRO\\Entity\\DownloadRevision':
				return 'download';
				break;

			case 'Application\\DeskPRO\\Entity\\Feedback':
			case 'Application\\DeskPRO\\Entity\\FeedbackRevision':
				return 'feedback';
				break;
		}

		throw new \InvalidArgumentException("Unknown type `$type`");
	}

	public static function getRevisionClass($content, $entity_name = false)
	{
		$type = get_class($content);

		switch ($type) {
			case 'Application\\DeskPRO\\Entity\\Article':
				if ($entity_name) {
					return 'DeskPRO:ArticleRevision';
				} else {
					return 'Application\\DeskPRO\\Entity\\ArticleRevision';
				}
				break;

			case 'Application\\DeskPRO\\Entity\\News':
				if ($entity_name) {
					return 'DeskPRO:NewsRevision';
				} else {
					return 'Application\\DeskPRO\\Entity\\NewsRevision';
				}
				break;

			case 'Application\\DeskPRO\\Entity\\Download':
				if ($entity_name) {
					return 'DeskPRO:DownloadRevision';
				} else {
					return 'Application\\DeskPRO\\Entity\\DownloadRevision';
				}
				break;

			case 'Application\\DeskPRO\\Entity\\Feedback':
				if ($entity_name) {
					return 'DeskPRO:FeedbackRevision';
				} else {
					return 'Application\\DeskPRO\\Entity\\FeedbackRevision';
				}
				break;
		}

		throw new \InvalidArgumentException("Unknown type `$type`");
	}
}
