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

namespace Application\DeskPRO\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

use Application\DeskPRO\App;
use DeskPRO\Kernel\License;

class LicenseInfoCommand extends \Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('dp:license-info');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$lic = License::getLicense();

		if ($lic->isLicenseCodeError()) {
			echo "License Error: " . $lic->getLicenseCodeError();
			return 1;
		}

		echo "License ID: " . $lic->getLicenseId();
		echo "\n";

		echo "Expires: ";
		if ($lic->getExpireDate()) {
			echo $lic->getExpireDate()->format('Y-m-d H:i:s');

			$diff = $lic->getExpireDate()->getTimestamp() - time();
			if ($diff < 1) {
				echo " (EXPIRED)";
			} else {
				echo " (" . \Orb\Util\Dates::secsToReadable($diff, 3) . ")";
			}
		} else {
			echo "Never";
		}
		echo "\n";

		echo "Agents: ";
		if ($lic->getMaxAgents()) {
			echo $lic->getMaxAgents();
		} else {
			echo "Unlimited";
		}
		echo "\n";

		return 0;
	}
}
