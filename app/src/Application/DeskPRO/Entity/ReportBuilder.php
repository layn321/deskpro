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
 * @category Entities
 */

namespace Application\DeskPRO\Entity;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;

use Orb\Util\Strings;
use Orb\Util\Util;

/**
 * Report builder query
 */
class ReportBuilder extends \Application\DeskPRO\Domain\DomainObject
{
	/**
	 * @var int
	 */
	protected $id = null;

	/**
	 * @var string|null
	 */
	protected $unique_key = null;

	/**
	 * @var string
	 */
	protected $title = '';

	/**
	 * @var string
	 */
	protected $description = '';

	/**
	 * @var string
	 */
	protected $query = '';

	/**
	 * @var \Application\DeskPRO\Entity\ReportBuilder
	 */
	protected $parent = null;

	/**
	 * @var bool
	 */
	protected $is_custom = true;

	/**
	 * @var string|null
	 */
	protected $category = null;

	/**
	 * @var int
	 */
	protected $display_order = 0;


	public function __construct()
	{
		$this->favorited_by = new \Doctrine\Common\Collections\ArrayCollection();
	}

	public function getTitle($type = 'raw', $params = array())
	{
		if ($type == 'raw') {
			return $this->title;
		} else if ($type == 'no_groupable') {
			return preg_replace('/\[(.+?)\]/', '$1',  $this->title);
		}

		$repository = $this->getRepository();
		$groupParams = $repository->getReportGroupParams();

		if (!is_array($params)) {
			$newParams = array();
			foreach ($params ? explode(',', $params) : array() AS $k => $v) {
				$newParams[$k + 1] = $v;
			}
			$params = $newParams;
		}

		$title = $this->title;

		if ($type != 'groupable') {
			$title = preg_replace('/\[(.+?)\]/', '$1', $title);
		}

		$getDefault = function($extras, array $paramSet, $component = false) use ($type) {
			if ($type == 'placeholder') {
				return false;
			}

			/*if (preg_match('#,\s*default:\s*([^,]+)\s*#', $extras, $match)) {
				if ($component) {
					if (isset($paramSet[$component][$match[1]])) {
						return $paramSet[$component][$match[1]];
					}
				} else if (isset($paramSet[$match[1]])) {
					return $paramSet[$match[1]];
				}
			}*/

			if ($component) {
				return reset($paramSet[$component]);
			} else {
				return reset($paramSet);
			}
		};

		$placeholderTitle = $title;

		$title = preg_replace_callback('/<(\d+):(date group)([^>]*)>/', function($match) use ($params, $groupParams, $getDefault) {
			$id = $match[1];
			if (isset($params[$id]) && isset($groupParams['dates'][$params[$id]])) {
				return $groupParams['dates'][$params[$id]][0];
			}

			$default = $getDefault($match[3], $groupParams['dates']);
			if ($default) {
				return $default[0];
			}

			return "<date>";
		}, $title);

		$title = preg_replace_callback('/<(\d+):(field group):([a-zA-Z0-9_]+)([^>]*)>/', function($match) use ($params, $groupParams, $getDefault) {
			$id = $match[1];
			$type = $match[3];
			if (isset($params[$id]) && isset($groupParams['fields'][$type][$params[$id]])) {
				return $groupParams['fields'][$type][$params[$id]][0];
			}

			$default = $getDefault($match[4], $groupParams['fields'], $type);
			if ($default) {
				return $default[0];
			}

			return "<field>";
		}, $title);

		$title = preg_replace_callback('/<(\d+):(status group):([a-zA-Z0-9_]+)([^>]*)>/', function($match) use ($params, $groupParams, $getDefault) {
			$id = $match[1];
			$type = $match[3];
			if (isset($params[$id]) && isset($groupParams['statuses'][$type][$params[$id]])) {
				return $groupParams['statuses'][$type][$params[$id]][0];
			}

			$default = $getDefault($match[4], $groupParams['statuses'], $type);
			if ($default) {
				return $default[0];
			}

			return "<status>";
		}, $title);

		$title = preg_replace_callback('/<(\d+):(order group):([a-zA-Z0-9_]+)([^>]*)>/', function($match) use ($params, $groupParams, $getDefault) {
			$id = $match[1];
			$type = $match[3];
			if (isset($params[$id]) && isset($groupParams['orders'][$type][$params[$id]])) {
				return $groupParams['orders'][$type][$params[$id]][0];
			}

			$default = $getDefault($match[4], $groupParams['orders'], $type);
			if ($default) {
				return $default[0];
			}

			return "<order>";
		}, $title);

		$title = preg_replace('/<chart:[a-zA-Z0-9_-]+>/', '', $title);

		if ($placeholderTitle != $title) {
			$title = preg_replace_callback('/(, )?(split by|grouped by) ([a-zA-Z0-9 ]+) & ([a-zA-Z0-9]+)/', function($match) {
				$firstMatch = rtrim($match[3]);
				$secondMatch = rtrim($match[4]);

				if ($firstMatch == 'nothing' && $secondMatch == 'nothing') {
					// double group/splt on nothing - remove whole string
					return '';
				} else if ($firstMatch == 'nothing') {
					// first group is nothing, but second on something
					return $match[1] . $match[2] . ' ' . $match[4];
				} else if ($secondMatch == 'nothing') {
					// first group is something, but second on nothing
					return $match[1] . $match[2] . ' ' . $match[3];
				}

				return $match[0];
			}, $title);

			$title = preg_replace('/(, )?(split by|grouped by) nothing/', '', $title);
		}

		return $title;
	}

