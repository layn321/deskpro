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

namespace Application\DeskPRO;

use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DeskPROBundle extends \Symfony\Component\HttpKernel\Bundle\Bundle
{
	public function __construct()
	{
		$this->name = 'DeskPRO';
	}

	public function build(ContainerBuilder $container)
    {
        // register the extension(s) found in DependencyInjection/ directory
        parent::build($container);

        $container->registerExtension(new \Application\DeskPRO\DependencyInjection\CoreExtension());
        $container->registerExtension(new \Application\DeskPRO\DependencyInjection\SearchExtension());
    }

	/**
     * @param Application $application An Application instance
     */
    public function registerCommands(Application $application)
    {
		$commands = array(
			'Application\\DeskPRO\\Command\\AgentsCommand',
			'Application\\DeskPRO\\Command\\AsseticCommand',
			'Application\\DeskPRO\\Command\\DbCollationChangeCommand',
			'Application\\DeskPRO\\Command\\DecodeTacCommand',
			'Application\\DeskPRO\\Command\\DevBuildLangCommand',
			'Application\\DeskPRO\\Command\\DevCheckReservedWordsCommand',
			'Application\\DeskPRO\\Command\\DevCommand',
			'Application\\DeskPRO\\Command\\DevDoMigrationCommand',
			'Application\\DeskPRO\\Command\\DevExportLangCommand',
			'Application\\DeskPRO\\Command\\DevGenChangelogDocCommand',
			'Application\\DeskPRO\\Command\\DevGenDpqlDocsCommand',
			'Application\\DeskPRO\\Command\\DevLangCheckPhraseIdsCommand',
			'Application\\DeskPRO\\Command\\DevLangCheckVarsCommand',
			'Application\\DeskPRO\\Command\\DevLangOneSkyInitCommand',
			'Application\\DeskPRO\\Command\\DevLoadDataCommand',
			'Application\\DeskPRO\\Command\\DevPagelogCommand',
			'Application\\DeskPRO\\Command\\DevRebuildSyncDataCommand',
			'Application\\DeskPRO\\Command\\ElasticInitializerCommand',
			'Application\\DeskPRO\\Command\\GenBuildClassCommand',
			'Application\\DeskPRO\\Command\\GenerateSchemaFileCommand',
			'Application\\DeskPRO\\Command\\GenRandomEmailCommand',
			'Application\\DeskPRO\\Command\\ImportCommand',
			'Application\\DeskPRO\\Command\\ImportRestoreUnknownAgentsCommand',
			'Application\\DeskPRO\\Command\\ImportZendeskCommand',
			'Application\\DeskPRO\\Command\\InstallCommand',
			'Application\\DeskPRO\\Command\\InternalUpgradeRunnerCommand',
			'Application\\DeskPRO\\Command\\LanguageToPOCommand',
			'Application\\DeskPRO\\Command\\LicenseInfoCommand',
			'Application\\DeskPRO\\Command\\LoginTokenCommand',
			'Application\\DeskPRO\\Command\\MoveBlobsCommand',
			'Application\\DeskPRO\\Command\\PhraseCheckCommand',
			'Application\\DeskPRO\\Command\\PluginCommand',
			'Application\\DeskPRO\\Command\\ProcessEmailCommand',
			'Application\\DeskPRO\\Command\\RecountRatingsCommand',
			'Application\\DeskPRO\\Command\\RefillTicketActiveCommand',
			'Application\\DeskPRO\\Command\\SchemaCommand',
			'Application\\DeskPRO\\Command\\SchemaCorrectionCommand',
			'Application\\DeskPRO\\Command\\SearchReindexCommand',
			'Application\\DeskPRO\\Command\\SyncDataCommand',
			'Application\\DeskPRO\\Command\\TestCommand',
			'Application\\DeskPRO\\Command\\TestEmailDecodeCommand',
			'Application\\DeskPRO\\Command\\UpgradeCommand',
			'Application\\DeskPRO\\Command\\VerifyBlobsCommand',
			'Application\\DeskPRO\\Command\\VerifySearchTablesCommand',
			'Application\\DeskPRO\\Command\\WorkerJobCommand',
		);

		foreach ($commands as $cmd) {
			$application->add(new $cmd);
		}
    }

	public function getNamespace()
	{
		return __NAMESPACE__;
	}

	public function getPath()
	{
		return __DIR__;
	}
}
