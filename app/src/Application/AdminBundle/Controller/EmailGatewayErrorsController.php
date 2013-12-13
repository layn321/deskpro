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
 */

namespace Application\AdminBundle\Controller;

use Application\DeskPRO\App;
use DeskPRO\Kernel\KernelErrorHandler;
use Orb\Util\Arrays;
use Orb\Util\Numbers;

use Application\DeskPRO\Entity\EmailGateway;
use Application\DeskPRO\Entity\EmailSource;
use Application\DeskPRO\EmailGateway\Reader\EzcReader;

class EmailGatewayErrorsController extends AbstractController
{
	####################################################################################################################
	# index
	####################################################################################################################

	public function indexAction($type, $object_type = 'ticket')
	{
		$objects = $this->getObjectsForType($object_type);

		if ($type == 'errors') {
			$count = $this->em->getRepository('DeskPRO:EmailSource')->countErrorStatus($objects);
		} elseif ($type == 'all') {
			$count = $this->em->getRepository('DeskPRO:EmailSource')->countAllSources($objects);
		} else {
			$count = $this->em->getRepository('DeskPRO:EmailSource')->countRejectionStatus($objects);
		}

		$per_page = 25;
		$p = $this->in->getUint('p');
		if (!$p) $p = 1;

		$pageinfo = Numbers::getPaginationPages($count, $p, $per_page, 5);

		if ($type == 'errors') {
			$sources = $this->em->createQuery("
				SELECT source
				FROM DeskPRO:EmailSource source
				WHERE source.object_type IN (?0) AND source.status = 'error' AND source.error_code IN ('server_error', 'timeout')
				ORDER BY source.id DESC
			")->setFirstResult(($p - 1) * $per_page)->setMaxResults($per_page)->execute(array($objects));
		} elseif ($type == 'all') {
			$sources = $this->em->createQuery("
				SELECT source
				FROM DeskPRO:EmailSource source
				WHERE source.object_type IN (?0)
				ORDER BY source.id DESC
			")->setFirstResult(($p - 1) * $per_page)->setMaxResults($per_page)->execute(array($objects));
		} else {
			$sources = $this->em->createQuery("
				SELECT source
				FROM DeskPRO:EmailSource source
				WHERE source.object_type IN (?0) AND source.status = 'error' AND source.error_code NOT IN ('server_error', 'timeout')
				ORDER BY source.id DESC
			")->setFirstResult(($p - 1) * $per_page)->setMaxResults($per_page)->execute(array($objects));
		}

		return $this->render('AdminBundle:EmailGatewayErrors:index.html.twig', array(
			'pageinfo'  => $pageinfo,
			'count'     => $count,
			'sources'   => $sources,
			'type'      => $type,
			'object_type' => $object_type
		));
	}

	####################################################################################################################
	# view
	####################################################################################################################

	public function viewAction($id)
	{
		$source = $this->em->find('DeskPRO:EmailSource', $id);

		if (!$source) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$data_structure = null;
		if ($source->source_info) {
			$data_structure = $source->getSourceInfoAsString();
		}

		$type = ($source->error_code == 'server_error' || $source->error_code == 'timeout') ? 'errors' : 'rejections';
		if ($source->status != 'error') {
			$type = 'all';
		}

		return $this->render('AdminBundle:EmailGatewayErrors:view.html.twig', array(
			'source' => $source,
			'data_structure' => $data_structure,
			'type' => $type,
		));
	}

	####################################################################################################################
	# clear
	####################################################################################################################

	public function clearAction($type, $security_token, $object_type = 'ticket')
	{
		$this->ensureAuthToken('clear_gateway_errors', $security_token);

		$objects = $this->getObjectsForType($object_type);
		$objects_quoted = array();
		foreach ($objects AS $object) {
			$objects_quoted[] = App::getDb()->quote($object);
		}

		if ($type == 'errors') {
			$where = "object_type IN (" . implode(',', $objects_quoted) . ") AND status = 'error' AND error_code IN ('server_error', 'timeout')";
		} else {
			$where = "object_type IN (" . implode(',', $objects_quoted) . ") AND status = 'error' AND error_code NOT IN ('server_error', 'timeout')";
		}
		$blob_ids = App::getDb()->fetchAllCol("SELECT blob_id FROM email_sources WHERE $where AND blob_id IS NOT NULL");

		$this->db->executeUpdate("DELETE FROM email_sources WHERE $where");

		$blobs = App::getOrm()->getRepository('DeskPRO:Blob')->getByIds($blob_ids);
		foreach ($blobs as $blob) {
			try {
				App::getContainer()->getBlobStorage()->deleteBlobRecord($blob);
			} catch (\Exception $e) {
				KernelErrorHandler::logException($e, false);
			}
		}

		if ($type == 'errors') {
			return $this->redirectRoute('admin_emailgateway_errors', array('object_type' => $object_type));
		} else {
			return $this->redirectRoute('admin_emailgateway_rejections', array('object_type' => $object_type));
		}
	}

	####################################################################################################################
	# delete
	####################################################################################################################

	public function deleteAction($id, $security_token)
	{
		$this->ensureAuthToken('delete_gateway_error', $security_token);

		$source = $this->em->find('DeskPRO:EmailSource', $id);

		if (!$source) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		if ($source->blob) {
			try {
				App::getContainer()->getBlobStorage()->deleteBlobRecord($source->blob);
			} catch (\Exception $e) {
				KernelErrorHandler::logException($e, false);
			}
		}

		$this->em->remove($source);
		$this->em->flush();

		if ($source->error_code == 'server_error' || $source->error_code == 'timeout') {
			return $this->redirectRoute('admin_emailgateway_errors');
		} elseif ($source->status != 'error') {
			return $this->redirectRoute('admin_emailgateway_all');
		} else {
			return $this->redirectRoute('admin_emailgateway_rejections');
		}
	}

	####################################################################################################################
	# reprocess
	####################################################################################################################

	public function reprocessAction($id, $security_token)
	{
		$this->ensureAuthToken('reprocess_gateway_error', $security_token);

		$source = $this->em->find('DeskPRO:EmailSource', $id);

		if (!$source) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$type = ($source->error_code == 'server_error' || $source->error_code == 'timeout') ? 'errors' : 'rejections';
		if ($source->status != 'error') {
			$type = 'all';
		}

		$source['status'] = 'inserted';
		$source['error_code'] = null;

		$runner = new \Application\DeskPRO\EmailGateway\Runner();
		$runner->executeSource($source);

		return $this->render('AdminBundle:EmailGatewayErrors:reprocess-result.html.twig', array(
			'source' => $source,
			'type'   => $type,
		));
	}

	protected function getObjectsForType($object_type)
	{
		if ($object_type == 'article') {
			return array('article');
		} else {
			return array('ticket', 'ticketmessage');
		}
	}
}