	/**
	 * Gets the DPQL parts for this report's query
	 *
	 * @return array
	 */
	public function getParts()
	{
		$compiler = new \Application\DeskPRO\Dpql\Compiler();
		$statement = $compiler->compile($this->query);

		return $statement->getDpqlParts();
	}

	/**
	 * @return bool
	 */
	public function isEditable()
	{
		return ($this->is_custom || App::getConfig('debug.dev'));
	}

	public function hasPlaceholders()
	{
		return preg_match('/<\d+:[^>]+>/', $this->title);
	}

	/**
	 * Determines if the passed string is effectively different.
	 * The string may be slightly different and still pass.
	 *
	 * @param string $query
	 * 
	 * @return bool
	 */
	public function isQueryDifferent($query)
	{
		$query = preg_replace('/\s/', '', $query);
		$thisQuery = preg_replace('/\s/', '', $this->query);
		return ($query != $thisQuery);
	}

	/**
	 * Quick lookup handler to determine if a particular user has favorited this
	 *
	 * @var array
	 */
	protected $_is_favorited = array();

	/**
	 * Returns true if the specified person has favorited this
	 *
	 * @param Person|null $person Defaults to current person
	 *
	 * @return bool
	 */
	public function isFavorited(Person $person = null)
	{
		if ($person === null) {
			$person = App::getCurrentPerson();
		}

		return false;
	}

	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\ReportBuilder';
		$metadata->setPrimaryTable(array(
			'name' => 'report_builder',
			'indexes' => array(
				'parent_id_idx' => array('columns' => array('parent_id'))
			),
			'uniqueConstraints' => array(
				'unique_key_idx' => array('columns' => array('unique_key'))
			)
		));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_DEFERRED_IMPLICIT);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'unique_key', 'type' => 'string', 'length' => 50, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'unique_key', ));
		$metadata->mapField(array( 'fieldName' => 'title', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'title', ));
		$metadata->mapField(array( 'fieldName' => 'description', 'type' => 'text', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'description', ));
		$metadata->mapField(array( 'fieldName' => 'query', 'type' => 'text', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'query', ));
		$metadata->mapField(array( 'fieldName' => 'is_custom', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_custom', ));
		$metadata->mapField(array( 'fieldName' => 'category', 'type' => 'string', 'length' => 25, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'category', ));
		$metadata->mapField(array( 'fieldName' => 'display_order', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'display_order', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);

		$metadata->mapManyToOne(array( 'fieldName' => 'parent', 'targetEntity' => 'Application\\DeskPRO\\Entity\\ReportBuilder', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'parent_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
	}
}
