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

namespace Application\InstallBundle\Upgrade\Build;

use Orb\Util\Arrays;

class Build1369921235 extends AbstractBuild
{
	public function run()
	{
		$this->out("Add object_lang");

		$this->execMutateSql("CREATE TABLE object_lang (id INT AUTO_INCREMENT NOT NULL, language_id INT DEFAULT NULL, ref VARCHAR(200) NOT NULL, prop_name VARCHAR(100) NOT NULL, value LONGTEXT NOT NULL, INDEX IDX_AC1CB87182F1BAF4 (language_id), UNIQUE INDEX prop_ref (ref, prop_name, language_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
		$this->execMutateSql("ALTER TABLE object_lang ADD CONSTRAINT FK_AC1CB87182F1BAF4 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE CASCADE");

		$lang_id = $this->container->getDb()->fetchColumn("SELECT value FROM settings WHERE name = 'core.default_language_id'");
		if (!$lang_id) {
			$lang_id = 1;
		}

		#------------------------------
		# Ticket snippet cats over to generic snippets
		#------------------------------

		$cat_map = array();

		$this->out("Move ticket snippets categories to the generic table");
		$raw = $this->container->getDb()->fetchAll("
			SELECT *
			FROM ticket_snippet_categories
		");

		foreach ($raw as $r) {
			$this->container->getDb()->insert('text_snippet_categories', array(
				'person_id' => $r['person_id'],
				'typename'  => 'tickets',
				'is_global' => $r['is_global'] ? 1 : 0,
				'title'     => $r['title'],
			));

			$cat_map[$r['id']] = $this->container->getDb()->lastInsertId();
		}

		#------------------------------
		# Ticket snippets over to generic snippets
		#------------------------------

		$this->out("Move ticket snippets to the generic table");
		$raw = $this->container->getDb()->fetchAll("
			SELECT *
			FROM ticket_snippets
		");

		$find_replace = array(
			'{{ ticket.department }}'           => '{{ ticket.department.title }}',
			'{{ ticket.product }}'              => '{{ ticket.product.title }}',
			'{{ ticket.category }}'             => '{{ ticket.category.title }}',
			'{{ ticket.workflow }}'             => '{{ ticket.workflow.title }}',
			'{{ ticket.priority }}'             => '{{ ticket.priority.title }}',
			'{{ ticket.agent }}'                => '{{ ticket.agent.display_name }}',
			'{{ ticket.agent_email }}'          => '{{ ticket.agent.primary_email }}',
			'{{ ticket.agent_team }}'           => '{{ ticket.agent_team.name }}',
			'{{ user.name }}'                   => '{{ ticket.person.display_name }}',
			'{{ user.email }}'                  => '{{ ticket.person.primary_email }}',
			'{{ org.name }}'                    => '{{ ticket.person.organization.name }}',
			'{{ user.organization_position }}'  => '{{ ticket.person.organization.name }}',
		);

		foreach ($raw as $r) {
			$snippet = $r['snippet_html'] ? $r['snippet_html'] : nl2br(htmlspecialchars($r['snippet']));
			$snippet = str_replace(array_keys($find_replace), array_values($find_replace), $snippet);

			$this->container->getDb()->insert('text_snippets', array(
				'person_id'     => $r['person_id'],
				'category_id'   => isset($cat_map[$r['category_id']]) ? $cat_map[$r['category_id']] : Arrays::getFirstItem($cat_map),
				'title'         => $r['title'],
				'snippet'       => $snippet,
				'shortcut_code' => $r['shortcut_code'] ?: ''
			));
		}

		#------------------------------
		# Now copy all snippet texts to new object lang system
		#------------------------------

		$this->out("Copy existing snippet values to object lang");
		$raw = $this->container->getDb()->fetchAll("
			SELECT id, title, snippet
			FROM text_snippets
		");

		$batch = array();
		foreach ($raw as $r) {
			$batch[] = array(
				'ref'         => "text_snippets.{$r['id']}",
				'language_id' => $lang_id,
				'prop_name'   => 'title',
				'value'       => $r['title'],
			);
			$batch[] = array(
				'ref'         => "text_snippets.{$r['id']}",
				'language_id' => $lang_id,
				'prop_name'   => 'snippet',
				'value'       => $r['snippet'],
			);

			if (count($batch) > 40) {
				$this->container->getDb()->batchInsert('object_lang', $batch);
				$batch = array();
			}
		}
		if ($batch) {
			$this->container->getDb()->batchInsert('object_lang', $batch);
			$batch = array();
		}

		$this->out("Copy existing snippet cat values to object lang");
		$raw = $this->container->getDb()->fetchAll("
			SELECT id, title
			FROM text_snippet_categories
		");

		$batch = array();
		foreach ($raw as $r) {
			$batch[] = array(
				'ref'         => "text_snippet_categories.{$r['id']}",
				'language_id' => $lang_id,
				'prop_name'   => 'title',
				'value'       => $r['title'],
			);

			if (count($batch) > 40) {
				$this->container->getDb()->batchInsert('object_lang', $batch);
				$batch = array();
			}
		}
		if ($batch) {
			$this->container->getDb()->batchInsert('object_lang', $batch);
			$batch = array();
		}

		#------------------------------
		# Now we can drop the old string cols
		#------------------------------

		$this->execMutateSql("ALTER TABLE text_snippets DROP title, DROP snippet");
		$this->execMutateSql("ALTER TABLE text_snippet_categories DROP title");

		#------------------------------
		# And remove the old ticket snippet tables
		#------------------------------

		$this->execMutateSql("DROP TABLE ticket_snippets");
		$this->execMutateSql("DROP TABLE ticket_snippet_categories");
	}
}