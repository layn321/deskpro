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

use Application\DeskPRO\Languages\LangPackInfo;
use Orb\Util\Arrays;
use Orb\Util\Strings;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Guzzle\Http\Client as HttpClient;

class DevLangOneSkyInitCommand extends \Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand
{
	protected $api_key;
	protected $secret_key;

	protected function configure()
	{
		$this->setName('dpdev:lang:onesky-init');
		$this->addOption('api-key', null, InputOption::VALUE_REQUIRED);
		$this->addOption('secret-key', null, InputOption::VALUE_REQUIRED);
		$this->addOption('user-platform-id', null, InputOption::VALUE_REQUIRED);
		$this->addOption('skip-base-lang', null, InputOption::VALUE_NONE);
		$this->addOption('skip-other-langs', null, InputOption::VALUE_NONE);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$this->api_key = $input->getOption('api-key');
		$this->secret_key = $input->getOption('secret-key');
		$user_platform_id = $input->getOption('user-platform-id');

		$lang_packs = new LangPackInfo();

		#------------------------------
		# Upload base english
		#------------------------------

		if (!$input->getOption('skip-base-lang')) {
			$output->writeln("<info>Uploading base English</info>");

			foreach ($lang_packs->getDefaultCategories('user') as $file) {
				$file .= '.php';
				$filepath = DP_ROOT.'/languages/default/user/' . $file;

				$phrases = include($filepath);

				$phrases_send = array();
				foreach ($phrases as $k => $v) {
					$phrases_send[] = array(
						'string' => $v,
						'string-key' => $k
					);
				}

				echo "$file ...";
				$this->_restPost('string/input', array(
					'platform-id' => $user_platform_id,
					'tag' => $file,
					'is-allow-update' => true,
					'input' => $phrases_send
				));
				echo " Done\n";
			}
		}

		#------------------------------
		# Upload other langs
		#------------------------------

		if (!$input->getOption('skip-other-langs')) {
			$output->writeln("<info>Uploading other languages</info>");

			foreach ($lang_packs->getLangIds() as $lid) {
				if ($lid == 'default') continue;

				$tmp_dir = sys_get_temp_dir() . "/dp-$lid-" . Strings::random(5);
				mkdir($tmp_dir, 0777, true);

				echo "\nProcessing $lid\n";

				$locale = $lang_packs->getLangInfo($lid, 'locale');

				foreach ($lang_packs->getDefaultCategories('user') as $file) {
					$file .= '.php';
					$filepath = DP_ROOT.'/languages/'.$lid.'/user/' . $file;

					if (!is_file($filepath)) {
						continue;
					}

					$phrases = include($filepath);

					$requests = array();

					foreach ($phrases as $k => $v) {
						echo "[$lid] $k ...";
						$res = $this->_restPost('string/translate', array(
							'platform-id' => $user_platform_id,
							'string-key' => $k,
							'translation' => $v,
							'locale' => $locale
						), true);

						$requests[] = $res;
						echo " Done\n";

						if (count($requests) == 100) {
							echo "Sending ...";
							$r = $this->_getHttpClient()->send($requests);
							$requests = array();
							echo "Done\n";
						}
					}

					if (count($requests)) {
						echo "Sending ...";
						$r = $this->_getHttpClient()->send($requests);
						$requests = array();
						echo "Done\n";
					}
				}
			}
		}

		echo "\n";
	}

	/**
	 * @param string $path
	 * @return array
	 * @throws \RuntimeException
	 */
	private function _restGet($path, array $vars = array())
	{
		$vars['api-key']   = $this->api_key;
		$vars['timestamp'] = time();
		$vars['dev-hash']  = md5($vars['timestamp'] . $this->secret_key);

		$request = $this->_getHttpClient()->get($path);
		$request->getQuery()->merge($vars);

		$response = $request->send();

		$body = $response->getBody(true);
		$data = json_decode($body, true);

		return $data;
	}

	/**
	 * @param string $path
	 * @return array
	 * @throws \RuntimeException
	 */
	private function _restPost($path, array $post_vars = array(), $return = false)
	{
		$vars = array();
		$vars['api-key']   = $this->api_key;
		$vars['timestamp'] = time();
		$vars['dev-hash']  = md5($vars['timestamp'] . $this->secret_key);

		$request = $this->_getHttpClient()->post($path);
		$request->getQuery()->merge($vars);

		if ($post_vars) {

			if ($path == 'string/input') {
				$post_vars['input'] = json_encode($post_vars['input']);
			}

			if ($path == 'string/delete') {
				$post_vars['to-delete'] = json_encode($post_vars['to-delete']);
			}

			if (isset($post_vars['@FILES'])) {
				foreach ($post_vars['@FILES'] as $f) {
					$request->addPostFile($f['field'], $f['filename'], $f['filetype']);
				}
				unset($post_vars['@FILE']);
			}

			$request->addPostFields($post_vars);
		}

		if ($return) {
			return $request;
		}

		$response = $request->send();

		$body = $response->getBody(true);
		$data = json_decode($body, true);

		return $data;
	}

	/**
	 * @return \Guzzle\Http\Client
	 */
	private function _getHttpClient()
	{
		$http_client = new HttpClient('http://api.oneskyapp.com/2', array(
			'ssl.certificate_authority' => false
		));
		return $http_client;
	}
}