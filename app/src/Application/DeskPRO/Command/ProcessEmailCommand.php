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

use Application\DeskPRO\EmailGateway\Runner;
use Application\DeskPRO\Entity\EmailSource;
use Application\DeskPRO\Log\Logger;
use Orb\Util\Strings;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

use Application\DeskPRO\App;

class ProcessEmailCommand extends \Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('dp:process-email');
		$this->addOption('gateway', null, InputOption::VALUE_REQUIRED, 'ID of the gateway to process the source under. If not provided, then the first ticket gateway will be used.');
		$this->addOption('to', null, InputOption::VALUE_REQUIRED, 'The TO address to interpret the email to. If provided, the gateway will be determiend based on this.');
		$this->addOption('source', null, InputOption::VALUE_REQUIRED,  'ID of an existing source ID to re-process.');
		$this->addOption('file', null, InputOption::VALUE_OPTIONAL,  'Path to an email file to process. No filename is required if you are sending the file through standard input (e.g., piping).');
		$this->addOption('success-string', null, InputOption::VALUE_OPTIONAL,  'A special string to output in case of success (e.g., use as a trigger for external tool)');
		$this->setHelp("Example usage with dp:gen-rand-email:\n\tphp cmd.php dp:gen-rand-email --from-email=\"user@example.com\" --to-email=\"gateway@example.com\" | php cmd.php dp:process-email --file");
	}

	/**
	 * @return \Application\DeskPRO\DependencyInjection\DeskproContainer
	 */
	public function getContainer()
	{
		return parent::getContainer();
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$success_string = $input->getOption('success-string');

		#----------------------------------------
		# Get gateway account
		#----------------------------------------

		$gateway_id = $input->getOption('gateway');
		$gateway = null;

		if (!$gateway_id and $input->getOption('to')) {
			$matcher = App::getSystemService('gateway_address_matcher');
			$addr = $matcher->getMatchingAddress($input->getOption('to'));
			if ($addr) {
				$gateway = $addr->gateway;
			}
		}

		if (!$gateway) {
			if ($gateway_id) {
				$gateway = $this->getContainer()->getEm()->find('DeskPRO:EmailGateway', $input->getOption('gateway'));

				if (!$gateway) {
					$output->writeln("<error>Could not find gateway</error>");
					return 1;
				}
			} else {
				$gateway = $this->getContainer()->getEm()->createQuery("
					SELECT g
					FROM DeskPRO:EmailGateway g
					WHERE g.gateway_type = 'tickets'
					ORDER BY g.id ASC
				")->setMaxResults(1)->getOneOrNullResult();

				if (!$gateway) {
					$output->writeln("<error>No ticket gateways exist</error>");
					return 1;
				}
			}
		}

		#----------------------------------------
		# Read/save source object
		#----------------------------------------

		if ($input->getOption('source')) {
			$source = $this->getContainer()->getEm()->find('DeskPRO:EmailSource', $input->getOption('source'));

			if (!$source) {
				$output->writeln("<error>Could not find source</error>");
				return 1;
			}
		} else {

			if ($input->getOption('file')) {
				$raw_source = file_get_contents($input->getOption('file'));
			} else {
				$raw_source = '';
				while (!feof(STDIN)) {
					$raw_source .= fread(STDIN, 1024);
				}
			}

			$raw_source = trim($raw_source);
			if (!$raw_source) {
				$output->writeln("<error>No email source file provided</error>");
				return 1;
			}

			$raw_source = Strings::standardEol($raw_source);

			$header_end = strpos($raw_source, "\n\n");
			if ($header_end === false) {
				// Means an empty body (eg message with only subject)
				// But we trimmed above so the \n\n sep would be trimmed off
				$raw_source .= "\n\n";
				$header_end = strpos($raw_source, "\n\n");
			}

			$raw_headers = trim(substr($raw_source,0, $header_end));

			$source = new EmailSource();
			$source->fromArray(array(
				'gateway' => $gateway,
				'headers' => $raw_headers,
				'status' => 'inserted'
			));

			// Rough matching, just for info purposes when browsing a list
			$source->header_to      = Strings::extractRegexMatch('#^To:\s*(.*?)$#m', $raw_headers) ?: '';
			$source->header_from    = Strings::extractRegexMatch('#^From:\s*(.*?)$#m', $raw_headers) ?: '';
			$source->header_subject = Strings::extractRegexMatch('#^Subject:\s*(.*?)$#m', $raw_headers) ?: '';
			$source->object_type    = ($gateway->gateway_type == 'tickets' ? 'ticket' : $gateway->gateway_type);

			$t = microtime(true);
			$output->writeln("<info>Saving blob...</info>");

			$blob = App::getContainer()->getBlobStorage()->createBlobRecordFromString(
				$raw_source,
				'email.eml',
				'message/rfc822'
			);

			$source->blob = $blob;

			// Set the copied raw source or else $source->getRawSource() will
			// attempt to load it from the blob storage which is wasteful (eg could read back from s3 what we just wrote)
			$source->_raw = $raw_source;

			App::getOrm()->persist($source);
			App::getOrm()->flush();

			$output->writeln(sprintf("<info>Saved email source #" . $source->getId() . " (took %.5s)</info>", microtime(true) - $t));
		}

		#----------------------------------------
		# Run the gateway
		#----------------------------------------

		$logger = new Logger();
		$logger->addWriter(new \Orb\Log\Writer\ConsoleOutputWriter($output));
		$logger->addFilter(new \Orb\Log\Filter\SimpleLineFormatter());

		$runner = new Runner();
		$runner->setLogger($logger);
		$runner->setPhpTimeLimit(900);
		$runner->executeSource($source);

		if ($success_string) {
			echo "\n";
			echo $success_string;
			echo "\n";
		} else {
			echo "\n\n";
			echo "STATUS: DPC_EMAIL_SUCCESS";
			echo "\n\n";
		}

		return 0;
	}
}