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

namespace Application\AdminBundle\FormModel;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\EmailTransport;

use Orb\Util\Arrays;

class EditEmailTransport
{
	public $match_type;
	public $match_email;
	public $match_domain;
	public $match_regex;

	public $transport_type;
	public $smtp_options = array();
	public $gmail_options = array();

	/**
	 * @var \Application\DeskPRO\Entity\EmailTransport
	 */
	protected $transport;

	public function __construct(EmailTransport $transport)
	{
		$this->transport = $transport;

		$this->match_type = $transport->match_type;
		if ($this->match_type == 'exact') {
			$this->match_email = $transport->match_pattern;
		} elseif ($this->match_type == 'domain') {
			$this->match_domain = $transport->match_pattern;
		} elseif ($this->match_type == 'regex') {
			$this->match_regex = $transport->match_pattern;
		}

		if ($transport->transport_type == 'smtp') {
			$this->smtp_options = $transport->transport_options;
		} elseif ($transport->transport_type == 'gmail') {
			$this->gmail_options = $transport->transport_options;
		}

		if (!isset($this->smtp_options['port'])) $this->smtp_options['port'] = 25;
	}

	public function apply()
	{
		$this->transport->match_type = $this->match_type;

		if ($this->match_email) {
			$this->transport->match_pattern = $this->match_email;
		} elseif ($this->match_domain) {
			$this->transport->match_pattern = $this->match_domain;
		} elseif ($this->match_regex) {
			$this->transport->match_pattern = $this->match_regex;
		}

		$this->transport->transport_type = $this->transport_type;
		if ($this->transport_type == 'smtp') {
			$this->transport->title = $this->smtp_options['host'] . ':' . $this->smtp_options['username'];
			$this->transport->transport_options = $this->smtp_options;
		} elseif ($this->transport_type == 'gmail') {
			$this->transport->title = 'Gmail / Google Apps: ' . $this->gmail_options['username'];
			$this->transport->transport_options = $this->gmail_options;
		} else {
			$this->transport->title = 'PHP mail()';
			$this->transport->transport_options = array();
		}

		if ($this->match_type == 'any') {
			$this->transport->run_order = 100000000;
		}
	}


	public function save()
	{
		$this->apply();

		App::getOrm()->persist($this->transport);
		App::getOrm()->flush();
	}
}
