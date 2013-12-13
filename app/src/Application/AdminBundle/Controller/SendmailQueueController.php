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
 * @subpackage AdminBundle
 */

namespace Application\AdminBundle\Controller;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;
use Application\DeskPRO\Mail\SendmailUtil;
use Orb\Util\Arrays;
use Orb\Util\Numbers;

class SendmailQueueController extends AbstractController
{
	####################################################################################################################
	# index
	####################################################################################################################

	public function indexAction()
	{
		$count_queued = $this->db->fetchColumn("SELECT COUNT(*) FROM sendmail_queue WHERE date_next_attempt IS NOT NULL");
		$count_failed = $this->db->fetchColumn("SELECT COUNT(*) FROM sendmail_queue WHERE date_next_attempt IS NULL");

		#----------------------------------------
		# View options
		#----------------------------------------

		$show_queued = false;
		$show_failed = false;

		$view_options = array();
		if (!$this->in->getBool('show_queued') && !$this->in->getBool('show_failed')) {
			if ($count_failed) {
				$show_failed = true;
				$view_options['show_failed'] = true;
			} else {
				$show_queued = true;
				$view_options['show_queued'] = true;
			}
		} else {
			$show_failed = $this->in->getBool('show_failed');
			$show_queued = $this->in->getBool('show_queued');

			$view_options['show_failed'] = $show_failed;
			$view_options['show_queued'] = $show_queued;
		}

		$filter_to = $this->in->getString('filter_to');
		if ($filter_to) {
			$view_options['filter_to'] = $filter_to;
		}

		$filter_from = $this->in->getString('filter_from');
		if ($filter_from) {
			$view_options['filter_from'] = $filter_from;
		}

		$filter_subject = $this->in->getString('filter_subject');
		if ($filter_subject) {
			$view_options['filter_subject'] = $filter_subject;
		}

		$page = $this->in->getUint('page');
		if (!$page) {
			$page = 1;
		}

		$per_page = 500;

		#----------------------------------------
		# Find results
		#----------------------------------------

		$sql = "SELECT * FROM sendmail_queue ";
		$sql_count = "SELECT COUNT(*) FROM sendmail_queue ";

		$wheres = array();

		if (!($show_queued AND $show_failed)) {
			if ($show_queued) {
				$wheres[] = "date_next_attempt IS NOT NULL";
			} else {
				$wheres[] = "date_next_attempt IS NULL";
			}
		}

		if ($filter_to) {
			$wheres[] = "to_address LIKE " . $this->db->quote("%$filter_to%");
		}
		if ($filter_from) {
			$wheres[] = "from_address LIKE " . $this->db->quote("%$filter_from%");
		}
		if ($filter_subject) {
			$wheres[] = "subject LIKE " . $this->db->quote("%$filter_subject%");
		}

		if ($wheres) {
			$sql .= "WHERE " . implode(' AND ', $wheres) . " ";
			$sql_count .= "WHERE " . implode(' AND ', $wheres) . " ";
		}

		$limit_start = ($page - 1) * $per_page;
		$sql .= "ORDER BY id ASC LIMIT $limit_start, $per_page";

		$results     = $this->db->fetchAll($sql);
		$num_results = $this->db->fetchColumn($sql_count);
		$pages       = Numbers::getPaginationPages($num_results	, $page, $per_page, 10);

		#----------------------------------------
		# Deliver results
		#----------------------------------------

		$vars = array(
			'results'      => $results,
			'num_results'  => $num_results,
			'pages'        => $pages,
			'view_options' => $view_options,
			'count_queued' => $count_queued,
			'count_failed' => $count_failed
		);

		return $this->render('AdminBundle:SendmailQueue:index.html.twig', $vars);
	}

	####################################################################################################################
	# view
	####################################################################################################################

