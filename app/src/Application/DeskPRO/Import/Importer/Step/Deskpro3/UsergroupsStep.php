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
 * @subpackage Import
 */

namespace Application\DeskPRO\Import\Importer\Step\Deskpro3;

use Application\DeskPRO\Entity\Usergroup;

class UsergroupsStep extends AbstractDeskpro3Step
{
	/**
	 * @var array
	 */
	public $perms;
	public $ticket_cats;
	public $faq_cats;
	public $files_cats;
	public $feedback_types;

	public static function getTitle()
	{
		return 'Import Usergroups';
	}

	public function run($page = 1)
	{
		$usergroups = $this->getOldDb()->fetchAll("SELECT * FROM user_groups ORDER BY id ASC");

		$this->logMessage(sprintf("Importing %d usergroups", count($usergroups)));
		if (!$usergroups) {
			return;
		}

		$start_time = microtime(true);

		$scanner = new \Application\InstallBundle\Data\UserGroupPermScanner();
		$this->perms = $scanner->getNames();

		$this->ticket_cats = $this->getOldDb()->fetchAllKeyed("SELECT * FROM ticket_cat ORDER BY displayorder ASC");
		$this->ticket_cats = \Orb\Util\Arrays::intoHierarchy($this->ticket_cats, 0, 'parent');

		$this->faq_cats = $this->getOldDb()->fetchAllKeyed("SELECT * FROM faq_cats ORDER BY displayorder ASC");
		$this->faq_cats = \Orb\Util\Arrays::intoHierarchy($this->faq_cats, 0, 'parent');

		$this->files_cats = $this->getOldDb()->fetchAllKeyed("SELECT * FROM files_cats ORDER BY displayorder ASC");

		$this->feedback_types = $this->getDb()->fetchAllKeyed("SELECT * FROM feedback_categories");

		$this->getDb()->beginTransaction();

		try {
			foreach ($usergroups as $group_info) {
				$this->processUsergroup($group_info);
			}

			$this->getDb()->commit();
		} catch (\Exception $e) {
			$this->getDb()->rollback();
			throw $e;
		}

		// Clean up
		$this->getDb()->beginTransaction();
		try {

			// Parents never have permissions of their own, so we should
			// delete any that we might've inserted with processing

			$parent_ids = $this->getDb()->fetchAllCol("SELECT DISTINCT(parent_id) FROM departments WHERE parent_id IS NOT NULL");
			if ($parent_ids) {
				$this->getDb()->executeUpdate("
					DELETE FROM department_permissions
					WHERE department_id IN (" . implode(',', $parent_ids) . ")
				");
			}

			$this->getDb()->commit();
		} catch (\Exception $e) {
			$this->getDb()->rollback();
			throw $e;
		}

		$end_time = microtime(true);
		$this->logMessage(sprintf("Done all usergroups. Took %.3f seconds.", $end_time-$start_time));
	}

	public function processUsergroup(array $group_info)
	{
		#------------------------------
		# Make sure we havent already done them
		#------------------------------

		$check_exist = $this->getMappedNewId('usergroup', $group_info['id']);
		if ($check_exist) {
			$this->getLogger()->log("{$group_info['id']} already mapped, skipping", 'DEBUG');
			return;
		}

		#------------------------------
		# Copy permissions
		#------------------------------

		$insert_perms = array();
		foreach ($this->perms as $n) {
			$insert_perms[$n] = 1;
		}


		//-----
		// Tickets
		//-----

		if (!$group_info['p_ticket']) {
			unset($insert_perms['tickets.use']);
			unset($insert_perms['tickets.reopen_resolved']);
		}


		//-----
		// Chat
		//-----

		if (!$group_info['p_chat']) {
			unset($insert_perms['chat.use']);
		}

		//-----
		// KB
		//-----

		if (!$group_info['p_kb']) {
			unset(
				$insert_perms['articles.use'],
				$insert_perms['articles.rate'],
				$insert_perms['articles.comment'],
				$insert_perms['articles.comment_validate']
			);
		} else {
			if (!$group_info['p_kb_comment']) {
				unset($insert_perms['articles.comment']);
				unset($insert_perms['articles.comment_validate']);
			}
			if (!$group_info['p_kb_rate']) {
				unset($insert_perms['articles.rate']);
			}
		}

		//-----
		// Downloads
		//-----

		if (!$group_info['p_dl']) {
			unset(
				$insert_perms['downloads.use'],
				$insert_perms['downloads.rate'],
				$insert_perms['downloads.comment'],
				$insert_perms['downloads.comment_validate']
			);
		} else {
			if (!$group_info['p_kb'] || !$group_info['p_kb_comment']) {
				unset($insert_perms['downloads.comment']);
				unset($insert_perms['downloads.comment_validate']);
			}
			if (!$group_info['p_kb'] || !$group_info['p_kb_rate']) {
				unset($insert_perms['downloads.rate']);
			}
		}

		//-----
		// News
		//-----

		if (!$group_info['p_kb'] || !$group_info['p_kb_comment']) {
			unset($insert_perms['news.comment']);
			unset($insert_perms['news.comment_validate']);
		}
		if (!$group_info['p_kb'] || !$group_info['p_kb_rate']) {
			unset($insert_perms['news.rate']);
		}

		//-----
		// Feedback
		//-----

		if (!isset($group_info['p_ideas']) || !$group_info['p_ideas']) {
			unset(
				$insert_perms['feedback.use'],
				$insert_perms['feedback.submit'],
				$insert_perms['feedback.no_submit_validate'],
				$insert_perms['feedback.rate'],
				$insert_perms['feedback.comment'],
				$insert_perms['feedback.no_comment_validate']
			);
		} else {
			if (!$group_info['p_ideas_new']) {
				unset($insert_perms['feedback.submit']);
				unset($insert_perms['feedback.no_submit_validate']);
			} elseif (!$group_info['p_ideas_new_visible']) {
				unset($insert_perms['feedback.no_submit_validate']);
			}
			if (!$group_info['p_ideas_comment_new']) {
				unset($insert_perms['feedback.comment']);
				unset($insert_perms['feedback.no_comment_validate']);
			}
			if (!$group_info['p_ideas_vote']) {
				unset($insert_perms['feedback.rate']);
			}
		}

		//-----
		// Departments
		//-----

		$insert_depperms = array();

		$dep_perms = $this->getOldDb()->fetchAllCol("SELECT category FROM ticket_cat_permissions WHERE usergroup = ?", array($group_info['id']));
		foreach ($this->ticket_cats as $cat) {
			// top levels are on if theyre added or theyre set to inherit (ie inherit from parent 0 magically means give permission)
			if ($cat['perm_inherit'] || in_array($cat['id'], $dep_perms)) {
				$insert_depperms[] = $this->getMappedNewId('ticket_category', $cat['id']);
				if ($cat['children']) {
					foreach ($cat['children'] as $subcat) {
						if ($subcat['perm_inherit']) {
							$insert_depperms[] = $this->getMappedNewId('ticket_category', $subcat['id']);
						} elseif (in_array($subcat['id'], $dep_perms)) {
							$insert_depperms[] = $this->getMappedNewId('ticket_category', $subcat['id']);
						}
					}
				}
			}
		}

		//-----
		// Article Cats
		//-----

		$self = $this;
		$insert_faqperms = array();
		$cat_perms = $this->getOldDb()->fetchAllCol("SELECT catid FROM faq_permissions WHERE groupid = ?", array($group_info['id']));

		$fn_proc_cat = function($cat) use ($self, $cat_perms, &$fn_proc_cat, &$insert_faqperms) {
			if ($cat['perm_inherit'] || in_array($cat['id'], $cat_perms)) {
				$insert_faqperms[] = $self->getMappedNewId('faq_cat', $cat['id']);
				if ($cat['children']) {
					foreach ($cat['children'] as $subcat) {
						$fn_proc_cat($subcat);
					}
				}
			}
		};

		foreach ($this->faq_cats as $cat) {
			$fn_proc_cat($cat);
		}

		//-----
		// Files Cats
		//-----

		$insert_filesperms = array();

		$cat_perms = $this->getOldDb()->fetchAllCol("SELECT catid FROM files_permissions WHERE groupid = ?", array($group_info['id']));
		foreach ($this->files_cats as $cat) {
			if (in_array($cat['id'], $cat_perms)) {
				$insert_filesperms[] = $this->getMappedNewId('file_cat', $cat['id']);
			}
		}

		// The new top level we created
		if ($this->getMappedNewId('file_cat', 0)) {
			$insert_filesperms[] = $this->getMappedNewId('file_cat', 0);
		}

		#------------------------------
		# Create it
		#------------------------------

		if ($group_info['system_name'] == 'guest') {
			$this->_insertPerms($insert_perms, $insert_depperms, $insert_faqperms, $insert_filesperms, Usergroup::EVERYONE_ID);
		} elseif ($group_info['system_name'] == 'registered') {
			$this->_insertPerms($insert_perms, $insert_depperms, $insert_faqperms, $insert_filesperms, Usergroup::REG_ID);
			$this->saveMappedId('usergroup', $group_info['id'], Usergroup::REG_ID);
			$this->saveMappedId('usergroup_sys', 'registered', Usergroup::REG_ID);
		} else {
			$usergroup = new Usergroup();
			$usergroup->title = $group_info['name'];
			$this->getEm()->persist($usergroup);
			$this->getEm()->flush();

			$this->saveMappedId('usergroup', $group_info['id'], $usergroup->id);
			$this->_insertPerms($insert_perms, $insert_depperms, $insert_faqperms, $insert_filesperms, $usergroup->id);
		}
	}

	public function _insertPerms($insert_perms, $insert_depperms, $insert_faqperms, $insert_filesperms, $ug_id)
	{
		foreach ($insert_perms as $k => $v) {
			$this->getDb()->replace('permissions', array(
				'usergroup_id' => $ug_id,
				'name' => $k,
				'value' => 1
			));
		}

		$insert_depperms = array_unique($insert_depperms);
		foreach ($insert_depperms as $v) {
			if (!$v) continue;
			$this->getDb()->replace('department_permissions', array(
				'usergroup_id' => $ug_id,
				'department_id' => $v,
				'app' => 'tickets',
				'name' => 'full',
				'value' => 1
			));
		}

		$insert_faqperms = array_unique($insert_faqperms);
		foreach ($insert_faqperms as $v) {
			if (!$v) continue;
			$this->getDb()->replace('article_category2usergroup', array(
				'usergroup_id' => $ug_id,
				'category_id' => $v,
			));
		}

		$insert_filesperms = array_unique($insert_filesperms);
		foreach ($insert_filesperms as $v) {
			if (!$v) continue;
			$this->getDb()->replace('download_category2usergroup', array(
				'usergroup_id' => $ug_id,
				'category_id' => $v,
			));
		}

		// DP3 didnt have the concept of types, so
		// the ones we created are default and should be usable by all

		foreach ($this->feedback_types as $type) {
			$this->getDb()->replace('feedback_category2usergroup', array(
				'usergroup_id' => $ug_id,
				'category_id' => $type['id'],
			));
		}
	}
}
