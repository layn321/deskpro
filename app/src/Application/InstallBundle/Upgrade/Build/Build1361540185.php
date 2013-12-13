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

class Build1361540185 extends AbstractBuild
{
	public function run()
	{
		$this->out("Changes to chat_conversations");
		$this->execMutateSql("ALTER TABLE chat_conversations ADD should_send_transcript TINYINT(1) NOT NULL, ADD date_transcript_sent DATETIME DEFAULT NULL");
		$this->execMutateSql("CREATE INDEX should_send_transcript_idx ON chat_conversations (should_send_transcript)");

		// Set chat abandonded flag now
		// so transcripts dont send on these when next cron
		// decides timeouts were abandonded
		$this->execMutateSql("
			UPDATE chat_conversations
			SET ended_by = 'abandoned'
			WHERE status = 'ended' AND ended_by = 'timeout'
		");

		// Insert new worker job
		$j = new \Application\DeskPRO\Entity\WorkerJob();
		$j['id'] = 'chat_transcripts';
		$j['worker_group'] = 'chat';
		$j['title'] = 'Send Chat Transcripts';
		$j['description'] = 'Send chat transcripts';
		$j['job_class'] = 'Application\\DeskPRO\\WorkerProcess\\Job\\ChatTranscripts';
		$j['interval'] = \Application\DeskPRO\WorkerProcess\Job\ChatTranscripts::DEFAULT_INTERVAL;
		$this->container->getEm()->persist($j);
		$this->container->getEm()->flush();
	}
}