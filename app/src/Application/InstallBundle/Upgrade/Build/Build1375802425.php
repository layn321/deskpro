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

class Build1375802425 extends AbstractBuild
{
	public function run()
	{
		$this->out("Add articles.date_updated and articles.date_last_comment");
		$this->execMutateSql("ALTER TABLE articles ADD date_updated DATETIME DEFAULT NULL, ADD date_last_comment DATETIME DEFAULT NULL");
		$this->execMutateSql("CREATE INDEX date_updated_idx ON articles (date_updated)");
		$this->execMutateSql("CREATE INDEX date_last_comment_idx ON articles (date_last_comment)");

		//------

		$this->out("Add kb_subscriptions table");
		$this->execMutateSql("CREATE TABLE kb_subscriptions (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, article_id INT DEFAULT NULL, category_id INT DEFAULT NULL, INDEX IDX_1F05AAF5217BBB47 (person_id), INDEX IDX_1F05AAF57294869C (article_id), INDEX IDX_1F05AAF512469DE2 (category_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
		$this->execMutateSql("ALTER TABLE kb_subscriptions ADD CONSTRAINT FK_1F05AAF5217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE");
		$this->execMutateSql("ALTER TABLE kb_subscriptions ADD CONSTRAINT FK_1F05AAF57294869C FOREIGN KEY (article_id) REFERENCES articles (id) ON DELETE CASCADE");
		$this->execMutateSql("ALTER TABLE kb_subscriptions ADD CONSTRAINT FK_1F05AAF512469DE2 FOREIGN KEY (category_id) REFERENCES article_categories (id) ON DELETE CASCADE");

		//------

		$this->out("Add sendmail_queue.priority");
		$this->execMutateSql("ALTER TABLE sendmail_queue ADD priority INT NOT NULL");

		//------

		$this->out("Adding new task to send KB subs");
		$j = new \Application\DeskPRO\Entity\WorkerJob();
		$j['id'] = 'kb_subscriptions';
		$j['worker_group'] = 'kb_subscriptions';
		$j['title'] = 'KB Subscriptions';
		$j['description'] = 'Sends notifications to users who are subscribed to articles or categories';
		$j['job_class'] = 'Application\\DeskPRO\\WorkerProcess\\Job\\KbSubscriptions';
		$j['interval'] = \Application\DeskPRO\WorkerProcess\Job\KbSubscriptions::DEFAULT_INTERVAL;
		$this->container->getEm()->persist($j);
		$this->container->getEm()->flush();
	}
}