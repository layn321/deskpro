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
 * Orb
 *
 * @package Orb
 * @category Auth
 */

namespace Orb\Assetic\Filter;

use Assetic\Filter\FilterInterface;
use Assetic\Asset\AssetInterface;
use Assetic\Util\ProcessBuilder;

class SmartSprites implements FilterInterface
{
	protected $smartsprites_bin;

	/**
	 * @var \Orb\Util\OptionsArray
	 */
	public $options;

	public function __construct($smartsprites_bin, array $options = array())
	{
		$this->smartsprites_bin = $smartsprites_bin;
		$this->options = new \Orb\Util\OptionsArray($options);
	}

	public function setOptions(array $options)
	{
		$this->options->setAll($options);
	}

	public function filterDump(AssetInterface $asset)
    {

    }

	public function filterLoad(AssetInterface $asset)
	{
		if ($this->options->get('ignore')) {
			return;
		}
		$pb = new ProcessBuilder();
		$pb->add($this->smartsprites_bin);

		$pb->setWorkingDirectory(dirname($this->smartsprites_bin));

		$prefix = preg_replace('#[^0-9a-zA-Z\-_]#', '-', $asset->getSourcePath());
		$tmpfile = $asset->getSourceRoot() . '/' . $prefix . '-' . substr(sha1(time().rand(11111, 99999)), 0, 7) . '.css';
		$expect_outfile = str_replace('.css', '-sprite.css', $tmpfile);

		if (file_put_contents($tmpfile, $asset->getContent()) === false) {
			@unlink($tmpfile);
			throw new \RuntimeException('Error creating tmp CSS file in source directory. SmartSprites requires the file to be in the proper location, so we tried to make a temp file there but failed: '.$tmpfile);
		}

		$pb->add('--css-files')->add($tmpfile);

		$proc = $pb->getProcess();
		$code = $proc->run();

		@unlink($tmpfile);

		if (0 < $code) {
			if (file_exists($expect_outfile)) {
				unlink($expect_outfile);
			}

			throw new \RuntimeException("[SmartSprites] " . $proc->getCommandLine() . "\n\n" . $proc->getOutput() . "\n\n" . $proc->getErrorOutput());
		}

		// No file means SmartSprites just didnt need to do anything,
		// so we need to check for it

		if (file_exists($expect_outfile)) {
        	$asset->setContent(file_get_contents($expect_outfile));
			unlink($expect_outfile);
		}
	}
}
