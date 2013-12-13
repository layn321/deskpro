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

namespace Application\DevBundle\Controller;

use Application\DeskPRO\App;

class ModelsController extends \Application\DeskPRO\HttpKernel\Controller\Controller
{
	public function indexAction()
	{
		$em = $this->get('doctrine.orm.entity_manager');
		$all_metadata = $em->getMetadataFactory()->getAllMetadata();

		return $this->render('DevBundle:Models:index.html.php', array(
			'all_metadata' => $all_metadata,
		));
	}

	public function getSqlAction()
	{
		$model = '';
		$all_sql = false;
		if (!empty($_GET['model'])) {
			$model = $_GET['model'];

			if (strpos($model, ':') === false) {
				$model = 'DeskPRO:' . $model;
			}

			$em = $this->container->get('doctrine.orm.entity_manager');
			$metadata = $em->getMetadataFactory()->getMetadataFor($model);
			$tool = new \Doctrine\ORM\Tools\SchemaTool($em);
			$all_sql = $tool->getCreateSchemaSql(array($metadata));
		} else {
			$em = $this->container->get('doctrine.orm.entity_manager');
			$metadata = $em->getMetadataFactory()->getAllMetadata();
			$tool = new \Doctrine\ORM\Tools\SchemaTool($em);
			$all_sql = $tool->getCreateSchemaSql($metadata);

			//$data_reader = new \Application\DeskPRO\Install\InstallData('other_tables.sql');
			//foreach ($data_reader as $sql) {
			//	$all_sql[] = $sql;
			//}

			//$data_reader = new \Application\DeskPRO\Install\InstallData('triggers.sql');
			//foreach ($data_reader as $sql) {
			//	$all_sql[] = $sql;
			//}
		}

		if (!empty($_GET['get_php'])) {
			foreach ($all_sql as &$s) {
				$s = "\$queries[] = \"" . addslashes($s) . "\";";
			}
		} else {
			foreach ($all_sql as &$s) {
				$s = $s . ';';
			}
		}

		return $this->render('DevBundle:Models:get-sql.html.php', array(
			'model' => $model,
			'all_sql' => $all_sql,
		));
	}

	public function regenerateProxiesAction()
	{
		$warmer = new \Symfony\Bundle\DoctrineBundle\CacheWarmer\ProxyCacheWarmer(App::getContainer());
		$warmer->warmUp(null /* doctrine has its own config for cache dir */);

		return $this->render('DevBundle:Models:regenerate-proxies-done.html.php', array(
		));
	}
}
