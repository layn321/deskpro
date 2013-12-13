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

namespace Application\DevBundle\Language;

class FileFinder
{
    private $files_temp;

    public function flatFileListToNonFlat($flat_files)
    {
        $files = array();

        foreach($flat_files as $file) {
            $files[] = array('filename' => $file);
        }

        return $files;
    }

    public function readLangDir($path)
    {
        $dh = opendir($path);

        while(false !== ($filename = readdir($dh))) {
            $filepath = $path.'/'.$filename;

            if(is_file($filepath) && substr($filepath, -3 == 'php')) {
                $this->files_temp[] = $filepath;
            }
        }

        closedir($dh);
    }

    public function getLanguageFileList($package = '')
    {
        $rootdir = DP_ROOT.'/languages/default/'.$package;
        $this->files_temp = array();

        if(empty($package)) {
            $dh1 = opendir($rootdir);

            while(false !== ($dirname = readdir($dh1))) {
                if($dirname == '.' || $dirname == '..')
                    continue;

                $path = $rootdir.'/'.$dirname;

                if(is_dir($path)) {
                    $this->readLangDir($path);
                }
            }

            closedir($dh1);
        }
        else {
            $this->readLangDir($rootdir);
        }

        return $this->files_temp;
    }

    public function getPhpFileList($bundle = null)
    {
        if($bundle)
            $bundles = array($bundle);
        else
            $bundles = Language::$BUNDLES;

        $this->files_temp = array();

        foreach($bundles as $bundle)
            $this->scanDir(DP_ROOT . '/src/Application/'.$bundle, '.php');

        return $this->files_temp;
    }

    public function getTwigFileList($bundle = null)
    {
        if($bundle)
            $bundles = array($bundle);
        else
            $bundles = Language::$BUNDLES;

        $this->files_temp = array();

        foreach($bundles as $bundle)
            $this->scanDir(DP_ROOT . '/src/Application/'.$bundle, '.html.twig');

        return $this->files_temp;
    }

    function scanDir($dir, $suffix)
    {
        if ($handle = opendir($dir)) {
            while (false !== ($filename = readdir($handle))) {
                if (is_dir($dir.'/'.$filename)) {
                    if ($filename == "." || $filename == "..")
                        continue;

                    $this->scanDir($dir.'/'.$filename, $suffix);
                }
                else {
                    if(preg_match('/'.preg_quote($suffix, '/').'$/i' , $filename)) {
                        $this->files_temp[] = $dir.'/'.$filename;
                    }
                }
            }

            closedir($handle);
        }
    }
}