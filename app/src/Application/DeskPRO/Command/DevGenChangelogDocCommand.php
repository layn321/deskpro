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

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

use Application\DeskPRO\App;

class DevGenChangelogDocCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setHelp("Generates a new changelog doc");
		$this->setName('dpdev:gen-changelog-doc');
		$this->addArgument('id', InputArgument::REQUIRED, 'The ID of the doc. The date prefix will be added automatically.');
		$this->addArgument('target', InputArgument::REQUIRED, 'Target must be "agent" or "admin"');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$target = $input->getArgument('target');

		if (!$target || ($target != 'agent' && $target != 'admin')) {
			$output->writeln('<error>You must enter a target of either agent or admin</error>');
		}

		$real_id = date('Ymd') . '-' . $input->getArgument('id');

		$path = DP_ROOT . '/docs/changelog/' . $real_id . '/log.html';
		$dir  = DP_ROOT . '/docs/changelog/' . $real_id;
		$output->writeln("Path: <info>$path</info>");

		if (file_exists($path)) {
			$output->writeln("<error>A changelog doc of this ID and date already exist.</error>");
			return 1;
		}

		if (!is_dir($dir) && !mkdir($dir)) {
			$output->writeln("<error>Failed to create doc directory</error>");
			return 1;
		}

		$tpl = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
	<link href="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/css/bootstrap-combined.no-icons.min.css" rel="stylesheet" />
	<style type="text/css">body { padding: 100px; }</style>
</head>
<body>

<h1>Article Title</h1>

Your changelog document here.

</body>
</html>
HTML;

		if (!file_put_contents($path, $tpl)) {
			$output->writeln("<error>Failed to write empty log template file</error>");
			return 1;
		}

		$docs_path = DP_ROOT.'/docs/changelog/docs.php';
		$docs = require($docs_path);
		$docs[$real_id] = array(
			'date' => date('Y-m-d H:i:s'),
			'target' => $target
		);

		file_put_contents($docs_path, '<?php return ' . var_export($docs, true) . ';');

		return 0;
	}
}
