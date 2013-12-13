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
use Application\DeskPRO\Entity;
use Application\DeskPRO\Log\Logger;

use Orb\Util\Util;
use Orb\Util\Numbers;

class LoginTokenCommand extends \Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('dp:login-token');
		$this->setHelp("Generates a temporary (valid for 5 mins) login token that can be used to login as any user");
		$this->addArgument('email', InputArgument::REQUIRED, 'The email address of the user');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$email = $input->getArgument('email');

		$person = $this->getContainer()->getEm()->getRepository('DeskPRO:Person')->findOneByEmail($email);

		if (!$person) {
			$output->writeln(sprintf('<error>Could not find any user with email address: %s</error>', $email));
			return 1;
		}

		$secret = sha1($person->secret_string . $person->salt);
		$token = Util::generateStaticSecurityToken($secret, 300);

		$output->writeln("Log in with:");
		$output->writeln("<info>Email: $email</info>");
		$output->writeln("<info>Password: $token</info>");

		if ($person->is_agent) {
			if ($person->can_admin) {
				$url = App::getRouter()->generateUrl('user') . 'admin/login?tok=' . $person->getId() . '-' . $token;
				$output->writeln("<info>Admin Quick Login: $url</info>");
			}

			$url = App::getRouter()->generateUrl('user') . 'agent/login?tok=' . $person->getId() . '-' . $token;
			$output->writeln("<info>Agent Quick Login: $url</info>");
		}

		$url = App::getRouter()->generateUrl('user') . 'login?tok=' . $person->getId() . '-' . $token;
		$output->writeln("<info>Agent Quick Login: $url</info>");

		$output->writeln("Note: This token will only work for the next 5 minutes.");
	}
}
