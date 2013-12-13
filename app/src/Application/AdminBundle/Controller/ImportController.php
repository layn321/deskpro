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

/**
 * Handles setting up import processes
 */
class ImportController extends AbstractController
{
	public function indexAction()
	{
		$error = $this->in->getString('error');
		$success = $this->in->getString('success');

		$tasks = $this->em->getRepository('DeskPRO:TaskQueue')->getTasksInGroup('data_import');
		$show_task_status = count($tasks) > 0;

		return $this->render('AdminBundle:Import:csv-upload.html.twig', array(
			'error' => $error,
			'success' => $success,
			'show_task_status' => $show_task_status
		));
	}

	public function csvConfigureAction()
	{
		$this->ensureRequestToken();

		/** @var $file \Symfony\Component\HttpFoundation\File\UploadedFile */
		$file = $this->request->files->get('upload');
		if (!$file instanceof \Symfony\Component\HttpFoundation\File\UploadedFile || !$file->getSize()) {
			return $this->redirectRoute('admin_import', array('error' => 'no_file'));
		}

		if (!is_uploaded_file($file->getPath() . DIRECTORY_SEPARATOR . $file->getFilename())) {
			return $this->redirectRoute('admin_import', array('error' => 'no_move'));
		}

		$blob = App::getContainer()->getBlobStorage()->createBlobRecordFromFile(
			$file->getPath() . DIRECTORY_SEPARATOR . $file->getFilename(),
			$file->getClientOriginalName(),
			'text/csv'
		);

		$csv_path = dp_get_tmp_dir() . '/blob-' . $blob->getId() . '.csv';
		copy($file->getPath() . DIRECTORY_SEPARATOR . $file->getFilename(), $csv_path);

		$filename = $blob->getId();

		return $this->_renderCsvConfigureForm($filename, $file->getClientOriginalName());
	}

	public function csvImportAction()
	{
		$this->ensureRequestToken();

		$field_maps = $this->in->getCleanValueArray('field_maps', 'raw', 'uint');
		$filename = $this->in->getUint('filename');
		$user_filename = $this->in->getString('user_filename');
		$skip_first = $this->in->getBool('skip_first');

		$has_email = false;
		foreach ($field_maps AS $map_field) {
			if ($map_field['map'] == 'primary_email') {
				$has_email = true;
				break;
			}
		}

		if (!$has_email) {
			return $this->_renderCsvConfigureForm($filename, $user_filename);
		}

		$welcome_email = $this->in->getBool('welcome_email') && !defined('DPC_IS_CLOUD');

		$blob = App::getOrm()->find('DeskPRO:Blob', $filename);
		if (!$blob) {
			return $this->redirectRoute('admin_import', array('error' => 'no_move'));
		}

		$task_data = array(
			'blob_id' => $blob->getId(),
			'field_maps' => $field_maps,
			'skip_first' => $skip_first,
			'welcome_email' => $welcome_email,
			'user_filename' => $user_filename
		);

		if ($welcome_email) {
			$task_data['welcome_from_name'] = $this->in->getString('from_name');
			$task_data['welcome_from_email'] = $this->in->getString('from_email');
			$task_data['welcome_subject'] = $this->in->getString('subject');
			$task_data['welcome_message'] = $this->in->getString('message');
		}

		$task = $this->em->getRepository('DeskPRO:TaskQueue')->enqueueTask(
			'Application\\DeskPRO\\TaskQueueJob\\CsvImport',
			$task_data,
			'data_import'
		);

		return $this->redirectRoute('admin_import', array('success' => 'inserted'));
	}

	protected function _renderCsvConfigureForm($filename, $user_filename)
	{
		$csv_path = dp_get_tmp_dir() . '/blob-' . $filename . '.csv';

		$blob = App::getOrm()->find('DeskPRO:Blob', $filename);
		if (!$blob) {
			return $this->redirectRoute('admin_import', array('error' => 'no_move'));
		}

		if (!is_file($csv_path)) {
			App::getContainer()->getBlobStorage()->copyBlobRecordToFile($csv_path, $blob);
		}

		$fp = fopen($csv_path, 'r');
		$columns = fgetcsv($fp);
		$column_count = count($columns);

		$examples = array();
		$example_total = 0;

		for ($i = 0; $i < 100; $i++) {
			$row = fgetcsv($fp);
			if (!$row) {
				// eof or can't read properly
				break;
			}
			if (isset($row[0]) && $row[0] === null) {
				// empty row
				continue;
			}
			foreach ($row AS $id => $value) {
				if ($value !== '' && !isset($examples[$id])) {
					$examples[$id] = $value;
					$example_total++;

					if ($example_total == $column_count) {
						// have example for all columns
						break 2;
					}
				}
			}
		}

		fclose($fp);

		$custom_fields = App::getApi('custom_fields.people')->getEnabledFields();

		$show_welcome_email = !defined('DPC_IS_CLOUD');

		return $this->render('AdminBundle:Import:csv-configure.html.twig', array(
			'filename' => $filename,
			'user_filename' => $user_filename,
			'columns' => $columns,
			'examples' => $examples,
			'custom_fields' => $custom_fields,
			'show_welcome_email' => $show_welcome_email
		));
	}
}