	public function viewAction($id)
	{
		$sendmail = $this->em->find('DeskPRO:SendmailQueue', $id);

		if (!$sendmail) {
			throw $this->createNotFoundException();
		}

		$raw_source = $this->container->getBlobStorage()->copyBlobRecordToString($sendmail->blob);

		$raw_source = $sendmail->getMessageAsString();

		$vars = array(
			'sendmail' => $sendmail,
			'raw_source' => $raw_source,
		);

		if ($this->in->getBool('download')) {
			header('Content-Type: message/rfc822; filename=sendmail-' . $id . '.eml');
			header('Content-Disposition: attachment; filename=sendmail-' . $id . '.eml');
			header('Content-Length: ' . $sendmail->blob->filesize);
			echo $raw_source;
			exit;
		}
		if ($this->in->getBool('download_log')) {
			header('Content-Type: plain/text; filename=sendmail-log-' . $id . '.txt');
			header('Content-Disposition: attachment; filename=sendmail-log-' . $id . '.txt');
			header('Content-Length: ' . strlen($sendmail->log));
			echo $sendmail->log;
			exit;
		}

		return $this->render('AdminBundle:SendmailQueue:view.html.twig', $vars);
	}

	####################################################################################################################
	# actions
	####################################################################################################################

	public function massActionsAction()
	{
		$ids = $this->in->getCleanValueArray('sendmail_ids', 'uint', 'discard');

		if (!$ids) {
			return $this->redirectRoute('admin_sendmail_queue_index');
		}

		$action = $this->in->getString('action');

		$email_transports = $this->em->getRepository('DeskPRO:EmailTransport')->findAll();
		$email_transports = Arrays::keyFromData($email_transports, 'id');

		if ($this->in->getBool('confirm') && $this->checkStandardRequestToken()) {
			switch ($action) {
				case 'resend':
					$this->db->updateIn('sendmail_queue', array('date_next_attempt' => date('Y-m-d H:i:s')), $ids);
					return $this->redirectRoute('admin_sendmail_queue_index');
					break;

				case 'resend_from':
					$transport_id = $this->in->getUint('transport_id');
					if (!$transport_id || !isset($email_transports[$transport_id])) {
						throw $this->createNotFoundException();
					}

					$transport = $email_transports[$transport_id];
					$from_email = $transport->match_pattern;

					foreach ($ids as $id) {
						$sendmail = $this->em->createQuery("
							SELECT s, b
							FROM DeskPRO:SendmailQueue s
							LEFT JOIN s.blob b
							WHERE s.id = ?0
						")->setParameters(array($id))->getOneOrNullResult();

						if (!$sendmail) {
							continue;
						}

						$sendmail->date_next_attempt = new \DateTime();
						$this->em->persist($sendmail);

						SendmailUtil::rewriteFromAddress($sendmail, $from_email);

						$this->em->flush();
						$this->em->clear('DeskPRO:SendmailQueue');
						$this->em->clear('DeskPRO:Blob');
					}

					return $this->redirectRoute('admin_sendmail_queue_index');
					break;

				case 'delete':
					foreach ($ids as $id) {
						$sendmail = $this->em->createQuery("
							SELECT s, b
							FROM DeskPRO:SendmailQueue s
							LEFT JOIN s.blob b
							WHERE s.id = ?0
						")->setParameters(array($id))->getOneOrNullResult();

						if (!$sendmail) {
							continue;
						}

						$this->em->remove($sendmail);

						if ($sendmail->blob) {
							$this->container->getBlobStorage()->deleteBlobRecord($sendmail->blob);
						}

						$this->em->flush();
						$this->em->clear('DeskPRO:SendmailQueue');
						$this->em->clear('DeskPRO:Blob');
					}

					return $this->redirectRoute('admin_sendmail_queue_index');
					break;

				default:
					return $this->redirectRoute('admin_sendmail_queue_index');
			}
		}

		$vars = array(
			'ids'              => $ids,
			'count'            => count($ids),
			'action'           => $action,
			'email_transports' => $email_transports,
		);

		return $this->render('AdminBundle:SendmailQueue:mass-actions-confirm.html.twig', $vars);
	}
}