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
use Application\DeskPRO\Entity;

use Orb\Util\Strings;

class LanguageToPOCommand extends \Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('dp:languages:export:po');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
		if (!dp_get_config('debug.dev')) {
			$output->write("Dev mode is not enabled.");
			return 0;
		}

        $packages = array('user', 'agent', 'admin');

        foreach($packages as $package) {
            $files = array();
            $folder = DP_ROOT.'/languages/default/'.$package;

            $dh = opendir($folder);

            while(($file = readdir($dh)) !== false) {
                $file = $folder.'/'.$file;

                if(is_file($file)) {
                    $files[] = $file;
                }
            }

            closedir($dh);

            $strings = array();

            foreach($files as $file) {
                $strings = array_merge(require($file), $strings);
            }

            $file = DP_ROOT.'/languages/default/'.ucfirst($package).'.po';
            echo "Exporting: {$file}\n";
            $fs = fopen($file, 'w');

            foreach($strings as $source => $target) {
                fwrite($fs, "\nmsgid \"{$source}\"\n");
                fwrite($fs, "msgstr ");
                $parts = explode("\n", $target);

                foreach($parts as $i=>$part) {
                    fwrite($fs, '"'.$part);

                    if($i != count($parts) -1) {
                        fwrite($fs, '\n');
                    }

                    fwrite($fs, "\"\n");
                }
            }

            fclose($fs);
        }

    }
}
