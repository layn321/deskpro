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

namespace Application\DevBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

use Application\DeskPRO\App;

use Orb\Util\Arrays;
use Orb\Util\Strings;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Routing\Route;

class PhraseCheckCommand extends \Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand
{
	protected function configure()
	{
		$this->setDefinition(array(
		))->setName('dpdev:phrase-check');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{

        /*
         * Words defined in language files but only used from within PHP scripts (and not templates)
         * will currently generate a false positive. Either need to scan .php files as well or create
         * a list of phrases manually to ingore.
         */

        // get list of folders that contain templates
        $bundles = DP_ROOT . '\src\Application';
        $folders = array();
        if ($handle = opendir($bundles)) {
            while (false !== ($filename = readdir($handle))) {
                if ($filename != "." && $filename != "..") {

                    $folder = $bundles . '/' . $filename . '/Resources/views';
                    $folders[] = $folder;

                    if ($handle2 = opendir($folder)) {
                        while (false !== ($filename2 = readdir($handle2))) {
                            if ($filename2 != "." && $filename2 != ".." && is_dir($folder . '/' . $filename2)) {
                                $folders[] = $folder . '/' . $filename2;
                            }
                        }
                    }
                }
            }

			closedir($handle);
        }

        $templates = array();
        $templates_content = '';

        // get list of templates and a giant variable holding all templates
        foreach ($folders AS $folder) {

            if ($handle = opendir($folder)) {

                while (false !== ($filename = readdir($handle))) {
                    if (substr($filename, -4) == 'twig') {
                        $templates[] = $folder . '/' . $filename;
                        $templates_content .= file_get_contents($folder . '/' . $filename);
                    }
                }
            }
        }

        // we now have all the phrases defined in templates that should exist
        $matches = preg_match_all('/{{\s+phrase\\(\'([_a-zA-|.]*)\'/', $templates_content, $results);

        $template_phrases = array_merge($this->getPhrasesFromPHPFiles(), $results[1]);

        // now let's get all the phrases defined in language files
        $directories = array(
            array('user', 'User Interface'),
            array('agent', 'Agent Interface'),
            array('admin', 'Admin Interface')
        );

        $language_phrases = array();

        foreach ($directories AS $interface) {

            $dir = DP_ROOT . '/languages/DeskPRO/' . $interface['0'];

            if ($handle = opendir($dir)) {
                while (false !== ($filename = readdir($handle))) {
                    if ($filename != "." && $filename != "..") {

                        $words = include($dir . '/' . $filename);
                        foreach ($words AS $key => $var) {
                            $language_phrases[] = $key;
                        }
                    }
                }
            }
        }

        // so what's the difference?
        $missing_language_phrases = array();
        foreach ($template_phrases AS $phrase) {
            if (!in_array($phrase, $language_phrases)) {
                $missing_language_phrases[] = $phrase;
            }
        }

        $missing_template_phrases = array();
        foreach ($language_phrases AS $phrase) {
            if (!in_array($phrase, $template_phrases)) {
                $missing_template_phrases[] = $phrase;
            }
        }

        echo "\n\n\n\nThere are " . count($missing_template_phrases) . " phrases defined in languages but not found in templates\n\n";
        print_r($missing_template_phrases);

        echo "\n\nThere are " . count($missing_language_phrases) . " phrases defined in templates but not found in language files\n\n";
        print_r($missing_language_phrases);

    }

    private function getPhrasesFromPHPFiles()
    {
        // Recursively scan directories.
        function scan_dir($dir, &$files)
        {
            if ($handle = opendir($dir)) {
                while (false !== ($filename = readdir($handle))) {
                    if (is_dir($dir.'/'.$filename)) {
                        if ($filename == "." || $filename == "..")
                                continue;

                        scan_dir($dir.'/'.$filename, $files);
                    }
                    else {
                        if(preg_match('/\.php$/i' , $filename)) {
                            $files[] = $dir.'/'.$filename;
                        }
                    }
                }

                closedir($handle);
            }
        }

        $files = array();
        // Create a large list containing all php files.
        scan_dir(DP_ROOT . '/src/', $files);
        $phrases = array();

        foreach($files as $file) {
            // Do this later.
            /*$tokens = token_get_all(file_get_contents($file));
            $state = '';

            foreach($tokens as $token) {
                if(is_array($token) && $token[1] == 'phrase')
            }*/

            $matches = preg_match_all('/->phrase\(\'([_a-zA-|.]*)\'/', file_get_contents($file), $results);

            if($matches) {
                $phrases = array_merge($phrases, $results[1]);
            }
        }

        return $phrases;
    }
}
