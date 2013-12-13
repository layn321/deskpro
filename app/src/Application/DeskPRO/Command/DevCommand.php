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

use Orb\Util\Arrays;
use Orb\Util\Strings;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

use Application\DeskPRO\App;

class DevCommand extends \Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('dpdev:dev');
		$this->addOption('reset-routing', null, InputOption::VALUE_NONE, "Deletes cached routing so it will be regenerated next load");
		$this->addOption('reset-templates', null, InputOption::VALUE_NONE, "Deletes compiled template files");
		$this->addOption('reset-cache', null, InputOption::VALUE_NONE, "Deletes the `cache` table");
		$this->addOption('reset-symfony', null, InputOption::VALUE_NONE, "Deletes the symfony and doctrine cache files");
		$this->addOption('find-unused-templates', null, InputOption::VALUE_NONE, "Tries to find unused templates");
		$this->addOption('cause-php-exception', null, InputOption::VALUE_NONE, "Throws a new PHP exception (test for error reporting)");
		$this->addOption('cause-php-error', null, InputOption::VALUE_NONE, "Causes a PHP error (test for error reporting)");
		$this->addOption('always', null, InputOption::VALUE_REQUIRED, "With cause php error/exception, a number from 0-100 for line");
		$this->addOption('load-newreply-notifs', null, InputOption::VALUE_REQUIRED, "Send loads of new reply notifications to the browser");
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		if (!dp_get_config('debug.dev')) {
			$output->write("Dev mode is not enabled");
			return 0;
		}
		if ($input->getOption('reset-routing')) {
			$output->writeln("Deleting cached routing ...");

			$finder = new \Symfony\Component\Finder\Finder();

			$dirs = array();
			if (is_dir(DP_ROOT.'/sys/cache/dev')) $dirs[] = DP_ROOT.'/sys/cache/dev';
			if (is_dir(DP_ROOT.'/sys/cache/prod')) $dirs[] = DP_ROOT.'/sys/cache/prod';

			$finder->name('/Url(Generator|Matcher)/')->in($dirs);
			$fs = new \Symfony\Component\Filesystem\Filesystem();
			foreach ($finder as $file) {
				$output->writeln(sprintf("<info>Removing %s</info>", $file->getFileName()));
				$fs->remove($file->getRealPath());
			}

			$output->writeln("Done.");

			return 0;
		} else if ($input->getOption('reset-templates')) {
			$fs = new \Symfony\Component\Filesystem\Filesystem();
			$fs->remove(DP_ROOT.'/sys/cache/twig-compiled');
			$output->writeln("Done");
			return 0;
		} else if ($input->getOption('reset-templates')) {
			App::getDb()->exec("TRUNCATE TABLE cache");
			$output->writeln("Done");
			return 0;
		} else if ($input->getOption('reset-cache')) {

			App::getDb()->exec("TRUNCATE TABLE `cache`");
			$output->writeln("Done");
			return 0;

		} else if ($input->getOption('reset-symfony')) {
			$oldcwd = getcwd();
			chdir(DP_ROOT . '/sys/cache');
			exec('rm -rf ./dev');
			exec('rm -rf ./prod');
			exec('rm -rf ./doctrine-proxies');
			chdir($oldcwd);
			$output->writeln("Done.");
			return 0;
		} else if ($input->getOption('cause-php-exception')) {
			return $this->executeCausePhpException($input, $output);
		} else if ($input->getOption('cause-php-error')) {
			return $this->executeCausePhpError($input, $output);
		} else if ($input->getOption('find-unused-templates')) {
			return $this->executeFindUnusedTemplates($input, $output);
		} else if ($input->getOption('load-newreply-notifs')) {
			return $this->loadNewreplyNotifs($input, $output, $input->getOption('load-newreply-notifs'));
		}

		$output->writeln("Unknown command, try --help");
		return 1;
	}

	protected function executeFindUnusedTemplates(InputInterface $input, OutputInterface $output)
	{
		$paths = array(
			//'AdminBundle'      => DP_ROOT.'/src/Application/AdminBundle/Resources/views',
			//'AgentBundle'      => DP_ROOT.'/src/Application/AgentBundle/Resources/views',
			//'DeskPRO'          => DP_ROOT.'/src/Application/DeskPRO/Resources/views',
			//'ReportBundle'     => DP_ROOT.'/src/Application/ReportBundle/Resources/views',
			'UserBundle'       => DP_ROOT.'/src/Application/UserBundle/Resources/views',
			//'BillingBundle'    => DP_ROOT.'/src/Application/BillingBundle/Resources/views',
		);

		$in_files = Finder::create()->in(array(
			DP_ROOT.'/src/Application',
			DP_ROOT.'/src/Cloud',
			DP_ROOT.'/sys/cache/twig-compiled'
		))->files();

		foreach ($paths as $bundle => $dir) {
			$finder = new \Symfony\Component\Finder\Finder();
			$finder->files()->name('*.twig')->in($dir);

			foreach ($finder as $file) {
				set_time_limit(200);
				/** @var \Symfony\Component\Finder\SplFileinfo $file */

				$filepath = $file->getRealPath();

				$tplname = str_replace($dir . '/', ':', $filepath);
				$tplname = str_replace('/', ':', $tplname);
				if (substr_count($tplname, ':') < 2) {
					$tplname = ':' . $tplname; // for layouts that are in top dir, MyBundle::layout
				}
				$tplname = $bundle . $tplname;

				$tplname_re = preg_quote($tplname, '/');

				$found = 0;
				foreach ($in_files as $f) {
					$out = null;
					$cmd = "awk '/" . $tplname_re . "/{ print \"FOUND\"; exit 1; }' " . escapeshellarg($f->getRealPath()) . '';
					exec($cmd, $out);

					$out = implode('', $out);
					if (strpos($out, 'FOUND') !== false) {
						$found++;
						if ($found >= 2) {
							// Needs to be two, because the twig file itself will
							// have the name of the template in it
							break;
						}
					}
				}

				if ($found < 2) {
					echo "Template appears to be unused: " . $tplname;
					echo "\n";
				}
			}
		}

		return 0;
	}

	public function loadNewreplyNotifs(InputInterface $input, OutputInterface $output, $count)
	{
		$agents = App::getContainer()->getAgentData()->getAgents();

		$online_accounts = App::getDb()->fetchAllCol("
			SELECT person_id
			FROM sessions
			WHERE interface = 'agent' AND person_id IS NOT NULL AND date_last > ?
		", array(date('Y-m-d H:i:s', strtotime('-10 minutes'))));

		if (!$online_accounts) {
			echo "No agents online\n";
			return 0;
		}

		for ($i = 0; $i < $count; $i++) {
			$ticket_id = App::getDb()->fetchColumn("
				SELECT id
				FROM tickets
				WHERE status IN ('awaiting_user', 'awaiting_agent', 'resolved')
				ORDER BY RAND()
				LIMIT 1
			");

			if (!$ticket_id) {
				echo "No tickets to use\n";
				break;
			}

			$ticket = App::getOrm()->find('DeskPRO:Ticket', $ticket_id);

			if (mt_rand(0,10) < 5) {
				$person = $ticket->person;
			} else {
				$person = $agents[array_rand($agents)];
			}

			echo "Sending notifs for ticket {$ticket->id} ...\n";

			$notif = array(
				'@fetch_types' => array(
					'ticket' => 'DeskPRO:Ticket',
					'performer' => 'DeskPRO:Person',
					'log_items' => 'DeskPRO:TicketLog',
				),
				'browser_rendered' => '
					<li
						class="inside ticket new-reply ticket-row-'.$ticket->id.' ticket-'.$ticket->id.'"
						data-class-id="ticket-row-'.$ticket->id.'"
						data-type="tickets"
						data-route="ticket:/agent/tickets/'.$ticket->id.'"
						data-route-notabreload="1"
						>
						<div class="dismiss"><i class="icon-ban-circle"></i></div>
						<time datetime="'.date('r').'"></time>
						<big>
							<span class="row-id">#'.$ticket->id.'</span>
							' . $ticket->subject . '
						</big>q
						<small>
							New reply by ' . $person->getDisplayContact() . '
						</small>
					</li>
				',
				'ticket' => $ticket->id,
				'performer' => $person->id,
				'is_new_ticket' => false,
				'is_new_agent_reply' => false,
				'is_new_agent_note' => (bool)$person->is_agent,
				'is_new_user_reply' => (bool)(!$person->is_agent),
				'log_items' => array(),
				'@target_maps' => array (
					'browser' => array('browser_rendered'),
				),
			);

			foreach ($online_accounts as $aid) {
				$notif_x = $notif;
				App::getDb()->insert('agent_alerts', array(
					'person_id'    => $aid,
					'typename'     => 'ticket',
					'data'         => serialize($notif_x),
					'date_created' => date('Y-m-d H:i:s'),
					'is_dismissed' => 0
				));
				$alert_id = App::getDb()->lastInsertId();

				$alert = array(
					'type' => 'tickets',
					'alert_id' => $alert_id,
					'row' => $notif['browser_rendered']
				);

				App::getDb()->insert('client_messages', array(
					'for_person_id' => $aid,
					'channel'       => 'agent-notify.tickets',
					'auth'          => Strings::random(15, Strings::CHARS_ALPHANUM_I),
					'handler_class' => 'Application\\DeskPRO\\ClientMessage\\MessageHandler\\BasicArray',
					'data'          => serialize($alert),
					'date_created'  => date('Y-m-d H:i:s'),
				));
			}

		}

		return 0;
	}

	protected function executeCausePhpException(InputInterface $input, OutputInterface $output)
	{
		$rand = uniqid('e_', true);
		$rand_code_code = mt_rand(1,1000);

		// Nasty way of faking line number
		$line = mt_rand(0,100);

		if ($input->getOption('always')) {
			$line = $input->getOption('always');
		}

		switch ($line) {
			case 0: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 1: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 2: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 3: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 4: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 5: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 6: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 7: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 8: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 9: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 10: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 11: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 12: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 13: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 14: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 15: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 16: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 17: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 18: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 19: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 20: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 21: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 22: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 23: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 24: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 25: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 26: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 27: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 28: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 29: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 30: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 31: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 32: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 33: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 34: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 35: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 36: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 37: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 38: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 39: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 40: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 41: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 42: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 43: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 44: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 45: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 46: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 47: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 48: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 49: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 50: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 51: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 52: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 53: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 54: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 55: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 56: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 57: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 58: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 59: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 60: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 61: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 62: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 63: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 64: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 65: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 66: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 67: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 68: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 69: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 70: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 71: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 72: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 73: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 74: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 75: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 76: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 77: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 78: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 79: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 80: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 81: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 82: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 83: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 84: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 85: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 86: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 87: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 88: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 89: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 90: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 91: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 92: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 93: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 94: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 95: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 96: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 97: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 98: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 99: throw new \Exception("Exception $rand", $rand_code_code); break;
			case 100: throw new \Exception("Exception $rand", $rand_code_code); break;
		}

		return 1;
	}

	protected function executeCausePhpError(InputInterface $input, OutputInterface $output)
	{
		$line = mt_rand(0,100);

		if ($input->getOption('always')) {
			$line = $input->getOption('always');
		}

		switch ($line) {
			case 0: echo $not_existing; break;
			case 1: echo $not_existing; break;
			case 2: $x = array(); $x .= 'abc'; break;
			case 3: implode(); break;
			case 4: not_exist_function(); break;
			case 5: file_get_contents('not_existing_filename'); break;
			case 6: $obj = new \stdClass(); $obj->not_existing; break;
			case 7: $x = array(); echo $x['not_existing']; break;
			case 8: \NotExistingClass::notExistingMethod(); break;
			case 9: echo $not_existing; break;
			case 10: file_get_contents('not_existing_filename'); break;
			case 11: echo $not_existing; break;
			case 12: $obj = new \stdClass(); $obj->not_existing; break;
			case 13: echo $not_existing; break;
			case 14: $x = array(); echo $x['not_existing']; break;
			case 15: file_get_contents('not_existing_filename'); break;
			case 16: \NotExistingClass::notExistingMethod(); break;
			case 17: echo $not_existing; break;
			case 18: echo $not_existing; break;
			case 19: echo $not_existing; break;
			case 20: file_get_contents('not_existing_filename'); break;
			case 21: $x = array(); echo $x['not_existing']; break;
			case 22: $x = array(); $x .= 'abc'; break;
			case 23: echo $not_existing; break;
			case 24: \NotExistingClass::notExistingMethod(); break;
			case 25: file_get_contents('not_existing_filename'); break;
			case 26: $x = array(); $x .= 'abc'; break;
			case 27: echo $not_existing; break;
			case 28: $x = array(); echo $x['not_existing']; break;
			case 29: echo $not_existing; break;
			case 30: $obj = new \stdClass(); $obj->not_existing; break;
			case 31: echo $not_existing; break;
			case 32: \NotExistingClass::notExistingMethod(); break;
			case 33: implode(); break;
			case 34: $x = array(); $x .= 'abc'; break;
			case 35: $x = array(); echo $x['not_existing']; break;
			case 36: echo $not_existing; break;
			case 37: echo $not_existing; break;
			case 38: $x = array(); $x .= 'abc'; break;
			case 39: implode(); break;
			case 40: \NotExistingClass::notExistingMethod(); break;
			case 41: echo $not_existing; break;
			case 42: $x = array(); echo $x['not_existing']; break;
			case 43: echo $not_existing; break;
			case 44: not_exist_function(); break;
			case 45: echo $not_existing; break;
			case 46: $x = array(); $x .= 'abc'; break;
			case 47: echo $not_existing; break;
			case 48: \NotExistingClass::notExistingMethod(); break;
			case 49: $x = array(); echo $x['not_existing']; break;
			case 50: file_get_contents('not_existing_filename'); break;
			case 51: implode(); break;
			case 52: not_exist_function(); break;
			case 53: echo $not_existing; break;
			case 54: echo $not_existing; break;
			case 55: file_get_contents('not_existing_filename'); break;
			case 56: \NotExistingClass::notExistingMethod(); break;
			case 57: implode(); break;
			case 58: $x = array(); $x .= 'abc'; break;
			case 59: echo $not_existing; break;
			case 60: $obj = new \stdClass(); $obj->not_existing; break;
			case 61: echo $not_existing; break;
			case 62: $x = array(); $x .= 'abc'; break;
			case 63: echo $not_existing; break;
			case 64: \NotExistingClass::notExistingMethod(); break;
			case 65: file_get_contents('not_existing_filename'); break;
			case 66: $obj = new \stdClass(); $obj->not_existing; break;
			case 67: echo $not_existing; break;
			case 68: not_exist_function(); break;
			case 69: implode(); break;
			case 70: $x = array(); echo $x['not_existing']; break;
			case 71: echo $not_existing; break;
			case 72: echo $not_existing; break;
			case 73: echo $not_existing; break;
			case 74: $x = array(); $x .= 'abc'; break;
			case 75: file_get_contents('not_existing_filename'); break;
			case 76: not_exist_function(); break;
			case 77: $x = array(); echo $x['not_existing']; break;
			case 78: $obj = new \stdClass(); $obj->not_existing; break;
			case 79: echo $not_existing; break;
			case 80: \NotExistingClass::notExistingMethod(); break;
			case 81: echo $not_existing; break;
			case 82: $x = array(); $x .= 'abc'; break;
			case 83: echo $not_existing; break;
			case 84: $x = array(); echo $x['not_existing']; break;
			case 85: file_get_contents('not_existing_filename'); break;
			case 86: $x = array(); $x .= 'abc'; break;
			case 87: implode(); break;
			case 88: \NotExistingClass::notExistingMethod(); break;
			case 89: echo $not_existing; break;
			case 90: echo $not_existing; break;
			case 91: $x = array(); echo $x['not_existing']; break;
			case 92: not_exist_function(); break;
			case 93: implode(); break;
			case 94: $x = array(); $x .= 'abc'; break;
			case 95: file_get_contents('not_existing_filename'); break;
			case 96: \NotExistingClass::notExistingMethod(); break;
			case 97: echo $not_existing; break;
			case 98: $x = array(); echo $x['not_existing']; break;
			case 99: echo $not_existing; break;
			case 100: file_get_contents('not_existing_filename'); break;
		}
		return 1;
	}

}