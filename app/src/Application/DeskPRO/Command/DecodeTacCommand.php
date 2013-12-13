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
 * @category Commands
 */

namespace Application\DeskPRO\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

use Application\DeskPRO\App;

class DecodeTacCommand extends \Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand
{
	protected function configure()
	{
		$this->setDefinition(array(
			new InputArgument('tac', InputArgument::REQUIRED, 'The TAC or PTAC to decode. These are the codes that usually begin with TAC- or PTAC- or TICKET- in email headers.'),
		))->setName('dp:decode-tac');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$tac = $input->getArgument('tac');
		$tac = preg_replace('#^(TAC|PTAC|TICKET)\-#', '', $tac);

		$info = \Application\DeskPRO\Entity\Ticket::decodeAccessCode($tac);
		if ($info) {
			echo "ID: {$info['ticket_id']}\nAuth: {$info['auth']}\n";
		} else {
			echo "Invalid code\n";
		}
	}
}
