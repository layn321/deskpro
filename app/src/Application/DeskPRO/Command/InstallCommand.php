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

class InstallCommand extends \Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('dp:install');
		$this->addOption('insert-initial', null, InputOption::VALUE_NONE, "Inserts initial data with initial admin account");
		$this->addOption('admin-email', null, InputOption::VALUE_REQUIRED, "(With insert-initial) The initial admin email");
		$this->addOption('admin-password', null, InputOption::VALUE_REQUIRED, "(With insert-initial) The initial admin password");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->ensureNotInstalled()) {
            exit;
        }

        $this->createDatabase();
        $this->getLogger()->log('Install::createTables', 'debug');

        $db = $this->getDb();
        $check = $db->fetchColumn("SHOW TABLES LIKE 'install_data'");

        if ($check != 'install_data') {
            try {
                $db->exec("
					CREATE TABLE `install_data` (
					  `build` varchar(30) NOT NULL,
					  `name` varchar(75) NOT NULL DEFAULT '',
					  `data` blob NOT NULL,
					  PRIMARY KEY (`build`,`name`)
					) ENGINE=InnoDB DEFAULT CHARSET=latin1
				");
            } catch (\Exception $e) {
                $this->getLogger()->log('Failed to craete install_data: ' . $e->getCode() . ' ' . $e->getMessage(), 'err');
                return;
            }

            $tableinfo = $db->fetchColumn("SHOW CREATE TABLE `install_data`", array(), 1);
            if (stripos($tableinfo, 'innodb') === false) {
                $this->getLogger()->log('install_data is not innodb', 'err');
                return;
            }
        }

        if (!defined('DP_BUILD_TIME')) {
            $build_file = DP_ROOT.'/sys/config/build-time.php';
            if (is_file($build_file)) {
                require $build_file;
            } else {
                define('DP_BUILD_TIME', time());
            }
        }

        $logger = new \Orb\Log\Logger();
        $schema = null;
        if (file_exists(DP_ROOT.'/src/Application/InstallBundle/Data/schema.php')) {
            $schema = require DP_ROOT.'/src/Application/InstallBundle/Data/schema.php';
        } else {
            $logger->log('schema.php does not exist, will auto-generate', 'debug');
        }

        $install_schema = new \Application\InstallBundle\Install\InstallSchema($this->getDb(), $schema, DP_BUILD_TIME);

        $limit = 50;
        $install_schema->setLogger($logger);

        $install_schema->run(false);


		#------------------------------
		# Install Data
		#------------------------------

		if ($input->getOption('insert-initial')) {

			$initial_password = 'password';
			$initial_email    = 'admin@example.com';

			if ($input->getOption('admin-email')) {
				$initial_email = $input->getOption('admin-email');
			}
			if ($input->getOption('admin-password')) {
				$initial_password = $input->getOption('admin-password');
			}

			if ($initial_email == 'CONFIG') {
				if (defined('DP_TECHNICAL_EMAIL')) {
					$initial_email = DP_TECHNICAL_EMAIL;
				} else {
					$initial_email = 'admin@example.com';
				}
			}

			$agent = new \Application\DeskPRO\Entity\Person();
			$agent->first_name = 'Admin';
			$agent->last_name = 'Admin';
			$agent->setEmail($initial_email, true);
			$agent->setPassword($initial_password);
			$agent->is_user = true;
			$agent->is_confirmed = true;
			$agent->is_agent_confirmed = true;
			$agent->is_agent = true;
			$agent->can_agent = true;
			$agent->can_admin = true;
			$agent->can_billing = true;
			$agent->can_reports = true;

			$this->getOrm()->persist($agent);
			$this->getOrm()->flush();

			$this->getDb()->insert('permissions', array('person_id' => $agent->id, 'name' => 'admin.use', 'value' => 1));

			// Install data stuff
			$AGENTGROUP_ALL = null; // should be defined by the time we finish processing data.php
			$USERGROUP_EVERYONE = null; // should be defined by the time we finish processing data.php
			$AGENT = $agent; // can be used in data.php
			$WEB_INSTALL = true;
			$IMPORT_INSTALL = false;

			$install_data = new \Application\InstallBundle\Install\InstallDataReader(DP_ROOT.'/src/Application/InstallBundle/Data/data.php');
			$em = $this->getOrm();
			$translate = $this->getContainer()->get('deskpro.core.translate');

			foreach ($install_data as $php) {
				eval($php);
			}

			$this->getOrm()->flush();

			\Application\DeskPRO\DataSync\AbstractDataSync::syncAllBaseToLive();

			// For the all agent group, fetch permissions from the template
			if ($AGENTGROUP_ALL) {
				$scanner = new \Application\InstallBundle\Data\AgentGroupPermScanner();
				foreach ($scanner->getNames() as $p_name) {
					$p = new \Application\DeskPRO\Entity\Permission();
					$p->usergroup = $AGENTGROUP_ALL;
					$p->name = $p_name;
					$p->value = 1;
					$this->getOrm()->persist($p);
				}
				$this->getOrm()->flush();

				$ch = new \Application\DeskPRO\ORM\CollectionHelper($agent, 'usergroups');
				$ch->setCollection(array($AGENTGROUP_ALL));
				$this->getOrm()->persist($agent);
				$this->getOrm()->flush();
			}

			if ($USERGROUP_EVERYONE) {
				$scanner = new \Application\InstallBundle\Data\UserGroupPermScanner();
				foreach ($scanner->getNames() as $p_name) {
					$p = new \Application\DeskPRO\Entity\Permission();
					$p->usergroup = $USERGROUP_EVERYONE;
					$p->name = $p_name;
					$p->value = 1;
					$this->getOrm()->persist($p);
				}
				$this->getOrm()->flush();
			}

			$data_init = new \Application\InstallBundle\Data\DataInitializer($this->getContainer());
			$data_init->admin_user = $agent;
			$data_init->run();
			App::getDb()->replace('settings', array(
				'name' => 'core.done_data_initializer',
				'value' => 1,
			));
		}

		App::getDb()->replace('install_data', array(
			'build' => 'default',
			'name' => 'install_build',
			'data' => DP_BUILD_TIME
		));

		if (defined('BUILDING_CLOUD')) {
			App::getDb()->replace('settings', array(
				'name' => 'core.deskpro_build',
				'value' => defined('DP_BUILD_TIME') ? DP_BUILD_TIME : 0,
			));
			App::getDb()->replace('settings', array(
				'name' => 'core.deskpro_build_num',
				'value' => defined('DP_BUILD_NUM') ? DP_BUILD_NUM : 0,
			));
			App::getDb()->replace('settings', array(
				'name' => 'core.install_build',
				'value' => defined('DP_BUILD_TIME') ? DP_BUILD_TIME : time(),
			));
		}

		App::getDb()->replace('settings', array(
			'name' => 'core.install_timestamp',
			'value' => time(),
		));
		App::getDb()->replace('settings', array(
			'name' => 'core.install_key',
			'value' => Strings::random(20, Strings::CHARS_KEY),
		));
		App::getDb()->replace('settings', array(
			'name' => 'core.deskpro_version',
			'value' => date('YmdHis'),
		));
		App::getDb()->replace('settings', array(
			'name' => 'core.install_via_cmd',
			'value' => 1,
		));
    }

    private function createDatabase()
    {
        try {
            App::getDb()->connect();
        } catch (\PDOException $e) {
            if ($e->getCode() == '1049') {

                // Attempt to create an empty database
                try {
                    global $DP_CONFIG;
                    $dbh = new \PDO("mysql:host={$DP_CONFIG['db']['host']}", $DP_CONFIG['db']['user'], $DP_CONFIG['db']['password']);
                    $dbh->exec("CREATE DATABASE `{$DP_CONFIG['db']['dbname']}`");
                } catch (\Exception $e) {}
            }
        }
    }

    public function ensureNotInstalled()
    {
        try {
            $this->getDb()->connect();

            $installed = $this->getDb()->fetchColumn("SELECT value FROM settings WHERE name = ?", array('core.install_timestamp'));
            if ($installed) {
                return false;
            }

        } catch (\Exception $e) {
            return true;
        }

        return true;
    }

    public function getDb()
    {
        return App::getDb();
    }

    public function getLogger()
    {
        return new \Orb\Log\Logger();
    }

    public function getOrm()
    {
        return App::getOrm();
    }
}