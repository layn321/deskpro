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
use Orb\Util\Util;


class DevGenDpqlDocsCommand extends \Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('dpdev:gen-dpql-docs');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$tableDescriptions = array(
			'articles' => 'Articles and information in the knowledgebase',
			'article_attachments' => 'Attachments to knowledgebase articles',
			'article_comments' => 'Comments for each knowledgebase article',
			'chat_conversations' => 'Records for each chat',
			'chat_messages' => 'Individual messages in each chat',
			'downloads' => 'Information about each file that has been specified as a download',
			'download_comments' => 'Individual comments for each download',
			'feedback' => 'Customer feedback and suggestion records',
			'feedback_attachments' => 'Attachments to feedback and suggestions',
			'feedback_comments' => 'Comments for feedback and suggestions',
			'labels_articles' => 'Labels for knowledgebase articles',
			'labels_chat_conversations' => 'Labels for chats',
			'labels_downloads' => 'Labels for downloads',
			'labels_feedback' => 'Labels for feedback and suggestions',
			'labels_news' => 'Labels for news entries',
			'labels_organizations' => 'Labels for organizations',
			'labels_people' => 'Labels for registered people',
			'labels_tasks' => 'Labels for tasks',
			'labels_tickets' => 'Labels for tickets',
			'news' => 'News entries',
			'news_comments' => 'Comments on news entries',
			'organizations' => 'Organizations',
			'people' => 'Registered people (users)',
			'people_emails' => 'Email addresses in use by registered people',
			'tasks' => 'Tasks',
			'task_comments' => 'Comments on tasks',
			'tickets' => 'Tickets',
			'tickets_log' => 'Ticket change log entries',
			'tickets_messages' => 'Individual messages in tickets',
			'ticket_attachments' => 'Attachments to tickets',
			'ticket_charges' => 'Ticket billing charges',
			'ticket_feedback' => 'Feedback on ticket responses',
			'ticket_slas' => 'SLA status records for tickets'
		);

		$conditionResolvers = array(
			'custom_data_article' => array('#', ' (Gets data for the article field with ID #)'),
			'custom_data_feedback' => array('#', ' (Gets data for the feedback field with ID #)'),
			'custom_data_organizations' => array('#', ' (Gets data for the organization field with ID #)'),
			'custom_data_person' => array('#', ' (Gets data for the person field with ID #)'),
			'custom_data_ticket' => array('#', ' (Gets data for the ticket field with ID #)'),
			'ticket_slas' => array('#', ' (Gets ticket SLA data for the SLA with ID #)'),
		);

		$tableEntities = \Application\DeskPRO\Dpql\Statement\Display::getTableEntityList();

		$entityMap = array();
		$toProcess = $tableEntities;

		while ($entityName = array_shift($toProcess)) {
			/** @var $repository \Application\DeskPRO\EntityRepository\AbstractEntityRepository */
			$repository = App::getEntityRepository($entityName);
			$fields = array();

			foreach ($repository->getFieldMappings() AS $key => $field) {
				if (isset($field['dpqlAccess']) && !$field['dpqlAccess']) {
					continue;
				}

				switch ($field['type']) {
					case 'datetime':
						$type = 'datetime';
						break;

					case 'integer':
					case 'smallint':
					case 'bigint':
					case 'decimal':
					case 'float':
						$type = 'number';
						break;

					case 'date':
						$type = 'date';
						break;

					case 'time':
						$type = 'time';
						break;

					case 'boolean':
						$type = 'boolean';
						break;

					case 'string':
					case 'text':
					default:
						$type = 'string';
						break;
				}

				$fields[$key] = array('type' => $type);
			}

			foreach ($repository->getAssociationMappings() AS $association) {
				if (empty($association['joinColumns'])) {
					// need to know how to make the join; ignore this
					continue;
				}

				foreach ($association['joinColumns'] AS $joinColumn) {
					if (!isset($fields[$joinColumn['name']])) {
						$fields[$joinColumn['name']] = array('type' => 'number');
					}
				}
			}

			$associations = array();

			foreach ($repository->getAssociationMappings() AS $association) {
				$target = $association['targetEntity'];
				$childRepository = $target::getRepository();

				if ((isset($association['dpqlAccess']) && !$association['dpqlAccess'])
					|| !($childRepository instanceof \Application\DeskPRO\EntityRepository\AbstractEntityRepository)
					|| $association['type'] == \Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_MANY
				) {
					continue;
				}

				if (!empty($association['joinColumns'])) {
					// join can be resolved directly
				} else {
					$childAssociations = $childRepository->getAssociationMappings();
					if (!empty($childAssociations[$association['mappedBy']]['joinColumns'])) {
						// join details are on the other table
					} else {
						continue;
					}
				}

				$associations[$association['fieldName']] = $target;

				$childTable = $childRepository->getTableName();
				if (isset($conditionResolvers[$childTable])) {
					$condition = $conditionResolvers[$childTable];
					$associations[$association['fieldName'] . "[$condition[0]]"] = array($target, $condition[1]);
				}
			}

			foreach ($repository->getReportAssociations() AS $name => $association) {
				$target = $association['targetEntity'];
				$childRepository = $target::getRepository();

				if (!($childRepository instanceof \Application\DeskPRO\EntityRepository\AbstractEntityRepository)) {
					continue;
				}

				$associations[$name] = $target;

				$childTable = $childRepository->getTableName();
				if (isset($conditionResolvers[$childTable])) {
					$condition = $conditionResolvers[$childTable];
					$associations[$name . "[$condition[0]]"] = array($target, $condition[1]);
				}
			}

			uksort($fields, 'strnatcasecmp');
			uksort($associations, 'strnatcasecmp');

			$entityMap[$repository->getName()] = array(
				'repository' => $repository,
				'fields' => $fields,
				'associations' => $associations
			);

			foreach ($associations AS $toProcessAssociation) {
				if (is_array($toProcessAssociation)) {
					$toProcessAssociation = $toProcessAssociation[0];
				}
				if (!isset($entityMap[$toProcessAssociation])) {
					$toProcess[] = $toProcessAssociation;
				}
			}
		}

		uksort($entityMap, 'strnatcasecmp');
		uksort($tableEntities, 'strnatcasecmp');

		$tableList = array();
		foreach ($tableEntities AS $table => $entityName) {
			/** @var $repository \Application\DeskPRO\EntityRepository\AbstractEntityRepository */
			$repository = App::getEntityRepository($entityName);
			$entityName = $repository->getName();
			$description = isset($tableDescriptions[$table]) ? $tableDescriptions[$table] : '';
			$tableList[] = '<tr><td><a href="#dp-user-' . $this->_getEntityHtmlId($entityName)
				. '">' . htmlspecialchars($table) . '</a></td><td>' . $description . '</td></tr>';
		}

		$html = "<table class=\"dpql-table-list\"><tr><th>Table Name</th><th>Description</th></tr>\n" . implode("\n", $tableList) . "\n</table>\n\n"
			. "<div class=\"dpql-data-types\"><h2>Data Types</h2>\n\n";

		foreach ($entityMap AS $info) {
			/** @var $repository \Application\DeskPRO\EntityRepository\AbstractEntityRepository */
			$repository = $info['repository'];
			$entityName = $repository->getName();

			$columnList = array();
			foreach ($info['fields'] AS $fieldId => $fieldInfo) {
				$columnList[] = '<tr><td>' . $fieldId . '</td><td>' . $fieldInfo['type'] . '</td></tr>';
			}
			foreach ($info['associations'] AS $fieldId => $associationEntity) {
				if (is_array($associationEntity)) {
					list($associationEntity, $append) = $associationEntity;
				} else {
					$append = '';
				}

				$columnList[] = '<tr><td>' . $fieldId . '</td><td><a href="#dp-user-' . $this->_getEntityHtmlId($associationEntity) . '">'
					. $this->_getDataTypeName($associationEntity) . '</a>' . $append . '</td></tr>';
			}

			$html .= '<div class="dpql-data-type"><h3 id="' . $this->_getEntityHtmlId($entityName) . '">'
				. $this->_getDataTypeName($entityName) . '</h3>' . "\n"
				. "<table><tr><th>Field Name</th><th>Data Type</th></tr>\n" . implode("\n", $columnList) . "\n</table></div>\n\n";
		}

		$html .= '</div>';

		$html = '<div class="dpql-field-list">' . "\n" . $html . '</div>';

		$writePath = DP_WEB_ROOT . '/data/tmp/dpql-docs.html';
		file_put_contents($writePath, $html);

		$output->writeln("Output written to $writePath");

		return 0;
	}

	protected function _getEntityHtmlId($entityName)
	{
		$name = Util::getBaseClassname($entityName);
		return 'dp-entity-' . preg_replace('/[^a-z0-9_]/', '_', strtolower($name));
	}

	protected function _getDataTypeName($entityName)
	{
		$name = Util::getBaseClassname($entityName);
		return trim(preg_replace('/([A-Z]+)/', ' $1', $name));
	}
}
