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

use Orb\Util\Strings;
use Orb\Util\Arrays;

/**
 * Settings used by the system.
 *
 */
class Style extends \Application\DeskPRO\Domain\DomainObject
{
	/**
	 * The unique ID.
	 *
	 * @var int
	 *
	 */
	protected $id = null;

	/**
	 * @var Style
	 */
	protected $parent;

	/**
	 * Title of the style
	 *
	 * @var string
	 */
	protected $title;

	/**
	 * A note or description about the style
	 *
	 * @var string
	 */
	protected $note = '';

	/**
	 * The blob containing the logo for this style.
	 * Later we'll allow multiple resources to be attached to styles, but for now the logo is
	 * here.
	 *
	 * @var \Application\DeskPRO\Entity\Blob
	 */
	protected $logo_blob_id = null;

	/**
	 * @var \Application\DeskPRO\Entity\Blob
	 */
	protected $css_blob = null;

	/**
	 * @var \Application\DeskPRO\Entity\Blob
	 */
	protected $css_blob_rtl = null;

	/**
	 * CSS dir under static with CSS files
	 *
	 * @var string
	 */
	protected $css_dir = '';

	/**
	 * Last time the CSS variable was updated.
	 *
	 * @var string
	 */
	protected $css_updated;

	/**
	 * Options for the style
	 *
	 * @var array
	 */
	protected $options = array();

	/**
	 * @var \DateTime
	 */
	protected $created_at;

	public function __construct()
	{
		$this->setModelField('created_at', new \DateTime());
		$this->setModelField('css_updated', new \DateTime());
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	public function setParentId($parent_id)
	{
		if ($parent_id) {
			$this->setModelField('parent', App::getEntityRepository('DeskPRO:Style')->find($parent_id));
		} else {
			$this->setModelField('parent', null);
		}
	}

	public function getParentId()
	{
		return $this->parent ? $this->parent['id'] : 0;
	}


	public function getTemplate($template_name)
	{
		return App::getEntityRepository('DeskPRO:Template')->getTemplateForStyle($template_name, $this);
	}

	public function getTemplateObject($template_name)
	{
		$tpl = $this->getTemplate($template_name);
		if (!$tpl) {
			$tpl = new Template();
			$tpl['path'] = $template_name;
			$tpl['style'] = $this;
		}

		return $tpl;
	}

	public function getCustomTemplateNames()
	{
		return App::getEntityRepository('DeskPRO:Template')->getCustomTemplateNamesInStyle($this);
	}

	public function getCustomTemplateInfo()
	{
		return App::getEntityRepository('DeskPRO:Template')->getCustomTemplateInfoInStyle($this);
	}

	public function setCssVar($name, $value)
	{
		$old_opts = $this->options;

		if (!isset($this->options['css_vars'])) {
			$this->options['css_vars'] = array();
		}

		$this->options['css_vars'][$name] = $value;

		$this->_onPropertyChanged('options', $old_opts, $this->options);
		$this->setModelField('css_updated', new \DateTime());
	}

	public function getCssVars()
	{
		if (!isset($this->options['css_vars'])) {
			return array();
		}

		return $this->options['css_vars'];
	}



	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->setPrimaryTable(array( 'name' => 'styles', ));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'title', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'title', ));
		$metadata->mapField(array( 'fieldName' => 'note', 'type' => 'text', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'note', ));
		$metadata->mapField(array( 'fieldName' => 'css_dir', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'css_dir', ));
		$metadata->mapField(array( 'fieldName' => 'css_updated', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'css_updated', ));
		$metadata->mapField(array( 'fieldName' => 'options', 'type' => 'array', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'options', ));
		$metadata->mapField(array( 'fieldName' => 'created_at', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'created_at', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
		$metadata->mapManyToOne(array( 'fieldName' => 'parent', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Style', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'parent_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'logo_blob_id', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Blob', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'logo_blob_id', 'referencedColumnName' => 'id', 'unique' => true, 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'css_blob', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Blob', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'css_blob_id', 'referencedColumnName' => 'id', 'unique' => true, 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'css_blob_rtl', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Blob', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'css_blob_rtl_id', 'referencedColumnName' => 'id', 'unique' => true, 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
	}
}
