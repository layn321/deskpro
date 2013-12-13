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
use Application\DeskPRO\PageDisplay\Item\Portal\PortalItemAbstract;

use Orb\Util\Strings;
use Orb\Util\Arrays;

/**
 * Description of layout of the user portal
 *
 * = $data format =
 * <pre>
 * array(
 *     array(
 *         'xxx' => 'xxx
 *     )
 * );
 * </pre>
 *
 * The type can be a short name in which case the full PHP namespace for DeskPRO's types
 * will be prepended (Application\DeskPRO\PageDisplay\Item\Portal\XXX). You may also use underscore
 * format which will be converted into camel case (some_type to SomeType).
 *
 * Keys in the data array are insignificant. They may be used to keep track of things in the designer.
 *
 */
class PortalPageDisplay extends PageDisplayAbstract
{
	/**
	 * Portal (main page)
	 */
	const SECTION_PORTAL = 'portal';

	/**
	 * Across the top (not columned)
	 */
	const SECTION_PAGETOP = 'pagetop';

	/**
	 * The sidebar.
	 */
	const SECTION_SIDEBAR = 'sidebar';

	/**
	 * The header content. Usually just one item thats rendered into the header.
	 */
	const SECTION_HEADER = 'header';

	/**
	 * The footer content. Usually just one item thats rendered into the footer.
	 */
	const SECTION_FOOTER = 'footer';

	/**
	 * The class handler
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * @var int
	 */
	protected $display_order = 0;

	/**
	 * @var bool
	 */
	protected $is_enabled = 0;

	protected $id;

	protected $section = 'sidebar';

	protected $data = array();

	public function addData($key, $value)
	{
		$old = $this->data;
		$this->data[$key] = $value;
		$this->_onPropertyChanged('data', $old, $this->data);
	}

	public function removeData($key)
	{
		$old = $this->data;
		unset($this->data[$key]);
		$this->_onPropertyChanged('data', $old, $this->data);
	}

	public function deleteCachedPages()
	{
		$cache_id = "d.portal.block.block.portal_{$this->section}_" . str_replace('\\', '', get_class($this));
		App::getDb()->executeUpdate('
			DELETE FROM cache WHERE id LIKE ?
		', array("$cache_id%"));
	}



	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\PortalPageDisplay';
		$metadata->setPrimaryTable(array( 'name' => 'portal_page_display', ));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'type', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'type', ));
		$metadata->mapField(array( 'fieldName' => 'display_order', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'display_order', ));
		$metadata->mapField(array( 'fieldName' => 'is_enabled', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_enabled', ));
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'section', 'type' => 'string', 'length' => 50, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'section', ));
		$metadata->mapField(array( 'fieldName' => 'data', 'type' => 'array', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'data', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
	}
}
