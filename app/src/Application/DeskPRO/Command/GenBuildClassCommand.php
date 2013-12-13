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
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

use Application\DeskPRO\App;

class GenBuildClassCommand extends \Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('dpdev:gen-build-class');
		$this->addOption('out', null, InputOption::VALUE_NONE, 'Output code instead of writing it');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$time = time();

		$diff = \Application\DeskPRO\ORM\Util\Util::getUpdateSchemaSql(App::getOrm());

		if ($diff) {
			$defaultcode = array();

			foreach ($diff as $sql) {
				$sql = str_replace("\\'", "'", addslashes($sql));
				$sql = str_replace('$', '\\$', $sql);
				$defaultcode[] = "\t\t\$this->execMutateSql(\"".$sql."\");";
			}

			$defaultcode = implode("\n", $defaultcode);

		} else {
			$defaultcode = "\t\t//\$this->execMutateSql(\"...\");";
		}

		$tpl = <<<CODE
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
 * @subpackage
 */

namespace Application\InstallBundle\Upgrade\Build;

class Build$time extends AbstractBuild
{
	public function run()
	{
		\$this->out("My Upgrade Class");
$defaultcode
	}
}
CODE;

		$path = DP_ROOT . "/src/Application/InstallBundle/Upgrade/Build/Build$time.php";

		if ($input->getOption('out')) {
			echo $tpl;
			echo "\n";
		} else {
			file_put_contents($path, $tpl);

			$build_file = DP_ROOT.'/sys/config/build-time.php';
			file_put_contents($build_file, '<?php define("DP_BUILD_TIME", '.$time.'); ');

			echo "Wrote file: $path\n";
			echo "Updated: $build_file\n";
		}

		return 0;
	}
}