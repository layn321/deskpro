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

namespace Application\DeskPRO\Debug;

use Application\DeskPRO\Debug\Data\DataInterface;

class DataReportGenerator
{
	/**
	 * @var \Application\DeskPRO\Debug\Data\DataInterface[]
	 */
	public $datas = array();

	/**
	 * @var bool
	 */
	public $enable_gzip = true;

	public function addData(DataInterface $data)
	{
		$this->datas[] = $data;
	}

	/**
	 * @return string
	 */
	public function generateReport()
	{
		$data = array();

		foreach ($this->datas as $d) {
			$name = get_class($d);
			$data[$name] = $d->getData();
		}

		$type = 'json';
		if (defined('JSON_PRETTY_PRINT')) {
			$data = json_encode($data, constant('JSON_PRETTY_PRINT'));
		} else {
			$data = json_encode($data);
		}

		$encode = 'plain';
		if ($this->enable_gzip && function_exists('gzencode')) {
			$encode = 'gzip';
			$data = gzencode($data);
		}

		return array(
			'data'        => $data,
			'data_encode' => $type,
			'file_encode' => $encode
		);
	}
}